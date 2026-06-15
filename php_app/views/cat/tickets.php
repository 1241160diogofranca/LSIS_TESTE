<div class="dash"><?php $user = current_user(); require __DIR__ . '/../_sidebar.php'; ?>
<div class="dash-main">
  <div class="dash-head"><div><h1>Pedidos atribuídos</h1><p>Gestão completa de cada pedido de assistência.</p></div></div>
  <?php if (empty($rows)): ?>
    <div class="card empty-state"><h3>Sem pedidos</h3><p>Quando lhe atribuírem pedidos, aparecerão aqui.</p></div>
  <?php else: ?>
  <div class="table-wrap"><table class="table" data-testid="cat-tickets-table">
    <thead><tr><th>#</th><th>Cliente</th><th>Produto</th><th>Título</th><th>Estado</th><th>Prioridade</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): [$lbl,$cls]=status_label($r['status']); ?>
      <tr><td>#<?= (int)$r['id'] ?></td><td><?= e($r['client']) ?></td><td><?= e($r['product_name'] ?? '-') ?></td>
        <td><?= e($r['title']) ?></td><td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td>
        <td><?= e($r['priority']) ?></td>
        <td><a class="btn btn-primary btn-sm" href="/cat/ticket?id=<?= (int)$r['id'] ?>" data-testid="cat-ticket-open-<?= (int)$r['id'] ?>">Abrir</a></td></tr>
    <?php endforeach; ?>
    </tbody></table></div>
  <?php endif; ?>
</div></div>
