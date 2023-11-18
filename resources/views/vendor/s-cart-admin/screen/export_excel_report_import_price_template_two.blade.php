<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BaoCaonhaphangChiTiet</title>
</head>

<body>
<table>
    @php
        $from_to = $dataSearch['from_to'] ?? convertVnDateObject($dataSearch['from_to']);
        $end_to = $dataSearch['end_to'] ?? convertVnDateObject($dataSearch['end_to']);
    @endphp
    <thead>

    <tr>
        <th colspan="8" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="8" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="8" align="center"
            style="font-size: 16px; font-weight: 700; border: 1px solid white; border-right: 1px solid #E0E0E0"><h1>BÁO CÁO NHẬP HÀNG CHI TIẾT THEO MẶT HÀNG</h1>
        </th>
    </tr>
    <tr>
        <th colspan="8" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="8" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0">Từ
            ngày {{  $from_to }} đến ngày {{ $end_to }}</th>
    </tr>
    <tr>
        <th colspan="8" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0">Kho: -</th>
    </tr>
    <tr>
        <th colspan="8" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>

    <tr>
        <th align="center" style="font-weight: 800; border: 1px solid black">Stt</th>
        <th align="center" style="font-weight: 800; border: 1px solid black">Mã vật tư</th>
        <th align="center" style="font-weight: 800; border: 1px solid black">Tên vật tư</th>
        <th align="center" style="font-weight: 800; border: 1px solid black">Đvt</th>
        <th align="center" style="font-weight: 800; border: 1px solid black">Số lượng đặt</th>
        <th align="center" style="font-weight: 800; border: 1px solid black">đơn giá nhập</th>
        <th align="center" style="font-weight: 800; border: 1px solid black">Thành tiền</th>
        <th align="center" style="font-weight: 800; border: 1px solid black">Ghi chú</th>
    </tr>
    </thead>
    <tbody>
    @php
        $total_qty = 0;
        $total_money = 0;
        $i = 1;
    @endphp
    @forelse(session('dataReportImportPriceTemplateTwo') as $keySupplier => $itemSupplier)
        <tr>
            <td valign="center" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black"> {{ $itemSupplier['code'] == '' ? '' : $i }}</td>
            <td valign="center" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $itemSupplier['code'] ?? '' }}</td>
            @if($itemSupplier['code'] == '')
                @php
                    $i = 0;
                @endphp
                <td valign="center" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: bold">{{ $itemSupplier['name'] ?? '' }}</td>
            @else
                <td valign="center" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $itemSupplier['name'] ?? '' }}</td>
            @endif
            <td valign="center" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $itemSupplier['product_unit'] ?? '' }}</td>
            <td valign="center" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $itemSupplier['qty_order'] ?? '' }}</td>
            <td align="right" valign="center" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $itemSupplier['price_import'] != '' ? $itemSupplier['price_import'] : '' }}</td>
            <td align="right" valign="center" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $itemSupplier['price_import'] != '' ?  ((float)$itemSupplier['price_import'] ?? 0) * ((float)$itemSupplier['qty_order'] ?? 0) : '' }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{!! $itemSupplier['note'] ?? '' !!}</td>
        </tr>
        @php
            $total_qty += (float)$itemSupplier['qty_order'];
            $total_money += ((float)(float)$itemSupplier['price_import'] ?? 0) * ((float)$itemSupplier['qty_order'] ?? 0);
        @endphp
        @php
            $i++;
        @endphp
    @empty
        <td colspan="6">Không có dữ liệu!</td>
    @endforelse
    <tr>
        <td style="text-align: left; font-weight: bold"  colspan="4">Tổng cộng</td>
        <td align="right" colspan="1" style="font-weight: bold">{{ (float)$total_qty }}</td>
        <td colspan="1"></td>
        <td align="right" colspan="1" style="font-weight: bold">{{ (float)$total_money }}</td>
        <td colspan="1"></td>
    </tr>
    </tbody>
</table>
</body>
</html>