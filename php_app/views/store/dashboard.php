<div class="dash"><?php $user = current_user(); require __DIR__ . '/../_sidebar.php'; ?>
<div class="dash-main">
  <div class="dash-head"><div><h1>Painel da Loja</h1><p>Acompanhe encomendas e assistências associadas.</p></div></div>
  <div class="kpi-grid">
    <div class="kpi"><div class="label">Encomendas</div><div class="value"><?= (int)$kpi_orders ?></div></div>
    <div class="kpi"><div class="label">Receita (pagas)</div><div class="value"><?= money($kpi_revenue) ?></div></div>
    <div class="kpi"><div class="label">Assistências abertas</div><div class="value"><?= (int)$kpi_open_tix ?></div></div>
    <div class="kpi"><div class="label">Entregues</div><div class="value"><?= (int)$kpi_delivered ?></div></div>
  </div>
  <div class="card">
    <div class="flex-between"><h3>Encomendas recentes</h3><a href="/store/orders" class="muted">Ver todas →</a></div>
    <table class="table">
      <thead><tr><th>#</th><th>Cliente</th><th>Estado</th><th>Total</th><th>Data</th></tr></thead>
      <tbody>
        <?php foreach ($recent as $r): [$lbl,$cls]=status_label($r['status']); ?>
          <tr><td>#<?= (int)$r['id'] ?></td><td><?= e($r['client']) ?></td><td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td><td><?= money((float)$r['total']) ?></td><td><?= fmt_date($r['created_at'],'d/m/Y') ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div></div>
