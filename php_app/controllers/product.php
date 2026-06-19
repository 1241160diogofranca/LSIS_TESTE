<?php
function index() {
    require_once __DIR__ . '/../config/db.php';
    require_once __DIR__ . '/../lib/helpers.php';
    $db = get_db();
    $id = (int)($_GET['id'] ?? 0);
    $st = $db->prepare("SELECT p.*, c.slug cat_slug, c.name cat_name FROM products p JOIN categories c ON c.id=p.category_id WHERE p.id=?");
    $st->execute([$id]);
    $product = $st->fetch();
    if (!$product) { http_response_code(404); flash('error','Produto não encontrado.'); redirect('/catalog'); }

    $pst = $db->prepare("SELECT * FROM parts WHERE compatible_models LIKE ? LIMIT 6");
    $pst->execute(['%' . $product['model'] . '%']);
    $parts = $pst->fetchAll();
    $page_title = $product['name'];
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/product.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}
