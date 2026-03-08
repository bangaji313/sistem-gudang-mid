@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')
    {{-- Tom Select CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.min.css">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
    <style>
        .ts-wrapper.single .ts-control {
            background-color: rgb(249 250 251);
            border: 1px solid rgb(209 213 219);
            border-radius: 0.5rem;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            color: rgb(17 24 39);
            min-height: auto;
            box-shadow: none;
        }
        .ts-wrapper.single .ts-control:focus,
        .ts-wrapper.single.focus .ts-control {
            border-color: rgb(59 130 246);
            box-shadow: 0 0 0 2px rgba(59,130,246,0.3);
        }
        .ts-dropdown {
            border: 1px solid rgb(209 213 219);
            border-radius: 0.5rem;
            margin-top: 4px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,.1);
        }
        .ts-dropdown .option { padding: 0.5rem 1rem; font-size: 0.875rem; }
        .ts-dropdown .active { background-color: rgb(239 246 255); color: rgb(29 78 216); }
        .ts-dropdown .create { padding: 0.5rem 1rem; font-size: 0.875rem; color: rgb(37 99 235); }
    </style>

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('items.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition mb-2">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
            Kembali ke Data Barang
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Tambah Barang</h1>
        <p class="text-sm text-gray-500 mt-1">Kode barang akan di-generate otomatis oleh sistem</p>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm max-w-2xl">
        <form method="POST" action="{{ route('items.store') }}" class="p-6 space-y-5">
            @csrf

            {{-- Nama Barang --}}
            <div>
                <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nama Barang <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama_barang" name="nama_barang" value="{{ old('nama_barang') }}" required
                       class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                       placeholder="Contoh: Semen Portland" />
                @error('nama_barang') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Merk --}}
            <div>
                <label for="merk" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Merk
                </label>
                <input type="text" id="merk" name="merk" value="{{ old('merk') }}"
                       class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                       placeholder="Contoh: Tiga Roda" />
                @error('merk') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Satuan (UOM) Dropdown — Tom Select Creatable --}}
            <div>
                <label for="uom_select" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Satuan (UOM) <span class="text-red-500">*</span>
                </label>
                <select id="uom_select" name="nama_uom" required autocomplete="off" placeholder="Pilih atau ketik satuan baru...">
                    <option value="">— Pilih Satuan —</option>
                    @foreach ($uoms as $uom)
                        <option value="{{ $uom->nama_satuan }}" @selected(old('nama_uom') == $uom->nama_satuan)>
                            {{ $uom->nama_satuan }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-400">Ketik nama baru jika satuan belum terdaftar.</p>
                @error('nama_uom') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Stok Awal --}}
            <div>
                <label for="stok" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Stok Awal
                </label>
                <input type="number" id="stok" name="stok" value="{{ old('stok', 0) }}" min="0"
                       class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                       placeholder="0" />
                <p class="mt-1 text-xs text-gray-400">Isi jika barang sudah ada stok fisik di gudang. Kosongkan / 0 jika belum.</p>
                @error('stok') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Info Box --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    <div class="text-sm text-blue-700">
                        <p class="font-medium">Informasi</p>
                        <ul class="mt-1 space-y-0.5 text-blue-600">
                            <li>• <strong>Kode barang</strong> akan di-generate otomatis (format: MID-0001)</li>
                            <li>• <strong>QR Code</strong> akan dibuat secara otomatis setelah barang disimpan</li>
                            <li>• <strong>Stok awal</strong> bisa diisi manual — penambahan stok selanjutnya via Purchase Order</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    Simpan Barang
                </button>
                <a href="{{ route('items.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">Batal</a>
            </div>
        </form>
    </div>

    <script>
        new TomSelect('#uom_select', {
            create: true,
            createOnBlur: true,
            maxItems: 1,
            persist: false,
            sortField: { field: 'text', direction: 'asc' },
            render: {
                option_create: function(data, escape) {
                    return '<div class="create">+ Satuan baru: <strong>' + escape(data.input) + '</strong></div>';
                },
                no_results: function() {
                    return '<div style="padding:0.5rem 1rem;font-size:0.875rem;color:#9ca3af;">Tidak ditemukan — ketik untuk menambahkan</div>';
                }
            }
        });
    </script>
@endsection
