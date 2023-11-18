<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Suất ăn - {{ $order->first()->id_name }}</title>
</head>
<body>
<table>
    <tr>
        <td colspan="2" style="padding-right: 0.5cm; text-align: center"><img width="130" style="display: block; margin-left: 10px" class="logo" src="{{ public_path('images/print_assets/davicorp.png')}}"/>
        </td>
        <td  class="company_title" colspan="3" style="text-align: left; vertical-align: middle">
            <b>{{ 'CÔNG TY CỔ PHẦN DAVICOOK HÀ NỘI' }}</b><br/>{{ 'Xóm 10,Thôn 3 Xã Yên Mỹ, Huyện Thanh Trì, TP Hà Nội' }}
            <br/>{{ 'ĐT: 024.22117272; 024.32004165' }}
        </td>
    </tr>
</table>
<table>
    <thead>
    <tr>
        <th colspan="5" style="font-weight: bold; text-align: center; font-size: 14pt">PHIẾU XUẤT KHO</th>
    </tr>
    </thead>
    <br>
    <tbody>
    <tr>
        <td colspan="5" align="center" style="font-size: 12pt">{{ formatStringDate(($order->first()->bill_date), 'Ngaythangnam') }}</td>
    </tr>
    <tr>
        <td colspan="5" align="center" style="font-size: 12pt">Số {{ $order->first()->id_name ?? ''}}</td>
    </tr>
    <tr>
        <td colspan="5" style="font-size: 12pt">Tên khách hàng: {{ $order->first()->customer_name }}</td>
    </tr>
    <tr>
        <td colspan="5" style="font-size: 12pt">Địa chỉ: {{ $order->first()->address ?? ''}}</td>
    </tr>
    <tr>
        <td colspan="2" style="font-size: 12pt">Lý do xuất: {{ ($order->first()->explain ?? '') }}</td>
        <td colspan="2" style="font-size: 12pt">Tuyến hàng: {{ $order->first()->customer->route ?? ''}}</td>
        <td colspan="1" style="font-size: 12pt"> STT: {{ $order->first()->customer_num ?? ''}}</td>
    </tr>
    </tbody>
    <thead>
    <tr>
        <th  align="center" style="border: 1px solid black;font-weight: bold">STT</th>
        <th  align="left" style="border: 1px solid black;font-weight: bold">Tên hàng hoá, dịch vụ</th>
        <th  align="left" style="border: 1px solid black;font-weight: bold">ĐVT</th>
        <th  align="right" style="border: 1px solid black;font-weight: bold">Số lượng</th>
        <th  align="right" style="border: 1px solid black;font-weight: bold">giá</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td align="center" style="border-right: 1px solid black;border-left: 1px solid black;">1</td>
        <td style="border-right: 1px solid black; text-align: left;">Suất ăn</td>
        <td style="border-right: 1px solid black; text-align: left;">Suất</td>
        <td style="border-right: 1px solid black; text-align: right;">{{ $order->sum('number_of_reality_servings') ?? 0 }}</td>
        <td style="border-right: 1px solid black; text-align: right;">{{ $order->first()->customer->serving_price ?? $order->first()->price_of_servings }}</td>
    </tr>
    <tr>
        <td style="border-right: 1px solid black;border-left: 1px solid black;"></td>
        <td style="border-right: 1px solid black;"></td>
        <td style="border-right: 1px solid black;"></td>
        <td style="border-right: 1px solid black;"></td>
        <td style="border-right: 1px solid black;"></td>
    </tr>
    @php
        $sum = ($order->sum('number_of_reality_servings'))
                        * ($order->first()->customer->serving_price ?? $order->first()->price_of_servings);
    @endphp
    <tr>
        <td colspan="4" style="border-left: 1px solid black;font-weight: bold;border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align: center"><b>Cộng tiền bán hàng hóa, dịch vụ</b></td>
        <td class="currency" style="font-weight: bold;border-top: 1px solid black;border-right: 1px solid black;border-bottom: 1px solid black;">{{ $sum }}</td>
    </tr>
    <tr>
        <td colspan="5" style="border-left: 1px solid black;font-weight: bold">Số tiền bằng chữ: {{ convert_number_to_words($sum ?? 0, true) }}</td>
    </tr>
    </tbody>
</table>

<table class="foot_table">
    <tr>
        <td colspan="2" align="center"><b>Bên mua hàng</b></td>
        <td colspan="1"></td>
        <td colspan="2" align="center"><b>Bên bán hàng</b></td>
    </tr>
    <tr>
        <td colspan="2" align="center"><i style="font-weight: 400">(Kí, họ tên)</i></td>
        <td colspan="1"></td>
        <td colspan="2" align="center"><i style="font-weight: 400">(Kí, họ tên)</i></td>
    </tr>
</table>
</body>
</html>