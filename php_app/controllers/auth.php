<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

function show_login() {
    $page_title = 'Entrar';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/login.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function do_login() {
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','Sessão expirou. Tente novamente.'); redirect('/login'); }
    $email = trim($_POST['email'] ?? '');
    $pw    = $_POST['password'] ?? '';
    $db = get_db();
    $st = $db->prepare("SELECT * FROM users WHERE email=?");
    $st->execute([$email]);
    $u = $st->fetch();
    if (!$u || !password_verify($pw, $u['password_hash'])) {
        flash('error', 'Credenciais inválidas.');
        redirect('/login');
    }
    $_SESSION['user'] = [
        'id'=>(int)$u['id'], 'name'=>$u['name'], 'email'=>$u['email'],
        'role'=>$u['role'], 'store_id'=>$u['store_id'], 'cat_id'=>$u['cat_id']
    ];
    log_event('login', $email);
    flash('success', 'Sessão iniciada, bem-vindo(a) ' . $u['name'] . '.');
    // Role-based redirect
    $dest = '/account';
    if ($u['role'] === 'admin')          $dest = '/admin';
    elseif ($u['role'] === 'store_manager') $dest = '/store';
    elseif ($u['role'] === 'cat')        $dest = '/cat';
    redirect($dest);
}

function show_register() {
    $page_title = 'Criar conta';
    require __DIR__ . '/../views/_layout_top.php';
    require __DIR__ . '/../views/register.php';
    require __DIR__ . '/../views/_layout_bottom.php';
}

function do_register() {
    if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','Sessão expirou.'); redirect('/register'); }
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $pw    = $_POST['password'] ?? '';
    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pw) < 6) {
        flash('error', 'Preencha todos os campos corretamente (palavra-passe ≥ 6).'); redirect('/register');
    }
    $db = get_db();
    $st = $db->prepare("SELECT id FROM users WHERE email=?");
    $st->execute([$email]);
    if ($st->fetch()) { flash('error','Email já registado.'); redirect('/register'); }
    $hash = password_hash($pw, PASSWORD_BCRYPT);
    $st = $db->prepare("INSERT INTO users (name,email,password_hash,role,phone) VALUES (?,?,?,?,?)");
    $st->execute([$name,$email,$hash,'consumer',$phone]);
    $id = (int)$db->lastInsertId();
    $_SESSION['user'] = ['id'=>$id,'name'=>$name,'email'=>$email,'role'=>'consumer','store_id'=>null,'cat_id'=>null];
    notify($id, 'Bem-vindo à Meireles Connect', 'A sua conta foi criada com sucesso. Pode agora ativar garantias e abrir pedidos de assistência.');
    log_event('register', $email);
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
