<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicine;

class MedicineSeeder extends Seeder
{
    public function run()
    {
        $medicines = [
            // ==================== Painkillers & Anti-inflammatory ====================
            ['name' => 'Paracetamol 500mg', 'description' => 'Pain reliever and fever reducer', 'category' => 'Painkillers', 'requires_prescription' => false, 'price' => 5000],
            ['name' => 'Panadol Extra 500mg', 'description' => 'Strong pain reliever', 'category' => 'Painkillers', 'requires_prescription' => false, 'price' => 7500],
            ['name' => 'Voltaren 50mg', 'description' => 'Anti-inflammatory', 'category' => 'Anti-inflammatory', 'requires_prescription' => true, 'price' => 12000],
            ['name' => 'Brufen 400mg', 'description' => 'Pain reliever and anti-inflammatory', 'category' => 'Anti-inflammatory', 'requires_prescription' => false, 'price' => 8000],
            ['name' => 'Aspirin 100mg', 'description' => 'Pain reliever and blood thinner', 'category' => 'Painkillers', 'requires_prescription' => false, 'price' => 4000],
            ['name' => 'Naproxen 250mg', 'description' => 'Pain reliever and anti-inflammatory', 'category' => 'Anti-inflammatory', 'requires_prescription' => true, 'price' => 9000],
            ['name' => 'Diclofenac 75mg', 'description' => 'Pain reliever for rheumatic pain', 'category' => 'Anti-inflammatory', 'requires_prescription' => true, 'price' => 11000],
            ['name' => 'Celebrex 200mg', 'description' => 'Selective anti-inflammatory', 'category' => 'Anti-inflammatory', 'requires_prescription' => true, 'price' => 25000],
            ['name' => 'Mefenamic Acid 250mg', 'description' => 'Pain reliever for menstrual pain', 'category' => 'Painkillers', 'requires_prescription' => true, 'price' => 7000],
            ['name' => 'Ketoprofen 100mg', 'description' => 'Muscle pain reliever', 'category' => 'Anti-inflammatory', 'requires_prescription' => true, 'price' => 10000],

            // ==================== Antibiotics ====================
            ['name' => 'Amoxicillin 500mg', 'description' => 'Broad-spectrum antibiotic', 'category' => 'Antibiotics', 'requires_prescription' => true, 'price' => 15000],
            ['name' => 'Azithromycin 500mg', 'description' => 'Antibiotic for respiratory infections', 'category' => 'Antibiotics', 'requires_prescription' => true, 'price' => 25000],
            ['name' => 'Ciprofloxacin 500mg', 'description' => 'Antibiotic for urinary tract infections', 'category' => 'Antibiotics', 'requires_prescription' => true, 'price' => 18000],
            ['name' => 'Doxycycline 100mg', 'description' => 'Antibiotic for acne and skin infections', 'category' => 'Antibiotics', 'requires_prescription' => true, 'price' => 20000],
            ['name' => 'Clindamycin 300mg', 'description' => 'Antibiotic for bacterial infections', 'category' => 'Antibiotics', 'requires_prescription' => true, 'price' => 22000],
            ['name' => 'Cephalexin 500mg', 'description' => 'Antibiotic for skin and urinary infections', 'category' => 'Antibiotics', 'requires_prescription' => true, 'price' => 16000],
            ['name' => 'Levofloxacin 500mg', 'description' => 'Broad-spectrum antibiotic', 'category' => 'Antibiotics', 'requires_prescription' => true, 'price' => 28000],
            ['name' => 'Metronidazole 500mg', 'description' => 'Antibiotic for anaerobic bacteria and parasites', 'category' => 'Antibiotics', 'requires_prescription' => true, 'price' => 10000],
            ['name' => 'Clarithromycin 500mg', 'description' => 'Antibiotic for stomach and respiratory infections', 'category' => 'Antibiotics', 'requires_prescription' => true, 'price' => 24000],
            ['name' => 'Amoxicillin/Clavulanate 625mg', 'description' => 'Broad-spectrum antibiotic with inhibitor', 'category' => 'Antibiotics', 'requires_prescription' => true, 'price' => 20000],
            ['name' => 'Cefixime 200mg', 'description' => 'Antibiotic for ear and throat infections', 'category' => 'Antibiotics', 'requires_prescription' => true, 'price' => 19000],
            ['name' => 'Erythromycin 250mg', 'description' => 'Penicillin alternative antibiotic', 'category' => 'Antibiotics', 'requires_prescription' => true, 'price' => 14000],

            // ==================== Blood Pressure Medications ====================
            ['name' => 'Captopril 25mg', 'description' => 'Treatment for high blood pressure', 'category' => 'Blood Pressure', 'requires_prescription' => true, 'price' => 10000],
            ['name' => 'Lisinopril 10mg', 'description' => 'Treatment for high blood pressure', 'category' => 'Blood Pressure', 'requires_prescription' => true, 'price' => 13000],
            ['name' => 'Amlodipine 5mg', 'description' => 'Treatment for hypertension and angina', 'category' => 'Blood Pressure', 'requires_prescription' => true, 'price' => 14000],
            ['name' => 'Enalapril 10mg', 'description' => 'Treatment for high blood pressure', 'category' => 'Blood Pressure', 'requires_prescription' => true, 'price' => 12000],
            ['name' => 'Losartan 50mg', 'description' => 'Treatment for high blood pressure', 'category' => 'Blood Pressure', 'requires_prescription' => true, 'price' => 15000],
            ['name' => 'Valsartan 80mg', 'description' => 'Treatment for high blood pressure', 'category' => 'Blood Pressure', 'requires_prescription' => true, 'price' => 17000],
            ['name' => 'Bisoprolol 5mg', 'description' => 'Treatment for hypertension and anxiety', 'category' => 'Blood Pressure', 'requires_prescription' => true, 'price' => 13000],

            // ==================== Diabetes Medications ====================
            ['name' => 'Metformin 500mg', 'description' => 'Treatment for type 2 diabetes', 'category' => 'Diabetes', 'requires_prescription' => true, 'price' => 9000],
            ['name' => 'Insulin (Regular)', 'description' => 'Treatment for type 1 and type 2 diabetes', 'category' => 'Diabetes', 'requires_prescription' => true, 'price' => 35000],
            ['name' => 'Gliclazide 80mg', 'description' => 'Treatment for type 2 diabetes', 'category' => 'Diabetes', 'requires_prescription' => true, 'price' => 11000],
            ['name' => 'Sitagliptin 100mg', 'description' => 'Treatment for type 2 diabetes', 'category' => 'Diabetes', 'requires_prescription' => true, 'price' => 40000],
            ['name' => 'Pioglitazone 30mg', 'description' => 'Treatment for type 2 diabetes', 'category' => 'Diabetes', 'requires_prescription' => true, 'price' => 18000],
            ['name' => 'Glimepiride 2mg', 'description' => 'Treatment for type 2 diabetes', 'category' => 'Diabetes', 'requires_prescription' => true, 'price' => 10000],

            // ==================== Heart & Cholesterol ====================
            ['name' => 'Atorvastatin 20mg', 'description' => 'Cholesterol-lowering medication', 'category' => 'Cholesterol', 'requires_prescription' => true, 'price' => 16000],
            ['name' => 'Simvastatin 20mg', 'description' => 'Cholesterol-lowering medication', 'category' => 'Cholesterol', 'requires_prescription' => true, 'price' => 15000],
            ['name' => 'Clopidogrel 75mg', 'description' => 'Blood thinner to prevent clots', 'category' => 'Heart', 'requires_prescription' => true, 'price' => 20000],
            ['name' => 'Warfarin 5mg', 'description' => 'Blood thinner', 'category' => 'Heart', 'requires_prescription' => true, 'price' => 12000],
            ['name' => 'Rosuvastatin 10mg', 'description' => 'Cholesterol-lowering medication', 'category' => 'Cholesterol', 'requires_prescription' => true, 'price' => 22000],
            ['name' => 'Aspirin 75mg', 'description' => 'Blood thinner for clot prevention', 'category' => 'Heart', 'requires_prescription' => true, 'price' => 3000],
            ['name' => 'Digoxin 0.25mg', 'description' => 'Treatment for heart failure', 'category' => 'Heart', 'requires_prescription' => true, 'price' => 8000],

            // ==================== Allergy Medications ====================
            ['name' => 'Loratadine 10mg', 'description' => 'Allergy treatment', 'category' => 'Allergy', 'requires_prescription' => false, 'price' => 6000],
            ['name' => 'Cetirizine 10mg', 'description' => 'Treatment for allergies and cold symptoms', 'category' => 'Allergy', 'requires_prescription' => false, 'price' => 5500],
            ['name' => 'Fexofenadine 120mg', 'description' => 'Treatment for seasonal allergies', 'category' => 'Allergy', 'requires_prescription' => false, 'price' => 10000],
            ['name' => 'Desloratadine 5mg', 'description' => 'Treatment for chronic allergies', 'category' => 'Allergy', 'requires_prescription' => false, 'price' => 11000],
            ['name' => 'Levocetirizine 5mg', 'description' => 'Allergy treatment', 'category' => 'Allergy', 'requires_prescription' => false, 'price' => 9000],

            // ==================== Digestive Medications ====================
            ['name' => 'Omeprazole 20mg', 'description' => 'Treatment for acid reflux and heartburn', 'category' => 'Digestive', 'requires_prescription' => false, 'price' => 8000],
            ['name' => 'Pantoprazole 40mg', 'description' => 'Treatment for stomach ulcers', 'category' => 'Digestive', 'requires_prescription' => true, 'price' => 12000],
            ['name' => 'Domperidone 10mg', 'description' => 'Treatment for nausea and vomiting', 'category' => 'Digestive', 'requires_prescription' => true, 'price' => 7000],
            ['name' => 'Ranitidine 150mg', 'description' => 'Treatment for stomach ulcers', 'category' => 'Digestive', 'requires_prescription' => false, 'price' => 6000],
            ['name' => 'Esomeprazole 40mg', 'description' => 'Treatment for GERD', 'category' => 'Digestive', 'requires_prescription' => true, 'price' => 15000],
            ['name' => 'Lansoprazole 30mg', 'description' => 'Treatment for stomach ulcers', 'category' => 'Digestive', 'requires_prescription' => true, 'price' => 11000],

            // ==================== Vitamins & Supplements ====================
            ['name' => 'Vitamin C 1000mg', 'description' => 'Immune support supplement', 'category' => 'Vitamins', 'requires_prescription' => false, 'price' => 10000],
            ['name' => 'Vitamin D3 5000IU', 'description' => 'Bone health supplement', 'category' => 'Vitamins', 'requires_prescription' => false, 'price' => 15000],
            ['name' => 'Calcium + Vitamin D', 'description' => 'Bone and teeth supplement', 'category' => 'Supplements', 'requires_prescription' => false, 'price' => 12000],
            ['name' => 'Omega-3 1000mg', 'description' => 'Fatty acids for heart health', 'category' => 'Supplements', 'requires_prescription' => false, 'price' => 25000],
            ['name' => 'Zinc 50mg', 'description' => 'Immune support supplement', 'category' => 'Supplements', 'requires_prescription' => false, 'price' => 8000],
            ['name' => 'Magnesium 400mg', 'description' => 'Muscle and nerve support', 'category' => 'Supplements', 'requires_prescription' => false, 'price' => 13000],
            ['name' => 'Iron 100mg', 'description' => 'Treatment for anemia', 'category' => 'Supplements', 'requires_prescription' => false, 'price' => 7000],

            // ==================== Neurology & Sedatives ====================
            ['name' => 'Diazepam 5mg', 'description' => 'Sedative and sleeping aid', 'category' => 'Neurology', 'requires_prescription' => true, 'price' => 8000],
            ['name' => 'Amitriptyline 25mg', 'description' => 'Antidepressant', 'category' => 'Neurology', 'requires_prescription' => true, 'price' => 10000],
            ['name' => 'Propranolol 40mg', 'description' => 'Treatment for anxiety and tremors', 'category' => 'Neurology', 'requires_prescription' => true, 'price' => 9000],
            ['name' => 'Sertraline 50mg', 'description' => 'Antidepressant', 'category' => 'Neurology', 'requires_prescription' => true, 'price' => 18000],
            ['name' => 'Fluoxetine 20mg', 'description' => 'Antidepressant', 'category' => 'Neurology', 'requires_prescription' => true, 'price' => 15000],
            ['name' => 'Clonazepam 0.5mg', 'description' => 'Treatment for epilepsy and anxiety', 'category' => 'Neurology', 'requires_prescription' => true, 'price' => 12000],

            // ==================== Respiratory Medications ====================
            ['name' => 'Salbutamol Inhaler', 'description' => 'Bronchodilator for asthma', 'category' => 'Respiratory', 'requires_prescription' => true, 'price' => 15000],
            ['name' => 'Ambroxol 30mg', 'description' => 'Mucolytic (relieves mucus)', 'category' => 'Respiratory', 'requires_prescription' => false, 'price' => 5000],
            ['name' => 'Pseudoephedrine 60mg', 'description' => 'Nasal decongestant', 'category' => 'Respiratory', 'requires_prescription' => false, 'price' => 6000],
            ['name' => 'Budesonide Inhaler', 'description' => 'Chronic asthma treatment', 'category' => 'Respiratory', 'requires_prescription' => true, 'price' => 35000],
            ['name' => 'Montelukast 10mg', 'description' => 'Allergic asthma treatment', 'category' => 'Respiratory', 'requires_prescription' => true, 'price' => 20000],
            ['name' => 'Guaifenesin 200mg', 'description' => 'Expectorant', 'category' => 'Respiratory', 'requires_prescription' => false, 'price' => 4000],

            // ==================== Dermatology ====================
            ['name' => 'Clotrimazole Cream 1%', 'description' => 'Antifungal for skin', 'category' => 'Dermatology', 'requires_prescription' => false, 'price' => 7000],
            ['name' => 'Hydrocortisone Cream 1%', 'description' => 'Anti-inflammatory for itching', 'category' => 'Dermatology', 'requires_prescription' => false, 'price' => 8000],
            ['name' => 'Tretinoin Cream 0.05%', 'description' => 'Acne treatment', 'category' => 'Dermatology', 'requires_prescription' => true, 'price' => 20000],
            ['name' => 'Miconazole Cream', 'description' => 'Antifungal for athlete’s foot', 'category' => 'Dermatology', 'requires_prescription' => false, 'price' => 7500],
            ['name' => 'Betamethasone Cream', 'description' => 'Treatment for eczema', 'category' => 'Dermatology', 'requires_prescription' => true, 'price' => 12000],
            ['name' => 'Benzoyl Peroxide Gel 5%', 'description' => 'Acne treatment', 'category' => 'Dermatology', 'requires_prescription' => false, 'price' => 9000],

            // ==================== Eye & Ear Medications ====================
            ['name' => 'Chloramphenicol Eye Drops', 'description' => 'Antibiotic for eye infections', 'category' => 'Eye', 'requires_prescription' => true, 'price' => 8000],
            ['name' => 'Tobramycin Eye Drops', 'description' => 'Antibiotic for eye infections', 'category' => 'Eye', 'requires_prescription' => true, 'price' => 15000],
            ['name' => 'Ciprofloxacin Eye Drops', 'description' => 'Antibiotic for eye infections', 'category' => 'Eye', 'requires_prescription' => true, 'price' => 12000],
            ['name' => 'Dexamethasone Eye Drops', 'description' => 'Anti-inflammatory for eye', 'category' => 'Eye', 'requires_prescription' => true, 'price' => 10000],
            ['name' => 'Ciprofloxacin Ear Drops', 'description' => 'Antibiotic for ear infections', 'category' => 'Ear', 'requires_prescription' => true, 'price' => 13000],

            // ==================== Urinary Tract Medications ====================
            ['name' => 'Tamsulosin 0.4mg', 'description' => 'Treatment for enlarged prostate', 'category' => 'Urology', 'requires_prescription' => true, 'price' => 18000],
            ['name' => 'Finasteride 5mg', 'description' => 'Treatment for enlarged prostate', 'category' => 'Urology', 'requires_prescription' => true, 'price' => 20000],
            ['name' => 'Phenazopyridine 200mg', 'description' => 'Pain relief for urinary tract', 'category' => 'Urology', 'requires_prescription' => true, 'price' => 8000],

            // ==================== Hormonal Medications ====================
            ['name' => 'Levothyroxine 50mcg', 'description' => 'Treatment for hypothyroidism', 'category' => 'Hormones', 'requires_prescription' => true, 'price' => 10000],
            ['name' => 'Prednisolone 5mg', 'description' => 'Corticosteroid for inflammation', 'category' => 'Hormones', 'requires_prescription' => true, 'price' => 9000],
            ['name' => 'Dexamethasone 0.5mg', 'description' => 'Strong corticosteroid', 'category' => 'Hormones', 'requires_prescription' => true, 'price' => 8000],
        ];

        // Insert all medicines
        foreach ($medicines as $medicine) {
            Medicine::create($medicine);
        }
    }
}