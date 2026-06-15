<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

function index() {
    require_login('consumer');
    $db = get_db(); $u = current_user();
    $st = $db->prepare("SELECT t.*, p.name product_name, c.name cat_name FROM service_tickets t LEFT JOIN products p ON p.id=t.product_id LEFT JOIN cats c ON c.id=t.cat_id WHERE t.user_id=? ORDER BY t.id DESC");
    $st->execute([$u['id']]);
    $tickets = $st->fetchAll();
    $products = $db->query("SELECT id,name,model FROM products ORDER BY name")->fetchAll();
    $page_title='Assistência Técnica'; $section='service';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/account/service.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function open_ticket() {
    require_login('consumer');
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','CSRF.'); redirect('/account/service'); }
    $u = current_user();
    $title = trim($_POST['title'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $pid   = (int)($_POST['product_id'] ?? 0) ?: null;
    $prio  = in_array($_POST['priority'] ?? 'normal', ['low','normal','high'], true) ? $_POST['priority'] : 'normal';
    if (!$title || !$desc) { flash('error','Título e descrição obrigatórios.'); redirect('/account/service'); }

    $photo = null;
    if (!empty($_FILES['photo']['name'])) {
        $dir = __DIR__ . '/../uploads/tickets';
        @mkdir($dir, 0777, true);
        $safe = preg_replace('/[^a-zA-Z0-9_.-]/','_', basename($_FILES['photo']['name']));
        $name = time().'_'.$safe;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $dir.'/'.$name)) $photo = $name;
    }
    $db = get_db();
    $st = $db->prepare("INSERT INTO service_tickets (user_id, product_id, title, description, priority, photo_filename) VALUES (?,?,?,?,?,?)");
    $st->execute([$u['id'],$pid,$title,$desc,$prio,$photo]);
    $tid = (int)$db->lastInsertId();
    notify($u['id'],'Pedido de assistência aberto','Pedido #'.$tid.' criado. Será atribuído a um CAT em breve.');
    // Notify admins
    foreach ($db->query("SELECT id FROM users WHERE role='admin'")->fetchAll() as $a) {
        notify((int)$a['id'],'Novo pedido de assistência','#'.$tid.' aguarda atribuição.','alert');
    }
    log_event('ticket_open', "#$tid");
    flash('success','Pedido de assistência criado com sucesso.');
    redirect('/account/service');
}

function view() {
    require_login('consumer');
    $db = get_db(); $u = current_user();
    $tid = (int)($_GET['id'] ?? 0);
    $st = $db->prepare("SELECT t.*, p.name product_name, c.name cat_name FROM service_tickets t LEFT JOIN products p ON p.id=t.product_id LEFT JOIN cats c ON c.id=t.cat_id WHERE t.id=? AND t.user_id=?");
    $st->execute([$tid, $u['id']]);
    $ticket = $st->fetch();
    if (!$ticket) { flash('error','Pedido não encontrado.'); redirect('/account/service'); }
    $page_title = 'Pedido #' . $tid; $section='service';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/account/service_detail.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}
