<table>
    <tr>
    </tr>

    <tr>
        <th colspan="10" align="center" valign="center" height="30"
            style="font-weight: bold; text-decoration: underline; font-size: 24rem;">INVOICE</th>
    </tr>
    <tr>

    </tr>
    <tr>
        <td style="font-weight: bold;" width="5">TO</td>
        <td> : </td>
        <td colspan="2" style="font-weight: bold; border-bottom: 1px solid gray;">
            {{ $customerData->name }}</td>
        <td width="7"></td>
        <td width="25">Invoice No</td>
        <td>:</td>
        <td width="8">=Setup!C5 &amp;
            Setup!D5+{{ $count - 1 }}</td>
        <td>=Setup!C4</td>
        <td width="4"></td>
    </tr>

    <tr>
        <td></td>
        <td></td>
        <td colspan="2" rowspan="2" style="word-wrap: break-word;">{{ $customerData->address }}
        </td>
        <td></td>
        <td>Date of Invoice</td>
        <td>:</td>
        <td colspan="2">=Setup!C1</td>
    </tr>

    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>Contract No</td>
        <td>:</td>
        <td colspan="2">=Setup!C6</td>
    </tr>

    <tr>
        <td style="font-style: italic">Attn</td>
        <td style="font-style: italic">:</td>
        <td style="font-style: italic" colspan="2">Account Payable Department</td>
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
            <th style="font-weight: bold; border: 1px solid black;" colspan="3">
                Description
            </th>
            <th style="font-weight: bold; border: 1px solid black;" colspan="2">
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
            <td style="border-right: 1px solid black" colspan="3">Supply Of Manpower</td>
            <td style="border-right: 1px solid black" colspan="2"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
        </tr>
        <tr>
            <td style="border-left: 1px solid black; border-right: 1px solid black"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
            <td>Period :</td>
            <td style="border-right: 1px solid black" colspan="2">=Setup!C3</td>
            <td style="border-right: 1px solid black" colspan="2"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
        </tr>

        <tr>
            <td style="border-left: 1px solid black; border-right: 1px solid black"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
            <td>Project Code :</td>
            <td style="border-right: 1px solid black" colspan="2">{{ $prCode }}</td>
            <td style="border-right: 1px solid black" colspan="2"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
        </tr>

        <tr>
            <td style="border-left: 1px solid black; border-right: 1px solid black"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
            <td style="border-right: 1px solid black" colspan="3">Job Charge :</td>
            <td style="border-right: 1px solid black" colspan="2"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
        </tr>

        @php
            $count = 1;
            $total = 0;
        @endphp
        @foreach ($data as $dataItem)
            <tr>
                <td style="border-left: 1px solid black; border-right: 1px solid black">{{ $count }}</td>
                <td style="border-right: 1px solid black" colspan="2" data-format="#,##0.00">
                    {{ $dataItem->total_hours }}</td>
                <td style="border-right: 1px solid black" colspan="3">{{ $dataItem->oracle_job_number }}</td>
                <td style="border-right: 1px solid black" colspan="2">Attachment</td>
                <td style="border-right: 1px solid black" colspan="2" data-format="#,##0.00">
                    {{ $dataItem->total_amount }}</td>
            </tr>
            @php
                $count++;
                $total += $dataItem->total_amount;
            @endphp
        @endforeach
        @for ($i = 0; $i < 15 - count($data); $i++)
            <tr>
                <td style="border-left: 1px solid black; border-right: 1px solid black"></td>
                <td style="border-right: 1px solid black" colspan="2"></td>
                <td style="border-right: 1px solid black" colspan="3"></td>
                <td style="border-right: 1px solid black" colspan="2"></td>
                <td style="border-right: 1px solid black" colspan="2"></td>
            </tr>
        @endfor
        <tr>
            <td style="border-left: 1px solid black; border-right: 1px solid black"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
            <td style="border-right: 1px solid black" colspan="3"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
        </tr>
        <tr>
            <td style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;">
            </td>
            <td style="border-right: 1px solid black; border-bottom: 1px solid black;" colspan="2"></td>
            <td style="border-right: 1px solid black; border-bottom: 1px solid black;" colspan="3">Summary
                timesheet
                as per attachment</td>
            <td style="border-right: 1px solid black" colspan="2"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
        </tr>

        <tr>
            <td style="border-left: 1px solid black; border-right: 1px solid black; font-weight: bold; text-decoration: underline; font-style: italic;"
                colspan="6"> Please
                Telegraphycally Transfer to :</td>
            <td style="border-right: 1px solid black" colspan="2"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
        </tr>
        <tr>
            <td style="border-left: 1px solid black" colspan="3">A/C Name</td>
            <td style="border-right: 1px solid black" colspan="3">: PT. PAN NUSANTARA SENTOSA</td>
            <td style="border-right: 1px solid black" colspan="2"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
        </tr>
        <tr>
            <td style="border-left: 1px solid black" colspan="3">A/C No.</td>
            <td style="border-right: 1px solid black" colspan="3">: 109-00-1223515-6</td>
            <td style="border-right: 1px solid black" colspan="2"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
        </tr>
        <tr>
            <td style="border-left: 1px solid black" colspan="3">Bank Name</td>
            <td style="border-right: 1px solid black" colspan="3">: BANK MANDIRI</td>
            <td style="border-right: 1px solid black" colspan="2"></td>
            <td style="border-right: 1px solid black" colspan="2"></td>
        </tr>
        <tr>
            <td style="border-left: 1px solid black; border-bottom: 1px solid black;" colspan="3" height="40">
                Address Bank
            </td>
            <td style="word-wrap: break-word; border-bottom: 1px solid black;" colspan="2" height="60">: Jln.
                Raja Ali Haji,
                Nagoya Batam, Kepulauan
                Riau, Indonesia</td>
            <td style="border-right: 1px solid black; border-bottom: 1px solid black;"></td>
            <td style="border-right: 1px solid black; border-bottom: 1px solid black;" colspan="2" height="40">
            </td>
            <td style="border-right: 1px solid black; border-bottom: 1px solid black;" colspan="2" height="40">
            </td>
        </tr>

        <tr>
            <td colspan="3">
            </td>
            <td colspan="2"></td>
            <td></td>
            <td style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;"
                colspan="2" valign="center" align="center">
                Total
            </td>
            <td style="border-right: 1px solid black; border-bottom: 1px solid black;" colspan="2"
                data-format="#,##0.00">
                {{ $total }}
            </td>
        </tr>
    </tbody>

    <tr></tr>

    <tr>
        <td></td>
        <td colspan="3" valign="center" align="center">Approved by</td>
        <td></td>
        <td></td>
        <td></td>
        <td colspan="2" valign="center" align="center">Signed by</td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr>
        <td></td>
        <td colspan="3" style="border-bottom: 1px solid black"></td>
        <td></td>
        <td></td>
        <td></td>
        <td colspan="2" valign="center" align="center"
            style="border-bottom: 1px solid black; font-weight: bold; text-decoration: underline;">
            =Setup!C2</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3" valign="center" align="center">Customers
            signature Chop</td>
        <td></td>
        <td></td>
        <td></td>
        <td colspan="2" valign="center" align="center">Accounting</td>
    </tr>
</table>
