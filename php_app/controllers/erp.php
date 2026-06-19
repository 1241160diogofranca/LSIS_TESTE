<?php
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../dal/ProductDAL.php';

// Mocked ERP SAGE sync — simula sincronização de stock.
function sync() {
    require_login('admin');
    $dal = new ProductDAL();
    foreach ($dal->findAll('id ASC') as $p) {
        $delta = random_int(-2, 5);
        $new = max(0, (int)$p['stock'] + $delta);
        $dal->update((int)$p['id'], ['stock' => $new]);
    }
    log_event('erp_sync', 'mocked sync');
    flash('success', 'Sincronização ERP SAGE (simulada) concluída.');
    redirect('/admin');
}
