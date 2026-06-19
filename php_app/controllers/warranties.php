<?php
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../bll/WarrantyService.php';
require_once __DIR__ . '/../dal/ProductDAL.php';

function index() {
    require_login('consumer');
    $u = current_user();
    $warranties = (new WarrantyService())->listForUser($u['id']);
    $products   = (new ProductDAL())->findAll('name ASC');
    $page_title = 'Garantias'; $section='warranties';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/account/warranties.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function activate() {
    require_login('consumer');
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','CSRF.'); redirect('/account/warranties'); }
    $u = current_user();

    // upload de prova
    $proof = null;
    if (!empty($_FILES['proof']['name'])) {
        $dir = __DIR__ . '/../uploads/warranties';
        @mkdir($dir, 0777, true);
        $safe = preg_replace('/[^a-zA-Z0-9_.-]/','_', basename($_FILES['proof']['name']));
        $name = time().'_'.$safe;
        if (move_uploaded_file($_FILES['proof']['tmp_name'], $dir.'/'.$name)) $proof = $name;
    }

    [$ok, $msg] = (new WarrantyService())->activate(
        $u['id'],
        (int)($_POST['product_id'] ?? 0),
        trim($_POST['serial'] ?? ''),
        $_POST['purchase_date'] ?? '',
        $proof
    );
    flash($ok ? 'success' : 'error', $msg);
    if ($ok) log_event('warranty_activate', $_POST['serial'] ?? '');
    redirect('/account/warranties');
}
