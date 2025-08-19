<?php
/**
 * Text Cleaner Class
 * يقوم بتنظيف النص من HTML tags والخصائص غير المرغوب فيها
 */
class TextCleaner {
    
    /**
     * تنظيف شامل للنص من جميع HTML tags والخصائص
     * @param string $text النص المراد تنظيفه
     * @param bool $preserve_line_breaks الحفاظ على الأسطر الجديدة
     * @return string النص المنظف
     */
    public static function cleanText($text, $preserve_line_breaks = false) {
        if(empty($text)) {
            return '';
        }
        
        // فك تشفير HTML entities أولاً
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // فك تشفير HTML entities مرة أخرى في حالة التشفير المزدوج
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // إزالة جميع خصائص style
        $text = preg_replace('/\s*style\s*=\s*"[^"]*"/i', '', $text);
        
        // إزالة جميع خصائص data-huuid
        $text = preg_replace('/\s*data-huuid\s*=\s*"[^"]*"/i', '', $text);
        
        // إزالة جميع خصائص data-*
        $text = preg_replace('/\s*data-[^=]*\s*=\s*"[^"]*"/i', '', $text);
        
        // إزالة جميع خصائص class
        $text = preg_replace('/\s*class\s*=\s*"[^"]*"/i', '', $text);
        
        // إزالة جميع خصائص id
        $text = preg_replace('/\s*id\s*=\s*"[^"]*"/i', '', $text);
        
        // إزالة جميع خصائص font-family
        $text = preg_replace('/\s*font-family\s*:\s*[^;]*;?/i', '', $text);
        
        // إزالة جميع خصائص font-size
        $text = preg_replace('/\s*font-size\s*:\s*[^;]*;?/i', '', $text);
        
        // إزالة جميع خصائص background-color
        $text = preg_replace('/\s*background-color\s*:\s*[^;]*;?/i', '', $text);
        
        // إزالة جميع خصائص color
        $text = preg_replace('/\s*color\s*:\s*[^;]*;?/i', '', $text);
        
        // إزالة جميع خصائص CSS الأخرى
        $text = preg_replace('/\s*[a-z-]+\s*:\s*[^;]*;?/i', '', $text);
        
        // إزالة جميع HTML tags
        $text = strip_tags($text);
        
        // تنظيف المسافات الزائدة
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        if($preserve_line_breaks) {
            // الحفاظ على الأسطر الجديدة مع تنظيفها
            $text = preg_replace('/\n\s*\n/', "\n", $text);
        } else {
            // إزالة الأسطر الفارغة المتكررة
            $text = preg_replace('/\n\s*\n/', " ", $text);
        }
        
        return $text;
    }
    
    /**
     * تنظيف النص مع الحفاظ على طول محدد
     * @param string $text النص المراد تنظيفه
     * @param int $max_length الحد الأقصى لطول النص
     * @param string $suffix النص المضاف في النهاية إذا تم اقتطاع النص
     * @return string النص المنظف والمقتطع
     */
    public static function cleanAndTruncate($text, $max_length = 60, $suffix = '...') {
        $clean_text = self::cleanText($text);
        
        if(strlen($clean_text) > $max_length) {
            return substr($clean_text, 0, $max_length) . $suffix;
        }
        
        return $clean_text;
    }
    
    /**
     * تنظيف النص للعرض الآمن في HTML
     * @param string $text النص المراد تنظيفه
     * @return string النص المنظف والمؤمن
     */
    public static function cleanForHtml($text) {
        $clean_text = self::cleanText($text);
        return htmlspecialchars($clean_text, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * تنظيف شامل للنص المشفر والمتعدد الطبقات
     * @param string $text النص المراد تنظيفه
     * @param bool $preserve_line_breaks الحفاظ على الأسطر الجديدة
     * @return string النص المنظف بالكامل
     */
    public static function deepClean($text, $preserve_line_breaks = false) {
        if(empty($text)) {
            return '';
        }
        
        // فك تشفير HTML entities عدة مرات للتأكد من إزالة جميع الطبقات
        $original_text = $text;
        $decoded_text = $text;
        
        // فك التشفير حتى لا يتغير النص
        do {
            $previous_text = $decoded_text;
            $decoded_text = html_entity_decode($decoded_text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        } while ($previous_text !== $decoded_text);
        
        // تنظيف النص من جميع HTML tags والخصائص
        $clean_text = self::cleanText($decoded_text, $preserve_line_breaks);
        
        // إزالة أي HTML entities متبقية
        $clean_text = html_entity_decode($clean_text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // تنظيف نهائي للمسافات
        $clean_text = preg_replace('/\s+/', ' ', $clean_text);
        $clean_text = trim($clean_text);
        
        return $clean_text;
    }
    
    /**
     * تنظيف شامل للنص مع معالجة جميع أنواع HTML
     * @param string $text النص المراد تنظيفه
     * @param bool $preserve_line_breaks الحفاظ على الأسطر الجديدة
     * @return string النص المنظف بالكامل
     */
    public static function ultraClean($text, $preserve_line_breaks = false) {
        if(empty($text)) {
            return '';
        }
        
        // فك تشفير HTML entities عدة مرات
        $decoded_text = $text;
        do {
            $previous_text = $decoded_text;
            $decoded_text = html_entity_decode($decoded_text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        } while ($previous_text !== $decoded_text);
        
        // إزالة جميع خصائص style (بشكل أكثر شمولية)
        $decoded_text = preg_replace('/\s*style\s*=\s*"[^"]*"/i', '', $decoded_text);
        
        // إزالة جميع خصائص data-*
        $decoded_text = preg_replace('/\s*data-[^=]*\s*=\s*"[^"]*"/i', '', $decoded_text);
        
        // إزالة جميع خصائص class
        $decoded_text = preg_replace('/\s*class\s*=\s*"[^"]*"/i', '', $decoded_text);
        
        // إزالة جميع خصائص id
        $decoded_text = preg_replace('/\s*id\s*=\s*"[^"]*"/i', '', $decoded_text);
        
        // إزالة جميع خصائص font-family
        $decoded_text = preg_replace('/\s*font-family\s*:\s*[^;]*;?/i', '', $decoded_text);
        
        // إزالة جميع خصائص font-size
        $decoded_text = preg_replace('/\s*font-size\s*:\s*[^;]*;?/i', '', $decoded_text);
        
        // إزالة جميع خصائص background-color
        $decoded_text = preg_replace('/\s*background-color\s*:\s*[^;]*;?/i', '', $decoded_text);
        
        // إزالة جميع خصائص color
        $decoded_text = preg_replace('/\s*color\s*:\s*[^;]*;?/i', '', $decoded_text);
        
        // إزالة جميع خصائص text-align
        $decoded_text = preg_replace('/\s*text-align\s*:\s*[^;]*;?/i', '', $decoded_text);
        
        // إزالة جميع خصائص CSS الأخرى
        $decoded_text = preg_replace('/\s*[a-z-]+\s*:\s*[^;]*;?/i', '', $decoded_text);
        
        // إزالة جميع HTML tags
        $decoded_text = strip_tags($decoded_text);
        
        // تنظيف المسافات الزائدة
        $decoded_text = preg_replace('/\s+/', ' ', $decoded_text);
        $decoded_text = trim($decoded_text);
        
        if($preserve_line_breaks) {
            // الحفاظ على الأسطر الجديدة مع تنظيفها
            $decoded_text = preg_replace('/\n\s*\n/', "\n", $decoded_text);
        } else {
            // إزالة الأسطر الفارغة المتكررة
            $decoded_text = preg_replace('/\n\s*\n/', " ", $decoded_text);
        }
        
        // إزالة أي HTML entities متبقية
        $decoded_text = html_entity_decode($decoded_text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return $decoded_text;
    }
    
    /**
     * تنظيف النص مع الحفاظ على طول محدد (إصدار محسن)
     * @param string $text النص المراد تنظيفه
     * @param int $max_length الحد الأقصى لطول النص
     * @param string $suffix النص المضاف في النهاية إذا تم اقتطاع النص
     * @return string النص المنظف والمقتطع
     */
    public static function cleanAndTruncateEnhanced($text, $max_length = 60, $suffix = '...') {
        $clean_text = self::deepClean($text);
        
        if(strlen($clean_text) > $max_length) {
            return substr($clean_text, 0, $max_length) . $suffix;
        }
        
        return $clean_text;
    }
    
    /**
     * تنظيف النص مع الحفاظ على طول محدد (إصدار نهائي)
     * @param string $text النص المراد تنظيفه
     * @param int $max_length الحد الأقصى لطول النص
     * @param string $suffix النص المضاف في النهاية إذا تم اقتطاع النص
     * @return string النص المنظف والمقتطع
     */
    public static function cleanAndTruncateUltra($text, $max_length = 60, $suffix = '...') {
        $clean_text = self::ultraClean($text);
        
        if(strlen($clean_text) > $max_length) {
            return substr($clean_text, 0, $max_length) . $suffix;
        }
        
        return $clean_text;
    }
    
    /**
     * Sanitizes rich HTML content for safe rendering while preserving basic formatting.
     * Allowed tags: p, br, b, strong, i, em, u, ul, ol, li, a
     * Allowed attributes: href on <a> only (http/https/mailto/tel). Others are stripped.
     * @param string $html Raw HTML (e.g., from Summernote)
     * @return string Safe HTML retaining basic formatting
     */
    public static function sanitizeForDescription($html){
        if(empty($html)) return '';
        // Decode entities twice to avoid double-encoding issues
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Remove script/style blocks and comments entirely
        $html = preg_replace('#<\s*script[^>]*>.*?<\s*/\s*script\s*>#is', '', $html);
        $html = preg_replace('#<\s*style[^>]*>.*?<\s*/\s*style\s*>#is', '', $html);
        $html = preg_replace('/<!--.*?-->/s', '', $html);

        // Keep only basic formatting tags
        $allowed = '<p><br><b><strong><i><em><u><ul><ol><li><a>'; 
        $html = strip_tags($html, $allowed);

        // Remove dangerous/on* attributes and common styling/ids/classes/data-*
        // on* handlers
        $html = preg_replace('/\s+on[\w-]+\s*=\s*(["\"]).*?\1/i', '', $html);
        // style, class, id, dir, align
        $html = preg_replace('/\s*(?:style|class|id|dir|align)\s*=\s*(["\"]).*?\1/i', '', $html);
        // data-* attributes
        $html = preg_replace('/\s*data-[^=\s]*\s*=\s*(["\"]).*?\1/i', '', $html);

        // Sanitize <a> tags: allow only safe href protocols and normalize attributes
        $html = preg_replace_callback('/<a\b([^>]*)>(.*?)<\/a>/is', function($m){
            $attrs = $m[1];
            $text  = $m[2];
            $href = '';
            if(preg_match('/href\s*=\s*(["\"])(.*?)\1/i', $attrs, $am)){
                $href = trim($am[2]);
            }
            // Allow only http(s), mailto, tel
            if($href === '' || !preg_match('#^(https?:|mailto:|tel:)#i', $href)){
                // Drop link, keep inner text
                return $text;
            }
            // Escape attribute value
            $safeHref = htmlspecialchars($href, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return '<a href="' . $safeHref . '" target="_blank" rel="nofollow noopener">' . $text . '</a>';
        }, $html);

        // Remove CSS-like declarations that may have become plain text
        $cssProps = '(?:font-family|font-size|background(?:-color)?|color|text-align|line-height|margin|padding|display|border(?:-radius)?|width|height|position|top|left|right|bottom|float|clear|overflow|visibility|z-index|cursor|list-style(?:-type)?|vertical-align|white-space|word-wrap|word-break|text-overflow|text-shadow|box-shadow|transform|transition|animation|filter|opacity|backdrop-filter|perspective|will-change|content|quotes|resize|user-select|pointer-events|touch-action|gap|row-gap|column-gap|grid|flex|justify-content|align-items|align-content)';
        $pattern = '/'.$cssProps.'\s*:\s*[^;<]+;?/i';
        $html = preg_replace($pattern, ' ', $html);

        // Collapse excessive whitespace while preserving line breaks and list structure
        // Replace multiple <br> with at most two
        $html = preg_replace('/(?:<br\s*\/?\s*>\s*){3,}/i', '<br><br>', $html);
        // Trim spaces between tags
        $html = preg_replace('/\s{2,}/', ' ', $html);
        $html = trim($html);

        return $html;
    }
}
?>
