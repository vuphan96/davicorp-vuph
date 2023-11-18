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
            /*margin: 0.6cm 0.96cm 0.6cm 0.96cm;*/
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
            font-size: 10.8pt;
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
@php $j = 0; $k = 1; @endphp
@forelse($data as $datum)
    @foreach($datum as $keyDate => $billDateItem)
        @foreach($billDateItem as $keyObject => $objectItem)
            @foreach($objectItem as $keyExplain => $explainItem)
                <table id="invoice">
                    <table>
                        <tr>
                            <td rowspan="1" colspan="1" style="width: 1cm;padding-right: 0.5cm"><img style="width: 85px" class="logo" src="{{ asset($explainItem->first()->image ?? "images/print_assets/davicorp.png") }}"/>
                            </td>
                            <td  style="font-size: 9.5pt; line-height: 1.2" class="company_title" colspan="5">
                                <b  style="font-size: 9.5pt">{{ ($explainItem->first()->department_name) ?? 'Lỗi dữ liệu' }}</b>
                                <br/>{{ $explainItem->first()->department_address ?? 'Lỗi dữ liệu!' }}
                                <br/>{{ $explainItem->first()->department_contact ?? 'Lỗi dữ liệu' }}
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td colspan="6" class="invoice_title">PHIẾU XUẤT KHO</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="invoice_time">Ngày {{ \Carbon\Carbon::make($explainItem->first()->bill_date)->format('d') }}
                                tháng {{ \Carbon\Carbon::make($explainItem->first()->bill_date)->format('m') }}
                                năm {{ \Carbon\Carbon::make($explainItem->first()->bill_date)->format('Y') }}
                        </tr>
                        <tr>
                            <td colspan="3" class="invoice_time">Số {{ $explainItem->first()->id_name ?? ''}}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="invoice_order_common_info">
                                Tên khách hàng: {{ $explainItem->first()->object_id == 1 ? $explainItem->first()->name . ' - GV' : $explainItem->first()->name }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="invoice_order_common_info">
                                Địa chỉ: {{ $explainItem->first()->address ?? ''}}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 55%" class="invoice_order_common_info">
                                Lý do xuất: {{ ($explainItem->first()->explain ?? '') }}
                            </td>
                            <td style="width: 30%" class="invoice_order_common_info">
                                Tuyến hàng: {{ $explainItem->first()->customer->route ?? ''}}
                            </td>
                            <td style="width: 15%" class="invoice_order_common_info">
                                STT: {{ $explainItem->first()->customer_num ?? ''}}
                            </td>
                        </tr>
                    </table>
                    <table class="table_detail">
                        <tr>
                            <th class="invoice_centered">STT</th>
                            <th>Tên hàng hoá, dịch vụ</th>
                            <th width="5%">ĐVT</th>
                            <th width="10%">Số lượng</th>
                            <th width="15%">Đơn giá</th>
                            <th width="22%">Thành tiền</th>
                        </tr>
                        @php $no = 1; $j++; $sum = 0; @endphp
                        @foreach($explainItem->groupBy(['product_id', 'price']) as $itemProduct)
                            @forelse($itemProduct as $keyPrice => $item)
                                <tr>
                                    <td class="no">{{ $no++ }}</td>
                                    <td class="name">{{ $item->first()->product_name ?? 'Sản phẩm bị xoá khỏi hệ thống' }}</td>
                                    <td class="qty">{{ $item->first()->unit_name ?? "" }}</td>
                                    <td class="currency qty">{{ $item->sum('qty') ?? 0 }}</td>
                                    <td class="currency price">{{ money_format($item->first()->price) }}</td>
                                    <td class="currency price">{{ money_format($item->sum('total_price')) }}</td>
                                </tr>
                                @php $sum = $sum + $item->sum('total_price'); @endphp
                            @empty
                                <tr>
                                    <td colspan="6">Không có dữ liệu!</td>
                                </tr>
                            @endforelse
                        @endforeach
                        @if($no < 6)
                            <tr>
                                <td>&nbsp;</td>
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
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
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
                            </tr>
                        @endif
                        <tr>
                            <td colspan="5" class="invoice_total" style="font-weight: bold"><b>Cộng tiền bán hàng hóa, dịch vụ</b></td>
                            <td class="currency" style="font-weight: bold">{{ money_format($sum) }}</td>
                        </tr>
                    </table>
                    <table class="foot_table">
                        <tr>
                            <td colspan="6" style="font-weight: bold">Số tiền bằng chữ: {{ convert_number_to_words($sum ?? 0, true) }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="invoice_total"><b>Bên mua hàng</b><br/><i style="font-weight: 400">(Kí, họ tên)</i></td>
                            <td colspan="2"></td>
                            <td colspan="2" class="invoice_total"><b>Bên bán hàng</b><br/><i style="font-weight: 400">(Kí, họ tên)</i></td>
                        </tr>
                    </table>
                </table>
                @if($j <= ($totalData-1))
                    <p style="page-break-after: always;">&nbsp;</p>
                @endif
            @endforeach
        @endforeach
    @endforeach
@empty
    Không có dữ liệu đơn hàng nào!
@endforelse

</body>
</html>