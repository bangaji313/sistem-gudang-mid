<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\Uom;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of purchase orders (with filters).
     */
    public function index(Request $request): View
    {
        $purchaseOrders = PurchaseOrder::with(['vendor', 'user'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('nomor_po', 'like', "%{$request->search}%")
                        ->orWhereHas('vendor', fn($v) => $v->where('nama_vendor', 'like', "%{$request->search}%"));
                });
            })
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal_po', $request->bulan))
            ->when($request->filled('tahun'), fn($q) => $q->whereYear('tanggal_po', $request->tahun))
            ->when($request->filled('tipe_po'), fn($q) => $q->where('tipe_po', $request->tipe_po))
            ->when($request->filled('status_pembayaran'), fn($q) => $q->where('status_pembayaran', $request->status_pembayaran))
            ->when($request->filled('status_pesanan'), fn($q) => $q->where('status_pesanan', $request->status_pesanan))
            ->orderBy('id', 'asc')
            ->get();

        return view('purchase_orders.index', compact('purchaseOrders'));
    }

    /**
     * Show the form for creating a new purchase order.
     */
    public function create(): View
    {
        $vendors = Vendor::orderBy('nama_vendor')->get();
        $items   = Item::with('uom')->orderBy('nama_barang')->get();
        $uoms    = Uom::orderBy('nama_satuan')->get();

        return view('purchase_orders.create', compact('vendors', 'items', 'uoms'));
    }

    /**
     * Store a newly created purchase order in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_vendor'          => ['required', 'string', 'max:255'],
            'tanggal_po'           => ['required', 'date'],
            'tipe_po'              => ['required', 'in:Ready Stock,Pre-Order'],
            'estimasi_sampai'      => ['nullable', 'date', 'after_or_equal:tanggal_po'],
            'status_pembayaran'    => ['required', 'in:Lunas,DP,Tempo'],
            'tanggal_jatuh_tempo'  => ['required_if:status_pembayaran,DP,Tempo', 'nullable', 'date', 'after_or_equal:tanggal_po'],
            'nominal_dp'           => ['nullable', 'numeric', 'min:0'],
            'diskon'               => ['nullable', 'numeric', 'min:0'],
            'items'                => ['required', 'array', 'min:1'],
            'items.*.item_id'      => ['required'],
            'items.*.qty'          => ['required', 'integer', 'min:1'],
            'items.*.harga_satuan' => ['required', 'numeric', 'min:0'],
            'items.*.uom_id'       => ['nullable'],
            'items.*.nama_uom'     => ['required', 'string', 'max:255'],
            'items.*.merk'         => ['nullable', 'string', 'max:255'],
        ], [
            'tanggal_jatuh_tempo.required_if'    => 'Tanggal jatuh tempo wajib diisi jika status pembayaran DP atau Tempo.',
            'tanggal_jatuh_tempo.after_or_equal'  => 'Tanggal jatuh tempo tidak boleh sebelum tanggal PO.',
            'estimasi_sampai.after_or_equal'      => 'Estimasi sampai tidak boleh sebelum tanggal PO.',
        ]);

        // Auto-generate nomor_po: PO-20260305-001
        $today  = now()->format('Ymd');
        $prefix = "PO-{$today}-";
        $lastPo = PurchaseOrder::where('nomor_po', 'like', "{$prefix}%")
            ->orderBy('nomor_po', 'desc')
            ->first();

        $nextSeq = $lastPo
            ? ((int) substr($lastPo->nomor_po, -3)) + 1
            : 1;

        $nomorPo = $prefix . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);

        // Calculate totals
        $totalHarga = 0;
        foreach ($request->items as $line) {
            $totalHarga += $line['qty'] * $line['harga_satuan'];
        }

        $diskon     = $request->diskon ?? 0;
        $grandTotal = $totalHarga - $diskon;

        // Kalkulasi DP & Sisa Pembayaran di backend (tidak bergantung frontend)
        $nominalDp       = 0;
        $sisaPembayaran  = 0;

        switch ($request->status_pembayaran) {
            case 'Lunas':
                $nominalDp      = $grandTotal;
                $sisaPembayaran = 0;
                break;
            case 'DP':
                $nominalDp      = min($request->nominal_dp ?? 0, $grandTotal);
                $sisaPembayaran = $grandTotal - $nominalDp;
                break;
            case 'Tempo':
                $nominalDp      = 0;
                $sisaPembayaran = $grandTotal;
                break;
        }

        // Tentukan status_pesanan berdasarkan tipe_po
        $statusPesanan = $request->tipe_po === 'Pre-Order' ? 'Menunggu Kedatangan' : 'Selesai';

        DB::transaction(function () use ($request, $nomorPo, $totalHarga, $diskon, $grandTotal, $nominalDp, $sisaPembayaran, $statusPesanan) {
            // firstOrCreate: jika vendor baru, simpan beserta kontak & alamat
            $vendor = Vendor::firstOrCreate(
                ['nama_vendor' => $request->nama_vendor],
                [
                    'kontak' => $request->kontak_vendor,
                    'alamat' => $request->alamat_vendor,
                ]
            );

            $po = PurchaseOrder::create([
                'nomor_po'            => $nomorPo,
                'vendor_id'           => $vendor->id,
                'tanggal_po'          => $request->tanggal_po,
                'tipe_po'             => $request->tipe_po,
                'estimasi_sampai'     => $request->estimasi_sampai,
                'status_pesanan'      => $statusPesanan,
                'status_pembayaran'   => $request->status_pembayaran,
                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'total_harga'         => $totalHarga,
                'diskon'              => $diskon,
                'grand_total'         => $grandTotal,
                'nominal_dp'          => $nominalDp,
                'sisa_pembayaran'     => $sisaPembayaran,
                'user_id'             => auth()->id(),
            ]);

            foreach ($request->items as $line) {
                $subtotal = $line['qty'] * $line['harga_satuan'];

                // firstOrCreate UOM berdasarkan nama_satuan
                $uom = \App\Models\Uom::firstOrCreate(
                    ['nama_satuan' => $line['nama_uom']]
                );

                // Jika item_id berupa string (barang baru), firstOrCreate
                if (!is_numeric($line['item_id'])) {
                    $newItem = Item::firstOrCreate(
                        ['nama_barang' => $line['item_id']],
                        [
                            'uom_id' => $uom->id,
                            'merk'   => $line['merk'] ?? '-',
                            'stok'   => 0,
                        ]
                    );
                    $itemId = $newItem->id;
                } else {
                    $itemId = $line['item_id'];
                }

                // Observer will auto-increment item stock (only if Ready Stock)
                $po->details()->create([
                    'item_id'      => $itemId,
                    'qty'          => $line['qty'],
                    'harga_satuan' => $line['harga_satuan'],
                    'subtotal'     => $subtotal,
                ]);
            }
        });

        return redirect()->route('purchase-orders.index')
            ->with('success', "Purchase Order [{$nomorPo}] berhasil dibuat.");
    }

    /**
     * Konfirmasi barang Pre-Order telah sampai.
     * Update status_pesanan → Selesai, lalu tambah stok per detail.
     */
    public function markAsArrived(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        if ($purchaseOrder->status_pesanan === 'Selesai') {
            return back()->with('info', 'Pesanan ini sudah berstatus Selesai.');
        }

        DB::transaction(function () use ($purchaseOrder) {
            $purchaseOrder->update(['status_pesanan' => 'Selesai']);

            foreach ($purchaseOrder->details as $detail) {
                $detail->item()->increment('stok', $detail->qty);
            }
        });

        // ── Kirim notifikasi Telegram ───────────────────────────
        try {
            $token  = env('TELEGRAM_BOT_TOKEN');
            $chatId = env('TELEGRAM_CHAT_ID');

            if ($token && $chatId) {
                $po      = $purchaseOrder->load('vendor', 'details.item');
                $details = $po->details->map(function ($d) {
                    $uom = $d->item->uom->singkatan ?? 'pcs';
                    return "   • {$d->item->nama_barang} — {$d->qty} {$uom}";
                })->implode("\n");

                $message = "✅ <b>BARANG PO TELAH SAMPAI</b>\n\n"
                    . "📦 <b>No. PO:</b> {$po->nomor_po}\n"
                    . "🏢 <b>Vendor:</b> {$po->vendor->nama_vendor}\n"
                    . "📅 <b>Tanggal PO:</b> {$po->tanggal_po->format('d/m/Y')}\n"
                    . "💰 <b>Grand Total:</b> Rp " . number_format($po->grand_total, 0, ',', '.') . "\n"
                    . "💳 <b>Status Bayar:</b> {$po->status_pembayaran}\n\n"
                    . "📋 <b>Detail Barang:</b>\n{$details}\n\n"
                    . "🕐 <i>Dikonfirmasi oleh: " . auth()->user()->nama . " pada " . now()->format('d/m/Y H:i') . "</i>";

                Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id'    => $chatId,
                    'text'       => $message,
                    'parse_mode' => 'HTML',
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Telegram notification failed (markAsArrived): ' . $e->getMessage());
        }

        return back()->with('success', "Barang PO [{$purchaseOrder->nomor_po}] dikonfirmasi sampai — stok telah diperbarui.");
    }

    /**
     * Tandai PO sebagai lunas (pelunasan hutang / Account Payable).
     */
    public function markAsLunas(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        if ($purchaseOrder->status_pembayaran === 'Lunas') {
            return back()->with('info', 'PO ini sudah berstatus Lunas.');
        }

        $purchaseOrder->update([
            'status_pembayaran' => 'Lunas',
            'nominal_dp'        => $purchaseOrder->grand_total,
            'sisa_pembayaran'   => 0,
        ]);

        return back()->with('success', "PO [{$purchaseOrder->nomor_po}] berhasil dilunasi.");
    }

    /**
     * Display the specified purchase order.
     */
    public function show(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load(['vendor', 'user', 'details.item.uom']);

        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    /**
     * Batalkan PO — status_pesanan → 'Dibatalkan', status_pembayaran → 'Void / Refund'.
     * Reset seluruh sisa tagihan & nominal DP.
     */
    public function markAsCanceled(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        if ($purchaseOrder->status_pesanan === 'Dibatalkan') {
            return back()->with('info', 'PO ini sudah berstatus Dibatalkan.');
        }

        if ($purchaseOrder->status_pesanan === 'Selesai') {
            return back()->with('error', 'PO yang sudah Selesai tidak dapat dibatalkan.');
        }

        $purchaseOrder->update([
            'status_pesanan'   => 'Dibatalkan',
            'status_pembayaran' => 'Void / Refund',
            'nominal_dp'        => 0,
            'sisa_pembayaran'   => 0,
        ]);

        return back()->with('success', "PO [{$purchaseOrder->nomor_po}] berhasil dibatalkan. Status pembayaran diubah ke Void / Refund.");
    }

    /**
     * Export data purchase order ke PDF (with active filters).
     */
    public function exportPdf(Request $request)
    {
        $purchaseOrders = PurchaseOrder::with(['vendor', 'user'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('nomor_po', 'like', "%{$request->search}%")
                        ->orWhereHas('vendor', fn($v) => $v->where('nama_vendor', 'like', "%{$request->search}%"));
                });
            })
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal_po', $request->bulan))
            ->when($request->filled('tahun'), fn($q) => $q->whereYear('tanggal_po', $request->tahun))
            ->when($request->filled('tipe_po'), fn($q) => $q->where('tipe_po', $request->tipe_po))
            ->when($request->filled('status_pembayaran'), fn($q) => $q->where('status_pembayaran', $request->status_pembayaran))
            ->when($request->filled('status_pesanan'), fn($q) => $q->where('status_pesanan', $request->status_pesanan))
            ->orderBy('id', 'asc')
            ->get();

        $pdf = Pdf::loadView('purchase_orders.pdf', compact('purchaseOrders'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Data_PO_PT_MULTYARTHA.pdf');
    }
}
