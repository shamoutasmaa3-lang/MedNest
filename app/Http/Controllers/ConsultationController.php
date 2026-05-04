<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\ConsultationMessage;
use App\Models\User;
use App\Notifications\ConsultationReplyNotification;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function createConsultation(Request $request)
{
    $request->validate([
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
    ]);

    $consultation = Consultation::create([
        'patient_id' => $request->user()->id,
        'subject' => $request->subject,
    ]);

      ConsultationMessage::create([
        'consultation_id' => $consultation->id,
        'sender_id' => $request->user()->id,
        'message' => $request->message,
    ]);

    return response()->json([
        'status' => 'success',
        'consultation' => $consultation,
    ]);
}
public function patientConsultations(Request $request)
{
    $consultations = Consultation::where('patient_id', $request->user()->id)
        ->with('messages')
        ->latest()
        ->get();

    return response()->json($consultations);
}

public function reply(Request $request, $id)
{
    $request->validate([
        'message' => 'required|string',
    ]);

    $consultation = Consultation::findOrFail($id);

    
    if ($request->user()->role !== 'pharmacist') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

   
    if (!$consultation->pharmacist_id) {
        $consultation->pharmacist_id = $request->user()->id;
        $consultation->save();
    }

    $message = ConsultationMessage::create([
        'consultation_id' => $consultation->id,
        'sender_id' => $request->user()->id,
        'message' => $request->message,
    ]);

    $consultation->update(['status' => 'answered']);

    $patient = User::find($consultation->patient_id);
    $patient->notify(new ConsultationReplyNotification($message));

    return response()->json([
        'status' => 'success',
        'message' => 'Reply sent',
    ]);
}

public function pharmacistConsultations(Request $request)
{
    if ($request->user()->role !== 'pharmacist') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    $consultations = Consultation::whereNull('pharmacist_id')
        ->orWhere('pharmacist_id', $request->user()->id)
        ->with('messages')
        ->latest()
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $consultations
    ]);
}

}
