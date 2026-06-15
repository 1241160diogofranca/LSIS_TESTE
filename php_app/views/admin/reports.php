<div class="dash"><?php $user = current_user(); require __DIR__ . '/../_sidebar.php'; ?>
<div class="dash-main">
  <div class="dash-head"><div><h1>Relatórios</h1><p>Indicadores comerciais e operacionais.</p></div>
    <div class="flex gap-2">
      <a class="btn btn-outline" href="/admin/export?type=orders"><i class="ph ph-download"></i> CSV Encomendas</a>
      <a class="btn btn-outline" href="/admin/export?type=tickets"><i class="ph ph-download"></i> CSV Assistências</a>
      <a class="btn btn-outline" href="/admin/export?type=users"><i class="ph ph-download"></i> CSV Utilizadores</a>
    </div>
  </div>

  <h3>Receita mensal (últimos meses)</h3>
  <?php $maxr = 1; foreach ($by_month as $b) $maxr = max($maxr,(float)$b['tot']); ?>
  <div class="bars">
    <?php foreach ($by_month as $b): $h = max(8,(int)((float)$b['tot']/$maxr*180)); ?>
      <div class="bar" style="height:<?= $h ?>px">
        <span><?= money((float)$b['tot']) ?></span>
        <small><?= e($b['ym']) ?></small>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="grid" style="grid-template-columns:1fr 1fr;gap:1.5rem;margin-top:3rem">
    <div class="card">
      <h3>Receita por categoria</h3>
      <table class="table"><thead><tr><th>Categoria</th><th class="text-right">Total</th></tr></thead><tbody>
      <?php foreach ($by_cat as $c): ?><tr><td><?= e($c['name']) ?></td><td class="text-right"><?= money((float)$c['tot']) ?></td></tr><?php endforeach; ?>
      </tbody></table>
    </div>
    <div class="card">
      <h3>Assistências por estado</h3>
      <table class="table"><thead><tr><th>Estado</th><th class="text-right">#</th></tr></thead><tbody>
      <?php foreach ($tix_state as $s): [$lbl,$cls]=status_label($s['status']); ?>
        <tr><td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td><td class="text-right"><?= (int)$s['n'] ?></td></tr>
      <?php endforeach; ?></tbody></table>
    </div>
  </div>

  <div class="card" style="margin-top:1.5rem">
    <h3>Desempenho dos CAT</h3>
    <table class="table"><thead><tr><th>CAT</th><th class="text-right">Pedidos resolvidos</th><th class="text-right">Tempo médio (h)</th></tr></thead><tbody>
    <?php foreach ($cat_perf as $c): ?>
      <tr><td><?= e($c['name']) ?></td><td class="text-right"><?= (int)$c['n'] ?></td><td class="text-right"><?= $c['avg_hours'] ? round((float)$c['avg_hours'],1) : '-' ?></td></tr>
    <?php endforeach; ?></tbody></table>
  </div>
</div></div>
