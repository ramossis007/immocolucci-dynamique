<?php
require_once __DIR__ . '/includes/auth.php';
$pdo = Database::getInstance()->getPdo();

// Actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifierCsrf($_POST['csrf_token'] ?? '')) {
    if (($_POST['action'] ?? '') === 'delete') {
        $pdo->prepare("DELETE FROM biens WHERE id = ?")->execute([(int)$_POST['id']]);
        redirect(BASE_PATH . '/admin/biens.php?msg=supprime');
    }
    if (($_POST['action'] ?? '') === 'toggle') {
        $pdo->prepare("UPDATE biens SET publie = NOT publie WHERE id = ?")->execute([(int)$_POST['id']]);
        redirect(BASE_PATH . '/admin/biens.php?msg=toggle');
    }
}

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

$total = (int)$pdo->query("SELECT COUNT(*) FROM biens")->fetchColumn();
$pages = max(1, (int)ceil($total / $limit));

$stmt = $pdo->prepare(
    "SELECT b.*,
        (SELECT chemin FROM medias m
         JOIN biens_medias bm ON m.id = bm.media_id
         WHERE bm.bien_id = b.id AND bm.est_principale = 1 LIMIT 1) AS image
     FROM biens b
     ORDER BY b.created_at DESC
     LIMIT :limit OFFSET :offset"
);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$biens = $stmt->fetchAll();

$csrf = genererCsrf();
require __DIR__ . '/includes/header.php';
?>
<div class="page-header">
    <h1>Biens immobiliers</h1>
    <a href="<?= BASE_PATH ?>/admin/bien-edit.php" class="btn-primary">+ Ajouter un bien</a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert-success">Operation effectuee.</div>
<?php endif; ?>

<?php if (empty($biens)): ?>
    <p>Aucun bien pour le moment.</p>
<?php else: ?>
<table class="admin-table">
    <thead>
        <td><img src="<?= e($b['image'] ?? BASE_PATH . '/assets/img/placeholder.jpg') ?>" width="60"></td>
    </thead>
    <tbody>
        <?php foreach ($biens as $b): ?>
        <tr>
            <td><img src="<?= e($b['image'] ?? BASE_PATH . '/assets/img/placeholder.jpg') ?>" width="60"></td>
            <td><?= e($b['titre']) ?></td>
            <td><?= e($b['type_bien']) ?></td>
            <td><?= e($b['transaction_type']) ?></td>
            <td><?= number_format((float)$b['prix'], 0, ',', ' ') ?> DH</td>
            <td><span class="badge"><?= $b['publie'] ? 'Publie' : 'Brouillon' ?></span></td>
            <td>
                <a href="<?= BASE_PATH ?>/admin/bien-edit.php?id=<?= (int)$b['id'] ?>" class="link-btn">Éditer</a>
                <form method="POST" style="display:inline">
                    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                    <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                    <input type="hidden" name="action" value="toggle">
                    <button type="submit" class="link-btn"><?= $b['publie'] ? 'Depublier' : 'Publier' ?></button>
                </form>
                <form method="POST" style="display:inline" onsubmit="return confirm('Supprimer ?')">
                    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                    <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="link-btn danger">Supprimer</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>