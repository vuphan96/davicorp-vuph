<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{--    <title>{{ $sheet->priceboard_code ?? '' }}</title>--}}
</head>
<body>
<table>
    <thead>
        <tr>
            <th>Mã NCC</th>
            <th>Mã NCC</th>
            <th>Sản phẩm</th>
            <th>Nội dung</th>
            <th>Giá nhập cũ</th>
            <th>Giá nhập mới</th>
            <th>Chênh lệch giá</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $index => $arr)
        @foreach($arr as $key => $line)
                <tr>
                @if($key == 0)
                    <td>{{ $line->supplier_code ?? $line['supplier_code'] }}</td>
                    <td>{{ $line->supplier_name ?? $line['supplier_name'] }}</td>
                @else
                    <td></td>
                    <td></td>
                @endif
                    <td>{{ $line->product_name ?? $line['product_name'] }}</td>
                    <td>{{ $line->desc ?? $line['desc'] }}</td>
                    <td>{{ $line->old_price ?? $line['old_price'] }}</td>
                    <td>{{ $line->new_price ?? $line['new_price'] }}</td>
                    <td>{{ $line->diff_price ?? $line['diff_price'] }}</td>
                </tr>
        @endforeach
    @endforeach
    </tbody>
</table>
</body>
</html>