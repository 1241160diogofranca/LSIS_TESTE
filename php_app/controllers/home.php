<?php
function index() {
    require_once __DIR__ . '/../config/db.php';
    require_once __DIR__ . '/../lib/helpers.php';
    $db = get_db();
    $categories = $db->query("SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id=c.id) AS n FROM categories c")->fetchAll();
    $featured = $db->query("SELECT p.*, c.slug AS cat_slug, c.name AS cat_name FROM products p JOIN categories c ON c.id=p.category_id ORDER BY p.id LIMIT 4")->fetchAll();
    $page_title = 'Início';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/home.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}
