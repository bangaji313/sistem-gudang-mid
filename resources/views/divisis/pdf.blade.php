<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Divisi — PT MULTYARTHA INDO DAYA</title>
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
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data th, table.data td { border: 1px solid #bbb; padding: 6px 8px; }
        table.data th {
            background-color: #1e3a5f;
            color: #fff;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
        }
        table.data td { font-size: 11px; }
        table.data tbody tr:nth-child(even) { background-color: #f0f4f8; }
        table.data tbody tr:nth-child(odd) { background-color: #ffffff; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        /* Ringkasan */
        table.summary { border-collapse: collapse; margin-bottom: 20px; }
        table.summary td { border: 1px solid #bbb; padding: 6px 10px; font-size: 11px; }

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
        <p><strong>Laporan:</strong> Data Divisi / Tukang</p>
        <p><strong>Tanggal Cetak:</strong> {{ now()->format('d F Y — H:i') }} WIB</p>
        <p><strong>Dicetak oleh:</strong> {{ auth()->user()->name }} ({{ auth()->user()->role }})</p>
    </div>

    {{-- Tabel Data --}}
    <table class="data">
        <thead>
            <tr>
                <th class="text-center" style="width:35px;">No</th>
                <th>Nama Divisi / Tukang</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($divisis as $divisi)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="font-bold">{{ $divisi->nama_divisi_tukang }}</td>
                    <td>{{ $divisi->keterangan ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center" style="padding:20px;color:#999;">Belum ada data divisi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Ringkasan --}}
    <table class="summary" style="width:200px; margin-left:auto;">
        <tr>
            <td class="font-bold">Total Divisi</td>
            <td class="text-right font-bold">{{ $divisis->count() }}</td>
        </tr>
    </table>

    {{-- Footer --}}
    <div class="footer">
        Dokumen ini digenerate otomatis oleh Sistem Gudang PT MULTYARTHA INDO DAYA pada {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
