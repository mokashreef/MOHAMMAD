<?php
// ========================================
// Setup Script - Run Once to Create Tables & Seed Data
// ========================================
// زُر هذا الملف مرة واحدة في المتصفح لإنشاء الجداول والبيانات الأولية
// بعد التشغيل احذفه أو أُعد تسميته لأسباب أمنية
// ========================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/api/config.php';

echo "<html dir='rtl'><head><meta charset='utf-8'><title>إعداد قاعدة البيانات</title>
<style>body{font-family:sans-serif;max-width:700px;margin:40px auto;padding:20px;background:#0f1117;color:#e2e8f0;}
.ok{color:#22c55e;}.err{color:#ef4444;}.warn{color:#f59e0b;}h1{color:#6366f1;}
pre{background:#1c1f2e;padding:15px;border-radius:8px;overflow-x:auto;font-size:14px;}
</style></head><body>";

echo "<h1>⚡ إعداد قاعدة البيانات</h1>";

try {
    $db = getDB();
    echo "<p class='ok'>✅ تم الاتصال بقاعدة البيانات بنجاح</p>";
} catch (Exception $e) {
    echo "<p class='err'>❌ فشل الاتصال: " . $e->getMessage() . "</p>";
    echo "<p class='warn'>تأكد من تعديل بيانات الاتصال في <code>api/config.php</code></p>";
    exit;
}

// Create tables
$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        nameEn VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS projects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(500) NOT NULL,
        titleEn VARCHAR(500) DEFAULT '',
        description TEXT DEFAULT '',
        descriptionEn TEXT DEFAULT '',
        image VARCHAR(500) DEFAULT '',
        link VARCHAR(500) DEFAULT '#',
        category_id INT,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS sections (
        id INT AUTO_INCREMENT PRIMARY KEY,
        section_key VARCHAR(100) NOT NULL UNIQUE,
        content JSON,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

foreach ($tables as $sql) {
    try {
        $db->exec($sql);
    } catch (Exception $e) {
        echo "<p class='err'>❌ خطأ في إنشاء جدول: " . $e->getMessage() . "</p>";
    }
}
echo "<p class='ok'>✅ تم إنشاء الجداول</p>";

// Check if already seeded
$stmt = $db->query("SELECT COUNT(*) as cnt FROM users");
$count = $stmt->fetch()['cnt'];

if ($count > 0) {
    echo "<p class='warn'>⚠️ البيانات موجودة مسبقاً. لإعادة التعبئة، احذف البيانات أولاً.</p>";
    echo "</body></html>";
    exit;
}

// ========================================
// Seed Admin User
// ========================================
$adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
$db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)")
   ->execute(['محمد', 'admin@admin.com', $adminPassword]);
echo "<p class='ok'>✅ تم إنشاء حساب المدير: <code>admin@admin.com</code> / <code>admin123</code></p>";

// ========================================
// Seed Categories
// ========================================
$db->exec("INSERT INTO categories (name, nameEn, slug) VALUES
    ('فرونت إند', 'Frontend', 'frontend'),
    ('بلوجر', 'Blogger', 'blogger'),
    ('ووردبريس', 'WordPress', 'wordpress')
");
echo "<p class='ok'>✅ تم إنشاء التصنيفات</p>";

// Get category IDs
$categories = [];
$stmt = $db->query("SELECT id, slug FROM categories");
while ($row = $stmt->fetch()) {
    $categories[$row['slug']] = $row['id'];
}

$fe = $categories['frontend'];
$bl = $categories['blogger'];
$wp = $categories['wordpress'];

// ========================================
// Seed Projects
// ========================================
$projects = [
    // Frontend
    [$fe, 'تطبيق إدارة الميزانية', 'Budget Management App', '/img/Budget App.png', 'https://mokashreef.github.io/comprehensive-budget-management/', 1],
    [$fe, 'موقع توصيل الزعيم', 'Alzaim Delivery', '/img/Alzaim.png', 'https://mokashreef.github.io/alzaim', 2],
    [$fe, 'فلاش كريبتو', 'Flash Crypto', '/img/Flash-crypto.png', 'https://mokashreef.github.io/Flash-crypto', 3],
    [$fe, 'داشبورد عصري', 'Modern Dashboard', '/img/Dashboard.png', 'https://mokashreef.github.io/dashboard', 4],
    [$fe, 'موقع ألعاب Lugx', 'Lugx Gaming', '/img/ugx-gaming.png', 'https://mokashreef.github.io/lugx-gaming', 5],
    [$fe, 'متتبع وقت الكود', 'Code Timer Tracker', '/img/Code-Timer-Tracker.png', 'https://mokashreef.github.io/Code-Timer-Tracker/', 6],
    [$fe, 'قائمة المهام', 'To-Do List', '/img/o-Do-List.png', 'https://mokashreef.github.io/To-Do-List/', 7],
    [$fe, 'مؤقت عكسي', 'Countdown Timer', '/img/countdown-Timer.png', 'https://mokashreef.github.io/countdown-Timer/', 8],
    [$fe, 'كود كيو', 'CodeQ', '/img/codeq.png', 'https://mokashreef.github.io/codeq/', 9],
    [$fe, 'تصميم موقع كامل', 'Full Website Design', '/img/code.png', 'https://mokashreef.github.io/code', 10],
    [$fe, 'دكتور لاين', 'DoctorLine', '/img/DoctorLine.png', 'https://mokashreef.github.io/DoctorLine/', 11],
    // Blogger
    [$bl, 'مدونة وسيم تيك', 'Waseem Tech Blog', '/img/waseem-tech.png', 'https://www.waseem-tech.com/', 12],
    [$bl, 'عبد الرحمن أكاديمي', 'Abdelrahman Academy', '/img/abdelrahman.png', 'https://www.abdelrahman-academy.com/', 13],
    [$bl, 'محمد سالم بن رباع', 'Mohamed Salem', '/img/msar25.png', 'https://www.msar25.com/', 14],
    [$bl, 'بوصلة المسافر', 'Departure Ticket', '', 'https://www.departureticket.com/', 15],
    [$bl, 'مدونة الرياضة', 'Sports Blog', '/img/f2025.png', 'https://www.f-2025.com/', 16],
    [$bl, 'أوروبا اليوم', 'Europa Today', '/img/europa.png', 'https://europa-heute.blogspot.com/', 17],
    [$bl, 'مدونة العراق', 'Iraq Blog', '/img/aliraqbasra.png', 'https://aliraqbasra.blogspot.com/', 18],
    [$bl, 'مدونة أبيكي', 'Apppiki Blog', '', 'https://apppikiapps.blogspot.com/', 19],
    [$bl, 'عرب جيمز', 'Arab Games', '', 'https://arabgameings.blogspot.com/', 20],
    [$bl, 'ويستمر الخبر', 'News Blog', '', 'https://wastmral5abr.blogspot.com/', 21],
    // WordPress
    [$wp, 'منصة كود التطور', 'Code Elta6ur', '/img/code elta6ur.png', 'https://code-elta6ur.com/', 22],
    [$wp, 'صحتي تاج', 'Sihtitaj', '/img/sihtitaj.png', 'https://sihtitaj.com/', 23],
    [$wp, 'سياحة وسفر', 'Travel & Tourism', '/img/alons.png', 'https://alons-safr88.com', 24],
    [$wp, 'سوق الرومية', 'Sougroumia Store', '/img/sougroumia.png', 'https://sougroumia.com/', 25],
];

$stmt = $db->prepare("INSERT INTO projects (category_id, title, titleEn, image, link, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
foreach ($projects as $p) {
    $stmt->execute($p);
}
echo "<p class='ok'>✅ تم إنشاء " . count($projects) . " مشروع</p>";

// ========================================
// Seed Sections
// ========================================
$heroContent = json_encode([
    'title' => 'أهلاً، أنا <span style="color:var(--primary-color)">محمد</span>',
    'titleEn' => 'Hi, I\'m <span style="color:var(--primary-color)">Mohammad</span>',
    'subtitle' => 'مهندس برمجيات ومطور واجهات مستخدم',
    'subtitleEn' => 'Software Engineer & Frontend Developer',
    'description' => 'مؤسس منصة وقناة "كود التطور". أحول الأفكار المعقدة إلى تجارب رقمية سلسة، سريعة، وجميلة.',
    'descriptionEn' => 'Founder of "Code Elta6ur" platform & channel. I turn complex ideas into smooth, fast, and beautiful digital experiences.',
    'btnPrimary' => 'اطلب خدمتك',
    'btnPrimaryEn' => 'Request Service',
    'btnSecondary' => 'شاهد أعمالي',
    'btnSecondaryEn' => 'View My Work'
], JSON_UNESCAPED_UNICODE);

$aboutContent = json_encode([
    'text1' => 'أنا محمد، مهندس برمجيات ومؤسس منصة "كود التطور". شغفي هو التكنولوجيا وكيف يمكنها تسهيل حياتنا.',
    'text1En' => 'I\'m Mohammad, a software engineer and founder of the "Code Elta6ur" platform. My passion is technology and how it can make our lives easier.',
    'text2' => 'أمتلك خبرة واسعة في تطوير الواجهات الأمامية (Frontend) وإدارة أنظمة المحتوى (Blogger & Wordpress). أهتم بأدق التفاصيل، من نظافة الكود إلى جمالية التصميم وسرعة الأداء.',
    'text2En' => 'I have extensive experience in frontend development and content management systems (Blogger & WordPress). I pay attention to the finest details, from clean code to beautiful design and fast performance.',
    'text3' => 'هدفي هو تمكين الأفراد والشركات من التواجد الرقمي القوي من خلال حلول تقنية مبتكرة.',
    'text3En' => 'My goal is to empower individuals and businesses with a strong digital presence through innovative technical solutions.',
    'image' => '/img/me.png'
], JSON_UNESCAPED_UNICODE);

$contactContent = json_encode([
    'email' => 'qaseemmohammad60@gmail.com',
    'whatsapp' => 'https://iwtsp.com/963983769230',
    'telegram' => 'https://t.me/mo_a_kashreef',
    'youtube' => 'https://www.youtube.com/@code-elta6ur',
    'formAction' => 'https://formspree.io/f/mqakjvzj'
], JSON_UNESCAPED_UNICODE);

$db->prepare("INSERT INTO sections (section_key, content) VALUES (?, ?)")->execute(['hero', $heroContent]);
$db->prepare("INSERT INTO sections (section_key, content) VALUES (?, ?)")->execute(['about', $aboutContent]);
$db->prepare("INSERT INTO sections (section_key, content) VALUES (?, ?)")->execute(['contact', $contactContent]);

echo "<p class='ok'>✅ تم إنشاء أقسام الصفحة</p>";

echo "<hr>";
echo "<h2>🎉 تم الإعداد بنجاح!</h2>";
echo "<pre>";
echo "🔑 بيانات تسجيل الدخول:\n";
echo "   البريد: admin@admin.com\n";
echo "   كلمة المرور: admin123\n\n";
echo "🌐 روابط مهمة:\n";
echo "   لوحة التحكم: /admin/login.html\n";
echo "   الموقع: /index.html\n";
echo "</pre>";
echo "<p class='warn'>⚠️ مهم: احذف هذا الملف (setup.php) بعد التشغيل لأسباب أمنية!</p>";
echo "</body></html>";
