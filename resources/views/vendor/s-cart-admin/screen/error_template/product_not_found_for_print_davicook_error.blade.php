<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Davicorp - In hoá đơn</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">
        @media print {
            table.table_detail th, table.table_detail td {
                border: 1pt solid black;
            }

            .webview_hide {
                display: block;
            }

            .print_note {
                display: none;
            }
        }

        html {
            margin: 0.6cm 0.96cm 0.6cm 0.96cm;
        }

        table {
            width: 100%;
            height: auto;
        }

        * {
            font-family: "Times New Roman", Serif;
            line-height: 0.9;
        }

        .company_title {
            line-height: 8px;
        }

    </style>
</head>
<body>

        <div id="invoice">
            <table>
                <tr>
                    <td class="company_title">
                        <b>{{ $error }}
                    </td>
                </tr>
            </table>
        </div>

</body>
</html>