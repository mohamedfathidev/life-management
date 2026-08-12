# نشر التطبيق على Hostinger

دليل خطوة‑بخطوة لرفع «سيبها على الله (فانية)» على استضافة Hostinger.

> **مهم:** خطة الاستضافة لازم تدعم **PHP 8.2+** و**MySQL**، ويفضّل **SSH** (متوفّر في خطة Business فما فوق، وفي الـ VPS). لو مفيش SSH ينفع بردو لكن أصعب شوية.

---

## نظرة عامة على الفكرة
- التطبيق فيه واجهة متبنية بـ Vite. **Hostinger المشتركة غالبًا مفيهاش Node**، عشان كده **هنبني الواجهة على جهازك (`npm run build`) ونرفع فولدر `public/build` جاهز** — كده مش محتاجين Node على السيرفر.
- نقطة دخول Laravel هي فولدر `public/`، فلازم نخلي **document root** للموقع يشاور على `public`.

---

## 1) جهّز نسخة الإنتاج على جهازك

```bash
# ابنِ الواجهة (بيولّد public/build)
npm run build

# نصّب حزم PHP لنسخة الإنتاج
composer install --optimize-autoloader --no-dev
```

جهّز ملف `.env` للإنتاج (هترفعه للسيرفر لاحقًا):
```env
APP_NAME="سيبها على الله (فانية)"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_TIMEZONE=Africa/Cairo
APP_LOCALE=ar

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=اسم_الداتابيز_من_هوستنجر
DB_USERNAME=يوزر_الداتابيز
DB_PASSWORD=باسورد_الداتابيز
```
> سيب `APP_KEY` فاضي دلوقتي — هنولّده على السيرفر (أو ولّده محليًا `php artisan key:generate --show` وانسخه).

---

## 2) اعمل قاعدة البيانات في hPanel
1. ادخل **hPanel → Databases → MySQL Databases**.
2. اعمل داتابيز جديدة + يوزر + باسورد، واربط اليوزر بالداتابيز بكل الصلاحيات.
3. سجّل: اسم الداتابيز، اليوزر، الباسورد، والـ Host (غالبًا `localhost`).
4. حطّهم في ملف `.env`.

---

## 3) ارفع الملفات

### الطريقة (أ) — SSH / Git (الأسهل والأنضف)
```bash
# اتصل بالسيرفر
ssh u123456789@your-server-ip -p 65002

# روح لفولدر الدومين
cd domains/your-domain.com

# استنسخ المشروع (أو ارفعه بـ Git deployment من hPanel)
git clone <repo-url> app
cd app

composer install --optimize-autoloader --no-dev
```
> لو رفعت بـ Git، تأكّد إن `public/build` **مرفوع** (لأن Node مش موجود على السيرفر). لو الـ `.gitignore` بيتجاهله، شيله من التجاهل وارفعه، أو ارفع الفولدر يدويًا بالـ File Manager.

### الطريقة (ب) — File Manager / FTP (من غير SSH)
1. اضغط المشروع zip على جهازك **بعد** ما عملت `npm run build` و`composer install --no-dev` (يعني الـ `vendor` و`public/build` موجودين). استبعد `node_modules` و`.git`.
2. من **hPanel → File Manager** ارفع الـ zip جوا فولدر الدومين وفكّ الضغط.

---

## 4) اضبط document root على `public`
Laravel لازم يخدم من فولدر `public`:
- **hPanel → Websites → (دومينك) → Advanced / Configuration → Document Root** → غيّره لـ:
  `.../domains/your-domain.com/app/public`

> لو خطتك مش بتسمح بتغيير الـ document root، الحل البديل: انقل محتويات `app/public/*` جوا `public_html/`، وعدّل في `public_html/index.php` المسارين اللي بيعملوا `require` لـ `vendor/autoload.php` و`bootstrap/app.php` عشان يشاوروا على فولدر التطبيق. (تغيير الـ document root أنضف بكتير.)

---

## 5) الإعداد النهائي على السيرفر (عبر SSH)
```bash
# لو مرفعتش .env، اعمله
cp .env.example .env   # ثم عدّل بيانات الإنتاج والداتابيز

php artisan key:generate
php artisan migrate --force

# كاش الإنتاج (سرعة)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# اعمل مستخدم الدخول (التسجيل مقفول)
php artisan app:create-user
```
> بعد أي تحديث للكود مستقبلًا: `php artisan optimize:clear` ثم اعمل الكاش تاني.

---

## 6) صلاحيات الفولدرات
لازم `storage` و`bootstrap/cache` يكونوا قابلين للكتابة:
```bash
chmod -R 775 storage bootstrap/cache
```

---

## 7) فعّل HTTPS (SSL)
- **hPanel → Security → SSL** → فعّل الشهادة المجانية للدومين.
- تأكّد إن `APP_URL` في `.env` بيبدأ بـ `https://`.
- التطبيق مضبوط أصلًا على **trusted proxies**، فالـ HTTPS و**تثبيت PWA** هيشتغلوا صح.

---

## 8) (اختياري) الكرون — للمهام المجدولة مستقبلًا
لو ضفنا لاحقًا مهام مجدولة (زي إشعارات push في مواعيدها)، ضيف Cron Job من **hPanel → Advanced → Cron Jobs**:
```
* * * * * php /home/u123456789/domains/your-domain.com/app/artisan schedule:run >> /dev/null 2>&1
```

---

## استكشاف الأخطاء
| المشكلة | الحل |
|---|---|
| صفحة بيضا / خطأ 500 | خلي `APP_DEBUG=true` مؤقتًا وشوف الرسالة، أو راجع `storage/logs/laravel.log` |
| «No application encryption key» | `php artisan key:generate` |
| الـ CSS/الشكل مش ظاهر | تأكّد إن `public/build` مرفوع، والـ document root على `public`، و`APP_URL` صح بـ https |
| خطأ اتصال بالداتابيز | راجع `DB_*` في `.env` (غالبًا `DB_HOST=localhost`) |
| صلاحيات | `chmod -R 775 storage bootstrap/cache` |
| بعد التعديل مفيش تغيير | `php artisan optimize:clear` |

---

**بعد كده:** افتح دومينك → `/login` → ادخل بالمستخدم اللي عملته. ومن الموبايل تقدر **تثبّت التطبيق** (PWA) مباشرة لأنه على HTTPS. 🎉
