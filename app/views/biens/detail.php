<?php
$css = 'detail';
require __DIR__ . '/../partials/header.php';
?>

<!-- BANNIERE -->
<section class="page-banner-detail">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?= BASE_PATH ?>/public/">Accueil</a>
            <span>/</span>
            <a href="<?= BASE_PATH ?>/public/biens">Nos biens</a>
            <span>/</span>
            <span><?= e($bien['titre']) ?></span>
        </nav>
    </div>
</section>

<!-- DETAIL BIEN -->
<section class="detail-bien">
    <div class="container">
        <div class="detail-grid">
            <!-- GALERIE PHOTOS -->
            <div class="detail-gallery">
                <?php if (!empty($medias)): ?>
                    <div class="gallery-main">
                        <img id="mainImage" src="<?= e($medias[0]['chemin']) ?>" alt="<?= e($bien['titre']) ?>">
                    </div>
                    <?php if (count($medias) > 1): ?>
                        <div class="gallery-thumbs">
                            <?php foreach ($medias as $i => $m): ?>
                                <img src="<?= e($m['chemin']) ?>" 
                                     alt="Photo <?= $i + 1 ?>"
                                     onclick="document.getElementById('mainImage').src=this.src"
                                     class="<?= $i === 0 ? 'active' : '' ?>">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="gallery-main">
                        <img src="<?= BASE_PATH ?>/public/assets/img/placeholder.png" alt="<?= e($bien['titre']) ?>">
                    </div>
                <?php endif; ?>
            </div>

            <!-- INFOS BIEN -->
            <div class="detail-info">
                <span class="badge badge-<?= $bien['transaction_type'] === 'vente' ? 'sale' : 'rent' ?>">
                    <?= $bien['transaction_type'] === 'vente' ? 'Vente' : 'Location' ?>
                </span>
                <?php if (!empty($bien['vedette'])): ?>
                    <span class="badge-vedette"><i class="fas fa-star"></i> Coup de cœur</span>
                <?php endif; ?>

                <h1><?= e($bien['titre']) ?></h1>
                <p class="detail-location">
                    <i class="fas fa-map-pin"></i>
                    <?= e($bien['quartier']) ?>, <?= e($bien['ville']) ?>
                </p>
                <p class="detail-price">
                    <?= number_format((float)$bien['prix'], 0, ',', ' ') ?> DH
                    <?= $bien['transaction_type'] === 'location' ? ' / mois' : '' ?>
                </p>

                <div class="detail-features">
                    <?php if (!empty($bien['chambres'])): ?>
                        <div><i class="fas fa-bed"></i> <strong><?= (int)$bien['chambres'] ?></strong> chambres</div>
                    <?php endif; ?>
                    <?php if (!empty($bien['salles_bain'])): ?>
                        <div><i class="fas fa-bath"></i> <strong><?= (int)$bien['salles_bain'] ?></strong> salles de bain</div>
                    <?php endif; ?>
                    <?php if (!empty($bien['surface_habitable'])): ?>
                        <div><i class="fas fa-vector-square"></i> <strong><?= (int)$bien['surface_habitable'] ?></strong> m² habitables</div>
                    <?php endif; ?>
                    <div><i class="fas fa-tag"></i> Type : <strong><?= e($bien['type_bien']) ?></strong></div>
                </div>

                <div class="detail-actions">
                    <a href="#contact-bien" class="btn-primary">
                        <i class="fas fa-phone-alt"></i> Demander une visite
                    </a>
                </div>
            </div>
        </div>

        <!-- DESCRIPTION -->
        <div class="detail-section">
            <h2>Présentation de la propriété</h2>
            <div class="detail-description">
                <?= nl2br(e($bien['description'] ?? 'Aucune description disponible.')) ?>
            </div>
        </div>

        <!-- EQUIPEMENTS -->
        <?php if (!empty($bien['equipements'])): ?>
            <?php
            $equipArr = json_decode($bien['equipements'], true);
            if (!is_array($equipArr)) $equipArr = [];
            ?>
            <?php if (!empty($equipArr)): ?>
            <div class="detail-section">
                <h2><i class="fas fa-list-ul"></i> Équipements</h2>
                <ul class="equipements-list">
                    <?php foreach ($equipArr as $equip): ?>
                        <li><i class="fas fa-check-circle"></i> <?= e($equip) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- FORMULAIRE CONTACT -->
        <div class="detail-section contact-bien" id="contact-bien">
            <h2><i class="fas fa-pen"></i> Vous souhaitez visiter ce bien ?</h2>
            <p>Laissez-nous vos coordonnées, nous vous recontactons dans l'heure.</p>

            <?php $csrf = genererCsrf(); ?>
<form class="contact-form" id="contactBienForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                <input type="text" name="website" class="hp-field" tabindex="-1" autocomplete="off">
                <div class="form-row-2">
                    <input type="text" name="name" placeholder="Votre nom" required>
                    <input type="email" name="email" placeholder="Votre email" required>
                </div>
                <div class="form-row-2">
                    <input type="tel" name="phone" placeholder="Votre téléphone">
                    <select name="subject" required>
                        <option value="">Disponibilité pour visite</option>
                        <option>Cette semaine</option>
                        <option>La semaine prochaine</option>
                        <option>Dans les 15 jours</option>
                        <option>À définir</option>
                    </select>
                </div>
                <textarea name="message" rows="3" placeholder="Votre message (optionnel)"></textarea>
                <input type="hidden" name="bien_id" value="<?= (int)$bien['id'] ?>">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane"></i> Envoyer ma demande
                </button>
            </form>
        </div>

        <div class="detail-back">
            <a href="<?= BASE_PATH ?>/public/biens" class="btn-details">
                <i class="fas fa-arrow-left"></i> Retour à nos biens
            </a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../partials/chatbot-widget.php'; ?>
<?php require __DIR__ . '/../partials/footer.php'; ?>