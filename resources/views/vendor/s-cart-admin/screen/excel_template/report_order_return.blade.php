<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Phiếu hoàn trả</title>
</head>

<body>
<table>
    <thead>
    <tr>
        <th colspan="11" align="center"
            style="font-size: 14px;">BẢNG KÊ PHIẾU HÀNG TRẢ
        </th>
    </tr>
    <tr >
        <th colspan="11" align="center" style="border-bottom: 1px solid black; border-right: 1px solid #E0E0E0">( Từ
            ngày {{  $date['start'] }} Đến ngày {{ $date['end'] }} )</th>
    </tr>
    <tr></tr>
    <tr style="width: 100%" class="heading-report">
        <th colspan="4" style="border: 1px solid black;; text-align: center;font-weight: bold">Chứng từ</th>
        <th rowspan="2" align="center" style="border: 1px solid black;font-weight: bold">Diễn giải</th>
        <th rowspan="2" align="center" style="border: 1px solid black;font-weight: bold">Mã sản phẩm</th>
        <th rowspan="2" align="center" style="border: 1px solid black;font-weight: bold">Tên mặt hàng</th>
        <th rowspan="2" align="center" style="border: 1px solid black;font-weight: bold">Đvt</th>
        <th rowspan="2" align="center" style="border: 1px solid black;font-weight: bold">Số lượng</th>
        <th rowspan="2" align="center" style="border: 1px solid black;font-weight: bold">Đơn giá</th>
        <th rowspan="2" align="center" style="border: 1px solid black;font-weight: bold; border-bottom: 1px solid black;">Thành tiền</th>
    </tr>
    <tr style="width: 100%" class="heading-report">
        <th align="center" style="border-right: 1px solid black; border-bottom: 1px solid black;font-weight: bold">Ngày</th>
        <th align="center" style="border-right: 1px solid black; border-bottom: 1px solid black;font-weight: bold">Mã khách hàng</th>
        <th align="center" style="border-right: 1px solid black; border-bottom: 1px solid black;font-weight: bold">Tên khách hàng</th>
        <th align="center" style="border-right: 1px solid black; border-bottom: 1px solid black;font-weight: bold">Mã đơn hàng</th>
    </tr>
    </thead>
    <tbody>
    @php $flag = $order->first()['customer_code']; @endphp
    @foreach ($order as $key => $row)
        @if($flag != $row['customer_code'])
            <tr>
                <td colspan="11" style="background-color: #DCE6F1"></td>
            </tr>
            @php $flag = $row['customer_code']; @endphp
        @endif
        <tr>
            <td align="left" style="border-right: 1px solid black;">
                {{ \Carbon\Carbon::parse($row['delivery_date'] ?? '')->format('d/m/Y') }}
            </td>
            <td align="left" style="border-right: 1px solid black;">{{ $row['customer_code'] }}</td>
            <td align="left" style="border-right: 1px solid black;">{{ $row['customer_name'] }}</td>
            <td align="left" style="border-right: 1px solid black;">{{ $row['id_name'] }}</td>
            <td align="left" style="border-right: 1px solid black;">{{ $row['explain'] }}</td>
            <td align="left" style="border-right: 1px solid black;">{{ $row['product_code'] }}</td>
            <td style="border-right: 1px solid black; text-align: left;">{{ $row['product_name'] }}</td>
            <td style="border-right: 1px solid black; text-align: left;">{{ $row['product_unit'] }}</td>
            <td style="border-right: 1px solid black;">{{ (isset($row['created_at']) ? '-' : '') . $row['qty'] }}</td>
            <td style="border-right: 1px solid black; text-align: right;">{{ $row->price }}</td>
            <td style="border-right: 1px solid black; text-align: right;">{{ (isset($row['created_at']) ? '-' : '') . number_format($row['qty'] * $row['price']) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>