<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
        'rack_location',
        'description',
        'stock',
        'stock_limit',
        'image_url',
    ];

    protected $casts = [
        'stock' => 'integer',
        'stock_limit' => 'integer',
    ];
}
