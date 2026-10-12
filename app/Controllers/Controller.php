<?php

class Controller {
    protected function view($viewPath, $data = []) {
        extract($data);
        require_once __DIR__ . '/../../resources/views/' . $viewPath . '.php';
    }

    protected function redirect($path) {
        header('Location: ' . url($path));
        exit;
    }
}
