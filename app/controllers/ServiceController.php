<?php
class ServiceController {
    private Service $serviceModel;

    public function __construct() {
        $this->serviceModel = new Service();
    }

    public function liste() {
        $services = $this->serviceModel->getAll();
        require __DIR__ . '/../views/services/liste.php';
    }

    public function detail($slug) {
        $service = $this->serviceModel->getBySlug($slug);
        if (!$service) {
            http_response_code(404);
            require __DIR__ . '/../views/pages/404.php';
            return;
        }
        require __DIR__ . '/../views/services/detail.php';
    }
}
