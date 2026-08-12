# سيبها على الله (فانية) — منصّة إدارة الحياة

تطبيق شخصي لإدارة حياتك: أحلام، أهداف، مخطط يومي، عادات، تحديات، تعافٍ، مذكرات، دين، مالية، كارير، وأكتر — عربي بالكامل (RTL)، وضع داكن، وقابل للتثبيت على الموبايل (PWA).

> **ملاحظة:** التطبيق **single-user** والتسجيل من المتصفح **مقفول**. كل واحد بيشغّل نسخته الخاصة وبيعمل مستخدمه بأمر واحد (خطوة ٧).

---

## المتطلبات

| الأداة | النسخة |
|---|---|
| PHP | 8.2 أو أحدث (مع إضافات: `gd`, `pdo_mysql`, `mbstring`, `openssl`) |
| Composer | 2.x |
| Node.js + npm | Node 18+ |
| MySQL / MariaDB | أي نسخة حديثة (XAMPP / Laragon تمام) |

---

## خطوات التنصيب

### 1) نزّل المشروع
```bash
git clone <repo-url> life
cd life
```
(أو فك ضغط الملفات في فولدر واعمل `cd` عليه.)

### 2) نصّب حزم PHP
```bash
composer install
```

### 3) نصّب حزم الواجهة
```bash
npm install
```

### 4) جهّز ملف الإعدادات
```bash
cp .env.example .env
php artisan key:generate
```

### 5) اعمل قاعدة البيانات
اعمل قاعدة بيانات فاضية اسمها `life` (من phpMyAdmin أو):
```bash
mysql -u root -e "CREATE DATABASE life CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```
لو بياناتك مختلفة، عدّل `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD` في ملف `.env`.

### 6) جهّز الجداول
```bash
php artisan migrate
```

### 7) اعمل مستخدم للدخول (بدل التسجيل)
```bash
php artisan app:create-user
```
هيسألك الاسم والبريد وكلمة المرور. (أو مرّرهم مباشرة: `php artisan app:create-user "اسمك" you@example.com`)

### 8) ابنِ ملفات الواجهة
```bash
npm run build
```

### 9) شغّل التطبيق
```bash
php artisan serve
```
افتح **http://127.0.0.1:8000** وسجّل الدخول ببيانات الخطوة ٧. 🎉

---

## تثبيته على الموبايل (PWA)

التثبيت محتاج **HTTPS** (أو localhost). أسهل طريقة للتجربة على الموبايل هي نفق HTTPS مؤقّت:

```bash
# بعد ما تشغّل php artisan serve على 8000
ngrok http 8000
```
افتح رابط الـ `https://...ngrok...` على موبايلك → من قائمة المتصفح اختر **«تثبيت التطبيق / إضافة للشاشة الرئيسية»**.

> التطبيق مضبوط على **trusted proxies** فبيشتغل صح من خلال النفق (الـ CSS/JS بيتحمّلوا بـ HTTPS).

---

## أوامر مفيدة

| الأمر | الوظيفة |
|---|---|
| `php artisan app:create-user` | إنشاء مستخدم جديد |
| `php artisan migrate` | تشغيل أي جداول جديدة |
| `php artisan migrate:fresh` | **مسح كل البيانات** والبدء من جديد |
| `npm run dev` | وضع التطوير (watch) بدل `build` |
| `php artisan optimize:clear` | تنظيف الكاش |

---

## التقنية
Laravel 12 · Livewire 3 + Alpine.js · Tailwind CSS · MySQL · محرّر Trix · التوقيت: القاهرة.

الحقول الحسّاسة (المذكرات، ملاحظات التعافي، التغذية الذهنية) **مشفّرة** في قاعدة البيانات، والمذكرات والتعافي خلف **قفل PIN** إضافي (تفعّله من الملف الشخصي).
