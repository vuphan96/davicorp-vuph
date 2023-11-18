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
        <th><b>Mã nhà cung cấp</b></th>
        <th><b>Tên nhà cung cấp</b></th>
        <th><b>Địa chỉ</b></th>
        <th><b>Số điện thoại</b></th>
        <th><b>Email</b></th>
        <th><b>Trạng thái</b></th>
        <th><b>Tên đăng nhập</b></th>
        <th><b>Mật khẩu</b></th>
        <th><b>Mẫu hàng nhập</b></th>
    </tr>
    </thead>
    <tbody>
    @forelse($data as  $datum)
        <tr>
            <td>{{ $datum['supplier_code'] ?? ''}}</td>
            <td>{{ $datum['name'] ?? '' }}</td>
            <td>{{ $datum['address'] ?? '' }}</td>
            <td>{{ $datum['phone'] ?? '' }}</td>
            <td>{{ $datum['email'] ??  '' }}</td>
            <td>{{ $datum['status'] == 1 ? '1' : '0 ' }}</td>
            <td>{{ $datum['name_login'] ?? '' }}</td>
            <td>{{ $datum['password'] = '' }}</td>
            <td>{{ $datum['type_form_report'] == 1 ? 'Mẫu 1' : 'Mẫu 2' }}</td>
        </tr>
    @empty
        <td colspan="6">Không có nội dung!</td>
    @endforelse
    </tbody>
</table>
</body>
</html>