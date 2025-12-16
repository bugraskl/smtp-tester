<?php
/**
 * SMTP Tester Class
 * Tests SMTP server connections with detailed debug output
 */

class SmtpTester {
    private $host;
    private $port;
    private $username;
    private $password;
    private $encryption;
    private $timeout;
    private $socket;
    private $debugLog = [];
    private $lang;
    
    // Status constants
    const STATUS_INFO = 'info';
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';
    const STATUS_SENT = 'sent';
    const STATUS_RECEIVED = 'received';
    
    public function __construct($host, $port, $username, $password, $encryption = 'tls', $timeout = 30, $lang = []) {
        $this->host = $host;
        $this->port = (int) $port;
        $this->username = $username;
        $this->password = $password;
        $this->encryption = strtolower($encryption);
        $this->timeout = $timeout;
        $this->lang = $lang;
    }
    
    /**
     * Add entry to debug log
     */
    private function log($message, $status = self::STATUS_INFO, $raw = '') {
        $this->debugLog[] = [
            'time' => date('H:i:s') . '.' . substr(microtime(), 2, 3),
            'message' => $message,
            'status' => $status,
            'raw' => $raw
        ];
    }
    
    /**
     * Get debug log
     */
    public function getDebugLog() {
        return $this->debugLog;
    }
    
    /**
     * Test SMTP connection
     */
    public function test() {
        $this->debugLog = [];
        
        try {
            // Step 1: Connect to server
            if (!$this->connect()) {
                return false;
            }
            
            // Step 2: Read greeting
            if (!$this->readGreeting()) {
                return false;
            }
            
            // Step 3: Send EHLO
            if (!$this->sendEhlo()) {
                return false;
            }
            
            // Step 4: Start TLS if needed
            if ($this->encryption === 'tls') {
                if (!$this->startTls()) {
                    return false;
                }
                // Re-send EHLO after TLS
                if (!$this->sendEhlo()) {
                    return false;
                }
            }
            
            // Step 5: Authenticate
            if (!empty($this->username)) {
                if (!$this->authenticate()) {
                    return false;
                }
            }
            
            // Step 6: Quit gracefully
            $this->quit();
            
            $this->log($this->lang['debug_test_complete'] ?? 'SMTP test completed successfully!', self::STATUS_SUCCESS);
            return true;
            
        } catch (Exception $e) {
            $this->log(($this->lang['debug_error'] ?? 'Error') . ': ' . $e->getMessage(), self::STATUS_ERROR);
            return false;
        } finally {
            $this->disconnect();
        }
    }
    
    /**
     * Connect to SMTP server
     */
    private function connect() {
        $this->log(($this->lang['debug_connecting'] ?? 'Connecting to') . " {$this->host}:{$this->port}...", self::STATUS_INFO);
        
        $protocol = '';
        if ($this->encryption === 'ssl') {
            $protocol = 'ssl://';
        }
        
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        
        $errno = 0;
        $errstr = '';
        
        $this->socket = @stream_socket_client(
            $protocol . $this->host . ':' . $this->port,
            $errno,
            $errstr,
            $this->timeout,
            STREAM_CLIENT_CONNECT,
            $context
        );
        
        if (!$this->socket) {
            $this->log(($this->lang['debug_connection_failed'] ?? 'Connection failed') . ": $errstr ($errno)", self::STATUS_ERROR);
            return false;
        }
        
        stream_set_timeout($this->socket, $this->timeout);
        
        $this->log($this->lang['debug_connection_established'] ?? 'Connection established', self::STATUS_SUCCESS);
        return true;
    }
    
    /**
     * Read server greeting
     */
    private function readGreeting() {
        $response = $this->getResponse();
        
        if ($response === false) {
            $this->log($this->lang['debug_no_greeting'] ?? 'No greeting received from server', self::STATUS_ERROR);
            return false;
        }
        
        if (substr($response, 0, 3) !== '220') {
            $this->log(($this->lang['debug_unexpected_greeting'] ?? 'Unexpected greeting') . ": $response", self::STATUS_ERROR);
            return false;
        }
        
        return true;
    }
    
    /**
     * Send EHLO command
     */
    private function sendEhlo() {
        $hostname = gethostname() ?: 'localhost';
        $this->sendCommand("EHLO $hostname");
        
        $response = $this->getResponse();
        
        if (substr($response, 0, 3) !== '250') {
            // Try HELO as fallback
            $this->sendCommand("HELO $hostname");
            $response = $this->getResponse();
            
            if (substr($response, 0, 3) !== '250') {
                $this->log(($this->lang['debug_ehlo_failed'] ?? 'EHLO/HELO failed') . ": $response", self::STATUS_ERROR);
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Start TLS encryption
     */
    private function startTls() {
        $this->sendCommand("STARTTLS");
        $response = $this->getResponse();
        
        if (substr($response, 0, 3) !== '220') {
            $this->log(($this->lang['debug_starttls_failed'] ?? 'STARTTLS failed') . ": $response", self::STATUS_ERROR);
            return false;
        }
        
        $this->log($this->lang['debug_starting_tls'] ?? 'Starting TLS encryption...', self::STATUS_INFO);
        
        $crypto = stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        
        if (!$crypto) {
            $this->log($this->lang['debug_tls_failed'] ?? 'TLS encryption failed', self::STATUS_ERROR);
            return false;
        }
        
        $this->log($this->lang['debug_tls_enabled'] ?? 'TLS encryption enabled', self::STATUS_SUCCESS);
        return true;
    }
    
    /**
     * Authenticate with server
     */
    private function authenticate() {
        $this->log($this->lang['debug_authenticating'] ?? 'Authenticating...', self::STATUS_INFO);
        
        // Try AUTH LOGIN
        $this->sendCommand("AUTH LOGIN");
        $response = $this->getResponse();
        
        if (substr($response, 0, 3) === '334') {
            // Send username (base64 encoded)
            $this->sendCommand(base64_encode($this->username), true, '[USERNAME]');
            $response = $this->getResponse();
            
            if (substr($response, 0, 3) !== '334') {
                $this->log(($this->lang['debug_auth_username_failed'] ?? 'Username rejected') . ": $response", self::STATUS_ERROR);
                return false;
            }
            
            // Send password (base64 encoded)
            $this->sendCommand(base64_encode($this->password), true, '[PASSWORD]');
            $response = $this->getResponse();
            
            if (substr($response, 0, 3) !== '235') {
                $this->log(($this->lang['debug_auth_failed'] ?? 'Authentication failed') . ": $response", self::STATUS_ERROR);
                return false;
            }
            
            $this->log($this->lang['debug_auth_success'] ?? 'Authentication successful', self::STATUS_SUCCESS);
            return true;
        }
        
        // Try AUTH PLAIN
        $this->sendCommand("AUTH PLAIN " . base64_encode("\0" . $this->username . "\0" . $this->password), true, 'AUTH PLAIN [CREDENTIALS]');
        $response = $this->getResponse();
        
        if (substr($response, 0, 3) !== '235') {
            $this->log(($this->lang['debug_auth_failed'] ?? 'Authentication failed') . ": $response", self::STATUS_ERROR);
            return false;
        }
        
        $this->log($this->lang['debug_auth_success'] ?? 'Authentication successful', self::STATUS_SUCCESS);
        return true;
    }
    
    /**
     * Send QUIT command
     */
    private function quit() {
        $this->sendCommand("QUIT");
        $this->getResponse();
    }
    
    /**
     * Send command to server
     */
    private function sendCommand($command, $sensitive = false, $displayCommand = null) {
        $display = $displayCommand ?: ($sensitive ? '[HIDDEN]' : $command);
        $this->log("→ $display", self::STATUS_SENT, $command);
        
        fwrite($this->socket, $command . "\r\n");
    }
    
    /**
     * Get response from server
     */
    private function getResponse() {
        $response = '';
        
        while (true) {
            $line = fgets($this->socket, 515);
            
            if ($line === false) {
                break;
            }
            
            $response .= $line;
            
            // Check if this is the last line (no hyphen after code)
            if (isset($line[3]) && $line[3] !== '-') {
                break;
            }
        }
        
        $response = trim($response);
        
        if (!empty($response)) {
            $this->log("← $response", self::STATUS_RECEIVED, $response);
        }
        
        return $response;
    }
    
    /**
     * Disconnect from server
     */
    private function disconnect() {
        if ($this->socket) {
            @fclose($this->socket);
            $this->socket = null;
        }
    }
}
