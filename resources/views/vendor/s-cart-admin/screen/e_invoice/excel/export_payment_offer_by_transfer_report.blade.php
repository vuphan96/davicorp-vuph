
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mẫu đề nghị thanh toán</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Times New Roman", Serif;
            line-height: 0.9;
            font-size: 12pt;
        }
        body {
            width: 630px;
            height: 100%;
            margin: 20px auto;
            padding: 0;
        }
        @page {
            size: A4;
            margin: 0;
        }

        @media print {

            html,
            body {
                width: 210mm;
                height: 297mm;
            }

            .page {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                page-break-after: always;
            }
        }
        .webview_hide {
            margin: auto -10px;
        }

        table {
            width: 100%;
            height: auto;
        }
        * {
            font-size: 10pt;
        }
        .invoice_title {
            font-size: 15pt;
            text-align: center;
            /*font-weight: 800 ;*/
            /*font-family: DejaVu Sans, sans-serif;*/
        }
        .invoice_time {
            text-align: center;
        }

        table.table_detail {
            border-collapse: collapse;
        }
        table.table_detail tr td.detail {
            text-align: center;
        }
        table.table_detail th {
            background-color: #cdcdcd;
            border: 1px solid black;
            font-size: 12px;
            text-align: center;
            font-style: bold;
            padding: 5px auto;
        }
        table.table_detail td {
            border: 1px solid black;
            font-size: 12px;
            padding: 5px 0px 5px 5px;
        }
        .logo {
            width: 3.5cm;
            height: auto;
        }
    </style>
</head>
<body>
<div id="invoice" class="webview_hide">
    @if($data['keyDepartment'] == 1 || $data['keyDepartment'] == 2)
        <table>
            <tr>
                <td rowspan="1" colspan="2" style="padding-right: 0.5cm; text-align: center"><img width="150" style="display: block; margin-left: 10px" class="logo" src="{{ public_path('images/print_assets/davicorp.png')}}"/>
                </td>
                <td  class="company_title" colspan="6" style="text-align: center">
                    <b>CÔNG TY CỔ PHẦN DAVICORP VIỆT NAM</b>
                    <br/>Địa chỉ: Số 34b Lô 2, Đền lừ 1, Hoàng Mai, Hà Nội
                    <br/>ĐT: 04 3634 3714  -   04 3634 3961
                    <br/>Website: Davicorp.vn - Email : davicorp.vn@gmail.com
                </td>
            </tr>
        </table>
    @endif
    @if($data['keyDepartment'] == 5)
        <table>
            <tr>
                <td rowspan="1" colspan="2" style="padding-right: 0.5cm; text-align: center"><img width="150" style="display: block; margin-left: 10px" class="logo" src="{{ public_path('images/print_assets/davicorp.png')}}"/>
                </td>
                <td  class="company_title" colspan="6" style="text-align: center">
                    <b>CỬA HÀNG THỰC PHẨM SẠCH DAVICORP VŨ TRƯỜNG GIANG</b>
                    <br/>Địa chỉ: Xóm 10, thôn 3, xã Yên Mỹ, Huyễn Thanh Trì, TP Hà Nội
                    <br/>ĐT: 04 3634 3714  -   04 3634 3961
                    <br/>Website: Davicorp.vn - Email : davicorp.vn@gmail.com
                </td>
            </tr>
        </table>
    @endif
    @if($data['keyDepartment'] == 3)
        <table>
            <tr>
                <th colspan="8" style="text-align: center;font-size: 13pt">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</th>
            </tr>
            <tr>
                <th colspan="8" style="text-align: center;font-weight: bold;font-size: 13pt">Độc Lập - Tự Do - Hạnh Phúc</th>
            </tr>
            <tr>
                <th colspan="8" style="text-align: center; font-size: 13pt">......o0o......</th>
            </tr>
        </table>
    @endif
    <table>
        <thead>
        <tr>
            <th colspan="8" style="font-weight: bold; text-align: center; font-size: 14pt">GIẤY ĐỀ NGHỊ THANH TOÁN</th>
        </tr>
        </thead>
        <br>
        <tbody>
        <tr>
            <td colspan="1" style="font-size: 12pt">Kính gửi : </td>
            <td colspan="7" align="left" style="font-size: 12pt">{{ $data['customer']->name }} </td>
        </tr>
        <tr>
            <td colspan="1" style="font-size: 12pt">Địa chỉ : </td>
            <td colspan="7" align="left" style="font-size: 12pt">{{ $data['customer']->address }}</td>
        </tr>
        <tr>
            <td colspan="8" style="font-size: 12pt">Đề nghị quý khách hàng thanh toán tiền  {{ $attributes['infor_payment'] ?? '....' }} cho đơn vị chúng tôi như sau: </td>
        </tr>
        @if(isset($attributes['name_price_info_by_transfer']) || isset($attributes['number_price_info_by_transfer']))
            <tr>
                <td colspan="1" style="font-size: 12pt">{{ $attributes['name_price_info_by_transfer'] ?? '.....' }} :</td>
                <td colspan="2" style="font-size: 12pt; text-align: right;font-weight: bold;">{{ ($attributes['number_price_info_by_transfer'] ?? 0) }}</td>
                <td colspan="5" style="font-size: 12pt;font-weight: bold;">đồng</td>
            </tr>
        @endif
        @if(isset($attributes['name_price_by_transfer']) || isset($attributes['number_price_by_transfer']))
            <tr>
                <td colspan="1">{{ $attributes['name_price_by_transfer'] ?? '.....' }} :</td>
                <td colspan="2" style="font-weight: bold; text-align: right">{{ ($attributes['number_price_by_transfer'] ?? 0) }}</td>
                <td colspan="5" style="font-size: 12pt;font-weight: bold;">đồng</td>
            </tr>
        @endif
        <tr>
            <td colspan="1" style="font-size: 12pt">Tổng tiền : </td>
            <td colspan="2" style="font-size: 12pt; text-align: right;font-weight: bold;">{{ ($attributes['number_total_price_by_transfer'] ?? 0) }}</td>
            <td colspan="5" style="font-size: 12pt;font-weight: bold;">đồng</td>
        </tr>
        <tr>
            <td colspan="1" style="font-size: 12pt">Bằng chữ : </td>
            <td colspan="7" style="font-size: 12pt; font-weight: bold">{{ $attributes['text_total_price_by_transfer'] ?? '.....' }}</td>
        </tr>
        <tr>
            <td colspan="8" style="font-size: 12pt">Số tiền trên được chuyển khoản vào :</td>
        </tr>
        @if($data['keyDepartment'] == 1 || $data['keyDepartment'] == 2)
            <tr>
                <td colspan="1" style="font-size: 12pt">Chủ tài khoản : </td>
                <td colspan="7" style="font-weight: bold;font-size: 12pt">Công ty cổ phần Davicorp Việt Nam</td>
            </tr>
            <tr>
                <td colspan="1" style="font-size: 12pt">Số tài khoản : </td>
                <td colspan="3" style="font-weight: bold; text-align: left; font-size: 12pt">&nbsp;{{ " 113002655224 " }}</td>
                <td colspan="4" style="text-align: left; font-size: 12pt">Mã citad: 01201023</td>

            </tr>
            <tr>
                <td colspan="1" style="font-size: 12pt">Tại Ngân Hàng : </td>
                <td colspan="7" style="font-weight: bold; font-size: 12pt">Ngân hàng Vietinbank Tràng An Hà Nội</td>
            </tr>
        @endif
        @if($data['keyDepartment'] == 3)
            <tr>
                <td colspan="1" style="font-size: 12pt">Chủ tài khoản : </td>
                <td colspan="7" style="font-weight: bold; font-size: 12pt">NGUYỄN ĐỨC ĐẠI</td>
            </tr>
            <tr>
                <td colspan="1" style="font-size: 12pt">Số tài khoản : </td>
                <td colspan="3" style="font-weight: bold; text-align: left; font-size: 12pt">&nbsp;{{ " 6860148168888 " }}</td>
                <td colspan="4" style="text-align: left; font-size: 12pt">Mã citad : 01311007 </td>
            </tr>
            <tr>
                <td colspan="1" style="font-size: 12pt">Tại Ngân Hàng : </td>
                <td colspan="7" style="font-weight: bold; font-size: 12pt">Ngân hàng MB  Hai Bà Trưng</td>
            </tr>
        @endif
        @if($data['keyDepartment'] == 5)
            <tr>
                <td colspan="1" style="font-size: 12pt">Chủ tài khoản : </td>
                <td colspan="7" style="font-weight: bold; font-size: 12pt">Vũ Trường Giang</td>
            </tr>
            <tr>
                <td colspan="1" style="font-size: 12pt">Số tài khoản : </td>
                <td colspan="3" style="font-weight: bold; text-align: left; font-size: 12pt">&nbsp;{{ " 03501011979598 " }}</td>
                <td colspan="4" style="text-align: left; font-size: 12pt">Mã citad: 01302001 </td>
            </tr>
            <tr>
                <td colspan="1" style="font-size: 12pt">Tại Ngân Hàng : </td>
                <td colspan="7" style="font-weight: bold; font-size: 12pt">Ngân hàng Maritime Bank Nam Hà Nội</td>
            </tr>
        @endif
        <tr>
            <td colspan="8" style="font-size: 12pt">Trân trọng cảm ơn!</td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td colspan="3" style="font-style: italic; font-size: 12pt">Hà Nội, {{ formatStringDate($attributes['date_create'], 'ngaythangnam') ?? '....' }}</td>
        </tr>
        </tbody>
    </table>
    <div style="min-height: 200px;">
        <table>
            <tr>
                @if($data['keyDepartment'] == 3 || $data['keyDepartment'] == 5)
                    <td colspan="4" rowspan="2" style="text-align: center; font-weight: bold;font-size: 13pt">Chủ cửa hàng</td>
                @else
                    <td colspan="4" rowspan="2" style="text-align: center; font-weight: bold; font-size: 13pt">Giám đốc công ty</td>
                @endif
                <td colspan="4" rowspan="2" style="text-align: center; font-weight: bold; font-size: 13pt">Người lập</td>
            </tr>
            <tr>
                <td colspan="4" style="border: none"></td>
                <td colspan="4" style="border: none"></td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>