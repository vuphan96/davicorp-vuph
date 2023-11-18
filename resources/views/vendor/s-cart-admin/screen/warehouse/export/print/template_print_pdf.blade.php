<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Davicorp - In đơn hàng xuất</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">
        .print_note {
            font-size: 18px !important;
        }

        @media print {
            table.table_detail th, table.table_detail td {
                border: 0.6pt solid black;
            }

            .webview_hide {
                display: block;
            }

            .print_note {
                display: none;
            }
        }

        @page {
            margin: 0.7cm 0.96cm 0.6cm 0.96cm;
        }

        html {
            /*margin: 0 !important;*/
        }

        table {
            width: 100%;
            height: auto;
        }

        table.foot_table {
            table-layout: fixed
        }

        * {
            font-size: 10.8pt;
            /*font-family: 'Source Serif Pro', serif;*/
            font-family: "Times New Roman", Serif;
            line-height: 1;
        }

        body {
            margin-top: 0;
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
        table.table_detail tr td.code {
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

        .table_detail table, .table_detail td {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .table_detail td {
            padding-right: 3pt;
            padding-top: 2pt;
            padding-bottom: 2pt;
        }

        .table_detail th {
            padding-top: 2pt;
            padding-bottom: 2pt;
        }
    </style>
</head>
<body>
@forelse($data as $datum)
    @php $details = $datum->details; $dataExport = $datum @endphp
    <table id="invoice_{{ $loop->iteration }}">
        <table>
            <tr>
                <td  style="font-size: 10pt; line-height: 20px" class="company_title" colspan="5">
                    Công ty Cổ phần Davicorp Việt Nam
                    <br/>Số 34B, Lô 2, Đền Lừ 1, Hoàng Văn Thụ, Hoàng Mai, Hà Nội<br>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td colspan="6" class="invoice_title">PHIẾU XUẤT KHO</td>
            </tr>
            <tr>
                <td colspan="3" class="invoice_time">Ngày {{ \Carbon\Carbon::make($dataExport->date_export)->format('d') }}
                    tháng {{ \Carbon\Carbon::make($dataExport->date_export)->format('m') }}
                    năm {{ \Carbon\Carbon::make($dataExport->date_export)->format('Y') }}
            </tr>
            <br>
            <tr>
                <td colspan="4" class="invoice_order_common_info">
                    Họ tên người giao hàng:
                </td>
                <td colspan="3" class="invoice_order_common_info">
                    Chọn kho xuất: {{ $dataExport->warehouse_name ?? ''}}
                </td>
            </tr>
            <tr>
                <td colspan="4" class="invoice_order_common_info">
                    Đơn vị: {{ $dataExport->customer_name ?? ''}}
                </td>
            </tr>
            <tr>
                <td colspan="4" class="invoice_order_common_info">
                    Địa chỉ: {{ $dataExport->customer_addr ?? ''}}
                </td>
            </tr>
            <tr>
                <td colspan="4" class="invoice_order_common_info">
                    Lý do xuất: {{ $dataExport->note ?? ''}}
                </td>
            </tr>
        </table>
        <table class="table_detail">
            <tr>
                <th width="5%">STT</th>
                <th width="10%">Mã</th>
                <th width="35%">Tên vật tư</th>
                <th width="10%">ĐVT</th>
                <th width="15%">Số lượng</th>
                <th width="22%">Ghi chú</th>
            </tr>
            @forelse($details->groupBy('product_id') as $detail)
                @php
                    $qty = $detail->sum('qty');
                    $arrNote = [];
                @endphp
                @foreach($detail as $item)
                    @php
                            $arrNote[] = ($item['comment'] && $item['order_id_name']) ? $item['order_id_name']. ':'.$item['comment'] : $item['comment'];
                    @endphp
                @endforeach
                <tr>
                    <td class="no">{{ $loop->iteration }}</td>
                    <td class="code">{{ $detail->first()['product_sku'] ?? '' }}</td>
                    <td class="name">{{ $detail->first()['product_name'] ?? '' }}</td>
                    <td class="currency code">{{ $detail->first()['unit'] ?? '' }}</td>
                    <td class="currency qty">{{ $qty }}</td>
                    <td class="name ">
                        {{ implode(' ; ', $arrNote) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Không có dữ liệu!</td>
                </tr>
            @endforelse
        </table>
        <table class="foot_table">
            <tr>
                <td colspan="3" class="invoice_total"><b>Người giao hàng</b><br/><i style="font-weight: 400">(Kí, họ tên)</i></td>
                <td colspan="1"></td>
                <td colspan="4" class="invoice_total"><b>Người nhận hàng</b><br/><i style="font-weight: 400">(Kí, họ tên)</i></td>
                <td colspan="1"></td>
                <td colspan="2" class="invoice_total"><b>Thủ kho</b><br/><i style="font-weight: 400">(Kí, họ tên)</i></td>
            </tr>
        </table>
    </table>
    @if(!$loop->last)
        <p style="page-break-after: always;">&nbsp;</p>
    @endif
@empty
    Không có dữ liệu đơn hàng nào!
@endforelse


</body>
</html>