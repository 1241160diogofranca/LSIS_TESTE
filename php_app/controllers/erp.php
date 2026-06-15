<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

// Mocked ERP SAGE sync — simulates synchronisation of products & stock.
function sync() {
    require_login('admin');
    $db = get_db();
    // Simulate small stock variation
    $st = $db->query("SELECT id, stock FROM products");
    $up = $db->prepare("UPDATE products SET stock=? WHERE id=?");
    foreach ($st as $r) {
        $delta = random_int(-2, 5);
        $new = max(0, (int)$r['stock'] + $delta);
        $up->execute([$new, (int)$r['id']]);
    }
    log_event('erp_sync', 'mocked sync');
    flash('success', 'Sincronização ERP SAGE (simulada) concluída.');
    redirect('/admin');
}
