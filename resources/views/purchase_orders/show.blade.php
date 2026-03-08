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
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

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
@endsection
