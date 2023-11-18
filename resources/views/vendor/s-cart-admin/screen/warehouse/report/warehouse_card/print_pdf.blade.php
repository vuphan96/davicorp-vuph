
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BaoCaoTheKho</title>
    <style>
        * {
            margin: 5px;
            padding: 0;
            box-sizing: border-box;
            font-family: "Times New Roman", Serif;
            line-height: 0.9;
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
            html,
            body {
                width: 210mm;
                height: 297mm;
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
            width: 100%;
            height: auto;
        }

        * {
            font-size: 12pt;
        }

        .invoice_title {
            font-size: 15pt;
            text-align: center;
        }

        .invoice_time {
            text-align: center;
        }

        table.table_detail {
            border-collapse: collapse;
        }

        table.table_detail tr td.detail {
            text-align: center;
        }

        table.table_detail th {
            background-color: #cdcdcd;
            border: 1px solid black;
            font-size: 13pt;
            text-align: center;
            padding: 5px 5px;
        }

        table.table_detail td {
            border: 1px solid black;
            font-size: 12pt;
            padding: 5px 5px 5px 5px;
        }
    </style>
</head>
<body>
<div id="invoice" class="webview_hide">
    <table>
        <tr>
            <th colspan="9"></th>
        </tr>
        <tr>
            <th colspan="9"></th>
        </tr>
        <tr>
            <th colspan="9" class="invoice_title" style="text-align: center; line-height: 30px"> BÁO CÁO THẺ KHO
            </th>
        </tr>
        <tr>
            <th colspan="9" style="text-align: center; ">Từ {{  $dataSearch['from_to'] }} đến {{ $dataSearch['end_to'] }}
            </th>
        </tr>
        <tr>
            <th colspan="9" align="center"></th>
        </tr>

    </table>
    <br><br>
    <table style="border-collapse: collapse" border="1px" class="table_detail">
        <thead>
        <tr>
            <th style="">Ngày chứng từ</th>
            <th style="">Mã sản phẩm</th>
            <th >Tên sản phẩm</th>
            <th >Mã phiếu</th>
            <th >Diễn giải</th>
            <th >SL nhập</th>
            <th >SL xuất</th>
            <th >SL tồn kho</th>
            <th >Tên đối tượng</th>
        </tr>
        </thead>
        <tbody>
        @php
            $qtyImportAmount = $data->sum('qty_import');
            $qtyExportAmount = $data->sum('qty_export');
            $qtyStockAmount = $data->sum('qty_stock');
        @endphp
        @forelse($data as $keyProduct => $product)
            <tr>
                <td style="border-right: 1px solid black">{{formatDateVn($product->bill_date)}}</td>
                <td style="border-right: 1px solid black">{{$product->product_code ?? ''}}</td>
                <td style="border-right: 1px solid black">{{$product->product_name ?? ''}}</td>
                <td style="border-right: 1px solid black">{{$product->order_id_name ?? ''}}</td>
                <td style="border-right: 1px solid black">{{$product->explain ?? ''}}</td>
                <td style="border-right: 1px solid black; text-align: right">{{$product->qty_import ?? ''}}</td>
                <td style="border-right: 1px solid black; text-align: right">{{$product->qty_export ?? ''}}</td>
                <td style="border-right: 1px solid black; text-align: right">{{$product->qty_stock ?? ''}}</td>
                <td style="border-right: 1px solid black">{{$product->object_name ?? ''}}</td>
            </tr>
        @empty
            <td colspan="8">Không có nội dung!</td>
        @endforelse
        <td colspan="5" style="font-weight: bold" align="center">Tổng cộng</td>
        <td style="font-weight: bold; text-align: right">{{number_format($qtyImportAmount, 2, '.')}}</td>
        <td style="font-weight: bold; text-align: right" >{{number_format($qtyExportAmount, 2, '.')}}</td>
        <td style="font-weight: bold; text-align: right" >{{number_format($qtyStockAmount, 2, '.')}}</td>
        <td align="center"></td>
        </tbody>

    </table>

</div>
</body>
</html>