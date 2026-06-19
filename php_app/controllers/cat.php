<?php
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../bll/ServiceTicketService.php';

function dashboard() {
    require_login('cat');
    $u = current_user();
    $svc = new ServiceTicketService();
    $kpis = $svc->kpisForCat((int)$u['cat_id']);
    $kpi_total = $kpis['total']; $kpi_open = $kpis['open']; $kpi_closed = $kpis['closed']; $kpi_avg = $kpis['avg_hours'];
    $recent = $svc->recentForCat((int)$u['cat_id'], 8);
    $page_title='Centro de Assistência'; $section='dashboard';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/cat/dashboard.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function tickets() {
    require_login('cat');
    $u = current_user();
    $rows = (new ServiceTicketService())->listForCat((int)$u['cat_id']);
    $page_title='Pedidos atribuídos'; $section='tickets';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/cat/tickets.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function ticket_view() {
    require_login('cat');
    $u = current_user();
    $tid = (int)($_GET['id'] ?? 0);
    $ticket = (new ServiceTicketService())->findForCat($tid, (int)$u['cat_id']);
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

    [$ok, $msg] = (new ServiceTicketService())->updateByCat($tid, (int)$u['cat_id'], [
        'diagnosis'    => trim($_POST['diagnosis']    ?? ''),
        'intervention' => trim($_POST['intervention'] ?? ''),
        'parts_used'   => trim($_POST['parts_used']   ?? ''),
        'status'       => $_POST['status']            ?? '',
    ]);
    flash($ok ? 'success' : 'error', $msg);
    if ($ok) log_event('ticket_update', "#{$tid} -> " . ($_POST['status'] ?? ''));
    redirect('/cat/ticket?id=' . $tid);
}
