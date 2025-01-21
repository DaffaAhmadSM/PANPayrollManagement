<table>
    <thead>
        @php
            $dayCount = count($days);
        @endphp
        <tr>
            <th colspan="{{ $dayCount * 3 + 13 }}"
                style="background-color: lightblue; border: 2px solid #000000; font-weight: bold;">DAILY RATES</th>
            valign="center" align="center">STAFF DAILY RATES</th>
        </tr>
        <tr>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">Employee Name</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">EMP</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">Classification</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">Service Order No</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">Kronos Job Charge</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">Parent ID</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">Oracle Job Charge</th>
            <th style="border: 2px solid black" valign="center" align="center" colspan="{{ $dayCount }}">Date</th>
            @foreach ($days as $day)
                <th style="border: 2px solid black" valign="center" align="center"></th>
                <th style="border: 2px solid black" valign="center" align="center"></th>
            @endforeach
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">Work Hours</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">Invoice Hours</th>
            <th style="border: 2px solid black" valign="center" align="center" rowspan="2">Rate IDR</th>
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
        @foreach ($dailyRates as $dailyRate)
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
                        <td style="border: 2px solid black">{{ $date->value }}</td>
                    @else
                        <td style="border: 2px solid black"></td>
                    @endif
                @endforeach
                @foreach ($dailyRate->dailyDetails as $date)
                    <td style="border: 2px solid black"></td>
                    <td style="border: 2px solid black"></td>
                @endforeach
                <td style="border: 2px solid black">{{ $dailyRate->work_hours_total }}</td>
                <td style="border: 2px solid black">{{ $dailyRate->invoice_hours_total }}</td>
                <td style="border: 2px solid black">{{ $dailyRate->rate }}</td>
                <td style="border: 2px solid black">{{ $dailyRate->eti_bonus_total }}</td>
                <td style="border: 2px solid black">{{ $dailyRate->grand_total }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
