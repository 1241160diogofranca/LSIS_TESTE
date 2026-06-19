<?php
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../bll/CartService.php';

function show() {
    $cart = (new CartService())->getAll();
    $page_title = 'Carrinho';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/cart.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function add() {
    $type = $_POST['type'] ?? 'product';
    $id   = (int)($_POST['id'] ?? 0);
    $qty  = (int)($_POST['qty'] ?? 1);

    [$ok, $msg] = (new CartService())->add($type, $id, $qty);
    flash($ok ? 'success' : 'error', $msg);
    redirect($_POST['back'] ?? '/cart');
}

function update() {
    (new CartService())->update($_POST['key'] ?? '', (int)($_POST['qty'] ?? 1));
    redirect('/cart');
}

function remove() {
    (new CartService())->remove($_POST['key'] ?? '');
    flash('success', 'Item removido.');
    redirect('/cart');
}
