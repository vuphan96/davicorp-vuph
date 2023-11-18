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
        <th colspan="5" style="font-weight: bold; font-size: 16pt; padding: 16cm; text-align: center">BẢNG TỔNG HỢP ĐIỂM THƯỞNG</th>
    </tr>
    <tr>
        <th colspan="5" style="font-weight: bold; font-size: 16pt; padding: 16cm; text-align: center">Khách hàng : {{ $data->first()->history->first()->order->name ?? '' }}</th>
    </tr>
</table>

@forelse($data as $datum)
    <tr>
        <th colspan="5">Tháng {{ $datum->month ?? '' }}/{{ $datum->year ?? '' }}</th>
    <tr>
    <table class="table">
        <thead>

        <tr>
            <th >Mã đơn hàng</th>
            <th >Ngày giao hàng</th>
            <th style="text-align: right">Tổng tiền</th>
            <th style="text-align: right">Điểm thưởng</th>
            <th style="text-align: right">Điểm thưởng thực tế</th>
        </tr>
        </thead>
        <tbody>
        @forelse($datum->history as $item)
            <tr>
                <th scope="row">{{ $item->order->id_name ?? 'Đơn hàng bị xóa' }}</th>
                <td> {{ \Carbon\Carbon::parse($item->order->delivery_time ?? '')->format('d/m/Y') ?? '' }}</td>
                <td style="text-align: right"> {{ isset($item->order->total) ? sc_currency_render($item->order->total ?? 0, 'VND') : ''  }}</td>
                <td style="text-align: right"> {{ $item->change_point ?? 0 }}</td>
                <td style="text-align: right">{{ $item->actual_point ?? 0 }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5">Không có dữ liệu</td>
            </tr>
        @endforelse
        </tbody>
    </table>
@empty
    <tr>
        <td colspan="5">Không có dữ liệu</td>
    </tr>
@endforelse
</body>
</html>