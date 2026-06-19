<?php
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../bll/ServiceTicketService.php';
require_once __DIR__ . '/../dal/ProductDAL.php';

function index() {
    require_login('consumer');
    $u = current_user();
    $tickets  = (new ServiceTicketService())->listForUser($u['id']);
    $products = (new ProductDAL())->findAll('name ASC');
    $page_title='Assistência Técnica'; $section='service';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/account/service.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function open_ticket() {
    require_login('consumer');
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','CSRF.'); redirect('/account/service'); }
    $u = current_user();

    // upload da foto
    $photo = null;
    if (!empty($_FILES['photo']['name'])) {
        $dir = __DIR__ . '/../uploads/tickets';
        @mkdir($dir, 0777, true);
        $safe = preg_replace('/[^a-zA-Z0-9_.-]/','_', basename($_FILES['photo']['name']));
        $name = time().'_'.$safe;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $dir.'/'.$name)) $photo = $name;
    }

    [$ok, $tid, $msg] = (new ServiceTicketService())->open(
        $u['id'],
        trim($_POST['title']       ?? ''),
        trim($_POST['description'] ?? ''),
        (int)($_POST['product_id'] ?? 0) ?: null,
        $_POST['priority']         ?? 'normal',
        $photo
    );
    flash($ok ? 'success' : 'error', $msg);
    if ($ok) log_event('ticket_open', "#{$tid}");
    redirect('/account/service');
}

function view() {
    require_login('consumer');
    $u = current_user();
    $tid = (int)($_GET['id'] ?? 0);
    $ticket = (new ServiceTicketService())->findForUser($tid, $u['id']);
    if (!$ticket) { flash('error','Pedido não encontrado.'); redirect('/account/service'); }
    $page_title = 'Pedido #' . $tid; $section='service';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/account/service_detail.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}
