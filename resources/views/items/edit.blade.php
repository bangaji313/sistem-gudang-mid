@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('items.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition mb-2">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
            Kembali ke Data Barang
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Edit Barang</h1>
        <p class="text-sm text-gray-500 mt-1">
            Kode: <span class="font-mono font-medium text-gray-700">{{ $item->kode_barang }}</span>
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-4xl">
        {{-- Form Card --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm">
            <form method="POST" action="{{ route('items.update', $item) }}" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                {{-- Nama Barang --}}
                <div>
                    <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nama Barang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama_barang" name="nama_barang" value="{{ old('nama_barang', $item->nama_barang) }}" required
                           class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                    @error('nama_barang') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Merk --}}
                <div>
                    <label for="merk" class="block text-sm font-medium text-gray-700 mb-1.5">Merk</label>
                    <input type="text" id="merk" name="merk" value="{{ old('merk', $item->merk) }}"
                           class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                    @error('merk') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Satuan (UOM) Dropdown --}}
                <div>
                    <label for="uom_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Satuan (UOM) <span class="text-red-500">*</span>
                    </label>
                    <select id="uom_id" name="uom_id" required
                            class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">— Pilih Satuan —</option>
                        @foreach ($uoms as $uom)
                            <option value="{{ $uom->id }}" {{ old('uom_id', $item->uom_id) == $uom->id ? 'selected' : '' }}>
                                {{ $uom->nama_satuan }}
                            </option>
                        @endforeach
                    </select>
                    @error('uom_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Stok (Koreksi Fisik / Stock Opname) --}}
                <div>
                    <label for="stok" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Stok (Koreksi Fisik) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="stok" name="stok" value="{{ old('stok', $item->stok) }}" required min="0" step="1"
                           class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                           placeholder="Masukkan jumlah stok aktual" />
                    @error('stok') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-1.5 text-xs text-red-500">
                        *Peringatan: Mengubah stok di sini tidak akan tercatat dalam riwayat transaksi. Gunakan hanya untuk keperluan Stock Opname / Koreksi selisih fisik.
                    </p>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        Perbarui
                    </button>
                    <a href="{{ route('items.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">Batal</a>
                </div>
            </form>
        </div>

        {{-- QR Code Card --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">QR Code</h3>
            <div class="flex flex-col items-center gap-3">
                <div class="bg-white p-3 rounded-lg border border-gray-200">
                    {!! QrCode::size(160)->generate($item->kode_barang) !!}
                </div>
                <p class="text-xs font-mono text-gray-500">{{ $item->kode_barang }}</p>
            </div>

            {{-- Item Info --}}
            <div class="mt-6 pt-4 border-t border-gray-100 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Stok</span>
                    <span class="font-semibold {{ $item->stok === 0 ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($item->stok, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Satuan</span>
                    <span class="text-gray-900">{{ $item->uom->nama_satuan }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
