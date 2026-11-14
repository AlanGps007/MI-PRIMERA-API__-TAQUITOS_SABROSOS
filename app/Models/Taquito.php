<?php

namespace App\Models;

use Database\Factories\TaquitoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'price', 'category', 'is_available'])]
class Taquito extends Model
{
    /** @use HasFactory<TaquitoFactory> */
    use HasFactory;

    protected $attributes = [
        'is_available' => true,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }
}
