
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
            line-height: 1;
        }
        @page {
            size: A4;
            margin: 1.2cm;
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
            font-size: 12pt;
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
            <th colspan="10" class="invoice_title">BÁO CÁO CHÊNH LỆCH HÀNG XUẤT BẾP ĂN</th>
        </tr>
        <tr>
            <th colspan="10" class="invoice_time">Từ {{ $from_to }} đến {{ $end_to }}</th>
        </tr>
    </table>
    <br>
    <table style="border-collapse: collapse" border="1px" class="table_detail">
        <thead>
        <tr style="width: 100%" class="heading-report">
            <th rowspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black;">STT</th>
            <th rowspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black;">Mã vật tư</th>
            <th rowspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black;min-width: 150px">Tên Vật tư</th>
            <th rowspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black;">ĐVT</th>
            <th colspan="2" align="center" style="border-right: 1px solid black; max-width: 200px">Hàng xuất theo ngân hàng TĐ ({{ $datas['number_of_servings'] }}) </th>
            <th colspan="2" align="center" style="border-right: 1px solid black;">Hàng xuất thực tế ({{ $datas['number_of_servings_fact'] }}) </th>
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
        @forelse($datas['details'] as $key => $value)
            <tr>
                <td>{{ $i++ }}</td>
                <td>
                    {{ $value['product_code'] }}
                </td>
                <td>
                    {{ $value['product_name'] }}
                </td>
                <td>{{ $value['product_unit'] }}</td>
                <td style="text-align: right; padding-right: 5px">{{ number_format($value['qty_total'] , 2) }}</td>
                <td style="text-align: right; padding-right: 5px">{{ number_format($value['price_menu'])}}</td>
                <td style="text-align: right; padding-right: 5px">{{ number_format($value['qty_total_fact'] + $value['extra_bom'] , 2) }}</td>
                <td style="text-align: right; padding-right: 5px">{{ number_format($value['price_menu_fact'] + $value['extra_bom_price'])}}</td>
                <td style="text-align: right; padding-right: 5px">{{ number_format($value['qty_total'] - ($value['qty_total_fact'] + $value['extra_bom']), 2)  }}</td>
                <td style="text-align: right; padding-right: 5px">
                    {{ ( $value['price_menu'] - ($value['price_menu_fact'] + $value['extra_bom_price'])) < 0 ? '(' . number_format(abs($value['price_menu'] - ($value['price_menu_fact'] + $value['extra_bom_price']))) . ')'
                                    : number_format($value['price_menu'] - ($value['price_menu_fact'] + $value['extra_bom_price'])) }}
                </td>
            </tr>

        @empty
            <td colspan="10">Không có nội dung!</td>
        @endforelse
        </tbody>
    </table>
</div>
</body>
</html>