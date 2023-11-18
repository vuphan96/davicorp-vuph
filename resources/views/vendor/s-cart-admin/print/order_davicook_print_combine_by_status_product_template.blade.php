<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Davicorp - In hoá đơn</title>
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

        @page {
            margin: 0.7cm 0.96cm 0.6cm 0.96cm;
        }

        table {
            width: 100%;
            height: auto;
        }

        table.foot_table {
            table-layout: fixed
        }

        * {
            font-size: 10.5pt;
            font-family: "Times New Roman", Serif;
            line-height: 1;
        }

        body {
            margin-top: 0!important;
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

        .table_detail table, .table_detail th, .table_detail td {
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
@php $j=1; @endphp
@foreach($data as $keyCustomer => $itemCustomer)
    @foreach($itemCustomer as $keyBillDate => $itemBillDate)
        @foreach($itemBillDate as $keyExplain => $itemExplain)
            <div id="invoice" style="padding-top: {{ $j == 0 ? '0px' : '25px'}}">
                <table>
                    <tr>
                        <td rowspan="1" style="width: 3.5cm;padding-right: 0.5cm"><img class="logo" src="{{ asset('images/print_assets/davicook.png')}}"/>
                        </td>
                        <td class="company_title">
                            <b>CÔNG TY CỔ PHẦN DAVICOOK HÀ NỘI</b><br/>Xóm 10,Thôn 3 Xã Yên Mỹ, Huyện Thanh Trì, TP Hà Nội
                            <br/>ĐT: 024.22117272; 024.32004165
                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td colspan="6" class="invoice_title">PHIẾU XUẤT KHO</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="invoice_time">
                            {{ formatStringDate(($keyBillDate), 'Ngaythangnam') }} </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="invoice_time">Số: {{ $itemExplain->first()->id_name ?? ''}}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="invoice_order_common_info">
                            Tên khách hàng: {{ $itemExplain->first()->customer_name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="invoice_order_common_info">
                            Địa chỉ: {{ $itemExplain->first()->address ?? ''}}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 55%" class="invoice_order_common_info">
                            Lý do xuất: {{ $itemExplain->first()->explain }}
                        </td>
                        <td style="width: 30%" class="invoice_order_common_info">
                            Tuyến hàng: {{ $itemExplain->first()->route ?? ''}}
                        </td>
                        <td style="width: 15%" class="invoice_order_common_info">
                            STT: {{ $itemExplain->first()->customer_num ?? ''}}
                        </td>
                    </tr>
                </table>
                <table class="table_detail">
                    <tr>
                        <th class="invoice_centered">STT</th>
                        <th>Tên hàng hoá, dịch vụ</th>
                        <th width="10%">ĐVT</th>
                        <th width="15%">Số lượng</th>
                    </tr>
                    @php $no = 1; $sum = 0; $j++ @endphp
                    @forelse($itemExplain->groupBy('product_id') as $keyProduct => $itemProduct)
                        <tr>
                            <td class="no">{{ $no++ }}</td>
                            <td class="name">{{ $itemProduct->first()->product_name ?? '' }}</td>
                            <td class="qty">{{ $itemProduct->first()->product_unit ?? '' }}</td>
                            <td align="right">{{ round($itemProduct->sum('qty'), 2) ?? 0 }}</td>
                        </tr>
                        @php $sum += round($itemProduct->sum('qty'), 2); @endphp
                    @empty
                        <tr>
                            <td colspan="4">Không có dữ liệu!</td>
                        </tr>
                    @endforelse
                    @if($no < 6)
                        <tr>
                            <td>&nbsp;</td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="invoice_total"><b>Tổng số lượng bán hàng hóa, dịch vụ</b></td>
                        <td  align="right">
                            {{ $sum }}
                        </td>
                    </tr>
                </table>
                <table class="foot_table">
                    <tr>
                        <td colspan="2" class="invoice_total"><b>Bên mua hàng</b><br/><i style="font-weight: 400">(Kí, họ tên)</i></td>
                        <td colspan="2"></td>
                        <td colspan="2" class="invoice_total"><b>Bên bán hàng</b><br/><i style="font-weight: 400">(Kí, họ tên)</i></td>
                    </tr>
                </table>

            </div>
            @if($j <= ($totalData))
                <p style="page-break-after: always;">&nbsp;</p>
            @endif
        @endforeach
    @endforeach
@endforeach
</body>
</html>