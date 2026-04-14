<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryCatalogTypeMapping extends Model
{
    protected $fillable = ['category_prefix', 'catalog_type_id'];

    public function catalogType(): BelongsTo
    {
        return $this->belongsTo(CatalogType::class);
    }

    public static function findCatalogTypeForCategory(string $categoryName): ?int
    {
        $mappings = self::orderByRaw('LENGTH(category_prefix) DESC')->get();

        foreach ($mappings as $mapping) {
            if (str_starts_with($categoryName, $mapping->category_prefix)) {
                return $mapping->catalog_type_id;
            }
        }

        return null;
    }
}
