<?php require_once __DIR__ . '/../lib/helpers.php'; ?><!doctype html>
<html lang="pt">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($page_title) ? e($page_title).' · ' : '' ?>Meireles Connect</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/app.css">
<script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<?php $user = current_user(); ?>
<header class="topbar">
  <div class="container topbar-inner">
    <a class="brand" href="/" data-testid="brand-link">
      <span class="brand-mark"><i class="ph-fill ph-flame"></i></span>
      <span class="brand-name">Meireles<span>Connect</span></span>
    </a>
    <nav class="topnav" data-testid="main-nav">
      <a href="/catalog" data-testid="nav-catalog">Catálogo</a>
      <a href="/parts" data-testid="nav-parts">Peças</a>
      <?php if ($user): ?>
        <?php if ($user['role'] === 'consumer'): ?>
          <a href="/account" data-testid="nav-account">A minha conta</a>
        <?php elseif ($user['role'] === 'store_manager'): ?>
          <a href="/store" data-testid="nav-store">Loja</a>
        <?php elseif ($user['role'] === 'cat'): ?>
          <a href="/cat" data-testid="nav-cat">Assistência</a>
        <?php elseif ($user['role'] === 'admin'): ?>
          <a href="/admin" data-testid="nav-admin">Backoffice</a>
        <?php endif; ?>
      <?php endif; ?>
    </nav>
    <div class="topbar-actions">
      <a class="icon-btn" href="/cart" data-testid="nav-cart" title="Carrinho">
        <i class="ph ph-shopping-cart"></i>
        <?php $cc = cart_count(); if ($cc > 0): ?><span class="badge-count"><?= $cc ?></span><?php endif; ?>
      </a>
      <?php if ($user): ?>
        <div class="user-menu" data-testid="user-menu">
          <button class="user-btn" onclick="document.getElementById('umenu').classList.toggle('open')">
            <span class="avatar"><?= e(strtoupper(substr($user['name'],0,1))) ?></span>
            <span class="user-name"><?= e($user['name']) ?></span>
            <i class="ph ph-caret-down"></i>
          </button>
          <div class="umenu" id="umenu">
            <div class="umenu-head">
              <strong><?= e($user['name']) ?></strong>
              <small><?= e($user['email']) ?></small>
              <span class="role-tag"><?= e($user['role']) ?></span>
            </div>
            <a href="/account/notifications" data-testid="nav-notifications"><i class="ph ph-bell"></i> Notificações</a>
            <form method="POST" action="/logout" style="margin:0">
              <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
              <button type="submit" class="umenu-logout" data-testid="logout-btn"><i class="ph ph-sign-out"></i> Terminar sessão</button>
            </form>
          </div>
        </div>
      <?php else: ?>
        <a class="btn btn-ghost" href="/login" data-testid="nav-login">Entrar</a>
        <a class="btn btn-primary" href="/register" data-testid="nav-register">Criar conta</a>
      <?php endif; ?>
    </div>
  </div>
</header>
<?php $okFlash = flash('success'); $errFlash = flash('error'); ?>
<?php if ($okFlash || $errFlash): ?>
<div class="toaster" id="toaster">
  <?php if ($okFlash): ?><div class="toast toast-ok" data-testid="flash-success"><i class="ph ph-check-circle"></i><?= e($okFlash) ?></div><?php endif; ?>
  <?php if ($errFlash): ?><div class="toast toast-err" data-testid="flash-error"><i class="ph ph-warning"></i><?= e($errFlash) ?></div><?php endif; ?>
</div>
<?php endif; ?>
<main class="site-main">
