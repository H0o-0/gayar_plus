<?php
/**
 * ملف اختبار الجلسة - للتحقق من بيانات المستخدم المسجل
 */

require_once('../config.php');
require_once('inc/sess_auth.php');

header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>فحص الجلسة</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            direction: rtl; 
            text-align: right;
            padding: 20px;
        }
        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-right: 4px solid #007bff;
        }
        .success { border-right-color: #28a745; background: #d4edda; }
        .warning { border-right-color: #ffc107; background: #fff3cd; }
        .danger { border-right-color: #dc3545; background: #f8d7da; }
    </style>
</head>
<body>

<h1>🔍 فحص حالة الجلسة</h1>

<div class="info-box">
    <h3>معلومات PHP:</h3>
    <ul>
        <li>إصدار PHP: <?php echo phpversion() ?></li>
        <li>حالة الجلسة: <?php echo session_status() == PHP_SESSION_ACTIVE ? 'نشطة' : 'غير نشطة' ?></li>
        <li>معرف الجلسة: <?php echo session_id() ?></li>
    </ul>
</div>

<?php if (!isset($_SESSION['userdata'])): ?>
    <div class="info-box danger">
        <h3>❌ لست مسجل دخول</h3>
        <p>يجب عليك تسجيل الدخول أولاً</p>
        <a href="login.php">انقر هنا لتسجيل الدخول</a>
    </div>
<?php else: ?>
    <div class="info-box success">
        <h3>✅ أنت مسجل دخول بنجاح</h3>
        <h4>بيانات المستخدم:</h4>
        <ul>
            <?php foreach ($_SESSION['userdata'] as $key => $value): ?>
                <li><strong><?php echo $key ?>:</strong> <?php echo htmlspecialchars($value) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <?php if ($_SESSION['userdata']['login_type'] == 1): ?>
        <div class="info-box success">
            <h3>✅ لديك صلاحية الوصول للمخزن</h3>
            <p>نوع المستخدم: مدير (login_type = 1)</p>
            <a href="index.php?page=warehouse" class="btn btn-primary">دخول المخزن</a>
        </div>
    <?php else: ?>
        <div class="info-box danger">
            <h3>❌ ليس لديك صلاحية الوصول للمخزن</h3>
            <p>نوع المستخدم: <?php echo $_SESSION['userdata']['login_type'] ?> (يجب أن يكون 1)</p>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="info-box">
    <h3>📋 جميع متغيرات الجلسة:</h3>
    <pre><?php print_r($_SESSION) ?></pre>
</div>

<hr>
<p><a href="index.php">← العودة للوحة التحكم</a></p>

</body>
</html>
