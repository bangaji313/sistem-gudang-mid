<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Purchase Order — PT MULTYARTHA INDO DAYA</title>
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
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data th, table.data td { border: 1px solid #bbb; padding: 5px 6px; }
        table.data th {
            background-color: #1e3a5f;
            color: #fff;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
        }
        table.data td { font-size: 10px; }
        table.data tbody tr:nth-child(even) { background-color: #f0f4f8; }
        table.data tbody tr:nth-child(odd) { background-color: #ffffff; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        /* Badge */
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .badge-lunas { background: #dcfce7; color: #16a34a; }
        .badge-dp { background: #fef9c3; color: #ca8a04; }
        .badge-tempo { background: #fee2e2; color: #dc2626; }
        .badge-void { background: #e5e7eb; color: #374151; }
        .badge-selesai { background: #dcfce7; color: #16a34a; }
        .badge-menunggu { background: #ffedd5; color: #ea580c; }
        .badge-dibatalkan { background: #fee2e2; color: #991b1b; }

        /* Ringkasan */
        table.summary { border-collapse: collapse; margin-bottom: 20px; }
        table.summary td { border: 1px solid #bbb; padding: 5px 8px; font-size: 10px; }

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
        <p><strong>Laporan:</strong> Data Purchase Order</p>
        <p><strong>Tanggal Cetak:</strong> {{ now()->format('d F Y — H:i') }} WIB</p>
        <p><strong>Dicetak oleh:</strong> {{ auth()->user()->name }} ({{ auth()->user()->role }})</p>
    </div>

    {{-- Tabel Data --}}
    <table class="data">
        <thead>
            <tr>
                <th class="text-center" style="width:22px;">No</th>
                <th style="width:95px;">Nomor PO</th>
                <th>Vendor</th>
                <th style="width:65px;">Tgl PO</th>
                <th style="width:55px;">Tipe</th>
                <th style="width:55px;">Pesanan</th>
                <th style="width:50px;">Bayar</th>
                <th class="text-right" style="width:85px;">Grand Total</th>
                <th class="text-right" style="width:80px;">Sisa Tagihan</th>
                <th>Dibuat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($purchaseOrders as $po)
                @php
                    $badgeBayar = match($po->status_pembayaran) {
                        'Lunas'         => 'badge-lunas',
                        'DP'            => 'badge-dp',
                        'Tempo'         => 'badge-tempo',
                        'Void / Refund' => 'badge-void',
                        default         => '',
                    };
                    $badgePesanan = match($po->status_pesanan) {
                        'Selesai'    => 'badge-selesai',
                        'Dibatalkan' => 'badge-dibatalkan',
                        default      => 'badge-menunggu',
                    };
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="font-bold">{{ $po->nomor_po }}</td>
                    <td>{{ $po->vendor->nama_vendor ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($po->tanggal_po)->format('d/m/Y') }}</td>
                    <td>{{ $po->tipe_po }}</td>
                    <td><span class="badge {{ $badgePesanan }}">{{ $po->status_pesanan }}</span></td>
                    <td><span class="badge {{ $badgeBayar }}">{{ $po->status_pembayaran === 'Tempo' ? 'NON-DP' : $po->status_pembayaran }}</span></td>
                    <td class="text-right font-bold">Rp {{ number_format($po->grand_total, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: {{ $po->sisa_pembayaran > 0 ? '#dc2626' : '#16a34a' }}; font-weight:bold;">
                        Rp {{ number_format($po->sisa_pembayaran, 0, ',', '.') }}
                    </td>
                    <td>{{ $po->user->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding:20px;color:#999;">Belum ada purchase order</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Ringkasan --}}
    <table class="summary" style="width:320px; margin-left:auto;">
        <tr>
            <td class="font-bold">Total PO</td>
            <td class="text-right font-bold">{{ $purchaseOrders->count() }}</td>
        </tr>
        <tr>
            <td class="font-bold">Total Nilai PO</td>
            <td class="text-right font-bold">Rp {{ number_format($purchaseOrders->sum('grand_total'), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="font-bold" style="color:#16a34a;">Lunas</td>
            <td class="text-right font-bold" style="color:#16a34a;">{{ $purchaseOrders->where('status_pembayaran','Lunas')->count() }}</td>
        </tr>
        <tr>
            <td class="font-bold" style="color:#ca8a04;">DP</td>
            <td class="text-right font-bold" style="color:#ca8a04;">{{ $purchaseOrders->where('status_pembayaran','DP')->count() }}</td>
        </tr>
        <tr>
            <td class="font-bold" style="color:#dc2626;">Non-DP (Tempo)</td>
            <td class="text-right font-bold" style="color:#dc2626;">{{ $purchaseOrders->where('status_pembayaran','Tempo')->count() }}</td>
        </tr>
        <tr>
            <td class="font-bold" style="color:#dc2626;">Total Sisa Tagihan</td>
            <td class="text-right font-bold" style="color:#dc2626;">Rp {{ number_format($purchaseOrders->sum('sisa_pembayaran'), 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- Footer --}}
    <div class="footer">
        Dokumen ini digenerate otomatis oleh Sistem Gudang PT MULTYARTHA INDO DAYA pada {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
