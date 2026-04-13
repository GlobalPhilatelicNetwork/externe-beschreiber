<?php

namespace App\Models;

use Database\Factories\CatalogPartFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogPart extends Model
{
    /** @use HasFactory<CatalogPartFactory> */
    use HasFactory;

    protected $fillable = ['name_de', 'name_en', 'is_default'];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function getNameAttribute(): string
    {
        return $this->{'name_' . app()->getLocale()} ?? $this->name_en;
    }
}
