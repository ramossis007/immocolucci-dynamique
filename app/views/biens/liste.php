<?php require __DIR__ . '/../partials/header.php'; ?>

<!-- BANNIERE -->
<section class="page-banner">
    <div class="page-banner-overlay"></div>
    <div class="container">
        <div class="page-banner-content">
            <nav class="breadcrumb">
                <a href="<?= BASE_PATH ?>/public/">Accueil</a>
                <span>/</span>
                <span>Nos biens</span>
            </nav>
            <h1>Nos Biens d'Exception</h1>
            <p>Villas, riads et appartements soigneusement sélectionnés à Agadir.</p>
        </div>
    </div>
</section>

<!-- FILTRES + LISTE -->
<section class="properties-page">
    <div class="container">
        <form method="GET" class="filters-bar">
            <div class="filter-group">
                <label>Transaction</label>
                <select name="transaction">
                    <option value="">Toutes</option>
                    <option value="vente" <?= ($_GET['transaction'] ?? '') === 'vente' ? 'selected' : '' ?>>Vente</option>
                    <option value="location" <?= ($_GET['transaction'] ?? '') === 'location' ? 'selected' : '' ?>>Location</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Type</label>
                <select name="type">
                    <option value="">Tous</option>
                    <option value="villa" <?= ($_GET['type'] ?? '') === 'villa' ? 'selected' : '' ?>>Villa</option>
                    <option value="riad" <?= ($_GET['type'] ?? '') === 'riad' ? 'selected' : '' ?>>Riad</option>
                    <option value="appartement" <?= ($_GET['type'] ?? '') === 'appartement' ? 'selected' : '' ?>>Appartement</option>
                    <option value="terrain" <?= ($_GET['type'] ?? '') === 'terrain' ? 'selected' : '' ?>>Terrain</option>
                    <option value="local" <?= ($_GET['type'] ?? '') === 'local' ? 'selected' : '' ?>>Local</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Prix max (DH)</label>
                <input type="number" name="prix_max" value="<?= e($_GET['prix_max'] ?? '') ?>" placeholder="Ex: 5000000">
            </div>
            <div class="filter-group">
                <label>Quartier</label>
                <input type="text" name="quartier" value="<?= e($_GET['quartier'] ?? '') ?>" placeholder="Ex: Founty">
            </div>
            <button type="submit" class="btn-search">
                <i class="fas fa-search"></i> Filtrer
            </button>
            <?php if (!empty($_GET)): ?>
                <a href="<?= BASE_PATH ?>/public/biens" class="btn-reset">Réinitialiser</a>
            <?php endif; ?>
        </form>

        <div class="results-count">
            <strong><?= $total ?></strong> bien(s) trouvé(s)
            <?php if ($nbPages > 1): ?> — page <?= $page ?> / <?= $nbPages ?><?php endif; ?>
        </div>

        <?php if (empty($biens)): ?>
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h3>Aucun bien trouvé</h3>
                <p>Essayez de modifier vos critères de recherche.</p>
                <a href="<?= BASE_PATH ?>/public/biens" class="btn-primary">Voir tous les biens</a>
            </div>
        <?php else: ?>
            <div class="properties-grid">
                <?php foreach ($biens as $b): ?>
                    <?php require __DIR__ . '/carte.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($nbPages > 1): ?>
            <?php $query = array_filter($_GET); unset($query['page']); ?>
            <?php $pageBase = BASE_PATH . '/public/biens' . ($query ? '?' . http_build_query($query) : ''); ?>
            <?php $sep = $query ? '&' : '?'; ?>
            <nav class="pagination" aria-label="Pagination">
                <?php if ($page > 1): ?>
                    <a class="page-link page-prev" href="<?= e($pageBase . $sep . 'page=' . ($page - 1)) ?>">
                        <i class="fas fa-chevron-left"></i> Précédent
                    </a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $nbPages; $i++): ?>
                    <?php if ($i === $page): ?>
                        <span class="page-link active"><?= $i ?></span>
                    <?php else: ?>
                        <a class="page-link" href="<?= e($pageBase . $sep . 'page=' . $i) ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($page < $nbPages): ?>
                    <a class="page-link page-next" href="<?= e($pageBase . $sep . 'page=' . ($page + 1)) ?>">
                        Suivant <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../partials/chatbot-widget.php'; ?>
<?php require __DIR__ . '/../partials/footer.php'; ?>
