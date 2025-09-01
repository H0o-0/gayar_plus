<?php
/**
 * صفحة اختبار القائمة المتدرجة
 * Test page for the mega menu dropdown
 */

$pageTitle = 'اختبار القائمة المتدرجة';
require_once 'config.php';
include 'inc/header.php';
?>

<style>
/* تنسيق خاص لصفحة الاختبار */
.test-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 2rem;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.test-header {
    text-align: center;
    margin-bottom: 3rem;
    padding-bottom: 2rem;
    border-bottom: 2px solid #e2e8f0;
}

.test-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, #3b82f6, #1e40af);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.test-description {
    font-size: 1.1rem;
    color: #64748b;
    max-width: 800px;
    margin: 0 auto;
    line-height: 1.6;
}

.test-section {
    margin-bottom: 3rem;
    padding: 2rem;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.test-section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.test-instructions {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    border-left: 4px solid #3b82f6;
    margin-bottom: 2rem;
}

.test-instructions h4 {
    color: #3b82f6;
    font-weight: 600;
    margin-bottom: 1rem;
}

.test-instructions ul {
    margin: 0;
    padding-right: 1.5rem;
}

.test-instructions li {
    margin-bottom: 0.5rem;
    color: #475569;
}

.highlight-menu {
    background: #fef3c7;
    padding: 1rem;
    border-radius: 8px;
    border: 2px dashed #f59e0b;
    text-align: center;
    margin: 2rem 0;
}

.highlight-menu p {
    margin: 0;
    color: #92400e;
    font-weight: 600;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.stat-number {
    font-size: 2rem;
    font-weight: 800;
    color: #3b82f6;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 500;
}

.demo-badge {
    display: inline-block;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 1rem;
}
</style>

<div class="test-container">
    <div class="test-header">
        <div class="demo-badge">🚀 صفحة اختبار القائمة المتدرجة</div>
        <h1 class="test-title">اختبار القائمة المتدرجة للملحقات</h1>
        <p class="test-description">
            هذه الصفحة مخصصة لاختبار القائمة المتدرجة ثلاثية المستويات للملحقات. 
            تحتوي القائمة على العلامات التجارية، الفئات، والموديلات مع ربط كامل بقاعدة البيانات.
        </p>
    </div>

    <div class="test-section">
        <h2 class="test-section-title">
            <i class="fas fa-clipboard-list"></i>
            تعليمات الاختبار
        </h2>
        
        <div class="test-instructions">
            <h4>كيفية اختبار القائمة المتدرجة:</h4>
            <ul>
                <li><strong>المستوى الأول:</strong> مرر مؤشر الفأرة على "ملحقات" في شريط التنقل أعلاه</li>
                <li><strong>المستوى الثاني:</strong> مرر المؤشر على أي علامة تجارية لرؤية فئاتها</li>
                <li><strong>المستوى الثالث:</strong> مرر المؤشر على أي فئة لرؤية موديلاتها</li>
                <li><strong>التنقل:</strong> انقر على أي عنصر للانتقال إلى صفحة المنتجات المفلترة</li>
                <li><strong>الموبايل:</strong> جرب القائمة على الشاشات الصغيرة (اضغط على العناصر بدلاً من التمرير)</li>
            </ul>
        </div>

        <div class="highlight-menu">
            <p>👆 ابدأ الاختبار بالنقر على "ملحقات" في شريط التنقل أعلاه</p>
        </div>
    </div>

    <div class="test-section">
        <h2 class="test-section-title">
            <i class="fas fa-database"></i>
            إحصائيات قاعدة البيانات
        </h2>
        
        <div class="stats-grid">
            <?php
            // جلب إحصائيات قاعدة البيانات
            
            // عدد العلامات التجارية النشطة
            $brands_count = 0;
            $brands_result = $conn->query("SELECT COUNT(*) as count FROM brands WHERE status = 1");
            if ($brands_result) {
                $brands_count = $brands_result->fetch_assoc()['count'];
            }
            
            // عدد الفئات النشطة
            $categories_count = 0;
            $categories_result = $conn->query("SELECT COUNT(*) as count FROM series WHERE status = 1");
            if ($categories_result) {
                $categories_count = $categories_result->fetch_assoc()['count'];
            }
            
            // عدد الموديلات النشطة
            $models_count = 0;
            $models_result = $conn->query("SELECT COUNT(*) as count FROM models WHERE status = 1");
            if ($models_result) {
                $models_count = $models_result->fetch_assoc()['count'];
            }
            
            // عدد المنتجات النشطة
            $products_count = 0;
            $products_result = $conn->query("SELECT COUNT(*) as count FROM products WHERE status = 1");
            if ($products_result) {
                $products_count = $products_result->fetch_assoc()['count'];
            }
            ?>
            
            <div class="stat-card">
                <div class="stat-number"><?= $brands_count ?></div>
                <div class="stat-label">علامة تجارية نشطة</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?= $categories_count ?></div>
                <div class="stat-label">فئة نشطة</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?= $models_count ?></div>
                <div class="stat-label">موديل نشط</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?= $products_count ?></div>
                <div class="stat-label">منتج نشط</div>
            </div>
        </div>
    </div>

    <div class="test-section">
        <h2 class="test-section-title">
            <i class="fas fa-check-circle"></i>
            الميزات المنجزة
        </h2>
        
        <div class="test-instructions">
            <h4>✅ الميزات التي تم تطبيقها بنجاح:</h4>
            <ul>
                <li><strong>قائمة متدرجة ثلاثية المستويات:</strong> العلامات التجارية → الفئات → الموديلات</li>
                <li><strong>ربط كامل بقاعدة البيانات:</strong> جميع البيانات مستمدة ديناميكياً من قاعدة البيانات</li>
                <li><strong>تصميم احترافي وسلس:</strong> تأثيرات انتقال أنيقة وتصميم متجاوب</li>
                <li><strong>تحميل تدريجي:</strong> البيانات تُحمل عند الحاجة لتحسين الأداء</li>
                <li><strong>ذاكرة مؤقتة:</strong> منع التحميل المتكرر للبيانات المحملة مسبقاً</li>
                <li><strong>تصميم متجاوب:</strong> يعمل بشكل مثالي على جميع الشاشات</li>
                <li><strong>معالجة الأخطاء:</strong> رسائل خطأ واضحة ومفيدة للمستخدم</li>
                <li><strong>إشعارات تفاعلية:</strong> إشعارات جميلة لتحسين تجربة المستخدم</li>
                <li><strong>دعم اللغة العربية:</strong> أسماء عربية للعناصر مع دعم RTL</li>
                <li><strong>تحسينات الأداء:</strong> Hardware acceleration و optimizations متقدمة</li>
            </ul>
        </div>
    </div>

    <div class="test-section">
        <h2 class="test-section-title">
            <i class="fas fa-mobile-alt"></i>
            اختبار الاستجابة للشاشات
        </h2>
        
        <div class="test-instructions">
            <h4>جرب القائمة على الأحجام المختلفة:</h4>
            <ul>
                <li><strong>الشاشات الكبيرة (Desktop):</strong> تمرير الماوس لإظهار القوائم الفرعية</li>
                <li><strong>الشاشات المتوسطة (Tablet):</strong> قوائم متكيفة مع حجم الشاشة</li>
                <li><strong>الشاشات الصغيرة (Mobile):</strong> قوائم منسدلة عمودية مع إمكانية النقر</li>
            </ul>
        </div>
        
        <p style="text-align: center; margin-top: 2rem;">
            <button onclick="testResponsive()" class="btn btn-primary">
                <i class="fas fa-eye"></i>
                اختبار الاستجابة
            </button>
        </p>
    </div>
</div>

<script>
// اختبار الاستجابة
function testResponsive() {
    const sizes = [
        { width: 1200, label: 'شاشة كبيرة' },
        { width: 768, label: 'تابلت' },
        { width: 480, label: 'موبايل' }
    ];
    
    let currentIndex = 0;
    
    function resizeWindow() {
        if (currentIndex < sizes.length) {
            const size = sizes[currentIndex];
            window.resizeTo(size.width, 800);
            
            if (window.megaMenu) {
                window.megaMenu.showNotification(`🔍 اختبار حجم ${size.label} (${size.width}px)`, 'info');
            }
            
            currentIndex++;
            setTimeout(resizeWindow, 3000);
        } else {
            if (window.megaMenu) {
                window.megaMenu.showNotification('✅ انتهى اختبار الاستجابة', 'success');
            }
        }
    }
    
    resizeWindow();
}

// اختبار تحميل البيانات
async function testDataLoading() {
    console.log('🧪 بدء اختبار تحميل البيانات...');
    
    try {
        // اختبار ملف get_mega_menu_data.php
        const response = await fetch('./ajax/get_mega_menu_data.php');
        const data = await response.json();
        
        if (data.success) {
            console.log('✅ تم تحميل البيانات بنجاح:', data.stats);
            
            if (window.megaMenu) {
                window.megaMenu.showNotification(
                    `✅ تم تحميل ${data.stats.total_brands} علامة تجارية و ${data.stats.total_categories} فئة`, 
                    'success'
                );
            }
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('❌ خطأ في تحميل البيانات:', error);
        
        if (window.megaMenu) {
            window.megaMenu.showNotification('❌ خطأ في تحميل البيانات', 'error');
        }
    }
}

// تشغيل الاختبارات عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 صفحة اختبار القائمة المتدرجة جاهزة');
    
    // اختبار تحميل البيانات بعد ثانيتين
    setTimeout(testDataLoading, 2000);
    
    // إضافة مستمع للنقر على القائمة لتتبع الاختبارات
    document.addEventListener('click', function(e) {
        const megaMenuElement = e.target.closest('.mega-menu-dropdown');
        if (megaMenuElement) {
            console.log('🖱️ تم النقر على القائمة المتدرجة');
            
            if (window.megaMenu) {
                window.megaMenu.trackMenuInteraction('click', {
                    target: e.target.className,
                    test_page: true
                });
            }
        }
    });
});
</script>

<div style="height: 100vh; background: #f1f5f9; display: flex; align-items: center; justify-content: center;">
    <div style="text-align: center; padding: 3rem; background: white; border-radius: 16px; box-shadow: 0 8px 30px rgba(0,0,0,0.1);">
        <i class="fas fa-arrow-up" style="font-size: 3rem; color: #3b82f6; margin-bottom: 1rem;"></i>
        <h2 style="color: #1e293b; margin-bottom: 1rem;">القائمة المتدرجة جاهزة!</h2>
        <p style="color: #64748b; font-size: 1.1rem;">جرب القائمة في شريط التنقل أعلاه</p>
        <button onclick="testDataLoading()" style="background: #3b82f6; color: white; border: none; padding: 0.75rem 2rem; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 1rem;">
            <i class="fas fa-play"></i>
            اختبار تحميل البيانات
        </button>
    </div>
</div>

<?php include 'inc/modern-footer.php'; ?>
