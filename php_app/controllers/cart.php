<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

function show() {
    $db = get_db();
    $cart = cart_get();
    $page_title = 'Carrinho';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/cart.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function add() {
    $type = $_POST['type'] ?? 'product';
    $id   = (int)($_POST['id'] ?? 0);
    $qty  = max(1, (int)($_POST['qty'] ?? 1));
    $db = get_db();
    if ($type === 'part') {
        $st = $db->prepare("SELECT id, name, price, image_url FROM parts WHERE id=?");
    } else {
        $st = $db->prepare("SELECT id, name, price, image_url FROM products WHERE id=?");
    }
    $st->execute([$id]);
    $item = $st->fetch();
    if (!$item) { flash('error','Item não encontrado.'); redirect('/catalog'); }
    $cart = cart_get();
    $key = $type . '_' . $id;
    if (isset($cart[$key])) {
        $cart[$key]['qty'] += $qty;
    } else {
        $cart[$key] = [
            'key'=>$key, 'type'=>$type, 'id'=>(int)$item['id'],
            'name'=>$item['name'], 'price'=>(float)$item['price'],
            'image'=>$item['image_url'], 'qty'=>$qty,
        ];
    }
    cart_set($cart);
    flash('success', $item['name'] . ' adicionado ao carrinho.');
    $back = $_POST['back'] ?? '/cart';
    redirect($back);
}

function update() {
    $key = $_POST['key'] ?? '';
    $qty = max(1, (int)($_POST['qty'] ?? 1));
    $cart = cart_get();
    if (isset($cart[$key])) {
        $cart[$key]['qty'] = $qty;
        cart_set($cart);
    }
    redirect('/cart');
}

function remove() {
    $key = $_POST['key'] ?? '';
    $cart = cart_get();
    unset($cart[$key]);
    cart_set($cart);
    flash('success', 'Item removido.');
    redirect('/cart');
}
