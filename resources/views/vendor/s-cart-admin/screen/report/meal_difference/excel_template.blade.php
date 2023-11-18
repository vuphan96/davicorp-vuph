<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BaoCaoHangXuatBepAn</title>
</head>

<body>
<table>
    <thead>
    @php
        $from_to = $dataSearch['from_to'] ?? convertVnDateObject($dataSearch['from_to']);
        $end_to = $dataSearch['end_to'] ?? convertVnDateObject($dataSearch['end_to']);
    @endphp

    <tr>
        <th colspan="10" align="center"
            style="font-size: 14px;">BÁO CÁO CHÊNH LỆCH HÀNG XUẤT BẾP ĂN
        </th>
    </tr>
    <tr >
        <th colspan="10" align="center" style="border-bottom: 1px solid black; border-right: 1px solid #E0E0E0">Từ
            ngày {{  $from_to }} đến ngày {{ $end_to }}</th>
    </tr>
    <tr style="width: 100%" >
        <th rowspan="2" style="border: 1px solid black; font-weight: bold">STT</th>
        <th rowspan="2" style="border: 1px solid black; font-weight: bold">Mã vật tư</th>
        <th rowspan="2" style="border: 1px solid black; font-weight: bold">Tên Vật tư</th>
        <th rowspan="2" style="border: 1px solid black; font-weight: bold">ĐVT</th>
        <th colspan="2" align="center" style="border: 1px solid black; font-weight: bold">Hàng xuất theo ngân hàng TĐ ({{$dataProducts['number_of_servings'] ?? 0}})</th>
        <th colspan="2" align="center" style="border: 1px solid black; font-weight: bold">Hàng xuất thực tế ({{$dataProducts['number_of_servings_fact'] ?? 0}})</th>
        <th colspan="2" align="center" style="border: 1px solid black; font-weight: bold">Chênh lệch</th>
    </tr>
    <tr style="width: 100%">
        <th style="border: 1px solid black; font-weight: bold">Số lượng</th>
        <th style="border: 1px solid black; font-weight: bold">Giá trị</th>
        <th style="border: 1px solid black; font-weight: bold">Số lượng</th>
        <th style="border: 1px solid black; font-weight: bold">Giá trị</th>
        <th style="border: 1px solid black; font-weight: bold">Số lượng</th>
        <th style="border: 1px solid black; font-weight: bold">Giá trị</th>
    </tr>
    </thead>
    <tbody style="border-bottom: 1px solid black">
    @php
        $i = 1;
    @endphp
    @foreach ($dataProducts['details'] as $key => $value)
        <tr>
            <td style="text-align: center; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $i++ }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">
                {{ $value['product_code'] }}
            </td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">
                {{ $value['product_name'] }}
            </td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $value['product_unit'] }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $value['qty_total'] }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $value['price_menu'] }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $value['qty_total_fact'] + $value['extra_bom'] }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $value['price_menu_fact'] + $value['extra_bom_price'] }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $value['qty_total'] - ($value['qty_total_fact'] + $value['extra_bom']) }}</td>
            <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">
                {{ ( $value['price_menu'] - ($value['price_menu_fact'] + $value['extra_bom_price'])) < 0 ? '(' . (abs($value['price_menu'] - ($value['price_menu_fact'] + $value['extra_bom_price']))) . ')'
            : ($value['price_menu'] - ($value['price_menu_fact'] + $value['extra_bom_price'])) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>