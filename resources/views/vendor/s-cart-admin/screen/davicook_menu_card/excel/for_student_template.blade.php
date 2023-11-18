
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Phiếu Cháu</title>
</head>
<body>
@php $sumExtra = 0; $i = 1; $j = 1 @endphp
@foreach($data as $key => $item)
    <table class="header_table">
        <tr>
            <td colspan="1" rowspan="2" style="padding-right: 0.5cm; text-align: center"><img width="150" style="display: block; margin-left: 10px" class="logo" src="{{ public_path('images/print_assets/davicook.png')}}"/></td>
            <td colspan="3" align="center" style="font-weight: bold">CÔNG TY CỔ PHẦN DAVICOOK HÀ NỘI</td>
            <td colspan="1" align="center" style="font-weight: bold">{{ $item['week_no'] != '' ? 'Tuần - '.$item['week_no'] : "" }}</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center;">
                Địa chỉ : Xóm 10, Thôn 3, Xã Yên Mỹ, Huyện Thanh Trì, Hà Nội; ĐT: 024.3634.3714
            </td>
        </tr>
        <tr>
            <td colspan="1"></td>
            <td colspan="3" align="center" style="font-weight: bold; text-transform: uppercase"> {{ $item['card_name'] }}</td>
            <td colspan="1" align="center" style="font-weight: bold; text-transform: uppercase"></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th rowspan="2" align="center" style="font-weight: bold; border: 1px solid black">THỨ</th>
                <th rowspan="2" align="center" style="font-weight: bold; border: 1px solid black">MÓN ĂN</th>
                <th colspan="2" align="center" style="font-weight: bold; border: 1px solid black">Định lượng TP (gram)</th>
                <th rowspan="2" align="center" style="font-weight: bold; border: 1px solid black">QUÀ CHIỀU</th>
            </tr>
            <tr>
                <th align="center" style="font-weight: bold; border: 1px solid black">Sống</th>
                <th align="center" style="font-weight: bold; border: 1px solid black">Chín</th>
            </tr>
        </thead>
        <tbody>
        @foreach($item['first_details'] as $detail)
            <tr>
                <td rowspan="{{ $detail['count'] }}" align="center" valign="middle" style="border: 1px solid black"><span>{{ $detail['day'] }}</span><br><span>{{ $detail['date'] }}</span></td>
                <td valign="middle" style="border-right: 1px solid black; border-bottom: {{ $detail['count'] == 1 ? '1px solid black' : '' }} ">{{ $detail['dish_name'] }}</td>
                <td valign="middle" align="center" style="border-right: 1px solid black;word-wrap: break-word; border-bottom: {{ $detail['count'] == 1 ? '1px solid black' : '' }}">{{ $detail['qty_raw_dish'] }}</td>
                <td valign="middle" align="center" style="border-right: 1px solid black;word-wrap: break-word; border-bottom: {{ $detail['count'] == 1 ? '1px solid black' : '' }}">{{ $detail['qty_cooked_dish'] }}</td>
                <td valign="middle" rowspan="{{ $detail['count'] }}" style="vertical-align: middle;border: 1px solid black">{!! $detail['product_gift_or_comment'] !!}</td>
            @foreach($detail['item'] as $keyNo => $value)
                @if($detail['count'] == $keyNo + 2)
                    <tr>
                        <td valign="middle" style="border-right: 1px solid black; border-bottom: 1px solid black;">{{ $value['dish_name'] }}</td>
                        <td valign="middle" align="center" style="word-wrap: break-word;border-right: 1px solid black; border-bottom: 1px solid black;">{{ $value['qty_raw_dish'] }}</td>
                        <td valign="middle" align="center" style="word-wrap: break-word;border-right: 1px solid black; border-bottom: 1px solid black;">{{ $value['qty_cooked_dish'] }}</td>
                    </tr>
                @else
                    <tr>
                        <td valign="middle" style="border-right: 1px solid black">{{ $value['dish_name'] }}</td>
                        <td valign="middle" align="center" style="word-wrap: break-word; border-right: 1px solid black">{{ $value['qty_raw_dish'] }}</td>
                        <td valign="middle" align="center" style="word-wrap: break-word; border-right: 1px solid black">{{ $value['qty_cooked_dish'] }}</td>
                    </tr>
                @endif

            @endforeach
        @endforeach
        </tbody>
    </table>
    <table class="footer_table">
        <tr style="font-size: 6pt">
            <td colspan="5" rowspan="2">
                <i>(Khuyến cáo các món ăn kết hợp không có lợi cho sức khỏe: Cá với sữa, trứng với sữa, Tôm với sữa, Bún, Phở với sữa - Rau,
                    <br>
                    canh có thể thay đổi theo mùa, hoặc do điều kiện khách quan)</i>
            </td>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="2" style="text-align: left"><i>Thực đơn gửi ngày:</i></td>
            <td colspan="3" style="text-align: right">Hà Nội, ngày …… tháng …… năm 20……</td>
        </tr>
        <tr>
            <td colspan="1" align="left"><b>Người lập</b></td>
            <td colspan="1" align="center"><b>Bếp trưởng</b></td>
            <td colspan="2" align="center"><b>P.Kỹ thuật chế biến món ăn</b></td>
            <td colspan="1" align="right"><b>Đại diện nhà trường</b></td>
        </tr>
    </table>
    <tr></tr>
@endforeach

</body>
</html>