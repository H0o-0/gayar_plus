<?php
class TextCleaner {
    
    /**
     * Clean and sanitize text for safe display in descriptions
     * @param string $text The text to clean
     * @return string Clean and safe text
     */
    public static function sanitizeForDescription($text) {
        if (empty($text)) {
<<<<<<< HEAD
            return 'وصف غير متوفر';
        }
        
        // First decode any HTML entities
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        
        // Remove all style attributes (including massive Tailwind CSS)
        $text = preg_replace('/style\s*=\s*["\'][^"\'>]*["\']/', '', $text);
        
        // Remove all data attributes
        $text = preg_replace('/data-[a-zA-Z0-9\-_]+\s*=\s*["\'][^"\'>]*["\']/', '', $text);
        
        // Remove all class attributes with CSS framework classes
        $text = preg_replace('/class\s*=\s*["\'][^"\'>]*["\']/', '', $text);
        
        // Remove PHP error messages and notices
        $text = preg_replace('/\(\s*!\s*\)\s*Notice:.*?Call Stack.*?php:\d+/s', '', $text);
        $text = preg_replace('/Notice:.*?on line.*?\d+/i', '', $text);
        $text = preg_replace('/Warning:.*?on line.*?\d+/i', '', $text);
        $text = preg_replace('/Fatal error:.*?on line.*?\d+/i', '', $text);
        
        // Remove all HTML tags except basic formatting
        $allowed_tags = '<p><br><strong><b><em><i><u>';
        $text = strip_tags($text, $allowed_tags);
        
        // Clean up extra whitespace and line breaks
        $text = preg_replace('/\s+/', ' ', $text);
        $text = preg_replace('/(<br\s*\/?\s*>\s*){3,}/', '<br><br>', $text);
        $text = trim($text);
        
        // If text is still too messy or contains only symbols, provide a clean fallback
        if (strlen($text) < 10 || preg_match('/^[\s\W]*$/', strip_tags($text))) {
            return 'منتج عالي الجودة مناسب لجميع الاستخدامات';
        }
        
        // Limit length for display
        if (strlen($text) > 300) {
            $text = substr($text, 0, 300) . '...';
        }
=======
            return '';
        }
        
        // Remove potentially dangerous HTML tags
        $allowed_tags = '<p><br><br/><strong><b><em><i><u><ul><ol><li><h1><h2><h3><h4><h5><h6><span><div>';
        $text = strip_tags($text, $allowed_tags);
        
        // Clean up extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        // Convert special characters to HTML entities for safety
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8', false);
        
        // Convert back some allowed HTML tags
        $text = str_replace('&lt;br&gt;', '<br>', $text);
        $text = str_replace('&lt;br/&gt;', '<br/>', $text);
        $text = str_replace('&lt;p&gt;', '<p>', $text);
        $text = str_replace('&lt;/p&gt;', '</p>', $text);
        $text = str_replace('&lt;strong&gt;', '<strong>', $text);
        $text = str_replace('&lt;/strong&gt;', '</strong>', $text);
        $text = str_replace('&lt;b&gt;', '<b>', $text);
        $text = str_replace('&lt;/b&gt;', '</b>', $text);
        $text = str_replace('&lt;em&gt;', '<em>', $text);
        $text = str_replace('&lt;/em&gt;', '</em>', $text);
        $text = str_replace('&lt;i&gt;', '<i>', $text);
        $text = str_replace('&lt;/i&gt;', '</i>', $text);
>>>>>>> cebc63a3bc4f7e2f5ae4119daff21338fea35eb8
        
        return $text;
    }
    
    /**
     * Clean and truncate text for display
     * @param string $text The text to clean
     * @param int $length Maximum length
     * @return string Clean and truncated text
     */
    public static function cleanAndTruncate($text, $length = 150) {
        if (empty($text)) {
            return '';
        }
        
        // First decode any existing HTML entities to get the raw text
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        
        // Remove HTML tags
        $text = strip_tags($text);
        
        // Clean up whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        // Truncate if necessary
        if (strlen($text) > $length) {
            $text = substr($text, 0, $length) . '...';
        }
        
        // Escape for safe output
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Ultra clean and truncate text for display (for homepage cards)
     * @param string $text The text to clean
     * @param int $length Maximum length
     * @return string Clean and truncated text
     */
    public static function cleanAndTruncateUltra($text, $length = 100) {
        if (empty($text)) {
            return 'وصف غير متوفر';
        }
        
        // First decode any existing HTML entities to get the raw text
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        
        // Remove HTML tags completely
        $text = strip_tags($text);
        
        // Remove extra whitespace and line breaks
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        // Remove any remaining special characters that might cause issues
        $text = preg_replace('/[^\p{L}\p{N}\s\.\,\!\?\-\(\)]/u', '', $text);
        
        // Truncate if necessary
        if (strlen($text) > $length) {
            $text = substr($text, 0, $length) . '...';
        }
        
        // Escape for safe output
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Clean text for search purposes
     * @param string $text The text to clean
     * @return string Clean text for search
     */
    public static function cleanForSearch($text) {
        if (empty($text)) {
            return '';
        }
        
        // Remove HTML tags
        $text = strip_tags($text);
        
        // Convert to lowercase for search
        $text = mb_strtolower($text, 'UTF-8');
        
        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        return $text;
    }
    
    /**
     * Validate and clean URL parameters
     * @param string $param The parameter to clean
     * @return string Clean parameter
     */
    public static function cleanUrlParam($param) {
        // Remove any non-alphanumeric characters except hyphens and underscores
        return preg_replace('/[^a-zA-Z0-9\-_]/', '', $param);
    }
    
    /**
     * Clean and format price display
     * @param mixed $price The price to format
     * @return string Formatted price
     */
    public static function formatPrice($price) {
        // Check if price is empty, not set, or not numeric
        if (empty($price) || !is_numeric($price) || $price <= 0) {
            return 'السعر عند الطلب';
        }
        
        // Format the price as a number with commas
        return number_format(floatval($price)) . ' د.ع';
    }
    
    /**
<<<<<<< HEAD
     * Ultra aggressive cleaning for severely corrupted content
     * @param string $text The text to clean
     * @return string Clean and safe text
     */
    public static function ultraClean($text) {
        if (empty($text)) {
            return 'وصف غير متوفر';
        }
        
        // Check if content contains massive style attributes or errors
        if (strpos($text, '--tw-') !== false || 
            strpos($text, 'Notice:') !== false || 
            strpos($text, 'Call Stack') !== false ||
            strlen($text) > 1000) {
            return 'منتج عالي الجودة مع ضمان شامل وخدمة عملاء ممتازة';
        }
        
        // Remove all HTML
        $text = strip_tags($text);
        
        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        // If still too short or messy, use fallback
        if (strlen($text) < 20) {
            return 'منتج عالي الجودة';
        }
        
        return $text;
    }

    /**
=======
>>>>>>> cebc63a3bc4f7e2f5ae4119daff21338fea35eb8
     * General purpose sanitization method
     * @param string $text The text to sanitize
     * @return string Sanitized text
     */
    public static function sanitize($text) {
        if (empty($text)) {
            return '';
        }
        
        // Remove HTML tags
        $text = strip_tags($text);
        
        // Clean up whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        // Escape for safe output
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}
?>