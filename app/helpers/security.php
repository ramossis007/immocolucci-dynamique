<?php
function verifierConnexion(): void {
    if (empty($_SESSION['utilisateur_id'])) {
        redirect(BASE_PATH . '/admin/login.php');
    }
}

function verifierRole(array $rolesAutorises = []): void {
    verifierConnexion();
    if (!empty($rolesAutorises) && !in_array($_SESSION['utilisateur_role'] ?? '', $rolesAutorises, true)) {
        http_response_code(403);
        die('Acces refuse.');
    }
}

function genererCsrf(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifierCsrf(?string $token): bool {
    return !empty($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function exigerPost(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        die('Methode non autorisee.');
    }
}

// ===== Rate limiting (par IP, stockage fichier) =====

function _throttleFichier(string $cle): string {
    $dir = dirname(__DIR__) . '/storage/throttle';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    return $dir . '/' . hash('sha256', $cle) . '.json';
}

function _throttleLire(string $fichier): array {
    $defaut = ['tentatives' => 0, 'premiere' => 0, 'bloque_jusqu' => 0];
    if (!is_file($fichier)) return $defaut;
    $j = json_decode((string)@file_get_contents($fichier), true);
    return is_array($j) ? array_merge($defaut, $j) : $defaut;
}

function throttleRestant(string $cle, int $max = 5, int $fenetre = 900): int {
    $d = _throttleLire(_throttleFichier($cle));
    return $d['bloque_jusqu'] > time() ? $d['bloque_jusqu'] - time() : 0;
}

function throttleEchec(string $cle, int $max = 5, int $fenetre = 900): void {
    $f = _throttleFichier($cle);
    $d = _throttleLire($f);
    if ($d['premiere'] > 0 && (time() - $d['premiere']) > $fenetre) {
        $d = ['tentatives' => 0, 'premiere' => 0, 'bloque_jusqu' => 0];
    }
    $d['tentatives']++;
    if ($d['premiere'] === 0) $d['premiere'] = time();
    if ($d['tentatives'] >= $max) {
        $d['bloque_jusqu'] = time() + $fenetre;
        $d['tentatives'] = 0;
        $d['premiere'] = 0;
    }
    file_put_contents($f, json_encode($d), LOCK_EX);
}

function throttleSucces(string $cle): void {
    $f = _throttleFichier($cle);
    if (is_file($f)) @unlink($f);
}
