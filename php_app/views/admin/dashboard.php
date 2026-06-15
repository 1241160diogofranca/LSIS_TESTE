<div class="dash"><?php $user = current_user(); require __DIR__ . '/../_sidebar.php'; ?>
<div class="dash-main">
  <div class="dash-head"><div><h1>Painel Administrador</h1><p>Visão global de toda a operação do Meireles Connect.</p></div>
    <form method="GET" action="/erp/sync" style="display:inline">
      <button class="btn btn-outline" data-testid="erp-sync-btn"><i class="ph ph-arrows-clockwise"></i> Sincronizar ERP (mock)</button>
    </form>
  </div>
  <div class="kpi-grid">
    <div class="kpi"><div class="label">Utilizadores</div><div class="value" data-testid="kpi-users"><?= (int)$k['users'] ?></div></div>
    <div class="kpi"><div class="label">Produtos</div><div class="value" data-testid="kpi-products"><?= (int)$k['products'] ?></div></div>
    <div class="kpi"><div class="label">Encomendas</div><div class="value" data-testid="kpi-orders"><?= (int)$k['orders'] ?></div></div>
    <div class="kpi"><div class="label">Receita (pagas)</div><div class="value" data-testid="kpi-revenue"><?= money($k['revenue']) ?></div></div>
  </div>
  <div class="kpi-grid" style="grid-template-columns:repeat(3,1fr)">
    <div class="kpi"><div class="label">Assist. abertas</div><div class="value"><?= (int)$k['open_tix'] ?></div></div>
    <div class="kpi"><div class="label">Fechadas</div><div class="value"><?= (int)$k['closed_tix'] ?></div></div>
    <div class="kpi"><div class="label">Não atribuídas</div><div class="value" style="color:var(--accent-red)"><?= (int)$k['unassigned'] ?></div></div>
  </div>

  <h3 style="margin-top:2rem">Receita por mês (pagas)</h3>
  <?php
    $max = 1; foreach ($bars as $b) $max = max($max, (float)$b['tot']);
  ?>
  <div class="bars">
    <?php foreach ($bars as $b): $h = $max ? max(8, (int)((float)$b['tot']/$max*180)) : 8; ?>
      <div class="bar" style="height: <?= $h ?>px">
        <span><?= money((float)$b['tot']) ?></span>
        <small><?= e($b['ym']) ?></small>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="grid" style="grid-template-columns:1fr 1fr; gap:1.5rem; margin-top:3rem">
    <div class="card">
      <div class="flex-between"><h3>Encomendas recentes</h3><a href="/admin/orders" class="muted">Ver →</a></div>
      <table class="table"><thead><tr><th>#</th><th>Cliente</th><th>Estado</th><th>Total</th></tr></thead><tbody>
      <?php foreach ($recent_orders as $r): [$lbl,$cls]=status_label($r['status']); ?>
        <tr><td>#<?= (int)$r['id'] ?></td><td><?= e($r['client']) ?></td><td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td><td><?= money((float)$r['total']) ?></td></tr>
      <?php endforeach; ?></tbody></table>
    </div>
    <div class="card">
      <div class="flex-between"><h3>Pedidos assistência</h3><a href="/admin/tickets" class="muted">Ver →</a></div>
      <table class="table"><thead><tr><th>#</th><th>Cliente</th><th>Estado</th><th>Aberto</th></tr></thead><tbody>
      <?php foreach ($recent_tix as $r): [$lbl,$cls]=status_label($r['status']); ?>
        <tr><td>#<?= (int)$r['id'] ?></td><td><?= e($r['client']) ?></td><td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td><td><?= fmt_date($r['opened_at'],'d/m') ?></td></tr>
      <?php endforeach; ?></tbody></table>
    </div>
  </div>
</div></div>
