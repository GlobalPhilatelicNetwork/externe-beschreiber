<?php

namespace App\Models;

use Database\Factories\ConsignmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consignment extends Model
{
    /** @use HasFactory<ConsignmentFactory> */
    use HasFactory;

    protected $fillable = [
        'consignor_number',
        'internal_nid',
        'start_number',
        'next_number',
        'catalog_part_id',
        'user_id',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function catalogPart(): BelongsTo
    {
        return $this->belongsTo(CatalogPart::class);
    }

    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}
