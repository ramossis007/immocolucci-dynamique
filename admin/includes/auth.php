<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/helpers/security.php';
require_once __DIR__ . '/../../app/helpers/functions.php';

// Rôles requis, surchargeables par la page avant inclusion
$rolesRequis = $rolesRequis ?? ['admin'];
verifierRole($rolesRequis);