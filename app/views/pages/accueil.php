<?php require __DIR__ . '/../partials/header.php'; ?>

<!-- HERO -->
<section class="hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <span class="eyebrow">Agence Immobilière & Conciergerie — Agadir</span>
        <h1>L'art de vivre<br>sur la côte atlantique</h1>
        <p>Villas contemporaines, riads authentiques et services sur-mesure pour une expérience d'exception.</p>
        <div class="hero-actions">
            <a href="<?= BASE_PATH ?>/public/biens" class="btn-gold">Découvrir nos biens</a>
            <a href="<?= BASE_PATH ?>/public/services" class="btn-ghost">Nos services</a>
        </div>
    </div>
    <div class="hero-scroll"><i class="fas fa-chevron-down"></i></div>
</section>

<!-- STATS -->
<section class="stats-band">
    <div class="container">
        <div class="stat">
            <div class="stat-number">15<span>+</span></div>
            <div class="stat-label">Années d'expérience</div>
        </div>
        <div class="stat">
            <div class="stat-number">320</div>
            <div class="stat-label">Biens gérés</div>
        </div>
        <div class="stat">
            <div class="stat-number">99<span>%</span></div>
            <div class="stat-label">Satisfaction</div>
        </div>
        <div class="stat">
            <div class="stat-number">4</div>
            <div class="stat-label">Quartiers d'exception</div>
        </div>
    </div>
</section>

<!-- BIENS -->
<section class="section section-light" id="properties">
    <div class="container">
        <div class="section-header">
            <span class="eyebrow">Nos sélections</span>
            <h2>Biens d'exception</h2>
            <p>Des propriétés uniques, rigoureusement sélectionnées pour leur charme et leur prestige.</p>
        </div>

        <div class="filter-buttons">
            <button class="filter-btn active" data-filter="all">Tous</button>
            <button class="filter-btn" data-filter="vente">À vendre</button>
            <button class="filter-btn" data-filter="location">À louer</button>
        </div>

        <div class="properties-grid">
            <?php if (empty($biens)): ?>
                <p>Aucun bien pour le moment.</p>
            <?php else: ?>
                <?php foreach ($biens as $b): ?>
                    <?php require __DIR__ . '/../biens/carte.php'; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="section-footer">
            <a href="<?= BASE_PATH ?>/public/biens" class="btn-ghost">Voir tous les biens <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<!-- SERVICES -->
<section class="section" id="services">
    <div class="container">
        <div class="section-header">
            <span class="eyebrow">Conciergerie</span>
            <h2>Un service sur-mesure</h2>
            <p>Au-delà de l'immobilier, nous prenons soin de votre quotidien.</p>
        </div>
        <div class="services-grid">
            <article class="service-card">
                <i class="fas fa-building"></i>
                <h3>Transaction immobilière</h3>
                <p>Vente, location et acquisition de biens d'exception à Agadir.</p>
                <a href="<?= BASE_PATH ?>/public/services" class="btn-details">En savoir plus</a>
            </article>
            <article class="service-card">
                <i class="fas fa-concierge-bell"></i>
                <h3>Conciergerie de luxe</h3>
                <p>Gestion locative, accueil et services personnalisés pour votre résidence.</p>
                <a href="<?= BASE_PATH ?>/public/services" class="btn-details">En savoir plus</a>
            </article>
            <article class="service-card">
                <i class="fas fa-key"></i>
                <h3>Gestion de biens</h3>
                <p>Entretien, suivi et valorisation de votre patrimoine immobilier.</p>
                <a href="<?= BASE_PATH ?>/public/services" class="btn-details">En savoir plus</a>
            </article>
        </div>
    </div>
</section>

<!-- A PROPOS -->
<section class="section section-dark" id="about">
    <div class="container about-content">
        <span class="eyebrow">À propos</span>
        <h2>Une vision, une passion</h2>
        <p><strong>Immo Colucci</strong> vous accompagne dans la recherche de biens d'exception à Agadir, avec un service discret et exigeant.</p>
    </div>
</section>

<!-- CONTACT -->
<section class="section section-light" id="contact">
    <div class="container">
        <div class="section-header">
            <span class="eyebrow">Contact</span>
            <h2>Parlons de votre projet</h2>
        </div>
        <div class="contact-wrapper">
            <div class="contact-infos">
                <div class="info-item">
                    <h4><i class="fas fa-map-marker-alt"></i> Adresse</h4>
                    <p>123, Boulevard Mohammed V, Gueliz, Agadir</p>
                </div>
                <div class="info-item">
                    <h4><i class="fas fa-phone-alt"></i> Téléphone</h4>
                    <p>+212 5 28 XX XX XX</p>
                </div>
                <div class="info-item">
                    <h4><i class="fas fa-envelope"></i> Email</h4>
                    <p>contact@immocolucci.ma</p>
                </div>
            </div>
            <?php $csrf = genererCsrf(); ?>
<form class="contact-form" id="contactForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                <input type="text" name="website" class="hp-field" tabindex="-1" autocomplete="off">
                <input type="text" name="name" placeholder="Votre nom" required>
                <input type="email" name="email" placeholder="Votre email" required>
                <input type="tel" name="phone" placeholder="Votre téléphone">
                <select name="subject" required>
                    <option value="">Sujet</option>
                    <option>Achat immobilier</option>
                    <option>Location</option>
                    <option>Conciergerie</option>
                    <option>Autre</option>
                </select>
                <textarea name="message" rows="4" placeholder="Votre message..."></textarea>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane"></i> Envoyer
                </button>
            </form>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../partials/chatbot-widget.php'; ?>
<?php require __DIR__ . '/../partials/footer.php'; ?>
