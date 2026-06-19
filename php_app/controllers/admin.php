<?php
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../bll/ReportService.php';
require_once __DIR__ . '/../bll/OrderService.php';
require_once __DIR__ . '/../bll/ServiceTicketService.php';
require_once __DIR__ . '/../bll/UserService.php';
require_once __DIR__ . '/../bll/ProductService.php';
require_once __DIR__ . '/../dal/MiscDAL.php';

function dashboard() {
    require_login('admin');
    $rs = new ReportService();
    $k = $rs->adminKpis();
    $bars = $rs->revenueMonths(6);
    $recent_orders = (new OrderService())->recentForAdmin(6);
    $recent_tix    = (new ServiceTicketService())->recentForAdmin(6);
    $page_title='Backoffice'; $section='dashboard';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/admin/dashboard.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function users() {
    require_login('admin');
    $rows   = (new UserService())->listForAdmin();
    $stores = (new StoreDAL())->findAll('id ASC');
    $cats   = (new CatDAL())->findAll('id ASC');
    $page_title='Utilizadores'; $section='users';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/admin/users.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function users_save() {
    require_login('admin');
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','CSRF.'); redirect('/admin/users'); }
    $id = (int)($_POST['id'] ?? 0);
    [$ok, $msg] = (new UserService())->save($id, $_POST);
    flash($ok ? 'success' : 'error', $msg);
    if ($ok) log_event('admin_user_save', $_POST['email'] ?? '');
    redirect('/admin/users');
}

function products() {
    require_login('admin');
    $svc  = new ProductService();
    $data = $svc->adminProductsList();
    $rows = $data['products']; $cats = $data['categories'];
    $page_title='Produtos'; $section='products';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/admin/products.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function products_save() {
    require_login('admin');
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','CSRF.'); redirect('/admin/products'); }
    $id   = (int)($_POST['id'] ?? 0);
    $data = [
        'sku'             => trim($_POST['sku'] ?? ''),
        'name'            => trim($_POST['name'] ?? ''),
        'brand'           => trim($_POST['brand'] ?? 'Meireles'),
        'model'           => trim($_POST['model'] ?? ''),
        'category_id'     => (int)($_POST['category_id'] ?? 0),
        'description'     => trim($_POST['description'] ?? ''),
        'specs'           => trim($_POST['specs'] ?? ''),
        'price'           => (float)($_POST['price'] ?? 0),
        'stock'           => (int)($_POST['stock'] ?? 0),
        'image_url'       => trim($_POST['image_url'] ?? ''),
        'warranty_months' => (int)($_POST['warranty_months'] ?? 24),
    ];
    [$ok, $msg] = (new ProductService())->saveProduct($id, $data);
    flash($ok ? 'success' : 'error', $msg);
    if ($ok) log_event('admin_product_save', $data['sku']);
    redirect('/admin/products');
}

function orders() {
    require_login('admin');
    $rows = (new OrderService())->allForAdmin();
    $page_title='Encomendas'; $section='orders';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/admin/orders.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function orders_update() {
    require_login('admin');
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','CSRF.'); redirect('/admin/orders'); }
    $oid    = (int)($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    [$ok, $msg] = (new OrderService())->updateStatusByAdmin($oid, $status);
    flash($ok ? 'success' : 'error', $msg);
    if ($ok) log_event('admin_order_update', "#{$oid} -> {$status}");
    redirect('/admin/orders');
}

function tickets() {
    require_login('admin');
    $rows = (new ServiceTicketService())->allForAdmin();
    $cats = (new CatDAL())->findAll('id ASC');
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
    (new ServiceTicketService())->assignToCat($tid, $cid);
    log_event('admin_ticket_assign', "#{$tid} -> CAT {$cid}");
    flash('success','Pedido atribuído.');
    redirect('/admin/tickets');
}

function reports() {
    require_login('admin');
    $rs = new ReportService();
    $by_month  = $rs->revenueMonths(12);
    $by_cat    = $rs->revenueByCategory();
    $tix_state = $rs->ticketsByStatus();
    $cat_perf  = $rs->catPerformance();
    $page_title='Relatórios'; $section='reports';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/admin/reports.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function export() {
    require_login('admin');
    $type = $_GET['type'] ?? 'orders';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="'.$type.'_'.date('Ymd_His').'.csv"');
    $out = fopen('php://output', 'w');

    if ($type === 'orders') {
        fputcsv($out, ['ID','Cliente','Email','Estado','Pagamento','Subtotal','Envio','Total','Data']);
        foreach ((new OrderService())->allForAdmin() as $o) {
            fputcsv($out, [$o['id'],$o['client'],$o['email'],$o['status'],$o['payment_status'],$o['subtotal'],$o['shipping_cost'],$o['total'],$o['created_at']]);
        }
    } elseif ($type === 'tickets') {
        fputcsv($out, ['ID','Cliente','Título','Estado','Prioridade','CAT','Aberto','Fechado']);
        foreach ((new ServiceTicketService())->allForAdmin() as $t) {
            fputcsv($out, [$t['id'],$t['client'],$t['title'],$t['status'],$t['priority'],$t['cat_name'] ?? '',$t['opened_at'],$t['closed_at'] ?? '']);
        }
    } elseif ($type === 'users') {
        fputcsv($out, ['ID','Nome','Email','Role','Criado']);
        foreach ((new UserService())->listForAdmin() as $u) {
            fputcsv($out, [$u['id'],$u['name'],$u['email'],$u['role'],$u['created_at']]);
        }
    } else {
        fputcsv($out, ['error','unknown export type']);
    }
    fclose($out); exit;
}

function settings() {
    require_login('admin');
    $settings = (new SettingsService())->getAll();
    $page_title='Configurações'; $section='settings';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/admin/settings.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function settings_save() {
    require_login('admin');
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','CSRF.'); redirect('/admin/settings'); }
    (new SettingsService())->save($_POST);
    flash('success','Configurações guardadas.');
    redirect('/admin/settings');
}
