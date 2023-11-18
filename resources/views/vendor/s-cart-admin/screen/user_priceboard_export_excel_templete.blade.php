<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $sheet->priceboard_code ?? '' }}</title>
</head>
<body>
<table>
    <thead>
    <tr>
        <th class="export-header-table" colspan="1">Mã bảng báo giá (*)</th>
        <th class="export-header-table" colspan="2">{{ $sheet->priceboard_code ?? '' }}</th>
    </tr>
    <tr>
        <th class="export-header-table" colspan="1">Tên bảng báo giá (*)</th>
        <th class="export-header-table" colspan="2">{{ $sheet->name ?? '' }}</th>
    </tr>
    <tr>
        <th class="export-header-table" colspan="1">Mã bảng giá (*)</th>
        <th class="export-header-table" colspan="2">{{ $sheet->priceboard->price_code ?? 'Bảng giá bị xoá' }}</th>
    </tr>
    <tr>
        <th class="export-header-table" colspan="1">Tên bảng giá (*)</th>
        <th class="export-header-table" colspan="2">{{ $sheet->priceboard->name ?? 'Bảng giá bị xoá' }}</th>
    </tr>
    <tr>
        <th class="export-header-table" colspan="1">Ngày bắt đầu hiệu lực (*)</th>
        <th class="export-header-table" colspan="2">{{ \Carbon\Carbon::parse($sheet->start_date )->format("d/m/Y") ?? '' }}</th>
    </tr>
    <tr>
        <th class="export-header-table" colspan="1">Ngày kết thúc hiệu lực (*)</th>
        <th class="export-header-table" colspan="2">{{ \Carbon\Carbon::parse($sheet->due_date)->format("d/m/Y") ?? '' }}</th>
    </tr>

    <tr>
        <th>STT</th>
        <th>Mã khách hàng (*)</th>
        <th>Tên khách hàng</th>
    </tr>
    </thead>
    <tbody>
    @forelse($sheet->customers ?? [] as $customer)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $customer->customer->customer_code ?? 'Khách hàng bị xoá' }}</td>
            <td>{{ $customer->customer->name ?? 'Khách hàng bị xoá' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="3">Không có nội dung!</td>
        </tr>
    @endforelse

    </tbody>
</table>
</body>
</html>