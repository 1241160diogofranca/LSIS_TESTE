<div class="dash"><?php $user = current_user(); require __DIR__ . '/../_sidebar.php'; ?>
<div class="dash-main">
  <div class="dash-head"><div><h1>Encomendas</h1><p>Visualização geral das encomendas para apoio ao cliente.</p></div></div>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>#</th><th>Cliente</th><th>Email</th><th>Estado</th><th>Pagamento</th><th>Total</th><th>Data</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): [$sl,$sc]=status_label($r['status']); [$pl,$pc]=status_label($r['payment_status']); ?>
      <tr><td>#<?= (int)$r['id'] ?></td><td><?= e($r['client']) ?></td><td><?= e($r['email']) ?></td>
        <td><span class="badge <?= $sc ?>"><?= $sl ?></span></td>
        <td><span class="badge <?= $pc ?>"><?= $pl ?></span></td>
        <td><?= money((float)$r['total']) ?></td><td><?= fmt_date($r['created_at'],'d/m/Y') ?></td></tr>
    <?php endforeach; ?>
    </tbody></table></div>
</div></div>
