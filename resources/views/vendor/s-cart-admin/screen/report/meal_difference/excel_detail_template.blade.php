<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BaoCaoChiTietHangXuatBepAn</title>
</head>

<body>
<table>
    <thead>
    @php
        $from_to = $dataSearch['from_to'] ? date("d-m-Y", strtotime($dataSearch['from_to'])) : '-';
        $end_to = $dataSearch['end_to'] ? date("d-m-Y", strtotime($dataSearch['end_to'])) : '-';
    @endphp

    <tr>
        <th colspan="10" align="center"
            style="font-size: 14px;">BÁO CÁO CHÊNH LỆCH HÀNG XUẤT BẾP ĂN
        </th>
    </tr>
    <tr >
        <th colspan="10" align="center" style="border-bottom: 1px solid black; border-right: 1px solid #E0E0E0">Từ
            ngày {{  $dataSearch['from_to'] }} đến ngày {{ $dataSearch['end_to'] }}</th>
    </tr>
    <tr style="width: 100%" class="heading-report">
        <th rowspan="2" style="border: 1px solid black;">STT</th>
        <th rowspan="2" style="border: 1px solid black;">Mã vật tư</th>
        <th rowspan="2" style="border: 1px solid black;">Tên Vật tư</th>
        <th rowspan="2" style="border: 1px solid black;">DVT</th>
        <th colspan="2" align="center" style="border-right: 1px solid black;">Hàng xuất theo ngân hàng TD</th>
        <th colspan="2" align="center" style="border-right: 1px solid black;">Hàng xuất thực tê</th>
        <th colspan="2" align="center" style="border-right: 1px solid black;">Chênh lệch</th>
    </tr>
    <tr style="width: 100%" class="heading-report">
        <th style="border: 1px solid black;">Số lượng</th>
        <th style="border: 1px solid black;">Giá trị</th>
        <th style="border: 1px solid black;">Số lượng</th>
        <th style="border: 1px solid black;">Giá trị</th>
        <th style="border: 1px solid black;">Số lượng</th>
        <th style="border: 1px solid black;">Giá trị</th>
    </tr>
    </thead>
    <tbody>
    @php
        $i = 1;
    @endphp
    @foreach ($data as $key => $value)
        <tr>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px solid black">{{ $i }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px solid black">
                {{ $value['product_code'] }}
            </td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px solid black">
                {{ $value['product_name'] }}
            </td>
            <td style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black">{{ $value['product_unit'] }}</td>
            <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px solid black">{{ $value['qty_total'] }}</td>
            <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px solid black">{{ $value['price_menu'] }}</td>
            <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px solid black">{{ $value['qty_total_fact'] + $value['extra_bom'] }}</td>
            <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px solid black">{{ $value['price_menu_fact'] + $value['extra_bom_price'] }}</td>
            <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px solid black">{{ $value['qty_total'] - ($value['qty_total_fact'] + $value['extra_bom']) }}</td>
            <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px solid black">
                {{ $value['price_menu'] - ($value['price_menu_fact'] + $value['extra_bom_price']) < 0 ? '(' . number_format(abs($value['price_menu'] - ($value['price_menu_fact'] + $value['extra_bom_price']))) . ')'
            : ($value['price_menu'] - ($value['price_menu_fact'] + $value['extra_bom_price'])) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<table>
    <thead>
    <tr>
        <th colspan="10" align="center"
            style="font-size: 14px;">BÁO CÁO CHI TIẾT
        </th>
    </tr>
    <tr style="width: 100%" class="heading-report">
        <th style="border: 1px solid black;" rowspan="2">STT</th>
        <th style="border: 1px solid black;" rowspan="2">Ngày chứng từ</th>
        <th style="border: 1px solid black;" colspan="2" align="center">Hàng xuất theo ngân hàng TĐ</th>
        <th style="border: 1px solid black;" colspan="2" align="center">Hàng xuất thực tế</th>
        <th style="border: 1px solid black;" colspan="2" align="center">Chênh lệch</th>
        <th style="border: 1px solid black;" rowspan="2">Mã khách hàng</th>
        <th style="border: 1px solid black;" rowspan="2">Tên khách hàng</th>
    </tr>
    <tr style="width: 100%" class="heading-report">
        <th style="border: 1px solid black;">Số lượng</th>
        <th style="border: 1px solid black;">Giá trị</th>
        <th style="border: 1px solid black;">Số lượng</th>
        <th style="border: 1px solid black;">Giá trị</th>
        <th style="border: 1px solid black;">Số lượng</th>
        <th style="border: 1px solid black;">Giá trị</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($dataDetails as $key => $value)
        @if($value->type == 1)
            <tr>
                <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $i++ }}</td>
                <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">
                    {{ isset($value->bill_date) ? date_format(new DateTime($value->bill_date), "d/m/Y") : '' }}
                </td>
                <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">0.00</td>
                <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">0</td>
                <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $value->real_total_bom }}</td>
                <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $value->real_total_bom * $value->import_price }}</td>
                <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ 0 - $value->real_total_bom }}</td>
                <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">
                    ({{ number_format(abs(0 - ($value->real_total_bom * $value->import_price))) }})
                </td>
                <td style="text-align: left; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $value->customer_code ?? "Mã khách hàng đã bị xóa" }}</td>
                <td style="text-align: left; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $value->customer_name }}</td>
            </tr>
        @else
            <tr>
                <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $i++ }}</td>
                <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">
                    {{ isset($value->bill_date) ? date_format(new DateTime($value->bill_date), "d/m/Y") : '' }}
                </td>
                <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $value->qty_total }}</td>
                <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $value->price_menu ?? '0' }}</td>
                <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $value->qty_total_fact }}</td>
                <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $value->price_menu_fact ?? '0'}}</td>
                <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $value->qty_total - $value->qty_total_fact }}</td>
                <td style="text-align: right; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">
                    {{ $value->price_menu - $value->price_menu_fact < 0 ? '(' . number_format(abs($value->price_menu - $value->price_menu_fact)) . ')'
                : $value->price_menu - $value->price_menu_fact }}
                </td>
                <td style="text-align: left; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $value->customer_code ?? "Mã khách hàng đã bị xóa" }}</td>
                <td style="text-align: left; border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $value->customer_name }}</td>
            </tr>
        @endif

    @endforeach
    </tbody>
</table>
</body>
</html>