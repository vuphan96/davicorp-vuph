
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BaoCaoNhapXuatTon</title>
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
            <th colspan="11"></th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: left"> {{ sc_language_render("admin.report.name_cty") }}
            </th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: left">{{ sc_language_render("admin.report.address_cty") }}
            </th>
        </tr>
        <tr>
            <th colspan="11"></th>
        </tr>
        <tr>
            <th colspan="11"></th>
        </tr>
        <tr>
            <th colspan="11" class="invoice_title">BÁO CÁO NHẬP XUẤT TỒN</th>
        </tr>
        <tr>
            <th colspan="11" class="invoice_time">Từ {{  $dataSearch['date_start'] }} đến {{ $dataSearch['date_end'] }}</th>
        </tr>
        <tr>
            <th colspan="11" align="center">Kho: -</th>
        </tr>
        <tr>
            <th colspan="11" align="center"></th>
        </tr>
    </table>
    <br><br>
    @php $i = 1; @endphp
    <table style="border-collapse: collapse" border="1px" class="table_detail">
        <thead>
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
    </table>
    <table>
        <tr>
            <th colspan="11" align="center" style="border: 1px solid white; border-right: 1px solid #E0E0E0"></th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: right; border: 1px solid white; border-right: 1px solid white; padding-right: 50px">Ngày ...... Tháng ...... năm ..........</th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: right; font-weight: bold; border: 1px solid white; border-right: 1px solid white; padding-right: 120px">Người Lập</th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: right; border: 1px solid white; border-right: 1px solid white; font-style: italic; font-size: 10px; padding-right: 130px">(Ký, họ tên)</th>
        </tr>
        <tr>
            <th colspan="11" align="center"></th>
        </tr>
        <tr>
            <th colspan="11" align="center"></th>
        </tr>
        <tr>
            <th colspan="11" align="center"></th>
        </tr>
    </table>
</div>
</body>
</html>