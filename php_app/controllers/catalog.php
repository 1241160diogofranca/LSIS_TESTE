<?php
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../bll/ProductService.php';

function index() {
    $cat_slug = $_GET['cat']   ?? '';
    $q        = trim($_GET['q'] ?? '');
    $maxp     = (float)($_GET['max']   ?? 1500);
    $brand    = trim($_GET['brand']    ?? '');

    $svc = new ProductService();
    $data = $svc->catalogData($cat_slug ?: null, $q ?: null, $brand ?: null, $maxp ?: null);
    $products   = $data['products'];
    $categories = $data['categories'];
    $brands     = $data['brands'];

    $page_title = 'Catálogo';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/catalog.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}
