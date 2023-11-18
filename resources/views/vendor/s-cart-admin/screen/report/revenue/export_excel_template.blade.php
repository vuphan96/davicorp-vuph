<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BaoCaoDanhThu</title>
</head>

<body>
<table>
    @php
        $from_to = $dataSearch['from_to'] ?? convertVnDateObject($dataSearch['from_to']);
        $end_to = $dataSearch['end_to'] ?? convertVnDateObject($dataSearch['end_to']);
    @endphp
    <thead>

    <tr>
        <th colspan="7" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="7" style="border: 1px solid white; border-right: 1px solid #E0E0E0">{{ sc_language_render("admin.report.name_cty") }}
        </th>
    </tr>
    <tr>
        <th colspan="7" style="border: 1px solid white; border-right: 1px solid #E0E0E0">{{ sc_language_render("admin.report.address_cty") }}
        </th>
    </tr>
    <tr>
        <th colspan="7" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="7" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="7" align="center"
            style="font-size: 16px; font-weight: 700; border: 1px solid white; border-right: 1px solid #E0E0E0">BÁO CÁO DOANH THU THEO KHÁCH HÀNG
        </th>
    </tr>
    <tr>
        <th colspan="7" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0">Từ
            ngày {{  $from_to }} đến ngày {{ $end_to }}</th>
    </tr>
    <tr>
        <th colspan="7" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0">Kho: -</th>
    </tr>
    <tr>
        <th colspan="7" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th align="center" style="font-weight: 800; border: 1px solid black">Stt</th>
        <th align="center" style="font-weight: 800; border: 1px solid black">Ngày giao hàng</th>
        <th align="center" style="font-weight: 800; border: 1px solid black">Mã đơn hàng</th>
        <th align="center" style="font-weight: 800; border: 1px solid black">Mã khách hàng</th>
        <th align="center" style="font-weight: 800; border: 1px solid black">Tên khách hàng</th>
        <th align="center" style="font-weight: 800; border: 1px solid black">Diễn giải</th>
        <th align="center" style="font-weight: 800; border: 1px solid black">Tổng giá trị đơn hàng</th>
    </tr>
    </thead>
    <tbody>
    @php
        $i = 1;
        $total_revenue = 0;
    @endphp
    @forelse($dataRevenueReportOrders as $datum)
        <tr>
            <td style="border-right: 1px solid black;">{{ $i ?? '' }}</td>
            <td style="border-right: 1px solid black;">{{ \Carbon\Carbon::parse($datum['delivery_date'] ?? '')->format('d/m/Y')  }}</td>
            <td style="border-right: 1px solid black;">{{ $datum['id_name'] ?? '' }}</td>
            <td style="border-right: 1px solid black;">{{ $datum['customer_code'] ?? '' }}</td>
            <td style="border-right: 1px solid black;">{{ $datum['customer_name'] ?? '' }}</td>
            <td style="border-right: 1px solid black;">{{ $datum['explain'] ?? '' }}</td>
            <td style="border-right: 1px solid black;">{{ $datum['amount'] ?? '' }}</td>
        </tr>
        @php
            $total_revenue += $datum['amount'] ?? 0;
            $i++;
        @endphp
    @empty
        <td colspan="7">Không có nội dung!</td>
    @endforelse
    <tr>
        <td colspan="6" align="center" style="border: 1px solid black"><b>TỔNG DOANH THU</b></td>
        <td colspan="1" align="right" style="border: 1px solid black"><b>{{ $total_revenue ?? '' }}</b></td>
    </tr>

    </tbody>
</table>
</body>
</html>