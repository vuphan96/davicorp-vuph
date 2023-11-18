
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
                <td rowspan="1" style="width: 3.5cm;padding-right: 0.5cm"><img class="logo" src="{{ public_path('images/print_assets/davicorp.png')}}"/>
                </td>
                <td  class="company_title">
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
                <td rowspan="1" style="width: 3.5cm;padding-right: 0.5cm"><img class="logo" src="{{ public_path('images/print_assets/davicorp.png')}}"/>
                </td>
                <td  class="company_title">
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
                <th colspan="10" style="text-align: center">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</th>
            </tr>
            <tr>
                <th colspan="10" style="text-align: center">Độc Lập - Tự Do - Hạnh Phúc</th>
            </tr>
            <tr>
                <th colspan="10" style="text-align: center">......o0o......</th>
            </tr>
        </table>
    @endif
    <br>
    <table>
        <thead>
        <tr>
            <th colspan="10" style="font-weight: bold; text-align: center; font-size: 14pt">GIẤY ĐỀ NGHỊ THANH TOÁN</th>
        </tr>
        </thead>
        <br>
        <tbody>
        <tr>
            <td colspan="1">Kính gửi : </td>
            <td colspan="9" align="left">{{ $data['customer']->name }} </td>
        </tr>
        <tr>
            <td colspan="1">Địa chỉ : </td>
            <td colspan="9" align="left">{{ $data['customer']->address }}</td>
        </tr>
        <tr>
            <td colspan="10">Đề nghị quý khách hàng thanh toán tiền  {{ $attributes['infor_payment'] ?? '....' }} cho đơn vị chúng tôi như sau: </td>
        </tr>
        @if(isset($attributes['name_price_info_by_transfer']) || isset($attributes['number_price_info_by_transfer']))
            <tr>
                <td colspan="2">{{ $attributes['name_price_info_by_transfer'] ?? '.....' }} : </td>
                <td colspan="3" style="font-weight: bold">{{ number_format($attributes['number_price_info_by_transfer'] ?? 0) }}</td>
                <td colspan="5" style="font-weight: bold;">đồng</td>
            </tr>
        @endif
        @if(isset($attributes['name_price_by_transfer']) || isset($attributes['number_price_by_transfer']))
            <tr>
                <td colspan="2">{{ $attributes['name_price_by_transfer'] ?? '.....' }} : </td>
                <td colspan="3" style="font-weight: bold">{{ number_format($attributes['number_price_by_transfer'] ?? 0) }}</td>
                <td colspan="5" style="font-weight: bold;">đồng</td>
            </tr>
        @endif
        <tr>
            <td colspan="2">Tổng tiền : </td>
            <td colspan="3" style="font-weight: bold">{{ number_format($attributes['number_total_price_by_transfer']) }}</td>
            <td colspan="5" style="font-weight: bold;">đồng</td>

        </tr>
        <tr>
            <td colspan="2" style="vertical-align: top">Bằng chữ : </td>
            <td colspan="8" style="font-weight: bold">{{ $attributes['text_total_price_by_transfer'] ?? '.....' }}</td>
        </tr>
        <tr>
            <td colspan="10">Số tiền trên được chuyển khoản vào :</td>
        </tr>
        @if($data['keyDepartment'] == 1 || $data['keyDepartment'] == 2)
            <tr>
                <td colspan="2">Chủ tài khoản : </td>
                <td colspan="8" style="font-weight: bold">Công ty cổ phần Davicorp Việt Nam</td>
            </tr>
            <tr>
                <td colspan="2">Số tài khoản : </td>
                <td colspan="3" style="font-weight: bold">113002655224</td>
                <td colspan="5" >Mã citad: 01201023</td>
            </tr>
            <tr>
                <td colspan="2">Tại Ngân Hàng : </td>
                <td colspan="8" style="font-weight: bold">Ngân hàng Vietinbank Tràng An Hà Nội</td>
            </tr>
        @endif
        @if($data['keyDepartment'] == 3)
            <tr>
                <td colspan="2">Chủ tài khoản : </td>
                <td colspan="8" style="font-weight: bold">NGUYỄN ĐỨC ĐẠI</td>
            </tr>
            <tr>
                <td colspan="2">Số tài khoản : </td>
                <td colspan="3" style="font-weight: bold">6860148168888</td>
                <td colspan="5" >Mã citad : 01311007 </td>

            </tr>
            <tr>
                <td colspan="2">Tại Ngân Hàng : </td>
                <td colspan="8" style="font-weight: bold">Ngân hàng MB  Hai Bà Trưng</td>
            </tr>
        @endif
        @if($data['keyDepartment'] == 5)
            <tr>
                <td colspan="2">Chủ tài khoản : </td>
                <td colspan="8" style="font-weight: bold">Vũ Trường Giang</td>
            </tr>
            <tr>
                <td colspan="2">Số tài khoản : </td>
                <td colspan="3" style="font-weight: bold">03501011979598</td>
                <td colspan="5" >Mã citad: 01302001 </td>

            </tr>
            <tr>
                <td colspan="2">Tại Ngân Hàng : </td>
                <td colspan="8" style="font-weight: bold">Ngân hàng Maritime Bank Nam Hà Nội</td>
            </tr>
        @endif
        <tr>
            <td colspan="10">Trân trọng cảm ơn!</td>
        </tr>
        <tr>
            <td colspan="6"></td>
            <td colspan="4" style="font-style: italic">Hà Nội, {{ formatStringDate($attributes['date_create'], 'ngaythangnam') ?? '....' }}</td>
        </tr>
        </tbody>
    </table>
    <br>
    <div style="min-height: 200px;">
        <table>
            <tr>
                @if($data['keyDepartment'] == 3 || $data['keyDepartment'] == 5)
                    <td colspan="5" rowspan="2" style="text-align: center; font-weight: bold">Chủ cửa hàng</td>
                @else
                    <td colspan="5" rowspan="2" style="text-align: center; font-weight: bold">Giám đốc công ty</td>
                @endif
                <td colspan="5" rowspan="2" style="text-align: center; font-weight: bold">Người lập</td>
            </tr>
            <tr>
                <td colspan="2" style="border: none"></td>
                <td colspan="4" style="border: none"></td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>