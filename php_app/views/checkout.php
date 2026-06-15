<div class="container">
  <div class="section-head"><span class="eyebrow">Checkout</span><h1>Finalizar compra</h1></div>
  <div class="steps">
    <div class="step done"><span class="num">1</span> Carrinho</div>
    <div class="step active"><span class="num">2</span> Morada</div>
    <div class="step"><span class="num">3</span> Pagamento</div>
    <div class="step"><span class="num">4</span> Confirmação</div>
  </div>
  <div class="cart-wrap">
    <div class="card">
      <h3>Dados de entrega</h3>
      <form method="POST" action="/checkout" class="form" data-testid="checkout-form">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div class="form-row">
          <div class="form-group"><label>Nome</label><input class="input" value="<?= e($user['name']) ?>" disabled></div>
          <div class="form-group"><label>Email</label><input class="input" value="<?= e($user['email']) ?>" disabled></div>
        </div>
        <div class="form-group">
          <label>Morada completa de entrega</label>
          <textarea class="textarea" name="address" required placeholder="Rua, número, código postal, cidade" data-testid="checkout-address"></textarea>
        </div>
        <div class="form-group">
          <label>Método de pagamento</label>
          <div class="grid" style="grid-template-columns:repeat(3,1fr);gap:.75rem">
            <label class="card card-tight" style="cursor:pointer;text-align:center">
              <input type="radio" name="pay_method" value="card" checked> <i class="ph ph-credit-card"></i><br><small>Cartão</small></label>
            <label class="card card-tight" style="cursor:pointer;text-align:center">
              <input type="radio" name="pay_method" value="mbway"> <i class="ph ph-device-mobile"></i><br><small>MB WAY</small></label>
            <label class="card card-tight" style="cursor:pointer;text-align:center">
              <input type="radio" name="pay_method" value="transfer"> <i class="ph ph-bank"></i><br><small>Transferência</small></label>
          </div>
          <div class="hint">Pagamento simulado — não será processado nenhum valor real.</div>
        </div>
        <button class="btn btn-primary btn-lg" data-testid="checkout-submit"><i class="ph ph-shield-check"></i> Confirmar encomenda</button>
      </form>
    </div>
    <aside class="cart-summary">
      <h3>Resumo</h3>
      <?php foreach ($cart as $i): ?>
        <div class="summary-line"><span><?= e($i['name']) ?> ×<?= (int)$i['qty'] ?></span><span><?= money($i['price']*$i['qty']) ?></span></div>
      <?php endforeach; ?>
      <div class="summary-line"><span>Subtotal</span><span><?= money(cart_subtotal()) ?></span></div>
      <div class="summary-line"><span>Envio</span><span>5,90 €</span></div>
      <div class="summary-line total"><span>Total</span><span><?= money(cart_subtotal() + 5.90) ?></span></div>
    </aside>
  </div>
</div>
