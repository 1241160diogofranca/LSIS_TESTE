<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

function dashboard() {
    require_login('consumer');
    $db = get_db(); $u = current_user();
    $orders = $db->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY id DESC LIMIT 5"); $orders->execute([$u['id']]); $orders = $orders->fetchAll();
    $war = $db->prepare("SELECT w.*, p.name product_name FROM warranties w JOIN products p ON p.id=w.product_id WHERE w.user_id=? ORDER BY w.id DESC LIMIT 5"); $war->execute([$u['id']]); $warranties = $war->fetchAll();
    $tickets = $db->prepare("SELECT * FROM service_tickets WHERE user_id=? ORDER BY id DESC LIMIT 5"); $tickets->execute([$u['id']]); $tickets = $tickets->fetchAll();
    $n_orders = (int)$db->query("SELECT COUNT(*) FROM orders WHERE user_id=" . (int)$u['id'])->fetchColumn();
    $n_wars   = (int)$db->query("SELECT COUNT(*) FROM warranties WHERE user_id=" . (int)$u['id'])->fetchColumn();
    $n_tix    = (int)$db->query("SELECT COUNT(*) FROM service_tickets WHERE user_id=" . (int)$u['id'])->fetchColumn();
    $page_title = 'A minha conta'; $section = 'dashboard';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/account/dashboard.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function orders() {
    require_login('consumer');
    $db = get_db(); $u = current_user();
    $st = $db->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY id DESC"); $st->execute([$u['id']]);
    $orders = $st->fetchAll();
    $page_title = 'Encomendas'; $section = 'orders';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/account/orders.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function order_detail() {
    require_login('consumer');
    $db = get_db(); $u = current_user();
    $oid = (int)($_GET['id'] ?? 0);
    $st = $db->prepare("SELECT * FROM orders WHERE id=? AND user_id=?");
    $st->execute([$oid, $u['id']]);
    $order = $st->fetch();
    if (!$order) { flash('error','Encomenda não encontrada.'); redirect('/account/orders'); }
    $it = $db->prepare("SELECT * FROM order_items WHERE order_id=?"); $it->execute([$oid]); $items = $it->fetchAll();
    $page_title = 'Encomenda #' . $oid; $section='orders';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/account/order_detail.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function notifications() {
    require_login();
    $db = get_db(); $u = current_user();
    $st = $db->prepare("SELECT * FROM notifications WHERE user_id=? ORDER BY id DESC"); $st->execute([$u['id']]);
    $notifs = $st->fetchAll();
    $db->prepare("UPDATE notifications SET is_read=1 WHERE user_id=?")->execute([$u['id']]);
    $page_title='Notificações'; $section='notifications';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/account/notifications.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}
