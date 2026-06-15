<div class="dash"><?php $user = current_user(); require __DIR__ . '/../_sidebar.php'; ?>
<div class="dash-main">
  <div class="dash-head"><div><h1>Pedidos de Assistência</h1><p>Apoio operacional aos clientes da sua loja.</p></div></div>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>#</th><th>Cliente</th><th>Título</th><th>Estado</th><th>CAT</th><th>Aberto</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): [$lbl,$cls]=status_label($r['status']); ?>
      <tr><td>#<?= (int)$r['id'] ?></td><td><?= e($r['client']) ?></td><td><?= e($r['title']) ?></td>
        <td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td><td><?= e($r['cat_name'] ?? '-') ?></td><td><?= fmt_date($r['opened_at']) ?></td></tr>
    <?php endforeach; ?>
    </tbody></table></div>
</div></div>
