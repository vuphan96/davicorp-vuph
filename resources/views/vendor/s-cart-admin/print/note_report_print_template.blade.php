<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Báo cáo ghi chú</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Times New Roman", Serif;
            line-height: 0.9;
        }
        @page {
            size: A4;
            margin: 1.2cm;
        }


        table {
            width: 100%;
            height: auto;

        }
        table th {
            font-style: normal;
        }
        * {
            font-size: 10pt;
        }
        .invoice_title {
            font-size: 15pt;
            text-align: center;
        }
        .invoice_time {
            text-align: center;
        }

        table.table_detail {
            box-sizing: border-box;
            border: 1px solid black;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        table.table_detail tr td.detail {
            text-align: center;
        }
        table.table_detail th {
            background-color: #cdcdcd;
            border: 1px solid black;
            font-size: 12px;
            text-align: center;
            font-style: bold;
            padding: 5px auto;
        }
        table.table_detail td {
            box-sizing: border-box;
            border: 1px solid black;
            font-size: 12px;
            padding: 5px 0px 5px 5px;
        }
    </style>
</head>
<body>
<div id="invoice" class="webview_hide">
    @php
        $from_to = $dataSearch['from_to'] ?? convertVnDateObject($dataSearch['from_to']);
        $end_to = $dataSearch['end_to'] ?? convertVnDateObject($dataSearch['end_to']);
    @endphp
    <table>
        <tr>
            <th colspan="5" ></th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: left"> {{ sc_language_render("admin.report.name_cty") }}
            </th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: left">{{ sc_language_render("admin.report.address_cty") }}
            </th>
        </tr>
        <tr>
            <th colspan="5" ></th>
        </tr>
        <tr>
            <th colspan="5"></th>
        </tr>
        <tr>
            <th colspan="6" class="invoice_title" >BÁO CÁO GHI CHÚ</th>
        </tr>
        <tr>
            <th colspan="6" class="invoice_time">Từ {{ $from_to }} đến {{ $end_to }}</th>
        </tr>
        <tr>
            <th colspan="5" align="center">Kho: -</th>
        </tr>
        <tr>
            <th colspan="5" ></th>
        </tr>
    </table>
    <br><br>
    <table style="border-collapse: collapse" border="1px" class="table_detail">
        <thead>
        <tr>
            <th style="width: 30px">Stt</th>
            <th style="width: 160px">Tên</th>
            <th style="width: 100px">Mã</th>
            <th style="width: 210px">Ghi chú mặt hàng</th>
            <th style="width: 150px">Ghi chú Đơn hàng</th>
        </tr>
        </thead>
        <tbody>
        @php
            $i = 1;
            $j = 0;
            $k = 0;
        @endphp
        @forelse($data as $datum)
            <tr>
                <td>{{ $i++ }}</td>
                <td style="padding-right: 4px">{{ $datum->name ?? $datum->customer_name }} {{ (isset($datum->object_id) && $datum->object_id == 1) ? ' - GV' : '' }}</td>
                <td>{{ $datum->id_name . ' - ' . $datum->explain }}</td>
                <td style="padding:0">
                    @foreach($datum->details as $key => $detailItem)
                        @if(!empty($detailItem->comment))
                            @php $j++; @endphp
                        @endif
                    @endforeach
                    @foreach($datum->details as $key => $detailItem)
                        @if(!empty($detailItem->comment))
                                @php $k++; @endphp
                            <p style="padding: 3px 2px">{{ ( $detailItem->product_name ?? '' ) . ' ( ' . ( $detailItem->real_total_bom ?? $detailItem->qty_reality ) . ' ) ' . ' : { ' . $detailItem->comment . ' }' }}</p>
                            @if($k < $j)
                                <hr style="width: 99%;border-top: 0.5px solid #8c8b8b">
                            @endif
                        @endif
                    @endforeach
                </td>
                <td>{{ !empty($datum->comment) ? $datum->explain . ' : ' . $datum->comment : '' }}</td>
            </tr>
        @empty
            <td colspan="6">Không có nội dung!</td>
        @endforelse
        </tbody>
    </table>
</div>
</body>
</html>