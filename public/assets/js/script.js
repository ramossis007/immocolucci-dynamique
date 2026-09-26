
// ============================================================
//  Immo Colucci — Script principal
// ============================================================
(function () {
    'use strict';

    // ========================================================
    // 1. MENU BURGER
    // ========================================================
    const hamburger = document.getElementById('hamburgerBtn');
    const mainNav = document.getElementById('mainNav');

    if (hamburger && mainNav) {
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            mainNav.classList.toggle('open');
            document.body.style.overflow = mainNav.classList.contains('open') ? 'hidden' : '';
        });

        document.querySelectorAll('.nav ul li a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                mainNav.classList.remove('open');
                document.body.style.overflow = '';
            });
        });

        // Fermer au clic en dehors
        document.addEventListener('click', (e) => {
            if (mainNav.classList.contains('open') &&
                !mainNav.contains(e.target) &&
                !hamburger.contains(e.target)) {
                hamburger.classList.remove('active');
                mainNav.classList.remove('open');
                document.body.style.overflow = '';
            }
        });
    }

    // ========================================================
    // 2. HEADER ombre au scroll
    // ========================================================
    const header = document.querySelector('.header');
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
                header.style.background = 'rgba(21, 21, 21, 0.98)';
                header.style.boxShadow = '0 4px 30px rgba(0,0,0,0.3)';
            } else {
                header.style.background = 'rgba(21, 21, 21, 0.92)';
                header.style.boxShadow = 'none';
            }
        });
    }

    // ========================================================
    // 3. FILTRES biens (page /biens avec boutons)
    // ========================================================
    const filterButtons = document.querySelectorAll('.filter-btn');
    const propertyCards = document.querySelectorAll('.property-card');

    if (filterButtons.length && propertyCards.length) {
        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                const filterValue = button.getAttribute('data-filter');

                propertyCards.forEach(card => {
                    const cardType = card.getAttribute('data-type');
                    const visible = (filterValue === 'all' || filterValue === cardType);
                    card.style.display = visible ? 'block' : 'none';
                });
            });
        });
    }

    // ========================================================
    // 4. SLIDER HERO (page accueil)
    // ========================================================
    const heroSection = document.querySelector('.hero');
    const heroContent = document.getElementById('heroContent');

    if (heroSection && heroContent) {
        const slides = [
            { bg: 'https://picsum.photos/seed/agadirHero1/1920/1080',
              subtitle: 'Agence Immobiliere & Conciergerie de Luxe',
              title: "L'Art de Vivre <br />a Agadir",
              desc: "Villas contemporaines, riads authentiques et services sur-mesure." },
            { bg: 'https://picsum.photos/seed/agadirHero2/1920/1080',
              subtitle: 'Immobilier de Prestige',
              title: "Des Proprietes <br />d'Exception",
              desc: "Decouvrez notre selection de biens uniques." },
            { bg: 'https://picsum.photos/seed/agadirHero3/1920/1080',
              subtitle: 'Conciergerie Sur-Mesure',
              title: "Un Service <br />d'Excellence",
              desc: "Confiez-nous votre bien." }
        ];
        let currentSlide = 0;
        let slideInterval = null;

        function updateSlide(index) {
            const slide = slides[index];
            heroSection.style.background = "url('" + slide.bg + "') center/cover no-repeat";
            const sub = heroContent.querySelector('.hero-subtitle');
            const h2 = heroContent.querySelector('h2');
            const des = heroContent.querySelector('.hero-description');
            if (sub) sub.textContent = slide.subtitle;
            if (h2) h2.innerHTML = slide.title;
            if (des) des.textContent = slide.desc;
        }
        function nextSlide() { currentSlide = (currentSlide + 1) % slides.length; updateSlide(currentSlide); }
        function startSlider() { slideInterval = setInterval(nextSlide, 8000); }

        updateSlide(0);
        startSlider();
    }

    // ========================================================
    // 5. COMPTEURS
    // ========================================================
    const statNumbers = document.querySelectorAll('.stat-number');
    const aboutSection = document.getElementById('about');

    if (statNumbers.length && aboutSection) {
        let animated = false;
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !animated) {
                    animated = true;
                    statNumbers.forEach(el => {
                        const target = parseInt(el.getAttribute('data-count'), 10) || 0;
                        let current = 0;
                        const step = target / 50;
                        const timer = setInterval(() => {
                            current += step;
                            if (current >= target) { el.textContent = target; clearInterval(timer); }
                            else el.textContent = Math.floor(current);
                        }, 40);
                    });
                }
            });
        }, { threshold: 0.3 });
        observer.observe(aboutSection);
    }

    // ========================================================
    // 6. FORMULAIRES
    // ========================================================
    function handleForm(formEl) {
        formEl.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = formEl.querySelector('button[type="submit"]');
            if (btn) { btn.disabled = true; btn.dataset.orig = btn.innerHTML; btn.innerHTML = 'Envoi...'; }
            try {
                const res = await fetch((window.BASE_PATH || '') + '/public/contact', {
                    method: 'POST',
                    body: new FormData(formEl)
                });
                const data = await res.json();
                if (data.success) {
                    alert('Merci ! Nous vous contacterons rapidement.');
                    formEl.reset();
                } else {
                    alert(data.message || 'Erreur');
                }
            } catch (err) {
                alert('Erreur reseau. Reessayez plus tard.');
            } finally {
                if (btn) { btn.disabled = false; btn.innerHTML = btn.dataset.orig; }
            }
        });
    }

    document.querySelectorAll('#contactForm, #contactBienForm').forEach(handleForm);

    // ========================================================
    // 7. Liens "#"
    // ========================================================
    document.querySelectorAll('a[href="#"]').forEach(link => {
        link.addEventListener('click', (e) => e.preventDefault());
    });
})();