<?php
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../bll/ProductService.php';

function index() {
    $id = (int)($_GET['id'] ?? 0);
    $svc = new ProductService();
    $data = $svc->productPageData($id);
    if (!$data) { http_response_code(404); flash('error','Produto não encontrado.'); redirect('/catalog'); }
    $product = $data['product'];
    $parts   = $data['parts'];
    $page_title = $product['name'];
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/product.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}
