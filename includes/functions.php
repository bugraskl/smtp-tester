<?php
/**
 * Helper Functions
 */

require_once __DIR__ . '/config.php';

/**
 * Detect browser language and return matching supported language code
 */
function detectLanguage() {
    global $SUPPORTED_LANGUAGES;
    
    // Check cookie first
    if (isset($_COOKIE['smtp_tester_lang']) && isset($SUPPORTED_LANGUAGES[$_COOKIE['smtp_tester_lang']])) {
        return $_COOKIE['smtp_tester_lang'];
    }
    
    // Check Accept-Language header
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $browserLangs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        
        foreach ($browserLangs as $lang) {
            // Get primary language code (e.g., 'en' from 'en-US')
            $langCode = strtolower(substr(trim($lang), 0, 2));
            
            if (isset($SUPPORTED_LANGUAGES[$langCode])) {
                return $langCode;
            }
        }
    }
    
    return DEFAULT_LANGUAGE;
}

/**
 * Load language file
 */
function loadLanguage($langCode) {
    global $SUPPORTED_LANGUAGES;
    
    if (!isset($SUPPORTED_LANGUAGES[$langCode])) {
        $langCode = DEFAULT_LANGUAGE;
    }
    
    $langFile = __DIR__ . '/../languages/' . $langCode . '.php';
    
    if (file_exists($langFile)) {
        return include $langFile;
    }
    
    // Fallback to English
    return include __DIR__ . '/../languages/en.php';
}

/**
 * Sanitize input
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate hostname/IP
 */
function isValidHost($host) {
    // Check if valid IP
    if (filter_var($host, FILTER_VALIDATE_IP)) {
        return true;
    }
    
    // Check if valid hostname
    return preg_match('/^[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?(\.[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?)*$/', $host);
}

/**
 * Validate port number
 */
function isValidPort($port) {
    $port = intval($port);
    return $port >= 1 && $port <= 65535;
}

/**
 * Format timestamp for debug output
 */
function formatDebugTime() {
    return date('H:i:s') . '.' . substr(microtime(), 2, 3);
}

/**
 * JSON response helper
 */
function jsonResponse($success, $data = [], $message = '') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
