<?php
$role = $user['role'] ?? '';
$sidebar = [
  'consumer' => [
    ['/account', 'gauge', 'Início', 'dashboard'],
    ['/account/orders', 'package', 'Encomendas', 'orders'],
    ['/account/warranties', 'shield-check', 'Garantias', 'warranties'],
    ['/account/service', 'wrench', 'Assistência', 'service'],
    ['/account/notifications', 'bell', 'Notificações', 'notifications'],
  ],
  'store_manager' => [
    ['/store', 'gauge', 'Dashboard', 'dashboard'],
    ['/store/orders', 'package', 'Encomendas', 'orders'],
    ['/store/tickets', 'wrench', 'Assistências', 'tickets'],
  ],
  'cat' => [
    ['/cat', 'gauge', 'Dashboard', 'dashboard'],
    ['/cat/tickets', 'wrench', 'Pedidos', 'tickets'],
  ],
  'admin' => [
    ['/admin', 'gauge', 'Dashboard', 'dashboard'],
    ['/admin/users', 'users-three', 'Utilizadores', 'users'],
    ['/admin/products', 'cube', 'Produtos', 'products'],
    ['/admin/orders', 'package', 'Encomendas', 'orders'],
    ['/admin/tickets', 'wrench', 'Assistências', 'tickets'],
    ['/admin/reports', 'chart-bar', 'Relatórios', 'reports'],
    ['/admin/settings', 'gear', 'Configurações', 'settings'],
  ],
];
$items = $sidebar[$role] ?? [];
?>
<aside class="dash-side">
  <h5><?= ['consumer'=>'Cliente','store_manager'=>'Loja','cat'=>'CAT','admin'=>'Backoffice'][$role] ?? '' ?></h5>
  <?php foreach ($items as $it): [$href,$icon,$label,$key] = $it; ?>
    <a href="<?= e($href) ?>" class="<?= ($section ?? '') === $key ? 'active' : '' ?>" data-testid="side-<?= e($key) ?>"><i class="ph ph-<?= e($icon) ?>"></i> <?= e($label) ?></a>
  <?php endforeach; ?>
  <?php if ($role === 'admin'): ?>
    <h5>Integrações</h5>
    <a href="/erp/sync" data-testid="side-erp-sync"><i class="ph ph-arrows-clockwise"></i> Sincronizar ERP SAGE</a>
  <?php endif; ?>
</aside>
