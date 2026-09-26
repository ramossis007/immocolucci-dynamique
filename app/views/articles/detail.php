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
            <a href="<?= BASE_PATH ?>/public/blog">Blog</a>
            <span>/</span>
            <span><?= e($article['titre']) ?></span>
        </nav>
    </div>
</section>

<!-- DETAIL ARTICLE -->
<section class="detail-bien">
    <div class="container">
        <div class="detail-section">
            <?php $articleDate = $article['publie_le'] ?: ($article['created_at'] ?? null); ?>
            <?php if ($articleDate): ?>
                <p class="article-date"><?= e(date('d/m/Y', strtotime($articleDate))) ?></p>
            <?php endif; ?>
            <h1><?= e($article['titre']) ?></h1>
            <div class="detail-description">
                <?= nl2br(e($article['contenu'] ?? $article['extrait'] ?? 'Aucun contenu disponible.')) ?>
            </div>
        </div>
        <div class="detail-back">
            <a href="<?= BASE_PATH ?>/public/blog" class="btn-details">
                <i class="fas fa-arrow-left"></i> Retour au blog
            </a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../partials/chatbot-widget.php'; ?>
<?php require __DIR__ . '/../partials/footer.php'; ?>
