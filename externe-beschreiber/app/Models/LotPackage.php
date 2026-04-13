<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotPackage extends Model
{
    use HasFactory;
    protected $fillable = ['lot_id', 'pack_type_id', 'pack_number', 'pack_note'];

    public function lot(): BelongsTo { return $this->belongsTo(Lot::class); }
    public function packType(): BelongsTo { return $this->belongsTo(PackType::class); }
}
