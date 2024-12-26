<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="border: 2px solid black;" rowspan="2" align="center" valign="center" width="25">Kronos Job Charge</th>
            <th style="border: 2px solid black;" rowspan="2" align="center" valign="center" width="25">Parent ID</th>
            <th style="border: 2px solid black;" rowspan="2" align="center" valign="center" width="25">Oracle Job Charge</th>
            <th style="border: 2px solid black;" rowspan="2" align="center" valign="center" width="25">Employee Name</th>
            <th style="border: 2px solid black;" rowspan="2" align="center" valign="center" width="25">Emp.</th>
            <th style="border: 2px solid black;" rowspan="2" align="center" valign="center" width="25">Classification</th>
            <th style="border: 2px solid black;" rowspan="2" align="center" valign="center" width="25">Service Order No.</th>
            @foreach ($days as $day)
                @if ($day['is_holiday'])
                    <th colspan="3" align="center" valign="center" style="background-color: #f29a6e; border: 2px solid black;">{{$day['date']}}</th>
                @else
                    <th style="border: 2px solid black;" colspan="3" align="center" valign="center">{{$day['date']}}</th>
                @endif
            @endforeach
            <th style="border: 2px solid black;" rowspan="2" align="center">Actual Hours</th>
            <th style="border: 2px solid black;" rowspan="2" align="center">Invoice Hours</th>
            <th style="border: 2px solid black;" rowspan="2" align="center">Rate</th>
            <th style="border: 2px solid black;" rowspan="2" align="center">Amount (IDR)</th>
            <th style="border: 2px solid black;" rowspan="2" align="center">ETI Bonus {{$temptimesheet["eti_bonus_percentage"]}}%</th>
            <th style="border: 2px solid black;" rowspan="2" align="center">Total Amount</th>
        </tr>
        <tr>
            @foreach ($days as $day)
                @if ($day['is_holiday'])
                    <th style="border: 2px solid black;">2</th>
                    <th style="border: 2px solid black;">3</th>
                    <th style="border: 2px solid black;">4</th>
                @else
                    <th style="border: 2px solid black;">1</th>
                    <th style="border: 2px solid black;">1.5</th>
                    <th style="border: 2px solid black;">2</th>
                @endif
            @endforeach
        </tr>
    </thead>
    <tbody>
    @foreach ($output as $key => $depart)
    <tr>
        <td style="background-color: #44bd04;">{{$key}}</td>
        <td style="background-color: #44bd04;"></td>
        <td style="background-color: #44bd04;"></td>
        <td style="background-color: #44bd04;"></td>
        <td style="background-color: #44bd04;"></td>
        <td style="background-color: #44bd04;"></td>
        <td style="background-color: #44bd04;"></td>
        @foreach ($days as $day)
            <td style="background-color: #44bd04;"></td>
        @endforeach
        <td style="background-color: #44bd04;"></td>
        <td style="background-color: #44bd04;"></td>
        <td style="background-color: #44bd04;"></td>
        <td style="background-color: #44bd04;"></td>
        <td style="background-color: #44bd04;"></td>
        <td style="background-color: #44bd04;"></td>
    </tr>
    <tr></tr>
        @php
            $super_amount = 0;
            $super_eti_bonus = 0;
            $super_amount_total = 0;
            $super_actual_hours_total = 0;
            $super_paid_hours_total = 0;
        @endphp
        @foreach ($depart as $data_output)
            @php
                $emp_amount = 0;
                $emp_eti_bonus = 0;
                $emp_amount_total = 0;
            @endphp
            @foreach ($data_output["data"] as $row)
                <tr>
                    <td>{{$row['Kronos_job_number']}}</td>
                    <td>{{$row['parent_id']}}</td>
                    <td>{{$row['oracle_job_number']}}</td>
                    <td>{{$row['employee_name']}}</td>
                    <td>{{$row['emp']}}</td>
                    <td>{{$row['classification']}}</td>
                    <td>{{$row['slo_no']}}</td>
                    @foreach ($row['dates'] as $overtime)
                        @for ($i = 0; $i < 3; $i++)
                            @if (!$overtime['is_holiday'] && $i == 0)
                                @if ($overtime['basic_hours'] > 0)
                                    <td data-format="0.00">{{$overtime['basic_hours']}}</td>

                                @else
                                    <td></td>
                                @endif
                            @continue
                            @endif
                            @if (!$overtime['is_holiday'])
                                @if (isset($overtime['overtime_timesheet'][$i-1]) && $overtime['overtime_timesheet'][$i-1] != 0)
                                    <td data-format="0.00">{{$overtime['overtime_timesheet'][$i-1]}}</td>
                                @else
                                    <td></td>
                                @endif
                            @else
                                @if (isset($overtime['overtime_timesheet'][$i]) && $overtime['overtime_timesheet'][$i] != 0)
                                    <td data-format="0.00">{{$overtime['overtime_timesheet'][$i]}}</td>
                                @else
                                    <td></td>
                                @endif
                            @endif
                        @endfor
                    @endforeach
                    <td data-format="0.00">{{$row['actual_hours_total']}}</td>
                    <td data-format="0.00">{{$row['paid_hours_total']}}</td>
                    <td data-format="0.00">{{$row['rate']}}</td>
                    @php
                        $amount = bcmul($row['rate'] , $row['paid_hours_total'], 2);
                        // $eti_bonus = $amount * ($temptimesheet["eti_bonus_percentage"]/100);
                        $eti_bonus = bcmul($amount, bcdiv($temptimesheet["eti_bonus_percentage"], 100, 2), 2);
                        // $total = $amount + $eti_bonus;
                        $total = bcadd($amount, $eti_bonus, 2);

                        //precision
                        $emp_amount = bcadd($emp_amount, $amount, 2);
                        $emp_eti_bonus = bcadd($emp_eti_bonus, $eti_bonus, 2);
                        $emp_amount_total = bcadd($emp_amount_total, $total, 2);
                    @endphp
                    <td data-format="0.00">{{$amount}}</td>
                    <td data-format="0.00">{{$eti_bonus}}</td>
                    <td data-format="0.00">{{$total}}</td>
                </tr>

            @endforeach
            <tr>
                @for ($i = 0; $i < 7; $i++)
                    <td style="background-color: #d5d5d5;"></td>
                @endfor
                @foreach ($data_output["total_overtime_hours"] as $total)
                <td style="background-color: #d5d5d5;"></td>
                <td style="background-color: #d5d5d5;"></td>
                <td data-format="0.00" style="background-color: #d5d5d5;">{{$total}}</td>
                @endforeach
                <td data-format="0.00" style="background-color: #d5d5d5;">{{$data_output["actual_hours_total"]}}</td>
                <td data-format="0.00" style="background-color: #d5d5d5;">{{$data_output["paid_hours_total"]}}</td>
                <td style="background-color: #d5d5d5;"></td>
                <td data-format="0.00" style="background-color: #d5d5d5;">{{$emp_amount}}</td>
                <td data-format="0.00" style="background-color: #d5d5d5;">{{$emp_eti_bonus}}</td>
                <td data-format="0.00" style="background-color: #d5d5d5;">{{$emp_amount_total}}</td>
            </tr>
            <tr>
            </tr>
            @php
                $super_amount = bcadd($super_amount, $emp_amount, 2);
                $super_eti_bonus = bcadd($super_eti_bonus, $emp_eti_bonus, 2);
                $super_amount_total = bcadd($super_amount_total, $emp_amount_total, 2);
                $super_actual_hours_total = bcadd($super_actual_hours_total, $data_output["actual_hours_total"], 2);
                $super_paid_hours_total = bcadd($super_paid_hours_total, $data_output["paid_hours_total"], 2);
            @endphp
        @endforeach
    @endforeach
           
        <tr>
            @for ($i = 0; $i < 7; $i++)
                <td style="background-color: #d5d5d5;"></td>
            @endfor
            @foreach ($days as $day)
                <td style="background-color: #d5d5d5;"></td>
                <td style="background-color: #d5d5d5;"></td>
                <td style="background-color: #d5d5d5;"></td>
            @endforeach
            <td data-format="0.00" style="background-color: #d5d5d5;">{{$super_actual_hours_total}}</td>
            <td data-format="0.00" style="background-color: #d5d5d5;">{{$super_paid_hours_total}}</td>
            <td style="background-color: #d5d5d5;"></td>
            <td data-format="0.00" style="background-color: #d5d5d5;">{{$super_amount}}</td>
            <td data-format="0.00" style="background-color: #d5d5d5;">{{$super_eti_bonus}}</td>
            <td data-format="0.00" style="background-color: #d5d5d5;">{{$super_amount_total}}</td>
        </tr>
    </tbody>
</table>
