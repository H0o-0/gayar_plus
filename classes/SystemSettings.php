<?php
// Use a more robust path resolution
$root_path = dirname(__DIR__);

// Check if DB_NAME is already defined (meaning config.php was already included)
if (!defined('DB_NAME')) {
    // Use absolute path to ensure it works from any context
    $config_path = $root_path . '/config.php';
    if (file_exists($config_path)) {
        require_once $config_path;
    } else {
        // Fallback to relative path if absolute doesn't work
        require_once $root_path . '/config.php';
    }
}

// Only include DBConnection if the class doesn't exist
if(!class_exists('DBConnection')){
    // Use the same robust path approach
    $db_connection_path = $root_path . '/classes/DBConnection.php';
    if (file_exists($db_connection_path)) {
        require_once $db_connection_path;
    } else {
        // Fallback to relative path if absolute doesn't work
        require_once 'DBConnection.php';
    }
}

class SystemSettings extends DBConnection{
    private $root_path;
    
    public function __construct(){
        global $root_path;
        $this->root_path = $root_path;
        parent::__construct();
    }
    
    function check_connection(){
        return($this->conn);
    }
    
    function load_system_info(){
        // if(!isset($_SESSION['system_info'])){
            $sql = "SELECT * FROM system_info";
            $qry = $this->conn->query($sql);
                while($row = $qry->fetch_assoc()){
                    $_SESSION['system_info'][$row['meta_field']] = $row['meta_value'];
                }
        // }
    }
    
    function update_system_info(){
        $sql = "SELECT * FROM system_info";
        $qry = $this->conn->query($sql);
            while($row = $qry->fetch_assoc()){
                if(isset($_SESSION['system_info'][$row['meta_field']]))unset($_SESSION['system_info'][$row['meta_field']]);
                $_SESSION['system_info'][$row['meta_field']] = $row['meta_value'];
            }
        return true;
    }
    
    function update_settings_info(){
        $data = "";
        foreach ($_POST as $key => $value) {
            if(!in_array($key,array("about_us","privacy_policy")))
            if(isset($_SESSION['system_info'][$key])){
                $value = str_replace("'", "&apos;", $value);
                $qry = $this->conn->query("UPDATE system_info set meta_value = '{$value}' where meta_field = '{$key}' ");
            }else{
                $qry = $this->conn->query("INSERT into system_info set meta_value = '{$value}', meta_field = '{$key}' ");
            }
        }
        if(isset($_POST['about_us'])){
            file_put_contents($this->root_path . '/about.html',$_POST['about_us']);
        }
        if(isset($_POST['privacy_policy'])){
            file_put_contents($this->root_path . '/privacy_policy.html',$_POST['privacy_policy']);
        }
        if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
            $fname = 'uploads/'.strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
            $move = move_uploaded_file($_FILES['img']['tmp_name'], $this->root_path . '/' . $fname);
            if(isset($_SESSION['system_info']['logo'])){
                $qry = $this->conn->query("UPDATE system_info set meta_value = '{$fname}' where meta_field = 'logo' ");
                if(is_file($this->root_path . '/' . $_SESSION['system_info']['logo'])) unlink($this->root_path . '/' . $_SESSION['system_info']['logo']);
            }else{
                $qry = $this->conn->query("INSERT into system_info set meta_value = '{$fname}',meta_field = 'logo' ");
            }
        }
        if(isset($_FILES['cover']) && $_FILES['cover']['tmp_name'] != ''){
            $fname = 'uploads/'.strtotime(date('y-m-d H:i')).'_'.$_FILES['cover']['name'];
            $move = move_uploaded_file($_FILES['cover']['tmp_name'], $this->root_path . '/' . $fname);
            if(isset($_SESSION['system_info']['cover'])){
                $qry = $this->conn->query("UPDATE system_info set meta_value = '{$fname}' where meta_field = 'cover' ");
                if(is_file($this->root_path . '/' . $_SESSION['system_info']['cover'])) unlink($this->root_path . '/' . $_SESSION['system_info']['cover']);
            }else{
                $qry = $this->conn->query("INSERT into system_info set meta_value = '{$fname}',meta_field = 'cover' ");
            }
        }
        
        $update = $this->update_system_info();
        $flash = $this->set_flashdata('success','System Info Successfully Updated.');
        if($update && $flash){
            // var_dump($_SESSION);
            return true;
        }
    }
    
    function set_userdata($field='',$value=''){
        if(!empty($field) && !empty($value)){
            $_SESSION['userdata'][$field]= $value;
        }
    }
    
    function userdata($field = ''){
        if(!empty($field)){
            if(isset($_SESSION['userdata'][$field]))
                return $_SESSION['userdata'][$field];
            else
                return null;
        }else{
            return false;
        }
    }
    
    function set_flashdata($flash='',$value=''){
        if(!empty($flash) && !empty($value)){
            $_SESSION['flashdata'][$flash]= $value;
        return true;
        }
    }
    
    function chk_flashdata($flash = ''){
        if(isset($_SESSION['flashdata'][$flash])){
            return true;
        }else{
            return false;
        }
    }
    
    function flashdata($flash = ''){
        if(!empty($flash)){
            $_tmp = $_SESSION['flashdata'][$flash];
            unset($_SESSION['flashdata']);
            return $_tmp;
        }else{
            return false;
        }
    }
    
    function sess_des(){
        if(isset($_SESSION['userdata'])){
                unset($_SESSION['userdata']);
            return true;
        }
            return true;
    }
    
    function info($field=''){
        if(!empty($field)){
            if(isset($_SESSION['system_info'][$field]))
                return $_SESSION['system_info'][$field];
            else
                return false;
        }else{
            return false;
        }
    }
    
    function set_info($field='',$value=''){
        if(!empty($field) && !empty($value)){
            $_SESSION['system_info'][$field] = $value;
        }
    }
}

$_settings = new SystemSettings();
$_settings->load_system_info();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
    case 'update_settings':
        echo $sysset->update_settings_info();
        break;
    default:
        // echo $sysset->index();
        break;
}
?>