@extends('layouts.app')

@section('title', 'Buat Purchase Order')

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
        .ts-wrapper.single .ts-control input {
            font-size: 0.875rem;
            color: rgb(17 24 39);
        }
        .ts-wrapper.single .ts-control input::placeholder {
            color: rgb(156 163 175);
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
            background-color: rgb(239 246 255);
            color: rgb(29 78 216);
        }
        .ts-dropdown .create {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            color: rgb(37 99 235);
        }
    </style>

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('purchase-orders.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition mb-2">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
            Kembali ke Daftar PO
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Buat Purchase Order Baru</h1>
        <p class="text-sm text-gray-500 mt-1">Isi nomor PO secara manual, contoh: 012/KB/MID/II/2026</p>
    </div>

    <form method="POST" action="{{ route('purchase-orders.store') }}" id="poForm" class="max-w-5xl space-y-6">
        @csrf

        {{-- PO Header --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi PO</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Vendor --}}
                <div>
                    <label for="vendor_select" class="block text-sm font-medium text-gray-700 mb-1.5">Vendor <span class="text-red-500">*</span></label>
                    <select id="vendor_select" name="nama_vendor" required autocomplete="off" placeholder="Pilih atau ketik nama vendor baru...">
                        <option value="">Pilih vendor...</option>
                        @foreach ($vendors as $vendor)
                            <option value="{{ $vendor->nama_vendor }}" @selected(old('nama_vendor') == $vendor->nama_vendor)>
                                {{ $vendor->nama_vendor }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-400">Ketik nama baru jika vendor belum terdaftar, otomatis tersimpan.</p>
                    @error('nama_vendor') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Detail Vendor Baru (hidden by default) --}}
                <div id="form_vendor_baru" class="hidden md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-5 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                    <div>
                        <label for="kontak_vendor" class="block text-sm font-medium text-gray-700 mb-1.5">Kontak Vendor</label>
                        <input type="text" id="kontak_vendor" name="kontak_vendor" value="{{ old('kontak_vendor') }}" placeholder="No. HP / Telepon"
                               class="block w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                        @error('kontak_vendor') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="alamat_vendor" class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Vendor</label>
                        <input type="text" id="alamat_vendor" name="alamat_vendor" value="{{ old('alamat_vendor') }}" placeholder="Alamat lengkap vendor"
                               class="block w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                        @error('alamat_vendor') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <p class="md:col-span-2 text-xs text-amber-600">⚠ Vendor baru terdeteksi — silakan lengkapi kontak & alamat.</p>
                </div>

                {{-- Nomor PO (Manual) --}}
                <div>
                    <label for="nomor_po" class="block text-sm font-medium text-gray-700 mb-1.5">Nomor PO <span class="text-red-500">*</span></label>
                    <input type="text" id="nomor_po" name="nomor_po" value="{{ old('nomor_po') }}" required
                           placeholder="Contoh format: 012/KB/MID/II/2026"
                           class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                    <p class="mt-1 text-xs text-gray-400">Isi nomor PO secara manual. Harus unik (tidak boleh sama dengan PO lain).</p>
                    @error('nomor_po') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- No. Invoice --}}
                <div>
                    <label for="no_invoice" class="block text-sm font-medium text-gray-700 mb-1.5">No. Invoice</label>
                    <input type="text" id="no_invoice" name="no_invoice" value="{{ old('no_invoice') }}"
                           placeholder="Nomor invoice dari vendor (opsional)"
                           class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                    @error('no_invoice') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Tanggal PO --}}
                <div>
                    <label for="tanggal_po" class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal PO <span class="text-red-500">*</span></label>
                    <input type="date" id="tanggal_po" name="tanggal_po" value="{{ old('tanggal_po', date('Y-m-d')) }}" required
                           class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                    @error('tanggal_po') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Tipe Pembelian --}}
                <div>
                    <label for="tipe_po" class="block text-sm font-medium text-gray-700 mb-1.5">Tipe Pembelian <span class="text-red-500">*</span></label>
                    <select id="tipe_po" name="tipe_po" required
                            class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="Ready Stock" {{ old('tipe_po', 'Ready Stock') === 'Ready Stock' ? 'selected' : '' }}>Ready Stock</option>
                        <option value="Pre-Order" {{ old('tipe_po') === 'Pre-Order' ? 'selected' : '' }}>Pre-Order</option>
                    </select>
                    @error('tipe_po') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Estimasi Barang Sampai (hanya Pre-Order) --}}
                <div id="estimasiWrapper" class="hidden">
                    <label for="estimasi_sampai" class="block text-sm font-medium text-gray-700 mb-1.5">Estimasi Barang Sampai</label>
                    <input type="date" id="estimasi_sampai" name="estimasi_sampai" value="{{ old('estimasi_sampai') }}"
                           class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                    <span id="info_waktu_tunggu" class="text-xs text-blue-600 mt-1 block"></span>
                    @error('estimasi_sampai') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Status Pembayaran --}}
                <div>
                    <label for="status_pembayaran" class="block text-sm font-medium text-gray-700 mb-1.5">Status Pembayaran <span class="text-red-500">*</span></label>
                    <select id="status_pembayaran" name="status_pembayaran" required
                            class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="Lunas" {{ old('status_pembayaran') === 'Lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="DP" {{ old('status_pembayaran') === 'DP' ? 'selected' : '' }}>DP</option>
                        <option value="Tempo" {{ old('status_pembayaran') === 'Tempo' ? 'selected' : '' }}>Tempo/Non-DP</option>
                    </select>
                    @error('status_pembayaran') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Tanggal Jatuh Tempo --}}
                <div>
                    <label for="tanggal_jatuh_tempo" class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Jatuh Tempo</label>
                    <input type="date" id="tanggal_jatuh_tempo" name="tanggal_jatuh_tempo" value="{{ old('tanggal_jatuh_tempo') }}"
                           class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition disabled:opacity-50 disabled:cursor-not-allowed" />
                    <span id="help_jatuh_tempo" class="text-xs text-red-500 hidden">Wajib diisi karena status DP atau Tempo.</span>
                    @error('tanggal_jatuh_tempo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Line Items --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Detail Barang</h2>
                <button type="button" id="addRowBtn"
                        class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-3 py-2 rounded-lg transition">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Tambah Baris
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="itemsTable">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase w-1/4">Barang</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase w-28">Merk</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase w-28">Satuan</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase w-20">Qty</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase w-36">Harga Satuan</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase w-36">Subtotal</th>
                            <th class="px-3 py-2 w-12"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        {{-- Row template will be inserted by JS --}}
                    </tbody>
                </table>
            </div>

            @error('items') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Summary --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan</h2>
            <div class="max-w-sm ml-auto space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Total Harga</span>
                    <span class="font-semibold text-gray-900" id="displayTotal">Rp 0</span>
                </div>
                <div class="flex justify-between text-sm items-center gap-4">
                    <label for="diskon" class="text-gray-500 whitespace-nowrap">Diskon (Rp)</label>
                    <input type="number" id="diskon" name="diskon" value="{{ old('diskon', 0) }}" min="0" step="1"
                           class="w-40 px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-right text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                </div>
                <div class="flex justify-between text-base font-bold border-t border-gray-200 pt-3">
                    <span class="text-gray-700">Grand Total</span>
                    <span class="text-blue-700" id="displayGrand">Rp 0</span>
                </div>

                {{-- Nominal DP (hanya muncul jika status = DP) --}}
                <div id="row_dp" class="hidden flex justify-between text-sm items-center gap-4 border-t border-dashed border-gray-200 pt-3">
                    <label for="nominal_dp" class="text-gray-500 whitespace-nowrap">Nominal DP (Rp)</label>
                    <input type="number" id="nominal_dp" name="nominal_dp" value="{{ old('nominal_dp', 0) }}" min="0" step="1"
                           class="w-40 px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-right text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                </div>

                {{-- Sisa Tagihan --}}
                <div class="flex justify-between text-base font-bold border-t border-gray-200 pt-3">
                    <span class="text-gray-700">Sisa Tagihan</span>
                    <span class="text-red-600" id="text_sisa_tagihan">Rp 0</span>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                Simpan Purchase Order
            </button>
            <a href="{{ route('purchase-orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">Batal</a>
        </div>
    </form>

    <script>
        // ── Tom Select — Vendor (creatable) ──────────────────────────
        const existingVendors = @json($vendors->pluck('nama_vendor'));

        const vendorTomSelect = new TomSelect('#vendor_select', {
            create: true,
            createOnBlur: true,
            maxItems: 1,
            persist: false,
            sortField: { field: 'text', direction: 'asc' },
            render: {
                option_create: function(data, escape) {
                    return '<div class="create">Tambah baru: <strong>' + escape(data.input) + '</strong></div>';
                },
                no_results: function() {
                    return '<div style="padding:0.5rem 1rem;font-size:0.875rem;color:#9ca3af;">Tidak ditemukan — ketik untuk menambahkan</div>';
                }
            }
        });

        // ── Toggle Form Vendor Baru ──────────────────────────────────
        const formVendorBaru = document.getElementById('form_vendor_baru');
        const kontakVendor   = document.getElementById('kontak_vendor');
        const alamatVendor   = document.getElementById('alamat_vendor');

        vendorTomSelect.on('change', function(value) {
            if (value && !existingVendors.includes(value)) {
                formVendorBaru.classList.remove('hidden');
            } else {
                formVendorBaru.classList.add('hidden');
                kontakVendor.value = '';
                alamatVendor.value = '';
            }
        });

        // ── Toggle Tanggal Jatuh Tempo berdasarkan Status Pembayaran ─
        const statusPembayaran   = document.getElementById('status_pembayaran');
        const tanggalJatuhTempo  = document.getElementById('tanggal_jatuh_tempo');
        const helpJatuhTempo     = document.getElementById('help_jatuh_tempo');

        function toggleJatuhTempo() {
            const val = statusPembayaran.value;
            if (val === 'DP' || val === 'Tempo') {
                tanggalJatuhTempo.disabled = false;
                tanggalJatuhTempo.required = true;
                helpJatuhTempo.classList.remove('hidden');
            } else {
                tanggalJatuhTempo.disabled = true;
                tanggalJatuhTempo.required = false;
                tanggalJatuhTempo.value    = '';
                helpJatuhTempo.classList.add('hidden');
            }
        }

        statusPembayaran.addEventListener('change', toggleJatuhTempo);
        toggleJatuhTempo();

        // ── Toggle Tipe PO (Ready Stock / Pre-Order) ─────────────────
        const tipePo          = document.getElementById('tipe_po');
        const estimasiWrapper = document.getElementById('estimasiWrapper');
        const estimasiInput   = document.getElementById('estimasi_sampai');
        const infoWaktuTunggu = document.getElementById('info_waktu_tunggu');
        const tanggalPoInput  = document.getElementById('tanggal_po');

        function toggleTipePo() {
            if (tipePo.value === 'Pre-Order') {
                estimasiWrapper.classList.remove('hidden');
            } else {
                estimasiWrapper.classList.add('hidden');
                estimasiInput.value = '';
                infoWaktuTunggu.textContent = '';
            }
        }

        function hitungWaktuTunggu() {
            if (!estimasiInput.value || !tanggalPoInput.value) {
                infoWaktuTunggu.textContent = '';
                return;
            }
            const tglPo = new Date(tanggalPoInput.value);
            const tglEst = new Date(estimasiInput.value);
            const selisih = Math.ceil((tglEst - tglPo) / (1000 * 60 * 60 * 24));

            if (selisih > 0) {
                infoWaktuTunggu.textContent = '⏱ Waktu Tunggu: ' + selisih + ' Hari';
            } else if (selisih === 0) {
                infoWaktuTunggu.textContent = '⏱ Waktu Tunggu: Hari ini';
            } else {
                infoWaktuTunggu.textContent = '⚠ Estimasi sampai sebelum tanggal PO!';
            }
        }

        tipePo.addEventListener('change', toggleTipePo);
        estimasiInput.addEventListener('change', hitungWaktuTunggu);
        tanggalPoInput.addEventListener('change', hitungWaktuTunggu);
        toggleTipePo();

        // ── Item & UOM data from server ──────────────────────────────
        const items = @json($items);
        const uoms  = @json($uoms);

        let rowIndex = 0;

        function createRow() {
            const idx = rowIndex++;
            const tr = document.createElement('tr');
            tr.className = 'border-b border-gray-100 item-row';
            tr.dataset.index = idx;

            // Barang options (existing + creatable via Tom Select)
            let itemOptions = '<option value="">— Pilih / Ketik Barang Baru —</option>';
            items.forEach(item => {
                itemOptions += `<option value="${item.id}">${item.kode_barang} — ${item.nama_barang} (${item.uom.nama_satuan})</option>`;
            });

            // UOM options (by nama_satuan for firstOrCreate)
            let uomOptions = '<option value="">— Satuan —</option>';
            uoms.forEach(uom => {
                uomOptions += `<option value="${uom.nama_satuan}">${uom.nama_satuan}</option>`;
            });

            tr.innerHTML = `
                <td class="px-3 py-2">
                    <select name="items[${idx}][item_id]" required class="item-select w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        ${itemOptions}
                    </select>
                </td>
                <td class="px-3 py-2">
                    <input type="text" name="items[${idx}][merk]" class="merk-input w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Merk (opsional)" />
                </td>
                <td class="px-3 py-2">
                    <select name="items[${idx}][nama_uom]" required class="uom-select w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        ${uomOptions}
                    </select>
                </td>
                <td class="px-3 py-2">
                    <input type="number" name="items[${idx}][qty]" min="1" value="1" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm text-right focus:ring-2 focus:ring-blue-500 focus:border-blue-500 qty-input" />
                </td>
                <td class="px-3 py-2">
                    <input type="number" name="items[${idx}][harga_satuan]" min="0" step="1" value="0" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm text-right focus:ring-2 focus:ring-blue-500 focus:border-blue-500 harga-input" />
                </td>
                <td class="px-3 py-2 text-right font-semibold text-gray-900 subtotal-display">Rp 0</td>
                <td class="px-3 py-2 text-center">
                    <button type="button" class="remove-row text-red-500 hover:text-red-700 transition" title="Hapus baris">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </button>
                </td>
            `;

            // Init Tom Select pada item select (creatable — barang baru)
            const itemSel = tr.querySelector('.item-select');
            const uomSel  = tr.querySelector('.uom-select');
            const merkInp = tr.querySelector('.merk-input');

            const tsItem = new TomSelect(itemSel, {
                create: true,
                createOnBlur: true,
                maxItems: 1,
                persist: false,
                sortField: { field: 'text', direction: 'asc' },
                render: {
                    option_create: function(data, escape) {
                        return '<div class="create">+ Barang baru: <strong>' + escape(data.input) + '</strong></div>';
                    },
                    no_results: function() {
                        return '<div style="padding:0.5rem 1rem;font-size:0.875rem;color:#9ca3af;">Tidak ditemukan — ketik untuk menambahkan</div>';
                    }
                }
            });

            // Init Tom Select pada UOM select (creatable — satuan baru)
            const tsUom = new TomSelect(uomSel, {
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

            // Auto-fill UOM & Merk saat pilih barang existing
            tsItem.on('change', function(value) {
                const found = items.find(i => i.id == value);
                if (found) {
                    tsUom.setValue(found.uom.nama_satuan, true);
                    tsUom.disable();
                    merkInp.value = found.merk || '';
                    merkInp.readOnly = true;
                    merkInp.classList.add('opacity-50');
                } else {
                    // Barang baru — user harus pilih UOM & isi merk manual
                    tsUom.enable();
                    tsUom.clear(true);
                    merkInp.value = '';
                    merkInp.readOnly = false;
                    merkInp.classList.remove('opacity-50');
                }
            });

            return tr;
        }

        function recalculate() {
            calculateTotal();
        }

        function calculateTotal() {
            // 1. Hitung total subtotal dari semua row
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const qty = parseInt(row.querySelector('.qty-input').value) || 0;
                const harga = parseFloat(row.querySelector('.harga-input').value) || 0;
                const subtotal = qty * harga;
                row.querySelector('.subtotal-display').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
                total += subtotal;
            });

            // 2. Grand Total = Total - Diskon
            const diskon = parseFloat(document.getElementById('diskon').value) || 0;
            const grandTotal = Math.max(total - diskon, 0);

            document.getElementById('displayTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
            document.getElementById('displayGrand').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');

            // 3. Logika DP / Sisa Tagihan berdasarkan Status Pembayaran
            const status      = statusPembayaran.value;
            const rowDp       = document.getElementById('row_dp');
            const nominalDp   = document.getElementById('nominal_dp');
            const sisaTagihan = document.getElementById('text_sisa_tagihan');

            let sisa = 0;

            if (status === 'DP') {
                // Tampilkan row DP, hitung sisa
                rowDp.classList.remove('hidden');
                const dpVal = parseFloat(nominalDp.value) || 0;
                sisa = Math.max(grandTotal - dpVal, 0);
            } else if (status === 'Lunas') {
                // Lunas → DP = grand total, sisa = 0
                rowDp.classList.add('hidden');
                nominalDp.value = 0;
                sisa = 0;
            } else {
                // Tempo → DP = 0, sisa = grand total
                rowDp.classList.add('hidden');
                nominalDp.value = 0;
                sisa = grandTotal;
            }

            // Update tampilan Sisa Tagihan
            sisaTagihan.textContent = 'Rp ' + sisa.toLocaleString('id-ID');

            // Warna dinamis
            if (sisa > 0) {
                sisaTagihan.className = 'text-red-600';
            } else {
                sisaTagihan.className = 'text-green-600';
            }
        }

        // Event delegation for dynamic rows
        const tbody = document.getElementById('itemsBody');

        tbody.addEventListener('input', (e) => {
            if (e.target.classList.contains('qty-input') || e.target.classList.contains('harga-input')) {
                calculateTotal();
            }
        });

        tbody.addEventListener('click', (e) => {
            const btn = e.target.closest('.remove-row');
            if (btn) {
                btn.closest('tr').remove();
                calculateTotal();
            }
        });

        document.getElementById('diskon').addEventListener('input', calculateTotal);
        document.getElementById('nominal_dp').addEventListener('input', calculateTotal);
        statusPembayaran.addEventListener('change', calculateTotal);

        document.getElementById('addRowBtn').addEventListener('click', () => {
            tbody.appendChild(createRow());
            calculateTotal();
        });

        // Re-enable disabled Tom Select UOM before form submit
        document.getElementById('poForm').addEventListener('submit', function() {
            document.querySelectorAll('.item-row').forEach(row => {
                const uomTs = row.querySelector('.uom-select');
                if (uomTs && uomTs.tomselect) {
                    uomTs.tomselect.enable();
                }
            });
        });

        // Initialize with 1 row
        tbody.appendChild(createRow());
        calculateTotal();
    </script>
@endsection
