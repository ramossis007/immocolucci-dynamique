<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../app/helpers/upload.php';

$pdo = Database::getInstance()->getPdo();
$message = '';
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fichiers'])) {
    if (!verifierCsrf($_POST['csrf_token'] ?? '')) {
        $erreurs[] = 'Session expiree.';
    } else {
        $resultats = uploadMultiple($_FILES['fichiers'], 'general');
        $success = 0;
        foreach ($resultats as $r) {
            if (!empty($r['success'])) {
                $stmt = $pdo->prepare(
                    "INSERT INTO medias (nom_fichier, chemin, type_mime, taille, alt_text, dossier, uploade_par)
                     VALUES (?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute([
                    $r['nom'],
                    $r['chemin'],
                    $r['type'],
                    $r['taille'],
                    trim($_POST['alt'] ?? ''),
                    'general',
                    $_SESSION['utilisateur_id']
                ]);
                $success++;
            } else {
                $erreurs[] = "Echec : " . ($r['nom'] ?? '?') . " - " . ($r['erreur'] ?? '');
            }
        }
        if ($success) $message = "$success fichier(s) uploade(s) avec succes.";
    }
}

$medias = $pdo->query("SELECT * FROM medias ORDER BY id DESC LIMIT 100")->fetchAll();
$csrf = genererCsrf();
require __DIR__ . '/includes/header.php';
?>
<h1>Bibliotheque de medias</h1>

<?php if ($message): ?>
    <div class="alert-success"><?= e($message) ?></div>
<?php endif; ?>
<?php foreach ($erreurs as $err): ?>
    <div class="alert-error"><?= e($err) ?></div>
<?php endforeach; ?>

<form method="POST" enctype="multipart/form-data" class="upload-zone" id="uploadForm">
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <input type="file" name="fichiers[]" multiple accept="image/*" id="fileInput" hidden>
    <div class="upload-placeholder" onclick="document.getElementById('fileInput').click()">
        <i class="fas fa-cloud-upload-alt"></i>
        <p>Cliquez ou glissez-deposez vos images ici (JPG, PNG, WEBP, GIF - max 5 Mo)</p>
    </div>
    <input type="text" name="alt" placeholder="Texte alternatif (optionnel)">
</form>

<script>
document.getElementById('fileInput').addEventListener('change', function() {
    if (this.files.length > 0) {
        document.getElementById('uploadForm').submit();
    }
});
</script>

<div class="media-grid">
    <?php foreach ($medias as $m): ?>
    <div class="media-item" data-id="<?= (int)$m['id'] ?>">
        <img src="<?= e($m['chemin']) ?>" alt="<?= e($m['alt_text'] ?? '') ?>" loading="lazy">
        <div class="media-info">
            <span><?= e($m['nom_fichier']) ?></span>
            <small><?= round($m['taille'] / 1024) ?> Ko</small>
        </div>
        <button type="button" class="btn-delete" onclick="supprimerMedia(<?= (int)$m['id'] ?>)">×</button>
    </div>
    <?php endforeach; ?>
</div>

<script>
const CSRF_TOKEN = '<?= e($csrf) ?>';
async function supprimerMedia(id) {
    if (!confirm('Supprimer ce media ?')) return;
    try {
        const res = await fetch('<?= BASE_PATH ?>/admin/api/media-delete.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id, csrf_token: CSRF_TOKEN})
        });
        const data = await res.json();
        if (data.success) {
            document.querySelector('.media-item[data-id="' + id + '"]')?.remove();
        } else {
            alert(data.message || 'Erreur');
        }
    } catch (e) {
        alert('Erreur reseau.');
    }
}
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>