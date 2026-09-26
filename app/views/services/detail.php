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
            <a href="<?= BASE_PATH ?>/public/services">Nos services</a>
            <span>/</span>
            <span><?= e($service['titre']) ?></span>
        </nav>
    </div>
</section>

<!-- DETAIL SERVICE -->
<section class="detail-bien">
    <div class="container">
        <div class="detail-section">
            <?php if (!empty($service['icone'])): ?>
                <div class="service-detail-icon"><i class="<?= e($service['icone']) ?>"></i></div>
            <?php endif; ?>
            <h1><?= e($service['titre']) ?></h1>
            <div class="detail-description">
                <?= nl2br(e($service['description'] ?? $service['description_courte'] ?? 'Aucune description disponible.')) ?>
            </div>
        </div>
        <div class="detail-back">
            <a href="<?= BASE_PATH ?>/public/services" class="btn-details">
                <i class="fas fa-arrow-left"></i> Retour aux services
            </a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../partials/chatbot-widget.php'; ?>
<?php require __DIR__ . '/../partials/footer.php'; ?>
