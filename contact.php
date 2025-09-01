<<<<<<< HEAD
<?php
$pageTitle = "اتصل بنا - Gayar Plus";
require_once 'initialize.php';
require_once 'classes/TextCleaner.php';
include 'inc/header.php';
?>

=======
>>>>>>> cebc63a3bc4f7e2f5ae4119daff21338fea35eb8
<style>
/* Contact Page Styles */
.contact-hero {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-navy) 100%);
    color: white;
    padding: 6rem 0;
    text-align: center;
}

.contact-hero h1 {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 1rem;
}

.contact-hero p {
    font-size: 1.25rem;
    max-width: 700px;
    margin: 0 auto 2rem;
    opacity: 0.9;
}

.contact-container {
    max-width: 1200px;
    margin: 4rem auto;
    padding: 0 2rem;
}

.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
}

@media (max-width: 768px) {
    .contact-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .contact-container {
        margin: 2rem auto;
        padding: 0 1rem;
    }
    
    .contact-hero {
        padding: 4rem 0;
    }
    
    .contact-hero h1 {
        font-size: 2rem;
    }
}

.contact-info {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--medium-gray);
}

.contact-info h2 {
    color: var(--primary-navy);
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 2rem;
    text-align: center;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 2rem;
}

.info-icon {
    background: var(--primary-blue);
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.25rem;
}

.info-content h3 {
    color: var(--text-primary);
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.info-content p {
    color: var(--text-secondary);
    line-height: 1.6;
    margin: 0;
}

.social-links {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: var(--light-gray);
    color: var(--primary-blue);
    font-size: 1.25rem;
    transition: var(--transition);
}

.social-link:hover {
    background: var(--primary-blue);
    color: white;
    transform: translateY(-3px);
}

.contact-form {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--medium-gray);
}

.contact-form h2 {
    color: var(--primary-navy);
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 2rem;
    text-align: center;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    color: var(--text-primary);
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--medium-gray);
    border-radius: 8px;
    font-size: 1rem;
    transition: var(--transition);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

textarea.form-control {
    min-height: 150px;
    resize: vertical;
}

.btn-submit {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-navy) 100%);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    transition: var(--transition);
    width: 100%;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.map-container {
    height: 300px;
    border-radius: 16px;
    overflow: hidden;
    margin-top: 2rem;
    box-shadow: var(--shadow-lg);
}

.map-container iframe {
    width: 100%;
    height: 100%;
    border: none;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Device Selection Styles - REMOVED */
/*
.device-selection {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--medium-gray);
    margin-bottom: 2rem;
}

.device-selection h2 {
    color: var(--primary-navy);
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    text-align: center;
}

.device-form-group {
    margin-bottom: 1.5rem;
}

.device-form-group label {
    display: block;
    color: var(--text-primary);
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.device-form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--medium-gray);
    border-radius: 8px;
    font-size: 1rem;
    transition: var(--transition);
}

.device-form-control:focus {
    outline: none;
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.products-container {
    margin-top: 2rem;
}

.product-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--light-gray);
    margin-bottom: 1rem;
    transition: var(--transition);
}

.product-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

.product-card h3 {
    color: var(--primary-navy);
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.product-card p {
    color: var(--text-secondary);
    line-height: 1.6;
    margin: 0 0 1rem 0;
}

.product-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.9rem;
    color: var(--text-secondary);
}

.btn-view-product {
    background: var(--primary-blue);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
    display: inline-block;
}

.btn-view-product:hover {
    background: var(--primary-navy);
    transform: translateY(-2px);
}
*/
</style>

<!-- Contact Hero Section -->
<section class="contact-hero">
    <div class="container">
        <h1>اتصل بنا</h1>
        <p>هل لديك أي استفسارات أو تحتاج إلى المساعدة؟ فريق دعم العملاء لدينا مستعد لمساعدتك في أي وقت</p>
    </div>
</section>

<!-- Contact Content - REMOVED device selection section -->
<div class="contact-container">
    <div class="contact-grid">
        <!-- Contact Information -->
        <div class="contact-info">
            <h2>معلومات التواصل</h2>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="info-content">
                    <h3>العنوان</h3>
                    <p>بغداد - الكرادة - شارع أبو نواس</p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="info-content">
                    <h3>رقم الهاتف</h3>
                    <p>+964 770 123 4567</p>
                    <p>+964 780 987 6543</p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="info-content">
                    <h3>البريد الإلكتروني</h3>
                    <p>info@gayarplus.iq</p>
                    <p>support@gayarplus.iq</p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="info-content">
                    <h3>ساعات العمل</h3>
                    <p>السبت - الخميس: 9:00 ص - 10:00 م</p>
                    <p>الجمعة: 10:00 ص - 6:00 م</p>
                </div>
            </div>
            
            <div class="social-links">
                <a href="#" class="social-link">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="social-link">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="social-link">
                    <i class="fab fa-telegram"></i>
                </a>
                <a href="#" class="social-link">
                    <i class="fab fa-whatsapp"></i>
                </a>
            </div>
        </div>
        
        <!-- Contact Form -->
        <div class="contact-form">
            <h2>أرسل لنا رسالة</h2>
            
            <?php if(isset($_SESSION['message_sent'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['message_sent'] ?>
                </div>
                <?php unset($_SESSION['message_sent']); ?>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['message_error'])): ?>
                <div class="alert alert-error">
                    <?= $_SESSION['message_error'] ?>
                </div>
                <?php unset($_SESSION['message_error']); ?>
            <?php endif; ?>
            
            <form method="POST" action="ajax/send_message.php">
                <div class="form-group">
                    <label for="name">الاسم الكامل</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">رقم الهاتف</label>
                    <input type="tel" id="phone" name="phone" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="subject">الموضوع</label>
                    <input type="text" id="subject" name="subject" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="message">الرسالة</label>
                    <textarea id="message" name="message" class="form-control" required></textarea>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> إرسال الرسالة
                </button>
            </form>
        </div>
    </div>
    
    <!-- Map -->
    <div class="map-container">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2481.848950408033!2d44.408333!3d33.315278!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzPCsDE4JzU1LjAiTiA0NMKwMjQnMzAuMCJF!5e0!3m2!1sen!2siq!4v1650000000000!5m2!1sen!2siq" allowfullscreen="" loading="lazy"></iframe>
    </div>
</div>

<script>
$(document).ready(function() {
    // REMOVED device selection JavaScript code
    /*
    // When brand is selected, load series
    $('#brand-select').change(function() {
        var brandId = $(this).val();
        
        // Reset series and model selections
        $('#series-select').html('<option value="">اختر السلسلة</option>');
        $('#model-select').html('<option value="">اختر الموديل</option>');
        $('#series-group').hide();
        $('#model-group').hide();
        $('#products-container').hide();
        
        if (brandId) {
            // Show loading state for series
            $('#series-group').show().find('select').html('<option value="">جاري تحميل السلسلة...</option>');
            
            // AJAX request to get series by brand
            $.ajax({
                url: 'classes/Master.php?f=get_series_by_brand',
                method: 'POST',
                data: { brand_id: brandId },
                success: function(response) {
                    console.log("Series response:", response);
                    $('#series-select').html('<option value="">اختر السلسلة</option>' + response);
                    $('#series-group').show();
                    
                    // If no series are available, load products directly
                    if ($('#series-select option').length <= 1) {
                        console.log("No series found, loading products directly for brand:", brandId);
                        // Show loading state
                        $('#products-container').show().html('<p>جاري تحميل المنتجات...</p>');
                        
                        // AJAX request to get products by brand
                        $.ajax({
                            url: 'ajax/get_brand_products.php',
                            method: 'POST',
                            data: { brand_id: brandId },
                            success: function(response) {
                                console.log("Brand products response:", response);
                                $('#products-container').html(response);
                            },
                            error: function(xhr, status, error) {
                                console.log("Error loading brand products:", error);
                                $('#products-container').html('<p>حدث خطأ أثناء تحميل المنتجات. يرجى المحاولة مرة أخرى.</p>');
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Error loading series:", error);
                    $('#series-select').html('<option value="">خطأ في تحميل السلسلة</option>');
                }
            });
        }
    });
    
    // When series is selected, load models
    $('#series-select').change(function() {
        var seriesId = $(this).val();
        
        // Reset model selection
        $('#model-select').html('<option value="">اختر الموديل</option>');
        $('#model-group').hide();
        $('#products-container').hide();
        
        if (seriesId) {
            // Show loading state for models
            $('#model-group').show().find('select').html('<option value="">جاري تحميل الموديلات...</option>');
            
            // AJAX request to get models by series
            $.ajax({
                url: 'classes/Master.php?f=get_models_by_series',
                method: 'POST',
                data: { series_id: seriesId },
                success: function(response) {
                    console.log("Models response:", response);
                    $('#model-select').html('<option value="">اختر الموديل</option>' + response);
                    $('#model-group').show();
                },
                error: function(xhr, status, error) {
                    console.log("Error loading models:", error);
                    $('#model-select').html('<option value="">خطأ في تحميل الموديلات</option>');
                }
            });
        }
    });
    
    // When model is selected, load products
    $('#model-select').change(function() {
        var modelId = $(this).val();
        
        if (modelId) {
            // Show loading state
            $('#products-container').show().html('<p>جاري تحميل المنتجات...</p>');
            
            // AJAX request to get products by model
            $.ajax({
                url: 'ajax/get_model_products.php',
                method: 'POST',
                data: { model_id: modelId },
                success: function(response) {
                    console.log("Model products response:", response);
                    $('#products-container').html(response);
                },
                error: function(xhr, status, error) {
                    console.log("Error loading model products:", error);
                    $('#products-container').html('<p>حدث خطأ أثناء تحميل المنتجات. يرجى المحاولة مرة أخرى.</p>');
                }
            });
        } else {
            // If no model selected, but we have a brand, load products by brand
            var brandId = $('#brand-select').val();
            if (brandId) {
                $('#products-container').show().html('<p>جاري تحميل المنتجات...</p>');
                
                $.ajax({
                    url: 'ajax/get_brand_products.php',
                    method: 'POST',
                    data: { brand_id: brandId },
                    success: function(response) {
                        console.log("Brand products response (from model selection):", response);
                        $('#products-container').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.log("Error loading brand products (from model selection):", error);
                        $('#products-container').html('<p>حدث خطأ أثناء تحميل المنتجات. يرجى المحاولة مرة أخرى.</p>');
                    }
                });
            } else {
                $('#products-container').hide();
            }
        }
    });
    */
});
</script>

<?php include 'inc/modern-footer.php'; ?>