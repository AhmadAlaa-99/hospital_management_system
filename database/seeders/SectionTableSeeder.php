<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('section_translations')->delete();
        DB::table('sections')->delete();

        $sections = [
            [
                'name_ar' => 'قسم القلب والأوعية الدموية',
                'name_en' => 'Cardiology Department',
                'description_ar' => 'تخصص في تشخيص وعلاج أمراض القلب والشرايين مع أحدث أجهزة تخطيط القلب والقسطرة العلاجية.',
                'description_en' => 'Specialized care for heart and vascular diseases with modern ECG and catheterization services.',
            ],
            [
                'name_ar' => 'قسم المخ والأعصاب',
                'name_en' => 'Neurology Department',
                'description_ar' => 'رعاية متخصصة لأمراض الجهاز العصبي والصداع والصرع والسكتات الدماغية بإشراف أطباء أعصاب ذوي خبرة.',
                'description_en' => 'Specialized neurological care for headache, epilepsy, and stroke under experienced neurologists.',
            ],
            [
                'name_ar' => 'قسم الأطفال',
                'name_en' => 'Pediatrics Department',
                'description_ar' => 'رعاية صحية شاملة للرضع والأطفال والمراهقين تشمل التطعيمات والمتابعة الدورية وعلاج الأمراض الشائعة.',
                'description_en' => 'Comprehensive care for infants, children, and adolescents including vaccination and follow-up.',
            ],
            [
                'name_ar' => 'قسم العيون',
                'name_en' => 'Ophthalmology Department',
                'description_ar' => 'فحص وعلاج مشاكل الإبصار والمياه البيضاء والزرقاء وجراحات العيون الدقيقة.',
                'description_en' => 'Diagnosis and treatment of vision problems, cataract, glaucoma, and eye surgeries.',
            ],
            [
                'name_ar' => 'قسم الجراحة العامة',
                'name_en' => 'General Surgery Department',
                'description_ar' => 'عمليات جراحية عامة وتنظيرية مع رعاية قبل وبعد العملية في بيئة آمنة ومعقمة.',
                'description_en' => 'General and laparoscopic surgeries with safe pre and post operative care.',
            ],
            [
                'name_ar' => 'قسم الباطنة والجهاز الهضمي',
                'name_en' => 'Internal Medicine & Gastroenterology',
                'description_ar' => 'تشخيص وعلاج أمراض الجهاز الهضمي والكبد والمناظير التشخيصية والعلاجية.',
                'description_en' => 'Diagnosis and treatment of digestive and liver diseases with endoscopy services.',
            ],
        ];

        foreach ($sections as $data) {
            $section = new Section();
            $section->save();

            $section->translateOrNew('ar')->name = $data['name_ar'];
            $section->translateOrNew('ar')->description = $data['description_ar'];
            $section->translateOrNew('en')->name = $data['name_en'];
            $section->translateOrNew('en')->description = $data['description_en'];
            $section->save();
        }
    }
}
