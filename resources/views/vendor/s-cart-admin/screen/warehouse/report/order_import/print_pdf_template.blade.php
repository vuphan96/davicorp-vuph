
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BaoCaoNhapHang</title>
    <style>
        * {
            margin: 5px;
            padding: 0;
            box-sizing: border-box;
            font-family: "Times New Roman", Serif;
            line-height: 0.9;
        }
        body {
            height: 100%;
            margin: 20px auto;
            padding: 0;
        }
        @page {
            size: A4;
            margin: 1.2cm;
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
            font-size: 13pt;
            text-align: center;
            padding: 5px 5px;
        }

        table.table_detail td {
            border: 1px solid black;
            font-size: 12pt;
            padding: 5px 5px 5px 5px;
        }
    </style>
</head>
<body>
<div id="invoice" class="webview_hide">
    <table>
        <tr>
            <th colspan="8"></th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: left"> {{ sc_language_render("admin.report.name_cty") }}
            </th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: left">{{ sc_language_render("admin.report.address_cty") }}
            </th>
        </tr>
        <tr>
            <th colspan="8"></th>
        </tr>
        <tr>
            <th colspan="8"></th>
        </tr>
        <tr>
            <th colspan="8" class="invoice_title">BÁO CÁO ĐƠN NHẬP HÀNG</th>
        </tr>
        <tr>
            <th colspan="8" class="invoice_time">Từ {{  $dataSearch['date_start'] }} đến {{ $dataSearch['date_end'] }}</th>
        </tr>
        <tr>
            <th colspan="8" align="center">Kho: -</th>
        </tr>
        <tr>
            <th colspan="8" align="center"></th>
        </tr>
    </table>
    <br><br>
    <table style="border-collapse: collapse" border="1px" class="table_detail">
        <thead>
        <tr>
            <th style="width: 30px">Stt</th>
            <th style="width: 65px">Mã tương ứng</th>
            <th >Tên</th>
            <th >Thuộc kho</th>
            <th >Số lượng</th>
            <th >Giá nhập</th>
            <th >Thành tiền</th>
            <th >Ghi chú</th>
        </tr>
        </thead>
        <tbody>
        @forelse($data->groupBy(['supplier','product_id']) as $keySupplier => $supplier)
            <tr>
                <td></td>
                <td></td>
                <td  style="font-weight: bold; text-transform: uppercase">{{ $supplier->first()->first()->supplier_name ?? '' }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @foreach($supplier as $keyProduct => $product)
                <tr>
                    <td></td>
                    <td style="font-weight: bold; text-transform: uppercase">{{$product->first()->product_code ?? '' }}</td>
                    <td style="font-weight: bold; text-transform: uppercase">{{ $product->first()->product_name ?? '' }}</td>
                    <td></td>
                    <td style="font-weight: bold; text-transform: uppercase;text-align: right">{{ number_format($product->sum('qty_reality'), 2) }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @foreach($product as $key => $item)
                    <tr>
                        <td align="center">{{ $key+1 }}</td>
                        <td>{{ $item->customer_code ?? '' }}</td>
                        <td>{{ $item->customer_name == '' ? "Hàng xuất từ kho" : $item->customer_name }}</td>
                        <td>{{ $item->warehouse_name }}</td>
                        <td style="text-align: right">{{ number_format($item->qty_reality, 2) }}</td>
                        <td style="text-align: right">{{ number_format($item->product_price) }}</td>
                        <td style="text-align: right">{{ number_format($item->amount_reality) }}</td>
                        <td>{{ $item->comment }}</td>
                    </tr>
                @endforeach
            @endforeach
        @empty
            <td colspan="8">Không có nội dung!</td>
        @endforelse
        <tr>
            <td colspan="4" style="font-weight: bold; text-align: left">Tổng cộng</td>
            <td style="font-weight: bold;  text-align: right">{{ number_format($count, 2) }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        </tbody>
    </table>
    <table>
        <tr>
            <th colspan="8" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: right; border: 1px solid white; border-right: 1px solid white; padding-right: 50px">Ngày ...... Tháng ...... năm ..........</th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: right; font-weight: bold; border: 1px solid white; border-right: 1px solid white; padding-right: 120px">Người Lập</th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: right; border: 1px solid white; border-right: 1px solid white; font-style: italic; font-size: 10px; padding-right: 130px">(Ký, họ tên)</th>
        </tr>
        <tr>
            <th colspan="8" align="center"></th>
        </tr>
        <tr>
            <th colspan="8" align="center"></th>
        </tr>
        <tr>
            <th colspan="8" align="center"></th>
        </tr>
    </table>
</div>
</body>
</html>