<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Consultation extends Model
{use HasFactory;
protected $fillable = [ 'patient_id','pharmacist_id','subject','status'];
//
    public function patient() {
    return $this->belongsTo(User::class, 'patient_id');
}

public function pharmacist() {
    return $this->belongsTo(User::class, 'pharmacist_id');
}

public function messages() {
    return $this->hasMany(ConsultationMessage::class);
}

}
