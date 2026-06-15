<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

function dashboard() {
    require_login('store_manager');
    $db = get_db(); $u = current_user();
    $sid = (int)$u['store_id'];
    $kpi_orders   = (int)$db->query("SELECT COUNT(*) FROM orders WHERE store_id={$sid} OR store_id IS NULL")->fetchColumn();
    $kpi_revenue  = (float)$db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE (store_id={$sid} OR store_id IS NULL) AND payment_status='paid'")->fetchColumn();
    $kpi_open_tix = (int)$db->query("SELECT COUNT(*) FROM service_tickets WHERE status IN ('open','assigned','in_progress')")->fetchColumn();
    $kpi_delivered= (int)$db->query("SELECT COUNT(*) FROM orders WHERE status='delivered'")->fetchColumn();
    $recent = $db->query("SELECT o.*, u.name client FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC LIMIT 8")->fetchAll();
    $page_title='Loja'; $section='dashboard';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/store/dashboard.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function orders() {
    require_login('store_manager');
    $db = get_db();
    $rows = $db->query("SELECT o.*, u.name client, u.email FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC")->fetchAll();
    $page_title='Encomendas da Loja'; $section='orders';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/store/orders.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function tickets() {
    require_login('store_manager');
    $db = get_db();
    $rows = $db->query("SELECT t.*, u.name client, c.name cat_name FROM service_tickets t JOIN users u ON u.id=t.user_id LEFT JOIN cats c ON c.id=t.cat_id ORDER BY t.id DESC")->fetchAll();
    $page_title='Assistências da Loja'; $section='tickets';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/store/tickets.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}
