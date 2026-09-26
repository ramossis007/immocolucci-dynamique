<?php
session_start();

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/helpers/security.php';

// Autoload
spl_autoload_register(function ($class) {
    foreach ([
        __DIR__ . '/../app/models/',
        __DIR__ . '/../app/controllers/',
    ] as $path) {
        $file = $path . $class . '.php';
        if (is_file($file)) { require_once $file; return; }
    }
});

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Retirer le chemin de base (ex: /immocolucci_dynamique)
if (defined('BASE_PATH') && BASE_PATH && strpos($uri, BASE_PATH) === 0) {
    $uri = substr($uri, strlen(BASE_PATH));
}

// Retirer /public si present
if (strpos($uri, '/public') === 0) {
    $uri = substr($uri, strlen('/public'));
}

$uri = trim($uri, '/');
$parts = $uri === '' ? [] : explode('/', $uri);
$route = $parts[0] ?? '';

$routes = [
    ''         => ['AccueilController', 'index'],
    'biens'    => ['BienController',    'liste'],
    'bien'     => ['BienController',    'detail'],
    'services' => ['ServiceController', 'liste'],
    'service'  => ['ServiceController', 'detail'],
    'blog'     => ['ArticleController', 'liste'],
    'article'  => ['ArticleController', 'detail'],
    'contact'  => ['ContactController', 'index'],
    'chatbot'  => ['ChatbotController', 'repondre'],
];

if (!isset($routes[$route])) {
    http_response_code(404);
    $notFound = __DIR__ . '/../app/views/pages/404.php';
    if (is_file($notFound)) require $notFound;
    else echo '<h1>404 - Page introuvable</h1>';
    exit;
}

[$controllerClass, $method] = $routes[$route];

if (!class_exists($controllerClass)) {
    http_response_code(500);
    die("Controleur manquant : $controllerClass");
}

$controller = new $controllerClass();
$args = array_slice($parts, 1);
call_user_func_array([$controller, $method], $args);