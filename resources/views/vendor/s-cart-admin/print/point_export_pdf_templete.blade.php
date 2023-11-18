<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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
            padding: 8px;
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

    </style>
    <title>Điểm thưởng</title>
</head>
<body>
{{--@php logger($time) @endphp--}}
<div style="font-size: 18px; font-weight: bold; padding: 8px; text-align: center">BẢNG TỔNG HỢP ĐIỂM THƯỞNG THÁNG {{ empty($time) ? now()->format("m/Y") : $time  }}</div>
<table class="table_detail">
    <thead>
    <tr>
        <th style="font-weight: bold; font-size: 12pt;">STT</th>
        <th style="font-weight: bold; font-size: 12pt;">Tên khách hàng</th>
        <th style="font-weight: bold; font-size: 12pt;">Mã khu vực</th>
        <th style="font-weight: bold; font-size: 12pt;">Mã khách hàng</th>
        <th style="font-weight: bold; font-size: 12pt;">Hạng khách hàng</th>
        <th style="font-weight: bold; font-size: 12pt;">Điểm thưởng</th>
        <th style="font-weight: bold; font-size: 12pt;">Tiền quy đổi</th>
    </tr>
    </thead>
    <tbody>
    @forelse($data as $datum)
        <tr>
            <td style="text-align: center">{{ $loop->iteration }}</td>
            <td>{{ $datum->name ?? '' }}</td>
            <td>{{ $datum->zone_code ?? '' }}</td>
            <td>{{ $datum->customer_code ?? ''}}</td>
            <td>{{ $datum->tier_name ?? ''}}</td>
            <td style="text-align: right">{{ $datum->point ?? 0}}</td>
            <td style="text-align: right">{{ number_format($datum->point * $datum->rate ?? 0) }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6">Không có nội dung!</td>
        </tr>
    @endforelse
    </tbody>
</table>
</body>
</html>