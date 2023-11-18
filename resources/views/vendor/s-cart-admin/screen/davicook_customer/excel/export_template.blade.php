<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>
<table>
    <thead>
    <tr>
        <th colspan="13" style="font-weight: 700 ; border: 1px solid black; background-color: #a8c6ed">Thông tin khách
            hàng
        </th>
    </tr>
    <tr>
        <th style="font-weight: 700 ; border: 1px solid black">Mã khách hàng(*)</th>
        <th style="font-weight: 700 ; border: 1px solid black">Tên khách hàng</th>
        <th style="font-weight: 700 ; border: 1px solid black">Email</th>
        <th style="font-weight: 700 ; border: 1px solid black">Số điện thoại</th>
        <th style="font-weight: 700 ; border: 1px solid black">Mã số thuế</th>
        <th style="font-weight: 700 ; border: 1px solid black">Giá mỗi suất ăn(*)</th>
        <th style="font-weight: 700 ; border: 1px solid black">Thuộc STT(*)</th>
        <th style="font-weight: 700 ; border: 1px solid black">Tên hiển thị trên tem(*)</th>
        <th style="font-weight: 700 ; border: 1px solid black">Thuộc Tuyến hàng(*)</th>
        <th style="font-weight: 700 ; border: 1px solid black">Địa chỉ</th>
        <th style="font-weight: 700 ; border: 1px solid black">Mã khu vực(*)</th>
        <th style="font-weight: 700 ; border: 1px solid black">Tên khu vực</th>
        <th style="font-weight: 700 ; border: 1px solid black">Trạng thái</th>
    </tr>
    </thead>
    <tbody>
    @if($data)
        <tr>
            <td style="border: 1px solid black">{{ $data['customer_code'] ?? ''}}</td>
            <td style="border: 1px solid black">{{ $data['name'] ?? ''}}</td>
            <td style="border: 1px solid black">{{ $data['email'] ?? ''}}</td>
            <td style="border: 1px solid black">{{ $data['phone'] ?? ''}}</td>
            <td style="border: 1px solid black">{{ $data['tax_code'] ?? ''}}</td>
            <td style="border: 1px solid black">{{ $data['serving_price'] ?? ''}}</td>
            <td style="border: 1px solid black">{{ $data['order_num'] ?? ''}}</td>
            <td style="border: 1px solid black">{{ $data['short_name'] ?? ''}}</td>
            <td style="border: 1px solid black">{{ $data['route'] ?? ''}}</td>
            <td style="border: 1px solid black">{{ $data['address'] ?? ''}}</td>
            <td style="border: 1px solid black">{{ $data['zone_code'] ?? ''}}</td>
            <td style="border: 1px solid black">{{ $data['zone_name'] ?? ''}}</td>
            <td style="border: 1px solid black">{{ $data['status'] ?? ''}}</td>
        </tr>
    @endif
    </tbody>
</table>
<table>
    <thead>
    <tr>
        <th colspan="4" style="font-weight: 700 ; border: 1px solid black; background-color: #a8c6ed">Thông tin sản
            phẩm
        </th>
    </tr>
    <tr>
        <th style="font-weight: 700 ; border: 1px solid black">Mã sản phẩm(*)</th>
        <th style="font-weight: 700 ; border: 1px solid black">Tên sản phẩm</th>
        <th style="font-weight: 700 ; border: 1px solid black">Mã nhà cung cấp(*)</th>
        <th style="font-weight: 700 ; border: 1px solid black">Tên nhà cung cấp</th>
    </tr>
    </thead>
    <tbody>
    @forelse($data['davicookProductSuppliers'] as $dataProductSupplier)
        <tr>
            <td valign="center" style="border: 1px solid black">{{ $dataProductSupplier['product_sku'] ?? ''}}</td>
            <td valign="center" style="border: 1px solid black">{{ $dataProductSupplier['product_name'] ?? ''}}</td>
            <td valign="center" style="border: 1px solid black">{{ $dataProductSupplier['supplier_code'] ?? ''}}</td>
            <td style="border: 1px solid black">{{ $dataProductSupplier['supplier_name'] ?? ''}}</td>
        </tr>
    @empty
        <td colspan="4">Không có nội dung!</td>
    @endforelse
    </tbody>
</table>
<table>
    <thead>
    <tr>
        <th colspan="8" style="font-weight: 700 ; border: 1px solid black; background-color: #a8c6ed">Thông tin món ăn
        </th>
    </tr>
    <tr>
        <th style="font-weight: 700 ; border: 1px solid black">Mã món ăn(*)</th>
        <th style="font-weight: 700 ; border: 1px solid black">Tên món ăn</th>
        <th style="font-weight: 700 ; border: 1px solid black">Món ăn xuất định lượng</th>
        <th style="font-weight: 700 ; border: 1px solid black">Mã nguyên liệu(*)</th>
        <th style="font-weight: 700 ; border: 1px solid black">Tên nguyên liệu</th>
        <th style="font-weight: 700 ; border: 1px solid black">Định lượng</th>
        <th style="font-weight: 700 ; border: 1px solid black">Định lượng chín</th>
        <th style="font-weight: 700 ; border: 1px solid black">Loại sản phẩm</th>
    </tr>
    </thead>
    <tbody>
    @forelse($data['menu'] as $dish)
        @php
            $details = [];
        @endphp
        @foreach($dish['details'] as $key => $detail)
            @php
                $details[$key] = $detail;
            @endphp
        @endforeach
        <tr>
            <td valign="center" rowspan="{{ $key + 1 }}" style="border: 1px solid black">{{ $dish['code'] ??''}}</td>
            <td valign="center" rowspan="{{ $key + 1 }}" style="border: 1px solid black">{{ $dish['name'] ??''}}</td>
            <td valign="center" align="center" rowspan="{{ $key + 1 }}" style="border: 1px solid black">{{ $dish['is_export_menu'] ?? '' }}</td>
            <td style="border: 1px solid black">{{ $details[0]['sku'] ?? ''}}</td>
            <td style="border: 1px solid black">{{ $details[0]['name'] ?? ''}}</td>
            <td style="border: 1px solid black">{{ $details[0]['qty'] ?? ''}}</td>
            <td rowspan="{{ $key + 1 }}" style="border: 1px solid black">{{ $dish['qty_cooked_dish'] ??'' }}</td>
            <td style="border: 1px solid black" align="center">{{ $details[0]['is_spice'] ?? ''}}</td>
        </tr>
        @foreach($details as $keyDetail => $item)
            @if($keyDetail > 0)
                <tr>
                    <td style="border: 1px solid black">{{ $item['sku'] ??''}}</td>
                    <td style="border: 1px solid black">{{ $item['name'] ?? ''}}</td>
                    <td style="border: 1px solid black">{{ $item['qty'] ?? ''}}</td>
                    <td style="border: 1px solid black" align="center">{{ $item['is_spice'] ?? ''}}</td>
                </tr>
            @endif
        @endforeach
    @empty
        <td colspan="5">Không có nội dung!</td>
    @endforelse
    </tbody>
</table>
</body>
</html>