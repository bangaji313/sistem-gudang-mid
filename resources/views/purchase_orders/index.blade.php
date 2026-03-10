@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('content')
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Purchase Orders</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar seluruh purchase order</p>
        </div>
        @if (in_array(auth()->user()->role, ['purchase', 'super_admin']))
            <div class="flex items-center gap-2">
                <a href="{{ route('purchase-orders.export-pdf', request()->query()) }}"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
                    📄 PDF
                </a>
                <a href="{{ route('purchase-orders.export-excel') }}"
                   class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M12 12v-1.5m0 1.5c0 .621.504 1.125 1.125 1.125M12 12c0 .621-.504 1.125-1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5" /></svg>
                    Excel
                </a>
                <a href="{{ route('purchase-orders.create') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Buat PO Baru
                </a>
            </div>
        @endif
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6">
        <form method="GET" action="{{ route('purchase-orders.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3 items-end">
                {{-- Search --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Cari Vendor / No PO</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik..."
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                {{-- Bulan --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Bulan</label>
                    <select name="bulan" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" @selected(request('bulan') == $m)>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                        @endfor
                    </select>
                </div>
                {{-- Tahun --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun</label>
                    <select name="tahun" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua</option>
                        @for ($y = now()->year; $y >= now()->year - 3; $y--)
                            <option value="{{ $y }}" @selected(request('tahun') == $y)>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                {{-- Tipe PO --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tipe PO</label>
                    <select name="tipe_po" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua</option>
                        <option value="Ready Stock" @selected(request('tipe_po') == 'Ready Stock')>Ready Stock</option>
                        <option value="Pre-Order" @selected(request('tipe_po') == 'Pre-Order')>Pre-Order</option>
                    </select>
                </div>
                {{-- Status Pembayaran --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Pembayaran</label>
                    <select name="status_pembayaran" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua</option>
                        <option value="Lunas" @selected(request('status_pembayaran') == 'Lunas')>Lunas</option>
                        <option value="DP" @selected(request('status_pembayaran') == 'DP')>DP</option>
                        <option value="Tempo" @selected(request('status_pembayaran') == 'Tempo')>Non-DP (Tempo)</option>
                    </select>
                </div>
                {{-- Status Pesanan --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Status Pesanan</label>
                    <select name="status_pesanan" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua</option>
                        <option value="Selesai" @selected(request('status_pesanan') == 'Selesai')>Selesai</option>
                        <option value="Menunggu Kedatangan" @selected(request('status_pesanan') == 'Menunggu Kedatangan')>Menunggu</option>
                        <option value="Dibatalkan" @selected(request('status_pesanan') == 'Dibatalkan')>Dibatalkan</option>
                    </select>
                </div>
                {{-- Tombol --}}
                <div class="flex items-center gap-2">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                        🔍 Cari
                    </button>
                    <a href="{{ route('purchase-orders.index') }}"
                       class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-sm text-gray-500">Total PO</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $purchaseOrders->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-sm text-gray-500">Lunas</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $purchaseOrders->where('status_pembayaran', 'Lunas')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-sm text-gray-500">DP</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $purchaseOrders->where('status_pembayaran', 'DP')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-sm text-gray-500">Tempo</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $purchaseOrders->where('status_pembayaran', 'Tempo')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-sm text-gray-500">Menunggu Kedatangan</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ $purchaseOrders->where('status_pesanan', 'Menunggu Kedatangan')->count() }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor PO</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Invoice</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Vendor</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Grand Total</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Pembayaran</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Pesanan</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($purchaseOrders as $po)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <span class="font-mono font-medium text-blue-700">{{ $po->nomor_po }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 font-mono text-xs">
                                {{ $po->no_invoice ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ \Carbon\Carbon::parse($po->tanggal_po)->format('d M Y') }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $po->vendor->nama_vendor }}</td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-900">Rp {{ number_format($po->grand_total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $badgeClasses = match($po->status_pembayaran) {
                                        'Lunas'          => 'bg-green-100 text-green-700',
                                        'DP'             => 'bg-yellow-100 text-yellow-700',
                                        'Tempo'          => 'bg-red-100 text-red-700',
                                        'Void / Refund'  => 'bg-gray-200 text-gray-700',
                                        default          => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClasses }}">
                                    @if ($po->status_pembayaran === 'Tempo')
                                        NON-DP
                                    @else
                                        {{ $po->status_pembayaran }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($po->status_pesanan === 'Menunggu Kedatangan')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                        Menunggu
                                    </span>
                                @elseif ($po->status_pesanan === 'Dibatalkan')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        Dibatalkan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                        Selesai
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $po->user->name }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('purchase-orders.show', $po) }}"
                                   class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-medium transition">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                <p class="font-medium">Belum ada purchase order</p>
                                @if (in_array(auth()->user()->role, ['purchase', 'super_admin']))
                                    <a href="{{ route('purchase-orders.create') }}" class="text-blue-600 hover:underline text-sm mt-1 inline-block">Buat PO pertama →</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
