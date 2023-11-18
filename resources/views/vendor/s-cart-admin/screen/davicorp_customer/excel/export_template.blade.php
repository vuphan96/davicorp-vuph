<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Danh sách khách hàng</title>
</head>
<body>
<table>
    <thead>
    <tr>
        <th>Mã khách hàng (*)</th>
        <th>Tên khách hàng (*)</th>
        <th>Tên đăng nhập TK Hiệu trưởng (*)</th>
        <th>Mật khẩu TK Hiệu trưởng (*) </th>
        <th>Email</th>
        <th>Số điện thoại</th>
        <th>Khách hàng thuộc (*)</th>
        <th>Hạng khách hàng (*)</th>
        <th>Mã số thuế</th>
        <th>Thuộc STT (*)</th>
        <th>Tên hiển thị trên tem (*)</th>
        <th>Thuộc tuyến hàng (*)</th>
        <th>Địa chỉ</th>
        <th>Mã khu vực (*)</th>
        <th>Tên khu vực (*)</th>
        <th>Mật khẩu</th>
        <th>Loại khách hàng (*)</th>
        <th>Mã giáo viên</th>
        <th>Mã học sinh</th>
        <th>Trạng thái (*)</th>
        <th>Mã sản phẩm (*)</th>
        <th>Sản phẩm</th>
        <th>Mã nhà cung cấp (*)</th>
        <th>Nhà cung cấp</th>
    </tr>
    </thead>
    <tbody>
    @forelse($data as $datum)
        <tr>
            <td>{{ $datum['customer_code'] ?? ''}}</td>
            <td>{{ $datum['name'] ?? ''}}</td>
            <td>{{ $datum['schoolmaster_code'] ?? ''}}</td>
            <td></td>
            <td>{{ $datum['email'] ?? ''}}</td>
            <td>{{ $datum['phone'] ?? ''}}</td>
            <td>{{ $datum['department'] ?? ''}}</td>
            <td>{{ $datum['tier'] ?? ''}}</td>
            <td>{{ $datum['tax_code'] ?? ''}}</td>
            <td>{{ $datum['order_num'] ?? ''}}</td>
            <td>{{ $datum['short_name'] ?? ''}}</td>
            <td>{{ $datum['route'] ?? ''}}</td>
            <td>{{ $datum['address'] ?? ''}}</td>
            <td>{{ $datum['zone_code'] ?? ''}}</td>
            <td>{{ $datum['zone_name'] ?? ''}}</td>
            <td></td>
            <td align="right">{{ $datum['kind'] ?? ''}}</td>
            <td>{{ $datum['teacher_code'] ?? ''}}</td>
            <td>{{ $datum['student_code'] ?? ''}}</td>
            <td>{{ $datum['status'] ?? ''}}</td>
            <td>{{ $datum['sku'] ?? ''}}</td>
            <td>{{ $datum['product_name'] ?? ''}}</td>
            <td>{{ $datum['supplier_code'] ?? ''}}</td>
            <td>{{ $datum['supplier_name'] ?? ''}}</td>
        </tr>
    @empty
        <td colspan="16 ">Không có nội dung!</td>
    @endforelse
    </tbody>
</table>
</body>
</html>