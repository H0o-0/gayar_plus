<?php
if(!defined('DB_SERVER')){
    // Check if we're being called via AJAX from admin panel
    if (isset($_GET['f']) && !empty($_GET['f'])) {
        require_once(dirname(__DIR__) . '/initialize.php');
    } else {
        require_once('../config.php');
    }
}
class Master extends DBConnection {
    private $settings;
    public function __construct(){
        global $_settings;
        $this->settings = $_settings;
        parent::__construct();
    }
    public function __destruct(){
        parent::__destruct();
    }
    function capture_err(){
        if(!$this->conn->error)
            return false;
        else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
            return json_encode($resp);
            exit;
        }
    }
    
    // Function to get series by brand
    function get_series_by_brand() {
        if (isset($_POST['brand_id']) && is_numeric($_POST['brand_id'])) {
            $brand_id = intval($_POST['brand_id']);
            
            // Debug: Log the brand_id
            error_log("Getting series for brand_id: " . $brand_id);
            
            // Use series table with brand_id (this is the correct table structure)
            $query = "SELECT id, COALESCE(NULLIF(name_ar, ''), name) as series_name FROM series WHERE brand_id = ? AND status = 1 ORDER BY sort_order ASC, series_name ASC";
            if ($stmt = $this->conn->prepare($query)) {
                $stmt->bind_param("i", $brand_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                $options = '';
                $count = 0;
                while ($row = $result->fetch_assoc()) {
                    $options .= '<option value="' . $row['id'] . '">' . htmlspecialchars($row['series_name']) . '</option>';
                    $count++;
                }
                
                // Debug: Log results
                error_log("Found $count series for brand_id: $brand_id");
                
                if($count == 0) {
                    echo '<option value="">لا توجد فئات لهذا البراند</option>';
                } else {
                    echo $options;
                }
                
                $stmt->close();
            } else {
                // Debug: Log prepare error
                error_log("Prepare failed: " . $this->conn->error);
                echo '<option value="">خطأ في الاستعلام: ' . $this->conn->error . '</option>';
            }
        } else {
            error_log("Invalid brand_id received: " . print_r($_POST, true));
            echo '<option value="">معرف البراند غير صحيح</option>';
        }
    }
    
    // Function to get models by series
    function get_models_by_series() {
        if (isset($_POST['series_id']) && is_numeric($_POST['series_id'])) {
            $series_id = intval($_POST['series_id']);
            
            $query = "SELECT id, name FROM models WHERE series_id = ? AND status = 1 ORDER BY name ASC";
            if ($stmt = $this->conn->prepare($query)) {
                $stmt->bind_param("i", $series_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                $options = '';
                while ($row = $result->fetch_assoc()) {
                    $options .= '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
                }
                
                echo $options;
                $stmt->close();
            }
        }
    }
    
    // ==================================================================
    //  البداية: إضافة دوال التصنيف الجديدة
    // ==================================================================
    /**
     * قاموس الاختصارات والعلامات التجارية
     */
    function get_brands_dictionary() {
        return [
            'apple' => [
                'patterns' => ['iph', 'ip', 'iphone', 'ios', 'ايفون', 'ابل', 'apple'],
                'models' => [
                    '7' => ['iphone 7', 'ايفون 7', 'ip7'],
                    '8' => ['iphone 8', 'ايفون 8', 'ip8'],
                    'x' => ['iphone x', 'ايفون x', 'ipx'],
                    '11' => ['iphone 11', 'ايفون 11', 'ip11'],
                    '12' => ['iphone 12', 'ايفون 12', 'ip12'],
                    '13' => ['iphone 13', 'ايفون 13', 'ip13'],
                    '14' => ['iphone 14', 'ايفون 14', 'ip14'],
                    '15' => ['iphone 15', 'ايفون 15', 'ip15'],
                    '16' => ['iphone 16', 'ايفون 16', 'ip16']
                ]
            ],
            'samsung' => [
                'patterns' => ['sam', 'galaxy', 'gal', 'note', 'سام', 'سامسونج', 'samsung'],
                'models' => [
                    's20' => ['galaxy s20', 's20', 'سامسونج s20'],
                    's21' => ['galaxy s21', 's21', 'سامسونج s21'],
                    's22' => ['galaxy s22', 's22', 'سامسونج s22'],
                    's23' => ['galaxy s23', 's23', 'سامسونج s23'],
                    'note20' => ['note 20', 'note20', 'نوت 20'],
                    'a52' => ['galaxy a52', 'a52', 'سامسونج a52'],
                    'a53' => ['galaxy a53', 'a53', 'سامسونج a53'],
                    'a54' => ['galaxy a54', 'a54', 'سامسونج a54']
                ]
            ],
            'huawei' => [
                'patterns' => ['hua', 'mate', 'p', 'هواوي', 'huawei'],
                'models' => [
                    'p30' => ['p30', 'p 30', 'هواوي p30'],
                    'p40' => ['p40', 'p 40', 'هواوي p40'],
                    'p50' => ['p50', 'p 50', 'هواوي p50'],
                    'mate30' => ['mate 30', 'mate30', 'ميت 30'],
                    'mate40' => ['mate 40', 'mate40', 'ميت 40'],
                    'mate50' => ['mate 50', 'mate50', 'ميت 50']
                ]
            ],
            'xiaomi' => [
                'patterns' => ['xia', 'mi', 'redmi', 'شاومي', 'ريدمي', 'xiaomi', 'poco', 'بوكو'],
                'models' => [
                    'mi11' => ['mi 11', 'mi11', 'شاومي 11'],
                    'mi12' => ['mi 12', 'mi12', 'شاومي 12'],
                    'redmi10' => ['redmi 10', 'redmi10', 'ريدمي 10'],
                    'redmi11' => ['redmi 11', 'redmi11', 'ريدمي 11'],
                    'poco' => ['poco', 'بوكو']
                ]
            ],
            'oppo' => [
                'patterns' => ['opp', 'reno', 'أوبو', 'اوبو', 'oppo'],
                'models' => [
                    'reno5' => ['reno 5', 'reno5', 'رينو 5'],
                    'reno6' => ['reno 6', 'reno6', 'رينو 6'],
                    'reno7' => ['reno 7', 'reno7', 'رينو 7'],
                    'find' => ['find', 'فايند']
                ]
            ],
            'vivo' => [
                'patterns' => ['viv', 'فيفو', 'vivo'],
                'models' => [
                    'v20' => ['v20', 'v 20', 'فيفو 20'],
                    'v21' => ['v21', 'v 21', 'فيفو 21'],
                    'v23' => ['v23', 'v 23', 'فيفو 23'],
                    'y20' => ['y20', 'y 20', 'واي 20']
                ]
            ],
            'oneplus' => [
                'patterns' => ['onep', 'ون بلس', 'oneplus'],
                'models' => [
                    '9' => ['oneplus 9', 'ون بلس 9'],
                    '10' => ['oneplus 10', 'ون بلس 10'],
                    '11' => ['oneplus 11', 'ون بلس 11']
                ]
            ],
            'lg' => [
                'patterns' => ['lg'],
                'models' => [
                    'g8' => ['g8', 'g 8'],
                    'v60' => ['v60', 'v 60']
                ]
            ],
            'sony' => [
                'patterns' => ['son', 'xperia', 'سوني', 'sony'],
                'models' => [
                    'xperia1' => ['xperia 1', 'xperia1', 'اكسبيريا 1'],
                    'xperia5' => ['xperia 5', 'xperia5', 'اكسبيريا 5'],
                    'xperia10' => ['xperia 10', 'xperia10', 'اكسبيريا 10']
                ]
            ],
            'realme' => [
                'patterns' => ['real', 'realme', 'ريلمي'],
                'models' => [
                    '9' => ['realme 9', 'ريلمي 9'],
                    '10' => ['realme 10', 'ريلمي 10']
                ]
            ],
            'infinix' => [
                'patterns' => ['inf', 'infinix', 'انفينكس'],
                'models' => [
                    'hot12' => ['hot 12', 'hot12', 'هوت 12'],
                    'note12' => ['note 12', 'note12', 'نوت 12']
                ]
            ],
            'tecno' => [
                'patterns' => ['tec', 'tecno', 'تكنو'],
                'models' => [
                    'camon18' => ['camon 18', 'كامون 18'],
                    'spark8' => ['spark 8', 'سبارك 8']
                ]
            ]
        ];
    }
    /**
     * قاموس أنواع الملحقات
     */
    function get_accessories_dictionary() {
        return [
            'screen' => [
                'patterns' => ['lcd', 'display', 'scr', 'شاشة', 'screen'],
                'description' => 'شاشات الاستبدال'
            ],
            'battery' => [
                'patterns' => ['batt', 'battery', 'bat', 'بطارية'],
                'description' => 'بطاريات الهواتف'
            ],
            'charger' => [
                'patterns' => ['chg', 'charger', 'شاحن'],
                'description' => 'شواحن وكابلات'
            ],
            'cable' => [
                'patterns' => ['cab', 'cable', 'كابل'],
                'description' => 'كابلات البيانات والشحن'
            ],
            'case' => [
                'patterns' => ['case', 'cover', 'جراب', 'غطاء'],
                'description' => 'جرابات الحماية'
            ],
            'protector' => [
                'patterns' => ['screen', 'protector', 'واقي'],
                'description' => 'واقيات الشاشة'
            ]
        ];
    }
    /**
     * دالة جديدة لتحليل اسم المنتج
     */
    public function analyze_product_name($product_name) {
        $product_name_lower = mb_strtolower($product_name);
        $brands_dict = $this->get_brands_dictionary();
        $accessories_dict = $this->get_accessories_dictionary();
        $result = [
            'brand' => null,
            'model' => null,
            'type' => null
        ];
        // البحث عن العلامة التجارية
        foreach ($brands_dict as $brand => $data) {
            foreach ($data['patterns'] as $pattern) {
                if (mb_strpos($product_name_lower, mb_strtolower($pattern)) !== false) {
                    $result['brand'] = $brand;
                    // البحث عن الموديل
                    foreach ($data['models'] as $model => $model_patterns) {
                        foreach ($model_patterns as $model_pattern) {
                            if (mb_strpos($product_name_lower, mb_strtolower($model_pattern)) !== false) {
                                $result['model'] = $model;
                                break 2;
                            }
                        }
                    }
                    break 2;
                }
            }
        }
        // البحث عن نوع الملحق
        foreach ($accessories_dict as $type => $data) {
            foreach ($data['patterns'] as $pattern) {
                if (mb_strpos($product_name_lower, mb_strtolower($pattern)) !== false) {
                    $result['type'] = $type;
                    break 2;
                }
            }
        }
        return $result;
    }
    // ==================================================================
    //  النهاية
    // ==================================================================
    function save_category(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k =>$v){
            if(!in_array($k,array('id','description'))){
                if(!empty($data)) $data .=",";
                $data .= " `{$k}`='{$v}' ";
            }
        }
        if(isset($_POST['description'])){
            if(!empty($data)) $data .=",";
                $data .= " `description`='".addslashes(htmlentities($description))."' ";
        }
        $check = $this->conn->query("SELECT * FROM `categories` where `category` = '{$category}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
        if($this->capture_err())
            return $this->capture_err();
        if($check > 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Category already exist.";
            return json_encode($resp);
            exit;
        }
        if(empty($id)){
            $sql = "INSERT INTO `categories` set {$data} ";
            $save = $this->conn->query($sql);
        }else{
            $sql = "UPDATE `categories` set {$data} where id = '{$id}' ";
            $save = $this->conn->query($sql);
        }
        if($save){
            $resp['status'] = 'success';
            if(empty($id))
                $this->settings->set_flashdata('success',"New Category successfully saved.");
            else
                $this->settings->set_flashdata('success',"Category successfully updated.");
        }else{
            $resp['status'] = 'failed';
            $resp['err'] = $this->conn->error."[{$sql}]";
        }
        return json_encode($resp);
    }
    function delete_category(){
        extract($_POST);
        $del = $this->conn->query("DELETE FROM `categories` where id = '{$id}'");
        if($del){
            $resp['status'] = 'success';
            $this->settings->set_flashdata('success',"Category successfully deleted.");
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }
    function save_brand(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k =>$v){
            if(!in_array($k,array('id','description'))){
                if(!empty($data)) $data .=",";
                $data .= " `{$k}`='{$v}' ";
            }
        }
        if(isset($_POST['description'])){
            if(!empty($data)) $data .=",";
                $data .= " `description`='".addslashes(htmlentities($description))."' ";
        }
        $check_query = $this->conn->query("SELECT * FROM `brands` where (`name` = '{$name}' OR `name_ar` = '{$name}' OR `name` = '{$name_ar}' OR `name_ar` = '{$name_ar}') ".((!empty($id) ? " and id != {$id} " : ""))."");
        if($this->capture_err())
            return $this->capture_err();
        
        $check = ($check_query && $check_query->num_rows > 0) ? $check_query->num_rows : 0;
        if($check > 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Brand already exist.";
            return json_encode($resp);
            exit;
        }
        if(empty($id)){
            $sql = "INSERT INTO `brands` set {$data} ";
            $save = $this->conn->query($sql);
        }else{
            $sql = "UPDATE `brands` set {$data} where id = '{$id}' ";
            $save = $this->conn->query($sql);
        }
        if($save){
            $resp['status'] = 'success';
            if(empty($id))
                $this->settings->set_flashdata('success',"New Brand successfully saved.");
            else
                $this->settings->set_flashdata('success',"Brand successfully updated.");
        }else{
            $resp['status'] = 'failed';
            $resp['err'] = $this->conn->error."[{$sql}]";
        }
        return json_encode($resp);
    }
    function delete_brand(){
        extract($_POST);
        $del = $this->conn->query("DELETE FROM `brands` where id = '{$id}'");
        if($del){
            $resp['status'] = 'success';
            $this->settings->set_flashdata('success',"Brand successfully deleted.");
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }
    function save_series(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k =>$v){
            if(!in_array($k,array('id','description'))){
                if(!empty($data)) $data .=",";
                $data .= " `{$k}`='{$v}' ";
            }
        }
        if(isset($_POST['description'])){
            if(!empty($data)) $data .=",";
                $data .= " `description`='".addslashes(htmlentities($description))."' ";
        }
        $check = $this->conn->query("SELECT * FROM `series` where `name` = '{$name}' and `brand_id` = '{$brand_id}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
        if($this->capture_err())
            return $this->capture_err();
        if($check > 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Series already exist.";
            return json_encode($resp);
            exit;
        }
        if(empty($id)){
            $sql = "INSERT INTO `series` set {$data} ";
            $save = $this->conn->query($sql);
        }else{
            $sql = "UPDATE `series` set {$data} where id = '{$id}' ";
            $save = $this->conn->query($sql);
        }
        if($save){
            $resp['status'] = 'success';
            if(empty($id))
                $this->settings->set_flashdata('success',"New Series successfully saved.");
            else
                $this->settings->set_flashdata('success',"Series successfully updated.");
        }else{
            $resp['status'] = 'failed';
            $resp['err'] = $this->conn->error."[{$sql}]";
        }
        return json_encode($resp);
    }
    function delete_series(){
        extract($_POST);
        $del = $this->conn->query("DELETE FROM `series` where id = '{$id}'");
        if($del){
            $resp['status'] = 'success';
            $this->settings->set_flashdata('success',"Series successfully deleted.");
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }
    function save_model(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k =>$v){
            if(!in_array($k,array('id','description'))){
                if(!empty($data)) $data .=",";
                $data .= " `{$k}`='{$v}' ";
            }
        }
        if(isset($_POST['description'])){
            if(!empty($data)) $data .=",";
                $data .= " `description`='".addslashes(htmlentities($description))."' ";
        }
        
        // Map series_id to sub_category_id for compatibility
        $sub_category_id = isset($series_id) ? $series_id : '';
        
        // Check if model already exists with same name and series_id
        $check_query = $this->conn->query("SELECT * FROM `models` where `name` = '{$name}' and `series_id` = '{$sub_category_id}' ".((!empty($id) ? " and id != {$id} " : ""))." ");
        if($this->capture_err())
            return $this->capture_err();
            
        $check = ($check_query && $check_query->num_rows > 0) ? $check_query->num_rows : 0;
        if($check > 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Model already exist.";
            return json_encode($resp);
            exit;
        }
        
        // Prepare data for models table
        $model_data = "";
        $model_data .= " `series_id`='{$sub_category_id}' ";
        $model_data .= ", `name`='{$name}' ";
        if(isset($_POST['description'])){
            $model_data .= ", `description`='".addslashes(htmlentities($description))."' ";
        }
        $model_data .= ", `status`='{$status}' ";
        
        if(empty($id)){
            $sql = "INSERT INTO `models` set {$model_data} ";
            $save = $this->conn->query($sql);
        }else{
            $sql = "UPDATE `models` set {$model_data} where id = '{$id}' ";
            $save = $this->conn->query($sql);
        }
        if($save){
            $resp['status'] = 'success';
            if(empty($id))
                $this->settings->set_flashdata('success',"New Model successfully saved.");
            else
                $this->settings->set_flashdata('success',"Model successfully updated.");
        }else{
            $resp['status'] = 'failed';
            $resp['err'] = $this->conn->error."[{$sql}]";
        }
        return json_encode($resp);
    }
    function delete_model(){
        extract($_POST);
        $del = $this->conn->query("DELETE FROM `models` where id = '{$id}'");
        if($del){
            $resp['status'] = 'success';
            $this->settings->set_flashdata('success',"Model successfully deleted.");
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }
    function save_sub_category(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k =>$v){
            if(!in_array($k,array('id','description'))){
                if(!empty($data)) $data .=",";
                $data .= " `{$k}`='{$v}' ";
            }
        }
        if(isset($_POST['description'])){
            if(!empty($data)) $data .=",";
                $data .= " `description`='".addslashes(htmlentities($description))."' ";
        }
        $check = $this->conn->query("SELECT * FROM `sub_categories` where `sub_category` = '{$sub_category}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
        if($this->capture_err())
            return $this->capture_err();
        if($check > 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Sub Category already exist.";
            return json_encode($resp);
            exit;
        }
        if(empty($id)){
            $sql = "INSERT INTO `sub_categories` set {$data} ";
            $save = $this->conn->query($sql);
        }else{
            $sql = "UPDATE `sub_categories` set {$data} where id = '{$id}' ";
            $save = $this->conn->query($sql);
        }
        if($save){
            $resp['status'] = 'success';
            if(empty($id))
                $this->settings->set_flashdata('success',"New Sub Category successfully saved.");
            else
                $this->settings->set_flashdata('success',"Sub Category successfully updated.");
        }else{
            $resp['status'] = 'failed';
            $resp['err'] = $this->conn->error."[{$sql}]";
        }
        return json_encode($resp);
    }
    function delete_sub_category(){
        extract($_POST);
        $del = $this->conn->query("DELETE FROM `sub_categories` where id = '{$id}'");
        if($del){
            $resp['status'] = 'success';
            $this->settings->set_flashdata('success',"Sub Category successfully deleted.");
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }
    function get_inventory(){
        extract($_POST);
        $qry = $this->conn->query("SELECT * FROM `inventory` where product_id = '{$product_id}' and size = '{$size}' LIMIT 1");
        if($qry->num_rows > 0){
            $row = $qry->fetch_assoc();
            $resp['success'] = true;
            $resp['inventory_id'] = $row['id'];
            $resp['price'] = $row['price'];
            $resp['quantity'] = $row['quantity'];
        }else{
            $resp['success'] = false;
            $resp['msg'] = 'المنتج غير متوفر';
        }
        return json_encode($resp);
    }
    
    function save_product_new(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k =>$v){
            if(!in_array($k,array('id'))){
                if(!empty($data)) $data .=",";
                $data .= " `{$k}`='{$v}' ";
            }
        }
        if(empty($id)){
            $sql = "INSERT INTO `products` set {$data} ";
            $save = $this->conn->query($sql);
        }else{
            $sql = "UPDATE `products` set {$data} where id = '{$id}' ";
            $save = $this->conn->query($sql);
        }
        if($save){
            $resp['status'] = 'success';
            if(empty($id))
                $this->settings->set_flashdata('success',"New Product successfully saved.");
            else
                $this->settings->set_flashdata('success',"Product successfully updated.");
        }else{
            $resp['status'] = 'failed';
            $resp['err'] = $this->conn->error."[{$sql}]";
        }
        return json_encode($resp);
    }
    function save_product(){
        extract($_POST);
        $data = "";
        
        // تحويل brand_id إلى category_id و series_id إلى sub_category_id
        if(isset($_POST['brand_id']) && !isset($_POST['category_id'])) {
            $_POST['category_id'] = $_POST['brand_id'];
        }
        
        if(isset($_POST['series_id']) && !isset($_POST['sub_category_id'])) {
            $_POST['sub_category_id'] = $_POST['series_id'];
        }
        
        // الحقول المسموح بها في جدول products
        $allowed_fields = ['category_id', 'sub_category_id', 'model_id', 'product_name', 'description', 'status', 'has_colors', 'colors', 'price', 'quantity', 'unit'];
        
        // بناء البيانات للحفظ
        foreach($_POST as $k => $v){
            if(in_array($k, $allowed_fields) && !empty($v)){
                if(!empty($data)) $data .= ",";
                if($k == 'description'){
                    $data .= " `{$k}`='".addslashes(htmlentities($v))."' ";
                } else {
                    $data .= " `{$k}`='{$v}' ";
                }
            }
        }
        
        // معالجة بيانات الألوان
        if(isset($_POST['has_colors']) && $_POST['has_colors'] == '1'){
            if(isset($_POST['color_names']) && isset($_POST['color_codes'])){
                $colors = [];
                for($i = 0; $i < count($_POST['color_names']); $i++){
                    if(!empty($_POST['color_names'][$i])){
                        $colors[] = [
                            'name' => $_POST['color_names'][$i],
                            'code' => $_POST['color_codes'][$i]
                        ];
                    }
                }
                if(!empty($colors)){
                    $colors_json = json_encode($colors, JSON_UNESCAPED_UNICODE);
                    if(!empty($data)) $data .= ",";
                    $data .= " `colors`='" . addslashes($colors_json) . "' ";
                }
            }
        }
        
        // بناء استعلام الإدراج أو التحديث
        if(empty($id)){
            $sql = "INSERT INTO `products` set {$data}";
            $save = $this->conn->query($sql);
            if($save){
                $product_id = $this->conn->insert_id;
                
                // إضافة بيانات المخزون إذا كانت موجودة
                if(!empty($_POST['price']) || !empty($_POST['quantity'])){
                    // إزالة الفواصل من السعر قبل التحويل
                    $price_clean = !empty($_POST['price']) ? str_replace(',', '', $_POST['price']) : 0;
                    $price = floatval($price_clean);
                    $quantity = !empty($_POST['quantity']) ? intval($_POST['quantity']) : 0;
                    
                    $inventory_sql = "INSERT INTO `inventory` (product_id, price, quantity) VALUES ('{$product_id}', '{$price}', '{$quantity}')";
                    $this->conn->query($inventory_sql);
                }
            }
        }else{
            $sql = "UPDATE `products` set {$data} where id = '{$id}'";
            $save = $this->conn->query($sql);
            
            if($save){
                // تحديث بيانات المخزون
                if(!empty($_POST['price']) || !empty($_POST['quantity'])){
                    // إزالة الفواصل من السعر قبل التحويل
                    $price_clean = !empty($_POST['price']) ? str_replace(',', '', $_POST['price']) : 0;
                    $price = floatval($price_clean);
                    $quantity = !empty($_POST['quantity']) ? intval($_POST['quantity']) : 0;
                    
                    // التحقق من وجود سجل في جدول inventory
                    $check_inventory = $this->conn->query("SELECT id FROM `inventory` WHERE product_id = '{$id}'");
                    if($check_inventory && $check_inventory->num_rows > 0){
                        $inventory_sql = "UPDATE `inventory` SET price = '{$price}', quantity = '{$quantity}' WHERE product_id = '{$id}'";
                    }else{
                        $inventory_sql = "INSERT INTO `inventory` (product_id, price, quantity) VALUES ('{$id}', '{$price}', '{$quantity}')";
                    }
                    $this->conn->query($inventory_sql);
                }
            }
        }
        error_log("Save Product SQL: " . $sql);
        error_log("MySQL Error: " . $this->conn->error);
        
        if($save){
            $resp['status'] = 'success';
            $pid = !empty($id) ? $id : $this->conn->insert_id;
            
            // معالجة رفع الصور
            if(isset($_FILES['img']) && !empty($_FILES['img']['tmp_name'][0])){
                $upload_path = base_app . 'uploads/product_' . $pid;
                
                // إنشاء المجلد إذا لم يكن موجوداً
                if(!is_dir($upload_path)){
                    mkdir($upload_path, 0755, true);
                }
                
                // معالجة كل صورة
                foreach($_FILES['img']['tmp_name'] as $key => $tmp_name){
                    if(!empty($tmp_name)){
                        $file_name = $_FILES['img']['name'][$key];
                        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        
                        if(in_array($file_ext, $allowed_ext)){
                            // إنشاء اسم ملف فريد
                            $new_name = 'img_' . time() . '_' . $key . '.' . $file_ext;
                            $upload_file = $upload_path . '/' . $new_name;
                            
                            // رفع الملف
                            if(move_uploaded_file($tmp_name, $upload_file)){
                                // تسجيل نجاح الرفع
                                error_log("Image uploaded successfully: " . $upload_file);
                            } else {
                                error_log("Failed to upload image: " . $file_name);
                            }
                        } else {
                            error_log("Invalid file extension for: " . $file_name);
                        }
                    }
                }
            }
            
            if(empty($id))
                $this->settings->set_flashdata('success',"New Product successfully saved.");
            else
                $this->settings->set_flashdata('success',"Product successfully updated.");
        }else{
            $resp['status'] = 'failed';
            $resp['err'] = $this->conn->error."[{$sql}]";
            $resp['sql'] = $sql; // إضافة SQL للتصحيح
        }
        return json_encode($resp);
    }
    
    function delete_product(){
        extract($_POST);
        $del = $this->conn->query("DELETE FROM `products` where id = '{$id}'");
        if($del){
            $resp['status'] = 'success';
            $this->settings->set_flashdata('success',"Product successfully deleted.");
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }
    function delete_img(){
        extract($_POST);
        if(is_file($path)){
            if(unlink($path)){
                $resp['status'] = 'success';
            }else{
                $resp['status'] = 'failed';
                $resp['error'] = 'failed to delete '.$path;
            }
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = 'Unkown '.$path.' path';
        }
        return json_encode($resp);
    }
    function save_inventory(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k =>$v){
            if(!in_array($k,array('id','description'))){
                if(!empty($data)) $data .=",";
                $data .= " `{$k}`='{$v}' ";
            }
        }
        $check = $this->conn->query("SELECT * FROM `inventory` where `product_id` = '{$product_id}' and `size` = '{$size}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
        if($this->capture_err())
            return $this->capture_err();
        if($check > 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Inventory already exist.";
            return json_encode($resp);
            exit;
        }
        if(empty($id)){
            $sql = "INSERT INTO `inventory` set {$data} ";
            $save = $this->conn->query($sql);
        }else{
            $sql = "UPDATE `inventory` set {$data} where id = '{$id}' ";
            $save = $this->conn->query($sql);
        }
        if($save){
            $resp['status'] = 'success';
            if(empty($id))
                $this->settings->set_flashdata('success',"New Invenory successfully saved.");
            else
                $this->settings->set_flashdata('success',"Invenory successfully updated.");
        }else{
            $resp['status'] = 'failed';
            $resp['err'] = $this->conn->error."[{$sql}]";
        }
        return json_encode($resp);
    }
    function delete_inventory(){
        extract($_POST);
        $del = $this->conn->query("DELETE FROM `inventory` where id = '{$id}'");
        if($del){
            $resp['status'] = 'success';
            $this->settings->set_flashdata('success',"Invenory successfully deleted.");
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }
    function register(){
        extract($_POST);
        $data = "";
        $_POST['password'] = md5($_POST['password']);
        foreach($_POST as $k =>$v){
            if(!in_array($k,array('id'))){
                if(!empty($data)) $data .=",";
                $data .= " `{$k}`='{$v}' ";
            }
        }
        $check = $this->conn->query("SELECT * FROM `clients` where `email` = '{$email}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
        if($this->capture_err())
            return $this->capture_err();
        if($check > 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Email already taken.";
            return json_encode($resp);
            exit;
        }
        if(empty($id)){
            $sql = "INSERT INTO `clients` set {$data} ";
            $save = $this->conn->query($sql);
            $id = $this->conn->insert_id;
        }else{
            $sql = "UPDATE `clients` set {$data} where id = '{$id}' ";
            $save = $this->conn->query($sql);
        }
        if($save){
            $resp['status'] = 'success';
            if(empty($id))
                $this->settings->set_flashdata('success',"Account successfully created.");
            else
                $this->settings->set_flashdata('success',"Account successfully updated.");
            foreach($_POST as $k =>$v){
                    $this->settings->set_userdata($k,$v);
            }
            $this->settings->set_userdata('id',$id);
        }else{
            $resp['status'] = 'failed';
            $resp['err'] = $this->conn->error."[{$sql}]";
        }
        return json_encode($resp);
    }
    // Ensure `cart` table supports guest carts using session_id and nullable client_id
    private function ensure_cart_session_support(){
        $checkSessionIdCol = $this->conn->query("SHOW COLUMNS FROM `cart` LIKE 'session_id'");
        if(!$checkSessionIdCol || $checkSessionIdCol->num_rows == 0){
            $this->conn->query("ALTER TABLE `cart` ADD COLUMN `session_id` varchar(255) NULL AFTER `client_id`");
        }
        $checkClientIdCol = $this->conn->query("SHOW COLUMNS FROM `cart` LIKE 'client_id'");
        if($checkClientIdCol && $checkClientIdCol->num_rows > 0){
            $col = $checkClientIdCol->fetch_assoc();
            if(isset($col['Null']) && strtoupper($col['Null']) === 'NO'){
                $this->conn->query("ALTER TABLE `cart` MODIFY `client_id` int(30) NULL");
            }
        }
    }
    function get_cart_count(){
        $this->ensure_cart_session_support();
        if($this->settings->userdata('id') > 0){
            $count = $this->conn->query("SELECT SUM(quantity) as items from `cart` where client_id = ".$this->settings->userdata('id'))->fetch_assoc()['items'];
            $resp['count'] = $count > 0 ? $count : 0;
        }else{
            if(!session_id()){
                session_start();
            }
            $count = $this->conn->query("SELECT SUM(quantity) as items from `cart` where client_id IS NULL AND session_id='". session_id() ."'")->fetch_assoc()['items'];
            $resp['count'] = $count > 0 ? $count : 0;
        }
        return json_encode($resp);
    }
    function add_to_cart(){
        $this->ensure_cart_session_support();
        extract($_POST);
        $client_id = $this->settings->userdata('id');
        if(!empty($client_id)){
            $data = " client_id = '{$client_id}' ";
            $client_condition = "client_id = {$client_id}";
        } else {
            if(!session_id()){
                session_start();
            }
            $data = " session_id = '".session_id()."' ";
            $client_condition = "session_id = '".session_id()."' AND client_id IS NULL";
        }
        $_POST['price'] = str_replace(",","",$_POST['price']);
        foreach($_POST as $k =>$v){
            if(!in_array($k,array('id'))){
                if(!empty($data)) $data .=",";
                $data .= " `{$k}`='{$v}' ";
            }
        }
        $check = $this->conn->query("SELECT * FROM `cart` where `inventory_id` = '{$inventory_id}' and {$client_condition}")->num_rows;
        if($this->capture_err())
            return $this->capture_err();
        if($check > 0){
            $sql = "UPDATE `cart` set quantity = quantity + {$quantity} where `inventory_id` = '{$inventory_id}' and {$client_condition}";
        }else{
            $sql = "INSERT INTO `cart` set {$data} ";
        }
        $save = $this->conn->query($sql);
        if($this->capture_err())
            return $this->capture_err();
            if($save){
                $resp['status'] = 'success';
                $resp['cart_count'] = $this->conn->query("SELECT SUM(quantity) as items from `cart` where {$client_condition}")->fetch_assoc()['items'];
            }else{
                $resp['status'] = 'failed';
                $resp['err'] = $this->conn->error."[{$sql}]";
            }
            return json_encode($resp);
    }
    function update_cart_qty(){
        extract($_POST);
        $save = $this->conn->query("UPDATE `cart` set quantity = '{$quantity}' where id = '{$id}'");
        if($this->capture_err())
            return $this->capture_err();
        if($save){
            $resp['status'] = 'success';
        }else{
            $resp['status'] = 'failed';
            $resp['err'] = $this->conn->error."[{$sql}]";
        }
        return json_encode($resp);
    }
    function empty_cart(){
        $this->ensure_cart_session_support();
        $client_id = $this->settings->userdata('id');
        if(!empty($client_id)){
            $delete = $this->conn->query("DELETE FROM `cart` where client_id = '{$client_id}'");
        } else {
            if(!session_id()){
                session_start();
            }
            $delete = $this->conn->query("DELETE FROM `cart` where client_id IS NULL AND session_id='". session_id() ."'");
        }
        if($this->capture_err())
            return $this->capture_err();
        if($delete){
            $resp['status'] = 'success';
        }else{
            $resp['status'] = 'failed';
            $resp['err'] = $this->conn->error;
        }
        return json_encode($resp);
    }
    function delete_cart(){
        extract($_POST);
        $delete = $this->conn->query("DELETE FROM `cart` where id = '{$id}'");
        if($this->capture_err())
            return $this->capture_err();
        if($delete){
            $resp['status'] = 'success';
        }else{
            $resp['status'] = 'failed';
            $resp['err'] = $this->conn->error;
        }
        return json_encode($resp);
    }
    function delete_order(){
        extract($_POST);
        $delete = $this->conn->query("DELETE FROM `orders` where id = '{$id}'");
        $delete2 = $this->conn->query("DELETE FROM `order_list` where order_id = '{$id}'");
        $delete3 = $this->conn->query("DELETE FROM `sales` where order_id = '{$id}'");
        if($this->capture_err())
            return $this->capture_err();
        if($delete){
            $resp['status'] = 'success';
            $this->settings->set_flashdata('success',"Order successfully deleted");
        }else{
            $resp['status'] = 'failed';
            $resp['err'] = $this->conn->error;
        }
        return json_encode($resp);
    }
    function place_order(){
        extract($_POST);
        $client_id = $this->settings->userdata('id');
        if(empty($client_id)){
            // Handle guest checkout - create a new client record
            $client_data = " firstname = '{$customer_name}' ";
            $client_data .= " ,contact = '{$customer_phone}' ";
            $client_data .= " ,default_delivery_address = '{$delivery_address}' ";
            $client_sql = "INSERT INTO `clients` set $client_data";
            $save_client = $this->conn->query($client_sql);
            if($this->capture_err())
                return $this->capture_err();
            // Get the ID of the newly created client
            $client_id = $this->conn->insert_id;
        }
        // Now create the order with the correct client_id
        $data = " client_id = '{$client_id}' ";
        $data .= " ,payment_method = '{$payment_method}' ";
        $data .= " ,amount = '{$amount}' ";
        $data .= " ,paid = '{$paid}' ";
        $data .= " ,delivery_address = '{$delivery_address}' ";
        $order_sql = "INSERT INTO `orders` set $data";
        $save_order = $this->conn->query($order_sql);
        if($this->capture_err())
            return $this->capture_err();
        if($save_order){
            $order_id = $this->conn->insert_id;
            $data = '';
            $cart_condition = "c.client_id ='{$client_id}'";
            if(empty($this->settings->userdata('id'))){
                $cart_condition = "c.client_id IS NULL AND c.session_id='" . session_id() . "'";
            }
            $cart = $this->conn->query("SELECT c.*,p.product_name,i.size,i.price,p.id as pid,i.unit from `cart` c inner join `inventory` i on i.id=c.inventory_id inner join products p on p.id = i.product_id where $cart_condition");
            while($row= $cart->fetch_assoc()):
                if(!empty($data)) $data .= ", ";
                $total = $row['price'] * $row['quantity'];
                $data .= "('{$order_id}','{$row['pid']}','{$row['size']}','{$row['unit']}','{$row['quantity']}','{$row['price']}', $total)";
            endwhile;
            if(!empty($data)){
                $list_sql = "INSERT INTO `order_list` (order_id,product_id,size,unit,quantity,price,total) VALUES {$data} ";
                $save_olist = $this->conn->query($list_sql);
                if($this->capture_err())
                    return $this->capture_err();
                if($save_olist){
                    if(empty($this->settings->userdata('id'))){
                        $empty_cart = $this->conn->query("DELETE FROM `cart` where client_id IS NULL AND session_id='" . session_id() . "'");
                    } else {
                        $empty_cart = $this->conn->query("DELETE FROM `cart` where client_id = '{$client_id}'");
                    }
                    $data = " order_id = '{$order_id}'";
                    $data .= " ,total_amount = '{$amount}'";
                    $save_sales = $this->conn->query("INSERT INTO `sales` set $data");
                    if($this->capture_err())
                        return $this->capture_err();
                    $resp['status'] ='success';
                }else{
                    $resp['status'] ='failed';
                    $resp['err_sql'] =$save_olist;
                }
            } else {
                $resp['status'] ='failed';
                $resp['error'] ='No items in cart';
            }
        }else{
            $resp['status'] ='failed';
            $resp['err_sql'] =$save_order;
        }
        return json_encode($resp);
    }
    function update_order_status(){
        extract($_POST);
        $update = $this->conn->query("UPDATE `orders` set `status` = '$status' where id = '{$id}' ");
        if($update){
            $resp['status'] ='success';
            $this->settings->set_flashdata("success"," Order status successfully updated.");
        }else{
            $resp['status'] ='failed';
            $resp['err'] =$this->conn->error;
        }
        return json_encode($resp);
    }
    function pay_order(){
        extract($_POST);
        $update = $this->conn->query("UPDATE `orders` set `paid` = '1' where id = '{$id}' ");
        if($update){
            $resp['status'] ='success';
            $this->settings->set_flashdata("success"," Order payment status successfully updated.");
        }else{
            $resp['status'] ='failed';
            $resp['err'] =$this->conn->error;
        }
        return json_encode($resp);
    }function update_account(){
        extract($_POST);
        $data = "";
        if(!empty($password)){
            $_POST['password'] = md5($password);
            if(md5($cpassword) != $this->settings->userdata('password')){
                $resp['status'] = 'failed';
                $resp['msg'] = "Current Password is Incorrect";
                return json_encode($resp);
                exit;
            }
        }
        $check = $this->conn->query("SELECT * FROM `clients`  where `email`='{$email}' and `id` != $id ")->num_rows;
        if($check > 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Email already taken.";
            return json_encode($resp);
            exit;
        }
        foreach($_POST as $k =>$v){
            if($k == 'cpassword' || ($k == 'password' && empty($v)))
                continue;
                if(!empty($data)) $data .=",";
                    $data .= " `{$k}`='{$v}' ";
        }
        $save = $this->conn->query("UPDATE `clients` set $data where id = $id ");
        if($save){
            foreach($_POST as $k =>$v){
                if($k != 'cpassword')
                $this->settings->set_userdata($k,$v);
            }
            $this->settings->set_userdata('id',$this->conn->insert_id);
            $resp['status'] = 'success';
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }
    function login(){
        extract($_POST);
        $qry = $this->conn->query("SELECT * FROM clients where email = '$email' and password = '".md5($password)."' ");
        if($this->capture_err())
            return $this->capture_err();
        if($qry->num_rows > 0){
            $data = $qry->fetch_array();
            foreach($data as $k => $v){
                if(!is_numeric($k) && $k != 'password')
                    $this->settings->set_userdata($k,$v);
            }
            $this->settings->set_userdata('login_type',1);
            $resp['status'] = 'success';
        }else{
            $resp['status'] = 'incorrect';
            $resp['error'] = 'Incorrect Username or Password.';
        }
        if($this->capture_err())
            return $this->capture_err();
        return json_encode($resp);
    }
    function logout(){
        if($this->settings->sess_des()){
            redirect('admin/login.php');
        }
    }
    function logout_user(){
        if($this->settings->sess_des()){
            redirect('./');
        }
    }
} // إغلاق class Master

$Master = new Master();

// Process actions if this file is called directly or via AJAX request
if (basename($_SERVER['PHP_SELF']) == 'Master.php' || (isset($_GET['f']) && !empty($_GET['f']))) {
	$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
	$sysset = new SystemSettings();
	switch ($action) {
		case 'save_category':
			echo $Master->save_category();
		break;
		case 'delete_category':
			echo $Master->delete_category();
		break;
		case 'save_brand':
			echo $Master->save_brand();
		break;
		case 'delete_brand':
			echo $Master->delete_brand();
		break;
		case 'save_series':
			echo $Master->save_series();
		break;
		case 'delete_series':
			echo $Master->delete_series();
		break;
		case 'save_model':
			echo $Master->save_model();
		break;
		case 'delete_model':
			echo $Master->delete_model();
		break;
		case 'save_sub_category':
			echo $Master->save_sub_category();
		break;
		case 'delete_sub_category':
			echo $Master->delete_sub_category();
		break;
		case 'save_product':
			echo $Master->save_product();
		break;
		case 'save_product_new':
			echo $Master->save_product_new();
		break;
		case 'delete_product':
			echo $Master->delete_product();
		break;
		case 'delete_img':
			echo $Master->delete_img();
		break;
		case 'save_inventory':
			echo $Master->save_inventory();
		break;
		case 'delete_inventory':
			echo $Master->delete_inventory();
		break;
		case 'register':
			echo $Master->register();
		break;
		case 'get_inventory':
			echo $Master->get_inventory();
		break;
		case 'get_cart_count':
			echo $Master->get_cart_count();
		break;
		case 'add_to_cart':
			echo $Master->add_to_cart();
		break;
		case 'update_cart_qty':
			echo $Master->update_cart_qty();
		break;
		case 'empty_cart':
			echo $Master->empty_cart();
		break;
		case 'delete_cart':
			echo $Master->delete_cart();
		break;
		case 'delete_order':
			echo $Master->delete_order();
		break;
		case 'place_order':
			echo $Master->place_order();
		break;
		case 'update_order_status':
			echo $Master->update_order_status();
		break;
		case 'pay_order':
			echo $Master->pay_order();
		break;
		case 'update_account':
			echo $Master->update_account();
		break;
		case 'login':
			echo $Master->login();
		break;
		case 'logout':
			echo $Master->logout();
		break;
		case 'logout_user':
			echo $Master->logout_user();
		break;
		default:
			// echo $sysset->index();
			break;
	}
}

// Handle direct function calls
if (isset($_GET['f'])) {
    $master = new Master();
    switch ($_GET['f']) {
        case 'get_series_by_brand':
            $master->get_series_by_brand();
            break;
        case 'get_models_by_series':
            $master->get_models_by_series();
            break;
    }
}
