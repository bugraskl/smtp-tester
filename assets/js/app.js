/**
 * SMTP Tester - Main JavaScript
 */

// Global state
let currentLang = 'en';
let langData = {};
let isLoading = false;

// DOM Elements
const elements = {
    langBtn: null,
    langMenu: null,
    langDropdown: null,
    form: null,
    submitBtn: null,
    terminalBody: null,
    resultContainer: null
};

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', init);

function init() {
    // Cache DOM elements
    elements.langBtn = document.getElementById('langBtn');
    elements.langMenu = document.getElementById('langMenu');
    elements.langDropdown = document.querySelector('.lang-dropdown');
    elements.form = document.getElementById('smtpForm');
    elements.submitBtn = document.getElementById('submitBtn');
    elements.terminalBody = document.getElementById('terminalBody');
    elements.resultContainer = document.getElementById('resultContainer');
    
    // Set up event listeners
    setupLanguageSelector();
    setupForm();
    setupPresets();
    
    // Load initial language
    if (window.initialLang) {
        currentLang = window.initialLang;
    }
    if (window.langData) {
        langData = window.langData;
    }
}

/**
 * Language Selector Setup
 */
function setupLanguageSelector() {
    // Toggle dropdown
    elements.langBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        elements.langDropdown.classList.toggle('open');
    });
    
    // Close on outside click
    document.addEventListener('click', () => {
        elements.langDropdown.classList.remove('open');
    });
    
    // Language options
    elements.langMenu.addEventListener('click', (e) => {
        const option = e.target.closest('.lang-option');
        if (option) {
            const lang = option.dataset.lang;
            changeLanguage(lang);
        }
    });
}

/**
 * Change language
 */
function changeLanguage(lang) {
    // Set cookie
    document.cookie = `smtp_tester_lang=${lang};path=/;max-age=31536000`;
    
    // Reload page
    window.location.reload();
}

/**
 * Form Setup
 */
function setupForm() {
    elements.form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        if (isLoading) return;
        
        await runTest();
    });
    
    // Update port based on encryption
    const encryptionSelect = document.getElementById('encryption');
    const portInput = document.getElementById('port');
    
    encryptionSelect.addEventListener('change', () => {
        const ports = {
            'none': 25,
            'tls': 587,
            'ssl': 465
        };
        portInput.value = ports[encryptionSelect.value] || 587;
    });
}

/**
 * Preset buttons setup
 */
function setupPresets() {
    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const host = btn.dataset.host;
            const port = btn.dataset.port;
            const encryption = btn.dataset.encryption;
            
            document.getElementById('host').value = host;
            document.getElementById('port').value = port;
            document.getElementById('encryption').value = encryption;
        });
    });
}

/**
 * Run SMTP Test
 */
async function runTest() {
    isLoading = true;
    
    // Update button state
    const btnText = elements.submitBtn.querySelector('.btn-text');
    const btnSpinner = elements.submitBtn.querySelector('.spinner');
    
    btnText.textContent = langData.testing || 'Testing...';
    btnSpinner.style.display = 'block';
    elements.submitBtn.disabled = true;
    
    // Clear previous results
    clearTerminal();
    hideResult();
    
    // Show connecting message
    addTerminalLine({
        time: getTime(),
        message: langData.debug_preparing || 'Preparing test...',
        status: 'info'
    });
    
    // Gather form data
    const formData = {
        host: document.getElementById('host').value.trim(),
        port: document.getElementById('port').value,
        username: document.getElementById('username').value.trim(),
        password: document.getElementById('password').value,
        encryption: document.getElementById('encryption').value,
        lang: currentLang
    };
    
    try {
        const response = await fetch('api/test.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        // Clear preparing message
        clearTerminal();
        
        // Display debug log with animation
        if (result.data && result.data.debug) {
            await displayDebugLog(result.data.debug);
        }
        
        // Show result
        showResult(result.success, result.message);
        
    } catch (error) {
        clearTerminal();
        addTerminalLine({
            time: getTime(),
            message: (langData.debug_error || 'Error') + ': ' + error.message,
            status: 'error'
        });
        showResult(false, langData.connection_error || 'Connection error');
    } finally {
        // Reset button
        btnText.textContent = langData.test_button || 'Test SMTP Connection';
        btnSpinner.style.display = 'none';
        elements.submitBtn.disabled = false;
        isLoading = false;
    }
}

/**
 * Display debug log with animation
 */
async function displayDebugLog(log) {
    for (let i = 0; i < log.length; i++) {
        const entry = log[i];
        addTerminalLine(entry);
        
        // Small delay for animation effect
        await sleep(100);
        
        // Scroll to bottom
        elements.terminalBody.scrollTop = elements.terminalBody.scrollHeight;
    }
}

/**
 * Add line to terminal
 */
function addTerminalLine(entry) {
    const line = document.createElement('div');
    line.className = `terminal-line ${entry.status}`;
    
    const icon = getStatusIcon(entry.status);
    
    line.innerHTML = `
        <span class="terminal-time">[${entry.time}]</span>
        <span class="terminal-icon">${icon}</span>
        <span class="terminal-message">${escapeHtml(entry.message)}</span>
    `;
    
    elements.terminalBody.appendChild(line);
}

/**
 * Get icon for status
 */
function getStatusIcon(status) {
    const icons = {
        'info': '●',
        'success': '✓',
        'error': '✗',
        'sent': '→',
        'received': '←'
    };
    return icons[status] || '●';
}

/**
 * Clear terminal
 */
function clearTerminal() {
    elements.terminalBody.innerHTML = '';
}

/**
 * Show result badge
 */
function showResult(success, message) {
    const badge = document.createElement('div');
    badge.className = `result-badge ${success ? 'success' : 'error'}`;
    badge.innerHTML = `
        <span>${success ? '✓' : '✗'}</span>
        <span>${escapeHtml(message)}</span>
    `;
    
    elements.resultContainer.innerHTML = '';
    elements.resultContainer.appendChild(badge);
}

/**
 * Hide result
 */
function hideResult() {
    elements.resultContainer.innerHTML = '';
}

/**
 * Get current time formatted
 */
function getTime() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const ms = String(now.getMilliseconds()).padStart(3, '0');
    return `${hours}:${minutes}:${seconds}.${ms}`;
}

/**
 * Sleep helper
 */
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

/**
 * Escape HTML
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
