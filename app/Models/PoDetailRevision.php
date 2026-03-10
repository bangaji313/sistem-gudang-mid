<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoDetailRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_detail_id',
        'old_qty',
        'new_qty',
        'old_price',
        'new_price',
        'keterangan',
        'revised_by',
    ];

    protected function casts(): array
    {
        return [
            'old_price' => 'decimal:2',
            'new_price' => 'decimal:2',
        ];
    }

    /**
     * Get the PO detail line this revision belongs to.
     */
    public function purchaseOrderDetail(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderDetail::class);
    }

    /**
     * Get the user who made this revision.
     */
    public function revisedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revised_by');
    }
}
