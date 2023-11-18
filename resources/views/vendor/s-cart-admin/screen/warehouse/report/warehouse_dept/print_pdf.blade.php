
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BaoCaoNoHang</title>
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
            <th colspan="8"></th>
        </tr>
        <tr>
            <th colspan="8" class="invoice_title">BÁO CÁO NHẬP XUẤT TỒN</th>
        </tr>
        <tr>
            <th colspan="8" class="invoice_time">Từ {{  $dataSearch['date_start'] }} đến {{ $dataSearch['date_end'] }}</th>
        </tr>
        <tr>
            <th colspan="8" align="center">Kho: -</th>
        </tr>
        <tr>
            <th colspan="8" align="center"></th>
        </tr>
    </table>
    <br><br>
    <table style="border-collapse: collapse" border="1px" class="table_detail">
        <thead>
        <tr>
            <th align="center" >Ngày xuất kho</th>
            <th align="center" >Mã sản phẩm</th>
            <th align="center" >Tên sản phẩm</th>
            <th align="center" >Phiếu xuất kho</th>
            <th align="center" >Mã đơn hàng</th>
            <th align="center" >Tên khách hàng</th>
            <th align="center" >Số lượng nợ</th>
            <th align="center" >Số lượng đã trả <br> (Số lượng xuất)</th>
            <th align="center" >Số lượng còn phải trả</th>
        </tr>
        </thead>
        <tbody>
        @forelse($data as $key => $datum)
            <tr>
                <td>{{ date('d/m/Y', strtotime($datum->created_at)) }}</td>
                <td>{{ $datum->product_code }}</td>
                <td>{{ $datum->product_name }}</td>
                <td>{{ $datum->export_code }}</td>
                <td>{{ $datum->order_id_name }}</td>
                <td>{{ $datum->customer_name }}</td>
                <td align="right">{{ $datum->qty_dept }}</td>
                <td align="right">{{ $datum->qty_export }}</td>
                <td align="right">{{ $datum->qty_dept - $datum->qty_export }}</td>
            </tr>
        @empty
            <td colspan="8">Không có nội dung!</td>
        @endforelse
        </tbody>
    </table>
</div>
</body>
</html>