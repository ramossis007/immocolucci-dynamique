<div class="property-card" data-type="<?= e($b['transaction_type']) ?>">
    <a href="<?= BASE_PATH ?>/public/bien/<?= e($b['slug']) ?>" class="card-link">
        <div class="card-image">
            <img src="<?= e($b['image_principale'] ?? BASE_PATH . '/public/assets/img/placeholder.png') ?>" 
                 alt="<?= e($b['titre']) ?>" loading="lazy">
            <span class="badge badge-<?= $b['transaction_type'] === 'vente' ? 'sale' : 'rent' ?>">
                <?= $b['transaction_type'] === 'vente' ? 'Vente' : 'Location' ?>
            </span>
            <?php if (!empty($b['vedette'])): ?>
                <span class="badge-vedette"><i class="fas fa-star"></i> Coup de cœur</span>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <h3><?= e($b['titre']) ?></h3>
            <p class="location">
                <i class="fas fa-map-pin"></i>
                <?= e($b['quartier']) ?>, <?= e($b['ville']) ?>
            </p>
            <p class="price"><?= number_format((float)$b['prix'], 0, ',', ' ') ?> DH<?= $b['transaction_type'] === 'location' ? ' / mois' : '' ?></p>
            <div class="features">
                <?php if (!empty($b['chambres'])): ?>
                    <span><i class="fas fa-bed"></i> <?= (int)$b['chambres'] ?> ch.</span>
                <?php endif; ?>
                <?php if (!empty($b['salles_bain'])): ?>
                    <span><i class="fas fa-bath"></i> <?= (int)$b['salles_bain'] ?> sdb</span>
                <?php endif; ?>
                <?php if (!empty($b['surface_habitable'])): ?>
                    <span><i class="fas fa-vector-square"></i> <?= (int)$b['surface_habitable'] ?> m²</span>
                <?php endif; ?>
            </div>
        </div>
    </a>
</div>