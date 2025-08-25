<?php
$pageTitle = "Container Test - Gayar Plus";
require_once 'config.php';
include 'inc/header.php';
?>

<style>
.test-hero {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-navy) 100%);
    color: white;
    padding: 4rem 0;
    text-align: center;
}

.test-hero h1 {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
    text-align: center;
}

.test-hero p {
    font-size: 1.125rem;
    max-width: 700px;
    margin: 0 auto 1.5rem;
    opacity: 0.9;
    text-align: center;
}

.test-content {
    padding: 3rem 0;
}

.test-section {
    margin-bottom: 3rem;
}

.test-section h2 {
    color: var(--primary-navy);
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    text-align: center;
}
</style>

<div class="test-hero">
    <div class="container">
        <h1>اختبار الحاوية</h1>
        <p>هذا الاختبار للتحقق من أن المحتوى يتم توسيطه بشكل صحيح باستخدام فئة الحاوية</p>
    </div>
</div>

<div class="test-content">
    <div class="container">
        <section class="test-section">
            <h2>اختبار التوسيط</h2>
            <p>هذا النص يجب أن يكون في منتصف الصفحة وليس منتصبًا إلى اليمين أو اليسار.</p>
        </section>
    </div>
</div>

<?php include 'inc/modern-footer.php'; ?>