<div class="container">
  <div class="section-head"><span class="eyebrow">Compras</span><h1>O seu carrinho</h1></div>
  <?php if (empty($cart)): ?>
    <div class="empty-state card">
      <i class="ph ph-shopping-cart-simple" style="font-size:3rem;color:var(--text-muted)"></i>
      <h2>O carrinho está vazio</h2>
      <p>Adicione produtos do catálogo ou peças para começar.</p>
      <a class="btn btn-primary" href="/catalog" data-testid="cart-empty-cta">Explorar catálogo</a>
    </div>
  <?php else: ?>
    <div class="cart-wrap">
      <div class="card">
        <?php foreach ($cart as $i): ?>
          <div class="cart-item" data-testid="cart-row-<?= e($i['key']) ?>">
            <img src="<?= e($i['image']) ?>" alt="">
            <div>
              <h4 style="margin:0 0 .25rem"><?= e($i['name']) ?></h4>
              <small class="muted"><?= $i['type']==='part' ? 'Peça' : 'Produto' ?> · <?= money($i['price']) ?> / un.</small>
              <form id="cart-form-<?= e($i['key']) ?>" method="POST" action="/cart/update" style="margin-top:.5rem">
                <input type="hidden" name="key" value="<?= e($i['key']) ?>">
                <div class="qty">
                  <button type="button" onclick="qtyChange('<?= e($i['key']) ?>', -1)">−</button>
                  <input type="hidden" name="qty" value="<?= (int)$i['qty'] ?>">
                  <span><?= (int)$i['qty'] ?></span>
                  <button type="button" onclick="qtyChange('<?= e($i['key']) ?>', +1)">+</button>
                </div>
              </form>
            </div>
            <div style="text-align:right">
              <strong><?= money($i['price']*$i['qty']) ?></strong>
              <form method="POST" action="/cart/remove" style="margin-top:.5rem">
                <input type="hidden" name="key" value="<?= e($i['key']) ?>">
                <button class="btn btn-ghost btn-sm" data-testid="cart-remove-<?= e($i['key']) ?>"><i class="ph ph-trash"></i> Remover</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <aside class="cart-summary">
        <h3>Resumo</h3>
        <div class="summary-line"><span>Subtotal</span><span data-testid="cart-subtotal"><?= money(cart_subtotal()) ?></span></div>
        <div class="summary-line"><span>Envio</span><span>5,90 €</span></div>
        <div class="summary-line total"><span>Total</span><span data-testid="cart-total"><?= money(cart_subtotal() + 5.90) ?></span></div>
        <a href="/checkout" class="btn btn-primary btn-block btn-lg" style="margin-top:1rem" data-testid="cart-checkout"><i class="ph ph-credit-card"></i> Finalizar compra</a>
        <a href="/catalog" class="btn btn-ghost btn-block" style="margin-top:.5rem">Continuar a comprar</a>
      </aside>
    </div>
  <?php endif; ?>
</div>
