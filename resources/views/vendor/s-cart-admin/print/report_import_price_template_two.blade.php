<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Báo cáo nhập hàng chi tiết theo mặt hàng</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Times New Roman", Serif;
            line-height: 1.1;
        }

        body {
            height: 100%;
            margin: 20px auto;
            padding: 0;
        }
        @page {
            size: A4;
            margin: 1.2cm;
        }
        @media print {
            * {
                font-size: 13pt;
            }
            html,
            body {
                width: 210mm;
                height: 297mm;
                padding: 10px;
            }

            .page {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                page-break-after: always;
            }
        }


        table {
            border-collapse: collapse;
            width: 100%;
            height: auto;
            /*margin: 10px 0px;*/

        }

        table th {
            font-style: normal;
        }
        table.table_detail th {
            font-size: 13pt;
            text-align: center;
            font-weight: bold;
            padding: 5px 5px 3px 5px;
            background-color: #cdcdcd;
        }

        .invoice_title {
            font-size: 15pt;
            text-align: center;
        }

        table.table_detail {
            box-sizing: border-box;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        table.table_detail td {
            box-sizing: border-box;
            font-size: 13pt;
            padding: 5px 5px 3px 5px;
        }

        .supplier-name {
            background-color: yellow;
            font-size: 13pt;
            /*text-align: center;*/
            font-weight: bold;
            padding: 5px 5px 3px 5px;
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
            <th colspan="8"></th>
        </tr>
        <tr>
            <th colspan="8"></th>
        </tr>
        <tr>
            <th colspan="8" class="invoice_title">BÁO CÁO NHẬP HÀNG CHI TIẾT THEO MẶT HÀNG</th>
        </tr>
        <tr>
            <th colspan="8"></th>
        </tr>
        <tr>
            <th colspan="8"></th>
        </tr>
        <tr>
            <th colspan="8" class="invoice_time">Từ {{ $from_to }} đến {{ $end_to }}</th>
        </tr>
        <tr>
            <th colspan="8" class="invoice_time">Kho: - </th>
        </tr>
        <tr>
            <th colspan="8"></th>
        </tr>
        <tr>
            <th colspan="8"></th>
        </tr>
    </table>
    <br><br>
    <table style="border-collapse: collapse" border="1px" class="table_detail">
        <thead>
        <tr>
            <th style="width: 30px">Stt</th>
            <th style="width: 50px">Mã vật tư</th>
            <th style="width: 170px">Tên vật tư</th>
            <th style="width: 30px">Đvt</th>
            <th style="width: 80px">Số lượng đặt</th>
            <th style="width: 80px">Đơn giá nhập
            <th style="width: 80px">Thành tiền</th>
            <th style="width: 90px">Ghi chú</th>
        </tr>
        </thead>
        <tbody>
        @php
            $total_qty = 0;
            $total_money = 0;
            $i = 1;
        @endphp
        @forelse(session('dataReportImportPriceTemplateTwo') as $keySupplier => $itemSupplier)
            <tr>
                <td class="product-name">{{ $itemSupplier['code'] == '' ? '' : $i }}</td>
                <td class="product-name">{{ $itemSupplier['code'] ?? '' }}</td>
                @if($itemSupplier['code'] == '')
                    @php
                        $i = 0;
                    @endphp
                    <td class="product-name" style="font-weight: bold">{{ $itemSupplier['name'] ?? '' }}</td>
                @else
                    <td class="product-name">{{ $itemSupplier['name'] ?? '' }}</td>
                @endif

                <td class="product-name">{{ $itemSupplier['product_unit'] ?? '' }}</td>
                <td align="right" class="product-name" style="padding-right: 5px">{{ $itemSupplier['qty_order'] != '' ? number_format($itemSupplier['qty_order'], 2) : '' }}</td>
                <td align="right" class="product-name"
                    style="padding-right: 5px">{{ $itemSupplier['price_import'] != '' ? number_format($itemSupplier['price_import']) : '' }}</td>
                <td align="right" class="product-name"
                    style="padding-right: 5px">{{ $itemSupplier['price_import'] != '' ? number_format(((float)$itemSupplier['price_import'] ?? '') * ((float)$itemSupplier['qty_order'] ?? '')) : '' }}</td>
                <td class="product-name">{!! $itemSupplier['note'] ?? '' !!}</td>
            </tr>
            @php
                $total_qty += (float)$itemSupplier['qty_order'];
                $total_money += ((float)(float)$itemSupplier['price_import'] ?? '') * ((float)$itemSupplier['qty_order'] ?? '');
                $i++;
            @endphp
        @empty
            <td colspan="6">Không có nội dung!</td>
        @endforelse
        <tr>
            <td colspan="4">Tổng cộng</td>
            <td align="right" colspan="1"
                style="font-weight: bold; padding-right: 5px">{{ number_format((float)$total_qty, 2) }}</td>
            <td colspan="1"></td>
            <td align="right" colspan="1"
                style="font-weight: bold; padding-right: 5px">{{ number_format((float)$total_money) }}</td>
            <td colspan="1"></td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>