<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR — {{ $item->kode_barang }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #fff;
            font-family: Arial, Helvetica, sans-serif;
        }

        .sticker {
            width: 200px;
            height: 200px;
            border: 2px dashed #999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 10px;
        }

        .watermark {
            position: absolute;
            top: 8px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #ccc;
            letter-spacing: 1px;
            text-transform: uppercase;
            pointer-events: none;
        }

        .qr-code {
            margin-top: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .qr-code svg {
            width: 100px;
            height: 100px;
        }

        .item-name {
            margin-top: 8px;
            font-size: 11px;
            font-weight: bold;
            color: #333;
            text-align: center;
            line-height: 1.3;
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .item-code {
            margin-top: 3px;
            font-size: 13px;
            font-weight: bold;
            color: #000;
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: 1px;
        }

        @media print {
            body {
                min-height: auto;
            }

            .sticker {
                border: 2px dashed #999;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="sticker">
        <div class="watermark">PT MULTYARTHA INDO DAYA</div>

        <div class="qr-code">
            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)->generate($item->kode_barang) !!}
        </div>

        <div class="item-name" title="{{ $item->nama_barang }}">{{ $item->nama_barang }}</div>
        <div class="item-code">{{ $item->kode_barang }}</div>
    </div>

</body>
</html>
