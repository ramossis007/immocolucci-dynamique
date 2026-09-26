<?php
class BienController {
    private Bien $bienModel;

    public function __construct() {
        $this->bienModel = new Bien();
    }

    public function liste() {
        $filtres = array_filter([
            'transaction' => $_GET['transaction'] ?? null,
            'type'        => $_GET['type']        ?? null,
            'quartier'    => $_GET['quartier']    ?? null,
            'prix_max'    => $_GET['prix_max']    ?? null,
        ]);

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $limit   = BIENS_PAR_PAGE;
        $total   = $this->bienModel->countAll($filtres);
        $nbPages = max(1, (int)ceil($total / $limit));
        $page    = min($page, $nbPages);
        $offset  = ($page - 1) * $limit;

        $biens = $this->bienModel->getAll($filtres, $limit, $offset);

        require __DIR__ . '/../views/biens/liste.php';
    }

    public function detail($slug) {
        $bien = $this->bienModel->getBySlug($slug);
        if (!$bien) {
            http_response_code(404);
            require __DIR__ . '/../views/pages/404.php';
            return;
        }
        $medias      = $this->bienModel->getMedias((int)$bien['id']);
        $equipements = json_decode($bien['equipements'] ?? '[]', true);
        $proximites  = json_decode($bien['proximites']  ?? '[]', true);

        require __DIR__ . '/../views/biens/detail.php';
    }
}
