</main>
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <h2>Immo<span>Colucci</span></h2>
                <p>Agence immobilière & conciergerie de luxe à Agadir.</p>
            </div>
            <div class="footer-col">
                <h4>Navigation</h4>
                <ul>
                    <li><a href="<?= BASE_PATH ?>/public/">Accueil</a></li>
                    <li><a href="<?= BASE_PATH ?>/public/biens">Biens</a></li>
                    <li><a href="<?= BASE_PATH ?>/public/services">Services</a></li>
                    <li><a href="<?= BASE_PATH ?>/public/blog">Blog</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <ul>
                    <li><a href="<?= BASE_PATH ?>/public/#contact">Formulaire de contact</a></li>
                    <li><a href="mailto:contact@immocolucci.ma">contact@immocolucci.ma</a></li>
                    <li><a href="tel:+212528000000">+212 5 28 XX XX XX</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Agence</h4>
                <ul>
                    <li><a href="<?= BASE_PATH ?>/public/#about">À propos</a></li>
                    <li><a href="#">Mentions légales</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; <?= date('Y') ?> Immo Colucci. Tous droits réservés.</span>
            <span>Agadir, Maroc</span>
        </div>
    </div>
</footer>
<script>window.BASE_PATH = '<?= BASE_PATH ?>';</script>
<script src="<?= BASE_PATH ?>/public/assets/js/script.js"></script>
<script src="<?= BASE_PATH ?>/public/assets/js/chatbot.js"></script>
</body>
</html>
