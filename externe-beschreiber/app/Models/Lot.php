<?php

namespace App\Models;

use Database\Factories\LotFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lot extends Model
{
    /** @use HasFactory<LotFactory> */
    use HasFactory;

    protected $fillable = [
        'consignment_id',
        'sequence_number',
        'category_id',
        'description',
        'catalog_type_id',
        'catalog_number',
        'starting_price',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'starting_price' => 'decimal:2',
        ];
    }

    public function consignment(): BelongsTo
    {
        return $this->belongsTo(Consignment::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function catalogType(): BelongsTo
    {
        return $this->belongsTo(CatalogType::class);
    }
}
