<?php
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../bll/OrderService.php';
require_once __DIR__ . '/../bll/ServiceTicketService.php';

function dashboard() {
    require_login('store_manager');
    $os = new OrderService(); $ts = new ServiceTicketService();
    $allOrders   = $os->allForAdmin();
    $kpi_orders  = count($allOrders);
    $kpi_revenue = array_sum(array_map(fn($o) => $o['payment_status'] === 'paid' ? (float)$o['total'] : 0, $allOrders));
    $allTix      = $ts->allForAdmin();
    $kpi_open_tix = count(array_filter($allTix, fn($t) => in_array($t['status'], ['open','assigned','in_progress'], true)));
    $kpi_delivered = count(array_filter($allOrders, fn($o) => $o['status'] === 'delivered'));
    $recent = $os->recentForAdmin(8);
    $page_title='Loja'; $section='dashboard';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/store/dashboard.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function orders() {
    require_login('store_manager');
    $rows = (new OrderService())->allForAdmin();
    $page_title='Encomendas da Loja'; $section='orders';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/store/orders.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function tickets() {
    require_login('store_manager');
    $rows = (new ServiceTicketService())->allForAdmin();
    $page_title='Assistências da Loja'; $section='tickets';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/store/tickets.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}
