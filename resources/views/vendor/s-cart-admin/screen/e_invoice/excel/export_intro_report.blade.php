<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MauGiayGioiThieu</title>
</head>
<body>
<table>
    <thead>
    <tr>
        <th colspan="3" rowspan="2" valign="center" align="center" style="font-size: 12pt"><h2><b>{{ $data['object_header'] ?? '' }}</b></h2></th>
        <th colspan="3" valign="center" align="center"style="font-size: 12pt"><h2><b>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</b></h2></th>
    </tr>
    <tr>
        <th colspan="3" valign="center" align="center" style="font-size: 12pt"><b>Độc lập – Tự do – Hạnh phúc</b></th>
    </tr>
    <tr>
        <th ></th>
    </tr>
    <tr>
        <th colspan="4"></th>
        <th colspan="2" style="font-size: 12pt"><i>Hà Nội, {{ formatStringDate($data['invoice_date'], 'ngaythangnam') ?? 'ngày ... tháng ... năm ...' }}</i></th>
    </tr>
    <tr>
        <th ></th>
    </tr>
    <tr>
        <th colspan="6" align="center" style="font-size: 16pt"><b>GIẤY GIỚI THIỆU</b></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td colspan="1" style="font-size: 12pt">Kính gửi:</td>
        <td colspan="5" style="font-size: 12pt">{{ $data['name_customer'] ?? '' }}</td>
    </tr>
    <tr>
        <td colspan="6" style="font-size: 12pt">Giới thiệu ông/ bà : {{ $data['name'] ?? '' }}</td>
    </tr>
    <tr>
        <td colspan="1" style="font-size: 12pt">Chức vụ:</td>
        <td colspan="5" style="font-size: 12pt">{{ $data['position'] ?? '' }}</td>
    </tr>
    <tr>
        <td style="font-size: 12pt">CMT số:</td>
        <td style="font-size: 12pt">&nbsp;{{ $data['cmt'] ?? '' }}</td>
        <td style="font-size: 12pt">Ngày cấp:</td>
        <td style="font-size: 12pt">{{ !empty($data['date_supply']) ? \Carbon\Carbon::parse($data['date_supply'])->format('d/m/Y') : '' }}</td>
        <td style="font-size: 12pt">Nơi cấp:</td>
        <td style="font-size: 12pt">{{ $data['local_supply'] ?? '' }}</td>
    </tr>
    <tr>
        <td style="font-size: 12pt">Được cử đến: {{ $data['address'] ?? '' }}</td>
    </tr>
    <tr>
        <td colspan="1" style="font-size: 12pt">Về việc:</td>
    </tr>
    <tr>
        <td colspan="6" style="font-size: 12pt">Đề nghị Quý cơ quan tạo điều kiện để ông (bà) có tên ở trên hoàn thành nhiệm vụ.</td>
    </tr>
    <tr>
        <td colspan="6" style="font-size: 12pt">Giấy này có giá trị đến hết {{ !empty(formatStringDate($data['date_effect'], 'ngaythangnam')) ? formatStringDate($data['date_effect'], 'ngaythangnam') : 'ngày ... tháng ... năm ...' }}</td>
    </tr>
    <tr>
        <td colspan="1" style="font-size: 12pt">Nơi nhận:</td>
        <td colspan="5" style="font-size: 12pt"></td>
    </tr>
    <tr>
        <td colspan="1" style="font-size: 12pt">Như trên</td>
        <td colspan="3"></td>
        <td colspan="2" align="center"><b>{{ $data['object_name'] ?? '' }}</b></td>
    </tr>
    <tr>
        <td style="font-size: 12pt">Lưu TV</td>
    </tr>

    </tbody>
</table>
</body>
</html>