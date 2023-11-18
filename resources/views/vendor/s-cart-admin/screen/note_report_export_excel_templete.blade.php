<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BaoCaoGhiChu</title>
</head>

<body>
<table>
    @php
        $from_to = $dataSearch['from_to'] ?? convertVnDateObject($dataSearch['from_to']);
        $end_to = $dataSearch['end_to'] ?? convertVnDateObject($dataSearch['end_to']);
    @endphp
    <thead>
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
            style="font-size: 16px; font-weight: 700; border: 1px solid white; border-right: 1px solid #E0E0E0">BÁO CÁO GHI CHÚ
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
        <th colspan="5" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th align="center" style="font-weight: 800; border: 1px solid black">Stt</th>
        <th align="center" style="font-weight: 800; border: 1px solid black">Tên</th>
        <th align="center" style="font-weight: 800; border: 1px solid black">Mã</th>
        <th align="center" style="font-weight: 800; border: 1px solid black">Ghi chú mặt hàng</th>
        <th align="center" style="font-weight: 800; border: 1px solid black">Ghi chú Đơn hàng</th>
    </tr>
    </thead>
    <tbody>
    @php
        $i = 1;
    @endphp
    @forelse($dataNoteReportOrders as $dataNote)
        @php
            $orderDetailItem = [];
        @endphp
        @foreach($dataNote->details as $key => $item)
            @if($item->comment != '')
                @php
                    $orderDetailItem[] = [
                                    'note' => $item->comment ?? '',
                                    'qty' => number_format( ($item->real_total_bom ?? $item->qty_reality) , 2),
                                    'product' => $item->product_name ?? '',
                                ];
                    //$countProduct[] = ;
                @endphp
            @endif

        @endforeach
        @php
            $num = count($orderDetailItem);
            $name = (empty($dataNote['name']) ? $dataNote['customer_name'] : $dataNote['name']) . ((isset($dataNote['object_id']) && $dataNote['object_id'] == 1) ? ' - GV' : '' );
        @endphp
        <tr>
            <td valign="middle" rowspan="{{ !empty($num) ? $num : 1 }}" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px solid black; text-align: center">{{ $i ?? '' }}</td>
            <td valign="middle" rowspan="{{ !empty($num) ? $num : 1 }}" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px solid black">{{ $name ?? '' }}</td>
            <td valign="middle" rowspan="{{ !empty($num) ? $num : 1 }}" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px solid black">{{ $dataNote->id_name ? $dataNote->id_name . ' - ' . $dataNote->explain : '' }}</td>
            <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px solid black">{{ isset($orderDetailItem[0]['product']) ? ( $orderDetailItem[0]['product'] .'('. $orderDetailItem[0]['qty'] . ') : ' ) : '' }}
                {{ !empty($orderDetailItem[0]['note']) ? '{ ' . $orderDetailItem[0]['note'] . ' }' : '' }}</td>
            <td valign="middle" rowspan="{{ !empty($num) ? $num : 1 }}" style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px solid black">{{ empty($dataNote->comment) ? '' : $dataNote->explain . ' : ' . $dataNote->comment }}</td>
        </tr>

        @foreach($orderDetailItem as $keyItem => $value)
            @if($keyItem > 0)
                <tr>
                    <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px solid black">{{ $value['product'] ?? '' }} ({{$value['qty']}}) : {{ !empty($value['note']) ? '{ ' . $value['note'] . ' }' : '' }}</td>
                </tr>
            @endif

        @endforeach
        @php
            $i++;
        @endphp
    @empty
        <td colspan="6">Không có nội dung!</td>
    @endforelse
    </tbody>
</table>
</body>
</html>