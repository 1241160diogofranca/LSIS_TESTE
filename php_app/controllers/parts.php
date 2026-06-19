<?php
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../bll/ProductService.php';

function index() {
    $q = trim($_GET['q'] ?? '');
    $svc = new ProductService();
    $parts = $svc->partsList($q ?: null);
    $page_title = 'Peças e Consumíveis';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/parts.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}
