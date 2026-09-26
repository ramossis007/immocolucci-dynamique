<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../app/config/config.php';

header('Content-Type: application/json');

exigerPost();

$input = json_decode(file_get_contents('php://input'), true) ?? [];

if (!verifierCsrf($input['csrf_token'] ?? '')) {
    jsonResponse(['success' => false, 'message' => 'CSRF invalide'], 403);
}

$id = (int)($input['id'] ?? 0);
if ($id <= 0) {
    jsonResponse(['success' => false, 'message' => 'ID invalide'], 400);
}

$pdo = Database::getInstance()->getPdo();

// Récupérer le chemin
$stmt = $pdo->prepare("SELECT chemin FROM medias WHERE id = ?");
$stmt->execute([$id]);
$media = $stmt->fetch();

if (!$media) {
    jsonResponse(['success' => false, 'message' => 'Média introuvable'], 404);
}

// Supprimer les liens
$pdo->prepare("DELETE FROM biens_medias WHERE media_id = ?")->execute([$id]);
$pdo->prepare("DELETE FROM medias WHERE id = ?")->execute([$id]);

// Supprimer le fichier physique
$filePath = __DIR__ . '/../../../public' . $media['chemin'];
if (is_file($filePath)) {
    @unlink($filePath);
}

jsonResponse(['success' => true]);
