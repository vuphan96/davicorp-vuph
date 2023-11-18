<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>E Invoice</title>
</head>
<body>
<table>
    <thead>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr>
        <th>Mã khách</th>
        <th>Tên khách hàng</th>
        <th>Ngày</th>
        <th>Số hóa đơn</th>
        <th>Ký hiệu</th>
        <th>Diễn giải</th>
        <th>Nhân viên bán</th>
        <th>Tên nhân viên bán</th>
        <th>Mã hàng</th>
        <th>Tên mặt hàng</th>
        <th>Đvt</th>
        <th>Mã kho</th>
        <th>Số lượng</th>
        <th>Giá bán</th>
        <th>Tiền hàng</th>
        <th>Mã nt</th>
        <th>Tỷ giá</th>
        <th>Mã thuế</th>
        <th>Tk nợ</th>
        <th>Tk doanh thu</th>
        <th>Tk giá vốn</th>
        <th>Tk thuế có</th>
        <th>Cục thuế</th>
        <th>Mã thanh toán</th>
        <th>Vụ việc</th>

        <th>Bộ phận</th>
        <th>Lsx</th>
        <th>Sản phẩm</th>
        <th>Hợp đồng</th>
        <th>Phí</th>
        <th>Khế ước</th>
    </tr>
    </thead>
    <tbody>
    @forelse($data as $datum)
        <tr>
            <td>{{ ($datum->object_id == 1 ? ($datum->teacher_code != '' ? $datum->teacher_code : $datum->customer_code) : $datum->customer_code ) }}</td>
            <td></td>
            <td>{{date('d/m/Y', strtotime($datum->delivery_time)) ?? ''}}</td>
            <td>{{ $datum->id_name ?? ''}}</td>
            <td>DV/22P</td>
            <td>{{ $datum->explain ?? ''}} {{date('d/m/Y', strtotime($datum->bill_date)) ?? ''}}</td>
            <td></td>
            <td></td>
            <td>{{ $datum->product_code ?? ''}}</td>
            <td>{{ $datum->product_name ?? ''}}</td>
            <td>{{ $datum->product_unit ?? ''}}</td>
            <td>KHOCTY</td>
            <td>{{ $datum->qty ?? ''}}</td>
            <td>{{ $datum->price ?? ''}}</td>
            <td>{{ $datum->total_price ?? '' }}</td>
            <td></td>
            <td></td>
            <td>kt</td>
            <td>1311</td>
            <td>5111</td>
            <td>63211</td>
            <td>333111</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
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