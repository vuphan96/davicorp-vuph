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
        @endphp
        @forelse($dataSupplier as $keySupplier => $itemSupplier)
            <tr>
                <td></td>
                <td></td>
                <td class="supplier-name">{{ $keySupplier ? mb_strtoupper($itemSupplier->first()->supplier_name, 'UTF-8') : 'Nhà cung cấp đã bị xóa' }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @php
                $dataProduct = $itemSupplier->groupBy('product_id')->sortBy('sort');
                $i = 1;
            @endphp
            @foreach($dataProduct as $itemProducts)
                @php
                    $arrProduct = [];
                @endphp
                @foreach($itemProducts as $product)
                    @php
                        $price = '';
                        $date = ($dataSearch['select_warehouse'] == 2) ? $product->bill_date : $product->delivery_date;
                    @endphp
                    @if($date >= $product->start_date && $date <= $product->end_date)
                        @php
                            $price = $product->price;
                        @endphp
                    @endif
                    @php
                        $arrProduct[] = [
                            'code' => $product->product_code ?? '',
                            'name' => $product->product_name ?? '',
                            'dvt' => $product->product_unit ?? '',
                            'qty_order' => $product->qtyProduct ?? '',
                            'price_import' => $price ?? '',
                            'into_money' =>$price ? ((float)$price ?? '') * ((float)$product->qtyProduct ?? '') : '',
                            'note' => $product->comment,
                            'customer_name' => $product->customer_name,
                        ];
                        $total_qty += (float)$product->qtyProduct;
                        $total_money += ((float)$product->qtyProduct ?? '') * ((float)$price ?? '');
                    @endphp
                @endforeach
                @php
                    $productCollect = collect($arrProduct)->groupBy('price_import');
                @endphp
                @foreach($productCollect as $row)
                    @php
                        $comment = '';
                        $sumQty = $row->sum('qty_order');
                    @endphp
                    @foreach($row as $item)
                        @if($item['note'] != '')
                            @php
                                $comment .= '<span>' . $item['customer_name'] . ' : ' . $item['note'] . '</span><br>';
                            @endphp
                        @endif
                    @endforeach
                    <tr>
                        <td class="product-name">{{ $i }}</td>
                        <td class="product-name">{{ $row->first()['code'] ?? '' }}</td>
                        <td class="product-name">{{ $row->first()['name'] ?? '' }}</td>
                        <td class="product-name">{{ $row->first()['dvt'] ?? '' }}</td>
                        <td align="right" class="product-name" style="padding-right: 5px">{{ $sumQty ?? '' }}</td>
                        <td align="right" class="product-name"
                            style="padding-right: 5px">{{ $row->first()['price_import'] ? number_format((float)$row->first()['price_import']) : '' }}</td>
                        <td align="right" class="product-name"
                            style="padding-right: 5px">{{$row->first()['price_import'] ? number_format(((float)$row->first()['price_import'] ?? '') * ((float)$sumQty ?? '')) : '' }}</td>
                        <td class="product-name">{!! $comment !!}</td>
                    </tr>
                    @php
                        $i++;
                    @endphp
                @endforeach
            @endforeach
        @empty
            <td colspan="6">Không có nội dung!</td>
        @endforelse
        <tr>
            <td colspan="4">Tổng cộng</td>
            <td align="right" colspan="1"
                style="font-weight: bold; padding-right: 5px">{{ number_format((float)$total_qty) }}</td>
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