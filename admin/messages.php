<?php
$rolesRequis = ['admin', 'editeur'];
require_once __DIR__ . '/includes/auth.php';

$pdo  = Database::getInstance()->getPdo();
$csrf = genererCsrf();

// Actions POST (protégées CSRF)
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!verifierCsrf($_POST['csrf_token'] ?? '')) {
        die('Session expirée. Rechargez la page.');
    }
    $id     = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($id > 0 && $action === 'toggle_lu') {
        $pdo->prepare("UPDATE messages SET lu = 1 - lu WHERE id = ?")->execute([$id]);
        redirect(BASE_PATH . '/admin/messages.php');
    }
    if ($id > 0 && $action === 'supprimer') {
        $pdo->prepare("DELETE FROM messages WHERE id = ?")->execute([$id]);
        redirect(BASE_PATH . '/admin/messages.php');
    }
}

// Détail éventuel (+ marquage lu automatique à l'ouverture)
$message = null;
$bienLie = null;
if (!empty($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $message = $stmt->fetch() ?: null;

    if ($message) {
        if (!empty($message['bien_id'])) {
            $stmt = $pdo->prepare("SELECT titre, slug FROM biens WHERE id = ?");
            $stmt->execute([(int)$message['bien_id']]);
            $bienLie = $stmt->fetch() ?: null;
        }
        if (!$message['lu']) {
            $pdo->prepare("UPDATE messages SET lu = 1 WHERE id = ?")->execute([(int)$message['id']]);
            $message['lu'] = 1;
        }
    }
}

$messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
$nonLus   = (int)$pdo->query("SELECT COUNT(*) FROM messages WHERE lu = 0")->fetchColumn();

require __DIR__ . '/includes/header.php';
?>

<h1>Messages</h1>
<div class="page-header">
    <p><strong><?= $nonLus ?></strong> non lu(s) sur <?= count($messages) ?></p>
</div>

<?php if ($message): ?>
    <div class="message-detail">
        <a href="<?= BASE_PATH ?>/admin/messages.php" class="btn-primary">&larr; Retour à la liste</a>
        <h2><?= e($message['sujet']) ?></h2>
        <p>De : <strong><?= e($message['nom']) ?></strong> &lt;<?= e($message['email']) ?>&gt;
           <?= !empty($message['telephone']) ? ' — ' . e($message['telephone']) : '' ?></p>
        <p>Reçu le : <?= date('d/m/Y H:i', strtotime($message['created_at'])) ?></p>
        <?php if ($bienLie): ?>
            <p>Bien concerné :
                <a href="<?= BASE_PATH ?>/public/bien/<?= e($bienLie['slug']) ?>"><?= e($bienLie['titre']) ?></a>
            </p>
        <?php endif; ?>

        <div class="message-body"><?= nl2br(e($message['message'])) ?></div>

        <div class="message-actions">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                <input type="hidden" name="id" value="<?= (int)$message['id'] ?>">
                <button type="submit" name="action" value="toggle_lu" class="btn-primary">Marquer comme non lu</button>
                <button type="submit" name="action" value="supprimer" class="btn-danger">Supprimer</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <table class="admin-table">
        <thead>
            <tr><th>Nom</th><th>Email</th><th>Sujet</th><th>Date</th><th>Statut</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php if (empty($messages)): ?>
                <tr><td colspan="6">Aucun message.</td></tr>
            <?php else: ?>
                <?php foreach ($messages as $m): ?>
                <tr>
                    <td><?= e($m['nom']) ?></td>
                    <td><?= e($m['email']) ?></td>
                    <td><?= e($m['sujet']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></td>
                    <td>
                        <?php if ($m['lu']): ?>
                            <span class="badge badge-lu">Lu</span>
                        <?php else: ?>
                            <span class="badge badge-nonlu">Non lu</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a class="link-btn" href="<?= BASE_PATH ?>/admin/messages.php?id=<?= (int)$m['id'] ?>">Voir</a>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                            <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                            <button type="submit" name="action" value="toggle_lu" class="link-btn"><?= $m['lu'] ? 'Non lu' : 'Lu' ?></button>
                            <button type="submit" name="action" value="supprimer" class="link-btn danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
