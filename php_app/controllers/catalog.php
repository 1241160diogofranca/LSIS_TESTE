<?php
function index() {
    require_once __DIR__ . '/../config/db.php';
    require_once __DIR__ . '/../lib/helpers.php';
    $db = get_db();
    $cat_slug = $_GET['cat'] ?? '';
    $q = trim($_GET['q'] ?? '');
    $maxp = (int)($_GET['max'] ?? 1500);
    $brand = trim($_GET['brand'] ?? '');

    $sql = "SELECT p.*, c.slug cat_slug, c.name cat_name FROM products p JOIN categories c ON c.id=p.category_id WHERE 1=1";
    $args = [];
    if ($cat_slug) { $sql .= " AND c.slug=?"; $args[] = $cat_slug; }
    if ($q)        { $sql .= " AND (p.name LIKE ? OR p.model LIKE ? OR p.sku LIKE ?)"; $args[] = "%$q%"; $args[] = "%$q%"; $args[] = "%$q%"; }
    if ($brand)    { $sql .= " AND p.brand=?"; $args[] = $brand; }
    if ($maxp)     { $sql .= " AND p.price <= ?"; $args[] = $maxp; }
    $sql .= " ORDER BY p.name";
    $st = $db->prepare($sql); $st->execute($args);
    $products = $st->fetchAll();
    $categories = $db->query("SELECT * FROM categories")->fetchAll();
    $brands = $db->query("SELECT DISTINCT brand FROM products")->fetchAll(PDO::FETCH_COLUMN);

    $page_title = 'Catálogo';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/catalog.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}
index();
