<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tổng hợp điểm thưởng</title>
</head>
<body>
<table>
    <thead>
    <tr>
        <th colspan="7" style="font-weight: bold; font-size: 16pt; padding: 16cm; text-align: center">BẢNG TỔNG HỢP ĐIỂM THƯỞNG THÁNG {{ empty($time) ? now()->format("m/Y") : $time }}</th>
    </tr>
    <tr>
        <th style="font-weight: bold; font-size: 12pt; text-align: center">STT</th>
        <th style="font-weight: bold; font-size: 12pt; text-align: center">Tên khách hàng</th>
        <th style="font-weight: bold; font-size: 12pt; text-align: center">Mã khu vực</th>
        <th style="font-weight: bold; font-size: 12pt; text-align: center">Mã khách hàng</th>
        <th style="font-weight: bold; font-size: 12pt; text-align: center">Hạng khách hàng</th>
        <th style="font-weight: bold; font-size: 12pt; text-align: center">Điểm thưởng</th>
        <th style="font-weight: bold; font-size: 12pt; text-align: center">Tiền quy đổi</th>
    </tr>
    </thead>
    <tbody>
    @forelse($data as $datum)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $datum->name ?? '' }}</td>
        <td>{{ $datum->zone_code ?? '' }}</td>
        <td>{{ $datum->customer_code ?? ''}}</td>
        <td>{{ $datum->tier_name ?? ''}}</td>
        <td>{{ $datum->point ?? 0}}</td>
        <td>{{ $datum->point * $datum->rate ?? 0 }}</td>
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