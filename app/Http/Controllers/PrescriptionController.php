<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UploadPrescriptionRequest;
use App\Models\Medicine;
use App\Models\Prescription;
use Illuminate\Support\Facades\Auth;
use Exception;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Models\User;

class PrescriptionController extends Controller
{
    public function storeDoctorPrescription(Request $request)
    {
        // 1. Authorization: only doctors
        $doctor = Auth::user();
        if ($doctor->role !== 'doctor') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // 2. Validate request
        $request->validate([
            'patient_id'        => 'required|exists:users,id',
            'prescription_date' => 'required|date',
            'notes'             => 'nullable|string',
            'items'             => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.dosage'      => 'nullable|string|max:255',
            'items.*.duration'    => 'nullable|string|max:255', // e.g., "7 days"
            'items.*.instructions' => 'nullable|string',
        ]);

        // 3. Create prescription record
        $prescription = Prescription::create([
            'doctor_id'          => $doctor->id,
            'patient_id'         => $request->patient_id,
            'status'             => 'pending',
            'image_path'         => null,
            'pharmacist_notes'   => null,
            'review_date'        => null,
            'fhir_data'          => null,
        ]);

        // 4. Attach medicines with pivot data
        foreach ($request->items as $item) {
            $prescription->medicines()->attach($item['medicine_id'], [
                'quantity'  => $item['quantity'],
                'dosage'    => $item['dosage'] ?? null,
                'duration'  => $item['duration'] ?? null,
            ]);
        }

        // 5. Generate digital signature
        $signatureData = $prescription->doctor_id . $prescription->patient_id . $prescription->created_at->timestamp;
        $signature = hash_hmac('sha256', $signatureData, config('app.key'));
        $prescription->digital_signature = json_encode(['signature' => $signature, 'algorithm' => 'hmac-sha256']);
        $prescription->save();

        // 6. Load relationships for response
        $prescription->load('doctor', 'patient', 'medicines');

        return response()->json([
            'success' => true,
            'message' => 'Prescription created successfully',
            'data'    => $prescription
        ], 201);
    }
    public function dispense($id)
    {
        $pharmacist = Auth::user();
        if ($pharmacist->role !== 'pharmacist') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $prescription = Prescription::findOrFail($id);

        if ($prescription->status !== 'verified') {
            return response()->json(['message' => 'Prescription must be verified before dispensing'], 400);
        }

        $prescription->status = 'dispensed';
        $prescription->save();

        return response()->json(['success' => true, 'message' => 'Prescription dispensed']);
    }
    private function verifyDigitalSignature($prescription)
    {
        if (!$prescription->digital_signature) {
            return false;
        }

        $sigData = json_decode($prescription->digital_signature, true);
        if (!isset($sigData['signature'])) {
            return false;
        }

        $signatureData = $prescription->doctor_id . $prescription->patient_id . strtotime($prescription->created_at);
        $expectedSignature = hash_hmac('sha256', $signatureData, config('app.key'));

        return hash_equals($expectedSignature, $sigData['signature']);
    }

    public function verifyPrescriptionSignature($id)
    {
        $pharmacist = Auth::user();
        if ($pharmacist->role !== 'pharmacist') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $prescription = Prescription::findOrFail($id);
        $isValid = $this->verifyDigitalSignature($prescription);

        return response()->json([
            'valid' => $isValid,
            'message' => $isValid ? 'Signature is valid' : 'Signature is invalid or missing'
        ]);
    }

    public function review(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'notes'  => 'nullable|string',
        ]);

        $pharmacist = Auth::user();

        if ($pharmacist->role !== 'pharmacist') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $prescription = Prescription::findOrFail($id);

        // ✅ التحقق من التوقيع الرقمي قبل الموافقة
        if ($request->status === 'verified' && $prescription->digital_signature) {
            if (!$this->verifyDigitalSignature($prescription)) {
                return response()->json([
                    'message' => 'Cannot approve: Digital signature is invalid or tampered'
                ], 400);
            }
        }

        $prescription->pharmacist_id   = $pharmacist->id;
        $prescription->pharmacist_notes = $request->notes;
        $prescription->status           = $request->status;
        $prescription->review_date      = now();

        $prescription->save();

        return response()->json([
            'message' => 'Prescription reviewed successfully',
            'data'    => $prescription->load('doctor', 'patient', 'pharmacist'),
        ]);
    }
}
