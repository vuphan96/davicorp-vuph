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
            font-size: 10.5pt;
            font-family: "Times New Roman", Serif;
            line-height: 1;
        }

        body {
            margin-top: 0 !important;
        }

        /*=Boot css*/
        /**/
        .logo {
            width: 3.5cm;
            height: auto;
        }

        .invoice_title {
            font-size: 12pt;
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
    @php $j = 0; @endphp
    @forelse($data as $datum)

        <div id="invoice" style="padding-top: {{ $j == 0 ? '0px' : '25px' }}">
            <table>
                <tr>
                    <td rowspan="1" style="width: 3.5cm;padding-right: 0.5cm"><img class="logo" src="{{ asset('images/print_assets/davicook.png') }}"/>
                    </td>
                    <td class="company_title">
                        <b>{{ 'CÔNG TY CỔ PHẦN DAVICOOK HÀ NỘI' }}</b><br/>{{ 'Xóm 10,Thôn 3 Xã Yên Mỹ, Huyện Thanh Trì, TP Hà Nội' }}
                        <br/>{{ 'ĐT: 024.22117272; 024.32004165' }}
                    </td>
                </tr>
            </table>
            @php
                $j++;
                $sum = ($datum->number_of_reality_servings)
                                * ($datum->customer->serving_price ?? $datum->price_of_servings);
            @endphp
            <table>
                <tr>
                    <td colspan="6" class="invoice_title">PHIẾU XUẤT KHO</td>
                </tr>
                <tr>
                    <td colspan="3" class="invoice_time">
                        {{ formatStringDate(($datum->bill_date), 'Ngaythangnam') }} </td>
                </tr>
                <tr>
                    <td colspan="3" class="invoice_time">Số {{ $datum->id_name ?? ''}}</td>
                </tr>
                <tr>
                    <td colspan="3" class="invoice_order_common_info">
                        Tên khách hàng: {{ $datum->customer_name ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="invoice_order_common_info">
                        Địa chỉ: {{ $datum->address ?? ''}}
                    </td>
                </tr>
                <tr>
                    <td style="width: 55%" class="invoice_order_common_info">
                        Lý do xuất: {{ ($datum->explain ?? '') }}
                    </td>
                    <td style="width: 30%" class="invoice_order_common_info">
                        Tuyến hàng: {{ $datum->customer->route ?? ''}}
                    </td>
                    <td style="width: 15%" class="invoice_order_common_info">
                        STT: {{ $datum->customer_num ?? ''}}
                    </td>
                </tr>
            </table>
            <table class="table_detail">
                <tr>
                    <th class="invoice_centered">STT</th>
                    <th>Tên hàng hoá, dịch vụ</th>
                    <th width="5%">ĐVT</th>
                    <th width="10%">Số lượng</th>
                    <th width="15%">Giá</th>
                </tr>
                    <tr>
                        <td class="no">1</td>
                        <td class="name">Suất ăn</td>
                        <td class="qty">Suất</td>
                        <td class="currency qty">{{ $datum->number_of_reality_servings }}</td>
                        <td class="currency price">{{ money_format($datum->customer->serving_price ?? $datum->price_of_servings) }}</td>
                    </tr>
                <tr>
                    <td>&nbsp;</td>
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
                </tr>
                <tr>
                    <td>&nbsp;</td>
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
                </tr>
                <tr>
                    <td colspan="4" class="invoice_total"><b>Cộng tiền bán hoá đơn dịch vụ</b></td>
                    <td class="currency">{{ money_format($sum) }}</td>
                </tr>
            </table>
            <table class="foot_table">
                <tr>
                    <td colspan="6" style="font-weight: bold">Số tiền bằng chữ: {{ convert_number_to_words($sum, true) }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="invoice_total"><b>Bên mua hàng</b><br/><i style="font-weight: 400">(Kí, họ tên)</i></td>
                    <td colspan="2"></td>
                    <td colspan="2" class="invoice_total"><b>Bên bán hàng</b><br/><i style="font-weight: 400">(Kí, họ tên)</i></td>
                </tr>
            </table>
        </div>
        @if(!$loop->last)
        <p style="page-break-after: always;">&nbsp;</p>
        @endif
    @empty
        Không có dữ liệu đơn hàng nào!
    @endforelse
    </body>
</html>