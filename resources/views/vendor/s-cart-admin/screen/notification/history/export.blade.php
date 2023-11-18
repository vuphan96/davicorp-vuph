<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lịch sử thông báo</title>
</head>

<body>
<table>
    <thead>
    <tr>
        <td colspan="6" align="left"
            style="font-size: 12px;">Công ty cổ phần Davicorp Việt nam
        </td>
    </tr>
    <tr>
        <td colspan="6" align="left"
            style="font-size: 12px;">Số 34B, Lô 2, Đền Lừ 1, Hoàng Văn Thụ, Hoàng Mai, Hà Nội
        </td>
    </tr>
    <tr>
        <th colspan="6" align="center"
            style="font-size: 14px;">DANH SÁCH THÔNG BÁO
        </th>
    </tr>
    <tr >
        <th colspan="6" align="center" style="border-bottom: 1px solid black; border-right: 1px solid #E0E0E0">Từ
            ngày {{  $date['start'] ?? '--' }} Đến ngày {{ $date['end'] ?? '--' }}</th>
    </tr>
    <tr></tr>
    <tr style="width: 100%" class="heading-report">
        <th align="center" style="border: 1px solid black;font-weight: bold">STT</th>
        <th align="center" style="border: 1px solid black;font-weight: bold">Thời gian gửi</th>
        <th align="center" style="border: 1px solid black;font-weight: bold">Tiêu đề</th>
        <th align="center" style="border: 1px solid black;font-weight: bold">Mã đơn hàng</th>
        <th align="center" style="border: 1px solid black;font-weight: bold">Khách hàng</th>
        <th align="center" style="border: 1px solid black;font-weight: bold">Nội dung</th>
    </tr>

    </thead>
    <tbody>

    @foreach ($data as $key => $datum)
        <tr>
            <td align="center" style="border-right: 1px solid black;">{{ $key+1 }}</td>
            <td align="left" style="border-right: 1px solid black;">
                {{ \Carbon\Carbon::parse($datum->created_at ?? '')->format('d/m/Y - H:i:s') }}
            </td>
            <td align="left" style="border-right: 1px solid black;">{{ $datum->title }}</td>
            <td align="left" style="border-right: 1px solid black;">{{ $datum->order_code }}</td>
            <td align="left" style="border-right: 1px solid black;">{{ $datum->customer_name }}</td>
            @if($datum->edit_type == 5)
                <td align="left" style="border-right: 1px solid black;">
                    @foreach(json_decode($datum->content) as $key => $item)
                        <span>{{ $key }}</span><br>
                        @foreach($item as $value)
                            <span>&nbsp;&nbsp; - {{ $value }}</span><br>
                        @endforeach
                    @endforeach
                </td>
            @else
                <td align="left" style="border-right: 1px solid black; color: {{ strlen(strstr($datum->content, 'Admin')) > 0 ? '' : '#3C8DBC' }};">
                    {{ $datum->content }}
                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>