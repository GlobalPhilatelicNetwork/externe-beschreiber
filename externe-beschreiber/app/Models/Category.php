<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $fillable = ['name_de', 'name_en'];

    public function getNameAttribute(): string
    {
        return $this->{'name_' . app()->getLocale()} ?? $this->name_en;
    }
}
