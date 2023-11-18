<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{--    <title>{{ $sheet->priceboard_code ?? '' }}</title>--}}
</head>
<body>
<table>
    <thead>
    <tr>
        <th>Mã bảng giá nhập (*)</th>
        <th>Tên bảng giá nhập (*)</th>
        <th>Mã Nhà cung cấp (*)</th>
        <th>Tên Nhà cung cấp</th>
        <th>Ngày bắt đầu hiệu lực (*)</th>
        <th>Ngày kết thúc hiệu lực (*)</th>
        <th>Mã Sản phẩm (*)</th>
        <th>Tên sản phẩm</th>
        <th>Giá nhập (*)</th>
    </tr>
    </thead>
    <tbody>
    @foreach($sheet->details as $index => $product)
        <tr>
            @if($index == 0)
                <td>{{ $sheet->code ?? "" }}</td>
                <td>{{ $sheet->name ?? ""}}</td>
                <td>{{ $sheet->supplier->supplier_code ?? "" }}</td>
                <td>{{ $sheet->supplier->name ?? "" }}</td>
                <td>{{ \Carbon\Carbon::parse($sheet->start_date)->format("d/m/Y") ?? "" }}</td>
                <td>{{ \Carbon\Carbon::parse($sheet->end_date)->format("d/m/Y") ?? ""}}</td>
            @else
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            @endif
            <td>{{ $product->product->sku ?? "" }}</td>
            <td>{{ $product->product ? $product->product->getName() : "" }}</td>
            <td>{{ $product->price ?? "" }}</td>
        </tr>
    @endforeach

    </tbody>
</table>
</body>
</html>