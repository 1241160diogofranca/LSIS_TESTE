<div class="dash"><?php $user = current_user(); require __DIR__ . '/../_sidebar.php'; ?>
<div class="dash-main">
  <div class="dash-head"><div><h1>Encomendas</h1><p>Gestão centralizada de encomendas e estados.</p></div>
    <a href="/admin/export?type=orders" class="btn btn-outline" data-testid="export-orders-btn"><i class="ph ph-download"></i> Exportar CSV</a>
  </div>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>#</th><th>Cliente</th><th>Email</th><th>Estado</th><th>Pagamento</th><th>Total</th><th>Data</th><th>Ações</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): [$sl,$sc]=status_label($r['status']); [$pl,$pc]=status_label($r['payment_status']); ?>
      <tr><td>#<?= (int)$r['id'] ?></td><td><?= e($r['client']) ?></td><td><?= e($r['email']) ?></td>
        <td><span class="badge <?= $sc ?>"><?= $sl ?></span></td><td><span class="badge <?= $pc ?>"><?= $pl ?></span></td>
        <td><?= money((float)$r['total']) ?></td><td><?= fmt_date($r['created_at'],'d/m/Y') ?></td>
        <td>
          <form method="POST" action="/admin/orders/update" class="flex gap-2">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="order_id" value="<?= (int)$r['id'] ?>">
            <select class="select" name="status" style="padding:.4rem .6rem;font-size:.85rem">
              <?php foreach (['pending_payment','paid','processing','shipped','in_transit','delivered','cancelled'] as $s): [$l,]=status_label($s); ?>
                <option value="<?= $s ?>" <?= $r['status']===$s?'selected':'' ?>><?= $l ?></option>
              <?php endforeach; ?>
            </select>
            <button class="btn btn-primary btn-sm" data-testid="order-update-<?= (int)$r['id'] ?>">OK</button>
          </form>
        </td></tr>
    <?php endforeach; ?>
    </tbody></table></div>
</div></div>
