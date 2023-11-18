<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Danhsachmonan</title>
</head>
<body>
<table>
    <thead>
    <tr>
        <th>Mã món ăn (*)</th>
        <th>Tên món ăn (*)</th>
        <th>Trạng thái (*)</th>
    </tr>
    </thead>
    <tbody>
    @forelse($data as $datum)
        <tr>
            <td>{{ $datum['code'] ?? ''}}</td>
            <td>{{ $datum['name']}}</td>
            <td>{{ $datum['status'] ? 1 : 0 }}</td>
        </tr>
    @empty
        <td colspan="3">Không có nội dung!</td>
    @endforelse
    </tbody>
</table>
</body>
</html>