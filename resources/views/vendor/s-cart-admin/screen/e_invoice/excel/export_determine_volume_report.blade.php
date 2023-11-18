<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>XacDinhKhoiLuong</title>
</head>

<body style="font-family: Times New Roman, Serif">
<table>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th colspan="2" align="center" style="font-weight: bold; font-size: 12pt;">Mẫu số 08a</th>
    </tr>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th colspan="2" align="center" style="font-size: 12pt">Mã hiệu: {{ $data['brand_code'] ?? '....' }}</th>
    </tr>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th colspan="2" align="center" style="font-size: 12pt">Số: {{ $data['number'] ?? '....' }}</th>
    </tr>
    <tr>
        <th colspan="6" align="center" style="font-weight: bold; font-size: 14pt"><h5>BẢNG XÁC ĐỊNH GIÁ TRỊ KHỐI LƯỢNG CÔNG VIỆC HOÀN THÀNH</h5></th>
    </tr>
    <tr>
        <th colspan="4" style="font-size: 12pt">1. Đơn vị sử dụng ngân sách: {{ $data['units_use'] ?? '....' }}</th>
    </tr>
    <tr>
        <th colspan="2" style="font-size: 12pt">2. Mã đơn {{ $data['units_code'] ?? '....' }}    Mã nguồn: {{ $data['source_code'] ?? '....' }}</th>
    </tr>
    <tr>
        <th colspan="5" style="font-size: 12pt">3. Mã CTMTQG, Dự án ODA {{ $data['project_code'] ?? '................' }}</th>
    </tr>
    <tr>
        <th colspan="6" rowspan="2" style=" font-size: 12pt">4. Căn cứ Hợp đồng số: {{ $data['number_contract'] ?? '....' }} , ký {{ formatStringDate($data['date_acceptance'], 'ngaythangnam') ?? ' ... tháng ... năm ...' }} giữa {{ $data['objecta'] ?? '....' }} với {{ $data['objectb'] ?? '....' }} hai bên tiến hành xác định giá trị khối lượng hàng hóa như sau:</th>
    </tr>
    <tr>
        <th></th>
        <th></th>
    </tr>
    <tr>
        <th colspan="6" rowspan="2" style=" font-size: 12pt">5. Căn cứ Biên bản nghiệm thu {{ formatStringDate($data['report_acceptance'], 'ngaythangnam') ?? 'ngày ... tháng ... năm ...' }} giữa trường {{ $data['object_start'] ?? '....' }} với {{ $data['object_end'] ?? '....' }}</th>
    </tr>
    <tr>
        <th></th>
        <th></th>
    </tr>
    <tr>
        <th colspan="6"style=" text-align: right; font-size: 12pt">Đơn vị: Đồng</th>
    </tr>
    <thead>
    <tr>
        <th align="center" style="font-weight: bold; border: 1px solid black; font-size: 12pt">STT</th>
        <th style="font-weight: bold; border: 1px solid black; font-size: 12pt">Tên mặt hàng</th>
        <th style="font-weight: bold; border: 1px solid black; font-size: 12pt">Đvt</th>
        <th style="font-weight: bold; border: 1px solid black; font-size: 12pt">Số lượng</th>
        <th style="font-weight: bold; border: 1px solid black; font-size: 12pt">Giá Bán</th>
        <th style="font-weight: bold; border: 1px solid black; font-size: 12pt">Doanh thu</th>
    </tr>
    </thead>
    <tbody>
    @php
        $sumPrice = 0;
    @endphp
    @forelse($objInvoiceDetails as $key => $objInvoiceDetail)
        <tr>
            <td align="center" style="border: 1px solid black; font-size: 12pt">{{ $key + 1 ?? ''}}</td>
            <td style="border: 1px solid black; font-size: 12pt">{{ $objInvoiceDetail['product_name'] ?? ''}}</td>
            <td style="border: 1px solid black; font-size: 12pt">{{ $objInvoiceDetail['unit'] ?? ''}}</td>
            <td style="border: 1px solid black; font-size: 12pt">{{ $objInvoiceDetail['qty'] ?? ''}}</td>
            <td align="right" style="border: 1px solid black; font-size: 12pt">{{ number_format((int)$objInvoiceDetail['price']) ?? ''}}</td>
            <td align="right" style="border: 1px solid black; font-size: 12pt">{{ number_format((int)$objInvoiceDetail['total_price']) ?? ''}}</td>
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
        <td style="font-weight: bold; border: 1px solid black; font-size: 12pt"> Tiền hàng</td>
        <td colspan="4"  style="border: 1px solid black"></td>
        <td align="right" style="font-weight: bold;border: 1px solid black; font-size: 12pt">{{ number_format((int)$sumPrice) ?? '....'}}</td>
    </tr>
    <tr>
        <td style="font-weight: bold; border: 1px solid black; font-size: 12pt">Tổng cộng</td>
        <td colspan="4"  style="border: 1px solid black"></td>
        <td align="right" style="font-weight: bold; border: 1px solid black; font-size: 12pt">{{ number_format((int)$sumPrice) ?? '....'}}</td>
    </tr>
    <tr>
        <td style="font-weight: bold; border: 1px solid black; font-size: 12pt">Bằng chữ</td>
        <td colspan="5"  style="font-weight: bold; border: 1px solid black; font-size: 12pt">{{ convert_number_to_words($sumPrice) }} đồng</td>
    </tr>
    <tr><td></td></tr>
    <tr>
        <td colspan="4" style="font-size: 12pt">6. Lũy kế thanh toán khối lượng hoàn thành đến cuối kỳ trước:</td>
        <td align="right" colspan="1" style="font-size: 12pt"><strong>{{ number_format((int)$data['volume_finished']) ?? '....' }}</strong></td>
        <td colspan="1" style="font-size: 12pt">đồng </td>
    </tr>
    <tr>
        <td colspan="4" style="font-size: 12pt">- Thanh toán tạm ứng:</td>
        <td align="right" colspan="1" style="font-size: 12pt"><strong>{{ number_format((int)$data['pay_advance']) ?? '....' }}</strong></td>
        <td colspan="1" style="font-size: 12pt">đồng </td>
    </tr>
    <tr>
        <td colspan="4" style="font-size: 12pt">7. Số dư tạm ứng đến cuối kỳ trước: </td>
        <td align="right" colspan="1" style="font-size: 12pt"><strong>{{ number_format((int)$data['surplus']) ?? '....' }}</strong></td>
        <td colspan="1" style="font-size: 12pt">đồng </td>
    </tr>
    <tr>
        <td colspan="4" style="font-size: 12pt">8. Số đề nghị thanh toán kỳ này : </td>
        <td align="right" colspan="1" style="font-size: 12pt"><strong>{{ number_format((int)$sumPrice) ?? '....'}}</strong></td>
        <td colspan="1" style="font-size: 12pt">đồng </td>
    </tr>
    <tr>
        <td colspan="5" style="font-size: 12pt">- Thanh toán tạm ứng: {{ number_format((int)$data['pay_advance_request']) ?? '....' }} - Thanh toán chuyển khoản: {{ number_format((int)$data['pay_transfer']) ?? '....' }} đồng</td>
    </tr>
    <tr>
        <td colspan="1"></td>
        <td colspan="5" style="text-align: right; font-size: 12pt"><i>{{ formatStringDate($data['date'], 'Ngaythangnam') ?? 'Ngày ... tháng ... năm ...' }}</i></td>
    </tr>
    <tr>
        <td valign="top" colspan="2" rowspan="2" style="text-align: center; font-size: 12pt; font-weight: bold">ĐẠI DIỆN NHÀ CUNG CẤP HÀNG HÓA, DỊCH VỤ</td>
        <td valign="top" colspan="4" rowspan="2" style="text-align: center; font-size: 12pt; font-weight: bold">ĐẠI DIỆN ĐƠN VỊ SỬ DỤNG NGÂN SÁCH</td>
    </tr>
    <tr>
        <td colspan="2" style="border: none; font-size: 12pt"></td>
        <td colspan="4" style="border: none; font-size: 12pt"></td>
    </tr>
    <tr>
        <td valign="center" colspan="2" rowspan="4" style="text-align: center; font-size: 12pt"><i>(Ký, ghi rõ họ tên và đóng dấu)</i></td>
        <td valign="center" colspan="4" rowspan="4" style="text-align: center; font-size: 12pt"><i>(Ký, ghi rõ họ tên và đóng dấu)</i></td>
    </tr>
    <tr>
        <td colspan="2" style="border: none; font-size: 12pt"></td>
        <td colspan="4" style="border: none; font-size: 12pt"></td>
    </tr>
    </tbody>
</table>
</body>
</html>