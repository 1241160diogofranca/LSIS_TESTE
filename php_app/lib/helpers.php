<?php
// Common helpers: auth, session, csrf, flash messages, url helpers, json responses.

function start_session_once(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params(['lifetime'=>0,'path'=>'/','httponly'=>true,'samesite'=>'Lax']);
        session_start();
    }
}

function csrf_token(): string {
    start_session_once();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_check(?string $token): bool {
    start_session_once();
    return !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)$token);
}

function current_user(): ?array {
    start_session_once();
    return $_SESSION['user'] ?? null;
}

function require_login(?string $role = null): void {
    $u = current_user();
    if (!$u) {
        flash('error', 'Sessão necessária.');
        redirect('/login');
    }
    if ($role && $u['role'] !== $role) {
        http_response_code(403);
        flash('error', 'Sem permissão para aceder a esta página.');
        redirect('/');
    }
}

function require_role(array $roles): void {
    $u = current_user();
    if (!$u || !in_array($u['role'], $roles, true)) {
        http_response_code(403);
        flash('error', 'Sem permissão para aceder a esta página.');
        redirect('/');
    }
}

function redirect(string $path): void {
    header('Location: ' . $path);
    exit;
}

function flash(string $key, ?string $value = null) {
    start_session_once();
    if ($value === null) {
        $v = $_SESSION['flash'][$key] ?? null;
        if ($v !== null) unset($_SESSION['flash'][$key]);
        return $v;
    }
    $_SESSION['flash'][$key] = $value;
}

function e(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function money(float $v): string {
    return number_format($v, 2, ',', '.') . ' €';
}

function fmt_date(?string $datetime, string $fmt = 'd/m/Y H:i'): string {
    if (!$datetime) return '-';
    $ts = strtotime($datetime);
    return $ts ? date($fmt, $ts) : $datetime;
}

function status_label(string $status): array {
    $map = [
        'pending_payment' => ['Aguarda pagamento', 'warn'],
        'paid'            => ['Pago',              'info'],
        'processing'      => ['Em processamento',  'info'],
        'shipped'         => ['Em expedição',      'info'],
        'in_transit'      => ['Em trânsito',       'info'],
        'delivered'       => ['Entregue',          'ok'],
        'cancelled'       => ['Cancelada',         'danger'],
        'open'            => ['Aberto',            'warn'],
        'assigned'        => ['Atribuído',         'info'],
        'in_progress'     => ['Em curso',          'info'],
        'awaiting_parts'  => ['Aguarda peças',     'warn'],
        'closed'          => ['Fechado',           'ok'],
        'active'          => ['Activa',            'ok'],
        'expired'         => ['Expirada',          'danger'],
        'pending_doc'     => ['Aguarda documento', 'warn'],
        'unpaid'          => ['Por pagar',         'warn'],
        'refunded'        => ['Reembolsado',       'danger'],
    ];
    return $map[$status] ?? [$status, 'info'];
}

function json_response($data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

function log_event(string $action, ?string $details = null): void {
    try {
        $db = get_db();
        $u = current_user();
        $st = $db->prepare("INSERT INTO logs (user_id, action, details, ip) VALUES (?,?,?,?)");
        $st->execute([$u['id'] ?? null, $action, $details, $_SERVER['REMOTE_ADDR'] ?? null]);
    } catch (Throwable $e) { /* swallow */ }
}

function notify(int $user_id, string $title, string $body, string $type='info'): void {
    try {
        $db = get_db();
        $st = $db->prepare("INSERT INTO notifications (user_id,title,body,type) VALUES (?,?,?,?)");
        $st->execute([$user_id, $title, $body, $type]);
    } catch (Throwable $e) { /* swallow */ }
}

function cart_get(): array {
    start_session_once();
    return $_SESSION['cart'] ?? [];
}
function cart_set(array $cart): void {
    start_session_once();
    $_SESSION['cart'] = $cart;
}
function cart_count(): int {
    $sum = 0;
    foreach (cart_get() as $i) $sum += (int)$i['qty'];
    return $sum;
}
function cart_subtotal(): float {
    $sum = 0.0;
    foreach (cart_get() as $i) $sum += (float)$i['price'] * (int)$i['qty'];
    return $sum;
}
