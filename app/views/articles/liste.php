<?php require __DIR__ . '/../partials/header.php'; ?>

<!-- BANNIERE -->
<section class="page-banner">
    <div class="page-banner-overlay"></div>
    <div class="container">
        <div class="page-banner-content">
            <nav class="breadcrumb">
                <a href="<?= BASE_PATH ?>/public/">Accueil</a>
                <span>/</span>
                <span>Blog</span>
            </nav>
            <h1>Blog</h1>
            <p>Actualités, conseils et tendances de l'immobilier à Agadir.</p>
        </div>
    </div>
</section>

<!-- LISTE ARTICLES -->
<section class="properties-page">
    <div class="container">
        <?php if (empty($articles)): ?>
            <div class="empty-state">
                <i class="fas fa-newspaper"></i>
                <h3>Aucun article disponible</h3>
                <p>Revenez bientôt pour découvrir nos actualités.</p>
                <a href="<?= BASE_PATH ?>/public/" class="btn-primary">Retour à l'accueil</a>
            </div>
        <?php else: ?>
            <div class="services-grid">
                <?php foreach ($articles as $a): ?>
                    <?php $articleDate = $a['publie_le'] ?: ($a['created_at'] ?? null); ?>
                    <article class="service-card">
                        <?php if ($articleDate): ?>
                            <p class="article-date"><?= e(date('d/m/Y', strtotime($articleDate))) ?></p>
                        <?php endif; ?>
                        <h3><?= e($a['titre']) ?></h3>
                        <p><?= e($a['extrait'] ?? '') ?></p>
                        <a href="<?= BASE_PATH ?>/public/article/<?= e($a['slug']) ?>" class="btn-details">Lire la suite</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../partials/chatbot-widget.php'; ?>
<?php require __DIR__ . '/../partials/footer.php'; ?>
