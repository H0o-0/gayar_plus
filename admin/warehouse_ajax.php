<?php
require_once('../config.php');

header('Content-Type: application/json');

// دعم GET و POST requests
$action = $_POST['action'] ?? $_GET['action'] ?? null;

// Ensure Master class is available
if (file_exists(base_app . 'classes/Master.php')) {
    require_once(base_app . 'classes/Master.php');
} else {
    echo json_encode(['status' => 'error', 'message' => 'ملف Master.php غير موجود']);
    exit;
}

$Master = new Master();

if (!$action) {
    echo json_encode(['status' => 'error', 'message' => 'لم يتم تحديد العملية']);
    exit;
}

switch ($action) {
    case 'delete_product':
        deleteProduct($conn);
        break;
    case 'publish_product':
        publishProduct($conn);
        break;
    case 'quick_publish':
        quickPublish($conn);
        break;
    case 'republish_product':
        republishProduct($conn);
        break;
    case 'bulk_delete':
        bulkDelete($conn);
        break;
    case 'bulk_publish':
        bulkPublish($conn);
        break;
    case 'bulk_save_draft':
        bulkSaveDraft($conn);
        break;
    case 'delete_unclassified':
        deleteUnclassified($conn);
        break;
    case 'auto_classify':
        autoClassify($conn, $Master); // Pass Master instance
        break;
    case 'get_subcategories':
        getSubcategories($conn);
        break;
    case 'filter_products':
        filterProducts($conn);
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => "عملية غير مدعومة: {$action}"]);
}

function deleteProduct($conn) {
    try {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) {
            throw new Exception('معرف المنتج غير صحيح');
        }
        
        $sql = "DELETE FROM temp_warehouse WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'تم حذف المنتج بنجاح']);
        } else {
            throw new Exception('فشل في حذف المنتج: ' . $stmt->error);
        }
        $stmt->close();
    } catch (Exception $e) {
        error_log("Delete product error: " . $e->getMessage() . "\n", 3, base_app . "debug.log");
        echo json_encode(['status' => 'error', 'message' => 'حدث خطأ فادح أثناء حذف المنتج.']);
    }
}

function publishProduct($conn) {
    global $_settings;
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'معرف غير صحيح']);
        return;
    }
    
    // الحصول على بيانات المنتج من المخزن المؤقت
    $product_query = $conn->query("SELECT * FROM temp_warehouse WHERE id = $id");
    if ($product_query->num_rows == 0) {
        echo json_encode(['status' => 'error', 'message' => 'المنتج غير موجود']);
        return;
    }
    
    $product = $product_query->fetch_assoc();
    
    // التحقق من وجود التصنيفات
    if (!$product['category_id'] || !$product['sub_category_id']) {
        echo json_encode(['status' => 'error', 'message' => 'يجب تصنيف المنتج أولاً']);
        return;
    }
    
    // إضافة المنتج للنظام الأساسي
    $product_name = $conn->real_escape_string($product['product_name']);
    $category_id = $product['category_id'];
    $sub_category_id = $product['sub_category_id'];
    
    // إدراج في جدول products
    $insert_product = "INSERT INTO products (category_id, sub_category_id, product_name, description, status) 
                       VALUES ($category_id, $sub_category_id, '$product_name', 'منتج مستورد من المخزن المؤقت', 1)";
    
    if ($conn->query($insert_product)) {
        $new_product_id = $conn->insert_id;
        
        // إدراج في جدول inventory
        $price = $product['original_price'];
        $insert_inventory = "INSERT INTO inventory (product_id, quantity, unit, price, size) 
                            VALUES ($new_product_id, 100, 'قطعة', $price, 'عادي')";
        
        if ($conn->query($insert_inventory)) {
            // تحديث حالة المنتج في المخزن المؤقت
            $conn->query("UPDATE temp_warehouse SET status = 'published' WHERE id = $id");
            
            echo json_encode(['status' => 'success', 'message' => 'تم نشر المنتج بنجاح']);
        } else {
            // حذف المنتج إذا فشل إدراج المخزون
            $conn->query("DELETE FROM products WHERE id = $new_product_id");
            echo json_encode(['status' => 'error', 'message' => 'فشل في إضافة المخزون: ' . $conn->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل في إضافة المنتج: ' . $conn->error]);
    }
}

function quickPublish($conn) {
    global $_settings;
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'معرف غير صحيح']);
        return;
    }
    
    // التحقق من وجود المنتج
    $product_query = $conn->query("SELECT * FROM temp_warehouse WHERE id = $id");
    if ($product_query->num_rows == 0) {
        echo json_encode(['status' => 'error', 'message' => 'المنتج غير موجود']);
        return;
    }
    
    // إرجاع رابط صفحة التحرير
    echo json_encode([
        'status' => 'success',
        'message' => 'جاري التحويل إلى صفحة التحرير',
        'redirect' => 'index.php?page=warehouse/edit_product&id=' . $id
    ]);
}

function republishProduct($conn) {
    global $_settings;
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'معرف غير صحيح']);
        return;
    }
    
    // التحقق من وجود المنتج
    $product_query = $conn->query("SELECT * FROM temp_warehouse WHERE id = $id");
    if ($product_query->num_rows == 0) {
        echo json_encode(['status' => 'error', 'message' => 'المنتج غير موجود']);
        return;
    }
    
    $product = $product_query->fetch_assoc();
    
    // التحقق من أن المنتج منشور بالفعل
    if ($product['status'] != 'published') {
        echo json_encode(['status' => 'error', 'message' => 'هذا المنتج غير منشور بعد']);
        return;
    }
    
    // تحويل المستخدم إلى صفحة التحرير
    echo json_encode([
        'status' => 'success',
        'message' => 'جاري التحويل إلى صفحة التحرير',
        'redirect' => 'index.php?page=warehouse/edit_product&id=' . $id
    ]);
}

function getSubcategories($conn) {
    global $_settings;
    $category_id = intval($_POST['category_id'] ?? 0);
    if ($category_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'معرف الفئة غير صحيح']);
        return;
    }
    
    $subcategories = [];
    $query = $conn->query("SELECT id, sub_category FROM sub_categories WHERE parent_id = $category_id AND status = 1");
    
    while ($row = $query->fetch_assoc()) {
        $subcategories[$row['id']] = $row['sub_category'];
    }
    
    echo json_encode([
        'status' => 'success',
        'subcategories' => $subcategories
    ]);
}

function bulkDelete($conn) {
    try {
        $ids = $_POST['ids'] ?? [];
        if (empty($ids) || !is_array($ids)) {
            throw new Exception('لم يتم تحديد منتجات للحذف');
        }
        
        $ids = array_filter(array_map('intval', $ids), function($id) {
            return $id > 0;
        });

        if (empty($ids)) {
            throw new Exception('معرفات غير صحيحة');
        }

        $chunk_size = 100;
        $chunks = array_chunk($ids, $chunk_size);
        $total_affected = 0;

        $conn->begin_transaction();

        foreach ($chunks as $chunk) {
            $placeholders = implode(',', array_fill(0, count($chunk), '?'));
            $sql = "DELETE FROM temp_warehouse WHERE id IN ($placeholders)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(str_repeat('i', count($chunk)), ...$chunk);
            
            if ($stmt->execute()) {
                $total_affected += $stmt->affected_rows;
            } else {
                throw new Exception('فشل في الحذف: ' . $stmt->error);
            }
            $stmt->close();
        }

        $conn->commit();

        echo json_encode([
            'status' => 'success',
            'message' => "تم حذف {$total_affected} منتج بنجاح."
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Bulk delete error: " . $e->getMessage() . "\n", 3, base_app . "debug.log");
        echo json_encode(['status' => 'error', 'message' => 'حدث خطأ فادح أثناء الحذف المجمع.']);
    }
}

function bulkPublish($conn) {
    global $_settings;
    $ids = $_POST['ids'] ?? [];
    if (empty($ids) || !is_array($ids)) {
        echo json_encode(['status' => 'error', 'message' => 'لم يتم تحديد منتجات للنشر']);
        return;
    }
    
    $ids = array_map('intval', $ids);
    $success_count = 0;
    $error_count = 0;
    
    foreach ($ids as $id) {
        try {
            // محاكاة نشر كل منتج
            $_POST['id'] = $id;
            ob_start();
            publishProduct($conn); // Pass $conn
            $result = ob_get_clean();
            $response = json_decode($result, true);
            
            if ($response && $response['status'] == 'success') {
                $success_count++;
            } else {
                $error_count++;
            }
        } catch (Exception $e) {
            $error_count++;
        }
    }
    
    if ($success_count > 0) {
        echo json_encode(['status' => 'success', 'message' => "تم نشر $success_count منتج بنجاح" . ($error_count > 0 ? " و فشل $error_count منتج" : "")]);
    } else {
        echo json_encode(['status' => 'error', 'message' => "فشل في نشر جميع المنتجات"]);
    }
}

function bulkSaveDraft($conn) {
    global $_settings;
    $ids = $_POST['ids'] ?? [];
    if (empty($ids) || !is_array($ids)) {
        echo json_encode(['status' => 'error', 'message' => 'لم يتم تحديد منتجات للحفظ']);
        return;
    }
    
    $ids = array_map('intval', $ids);
    $success_count = 0;
    $error_count = 0;
    
    // الحصول على البيانات المشتركة
    $category_id = intval($_POST['category_id'] ?? 0);
    $sub_category_id = intval($_POST['sub_category_id'] ?? 0);
    $brand = $conn->real_escape_string($_POST['brand'] ?? '');
    
    // الحصول على البيانات الفردية
    $prices = $_POST['prices'] ?? [];
    $names = $_POST['names'] ?? [];
    
    foreach ($ids as $id) {
        try {
            // تحديث اسم المنتج والسعر إذا تم توفيرهما
            $update_fields = [];
            if (isset($names[$id])) {
                $update_fields[] = "product_name = '" . $conn->real_escape_string($names[$id]) . "'";
            }
            if (isset($prices[$id])) {
                $update_fields[] = "original_price = " . floatval($prices[$id]);
            }
            if ($category_id > 0) {
                $update_fields[] = "category_id = $category_id";
            }
            if ($sub_category_id > 0) {
                $update_fields[] = "sub_category_id = $sub_category_id";
            }
            if (!empty($brand)) {
                $update_fields[] = "confirmed_brand = '$brand'";
            }
            $update_fields[] = "status = 'classified'";
            
            if (!empty($update_fields)) {
                $update_sql = "UPDATE temp_warehouse SET " . implode(', ', $update_fields) . " WHERE id = $id";
                if ($conn->query($update_sql)) {
                    $success_count++;
                } else {
                    $error_count++;
                }
            } else {
                $success_count++; // لا توجد تحديثات مطلوبة
            }
        } catch (Exception $e) {
            $error_count++;
        }
    }
    
    if ($success_count > 0) {
        echo json_encode(['status' => 'success', 'message' => "تم حفظ $success_count منتج كمسودة" . ($error_count > 0 ? " وفشل $error_count منتج" : "")]);
    } else {
        echo json_encode(['status' => 'error', 'message' => "فشل في حفظ جميع المنتجات"]);
    }
}

function deleteUnclassified($conn) {
    global $_settings;
    $sql = "DELETE FROM temp_warehouse WHERE status = 'unclassified'";

    if ($conn->query($sql)) {
        $affected = $conn->affected_rows;

        echo json_encode([
            'status' => 'success',
            'message' => "تم حذف $affected منتج غير مصنف بنجاح"
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل في الحذف: ' . $conn->error]);
    }
}

/**
 * التصنيف التلقائي المحسن
 */
function autoClassify($conn, $Master) {
    $products = $conn->query("SELECT id, product_name FROM `temp_warehouse` WHERE category_id IS NULL");
    
    $fixed_count = 0;
    while ($product = $products->fetch_assoc()) {
        $product_id = $product['id'];
        $product_name = $product['product_name'];

        $classification = $Master->get_classification_from_name($product_name);

        if ($classification['category_id']) {
            $update_sql = "UPDATE `temp_warehouse` SET 
                            `category_id` = ?, 
                            `sub_category_id` = ?, 
                            `status` = 'classified' 
                          WHERE `id` = ?";
            
            $stmt = $conn->prepare($update_sql);
            $sub_cat_id_to_update = $classification['sub_category_id'] ? $classification['sub_category_id'] : null;
            $stmt->bind_param("iii", $classification['category_id'], $sub_cat_id_to_update, $product_id);
            
            if ($stmt->execute()) {
                $fixed_count++;
            }
            $stmt->close();
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => "تم تصنيف {$fixed_count} منتج تلقائياً.",
        'fixed_count' => $fixed_count
    ]);
}

function filterProducts($conn) {
    try {
        $brandFilter = $_POST['brand'] ?? '';
        $typeFilter = $_POST['type'] ?? '';
        $statusFilter = $_POST['status'] ?? '';
        $searchText = $_POST['search'] ?? '';

        $params = [];
        $types = '';

        $sql = "SELECT tw.*, c.category, sc.sub_category
                FROM temp_warehouse tw
                LEFT JOIN categories c ON tw.category_id = c.id
                LEFT JOIN sub_categories sc ON tw.sub_category_id = sc.id
                WHERE 1=1";

        if (!empty($brandFilter)) {
            $sql .= " AND c.category = ?";
            $params[] = $brandFilter;
            $types .= 's';
        }
        if (!empty($statusFilter)) {
            $sql .= " AND tw.status = ?";
            $params[] = $statusFilter;
            $types .= 's';
        }
        if (!empty($searchText)) {
            $sql .= " AND tw.product_name LIKE ?";
            $params[] = "%{$searchText}%";
            $types .= 's';
        }

        $sql .= " ORDER BY tw.created_at DESC LIMIT 200"; // Add a limit to prevent overload

        $stmt = $conn->prepare($sql);
        if(!empty($types)){
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $html = '';
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // HTML generation logic from your original file...
                // This is a simplified placeholder for brevity
                 $status_class = '';
                $status_text = '';
                switch($row['status']) {
                    case 'unclassified':
                        $status_class = 'warning';
                        $status_text = 'غير مصنف';
                        break;
                    case 'classified':
                        $status_class = 'primary';
                        $status_text = 'مصنف';
                        break;
                    case 'published':
                        $status_class = 'success';
                        $status_text = 'منشور';
                        break;
                }

                $html .= "<tr data-id='{$row['id']}' data-status='{$row['status']}' data-brand='{$row['suggested_brand']}' data-type='{$row['suggested_type']}'>";
                $html .= "<td><input type='checkbox' class='product-checkbox' value='{$row['id']}'></td>";
                $html .= "<td><strong>" . htmlspecialchars($row['product_name']) . "</strong><br><small class='text-muted'>تاريخ الإضافة: " . date('Y/m/d H:i', strtotime($row['created_at'])) . "</small></td>";
                $html .= "<td>" . ($row['original_price'] > 0 ? "<span class='badge badge-info'>" . number_format($row['original_price']) . " د.ع</span>" : "<span class='text-muted'>غير محدد</span>") . "</td>";
                $html .= "<td>" . ($row['suggested_brand'] ? "<span class='badge badge-primary'>" . htmlspecialchars($row['suggested_brand']) . "</span>" : "<span class='text-muted'>غير محدد</span>") . "</td>";
                $html .= "<td>" . ($row['category'] ? "<span class='badge badge-secondary'>" . htmlspecialchars($row['category']) . "</span>" . ($row['sub_category'] ? "<br><small class='text-muted'>" . htmlspecialchars($row['sub_category']) . "</small>" : "") : "<span class='text-muted'>غير محدد</span>") . "</td>";
                $html .= "<td><span class='badge badge-{$status_class}'>{$status_text}</span></td>";
                $html .= "<td><div class='btn-group btn-group-sm'>";
                if ($row['status'] != 'published') {
                    $html .= "<a href='./index.php?page=warehouse/edit_product&id={$row['id']}' class='btn btn-primary btn-sm' title='تحرير ونشر'><i class='fas fa-edit'></i></a>";
                    $html .= "<button class='btn btn-success btn-sm quick-publish' data-id='{$row['id']}' title='نشر سريع'><i class='fas fa-bolt'></i></button>";
                }
                $html .= "<button class='btn btn-danger btn-sm delete-product' data-id='{$row['id']}' title='حذف'><i class='fas fa-trash'></i></button>";
                $html .= "</div></td>";
                $html .= "</tr>";
            }
        } else {
            $html = '<tr><td colspan="7" class="text-center">لا توجد منتجات تطابق معايير البحث.</td></tr>';
        }

        echo json_encode(['status' => 'success', 'html' => $html]);
        $stmt->close();

    } catch (Exception $e) {
        error_log("Filter products error: " . $e->getMessage() . "\n", 3, base_app . "debug.log");
        echo json_encode(['status' => 'error', 'message' => 'حدث خطأ فادح أثناء الفلترة.']);
    }
}
?>