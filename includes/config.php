<?php
/**
 * SMTP Tester Configuration
 */

// Application Settings
define('APP_NAME', 'SMTP Tester');
define('APP_VERSION', '1.0.0');
define('DEFAULT_LANGUAGE', 'en');

// Supported Languages (ordered by number of speakers)
$SUPPORTED_LANGUAGES = [
    'en' => ['name' => 'English', 'native' => 'English', 'flag' => '🇬🇧', 'dir' => 'ltr'],
    'zh' => ['name' => 'Chinese', 'native' => '中文', 'flag' => '🇨🇳', 'dir' => 'ltr'],
    'hi' => ['name' => 'Hindi', 'native' => 'हिन्दी', 'flag' => '🇮🇳', 'dir' => 'ltr'],
    'es' => ['name' => 'Spanish', 'native' => 'Español', 'flag' => '🇪🇸', 'dir' => 'ltr'],
    'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'flag' => '🇸🇦', 'dir' => 'rtl'],
    'bn' => ['name' => 'Bengali', 'native' => 'বাংলা', 'flag' => '🇧🇩', 'dir' => 'ltr'],
    'pt' => ['name' => 'Portuguese', 'native' => 'Português', 'flag' => '🇧🇷', 'dir' => 'ltr'],
    'ru' => ['name' => 'Russian', 'native' => 'Русский', 'flag' => '🇷🇺', 'dir' => 'ltr'],
    'ja' => ['name' => 'Japanese', 'native' => '日本語', 'flag' => '🇯🇵', 'dir' => 'ltr'],
    'de' => ['name' => 'German', 'native' => 'Deutsch', 'flag' => '🇩🇪', 'dir' => 'ltr'],
    'fr' => ['name' => 'French', 'native' => 'Français', 'flag' => '🇫🇷', 'dir' => 'ltr'],
    'ko' => ['name' => 'Korean', 'native' => '한국어', 'flag' => '🇰🇷', 'dir' => 'ltr'],
    'it' => ['name' => 'Italian', 'native' => 'Italiano', 'flag' => '🇮🇹', 'dir' => 'ltr'],
    'vi' => ['name' => 'Vietnamese', 'native' => 'Tiếng Việt', 'flag' => '🇻🇳', 'dir' => 'ltr'],
    'tr' => ['name' => 'Turkish', 'native' => 'Türkçe', 'flag' => '🇹🇷', 'dir' => 'ltr'],
    'pl' => ['name' => 'Polish', 'native' => 'Polski', 'flag' => '🇵🇱', 'dir' => 'ltr'],
    'uk' => ['name' => 'Ukrainian', 'native' => 'Українська', 'flag' => '🇺🇦', 'dir' => 'ltr'],
    'nl' => ['name' => 'Dutch', 'native' => 'Nederlands', 'flag' => '🇳🇱', 'dir' => 'ltr'],
    'id' => ['name' => 'Indonesian', 'native' => 'Bahasa Indonesia', 'flag' => '🇮🇩', 'dir' => 'ltr'],
    'th' => ['name' => 'Thai', 'native' => 'ไทย', 'flag' => '🇹🇭', 'dir' => 'ltr'],
    'fa' => ['name' => 'Persian', 'native' => 'فارسی', 'flag' => '🇮🇷', 'dir' => 'rtl'],
];

// Default SMTP Ports
$DEFAULT_PORTS = [
    'none' => 25,
    'tls' => 587,
    'ssl' => 465
];

// Timeout settings (in seconds)
define('SMTP_TIMEOUT', 30);
define('SMTP_DEBUG_LEVEL', 4);
