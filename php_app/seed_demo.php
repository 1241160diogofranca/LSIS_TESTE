<?php
// Seed demo activity: orders / warranties / service tickets for the demo client.
require __DIR__ . '/config/db.php';
require __DIR__ . '/lib/helpers.php';

$db = get_db();
$cli = $db->query("SELECT id FROM users WHERE email='cliente@meireles.pt'")->fetchColumn();
$cat = $db->query("SELECT id FROM cats LIMIT 1")->fetchColumn();
$cli = (int)$cli; $cat = (int)$cat;

if (!$cli) { echo "no client\n"; exit; }

// Skip if already seeded
if ((int)$db->query("SELECT COUNT(*) FROM orders WHERE user_id={$cli}")->fetchColumn() > 0) {
    echo "demo data already exists, skipping.\n"; exit;
}

$products = $db->query("SELECT id, name, price FROM products ORDER BY id")->fetchAll();

// Helper: create order with N items
function make_order(PDO $db, int $uid, array $items, string $status, string $pay, string $created_at): int {
    $sub = 0; foreach ($items as $i) $sub += $i['price']*$i['qty'];
    $sh = 5.90; $tot = $sub + $sh;
    $st = $db->prepare("INSERT INTO orders (user_id,status,payment_status,shipping_address,subtotal,shipping_cost,total,created_at) VALUES (?,?,?,?,?,?,?,?)");
    $st->execute([$uid,$status,$pay,'Rua Demo 100, 4000-000 Porto', $sub,$sh,$tot,$created_at]);
    $oid = (int)$db->lastInsertId();
    $sti = $db->prepare("INSERT INTO order_items (order_id,item_type,product_id,part_id,name,unit_price,quantity,line_total) VALUES (?,?,?,?,?,?,?,?)");
    foreach ($items as $i) $sti->execute([$oid,'product',$i['id'],null,$i['name'],$i['price'],$i['qty'],$i['price']*$i['qty']]);
    return $oid;
}

$now = time();
make_order($db,$cli,[['id'=>$products[0]['id'],'name'=>$products[0]['name'],'price'=>$products[0]['price'],'qty'=>1]], 'delivered','paid', date('Y-m-d H:i:s', $now - 60*86400));
make_order($db,$cli,[['id'=>$products[2]['id'],'name'=>$products[2]['name'],'price'=>$products[2]['price'],'qty'=>1]], 'in_transit','paid', date('Y-m-d H:i:s', $now - 5*86400));
make_order($db,$cli,[['id'=>$products[4]['id'],'name'=>$products[4]['name'],'price'=>$products[4]['price'],'qty'=>2]], 'pending_payment','unpaid', date('Y-m-d H:i:s', $now - 86400));

// Seed warranties
$wst = $db->prepare("INSERT INTO warranties (user_id,product_id,serial_number,purchase_date,expiry_date,status) VALUES (?,?,?,?,?,?)");
$wst->execute([$cli, $products[0]['id'], 'ME-2024-0001', date('Y-m-d', $now - 90*86400), date('Y-m-d', $now + 540*86400), 'active']);
$wst->execute([$cli, $products[3]['id'], 'ME-2023-1234', date('Y-m-d', $now - 400*86400), date('Y-m-d', $now + 200*86400), 'active']);

// Seed service tickets - some assigned to CAT, one closed
$tst = $db->prepare("INSERT INTO service_tickets (user_id,product_id,cat_id,title,description,status,priority,diagnosis,intervention,parts_used,opened_at,closed_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
$tst->execute([$cli, $products[0]['id'], $cat, 'Fogão não acende um dos bicos', 'O bico da frente esquerda não acende mesmo com gás aberto.', 'in_progress','high', 'Válvula de ignição com defeito.', 'Aguarda peça de substituição.', null, date('Y-m-d H:i:s', $now-3*86400), null]);
$tst->execute([$cli, $products[6]['id'], $cat, 'Máquina barulhenta na centrifugação', 'Ruído elevado durante o ciclo de centrifugação.', 'closed','normal', 'Rolamento traseiro gasto.', 'Substituído o rolamento.', '1x rolamento (PRT-RLM)', date('Y-m-d H:i:s', $now-30*86400), date('Y-m-d H:i:s', $now-26*86400)]);
$tst->execute([$cli, $products[4]['id'], null, 'Exaustor com pouco sucção', 'Aparentemente o filtro está saturado, queria avaliação.', 'open','low', null, null, null, date('Y-m-d H:i:s', $now - 86400), null]);

// notifications
$db->prepare("INSERT INTO notifications (user_id,title,body,type) VALUES (?,?,?,?)")
   ->execute([$cli, 'Encomenda em trânsito', 'A sua encomenda saiu do nosso armazém e chegará em breve.','info']);
$db->prepare("INSERT INTO notifications (user_id,title,body,type) VALUES (?,?,?,?)")
   ->execute([$cli, 'Garantia próxima do fim', 'A garantia ME-2023-1234 expira em menos de 30 dias.','alert']);

echo "Demo data seeded.\n";
