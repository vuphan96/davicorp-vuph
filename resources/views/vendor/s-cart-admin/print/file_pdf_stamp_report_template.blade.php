<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BaoCaoInTem</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Times New Roman", Serif;
            line-height: 0.9;
            font-size: 13pt;
        }

        @page {
            size: A4;
            margin: 1.2cm;
        }

        table {
            width: 100%;
            height: auto;
        }

        table.table_detail {
            border-collapse: collapse;
        }
        table.table_detail tr td.detail {
            text-align: center;
        }
        table.table_detail tr:last-child {
            border: 1px solid black;
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
            padding: 5px 5px 5px 5px;
        }
    </style>
</head>

<body>
<table>
    @php
        $from_to = $dataSearch['from_to'] ?? convertVnDateObject($dataSearch['from_to']);
        $end_to = $dataSearch['end_to'] ?? convertVnDateObject($dataSearch['end_to']);
    @endphp
    <thead>
{{--    {{ dd($data) }}--}}
    <tr>
        <th colspan="5" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="5" style="border: 1px solid white; border-right: 1px solid #E0E0E0">{{ sc_language_render("admin.report.name_cty") }}
        </th>
    </tr>
    <tr>
        <th colspan="5" style="border: 1px solid white; border-right: 1px solid #E0E0E0">{{ sc_language_render("admin.report.address_cty") }}
        </th>
    </tr>
    <tr>
        <th colspan="5" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="5" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="5" align="center"
            style="font-size: 16px; font-weight: 700; border: 1px solid white; border-right: 1px solid #E0E0E0">BÁO CÁO IN TEM
        </th>
    </tr>
    <tr>
        <th colspan="5" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0">Từ
            ngày {{  $from_to }} đến ngày {{ $end_to }}</th>
    </tr>
    <tr>
        <th colspan="5" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0">Kho: -</th>
    </tr>
    <tr>
        <th colspan="5" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    </thead>
    <tbody>


    </tbody>
</table>
<table class="table_detail">
    <thead>
        <tr>
            <th align="center" style="font-weight: 800; border: 1px solid black">Mã sản phẩm</th>
            <th align="center" style="font-weight: 800; border: 1px solid black">Tên sản phẩm</th>
            <th align="center" style="font-weight: 800; border: 1px solid black">Tên khách hàng</th>
            <th align="center" style="font-weight: 800; border: 1px solid black">STT</th>
            <th align="center" style="font-weight: 800; border: 1px solid black">Số lượng</th>
            <th align="center" style="font-weight: 800; border: 1px solid black">Đơn vị tính</th>
            <th align="center" style="font-weight: 800; border: 1px solid black">NSX</th>
        </tr>
    </thead>
    <tbody>
    @forelse($data as $datum)
        <tr>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; text-align: center">{{ $datum['product_sku'] ?? '' }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $datum['product_name'] ?? '' }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $datum['customer_name'] ?? '' }} {{ ($datum['object_id'] == 1) ? "(GV)" : "" }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black;text-align: right;">{{ $datum['customer_num'] ?? '' }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; text-align: right">{{ $datum['qty'] ?? '' }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; text-align: left; padding-right: 7px">{{ $datum['name_unit'] ?? '' }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; text-align: left;">{{ \Carbon\Carbon::make( $datum['delivery_time'] ?? '' )->format("d/m/Y")}}</td>
        </tr>
    @empty
        <td colspan="6">Không có nội dung!</td>
    @endforelse
    </tbody>
</table>
</body>
</html>