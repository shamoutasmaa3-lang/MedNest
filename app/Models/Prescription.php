<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'patient_id',
        'pharmacist_id',
        'image_path',
        'digital_signature',
        'status',
        'pharmacist_notes',
        'review_date',
        'fhir_data',
    ];

    protected $casts = [
        'fhir_data'   => 'array',
        'review_date' => 'datetime',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function pharmacist()
    {
        return $this->belongsTo(User::class, 'pharmacist_id');
    }

    public function medicines()
    {
        return $this->belongsToMany(Medicine::class, 'prescription_medicine')
            ->withPivot('dosage', 'duration')
            ->withTimestamps();
    }
}
