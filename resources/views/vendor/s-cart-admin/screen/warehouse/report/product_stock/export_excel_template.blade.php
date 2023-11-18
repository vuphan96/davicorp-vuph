<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BaoCaoNhapXuatTonKho</title>
</head>

<body>
<table>
    <thead>
    <tr>
        <th colspan="11" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="11" style="border: 1px solid white; border-right: 1px solid #E0E0E0">{{ sc_language_render("admin.report.name_cty") }}
        </th>
    </tr>
    <tr>
        <th colspan="11" style="border: 1px solid white; border-right: 1px solid #E0E0E0">{{ sc_language_render("admin.report.address_cty") }}
        </th>
    </tr>
    <tr>
        <th colspan="11" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="11" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="11" align="center"
            style="font-size: 16px; font-weight: 700; border: 1px solid white; border-right: 1px solid #E0E0E0">BÁO CÁO NHẬP - XUẤT - TỒN
        </th>
    </tr>
    <tr>
        <th colspan="11" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0">Từ
            ngày {{  $dataSearch['date_start'] }} đến ngày {{ $dataSearch['date_end'] }}</th>
    </tr>
    <tr>
        <th colspan="11" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0">Kho: </th>
    </tr>
    <tr>
        <th colspan="11" align="center" style="border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th align="center" >STT</th>
        <th align="center" >Mã sản phẩm</th>
        <th align="center" >Tên sản phẩm</th>
        <th align="center" >Tồn đầu kỳ</th>
        <th align="center" >Giá trị đầu kỳ</th>
        <th align="center" >SL nhập</th>
        <th align="center" >Giá trị nhập</th>
        <th align="center" >SL xuất</th>
        <th align="center" >Giá trị xuất</th>
        <th align="center" >Tồn cuối kỳ</th>
        <th align="center" >Giá trị cuối kỳ</th>
    </tr>
    </thead>
    <tbody>
    @php $i = 1; @endphp
    @forelse($data as $product => $datum)
        <tr>
            <td>{{ $i++ }}</td>
            <td>{{ $datum->first()->product_code }}</td>
            <td>{{ $datum->first()->product_name }}</td>
            <td>{{ $datum->first()->qty_stock }}</td>
            <td></td>
            <td>{{ $datum->sum('qty_import') }}</td>
            <td></td>
            <td>{{ $datum->sum('qty_export') }}</td>
            <td></td>
            <td>{{ $datum->last()->qty_stock }}</td>
            <td></td>
        </tr>
    @empty
        <td colspan="11">Không có nội dung!</td>
    @endforelse
    </tbody>
    <tr>
        <th colspan="11" align="center" style="border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="9" style="text-align: right; border: 1px solid white; border-right: 1px solid white">Ngày ...... Tháng ...... năm ..........</th>
        <th colspan="2" style="border: 1px solid white"></th>
    </tr>
    <tr>
        <th colspan="9" style="text-align: right; font-weight: bold; border: 1px solid white; border-right: 1px solid white">Người Lập</th>
        <th colspan="2" style="text-align: right; border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="9" style="text-align: right; border: 1px solid white; border-right: 1px solid white; font-style: italic; font-size: 10px">(Ký, họ tên)</th>
        <th colspan="2" style="text-align: right; border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="11" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="11" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="11" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0; border-bottom: 1px solid #E0E0E0"></th>
    </tr>
</table>
</body>
</html>