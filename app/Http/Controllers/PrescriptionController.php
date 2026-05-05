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
    /**
     * رفع وصفة ورقية (صورة/PDF) من قبل المريض
     * يجب أن يحدد المريض الطبيب الذي أصدر الوصفة
     */
    public function upload(UploadPrescriptionRequest $request)
    {
        // التحقق من وجود doctor_id في الطلب (إجباري)
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
        ]);

        $patient = Auth::user();
        
        // التحقق من أن الطبيب المحدد موجود ودوره doctor
        $doctor = User::where('id', $request->doctor_id)
                      ->where('role', 'doctor')
                      ->first();
        
        if (!$doctor) {
            return response()->json([
                'message' => 'Invalid doctor ID or the user is not a doctor'
            ], 400);
        }

        try {
            // حفظ الملف
            $path = $request->file('file')->store('prescriptions', 'public');

            // استخراج النص من الملف (OCR أو PDF)
            $text = $this->extractText($request->file('file'));
            $lines = preg_split('/\r\n|\r|\n/', $text);

            $availableMedicines = [];
            $missingMedicines = [];

            foreach ($lines as $line) {
                $medicineName = trim($line);
                if (empty($medicineName)) continue;

                $medicine = Medicine::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($medicineName) . '%'])->first();

                if (!$medicine) {
                    $missingMedicines[] = $medicineName;
                } else {
                    $availableMedicines[] = $medicine;
                }
            }

            $status = empty($missingMedicines) ? 'approved' : 'rejected';

            // إنشاء الوصفة مع ربطها بالطبيب المحدد (وليس null)
            $prescription = Prescription::create([
                'patient_id' => $patient->id,
                'doctor_id'  => $doctor->id,
                'file'       => $path,
                'status'     => $status,
            ]);

            // ربط الأدوية المتاحة
            foreach ($availableMedicines as $medicine) {
                $prescription->medicines()->attach($medicine->id, [
                    'dosage'   => null,
                    'duration' => null,
                ]);
            }

            return response()->json([
                'message'             => 'Prescription uploaded successfully',
                'status'              => $status,
                'doctor_name'         => $doctor->name,
                'available_medicines' => array_map(fn($m) => ['id' => $m->id, 'name' => $m->name], $availableMedicines),
                'missing_medicines'   => $missingMedicines,
                'prescription_id'     => $prescription->id,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Upload failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * استخراج النص من ملف (صورة أو PDF)
     */
    private function extractText($file)
    {
        $extension = $file->getClientOriginalExtension();
        $filePath = $file->getRealPath();

        if ($extension === 'pdf') {
            return \Spatie\PdfToText\Pdf::getText($filePath);
        }

        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return (new TesseractOCR($filePath))
                ->executable('C:\Program Files\Tesseract-OCR\tesseract.exe')
                ->lang('eng')
                ->run();
        }

        throw new \Exception("Unsupported file type");
    }

    /**
     * جلب الوصفات التي تخص المريض الحالي (للعرض فقط)
     */
    public function patientPrescriptions()
    {
        $user = Auth::user();

        $prescriptions = Prescription::with('medicines', 'doctor')
            ->where('patient_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $prescriptions->map(function ($prescription) {
                return [
                    'id'           => $prescription->id,
                    'doctor_name'  => $prescription->doctor->name ?? 'غير معروف',
                    'status'       => $prescription->status,
                    'image_path'   => $prescription->image_path,
                    'created_at'   => $prescription->created_at,
                    'medicines'    => $prescription->medicines->map(function ($medicine) {
                        return [
                            'id'       => $medicine->id,
                            'name'     => $medicine->name,
                            'dosage'   => $medicine->pivot->dosage,
                            'duration' => $medicine->pivot->duration,
                        ];
                    }),
                ];
            }),
        ]);
    }

    /**
     * إنشاء وصفة إلكترونية من قبل الطبيب (بدون رفع ملف)
     */
   
    public function storeDoctorPrescription(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'medicines'  => 'required|array|min:1',
            'medicines.*.medicine_id' => 'required|exists:medicines,id',
            'medicines.*.dosage'      => 'required|string|max:100',
            'medicines.*.duration'    => 'required|string|max:100',
            'fhir_data'  => 'nullable|array',
        ]);

        $doctorId = Auth::id();

        // توقيع رقمي
        $signatureData = $doctorId . $request->patient_id . now()->timestamp;
        $digitalSignature = hash_hmac('sha256', $signatureData, config('app.key'));

        DB::beginTransaction();

        try {
            $prescription = Prescription::create([
                'doctor_id'         => $doctorId,
                'patient_id'        => $request->patient_id,

                // دمج FHIR + التوقيع الرقمي
                'digital_signature' => json_encode([
                    'signature' => $digitalSignature,
                    'fhir'      => $request->fhir_data
                ]),

                'status'            => 'pending',
                'image_path'        => null,
                'fhir_data'         => $request->fhir_data,
            ]);

            foreach ($request->medicines as $medicine) {
                $prescription->medicines()->attach($medicine['medicine_id'], [
                    'dosage'   => $medicine['dosage'],
                    'duration' => $medicine['duration'],
                ]);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Electronic prescription issued successfully',
                'data'    => $prescription->load('medicines', 'doctor', 'patient'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to create prescription: ' . $e->getMessage(),
            ], 500);
        }
    }
    /**
     * جلب الوصفات التي كتبها الطبيب الحالي
     */
    public function doctorPrescriptions()
    {
        $doctorId = Auth::id();

        $prescriptions = Prescription::with('medicines', 'patient')
            ->where('doctor_id', $doctorId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $prescriptions,
        ]);
    }
    public function pharmacistPrescriptions()
{
    $pharmacist = Auth::user();

    if ($pharmacist->role !== 'pharmacist') {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $prescriptions = Prescription::where('doctor_id', $pharmacist->id)
        ->orWhere('status', 'pending')
        ->with('patient')
        ->latest()
        ->get();

    return response()->json($prescriptions);
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