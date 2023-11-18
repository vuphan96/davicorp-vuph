<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BaoCao2ChiTieu</title>
</head>

<body>
<table>
    <thead>
    @php
        $from_to = $dataSearch['from_to'] ?? convertVnDateObject($dataSearch['from_to']);
        $end_to = $dataSearch['end_to'] ?? convertVnDateObject($dataSearch['end_to']);
    @endphp
    <tr>
        <th colspan="5" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="5" style="border: 1px solid white; border-right: 1px solid #E0E0E0">{{ sc_language_render("admin.report.name_cty") }}
        </th>
    </tr>
    <tr>
        <th colspan="5" style="border: 1px solid white; border-right: 1px solid #E0E0E0">{{ sc_language_render("admin.report.address_cty") }}
        </th>
    </tr>
    <tr>
        <th colspan="5" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="5" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="5" align="center"
            style="font-size: 16px; font-weight: 700; border: 1px solid white; border-right: 1px solid #E0E0E0">BÁO CÁO
            BÁN
            HÀNG NHÓM THEO 2 CHỈ TIÊU
        </th>
    </tr>
    <tr>
        <th colspan="5" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0">Từ
            ngày {{  $from_to }} đến ngày {{ $end_to }}</th>
    </tr>
    <tr>
        <th colspan="5" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0">Kho: -</th>
    </tr>
    <tr>
        <th colspan="5" align="center" style="border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th align="center" >Stt</th>
        <th align="center" >Mã</th>
        <th align="center" >Tên</th>
        <th align="center" >Số lượng</th>
        <th align="center" >Ghi chú</th>
    </tr>
    </thead>
    <tbody>
    @php $totalQty = 0; @endphp
    @forelse($data->groupBy('product_id') as $key => $datum)
        @php $totalQty += $datum->sum('qty'); $i = 1; @endphp
        <tr>
            <td></td>
            <td></td>
            <td style="font-weight: 800;">{{ mb_strtoupper($datum->first()['product_name'], 'UTF-8') }}</td>
            <td style="font-weight: 800;">{{ $datum->sum('qty') ?? ''}}</td>
            <td></td>
        </tr>
        @foreach($datum as $keyItem => $item)
            <tr>
                <td> {{ $i++ }}</td>
                <td> {{ $item['customer_code'] }} </td>
                <td> {{ $item['customer_name'] }}</td>
                <td> {{ $item['qty'] }}</td>
                <td> {{ $item['note'] }} </td>
            </tr>
        @endforeach
    @empty
        <td colspan="6">Không có nội dung!</td>
    @endforelse
    </tbody>
    <tr>
        <th colspan="3" style="font-weight: bold">Tổng cộng</th>
        <th colspan="1" style="text-align: right; font-weight: bold">{{ number_format($totalQty, 2) }}</th>
        <th colspan="1" style=""></th>

    </tr>
    <tr>
        <th colspan="5" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="4" style="text-align: right; border: 1px solid white; border-right: 1px solid white">Ngày ...... Tháng ...... năm ..........</th>
        <th colspan="1"></th>
    </tr>
    <tr>
        <th colspan="3" style="text-align: right; font-weight: bold; border: 1px solid white; border-right: 1px solid white">Người Lập</th>
        <th colspan="2" style="text-align: right; border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="3" style="text-align: right; border: 1px solid white; border-right: 1px solid white; font-style: italic; font-size: 10px">(Ký, họ tên)</th>
        <th colspan="2" style="text-align: right; border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="5" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="5" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="5" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0; border-bottom: 1px solid #E0E0E0"></th>
    </tr>
</table>
</body>
</html>