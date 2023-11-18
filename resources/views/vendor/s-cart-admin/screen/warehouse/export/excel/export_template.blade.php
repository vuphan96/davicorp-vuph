
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Đơn xuất- {{ $data->id_name }}</title>
</head>
<body>
<table>
    <tr>
        <td colspan="8" rowspan="3" style="text-align: left; vertical-align: middle">
            Công ty Cổ phần Davicorp Việt Nam
            <br/>Số 34B, Lô 2, Đền Lừ 1, Hoàng Văn Thụ, Hoàng Mai, Hà Nội<br>
        </td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr>
        <th colspan="8" style="font-weight: bold; text-align: center; font-size: 14pt">PHIẾU XUẤT KHO</th>
    </tr>
    <br>
    <tr>
        <td colspan="8" align="center" style="font-size: 12pt">Ngày {{ \Carbon\Carbon::make($data->date_export)->format('d') }}
            tháng {{ \Carbon\Carbon::make($data->date_export)->format('m') }}
            năm {{ \Carbon\Carbon::make($data->date_export)->format('Y') }}</td>
    </tr>
    <tr>
        <td colspan="4" style="font-size: 12pt">Họ tên người giao hàng: </td>
        <td colspan="4" style="font-size: 12pt; border: none">Chọn kho xuất: {{ $data->warehouse_name ?? ''}}</td>
    </tr>
    <tr>
        <td colspan="8" style="font-size: 12pt">Đơn vị: {{ $data->customer_name ?? ''}}</td>
    </tr>
    <tr>
        <td colspan="8" style="font-size: 12pt">Địa chỉ: {{ $data->customer_addr ?? ''}}</td>
    </tr>
    <tr>
        <td colspan="8" style="font-size: 12pt">Lý do xuất: {{ $data->note ?? ''}}</td>
    </tr>

    <thead>
    <tr style="width: 100%" class="heading-report">
        <th  align="center" style="border: 1px solid black;font-weight: bold;background-color: #EDF5FF">STT</th>
        <th  align="left" style="border: 1px solid black;font-weight: bold;background-color: #EDF5FF">Mã vật tư</th>
        <th  align="left" colspan="3" style="border: 1px solid black;font-weight: bold;background-color: #EDF5FF">Tên vật tư</th>
        <th  align="left" style="border: 1px solid black;font-weight: bold;background-color: #EDF5FF">ĐVT</th>
        <th  align="right" style="border: 1px solid black;font-weight: bold;background-color: #EDF5FF">Số lượng</th>
        <th  align="right" style="border: 1px solid black;font-weight: bold;background-color: #EDF5FF">Ghi chú</th>
    </tr>
    </thead>
    <tbody >
{{--    @forelse($data->details as $key => $detail)--}}
    @php
        $i = 0;
    @endphp
    @forelse($data->details->groupBy('product_id') as $key => $detail)
        @php
            $qty = $detail->sum('qty');
            $arrNote = [];
        @endphp
        @foreach($detail as $item)
            @php
                $arrNote[] = ($item['comment'] && $item['order_id_name']) ? $item['order_id_name']. ': '.$item['comment'] : $item['comment'];
            @endphp
        @endforeach
        <tr>
            <td align="center" style="border-right: 1px solid black;border-left: 1px solid black;border-bottom: 1px solid black">{{ $i + 1 }}</td>
            <td style="border-right: 1px solid black; text-align: left;border-bottom: 1px solid black">{{ $detail->first()['product_sku'] ?? '' }}</td>
            <td colspan="3" style="border-right: 1px solid black; text-align: left;border-bottom: 1px solid black">{{ $detail->first()['product_name'] ?? '' }}</td>
            <td style="border-right: 1px solid black; text-align: left;border-bottom: 1px solid black">{{ $detail->first()['unit'] ?? ''  }}</td>
            <td style="border-right: 1px solid black; text-align: right;border-bottom: 1px solid black">{{ $qty }}</td>
            <td style="border-right: 1px solid black; text-align: left;border-bottom: 1px solid black">
                {{ implode(' ; ', $arrNote) }}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7">Không có dữ liệu!</td>
        </tr>
    @endforelse
    </tbody>
    <tr>
        <td colspan="8" style="border: 1px solid white; border-bottom: 1px solid #D4D4D4; "></td>
    </tr>
    <tr>
        <td colspan="3" align="center"><b>NGƯỜI GIAO HÀNG</b></td>
        <td colspan="3" align="center"><b>NGƯỜI NHẬN HÀNG</b></td>
        <td colspan="2"  align="center"><b>THỦ KHO</b></td>
    </tr>
    <tr>
        <td colspan="3" rowspan="3" align="center" style="vertical-align: top"><i style="font-weight: 400">(Kí, họ tên)</i></td>
        <td colspan="3" rowspan="3" align="center" style="vertical-align: top"><i style="font-weight: 400">(Kí, họ tên)</i></td>
        <td colspan="2" rowspan="3" align="center" style="vertical-align: top"><i style="font-weight: 400">(Kí, họ tên)</i></td>
    </tr>

</table>
</body>
</html>