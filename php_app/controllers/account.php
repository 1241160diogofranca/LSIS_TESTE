<?php
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../bll/OrderService.php';
require_once __DIR__ . '/../bll/WarrantyService.php';
require_once __DIR__ . '/../bll/ServiceTicketService.php';
require_once __DIR__ . '/../bll/NotificationService.php';
require_once __DIR__ . '/../bll/CartService.php';

function dashboard() {
    require_login('consumer');
    $u = current_user();
    $os = new OrderService(); $ws = new WarrantyService(); $ts = new ServiceTicketService();
    $orders     = $os->recentForUser($u['id'], 5);
    $warranties = $ws->recentForUser($u['id'], 5);
    $tickets    = $ts->recentForUser($u['id'], 5);
    $n_orders   = count($os->listForUser($u['id']));
    $n_wars     = count($ws->listForUser($u['id']));
    $n_tix      = count($ts->listForUser($u['id']));
    $page_title = 'A minha conta'; $section = 'dashboard';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/account/dashboard.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function orders() {
    require_login('consumer');
    $u = current_user();
    $orders = (new OrderService())->listForUser($u['id']);
    $page_title = 'Encomendas'; $section = 'orders';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/account/orders.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function order_detail() {
    require_login('consumer');
    $u = current_user();
    $oid = (int)($_GET['id'] ?? 0);
    $data = (new OrderService())->orderWithItemsForUser($oid, $u['id']);
    if (!$data) { flash('error','Encomenda não encontrada.'); redirect('/account/orders'); }
    $order = $data['order']; $items = $data['items'];
    $page_title = 'Encomenda #' . $oid; $section='orders';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/account/order_detail.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function notifications() {
    require_login();
    $u = current_user();
    $notifs = (new NotificationService())->listForUser($u['id']);
    $page_title='Notificações'; $section='notifications';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/account/notifications.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}
