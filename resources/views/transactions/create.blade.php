@extends('layouts.app')

@section('title', 'Catat Transaksi')

@section('content')
    {{-- Flatpickr CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    {{-- Tom Select CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.min.css">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
    <style>
        /* Override Tom Select agar menyatu dengan desain Tailwind form */
        .ts-wrapper.single .ts-control {
            background-color: rgb(249 250 251); /* bg-gray-50 */
            border: 1px solid rgb(209 213 219); /* border-gray-300 */
            border-radius: 0.5rem; /* rounded-lg */
            padding: 0.625rem 1rem; /* px-4 py-2.5 */
            font-size: 0.875rem;
            line-height: 1.25rem;
            color: rgb(17 24 39); /* text-gray-900 */
            min-height: auto;
            box-shadow: none;
        }
        .ts-wrapper.single .ts-control:focus,
        .ts-wrapper.single.focus .ts-control {
            border-color: rgb(59 130 246); /* border-blue-500 */
            box-shadow: 0 0 0 2px rgba(59,130,246,0.3); /* ring-2 ring-blue-500 */
        }
        .ts-wrapper.single .ts-control input {
            font-size: 0.875rem;
            color: rgb(17 24 39);
        }
        .ts-wrapper.single .ts-control input::placeholder {
            color: rgb(156 163 175); /* placeholder-gray-400 */
        }
        .ts-dropdown {
            border: 1px solid rgb(209 213 219);
            border-radius: 0.5rem;
            margin-top: 4px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,.1);
        }
        .ts-dropdown .option {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        .ts-dropdown .active {
            background-color: rgb(239 246 255); /* bg-blue-50 */
            color: rgb(29 78 216); /* text-blue-700 */
        }
        .ts-dropdown .create {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            color: rgb(37 99 235); /* text-blue-600 */
        }
    </style>

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('transactions.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition mb-2">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
            Kembali ke Daftar Transaksi
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Catat Transaksi Baru</h1>
        <p class="text-sm text-gray-500 mt-1">Pencatatan barang keluar, kembali sisa, atau retur cacat</p>
    </div>

    <form method="POST" action="{{ route('transactions.store') }}" class="max-w-2xl">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">

            {{-- Tipe Transaksi --}}
            <div>
                <label for="tipe_transaksi" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Tipe Transaksi <span class="text-red-500">*</span>
                </label>
                <select id="tipe_transaksi" name="tipe_transaksi" required
                        class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">— Pilih Tipe —</option>
                    <option value="keluar" {{ old('tipe_transaksi') === 'keluar' ? 'selected' : '' }}>Barang Keluar</option>
                    <option value="kembali_sisa" {{ old('tipe_transaksi') === 'kembali_sisa' ? 'selected' : '' }}>Kembali Sisa</option>
                    <option value="retur_cacat" {{ old('tipe_transaksi') === 'retur_cacat' ? 'selected' : '' }}>Retur Cacat</option>
                </select>
                @error('tipe_transaksi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Barang --}}
            <div>
                <label for="item_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Barang <span class="text-red-500">*</span>
                </label>
                <select id="item_id" name="item_id" required
                        class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">— Pilih Barang —</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" data-stok="{{ $item->stok }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->kode_barang }} — {{ $item->nama_barang }} (Stok: {{ $item->stok }} {{ $item->uom->nama_satuan }})
                        </option>
                    @endforeach
                </select>
                @error('item_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                <p id="stokInfo" class="mt-1 text-sm text-gray-500 hidden">Stok tersedia: <span id="stokValue" class="font-semibold"></span></p>
            </div>

            {{-- Qty --}}
            <div>
                <label for="qty" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Quantity <span class="text-red-500">*</span>
                </label>
                <input type="number" id="qty" name="qty" value="{{ old('qty', 1) }}" min="1" required
                       class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                @error('qty') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Tanggal Transaksi --}}
            <div>
                <label for="tanggal_transaksi" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Tanggal & Waktu <span class="text-red-500">*</span>
                </label>
                <input type="text" id="tanggal_transaksi" name="tanggal_transaksi"
                       value="{{ old('tanggal_transaksi') }}" required readonly="readonly"
                       class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                @error('tanggal_transaksi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Divisi (visible only if tipe_transaksi = keluar) --}}
            <div id="divisiWrapper">
                <label for="divisi_select" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Divisi Tukang <span class="text-red-500" id="divisiRequired">*</span>
                </label>
                <select id="divisi_select" name="nama_divisi" autocomplete="off" placeholder="Pilih atau ketik nama divisi/tukang baru...">
                    <option value="">Pilih divisi...</option>
                    @foreach ($divisis as $divisi)
                        <option value="{{ $divisi->nama_divisi_tukang }}" @selected(old('nama_divisi') == $divisi->nama_divisi_tukang)>
                            {{ $divisi->nama_divisi_tukang }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-400">Ketik nama baru jika divisi belum terdaftar, otomatis tersimpan.</p>
                @error('nama_divisi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Keterangan --}}
            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="3" maxlength="500"
                          class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                          placeholder="Opsional...">{{ old('keterangan') }}</textarea>
                @error('keterangan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    Simpan Transaksi
                </button>
                <a href="{{ route('transactions.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">Batal</a>
            </div>
        </div>
    </form>

    <script>
        const tipeSelect = document.getElementById('tipe_transaksi');
        const divisiWrapper = document.getElementById('divisiWrapper');
        const divisiRequired = document.getElementById('divisiRequired');
        const itemSelect = document.getElementById('item_id');
        const stokInfo = document.getElementById('stokInfo');
        const stokValue = document.getElementById('stokValue');

        // ── Tom Select init ──────────────────────────────────────────
        const divisiTomSelect = new TomSelect('#divisi_select', {
            create: true,                        // izinkan ketik manual (divisi baru)
            createOnBlur: true,                   // simpan saat klik di luar
            maxItems: 1,
            persist: false,
            sortField: { field: 'text', direction: 'asc' },
            render: {
                option_create: function(data, escape) {
                    return '<div class="create">Tambah baru: <strong>' + escape(data.input) + '</strong></div>';
                },
                no_results: function() {
                    return '<div class="no-results" style="padding:0.5rem 1rem;font-size:0.875rem;color:#9ca3af;">Tidak ditemukan — ketik untuk menambahkan</div>';
                }
            }
        });

        function toggleDivisi() {
            const isKeluar = tipeSelect.value === 'keluar';
            divisiWrapper.style.display = '';
            divisiRequired.textContent = isKeluar ? '*' : '';

            // Tom Select tidak punya properti `required` langsung;
            // kita atur pada <select> asli agar validasi HTML5 tetap bekerja.
            const originalSelect = document.querySelector('#divisi_select');
            if (isKeluar) {
                originalSelect.setAttribute('required', 'required');
            } else {
                originalSelect.removeAttribute('required');
            }
        }

        function showStok() {
            const selected = itemSelect.options[itemSelect.selectedIndex];
            if (selected && selected.dataset.stok !== undefined && itemSelect.value) {
                stokInfo.classList.remove('hidden');
                stokValue.textContent = selected.dataset.stok;
            } else {
                stokInfo.classList.add('hidden');
            }
        }

        tipeSelect.addEventListener('change', toggleDivisi);
        itemSelect.addEventListener('change', showStok);

        // Init
        toggleDivisi();
        showStok();

        // Flatpickr — Tanggal & Waktu 24 jam
        flatpickr('#tanggal_transaksi', {
            enableTime: true,
            time_24hr: true,
            dateFormat: 'Y-m-d H:i',
            defaultDate: "{{ old('tanggal_transaksi', \Carbon\Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d H:i')) }}",
        });
    </script>
@endsection
