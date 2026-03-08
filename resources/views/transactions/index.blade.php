@extends('layouts.app')

@section('title', 'Transaksi Gudang')

@section('content')
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Transaksi Gudang</h1>
            <p class="text-sm text-gray-500 mt-1">Riwayat seluruh transaksi barang keluar, kembali sisa, dan retur cacat</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('transactions.export-excel', request()->query()) }}"
               class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
                📊 Excel
            </a>
            <a href="{{ route('transactions.export-pdf', request()->query()) }}"
               class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
                📄 PDF
            </a>
            <a href="{{ route('transactions.create') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Catat Transaksi
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6">
        <form method="GET" action="{{ route('transactions.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Cari Barang / Divisi / Ket</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik..."
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tipe Transaksi</label>
                    <select name="tipe_transaksi" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua</option>
                        <option value="keluar" @selected(request('tipe_transaksi') == 'keluar')>Barang Keluar</option>
                        <option value="kembali_sisa" @selected(request('tipe_transaksi') == 'kembali_sisa')>Kembali Sisa</option>
                        <option value="retur_cacat" @selected(request('tipe_transaksi') == 'retur_cacat')>Retur Cacat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Bulan</label>
                    <select name="bulan" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" @selected(request('bulan') == $m)>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun</label>
                    <select name="tahun" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua</option>
                        @for ($y = now()->year; $y >= now()->year - 3; $y--)
                            <option value="{{ $y }}" @selected(request('tahun') == $y)>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                        🔍 Cari
                    </button>
                    <a href="{{ route('transactions.index') }}"
                       class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-sm text-gray-500">Barang Keluar</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $transactions->where('tipe_transaksi', 'keluar')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-sm text-gray-500">Kembali Sisa</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $transactions->where('tipe_transaksi', 'kembali_sisa')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-sm text-gray-500">Retur Cacat</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $transactions->where('tipe_transaksi', 'retur_cacat')->count() }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Tipe</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Barang</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Qty</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Satuan</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Divisi</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Oleh</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($transactions as $trx)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $badge = match($trx->tipe_transaksi) {
                                        'keluar'       => ['bg-red-100 text-red-700', 'Keluar'],
                                        'kembali_sisa' => ['bg-green-100 text-green-700', 'Kembali Sisa'],
                                        'retur_cacat'  => ['bg-yellow-100 text-yellow-700', 'Retur Cacat'],
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge[0] }}">
                                    {{ $badge[1] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-xs text-gray-500">{{ $trx->item->kode_barang }}</span>
                                <span class="ml-1 font-medium text-gray-900">{{ $trx->item->nama_barang }}</span>
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-900">{{ number_format($trx->qty, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $trx->item->uom->nama_satuan }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $trx->divisi?->nama_divisi_tukang ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $trx->user->name }}</td>
                            <td class="px-6 py-4 text-gray-500 max-w-xs truncate">{{ $trx->keterangan ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" /></svg>
                                <p class="font-medium">Belum ada transaksi</p>
                                <a href="{{ route('transactions.create') }}" class="text-blue-600 hover:underline text-sm mt-1 inline-block">Catat transaksi pertama →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
