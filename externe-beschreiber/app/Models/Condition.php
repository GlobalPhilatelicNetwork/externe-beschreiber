<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'image', 'circuit_id', 'sort_order'];

    public function getDisplayAttribute(): string
    {
        return $this->image ?: $this->name;
    }
}
