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
        <th colspan="12" align="center"
            style="font-size: 14px;">BẢNG KÊ PHIẾU HÀNG TRẢ
        </th>
    </tr>
    <tr >
        <th colspan="12" align="center" style="border-bottom: 1px solid black; border-right: 1px solid #E0E0E0">( Từ
            ngày {{  $date['start'] }} Đến ngày {{ $date['end'] }} )</th>
    </tr>
    <tr></tr>
    <tr style="width: 100%" class="heading-report">
        <th colspan="5" style="border: 1px solid black;; text-align: center;font-weight: bold">Chứng từ</th>
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
        <th align="center" style="border-right: 1px solid black; border-bottom: 1px solid black;font-weight: bold">Mã đơn hàng</th>
        <th align="center" style="border-right: 1px solid black; border-bottom: 1px solid black;font-weight: bold">Mã khách hàng</th>
        <th align="center" style="border-right: 1px solid black; border-bottom: 1px solid black;font-weight: bold">Tên khách hàng</th>
        <th align="center" style="border-right: 1px solid black; border-bottom: 1px solid black;font-weight: bold">Đối tượng</th>
    </tr>
    </thead>
    <tbody>

    @foreach ($order as $key => $datum)
        @php $sum = 0; @endphp
        @foreach($datum->returnHistory as $keyId => $item)
            <tr>
                <td align="left" style="border-right: 1px solid black;">
                    {{ \Carbon\Carbon::parse($datum->delivery_time ?? '')->format('d/m/Y') }}
                </td>
                <td align="left" style="border-right: 1px solid black;">{{ $datum->id_name }}</td>
                <td align="left" style="border-right: 1px solid black;">{{ $datum->customer_code }}</td>
                <td align="left" style="border-right: 1px solid black;">{{ $datum->name }}</td>
                <td align="left" style="border-right: 1px solid black;">{{ $datum->object_id == 1 ? 'Giáo viên' : 'Học sinh' }}</td>
                <td align="left" style="border-right: 1px solid black;">{{ $datum->explain }}</td>
                <td style="border-right: 1px solid black; text-align: left;">{{ $item->product_code ?? ($item->detail->product_code ?? 'Đã bị xóa') }}</td>
                <td style="border-right: 1px solid black; text-align: left;">{{ $item->product_name ?? ($item->detail->product_name ?? 'Đã bị xóa') }}</td>
                <td style="border-right: 1px solid black; text-align: left;">{{ $item->product_unit ?? ($item->detail->product_unit ?? 'Đã bị xóa') }}</td>
                <td style="border-right: 1px solid black; text-align: right;">{{ $item->return_qty }}</td>
                <td style="border-right: 1px solid black; text-align: right;">{{ $item->price }}</td>
                <td style="border-right: 1px solid black; text-align: right;">{{ $item->return_total }}
                </td>
            </tr>
            @php $sum += $item->return_total @endphp
        @endforeach
        @if($datum->returnHistory->isNotEmpty())
            <tr>
                <td style="border-right: 1px solid black; background-color: yellow;"></td>
                <td style="border-right: 1px solid black; background-color: yellow;"></td>
                <td style="border-right: 1px solid black; background-color: yellow;"></td>
                <td style="border-right: 1px solid black; background-color: yellow;"></td>
                <td style="text-align: center;border-right: 1px solid black; background-color: yellow;"></td>
                <td style="text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
                <td style="text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
                <td style="text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
                <td style="text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
                <td style="text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
                <td style="text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
                <td style="text-align: right;border-right: 1px solid black; font-weight: bold; background-color: yellow;">
                    {{ $sum }}
                </td>
            </tr>
        @endif
    @endforeach
    </tbody>
</table>
</body>
</html>