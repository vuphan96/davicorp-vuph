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
        <th class="export-header-table" colspan="2">Mã bảng giá <span style="color: red">(*)</span></th>
        <th class="export-header-table" colspan="3">{{ $data['price_code'] ?? ''}}</th>
    </tr>
    <tr>
        <th class="export-header-table" colspan="2">Tên bảng giá<span style="color: red">(*)</span></th>
        <th class="export-header-table" colspan="3">{{ $data['name'] ?? ''}}</th>
    </tr>
    <tr>
        <th>STT</th>
        <th>Mã sản phẩm<span style="color: red">(*)</span></th>
        <th>Tên sản phẩm</th>
        <th>Giá cho giáo viên<span style="color: red">(*)</span></th>
        <th>Giá cho học sinh<span style="color: red">(*)</span></th>
    </tr>
    </thead>
    <tbody>
    @forelse($data->prices as $datum)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $datum->product->sku ?? '' }}</td>
            <td>{{ $datum->product->getName() ?? '' }}</td>
            <td>{{ ($datum['price_1']) ?? '0'}}</td>
            <td>{{ ($datum['price_2']) ?? '0'}}</td>
        </tr>
    @empty
        <td colspan="5">Không có nội dung!</td>
    @endforelse
    </tbody>

</table>
</body>
</html>