<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lot extends Model
{
    use HasFactory;

    protected $fillable = [
        'consignment_id',
        'sequence_number',
        'lot_type',
        'grouping_category_id',
        'description',
        'provenance',
        'epos',
        'starting_price',
        'is_bid_lot',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'starting_price' => 'decimal:2',
            'is_bid_lot' => 'boolean',
        ];
    }

    public function consignment(): BelongsTo
    {
        return $this->belongsTo(Consignment::class);
    }

    public function groupingCategory(): BelongsTo
    {
        return $this->belongsTo(GroupingCategory::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'lot_category');
    }

    public function conditions(): BelongsToMany
    {
        return $this->belongsToMany(Condition::class, 'lot_condition');
    }

    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'lot_destination_category');
    }

    public function catalogEntries(): HasMany
    {
        return $this->hasMany(LotCatalogEntry::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(LotPackage::class);
    }
}
