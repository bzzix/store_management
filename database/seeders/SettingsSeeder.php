<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    // استخدم دالة set_setting لحفظ كل إعداد
    set_setting('appName', 'أولاد عبدالستار');
    set_setting('appManagerName', 'محمود حسن عبدالستار');
    set_setting('appInvoiceGenerate', true);
    set_setting('AppGetInstallments', 'everyYear');
    set_setting('appDisc', 'للأعلاف والأسمدة الزراعية');
    set_setting('appLogo', config('app.url').'/storage/branding/ETNvqx8K0CjhmnVIntWEmujNXeXxrcKD7DtOL0bl.png');
    set_setting('appMiniLogo', config('app.url').'/storage/branding/ETNvqx8K0CjhmnVIntWEmujNXeXxrcKD7DtOL0bl.png');
    set_setting('appDarkLogo', config('app.url').'/storage/branding/ETNvqx8K0CjhmnVIntWEmujNXeXxrcKD7DtOL0bl.png');
    set_setting('appMiniDarkLogo', config('app.url').'/storage/branding/ETNvqx8K0CjhmnVIntWEmujNXeXxrcKD7DtOL0bl.png');
    set_setting('appIcon', config('app.url').'/storage/branding/YP73ZWG3RWMntYG7oa185v0r6MtqRdAv3CWIONlL.png');
    set_setting('appMail', 'bzzixs@gmail.com');
    set_setting('appMobile', '01000944804 - 01062332549');
    set_setting('appPhone', '0473905067');
    set_setting('appHomepage', '1');
    set_setting('appAddress', 'كفر الشيخ - الرياض - قرية 4 الخرجين - خلف المدرسة الإبتدائية');
    set_setting('appNewAccount', '1');
    set_setting('appDefaultRole', 'user');
    set_setting('appPolicy', '<h1 style="box-sizing: border-box; font-family: NotoKufiArabic;">سياسة الخصوصية والحماية</h1>
        <p style="box-sizing: border-box; font-family: NotoKufiArabic;">تعتبر سياسة الخصوصية في نظام إدارة المحلات والمتاجر ملزمة لجميع مستخدمي النظام والعاملين دون استثناء. نحن ملتزمون بحماية بيانات عملائك والعاملين بكل حرص وأمان، وعدم إفشاء أي معلومات شخصية لطرف ثالث إلا بموافقة صريحة وخطية.</p>
        <h2 style="box-sizing: border-box; color: var(--main-color); font-family: NotoKufiArabic;">التزاماتنا تجاهكم</h2>
        <p style="box-sizing: border-box; font-family: NotoKufiArabic;">نتعهد بحماية كاملة لجميع البيانات المتعلقة بعملائك والعاملين في متجرك، والتعامل مع هذه البيانات بكل سرية وأمان. سيتم استخدام البيانات فقط لأغراض تشغيل النظام والخدمات المتفق عليها.</p>
        <h2 style="box-sizing: border-box; color: var(--main-color); font-family: NotoKufiArabic;">بنود الحماية الأساسية:</h2>
        <ol style="box-sizing: border-box; font-family: NotoKufiArabic;">
        <li style="box-sizing: border-box;">حماية بيانات المبيعات والعملاء من أي وصول غير مصرح</li>
        <li style="box-sizing: border-box;">عدم مشاركة معلومات العملاء مع طرف ثالث إلا بموافقة صريحة</li>
        <li style="box-sizing: border-box;">حماية كلمات المرور والبيانات الحساسة بتشفير قوي</li>
        <li style="box-sizing: border-box;">الالتزام بالقوانين المحلية والدولية المتعلقة بحماية البيانات</li>
        <li style="box-sizing: border-box;">تقديم دعم فني موثوق وآمن لجميع مستخدمي النظام</li>
        </ol>
        <p style="box-sizing: border-box; font-family: NotoKufiArabic;">يوافق المستخدم على هذه السياسة عند الاستخدام، وأي انتهاك لهذه السياسة قد يؤدي إلى توقف الخدمة واتخاذ الإجراءات القانونية اللازمة.</p>');
    set_setting('appTerms', '<h2 style="text-align: center; font-family: NotoKufiArabic;"><strong>شروط وأحكام استخدام نظام إدارة المحلات والمتاجر</strong></h2>
        <p style="font-family: NotoKufiArabic;">&nbsp;</p>
        <h3 style="font-family: NotoKufiArabic;">1. مقدمة</h3>
        <p style="font-family: NotoKufiArabic;">يوفر نظام إدارة المحلات والمتاجر حلاً متكاملاً لإدارة العمليات التجارية اليومية. الاستخدام المستمر للنظام يعني موافقتك على هذه الشروط والأحكام.</p>
        <h3 style="font-family: NotoKufiArabic;">2. تعريفات أساسية</h3>
        <ul style="font-family: NotoKufiArabic;">
        <li><strong>النظام:</strong> نظام إدارة المحلات والمتاجر الموفر بواسطة شركتنا</li>
        <li><strong>المستخدم:</strong> أي شخص يستخدم النظام أو يملك حساباً فيه</li>
        <li><strong>البيانات:</strong> جميع المعلومات التي تدخلها أو يتم تخزينها في النظام</li>
        </ul>
        <h3 style="font-family: NotoKufiArabic;">3. التزامات المستخدم</h3>
        <p style="font-family: NotoKufiArabic;">يتعهد المستخدم بـ:</p>
        <ul style="font-family: NotoKufiArabic;">
        <li>عدم استخدام النظام في أغراض غير قانونية أو غير أخلاقية</li>
        <li>الحفاظ على سرية كلمة المرور وعدم مشاركتها</li>
        <li>عدم محاولة الوصول لبيانات المستخدمين الآخرين</li>
        <li>الامتثال لجميع القوانين والتشريعات المحلية والدولية</li>
        <li>عدم إدخال بيانات كاذبة أو مضللة</li>
        </ul>
        <h3 style="font-family: NotoKufiArabic;">4. الاستخدام المقبول</h3>
        <p style="font-family: NotoKufiArabic;">يجب استخدام النظام فقط لإدارة متجرك والعمليات التجارية الشرعية. يُمنع بشكل صارم:</p>
        <ul style="font-family: NotoKufiArabic;">
        <li>إرسال فيروسات أو برامج ضارة</li>
        <li>محاولات الاختراق أو الهندسة الاجتماعية</li>
        <li>إساءة استخدام خوادم النظام</li>
        <li>الانخراط في أنشطة احتيالية</li>
        </ul>
        <h3 style="font-family: NotoKufiArabic;">5. المسؤولية والتعويضات</h3>
        <p style="font-family: NotoKufiArabic;">نحن لا نتحمل مسؤولية عن الخسائر غير المباشرة أو الأضرار الناشئة عن استخدام النظام. أنت تتحمل المسؤولية الكاملة عن استخدامك للنظام والبيانات المدخلة فيه.</p>
        <h3 style="font-family: NotoKufiArabic;">6. التعديلات والتحديثات</h3>
        <p style="font-family: NotoKufiArabic;">نحتفظ بحق تعديل أو تحديث النظام أو إلغاء بعض الخدمات في أي وقت. سيتم إخطارك بأي تغييرات جوهرية.</p>
        <h3 style="font-family: NotoKufiArabic;">7. الإنهاء</h3>
        <p style="font-family: NotoKufiArabic;">قد نقوم بإنهاء حسابك إذا قمت بانتهاك أي من هذه الشروط أو تعرضت المتطلبات القانونية ذلك.</p>
        <h3 style="font-family: NotoKufiArabic;">8. القانون الحاكم</h3>
        <p style="font-family: NotoKufiArabic;">تحكم هذه الشروط قوانين المملكة العربية السعودية، وتخضع لاختصاص المحاكم المختصة فيها.</p>
        <p style="font-family: NotoKufiArabic;"><strong>آخر تحديث: ديسمبر 2024</strong></p>');
                
    }
}
