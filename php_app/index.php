<?php
// Front controller: routes URLs to controllers.
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/lib/helpers.php';

// Bootstrap: ensure DB schema + seed exists.
try {
    get_db();
} catch (Throwable $e) {
    require_once __DIR__ . '/config/init_db.php';
    run_init_db();
}

start_session_once();

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

$routes = [
    // public
    ['GET',  '/',                       'controllers/home.php'],
    ['GET',  '/catalog',                'controllers/catalog.php'],
    ['GET',  '/product',                'controllers/product.php'],
    ['GET',  '/parts',                  'controllers/parts.php'],
    ['GET',  '/login',                  'controllers/auth.php',   'show_login'],
    ['POST', '/login',                  'controllers/auth.php',   'do_login'],
    ['GET',  '/register',               'controllers/auth.php',   'show_register'],
    ['POST', '/register',               'controllers/auth.php',   'do_register'],
    ['POST', '/logout',                 'controllers/auth.php',   'do_logout'],

    // cart + checkout
    ['GET',  '/cart',                   'controllers/cart.php',   'show'],
    ['POST', '/cart/add',               'controllers/cart.php',   'add'],
    ['POST', '/cart/remove',            'controllers/cart.php',   'remove'],
    ['POST', '/cart/update',            'controllers/cart.php',   'update'],
    ['GET',  '/checkout',               'controllers/checkout.php','show'],
    ['POST', '/checkout',               'controllers/checkout.php','submit'],
    ['POST', '/checkout/pay',           'controllers/checkout.php','pay'],

    // account (consumer)
    ['GET',  '/account',                'controllers/account.php',     'dashboard'],
    ['GET',  '/account/orders',        'controllers/account.php',     'orders'],
    ['GET',  '/account/order',         'controllers/account.php',     'order_detail'],
    ['GET',  '/account/warranties',    'controllers/warranties.php',  'index'],
    ['POST', '/account/warranties/activate', 'controllers/warranties.php', 'activate'],
    ['GET',  '/account/service',       'controllers/service.php',     'index'],
    ['POST', '/account/service/open',  'controllers/service.php',     'open_ticket'],
    ['GET',  '/account/service/view',  'controllers/service.php',     'view'],
    ['GET',  '/account/notifications', 'controllers/account.php',     'notifications'],

    // store manager
    ['GET',  '/store',                  'controllers/store.php',  'dashboard'],
    ['GET',  '/store/orders',           'controllers/store.php',  'orders'],
    ['GET',  '/store/tickets',          'controllers/store.php',  'tickets'],

    // CAT
    ['GET',  '/cat',                    'controllers/cat.php',    'dashboard'],
    ['GET',  '/cat/tickets',            'controllers/cat.php',    'tickets'],
    ['GET',  '/cat/ticket',             'controllers/cat.php',    'ticket_view'],
    ['POST', '/cat/ticket/update',      'controllers/cat.php',    'ticket_update'],

    // admin
    ['GET',  '/admin',                  'controllers/admin.php',  'dashboard'],
    ['GET',  '/admin/users',            'controllers/admin.php',  'users'],
    ['POST', '/admin/users/save',       'controllers/admin.php',  'users_save'],
    ['GET',  '/admin/products',         'controllers/admin.php',  'products'],
    ['POST', '/admin/products/save',    'controllers/admin.php',  'products_save'],
    ['GET',  '/admin/orders',           'controllers/admin.php',  'orders'],
    ['POST', '/admin/orders/update',    'controllers/admin.php',  'orders_update'],
    ['GET',  '/admin/tickets',          'controllers/admin.php',  'tickets'],
    ['POST', '/admin/tickets/assign',   'controllers/admin.php',  'tickets_assign'],
    ['GET',  '/admin/reports',          'controllers/admin.php',  'reports'],
    ['GET',  '/admin/export',           'controllers/admin.php',  'export'],
    ['GET',  '/admin/settings',         'controllers/admin.php',  'settings'],
    ['POST', '/admin/settings/save',    'controllers/admin.php',  'settings_save'],

    // mocked ERP endpoint
    ['GET',  '/erp/sync',               'controllers/erp.php',    'sync'],
];

foreach ($routes as $route) {
    [$rm, $rp, $file] = [$route[0], $route[1], $route[2]];
    $action = $route[3] ?? 'index';
    if ($rm === $method && $rp === $path) {
        require __DIR__ . '/' . $file;
        if (function_exists($action)) {
            $action();
        }
        return;
    }
}

http_response_code(404);
require __DIR__ . '/views/_layout_top.php';
echo '<div class="container"><div class="empty-state"><h1>404</h1><p>Página não encontrada.</p><a class="btn btn-primary" href="/" data-testid="back-home-btn">Voltar à página inicial</a></div></div>';
require __DIR__ . '/views/_layout_bottom.php';
