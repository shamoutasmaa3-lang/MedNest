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
     public function upload(UploadPrescriptionRequest $request){
        try{
             $user=Auth::user();
             
             $validated=$request->validated();
             $validated['user_id']=$user->id;
              if (!$request->hasFile('file')) {
                return response()->json(['message' => 'No file uploaded'], 400);
            }
               $path = $request->file('file')->store('prescriptions', 'public');
               $validated['file'] = $path;

            $missingMedicines = [];
            $availableMedicines = [];
             $text = $this->extractText($request->file('file'));
            $lines = preg_split('/\r\n|\r|\n/', $text);

            foreach ($lines as $line) {
                $medicineName = trim($line);//
                $medicine = Medicine::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($medicineName) . '%'])->first();


                if (!$medicine) {
                    $missingMedicines[] = $medicineName;
                } else {
                    $availableMedicines[] = $medicine;
                }
            }
              if (empty($missingMedicines)) {
                 $status = 'Approved';
            } 
            else {
                $status = 'Rejected';
                                     }

            $validated['status'] = $status;
              $prescription =Prescription::create($validated);
                foreach ($availableMedicines as $medicine) {
                 $medicineId = $medicine->id;
                 $prescription->medicines()->attach($medicineId);
            }
             return response()->json([
                'message' => 'Prescription uploaded successfully',
                'status' => $status,
                'available_medicines' => $availableMedicines,
                'missing_medicines' => $missingMedicines
                  ]);
        }
        catch(Exception $e){
        return response()->json(['message'=>'Upload failed','error'=>$e->getMessage()],500);
        }
   }
private function extractText($file)
    {
        $extension = $file->getClientOriginalExtension();

        if ($extension === 'pdf') {
            return \Spatie\PdfToText\Pdf::getText($file->getRealPath());
        }
        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $filePath = $file->getRealPath();

            return (new TesseractOCR($filePath))->executable('C:\Users\Classic\AppData\Local\Programs\Tesseract-OCR\tesseract.exe')->lang('eng')->run();
                }
            return "Unsupported file type";
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