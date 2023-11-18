
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Xuất pdf</title>
    <style type="text/css">
        @media print {
            table.table_detail th {
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
            font-size: 12pt;
            font-family: "Times New Roman", Serif;
            line-height: 1;
        }

        body {
            margin-top: 0 !important;
        }

        /*=Boot css*/
        /**/
        .logo {
            width: 3cm;
            height: auto;
        }

        .company_title {
            font-weight: bold;
            font-size: 13pt;
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

        td.dish-middle {
            border-right: 1px solid black;
            border-bottom: 1px solid black;
            border-top: 1px solid #e0e0e0;
            vertical-align: middle;

        }

        td.dish-last {
            border-right: 1px solid black;
            border-top: 1px solid #e0e0e0;
            vertical-align: middle;

        }

        .table_detail table, .table_detail th {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .table_detail td {
            padding-right: 1pt;
            padding-top: 1pt;
            padding-bottom: 1pt;
            line-height: 1.2;
        }

        .table_detail th {
            padding-top: 2pt;
            padding-bottom: 2pt;
            width: 100%;
        }
    </style>
</head>

<body>
@php $sumExtra = 0; $i = 1; $j = 1 @endphp
@foreach($data['student'] as $key => $item)
    <table id="invoice">
        <tr>
            <td colspan="1" rowspan="2" style="text-align: center">
                <img width="130" style="display: block;" class="logo" src="{{ asset('images/print_assets/davicook.png')}}"/>
            </td>
            <td class="company_title" colspan="10" align="center">CÔNG TY CỔ PHẦN DAVICOOK HÀ NỘI</td>
            <td colspan="1" align="center" class="company_title">{{ $item['week_no'] != '' ? 'Tuần '.$item['week_no'] : "" }}</td>
        </tr>
        <tr>
            <td colspan="10" style="text-align: center;">
                Địa chỉ : Xóm 10, Thôn 3, Xã Yên Mỹ, Huyện Thanh Trì, Hà Nội; ĐT: 024.3634.3714
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="company_title" style="font-size: 14pt" colspan="8" align="center"> {{ $item['card_name'] }}</td>
            <td colspan="2" ></td>
        </tr>
    </table>

    <table class="table_detail" >
        <thead>
        <tr>
            <th rowspan="2" align="center" style="width:8.5%;font-weight: bold; border: 1px solid black">THỨ</th>
            <th rowspan="2" align="center" style="width:40%;font-weight: bold; border: 1px solid black">MÓN ĂN</th>
            <th colspan="2" align="center" style="font-weight: bold; border: 1px solid black">Định lượng TP (gram)</th>
            <th rowspan="2" align="center" style="min-width:180px;font-weight: bold; border: 1px solid black">QUÀ CHIỀU</th>
        </tr>
        <tr>
            <th align="center" style="font-weight: bold; border: 1px solid black">Sống</th>
            <th align="center" style="max-width:200px;min-width: 200px; font-weight: bold; border: 1px solid black">Chín</th>
        </tr>
        </thead>
        <tbody>
        @foreach($item['first_details'] as $detail)
            <tr>
                <td rowspan="{{ $detail['count'] }}" colspan="1" align="center" style="vertical-align: middle;border: 1px solid black"><span>{{ $detail['day'] }}</span><br><span>{{ $detail['date'] }}</span></td>
                <td style="vertical-align: middle;border-right: 1px solid black; border-bottom: {{ $detail['count'] == 1 ? '1px solid black' : '' }}">{{ $detail['dish_name'] }}</td>
                <td style="vertical-align: middle;text-align: center;border-right: 1px solid black; border-bottom: {{ $detail['count'] == 1 ? '1px solid black' : '' }}">{{ $detail['qty_raw_dish'] }}</td>
                <td style="vertical-align: middle;text-align: center;border-right: 1px solid black; border-bottom: {{ $detail['count'] == 1 ? '1px solid black' : '' }}">{{ $detail['qty_cooked_dish'] }}</td>
                <td rowspan="{{ $detail['count'] }}" style="text-align: center;vertical-align: middle;border: 1px solid black">{!! $detail['product_gift_or_comment'] !!}</td>
            @foreach($detail['item'] as $keyNo => $value)
                @if($detail['count'] == $keyNo + 2)
                    <tr>
                        <td class="dish-middle" >{{ $value['dish_name'] }}</td>
                        <td class="dish-middle" style="text-align: center;" >{{ $value['qty_raw_dish'] }}</td>
                        <td class="dish-middle" style="text-align: center;" >{{ $value['qty_cooked_dish'] }}</td>
                    </tr>
                @else
                    <tr>
                        <td class="dish-last">{{ $value['dish_name'] }}</td>
                        <td class="dish-last" style="text-align: center;">{{ $value['qty_raw_dish'] }}</td>
                        <td class="dish-last" style="text-align: center;">{{ $value['qty_cooked_dish'] }}</td>
                    </tr>
                @endif

            @endforeach
        @endforeach
        </tbody>
    </table>
    <table class="footer_table">
        <tr style="font-size: 6pt">
            <td colspan="5" rowspan="2">
                <i>(Khuyến cáo các món ăn kết hợp không có lợi cho sức khỏe: Cá với sữa, trứng với sữa, Tôm với sữa, Bún, Phở với sữa - Rau,
                    canh có thể thay đổi theo mùa, hoặc do điều kiện khách quan)</i>
            </td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr>
            <td colspan="2" style="text-align: left"><i>Thực đơn gửi ngày:</i></td>
            <td colspan="3" style="text-align: right">Hà Nội, ngày …… tháng …… năm 20……</td>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="1" align="left"><b>Người lập</b></td>
            <td colspan="1" align="center"><b>Bếp trưởng</b></td>
            <td colspan="2" align="center"><b>P.Kỹ thuật chế biến món ăn</b></td>
            <td colspan="1" align="right"><b>Đại diện nhà trường</b></td>
        </tr>
    </table>
    <p style="page-break-after: always;">&nbsp;</p>
@endforeach

@foreach($data['teacher'] as $key => $item)
    <table class="header_table">
        <tr>
            <td colspan="1" rowspan="2" style="text-align: center">
                <img width="120" style="display: block;" class="logo" src="{{ asset('images/print_assets/davicook.png')}}"/>
            </td>
            <td colspan="3" align="center" class="company_title">CÔNG TY CỔ PHẦN DAVICOOK HÀ NỘI</td>
            <td colspan="1" align="center" class="company_title">{{ $item['week_no'] != '' ? 'Tuần '.$item['week_no'] : "" }}</td>
        </tr>
        <tr>
            <td colspan="5" style="text-align: center;">
                Địa chỉ : Xóm 10, Thôn 3, Xã Yên Mỹ, Huyện Thanh Trì, Hà Nội; ĐT: 024.3634.3714
            </td>
        </tr>
        <tr>
            <td colspan="1"></td>
            <td colspan="3" align="center" style="font-weight: bold; font-size: 14.5pt"> {{ $item['card_name'] }}</td>
            <td colspan="1" align="center" style="font-weight: bold; text-transform: uppercase"></td>
        </tr>
    </table>

    <table class="table_detail">
        <thead>
        <tr>
            <th align="center" style="width:8.5%;font-weight: bold; border: 1px solid black">THỨ</th>
            <th colspan="2" align="center" style="width:55%;font-weight: bold; border: 1px solid black">MÓN ĂN</th>
            <th colspan="2" align="center" style="font-weight: bold; border: 1px solid black; width: 25%">GHI CHÚ</th>
        </tr>
        </thead>
        <tbody>
        @foreach($item['first_details'] as $detail)
            <tr>
                <td rowspan="{{ $detail['count'] }}" align="center" style="vertical-align: middle;border: 1px solid black;">{{ $detail['day'] }}<br>{{ $detail['date'] }}</td>
                <td colspan="2" style="vertical-align: middle;border-right: 1px solid black; border-bottom: {{ $detail['count'] == 1 ? '1px solid black' : '' }} ">{{ $detail['dish_name'] }}</td>
                <td colspan="2" rowspan="{{ $detail['count'] }}"  style="vertical-align: middle;border: 1px solid black"></td>
            @foreach($detail['item'] as $keyNo => $value)
                @if($detail['count'] == $keyNo + 2)
                    <tr>
                        <td colspan="2" class="dish-middle">{{ $value['dish_name'] }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="2" class="dish-last">{{ $value['dish_name'] }}</td>
                    </tr>
                @endif
            @endforeach
        @endforeach
        </tbody>
    </table>

    <table class="footer_table">
        <tr style="font-size: 6pt">
            <td colspan="5" rowspan="2">
                <i>(Khuyến cáo các món ăn kết hợp không có lợi cho sức khỏe: Cá với sữa, trứng với sữa, Tôm với sữa, Bún, Phở với sữa - Rau,
                    canh có thể thay đổi theo mùa, hoặc do điều kiện khách quan)</i>
            </td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr>
            <td colspan="2" style="text-align: left"><i>Thực đơn gửi ngày:</i></td>
            <td colspan="3" style="text-align: right">Hà Nội, ngày …… tháng …… năm 20……</td>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="1" align="left"><b>Người lập</b></td>
            <td colspan="1" align="center"><b>Bếp trưởng</b></td>
            <td colspan="2" align="center"><b>P.Kỹ thuật chế biến món ăn</b></td>
            <td colspan="1" align="right"><b>Đại diện nhà trường</b></td>
        </tr>
    </table>
    <p style="page-break-after: always;">&nbsp;</p>
@endforeach
</body>
</html>