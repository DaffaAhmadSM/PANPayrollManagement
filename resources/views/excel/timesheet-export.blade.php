<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="border: 2px solid black; font-weight: bold;" rowspan="2" align="center" valign="center"
                width="25">Kronos Job
                Charge</th>
            <th style="border: 2px solid black; font-weight: bold;" rowspan="2" align="center" valign="center"
                width="25">Parent ID
            </th>
            <th style="border: 2px solid black; font-weight: bold;" rowspan="2" align="center" valign="center"
                width="25">Oracle Job
                Charge</th>
            <th style="border: 2px solid black; font-weight: bold;" rowspan="2" align="center" valign="center"
                width="25">Employee
                Name</th>
            <th style="border: 2px solid black; font-weight: bold;" rowspan="2" align="center" valign="center"
                width="25">Emp.</th>
            <th style="border: 2px solid black; font-weight: bold;" rowspan="2" align="center" valign="center"
                width="25">
                Classification</th>
            <th style="border: 2px solid black; font-weight: bold;" rowspan="2" align="center" valign="center"
                width="25">Service
                Order No.</th>
            @foreach ($days as $day)
                @if ($day['is_holiday'])
                    <th colspan="3" align="center" valign="center"
                        style="background-color: #f29a6e; border: 2px solid black; font-weight: bold;">
                        {{ $day['date'] }}</th>
                @else
                    <th style="border: 2px solid black; font-weight: bold;" colspan="3" align="center"
                        valign="center">
                        {{ $day['date'] }}</th>
                @endif
            @endforeach
            <th style="border: 2px solid black; font-weight: bold;" rowspan="2" align="center" valign="center">Actual
                Hours</th>
            <th style="border: 2px solid black; font-weight: bold;" rowspan="2" align="center" valign="center">
                Invoice Hours</th>
            <th style="border: 2px solid black; font-weight: bold;" rowspan="2" align="center" valign="center">Rate
            </th>
            <th style="border: 2px solid black; font-weight: bold;" rowspan="2" align="center" valign="center">Amount
                (IDR)</th>
            <th style="border-top: 2px solid black; font-weight: bold;" rowspan="1" align="center" valign="center">
                ETI
                Bonus</th>
            <th style="border: 2px solid black; font-weight: bold;" rowspan="2" align="center" valign="center">Total
                Amount</th>
        </tr>
        <tr>
            @foreach ($days as $day)
                @if ($day['is_holiday'])
                    <th style="border: 2px solid black; font-weight: bold;" width="10" align="center"
                        valign="center">2</th>
                    <th style="border: 2px solid black; font-weight: bold;" width="10" align="center"
                        valign="center">3</th>
                    <th style="border: 2px solid black; font-weight: bold;" width="10" align="center"
                        valign="center">4</th>
                @else
                    <th style="border: 2px solid black; font-weight: bold;" width="10" align="center"
                        valign="center">1</th>
                    <th style="border: 2px solid black; font-weight: bold;" width="10" align="center"
                        valign="center">1.5</th>
                    <th style="border: 2px solid black; font-weight: bold;" width="10" align="center"
                        valign="center">2</th>
                @endif
            @endforeach
            <th style="border-bottom: 2px solid black; font-weight: bold;" align="center" valign="center">
                {{ $temptimesheet['eti_bonus_percentage'] }}%
            </th>
        </tr>
    </thead>
    <tbody>
        @php
            $super_amount = 0;
            $super_eti_bonus = 0;
            $super_amount_total = 0;
            $super_actual_hours_total = 0;
            $super_paid_hours_total = 0;
        @endphp
        @foreach ($data_kronos as $data_output)
            @php
                $emp_amount = 0;
                $emp_eti_bonus = 0;
                $emp_amount_total = 0;
            @endphp
            @foreach ($data_output['data'] as $parent_id)
                @foreach ($parent_id as $row)
                    <tr>
                        <td style="border: 2px solid black;">{{ $row['Kronos_job_number'] }}</td>
                        <td style="border: 2px solid black;">{{ $row['parent_id'] }}</td>
                        <td style="border: 2px solid black;">{{ $row['oracle_job_number'] }}</td>
                        <td style="border: 2px solid black;">{{ $row['employee_name'] }}</td>
                        <td style="border: 2px solid black;">{{ $row['emp'] }}</td>
                        <td style="border: 2px solid black;">{{ $row['classification'] }}</td>
                        <td style="border: 2px solid black;">{{ $row['slo_no'] }}</td>
                        @foreach ($row['dates'] as $overtime)
                            @for ($i = 0; $i < 3; $i++)
                                @if (!$overtime['is_holiday'] && $i == 0)
                                    @if ($overtime['basic_hours'] > 0)
                                        <td style="border: 2px solid black;" data-format="0.0">
                                            {{ $overtime['basic_hours'] }}</td>
                                    @else
                                        <td style="border: 2px solid black;"></td>
                                    @endif
                                    @continue
                                @endif
                                @if (!$overtime['is_holiday'])
                                    @if (isset($overtime['overtime_timesheet'][$i - 1]) && $overtime['overtime_timesheet'][$i - 1] != 0)
                                        <td style="border: 2px solid black;" data-format="0.0">
                                            {{ $overtime['overtime_timesheet'][$i - 1] }}</td>
                                    @else
                                        <td style="border: 2px solid black;"></td>
                                    @endif
                                @else
                                    @if (isset($overtime['overtime_timesheet'][$i]) && $overtime['overtime_timesheet'][$i] != 0)
                                        <td style="border: 2px solid black;" data-format="0.0">
                                            {{ $overtime['overtime_timesheet'][$i] }}</td>
                                    @else
                                        <td style="border: 2px solid black;"></td>
                                    @endif
                                @endif
                            @endfor
                        @endforeach
                        <td style="border: 2px solid black;" data-format="#,##0.00">{{ $row['actual_hours_total'] }}
                        </td>
                        <td style="border: 2px solid black;" data-format="#,##0.00">{{ $row['paid_hours_total'] }}
                        </td>
                        <td style="border: 2px solid black;" data-format="#,##">{{ $row['rate'] }}</td>
                        @php
                            $amount = bcmul($row['rate'], $row['paid_hours_total'], 6);
                            // $eti_bonus = $amount * ($temptimesheet["eti_bonus_percentage"]/100);
                            $eti_bonus = bcdiv(bcmul($amount, $temptimesheet['eti_bonus_percentage'], 6), 100, 6);
                            // $total = $amount + $eti_bonus;
                            $total = bcadd($amount, $eti_bonus, 6);

                            //precision
                            $emp_amount = bcadd($emp_amount, $amount, 6);
                            $emp_eti_bonus = bcadd($emp_eti_bonus, $eti_bonus, 6);
                            $emp_amount_total = bcadd($emp_amount_total, $total, 6);
                        @endphp
                        <td style="border: 2px solid black;" data-format="#,##0.00">{{ $amount }}</td>
                        <td style="border: 2px solid black;" data-format="#,##0.00">{{ $eti_bonus }}</td>
                        <td style="border: 2px solid black;" data-format="#,##0.00">{{ $total }}</td>
                    </tr>
                @endforeach
                <tr>
                    @for ($i = 0; $i < 7; $i++)
                        <td style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;"></td>
                    @endfor
                    @foreach ($data_output['total_overtime_hours'] as $total)
                        <td style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;"></td>
                        <td style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;"></td>
                        <td style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;"></td>
                    @endforeach
                    <td data-format="#,##0.00"
                        style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;">
                        {{ $data_output['actual_hours_total'] }}
                    </td>
                    <td data-format="#,##0.00"
                        style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;">
                        {{ $data_output['paid_hours_total'] }}
                    </td>
                    <td style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;"></td>
                    <td data-format="#,##0.00"
                        style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;">
                        {{ $emp_amount }}</td>
                    <td data-format="#,##0.00"
                        style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;">
                        {{ $emp_eti_bonus }}</td>
                    <td data-format="#,##0.00"
                        style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;">
                        {{ $emp_amount_total }}</td>
                </tr>
                <tr>
                </tr>
            @endforeach
            @php
                $super_amount = bcadd($super_amount, $emp_amount, 6);
                $super_eti_bonus = bcadd($super_eti_bonus, $emp_eti_bonus, 6);
                $super_amount_total = bcadd($super_amount_total, $emp_amount_total, 6);
                $super_actual_hours_total = bcadd($super_actual_hours_total, $data_output['actual_hours_total'], 6);
                $super_paid_hours_total = bcadd($super_paid_hours_total, $data_output['paid_hours_total'], 6);
            @endphp
        @endforeach
        @foreach ($data_nk as $data_output)
            @php
                $emp_amount = 0;
                $emp_eti_bonus = 0;
                $emp_amount_total = 0;
            @endphp
            @foreach ($data_output['data'] as $parent_id)
                @foreach ($parent_id as $row)
                    <tr>
                        <td style="border: 2px solid black;">{{ $row['Kronos_job_number'] }}</td>
                        <td style="border: 2px solid black;">{{ $row['parent_id'] }}</td>
                        <td style="border: 2px solid black;">{{ $row['oracle_job_number'] }}</td>
                        <td style="border: 2px solid black;">{{ $row['employee_name'] }}</td>
                        <td style="border: 2px solid black;">{{ $row['emp'] }}</td>
                        <td style="border: 2px solid black;">{{ $row['classification'] }}</td>
                        <td style="border: 2px solid black;">{{ $row['slo_no'] }}</td>
                        @foreach ($row['dates'] as $overtime)
                            @for ($i = 0; $i < 3; $i++)
                                @if (!$overtime['is_holiday'] && $i == 0)
                                    @if ($overtime['basic_hours'] > 0)
                                        <td style="border: 2px solid black;" data-format="0.0">
                                            {{ $overtime['basic_hours'] }}</td>
                                    @else
                                        <td style="border: 2px solid black;"></td>
                                    @endif
                                    @continue
                                @endif
                                @if (!$overtime['is_holiday'])
                                    @if (isset($overtime['overtime_timesheet'][$i - 1]) && $overtime['overtime_timesheet'][$i - 1] != 0)
                                        <td style="border: 2px solid black;" data-format="0.0">
                                            {{ $overtime['overtime_timesheet'][$i - 1] }}</td>
                                    @else
                                        <td style="border: 2px solid black;"></td>
                                    @endif
                                @else
                                    @if (isset($overtime['overtime_timesheet'][$i]) && $overtime['overtime_timesheet'][$i] != 0)
                                        <td style="border: 2px solid black;" data-format="0.0">
                                            {{ $overtime['overtime_timesheet'][$i] }}</td>
                                    @else
                                        <td style="border: 2px solid black;"></td>
                                    @endif
                                @endif
                            @endfor
                        @endforeach
                        <td style="border: 2px solid black;" data-format="#,##0.00">{{ $row['actual_hours_total'] }}
                        </td>
                        <td style="border: 2px solid black;" data-format="#,##0.00">{{ $row['paid_hours_total'] }}
                        </td>
                        <td style="border: 2px solid black;" data-format="#,##">{{ $row['rate'] }}</td>
                        @php
                            $amount = bcmul($row['rate'], $row['paid_hours_total'], 6);
                            // $eti_bonus = $amount * ($temptimesheet["eti_bonus_percentage"]/100);
                            $eti_bonus = bcdiv(bcmul($amount, $temptimesheet['eti_bonus_percentage'], 6), 100, 6);
                            // $total = $amount + $eti_bonus;
                            $total = bcadd($amount, $eti_bonus, 6);

                            //precision
                            $emp_amount = bcadd($emp_amount, $amount, 6);
                            $emp_eti_bonus = bcadd($emp_eti_bonus, $eti_bonus, 6);
                            $emp_amount_total = bcadd($emp_amount_total, $total, 6);
                        @endphp
                        <td style="border: 2px solid black;" data-format="#,##0.00">{{ $amount }}</td>
                        <td style="border: 2px solid black;" data-format="#,##0.00">{{ $eti_bonus }}</td>
                        <td style="border: 2px solid black;" data-format="#,##0.00">{{ $total }}</td>
                    </tr>
                @endforeach
                <tr>
                    @for ($i = 0; $i < 7; $i++)
                        <td style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;"></td>
                    @endfor
                    @foreach ($data_output['total_overtime_hours'] as $total)
                        <td style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;"></td>
                        <td style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;"></td>
                        <td style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;"></td>
                    @endforeach
                    <td data-format="#,##0.00"
                        style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;">
                        {{ $data_output['actual_hours_total'] }}
                    </td>
                    <td data-format="#,##0.00"
                        style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;">
                        {{ $data_output['paid_hours_total'] }}
                    </td>
                    <td style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;"></td>
                    <td data-format="#,##0.00"
                        style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;">
                        {{ $emp_amount }}</td>
                    <td data-format="#,##0.00"
                        style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;">
                        {{ $emp_eti_bonus }}</td>
                    <td data-format="#,##0.00"
                        style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;">
                        {{ $emp_amount_total }}</td>
                </tr>
                <tr>
                </tr>
            @endforeach
            @php
                $super_amount = bcadd($super_amount, $emp_amount, 6);
                $super_eti_bonus = bcadd($super_eti_bonus, $emp_eti_bonus, 6);
                $super_amount_total = bcadd($super_amount_total, $emp_amount_total, 6);
                $super_actual_hours_total = bcadd($super_actual_hours_total, $data_output['actual_hours_total'], 6);
                $super_paid_hours_total = bcadd($super_paid_hours_total, $data_output['paid_hours_total'], 6);
            @endphp
        @endforeach
        <tr>
            @for ($i = 0; $i < 7; $i++)
                <td style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;"></td>
            @endfor
            @foreach ($days as $day)
                <td style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;"></td>
                <td style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;"></td>
                <td style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;"></td>
            @endforeach
            <td data-format="#,##0.00" style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;">
                {{ $super_actual_hours_total }}</td>
            <td data-format="#,##0.00" style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;">
                {{ $super_paid_hours_total }}</td>
            <td style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;"></td>
            <td data-format="#,##0.00" style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;">
                {{ $super_amount }}</td>
            <td data-format="#,##0.00" style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;">
                {{ $super_eti_bonus }}</td>
            <td data-format="#,##0.00" style="background-color: #d5d5d5; font-weight: bold; border: 2px solid black;">
                {{ $super_amount_total }}</td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
    </tbody>


    <thead>
        @php
            $dayCount = count($days);
        @endphp
        <tr>
            <th colspan="{{ $dayCount * 3 + 13 }}"
                style="background-color: lightblue; border: 2px solid #000000; font-weight: bold;">STAFF DAILY RATES
            </th>
        </tr>
        <tr>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">Employee Name</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">EMP</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">Classification</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">Service Order No</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">Kronos Job Charge</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">Parent ID</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">Oracle Job Charge</th>
            <th style="border: 2px solid black" valign="center" align="center" colspan="{{ $dayCount }}">Date
            </th>
            @foreach ($days as $day)
                <th style="border: 2px solid black" valign="center" align="center"></th>
                <th style="border: 2px solid black" valign="center" align="center"></th>
            @endforeach
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">Work Hours</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">Invoice Hours</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">Rate IDR</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">Amount</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">ETI</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">Total</th>

        </tr>
        <tr>
            @foreach ($days as $day)
                <th style="border: 2px solid black" valign="center" align="center">
                    {{ explode(' ', $day['date'])[1] }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @php
            $total_work_hours = 0;
            $total_invoice_hours = 0;
            $total_eti_bonus = 0;
            $total_amount = 0;
            $total_grand_total = 0;
        @endphp
        @foreach ($dailyRates as $dailyRate)
            @php
                $total_work_hours = bcadd($total_work_hours, $dailyRate->work_hours_total, 6);
                $total_invoice_hours = bcadd($total_invoice_hours, $dailyRate->invoice_hours_total, 6);
                $total_amount = bcadd($total_amount, $dailyRate->amount_total, 6);
                $total_eti_bonus = bcadd($total_eti_bonus, $dailyRate->eti_bonus_total, 6);
                $total_grand_total = bcadd($total_grand_total, $dailyRate->grand_total, 6);
            @endphp
            <tr>
                <td style="border: 2px solid black">{{ $dailyRate->employee_name }}</td>
                <td style="border: 2px solid black">{{ $dailyRate->leg_id }}</td>
                <td style="border: 2px solid black">{{ $dailyRate->classification }}</td>
                <td style="border: 2px solid black">{{ $dailyRate->SLO }}</td>
                <td style="border: 2px solid black">{{ $dailyRate->kronos_job_number }}</td>
                <td style="border: 2px solid black">{{ $dailyRate->parent_id }}</td>
                <td style="border: 2px solid black">{{ $dailyRate->oracle_job_number }}</td>
                @foreach ($dailyRate->dailyDetails as $date)
                    @if ($date->value > 0)
                        <td data-format="0.0" style="border: 2px solid black">{{ $date->value }}</td>
                    @else
                        <td style="border: 2px solid black"></td>
                    @endif
                @endforeach
                @foreach ($dailyRate->dailyDetails as $date)
                    <td style="border: 2px solid black"></td>
                    <td style="border: 2px solid black"></td>
                @endforeach
                <td data-format="#,##0.00" style="border: 2px solid black">{{ $dailyRate->work_hours_total }}</td>
                <td data-format="#,##0.00" style="border: 2px solid black">{{ $dailyRate->invoice_hours_total }}</td>
                <td data-format="#,##0.00" style="border: 2px solid black">{{ $dailyRate->rate }}</td>
                <td data-format="#,##0.00" style="border: 2px solid black">{{ $dailyRate->amount_total }}</td>
                <td data-format="#,##0.00" style="border: 2px solid black">{{ $dailyRate->eti_bonus_total }}</td>
                <td data-format="#,##0.00" style="border: 2px solid black">{{ $dailyRate->grand_total }}</td>
            </tr>
        @endforeach
        {{-- total daily rate --}}
        <tr>
            <td style="border: 2px solid black; background-color: #d5d5d5; font-weight: bold;">Total
            </td>
            <td style="border: 2px solid black; background-color: #d5d5d5; font-weight: bold;"></td>
            <td style="border: 2px solid black; background-color: #d5d5d5; font-weight: bold;"></td>
            <td style="border: 2px solid black; background-color: #d5d5d5; font-weight: bold;"></td>
            <td style="border: 2px solid black; background-color: #d5d5d5; font-weight: bold;"></td>
            <td style="border: 2px solid black; background-color: #d5d5d5; font-weight: bold;"></td>
            <td style="border: 2px solid black; background-color: #d5d5d5; font-weight: bold;"></td>
            @foreach ($dailyRate->dailyDetails as $date)
                <td style="border: 2px solid black; background-color: #d5d5d5; font-weight: bold;"></td>
                <td style="border: 2px solid black; background-color: #d5d5d5; font-weight: bold;"></td>
                <td style="border: 2px solid black; background-color: #d5d5d5; font-weight: bold;"></td>
            @endforeach
            <td data-format="#,##0.00" style="border: 2px solid black; background-color: #d5d5d5; font-weight: bold;">
                {{ $total_work_hours }}</td>
            <td data-format="#,##0.00" style="border: 2px solid black; background-color: #d5d5d5; font-weight: bold;">
                {{ $total_invoice_hours }}</td>
            <td data-format="#,##0.00" style="border: 2px solid black; background-color: #d5d5d5; font-weight: bold;">
            </td>
            <td data-format="#,##0.00" style="border: 2px solid black; background-color: #d5d5d5; font-weight: bold;">
                {{ $total_amount }}</td>
            <td data-format="#,##0.00" style="border: 2px solid black; background-color: #d5d5d5; font-weight: bold;">
                {{ $total_eti_bonus }}</td>
            <td data-format="#,##0.00" style="border: 2px solid black; background-color: #d5d5d5; font-weight: bold;">
                {{ $total_grand_total }}</td>
        </tr>
        <tr></tr>
        <tr></tr>

        <tr>
            <td style="border: 2px solid black; font-weight: bold; font-size: 22rem"
                colspan="{{ count($dailyRate->dailyDetails) * 3 + 7 }}">GRAND TOTAL</td>
            <td data-format="#,##0.00" style="border: 2px solid black; font-weight: bold; font-size: 13rem">
                {{ bcadd($total_work_hours, $super_actual_hours_total, 6) }}</td>
            <td data-format="#,##0.00" style="border: 2px solid black; font-weight: bold; font-size: 13rem">
                {{ bcadd($total_invoice_hours, $super_paid_hours_total, 6) }}</td>
            <td data-format="#,##0.00" style="border: 2px solid black; font-weight: bold; font-size: 13rem"></td>
            <td data-format="#,##0.00" style="border: 2px solid black; font-weight: bold; font-size: 13rem">
                {{ bcadd($total_amount, $super_amount, 6) }}
            </td>
            <td data-format="#,##0.00" style="border: 2px solid black; font-weight: bold; font-size: 13rem">
                {{ bcadd($total_eti_bonus, $super_eti_bonus, 6) }}</td>
            <td data-format="#,##0.00" style="border: 2px solid black; font-weight: bold; font-size: 13rem">
                {{ bcadd($total_grand_total, $super_amount_total, 6) }}</td>
        </tr>
    </tbody>
</table>
