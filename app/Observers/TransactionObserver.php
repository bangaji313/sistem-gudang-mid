<?php

namespace App\Observers;

use App\Models\Transaction;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     *
     * keluar       → stok berkurang
     * retur_cacat  → stok berkurang (barang cacat dikeluarkan)
     * kembali_sisa → stok bertambah (sisa material kembali ke gudang)
     */
    public function created(Transaction $transaction): void
    {
        match ($transaction->tipe_transaksi) {
            'keluar', 'retur_cacat' => $transaction->item()->decrement('stok', $transaction->qty),
            'kembali_sisa'          => $transaction->item()->increment('stok', $transaction->qty),
        };
    }

    /**
     * Handle the Transaction "deleted" event.
     * Reverse the stock changes.
     */
    public function deleted(Transaction $transaction): void
    {
        match ($transaction->tipe_transaksi) {
            'keluar', 'retur_cacat' => $transaction->item()->increment('stok', $transaction->qty),
            'kembali_sisa'          => $transaction->item()->decrement('stok', $transaction->qty),
        };
    }
}
