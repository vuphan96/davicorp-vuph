
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Báo cáo bán hàng nhóm theo 2 chỉ tiêu</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Times New Roman", Serif;
            line-height: 0.9;
        }
        body {
            height: 100%;
            margin: 20px auto;
            padding: 0;
        }
        @page {
            size: A4;
            margin: 1.2cm;
        }

        @media print {

            html,
            body {
                width: 210mm;
                height: 297mm;
            }

            .page {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                page-break-after: always;
            }
        }

        table {
            width: 100%;
            height: auto;
        }
        * {
            font-size: 12pt;
        }
        .invoice_title {
            font-size: 15pt;
            text-align: center;
        }
        .invoice_time {
            text-align: center;
        }

        table.table_detail {
            border-collapse: collapse;
        }
        table.table_detail tr td.detail {
            text-align: center;
        }
        table.table_detail th {
            background-color: #cdcdcd;
            border: 1px solid black;
            font-size: 13pt;
            text-align: center;
            font-style: bold;
            padding: 5px 5px;
        }
        table.table_detail td {
            border: 1px solid black;
            font-size: 12pt;
            padding: 5px 0px 5px 5px;
        }
    </style>
</head>
<body>
<div id="invoice" class="webview_hide">
    @php
        $from_to = $dataSearch['from_to'] ?? convertVnDateObject($dataSearch['from_to']);
        $end_to = $dataSearch['end_to'] ?? convertVnDateObject($dataSearch['end_to']);
    @endphp
    <table>
        <tr>
            <th colspan="5"></th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: left"> {{ sc_language_render("admin.report.name_cty") }}
            </th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: left">{{ sc_language_render("admin.report.address_cty") }}
            </th>
        </tr>
        <tr>
            <th colspan="5"></th>
        </tr>
        <tr>
            <th colspan="5"></th>
        </tr>
        <tr>
            <th colspan="6" class="invoice_title">BÁO CÁO BÁN HÀNG NHÓM THEO 2 CHỈ TIÊU</th>
        </tr>
        <tr>
            <th colspan="6" class="invoice_time">Từ {{ $from_to }} đến {{ $end_to }}</th>
        </tr>
        <tr>
            <th colspan="5" align="center">Kho: -</th>
        </tr>
        <tr>
            <th colspan="5" align="center"></th>
        </tr>
    </table>
    <br><br>
    <table style="border-collapse: collapse" border="1px" class="table_detail">
        <thead>
        <tr>
            <th style="width: 30px">Stt</th>
            <th style="width: 100px">Mã</th>
            <th style="width: 220px">Tên</th>
            <th style="width: 80px">Số lượng</th>
            <th style="width: 200px">Ghi chú</th>
        </tr>
        </thead>
        <tbody>
        @php $totalQty = 0; @endphp
        @forelse($data->groupBy('product_id') as $key => $datum)
            @php $totalQty += $datum->sum('qty'); $i = 1; @endphp
            <tr>
                <td ></td>
                <td ></td>
                <td style="font-weight: bold; text-transform: uppercase">{{ $datum->first()['product_name']}}</td>
                <td style="font-weight: bold ; text-align: right; padding-right: 5px">{{ number_format($datum->sum('qty'), 2) ?? ''}}</td>
                <td ></td>
            </tr>
            @foreach($datum as $keyItem => $item)
                <tr>
                    <td class="detail" > {{ $i++ }}</td>
                    <td class="detail" > {{ $item['customer_code'] ?? sc_language_render('customer.delete') }} </td>
                    <td >{{ $item['customer_name'] }}</td>
                    <td style="text-align: right ; padding-right: 5px">{{ number_format($item['qty'], 2) }}</td>
                    <td >{{ $item['note'] }}</td>
                </tr>
            @endforeach
        @empty
            <td colspan="6">Không có nội dung!</td>
        @endforelse
        </tbody>
    </table>
    <table>
        <tr>
            <th style="width: 30px"></th>
            <th style="width: 100px"></th>
            <th colspan="1" style="font-weight: bold; width: 220px; text-align: right">Tổng cộng</th>
            <th colspan="1" style="font-weight: bold; width: 80px; text-align: right">{{ number_format($totalQty, 2) }}</th>
            <th colspan="1" ></th>

        </tr>
        <tr>
            <th colspan="5" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: right; border: 1px solid white; border-right: 1px solid white; padding-right: 50px">Ngày ...... Tháng ...... năm ..........</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: right; font-weight: bold; border: 1px solid white; border-right: 1px solid white; padding-right: 120px">Người Lập</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: right; border: 1px solid white; border-right: 1px solid white; font-style: italic; font-size: 10px; padding-right: 130px">(Ký, họ tên)</th>
        </tr>
        <tr>
            <th colspan="5" align="center"></th>
        </tr>
        <tr>
            <th colspan="5" align="center"></th>
        </tr>
        <tr>
            <th colspan="5" align="center"></th>
        </tr>
    </table>
</div>
</body>
</html>