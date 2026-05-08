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
    // ... (keep all existing methods: upload, extractText, patientPrescriptions, storeDoctorPrescription, doctorPrescriptions, pharmacistPrescriptions, review)

    /**
     * Verify the digital signature of an electronic prescription
     */
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