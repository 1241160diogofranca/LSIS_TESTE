<?php
function index() {
    require_once __DIR__ . '/../config/db.php';
    require_once __DIR__ . '/../lib/helpers.php';
    $db = get_db();
    $q = trim($_GET['q'] ?? '');
    $sql = "SELECT * FROM parts";
    $args = [];
    if ($q) { $sql .= " WHERE name LIKE ? OR sku LIKE ? OR compatible_models LIKE ?"; $args = ["%$q%","%$q%","%$q%"]; }
    $sql .= " ORDER BY name";
    $st = $db->prepare($sql); $st->execute($args);
    $parts = $st->fetchAll();
    $page_title = 'Peças e Consumíveis';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/parts.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}
index();
