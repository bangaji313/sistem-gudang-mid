<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi Gudang — PT MULTYARTHA INDO DAYA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #333; }

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
        th, td { border: 1px solid #bbb; padding: 5px 6px; }
        th {
            background-color: #2563EB;
            color: #fff;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
        }
        td { font-size: 10px; }
        tr:nth-child(even) { background-color: #f3f4f6; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        /* Badge tipe */
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .badge-keluar { background: #fee2e2; color: #dc2626; }
        .badge-kembali { background: #dcfce7; color: #16a34a; }
        .badge-retur { background: #fef9c3; color: #ca8a04; }

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
        <p><strong>Laporan:</strong> Riwayat Transaksi Gudang</p>
        <p><strong>Tanggal Cetak:</strong> {{ now()->format('d F Y — H:i') }} WIB</p>
        <p><strong>Dicetak oleh:</strong> {{ auth()->user()->name }} ({{ auth()->user()->role }})</p>
    </div>

    {{-- Tabel Data --}}
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width:25px;">No</th>
                <th style="width:90px;">Tanggal</th>
                <th style="width:70px;">Tipe</th>
                <th>Barang</th>
                <th class="text-right" style="width:35px;">Qty</th>
                <th>Divisi</th>
                <th>Oleh</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $trx)
                @php
                    $tipe = match($trx->tipe_transaksi) {
                        'keluar'       => ['Barang Keluar', 'badge-keluar'],
                        'kembali_sisa' => ['Kembali Sisa', 'badge-kembali'],
                        'retur_cacat'  => ['Retur Cacat', 'badge-retur'],
                    };
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y H:i') }}</td>
                    <td><span class="badge {{ $tipe[1] }}">{{ $tipe[0] }}</span></td>
                    <td>
                        <span style="font-size:9px;color:#888;">{{ $trx->item->kode_barang }}</span>
                        {{ $trx->item->nama_barang }}
                    </td>
                    <td class="text-right font-bold">{{ number_format($trx->qty, 0, ',', '.') }}</td>
                    <td>{{ $trx->divisi?->nama_divisi_tukang ?? '—' }}</td>
                    <td>{{ $trx->user->name ?? '-' }}</td>
                    <td style="max-width:120px;word-wrap:break-word;">{{ $trx->keterangan ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding:20px;color:#999;">Belum ada transaksi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Ringkasan --}}
    <table style="width:280px; margin-left:auto;">
        <tr>
            <td class="font-bold">Total Transaksi</td>
            <td class="text-right font-bold">{{ $transactions->count() }}</td>
        </tr>
        <tr>
            <td class="font-bold" style="color:#dc2626;">Barang Keluar</td>
            <td class="text-right font-bold" style="color:#dc2626;">{{ $transactions->where('tipe_transaksi','keluar')->count() }}</td>
        </tr>
        <tr>
            <td class="font-bold" style="color:#16a34a;">Kembali Sisa</td>
            <td class="text-right font-bold" style="color:#16a34a;">{{ $transactions->where('tipe_transaksi','kembali_sisa')->count() }}</td>
        </tr>
        <tr>
            <td class="font-bold" style="color:#ca8a04;">Retur Cacat</td>
            <td class="text-right font-bold" style="color:#ca8a04;">{{ $transactions->where('tipe_transaksi','retur_cacat')->count() }}</td>
        </tr>
    </table>

    {{-- Footer --}}
    <div class="footer">
        Dokumen ini digenerate otomatis oleh Sistem Gudang PT MULTYARTHA INDO DAYA pada {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
