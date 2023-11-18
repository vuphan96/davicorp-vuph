
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Đơn hàng - {{ $order->id_name }}</title>
</head>
<body>
<table>
    <tr>
        <td colspan="2" rowspan="3" style="padding-right: 0.5cm; text-align: center"><img width="60px" style="margin-left: 10px" class="logo" src="{{ public_path('images/print_assets/davicorp.png')}}"/>
        </td>
        <td  class="company_title" colspan="5" rowspan="3" style="text-align: left; vertical-align: middle; word-wrap: break-word;">
            <b>{{ $order->customer->department->name ?? $order->departments->name }}</b>
            <br/>{{ $order->customer->department->address ?? $order->departments->address }}
            <br/>{{ $order->customer->department->contact ?? $order->departments->contact }}
        </td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr>
        <th colspan="7" style="font-weight: bold; text-align: center; font-size: 14pt">PHIẾU XUẤT KHO</th>
    </tr>
    <br>
    <tr>
        <td colspan="7" align="center" style="font-size: 12pt">Ngày {{ \Carbon\Carbon::make($order->bill_date)->format('d') }}
            tháng {{ \Carbon\Carbon::make($order->bill_date)->format('m') }}
            năm {{ \Carbon\Carbon::make($order->bill_date)->format('Y') }}</td>
    </tr>
    <tr>
        <td colspan="7" align="center" style="font-size: 12pt">Số {{ $order->id_name ?? ''}}</td>
    </tr>
    <tr>
        <td colspan="7" style="font-size: 12pt">Tên khách hàng: {{ $order->object_id == 1 ? $order->name . ' - GV' : $order->name }}</td>
    </tr>
    <tr>
        <td colspan="7" style="font-size: 12pt">Địa chỉ: {{ $order->address ?? ''}}</td>
    </tr>
    <tr>
        <td colspan="4" style="font-size: 12pt">Lý do xuất: {{ ($order->explain ?? '') }}</td>
        <td colspan="2" style="font-size: 12pt">Tuyến hàng: {{ $order->customer->route ?? ''}}</td>
        <td colspan="1" style="font-size: 12pt"> STT: {{ $order->customer->order_num ?? ''}}</td>
    </tr>
    <thead>
    <tr style="width: 100%" class="heading-report">
        <th  align="center" style="border: 1px solid black;font-weight: bold">STT</th>
        <th  align="left" colspan="2" style="border: 1px solid black;font-weight: bold">Tên hàng hoá, dịch vụ</th>
        <th  align="left" style="border: 1px solid black;font-weight: bold">Đvt</th>
        <th  align="right" style="border: 1px solid black;font-weight: bold">Số lượng</th>
        <th  align="right" style="border: 1px solid black;font-weight: bold">Đơn giá</th>
        <th  align="right" style="border: 1px solid black;font-weight: bold">Thành tiền</th>
    </tr>
    </thead>
    <tbody>
    @forelse($order->details as $key => $detail)
        <tr>
            <td align="center" style="border-right: 1px solid black;border-left: 1px solid black;">{{ $key + 1 }}</td>
            <td colspan="2" style="border-right: 1px solid black; text-align: left;">{{ $detail->product_name }}</td>
            <td style="border-right: 1px solid black; text-align: left;">{{ $detail->product_unit }}</td>
            <td style="border-right: 1px solid black; text-align: right;">{{ $detail->qty }}</td>
            <td style="border-right: 1px solid black; text-align: right;">{{ $detail->price }}</td>
            <td style="border-right: 1px solid black; text-align: right;">{{ $detail->total_price }}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7">Không có dữ liệu!</td>
        </tr>
    @endforelse
    <tr>
        <td style="border-right: 1px solid black;border-left: 1px solid black;"></td>
        <td colspan="2" style="border-right: 1px solid black;"></td>
        <td style="border-right: 1px solid black;"></td>
        <td style="border-right: 1px solid black;"></td>
        <td style="border-right: 1px solid black;"></td>
        <td style="border-right: 1px solid black;"></td>
    </tr>
    <tr>
        <td colspan="6" style="font-weight: bold;border: 1px solid black; text-align: center"><b>Cộng tiền bán hàng hóa, dịch vụ</b></td>
        <td class="currency" style="font-weight: bold;border-top: 1px solid black;border-right: 1px solid black;border-bottom: 1px solid black;">{{ $order->total }}</td>
    </tr>
    <tr>
        <td colspan="7" style="font-weight: bold">Số tiền bằng chữ: {{ convert_number_to_words($order->total ?? 0, true) }}</td>
    </tr>
    </tbody>
    <tr>
        <td colspan="3" align="center"><b>Bên mua hàng</b></td>
        <td colspan="1"></td>
        <td colspan="3" align="center"><b>Bên bán hàng</b></td>
    </tr>
    <tr>
        <td colspan="3" align="center"><i style="font-weight: 400">(Kí, họ tên)</i></td>
        <td colspan="1"></td>
        <td colspan="3" align="center"><i style="font-weight: 400">(Kí, họ tên)</i></td>
    </tr>
</table>
</body>
</html>