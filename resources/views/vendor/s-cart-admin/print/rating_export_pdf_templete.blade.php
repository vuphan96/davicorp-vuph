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

       @page  {
           size: A4 portrait;
           margin: 1.2cm;
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
</head>
<body>
<table>
    <tr>
        <td colspan="5" style="text-align: center; font-weight: bold; font-size: 16pt">BẢNG TỔNG HỢP LỊCH SỬ ĐÁNG GIÁ DỊCH VỤ THÁNG {{ $month ?? 0 }}/{{ $year ?? 0 }}</td>
    </tr>
</table>
<table class="table_detail">
    <thead>
    <tr>
        <th style="font-weight: bold; font-size: 12pt;">STT</th>
        <th style="font-weight: bold; font-size: 12pt;">Tên khách hàng</th>
        <th style="font-weight: bold; font-size: 12pt;">Mã khách hàng</th>
        <th style="font-weight: bold; font-size: 12pt;">Mức độ hài lòng</th>
        <th style="font-weight: bold; font-size: 12pt;">Nội dung phản hồi</th>
    </tr>
    </thead>
    <tbody>
    @forelse($data as $datum)
        <tr>
            <td style="text-align: center">{{ $loop->iteration }}</td>
            <td>{{ $datum->customer->name ?? '' }}</td>
            <td>{{ $datum->customer->customer_code ?? '' }}</td>
            <td style="text-align: center">{{ $datum->point ?? 0}}/5 sao</td>
            <td>{{ $datum->content ?? ""}}</td>
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