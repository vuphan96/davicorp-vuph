<!doctype html>
<html lang="en">
<head>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Mẫu giấy giới thiệu</title>
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
            font-size: 16pt;
            text-align: center;
        }
        .invoice_header {
            font-size: 12pt;
            text-align: center;
            line-height: 20px;
        }
        .wrapper-content{
            width: 100%;
        }
        .wrapper-content p {
            margin-bottom: 6px;
        }
        .title{
            width: 18%;
            float: left;
        }
        .content{
            width: 80%;
            float: right;
        }
        .clear{
            clear: both;
        }
    </style>
</head>
<body>
<div id="invoice" class="webview_hide">
    <div class="wrapper-content">
        <div style="width: 40%;float: left">
            <h3 class="invoice_header" >{{ $data['object_header'] ?? '' }}</h3>
        </div>
        <div style="width: 56%; float: right">
            <h3 class="invoice_header">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</h3>
            <p style="text-align: center"><span style="display: inline-block; border-bottom: 2px black solid "><b>Độc lập – Tự do – Hạnh phúc</b></span></p>
        </div>
        <div class="clear"></div>
    </div>
    <br>
    <div>
        <p align="right" style="padding-right: 30px"><i>Hà Nội, {{ formatStringDate($data['invoice_date'], 'ngaythangnam') ?? 'ngày ... tháng ... năm ...' }}</i></p>
    </div>
    <br>
    <div class="modal-title" style="text-align: center">
        <h2 class="invoice_title">GIẤY GIỚI THIỆU</h2>
    </div>
    <br><br>
    <div class="wrapper-content">
        <p class="title">Kính gửi:</p>
        <p class="content">{{ $data['name_customer'] ?? '' }}</p>
        <p class="clear"></p>
    </div>
    <div class="wrapper-content">
        <p style="margin-top: -6px">Giới thiệu ông/ bà : {{ $data['name'] ?? '' }}</p>
    </div>
    <div class="wrapper-content">
        <p class="title">Chức vụ:</p>
        <p class="content">{{ $data['position'] ?? '' }}</p>
        <p class="clear"></p>
    </div>
    <div class="wrapper-content">
        <p style="margin-top: -6px"><span style="margin-right: 20px">CMT số:</span>{{ $data['cmt'] ?? '' }}<span style="margin: 0px 20px">Ngày cấp:</span>{{ !empty($data['date_supply']) ? \Carbon\Carbon::parse($data['date_supply'])->format('d/m/Y') : '' }}<span style="margin: 0px 20px">Nơi cấp:</span>{{ $data['local_supply'] ?? '' }}</p>
    </div>
    <div class="wrapper-content">
        <p class="title">Được đề cử đến:</p>
        <p class="content">{{ $data['address'] ?? '' }}</p>
        <p class="clear"></p>
    </div>
    <div class="wrapper-content">
        <p style="margin-top: -5px">Về việc</p>
        <p>Đề nghị Quý cơ quan tạo điều kiện để ông (bà) có tên ở trên hoàn thành nhiệm vụ.</p>
        <p>Giấy này có giá trị đến hết {{ !empty(formatStringDate($data['date_effect'], 'ngaythangnam')) ? formatStringDate($data['date_effect'], 'ngaythangnam') : 'ngày ... tháng ... năm ...' }}</p>
    </div>
    <div class="wrapper-content">
        <p>Nơi nhận</p>
    </div>
    <div class="wrapper-content">
        <p class="title">Như trên:</p>
        <p class="content" style="text-align: center"><b>{{ $data['object_name'] ?? '' }}</b></p>
        <p class="clear"></p>
    </div>
    <div class="wrapper-content">
        <p style="margin-top: -6px">Như TV</p>
    </div>

</div>
</body>
</html>