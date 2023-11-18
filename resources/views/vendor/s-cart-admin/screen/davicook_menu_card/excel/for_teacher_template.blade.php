
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Phiếu cô</title>
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
            <th colspan="1" align="center" style="font-weight: bold; border: 1px solid black">THỨ</th>
            <th colspan="2" align="center" style="font-weight: bold; border: 1px solid black">MÓN ĂN</th>
            <th colspan="2" align="center" style="font-weight: bold; border: 1px solid black">GHI CHÚ</th>
        </tr>
        </thead>
        <tbody>
        @foreach($item['first_details'] as $detail)
            <tr>
                <td valign="middle" rowspan="{{ $detail['count'] }}" align="center" style="border: 1px solid black; word-wrap: break-word;"><span>{{ $detail['day'] }}</span><br><span>{{ $detail['date'] }}</span></td>
                <td valign="middle" colspan="2" style="border-right: 1px solid black; border-bottom: {{ $detail['count'] == 1 ? '1px solid black' : '' }} ">{{ $detail['dish_name'] }}</td>
                <td rowspan="{{ $detail['count'] }}" colspan="2" style="vertical-align: middle;border: 1px solid black"></td>
            @foreach($detail['item'] as $keyNo => $value)
                @if($detail['count'] == $keyNo + 2)
                    <tr>
                        <td colspan="2" valign="middle" style="border-right: 1px solid black; border-bottom: 1px solid black;">{{ $value['dish_name'] }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="2" valign="middle" style="border-right: 1px solid black">{{ $value['dish_name'] }}</td>
                    </tr>
                @endif

            @endforeach
        @endforeach
        </tbody>
    </table>
    <table class="footer_table">
        <tr style="font-size: 6px">
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