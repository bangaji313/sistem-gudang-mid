<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Uom extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_satuan',
    ];

    /**
     * Get the items that use this unit of measure.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
