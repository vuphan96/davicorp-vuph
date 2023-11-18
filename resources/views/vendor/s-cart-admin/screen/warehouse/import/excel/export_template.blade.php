
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Đơn hàng nhập - {{ $dataImport->id_name }}</title>
</head>
<body>
<table>
    <tr>
        <th colspan="1" rowspan="3" style="padding-right: 0.5cm; text-align: center"><img width="60px" style="margin-left: 10px" class="logo" src="{{ public_path('images/print_assets/davicorp.png')}}"/>
        </th>
        <th colspan="7" rowspan="3" style="text-align: left; vertical-align: middle">
            <b>Công ty Cổ phần Davicorp Việt Nam</b>
            <br/>Số 34B, Lô 2, Đền Lừ 1, Hoàng Văn Thụ, Hoàng Mai, Hà Nội
        </th>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr>
        <th colspan="8" style="font-weight: bold; text-align: center; font-size: 14pt">PHIẾU NHẬP KHO</th>
    </tr>
    <br>
    <tr>
        <td colspan="8" align="center" style="font-size: 12pt">Ngày {{ \Carbon\Carbon::make($dataImport->delivery_date)->format('d') }}
            tháng {{ \Carbon\Carbon::make($dataImport->delivery_date)->format('m') }}
            năm {{ \Carbon\Carbon::make($dataImport->delivery_date)->format('Y') }}</td>
    </tr>
    <tr>
        <td colspan="8" align="right" style="font-size: 12pt">Số hóa đơn: {{ $dataImport->id_name ?? ''}}</td>
    </tr>
    <tr>
        <td colspan="8" align="right" style="font-size: 12pt">Kho nhập: {{ $dataImport->warehouse_name ?? ''}}</td>
    </tr>
    <tr>
        <td colspan="8" style="font-size: 12pt">Họ tên người giao hàng: </td>
    </tr>
    <tr>
        <td colspan="8" style="font-size: 12pt">Tên đơn vị: {{ $dataImport->supplier_name ?? ''}}</td>
    </tr>
    <tr>
        <td colspan="8" style="font-size: 12pt">Địa chỉ: {{ $dataImport->address ?? ''}}</td>
    </tr>
    <tr>
        <td colspan="8" style="font-size: 12pt">Lý do nhập: </td>
    </tr>
    <thead>
    <tr style="width: 100%" class="heading-report">
        <th align="center" style="border: 1px solid black;font-weight: bold">STT</th>
        <th style="border: 1px solid black;font-weight: bold">Mã vật tư</th>
        <th style="border: 1px solid black;font-weight: bold">Tên vật tư</th>
        <th style="border: 1px solid black;font-weight: bold">Đvt</th>
        <th>Số lượng</th>
        <th>Giá</th>
        <th>Thành tiền</th>
        <th>Ghi chú</th>
    </tr>
    </thead>
    <tbody>
        @forelse($dataImport->details as $key => $detail)
            <tr>
                <td align="center" style="border-right: 1px solid black;border-left: 1px solid black;">{{ $key + 1 }}</td>
                <td style="border-right: 1px solid black">{{ $detail->product_code }}</td>
                <td style="border-right: 1px solid black;">{{ $detail->product_name }}</td>
                <td style="border-right: 1px solid black;">{{ $detail->unit_name }}</td>
                <td style="border-right: 1px solid black;">{{ $detail->qty_reality }}</td>
                <td style="border-right: 1px solid black;">{{ $detail->product_price }}</td>
                <td style="border-right: 1px solid black;">{{ $detail->amount_reality }}</td>
                <td style="border-right: 1px solid black;">{{ $detail->comment }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7">Không có dữ liệu!</td>
            </tr>
        @endforelse
        <tr>
            <td align="center" style="border-right: 1px solid black;border-left: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="border-right: 1px solid black;">Tổng Cộng</td>
            <td style="border-right: 1px solid black;">{{ $dataImport->total_reality }}</td>
            <td style="border-right: 1px solid black;"></td>
        </tr>
    </tbody>
    <tr>
        <td colspan="8" style="border-top: 1px solid black;"></td>
    </tr>
    <tr>
        <td colspan="8" style="font-weight: bold">Số tiền(Viết bằng chữ): {{ convert_number_to_words($dataImport->total_reality ?? 0, true) }}</td>
    </tr>
    <tr>
        <td colspan="8"><b></b></td>
    </tr>

    <tr>
        <td colspan="2" align="center"><b>NGƯỜI GIAO HÀNG</b></td>
        <td colspan="2" align="center"><b>NGƯỜI NHẬN HÀNG</b></td>
        <td colspan="2" align="center"><b>THỦ KHO</b></td>
        <td colspan="2" align="center"><b>GIÁM ĐỐC</b></td>
    </tr>
    <tr>
        <td colspan="2" align="center"><i style="font-weight: 400">(Kí, họ tên)</i></td>
        <td colspan="2" align="center"><i style="font-weight: 400">(Kí, họ tên)</i></td>
        <td colspan="2" align="center"><i style="font-weight: 400">(Kí, họ tên)</i></td>
        <td colspan="2" align="center"><i style="font-weight: 400">(Kí, họ tên, đóng dấu)</i></td>
    </tr>
</table>
</body>
</html>