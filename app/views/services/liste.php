<?php require __DIR__ . '/../partials/header.php'; ?>

<!-- BANNIERE -->
<section class="page-banner">
    <div class="page-banner-overlay"></div>
    <div class="container">
        <div class="page-banner-content">
            <nav class="breadcrumb">
                <a href="<?= BASE_PATH ?>/public/">Accueil</a>
                <span>/</span>
                <span>Nos services</span>
            </nav>
            <h1>Nos Services</h1>
            <p>Immobilier, conciergerie et gestion de biens à Agadir.</p>
        </div>
    </div>
</section>

<!-- LISTE SERVICES -->
<section class="properties-page">
    <div class="container">
        <?php if (empty($services)): ?>
            <div class="empty-state">
                <i class="fas fa-concierge-bell"></i>
                <h3>Aucun service disponible</h3>
                <p>Revenez bientôt pour découvrir nos prestations.</p>
                <a href="<?= BASE_PATH ?>/public/" class="btn-primary">Retour à l'accueil</a>
            </div>
        <?php else: ?>
            <div class="services-grid">
                <?php foreach ($services as $s): ?>
                    <article class="service-card">
                        <?php if (!empty($s['icone'])): ?>
                            <i class="<?= e($s['icone']) ?>"></i>
                        <?php endif; ?>
                        <h3><?= e($s['titre']) ?></h3>
                        <p><?= e($s['description_courte'] ?? '') ?></p>
                        <a href="<?= BASE_PATH ?>/public/service/<?= e($s['slug']) ?>" class="btn-details">En savoir plus</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../partials/chatbot-widget.php'; ?>
<?php require __DIR__ . '/../partials/footer.php'; ?>
