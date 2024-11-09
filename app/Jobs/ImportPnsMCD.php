<?php

namespace App\Jobs;

use App\Models\TempMcd;
use App\Models\TempPns;
use App\Imports\McdImport;
use App\Imports\PnsImport;
use App\Models\PnsMcdDiff;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpParser\Node\Expr\Cast\String_;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
            $dateHeadersMcd = array_slice($mcdHeaders, 8);  // Extract date headers
            foreach ($mcdRows as $row) {
                foreach ($dateHeadersMcd as $index => $date) {
                    $value = $row[$index + 8] !== null ? $row[$index + 8] : 0;  // Replace null with 0
                    $flattenedDataMcd[] = [
                        "temp_time_sheet_id" => $this->temptimesheet->id,
                        "kronos_job_number" => $row[0] ?? "N/A",
                        "parent_id" => $row[1] ?? "N/A",
                        "oracle_job_number" => $row[2] ?? 'N/A',
                        "employee_name" => $row[3] ?? 'N/A',
                        "leg_id" => $row[4] ?? 'N/A',
                        "job_dissipline" => $row[5] ?? 'N/A',
                        "slo_no" => $row[6] ?? 'N/A',
                        "rate" => $row[7] ?? 1,
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
                $dateHeadersPns = array_slice($pnsHeaders, 3);
                foreach ($pnsRows as $row) {
                    foreach ($dateHeadersPns as $index => $date) {
                        $value = $row[$index + 3] !== null ? $row[$index + 3] : 0;  // Replace null with 0
                        $flattenedDataPns[] = [
                            "temp_time_sheet_id" => $this->temptimesheet->id,
                            "kronos_job_number" => "N/A",
                            "parent_id" => "N/A",
                            "oracle_job_number" => 'N/A',
                            "employee_name" => $row[0] ?? 'N/A',
                            "leg_id" => $row[1] ?? 'N/A',
                            "job_dissipline" => 'N/A',
                            "slo_no" => 'N/A',
                            "rate" => $row[2] ?? 1,
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

        // compare the data
        try {
        $pns = TempPns::where('temp_time_sheet_id', $this->temptimesheet->id)->get();
        $mcd = TempMcd::where('temp_time_sheet_id', $this->temptimesheet->id)->get();

        $sumPNS = $pns->groupBy(function($item) {
            return $item['employee_name'] . '_' . $item['date'];
        })->map(function($items) {
            return [
                'employee_name' => $items->first()->employee_name,
                'date' => $items->first()->date,
                'ids' => $items->pluck('id'),               
                'value' => $items->sum('value')
            ];;
        });

        $sumMCD = $mcd->groupBy(function($item) {
            return $item['employee_name'] . '_' . $item['date'];
        })->map(function($items) {
            return [
                'temp_time_sheet_id' => $this->temptimesheet->id,
                'employee_name' => $items->first()->employee_name,
                'date' => $items->first()->date,
                'ids' => $items->pluck('id'),
                'value' => $items->sum('value')
            ];;
        });

        $differeces = [];
        // PnsMcdDiff::create([
        //     'temp_time_sheet_id' => $this->temptimesheet->id,
        //     'employee_name' => $item1['employee_name'],
        //     'date' => $item1['date'],
        //     'mcd_ids' => $item2['ids'],
        //     'pns_ids' => $item1['ids'],
        //     'mcd_value' => $item2['value'],
        //     'pns_value' => $item1['value']
        // ]);

        foreach ($sumPNS as $key => $item1) {
            if($sumMCD->has($key)) {
               $item2 = $sumMCD[$key];
               if ($item1['value'] != $item2['value']) {
                    $differeces[] = [
                        'temp_time_sheet_id' => $this->temptimesheet->id,
                        'employee_name' => $item1['employee_name'],
                        'date' => $item1['date'],
                        'mcd_ids' => $item2['ids'],
                        'pns_ids' => $item1['ids'],
                        'mcd_value' => $item2['value'],
                        'pns_value' => $item1['value']
                    ];
                }
            }else{
                $differeces[] = [
                    'temp_time_sheet_id' => $this->temptimesheet->id,
                    'employee_name' => $item1['employee_name'],
                    'date' => $item1['date'],
                    'mcd_ids' => "[]",
                    'pns_ids' => $item1['ids'],
                    'mcd_value' => 0,
                    'pns_value' => $item1['value']
                ];
            }
        }

        PnsMcdDiff::insert($differeces);
        } catch (\Throwable $th) {
            $this->failed($th);
        }catch (\Exception $e) {
            $this->failed($e);
        }catch (\Error $e) {
            $this->failed($e);
        }
    }

    public function failed($exception)
    {
        $this->temptimesheet->update([
            'status' => 'failed',
        ]);
        // delete the files
        Storage::disk('local')->delete($this->mcdFileLocation);
        Storage::disk('local')->delete($this->pnsFileLocation);

        // delete the data from the database
        TempMcd::where('temp_time_sheet_id', $this->temptimesheet->id)->delete();
        TempPns::where('temp_time_sheet_id', $this->temptimesheet->id)->delete();
        
        Log::error($exception);
        $this->delete();
    }
}
