<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DrugInteraction;
use App\Models\Medicine;

class DrugInteractionSeeder extends Seeder
{
    public function run()
    {
        // جلب الأدوية من قاعدة البيانات حسب أسمائها
        $ibuprofen = Medicine::where('name', 'like', '%Ibuprofen%')->first();
        $warfarin = Medicine::where('name', 'like', '%Warfarin%')->first();
        $aspirin = Medicine::where('name', 'like', '%Aspirin%')->first();
        $amoxicillin = Medicine::where('name', 'like', '%Amoxicillin%')->first();
        $azithromycin = Medicine::where('name', 'like', '%Azithromycin%')->first();
        $ciprofloxacin = Medicine::where('name', 'like', '%Ciprofloxacin%')->first();
        $doxycycline = Medicine::where('name', 'like', '%Doxycycline%')->first();
        $simvastatin = Medicine::where('name', 'like', '%Simvastatin%')->first();
        $atorvastatin = Medicine::where('name', 'like', '%Atorvastatin%')->first();
        $amiodarone = Medicine::where('name', 'like', '%Amiodarone%')->first();
        $clopidogrel = Medicine::where('name', 'like', '%Clopidogrel%')->first();
        $omeprazole = Medicine::where('name', 'like', '%Omeprazole%')->first();
        $lisinopril = Medicine::where('name', 'like', '%Lisinopril%')->first();
        $prednisolone = Medicine::where('name', 'like', '%Prednisolone%')->first();
        $metformin = Medicine::where('name', 'like', '%Metformin%')->first();
        $captopril = Medicine::where('name', 'like', '%Captopril%')->first();
        $digoxin = Medicine::where('name', 'like', '%Digoxin%')->first();

        // ================ 1. WARFARIN INTERACTIONS ================
        // الوارفارين هو دواء مميع للدم يتفاعل مع العديد من الأدوية الأخرى

        // Warfarin + NSAIDs (Ibuprofen) - زيادة خطر النزيف
        if ($warfarin && $ibuprofen) {
            DrugInteraction::create([
                'medicine_id_1' => $warfarin->id,
                'medicine_id_2' => $ibuprofen->id,
                'severity' => 'high',
                'description' => 'Combined use of warfarin with NSAIDs such as ibuprofen significantly increases the risk of gastrointestinal bleeding and hemorrhage. Avoid concurrent use if possible, or monitor INR closely.'
            ]);
        }

        // Warfarin + Aspirin - زيادة خطر النزيف
        if ($warfarin && $aspirin) {
            DrugInteraction::create([
                'medicine_id_1' => $warfarin->id,
                'medicine_id_2' => $aspirin->id,
                'severity' => 'high',
                'description' => 'Combined use of warfarin with aspirin greatly increases the risk of bleeding complications due to dual antiplatelet and anticoagulant effects. Use only when absolutely necessary with close INR monitoring.'
            ]);
        }

        // Warfarin + Amoxicillin - يزيد INR (زمن النزيف)
        if ($warfarin && $amoxicillin) {
            DrugInteraction::create([
                'medicine_id_1' => $warfarin->id,
                'medicine_id_2' => $amoxicillin->id,
                'severity' => 'moderate',
                'description' => 'Amoxicillin may potentiate the anticoagulant effect of warfarin, leading to increased INR and bleeding risk. Monitor INR closely during and after antibiotic therapy.'
            ]);
        }

        // Warfarin + Azithromycin - قد يزيد تأثير الوارفارين
        if ($warfarin && $azithromycin) {
            DrugInteraction::create([
                'medicine_id_1' => $warfarin->id,
                'medicine_id_2' => $azithromycin->id,
                'severity' => 'moderate',
                'description' => 'Azithromycin may potentiate the effects of warfarin. Postmarketing reports suggest increased anticoagulant effect. Monitor prothrombin time closely during concomitant use.'
            ]);
        }

        // Warfarin + Ciprofloxacin - يزيد تأثير الوارفارين بشكل كبير
        if ($warfarin && $ciprofloxacin) {
            DrugInteraction::create([
                'medicine_id_1' => $warfarin->id,
                'medicine_id_2' => $ciprofloxacin->id,
                'severity' => 'high',
                'description' => 'Ciprofloxacin can significantly increase the anticoagulant effect of warfarin, increasing INR and bleeding risk 2-4 fold over baseline. Avoid if possible; if unavoidable, reduce warfarin dose by 25-40% and monitor INR frequently.'
            ]);
        }

        // Warfarin + Doxycycline - يزيد تأثير الوارفارين
        if ($warfarin && $doxycycline) {
            DrugInteraction::create([
                'medicine_id_1' => $warfarin->id,
                'medicine_id_2' => $doxycycline->id,
                'severity' => 'moderate',
                'description' => 'Doxycycline may enhance the anticoagulant effect of warfarin. Monitor INR more frequently when starting or stopping doxycycline therapy.'
            ]);
        }

        // ================ 2. STATIN INTERACTIONS ================

        // Simvastatin + Amiodarone - خطر تلف العضلات (تسمم عضلي)
        if ($simvastatin && $amiodarone) {
            DrugInteraction::create([
                'medicine_id_1' => $simvastatin->id,
                'medicine_id_2' => $amiodarone->id,
                'severity' => 'high',
                'description' => 'Amiodarone inhibits CYP3A4 metabolism of simvastatin, significantly increasing simvastatin plasma levels. This increases the risk of myopathy and rhabdomyolysis. Maximum simvastatin dose with amiodarone is 20 mg daily. Consider switching to pravastatin or rosuvastatin.'
            ]);
        }

        // Atorvastatin + Amiodarone - خطر تلف العضلات
        if ($atorvastatin && $amiodarone) {
            DrugInteraction::create([
                'medicine_id_1' => $atorvastatin->id,
                'medicine_id_2' => $amiodarone->id,
                'severity' => 'moderate',
                'description' => 'Amiodarone may increase atorvastatin blood levels via CYP3A4 inhibition, increasing risk of myopathy. Caution advised, especially with high-dose atorvastatin. Monitor for muscle pain or weakness.'
            ]);
        }

        // Warfarin + Simvastatin - يزيد تأثير الوارفارين
        if ($warfarin && $simvastatin) {
            DrugInteraction::create([
                'medicine_id_1' => $warfarin->id,
                'medicine_id_2' => $simvastatin->id,
                'severity' => 'moderate',
                'description' => 'Simvastatin may enhance the anticoagulant effect of warfarin. Monitor INR when starting or adjusting simvastatin dose. Pravastatin has lower interaction potential.'
            ]);
        }

        // ================ 3. CLOPIDOGREL + OMEPRAZOLE (FDA WARNING) ================

        if ($clopidogrel && $omeprazole) {
            DrugInteraction::create([
                'medicine_id_1' => $clopidogrel->id,
                'medicine_id_2' => $omeprazole->id,
                'severity' => 'high',
                'description' => 'FDA warning: Omeprazole reduces the antiplatelet effect of clopidogrel by approximately 45-50% by inhibiting CYP2C19-mediated activation. Avoid concomitant use. Consider alternative PPIs such as pantoprazole, lansoprazole, or rabeprazole which have lower interaction potential.'
            ]);
        }

        // ================ 4. OTHER SIGNIFICANT INTERACTIONS ================

        // Aspirin + Ibuprofen - تقليل فعالية الأسبرين
        if ($aspirin && $ibuprofen) {
            DrugInteraction::create([
                'medicine_id_1' => $aspirin->id,
                'medicine_id_2' => $ibuprofen->id,
                'severity' => 'moderate',
                'description' => 'Ibuprofen may reduce the cardioprotective effects of low-dose aspirin by competing for the COX-1 binding site. If concurrent use is necessary, take ibuprofen at least 8 hours before or 30 minutes after immediate-release aspirin.'
            ]);
        }

        // Lisinopril + NSAIDs (Ibuprofen) - يقلل تأثير الـ ACE inhibitor
        if ($lisinopril && $ibuprofen) {
            DrugInteraction::create([
                'medicine_id_1' => $lisinopril->id,
                'medicine_id_2' => $ibuprofen->id,
                'severity' => 'moderate',
                'description' => 'NSAIDs such as ibuprofen may reduce the antihypertensive effect of ACE inhibitors like lisinopril and increase the risk of kidney dysfunction, especially in elderly or volume-depleted patients. Monitor blood pressure and renal function.'
            ]);
        }

        // Prednisolone + NSAIDs - زيادة خطر النزيف المعوي
        if ($prednisolone && $ibuprofen) {
            DrugInteraction::create([
                'medicine_id_1' => $prednisolone->id,
                'medicine_id_2' => $ibuprofen->id,
                'severity' => 'moderate',
                'description' => 'Concomitant use of corticosteroids (prednisolone) with NSAIDs increases the risk of gastrointestinal ulceration and bleeding. Consider adding gastroprotective therapy (e.g., PPI) if combination necessary.'
            ]);
        }

        // Metformin + contrast media (سياق سريري - لا ينطبق مباشرة على السيدر)
        // ملاحظة: هذه التفاعلات مسجلة لمعلومات سريرية عامة

        // Captopril + Potassium-sparing diuretics (مرجع عام)
        // ملاحظة: هذه التفاعلات مسجلة لمعلومات سريرية عامة

        // Digoxin + Amiodarone - يزيد مستوى الديجوكسين
        if ($digoxin && $amiodarone) {
            DrugInteraction::create([
                'medicine_id_1' => $digoxin->id,
                'medicine_id_2' => $amiodarone->id,
                'severity' => 'high',
                'description' => 'Amiodarone can double or triple digoxin serum levels by reducing renal clearance and volume of distribution. This increases risk of digoxin toxicity (nausea, vomiting, arrhythmias). Reduce digoxin dose by 50% when starting amiodarone and monitor digoxin levels.'
            ]);
        }

        // Clopidogrel + Aspirin - زيادة خطر النزيف (تُستخدم أحياناً معاً)
        if ($clopidogrel && $aspirin) {
            DrugInteraction::create([
                'medicine_id_1' => $clopidogrel->id,
                'medicine_id_2' => $aspirin->id,
                'severity' => 'moderate',
                'description' => 'Dual antiplatelet therapy with clopidogrel and aspirin increases bleeding risk compared to either agent alone. This combination is used in specific clinical situations (e.g., post-stent placement) under medical supervision. Monitor for signs of bleeding.'
            ]);
        }
    }
}