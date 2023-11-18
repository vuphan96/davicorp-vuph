<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BangKeDonHang-Ao</title>
</head>

<body>
<table>
    <thead>
    <tr>
        <th colspan="7"
            style="font-size: 14px; font-weight: bold; text-align: center">{{ $department->name }}
        </th>
    </tr>
    <tr>
        <th colspan="7"
            style="text-align: center">
            {!! $department->address !!}
        </th>
    </tr>
    <tr>

    </tr>
    <tr>
        <th colspan="7"
            style="font-size: 14px; font-weight: bold; text-align: center">BẢNG KÊ HÓA ĐƠN BÁN HÀNG
        </th>
    </tr>
    @php
        $now = date('Y-m-d');
    @endphp
    <tr>
        <th colspan="7" style="text-align: center; font-style: italic;">
            Kèm theo hóa đơn ký hiệu 2C22TDD
        </th>
    </tr>
    <tr >
        <th colspan="7" style="text-align: center; font-style: italic; border-right: 1px solid #E0E0E0">
            {{-- Số {{ $data->first()->einv_id ?? '' }}  {{ formatStringDate($now, 'Ngaythangnam'); }} --}}
            Số {{ $data[0]['einvoice_id'] ?? '' }}  {{ formatStringDate($now, 'Ngaythangnam'); }}
        </th>
    </tr>
    <tr>
        <td colspan="5" style="font-weight: bold; font-size: 12px">
            {{-- Tên đơn vị : {{ $data->first()->customer_name ?? '' }} --}}
            Tên đơn vị : {{ $data[0]['customer_name'] ?? '' }}
        </td>
    </tr>
    <tr>
        <td colspan="5" style="font-weight: bold; font-size: 12px">
            {{-- Địa chỉ : {{ $data->first()->customer_address ?? '' }} --}}
            Địa chỉ : {{ $data[0]['customer_address'] ?? '' }}
        </td>
    </tr>
    <tr>

    </tr>
    <tr style="width: 100%" class="heading-report">
        <th colspan="2" style="border: 1px solid black; font-weight: bold; text-align: center">Chứng từ</th>
        <th rowspan="2" style="border: 1px solid black; font-weight: bold; text-align: center">Tên mặt hàng</th>
        <th rowspan="2" style="border: 1px solid black; font-weight: bold; text-align: center">Đvt</th>
        <th rowspan="2" style="border: 1px solid black; font-weight: bold; text-align: center">Số lượng</th>
        <th rowspan="2" style="border: 1px solid black; font-weight: bold; text-align: center">Giá bán</th>
        <th rowspan="2" style="border: 1px solid black; border-bottom: 1px solid black; font-weight: bold; text-align: center">Doanh thu</th>
    </tr>
    <tr style="width: 100%" class="heading-report">
        <th style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; font-weight: bold; text-align: center">Ngày</th>
        <th style="border: 1px solid black; font-weight: bold; text-align: center">Mã hóa đơn</th>

    </tr>
    </thead>

    <tbody>
    @php
        $total = 0;
    @endphp
    {{-- @foreach ($data->groupBy(function($data){return date('Y-m-d',strtotime($data->plan_start_date));}) as $key => $datum) --}}
    @foreach ($data->groupBy('plan_start_date') as $key => $datum)
        @php
            $total_by_day = 0;
        @endphp
        @foreach($datum as $keyId => $item)
            <tr>
                <td style="border-left: 1px solid black; border-right: 1px solid black; text-align: center">{{ $item['plan_start_date'] ?? ''}}</td>
                <td style="border-right: 1px solid black; text-align: left">{{ $item['einvoice_id'] ?? '' }}</td>
                <td style="border-right: 1px solid black; text-align: left">{{ $item['product_name'] ?? '' }}</td>
                <td style="border-right: 1px solid black; text-align: left">{{ $item['unit'] ?? '' }}</td>
                <td style="border-right: 1px solid black; text-align: right">{{ $item['qty'] ?? 0 }}</td>
                <td style="border-right: 1px solid black; text-align: right">{{ $item['price'] ?? 0 }}</td>
                <td style="border-right: 1px solid black; text-align: right">{{ $item['qty']*$item['price'] }}</td>
            </tr>
            @php
                //   $total_by_day += ($item->qty*$item->price);
                  $total_by_day += $item['qty']*$item['price'];
            @endphp
        @endforeach
        <tr>
            <td style="border-left: 1px solid black; text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
            <td style="text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
            <td style="text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
            <td style="text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
            <td style="text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
            <td style="text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
            <td style="text-align: right; border-right: 1px solid black; font-weight: bold; background-color: yellow;">
                {{ $total_by_day }}
            </td>
        </tr>
        @php
            $total += $total_by_day;
        @endphp
    @endforeach
    <tr>
        <td colspan="1" style="border: 1px solid black; font-weight: bold; padding-left: 15px; text-align: left">Tiền hàng</td>
        <td colspan="5" style="border: 1px solid black; font-weight: bold;"></td>
        <td style="border: 1px solid black; font-weight: bold; text-align: right ">
            {{ $total }}
        </td>
    </tr>
    <tr>
        <td colspan="1" style="border: 1px solid black; font-weight: bold; padding-left: 15px; text-align: left">Bằng chữ</td>
        <td colspan="5" style="border: 1px solid black; text-align: center">{{ convert_number_to_words($total ?? 0, true) }}</td>
        <td colspan="1" style="border: 1px solid black; font-weight: bold;"></td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr>
        <td colspan="2" style="font-weight: bold; text-align: center">Bên bán hàng</td>
        <td colspan="3"></td>
        <td colspan="2"style="font-weight: bold; text-align: center">Bên mua hàng</td>
    </tr>
    </tbody>
</table>
</body>
</html>
