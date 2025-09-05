// JavaScript لإدارة قائمة الملحقات الجديدة
document.addEventListener('DOMContentLoaded', function() {
    console.log('Accessories menu script loaded');
    
    // متغيرات للعناصر
    let brandItems = document.querySelectorAll('.brand-item');
    const seriesList = document.getElementById('series-list');
    const modelsList = document.getElementById('models-list');
    
    let selectedBrandId = null;
    let selectedSeriesId = null;

    // التأكد من وجود العناصر
    if (!seriesList || !modelsList) {
        console.error('Series list or models list not found');
        return;
    }

    // إضافة أحداث النقر على البراندات
    function initBrandEvents() {
        brandItems = document.querySelectorAll('.brand-item');
        console.log('Found brand items:', brandItems.length);
        
        brandItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const brandId = this.getAttribute('data-brand-id');
                console.log('Brand clicked:', brandId);
                selectedBrandId = brandId;
                
                // إزالة التحديد من جميع البراندات
                brandItems.forEach(b => b.classList.remove('active'));
                
                // تحديد البراند المختار
                this.classList.add('active');
                
                // تحميل الفئات للبراند المختار
                loadSeries(brandId);
                
                // إعادة تعيين الموديلات
                resetModels();
            });
        });
    }

    // تشغيل الأحداث عند التحميل
    initBrandEvents();

    // تحميل الفئات
    function loadSeries(brandId) {
        seriesList.innerHTML = '<div class="dropdown-item" style="color: #9ca3af;">جاري التحميل...</div>';
        
        // إرسال طلب AJAX لجلب الفئات
        fetch(`./ajax/get_brand_series.php?brand_id=${brandId}`)
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Series data received:', data);
                if (data.success && data.series.length > 0) {
                    let html = '';
                    data.series.forEach(series => {
                        html += `<div class="dropdown-item series-item" data-series-id="${series.id}">
                                    <i class="fas fa-tag"></i>
                                    ${series.name}
                                </div>`;
                    });
                    seriesList.innerHTML = html;
                    
                    // إضافة أحداث النقر على الفئات
                    addSeriesClickEvents();
                } else {
                    seriesList.innerHTML = '<div class="dropdown-item" style="color: #9ca3af;">لا توجد فئات متاحة</div>';
                }
            })
            .catch(error => {
                console.error('خطأ في تحميل الفئات:', error);
                seriesList.innerHTML = '<div class="dropdown-item" style="color: #ef4444;">خطأ في التحميل</div>';
            });
    }

    // إضافة أحداث النقر على الفئات
    function addSeriesClickEvents() {
        const seriesItems = document.querySelectorAll('.series-item');
        
        seriesItems.forEach(item => {
            item.addEventListener('click', function() {
                const seriesId = this.getAttribute('data-series-id');
                selectedSeriesId = seriesId;
                
                // إزالة التحديد من جميع الفئات
                seriesItems.forEach(s => s.classList.remove('active'));
                
                // تحديد الفئة المختارة
                this.classList.add('active');
                
                // تحميل الموديلات للفئة المختارة
                loadModels(seriesId);
            });
        });
    }

    // تحميل الموديلات
    function loadModels(seriesId) {
        modelsList.innerHTML = '<div class="dropdown-item" style="color: #9ca3af;">جاري التحميل...</div>';
        
        // إرسال طلب AJAX لجلب الموديلات
        fetch(`./ajax/get_series_models.php?series_id=${seriesId}`)
            .then(response => {
                console.log('Models response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Models data received:', data);
                if (data.success && data.models.length > 0) {
                    let html = '';
                    data.models.forEach(model => {
                        html += `<a href="./?p=device_products&m=${model.id}" class="dropdown-item model-item">
                                    <i class="fas fa-mobile-alt"></i>
                                    ${model.name}
                                </a>`;
                    });
                    modelsList.innerHTML = html;
                } else {
                    modelsList.innerHTML = '<div class="dropdown-item" style="color: #9ca3af;">لا توجد موديلات متاحة</div>';
                }
            })
            .catch(error => {
                console.error('خطأ في تحميل الموديلات:', error);
                modelsList.innerHTML = '<div class="dropdown-item" style="color: #ef4444;">خطأ في التحميل</div>';
            });
    }

    // إعادة تعيين الموديلات
    function resetModels() {
        selectedSeriesId = null;
        modelsList.innerHTML = '<div class="dropdown-item" style="color: #9ca3af; font-style: italic;">اختر فئة</div>';
    }

    // إعادة تعيين الفئات
    function resetSeries() {
        selectedBrandId = null;
        selectedSeriesId = null;
        seriesList.innerHTML = '<div class="dropdown-item" style="color: #9ca3af; font-style: italic;">اختر علامة تجارية</div>';
        resetModels();
    }
});
