@extends('layouts.app')

@section('title', 'Edit User — ' . $user->name)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('users.index') }}" class="p-2 rounded-lg hover:bg-gray-100 transition" title="Kembali">
            <svg class="w-5 h-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
            <p class="text-sm text-gray-500 mt-0.5">Perbarui data <strong>{{ $user->name }}</strong></p>
        </div>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('users.update', $user) }}" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
        @csrf
        @method('PUT')

        {{-- Nama --}}
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus
                   class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
            @error('name')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Username --}}
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
            <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required
                   class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-500 @enderror">
            @error('username')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Role --}}
        <div>
            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
            <select id="role" name="role" required
                    class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 @error('role') border-red-500 @enderror">
                <option value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'selected' : '' }}>Super Admin (Direktur)</option>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="purchase" {{ old('role', $user->role) === 'purchase' ? 'selected' : '' }}>Purchase</option>
                <option value="gudang" {{ old('role', $user->role) === 'gudang' ? 'selected' : '' }}>Gudang</option>
            </select>
            @error('role')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Divider --}}
        <div class="border-t border-gray-200 pt-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-1">Ubah Password</h3>
            <p class="text-xs text-gray-500 mb-4">Kosongkan kedua field di bawah jika tidak ingin mengubah password.</p>

            {{-- Password Baru --}}
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <div class="relative">
                    <input type="password" id="password" name="password"
                           class="w-full rounded-lg border-gray-300 text-sm pr-11 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                           placeholder="Minimal 6 karakter (kosongkan jika tidak diubah)">
                    <button type="button" data-toggle-password="password"
                            class="absolute inset-y-0 right-0 flex items-center justify-center w-11 text-gray-400 hover:text-gray-600 transition"
                            tabindex="-1" aria-label="Tampilkan password">
                        <svg class="w-5 h-5 eye-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                        <svg class="w-5 h-5 eye-closed hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Konfirmasi Password Baru --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="w-full rounded-lg border-gray-300 text-sm pr-11 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Ulangi password baru">
                    <button type="button" data-toggle-password="password_confirmation"
                            class="absolute inset-y-0 right-0 flex items-center justify-center w-11 text-gray-400 hover:text-gray-600 transition"
                            tabindex="-1" aria-label="Tampilkan password">
                        <svg class="w-5 h-5 eye-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                        <svg class="w-5 h-5 eye-closed hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Telegram Section --}}
        <div class="border-t border-gray-200 pt-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-1">Koneksi Telegram</h3>
            <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4">
                <div>
                    <p class="text-sm text-gray-700">
                        Status:
                        @if($user->telegram_id)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                                Terhubung
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-600">
                                Belum Terhubung
                            </span>
                        @endif
                    </p>
                    @if($user->telegram_id)
                        <p class="text-xs text-gray-500 mt-1">Telegram ID: <code class="bg-gray-200 px-1 rounded">{{ $user->telegram_id }}</code></p>
                    @else
                        <p class="text-xs text-gray-500 mt-1">User belum login melalui Telegram Bot.</p>
                    @endif
                </div>
                @if($user->telegram_id)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="reset_telegram" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="text-xs text-red-600 font-medium">Reset Telegram</span>
                    </label>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
            <a href="{{ route('users.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-sm">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
