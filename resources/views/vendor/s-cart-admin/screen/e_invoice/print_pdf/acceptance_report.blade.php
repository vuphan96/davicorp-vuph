
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BIÊN BẢN NGHIỆM THU</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Times New Roman", Serif;
            line-height: 0.9;
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
            font-size: 12pt;
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
            font-size: 12pt;
            text-align: center;
            font-style: bold;
            padding: 5px auto;
        }
        table.table_detail td {
            border: 1px solid black;
            font-size: 12pt;
            padding: 5px;
        }
    </style>
</head>
<body>
<div id="invoice" class="webview_hide">
    <table>
        <tr>
            <th colspan="6" style="text-align: center; font-weight: bold">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: center; font-weight: bold">Độc Lập - Tự Do - Hạnh Phúc</th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: center">......o0o......</th>
        </tr>
        <br>
        <tr>
            <th colspan="6" style="font-weight: bold; text-align: center; font-size: 16pt">BIÊN BẢN NGHIỆM THU</th>
        </tr>
        <tr>
            <th colspan="6" style="font-weight: bold; text-align: center">{{ formatStringDate($attributes['einvoice_date'], 'Ngaythangnam') ?? 'Ngày ..... tháng ..... năm .....' }}</th>
        </tr>
        <tr>
            <th colspan="6" style="font-weight: bold; text-align: left">Căn cứ hợp đồng cung cấp thực phẩm Số : {{ $attributes['units_use'] ?? '.......' }} ngày&nbsp;{{ $attributes['units_date'] ?? '......' }}&nbsp;giữa&nbsp;{{ $attributes['units_code'] ?? '.......' }}
                &nbsp; Và {{ $attributes['source_code'] ?? '........' }}</th>
        </tr>
        <tr>
            <td colspan="6">Theo nhu cầu và thỏa thuận giữa hai bên.</td>
        </tr>
        <tr>
            <td colspan="6"></td>
        </tr>
        <tr>
            <td colspan="6">Hôm nay, {{ formatStringDate($attributes['date'], 'ngaythangnam') ?? 'ngày .... tháng .... năm ....' }} , Chúng tôi gồm:</td>
        </tr>
        <tr>
            <th colspan="6" style="font-weight: bold; text-align: left"><h5>1. ĐƠN VỊ SẢN XUẤT, SƠ CHẾ VÀ ĐÓNG GÓI:	</h5></th>
        </tr>
        <tr>
            <th colspan="6" style="font-weight: bold; text-align: left"><h5>CÔNG TY CỔ PHẦN DAVICORP VIỆT NAM</h5></th>
        </tr>
        <tr>
            <td colspan="6">Địa chỉ  : Số 34B - Lô 2 – KĐT Đền Lừ 1, Hoàng Mai,  Hà Nội</td>
        </tr>
        <tr>
            <td colspan="6">Điện thoại : 024.36340867</td>
        </tr>
        <tr>
            <td colspan="6">Tài khoản  : 113002655224   Tại NH VietinBank - CN Tràng An .</td>
        </tr>
        <tr>
            <td colspan="4">Người đại diện  : (Bà) Nguyễn Thị Hồng Hải</td>
            <td colspan="2">Chức vụ : Giám đốc</td>
        </tr>
        <tr>
            <th colspan="6" style="font-weight: bold;text-align: left"><h5>2. ĐƠN VỊ PHÂN PHỐI BÁN HÀNG TRỰC TIẾP: </h5></th>
        </tr>
        <tr>
            <th colspan="6" style="font-weight: bold;text-align: left"><h5>CỬA HÀNG THỰC PHẨM SẠCH DAVICORP</h5></th>
        </tr>
        <tr>
            <td colspan="6">Địa chỉ  : Số 15 ngõ 40 Phố Tạ Quang Bửu, P. Bách Khoa, Q. Hai Bà Trưng, TP Hà Nội</td>
        </tr>
        <tr>
            <td colspan="6">Điện thoại : 0982.229.536</td>
        </tr>
        <tr>
            <td colspan="4">Người đại diện    : (Ông) Nguyễn Đức Đại</td>
            <td colspan="2">Chức vụ: Chủ cửa hàng</td>
        </tr>
        <tr>
            <td colspan="6">Chủ tài khoản Nguyễn Đức Đại  Số TK : 6860.1481.68888 -  Ngân hàng MB – CN Hai Bà Trưng.</td>
        </tr>
        <tr>
            <th colspan="6" style="font-weight: bold; text-align: left; text-transform: uppercase"><h5>BÊN B.  {{ $data['customer']->name }} </h5></th>
        </tr>
        <tr>
            <td colspan="6">Địa chỉ  : {{ $data['customer']->address }}</td>
        </tr>
        <tr>
            <td colspan="6">Điện thoại : {{ $data['customer']->phone }}</td>
        </tr>
        <tr>
            <td colspan="6">Mã số thuế : {{ $data['customer']->tax_code }}</td>
        </tr>
        <tr>
            <td colspan="6">Số tài khoản : {{ $attributes['account_number_bank'] ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="4" style="font-weight: bold">Người đại diện : {{ $attributes['representative_name'] ?? '' }}</td>
            <td colspan="2" style="font-weight: bold">Chức vụ : {{ $attributes['position'] ?? '' }}</td>
        </tr>

        <tr>
            <td colspan="6">Hai bên thống nhất ký biên bản nghiệm thu với những nội dung sau:</td>
        </tr>
        <tr>
            <td colspan="6">Nội dung công việc đã thực hiện: Bên A đã hoàn thành cung cấp thực phẩm cho bên B trong tháng {{ $attributes['month_contract'] ?? '....' }}
                năm {{ $attributes['month_contract'] ?? '....' }}  đúng như trong hợp đồng số {{ $attributes['number_contract'] ?? '....' }}
                ký {{ formatStringDate($attributes['date_contract'], 'ngaythangnam') ?? '....' }} </td>
            \Carbon\Carbon::parse($user->created_at)->format('d/m/Y');
        </tr>
        <tr>
            <td colspan="6">theo bảng tổng hợp số lượng và giá trị hàng hóa từ
                {{ !empty($attributes['start_date_effective_contract']) ? \Carbon\Carbon::parse($attributes['start_date_effective_contract'])->format('d/m/Y') : '....' }}
                đến {{ !empty($attributes['end_date_effective_contract']) ? \Carbon\Carbon::parse($attributes['end_date_effective_contract'])->format('d/m/Y') : '....' }}
                như sau:</td>
        </tr>
    </table>
    <table>
        <tr>
            <td colspan="6" style="font-style: italic; text-align: right">Đơn vị: Đồng</td>
        </tr>
    </table>
    <table style="border-collapse: collapse" border="1px" class="table_detail">
        <thead>
        <tr style="width: 100%" class="heading-report">
            <th align="center" style="border-right: 1px solid black; border-bottom: 1px solid black;">STT</th>
            <th align="center" style="border-right: 1px solid black; border-bottom: 1px solid black;">Tên mặt hàng</th>
            <th align="center" style="border-right: 1px solid black; border-bottom: 1px solid black;">Đvt</th>
            <th align="center" style="border-right: 1px solid black; border-bottom: 1px solid black;">Số lượng</th>
            <th  align="center" style="border-right: 1px solid black; border-bottom: 1px solid black;">Giá Bán</th>
            <th  align="center" style="border-right: 1px solid black; border-bottom: 1px solid black;">Doanh thu</th>
        </tr>

        </thead>
        <tbody>
        @php
            $sumPrice = 0;
        @endphp
        @forelse($data['einvoice'] as $key => $value)
            <tr>
                <td align="center">{{ $key + 1 ?? ''}}</td>
                <td>{{ $value['product_name'] ?? ''}}</td>
                <td>{{ $value['unit'] ?? ''}}</td>
                <td style="text-align: right;">{{ $value['qty'] ?? ''}}</td>
                <td style="text-align: right;">{{ number_format($value['price'] ?? 0)}}</td>
                <td style="text-align: right;">{{ number_format($value['total_price'] ?? 0)}}</td>
            </tr>
            @php
                $sumPrice += $value['total_price'];
            @endphp
        @empty
            <td colspan="10">Không có nội dung!</td>
        @endforelse
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center"> Tiền hàng</td>
            <td colspan="4"  style="border: 1px solid black"></td>
            <td style="border: 1px solid black; text-align: right">{{ number_format($sumPrice ?? 0) }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center">Tổng cộng</td>
            <td colspan="4"  style="border: 1px solid black"></td>
            <td style="border: 1px solid black; text-align: right">{{ number_format($sumPrice ?? 0) }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center">Bằng chữ</td>
            <td colspan="5"  style="border: 1px solid black; font-weight: bold; text-align: center">{{ convert_number_to_words($sumPrice) }} đồng</td>
        </tr>
        </tbody>
    </table>
    <table>
        <br>
        <tr>
            <td colspan="6">Bên A đã hoàn thành việc cung cấp cho bên B hàng hoá các loại theo đúng quy cách chủng loại và số lượng như trên. Bên B đồng ý nghiệm thu.
            </td>
        </tr>
        <tr>
            <td colspan="6" style="font-weight: bold;">Tổng giá trị nghiệm thu : &nbsp; {{ number_format($sumPrice ?? 0) }} đồng ( Bằng chữ : &nbsp; {{ convert_number_to_words($sumPrice) }} đồng) </td>
        </tr>
        <tr>
            <td colspan="6" style="font-weight: bold;"> Số tiền bên B phải thanh toán cho bên A là : &nbsp; {{ number_format($sumPrice ?? 0) }} đồng ( Bằng chữ : &nbsp;  {{ convert_number_to_words($sumPrice) }} đồng)	</td>
        </tr>
        <tr>
            <td colspan="6"> Hai bên thống nhất nghiệm thu với nội dung nêu trên.</td>
        </tr>
        <tr>
            <td colspan="6">Biên bản nghiệm thu được lập thành 02 bản có giá trị pháp lý như nhau, bên A giữ 1 bản, bên B giữ 1 bản làm căn cứ thanh toán.</td>
        </tr>
    </table>
    <div style="min-height: 200px;">
        <table>
            <tr>
                <td colspan="2" rowspan="2" style="text-align: center">ĐẠI DIỆN BÊN A</td>
                <td colspan="4" rowspan="2" style="text-align: center">ĐẠI DIỆN BÊN B</td>
            </tr>
            <tr>
                <td colspan="2" style="border: none"></td>
                <td colspan="4" style="border: none"></td>
            </tr>
            <tr>
                <td colspan="2" rowspan="1" style="text-align: center">(Ký, ghi rõ họ tên và đóng dấu)</td>
                <td colspan="4" rowspan="1" style="text-align: center">(Ký, ghi rõ họ tên và đóng dấu)</td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>