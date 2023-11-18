<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<table>
    <thead>
    <tr>
        <th>Mã sản phẩm (*)</th>
        <th>Tên sản phẩm (*)</th>
        <th>Mã danh mục (*)</th>
        <th>Danh mục sản phẩm</th>
        <th>Tên hiển thị trên tem (*)</th>
        <th>STT trên tem (*)</th>
        <th>Tên hiển thị trên hoá đơn</th>
        <th>Định mức tối thiểu</th>
        <th>Link mã QRcode</th>
        <th>Đơn vị tính</th>
        <th>Loại mặt hàng (*)</th>
        <th>Mức độ ưu tiên (*)</th>
        <th style="word-wrap:break-word">Thuế suất áp dụng cho KH lấy hóa đơn cửa hàng (*)</th>
        <th style="word-wrap:break-word">Thuế suất cho KH là công ty xuất hóa đơn từ công ty (*)</th>
        <th style="word-wrap:break-word">Thuế suất áp dụng cho KH là trường học lấy hóa đơn công ty (*)</th>
        <th>Trạng thái (*)</th>
        <th>Hạn mức báo cáo</th>
        <th>Mã kho</th>
        <th>Tên kho</th>
        <th>Số lượng tồn kho</th>
    </tr>
    </thead>
    <tbody>
    @forelse($data as $datum)
        <tr>
            <td>{{ $datum->sku ?? ''}}</td>
            <td>{{ $datum->name ?? ''}}</td>
            <td>{{ $datum->category->sku ?? ''}}</td>
            <td>{{ $datum->category->name ?? 'Danh mục đã bị xoá' }}</td>
            <td>{{ $datum->short_name ?? ''}}</td>
            <td>{{ $datum->order_num ?? ''}}</td>
            <td>{{ $datum->bill_name ?? ''}}</td>
            <td>{{ $datum->minimum_qty_norm ?? '' }}</td>
            <td>{{ $datum->qr_code ?? '' }}</td>
            <td>{{ $datum->unit->name ?? '' }}</td>
            <td>{{ $datum->kind == 0 ? 'Hàng khô' : 'Hàng tươi sống' }}</td>
            <td>{{ $datum->purchase_priority_level == 1 ? 'Hàng cần đặt hàng ngay' : 'Hàng thường' }}</td>
            <td>{{ $datum->tax_default ?? '' }}</td>
            <td>{{ $datum->tax_company ?? '' }}</td>
            <td>{{ $datum->tax_school ?? '' }}</td>
            <td>{{ $datum->status ?? '' }}</td>
            <td>{{ $datum->qty_limit ?? '' }}</td>
            @foreach($datum["warehouse"] as $warehouse)
                @if($loop->iteration === 1)
                    @foreach($dataWarehouse as $key=> $data)
                        @if ($data->id === $warehouse['warehouse_id'])
                            <td>{{$data->warehouse_code}}</td>
                            <td>{{$data->name}}</td>
                        @endif
                    @endforeach
                <td>{{$warehouse['qty'] }}</td>
                @else
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                @foreach($dataWarehouse as $key=> $data)
                    @if ($data->id === $warehouse['warehouse_id'])
                        <td>{{$data->warehouse_code}}</td>
                        <td>{{$data->name}}</td>
                    @endif
                @endforeach
                <td>{{$warehouse['qty'] }}</td>
                @endif
        </tr>
            @endforeach
    @empty
        <tr>
            <td colspan="6">Không có nội dung!</td>
        </tr>
    @endforelse
    </tbody>
</table>
</body>
</html>