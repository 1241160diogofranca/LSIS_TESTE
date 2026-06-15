<div class="dash"><?php $user = current_user(); require __DIR__ . '/../_sidebar.php'; ?>
<div class="dash-main">
  <div class="dash-head"><div><h1>Encomendas</h1><p>Todas as suas encomendas e os respetivos estados.</p></div></div>
  <?php if (empty($orders)): ?>
    <div class="card empty-state"><h3>Sem encomendas</h3><p>Quando fizer uma compra, aparecerá aqui.</p><a class="btn btn-primary" href="/catalog">Ir ao catálogo</a></div>
  <?php else: ?>
  <div class="table-wrap"><table class="table" data-testid="orders-table">
    <thead><tr><th>#</th><th>Data</th><th>Estado</th><th>Pagamento</th><th>Itens</th><th class="text-right">Total</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($orders as $o):
      [$slbl,$scls] = status_label($o['status']);
      [$plbl,$pcls] = status_label($o['payment_status']);
    ?>
      <tr>
        <td>#<?= (int)$o['id'] ?></td>
        <td><?= fmt_date($o['created_at']) ?></td>
        <td><span class="badge <?= $scls ?>"><?= $slbl ?></span></td>
        <td><span class="badge <?= $pcls ?>"><?= $plbl ?></span></td>
        <td><?php $n=(int)get_db()->query("SELECT COUNT(*) FROM order_items WHERE order_id=".(int)$o['id'])->fetchColumn(); echo $n; ?></td>
        <td class="text-right"><?= money((float)$o['total']) ?></td>
        <td><a class="btn btn-outline btn-sm" href="/account/order?id=<?= (int)$o['id'] ?>" data-testid="order-view-<?= (int)$o['id'] ?>">Detalhes</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody></table></div>
  <?php endif; ?>
</div></div>
