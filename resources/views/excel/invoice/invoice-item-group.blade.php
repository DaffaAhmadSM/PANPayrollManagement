<table>
    <tr>

    </tr>

    <tr>
        <th colspan="10" align="center" valign="center" height="30">INVOICE</th>
    </tr>
    <tr>

    </tr>
    <tr>
        <td style="font-weight: bold;" width="5">TO</td>
        <td> : </td>
        <td colspan="3" style="font-weight: bold; border-bottom: 1px solid gray;">{{ $customerData->name }}</td>
        <td width="30"></td>
        <td>Invoice No</td>
        <td>:</td>
        <td>####</td>
        <td>###/##/###</td>
    </tr>

    <tr>
        <td></td>
        <td></td>
        <td colspan="3">{{ $customerData->address }}</td>
        <td></td>
        <td>Date of Invoice</td>
        <td>:</td>
        <td colspan="2">##/##/####</td>
    </tr>

    <tr>
        <td></td>
        <td></td>
        <td colspan="3"></td>
        <td></td>
        <td>Contract No</td>
        <td>:</td>
        <td colspan="2">###/####/###/###/####</td>
    </tr>

    <tr>
        <td style="font-style: italic">Attn</td>
        <td style="font-style: italic">:</td>
        <td style="font-style: italic" colspan="3">Account Payable Department</td>
        <td></td>
        <td>Service Order No.</td>
        <td>:</td>
        <td style="font-weight: bold;">Attachment</td>
    </tr>
    <tr height="6"></tr>
    <thead>
        <tr>
            <th style="font-weight: bold; border: 1px solid black;">
                N0.
            </th>
            <th style="font-weight: bold; border: 1px solid black;" colspan="2">
                Qty
            </th>
            <th style="font-weight: bold; border: 1px solid black;" colspan="4">
                Description
            </th>
            <th style="font-weight: bold; border: 1px solid black;">
                Unit Price
            </th>
            <th style="font-weight: bold; border: 1px solid black;" colspan="2">
                Amount IDR
            </th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td style="border-left: 1px solid black; border-right: 1px solid black"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
            <td style="border-right: 1px solid black" colspan="4">Supply Of Manpower</td>
            <td style="border-right: 1px solid black"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
        </tr>
        <tr>
            <td style="border-left: 1px solid black; border-right: 1px solid black"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
            <td>Period :</td>
            <td colspan="2">###</td>
            <td style="border-right: 1px solid black"></td>
            <td style="border-right: 1px solid black"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
        </tr>

        <tr>
            <td style="border-left: 1px solid black; border-right: 1px solid black"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
            <td>Project Code :</td>
            <td colspan="2">{{ $prCode }}</td>
            <td style="border-right: 1px solid black"></td>
            <td style="border-right: 1px solid black"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
        </tr>

        <tr>
            <td style="border-left: 1px solid black; border-right: 1px solid black"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
            <td style="border-right: 1px solid black" colspan="4">Job Charge :</td>
            <td style="border-right: 1px solid black"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
        </tr>

        @php
            $count = 1;
        @endphp
        @foreach ($data as $dataItem)
            <tr>
                <td style="border-left: 1px solid black; border-right: 1px solid black">{{ $count }}</td>
                <td style="border-right: 1px solid black" colspan="2">{{ $dataItem->total_hours }}</td>
                <td style="border-right: 1px solid black" colspan="4">{{ $dataItem->oracle_job_number }}</td>
                <td style="border-right: 1px solid black">Attachment</td>
                <td style="border-right: 1px solid black" colspan="2">{{ $dataItem->total_amount }}</td>
            </tr>
            @php
                $count++;
            @endphp
        @endforeach
    </tbody>
</table>
