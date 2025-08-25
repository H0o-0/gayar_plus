<?php
$page_title = "تأكيد الطلب - Gayar Plus";
require_once 'config.php';
require_once 'classes/TextCleaner.php';

include 'inc/header.php';

// Get order ID from URL
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch order details
$order = null;
if ($order_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();
}
?>

<style>
/* Order Confirmation Styles */
.order-confirmation-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.order-confirmation-header {
    text-align: center;
    margin-bottom: 2rem;
}

.order-confirmation-header h1 {
    font-size: 2rem;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.order-confirmation-header p {
    color: var(--text-secondary);
    font-size: 1.1rem;
}

.confirmation-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--medium-gray);
    text-align: center;
}

.confirmation-icon {
    font-size: 4rem;
    color: #10b981;
    margin-bottom: 1.5rem;
}

.confirmation-title {
    font-size: 1.5rem;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.confirmation-message {
    color: var(--text-secondary);
    margin-bottom: 2rem;
    font-size: 1.1rem;
    line-height: 1.6;
}

.order-details {
    background: var(--light-gray);
    border-radius: 12px;
    padding: 1.5rem;
    margin: 2rem 0;
    text-align: right;
}

.order-details h3 {
    color: var(--text-primary);
    margin-bottom: 1rem;
    text-align: center;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--medium-gray);
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 500;
    color: var(--text-primary);
}

.detail-value {
    font-weight: 600;
    color: var(--text-primary);
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-blue), var(--primary-navy));
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
    display: inline-block;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-secondary {
    background: white;
    color: var(--primary-blue);
    border: 2px solid var(--primary-blue);
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
    display: inline-block;
}

.btn-secondary:hover {
    background: var(--light-gray);
}

@media (max-width: 768px) {
    .order-confirmation-container {
        margin: 1rem auto;
        padding: 0 0.5rem;
    }
    
    .confirmation-card {
        padding: 1.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<div class="order-confirmation-container">
    <div class="order-confirmation-header">
        <h1><i class="fas fa-check-circle"></i> تأكيد الطلب</h1>
        <p>شكراً لك على طلبك من متجر Gayar Plus</p>
    </div>
    
    <?php if ($order && $order_id > 0): ?>
        <div class="confirmation-card">
            <div class="confirmation-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="confirmation-title">تم استلام طلبك بنجاح!</h2>
            <p class="confirmation-message">
                شكراً لك على تسوقك معنا. لقد استلمنا طلبك وسنقوم بمعالجته في أقرب وقت ممكن.
                سيتم التواصل معك قريباً لتأكيد تفاصيل الطلب وترتيب التوصيل.
            </p>
            
            <div class="order-details">
                <h3>تفاصيل الطلب</h3>
                <div class="detail-row">
                    <span class="detail-label">رقم الطلب:</span>
                    <span class="detail-value">ORD-<?php echo $order_id; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">تاريخ الطلب:</span>
                    <span class="detail-value"><?php echo date('Y-m-d H:i', strtotime($order['date_created'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">المبلغ الإجمالي:</span>
                    <span class="detail-value"><?php echo TextCleaner::formatPrice($order['amount']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">طريقة الدفع:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($order['payment_method']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">حالة الطلب:</span>
                    <span class="detail-value">
                        <?php 
                        switch($order['status']) {
                            case 0: echo 'قيد الانتظار'; break;
                            case 1: echo 'قيد التجهيز'; break;
                            case 2: echo 'قيد التوصيل'; break;
                            case 3: echo 'مكتمل'; break;
                            case 4: echo 'ملغى'; break;
                            default: echo 'غير معروف';
                        }
                        ?>
                    </span>
                </div>
            </div>
            
            <div class="action-buttons">
                <a href="./" class="btn-primary">
                    <i class="fas fa-shopping-bag"></i> متابعة التسوق
                </a>
                <a href="./?p=contact" class="btn-secondary">
                    <i class="fas fa-headset"></i> تواصل معنا
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="confirmation-card">
            <div class="confirmation-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h2 class="confirmation-title">خطأ في تحميل تفاصيل الطلب</h2>
            <p class="confirmation-message">
                عذراً، لم نتمكن من العثور على تفاصيل الطلب المطلوب. 
                إذا كنت تواجه مشكلة، يرجى التواصل مع فريق الدعم.
            </p>
            
            <div class="action-buttons">
                <a href="./" class="btn-primary">
                    <i class="fas fa-home"></i> العودة للرئيسية
                </a>
                <a href="./?p=contact" class="btn-secondary">
                    <i class="fas fa-headset"></i> تواصل معنا
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'inc/modern-footer.php'; ?>