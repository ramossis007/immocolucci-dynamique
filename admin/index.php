<?php
require_once __DIR__ . '/includes/auth.php';
$pdo = Database::getInstance()->getPdo();

$stats = [
    'biens' => $pdo->query("SELECT COUNT(*) FROM biens")->fetchColumn(),
    'biens_publies' => $pdo->query("SELECT COUNT(*) FROM biens WHERE publie = 1")->fetchColumn(),
    'services' => $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn(),
    'articles' => $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn(),
    'medias' => $pdo->query("SELECT COUNT(*) FROM medias")->fetchColumn(),
    'messages_non_lus' => $pdo->query("SELECT COUNT(*) FROM messages WHERE lu = 0")->fetchColumn(),
];

$derniers_messages = $pdo->query(
    "SELECT * FROM messages ORDER BY created_at DESC LIMIT 5"
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<h1>Tableau de bord</h1>

<div class="stats-grid">
    <div class="stat-card">
        <i class="fas fa-building"></i>
        <span class="stat-number"><?= $stats['biens'] ?></span>
        <span class="stat-label">Biens total</span>
    </div>
    <div class="stat-card">
        <i class="fas fa-check-circle"></i>
        <span class="stat-number"><?= $stats['biens_publies'] ?></span>
        <span class="stat-label">Biens publiés</span>
    </div>
    <div class="stat-card">
        <i class="fas fa-concierge-bell"></i>
        <span class="stat-number"><?= $stats['services'] ?></span>
        <span class="stat-label">Services</span>
    </div>
    <div class="stat-card">
        <i class="fas fa-newspaper"></i>
        <span class="stat-number"><?= $stats['articles'] ?></span>
        <span class="stat-label">Articles</span>
    </div>
    <div class="stat-card">
        <i class="fas fa-images"></i>
        <span class="stat-number"><?= $stats['medias'] ?></span>
        <span class="stat-label">Médias</span>
    </div>
    <div class="stat-card highlight">
        <i class="fas fa-envelope"></i>
        <span class="stat-number"><?= $stats['messages_non_lus'] ?></span>
        <span class="stat-label">Messages non lus</span>
    </div>
</div>

<h2>Derniers messages</h2>
<table class="admin-table">
    <thead>
        <tr><th>Nom</th><th>Email</th><th>Sujet</th><th>Date</th><th></th></tr>
    </thead>
    <tbody>
        <?php foreach ($derniers_messages as $m): ?>
        <tr>
            <td><?= e($m['nom']) ?></td>
            <td><?= e($m['email']) ?></td>
            <td><?= e($m['sujet']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></td>
            <td><a href="messages.php?id=<?= $m['id'] ?>">Voir</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/includes/footer.php'; ?>