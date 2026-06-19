<?php
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../bll/AuthService.php';

function show_login() {
    $page_title = 'Entrar';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/login.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function do_login() {
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','Sessão expirou. Tente novamente.'); redirect('/login'); }

    $svc = new AuthService();
    $user = $svc->login(trim($_POST['email'] ?? ''), $_POST['password'] ?? '');
    if (!$user) { flash('error', 'Credenciais inválidas.'); redirect('/login'); }

    $_SESSION['user'] = $user;
    log_event('login', $user['email']);
    flash('success', 'Sessão iniciada, bem-vindo(a) ' . $user['name'] . '.');
    redirect($svc->dashboardUrlFor($user['role']));
}

function show_register() {
    $page_title = 'Criar conta';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/register.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function do_register() {
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','Sessão expirou.'); redirect('/register'); }

    $svc = new AuthService();
    [$ok, $id, $err] = $svc->register(
        trim($_POST['name']  ?? ''),
        trim($_POST['email'] ?? ''),
        trim($_POST['phone'] ?? ''),
        $_POST['password']   ?? ''
    );
    if (!$ok) { flash('error', $err); redirect('/register'); }

    $_SESSION['user'] = [
        'id'=>$id, 'name'=>trim($_POST['name']), 'email'=>trim($_POST['email']),
        'role'=>'consumer', 'store_id'=>null, 'cat_id'=>null
    ];
    require_once __DIR__ . '/../bll/NotificationService.php';
    (new NotificationService())->send($id, 'Bem-vindo à Meireles Connect',
        'A sua conta foi criada com sucesso. Pode agora activar garantias e abrir pedidos de assistência.');
    log_event('register', $_POST['email']);
    flash('success','Conta criada com sucesso!');
    redirect('/account');
}

function do_logout() {
    start_session_once();
    $_SESSION = [];
    session_destroy();
    flash('success','Sessão terminada.');
    redirect('/');
}
