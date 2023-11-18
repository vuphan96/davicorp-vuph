
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BÁO CÁO CHÊNH LỆCH HÀNG XUẤT BẾP ĂN</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Times New Roman", Serif;
            line-height: 0.9;
        }
        body {
            width: 630px;
            height: 100%;
            margin: 20px auto;
            padding: 0;
        }
        @page {
            size: A4;
            margin: 0;
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
        .webview_hide {
            margin: auto -10px;
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
            /*font-weight: 800 ;*/
            /*font-family: DejaVu Sans, sans-serif;*/
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
        $from_to = $dataSearch['from_to'] ? date("d-m-Y", strtotime($dataSearch['from_to'])) : '-';
        $end_to = $dataSearch['end_to'] ? date("d-m-Y", strtotime($dataSearch['end_to'])) : '-';
    @endphp
    <table>

        <tr>
            <th colspan="10" class="invoice_title">BÁO CÁO CHÊNH LỆCH HÀNG XUẤT BẾP ĂN</th>
        </tr>
        <br>
        <tr>
            <th colspan="10" class="invoice_time">Từ {{ $dataSearch['from_to']  }} đến {{ $dataSearch['end_to'] }}</th>
        </tr>
    </table>
    <br>
    <table style="border-collapse: collapse" border="1px" class="table_detail">
        <thead>
        <tr style="width: 100%" class="heading-report">
            <th rowspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black;">STT</th>
            <th rowspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black;">Mã vật tư</th>
            <th rowspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black;">Tên Vật tư</th>
            <th rowspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black;">ĐVT</th>
            <th colspan="2" align="center" style="border-right: 1px solid black;">Hàng xuất theo ngân hàng TĐ</th>
            <th colspan="2" align="center" style="border-right: 1px solid black;">Hàng xuất thực tế</th>
            <th colspan="2" align="center" style="border-right: 1px solid black;">Chênh lệch</th>
        </tr>
        <tr style="width: 100%" class="heading-report">
            <th style="border-right: 1px solid black; border-bottom: 1px solid black;">Số lượng</th>
            <th style="border-right: 1px solid black; border-bottom: 1px solid black;">Giá trị</th>
            <th style="border-right: 1px solid black; border-bottom: 1px solid black;">Số lượng</th>
            <th style="border-right: 1px solid black; border-bottom: 1px solid black;">Giá trị</th>
            <th style="border-right: 1px solid black; border-bottom: 1px solid black;">Số lượng</th>
            <th style="border-right: 1px solid black; border-bottom: 1px solid black;">Giá trị</th>
        </tr>
        </thead>
        <tbody>
        @php
            $i = 1;
        @endphp
        @forelse($data as $key => $value)
            <tr>
                <td>{{ $i }}</td>
                <td>
                    {{ $value['product_code'] }}
                </td>
                <td>
                    {{ $value['product_name'] }}
                </td>
                <td>{{ $value['product_unit'] }}</td>
                <td style="text-align: right; padding-right: 5px">{{ number_format($value['qty_total'], 2) }}</td>
                <td style="text-align: right; padding-right: 5px">{{ number_format($value['price_menu']) }}</td>
                <td style="text-align: right; padding-right: 5px">{{ number_format($value['qty_total_fact'] + $value['extra_bom'], 2) }}</td>
                <td style="text-align: right; padding-right: 5px">{{ number_format($value['price_menu_fact'] + $value['extra_bom_price']) }}</td>
                <td style="text-align: right; padding-right: 5px">{{ number_format($value['qty_total'] - ($value['qty_total_fact'] + $value['extra_bom']), 2) }}</td>
                <td style="text-align: right; padding-right: 5px">
                    {{ $value['price_menu'] - ($value['price_menu_fact'] + $value['extra_bom_price']) < 0 ? '(' . number_format(abs($value['price_menu'] - ($value['price_menu_fact'] + $value['extra_bom_price']))) . ')'
                                    : number_format($value['price_menu'] - ($value['price_menu_fact'] + $value['extra_bom_price'])) }}
                </td>
            </tr>

        @empty
            <td colspan="10">Không có nội dung!</td>
        @endforelse
        </tbody>
    </table>

    {{--    Báo cáo chi tiết--}}
    <br><br>
    <table>
        <tr>
            <th colspan="10">BÁO CÁO CHI TIẾT</th>
        </tr>
    </table>
    <br>
    <table style="border-collapse: collapse" border="1px" class="table_detail">
        <thead>
        <tr style="width: 100%" class="heading-report">
            <th rowspan="2">STT</th>
            <th rowspan="2">Ngày chứng từ</th>
            <th colspan="2" align="center">Hàng xuất theo ngân hàng TĐ</th>
            <th colspan="2" align="center">Hàng xuất thực tế</th>
            <th colspan="2" align="center">Chênh lệch</th>
            <th rowspan="2">Mã khách hàng</th>
            <th rowspan="2">Tên khách hàng</th>
        </tr>
        <tr style="width: 100%" class="heading-report">
            <th>Số lượng</th>
            <th>Giá trị</th>
            <th>Số lượng</th>
            <th>Giá trị</th>
            <th>Số lượng</th>
            <th>Giá trị</th>
        </tr>
        </thead>
        <tbody>
        @forelse($dataDetails as $key => $value)
            @if($value->type == 1)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td style="text-align: right; padding-right: 5px">
                        {{ isset($value->bill_date) ? date_format(new DateTime($value->bill_date), "d/m/Y") : '' }}
                    </td>
                    <td style="text-align: right; padding-right: 5px">0.00</td>
                    <td style="text-align: right; padding-right: 5px">0</td>
                    <td style="text-align: right; padding-right: 5px">{{ number_format($value->real_total_bom, 2) }}</td>
                    <td style="text-align: right; padding-right: 5px">{{ number_format($value->real_total_bom * $value->import_price) }}</td>
                    <td style="text-align: right; padding-right: 5px">{{ number_format(0-$value->real_total_bom, 2) }}</td>
                    <td style="text-align: right; padding-right: 5px">
                        ({{ number_format(abs(0 - ($value->real_total_bom * $value->import_price))) }})
                    </td>
                    <td style="text-align: left; padding-left: 5px">{{ $value->customer_code ?? "Mã khách hàng đã bị xóa" }}</td>
                    <td style="text-align: left; padding-left: 5px">{{ $value->customer_name }}</td>
                </tr>
            @else
                <tr>
                    <td>{{ $i++ }}</td>
                    <td style="text-align: right; padding-right: 5px">
                        {{ isset($value->bill_date) ? date_format(new DateTime($value->bill_date), "d/m/Y") : '' }}
                    </td>
                    <td style="text-align: right; padding-right: 5px">{{ number_format($value->qty_total, 2) }}</td>
                    <td style="text-align: right; padding-right: 5px">{{ $value->price_menu != null ? number_format($value->price_menu) : '0' }}</td>
                    <td style="text-align: right; padding-right: 5px">{{ number_format($value->qty_total_fact, 2) }}</td>
                    <td style="text-align: right; padding-right: 5px">{{ $value->price_menu_fact != null ? number_format($value->price_menu_fact) : '0'}}</td>
                    <td style="text-align: right; padding-right: 5px">{{ number_format($value->qty_total - $value->qty_total_fact, 2) }}</td>
                    <td style="text-align: right; padding-right: 5px">
                        {{ $value->price_menu - $value->price_menu_fact < 0 ? '(' . number_format(abs($value->price_menu - $value->price_menu_fact)) . ')'
                    : number_format($value->price_menu - $value->price_menu_fact) }}
                    </td>
                    <td style="text-align: left; padding-left: 5px">{{ $value->customer_code ?? "Mã khách hàng đã bị xóa" }}</td>
                    <td style="text-align: left; padding-left: 5px">{{ $value->customer_name }}</td>
                </tr>
            @endif
        @empty
            <td colspan="10">Không có nội dung!</td>
        @endforelse
        </tbody>
    </table>
</div>
</body>
</html>