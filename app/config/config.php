<?php
// Chargement des variables d'environnement
if (file_exists(__DIR__ . '/../../.env')) {
    $lines = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
    }
}

// Préfixe d'URL détecté automatiquement (gère sous-dossier et racine).
// Ex. http://localhost/immocolucci_dynamique/public/ => BASE_PATH = '/immocolucci_dynamique'
if (!defined('BASE_PATH')) {
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $base = rtrim(dirname(dirname($script)), '/');
    define('BASE_PATH', ($base === '/' || $base === '' || $base === '.') ? '' : $base);
}

define('SITE_URL', $_ENV['SITE_URL'] ?? 'http://immocolucci.local');
define('UPLOAD_DIR', __DIR__ . '/../../public/assets/img/uploads/');
define('UPLOAD_URL', '/assets/img/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);
define('BIENS_PAR_PAGE', 12); // Nombre de biens par page (pagination)
define('ALLOWED_MIME', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

date_default_timezone_set('Africa/Casablanca');
ini_set('display_errors', $_ENV['APP_DEBUG'] ?? '0');
error_reporting(E_ALL);