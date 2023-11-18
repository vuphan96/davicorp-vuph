<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Davicorp - In hoá đơn trả hàng</title>
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
            font-size: 10pt;
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

    </style>
</head>
<body>
@php $details = $data->details; $order = $data @endphp
<div class="webview_hide">
    <table>
        <tr>
            <td rowspan="1" style="width: 3.5cm;padding-right: 0.5cm"><img class="logo"
                                                                           src="{{ public_path($order->customer ? $order->customer->department->image : "images/print_assets/davicorp.png") }}"/>
            </td>
            <td class="company_title">
                <b>{{ $order->customer->department->name ?? 'Lỗi dữ liệu' }}</b><br/>{{ $order->customer->department->address ?? 'Lỗi dữ liệu!' }}
                <br/>{{ $order->customer->department->contact ?? 'Lỗi dữ liệu' }}
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td colspan="6" class="invoice_title">PHIẾU TRẢ HÀNG</td>
        </tr>
        <tr>
            <td colspan="3" class="invoice_time">Ngày {{ \Carbon\Carbon::make($order->bill_date)->format('d') }}
                tháng {{ \Carbon\Carbon::make($order->bill_date)->format('m') }}
                năm {{ \Carbon\Carbon::make($order->bill_date)->format('Y') }}
        </tr>
        <tr>
            <td colspan="3" class="invoice_time">Số {{ $order->id_name ?? ''}}</td>
        </tr>
        <tr>
            <td colspan="3" class="invoice_order_common_info">
                Tên khách hàng: {{ $order->name ?? '' }}
            </td>
        </tr>
        <tr>
            <td colspan="4" class="invoice_order_common_info">
                Địa chỉ: {{ $order->address ?? ''}}
            </td>
        </tr>
        <tr>
            <td style="width: 60%" class="invoice_order_common_info">
                Lý do xuất: {{ ($order->explain ?? '') }}
            </td>
            <td style="width: 30%" class="invoice_order_common_info">
                Tuyến hàng: {{ $order->customer->route ?? ''}}
            </td>
            <td style="width: 10%" class="invoice_order_common_info">
                STT: {{ $order->customer->order_num ?? ''}}
            </td>
        </tr>
    </table>
    <table class="table_detail">
        <tr>
            <th class="invoice_centered">STT</th>
            <th>Tên hàng hoá, dịch vụ</th>
            <th>ĐVT</th>
            <th>Số lượng</th>
            <th>Đơn giá</th>
            <th>Thành tiền</th>
        </tr>
        @php $sumPrice = 0 @endphp
        @forelse($order->returnHistory as $detail)
            <tr>
                <td class="no">{{ $loop->iteration }}</td>
                <td class="name">{{ $detail->product->name ?? $detail->product_name }}</td>
                <td width="5%" class="qty">{{ $detail->product->unit->name ?? "" }}</td>
                <td width="10%" class="currency qty">{{ $detail->return_qty ?? 0 }}</td>
                <td width="15%" class="currency price">{{ money_format($detail->price != null ? $detail->price : ($detail->detail->price ?? 0)) }}</td>
                <td width="15%" class="currency price">{{ money_format($detail->return_total ?? 0)}}</td>
                @php $sumPrice += $detail->return_total @endphp
            </tr>
        @empty
            <tr><td colspan="6">Không có dữ liệu!</td></tr>
        @endforelse
        <tr>
            <td colspan="5" class="invoice_total"><b>Cộng tiền bán hoá đơn dịch vụ</b></td>
            <td class="currency">{{ money_format($sumPrice) }}</td>
        </tr>
    </table>
    <table class="foot_table">
        <tr>
            <td colspan="6">Số tiền bằng chữ: {{ convert_number_to_words($sumPrice ?? 0, true) }}</td>
        </tr>
        <tr>
            <td colspan="2" class="invoice_total">Bên mua hàng<br/><i style="font-weight: 400">(Kí, họ tên)</i></td>
            <td colspan="2"> </td>
            <td colspan="2" class="invoice_total">Bên bán hàng<br/><i style="font-weight: 400">(Kí, họ tên)</i></td>
        </tr>
    </table>
</div>
</body>
</html>