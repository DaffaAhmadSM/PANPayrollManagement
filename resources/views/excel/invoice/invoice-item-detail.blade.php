<tr>
    <th colspan="4">PT. PAN NUSANTARA SENTOSA</th>
</tr>
<tr>
    <th colspan="4">TIMESHEET SUMMARY</th>
</tr>
<tr>

</tr>
<tr>
    <th colspan="2">Invoice No.</th>
    <th>:</th>
    <th>#####/#####/##/##</th>
</tr>
<tr>
    <th colspan="2">Period</th>
    <th>:</th>
    <th>##### ##,####</th>
</tr>
<tr>
    <th colspan="2">Job Charge</th>
    <th>:</th>
    <th>{{ $oracle_job_number }}</th>
</tr>



<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="border: 2px solid black; font-weight: bold;" rowspan="2" align="center" valign="center"
                width="4">No.</th>

            <th style="border: 2px solid black; font-weight: bold;" rowspan="2" align="center" valign="center"
                colspan="2" width="30">Name</th>

            <th style="border: 2px solid black; font-weight: bold; word-wrap: break-word" rowspan="2" align="center"
                valign="center" width="11">
                I.D No.
            </th>
            <th style="border: 2px solid black; font-weight: bold;" rowspan="2" align="center" valign="center"
                width="25">
                Classification</th>
            <th style="border: 2px solid black; font-weight: bold; word-wrap: break-word" rowspan="2" align="center"
                valign="center" width="11">
                Service
                Order No.</th>
            @foreach ($days1 as $day)
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
            <th style="border: 2px solid black; font-weight: bold; word-wrap: break-word; font-size: 6rem;"
                rowspan="2" align="center" valign="center" width="9">
                Actual
                Work
                Hours</th>
            <th style="border: 2px solid black; font-weight: bold; word-wrap: break-word; font-size: 6rem;"
                rowspan="2" align="center" valign="center" width="9">
                Invoice
                Work
                Hours</th>
            <th style="border: 2px solid black; font-weight: bold; word-wrap: break-word; font-size: 7rem;"
                rowspan="2" align="center" valign="center" width="10">
                Rate
                (IDR)
            </th>
            <th style="border: 2px solid black; font-weight: bold; font-size: 7rem;" rowspan="2" align="center"
                valign="center">
                Amount
                (IDR)</th>
        </tr>
        <tr>
            @foreach ($days1 as $day)
                @if ($day['is_holiday'])
                    <th style="border: 2px solid black; font-weight: bold;" width="5" align="center"
                        valign="center">2</th>
                    <th style="border: 2px solid black; font-weight: bold;" width="5" align="center"
                        valign="center">3</th>
                    <th style="border: 2px solid black; font-weight: bold;" width="5" align="center"
                        valign="center">4</th>
                @else
                    <th style="border: 2px solid black; font-weight: bold;" width="5" align="center"
                        valign="center">1</th>
                    <th style="border: 2px solid black; font-weight: bold;" width="5" align="center"
                        valign="center">1.5</th>
                    <th style="border: 2px solid black; font-weight: bold;" width="5" align="center"
                        valign="center">2</th>
                @endif
            @endforeach
        </tr>
    </thead>
    <tbody>
        @php
            $grandTotalActualHours = 0;
            $grandTotalPaidHours = 0;
            $grandTotalAmount = 0;
            $total_eti_bonus = 0;
            $no = 1;
        @endphp
        @foreach ($data1 as $row)
            @if ((int) $row['actual_hours_total'] > 0)
                <tr>
                    <td style="border: 2px solid black;">{{ $no }}</td>
                    <td style="border: 2px solid black;" colspan="2">{{ $row['employee_name'] }}</td>
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
                    <td style="border: 2px solid black;" data-format="#,##0.00">
                        {{ $row['actual_hours_total'] }}
                    </td>
                    <td style="border: 2px solid black;" data-format="#,##0.00">
                        {{ $row['paid_hours_total'] }}
                    </td>
                    <td style="border: 2px solid black;" data-format="#,##">{{ $row['rate'] }}</td>
                    @php
                        $amount = bcmul($row['rate'], $row['paid_hours_total'], 6);
                        // $eti_bonus = $amount * ($temptimesheet['eti_bonus_percentage'] / 100);
                        $eti_bonus = bcdiv(bcmul($amount, $temptimesheet['eti_bonus_percentage'], 6), 100, 6);
                        $total_eti_bonus = bcadd($total_eti_bonus, $eti_bonus, 6);
                        // $total = $amount + $eti_bonus;
                        // $total = bcadd($amount, $eti_bonus, 6);

                        //precision
                        // $emp_amount = bcadd($emp_amount, $amount, 6);
                        // $emp_eti_bonus = bcadd($emp_eti_bonus, $eti_bonus, 6);
                        // $emp_amount_total = bcadd($emp_amount_total, $total, 6);

                        $grandTotalActualHours = bcadd($grandTotalActualHours, $row['actual_hours_total'], 6);
                        $grandTotalPaidHours = bcadd($grandTotalPaidHours, $row['paid_hours_total'], 6);
                        $grandTotalAmount = bcadd($grandTotalAmount, $amount, 6);

                    @endphp
                    <td style="border: 2px solid black;" data-format="#,##0.00">{{ $amount }}</td>
                </tr>

                @php
                    $no++;
                @endphp
            @endif
        @endforeach
        <tr>
            <td style="border: 2px solid black; font-weight: bold;" colspan="{{ 6 + count($days1) * 3 }}">Total I
            </td>
            <td style="border: 2px solid black; font-weight: bold;" data-format="#,##0.00">
                {{ $grandTotalActualHours }}</td>
            <td style="border: 2px solid black; font-weight: bold;" data-format="#,##0.00">{{ $grandTotalPaidHours }}
            </td>
            <td style="border: 2px solid black;"></td>
            <td style="border: 2px solid black; font-weight: bold;" data-format="#,##0.00">{{ $grandTotalAmount }}
            </td>
        </tr>
        <tr></tr>
    </tbody>
</table>

<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="border: 2px solid black; font-weight: bold;" rowspan="2" align="center" valign="center"
                width="4">No.</th>

            <th style="border: 2px solid black; font-weight: bold;" rowspan="2" align="center" valign="center"
                colspan="2" width="30">Name</th>

            <th style="border: 2px solid black; font-weight: bold; word-wrap: break-word" rowspan="2"
                align="center" valign="center" width="11">
                I.D No.
            </th>
            <th style="border: 2px solid black; font-weight: bold;" rowspan="2" align="center" valign="center"
                width="25">
                Classification</th>
            <th style="border: 2px solid black; font-weight: bold; word-wrap: break-word" rowspan="2"
                align="center" valign="center" width="11">
                Service
                Order No.</th>
            @foreach ($days2 as $day)
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

            @for ($i = 0; $i < count($days1) - count($days2); $i++)
                <th style="border: 2px solid black; font-weight: bold;" colspan="3" align="center"
                    valign="center"></th>
            @endfor
            <th style="border: 2px solid black; font-weight: bold; word-wrap: break-word; font-size: 6rem;"
                rowspan="2" align="center" valign="center" width="9">
                Actual
                Work
                Hours</th>
            <th style="border: 2px solid black; font-weight: bold; word-wrap: break-word; font-size: 6rem;"
                rowspan="2" align="center" valign="center" width="9">
                Invoice
                Work
                Hours</th>
            <th style="border: 2px solid black; font-weight: bold; word-wrap: break-word; font-size: 7rem;"
                rowspan="2" align="center" valign="center" width="10">
                Rate
                (IDR)
            </th>
            <th style="border: 2px solid black; font-weight: bold; font-size: 7rem;" rowspan="2" align="center"
                valign="center">
                Amount
                (IDR)</th>
        </tr>
        <tr>
            @foreach ($days2 as $day)
                @if ($day['is_holiday'])
                    <th style="border: 2px solid black; font-weight: bold;" width="5" align="center"
                        valign="center">2</th>
                    <th style="border: 2px solid black; font-weight: bold;" width="5" align="center"
                        valign="center">3</th>
                    <th style="border: 2px solid black; font-weight: bold;" width="5" align="center"
                        valign="center">4</th>
                @else
                    <th style="border: 2px solid black; font-weight: bold;" width="5" align="center"
                        valign="center">1</th>
                    <th style="border: 2px solid black; font-weight: bold;" width="5" align="center"
                        valign="center">1.5</th>
                    <th style="border: 2px solid black; font-weight: bold;" width="5" align="center"
                        valign="center">2</th>
                @endif
            @endforeach

            @for ($i = 0; $i < count($days1) - count($days2); $i++)
                <th style="border: 2px solid black; font-weight: bold;" width="5" align="center"
                    valign="center"></th>
                <th style="border: 2px solid black; font-weight: bold;" width="5" align="center"
                    valign="center"></th>
                <th style="border: 2px solid black; font-weight: bold;" width="5" align="center"
                    valign="center"></th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @php
            $grandTotalActualHours2 = 0;
            $grandTotalPaidHours2 = 0;
            $grandTotalAmount2 = 0;
            $no = 1;
        @endphp
        @foreach ($data2 as $row)
            @if ((int) $row['actual_hours_total'] > 0)
                <tr>
                    <td style="border: 2px solid black;">{{ $no }}</td>
                    <td style="border: 2px solid black;" colspan="2">{{ $row['employee_name'] }}</td>
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
                    @for ($i = 0; $i < count($days1) - count($days2); $i++)
                        <td style="border: 2px solid black;"></td>
                        <td style="border: 2px solid black;"></td>
                        <td style="border: 2px solid black;"></td>
                    @endfor
                    <td style="border: 2px solid black;" data-format="#,##0.00">
                        {{ $row['actual_hours_total'] }}
                    </td>
                    <td style="border: 2px solid black;" data-format="#,##0.00">
                        {{ $row['paid_hours_total'] }}
                    </td>
                    <td style="border: 2px solid black;" data-format="#,##">{{ $row['rate'] }}</td>
                    @php
                        $amount = bcmul($row['rate'], $row['paid_hours_total'], 6);
                        // $eti_bonus = $amount * ($temptimesheet['eti_bonus_percentage'] / 100);
                        $eti_bonus = bcdiv(bcmul($amount, $temptimesheet['eti_bonus_percentage'], 6), 100, 6);
                        $total_eti_bonus = bcadd($total_eti_bonus, $eti_bonus, 6);
                        // $total = $amount + $eti_bonus;

                        //precision
                        // $emp_amount = bcadd($emp_amount, $amount, 6);
                        // $emp_eti_bonus = bcadd($emp_eti_bonus, $eti_bonus, 6);
                        // $emp_amount_total = bcadd($emp_amount_total, $total, 6);

                        $grandTotalActualHours2 = bcadd($grandTotalActualHours2, $row['actual_hours_total'], 6);
                        $grandTotalPaidHours2 = bcadd($grandTotalPaidHours2, $row['paid_hours_total'], 6);
                        $grandTotalAmount2 = bcadd($grandTotalAmount2, $amount, 6);

                    @endphp
                    <td style="border: 2px solid black;" data-format="#,##0.00">{{ $amount }}</td>
                </tr>

                @php
                    $no++;
                @endphp
            @endif
        @endforeach
        <tr>
            <td style="border: 2px solid black; font-weight: bold;" colspan="{{ 6 + count($days1) * 3 }}">Total II
            </td>
            <td style="border: 2px solid black; font-weight: bold;" data-format="#,##0.00">
                {{ $grandTotalActualHours2 }}</td>
            <td style="border: 2px solid black; font-weight: bold;" data-format="#,##0.00">
                {{ $grandTotalPaidHours2 }}</td>
            <td style="border: 2px solid black;"></td>
            <td style="border: 2px solid black; font-weight: bold;" data-format="#,##0.00">{{ $grandTotalAmount2 }}
            </td>
        </tr>
    </tbody>
</table>

<tr>
    <td style="border: 4px solid black; font-weight: bold;"colspan="{{ 6 + count($days1) * 3 }}">Grand Total (Total I
        + Total II)</td>
    <td style="border: 2px solid black; font-weight: bold;" data-format="#,##0.00">
        {{ $grandTotalActualHours2 + $grandTotalActualHours }}</td>
    <td style="border: 2px solid black; font-weight: bold;" data-format="#,##0.00">
        {{ $grandTotalPaidHours2 + $grandTotalPaidHours }}
    </td>
    <td style="border: 2px solid black;"></td>
    <td style="border: 2px solid black; font-weight: bold;" data-format="#,##0.00">
        {{ $grandTotalAmount2 + $grandTotalAmount }}</td>
</tr>

<tr>
    <td height="4"></td>
</tr>

<tr>
    <td colspan="{{ 6 + count($days1) * 3 }}"></td>
    <td style="border: 2px solid black; font-weight: bold;" colspan="3">E T I Bonus
        {{ $temptimesheet->eti_bonus_percentage }}%</td>
    <td style="border: 2px solid black; font-weight: bold;" data-format="#,##0.00">{{ $total_eti_bonus }}</td>
</tr>

<tr>
    <td colspan="{{ 6 + count($days1) * 3 }}"></td>
    <td style="border: 2px solid black; font-weight: bold;" colspan="3">Total Invoice</td>
    <td style="border: 2px solid black; font-weight: bold;" data-format="#,##0.00">
        {{ $total_eti_bonus + $grandTotalAmount + $grandTotalAmount2 }}</td>
</tr>

<tr>

</tr>

<tr>
    <td colspan="25"></td>
    <td>Prepared by:</td>
</tr>
<tr>
    <td colspan="25"></td>
    <td>PT. Pan Nusantara Sentosa</td>
</tr>
<tr></tr>
<tr></tr>
<tr></tr>
<tr></tr>
<tr></tr>
<tr>
    <td colspan="25"></td>
    <td>Signature</td>
    <td></td>
    <td>:</td>
</tr>
<tr>
    <td colspan="25"></td>
    <td>Name</td>
    <td></td>
    <td colspan="7">: Riana Hutajulu</td>
</tr>
<tr>
    <td colspan="25"></td>
    <td>Date</td>
    <td></td>
    <td colspan="7">:</td>
</tr>
