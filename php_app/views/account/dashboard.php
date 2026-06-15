<div class="dash">
<?php $user = current_user(); require __DIR__ . '/../_sidebar.php'; ?>
<div class="dash-main">
  <div class="dash-head">
    <div>
      <h1>Olá, <?= e(explode(' ', $user['name'])[0]) ?> 👋</h1>
      <p>Bem-vindo(a) à sua área pessoal Meireles Connect.</p>
    </div>
    <a class="btn btn-primary" href="/catalog"><i class="ph ph-storefront"></i> Comprar</a>
  </div>

  <div class="kpi-grid">
    <div class="kpi" data-testid="kpi-orders"><div class="label">Encomendas</div><div class="value"><?= (int)$n_orders ?></div></div>
    <div class="kpi" data-testid="kpi-warranties"><div class="label">Garantias</div><div class="value"><?= (int)$n_wars ?></div></div>
    <div class="kpi" data-testid="kpi-tickets"><div class="label">Assistências</div><div class="value"><?= (int)$n_tix ?></div></div>
    <div class="kpi" data-testid="kpi-cart"><div class="label">No carrinho</div><div class="value"><?= cart_count() ?></div></div>
  </div>

  <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <div class="card">
      <div class="flex-between"><h3>Últimas encomendas</h3><a href="/account/orders" class="muted">Ver todas →</a></div>
      <?php if (empty($orders)): ?><p class="muted">Sem encomendas ainda.</p><?php else: ?>
        <table class="table">
          <thead><tr><th>#</th><th>Data</th><th>Estado</th><th class="text-right">Total</th></tr></thead>
          <tbody>
          <?php foreach ($orders as $o): [$lbl,$cls] = status_label($o['status']); ?>
            <tr><td><a href="/account/order?id=<?= (int)$o['id'] ?>">#<?= (int)$o['id'] ?></a></td>
              <td><?= fmt_date($o['created_at'],'d/m/Y') ?></td>
              <td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td>
              <td class="text-right"><?= money((float)$o['total']) ?></td></tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
    <div class="card">
      <div class="flex-between"><h3>Garantias recentes</h3><a href="/account/warranties" class="muted">Gerir →</a></div>
      <?php if (empty($warranties)): ?><p class="muted">Sem garantias ativas.</p><?php else: ?>
        <table class="table"><thead><tr><th>Produto</th><th>S/N</th><th>Válida até</th></tr></thead><tbody>
        <?php foreach ($warranties as $w): ?>
          <tr><td><?= e($w['product_name']) ?></td><td><?= e($w['serial_number']) ?></td><td><?= fmt_date($w['expiry_date'],'d/m/Y') ?></td></tr>
        <?php endforeach; ?></tbody></table>
      <?php endif; ?>
    </div>
  </div>

  <div class="spacer-md"></div>
  <div class="card">
    <div class="flex-between"><h3>Últimos pedidos de assistência</h3><a href="/account/service" class="muted">Ver todos →</a></div>
    <?php if (empty($tickets)): ?><p class="muted">Sem pedidos ainda.</p><?php else: ?>
      <table class="table"><thead><tr><th>#</th><th>Título</th><th>Estado</th><th>Aberto em</th></tr></thead><tbody>
      <?php foreach ($tickets as $t): [$lbl,$cls] = status_label($t['status']); ?>
        <tr><td><a href="/account/service?#t<?= (int)$t['id'] ?>">#<?= (int)$t['id'] ?></a></td><td><?= e($t['title']) ?></td><td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td><td><?= fmt_date($t['opened_at']) ?></td></tr>
      <?php endforeach; ?></tbody></table>
    <?php endif; ?>
  </div>
</div>
</div>
