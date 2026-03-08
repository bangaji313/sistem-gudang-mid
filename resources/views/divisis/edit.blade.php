@extends('layouts.app')

@section('title', 'Edit Divisi')

@section('content')
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('divisis.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition mb-2">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
            Kembali ke Data Divisi
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Edit Divisi</h1>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm max-w-2xl">
        <form method="POST" action="{{ route('divisis.update', $divisi) }}" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="nama_divisi_tukang" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Divisi / Tukang <span class="text-red-500">*</span></label>
                <input type="text" id="nama_divisi_tukang" name="nama_divisi_tukang" value="{{ old('nama_divisi_tukang', $divisi->nama_divisi_tukang) }}" required
                       class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                @error('nama_divisi_tukang') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="3"
                          class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">{{ old('keterangan', $divisi->keterangan) }}</textarea>
                @error('keterangan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    Perbarui
                </button>
                <a href="{{ route('divisis.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">Batal</a>
            </div>
        </form>
    </div>
@endsection
