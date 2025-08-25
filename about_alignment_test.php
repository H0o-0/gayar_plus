<?php
$pageTitle = "About Alignment Test - Gayar Plus";
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

.test-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.test-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--medium-gray);
    transition: var(--transition);
}

.test-card i {
    font-size: 2rem;
    color: var(--primary-blue);
    margin-bottom: 0.75rem;
}

.test-card h3 {
    color: var(--text-primary);
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
}

.test-card p {
    color: var(--text-secondary);
    line-height: 1.6;
    font-size: 0.95rem;
}
</style>

<div class="test-hero">
    <div class="container">
        <h1>من نحن</h1>
        <p>اكتشف قصتنا وقيمنا وفريقنا الذي يعمل بجد لتقديم أفضل تجربة تسوق لملحقات الهواتف الذكية</p>
    </div>
</div>

<div class="test-content">
    <div class="container">
        <section class="test-section">
            <h2>قصتنا</h2>
            <div class="test-grid">
                <div class="test-card">
                    <i class="fas fa-history"></i>
                    <h3>البدايات المتواضعة</h3>
                    <p>بدأ متجر Gayar Plus كمشروع صغير في بغداد بهدف تقديم ملحقات الهواتف الذكية عالية الجودة للمستهلكين العراقيين. منذ البداية، كان لدينا رؤية واضحة لتغيير طريقة تسوق الملحقات في العراق.</p>
                </div>
                <div class="test-card">
                    <i class="fas fa-rocket"></i>
                    <h3>النمو والتوسع</h3>
                    <p>مع مرور الوقت، نمت أعمالنا لنصبح واحدة من أبرز المتاجر المتخصصة في ملحقات الهواتف الذكية في العراق. نجحنا في بناء ثقة كبيرة مع عملائنا من خلال تقديم منتجات أصلية وخدمة عملاء متميزة.</p>
                </div>
                <div class="test-card">
                    <i class="fas fa-bullseye"></i>
                    <h3>الرؤية المستقبلية</h3>
                    <p>نسعى لتوسيع نطاق خدماتنا لتشمل جميع محافظات العراق، وتقديم حلول مبتكرة تسهل على العملاء الحصول على أفضل المنتجات بأفضل الأسعار مع ضمان الأصالة والجودة.</p>
                </div>
            </div>
        </section>
    </div>
</div>

<?php include 'inc/modern-footer.php'; ?>