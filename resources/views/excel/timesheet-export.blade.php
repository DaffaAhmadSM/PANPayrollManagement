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
        @foreach ($output as $data_output)
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
                <td style="background-color: #d5d5d5; border: 2px solid black;"></td>
            @endfor
            @foreach ($days as $day)
                <td style="background-color: #d5d5d5; border: 2px solid black;"></td>
                <td style="background-color: #d5d5d5; border: 2px solid black;"></td>
                <td style="background-color: #d5d5d5; border: 2px solid black;"></td>
            @endforeach
            <td data-format="#,##0.00" style="background-color: #d5d5d5; border: 2px solid black;">
                {{ $super_actual_hours_total }}</td>
            <td data-format="#,##0.00" style="background-color: #d5d5d5; border: 2px solid black;">
                {{ $super_paid_hours_total }}</td>
            <td style="background-color: #d5d5d5; border: 2px solid black;"></td>
            <td data-format="#,##0.00" style="background-color: #d5d5d5; border: 2px solid black;">
                {{ $super_amount }}</td>
            <td data-format="#,##0.00" style="background-color: #d5d5d5; border: 2px solid black;">
                {{ $super_eti_bonus }}</td>
            <td data-format="#,##0.00" style="background-color: #d5d5d5; border: 2px solid black;">
                {{ $super_amount_total }}</td>
        </tr>
    </tbody>
</table>
