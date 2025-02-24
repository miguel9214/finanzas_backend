<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'amount',
        'description',
        'date',
        'receipt_image', // Agregar el nuevo campo
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
