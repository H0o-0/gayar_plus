<?php
$pageTitle = 'الصفحة الرئيسية';
require_once 'config.php';
require_once 'classes/TextCleaner.php';
include 'inc/header.php';
?>
<style>
/* Global Container and Layout */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 clamp(1rem, 4vw, 2rem);
    position: relative;
}

/* Enhanced responsive section styling with unified background */
.section {
    padding: 2rem 0;
    position: relative;
    scroll-margin-top: 80px;
    background: #ffffff;
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .section {
        padding: 1.5rem 0;
    }
    
    .section-title {
        font-size: 1.8rem !important;
        margin-bottom: 1rem !important;
    }
    
    .section-subtitle {
        font-size: 1rem !important;
        margin-bottom: 1.5rem !important;
    }
    
    .container {
        padding: 0 1rem;
    }
}

.section-header {
    text-align: center;
    margin-bottom: clamp(2rem, 5vw, 3rem);
    animation: fadeInUp 0.8s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.section-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1a1a2e;
    margin-bottom: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #f3f4f6;
}

.product-section-title {
    font-size: clamp(1.75rem, 5vw, 2.5rem);
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 0.75rem;
    color: #1e293b;
    line-height: 1.2;
    position: relative;
}



.section-subtitle {
    font-size: 1.1rem;
    color: #6b7280;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

/* Enhanced Brands Section */
.brands-section {
    padding: clamp(3rem, 6vw, 5rem) 0;
    background: #ffffff;
    position: relative;
    overflow: hidden;
}

.brands-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: -50%;
    width: 200%;
    height: 100%;
    background: radial-gradient(ellipse at center, rgba(59, 130, 246, 0.02) 0%, transparent 70%);
    animation: float 20s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateX(0) rotate(0deg); }
    50% { transform: translateX(20px) rotate(1deg); }
}

.brands-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 clamp(1rem, 4vw, 2rem);
    position: relative;
    z-index: 2;
}

.brands-title {
    text-align: center;
    font-size: clamp(1.75rem, 5vw, 2.5rem);
    font-weight: 800;
    color: #1e293b;
    margin-bottom: clamp(2rem, 5vw, 3rem);
    color: #1e293b;
    position: relative;
}

.brands-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: clamp(1rem, 3vw, 2rem);
    margin-bottom: 2rem;
}

.brand-block {
    background: #ffffff;
    padding: clamp(1.5rem, 3vw, 2rem) clamp(1rem, 2vw, 1.5rem);
    border-radius: 16px;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    backdrop-filter: blur(10px);
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.brand-block::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
    transition: left 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.brand-block:hover::before {
    left: 100%;
}

.brand-block:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(59, 130, 246, 0.25);
    border-color: #3b82f6;
}

.brand-block:active {
    transform: translateY(-4px) scale(0.98);
    transition: all 0.1s ease;
}


.brand-logo-wrapper {
    width: clamp(60px, 8vw, 80px);
    height: clamp(60px, 8vw, 80px);
    margin: 0 auto clamp(1rem, 2vw, 1.5rem);
    border-radius: 50%;
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 3px solid #e2e8f0;
    position: relative;
}

.brand-logo-wrapper::after {
    content: '';
    position: absolute;
    inset: -2px;
    border-radius: 50%;
    background: linear-gradient(45deg, #3b82f6, #1e40af, #3b82f6);
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: -1;
}

.brand-block:hover .brand-logo-wrapper {
    transform: scale(1.1) rotate(5deg);
    border-color: transparent;
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
}

.brand-block:hover .brand-logo-wrapper::after {
    opacity: 1;
}

.brand-block:hover .brand-logo-wrapper .brand-icon {
    color: white;
}

.brand-block:hover .brand-logo-wrapper .brand-logo {
    transform: scale(1.1);
}

.brand-icon {
    font-size: 1.5rem;
    color: #64748b;
    transition: all 0.3s ease;
}

.brand-logo {
    max-width: 50px;
    max-height: 50px;
    width: auto;
    height: auto;
    object-fit: contain;
    transition: all 0.3s ease;
    filter: brightness(1) contrast(1.1);
}

.brand-name {
    font-size: clamp(0.9rem, 2vw, 1.1rem);
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.5rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.brand-block:hover .brand-name {
    color: #3b82f6;
    transform: translateY(-2px);
}

.no-brands-message {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.no-brands-message i {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}

.no-brands-message h3 {
    font-size: 1.5rem;
    color: #374151;
    margin-bottom: 0.5rem;
}

.no-brands-message p {
    color: #718096;
    font-size: 1rem;
}

/* Enhanced Products Section */
.products-section {
    background: white;
    position: relative;
}

.products-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    max-width: 1200px;
    margin: 0 auto;
}

.modern-product-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    position: relative;
    height: 350px;
    width: 100%;
    max-width: 280px;
    margin: 0 auto;
}

.modern-product-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #3b82f6, #1e40af);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.modern-product-card:hover::before {
    transform: scaleX(1);
}

.modern-product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    border-color: #3b82f6;
}

.card-image-container {
    position: relative;
    height: 180px;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.3s ease;
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.9), rgba(30, 64, 175, 0.9));
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.4s ease;
}

.modern-product-card:hover .product-overlay {
    opacity: 1;
}

.quick-view-btn {
    background: white;
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #3b82f6;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    font-size: 1.1rem;
}

.quick-view-btn:hover {
    background: #3b82f6;
    color: white;
    transform: scale(1.1) rotate(5deg);
}

.stock-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.stock-badge.in-stock {
    background: #10b981;
    color: white;
}

.stock-badge.out-of-stock {
    background: #ef4444;
    color: white;
}

.card-content {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    flex: 1;
    background: white;
}

.product-category {
    font-size: 0.8rem;
    color: #3b82f6;
    font-weight: 600;
    margin-bottom: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    background: #eff6ff;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    display: inline-block;
    border: 1px solid #dbeafe;
}

.product-name {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 1rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.3s ease;
}

.modern-product-card:hover .product-name {
    color: #3b82f6;
}

.product-desc {
    color: #6b7280;
    margin-bottom: 1rem;
    line-height: 1.5;
    font-size: 0.875rem;
    flex: 1;
}

.product-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #f3f4f6;
}

.product-price {
    font-size: 1.4rem;
    font-weight: 800;
    color: #059669;
    background: linear-gradient(135deg, #059669, #047857);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stock-info {
    font-size: 0.75rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.card-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: auto;
    padding-top: 1rem;
}

.add-to-cart-btn {
    flex: 1;
    background: linear-gradient(135deg, #3b82f6, #1e40af);
    color: white;
    border: none;
    padding: 0.875rem 1.25rem;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.add-to-cart-btn:hover:not(.disabled) {
    background: linear-gradient(135deg, #2563eb, #1e3a8a);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

.add-to-cart-btn.disabled {
    background: linear-gradient(135deg, #9ca3af, #6b7280);
    cursor: not-allowed;
    opacity: 0.6;
    box-shadow: none;
}

.view-details-btn {
    background: #f8fafc;
    color: #475569;
    border: 2px solid #e2e8f0;
    padding: 0.875rem 1rem;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    font-weight: 600;
}

.view-details-btn:hover {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
    transform: translateY(-1px);
}

.no-products-message {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    color: #6b7280;
}

.no-products-message i {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}

.no-products-message h3 {
    font-size: 1.5rem;
    color: #374151;
    margin-bottom: 0.5rem;
}

.no-products-message p {
    color: #6b7280;
    font-size: 1rem;
}

/* Color dots for products */
.card-colors {
    margin: 1rem 0;
    padding: 0.5rem 0;
}

.color-dots {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.color-dot {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2px solid #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
    cursor: pointer;
    flex-shrink: 0;
    position: relative;
}

.color-dot:hover {
    transform: scale(1.3);
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2), 0 0 0 2px rgba(59, 130, 246, 0.3);
    z-index: 2;
}

.more-colors {
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 600;
    margin-left: 0.5rem;
    background: #f3f4f6;
    padding: 0.125rem 0.375rem;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

/* Features Section - more compact */
.features-section {
    background: #ffffff;
    padding: 3rem 0;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.feature-card {
    background: #ffffff;
    padding: 1.75rem 1.5rem;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.feature-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.12);
    border-color: #3b82f6;
}

.feature-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #3b82f6, #1e40af);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: white;
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.feature-card:hover .feature-icon {
    transform: scale(1.05);
}

.feature-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.75rem;
    line-height: 1.3;
}

.feature-description {
    color: #64748b;
    line-height: 1.5;
    font-size: 0.9rem;
    font-weight: 400;
}

/* Mobile Show More Button */
.show-more-container {
    text-align: center;
    margin-top: 2rem;
    display: none;
}

.show-more-btn {
    background: linear-gradient(135deg, #3b82f6, #1e40af);
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    font-size: 0.9rem;
}

.show-more-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

.show-more-btn i {
    margin-right: 0.5rem;
}

/* Hidden brands for mobile */
.brand-block.hidden-mobile {
    display: none;
}

/* Mobile Responsive Design */
@media (max-width: 768px) {
    .section {
        padding: 2.5rem 0;
    }
    
    .section-title {
        font-size: 1.8rem;
        margin-bottom: 0.75rem;
    }
    
    .section-subtitle {
        font-size: 1rem;
        padding: 0 1rem;
    }
    
    .brands-section {
        padding: 2rem 0;
        background: #f8fafc;
    }
    
    .brands-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .brand-block {
        padding: 1.5rem 0.75rem;
        border-radius: 12px;
    }
    
    .brand-logo-wrapper {
        width: 55px;
        height: 55px;
        margin-bottom: 1rem;
    }
    
    .brand-icon {
        font-size: 1.25rem;
    }
    
    .brand-logo {
        max-width: 35px;
        max-height: 35px;
    }
    
    .brand-name {
        font-size: 0.95rem;
    }
    
    .show-more-container {
        display: block;
    }
    
    /* Show only first 4 brands on mobile initially */
    .brand-block:nth-child(n+5) {
        display: none;
    }
    
    .brand-block:nth-child(n+5).show-all {
        display: block;
    }
    
    /* Products grid mobile optimization - smaller cards */
    .products-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    
    .modern-product-card {
        margin: 0;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .card-image-container {
        height: 140px;
    }
    
    .card-content {
        padding: 0.75rem;
    }
    
    .product-category {
        font-size: 0.65rem;
        padding: 0.15rem 0.4rem;
        margin-bottom: 0.25rem;
    }
    
    .product-name {
        font-size: 0.85rem;
        line-height: 1.2;
        margin-bottom: 0.25rem;
    }
    
    .product-price {
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
    }
    
    .card-actions {
        flex-direction: column;
        gap: 0.4rem;
    }
    
    .add-to-cart-btn, .view-details-btn {
        width: 100%;
        padding: 0.5rem;
        font-size: 0.75rem;
    }
    
    /* Features section mobile - smaller and more compact */
    .features-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    
    .feature-card {
        padding: 1rem;
        border-radius: 8px;
    }
    
    .feature-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
        margin-bottom: 0.75rem;
    }
    
    .feature-title {
        font-size: 0.9rem;
        line-height: 1.2;
        margin-bottom: 0.5rem;
    }
    
    .feature-description {
        font-size: 0.75rem;
        line-height: 1.3;
    }
}

/* Tablet styles */
@media (min-width: 769px) and (max-width: 1024px) {
    .section {
        padding: 3rem 0;
    }
    
    .brands-section {
        padding: 2.5rem 0;
    }
    
    .brands-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
    }
    
    .brand-block {
        padding: 1.5rem 1rem;
    }
    
    .brand-logo-wrapper {
        width: 55px;
        height: 55px;
    }
    
    .brand-name {
        font-size: 1rem;
    }
    
    .products-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }
    
    .modern-product-card {
        border-radius: 10px;
    }
    
    .card-image-container {
        height: 160px;
    }
    
    .card-content {
        padding: 1rem;
    }
    
    .product-name {
        font-size: 0.9rem;
    }
    
    .product-price {
        font-size: 1rem;
    }
    
    .features-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }
    
    .feature-card {
        padding: 1.25rem;
    }
    
    .feature-icon {
        width: 45px;
        height: 45px;
        font-size: 1.1rem;
    }
    
    .feature-title {
        font-size: 1rem;
    }
    
    .feature-description {
        font-size: 0.8rem;
    }
    
    .nav-container {
        padding: 0 1.5rem;
    }
    
    .mega-menu {
        min-width: 700px;
        padding: 1.75rem;
    }
}

/* Large screens */
@media (min-width: 1200px) {
    .section {
        padding: 5rem 0;
    }
    
    .brands-grid {
        grid-template-columns: repeat(6, 1fr);
        gap: 2rem;
    }
    
    .products-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 2.5rem;
    }
    
    .features-grid {
        grid-template-columns: repeat(4, 1fr);
    }
    
    .nav-container {
        padding: 0 2rem;
    }
}

/* Extra small mobile devices */
@media (max-width: 480px) {
    .brands-container {
        padding: 0 0.5rem;
    }
    
    .brands-title {
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .brands-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }
    
    .brand-block {
        padding: 1rem 0.5rem;
    }
    
    .brand-logo-wrapper {
        width: 40px;
        height: 40px;
        margin-bottom: 0.5rem;
    }
    
    .brand-name {
        font-size: 0.8rem;
    }
    
    .section-title {
        font-size: 1.5rem;
    }
    
    .section-subtitle {
        font-size: 0.9rem;
    }
    
    .modern-product-card {
        margin: 0 0.25rem;
    }
    
    .card-content {
        padding: 1rem;
    }
}
</style>

<!-- Pattern Background -->
<div class="pattern-background"></div>

<!-- Brands Section -->
<section class="brands-section">
    <div class="container">
            <div class="section-header">
                <h2 class="section-title">تسوق حسب العلامة التجارية</h2>
                <p class="section-subtitle">اختر من مجموعة واسعة من العلامات التجارية المعتمدة والموثوقة</p>
            </div>
            
            <div class="brands-grid" id="brands-grid">
            <?php
            // جلب البراندات من قاعدة البيانات
            $brands_query = "
                SELECT 
                    b.id,
                    b.name,
                    b.name_ar,
                    b.description,
                    b.logo,
                    b.sort_order
                FROM brands b 
                WHERE b.status = 1 
                ORDER BY b.sort_order ASC, b.name ASC
            ";
            
            $brands_result = $conn->query($brands_query);
            
            if($brands_result && $brands_result->num_rows > 0) {
                // ألوان وأيقونات للبراندات
                $brand_styles = [
                    'apple' => [
                        'color' => '#007AFF',
                        'gradient' => 'linear-gradient(135deg, #007AFF 0%, #0056CC 100%)',
                        'shadow' => 'rgba(0, 122, 255, 0.3)',
                        'icon' => 'fab fa-apple'
                    ],
                    'samsung' => [
                        'color' => '#1428A0',
                        'gradient' => 'linear-gradient(135deg, #1428A0 0%, #0F1C7A 100%)',
                        'shadow' => 'rgba(20, 40, 160, 0.3)',
                        'icon' => 'fas fa-mobile-alt'
                    ],
                    'huawei' => [
                        'color' => '#FF0000',
                        'gradient' => 'linear-gradient(135deg, #FF0000 0%, #CC0000 100%)',
                        'shadow' => 'rgba(255, 0, 0, 0.3)',
                        'icon' => 'fas fa-wifi'
                    ],
                    'xiaomi' => [
                        'color' => '#FF6900',
                        'gradient' => 'linear-gradient(135deg, #FF6900 0%, #E55A00 100%)',
                        'shadow' => 'rgba(255, 105, 0, 0.3)',
                        'icon' => 'fas fa-bolt'
                    ],
                    'oppo' => [
                        'color' => '#1BAA55',
                        'gradient' => 'linear-gradient(135deg, #1BAA55 0%, #0F8A3F 100%)',
                        'shadow' => 'rgba(27, 170, 85, 0.3)',
                        'icon' => 'fas fa-mobile-alt'
                    ],
                    'vivo' => [
                        'color' => '#5470FF',
                        'gradient' => 'linear-gradient(135deg, #5470FF 0%, #4059D6 100%)',
                        'shadow' => 'rgba(84, 112, 255, 0.3)',
                        'icon' => 'fas fa-mobile-alt'
                    ],
                    'oneplus' => [
                        'color' => '#EB0028',
                        'gradient' => 'linear-gradient(135deg, #EB0028 0%, #C40020 100%)',
                        'shadow' => 'rgba(235, 0, 40, 0.3)',
                        'icon' => 'fas fa-rocket'
                    ],
                    'realme' => [
                        'color' => '#FFCC00',
                        'gradient' => 'linear-gradient(135deg, #FFCC00 0%, #E6B800 100%)',
                        'shadow' => 'rgba(255, 204, 0, 0.3)',
                        'icon' => 'fas fa-bolt'
                    ],
                    'google' => [
                        'color' => '#4285F4',
                        'gradient' => 'linear-gradient(135deg, #4285F4 0%, #3367D6 100%)',
                        'shadow' => 'rgba(66, 133, 244, 0.3)',
                        'icon' => 'fab fa-google'
                    ],
                    'anker' => [
                        'color' => '#0066CC',
                        'gradient' => 'linear-gradient(135deg, #0066CC 0%, #004C99 100%)',
                        'shadow' => 'rgba(0, 102, 204, 0.3)',
                        'icon' => 'fas fa-charging-station'
                    ],
                    'infinix' => [
                        'color' => '#1E88E5',
                        'gradient' => 'linear-gradient(135deg, #1E88E5 0%, #1565C0 100%)',
                        'shadow' => 'rgba(30, 136, 229, 0.3)',
                        'icon' => 'fas fa-mobile-alt'
                    ],
                    'tecno' => [
                        'color' => '#4CAF50',
                        'gradient' => 'linear-gradient(135deg, #4CAF50 0%, #388E3C 100%)',
                        'shadow' => 'rgba(76, 175, 80, 0.3)',
                        'icon' => 'fas fa-mobile-alt'
                    ],
                    'default' => [
                        'color' => '#6b7280',
                        'gradient' => 'linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%)',
                        'shadow' => 'rgba(107, 114, 128, 0.3)',
                        'icon' => 'fas fa-mobile-alt'
                    ]
                ];
                
                while($brand = $brands_result->fetch_assoc()) {
                    // تحديد النمط بناءً على اسم البراند
                    $brand_key = strtolower(trim($brand['name']));
                    $style = isset($brand_styles[$brand_key]) ? $brand_styles[$brand_key] : $brand_styles['default'];
                    
                    // استخدام الاسم العربي إذا كان متاحاً
                    $display_name = !empty($brand['name_ar']) ? $brand['name_ar'] : $brand['name'];
            ?>
            <div class="brand-block" 
                 onclick="goToBrand(<?= $brand['id'] ?>)"
                 style="--brand-color: <?= $style['color'] ?>; 
                        --brand-gradient: <?= $style['gradient'] ?>; 
                        --brand-shadow: <?= $style['shadow'] ?>; cursor: pointer;"
                 data-brand-id="<?= $brand['id'] ?>"
                 data-brand-name="<?= htmlspecialchars($display_name) ?>"
                 role="button"
                 tabindex="0">
                
                <div class="brand-logo-wrapper">
                    <?php
                    // فحص وجود لوجو البراند من مجلد admin/images
                    $logo_displayed = false;
                    $brand_key = strtolower(trim($brand['name']));
                    
                    // إضافة خيارات بحث متعددة للبراند
                    $search_keys = [
                        $brand_key,
                        strtolower(trim($brand['name_ar'] ?? '')),
                        str_replace(' ', '', $brand_key),
                        str_replace([' ', '-', '_'], '', $brand_key)
                    ];
                    $search_keys = array_filter(array_unique($search_keys));

                    // قائمة اللوغوهات المتوفرة مع خيارات متعددة لكل براند
                    $available_logos = [
                        'apple' => 'apple.png',
                        'ابل' => 'apple.png',
                        'samsung' => 'samsung-seeklogo.png',
                        'سامسونج' => 'samsung-seeklogo.png',
                        'huawei' => 'huawei-seeklogo.png',
                        'هواوي' => 'huawei-seeklogo.png',
                        'xiaomi' => 'xiaomi.png',
                        'شاومي' => 'xiaomi.png',
                        'oppo' => 'oppo.png',
                        'اوبو' => 'oppo.png',
                        'vivo' => 'vivo-seeklogo.png',
                        'فيفو' => 'vivo-seeklogo.png',
                        'infinix' => 'infinix-seeklogo.png',
                        'انفينكس' => 'infinix-seeklogo.png',
                        'tecno' => 'tecno-smartphone-seeklogo.png',
                        'تكنو' => 'tecno-smartphone-seeklogo.png',
                        'oneplus' => 'oneplus-seeklogo.png',
                        'ون بلس' => 'oneplus-seeklogo.png',
                        'realme' => 'realme-seeklogo.png',
                        'ريلمي' => 'realme-seeklogo.png',
                        'honor' => 'hihonor-seeklogo.png',
                        'هونر' => 'hihonor-seeklogo.png',
                        'hihonor' => 'hihonor-seeklogo.png',
                        'itel' => 'itel-seeklogo.png',
                        'ايتل' => 'itel-seeklogo.png',
                        'google' => 'google-2015-seeklogo.png',
                        'جوجل' => 'google-2015-seeklogo.png'
                    ];

                    // البحث عن اللوغو المناسب باستخدام جميع المفاتيح
                    foreach($search_keys as $key) {
                        if(isset($available_logos[$key])) {
                            $logo_path = 'admin/images/' . $available_logos[$key];
                            if(file_exists($logo_path)) {
                                echo '<img src="' . htmlspecialchars($logo_path) . '" alt="' . htmlspecialchars($display_name) . '" class="brand-logo">';
                                $logo_displayed = true;
                                break;
                            }
                        }
                    }
                    
                    // البحث الذكي - إذا لم يوجد تطابق مباشر، ابحث في أسماء الملفات
                    if(!$logo_displayed) {
                        $logo_files = glob('admin/images/*.png');
                        foreach($logo_files as $file) {
                            $filename = basename($file, '.png');
                            $filename_clean = strtolower(str_replace(['-seeklogo', '-logo', '_logo'], '', $filename));
                            
                            // فحص التطابق الجزئي مع جميع مفاتيح البحث
                            foreach($search_keys as $search_key) {
                                if(strpos($filename_clean, $search_key) !== false || strpos($search_key, $filename_clean) !== false) {
                                    echo '<img src="' . htmlspecialchars($file) . '" alt="' . htmlspecialchars($display_name) . '" class="brand-logo">';
                                    $logo_displayed = true;
                                    break 2; // خروج من كلا الحلقتين
                                }
                            }
                        }
                    }

                    // إذا لم يوجد لوغو، استخدم الأيقونة
                    if(!$logo_displayed) {
                        echo '<i class="' . $style['icon'] . ' brand-icon"></i>';
                    }
                    ?>
                </div>
                
                <h3 class="brand-name"><?= htmlspecialchars($display_name) ?></h3>
            </div>
            <?php 
                }
            } else {
            ?>
            <div class="no-brands-message">
                <i class="fas fa-box-open"></i>
                <h3>لا توجد علامات تجارية متاحة</h3>
                <p>سيتم إضافة علامات تجارية جديدة قريباً</p>
            </div>
            <?php 
            }
            ?>
            </div>
            
            <!-- Show More Button for Mobile -->
            <div class="show-more-container" id="show-more-container">
                <button class="show-more-btn" id="show-more-btn" onclick="toggleBrands()">
                    <i class="fas fa-chevron-down"></i>
                    عرض المزيد
                </button>
            </div>
    </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="section products-section" id="products">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">أحدث المنتجات</h2>
            <p class="section-subtitle">اكتشف أحدث الملحقات والمنتجات التي وصلت حديثاً إلى متجرنا</p>
        </div>
    
        <div class="products-grid" id="main-products-grid">
            <?php
            // استعلام المنتجات مع جلب معلومات البراند والأسعار
            $products_query = "
                SELECT 
                    p.id,
                    p.product_name,
                    p.description,
                    p.date_created,
                    p.has_colors,
                    p.colors,
                    p.category_id,
                    c.category as category_name,
                    sc.sub_category as sub_category_name,
                    b.name as brand_name,
                    i.price,
                    i.quantity
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN sub_categories sc ON p.sub_category_id = sc.id 
                LEFT JOIN brands b ON p.category_id = b.id
                LEFT JOIN inventory i ON p.id = i.product_id
                WHERE p.status = 1
                ORDER BY p.date_created DESC 
                LIMIT 12
            ";
            
            $featured_products = $conn->query($products_query);
            
            if($featured_products && $featured_products->num_rows > 0):
                while($product = $featured_products->fetch_assoc()):
                    // Get product images
                    $image_path = 'uploads/product_'.$product['id'];
                    $images = [];
                    if(is_dir($image_path)) {
                        $files = scandir($image_path);
                        foreach($files as $file) {
                            if(!in_array($file, ['.', '..'])) {
                                $images[] = $image_path.'/'.$file;
                            }
                        }
                    }
                    
                    // Default image if no images found
                    if(empty($images)) {
                        $images[] = 'assets/images/no-image.svg';
                    }
                    
                    // Format price correctly
                    $formatted_price = "السعر عند الطلب";
                    if(isset($product['price']) && $product['price'] > 0) {
                        $formatted_price = number_format($product['price'], 0, '.', ',') . " د.ع";
                    }
            ?>
                <div class="homepage-product-card" onclick="window.location.href='view_product.php?id=<?= md5($product['id']) ?>'">
                    <div class="card-image-container">
                        <img src="<?= validate_image($images[0]) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" loading="lazy">
                        <?php if(isset($product['featured']) && $product['featured'] == 1): ?>
                            <div class="new-badge">الأكثر مبيعاً</div>
                        <?php endif; ?>
                    </div>
                    <div class="card-content">
                        <div class="product-category">
                            <?= isset($product['brand_name']) ? htmlspecialchars($product['brand_name']) : 'ملحقات' ?>
                        </div>
                        <h3 class="product-name"><?= htmlspecialchars($product['product_name']) ?></h3>
                        <p class="product-description"><?= mb_substr(strip_tags(html_entity_decode($product['description'])), 0, 100) ?>...</p>
                        <div class="card-actions">
                            <div class="product-price"><?= $formatted_price ?></div>
                            <button class="add-to-cart-btn" onclick="event.stopPropagation(); addToCart(this, <?= $product['id'] ?>)">
                                <i class="fas fa-cart-plus"></i>
                                أضف للسلة
                            </button>
                        </div>
                    </div>
                </div>
            <?php 
                endwhile; 
            else:
            ?>
            <div class="no-products-message">
                <i class="fas fa-box-open"></i>
                <h3>لا توجد منتجات متاحة حالياً</h3>
                <p>نعمل على إضافة منتجات جديدة قريباً</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">لماذا Gayar Plus؟</h2>
            <p class="section-subtitle">نحن نفخر بتقديم تجربة تسوق استثنائية تجمع بين الجودة والأمان والراحة</p>
        </div>
        
        <div class="features-grid">
        <div class="feature-card will-change">
            <div class="feature-icon">
                <i class="fas fa-shield-check"></i>
            </div>
            <h3 class="feature-title">ضمان الأصالة</h3>
            <p class="feature-description">جميع منتجاتنا أصلية 100% ومستوردة من مصادر موثقة مع ضمان شامل يصل إلى سنة كاملة</p>
        </div>

        <div class="feature-card will-change">
            <div class="feature-icon">
                <i class="fas fa-rocket"></i>
            </div>
            <h3 class="feature-title">توصيل فائق السرعة</h3>
            <p class="feature-description">نوصل طلبك في نفس اليوم داخل بغداد وخلال 24-48 ساعة لجميع المحافظات العراقية</p>
        </div>

        <div class="feature-card will-change">
            <div class="feature-icon">
                <i class="fas fa-headset"></i>
            </div>
            <h3 class="feature-title">دعم فني متخصص</h3>
            <p class="feature-description">فريق دعم فني محترف متاح على مدار الساعة لحل جميع استفساراتك ومساعدتك</p>
        </div>

        <div class="feature-card will-change">
            <div class="feature-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h3 class="feature-title">أمان المعاملات</h3>
            <p class="feature-description">نظام دفع آمن ومشفر مع حماية كاملة لبياناتك الشخصية والمالية</p>
        </div>
    </div>
</section>

<!-- Modern Footer Only -->

<!-- Working Cart System -->
<script>
// Cart variables
var cartCount = 0;

// Show notifications
function showNotification(message, type) {
    type = type || 'success';
    document.querySelectorAll('.cart-notification').forEach(function(n) { n.remove(); });
    
    var notification = document.createElement('div');
    notification.className = 'cart-notification';
    notification.style.cssText = 
        'position: fixed; top: 80px; right: 20px; z-index: 10000;' +
        'background: ' + (type === 'success' ? '#10b981' : (type === 'info' ? '#3b82f6' : '#ef4444')) + ';' +
        'color: white; padding: 1rem 1.5rem; border-radius: 12px;' +
        'box-shadow: 0 10px 30px rgba(0,0,0,0.2); font-weight: 600;' +
        'max-width: 300px; font-size: 14px;';
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(function() { notification.remove(); }, 3000);
}

// Mobile brands toggle functionality
var brandsExpanded = false;

function toggleBrands() {
    const brandBlocks = document.querySelectorAll('.brand-block:nth-child(n+5)');
    const showMoreBtn = document.getElementById('show-more-btn');
    
    if (!brandsExpanded) {
        // Show all brands
        brandBlocks.forEach(block => {
            block.classList.add('show-all');
        });
        showMoreBtn.innerHTML = '<i class="fas fa-chevron-up"></i> عرض أقل';
        brandsExpanded = true;
    } else {
        // Hide extra brands
        brandBlocks.forEach(block => {
            block.classList.remove('show-all');
        });
        showMoreBtn.innerHTML = '<i class="fas fa-chevron-down"></i> عرض المزيد';
        brandsExpanded = false;
    }
}

// Brand click behavior handled by the main goToBrand function below

// Brand interaction is handled by onclick attributes in HTML
// Add to cart function
function addToCart(button, productId) {
    console.log('🛒 Adding product:', productId);
    
    if (button.disabled) return;
    
    button.disabled = true;
    var originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإضافة...';
    
    fetch('ajax/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&quantity=1'
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('خطأ في الشبكة: ' + response.status);
        }
        return response.json();
    })
    .then(function(data) {
        console.log('استجابة الخادم:', data);
        
        if (data.success) {
            cartCount = data.cart_count;
            updateCartDisplay(cartCount);
            showNotification('✅ تم إضافة المنتج للسلة!');
            
            button.innerHTML = '<i class="fas fa-check"></i> تمت الإضافة!';
            button.style.background = '#10b981';
            
            setTimeout(function() {
                button.innerHTML = originalHTML;
                button.style.background = '';
                button.disabled = false;
            }, 2000);
        } else {
            throw new Error(data.message || 'فشل في إضافة المنتج');
        }
    })
    .catch(function(error) {
        console.error('خطأ:', error);
        showNotification('❌ ' + error.message, 'error');
        button.innerHTML = originalHTML;
        button.disabled = false;
    });
}

// View product details
function viewProduct(productId) {
    if (productId) {
        if (typeof productId === 'number' || /^\d+$/.test(productId)) {
            window.location.href = 'view_product.php?id=' + productId;
        } else {
            window.location.href = 'view_product.php?id=' + productId;
        }
    }
}

// Update cart display
function updateCartDisplay(count) {
    var cartElements = document.querySelectorAll('.cart-count, .cart-badge, #cart-count');
    cartElements.forEach(function(element) {
        if (element) {
            element.textContent = count;
        }
    });
}

// Load cart count
function loadCartCount() {
    fetch('ajax/get_cart_count.php')
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            cartCount = data.count;
            updateCartDisplay(cartCount);
            console.log('📊 عدد عناصر السلة:', cartCount);
        }
    })
    .catch(function(error) {
        console.log('لا يمكن تحديث العدد:', error);
    });
}

// Brand navigation function
function goToBrand(brandId) {
    if (!brandId) {
        console.error('❌ Brand ID is required');
        return;
    }
    
    console.log('🏷️ الانتقال لصفحة البراند:', brandId);
    
    // البحث عن البراند في البلوكات للحصول على الاسم
    var brandBlock = document.querySelector('[data-brand-id="' + brandId + '"]');
    var brandName = 'البراند';
    
    if (brandBlock) {
        var nameAttr = brandBlock.getAttribute('data-brand-name');
        if (nameAttr && nameAttr !== 'null' && nameAttr.trim() !== '') {
            brandName = nameAttr;
        }
    }
    
    showNotification('🔍 جاري تحميل منتجات ' + brandName + '...', 'info');
    
    // الانتقال لصفحة المنتجات مع فلتر البراند
    setTimeout(function() {
        window.location.href = './?p=products&brand=' + brandId;
    }, 800);
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 تحميل نظام السلة والبراندات...');
    
    loadCartCount();
    
    var cartButtons = document.querySelectorAll('.cart-btn, .shopping-cart-btn');
    cartButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = './?p=cart';
        });
    });
    
    var addButtons = document.querySelectorAll('.add-to-cart-btn');
    console.log('✅ وُجد ' + addButtons.length + ' زر إضافة للسلة');
    
    var brandBlocks = document.querySelectorAll('.brand-block');
    console.log('🏷️ وُجد ' + brandBlocks.length + ' بلوك براند');
    
    // إضافة أحداث النقر للبراندات
    brandBlocks.forEach(function(block) {
        block.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var brandId = this.getAttribute('data-brand-id');
            console.log('🎯 تم النقر على البراند:', brandId);
            
            if (brandId) {
                goToBrand(brandId);
            } else {
                console.error('❌ لم يتم العثور على معرف البراند');
            }
        });
        
        // إضافة دعم لوحة المفاتيح
        block.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });
    
    console.log('🎉 تم تحميل النظام بنجاح!');
});

// Export functions globally
window.addToCart = addToCart;
window.viewProduct = viewProduct;
window.showNotification = showNotification;
window.goToBrand = goToBrand;
</script>

<?php include 'inc/modern-footer.php'; ?>
