<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DanhMucSanPham</title>
</head>
<body>
<table>
    <thead>
    <tr>
        <th>Mã danh mục</th>
        <th>Tên danh mục</th>
        <th>Trạng thái</th>
    </tr>
    </thead>
    <tbody>
    @forelse($data as $datum)
        <tr>
            <td>{{ $datum['sku'] ?? ''}}</td>
            <td>{{ $datum['name']}}</td>
            <td>{{ $datum['status'] == 1 ? '1' : '0 '}}</td>
        </tr>
    @empty
        <td colspan="6">Không có nội dung!</td>
    @endforelse
    </tbody>
</table>
</body>
</html>