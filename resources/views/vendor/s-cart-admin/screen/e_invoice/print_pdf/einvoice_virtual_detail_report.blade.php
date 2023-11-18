<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BangKeDonHang-Ao</title>
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
@forelse($datas as $data)
    <div id="invoice">
        <table>
            <tr>
                <th colspan="7"
                    style="font-size: 14px; font-weight: bold; text-align: center">{{ $data['department_name'] }}
                </th>
            </tr>
            <tr>
                <th colspan="7"
                    style="text-align: center; font-weight: normal;">
                    {!! $data['department_address'] !!}
                </th>
            </tr>
            <tr>
                <br>
            </tr>
            <tr>
                <th colspan="7"
                    style="font-size: 14px; font-weight: bold; text-align: center">BẢNG KÊ HÓA ĐƠN BÁN HÀNG
                </th>
            </tr>
            @php
                $now = date('Y-m-d');
            @endphp
            <tr>
                <th colspan="7" style="text-align: center; font-style: italic; font-size: 12px">
                    Kèm theo hóa đơn ký hiệu 2C22TDD
                </th>
            </tr>
            <tr >
                <th colspan="7" style="text-align: center; font-style: italic; font-size: 12px">
                    {{-- Số {{ $data['data']->first()->einv_id ?? '' }}  {{ formatStringDate($now, 'Ngaythangnam'); }} --}}
                    Số {{ $data['data'][0]['einvoice_id'] ?? '' }}  {{ formatStringDate($now, 'Ngaythangnam'); }}
                </th>
            </tr>
            <tr>
                <td colspan="5" style="font-weight: bold; font-size: 12px">
                    {{-- Tên đơn vị : {{ $data['data']->first()->customer_name ?? '' }} --}}
                    Tên đơn vị : {{ $data['data'][0]['customer_name'] ?? '' }}
                </td>
            </tr>
            <tr>
                <td colspan="5" style="font-weight: bold; font-size: 12px">
                    {{-- Địa chỉ : {{ $data['data']->first()->customer_address ?? '' }} --}}
                    Địa chỉ : {{ $data['data'][0]['customer_address'] ?? '' }}
                </td>
            </tr>
            <tr>

            </tr>
        </table>

        <table class="table_detail">
            <tr style="width: 100%" class="heading-report">
                <th colspan="2" style="border: 1px solid black; font-weight: bold; text-align: center">Chứng từ</th>
                <th rowspan="2" style="border: 1px solid black; font-weight: bold; text-align: center">Tên mặt hàng</th>
                <th rowspan="2" style="border: 1px solid black; font-weight: bold; text-align: center">Đvt</th>
                <th rowspan="2" style="border: 1px solid black; font-weight: bold; text-align: center">Số lượng</th>
                <th rowspan="2" style="border: 1px solid black; font-weight: bold; text-align: center">Giá bán</th>
                <th rowspan="2" style="border: 1px solid black; border-bottom: 1px solid black; font-weight: bold; text-align: center">Doanh thu</th>
            </tr>
            <tr style="width: 100%" class="heading-report">
                <th style="border: 1px solid black; font-weight: bold; text-align: center">Ngày</th>
                <th style="border: 1px solid black; font-weight: bold; text-align: center">Mã hóa đơn</th>
            </tr>
            @php
                $total = 0;
                $datab = $data['data'];
            @endphp
            {{-- @foreach ($datab->groupBy(function($datab){return date('Y-m-d',strtotime($datab->plan_start_date));}) as $key => $datum) --}}
            @foreach ($data['data']->groupBy('plan_start_date') as $key => $datum)
                @php
                    $total_by_day = 0;
                @endphp
                @foreach($datum as $keyId => $item)
                    <tr>
                        <td style="border-left: 1px solid black; border-right: 1px solid black; text-align: center">{{ $item['plan_start_date'] ?? ''}}</td>
                        <td style="border-right: 1px solid black; text-align: left">{{ $item['einvoice_id'] ?? '' }}</td>
                        <td style="border-right: 1px solid black; text-align: left">{{ $item['product_name'] ?? '' }}</td>
                        <td style="border-right: 1px solid black; text-align: left">{{ $item['unit'] ?? '' }}</td>
                        <td style="border-right: 1px solid black; text-align: right">{{ number_format($item['qty'], 2) ?? 0 }}</td>
                        <td style="border-right: 1px solid black; text-align: right">{{ number_format($item['price'], 2) ?? 0 }}</td>
                        <td style="border-right: 1px solid black; text-align: right">{{ number_format($item['qty']*$item['price'], 2) }}</td>
                    </tr>
                    @php
                        // $total_by_day += ($item->qty*$item->price);
                        $total_by_day += $item['qty']*$item['price'];
                    @endphp
                @endforeach
                <tr>
                    <td style="border-left: 1px solid black; text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
                    <td style="text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
                    <td style="text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
                    <td style="text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
                    <td style="text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
                    <td style="text-align: right;border-right: 1px solid black; background-color: yellow;"></td>
                    <td style="text-align: right;border-right: 1px solid black; font-weight: bold; background-color: yellow;">
                        {{ number_format($total_by_day, 2) }}
                    </td>
                </tr>
                @php
                    $total += $total_by_day;
                @endphp
            @endforeach
            <tr>
                <td colspan="1" style="border: 1px solid black; font-weight: bold; text-align: left">Tiền hàng</td>
                <td colspan="5" style="border: 1px solid black; font-weight: bold;"></td>
                <td style="border: 1px solid black; font-weight: bold; text-align: right ">
                    {{ number_format($total, 2) }}
                </td>
            </tr>
            <tr>
                <td colspan="1" style="border: 1px solid black; font-weight: bold;  text-align: left">Bằng chữ</td>
                <td colspan="5" style="border: 1px solid black; text-align: center">{{ convert_number_to_words($total ?? 0, true) }}</td>
                <td colspan="1" style="border: 1px solid black; font-weight: bold;"></td>
            </tr>
        </table>
        <table class="foot_table">
            <tr></tr>
            <tr></tr>
            <tr>
                <td style="font-weight: bold; text-align: center">Bên bán hàng</td>
                <td style="font-weight: bold; text-align: center">Bên mua hàng</td>
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
