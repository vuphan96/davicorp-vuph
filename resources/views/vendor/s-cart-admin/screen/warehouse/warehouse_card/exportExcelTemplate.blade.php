<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BaoCaoTheKho</title>
</head>

<body>
<table>
    <thead>
    <tr>
        <th colspan="9" align="center" style="border-right: 1px solid black ">BÁO CÁO THẺ KHO</th>
    </tr>
    <tr>
        <th colspan="9" align="center" style="border-right: 1px solid black">
            Từ ngày {{ $dataSearch['from_to'] }} đến ngày {{ $dataSearch['end_to'] }}
        </th>
    </tr>
    <tr>
        <th colspan="9" align="right" style="border-right: 1px solid black ">Tồn đầu: {{$qtyStockBegin ?? ''}}</th>
    </tr>
    <tr>
        <th align="center" style="border: 1px solid black">Ngày chứng từ</th>
        <th align="center" style="border: 1px solid black">Mã sản phẩm</th>
        <th align="center" style="border: 1px solid black">Tên sản phẩm</th>
        <th align="center" style="border: 1px solid black">Mã phiếu</th>
        <th align="center" style="border: 1px solid black">Diễn giải</th>
        <th align="center" style="border: 1px solid black">Số lượng nhập</th>
        <th align="center" style="border: 1px solid black">Sô lượng xuất</th>
        <th align="center" style="border: 1px solid black">Số lượng tồn kho</th>
        <th align="center" style="border: 1px solid black">Tên đối tượng</th>
    </tr>
    </thead>
    <tbody>
    @php
        $qtyImportAmount = $data->sum('qty_import');
        $qtyExportAmount = $data->sum('qty_export');
        $qtyStockAmount = $data->sum('qty_stock');
    @endphp
    @forelse($data as $keyProduct => $product)
        <tr>
            <td style="border-right: 1px solid black">{{formatDateVn($product->bill_date)}}</td>
            <td style="border-right: 1px solid black">{{$product->product_code ?? ''}}</td>
            <td style="border-right: 1px solid black">{{$product->product_name ?? ''}}</td>
            <td style="border-right: 1px solid black">{{$product->order_id_name ?? ''}}</td>
            <td style="border-right: 1px solid black">{{$product->explain ?? ''}}</td>
            <td style="border-right: 1px solid black">{{$product->qty_import ?? ''}}</td>
            <td style="border-right: 1px solid black">{{$product->qty_export ?? ''}}</td>
            <td style="border-right: 1px solid black">{{$product->qty_stock ?? ''}}</td>
            <td style="border-right: 1px solid black">{{$product->object_name ?? ''}}</td>
        </tr>
    @empty
        <td colspan="8">Không có nội dung!</td>
    @endforelse
    </tbody>
    <tr>
        <th align="center" style="border-top: 1px solid black"></th>
        <th align="center" style="border-top: 1px solid black"></th>
        <th align="center" style="border-top: 1px solid black"></th>
        <th align="center" style="border-top: 1px solid black"></th>
        <th align="center" style="border-top: 1px solid black; font-weight: bold">Tổng cộng</th>
        <th style="border-top: 1px solid black; font-weight: bold">{{$qtyImportAmount}}</th>
        <th style="border-top: 1px solid black; font-weight: bold">{{$qtyExportAmount}}</th>
        <th style="border-top: 1px solid black; font-weight: bold">{{$qtyStockAmount}}</th>
        <th align="center" style="border-top: 1px solid black"></th>
    </tr>
</table>
</body>
</html>