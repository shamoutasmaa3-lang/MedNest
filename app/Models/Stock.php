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
    'min_quantity',
    'location',
    'expiration_date',   // add this
    'batch_number',      // add this
];
protected $casts = [
    'expiration_date' => 'date',
];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}