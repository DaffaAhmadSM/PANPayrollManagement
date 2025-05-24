<tr>
    <th colspan="4">PT. PAN NUSANTARA SENTOSA</th>
</tr>
<tr>
    <th colspan="4">TIMESHEET SUMMARY</th>
</tr>
<tr>

</tr>
<tr>
    <th>Invoice No.</th>
    <th colspan="2">=": " &amp; Setup!C5 &amp;
        Setup!D5+{{ $count - 1 }} &amp; " " &amp;" " &amp;" " &amp;" " &amp;" " &amp;
        Setup!C4</th>
</tr>
<tr>
    <th>Period</th>
    <th colspan="2">=": " &amp; Setup!C3</th>
</tr>
<tr>
    <th>Job Charge</th>
    <th colspan="2">: {{ $oracle_job_number }}</th>
</tr>

@php
    $daycount = count($days);
@endphp



<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead>
        @php
            $dayCount = count($days);
        @endphp
        <tr>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2" width="12">No</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2" width="32">Name</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">ID. NO.</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2" width="25">
                Classification</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">SLO No</th>
            <th style="border: 2px solid black" valign="center" align="center" colspan="{{ $dayCount }}">
            </th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2" width="12">Work Hours
            </th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2" width="12">Work Days
            </th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2" width="15">Rate</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2" width="21">Amount</th>

        </tr>
        <tr>
            @foreach ($days as $day)
                @if ($day['is_holiday'])
                    <th style="border: 2px solid black; background-color: #f29a6e;" valign="center" align="center">
                        {{ $day['date'] }}</th>
                @elseif ($day['theday'] == 'Friday')
                    <th style="border: 2px solid black; background-color: aqua;" valign="center" align="center">
                        {{ $day['date'] }}</th>
                @else
                    <th style="border: 2px solid black" valign="center" align="center">
                        {{ $day['date'] }}</th>
                @endif
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
                <td style="border: 2px solid black">{{ 1 }}</td>
                <td style="border: 2px solid black">{{ $dailyRate->employee_name }}</td>
                <td style="border: 2px solid black">{{ $dailyRate->leg_id }}</td>
                <td style="border: 2px solid black">{{ $dailyRate->classification }}</td>
                <td style="border: 2px solid black">{{ $dailyRate->SLO }}</td>
                @foreach ($dailyRate->dailyDetails as $date)
                    @if ($date->value > 0)
                        <td data-format="0.0" style="border: 2px solid black">{{ $date->value }}</td>
                    @else
                        <td style="border: 2px solid black"></td>
                    @endif
                @endforeach
                <td data-format="#,##0.00" style="border: 2px solid black">{{ $dailyRate->work_hours_total }}</td>
                <td data-format="#,##0.00" style="border: 2px solid black">{{ $dailyRate->invoice_hours_total }}</td>
                <td data-format="#,##0.00" style="border: 2px solid black">{{ $dailyRate->rate }}</td>
                <td data-format="#,##0.00" style="border: 2px solid black">{{ $dailyRate->amount_total }}</td>
                {{-- <td data-format="#,##0.00" style="border: 2px solid black">{{ $dailyRate->eti_bonus_total }}</td>
                <td data-format="#,##0.00" style="border: 2px solid black">{{ $dailyRate->grand_total }}</td> --}}
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
            @foreach ($dailyRate->dailyDetails as $date)
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
        </tr>
        <tr></tr>
    </tbody>
</table>


<tr>
    <td height="4"></td>
</tr>

<tr>
    <td colspan="{{ 6 + $daycount }}"></td>
    <td style="border: 2px solid black; font-weight: bold;" colspan="2">E T I Bonus
        {{ $temptimesheet->eti_bonus_percentage }}%</td>
    <td style="border: 2px solid black; font-weight: bold;" data-format="#,##0.00">{{ $dailyRate->eti_bonus_total }}
    </td>
</tr>

<tr>
    <td colspan="{{ 6 + $daycount }}"></td>
    <td style="border: 2px solid black; font-weight: bold;" colspan="2">Total Invoice</td>
    <td style="border: 2px solid black; font-weight: bold;" data-format="#,##0.00">
        {{ $dailyRate->grand_total }}</td>
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
    <td>:</td>
</tr>
<tr>
    <td colspan="25"></td>
    <td>Name</td>
    <td colspan="7">=":" &amp; Setup!C2</td>
</tr>
<tr>
    <td colspan="25"></td>
    <td>Date</td>
    <td colspan="7">=":" &amp; Setup!C1</td>
</tr>
