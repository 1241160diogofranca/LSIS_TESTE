<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

function index() {
    require_login('consumer');
    $db = get_db(); $u = current_user();
    $st = $db->prepare("SELECT w.*, p.name product_name, p.image_url, p.warranty_months FROM warranties w JOIN products p ON p.id=w.product_id WHERE w.user_id=? ORDER BY w.id DESC");
    $st->execute([$u['id']]);
    $warranties = $st->fetchAll();
    $products = $db->query("SELECT id, name, model FROM products ORDER BY name")->fetchAll();
    $page_title = 'Garantias'; $section='warranties';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/account/warranties.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function activate() {
    require_login('consumer');
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','CSRF.'); redirect('/account/warranties'); }
    $u = current_user();
    $product_id = (int)($_POST['product_id'] ?? 0);
    $serial = trim($_POST['serial'] ?? '');
    $purchase_date = $_POST['purchase_date'] ?? '';
    if (!$product_id || !$serial || !$purchase_date) {
        flash('error','Todos os campos são obrigatórios.'); redirect('/account/warranties');
    }
    $db = get_db();
    $st = $db->prepare("SELECT warranty_months FROM products WHERE id=?"); $st->execute([$product_id]);
    $months = (int)$st->fetchColumn();
    $expiry = date('Y-m-d', strtotime($purchase_date . " +{$months} months"));
    // Optional file upload
    $proof = null;
    if (!empty($_FILES['proof']['name'])) {
        $dir = __DIR__ . '/../uploads/warranties';
        @mkdir($dir, 0777, true);
        $safe = preg_replace('/[^a-zA-Z0-9_.-]/','_', basename($_FILES['proof']['name']));
        $name = time().'_'.$safe;
        if (move_uploaded_file($_FILES['proof']['tmp_name'], $dir.'/'.$name)) {
            $proof = $name;
        }
    }
    $status = $proof ? 'active' : 'pending_doc';
    $ins = $db->prepare("INSERT INTO warranties (user_id, product_id, serial_number, purchase_date, expiry_date, proof_filename, status) VALUES (?,?,?,?,?,?,?)");
    $ins->execute([$u['id'],$product_id,$serial,$purchase_date,$expiry,$proof,$status]);
    notify($u['id'],'Garantia ativada','Garantia para s/n '.$serial.' válida até '.$expiry);
    log_event('warranty_activate', $serial);
    flash('success', $proof ? 'Garantia ativada com sucesso.' : 'Garantia registada (aguarda anexo de prova de compra).');
    redirect('/account/warranties');
}
