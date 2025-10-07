<?php
/**
 * WAY - App orario del Liceo Scientifico Nino Cortese
 * 
 * Copyright (c) 2025, WAY Project
 * Licensed under the BSD 3-Clause License
 * 
 * Configurazione del database e funzioni di utilità
 */

/**
 * ========================================
 * CONFIGURAZIONE DATABASE
 * ========================================
 * 
 * IMPORTANTE: Prima di utilizzare questa applicazione, devi configurare
 * i parametri del database. Puoi farlo in due modi:
 * 
 * METODO 1 - Variabili d'ambiente (RACCOMANDATO):
 * Crea un file .env nella cartella static/ con:
 * DB_HOST=il_tuo_host_database
 * DB_NAME=il_tuo_nome_database  
 * DB_USERNAME=il_tuo_username
 * DB_PASSWORD=la_tua_password
 * AUTH_SALT=una_stringa_casuale_lunga_per_sicurezza
 * 
 * METODO 2 - Modifica diretta (meno sicuro):
 * Sostituisci i valori 'CONFIGURA_XXX' qui sotto con i tuoi dati reali
 * 
 * STRUTTURA DATABASE RICHIESTA:
 * - Tabella per orari scolastici
 * - Tabella per sostituzioni docenti
 * - Tabella per autenticazione utenti
 * - Charset: UTF-8 (utf8mb4)
 */

// Carica configurazione da variabili d'ambiente se disponibili
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// Configurazione del database
// ATTENZIONE: Sostituisci questi valori con la tua configurazione!
define('DB_HOST', $_ENV['DB_HOST'] ?? 'CONFIGURA_HOST_DATABASE');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'CONFIGURA_NOME_DATABASE'); 
define('DB_USERNAME', $_ENV['DB_USERNAME'] ?? 'CONFIGURA_USERNAME');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? 'CONFIGURA_PASSWORD');
define('DB_CHARSET', 'utf8mb4');

// Configurazione per l'autenticazione
// ATTENZIONE: Genera una stringa casuale lunga per sicurezza!
define('AUTH_SALT', $_ENV['AUTH_SALT'] ?? 'CONFIGURA_SALT_CASUALE_LUNGO');

// Verifica che la configurazione sia stata impostata
if (DB_HOST === 'CONFIGURA_HOST_DATABASE' || 
    DB_NAME === 'CONFIGURA_NOME_DATABASE' || 
    DB_USERNAME === 'CONFIGURA_USERNAME' || 
    DB_PASSWORD === 'CONFIGURA_PASSWORD' ||
    AUTH_SALT === 'CONFIGURA_SALT_CASUALE_LUNGO') {
    
    die('ERRORE: Configurazione database non completata!\n\n' .
        'Prima di utilizzare questa applicazione devi configurare:\n' .
        '1. I parametri del database (host, nome, username, password)\n' .
        '2. Il salt per l\'autenticazione\n\n' .
        'Leggi i commenti in config.php per le istruzioni dettagliate.');
}

/**
 * Crea una connessione al database usando MySQLi
 * @return mysqli|false Connessione MySQLi o false in caso di errore
 */
function createMySQLiConnection() {
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Verifica della connessione
    if ($conn->connect_error) {
        error_log("Errore connessione database: " . $conn->connect_error);
        return false;
    }
    
    // Imposta il charset
    $conn->set_charset(DB_CHARSET);
    
    return $conn;
}

/**
 * Crea una connessione al database usando PDO
 * @return PDO|false Connessione PDO o false in caso di errore
 */
function createPDOConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Errore connessione PDO: " . $e->getMessage());
        return false;
    }
}

/**
 * Imposta gli header comuni per le API
 */
function setCommonHeaders() {
    // Header JSON
    header('Content-Type: application/json; charset=utf-8');
    
    // Header CORS
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    // Rimozione cache
    header_remove('ETag');
    header_remove('Pragma');
    header_remove('Cache-Control');
    header_remove('Last-Modified');
    header_remove('Expires');
    header('Expires: Thu, 1 Jan 1970 00:00:00 GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
}

/**
 * Gestisce la richiesta OPTIONS per CORS preflight
 */
function handleCORSPreflight() {
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

/**
 * Invia una risposta JSON e termina l'esecuzione
 * @param array $data Dati da inviare
 * @param int $httpCode Codice HTTP (default: 200)
 */
function sendJsonResponse($data, $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Invia una risposta di errore JSON
 * @param string $message Messaggio di errore
 * @param int $httpCode Codice HTTP (default: 500)
 */
function sendErrorResponse($message, $httpCode = 500) {
    sendJsonResponse([
        'success' => false,
        'error' => $message
    ], $httpCode);
}

/**
 * Valida un indirizzo email del dominio della scuola
 * @param string $email Email da validare
 * @return bool True se l'email è valida
 */
function isEmailValid($email) {
    $pattern = '/^[a-z]{6,}@lscortese\.com$/';
    return preg_match($pattern, $email);
}

/**
 * Genera un codice di verifica per l'email
 * @param string $email Email per cui generare il codice
 * @return string Codice a 6 cifre
 */
function generateVerificationCode($email) {
    $hash = md5($email . AUTH_SALT);
    $numeric = hexdec(substr($hash, 0, 8));
    return str_pad($numeric % 1000000, 6, '0', STR_PAD_LEFT);
}

// Inizializzazione automatica degli header comuni
setCommonHeaders();
handleCORSPreflight();
?>