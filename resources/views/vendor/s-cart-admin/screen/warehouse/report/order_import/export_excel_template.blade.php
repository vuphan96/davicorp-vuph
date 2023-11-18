<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BaoCaoNhapHang</title>
</head>

<body>
<table>
    <thead>
    <tr>
        <th colspan="8" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="8" style="border: 1px solid white; border-right: 1px solid #E0E0E0">{{ sc_language_render("admin.report.name_cty") }}
        </th>
    </tr>
    <tr>
        <th colspan="8" style="border: 1px solid white; border-right: 1px solid #E0E0E0">{{ sc_language_render("admin.report.address_cty") }}
        </th>
    </tr>
    <tr>
        <th colspan="8" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="8" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="8" align="center"
            style="font-size: 16px; font-weight: 700; border: 1px solid white; border-right: 1px solid #E0E0E0">BÁO CÁO ĐƠN NHẬP HÀNG
        </th>
    </tr>
    <tr>
        <th colspan="8" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0">Từ
            ngày {{  $dataSearch['date_start'] }} đến ngày {{ $dataSearch['date_end'] }}</th>
    </tr>
    <tr>
        <th colspan="8" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0">Kho: -</th>
    </tr>
    <tr>
        <th colspan="8" align="center" style="border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th align="center" >Stt</th>
        <th align="center" >Mã tương ứng</th>
        <th align="center" >Tên</th>
        <th align="center" >Thuộc kho</th>
        <th align="center" >Số lượng</th>
        <th align="center" >Giá nhập</th>
        <th align="center" >Thành tiền</th>
        <th align="center" >Ghi chú</th>
    </tr>
    </thead>
    <tbody>
    @forelse($data->groupBy(['supplier','product_id']) as $keySupplier => $supplier)
        <tr>
            <td></td>
            <td></td>
            <td  style="font-weight: 800;">{{ mb_strtoupper($supplier->first()->first()->supplier_name ?? '', 'UTF-8') }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @foreach($supplier as $keyProduct => $product)
            <tr>
                <td></td>
                <td style="font-weight: 800;">{{ mb_strtoupper($product->first()->product_code ?? '', 'UTF-8') }}</td>
                <td style="font-weight: 800;">{{ mb_strtoupper($product->first()->product_name ?? '', 'UTF-8') }}</td>
                <td></td>
                <td style="font-weight: 800;">{{ number_format($product->sum('qty_reality'), 2) }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @foreach($product as $key => $item)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $item->customer_code ?? '' }}</td>
                    <td>{{ $item->customer_name == '' ? "Hàng xuất từ kho" : $item->customer_name }}</td>
                    <td>{{ $item->warehouse_name }}</td>
                    <td>{{ $item->qty_reality }}</td>
                    <td>{{ $item->product_price }}</td>
                    <td>{{ $item->amount_reality }}</td>
                    <td>{{ $item->comment }}</td>
                </tr>
            @endforeach
        @endforeach
    @empty
        <td colspan="8">Không có nội dung!</td>
    @endforelse
    </tbody>
    <tr>
        <th colspan="4" style="font-weight: bold">Tổng cộng</th>
        <th colspan="1" style="text-align: right; font-weight: bold">{{ number_format($count, 2) }}</th>
        <th colspan="3" style=""></th>

    </tr>
    <tr>
        <th colspan="8" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="6" style="text-align: right; border: 1px solid white; border-right: 1px solid white">Ngày ...... Tháng ...... năm ..........</th>
        <th colspan="2"></th>
    </tr>
    <tr>
        <th colspan="6" style="text-align: right; font-weight: bold; border: 1px solid white; border-right: 1px solid white">Người Lập</th>
        <th colspan="2" style="text-align: right; border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="6" style="text-align: right; border: 1px solid white; border-right: 1px solid white; font-style: italic; font-size: 10px">(Ký, họ tên)</th>
        <th colspan="2" style="text-align: right; border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="8" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="8" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
    </tr>
    <tr>
        <th colspan="8" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0; border-bottom: 1px solid #E0E0E0"></th>
    </tr>
</table>
</body>
</html>