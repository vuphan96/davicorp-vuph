<!doctype html>
<html lang="en">
<head>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Báo cáo danh thu theo khách hàng</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Times New Roman", Serif;
            line-height: 1;
        }
        body {
            height: 100%;
            padding: 0;
        }
        @page {
            size: A4;
            margin: 1.5cm;
        }

        @media print {

            html,
            .page {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
            }
        }

        table {
            width: 100%;
            height: auto;
        }
        * {
            font-size: 10pt;
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
            font-size: 12px;
            text-align: center;
            font-style: bold;
            padding: 5px auto;
            margin-top: 8pt;
            margin-bottom: 8pt;
            height: 30pt;
        }
        table.table_detail td {
            border: 1px solid black;
            font-size: 12px;
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
            <th colspan="5" ></th>
        </tr>
        <tr>
            <th colspan="5"></th>
        </tr>
        <tr>
            <th colspan="6" class="invoice_title" >BÁO CÁO DOANH THU THEO KHÁCH HÀNG</th>
        </tr>
        <tr>
            <th colspan="6" class="invoice_time">Từ {{ $from_to }} đến {{ $end_to }}</th>
        </tr>
        <tr>
            <th colspan="5" align="center">Kho: -</th>
        </tr>
        <tr>
            <th colspan="5" ></th>
        </tr>
    </table>
    <br><br>
    <table style="border-collapse: collapse" border="1px" class="table_detail">
        <thead>
        <tr>
            <th style="width: 30px">Stt</th>
            <th>Ngày giao hàng</th>
            <th>Mã đơn hàng</th>
            <th>Mã khách hàng</th>
            <th>Tên khách hàng</th>
            <th style="">Diễn giải</th>
            <th style="">Tổng giá trị Đơn hàng</th>
        </tr>
        </thead>
        <tbody>
        @php
            $i = 1;
            $total_revenue = 0;
        @endphp
        @forelse($data as $datum)
            <tr>
                <td class="detail">{{ $i ?? '' }}</td>
                <td style="text-align: left">{{ \Carbon\Carbon::parse($datum['delivery_date'] ?? '')->format('d/m/Y')  }}</td>
                <td style="text-align: left">{{ $datum['id_name'] ?? '' }}</td>
                <td style="text-align: left">{{ $datum['customer_code'] ?? '' }}</td>
                <td style="text-align: left">{{ $datum['customer_name'] ?? '' }}</td>
                <td class="detail" style="text-align: left">{{ $datum['explain'] ?? '' }}</td>
                <td style="text-align: right ; padding-right: 5px">{{ number_format($datum['amount'] ?? 0) ?? '' }}</td>
            </tr>
            @php
                $total_revenue += $datum['amount'] ?? $datum->amount;
                $i++;
            @endphp
        @empty
            <td colspan="7">Không có nội dung!</td>
        @endforelse
        <tr>
            <td colspan="6" align="center"><b>TỔNG DOANH THU</b></td>
            <td colspan="1" style="text-align: right ; padding-right: 5px"><b>{{ number_format($total_revenue) ?? '' }}</b></td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>