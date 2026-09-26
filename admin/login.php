<?php
session_start();
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/security.php';
require_once __DIR__ . '/../app/helpers/functions.php';

// Si déjà connecté
if (!empty($_SESSION['utilisateur_id'])) {
    redirect(BASE_PATH . '/admin/index.php');
}

$erreur = '';
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$blocage = throttleRestant('login:' . $ip);

if ($blocage > 0) {
    $erreur = 'Trop de tentatives. Réessayez dans ' . ceil($blocage / 60) . ' min.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification CSRF
    if (!verifierCsrf($_POST['csrf_token'] ?? '')) {
        $erreur = 'Session expirée, veuillez réessayer.';
    } else {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $mdp   = $_POST['mot_de_passe'] ?? '';

        if ($email && $mdp !== '') {
            $stmt = Database::getInstance()->getPdo()
                ->prepare("SELECT * FROM utilisateurs WHERE email = :email AND actif = 1 LIMIT 1");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($mdp, $user['mot_de_passe'])) {
                throttleSucces('login:' . $ip);

                // Régénération de session (anti-fixation)
                session_regenerate_id(true);

                $_SESSION['utilisateur_id']   = (int)$user['id'];
                $_SESSION['utilisateur_nom']  = $user['nom'];
                $_SESSION['utilisateur_role'] = $user['role'];
                $_SESSION['csrf_token']       = bin2hex(random_bytes(32));

                // Mise à jour dernière connexion
                Database::getInstance()->getPdo()
                    ->prepare("UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = ?")
                    ->execute([$user['id']]);

                redirect(BASE_PATH . '/admin/index.php');
            }
        }
        throttleEchec('login:' . $ip);
        $erreur = 'Identifiants incorrects.';
    }
}
$csrf = genererCsrf();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Immo Colucci Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/assets/css/admin.css">
</head>
<body class="login-page">
    <form method="POST" class="login-form" autocomplete="off">
        <h1>Immo<span>Colucci</span></h1>
        <p>Administration</p>

        <?php if ($erreur): ?>
            <div class="alert-error"><?= e($erreur) ?></div>
        <?php endif; ?>

        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
        <input type="email" name="email" placeholder="Email" required autofocus>
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
