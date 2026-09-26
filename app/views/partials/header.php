<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="data:,">
    <title>Immo Colucci - Immobilier & Conciergerie</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/assets/css/chatbot.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <?php if (!empty($css)): ?>
        <link rel="stylesheet" href="<?= BASE_PATH ?>/public/assets/css/<?= e($css) ?>.css">
    <?php endif; ?>
</head>
<body>
<header class="header">
    <div class="container">
        <div class="logo">
            <a href="<?= BASE_PATH ?>/public/">
                <h1>Immo<span>Colucci</span></h1>
                <p class="logo-tagline">Immobilier & Conciergerie</p>
            </a>
        </div>
        <nav class="nav" id="mainNav">
            <ul>
                <li><a href="<?= BASE_PATH ?>/public/">Accueil</a></li>
                <li><a href="<?= BASE_PATH ?>/public/biens">Biens</a></li>
                <li><a href="<?= BASE_PATH ?>/public/services">Services</a></li>
                <li><a href="<?= BASE_PATH ?>/public/blog">Blog</a></li>
                <li><a href="<?= BASE_PATH ?>/public/#about">À propos</a></li>
            </ul>
        </nav>
        <a href="<?= BASE_PATH ?>/public/#contact" class="header-cta">Contact</a>
        <button class="hamburger" id="hamburgerBtn" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>
<main>
