<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_id',
        'quantity',
        'expiration_date',
        'location',
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}