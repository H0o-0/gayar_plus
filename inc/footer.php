<script>
  $(document).ready(function(){
    $('#p_use').click(function(){
      uni_modal("Privacy Policy","policy.php","mid-large")
    })
     window.viewer_modal = function($src = ''){
      start_loader()
      var t = $src.split('.')
      t = t[1]
      if(t =='mp4'){
        var view = $("<video src='"+$src+"' controls autoplay></video>")
      }else{
        var view = $("<img src='"+$src+"' />")
      }
      $('#viewer_modal .modal-content video,#viewer_modal .modal-content img').remove()
      $('#viewer_modal .modal-content').append(view)
      $('#viewer_modal').modal({
              show:true,
              backdrop:'static',
              keyboard:false,
              focus:true
            })
            end_loader()  

  }
    window.uni_modal = function($title = '' , $url='',$size=""){
        start_loader()
        $.ajax({
            url:$url,
            error:err=>{
                console.log(err)
                alert("An error occured")
                end_loader()
            },
            success:function(resp){
                if(resp){
                    $('#uni_modal .modal-title').html($title)
                    $('#uni_modal .modal-body').html(resp)
                    if($size != ''){
                        $('#uni_modal .modal-dialog').addClass($size+'  modal-dialog-centered')
                    }else{
                        $('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md modal-dialog-centered")
                    }
                    $('#uni_modal').modal({
                      show:true,
                      backdrop:'static',
                      keyboard:false,
                      focus:true
                    })
                    end_loader()
                }
            }
        })
    }
    window._conf = function($msg='',$func='',$params = []){
       $('#confirm_modal #confirm').attr('onclick',$func+"("+$params.join(',')+")")
       $('#confirm_modal .modal-body').html($msg)
       $('#confirm_modal').modal('show')
    }
  })
</script>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Additional Scripts for animations -->
<script>
// تأثيرات التمرير للنافبار
let lastScrollTop = 0;
const navbar = document.getElementById('navbar');

if(navbar) {
    window.addEventListener('scroll', () => {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > lastScrollTop && scrollTop > 100) {
            navbar.style.transform = 'translateY(-100%)';
        } else {
            navbar.style.transform = 'translateY(0)';
        }
        
        if (scrollTop > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        lastScrollTop = scrollTop;
    });
}

// تأثيرات الظهور للعناصر
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
}, observerOptions);

// مراقبة العناصر المتحركة
document.addEventListener('DOMContentLoaded', () => {
    const elementsToAnimate = document.querySelectorAll('.glass-card, .section, .brand-card');
    elementsToAnimate.forEach(el => observer.observe(el));
});

// وظيفة عرض المنتج
function viewProduct(id) {
    window.location.href = './?p=product_view_redirect&id=' + id;
}

// وظيفة إضافة للسلة - تم نقل هذه الوظيفة إلى cart_fix.js لتجنب التعارض
// Cart functionality moved to cart_fix.js to avoid conflicts

// تحسين وظائف القوائم المنسدلة
document.addEventListener('DOMContentLoaded', function() {
    const dropdowns = document.querySelectorAll('.dropdown');
    let activeDropdown = null;
    let dropdownTimeout = null;
    
    // إخفاء جميع القوائم عند بدء الصفحة
    dropdowns.forEach(dropdown => {
        const dropdownContent = dropdown.querySelector('.dropdown-content');
        if(dropdownContent) {
            dropdownContent.style.display = 'none';
            dropdown.classList.remove('show');
        }
    });
    
    dropdowns.forEach(dropdown => {
        const dropdownContent = dropdown.querySelector('.dropdown-content');
        const dropdownToggle = dropdown.querySelector('.dropdown-toggle');
        
        if(!dropdownContent || !dropdownToggle) return;
        
        // منع الرابط من العمل
        dropdownToggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            // إذا كانت القائمة مفتوحة، أغلقها
            if (dropdown.classList.contains('show')) {
                dropdownContent.style.display = 'none';
                dropdown.classList.remove('show');
                activeDropdown = null;
            } else {
                // إخفاء القوائم الأخرى
                dropdowns.forEach(otherDropdown => {
                    if (otherDropdown !== dropdown) {
                        const otherContent = otherDropdown.querySelector('.dropdown-content');
                        if(otherContent) {
                            otherContent.style.display = 'none';
                            otherDropdown.classList.remove('show');
                        }
                    }
                });
                
                // إظهار القائمة الحالية
                dropdownContent.style.display = 'block';
                dropdown.classList.add('show');
                activeDropdown = dropdown;
            }
        });
        
        dropdown.addEventListener('mouseenter', () => {
            // إلغاء أي timeout موجود
            if (dropdownTimeout) {
                clearTimeout(dropdownTimeout);
                dropdownTimeout = null;
            }
            
            // إخفاء القوائم الأخرى
            dropdowns.forEach(otherDropdown => {
                if (otherDropdown !== dropdown) {
                    const otherContent = otherDropdown.querySelector('.dropdown-content');
                    if(otherContent) {
                        otherContent.style.display = 'none';
                        otherDropdown.classList.remove('show');
                    }
                }
            });
            
            // إظهار القائمة الحالية
            dropdownContent.style.display = 'block';
            dropdown.classList.add('show');
            activeDropdown = dropdown;
        });
        
        dropdown.addEventListener('mouseleave', () => {
            // إضافة تأخير قبل الإخفاء
            dropdownTimeout = setTimeout(() => {
                if (activeDropdown === dropdown) {
                    dropdownContent.style.display = 'none';
                    dropdown.classList.remove('show');
                    activeDropdown = null;
                }
            }, 300); // 300ms delay for better UX
        });
    });
    
    // إغلاق القوائم عند النقر خارجها
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) {
            dropdowns.forEach(dropdown => {
                const dropdownContent = dropdown.querySelector('.dropdown-content');
                if(dropdownContent) {
                    dropdownContent.style.display = 'none';
                    dropdown.classList.remove('show');
                }
            });
            activeDropdown = null;
        }
    });
});
</script>

<!-- الفوتر مثبت في الأسفل -->
<footer class="techstore-footer">
    <div class="footer-container">
        <div class="footer-grid">
            <!-- قسم الشركة -->
            <div class="footer-column">
                <div class="company-info">
                    <h3>
                        <img src="admin/images/cropped_circle_image.png" alt="Logo" style="width: 35px; height: 35px; border-radius: 50%; margin-left: 10px;">
                        Gayar Plus
                    </h3>
                    <p>متجرك الموثوق لملحقات الهواتف الذكية في العراق. نوفر أحدث المنتجات الأصلية بأفضل الأسعار.</p>
                    <div class="social-media">
                        <a href="#" class="social-link facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="social-link instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link youtube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            
            <!-- روابط سريعة -->
            <div class="footer-column">
                <h4>روابط سريعة</h4>
                <ul class="quick-links">
                    <li><a href="#brands">تسوق حسب الشركة</a></li>
                    <li><a href="#products">أحدث المنتجات</a></li>
                    <li><a href="#">خدمة الصيانة</a></li>
                    <li><a href="#">عروض خاصة</a></li>
                    <li><a href="./?p=contact">اتصل بنا</a></li>
                </ul>
            </div>
            
            <!-- خدمة العملاء -->
            <div class="footer-column">
                <h4>خدمة العملاء</h4>
                <ul class="customer-service">
                    <li><a href="#">سياسة الاستبدال</a></li>
                    <li><a href="#">طرق الدفع</a></li>
                    <li><a href="#">التوصيل والشحن</a></li>
                    <li><a href="#">الأسئلة الشائعة</a></li>
                    <li><a href="#">تتبع الطلب</a></li>
                </ul>
            </div>
            
            <!-- معلومات التواصل -->
            <div class="footer-column">
                <h4>تواصل معنا</h4>
                <div class="contact-details">
                    <div class="contact-row">
                        <i class="fas fa-phone"></i>
                        <span>+964 770 123 4567</span>
                    </div>
                    <div class="contact-row">
                        <i class="fas fa-envelope"></i>
                        <span>info@techstore-iraq.com</span>
                    </div>
                    <div class="contact-row">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>شارع الرشيد، بغداد، العراق</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- حقوق الطبع -->
        <div class="footer-copyright">
            <p>&copy; 2024 Gayar Plus Iraq. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</footer>

<style>
/* إجبار الصفحة على استخدام Flexbox Layout */
html {
    height: 100% !important;
}

body {
    min-height: 100vh !important;
    display: flex !important;
    flex-direction: column !important;
    margin: 0 !important;
    padding: 0 !important;
    padding-top: 90px !important; /* مسافة للنافبار */
}

/* المحتوى الرئيسي يأخذ المساحة المتاحة */
.wrapper,
main,
.content-wrapper {
    flex: 1 !important;
}

/* الفوتر مثبت في الأسفل */
.techstore-footer {
    margin-top: auto !important;
    width: 100% !important;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%) !important;
    color: #ffffff !important;
    border-top: 4px solid #00d4ff !important;
    box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.3) !important;
    position: relative !important;
    z-index: 10 !important;
}

.footer-container {
    max-width: 1200px !important;
    margin: 0 auto !important;
    padding: 50px 20px 25px !important;
}

.footer-grid {
    display: grid !important;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)) !important;
    gap: 40px !important;
    margin-bottom: 40px !important;
}

.footer-column h3 {
    color: #00d4ff !important;
    font-size: 1.5rem !important;
    margin-bottom: 20px !important;
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    font-weight: 700 !important;
}

.footer-column h4 {
    color: #00d4ff !important;
    font-size: 1.2rem !important;
    margin-bottom: 20px !important;
    font-weight: 600 !important;
    position: relative !important;
}

.footer-column h4::after {
    content: '' !important;
    position: absolute !important;
    bottom: -8px !important;
    left: 0 !important;
    width: 30px !important;
    height: 2px !important;
    background: #00d4ff !important;
}

.company-info p {
    color: #cccccc !important;
    line-height: 1.8 !important;
    margin-bottom: 25px !important;
    font-size: 0.95rem !important;
}

.social-media {
    display: flex !important;
    gap: 15px !important;
}

.social-link {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 40px !important;
    height: 40px !important;
    background: rgba(0, 212, 255, 0.1) !important;
    border-radius: 50% !important;
    color: #00d4ff !important;
    text-decoration: none !important;
    transition: all 0.3s ease !important;
    border: 2px solid transparent !important;
}

.social-link:hover {
    background: #00d4ff !important;
    color: #1a1a2e !important;
    border-color: #00d4ff !important;
    transform: translateY(-3px) !important;
}

.quick-links,
.customer-service {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

.quick-links li,
.customer-service li {
    margin-bottom: 12px !important;
}

.quick-links a,
.customer-service a {
    color: #cccccc !important;
    text-decoration: none !important;
    transition: all 0.3s ease !important;
    font-size: 0.95rem !important;
    display: block !important;
    padding: 5px 0 !important;
}

.quick-links a:hover,
.customer-service a:hover {
    color: #00d4ff !important;
    padding-right: 10px !important;
}

.contact-details {
    display: flex !important;
    flex-direction: column !important;
    gap: 15px !important;
}

.contact-row {
    display: flex !important;
    align-items: center !important;
    gap: 15px !important;
    color: #cccccc !important;
    font-size: 0.95rem !important;
}

.contact-row i {
    color: #00d4ff !important;
    width: 20px !important;
    text-align: center !important;
    font-size: 1.1rem !important;
}

.footer-copyright {
    text-align: center !important;
    padding-top: 25px !important;
    border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
    color: #999999 !important;
    font-size: 0.9rem !important;
}

/* للشاشات الصغيرة */
@media (max-width: 768px) {
    .footer-grid {
        grid-template-columns: 1fr !important;
        gap: 30px !important;
    }
    
    .footer-container {
        padding: 40px 15px 20px !important;
    }
    
    .social-media {
        justify-content: center !important;
    }
}

/* منع أي تداخل */
.techstore-footer * {
    box-sizing: border-box !important;
}

/* ضمان عدم الطفو */
.techstore-footer {
    clear: both !important;
    display: block !important;
}
</style>

<!-- المكتبات -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
