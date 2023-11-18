<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="content-security-policy" content="require-trusted-types-for 'script';">
    <title>In tem</title>
    <style type="text/css">
        * {
            font-size: 10px;
            font-family: Arial;
        }

        @page {
            size: A4;
            margin: 0;
        }

        .i-row {
            /*width: 186mm;*/
            /*max-width: 186mm;*/
            display: flex;
        }

        @media print {

            .hidden-print,
            .hidden-print * {
                display: none !important;
            }

            .pagebreak { page-break-before: always; }
        }

        .i-tem {
            width: 52mm;
            max-width: 52mm;
            height: 28mm;
            max-height: 28mm;
            margin-bottom: 2.5mm;
            /*border: 1px solid rgba(121, 119, 119, 0.5);*/
            border-radius: 4mm;
        }

        .i-tem-1 {
            margin-left: 22mm;
        }

        .i-tem-1,
        .i-tem-2 {
            margin-right: 2mm;
        }

        .i-tem-header {
            border-bottom: 1px solid;
            font-size: 12px;
            text-align: center;
            font-weight: bold;
            height: 8.5mm;
        }

        .i-tem-body {
            height: 20mm;
        }

        .i-tem-body-left {
            width: 32mm;
            max-width: 32mm;
        }

        .i-tem-body-left div {
            font-size: 16px;
            text-align: center;
            font-weight: bold;
        }

        .i-tem-body-right {
            width: 20mm;
            max-width: 20mm;
            text-align: center;
        }
        .i-tem-body-rights {
            width: 10mm;
            max-width: 10mm;
            text-align: center;
        }


        .i-tem-body-right img {
            max-width: 16mm;
            max-height: 16mm;
        }

        .i-tem-footer {
            font-size: 12px;
            text-align: center;
            font-weight: bold;
            border-top: 1px solid;
            height: 9mm;
        }

        .i-tem-footer-left {
            font-size: 21px;
            border-right: 1px solid;
            width: 15mm;
            max-width: 15mm;
        }

        .i-tem-expire {
            font-size: 9px;
            font-weight: 400;
            font-style: italic;
            text-align: center;
        }

        .pagebreak {
            padding-top: 5mm;
        }

        .firstrow {
            padding-top: 3mm;
        }
    </style>
</head>
<body>
@php
    $itemNums = $data ? count($data) : 0;
@endphp

@for ($i = 0; $i < $itemNums; $i += 3)
    @php
        $rowClass = 'i-row';
        if ($i == 0 ) { $rowClass = $rowClass . ' firstrow'; }
        if ($i % 5 == 0 && $i > 0) { $rowClass = $rowClass . ' pagebreak'; }
    @endphp

    <div class="{{$rowClass}}">
        @php
            $items = array($data[$i]);
            if (($i + 1) < $itemNums){
                array_push($items, $data[$i + 1]);
            }

            if (($i + 2) < $itemNums){
                array_push($items, $data[$i + 2]);
            }

            $j = 1;
        @endphp
        @foreach ($items as $item)
            <table class="i-tem i-tem-{{ $j++ }}" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td colspan="2" class="i-tem-header">{{ $item['short_name'] ?? '' }}</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table cellspacing="0" cellpadding="0">
                            <tbody>
                            <tr>
                                <td class="i-tem-body i-tem-body-left">
                                    @php
                                        $productName = $item['product_name'] ?? '';
                                        $fontSize = strlen($productName) > 22 ? '12px' : '14px'
                                    @endphp
                                    <div style="font-size: {{ $fontSize }}; padding-bottom: 4px" >{{ $productName }}</div>
                                    <div style="padding-top: 2px; font-size: 12px">{{ number_format($item['qty'] ?? 0, 2) }}</div>
                                    <div style="padding-top: 2px; font-size: 12px">{{ $item['customer_name'] ?? '' }} {{ (($item['object_id'] ?? "")  == 1) ? " - GV" : "" }}</div>
                                </td>
                                <td class="i-tem-body i-tem-body-right">
                                    <div>
                                        @php $qr_item = $qr->where("url", $item["qr_code"] ?? "")->first() @endphp
                                        <img style="padding-top: 2px" src="{!! $qr_item ? $qr_item["image"] : "" !!}">
                                        <div style="font-size: 10px; padding-bottom: 2px;" >NSX : {{ (new DateTime($item['delivery_time'] ?? ''))->format('d.m.y') }}</div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>

                </tr>
                <tr>
                    <td class="i-tem-footer i-tem-body-left">
                        @php
                            $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
                            if(!empty($item['id_barcode'])){
                                echo '<img  class="i-tem-footer i-tem-body-left" style="margin-top: 5px;" width="100px" height="30px" src="data:image/png;base64,' . base64_encode($generator->getBarcode($item['id_barcode'], $generator::TYPE_CODE_128)) . '">';
                            }
                        @endphp
                    </td>
                    <td class="i-tem-footer i-tem-body-rights">{{ $item['customer_num'] ?? '0' }}</td>
                </tr>
                </tbody>
            </table>
        @endforeach
    </div>
@endfor
</body>
</html>