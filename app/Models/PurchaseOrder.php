<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nomor_po',
        'no_invoice',
        'tgl_invoice',
        'tgl_diterima',
        'vendor_id',
        'tanggal_po',
        'tipe_po',
        'estimasi_sampai',
        'status_pesanan',
        'status_pembayaran',
        'tanggal_jatuh_tempo',
        'total_harga',
        'diskon',
        'grand_total',
        'nominal_dp',
        'sisa_pembayaran',
        'user_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_po' => 'date',
            'estimasi_sampai' => 'date',
            'tanggal_jatuh_tempo' => 'date',
            'tgl_invoice' => 'date',
            'tgl_diterima' => 'date',
            'total_harga' => 'decimal:2',
            'diskon' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'nominal_dp' => 'decimal:2',
            'sisa_pembayaran' => 'decimal:2',
        ];
    }

    /**
     * Get the vendor that owns this purchase order.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the user who created this purchase order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the details/line items for this purchase order.
     */
    public function purchaseOrderDetails(): HasMany
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    /**
     * Alias: Get the details/line items for this purchase order.
     */
    public function details(): HasMany
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'purchase_order_id');
    }
}
