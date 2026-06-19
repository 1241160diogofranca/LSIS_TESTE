<?php
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../bll/CartService.php';
require_once __DIR__ . '/../bll/OrderService.php';

function show() {
    require_login();
    $cart = (new CartService())->getAll();
    if (empty($cart)) { flash('error','O seu carrinho está vazio.'); redirect('/cart'); }
    $user = current_user();
    $page_title = 'Checkout';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/checkout.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function submit() {
    require_login();
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','Sessão expirou.'); redirect('/checkout'); }

    $u = current_user();
    $cartSvc  = new CartService();
    $orderSvc = new OrderService();

    [$ok, $orderId, $msg] = $orderSvc->checkout($u['id'], trim($_POST['address'] ?? ''), $cartSvc->getAll());
    if (!$ok) { flash('error', $msg); redirect('/checkout'); }

    log_event('order_create', "Order #{$orderId}");
    $cartSvc->clear();
    flash('success', 'Encomenda criada. Conclua o pagamento abaixo.');
    redirect('/account/order?id=' . $orderId);
}

function pay() {
    require_login();
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','CSRF'); redirect('/account/orders'); }

    $u = current_user();
    $oid = (int)($_POST['order_id'] ?? 0);

    [$ok, $msg] = (new OrderService())->pay($oid, $u['id']);
    flash($ok ? 'success' : 'error', $msg);
    if ($ok) log_event('order_paid', "Order #{$oid}");
    redirect('/account/order?id=' . $oid);
}
