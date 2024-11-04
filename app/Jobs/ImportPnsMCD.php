<?php

namespace App\Jobs;

use App\Models\TempMcd;
use App\Imports\McdImport;
use App\Imports\PnsImport;
use App\Models\TempPns;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use PhpParser\Node\Expr\Cast\String_;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ImportPnsMCD implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

  protected $pnsFileLocation;
  protected $mcdFileLocation; 
  protected $temptimesheet;   
  public function __construct($mcdCsv, $pnsCsv, $temptimesheet)
  {
      $this->pnsFileLocation = $pnsCsv;
      $this->mcdFileLocation = $mcdCsv;
      $this->temptimesheet = $temptimesheet;
      //
  }

    /**
     * Execute the job.
     */
    public function handle(): void{

        Log::info('MCD File location: ' . $this->mcdFileLocation);
        Log::info('PNS File location: ' . $this->pnsFileLocation);

        

        // import csv data to storage

        $this->temptimesheet->update([
            'customer_file_name' => $this->mcdFileLocation,
            'employee_file_name' => $this->pnsFileLocation
        ]);
        if ($this->mcdFileLocation){
        try {
            DB::beginTransaction();
            $dataMcd = Excel::toCollection(new McdImport, $this->mcdFileLocation, 'local', \Maatwebsite\Excel\Excel::CSV);
            $collectMcd = collect($dataMcd->first());
        // $totals = $collect->pop();
            $mcdHeaders = $collectMcd->first()->toArray();
            $mcdRows = $collectMcd->except(0)->values()->toArray();
            $flattenedDataMcd = [];
            $dateHeadersMcd = array_slice($mcdHeaders, 7);  // Extract date headers
            foreach ($mcdRows as $row) {
                foreach ($dateHeadersMcd as $index => $date) {
                    $value = $row[$index + 7] !== null ? $row[$index + 7] : 0;  // Replace null with 0
                    $flattenedDataMcd[] = [
                        "temp_time_sheet_id" => $this->temptimesheet->id,
                        "kronos_job_number" => $row[0] ?? "N/A",
                        "parent_id" => $row[1] ?? "N/A",
                        "oracle_job_number" => $row[2] ?? 'N/A',
                        "employee_name" => $row[3] ?? 'N/A',
                        "leg_id" => $row[4] ?? 'N/A',
                        "job_dissipline" => $row[5] ?? 'N/A',
                        "slo_no" => $row[6] ?? 'N/A',
                        "date" => Carbon::createFromFormat('d/m/Y', $date),
                        "value" => $value
                    ];
                }
            }
            $chunk = array_chunk($flattenedDataMcd, 1000);
            foreach ($chunk as $data) {
               TempMcd::insert($data);
            }
            $this->temptimesheet->update([
                'status' => 'draft',
                'customer_total_imported' => count($flattenedDataMcd)
            ]);
            DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->failed($th);
            }catch (\Exception $e) {
                DB::rollBack();
                $this->failed($e);
            }catch (\Error $e) {
                DB::rollBack();
                $this->failed($e);
            }
        }

        if($this->pnsFileLocation){
            try {
                DB::beginTransaction();
                $dataPns = Excel::toCollection(new PnsImport, $this->pnsFileLocation, 'local', \Maatwebsite\Excel\Excel::CSV);
                $collectPns = collect($dataPns->first());
                $pnsHeaders = $collectPns->first()->toArray();
                $pnsRows = $collectPns->except(0)->values()->toArray();
                $flattenedDataPns = [];
                $dateHeadersPns = array_slice($pnsHeaders, 2);
                foreach ($pnsRows as $row) {
                    foreach ($dateHeadersPns as $index => $date) {
                        $value = $row[$index + 2] !== null ? $row[$index + 2] : 0;  // Replace null with 0
                        $flattenedDataPns[] = [
                            "temp_time_sheet_id" => $this->temptimesheet->id,
                            "kronos_job_number" => "N/A",
                            "parent_id" => "N/A",
                            "oracle_job_number" => 'N/A',
                            "employee_name" => $row[0] ?? 'N/A',
                            "leg_id" => $row[1] ?? 'N/A',
                            "job_dissipline" => 'N/A',
                            "slo_no" => 'N/A',
                            "date" => Carbon::createFromFormat('d/m/Y', $date),
                            "value" => $value
                        ];
                    }
                }
                $chunk = array_chunk($flattenedDataPns, 1000);
                foreach ($chunk as $data) {
                    TempPns::insert($data);
                }
                $this->temptimesheet->update([
                    'status' => 'draft',
                    'employee_total_imported' => count($flattenedDataPns)
                ]);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->failed($th);
            }catch (\Exception $e) {
                DB::rollBack();
                $this->failed($e);
            }catch (\Error $e) {
                DB::rollBack();
                $this->failed($e);
            }
        }

        

        $this->temptimesheet->update([
            'customer_file_name' => $this->mcdFileLocation,
            'employee_file_name' => $this->pnsFileLocation
        ]);
    }

    public function failed($exception)
    {
        $this->temptimesheet->update([
            'status' => 'failed',
        ]);
        Log::error($exception);
        $this->delete();
    }
}
