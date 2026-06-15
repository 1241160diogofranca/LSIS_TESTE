<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

function dashboard() {
    require_login('admin');
    $db = get_db();
    $k = [
        'users'    => (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
        'products' => (int)$db->query("SELECT COUNT(*) FROM products")->fetchColumn(),
        'orders'   => (int)$db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
        'revenue'  => (float)$db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE payment_status='paid'")->fetchColumn(),
        'open_tix' => (int)$db->query("SELECT COUNT(*) FROM service_tickets WHERE status IN ('open','assigned','in_progress')")->fetchColumn(),
        'closed_tix' => (int)$db->query("SELECT COUNT(*) FROM service_tickets WHERE status='closed'")->fetchColumn(),
        'unassigned' => (int)$db->query("SELECT COUNT(*) FROM service_tickets WHERE cat_id IS NULL AND status='open'")->fetchColumn(),
    ];
    // 6 months revenue
    $bars = $db->query("SELECT DATE_FORMAT(created_at,'%Y-%m') ym, COALESCE(SUM(total),0) tot FROM orders WHERE payment_status='paid' GROUP BY ym ORDER BY ym DESC LIMIT 6")->fetchAll();
    $bars = array_reverse($bars);
    $recent_orders = $db->query("SELECT o.*, u.name client FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC LIMIT 6")->fetchAll();
    $recent_tix    = $db->query("SELECT t.*, u.name client FROM service_tickets t JOIN users u ON u.id=t.user_id ORDER BY t.id DESC LIMIT 6")->fetchAll();
    $page_title='Backoffice'; $section='dashboard';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/admin/dashboard.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function users() {
    require_login('admin');
    $db = get_db();
    $rows = $db->query("SELECT u.*, s.name store_name, c.name cat_name FROM users u LEFT JOIN stores s ON s.id=u.store_id LEFT JOIN cats c ON c.id=u.cat_id ORDER BY u.id DESC")->fetchAll();
    $stores = $db->query("SELECT id,name FROM stores")->fetchAll();
    $cats   = $db->query("SELECT id,name FROM cats")->fetchAll();
    $page_title='Utilizadores'; $section='users';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/admin/users.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function users_save() {
    require_login('admin');
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','CSRF.'); redirect('/admin/users'); }
    $db = get_db();
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $email= trim($_POST['email'] ?? '');
    $role = in_array($_POST['role'] ?? 'consumer', ['consumer','store_manager','cat','admin'], true) ? $_POST['role'] : 'consumer';
    $store_id = (int)($_POST['store_id'] ?? 0) ?: null;
    $cat_id   = (int)($_POST['cat_id'] ?? 0) ?: null;
    $pw   = $_POST['password'] ?? '';
    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL)) { flash('error','Dados inválidos.'); redirect('/admin/users'); }

    if ($id) {
        if ($pw) {
            $db->prepare("UPDATE users SET name=?, email=?, role=?, store_id=?, cat_id=?, password_hash=? WHERE id=?")
               ->execute([$name,$email,$role,$store_id,$cat_id, password_hash($pw, PASSWORD_BCRYPT), $id]);
        } else {
            $db->prepare("UPDATE users SET name=?, email=?, role=?, store_id=?, cat_id=? WHERE id=?")
               ->execute([$name,$email,$role,$store_id,$cat_id,$id]);
        }
        flash('success','Utilizador atualizado.');
    } else {
        if (strlen($pw) < 6) { flash('error','Palavra-passe ≥ 6 caracteres.'); redirect('/admin/users'); }
        $db->prepare("INSERT INTO users (name,email,password_hash,role,store_id,cat_id) VALUES (?,?,?,?,?,?)")
           ->execute([$name,$email, password_hash($pw, PASSWORD_BCRYPT), $role,$store_id,$cat_id]);
        flash('success','Utilizador criado.');
    }
    log_event('admin_user_save', $email);
    redirect('/admin/users');
}

function products() {
    require_login('admin');
    $db = get_db();
    $rows = $db->query("SELECT p.*, c.name cat_name FROM products p JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC")->fetchAll();
    $cats = $db->query("SELECT id,name FROM categories")->fetchAll();
    $page_title='Produtos'; $section='products';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/admin/products.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function products_save() {
    require_login('admin');
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','CSRF.'); redirect('/admin/products'); }
    $db = get_db();
    $id = (int)($_POST['id'] ?? 0);
    $data = [
        'sku'=>trim($_POST['sku'] ?? ''), 'name'=>trim($_POST['name'] ?? ''),
        'brand'=>trim($_POST['brand'] ?? 'Meireles'), 'model'=>trim($_POST['model'] ?? ''),
        'category_id'=>(int)($_POST['category_id'] ?? 0),
        'description'=>trim($_POST['description'] ?? ''), 'specs'=>trim($_POST['specs'] ?? ''),
        'price'=>(float)($_POST['price'] ?? 0), 'stock'=>(int)($_POST['stock'] ?? 0),
        'image_url'=>trim($_POST['image_url'] ?? ''), 'warranty_months'=>(int)($_POST['warranty_months'] ?? 24),
    ];
    if (!$data['sku'] || !$data['name'] || !$data['category_id']) { flash('error','SKU, nome e categoria obrigatórios.'); redirect('/admin/products'); }
    if ($id) {
        $sql = "UPDATE products SET sku=?,name=?,brand=?,model=?,category_id=?,description=?,specs=?,price=?,stock=?,image_url=?,warranty_months=? WHERE id=?";
        $db->prepare($sql)->execute([...array_values($data), $id]);
        flash('success','Produto atualizado.');
    } else {
        $sql = "INSERT INTO products (sku,name,brand,model,category_id,description,specs,price,stock,image_url,warranty_months) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
        $db->prepare($sql)->execute(array_values($data));
        flash('success','Produto criado.');
    }
    log_event('admin_product_save', $data['sku']);
    redirect('/admin/products');
}

function orders() {
    require_login('admin');
    $db = get_db();
    $rows = $db->query("SELECT o.*, u.name client, u.email FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC")->fetchAll();
    $page_title='Encomendas'; $section='orders';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/admin/orders.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function orders_update() {
    require_login('admin');
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','CSRF.'); redirect('/admin/orders'); }
    $oid = (int)($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $allowed = ['pending_payment','paid','processing','shipped','in_transit','delivered','cancelled'];
    if (!in_array($status, $allowed, true)) { flash('error','Estado inválido.'); redirect('/admin/orders'); }
    $db = get_db();
    $db->prepare("UPDATE orders SET status=? WHERE id=?")->execute([$status, $oid]);
    $st = $db->prepare("SELECT user_id FROM orders WHERE id=?"); $st->execute([$oid]);
    if ($uid = (int)$st->fetchColumn()) notify($uid, 'Atualização da encomenda #'.$oid, 'Novo estado: '.$status);
    log_event('admin_order_update', "#$oid -> $status");
    flash('success','Encomenda atualizada.');
    redirect('/admin/orders');
}

function tickets() {
    require_login('admin');
    $db = get_db();
    $rows = $db->query("SELECT t.*, u.name client, c.name cat_name FROM service_tickets t JOIN users u ON u.id=t.user_id LEFT JOIN cats c ON c.id=t.cat_id ORDER BY t.id DESC")->fetchAll();
    $cats = $db->query("SELECT id,name FROM cats")->fetchAll();
    $page_title='Pedidos Assistência'; $section='tickets';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/admin/tickets.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function tickets_assign() {
    require_login('admin');
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','CSRF.'); redirect('/admin/tickets'); }
    $tid = (int)($_POST['ticket_id'] ?? 0);
    $cid = (int)($_POST['cat_id'] ?? 0) ?: null;
    $db = get_db();
    $db->prepare("UPDATE service_tickets SET cat_id=?, status='assigned' WHERE id=?")->execute([$cid, $tid]);
    // notify CAT users
    if ($cid) {
        $st = $db->prepare("SELECT id FROM users WHERE role='cat' AND cat_id=?"); $st->execute([$cid]);
        foreach ($st->fetchAll() as $r) notify((int)$r['id'],'Novo pedido atribuído','#'.$tid.' foi atribuído ao seu CAT.','alert');
    }
    log_event('admin_ticket_assign', "#$tid -> CAT $cid");
    flash('success','Pedido atribuído.');
    redirect('/admin/tickets');
}

function reports() {
    require_login('admin');
    $db = get_db();
    $by_month = $db->query("SELECT DATE_FORMAT(created_at,'%Y-%m') ym, COUNT(*) n, COALESCE(SUM(total),0) tot FROM orders WHERE payment_status='paid' GROUP BY ym ORDER BY ym DESC LIMIT 12")->fetchAll();
    $by_month = array_reverse($by_month);
    $by_cat = $db->query("SELECT c.name, COALESCE(SUM(oi.line_total),0) tot FROM categories c LEFT JOIN products p ON p.category_id=c.id LEFT JOIN order_items oi ON oi.product_id=p.id LEFT JOIN orders o ON o.id=oi.order_id AND o.payment_status='paid' GROUP BY c.id ORDER BY tot DESC")->fetchAll();
    $tix_state = $db->query("SELECT status, COUNT(*) n FROM service_tickets GROUP BY status")->fetchAll();
    $cat_perf = $db->query("SELECT c.name, COUNT(t.id) n, AVG(TIMESTAMPDIFF(HOUR, t.opened_at, t.closed_at)) avg_hours FROM cats c LEFT JOIN service_tickets t ON t.cat_id=c.id AND t.closed_at IS NOT NULL GROUP BY c.id")->fetchAll();
    $page_title='Relatórios'; $section='reports';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/admin/reports.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function export() {
    require_login('admin');
    $type = $_GET['type'] ?? 'orders';
    $db = get_db();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="'.$type.'_'.date('Ymd_His').'.csv"');
    $out = fopen('php://output', 'w');
    if ($type === 'orders') {
        fputcsv($out, ['ID','Cliente','Email','Estado','Pagamento','Subtotal','Envio','Total','Data']);
        $st = $db->query("SELECT o.id, u.name, u.email, o.status, o.payment_status, o.subtotal, o.shipping_cost, o.total, o.created_at FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC");
        while ($r = $st->fetch(PDO::FETCH_NUM)) fputcsv($out, $r);
    } elseif ($type === 'tickets') {
        fputcsv($out, ['ID','Cliente','Título','Estado','Prioridade','CAT','Aberto','Fechado']);
        $st = $db->query("SELECT t.id, u.name, t.title, t.status, t.priority, c.name, t.opened_at, t.closed_at FROM service_tickets t JOIN users u ON u.id=t.user_id LEFT JOIN cats c ON c.id=t.cat_id ORDER BY t.id DESC");
        while ($r = $st->fetch(PDO::FETCH_NUM)) fputcsv($out, $r);
    } elseif ($type === 'users') {
        fputcsv($out, ['ID','Nome','Email','Role','Criado']);
        $st = $db->query("SELECT id,name,email,role,created_at FROM users ORDER BY id DESC");
        while ($r = $st->fetch(PDO::FETCH_NUM)) fputcsv($out, $r);
    } else {
        fputcsv($out, ['error']); fputcsv($out, ['unknown export type']);
    }
    fclose($out); exit;
}

function settings() {
    require_login('admin');
    $db = get_db();
    $settings = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
    $page_title='Configurações'; $section='settings';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/admin/settings.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function settings_save() {
    require_login('admin');
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','CSRF.'); redirect('/admin/settings'); }
    $db = get_db();
    $st = $db->prepare("INSERT INTO settings (k,v) VALUES (?,?) ON DUPLICATE KEY UPDATE v=VALUES(v)");
    $st->execute(['warranty_alert_days', (int)($_POST['warranty_alert_days'] ?? 30)]);
    $st->execute(['shipping_flat_cost', (float)($_POST['shipping_flat_cost'] ?? 5.90)]);
    flash('success','Configurações guardadas.');
    redirect('/admin/settings');
}
