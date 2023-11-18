<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BaoCaoHangNhapNhomTheo2ChiTieu</title>
</head>

<body>
<table>
    @php
        $from_to = $dataSearch['from_to'] ?? convertVnDateObject($dataSearch['from_to']);
         $end_to = $dataSearch['end_to'] ?? convertVnDateObject($dataSearch['end_to']);
    @endphp
    <thead>

    <tr>
        <th colspan="7" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="7" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="7" align="center"
            style="font-size: 16px; font-weight: 700; border: 1px solid white; border-right: 1px solid #E0E0E0"><h1>BÁO CÁO HÀNG NHẬP NHÓM THEO 2 CHỈ TIÊU</h1>
        </th>
    </tr>
    <tr>
        <th colspan="7" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="7" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0">Từ
            ngày {{  $from_to }} đến ngày {{ $end_to }}</th>
    </tr>
    <tr>
        <th colspan="7" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0">Kho: -</th>
    </tr>
    <tr>
        <th colspan="7" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="7" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>

    <tr>
        <th align="center" style="font-weight: 800; border: 1px solid black">Stt</th>
        <th align="center" style="font-weight: 800; border: 1px solid black">Mã</th>
        <th align="center" style="font-weight: 800; border: 1px solid black">Tên</th>
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
    @endphp
    @forelse($dataSupplier->groupBy('supplier_code') as $keySupplier => $itemSupplier)
        <tr>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800;"></td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800;"></td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800; background-color: yellow">{{ $keySupplier ? mb_strtoupper($itemSupplier->first()['supplier_name'], 'UTF-8') : 'Nhà cung cấp đã bị xóa' }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800;"></td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800;"></td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800;"></td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800;"></td>
        </tr>
        @foreach($itemSupplier->groupBy('product_id') as $itemProduct)
            @php
                $j = 1;
            @endphp
            <tr>
                <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black;font-weight: 800;"></td>
                <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800;">{{  $itemProduct->first()['product_code'] ?? '' }}</td>
                <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800;">{{ mb_strtoupper($itemProduct->first()['product_name'], 'UTF-8') ?? '' }}</td>
                <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800;">{{ $itemProduct->sum('qtyProduct') ?? '' }}</td>
                <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800;"></td>
                <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800;"></td>
                <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800;"></td>
            </tr>
            @foreach($itemProduct as $orderDetail)
                <tr>
                    <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $j }}</td>
                    <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black"></td>
                    <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $orderDetail['customer_name'] ? $orderDetail['customer_name'] . ( isset($orderDetail['object_id']) ? ($orderDetail['object_id'] == 1 ? ' - GV' : '') : '') : '' }}</td>
                    <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $orderDetail['qtyProduct'] }}</td>
                    <td align="right" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $orderDetail['price'] ? (float)$orderDetail['price'] : ''}}</td>
                    <td align="right" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $orderDetail['price'] ? ((float)$orderDetail['qtyProduct'] ?? '') *  ((float)$orderDetail['price'] ?? '') : ' ' }}</td>
                    <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{!! $orderDetail['comment'] !!}</td>
                </tr>
                @php
                    $total_qty += $orderDetail['qtyProduct'];
                    $total_money += ((float)$orderDetail['qtyProduct'] ?? '') *  ((float)$orderDetail['price'] ?? '');
                    $j++ ;
                @endphp
            @endforeach
        @endforeach
    @empty
        <td colspan="6">Không có nội dung!</td>
    @endforelse
    <tr>
        <td align="right" colspan="3" style="font-weight: bold">Tổng cộng</td>
        <td align="right" colspan="1" style="font-weight: bold">{{ (float)$total_qty }}</td>
        <td colspan="1"></td>
        <td align="right" colspan="1" style="font-weight: bold;">{{ (float)$total_money }}</td>
        <td colspan="1"></td>
    </tr>
    </tbody>
</table>
</body>
</html>