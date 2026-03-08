<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Stok Barang — PT MULTYARTHA INDO DAYA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #333; }

        /* Kop Surat */
        .kop-table { width: 100%; border-bottom: 3px double #1e3a5f; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-table td { border: none; vertical-align: middle; padding: 0; }
        .kop-logo { width: 70px; }
        .kop-logo img { width: 60px; height: auto; }
        .kop-info { padding-left: 12px; }
        .kop-info h1 { font-size: 18px; font-weight: bold; color: #1e3a5f; letter-spacing: 1px; margin-bottom: 2px; }
        .kop-info p { font-size: 9px; color: #555; line-height: 1.5; }

        /* Info */
        .info { margin-bottom: 14px; }
        .info p { font-size: 10px; color: #666; }

        /* Tabel */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #bbb; padding: 6px 8px; }
        th {
            background-color: #2563EB;
            color: #fff;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
        }
        td { font-size: 11px; }
        tr:nth-child(even) { background-color: #f3f4f6; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .stok-habis { color: #dc2626; font-weight: bold; }

        /* Footer */
        .footer { text-align: center; font-size: 9px; color: #999; margin-top: 30px; border-top: 1px solid #ddd; padding-top: 8px; }
    </style>
</head>
<body>
    {{-- Kop Surat --}}
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo">
            </td>
            <td class="kop-info">
                <h1>PT MULTYARTHA INDO DAYA</h1>
                <p>Surya Asri Residence 9A</p>
                <p>Cipagalo Bojongsoang</p>
                <p>Kabupaten Bandung</p>
                <p>Email : multyarthaindodaya@gmail.com</p>
            </td>
        </tr>
    </table>

    {{-- Info Dokumen --}}
    <div class="info">
        <p><strong>Laporan:</strong> Data Stok Barang</p>
        <p><strong>Tanggal Cetak:</strong> {{ now()->format('d F Y — H:i') }} WIB</p>
        <p><strong>Dicetak oleh:</strong> {{ auth()->user()->name }} ({{ auth()->user()->role }})</p>
    </div>

    {{-- Tabel Data --}}
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width:35px;">No</th>
                <th style="width:80px;">Kode</th>
                <th>Nama Barang</th>
                <th>Merk</th>
                <th>Satuan</th>
                <th class="text-right" style="width:60px;">Stok</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->merk ?? '—' }}</td>
                    <td>{{ $item->uom->nama_satuan ?? '-' }}</td>
                    <td class="text-right {{ $item->stok == 0 ? 'stok-habis' : 'font-bold' }}">
                        {{ $item->stok == 0 ? 'HABIS' : number_format($item->stok, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding:20px;color:#999;">Belum ada data barang</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Ringkasan --}}
    <table style="width:250px; margin-left:auto;">
        <tr>
            <td class="font-bold">Total Item</td>
            <td class="text-right font-bold">{{ $items->count() }}</td>
        </tr>
        <tr>
            <td class="font-bold">Total Stok</td>
            <td class="text-right font-bold">{{ number_format($items->sum('stok'), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="font-bold stok-habis">Stok Kosong</td>
            <td class="text-right font-bold stok-habis">{{ $items->where('stok', 0)->count() }}</td>
        </tr>
    </table>

    {{-- Footer --}}
    <div class="footer">
        Dokumen ini digenerate otomatis oleh Sistem Gudang PT MULTYARTHA INDO DAYA pada {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
