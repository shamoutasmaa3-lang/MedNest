<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UploadPrescriptionRequest;
use App\Models\Medicine;
use App\Models\Prescription;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\User;

class PrescriptionController extends Controller
{
    // ========== Methods from your local branch (HEAD) ==========

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
            'items.*.duration'    => 'nullable|string|max:255',
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

    // ========== Methods from the remote branch (origin/main) ==========

    public function upload(UploadPrescriptionRequest $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validated();

            // Ensure the patient_id is set from the authenticated user
            $validated['patient_id'] = $user->id;

            if (!$request->hasFile('file')) {
                return response()->json(['message' => 'No file uploaded'], 400);
            }

            $path = $request->file('file')->store('prescriptions', 'public');
            // Use 'image_path' column (consistent with storeDoctorPrescription)
            $validated['image_path'] = $path;

            $missingMedicines = [];
            $availableMedicines = [];
            $text = $this->extractText($request->file('file'));
            $lines = preg_split('/\r\n|\r|\n/', $text);

            foreach ($lines as $line) {
                $medicineName = trim($line);
                $medicine = Medicine::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($medicineName) . '%'])->first();

                if (!$medicine) {
                    $missingMedicines[] = $medicineName;
                } else {
                    $availableMedicines[] = $medicine;
                }
            }

            $status = empty($missingMedicines) ? 'Approved' : 'Rejected';
            $validated['status'] = $status;

            $prescription = Prescription::create($validated);

            foreach ($availableMedicines as $medicine) {
                $prescription->medicines()->attach($medicine->id);
            }

            return response()->json([
                'message' => 'Prescription uploaded successfully',
                'status' => $status,
                'available_medicines' => $availableMedicines,
                'missing_medicines' => $missingMedicines
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Upload failed', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Extract text from uploaded file.
     * 
     * This version returns dummy medicine names to avoid dependency on 
     * Tesseract OCR and pdftotext. It works for testing immediately.
     * 
     * For real OCR, replace the return statement with the actual extraction logic.
     */
    private function extractText($file)
    {
        // 🔥 Temporary: return hardcoded text for testing.
        // This will work for any file type (image or PDF).
        return "Ibuprofen\nWarfarin\nAspirin";
    }

    // ========== patientPrescriptions() method ==========

    /**
     * Get all prescriptions for the authenticated patient
     */
    public function patientPrescriptions()
    {
        $user = Auth::user();

        // Optional: ensure the user is a patient (adjust role check as needed)
        if ($user->role !== 'patient') {
            return response()->json(['message' => 'Only patients can access their prescriptions'], 403);
        }

        $prescriptions = Prescription::where('patient_id', $user->id)
            ->with(['doctor', 'medicines'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $prescriptions
        ]);
    }

    // ========== doctorPrescriptions() method ==========

    /**
     * Get all prescriptions created by the authenticated doctor
     */
    public function doctorPrescriptions()
    {
        $doctor = Auth::user();

        if ($doctor->role !== 'doctor') {
            return response()->json(['message' => 'Only doctors can access this endpoint'], 403);
        }

        $prescriptions = Prescription::where('doctor_id', $doctor->id)
            ->with(['patient', 'medicines'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $prescriptions
        ]);
    }

    // ========== NEW: pharmacistPrescriptions() method ==========

    /**
     * Get all prescriptions for pharmacist view (all prescriptions)
     */
    public function pharmacistPrescriptions()
    {
        $pharmacist = Auth::user();

        if ($pharmacist->role !== 'pharmacist') {
            return response()->json(['message' => 'Only pharmacists can access this endpoint'], 403);
        }

        $prescriptions = Prescription::with(['doctor', 'patient', 'medicines'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $prescriptions
        ]);
    }

    // ========== Common methods (signature verification, review) ==========

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
        $prescription->pharmacist_id = $pharmacist->id;
        $prescription->pharmacist_notes = $request->notes;
        $prescription->status = $request->status;
        $prescription->review_date = now();

        $prescription->save();

        return response()->json([
            'message' => 'Prescription reviewed successfully',
            'data' => $prescription->load('doctor', 'patient', 'pharmacist'),
        ]);
    }
}