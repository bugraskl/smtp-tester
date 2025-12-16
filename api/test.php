<?php
/**
 * SMTP Test API Endpoint
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/SmtpTester.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, [], 'Method not allowed');
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    // Try form data
    $input = $_POST;
}

// Validate required fields
$host = isset($input['host']) ? sanitizeInput($input['host']) : '';
$port = isset($input['port']) ? (int) $input['port'] : 587;
$username = isset($input['username']) ? trim($input['username']) : '';
$password = isset($input['password']) ? $input['password'] : '';
$encryption = isset($input['encryption']) ? sanitizeInput($input['encryption']) : 'tls';
$langCode = isset($input['lang']) ? sanitizeInput($input['lang']) : 'en';

// Validate host
if (empty($host)) {
    jsonResponse(false, [], 'SMTP host is required');
}

if (!isValidHost($host)) {
    jsonResponse(false, [], 'Invalid SMTP host');
}

// Validate port
if (!isValidPort($port)) {
    jsonResponse(false, [], 'Invalid port number');
}

// Validate encryption
$validEncryptions = ['none', 'tls', 'ssl'];
if (!in_array($encryption, $validEncryptions)) {
    $encryption = 'tls';
}

// Load language for debug messages
$lang = loadLanguage($langCode);

// Create tester and run test
$tester = new SmtpTester($host, $port, $username, $password, $encryption, SMTP_TIMEOUT, $lang);
$success = $tester->test();
$debugLog = $tester->getDebugLog();

// Return results
jsonResponse($success, [
    'debug' => $debugLog,
    'host' => $host,
    'port' => $port,
    'encryption' => $encryption
], $success ? ($lang['test_success'] ?? 'SMTP test successful!') : ($lang['test_failed'] ?? 'SMTP test failed'));
