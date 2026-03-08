<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Uom;
use App\Exports\ItemsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource (with filters).
     */
    public function index(Request $request): View
    {
        $items = Item::with('uom')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('kode_barang', 'like', "%{$request->search}%")
                        ->orWhere('nama_barang', 'like', "%{$request->search}%")
                        ->orWhere('merk', 'like', "%{$request->search}%");
                });
            })
            ->when($request->filled('uom_id'), fn($q) => $q->where('uom_id', $request->uom_id))
            ->when($request->filled('stok_filter'), function ($q) use ($request) {
                match ($request->stok_filter) {
                    'kosong'  => $q->where('stok', 0),
                    'tersedia' => $q->where('stok', '>', 0),
                    default   => null,
                };
            })
            ->orderBy('kode_barang', 'asc')
            ->get();

        $uoms = Uom::orderBy('nama_satuan')->get();

        return view('items.index', compact('items', 'uoms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $uoms = Uom::orderBy('nama_satuan')->get();

        return view('items.create', compact('uoms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_barang' => ['required', 'string', 'max:255'],
            'merk'        => ['nullable', 'string', 'max:255'],
            'nama_uom'    => ['required', 'string', 'max:255'],
            'stok'        => ['nullable', 'numeric', 'min:0'],
        ]);

        // firstOrCreate UOM — satuan baru otomatis tersimpan
        $uom = Uom::firstOrCreate(['nama_satuan' => $request->nama_uom]);

        // Auto-generate kode_barang: MID-0001, MID-0002, ...
        $lastItem = Item::orderBy('id', 'desc')->first();
        $nextNumber = $lastItem ? ((int) substr($lastItem->kode_barang, 4)) + 1 : 1;
        $kodeBarang = 'MID-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        Item::create([
            'kode_barang' => $kodeBarang,
            'nama_barang' => $validated['nama_barang'],
            'merk'        => $validated['merk'],
            'uom_id'      => $uom->id,
            'stok'        => $validated['stok'] ?? 0,
        ]);

        return redirect()->route('items.index')
            ->with('success', "Barang [{$kodeBarang}] berhasil ditambahkan.");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item): View
    {
        $uoms = Uom::orderBy('nama_satuan')->get();

        return view('items.edit', compact('item', 'uoms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'nama_barang' => ['required', 'string', 'max:255'],
            'merk'        => ['nullable', 'string', 'max:255'],
            'uom_id'      => ['required', 'exists:uoms,id'],
            'stok'        => ['required', 'numeric', 'min:0'],
        ]);

        $item->update($validated);

        return redirect()->route('items.index')
            ->with('success', "Barang [{$item->kode_barang}] berhasil diperbarui.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item): RedirectResponse
    {
        try {
            $item->delete();

            return redirect()->route('items.index')
                ->with('success', 'Barang berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()->back()
                    ->with('error', 'Barang gagal dihapus karena sedang digunakan dalam riwayat transaksi atau purchase order!');
            }

            throw $e;
        }
    }

    /**
     * Export data barang ke Excel.
     */
    public function exportExcel()
    {
        return Excel::download(new ItemsExport, 'Data_Stok_Barang.xlsx');
    }

    /**
     * Export data barang ke PDF (with active filters).
     */
    public function exportPdf(Request $request)
    {
        $items = Item::with('uom')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('kode_barang', 'like', "%{$request->search}%")
                        ->orWhere('nama_barang', 'like', "%{$request->search}%")
                        ->orWhere('merk', 'like', "%{$request->search}%");
                });
            })
            ->when($request->filled('uom_id'), fn($q) => $q->where('uom_id', $request->uom_id))
            ->when($request->filled('stok_filter'), function ($q) use ($request) {
                match ($request->stok_filter) {
                    'kosong'  => $q->where('stok', 0),
                    'tersedia' => $q->where('stok', '>', 0),
                    default   => null,
                };
            })
            ->orderBy('kode_barang', 'asc')
            ->get();

        $pdf = Pdf::loadView('items.pdf', compact('items'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('Data_Stok_Barang.pdf');
    }

    /**
     * Print QR Code sticker for the specified item.
     */
    public function printQr(Item $item): View
    {
        return view('items.print-qr', compact('item'));
    }
}
