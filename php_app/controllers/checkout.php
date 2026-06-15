<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

function show() {
    require_login();
    $cart = cart_get();
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
    $cart = cart_get();
    if (empty($cart)) { flash('error','Carrinho vazio.'); redirect('/cart'); }

    $addr = trim($_POST['address'] ?? '');
    if (!$addr) { flash('error','Morada de entrega obrigatória.'); redirect('/checkout'); }

    $db = get_db();
    $subtotal = 0;
    foreach ($cart as $i) $subtotal += $i['price'] * $i['qty'];
    $shipping = 5.90;
    $total = $subtotal + $shipping;

    $db->beginTransaction();
    $st = $db->prepare("INSERT INTO orders (user_id, status, payment_status, shipping_address, subtotal, shipping_cost, total) VALUES (?,?,?,?,?,?,?)");
    $st->execute([$u['id'], 'pending_payment', 'unpaid', $addr, $subtotal, $shipping, $total]);
    $oid = (int)$db->lastInsertId();
    $sti = $db->prepare("INSERT INTO order_items (order_id,item_type,product_id,part_id,name,unit_price,quantity,line_total) VALUES (?,?,?,?,?,?,?,?)");
    foreach ($cart as $i) {
        $sti->execute([
            $oid, $i['type'],
            $i['type'] === 'product' ? $i['id'] : null,
            $i['type'] === 'part'    ? $i['id'] : null,
            $i['name'], $i['price'], $i['qty'], $i['price'] * $i['qty']
        ]);
    }
    $db->commit();

    notify($u['id'], 'Encomenda #' . $oid . ' criada', 'Aguarda pagamento. Total: ' . money($total));
    log_event('order_create', "Order #$oid total $total");
    cart_set([]);
    flash('success','Encomenda criada. Conclua o pagamento abaixo.');
    redirect('/account/order?id=' . $oid);
}

function pay() {
    require_login();
    $u = current_user();
    $oid = (int)($_POST['order_id'] ?? 0);
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','CSRF'); redirect('/account/orders'); }
    $db = get_db();
    $st = $db->prepare("SELECT * FROM orders WHERE id=? AND user_id=?");
    $st->execute([$oid, $u['id']]);
    $o = $st->fetch();
    if (!$o) { flash('error','Encomenda não encontrada.'); redirect('/account/orders'); }
    if ($o['payment_status'] === 'paid') { flash('error','Já paga.'); redirect('/account/order?id='.$oid); }

    // Simulate payment success
    $db->prepare("UPDATE orders SET payment_status='paid', status='paid' WHERE id=?")->execute([$oid]);
    notify($u['id'], 'Pagamento confirmado', 'Encomenda #' . $oid . ' paga. Total ' . money($o['total']) . '. Em breve será expedida.');
    log_event('order_paid', "Order #$oid");
    flash('success','Pagamento confirmado (simulado). Obrigado!');
    redirect('/account/order?id=' . $oid);
}
