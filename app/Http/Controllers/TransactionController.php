<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\Item;
use App\Models\Transaction;
use App\Exports\TransactionsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions (with filters).
     */
    public function index(Request $request): View
    {
        $transactions = Transaction::with(['item.uom', 'divisi', 'user'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->whereHas('item', fn($i) => $i->where('nama_barang', 'like', "%{$request->search}%"))
                        ->orWhereHas('divisi', fn($d) => $d->where('nama_divisi_tukang', 'like', "%{$request->search}%"))
                        ->orWhere('keterangan', 'like', "%{$request->search}%");
                });
            })
            ->when($request->filled('tipe_transaksi'), fn($q) => $q->where('tipe_transaksi', $request->tipe_transaksi))
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal_transaksi', $request->bulan))
            ->when($request->filled('tahun'), fn($q) => $q->whereYear('tanggal_transaksi', $request->tahun))
            ->orderBy('id', 'asc')
            ->get();

        return view('transactions.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create(): View
    {
        $items   = Item::with('uom')->orderBy('nama_barang')->get();
        $divisis = Divisi::orderBy('nama_divisi_tukang')->get();

        return view('transactions.create', compact('items', 'divisis'));
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tipe_transaksi'    => ['required', 'in:keluar,kembali_sisa,retur_cacat'],
            'item_id'           => ['required', 'exists:items,id'],
            'qty'               => ['required', 'integer', 'min:1'],
            'tanggal_transaksi' => ['required', 'date'],
            'nama_divisi'       => ['nullable', 'string', 'max:255'],
            'keterangan'        => ['nullable', 'string', 'max:500'],
        ]);

        // Divisi wajib jika tipe = keluar
        if ($request->tipe_transaksi === 'keluar' && ! $request->filled('nama_divisi')) {
            return back()->withErrors(['nama_divisi' => 'Divisi wajib diisi untuk transaksi keluar.'])->withInput();
        }

        // Cek stok cukup untuk keluar & retur_cacat
        if (in_array($request->tipe_transaksi, ['keluar', 'retur_cacat'])) {
            $item = Item::findOrFail($request->item_id);
            if ($item->stok < $request->qty) {
                return back()->withErrors(['qty' => "Stok tidak cukup. Stok tersedia: {$item->stok}"])->withInput();
            }
        }

        // Resolve divisi: cari yang sudah ada atau buat baru otomatis
        $divisiId = null;
        if ($request->filled('nama_divisi')) {
            $divisi = Divisi::firstOrCreate(
                ['nama_divisi_tukang' => $request->nama_divisi]
            );
            $divisiId = $divisi->id;
        }

        // Hapus nama_divisi dari validated (bukan kolom di tabel transactions)
        unset($validated['nama_divisi']);
        $validated['divisi_id'] = $divisiId;
        $validated['user_id']   = auth()->id();

        // Observer will auto-adjust item stock
        Transaction::create($validated);

        $tipeLabel = match ($request->tipe_transaksi) {
            'keluar'       => 'Barang Keluar',
            'kembali_sisa' => 'Kembali Sisa',
            'retur_cacat'  => 'Retur Cacat',
        };

        return redirect()->route('transactions.index')
            ->with('success', "Transaksi [{$tipeLabel}] berhasil dicatat.");
    }

    /**
     * Export riwayat transaksi ke Excel.
     */
    public function exportExcel()
    {
        return Excel::download(new TransactionsExport, 'Riwayat_Transaksi_Gudang.xlsx');
    }

    /**
     * Export riwayat transaksi ke PDF (with active filters).
     */
    public function exportPdf(Request $request)
    {
        $transactions = Transaction::with(['item.uom', 'divisi', 'user'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->whereHas('item', fn($i) => $i->where('nama_barang', 'like', "%{$request->search}%"))
                        ->orWhereHas('divisi', fn($d) => $d->where('nama_divisi_tukang', 'like', "%{$request->search}%"))
                        ->orWhere('keterangan', 'like', "%{$request->search}%");
                });
            })
            ->when($request->filled('tipe_transaksi'), fn($q) => $q->where('tipe_transaksi', $request->tipe_transaksi))
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal_transaksi', $request->bulan))
            ->when($request->filled('tahun'), fn($q) => $q->whereYear('tanggal_transaksi', $request->tahun))
            ->orderBy('id', 'asc')
            ->get();

        $pdf = Pdf::loadView('transactions.pdf', compact('transactions'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Riwayat_Transaksi_Gudang.pdf');
    }
}
