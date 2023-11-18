<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Davicorp - In đơn nhập</title>
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
            font-size: 10pt;
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
@forelse($data as $orderImport)
    @php $details = $orderImport->details; @endphp
    <table id="invoice_{{ $loop->iteration }}">
        <table>
            <tr>
                <td rowspan="1" colspan="1" style="width: 1cm;padding-right: 0.5cm">
                    <img style="width: 85px" class="logo" src="{{ asset("images/print_assets/davicorp.png") }}"/>
                </td>
                <td  style="font-size: 9.5pt; line-height: 1.2" colspan="7">Công ty Cổ phần Davicorp Việt Nam
                    <br/>Số 34B, Lô 2, Đền Lừ 1, Hoàng Văn Thụ, Hoàng Mai, Hà Nội
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td colspan="8" class="invoice_title">PHIẾU NHẬP KHO</td>
            </tr>
            <tr>
                <td colspan="8" class="invoice_time">Ngày {{ \Carbon\Carbon::make($orderImport->delivery_date)->format('d') }}
                    tháng {{ \Carbon\Carbon::make($orderImport->delivery_date)->format('m') }}
                    năm {{ \Carbon\Carbon::make($orderImport->delivery_date)->format('Y') }}
            </tr>
            <tr>
                <td colspan="8" align="right">Số hóa đơn: {{ $orderImport->id_name ?? ''}}</td>
            </tr>
            <tr>
                <td colspan="8" align="right" >Kho nhập: {{ $orderImport->warehouse_name ?? ''}}</td>
            </tr>
            <tr>
                <td colspan="8">Họ tên người giao hàng: </td>
            </tr>

            <tr>
                <td colspan="8" class="invoice_order_common_info">
                    Tên đơn vị: {{ $orderImport->supplier_name ?? ''}}
                </td>
            </tr>
            <tr>
                <td colspan="8" class="invoice_order_common_info">
                    Địa chỉ: {{ $orderImport->address ?? ''}}
                </td>
            </tr>
            <tr>
                <td colspan="8" class="invoice_order_common_info">
                    Lý do nhập:
                </td>
            </tr>
        </table>
        <table class="table_detail">
            <tr>
                <th class="invoice_centered">STT</th>
                <th>Mã sản phẩm</th>
                <th>Tên sản phẩm</th>
                <th width="5%">ĐVT</th>
                <th width="10%">Số lượng</th>
                <th width="10%">Giá</th>
                <th width="10%">Thành tiền</th>
                <th width="17%">Ghi chú</th>
            </tr>
            @forelse($details as $key => $detail)
                <tr>
                    <td class="no">{{ $key + 1 }}</td>
                    <td class="name">{{ $detail->product_code }}</td>
                    <td class="name">{{ $detail->product_name }}</td>
                    <td class="unit">{{ $detail->unit_name }}</td>
                    <td class="currency qty">{{ number_format($detail->qty_reality, 2) }}</td>
                    <td class="currency price">{{ money_format($detail->product_price) }}</td>
                    <td class="currency price">{{ money_format($detail->amount_reality) }}</td>
                    <td class="">{{ $detail->comment }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Không có dữ liệu!</td>
                </tr>
            @endforelse
            @if($details->count() < 5)
                <tr>
                    <td>&nbsp;</td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                </tr>
            @endif
            <tr>
                <td colspan="6" class="invoice_total" style="font-weight: bold"><b>Tổng tiền</b></td>
                <td class="currency" colspan="2" style="font-weight: bold; text-align: left">{{ money_format($orderImport->total_reality) }}</td>
            </tr>
        </table>
        <table class="foot_table">
            <tr>
                <td colspan="8">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="8" style="font-weight: bold">Số tiền (Viết bằng chữ): {{ convert_number_to_words($orderImport->total_reality ?? 0, true) }}</td>
            </tr>
            <tr>
                <td colspan="8">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" align="center"><b>NGƯỜI GIAO HÀNG</b><br/><i style="font-weight: 400">(Kí, họ tên)</i></td>
                <td colspan="2" align="center"><b>NGƯỜI NHẬN HÀNG</b><br/><i style="font-weight: 400">(Kí, họ tên)</i></td>
                <td colspan="2" align="center"><b>THỦ KHO</b><br/><i style="font-weight: 400">(Kí, họ tên)</i></td>
                <td colspan="2" align="center"><b>GIÁM ĐỐC</b><br/><i style="font-weight: 400">(Kí, họ tên, đóng dấu)</i></td>
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