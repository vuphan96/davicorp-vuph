<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BÁOCÁONỢHÀNG</title>
</head>
<body>
<table>
    <thead>
    <tr>
        <th colspan="8" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="8" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="8" align="center"
            style="font-size: 16px; font-weight: 700; border: 1px solid white; border-right: 1px solid #E0E0E0">BÁO CÁO NHẬP HÀNG ĐỐI VỚI TRẢ HÀNG
        </th>
    </tr>
    <tr>
        <th colspan="8" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0">Từ
            ngày {{  $dataSearch['date_start'] }} đến ngày {{ $dataSearch['date_end'] }}</th>
    </tr>
    <tr>
        <th colspan="8" align="center" style="border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th align="center" >Ngày trả hàng</th>
        <th align="center" >Mã sản phẩm</th>
        <th align="center" >Tên sản phẩm</th>
        <th align="center" >Mã đơn hàng</th>
        <th align="center" >Tên khách hàng</th>
        <th align="center" >Số lượng trả</th>
        <th align="center" >Số lượng nhập kho</th>
        <th align="center" >Số lượng chưa nhập</th>
    </tr>
    </thead>
    <tbody>
    @forelse($data as $key => $datum)
        <tr>
            <td>{{ date('d/m/Y', strtotime($datum->created_at)) }}</td>
            <td>{{ $datum->product_code }}</td>
            <td>{{ $datum->product_name }}</td>
            <td>{{ $datum->order_id_name }}</td>
            <td>{{ $datum->customer_name }}</td>
            <td>{{ $datum->return_qty }}</td>
            <td>{{ $datum->qty_import }}</td>
            <td>{{ $datum->return_qty - $datum->qty_import }}</td>
        </tr>
    @empty
        <td colspan="8">Không có nội dung!</td>
    @endforelse
    </tbody>
</table>
</body>
</html>