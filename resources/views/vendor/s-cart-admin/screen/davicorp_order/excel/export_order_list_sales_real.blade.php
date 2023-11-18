<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BangKeDonHang-Thuc</title>
</head>

<body>
<table>
    <thead>
    @php
        $from_to = $dataSearch['from_to'] ? $dataSearch['from_to'] : '---';
        $end_to = $dataSearch['end_to'] ? $dataSearch['end_to'] : '---';
        $sum = 0;
    @endphp
    <tr>
        <th colspan="9" align="center"
            style="font-size: 14px;">BẢNG KÊ HÓA ĐƠN BÁN HÀNG
        </th>
    </tr>
    <tr >
        <th colspan="9" align="center" style="border-bottom: 1px solid black; border-right: 1px solid #E0E0E0">( Từ
            ngày {{  $from_to }} Đến ngày {{ $end_to }} )</th>
    </tr>
    <tr>

    </tr>
    <tr>
        <td style="font-weight: bold; font-size: 12px">
            Tên đơn vị
        </td>
        <td colspan="8" style="font-weight: bold; font-size: 12px">
            {{ $data->first()->name ?? '' }}
        </td>
    </tr>
    <tr>
        <td style="font-weight: bold; font-size: 12px">
            Mã đơn vị (*)
        </td>
        <td colspan="8" style="font-weight: bold; font-size: 12px">
            {{ $data->first()->customer ? $data->first()->customer->customer_code : '' }}
        </td>
    </tr>
    <tr>
        <td style="font-weight: bold; font-size: 12px">
            Địa chỉ
        </td>
        <td colspan="8" style="font-weight: bold; font-size: 12px">
            {{ $data->first()->address ?? '' }}
        </td>
    </tr>
    <tr>

    </tr>
    <tr style="width: 100%" class="heading-report">
        <th colspan="2" style="border: 1px solid black;; text-align: center;font-weight: bold">Chứng từ</th>
        <th rowspan="2" align="center" style="border: 1px solid black;font-weight: bold">Diễn giải</th>
        <th rowspan="2" align="center" style="border: 1px solid black;font-weight: bold">Mã sản phẩm (*)</th>
        <th rowspan="2" align="center" style="border: 1px solid black;font-weight: bold">Tên mặt hàng</th>
        <th rowspan="2" align="center" style="border: 1px solid black;font-weight: bold">Đvt</th>
        <th rowspan="2" align="center" style="border: 1px solid black;font-weight: bold">Số lượng (*)</th>
        <th rowspan="2" align="center" style="border: 1px solid black;font-weight: bold">Giá bán (*)</th>
        <th rowspan="2" align="center" style="border: 1px solid black;font-weight: bold; border-bottom: 1px solid black;">Doanh thu</th>
    </tr>
    <tr style="width: 100%" class="heading-report">
        <th align="center" style="border-right: 1px solid black; border-bottom: 1px solid black;font-weight: bold">Ngày (*)</th>
        <th align="center" style="border-right: 1px solid black; border-bottom: 1px solid black;font-weight: bold">Số HĐ &nbsp;<span style="color: red">(*)</span></th>
    </tr>
    </thead>
    <tbody>
    @foreach ($data->groupBy('delivery_time') as $key => $datum)
        @php $sumDetail = 0;  @endphp
        @foreach($datum as $keyId => $item)
            <tr>
                <td align="left" style="border-right: 1px solid black;">
                    {{ \Carbon\Carbon::parse($item->delivery_time ?? '')->format('d/m/Y') }}
                </td>
                <td align="left" style="border-right: 1px solid black;">
                    {{ $item->id_name }}
                </td>
                <td align="left" style="border-right: 1px solid black;">{{ $item->explain }}</td>
                <td style="border-right: 1px solid black; text-align: left;">{{ $item->sku }}</td>
                <td style="border-right: 1px solid black; text-align: left;">{{ $item->product_name }}</td>
                <td style="border-right: 1px solid black; text-align: left;">{{ $item->unit_name }}</td>
                <td style="border-right: 1px solid black; text-align: right;">{{ isset($item->qty) ? $item->qty : 0}}</td>
                <td style="border-right: 1px solid black; text-align: right;">{{ isset($item->price) ? $item->price: 0 }}</td>
                <td style="border-right: 1px solid black; text-align: right;">{{ $item->qty * $item->price }}
                </td>
            </tr>
            @php
                $sum += $item->qty * $item->price;
                $sumDetail += $item->qty * $item->price;
            @endphp
        @endforeach
        <tr>
            <td style="border-right: 1px solid black; background-color: yellow;"></td>
            <td style="border-right: 1px solid black; background-color: yellow;"></td>
            <td style="border-right: 1px solid black; background-color: yellow;"></td>
            <td style="border-right: 1px solid black; background-color: yellow;"></td>
            <td style="text-align: center;border-right: 1px solid black; background-color: yellow;"></td>
            <td style="text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
            <td style="text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
            <td style="text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
            <td style="text-align: right;border-right: 1px solid black; font-weight: bold; background-color: yellow;">
                {{ $sumDetail }}
            </td>
        </tr>
    @endforeach
    <tr>
        <td colspan="8" style="border: 1px solid black; background-color: yellow; font-weight: bold; padding-left: 15px" align="left">Tổng tiền</td>
        <td align="right" style="border: 1px solid black; font-weight: bold; background-color: red;">
            {{ $sum }}
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>