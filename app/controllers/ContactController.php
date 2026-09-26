<?php
class ContactController {
    public function index() {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }

        // 1. Honeypot anti-bot : champ invisible qui doit rester vide
        if (!empty($_POST['website'])) {
            jsonResponse(['success' => true]);
        }

        // 2. Throttle anti-spam : 5 envois max / 10 min / IP
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        if (throttleRestant('contact:' . $ip, 5, 600) > 0) {
            jsonResponse(['success' => false, 'message' => 'Trop de messages. Réessayez plus tard.'], 429);
        }

        // 3. CSRF
        if (!verifierCsrf($_POST['csrf_token'] ?? '')) {
            jsonResponse(['success' => false, 'message' => 'Session expirée, rechargez la page.'], 403);
        }

        $nom     = trim($_POST['name']    ?? '');
        $email   = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $phone   = trim($_POST['phone']   ?? '');
        $sujet   = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $bien_id = !empty($_POST['bien_id']) ? (int)$_POST['bien_id'] : null;

        if (!$nom || !$email || !$sujet) {
            jsonResponse(['success' => false, 'message' => 'Champs obligatoires manquants'], 400);
        }

        try {
            $pdo = Database::getInstance()->getPdo();
            $stmt = $pdo->prepare(
                "INSERT INTO messages (nom, email, telephone, sujet, message, bien_id, lu, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, 0, NOW())"
            );
            $stmt->execute([$nom, $email, $phone, $sujet, $message, $bien_id]);

            throttleEchec('contact:' . $ip, 5, 600);

            jsonResponse(['success' => true]);
        } catch (Throwable $e) {
            error_log('Contact : ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }
}
