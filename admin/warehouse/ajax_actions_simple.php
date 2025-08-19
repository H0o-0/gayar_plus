<?php
// ملف AJAX مبسط للمخزن
error_reporting(E_ALL);
ini_set('display_errors', 0); // لا نعرض الأخطاء في JSON

require_once('../config.php');

// تأكد من إرجاع JSON
header('Content-Type: application/json');

try {
    // التحقق من وجود action
    $action = $_POST['action'] ?? $_GET['action'] ?? null;
    
    if (!$action) {
        throw new Exception('لم يتم تحديد العملية');
    }
    
    switch ($action) {
        case 'delete_product':
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('معرف المنتج غير صحيح');
            }
            
            $stmt = $conn->prepare("DELETE FROM temp_warehouse WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['status' => 'success', 'message' => 'تم حذف المنتج بنجاح']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'المنتج غير موجود']);
                }
            } else {
                throw new Exception('فشل في حذف المنتج: ' . $conn->error);
            }
            break;
            
        case 'bulk_delete':
            $ids = $_POST['ids'] ?? [];
            if (empty($ids) || !is_array($ids)) {
                throw new Exception('لم يتم تحديد منتجات للحذف');
            }
            
            // تنظيف المعرفات
            $ids = array_filter(array_map('intval', $ids), function($id) {
                return $id > 0;
            });
            
            if (empty($ids)) {
                throw new Exception('معرفات غير صحيحة');
            }
            
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            $stmt = $conn->prepare("DELETE FROM temp_warehouse WHERE id IN ($placeholders)");
            $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
            
            if ($stmt->execute()) {
                $affected = $stmt->affected_rows;
                echo json_encode(['status' => 'success', 'message' => "تم حذف $affected منتج بنجاح"]);
            } else {
                throw new Exception('فشل في الحذف المجمع: ' . $conn->error);
            }
            break;
            
        case 'delete_unclassified':
            $stmt = $conn->prepare("DELETE FROM temp_warehouse WHERE status = 'unclassified'");
            
            if ($stmt->execute()) {
                $affected = $stmt->affected_rows;
                echo json_encode(['status' => 'success', 'message' => "تم حذف $affected منتج غير مصنف بنجاح"]);
            } else {
                throw new Exception('فشل في حذف المنتجات غير المصنفة: ' . $conn->error);
            }
            break;
            
        case 'quick_publish':
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('معرف المنتج غير صحيح');
            }
            
            // التحقق من وجود المنتج
            $stmt = $conn->prepare("SELECT id FROM temp_warehouse WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 0) {
                throw new Exception('المنتج غير موجود');
            }
            
            echo json_encode([
                'status' => 'success',
                'message' => 'جاري التحويل إلى صفحة التحرير',
                'redirect' => 'index.php?page=warehouse/edit_product&id=' . $id
            ]);
            break;
            
        default:
            throw new Exception('عملية غير مدعومة: ' . $action);
    }
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} catch (Error $e) {
    echo json_encode(['status' => 'error', 'message' => 'خطأ في النظام: ' . $e->getMessage()]);
}
?>