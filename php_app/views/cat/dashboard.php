<div class="dash"><?php $user = current_user(); require __DIR__ . '/../_sidebar.php'; ?>
<div class="dash-main">
  <div class="dash-head"><div><h1>Centro de Assistência Técnica</h1><p>Pedidos atribuídos ao seu CAT.</p></div></div>
  <div class="kpi-grid">
    <div class="kpi"><div class="label">Total</div><div class="value"><?= (int)$kpi_total ?></div></div>
    <div class="kpi"><div class="label">Abertos / em curso</div><div class="value"><?= (int)$kpi_open ?></div></div>
    <div class="kpi"><div class="label">Fechados</div><div class="value"><?= (int)$kpi_closed ?></div></div>
    <div class="kpi"><div class="label">Resolução média</div><div class="value"><?= (float)$kpi_avg ?>h</div></div>
  </div>
  <div class="card">
    <div class="flex-between"><h3>Pedidos recentes</h3><a href="/cat/tickets" class="muted">Ver todos →</a></div>
    <table class="table">
      <thead><tr><th>#</th><th>Cliente</th><th>Produto</th><th>Estado</th><th>Prioridade</th><th>Aberto</th><th></th></tr></thead>
      <tbody><?php foreach ($recent as $r): [$lbl,$cls]=status_label($r['status']); ?>
        <tr><td>#<?= (int)$r['id'] ?></td><td><?= e($r['client']) ?></td><td><?= e($r['product_name'] ?? '-') ?></td>
          <td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td><td><?= e($r['priority']) ?></td>
          <td><?= fmt_date($r['opened_at'],'d/m/Y') ?></td>
          <td><a class="btn btn-outline btn-sm" href="/cat/ticket?id=<?= (int)$r['id'] ?>">Abrir</a></td></tr>
      <?php endforeach; ?></tbody>
    </table>
  </div>
</div></div>
