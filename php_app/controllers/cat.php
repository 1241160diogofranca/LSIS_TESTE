<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

function dashboard() {
    require_login('cat');
    $db = get_db(); $u = current_user();
    $cid = (int)$u['cat_id'];
    $kpi_total = (int)$db->query("SELECT COUNT(*) FROM service_tickets WHERE cat_id={$cid}")->fetchColumn();
    $kpi_open  = (int)$db->query("SELECT COUNT(*) FROM service_tickets WHERE cat_id={$cid} AND status IN ('assigned','in_progress','awaiting_parts')")->fetchColumn();
    $kpi_closed= (int)$db->query("SELECT COUNT(*) FROM service_tickets WHERE cat_id={$cid} AND status='closed'")->fetchColumn();
    $avg = $db->query("SELECT AVG(TIMESTAMPDIFF(HOUR, opened_at, closed_at)) FROM service_tickets WHERE cat_id={$cid} AND closed_at IS NOT NULL")->fetchColumn();
    $kpi_avg = $avg ? round((float)$avg,1) : 0;
    $st = $db->prepare("SELECT t.*, u.name client, p.name product_name FROM service_tickets t JOIN users u ON u.id=t.user_id LEFT JOIN products p ON p.id=t.product_id WHERE t.cat_id=? ORDER BY t.id DESC LIMIT 8");
    $st->execute([$cid]);
    $recent = $st->fetchAll();
    $page_title='Centro de Assistência'; $section='dashboard';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/cat/dashboard.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function tickets() {
    require_login('cat');
    $db = get_db(); $u = current_user();
    $st = $db->prepare("SELECT t.*, u.name client, p.name product_name FROM service_tickets t JOIN users u ON u.id=t.user_id LEFT JOIN products p ON p.id=t.product_id WHERE t.cat_id=? ORDER BY t.id DESC");
    $st->execute([(int)$u['cat_id']]);
    $rows = $st->fetchAll();
    $page_title='Pedidos atribuídos'; $section='tickets';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/cat/tickets.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function ticket_view() {
    require_login('cat');
    $db = get_db(); $u = current_user();
    $tid = (int)($_GET['id'] ?? 0);
    $st = $db->prepare("SELECT t.*, u.name client, u.email client_email, u.phone client_phone, p.name product_name, p.model FROM service_tickets t JOIN users u ON u.id=t.user_id LEFT JOIN products p ON p.id=t.product_id WHERE t.id=? AND t.cat_id=?");
    $st->execute([$tid, (int)$u['cat_id']]);
    $ticket = $st->fetch();
    if (!$ticket) { flash('error','Pedido não encontrado.'); redirect('/cat/tickets'); }
    $page_title='Pedido #' . $tid; $section='tickets';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/cat/ticket_detail.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function ticket_update() {
    require_login('cat');
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','CSRF.'); redirect('/cat/tickets'); }
    $u = current_user();
    $tid = (int)($_POST['ticket_id'] ?? 0);
    $db = get_db();
    $st = $db->prepare("SELECT * FROM service_tickets WHERE id=? AND cat_id=?");
    $st->execute([$tid, (int)$u['cat_id']]);
    $t = $st->fetch();
    if (!$t) { flash('error','Não autorizado.'); redirect('/cat/tickets'); }

    $diag = trim($_POST['diagnosis'] ?? $t['diagnosis']);
    $intv = trim($_POST['intervention'] ?? $t['intervention']);
    $parts = trim($_POST['parts_used'] ?? $t['parts_used']);
    $status = $_POST['status'] ?? $t['status'];
    $allowed = ['assigned','in_progress','awaiting_parts','closed','cancelled'];
    if (!in_array($status, $allowed, true)) $status = $t['status'];

    $closed_at = $t['closed_at'];
    if ($status === 'closed' && !$closed_at) $closed_at = date('Y-m-d H:i:s');

    $up = $db->prepare("UPDATE service_tickets SET diagnosis=?, intervention=?, parts_used=?, status=?, closed_at=? WHERE id=?");
    $up->execute([$diag,$intv,$parts,$status,$closed_at,$tid]);
    notify((int)$t['user_id'],'Atualização do pedido #'.$tid,'Estado: '.$status);
    log_event('ticket_update', "#$tid -> $status");
    flash('success','Pedido atualizado.');
    redirect('/cat/ticket?id=' . $tid);
}
