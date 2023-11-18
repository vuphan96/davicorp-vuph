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
    @endphp
    @forelse($dataSupplier as $keySupplier => $itemSupplier)
        <tr>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800;"></td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800;"></td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800; background-color: yellow">{{ $keySupplier ? mb_strtoupper($itemSupplier->first()->supplier_name, 'UTF-8') : 'Nhà cung cấp đã bị xóa' }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800;"></td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800;"></td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800;"></td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800;"></td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black; font-weight: 800;"></td>
        </tr>
        @php
            $dataProduct = $itemSupplier->groupBy('product_id')->sortBy('sort');
            $i = 1;
        @endphp
        @foreach($dataProduct as $itemProducts)
            @php
                $arrProduct = [];
            @endphp
            @foreach($itemProducts as $product)
                @php
                    $price = '';
                    $date = ($dataSearch['select_warehouse'] == 2) ? $product->bill_date : $product->delivery_date;
                @endphp
                @if($date >= $product->start_date && $date <= $product->end_date)
                    @php
                        $price = $product->price;
                    @endphp
                @endif
                @php
                    $arrProduct[] = [
                        'code' => $product->product_code ?? '',
                        'name' => $product->product_name ?? '',
                        'dvt' => $product->product_unit ?? '',
                        'qty_order' => $product->qtyProduct ?? '',
                        'price_import' => $price ?? '',
                        'into_money' =>$price ? ((float)$price ?? '') * ((float)$product->qtyProduct ?? '') : '',
                        'note' => $product->comment,
                        'customer_name' => $product->customer_name,
                    ];
                    $total_qty += (float)$product->qtyProduct;
                    $total_money += ((float)$product->qtyProduct ?? '') * ((float)$price ?? '');
                @endphp
            @endforeach
            @php
                $productCollect = collect($arrProduct)->groupBy('price_import');
            @endphp
            @foreach($productCollect as $row)
                @php
                    $comment = '';
                    $sumQty = $row->sum('qty_order');
                @endphp
                @foreach($row as $item)
                    @if($item['note'] != '')
                        @php
                            $comment .= '<span>' . $item['customer_name'] . ' : ' . $item['note'] . '</span><br>';
                        @endphp
                    @endif
                @endforeach
                <tr>
                    <td valign="center" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black"> {{ $i }}</td>
                    <td valign="center" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $row->first()['code'] ?? '' }}</td>
                    <td valign="center" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $row->first()['name'] ?? '' }}</td>
                    <td valign="center" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $row->first()['dvt'] ?? '' }}</td>
                    <td valign="center" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ $sumQty ?? '' }}</td>
                    <td align="right" valign="center" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ (float)$row->first()['price_import'] ?? '' }}</td>
                    <td align="right" valign="center" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{{ ((float)$row->first()['price_import'] ?? '') * ((float)$sumQty ?? '') }}</td>
                    <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px dotted black">{!! $comment !!}</td>
                </tr>
                @php
                    $i++;
                @endphp
            @endforeach
        @endforeach
    @empty
        <td colspan="6">Không có nội dung!</td>
    @endforelse
        <tr>
            <td align="right" colspan="4">Tổng cộng</td>
            <td align="right" colspan="1" style="font-weight: bold">{{ (float)$total_qty }}</td>
            <td colspan="1"></td>
            <td align="right" colspan="1" style="font-weight: bold">{{ (float)$total_money }}</td>
            <td colspan="1"></td>
        </tr>
    </tbody>
</table>
</body>
</html>