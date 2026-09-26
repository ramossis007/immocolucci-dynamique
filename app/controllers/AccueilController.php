<?php
class AccueilController {
    public function index() {
        $bienModel = new Bien();

        // 1. Récupérer les biens en vedette (max 6)
        $biensVedette = $bienModel->getAll(['vedette' => 1], 6);

        // 2. Récupérer tous les biens publiés (max 12)
        $tousLesBiens = $bienModel->getAll([], 12);

        // 3. Fusionner : vedettes d'abord, puis les autres
        $idsVedette = array_column($biensVedette, 'id');
        $autres = array_filter($tousLesBiens, function($b) use ($idsVedette) {
            return !in_array($b['id'], $idsVedette);
        });

        $biens = array_merge($biensVedette, $autres);
        // Limiter à 8 biens affichés
        $biens = array_slice($biens, 0, 8);

        require __DIR__ . '/../views/pages/accueil.php';
    }
}