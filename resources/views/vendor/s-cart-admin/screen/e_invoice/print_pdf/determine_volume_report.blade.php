<!doctype html>
<html lang="en">
<head>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bảng khối lượng công việc hoàn thành</title>
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
            /*margin: 10px auto;*/
            /*width: 100%;*/
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
            font-size: 14pt;
            text-align: center;
        }
        .invoice_time {
            text-align: center;
        }

        table.table_detail {
            border-collapse: collapse;
        }
        table.table_detail tr td, th {
            text-align: center;
            border: 1px solid black
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
            padding: 5px 0px 5px 5px;
        }
        .modal-title-header {
            text-align: right;
        }
        .clear{
            clear: both;
        }
        .webview_hide div {
            margin-bottom: 4pt !important;
        }
    </style>
</head>
<body>
<div id="invoice" class="webview_hide">
    <div class="modal-title-header ">
        <div style="font-weight: bold">
            Mẫu số 08a
        </div>
    </div>
    <div class="modal-title-header row mb-1">
        <div>
            Mã hiệu: {{ $data['brand_code'] ?? '....' }}
        </div>
    </div>
    <div class="modal-title-header ">
        <div>Số: {{ $data['number'] ?? '....' }}</div>
    </div>
    <br>
    <div class="modal-title" style="text-align: center">
        <h2 class="invoice_title">BẢNG XÁC ĐỊNH GIÁ TRỊ KHỐI LƯỢNG CÔNG VIỆC HOÀN THÀNH</h2>
    </div>
    <br>
    <div>
        <p>1. Đơn vị sử dụng ngân sách: {{ $data['units_use'] ?? '....' }}</p>
    </div>
    <div>
        <p>2. Mã đơn {{ $data['units_code'] ?? '....' }}    Mã nguồn: {{ $data['source_code'] ?? '....' }}</p>
    </div>
    <div>
        <p>3. Mã CTMTQG, Dự án ODA {{ $data['project_code'] ?? '................' }}</p>
    </div>
    <div>
        <p style="line-height: 18px; margin: -4px 0px">4. Căn cứ Hợp đồng số: {{ $data['number_contract'] ?? '....' }} , ký {{ formatStringDate($data['date_acceptance'], 'ngaythangnam') ?? 'ngày ... tháng ... năm ...' }} giữa {{ $data['objecta'] ?? '....' }} với {{ $data['objectb'] ?? '....' }} hai bên tiến hành xác định giá trị khối lượng hàng hóa như sau:</p>
    </div>
    <div>
        <p>5. Căn cứ Biên bản nghiệm thu {{ formatStringDate($data['report_acceptance'], 'ngaythangnam') ?? 'ngày ... tháng ... năm ...' }} giữa trường {{ $data['object_start'] ?? '....' }} với {{ $data['object_end'] ?? '....' }}</p>
    </div>
    <div>
        <p style="text-align: right">Đơn vị: Đồng</p>
    </div>
    <div>
        <table style="border-collapse: collapse" border="1px" class="table_detail">
            <thead>
            <tr>
                <th>STT</th>
                <th style="text-align: left; padding: 0px 5px;">Tên mặt hàng</th>
                <th>Đvt</th>
                <th>Số lượng</th>
                <th>Giá Bán</th>
                <th>Doanh thu</th>
            </tr>
            </thead>
            <tbody>
            @php
                $sumPrice = 0;
            @endphp
            @forelse($objInvoiceDetails as $key => $objInvoiceDetail)
                <tr>
                    <td>{{ $key + 1 ?? ''}}</td>
                    <td style="text-align: left; padding-left: 5px">{{ $objInvoiceDetail['product_name'] ?? ''}}</td>
                    <td>{{ $objInvoiceDetail['unit'] ?? ''}}</td>
                    <td>{{ $objInvoiceDetail['qty'] ?? ''}}</td>
                    <td style="text-align: right; padding-right: 5px">{{ number_format((int)$objInvoiceDetail['price']) ?? ''}}</td>
                    <td style="text-align: right; padding-right: 5px">{{ number_format((int)$objInvoiceDetail['total_price']) ?? ''}}</td>
                </tr>
                @php
                    $sumPrice += $objInvoiceDetail['total_price'];
                @endphp
            @empty
                <tr>
                    <td style="border: 1px solid black"></td>
                </tr>

            @endforelse
            <tr>
                <th style="text-align: left; padding-left: 5px"> Tiền hàng</th>
                <th colspan="4"></th>
                <th style="text-align: right; padding-right: 5px">{{ number_format((int)$sumPrice) ?? '....'}}</th>
            </tr>
            <tr>
                <th style="text-align: left; padding-left: 5px">Tổng cộng</th>
                <th colspan="4"></th>
                <th style="text-align: right; padding-right: 5px">{{ number_format((int)$sumPrice) ?? '....'}}</th>
            </tr>
            <tr>
                <th style="text-align: left; padding-left: 5px">Bằng chữ</th>
                <th colspan="5"  style="text-align: left; padding-left: 5px">{{ convert_number_to_words($sumPrice) }} đồng</th>
            </tr>
            </tbody>
        </table>
        <br>
        <div>
            <p>6. Lũy kế thanh toán khối lượng hoàn thành đến cuối kỳ trước: <b>{{ !empty($data['volume_finished']) ?  number_format((int)$data['volume_finished']) : '....' }}</b> đồng</p>
        </div>
        <div>
            <p>- Thanh toán tạm ứng: <b>{{ !empty($data['pay_advance']) ?  number_format((int)$data['pay_advance']) : '....' }}</b> đồng </p>
        </div>
        <div>
            <p>7. Số dư tạm ứng đến cuối kỳ trước:  <b>{{ !empty($data['surplus']) ? number_format((int)$data['surplus']) : '....' }}</b> đồng</p>
        </div>
        <div ><p>8. Số đề nghị thanh toán kỳ này : <b>{{ number_format((int)$sumPrice) ?? '....'}}</b> đồng</p>
        </div>
        <div>
            <p>- Thanh toán tạm ứng: <b>{{ !empty($data['pay_advance_request']) ? number_format((int)$data['pay_advance_request']) : '....' }}</b> &nbsp;&nbsp;&nbsp;&nbsp; - Thanh toán chuyển khoản: <b>{{ !empty($data['pay_transfer']) ? number_format((int)$data['pay_transfer']) : '....' }}</b> đồng</p>
        </div>
        <br>
        <br>
        <div>
            <p style="text-align: right"><i>{{ formatStringDate($data['date'], 'Ngaythangnam') ?? 'Ngày ... tháng ... năm ...' }}</i></p>
        </div>
        <br>
        <div style="width: 100%">
            <div style="width: 40%;float: left">
                <p style="text-align: center; font-weight: bold; line-height: 18px">ĐẠI DIỆN NHÀ CUNG CẤP HÀNG HÓA, DỊCH VỤ</p>
            </div>
            <div style="width: 40%; float: right">
                <p style="text-align: center; font-weight: bold; line-height: 18px">ĐẠI DIỆN ĐƠN VỊ SỬ DỤNG NGÂN SÁCH</p>
            </div>
            <div class="clear"></div>
        </div>
        <div style="width: 100%">
            <div style="width: 40%;float: left">
                <p style="text-align: center"><i>(Ký, ghi rõ họ tên và đóng dấu)</i></p>
            </div>
            <div style="width: 40%; float: right">
                <p style="text-align: center"><i>(Ký, ghi rõ họ tên và đóng dấu)</i></p>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>
</body>
</html>