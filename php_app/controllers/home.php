<?php
// PL — Home Controller (apenas orquestra; usa BLL)
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../bll/ProductService.php';

function index() {
    $svc = new ProductService();
    $data = $svc->homeData();
    $categories = $data['categories'];
    $featured   = $data['featured'];
    $page_title = 'Início';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/home.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}
