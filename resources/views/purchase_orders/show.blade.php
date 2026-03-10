@extends('layouts.app')

@section('title', 'Detail PO — ' . $purchaseOrder->nomor_po)

@section('content')
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('purchase-orders.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition mb-2">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
            Kembali ke Daftar PO
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $purchaseOrder->nomor_po }}</h1>
                <p class="text-sm text-gray-500 mt-1">Detail Purchase Order</p>
            </div>
            <div class="flex items-center gap-2">
                {{-- Tombol Cetak Continuous Form (PDF) --}}
                <a href="{{ route('purchase-orders.print-continuous', $purchaseOrder) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium px-3.5 py-1.5 rounded-lg transition"
                   title="Cetak Bukti PO (PDF Continuous Form)">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18.75 7.131H5.25" /></svg>
                    Print PDF
                </a>

                {{-- Tombol Cetak Dot Matrix (Excel) --}}
                <a href="{{ route('purchase-orders.print-excel', $purchaseOrder) }}"
                   class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-3.5 py-1.5 rounded-lg transition"
                   title="Cetak Kontra Bon Dot Matrix (Excel)">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M12 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5" /></svg>
                    Dot Matrix
                </a>

                {{-- Tombol Edit Tanggal --}}
                @if (in_array(auth()->user()->role, ['purchase', 'super_admin']))
                <button type="button" onclick="document.getElementById('modalEditTanggal').classList.remove('hidden')"
                        class="inline-flex items-center gap-1.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium px-3.5 py-1.5 rounded-lg transition"
                        title="Quick Edit Tanggal PO">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                    Edit Tanggal
                </button>
                @endif

                {{-- Badge Pembayaran --}}
                @php
                    $badgeClasses = match($purchaseOrder->status_pembayaran) {
                        'Lunas'          => 'bg-green-100 text-green-700',
                        'DP'             => 'bg-yellow-100 text-yellow-700',
                        'Tempo'          => 'bg-red-100 text-red-700',
                        'Void / Refund'  => 'bg-gray-200 text-gray-700',
                        default          => 'bg-gray-100 text-gray-600',
                    };
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $badgeClasses }}">
                    @if ($purchaseOrder->status_pembayaran === 'Tempo')
                        NON-DP
                    @else
                        {{ $purchaseOrder->status_pembayaran }}
                    @endif
                </span>

                {{-- Badge Pesanan --}}
                @php
                    $pesananBadge = match($purchaseOrder->status_pesanan) {
                        'Menunggu Kedatangan' => 'bg-orange-100 text-orange-700',
                        'Selesai'             => 'bg-blue-100 text-blue-700',
                        'Dibatalkan'          => 'bg-red-100 text-red-700',
                        default               => 'bg-gray-100 text-gray-700',
                    };
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $pesananBadge }}">
                    {{ $purchaseOrder->status_pesanan }}
                </span>
            </div>
        </div>
    </div>

    {{-- Info Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        {{-- Vendor Info --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Vendor</p>
            <p class="font-semibold text-gray-900 text-lg">{{ $purchaseOrder->vendor->nama_vendor }}</p>
            @if ($purchaseOrder->vendor->kontak)
                <div class="flex items-center gap-1.5 text-sm text-gray-500 mt-2">
                    <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                    {{ $purchaseOrder->vendor->kontak }}
                </div>
            @endif
            @if ($purchaseOrder->vendor->alamat)
                <div class="flex items-start gap-1.5 text-sm text-gray-500 mt-1">
                    <svg class="w-4 h-4 text-gray-400 mt-0.5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                    {{ $purchaseOrder->vendor->alamat }}
                </div>
            @endif
        </div>

        {{-- Tanggal & Pembayaran --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Informasi Tanggal</p>
            <div class="space-y-2">
                <div>
                    <span class="text-xs text-gray-400">Tanggal PO</span>
                    <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($purchaseOrder->tanggal_po)->format('d M Y') }}</p>
                </div>
                @if ($purchaseOrder->proyek)
                    <div>
                        <span class="text-xs text-gray-400">Proyek</span>
                        <p class="font-semibold text-gray-900">{{ $purchaseOrder->proyek }}</p>
                    </div>
                @endif
                @if ($purchaseOrder->tgl_kontra_bon)
                    <div>
                        <span class="text-xs text-gray-400">Tgl Kontra Bon</span>
                        <p class="font-semibold text-gray-900">{{ $purchaseOrder->tgl_kontra_bon->format('d M Y') }}</p>
                    </div>
                @endif
                @if ($purchaseOrder->tgl_delivery)
                    <div>
                        <span class="text-xs text-gray-400">Tgl Delivery</span>
                        <p class="font-semibold text-gray-900">{{ $purchaseOrder->tgl_delivery->format('d M Y') }}</p>
                    </div>
                @endif
                @if ($purchaseOrder->no_invoice)
                    <div>
                        <span class="text-xs text-gray-400">No. Invoice</span>
                        <p class="font-semibold text-gray-900">{{ $purchaseOrder->no_invoice }}</p>
                    </div>
                @endif
                @if ($purchaseOrder->tgl_invoice)
                    <div>
                        <span class="text-xs text-gray-400">Tgl Invoice</span>
                        <p class="font-semibold text-gray-900">{{ $purchaseOrder->tgl_invoice->format('d M Y') }}</p>
                    </div>
                @endif
                @if ($purchaseOrder->tgl_diterima)
                    <div>
                        <span class="text-xs text-gray-400">Tgl Diterima</span>
                        <p class="font-semibold text-gray-900">{{ $purchaseOrder->tgl_diterima->format('d M Y') }}</p>
                    </div>
                @endif
                <div>
                    <span class="text-xs text-gray-400">Status Pembayaran</span>
                    <p class="font-semibold text-gray-900">{{ $purchaseOrder->status_pembayaran === 'Tempo' ? 'NON-DP' : $purchaseOrder->status_pembayaran }}</p>
                </div>
                @if (in_array($purchaseOrder->status_pembayaran, ['DP', 'Tempo']) && $purchaseOrder->tanggal_jatuh_tempo)
                    <div>
                        <span class="text-xs text-gray-400">Jatuh Tempo</span>
                        <p class="font-semibold text-red-600">{{ \Carbon\Carbon::parse($purchaseOrder->tanggal_jatuh_tempo)->format('d M Y') }}</p>
                    </div>
                @endif
                <div>
                    <span class="text-xs text-gray-400">Tipe Pembelian</span>
                    <p class="font-semibold text-gray-900">{{ $purchaseOrder->tipe_po }}</p>
                </div>
            </div>
        </div>

        {{-- Dibuat Oleh --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Dibuat Oleh</p>
            <p class="font-semibold text-gray-900">{{ $purchaseOrder->user->name }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ $purchaseOrder->created_at->format('d M Y H:i') }}</p>
        </div>
    </div>

    {{-- Pre-Order: Countdown & Konfirmasi Sampai (sembunyikan jika Dibatalkan) --}}
    @if ($purchaseOrder->status_pesanan !== 'Dibatalkan')
      @if ($purchaseOrder->tipe_po === 'Pre-Order' && $purchaseOrder->status_pesanan === 'Menunggu Kedatangan')
        @php
            $diff = \Carbon\Carbon::now()->startOfDay()->diffInDays(
                \Carbon\Carbon::parse($purchaseOrder->estimasi_sampai)->startOfDay(),
                false
            );
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">📦 Status Pre-Order</h2>
            <div class="space-y-3">
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-500">Estimasi Sampai:</span>
                    <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($purchaseOrder->estimasi_sampai)->format('d M Y') }}</span>
                </div>

                {{-- Countdown --}}
                <div class="text-sm">
                    @if ($diff > 0)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-700 font-medium">
                            ⏱ Menunggu {{ $diff }} hari lagi
                        </span>
                    @elseif ($diff == 0)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-50 border border-green-200 text-green-700 font-medium">
                            🎉 Estimasi sampai hari ini!
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 border border-red-200 text-red-700 font-bold">
                            🚨 Terlambat {{ abs($diff) }} hari! Segera hubungi vendor.
                        </span>
                    @endif
                </div>

                {{-- Tombol Konfirmasi Barang Sampai --}}
                @if (in_array(auth()->user()->role, ['purchase', 'super_admin']))
                    <div class="pt-3 border-t border-gray-200">
                        <form method="POST" action="{{ route('purchase-orders.arrived', $purchaseOrder) }}" class="confirm-arrived">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition shadow-sm">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                ✅ Konfirmasi Barang Sampai
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
      @endif
    @endif

    {{-- Detail Items Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Detail Barang</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Satuan</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Qty</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Harga Satuan</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Subtotal</th>
                        @if (in_array(auth()->user()->role, ['purchase', 'super_admin']) && $purchaseOrder->status_pesanan !== 'Dibatalkan')
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($purchaseOrder->details as $detail)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-mono font-medium bg-gray-100 text-gray-700">
                                    {{ $detail->item->kode_barang }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $detail->item->nama_barang }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $detail->item->uom->nama_satuan }}</td>
                            <td class="px-6 py-4 text-right text-gray-900">{{ number_format($detail->qty, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-gray-600">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-900">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            @if (in_array(auth()->user()->role, ['purchase', 'super_admin']) && $purchaseOrder->status_pesanan !== 'Dibatalkan')
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center gap-1">
                                        {{-- Tombol Revisi --}}
                                        <button type="button" onclick="document.getElementById('modalRevisi{{ $detail->id }}').classList.remove('hidden')"
                                                class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-orange-700 bg-orange-50 border border-orange-200 rounded-md hover:bg-orange-100 transition"
                                                title="Revisi QTY / Harga">
                                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                            Revisi
                                        </button>
                                        {{-- Tombol History --}}
                                        @if ($detail->revisions->count() > 0)
                                        <button type="button" onclick="document.getElementById('modalHistory{{ $detail->id }}').classList.remove('hidden')"
                                                class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-md hover:bg-indigo-100 transition"
                                                title="Riwayat Revisi">
                                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                            {{ $detail->revisions->count() }}
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Revisi Detail & History (per detail line)             --}}
    {{-- ════════════════════════════════════════════════════════════ --}}
    @if (in_array(auth()->user()->role, ['purchase', 'super_admin']) && $purchaseOrder->status_pesanan !== 'Dibatalkan')
        @foreach ($purchaseOrder->details as $detail)
            {{-- ─── Modal Revisi ─── --}}
            <div id="modalRevisi{{ $detail->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-orange-50">
                        <h3 class="text-lg font-bold text-gray-900">⚠️ Revisi Detail Barang</h3>
                        <button type="button" onclick="document.getElementById('modalRevisi{{ $detail->id }}').classList.add('hidden')"
                                class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('purchase-orders.revise-detail', $detail) }}" class="revise-form px-6 py-5 space-y-4">
                        @csrf
                        @method('PUT')

                        {{-- Info Barang --}}
                        <div class="bg-gray-50 rounded-lg p-3 text-sm">
                            <p class="font-semibold text-gray-900">{{ $detail->item->nama_barang }}</p>
                            <p class="text-gray-500 text-xs mt-0.5">Kode: {{ $detail->item->kode_barang }} · Satuan: {{ $detail->item->uom->nama_satuan }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            {{-- QTY Saat Ini --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">QTY Saat Ini</label>
                                <p class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-semibold text-gray-700">{{ number_format($detail->qty, 0, ',', '.') }}</p>
                            </div>
                            {{-- QTY Baru --}}
                            <div>
                                <label for="new_qty_{{ $detail->id }}" class="block text-xs font-medium text-gray-700 mb-1">QTY Baru <span class="text-red-500">*</span></label>
                                <input type="number" id="new_qty_{{ $detail->id }}" name="new_qty" value="{{ $detail->qty }}" min="1" required
                                       class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500" />
                            </div>
                            {{-- Harga Saat Ini --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Harga Saat Ini</label>
                                <p class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-semibold text-gray-700">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</p>
                            </div>
                            {{-- Harga Baru --}}
                            <div>
                                <label for="new_harga_{{ $detail->id }}" class="block text-xs font-medium text-gray-700 mb-1">Harga Baru <span class="text-red-500">*</span></label>
                                <input type="number" id="new_harga_{{ $detail->id }}" name="new_harga_satuan" value="{{ intval($detail->harga_satuan) }}" min="0" step="1" required
                                       class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500" />
                            </div>
                        </div>

                        {{-- Keterangan Revisi --}}
                        <div>
                            <label for="keterangan_{{ $detail->id }}" class="block text-xs font-medium text-gray-700 mb-1">Keterangan Revisi</label>
                            <textarea id="keterangan_{{ $detail->id }}" name="keterangan" rows="2" placeholder="Alasan revisi, misal: Barang datang kurang 2 pcs..."
                                      class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-200">
                            <button type="button" onclick="document.getElementById('modalRevisi{{ $detail->id }}').classList.add('hidden')"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-5 py-2 text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 rounded-lg transition shadow-sm">
                                ✏️ Simpan Revisi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ─── Modal History ─── --}}
            @if ($detail->revisions->count() > 0)
            <div id="modalHistory{{ $detail->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-indigo-50">
                        <h3 class="text-lg font-bold text-gray-900">🕐 Riwayat Revisi — {{ $detail->item->nama_barang }}</h3>
                        <button type="button" onclick="document.getElementById('modalHistory{{ $detail->id }}').classList.add('hidden')"
                                class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="px-6 py-5 max-h-[60vh] overflow-y-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 border-b border-gray-200 sticky top-0">
                                <tr>
                                    <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">#</th>
                                    <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                                    <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Perubahan QTY</th>
                                    <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Perubahan Harga</th>
                                    <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                                    <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Oleh</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($detail->revisions->sortByDesc('created_at') as $rev)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2.5 text-gray-500">{{ $loop->iteration }}</td>
                                        <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">{{ $rev->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-3 py-2.5">
                                            @if ($rev->old_qty != $rev->new_qty)
                                                <span class="text-red-500 line-through">{{ $rev->old_qty }}</span>
                                                <span class="text-gray-400 mx-1">→</span>
                                                <span class="text-green-600 font-semibold">{{ $rev->new_qty }}</span>
                                            @else
                                                <span class="text-gray-400">{{ $rev->old_qty }} (tetap)</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5">
                                            @if ($rev->old_price != $rev->new_price)
                                                <span class="text-red-500 line-through">{{ number_format($rev->old_price, 0, ',', '.') }}</span>
                                                <span class="text-gray-400 mx-1">→</span>
                                                <span class="text-green-600 font-semibold">{{ number_format($rev->new_price, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-gray-400">{{ number_format($rev->old_price, 0, ',', '.') }} (tetap)</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 text-gray-600 text-xs max-w-[150px] truncate" title="{{ $rev->keterangan }}">{{ $rev->keterangan ?? '-' }}</td>
                                        <td class="px-3 py-2.5 text-gray-600 text-xs">{{ $rev->revisedBy->name ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-3 border-t border-gray-200 flex justify-end">
                        <button type="button" onclick="document.getElementById('modalHistory{{ $detail->id }}').classList.add('hidden')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    @endif

    {{-- Summary --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-sm ml-auto">
        <div class="space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Total Harga</span>
                <span class="text-gray-900">Rp {{ number_format($purchaseOrder->total_harga, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Diskon</span>
                <span class="text-red-600">- Rp {{ number_format($purchaseOrder->diskon, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-base font-bold border-t border-gray-200 pt-3">
                <span class="text-gray-700">Grand Total</span>
                <span class="text-blue-700">Rp {{ number_format($purchaseOrder->grand_total, 0, ',', '.') }}</span>
            </div>

            {{-- DP & Sisa Pembayaran --}}
            @if ($purchaseOrder->status_pembayaran === 'DP')
                <div class="flex justify-between text-sm border-t border-dashed border-gray-200 pt-3">
                    <span class="text-gray-500">Nominal DP</span>
                    <span class="text-green-600 font-semibold">Rp {{ number_format($purchaseOrder->nominal_dp, 0, ',', '.') }}</span>
                </div>
            @endif

            @if ($purchaseOrder->sisa_pembayaran > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Sisa Tagihan</span>
                    <span class="text-red-600 font-bold">Rp {{ number_format($purchaseOrder->sisa_pembayaran, 0, ',', '.') }}</span>
                </div>
            @else
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Sisa Tagihan</span>
                    <span class="text-green-600 font-semibold">Rp 0</span>
                </div>
            @endif

            {{-- Tombol aksi hanya tampil jika PO belum dibatalkan --}}
            @if ($purchaseOrder->status_pesanan !== 'Dibatalkan')
                {{-- Tombol Pelunasan --}}
                @if (in_array($purchaseOrder->status_pembayaran, ['DP', 'Tempo']))
                    <div class="border-t border-gray-200 pt-4 mt-1">
                        <form method="POST" action="{{ route('purchase-orders.lunas', $purchaseOrder) }}" class="confirm-lunas">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition shadow-sm">
                                💸 PEMBAYARAN TELAH DILUNASI
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Tombol Batalkan Pesanan (hanya jika Menunggu Kedatangan) --}}
                @if ($purchaseOrder->status_pesanan === 'Menunggu Kedatangan')
                    <div class="border-t border-gray-200 pt-4 mt-1">
                        <form method="POST" action="{{ route('purchase-orders.cancel', $purchaseOrder) }}" class="confirm-cancel">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition shadow-sm">
                                ❌ Batalkan Pesanan
                            </button>
                        </form>
                    </div>
                @endif
            @endif
        </div>
    </div>
    {{-- ════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Quick Edit Tanggal                                    --}}
    {{-- ════════════════════════════════════════════════════════════ --}}
    @if (in_array(auth()->user()->role, ['purchase', 'super_admin']))
    {{-- Flatpickr CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <div id="modalEditTanggal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-amber-50">
                <h3 class="text-lg font-bold text-gray-900">📅 Quick Edit Tanggal</h3>
                <button type="button" onclick="document.getElementById('modalEditTanggal').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <form id="formEditTanggal" method="POST" action="{{ route('purchase-orders.update-dates', $purchaseOrder) }}" class="px-6 py-5 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Proyek --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Proyek</label>
                        <input type="text" name="proyek"
                               value="{{ $purchaseOrder->proyek ?? '' }}"
                               placeholder="Ketik nama proyek (opsional)"
                               class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500" />
                    </div>

                    {{-- Tgl Kontra Bon --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tgl Kontra Bon</label>
                        <input type="text" name="tgl_kontra_bon" readonly
                               value="{{ $purchaseOrder->tgl_kontra_bon ? $purchaseOrder->tgl_kontra_bon->format('d/m/Y') : '' }}"
                               class="modal-datepicker block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500" />
                    </div>

                    {{-- Tanggal PO --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal PO <span class="text-red-500">*</span></label>
                        <input type="text" name="tanggal_po" readonly
                               value="{{ $purchaseOrder->tanggal_po ? $purchaseOrder->tanggal_po->format('d/m/Y') : '' }}"
                               class="modal-datepicker block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500" required />
                    </div>

                    {{-- Tgl Delivery --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tgl Delivery</label>
                        <input type="text" name="tgl_delivery" readonly
                               value="{{ $purchaseOrder->tgl_delivery ? $purchaseOrder->tgl_delivery->format('d/m/Y') : '' }}"
                               class="modal-datepicker block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500" />
                    </div>

                    {{-- Estimasi Sampai --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi Sampai</label>
                        <input type="text" name="estimasi_sampai" readonly
                               value="{{ $purchaseOrder->estimasi_sampai ? $purchaseOrder->estimasi_sampai->format('d/m/Y') : '' }}"
                               class="modal-datepicker block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500" />
                    </div>

                    {{-- Tgl Invoice --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tgl Invoice</label>
                        <input type="text" name="tgl_invoice" readonly
                               value="{{ $purchaseOrder->tgl_invoice ? $purchaseOrder->tgl_invoice->format('d/m/Y') : '' }}"
                               class="modal-datepicker block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500" />
                    </div>

                    {{-- Tgl Diterima --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tgl Diterima</label>
                        <input type="text" name="tgl_diterima" readonly
                               value="{{ $purchaseOrder->tgl_diterima ? $purchaseOrder->tgl_diterima->format('d/m/Y') : '' }}"
                               class="modal-datepicker block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500" />
                    </div>

                    {{-- Tanggal Jatuh Tempo --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Jatuh Tempo</label>
                        <input type="text" name="tanggal_jatuh_tempo" readonly
                               value="{{ $purchaseOrder->tanggal_jatuh_tempo ? $purchaseOrder->tanggal_jatuh_tempo->format('d/m/Y') : '' }}"
                               class="modal-datepicker block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500" />
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-200">
                    <button type="button" onclick="document.getElementById('modalEditTanggal').classList.add('hidden')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-lg transition shadow-sm">
                        💾 Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Flatpickr untuk field di dalam modal
        flatpickr(".modal-datepicker", {
            dateFormat: "d/m/Y",
            allowInput: true,
        });

        // SweetAlert2 konfirmasi sebelum submit
        document.getElementById('formEditTanggal').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Simpan Perubahan Tanggal?',
                text: 'Apakah Anda yakin ingin menyimpan perubahan tanggal ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // ═══ SweetAlert2 konfirmasi Revisi Detail Barang ═══
        document.querySelectorAll('.revise-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const f = this;
                Swal.fire({
                    title: 'Revisi Detail Barang?',
                    text: 'Perubahan QTY / Harga akan tercatat di rekam jejak (audit trail). Lanjutkan?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f97316',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Revisi!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        f.submit();
                    }
                });
            });
        });

        // ═══ Tutup modal jika klik backdrop ═══
        document.querySelectorAll('[id^="modalRevisi"], [id^="modalHistory"]').forEach(function(modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        });
    </script>
    @endif
@endsection
