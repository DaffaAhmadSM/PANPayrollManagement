<table>
    <tr>

    </tr>
    <tr>

    </tr>
    <tr style="font-weight: bold; font-size: 14rem;">
        <th colspan="8" height="30" align="center" valign="center" style="font-weight: bold">SUMMARY OF INVOICE</th>
    </tr>
    <tr></tr>
    <tr>
        <td style="font-weight: bold;">M/S</td>
        <td> : </td>
        <td colspan="2" style="font-weight: bold; border-bottom: 1px solid gray;">{{ $customerData->name }}</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="2">{{ $customerData->address }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">
            Attn.
        </td>
        <td>
            :
        </td>
        <td colspan="2" style="font-weight: bold; border-bottom: 1px solid gray;">
            Account Payable Department
        </td>
    </tr>
    <tr>
        <td style="font-weight: bold;">
            Contract No.
        </td>
        <td>
            :
        </td>
        <td colspan="2" style="font-weight: bold; border-bottom: 1px solid gray;">
            #####
        </td>
    </tr>
    <tr>
        <td style="font-weight: bold;">
            Date of Invoice
        </td>
        <td>
            :
        </td>
        <td colspan="2" style="font-weight: bold; border-bottom: 1px solid gray;">
            30/09/2024
        </td>
    </tr>
    {{-- Header --}}
    <tr>
        <th style="border: 1px solid #000000;" colspan="2">No.</th>
        <th style="border: 1px solid #000000;">Invoice No.</th>
        <th style="border: 1px solid #000000;">Project Code</th>
        <th style="border: 1px solid #000000;">Job Number</th>
        <th style="border: 1px solid #000000;">Work Amount Hours</th>
        <th style="border: 1px solid #000000;">Amount IDR</th>
        <th style="border: 1px solid #000000;">Total IDR</th>
    </tr>
    {{-- Block --}}
    @php
        $totalWorkHours = 0;
        $totalAmountIDR = 0;
        $countItemGroup = 1;
    @endphp
    @foreach ($dataKronos as $dataParentID)
        @foreach ($dataParentID as $itemGroup)
            @php
                $sumTotalIDR = $itemGroup->sum('total_amount');
                $totalWorkHours += $itemGroup->sum('total_hours');
                $totalAmountIDR += $sumTotalIDR;
                $itemGroup = $itemGroup->values();
            @endphp
            @for ($i = 0; $i < count($itemGroup); $i++)
                {{-- @dd($itemGroup[$i]->oracle_job_number) --}}

                @if ($i == 0)
                    <tr>
                        <td style="border: 1px solid #000000;" rowspan="{{ count($itemGroup) }}" align="center"
                            valign="center">{{ $countItemGroup }}</td>
                        <td style="border: 1px solid #000000;">{{ $i + 1 }}</td>
                        <td style="border: 1px solid #000000;">###</td>
                        <td style="border: 1px solid #000000;">{{ $itemGroup[0]->parent_id }}</td>
                        <td style="border: 1px solid #000000;">{{ $itemGroup[0]->oracle_job_number }}</td>
                        <td style="border: 1px solid #000000;" data-format="#,##0.00">{{ $itemGroup[0]->total_hours }}
                        </td>
                        <td style="border: 1px solid #000000;" data-format="#,##0.00">{{ $itemGroup[0]->total_amount }}
                        </td>
                        <td style="border: 1px solid #000000;" data-format="#,##0.00" rowspan="{{ count($itemGroup) }}"
                            align="center" valign="center">
                            {{ $sumTotalIDR }}</td>
                    </tr>
                    @php
                        $countItemGroup++;
                    @endphp
                @else
                    <tr>
                        <td style="border: 1px solid #000000;">{{ $i + 1 }}</td>
                        <td style="border: 1px solid #000000;">###</td>
                        <td style="border: 1px solid #000000;">{{ $itemGroup[$i]->parent_id }}</td>
                        <td style="border: 1px solid #000000;">{{ $itemGroup[$i]->oracle_job_number }}</td>
                        <td style="border: 1px solid #000000;" data-format="#,##0.00">{{ $itemGroup[$i]->total_hours }}
                        </td>
                        <td style="border: 1px solid #000000;" data-format="#,##0.00">
                            {{ $itemGroup[$i]->total_amount }}</td>
                    </tr>
                @endif
            @endfor
        @endforeach
    @endforeach
    <tr>
        <td style="border: 1px solid #000000; background-color: #DAE4C0"></td>
        <td style="border: 1px solid #000000; background-color: #DAE4C0"></td>
        <td style="border: 1px solid #000000; background-color: #DAE4C0">Total Kronos</td>
        <td style="border: 1px solid #000000; background-color: #DAE4C0"></td>
        <td style="border: 1px solid #000000; background-color: #DAE4C0"></td>
        <td style="border: 1px solid #000000; background-color: #DAE4C0" data-format="#,##0.00">{{ $totalWorkHours }}
        </td>
        <td style="border: 1px solid #000000; background-color: #DAE4C0" data-format="#,##0.00">{{ $totalAmountIDR }}
        </td>
        <td style="border: 1px solid #000000; background-color: #DAE4C0" data-format="#,##0.00">{{ $totalAmountIDR }}
        </td>

    </tr>

    <tr>
    </tr>
    <tr>
        <th style="border: 1px solid #000000; background-color: #DAE4C0" colspan="8" align="center">Non Kronos</th>
    </tr>

    @php
        $totalWorkHoursNK = 0;
        $totalAmountIDRNK = 0;
    @endphp
    @php
        $countItemGroup = 1;
    @endphp
    @foreach ($dataNonKronos['NK-'] as $dataParentID)
        @foreach ($dataParentID as $itemGroup)
            @php
                $sumTotalIDR = $itemGroup->sum('total_amount');
                $totalWorkHoursNK += $itemGroup->sum('total_hours');
                $totalAmountIDRNK += $sumTotalIDR;
                $itemGroup = $itemGroup->values();
            @endphp
            @for ($i = 0; $i < count($itemGroup); $i++)
                @if ($i == 0)
                    <tr>
                        <td style="border: 1px solid #000000;" rowspan="{{ count($itemGroup) }}" align="center"
                            valign="center">{{ $countItemGroup }}
                        </td>
                        <td style="border: 1px solid #000000;">{{ $i + 1 }}</td>
                        <td style="border: 1px solid #000000;">###</td>
                        <td style="border: 1px solid #000000;"></td>
                        <td style="border: 1px solid #000000;">{{ $itemGroup[0]->oracle_job_number }}</td>
                        <td style="border: 1px solid #000000;" data-format="#,##0.00">{{ $itemGroup[0]->total_hours }}
                        </td>
                        <td style="border: 1px solid #000000;" data-format="#,##0.00">{{ $itemGroup[0]->total_amount }}
                        </td>
                        <td style="border: 1px solid #000000;" data-format="#,##0.00" rowspan="{{ count($itemGroup) }}"
                            align="center" valign="center">
                            {{ $sumTotalIDR }}</td>
                    </tr>
                    @php
                        $countItemGroup++;
                    @endphp
                @else
                    <tr>
                        <td style="border: 1px solid #000000;">{{ $i + 1 }}</td>
                        <td style="border: 1px solid #000000;">###</td>
                        <td style="border: 1px solid #000000;"></td>
                        <td style="border: 1px solid #000000;">{{ $itemGroup[$i]->oracle_job_number }}</td>
                        <td style="border: 1px solid #000000;" data-format="#,##0.00">{{ $itemGroup[$i]->total_hours }}
                        </td>
                        <td style="border: 1px solid #000000;" data-format="#,##0.00">
                            {{ $itemGroup[$i]->total_amount }}</td>
                    </tr>
                @endif
            @endfor
        @endforeach
    @endforeach

    @php
        $sumTotalIDR = $dataNonKronos['NK']->sum('total_amount');
        $totalWorkHoursNK += $dataNonKronos['NK']->sum('total_hours');
        $totalAmountIDRNK += $sumTotalIDR;
    @endphp
    @foreach ($dataNonKronos['NK'] as $itemGroup)
        <tr>
            <td style="border: 1px solid #000000;" align="center" valign="center">{{ $countItemGroup }}
            </td>
            <td style="border: 1px solid #000000;"></td>
            <td style="border: 1px solid #000000;">###</td>
            <td style="border: 1px solid #000000;"></td>
            <td style="border: 1px solid #000000;">{{ $itemGroup->oracle_job_number }}</td>
            <td style="border: 1px solid #000000;" data-format="#,##0.00">{{ $itemGroup->total_hours }}
            </td>
            <td style="border: 1px solid #000000;" data-format="#,##0.00">
                {{ $itemGroup->total_amount }}</td>
            <td style="border: 1px solid #000000;" data-format="#,##0.00">
                {{ $itemGroup->total_amount }}</td>
        </tr>

        @php
            $countItemGroup++;
        @endphp
    @endforeach


    @php
        $sumTotalIDR = $dataNonKronos['Daily']->sum('grand_total');
        $totalWorkHoursNK += $dataNonKronos['Daily']->sum('work_hours_total');
        $totalAmountIDRNK += $sumTotalIDR;
    @endphp
    @foreach ($dataNonKronos['Daily'] as $key => $itemGroup)
        @if ($key == 0)
            <tr>
                <td style="border: 1px solid #000000;" rowspan="{{ count($dataNonKronos['Daily']) }}" align="center"
                    valign="center">{{ $countItemGroup }}
                </td>
                <td style="border: 1px solid #000000;"></td>
                <td style="border: 1px solid #000000;">###</td>
                <td style="border: 1px solid #000000;"></td>
                <td style="border: 1px solid #000000;">{{ $itemGroup->oracle_job_number }}</td>
                <td style="border: 1px solid #000000;" data-format="#,##0.00">{{ $itemGroup->work_hours_total }}
                </td>
                <td style="border: 1px solid #000000;" data-format="#,##0.00">{{ $itemGroup->grand_total }}
                </td>
                <td style="border: 1px solid #000000;" data-format="#,##0.00"
                    rowspan="{{ count($dataNonKronos['Daily']) }}" align="center" valign="center">
                    {{ $sumTotalIDR }}</td>
            </tr>
            @php
                $countItemGroup++;
            @endphp
        @else
            <tr>
                <td style="border: 1px solid #000000;"></td>
                <td style="border: 1px solid #000000;">###</td>
                <td style="border: 1px solid #000000;"></td>
                <td style="border: 1px solid #000000;">{{ $itemGroup->oracle_job_number }}</td>
                <td style="border: 1px solid #000000;" data-format="#,##0.00">{{ $itemGroup->work_hours_total }}
                </td>
                <td style="border: 1px solid #000000;" data-format="#,##0.00">
                    {{ $itemGroup->grand_total }}</td>
            </tr>
        @endif
    @endforeach

    <tr>
        <td style="border: 1px solid #000000; background-color: #DAE4C0"></td>
        <td style="border: 1px solid #000000; background-color: #DAE4C0"></td>
        <td style="border: 1px solid #000000; background-color: #DAE4C0">Total Kronos</td>
        <td style="border: 1px solid #000000; background-color: #DAE4C0"></td>
        <td style="border: 1px solid #000000; background-color: #DAE4C0"></td>
        <td style="border: 1px solid #000000; background-color: #DAE4C0" data-format="#,##0.00">
            {{ $totalWorkHoursNK }}
        </td>
        <td style="border: 1px solid #000000; background-color: #DAE4C0" data-format="#,##0.00">
            {{ $totalAmountIDRNK }}
        </td>
        <td style="border: 1px solid #000000; background-color: #DAE4C0" data-format="#,##0.00">
            {{ $totalAmountIDRNK }}
        </td>

    </tr>
    {{-- end of block --}}
</table>
