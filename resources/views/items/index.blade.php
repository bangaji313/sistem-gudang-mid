@extends('layouts.app')

@section('title', 'Data Barang')

@section('content')
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Data Barang</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola master data barang gudang</p>
        </div>
        @if (auth()->user()->role !== 'gudang')
            <div class="flex items-center gap-2">
                <a href="{{ route('items.export-excel', request()->query()) }}"
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
                    📊 Excel
                </a>
                <a href="{{ route('items.export-pdf', request()->query()) }}"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
                    📄 PDF
                </a>
                <a href="{{ route('items.create') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Barang
                </a>
            </div>
        @else
            <div class="flex items-center gap-2">
                <a href="{{ route('items.export-excel', request()->query()) }}"
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
                    📊 Excel
                </a>
                <a href="{{ route('items.export-pdf', request()->query()) }}"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
                    📄 PDF
                </a>
            </div>
        @endif
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6">
        <form method="GET" action="{{ route('items.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Cari Kode / Nama / Merk</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik..."
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Satuan</label>
                    <select name="uom_id" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua</option>
                        @foreach ($uoms as $uom)
                            <option value="{{ $uom->id }}" @selected(request('uom_id') == $uom->id)>{{ $uom->nama_satuan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Stok</label>
                    <select name="stok_filter" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua</option>
                        <option value="kosong" @selected(request('stok_filter') == 'kosong')>Stok Kosong</option>
                        <option value="tersedia" @selected(request('stok_filter') == 'tersedia')>Stok Tersedia</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                        🔍 Cari
                    </button>
                    <a href="{{ route('items.index') }}"
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
            <p class="text-sm text-gray-500">Total Item</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $items->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-sm text-gray-500">Stok Kosong</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $items->where('stok', 0)->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-sm text-gray-500">Total Stok</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($items->sum('stok'), 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Merk</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Satuan</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Stok</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">QR</th>
                        @if (auth()->user()->role !== 'gudang')
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($items as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-mono font-medium bg-gray-100 text-gray-700">
                                    {{ $item->kode_barang }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $item->nama_barang }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $item->merk ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $item->uom->nama_satuan }}</td>
                            <td class="px-6 py-4 text-right">
                                @if ($item->stok === 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Habis</span>
                                @else
                                    <span class="font-semibold text-gray-900">{{ number_format($item->stok, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('items.print-qr', $item) }}" target="_blank" class="inline-flex flex-col items-center gap-1 group">
                                    <div class="inline-block">
                                        {!! QrCode::size(50)->generate($item->kode_barang) !!}
                                    </div>
                                    <span class="text-[10px] text-blue-600 group-hover:text-blue-800 font-medium">Print</span>
                                </a>
                            </td>
                            @if (auth()->user()->role !== 'gudang')
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('items.edit', $item) }}"
                                           class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-medium transition">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" /></svg>
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('items.destroy', $item) }}" class="confirm-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 text-sm text-red-600 hover:text-red-800 font-medium transition">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>
                                <p class="font-medium">Belum ada data barang</p>
                                @if (auth()->user()->role !== 'gudang')
                                    <a href="{{ route('items.create') }}" class="text-blue-600 hover:underline text-sm mt-1 inline-block">Tambah barang pertama →</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
