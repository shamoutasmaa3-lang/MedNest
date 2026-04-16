<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadPrescriptionRequest;
use App\Models\Medicine;
use App\Models\Prescription;
use Illuminate\Support\Facades\Auth;
use Exception;
use thiagoalessio\TesseractOCR\TesseractOCR;

class PrescriptionController extends Controller
{
    public function upload(UploadPrescriptionRequest $request)
    {
        try {
            $user = Auth::user();

           
            $path = $request->file('file')->store('prescriptions', 'public');

           
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

            $prescription = Prescription::create([
                'patient_id' => $user->id,
                'doctor_id'  => null,
                'file'       => $path,        
                'status'     => $status,
            ]);

           
            foreach ($availableMedicines as $medicine) {
                $prescription->medicines()->attach($medicine->id, [
                    'dosage'   => null,
                    'duration' => null,
                ]);
            }

            return response()->json([
                'message'             => 'Prescription uploaded successfully',
                'status'              => $status,
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
}