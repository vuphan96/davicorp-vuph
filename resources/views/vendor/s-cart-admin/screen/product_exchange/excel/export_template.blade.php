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
        <th>Mã sản phẩm cơ sở (*)</th>
        <th>Tên sản phẩm cơ sở (*)</th>
        <th>Mã sản phẩm quy đổi (*)</th>
        <th>Tên sản phẩm quy đổi (*)</th>
        <th>Số lượng quy đổi (*)</th>
        <th>Trạng thái</th>
    </tr>
    </thead>
    <tbody>
    @forelse($data as $datum)
        <tr>
            <td>{{ $datum->product_code ?? ''}}</td>
            <td>{{ $datum->product_name ?? ''}}</td>
            <td>{{ $datum->product_code_exchange ?? ''}}</td>
            <td>{{ $datum->product_name_exchange ?? 'Danh mục đã bị xoá' }}</td>
            <td>{{ $datum->qty_exchange ?? ''}}</td>
            <td>{{ $datum->status ?? ''}}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6">Không có nội dung!</td>
        </tr>
    @endforelse
    </tbody>
</table>
</body>
</html>