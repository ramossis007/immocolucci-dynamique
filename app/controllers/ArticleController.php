<?php
class ArticleController {
    private Article $articleModel;

    public function __construct() {
        $this->articleModel = new Article();
    }

    public function liste() {
        $articles = $this->articleModel->getAll();
        require __DIR__ . '/../views/articles/liste.php';
    }

    public function detail($slug) {
        $article = $this->articleModel->getBySlug($slug);
        if (!$article) {
            http_response_code(404);
            require __DIR__ . '/../views/pages/404.php';
            return;
        }
        require __DIR__ . '/../views/articles/detail.php';
    }
}
