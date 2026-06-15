<div class="dash"><?php $user = current_user(); require __DIR__ . '/../_sidebar.php'; ?>
<div class="dash-main">
  <a href="/account/orders" class="muted"><i class="ph ph-arrow-left"></i> Voltar</a>
  <div class="dash-head" style="margin-top:1rem">
    <div><h1>Encomenda #<?= (int)$order['id'] ?></h1>
      <p>Criada em <?= fmt_date($order['created_at']) ?></p></div>
    <?php [$slbl,$scls] = status_label($order['status']); ?>
    <span class="badge <?= $scls ?>" style="font-size:.9rem;padding:.5rem 1rem"><?= $slbl ?></span>
  </div>

  <?php
    $statuses = ['pending_payment','paid','processing','shipped','in_transit','delivered'];
    $cur = array_search($order['status'], $statuses, true);
    $cur = $cur === false ? 0 : $cur;
  ?>
  <div class="steps">
    <?php foreach ($statuses as $i => $s): [$lbl,$_] = status_label($s); ?>
      <div class="step <?= $i<$cur?'done':($i===$cur?'active':'') ?>"><span class="num"><?= $i+1 ?></span> <?= $lbl ?></div>
    <?php endforeach; ?>
  </div>

  <div class="cart-wrap">
    <div class="card">
      <h3>Itens</h3>
      <?php foreach ($items as $i): ?>
        <div class="cart-item">
          <div></div>
          <div><strong><?= e($i['name']) ?></strong><br><small class="muted"><?= $i['item_type']==='part'?'Peça':'Produto' ?> × <?= (int)$i['quantity'] ?></small></div>
          <div class="text-right"><strong><?= money((float)$i['line_total']) ?></strong></div>
        </div>
      <?php endforeach; ?>
    </div>
    <aside class="cart-summary">
      <h3>Detalhes</h3>
      <div class="summary-line"><span>Subtotal</span><span><?= money((float)$order['subtotal']) ?></span></div>
      <div class="summary-line"><span>Envio</span><span><?= money((float)$order['shipping_cost']) ?></span></div>
      <div class="summary-line total"><span>Total</span><span><?= money((float)$order['total']) ?></span></div>
      <div class="divider"></div>
      <strong>Morada</strong>
      <p class="muted"><?= nl2br(e($order['shipping_address'])) ?></p>
      <?php if ($order['payment_status'] !== 'paid'): ?>
      <form method="POST" action="/checkout/pay">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
        <button class="btn btn-primary btn-block btn-lg" data-testid="order-pay-btn"><i class="ph ph-credit-card"></i> Pagar agora (simulado)</button>
      </form>
      <?php else: ?>
        <div class="badge ok" style="width:100%;justify-content:center;padding:.6rem"><i class="ph ph-check-circle"></i> Pagamento confirmado</div>
      <?php endif; ?>
    </aside>
  </div>
</div></div>
