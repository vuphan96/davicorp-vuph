<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BIÊN BẢN NGHIỆM THU</title>
</head>

<body>
<table>
    <tr>
        <th colspan="6" style="text-align: center;font-weight: bold">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</th>
    </tr>
    <tr>
        <th colspan="6" style="text-align: center; font-weight: bold">Độc Lập - Tự Do - Hạnh Phúc</th>
    </tr>
    <tr>
        <th colspan="6" style="text-align: center">......o0o......</th>
    </tr>
    <tr>
        <th colspan="6" style="font-weight: bold; text-align: center"><h5>BIÊN BẢN NGHIỆM THU </h5></th>
    </tr>
    <tr>
        <td colspan="6" align="center" style="font-weight: bold">{{ formatStringDate($attributes['einvoice_date'], 'Ngaythangnam') ?? 'Ngày ..... tháng ..... năm .....' }}</td>
    </tr>
    <tr>
        <th colspan="6" style="font-weight: bold">Căn cứ hợp đồng cung cấp thực phẩm Số : &nbsp; {{ $attributes['units_use'] ?? '......' }} &nbsp; ngày &nbsp; {{ $attributes['units_date'] ?? '......' }} &nbsp; giữa &nbsp; {{ $attributes['units_code'] ?? '......' }}
            &nbsp; Và &nbsp; {{ $attributes['source_code'] ?? '......' }}</th>
    </tr>
    <tr>
        <td colspan="6">Theo nhu cầu và thỏa thuận giữa hai bên.</td>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr>
    <tr>
        <td colspan="6">Hôm nay, {{ formatStringDate($attributes['date'], 'ngaythangnam') ?? 'ngày ..... tháng ..... năm .....' }} , Chúng tôi gồm:</td>
    </tr>
    <tr>
        <th colspan="6" style="font-weight: bold;"><h5>1. ĐƠN VỊ SẢN XUẤT, SƠ CHẾ VÀ ĐÓNG GÓI:	</h5></th>
    </tr>
    <tr>
        <th colspan="6" style="font-weight: bold;"><h5>CÔNG TY CỔ PHẦN DAVICORP VIỆT NAM</h5></th>
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
        <th colspan="6" style="font-weight: bold;"><h5>2. ĐƠN VỊ PHÂN PHỐI BÁN HÀNG TRỰC TIẾP: </h5></th>
    </tr>
    <tr>
        <th colspan="6" style="font-weight: bold;"><h5>CỬA HÀNG THỰC PHẨM SẠCH DAVICORP</h5></th>
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
        <th colspan="6" style="font-weight: bold;"><h5>BÊN B. {{ mb_strtoupper($data['customer']->name, 'UTF-8') }}</h5></th>
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
        <th colspan="4" style="font-weight: bold">Người đại diện : {{ $attributes['representative_name'] ?? '' }}</th>
        <th colspan="2" style="font-weight: bold">Chức vụ : {{ $attributes['position'] ?? '' }}</th>
    </tr>

    <tr>
        <td colspan="6">Hai bên thống nhất ký biên bản nghiệm thu với những nội dung sau:</td>
    </tr>
    <tr>
        <td colspan="6">Nội dung công việc đã thực hiện: Bên A đã hoàn thành cung cấp thực phẩm cho bên B trong tháng {{ $attributes['month_contract'] ?? '' }}
            năm {{ $attributes['month_contract'] ?? '.........' }}  đúng như trong hợp đồng số {{ $attributes['number_contract'] ?? '' }}
            ký {{ formatStringDate($attributes['date_contract'], 'ngaythangnam') ?? '...........' }} </td>
    </tr>
    <tr>
        <td colspan="6">theo bảng tổng hợp số lượng và giá trị hàng hóa từ
            {{ !empty($attributes['start_date_effective_contract']) ? \Carbon\Carbon::parse($attributes['start_date_effective_contract'])->format('d/m/Y') : '' }}
            đến {{ !empty($attributes['end_date_effective_contract']) ? \Carbon\Carbon::parse($attributes['end_date_effective_contract'])->format('d/m/Y') : '' }}
            như sau:</td>
    </tr>
    <tr>
        <td colspan="6" style="font-style: italic; text-align: right">Đơn vị: Đồng</td>
    </tr>
    <thead>
    <tr>
        <th style="border: 1px solid black; font-weight: bold; text-align: center">STT</th>
        <th style="border: 1px solid black; font-weight: bold; text-align: center">Tên mặt hàng</th>
        <th style="border: 1px solid black; font-weight: bold; text-align: center">Đvt</th>
        <th style="border: 1px solid black; font-weight: bold; text-align: center">Số lượng</th>
        <th style="border: 1px solid black; font-weight: bold; text-align: center">Giá Bán</th>
        <th style="border: 1px solid black; font-weight: bold; text-align: center">Doanh thu</th>
    </tr>
    </thead>
    <tbody>
    @php
        $sumPrice = 0;
    @endphp
    @forelse($data['einvoice'] as $key => $value)
        <tr>
            <td style="border: 1px solid black" align="center">{{ $key + 1 ?? ''}}</td>
            <td style="border: 1px solid black">{{ $value['product_name'] ?? ''}}</td>
            <td style="border: 1px solid black; text-align: left">{{ $value['unit'] ?? ''}}</td>
            <td style="border: 1px solid black; padding-right: 8px">{{ $value['qty'] ?? ''}}</td>
            <td style="border: 1px solid black; text-align: right; padding-right: 8px">{{ number_format($value['price'] ?? 0)}}</td>
            <td style="border: 1px solid black; text-align: right;padding-right: 8px">{{ number_format($value['total_price'] ?? 0)}}</td>
        </tr>
        @php
            $sumPrice += $value['total_price'];
        @endphp
    @empty
        <tr>
            <td style="border: 1px solid black">Chưa có dữ liệu!</td>
        </tr>
    @endforelse
    <tr>
        <th style="border: 1px solid black; font-weight: bold; text-align: center"> Tiền hàng</th>
        <th colspan="4"  style="border: 1px solid black"></th>
        <th style="border: 1px solid black; text-align: right;padding-right: 8px">{{ number_format($sumPrice ?? 0) }}</th>
    </tr>
    <tr>
        <th style="border: 1px solid black; font-weight: bold; text-align: center">Tổng cộng</th>
        <th colspan="4"  style="border: 1px solid black"></th>
        <th style="border: 1px solid black; text-align: right;padding-right: 8px">{{ number_format($sumPrice ?? 0) }}</th>
    </tr>
    <tr>
        <th style="border: 1px solid black; font-weight: bold; text-align: center">Bằng chữ</th>
        <th colspan="5"  style="border: 1px solid black; font-weight: bold; text-align: center">{{ convert_number_to_words($sumPrice) }} đồng</th>
    </tr>
    <tr><td></td></tr>
    <tr>
        <td colspan="6">Bên A đã hoàn thành việc cung cấp cho bên B hàng hoá các loại theo đúng quy cách chủng loại và số lượng như trên. Bên B đồng ý nghiệm thu.
        </td>
    </tr>
    <tr>
        <td colspan="6" style="font-weight: bold;">Tổng giá trị nghiệm thu : &nbsp; {{ number_format($sumPrice ?? 0) }} đồng ( Bằng chữ : &nbsp; {{ convert_number_to_words($sumPrice) }} đồng ) </td>
    </tr>
    <tr>
        <td colspan="6" style="font-weight: bold;"> Số tiền bên B phải thanh toán cho bên A là : &nbsp; {{ number_format($sumPrice ?? 0) }} đồng ( Bằng chữ : &nbsp;  {{ convert_number_to_words($sumPrice) }} đồng )	</td>
    </tr>
    <tr>
        <td colspan="6"> Hai bên thống nhất nghiệm thu với nội dung nêu trên.</td>
    </tr>
    <tr>
        <td colspan="6">Biên bản nghiệm thu được lập thành 02 bản có giá trị pháp lý như nhau, bên A giữ 1 bản, bên B giữ 1 bản làm căn cứ thanh toán.</td>
    </tr>
    <tr>
        <td colspan="2" rowspan="2" style="text-align: center; font-weight: bold">ĐẠI DIỆN BÊN A</td>
        <td colspan="4" rowspan="2" style="text-align: center; font-weight: bold">ĐẠI DIỆN BÊN B</td>
    </tr>
    <tr>
        <td colspan="2" style="border: none"></td>
        <td colspan="4" style="border: none"></td>
    </tr>
    <tr>
        <td colspan="2" rowspan="1" style="text-align: center">(Ký, ghi rõ họ tên và đóng dấu)</td>
        <td colspan="4" rowspan="1" style="text-align: center">(Ký, ghi rõ họ tên và đóng dấu)</td>
    </tr>
    <tr>
        <td colspan="2" style="border: none"></td>
        <td colspan="4" style="border: none"></td>
    </tr>
    </tbody>
</table>
</body>
</html>
