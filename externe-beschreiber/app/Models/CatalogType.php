<?php

namespace App\Models;

use Database\Factories\CatalogTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogType extends Model
{
    /** @use HasFactory<CatalogTypeFactory> */
    use HasFactory;

    protected $fillable = ['name_de', 'name_en'];

    public function getNameAttribute(): string
    {
        return $this->{'name_' . app()->getLocale()} ?? $this->name_en;
    }
}
