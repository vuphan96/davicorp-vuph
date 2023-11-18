<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BaoCaoInTem</title>
</head>

<body>
<table>
    @php
        $from_to = $dataSearch['from_to'] ?? convertVnDateObject($dataSearch['from_to']);
        $end_to = $dataSearch['end_to'] ?? convertVnDateObject($dataSearch['end_to']);
    @endphp
    <thead>

    <tr>
        <th colspan="7" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="7" style="border: 1px solid white; border-right: 1px solid #E0E0E0">{{ sc_language_render("admin.report.name_cty") }}
        </th>
    </tr>
    <tr>
        <th colspan="7" style="border: 1px solid white; border-right: 1px solid #E0E0E0">{{ sc_language_render("admin.report.address_cty") }}
        </th>
    </tr>
    <tr>
        <th colspan="7" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="7" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="7" align="center"
            style="font-size: 16px; font-weight: 700; border: 1px solid white; border-right: 1px solid #E0E0E0">BÁO CÁO IN TEM
        </th>
    </tr>
    <tr>
        <th colspan="7" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0">Từ
            ngày {{  $from_to }} đến ngày {{ $end_to }}</th>
    </tr>
    <tr>
        <th colspan="7" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0">Kho: -</th>
    </tr>
    <tr>
        <th colspan="7" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
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
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; text-align: left">{{ $datum['product_sku'] ?? '' }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $datum['product_name'] ?? '' }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $datum['customer_name'] ?? '' }} {{ ($datum['object_id'] == 1) ? "(GV)" : "" }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $datum['customer_num'] ?? '' }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $datum['qty'] ?? '' }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; text-align: left">{{ $datum['name_unit'] ?? '' }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; text-align: left">{{ \Carbon\Carbon::make( $datum['delivery_time'])->format("d/m/Y") ?? '' }}</td>
        </tr>
    @empty
        <td colspan="6">Không có nội dung!</td>
    @endforelse

    </tbody>
</table>
</body>
</html>