<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti PO — {{ $po->nomor_po }}</title>
    <style>
        /* ── Reset & Page ─────────────────────────────────── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            margin: 12px 16px 10px 16px;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            color: #000;
            line-height: 1.35;
        }

        /* ── Kop Surat ────────────────────────────────────── */
        .kop {
            width: 100%;
            border-bottom: 1px dashed #000;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }
        .kop td { border: none; vertical-align: middle; padding: 0; }
        .kop-logo { width: 52px; }
        .kop-logo img { width: 46px; height: auto; }
        .kop-info {
            text-align: right;
            padding-right: 0;
        }
        .kop-info .company-name {
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 1px;
        }
        .kop-info p {
            font-size: 9px;
            color: #000;
            line-height: 1.3;
        }

        /* ── Meta & Title Row ─────────────────────────────── */
        .meta-title-row {
            width: 100%;
            margin-bottom: 6px;
        }
        .meta-title-row td { border: none; vertical-align: top; padding: 0; }

        .meta-table td {
            border: none;
            font-size: 10px;
            padding: 1px 0;
            vertical-align: top;
        }
        .meta-table .lbl { width: 88px; }
        .meta-table .sep { width: 10px; text-align: center; }

        .doc-title {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            padding-top: 2px;
        }

        /* ── Vendor ───────────────────────────────────────── */
        .vendor-section {
            margin-bottom: 6px;
            font-size: 10px;
        }
        .vendor-section .vendor-label { font-style: italic; margin-bottom: 1px; }
        .vendor-table td {
            border: none;
            font-size: 10px;
            padding: 1px 0;
            vertical-align: top;
        }
        .vendor-table .lbl { width: 100px; }
        .vendor-table .sep { width: 10px; text-align: center; }

        /* ── Tabel Item (Dot Matrix) ──────────────────────── */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        table.data th,
        table.data td {
            padding: 3px 5px;
            font-size: 10px;
            border: none;
        }
        table.data thead tr {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
        }
        table.data th {
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 9px;
        }
        table.data td.num { text-align: right; }
        table.data td.center { text-align: center; }

        /* ── Ringkasan ────────────────────────────────────── */
        .summary-row {
            width: 100%;
            border-top: 1px dashed #000;
            padding-top: 3px;
            margin-bottom: 2px;
        }
        .summary-row td { border: none; font-size: 10px; padding: 1px 0; }
        .summary-row .lbl-col { width: 65%; text-align: right; padding-right: 8px; }
        .summary-row .val-col { text-align: right; font-weight: bold; }
        .summary-row .grand-total td {
            font-size: 11px;
            font-weight: bold;
            border-top: 1px dashed #000;
            padding-top: 3px;
        }

        /* ── Terbilang ────────────────────────────────────── */
        .terbilang {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 3px 0;
            margin-bottom: 6px;
            font-size: 10px;
            font-style: italic;
        }

        /* ── Tanda Tangan ─────────────────────────────────── */
        .ttd-table { width: 100%; margin-top: 6px; }
        .ttd-table td {
            border: none;
            text-align: center;
            font-size: 10px;
            vertical-align: top;
            width: 50%;
        }
        .ttd-space { height: 42px; }
    </style>
</head>
<body>

    {{-- ═══ Kop Surat ═══ --}}
    <table class="kop">
        <tr>
            <td class="kop-logo">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo">
            </td>
            <td class="kop-info">
                <div class="company-name">PT MULTYARTHA INDO DAYA</div>
                <p>Jl. Surya Asri Residence 9A Cipagalo Bojongsoang Kab. Bandung</p>
                <p>Email : multyarthaindodaya@gmail.com</p>
            </td>
        </tr>
    </table>

    {{-- ═══ Meta Data (Kiri) + Judul (Kanan) ═══ --}}
    <table class="meta-title-row">
        <tr>
            <td style="width: 55%;">
                <table class="meta-table">
                    <tr>
                        <td class="lbl">No. PO</td>
                        <td class="sep">:</td>
                        <td><strong>{{ $po->nomor_po }}</strong></td>
                    </tr>
                    <tr>
                        <td class="lbl">Tanggal</td>
                        <td class="sep">:</td>
                        <td>{{ \Carbon\Carbon::parse($po->tanggal_po)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Tipe / Status</td>
                        <td class="sep">:</td>
                        <td>{{ $po->tipe_po }} / {{ $po->status_pesanan }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 45%;">
                <div class="doc-title">BUKTI PURCHASE ORDER</div>
            </td>
        </tr>
    </table>

    {{-- ═══ Data Vendor ═══ --}}
    <div class="vendor-section">
        <div class="vendor-label">Kepada Yth,</div>
        <table class="vendor-table">
            <tr>
                <td class="lbl">Nama Vendor</td>
                <td class="sep">:</td>
                <td>{{ $po->vendor->nama_vendor }}</td>
            </tr>
            <tr>
                <td class="lbl">Alamat Vendor</td>
                <td class="sep">:</td>
                <td>{{ $po->vendor->alamat ?? ($po->vendor->kontak ?? '-') }}</td>
            </tr>
        </table>
    </div>

    {{-- ═══ Tabel Detail Barang (Dot Matrix Style) ═══ --}}
    <table class="data">
        <thead>
            <tr>
                <th style="width: 26px;">No</th>
                <th style="text-align: left;">Nama Barang</th>
                <th style="width: 40px;">Qty</th>
                <th style="width: 80px;">Harga</th>
                <th style="width: 90px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($po->details as $detail)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $detail->item->nama_barang }}</td>
                    <td class="center">{{ number_format($detail->qty, 0, ',', '.') }}</td>
                    <td class="num">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                    <td class="num">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ═══ Ringkasan Harga ═══ --}}
    <table class="summary-row">
        <tr>
            <td class="lbl-col">Total Harga</td>
            <td class="val-col">Rp {{ number_format($po->total_harga, 0, ',', '.') }}</td>
        </tr>
        @if ($po->diskon > 0)
            <tr>
                <td class="lbl-col">Diskon</td>
                <td class="val-col">- Rp {{ number_format($po->diskon, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr class="grand-total">
            <td class="lbl-col">Grand Total</td>
            <td class="val-col">Rp {{ number_format($po->grand_total, 0, ',', '.') }}</td>
        </tr>
        @if ($po->status_pembayaran === 'DP' && $po->nominal_dp > 0)
            <tr>
                <td class="lbl-col">Nominal DP</td>
                <td class="val-col">Rp {{ number_format($po->nominal_dp, 0, ',', '.') }}</td>
            </tr>
        @endif
        @if ($po->sisa_pembayaran > 0)
            <tr>
                <td class="lbl-col">Sisa Tagihan</td>
                <td class="val-col">Rp {{ number_format($po->sisa_pembayaran, 0, ',', '.') }}</td>
            </tr>
        @endif
    </table>

    {{-- ═══ Terbilang ═══ --}}
    <div class="terbilang">
        Terbilang: <strong>{{ ucwords($terbilang ?? ('Rp ' . number_format($po->grand_total, 0, ',', '.'))) }} Rupiah</strong>
    </div>

    {{-- ═══ Tanda Tangan ═══ --}}
    <table class="ttd-table">
        <tr>
            <td>Diserahkan Oleh,</td>
            <td>Diterima Oleh,</td>
        </tr>
        <tr>
            <td class="ttd-space"></td>
            <td class="ttd-space"></td>
        </tr>
        <tr>
            <td>( Meisya Auliya )</td>
            <td>( ____________________ )</td>
        </tr>
    </table>

</body>
</html>
