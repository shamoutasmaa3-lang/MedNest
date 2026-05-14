<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'requires_prescription',
        'price',
        'active_ingredient',
        'manufacturer',
        'image'
    ];

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function prescriptions()
    {
        return $this->belongsToMany(Prescription::class, 'prescription_medicine');
    }

    public function interactions()
    {
        return $this->belongsToMany(
            DrugInteraction::class,
            'drug_interactions',
            'medicine_id_1',
            'medicine_id_2'
        );
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class);
    }
}