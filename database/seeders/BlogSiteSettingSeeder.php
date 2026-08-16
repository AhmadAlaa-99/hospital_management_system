<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BlogSiteSettingSeeder extends Seeder
{
    public function run()
    {
        SiteSetting::query()->delete();
        SiteSetting::create([
            'hospital_name' => 'مستشفى الشام التخصصي',
            'address' => 'شارع أبو رمانة، مقابل حديقة الجلاء',
            'city' => 'دمشق، الجمهورية العربية السورية',
            'phone' => '+963 11 334 2200',
            'phone2' => '+963 933 456 789',
            'email' => 'info@alsham-hospital.sy',
            'working_hours' => 'السبت - الخميس: 8:00 صباحاً حتى 8:00 مساءً',
            'facebook' => 'https://facebook.com',
            'twitter' => 'https://twitter.com',
            'instagram' => 'https://instagram.com',
            'linkedin' => 'https://linkedin.com',
            'whatsapp' => '+963933456789',
            'about' => 'مستشفى الشام التخصصي مؤسسة طبية سورية تقدم خدمات التشخيص والعلاج والرعاية الشاملة للمرضى في دمشق وريفها.',
            'copyright' => 'مستشفى الشام التخصصي © جميع الحقوق محفوظة',
        ]);

        Blog::query()->delete();

        $articles = [
            [
                'title' => 'أهمية التشخيص المبكر لأمراض القلب في سوريا',
                'slug' => 'heart-early-diagnosis-syria',
                'image' => 'WebSite/images/hms/blogs/heart.jpg',
                'excerpt' => 'يساعد التشخيص المبكر على تقليل مضاعفات أمراض القلب وتحسين جودة حياة المرضى في المستشفيات السورية.',
                'body' => "يعد التشخيص المبكر لأمراض القلب من أهم ركائز الطب الوقائي في سوريا.\n\nيوفر مستشفى الشام التخصصي فحوصات قلب متقدمة تشمل تخطيط القلب والإيكو وجهاز الجهد، بإشراف أطباء اختصاصيين.\n\nننصح بمراجعة الطبيب عند الشعور بألم في الصدر أو ضيق التنفس أو تسارع نبضات القلب، خاصة لمن لديهم تاريخ عائلي أو يعانون من ضغط الدم والسكري.\n\nالرعاية المبكرة تقلل الحاجة للتدخلات الجراحية وتحسن نتائج العلاج.",
                'likes' => 126,
                'published_at' => Carbon::now()->subDays(3),
            ],
            [
                'title' => 'دليل الفحوصات المخبرية اللازمة قبل العمليات',
                'slug' => 'lab-tests-before-surgery',
                'image' => 'WebSite/images/hms/blogs/lab.jpg',
                'excerpt' => 'تعرّف على التحاليل الأساسية التي يطلبها الأطباء قبل الجراحة لضمان سلامة المريض.',
                'body' => "قبل أي عمل جراحي، يجري الفريق الطبي مجموعة فحوصات مخبرية لتقييم حالة المريض.\n\nمن أهمها: تعداد الدم الكامل، وظائف الكلى والكبد، سكر الدم، وزمرة الدم.\n\nفي مستشفى الشام التخصصي تتم هذه التحاليل داخل المختبر المركزي بسرعة ودقة، مع إرسال إشعار للطبيب والمريض عند اكتمال النتائج.\n\nالالتزام بتعليمات الصيام قبل التحليل يضمن نتائج دقيقة وخطة علاجية آمنة.",
                'likes' => 98,
                'published_at' => Carbon::now()->subDays(7),
            ],
            [
                'title' => 'نصائح للعناية بصحة الأطفال خلال فصل الشتاء',
                'slug' => 'children-winter-care-tips',
                'image' => 'WebSite/images/hms/blogs/children.jpg',
                'excerpt' => 'إرشادات طبية للأهل في دمشق لحماية الأطفال من نزلات البرد والالتهابات التنفسية.',
                'body' => "مع انخفاض درجات الحرارة في الشتاء السوري تزداد حالات الزكام والتهاب القصبات لدى الأطفال.\n\nننصح بالتدفئة المعتدلة، شرب السوائل، والنوم الكافي، مع تجنب التدخين داخل المنزل.\n\nعيادة الأطفال في مستشفى الشام التخصصي تقدم متابعة دورية وتلقيحاً وفق الجداول المعتمدة.\n\nفي حال ارتفاع الحرارة المستمر أو صعوبة التنفس يجب مراجعة الطوارئ فوراً.",
                'likes' => 84,
                'published_at' => Carbon::now()->subDays(12),
            ],
            [
                'title' => 'خدمات الأشعة الحديثة ودورها في دقة التشخيص',
                'slug' => 'modern-radiology-services',
                'image' => 'WebSite/images/hms/blogs/radiology.jpg',
                'excerpt' => 'أجهزة الأشعة والتصوير تساعد الأطباء على اتخاذ قرارات علاجية أدق وأسرع.',
                'body' => "يضم قسم الأشعة في مستشفى الشام التخصصي أجهزة تصوير حديثة تشمل الأشعة الرقمية والإيكو والطبقي المحوري وفق التوفر.\n\nتُرسل النتائج مباشرة إلى الطبيب المعالج والمريض عبر لوحة التحكم.\n\nالدقة في التصوير تقلل الأخطاء وتسرّع خطة العلاج، خاصة في حالات الطوارئ والإصابات.",
                'likes' => 112,
                'published_at' => Carbon::now()->subDays(18),
            ],
        ];

        foreach ($articles as $index => $article) {
            Blog::create([
                'title' => $article['title'],
                'slug' => $article['slug'],
                'image' => $article['image'],
                'excerpt' => $article['excerpt'],
                'body' => $article['body'],
                'author' => 'إدارة المستشفى',
                'likes' => 0,
                'views' => 0,
                'is_published' => true,
                'published_at' => $article['published_at'],
            ]);
        }
    }
}
