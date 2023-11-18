<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Davicorp - Ghi chú đơn hàng</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">
        .print_note {
            font-size: 18px !important;
        }

        @media print {
            table.table_detail th, table.table_detail td {
                border: 1pt solid black;
            }

            .webview_hide {
                display: block;
            }

            .print_note {
                display: none;
            }
        }

        html {
            margin: 0.6cm 0.96cm 0.6cm 0.96cm;
        }

        table {
            width: 100%;
            height: auto;
            page-break-after: avoid;

        }

        table.foot_table {
            table-layout: fixed
        }

        * {
            font-size: 13pt;
            /*font-family: 'Source Serif Pro', serif;*/
        }

        /*=Boot css*/
        /**/
        .logo {
            width: 3.5cm;
            height: auto;
        }

        .company_title {
            /*font-weight: 800;*/
        }

        .invoice_title {
            font-size: 13pt;
            text-align: center;
            font-weight: bold;
        }

        .invoice_time {
            text-align: center;
        }

        .invoice_order_common_info {

        }

        table.table_detail {
            border-collapse: collapse;
        }

        table.table_detail td {
            padding-left: 4px;
        }

        table.table_detail td.currency {
            text-align: right;
        }

        table.table_detail th {
            background-color: #cdcdcd;
        }

        table.table_detail th, table.table_detail td {
            /*border: 0.01pt solid black;*/
        }

        table.table_detail tr td.no {
            width: 18pt;
            text-align: center;
        }

        table.table_detail td.name {
            text-align: left;
            width: auto;
        }

        table.table_detail tr td.price {
            width: auto;
            text-align: right;
        }

        table.table_detail tr td.qty {
            width: auto;
            text-align: center;
        }

        .invoice_total {
            text-align: center;
            /*font-weight: 500;*/
        }

        .invoice_centered {
            text-align: center;
        }

        td.currency {
            text-align: right;
        }

        .custom-red {
            color: red;
        }

        .table_detail table, .table_detail th, .table_detail td {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .table_detail td {
            padding-right: 3pt;
        }

        .table_detail th {
            /*font-weight: 500;*/
        }

        * {
            font-family: "Times New Roman", Serif;
            line-height: 0.9;
        }
        table { page-break-inside:auto }
        tr    { page-break-inside:auto; page-break-after:auto }
        thead { display:table-header-group }
        tfoot { display:table-footer-group }
    </style>
</head>
<body>
<table>
    <tr>
        <td colspan="6" class="invoice_title">GHI CHÚ ĐƠN HÀNG</td>
    </tr>
</table>
<table class="table_detail">
    <tr style="page-break-after: avoid">
        <th class="invoice_centered">STT</th>
        <th>Tên khách hàng</th>
        <th width="3%">Mã đơn hàng</th>
        <th width="40%">Ghi chú mặt hàng</th>
        <th width="15%">Ghi chú đơn hàng</th>
    </tr>
    @forelse($data as $datum)
        @php
            $detailsCount = empty($datum['note_details']) ? 0 : count($datum['note_details']);
            $details = $datum['note_details'];
            $firstItem = array_shift($details);
            $items = $details;
            $rowspan = empty($datum['note_details']) ? 0 : count($datum['note_details']);
        @endphp
        <tr style="page-break-after: avoid">
            <td style="width: 1%" {!! $rowspan ? "rowspan=\"$rowspan\"" : "" !!}>{{ $loop->iteration }}</td>
            <td style="width: 24%" {!! $rowspan ? "rowspan=\"$rowspan\"" : "" !!}>{{ $datum['name'] }}</td>
            <td style="width: 25%"{!! $rowspan ? "rowspan=\"$rowspan\"" : "" !!}>{{ $datum['id_name'] }}</td>
            <td style="width: 25%">{{ $firstItem }}</td>
            <td style="width: 25%" {!! $rowspan ? "rowspan=\"$rowspan\"" : "" !!}>{{ $datum['comment'] }}</td>
        </tr>

        @foreach($items as $item)
            <tr>
                <td>{{ $item }}</td>
            </tr>
        @endforeach

    @empty
        <tr>
            <td colspan="5">Không có dữ liệu!</td>
        </tr>
    @endforelse
</table>
</body>
</html>