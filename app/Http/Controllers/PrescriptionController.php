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
use thiagoalessio\TesseractOCR\TesseractOCR;

class PrescriptionController extends Controller
{
    public function storeDoctorPrescription(Request $request)
    {
        $doctor = Auth::user();
        if ($doctor->role !== 'doctor') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'patient_id'           => 'required|exists:users,id',
            'prescription_date'    => 'required|date',
            'notes'                => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.medicine_id'  => 'required|exists:medicines,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.dosage'       => 'nullable|string|max:255',
            'items.*.duration'     => 'nullable|string|max:255',
            'items.*.instructions' => 'nullable|string',
        ]);

        $prescription = Prescription::create([
            'doctor_id'        => $doctor->id,
            'patient_id'       => $request->patient_id,
            'status'           => 'pending',
            'image_path'       => null,
            'pharmacist_notes' => null,
            'review_date'      => null,
            'fhir_data'        => null,
        ]);

        foreach ($request->items as $item) {
            $prescription->medicines()->attach($item['medicine_id'], [
                'quantity' => $item['quantity'],
                'dosage'   => $item['dosage'] ?? null,
                'duration' => $item['duration'] ?? null,
            ]);
        }

        $signatureData = $prescription->doctor_id
            . $prescription->patient_id
            . $prescription->created_at->timestamp;

        $signature = hash_hmac('sha256', $signatureData, config('app.key'));

        $prescription->digital_signature = json_encode([
            'signature' => $signature,
            'algorithm' => 'hmac-sha256',
        ]);

        $prescription->load('doctor', 'patient', 'medicines');

        $fhir = [
            'resourceType' => 'MedicationRequest',
            'id'           => (string) $prescription->id,
            'status'       => 'active',
            'intent'       => 'order',
            'authoredOn'   => $prescription->created_at->toIso8601String(),
            'subject'      => [
                'reference' => 'Patient/' . $prescription->patient_id,
            ],
            'requester'    => [
                'reference' => 'Practitioner/' . $prescription->doctor_id,
            ],
            'note'         => $request->notes
                ? [['text' => $request->notes]]
                : [],
            'dosageInstruction' => [],
        ];

        foreach ($prescription->medicines as $med) {
            $pivot = $med->pivot;

            $fhir['dosageInstruction'][] = [
                'text' => trim(
                    ($pivot->dosage ?? '') . ' ' . ($pivot->duration ?? '')
                ),
            ];
        }

        $prescription->fhir_data = json_encode($fhir, JSON_UNESCAPED_UNICODE);
        $prescription->save();

        return response()->json([
            'success' => true,
            'message' => 'Prescription created successfully',
            'data'    => $prescription->load('doctor', 'patient', 'medicines'),
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

        DB::table('audit_logs')->insert([
            'user_id'        => $pharmacist->id,
            'action'         => 'dispense_prescription',
            'prescription_id'=> $prescription->id,
            'created_at'     => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Prescription dispensed',
        ]);
    }

    public function upload(UploadPrescriptionRequest $request)
    {
        try {
            $user = Auth::user();
            $validated = $request->validated();

           
            $validated['patient_id'] = $user->id;

            if (!$request->hasFile('file')) {
                return response()->json(['message' => 'No file uploaded'], 400);
            }

            $path = $request->file('file')->store('prescriptions', 'public');
            $validated['image_path'] = $path;

            $missingMedicines   = [];
            $availableMedicines = [];

            $text  = $this->extractText($request->file('file'));
            $lines = preg_split('/\r\n|\r|\n/', $text);

            foreach ($lines as $line) {
                $medicineName = trim($line);
                if ($medicineName === '') {
                    continue;
                }

                $medicine = Medicine::whereRaw(
                    'LOWER(name) LIKE ?',
                    ['%' . strtolower($medicineName) . '%']
                )->first();

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

            $availableSummary = collect($availableMedicines)->map(function ($m) {
                return [
                    'id'   => $m->id,
                    'name' => $m->name,
                ];
            });

            return response()->json([
                'message'             => 'Prescription uploaded successfully',
                'status'              => $status,
                'available_medicines' => $availableSummary,
                'missing_medicines'   => $missingMedicines,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Upload failed',
            ], 500);
        }
    }

    private function extractText($file)
    {
        $extension = $file->getClientOriginalExtension();
        $filePath = $file->getRealPath();

        if ($extension === 'pdf') {
            
            return \Spatie\PdfToText\Pdf::getText($filePath);
        }

        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            
            return (new TesseractOCR($filePath))
                ->executable('C:\Users\Classic\AppData\Local\Programs\Tesseract-OCR\tesseract.exe')  
                ->lang('eng')
                ->run();
        }

        throw new \Exception("Unsupported file type");
    }
    

    public function patientPrescriptions()
    {
        $user = Auth::user();

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

    private function verifyDigitalSignature($prescription)
    {
        if (!$prescription->digital_signature) {
            return false;
        }

        $sigData = json_decode($prescription->digital_signature, true);
        if (!isset($sigData['signature'])) {
            return false;
        }

        $signatureData = $prescription->doctor_id
            . $prescription->patient_id
            . strtotime($prescription->created_at);

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
        $isValid      = $this->verifyDigitalSignature($prescription);

        DB::table('audit_logs')->insert([
            'user_id'        => $pharmacist->id,
            'action'         => 'verify_signature',
            'prescription_id'=> $prescription->id,
            'result'         => $isValid ? 'valid' : 'invalid',
            'created_at'     => now(),
        ]);

        return response()->json([
            'valid'   => $isValid,
            'message' => $isValid
                ? 'Signature is valid'
                : 'Signature is invalid or missing',
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

        if ($request->status === 'verified' && $prescription->digital_signature) {
            if (!$this->verifyDigitalSignature($prescription)) {
                return response()->json([
                    'message' => 'Cannot approve: Digital signature is invalid or tampered',
                ], 400);
            }
        }

        $prescription->pharmacist_id    = $pharmacist->id;
        $prescription->pharmacist_notes = $request->notes;
        $prescription->status           = $request->status;
        $prescription->review_date      = now();
        $prescription->save();

        DB::table('audit_logs')->insert([
            'user_id'        => $pharmacist->id,
            'action'         => 'review_prescription',
            'prescription_id'=> $prescription->id,
            'status'         => $prescription->status,
            'created_at'     => now(),
        ]);

        return response()->json([
            'message' => 'Prescription reviewed successfully',
            'data'    => $prescription->load('doctor', 'patient', 'pharmacist'),
        ]);
    }
}
