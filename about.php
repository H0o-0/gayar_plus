<?php
$pageTitle = "من نحن - Gayar Plus";
require_once 'initialize.php';
require_once 'classes/TextCleaner.php';

// Removed direct topBarNav.php include to prevent duplicate navbar
// include 'inc/topBarNav.php';

include 'inc/header.php';
?>

<style>
/* About Page Specific Styles */
.about-hero {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-navy) 100%);
    color: white;
    padding: 4rem 0;
    text-align: center;
}

.about-hero h1 {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
    text-align: center;
}

.about-hero p {
    font-size: 1.125rem;
    max-width: 700px;
    margin: 0 auto 1.5rem;
    opacity: 0.9;
    text-align: center;
}

.about-content {
    padding: 3rem 0;
}

.about-section {
    margin-bottom: 3rem;
}

.about-section h2 {
    color: var(--primary-navy);
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    text-align: center;
}

.about-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.about-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--medium-gray);
    transition: var(--transition);
}

.about-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-xl);
}

.about-card i {
    font-size: 2rem;
    color: var(--primary-blue);
    margin-bottom: 0.75rem;
}

.about-card h3 {
    color: var(--text-primary);
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
}

.about-card p {
    color: var(--text-secondary);
    line-height: 1.6;
    font-size: 0.95rem;
}

@media (max-width: 768px) {
    .about-hero {
        padding: 3rem 0;
    }
    
    .about-hero h1 {
        font-size: 1.75rem;
    }
    
    .about-hero p {
        font-size: 1rem;
    }
    
    .about-content {
        padding: 1.5rem 0;
    }
    
    .about-section h2 {
        font-size: 1.375rem;
    }
    
    .about-grid {
        gap: 1rem;
    }
    
    .about-card {
        padding: 1.25rem;
    }
}
</style>

<!-- About Hero Section -->
<section class="about-hero">
    <div class="container">
        <h1>من نحن</h1>
        <p>اكتشف قصتنا وقيمنا وفريقنا الذي يعمل بجد لتقديم أفضل تجربة تسوق لملحقات الهواتف الذكية</p>
    </div>
</section>

<!-- About Content -->
<div class="about-content">
    <div class="container">
        <!-- Our Story -->
        <section class="about-section">
            <h2>قصتنا</h2>
            <div class="about-grid">
                <div class="about-card">
                    <i class="fas fa-history"></i>
                    <h3>البدايات المتواضعة</h3>
                    <p>بدأ متجر Gayar Plus كمشروع صغير في بغداد بهدف تقديم ملحقات الهواتف الذكية عالية الجودة للمستهلكين العراقيين. منذ البداية، كان لدينا رؤية واضحة لتغيير طريقة تسوق الملحقات في العراق.</p>
                </div>
                <div class="about-card">
                    <i class="fas fa-rocket"></i>
                    <h3>النمو والتوسع</h3>
                    <p>مع مرور الوقت، نمت أعمالنا لنصبح واحدة من أبرز المتاجر المتخصصة في ملحقات الهواتف الذكية في العراق. نجحنا في بناء ثقة كبيرة مع عملائنا من خلال تقديم منتجات أصلية وخدمة عملاء متميزة.</p>
                </div>
                <div class="about-card">
                    <i class="fas fa-bullseye"></i>
                    <h3>الرؤية المستقبلية</h3>
                    <p>نسعى لتوسيع نطاق خدماتنا لتشمل جميع محافظات العراق، وتقديم حلول مبتكرة تسهل على العملاء الحصول على أفضل المنتجات بأفضل الأسعار مع ضمان الأصالة والجودة.</p>
                </div>
            </div>
        </section>

        <!-- Our Values -->
        <section class="about-section">
            <h2>قيمنا</h2>
            <div class="about-grid">
                <div class="about-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>الجودة والأصالة</h3>
                    <p>نؤمن أن الجودة هي أساس النجاح، لذلك نقدم فقط منتجات أصلية مضمونة مع ضمان شامل ضد العيوب. نعمل مع موردين موثوقين لضمان رضا العملاء التام.</p>
                </div>
                <div class="about-card">
                    <i class="fas fa-heart"></i>
                    <h3>رضا العميل</h3>
                    <p>رضا العميل هو محور كل ما نقوم به. نسعى جاهدين لتوفير تجربة تسوق استثنائية من خلال خدمة عملاء متميزة ودعم فني متواصل على مدار الساعة.</p>
                </div>
                <div class="about-card">
                    <i class="fas fa-hand-holding-usd"></i>
                    <h3>الأسعار العادلة</h3>
                    <p>نؤمن بتوفير أفضل الأسعار في السوق العراقي دون المساس بجودة المنتجات. نعمل على تقديم عروض وتخفيضات منتظمة لعملائنا المميزين.</p>
                </div>
            </div>
        </section>
    </div>
</div>

<?php include 'inc/modern-footer.php'; ?>