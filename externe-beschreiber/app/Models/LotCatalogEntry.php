<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotCatalogEntry extends Model
{
    use HasFactory;
    protected $fillable = ['lot_id', 'catalog_type_id', 'catalog_number'];

    public function lot(): BelongsTo { return $this->belongsTo(Lot::class); }
    public function catalogType(): BelongsTo { return $this->belongsTo(CatalogType::class); }
}
