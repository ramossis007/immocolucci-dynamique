<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../app/models/Bien.php';
require_once __DIR__ . '/../app/helpers/upload.php';
$pdo = Database::getInstance()->getPdo();

$id = (int)($_GET['id'] ?? 0);
$bien = null;
$erreur = '';
$mediasAssocies = [];
$tousMedias = [];

// Charger le bien si modification
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM biens WHERE id = ?");
    $stmt->execute([$id]);
    $bien = $stmt->fetch();
    if (!$bien) {
        redirect(BASE_PATH . '/admin/biens.php');
    }
    // Charger les médias associés
    $stmt = $pdo->prepare("
        SELECT m.*, bm.est_principale, bm.ordre
        FROM medias m
        JOIN biens_medias bm ON m.id = bm.media_id
        WHERE bm.bien_id = ?
        ORDER BY bm.est_principale DESC, bm.ordre ASC
    ");
    $stmt->execute([$id]);
    $mediasAssocies = $stmt->fetchAll();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifierCsrf($_POST['csrf_token'] ?? '')) {
        $erreur = 'Session expiree.';
    } else {
        $titre = trim($_POST['titre'] ?? '');
        if ($titre === '') {
            $erreur = 'Le titre est obligatoire.';
        } else {
            $equipStr = trim($_POST['equipements'] ?? '');
            $equipArr = array_filter(array_map('trim', explode(',', $equipStr)));
            $equipementsJson = json_encode(array_values($equipArr));

            $data = [
                'titre'             => $titre,
                'description'       => trim($_POST['description'] ?? ''),
                'type_bien'         => $_POST['type_bien'] ?? 'villa',
                'transaction_type'  => $_POST['transaction_type'] ?? 'vente',
                'statut'            => $_POST['statut'] ?? 'disponible',
                'prix'              => (float)($_POST['prix'] ?? 0),
                'surface_habitable' => (int)($_POST['surface_habitable'] ?? 0),
                'chambres'          => (int)($_POST['chambres'] ?? 0),
                'salles_bain'       => (int)($_POST['salles_bain'] ?? 0),
                'quartier'          => trim($_POST['quartier'] ?? ''),
                'ville'             => trim($_POST['ville'] ?? 'Agadir'),
                'equipements'       => $equipementsJson,
                'vedette'           => isset($_POST['vedette']) ? 1 : 0,
                'publie'            => isset($_POST['publie']) ? 1 : 0,
            ];

            $bienModel = new Bien();

            if ($id > 0) {
                $data['slug'] = $bienModel->genererSlug($data['titre']);
                $data['id'] = $id;
                $sql = "UPDATE biens SET
                    titre = :titre, slug = :slug, description = :description,
                    type_bien = :type_bien, transaction_type = :transaction_type,
                    statut = :statut, prix = :prix,
                    surface_habitable = :surface_habitable,
                    chambres = :chambres, salles_bain = :salles_bain,
                    quartier = :quartier, ville = :ville,
                    equipements = :equipements,
                    vedette = :vedette, publie = :publie
                    WHERE id = :id";
                $pdo->prepare($sql)->execute($data);
            } else {
                $data['slug'] = $bienModel->genererSlug($data['titre']);
                $sql = "INSERT INTO biens
                    (titre, slug, description, type_bien, transaction_type,
                     statut, prix, surface_habitable, chambres, salles_bain,
                     quartier, ville, equipements, vedette, publie, created_at)
                    VALUES
                    (:titre, :slug, :description, :type_bien, :transaction_type,
                     :statut, :prix, :surface_habitable, :chambres, :salles_bain,
                     :quartier, :ville, :equipements, :vedette, :publie, NOW())";
                $pdo->prepare($sql)->execute($data);
                $id = (int)$pdo->lastInsertId();
            }

            // Upload de nouvelles photos
            if (!empty($_FILES['photos']['name'][0])) {
                $resultats = uploadMultiple($_FILES['photos'], 'biens');
                foreach ($resultats as $r) {
                    if (!empty($r['success'])) {
                        // Enregistrer le média
                        $stmt = $pdo->prepare("INSERT INTO medias (nom_fichier, chemin, type_mime, taille, alt_text, dossier, uploade_par) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$r['nom'], $r['chemin'], $r['type'], $r['taille'], $titre, 'biens', $_SESSION['utilisateur_id']]);
                        $mediaId = (int)$pdo->lastInsertId();

                        // Lier au bien
                        $stmt = $pdo->prepare("INSERT INTO biens_medias (bien_id, media_id, est_principale, ordre) VALUES (?, ?, 0, 999)");
                        $stmt->execute([$id, $mediaId]);
                    }
                }
            }

            // Gérer les médias sélectionnés / principale
            if (!empty($_POST['medias_existants'])) {
                foreach ($_POST['medias_existants'] as $mediaId) {
                    $mediaId = (int)$mediaId;
                    $estPrincipale = ((int)($_POST['media_principale'] ?? 0) === $mediaId) ? 1 : 0;
                    $stmt = $pdo->prepare("UPDATE biens_medias SET est_principale = ? WHERE bien_id = ? AND media_id = ?");
                    $stmt->execute([$estPrincipale, $id, $mediaId]);
                }
            }

            redirect(BASE_PATH . '/admin/bien-edit.php?id=' . $id . '&msg=ok');
        }
    }
}

$csrf = genererCsrf();
require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1><?= $id > 0 ? 'Modifier' : 'Ajouter' ?> un bien</h1>
    <a href="<?= BASE_PATH ?>/admin/biens.php" class="btn-primary">← Retour</a>
</div>

<?php if ($erreur): ?><div class="alert-error"><?= e($erreur) ?></div><?php endif; ?>
<?php if (!empty($_GET['msg'])): ?><div class="alert-success">Bien enregistre.</div><?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="form-bien">
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

    <div class="form-row">
        <label>Titre *</label>
        <input type="text" name="titre" value="<?= e($bien['titre'] ?? '') ?>" required>
    </div>

    <div class="form-row">
        <label>Description</label>
        <textarea name="description" rows="5"><?= e($bien['description'] ?? '') ?></textarea>
    </div>

    <div class="form-row-3">
        <div>
            <label>Type de bien</label>
            <select name="type_bien">
                <?php foreach (['villa','riad','appartement','terrain','local'] as $t): ?>
                    <option value="<?= $t ?>" <?= ($bien['type_bien'] ?? '') === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>Transaction</label>
            <select name="transaction_type">
                <option value="vente" <?= ($bien['transaction_type'] ?? '') === 'vente' ? 'selected' : '' ?>>Vente</option>
                <option value="location" <?= ($bien['transaction_type'] ?? '') === 'location' ? 'selected' : '' ?>>Location</option>
            </select>
        </div>
        <div>
            <label>Statut</label>
            <select name="statut">
                <?php foreach (['disponible','reserve','vendu','loue'] as $s): ?>
                    <option value="<?= $s ?>" <?= ($bien['statut'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-row-4">
        <div><label>Prix (DH)</label><input type="number" name="prix" value="<?= e($bien['prix'] ?? '0') ?>"></div>
        <div><label>Surface habitable (m2)</label><input type="number" name="surface_habitable" value="<?= e($bien['surface_habitable'] ?? '0') ?>"></div>
        <div><label>Chambres</label><input type="number" name="chambres" value="<?= e($bien['chambres'] ?? '0') ?>"></div>
        <div><label>Salles de bain</label><input type="number" name="salles_bain" value="<?= e($bien['salles_bain'] ?? '0') ?>"></div>
    </div>

    <div class="form-row-2">
        <div><label>Quartier</label><input type="text" name="quartier" value="<?= e($bien['quartier'] ?? '') ?>"></div>
        <div><label>Ville</label><input type="text" name="ville" value="<?= e($bien['ville'] ?? 'Agadir') ?>"></div>
    </div>

    <div class="form-row">
        <label>Equipements (separes par virgules)</label>
        <textarea name="equipements" rows="3"><?php
            $equip = $bien['equipements'] ?? '';
            $equipArr = json_decode($equip, true);
            echo e(is_array($equipArr) ? implode(', ', $equipArr) : $equip);
        ?></textarea>
    </div>

    <div class="form-row-checkbox">
        <label><input type="checkbox" name="publie" <?= ($bien['publie'] ?? 1) ? 'checked' : '' ?>> Publier</label>
        <label><input type="checkbox" name="vedette" <?= ($bien['vedette'] ?? 0) ? 'checked' : '' ?>> Mettre en vedette</label>
    </div>

    <!-- PHOTOS -->
    <div class="form-section">
        <h3>📸 Photos du bien (max 20)</h3>

        <?php if (!empty($mediasAssocies)): ?>
        <div class="medias-associes">
            <?php foreach ($mediasAssocies as $m): ?>
            <div class="media-item-select">
                <img src="<?= e($m['chemin']) ?>" alt="">
                <label>
                    <input type="checkbox" name="medias_existants[]" value="<?= (int)$m['id'] ?>" checked>
                    Garder
                </label>
                <label>
                    <input type="radio" name="media_principale" value="<?= (int)$m['id'] ?>" <?= $m['est_principale'] ? 'checked' : '' ?>>
                    Principale
                </label>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="upload-photos">
            <label>Ajouter de nouvelles photos</label>
            <input type="file" name="photos[]" multiple accept="image/*">
            <small>Tu peux sélectionner plusieurs images à la fois</small>
        </div>
    </div>

    <button type="submit" class="btn-primary">Enregistrer</button>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>