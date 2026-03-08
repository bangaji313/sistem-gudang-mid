@extends('layouts.app')

@section('title', 'Edit Vendor')

@section('content')
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('vendors.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition mb-2">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
            Kembali ke Data Vendor
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Edit Vendor</h1>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm max-w-2xl">
        <form method="POST" action="{{ route('vendors.update', $vendor) }}" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="nama_vendor" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Vendor <span class="text-red-500">*</span></label>
                <input type="text" id="nama_vendor" name="nama_vendor" value="{{ old('nama_vendor', $vendor->nama_vendor) }}" required
                       class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                @error('nama_vendor') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="kontak" class="block text-sm font-medium text-gray-700 mb-1.5">Kontak</label>
                <input type="text" id="kontak" name="kontak" value="{{ old('kontak', $vendor->kontak) }}"
                       class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                @error('kontak') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1.5">Alamat</label>
                <textarea id="alamat" name="alamat" rows="3"
                          class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">{{ old('alamat', $vendor->alamat) }}</textarea>
                @error('alamat') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    Perbarui
                </button>
                <a href="{{ route('vendors.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">Batal</a>
            </div>
        </form>
    </div>
@endsection
