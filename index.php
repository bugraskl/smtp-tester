<?php
/**
 * SMTP Tester - Main Page
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Detect and load language
$currentLang = detectLanguage();
$lang = loadLanguage($currentLang);
$langInfo = $SUPPORTED_LANGUAGES[$currentLang];
$textDir = $langInfo['dir'] ?? 'ltr';
?>
<!DOCTYPE html>
<html lang="<?php echo $currentLang; ?>" dir="<?php echo $textDir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($lang['meta_description']); ?>">
    <meta name="keywords" content="SMTP, tester, email, mail server, debug, test">
    
    <title><?php echo htmlspecialchars($lang['page_title']); ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📧</text></svg>">
</head>
<body>
    <div class="bg-gradient"></div>
    
    <div class="container">
        <!-- Language Selector -->
        <div class="language-selector">
            <div class="lang-dropdown">
                <button type="button" class="lang-btn" id="langBtn">
                    <span class="flag"><?php echo $langInfo['flag']; ?></span>
                    <span class="name"><?php echo $langInfo['native']; ?></span>
                    <span class="arrow">▼</span>
                </button>
                <div class="lang-menu" id="langMenu">
                    <?php foreach ($SUPPORTED_LANGUAGES as $code => $info): ?>
                    <div class="lang-option <?php echo $code === $currentLang ? 'active' : ''; ?>" data-lang="<?php echo $code; ?>">
                        <span class="flag"><?php echo $info['flag']; ?></span>
                        <span class="name"><?php echo $info['native']; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Header -->
        <header class="header">
            <div class="logo">
                <div class="logo-icon">📧</div>
                <h1><?php echo htmlspecialchars($lang['app_title']); ?></h1>
            </div>
            <p class="header-subtitle"><?php echo htmlspecialchars($lang['app_subtitle']); ?></p>
        </header>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- SMTP Form -->
            <section class="card">
                <h2 class="card-title">
                    <span class="icon">⚙️</span>
                    <?php echo htmlspecialchars($lang['settings_title']); ?>
                </h2>
                
                <!-- Quick Presets -->
                <div class="presets">
                    <button type="button" class="preset-btn" data-host="smtp.gmail.com" data-port="587" data-encryption="tls">Gmail</button>
                    <button type="button" class="preset-btn" data-host="smtp.outlook.com" data-port="587" data-encryption="tls">Outlook</button>
                    <button type="button" class="preset-btn" data-host="smtp.mail.yahoo.com" data-port="587" data-encryption="tls">Yahoo</button>
                    <button type="button" class="preset-btn" data-host="smtp.yandex.com" data-port="465" data-encryption="ssl">Yandex</button>
                </div>
                
                <form id="smtpForm">
                    <div class="form-group">
                        <label class="form-label" for="host"><?php echo htmlspecialchars($lang['label_host']); ?></label>
                        <input type="text" class="form-input" id="host" name="host" 
                               placeholder="smtp.example.com" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="port"><?php echo htmlspecialchars($lang['label_port']); ?></label>
                            <input type="number" class="form-input" id="port" name="port" 
                                   value="587" min="1" max="65535" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="encryption"><?php echo htmlspecialchars($lang['label_encryption']); ?></label>
                            <select class="form-input form-select" id="encryption" name="encryption">
                                <option value="tls">TLS (STARTTLS)</option>
                                <option value="ssl">SSL/TLS</option>
                                <option value="none"><?php echo htmlspecialchars($lang['encryption_none']); ?></option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="username"><?php echo htmlspecialchars($lang['label_username']); ?></label>
                        <input type="text" class="form-input" id="username" name="username" 
                               placeholder="user@example.com">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password"><?php echo htmlspecialchars($lang['label_password']); ?></label>
                        <input type="password" class="form-input" id="password" name="password" 
                               placeholder="••••••••">
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="spinner" style="display: none;"></span>
                        <span class="btn-text"><?php echo htmlspecialchars($lang['test_button']); ?></span>
                    </button>
                </form>
            </section>
            
            <!-- Debug Output -->
            <section class="card">
                <h2 class="card-title">
                    <span class="icon">🖥️</span>
                    <?php echo htmlspecialchars($lang['debug_title']); ?>
                </h2>
                
                <div class="terminal">
                    <div class="terminal-header">
                        <div class="terminal-dots">
                            <span class="terminal-dot red"></span>
                            <span class="terminal-dot yellow"></span>
                            <span class="terminal-dot green"></span>
                        </div>
                        <span class="terminal-title">SMTP Debug Log</span>
                    </div>
                    <div class="terminal-body" id="terminalBody">
                        <div class="terminal-empty">
                            <span class="icon">📡</span>
                            <p><?php echo htmlspecialchars($lang['debug_empty']); ?></p>
                        </div>
                    </div>
                </div>
                
                <div id="resultContainer"></div>
            </section>
        </main>
        
        <!-- Footer -->
        <footer class="footer">
            <p><?php echo $lang['footer_text']; ?></p>
        </footer>
    </div>
    
    <!-- Pass data to JavaScript -->
    <script>
        window.initialLang = '<?php echo $currentLang; ?>';
        window.langData = <?php echo json_encode($lang, JSON_UNESCAPED_UNICODE); ?>;
    </script>
    
    <!-- Scripts -->
    <script src="assets/js/app.js"></script>
</body>
</html>
