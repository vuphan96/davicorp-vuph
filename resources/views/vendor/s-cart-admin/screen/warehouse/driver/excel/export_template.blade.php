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
        <th><b>Mã nhân viên</b></th>
        <th><b>Tên nhân viên</b></th>
        <th><b>Số điện thoại</b></th>
        <th><b>Email</b></th>
        <th><b>Địa chỉ</b></th>
        <th><b>Tên đăng nhập</b></th>
        <th><b>Mật khẩu</b></th>
        <th><b>Trạng thái</b></th>
        <th><b>Đợt</b></th>
        <th><b>Loại KH</b></th>
        <th><b>Mã khách hàng</b></th>
        <th><b>Tên khách hàng</b></th>
    </tr>
    </thead>
    <tbody>
    @forelse($data as  $datum)
        <tr>
            <td>{{ $datum['id_name'] ?? ''}}</td>
            <td>{{ $datum['full_name'] ?? '' }}</td>
            <td>{{ $datum['phone'] ?? '' }}</td>
            <td>{{ $datum['email'] ?? '' }}</td>
            <td>{{ $datum['address'] ??  '' }}</td>
            <td>{{ $datum['login_name'] ?? '' }}</td>
            <td>{{ $datum['password'] = '' }}</td>
            <td>{{ $datum['status'] == 1 ? '1' : '0 ' }}</td>
            @foreach($datum["details"] as $detail)
                @if($loop->iteration === 1)
                    <td>{{ $detail['type_order'] }}</td>
                    <td>{{ $detail['type_customer'] }}</td>
                    <td>{{ $detail['customer_code'] }}</td>
                    <td>{{ $detail['name'] }}</td>
                    </tr>
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
                    <td>{{ $detail['type_order'] }}</td>
                    <td>{{ $detail['type_customer'] }}</td>
                    <td>{{ $detail['customer_code'] }}</td>
                    <td>{{ $detail['name'] }}</td>
                    </tr>
                @endif
            @endforeach
    @empty
        <td colspan="6">Không có nội dung!</td>
    @endforelse
    </tbody>
</table>
</body>
</html>