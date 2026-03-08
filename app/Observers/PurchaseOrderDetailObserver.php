<?php

namespace App\Observers;

use App\Models\PurchaseOrderDetail;

class PurchaseOrderDetailObserver
{
    /**
     * Handle the PurchaseOrderDetail "created" event.
     * Barang masuk via PO → stok bertambah HANYA jika tipe_po = 'Ready Stock'.
     * Untuk Pre-Order, stok ditambah manual via markAsArrived().
     */
    public function created(PurchaseOrderDetail $detail): void
    {
        $tipePo = $detail->purchaseOrder->tipe_po ?? 'Ready Stock';

        if ($tipePo === 'Ready Stock') {
            $detail->item()->increment('stok', $detail->qty);
        }
    }

    /**
     * Handle the PurchaseOrderDetail "deleted" event.
     * Jika detail PO dihapus → stok dikurangi kembali (hanya jika Ready Stock & Selesai).
     */
    public function deleted(PurchaseOrderDetail $detail): void
    {
        $po = $detail->purchaseOrder;

        // Hanya kurangi stok jika PO sudah pernah menambah stok
        if ($po->tipe_po === 'Ready Stock' || $po->status_pesanan === 'Selesai') {
            $detail->item()->decrement('stok', $detail->qty);
        }
    }
}
