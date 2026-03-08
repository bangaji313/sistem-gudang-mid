<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'merk',
        'uom_id',
        'stok',
        'qr_code_path',
    ];

    /**
     * Boot method — auto-generate kode_barang sebelum insert.
     */
    protected static function booted(): void
    {
        static::creating(function (Item $item) {
            if (empty($item->kode_barang)) {
                $lastItem  = static::orderBy('id', 'desc')->first();
                $nextNumber = $lastItem ? $lastItem->id + 1 : 1;
                $item->kode_barang = 'MID-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Get the unit of measure for this item.
     */
    public function uom(): BelongsTo
    {
        return $this->belongsTo(Uom::class);
    }

    /**
     * Get the purchase order details for this item.
     */
    public function purchaseOrderDetails(): HasMany
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    /**
     * Get the transactions for this item.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
