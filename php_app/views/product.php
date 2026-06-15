<div class="container">
  <a href="/catalog" class="muted" data-testid="back-to-catalog"><i class="ph ph-arrow-left"></i> Voltar ao catálogo</a>
  <div class="product-page" style="margin-top:1.5rem">
    <div class="product-img">
      <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>">
    </div>
    <div class="product-info">
      <div class="badge-row">
        <span class="badge info"><?= e($product['cat_name']) ?></span>
        <span class="badge ok">Em stock: <?= (int)$product['stock'] ?></span>
        <span class="badge warn">Garantia <?= (int)$product['warranty_months'] ?> meses</span>
      </div>
      <h1 data-testid="product-name"><?= e($product['name']) ?></h1>
      <p class="muted"><?= e($product['brand']) ?> · Modelo <?= e($product['model']) ?> · SKU <?= e($product['sku']) ?></p>
      <p><?= nl2br(e($product['description'])) ?></p>
      <div class="price" data-testid="product-price"><?= money((float)$product['price']) ?></div>
      <form method="POST" action="/cart/add">
        <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
        <input type="hidden" name="type" value="product">
        <input type="hidden" name="back" value="/product?id=<?= (int)$product['id'] ?>">
        <div class="flex gap-2" style="margin-top:1rem">
          <input class="input" name="qty" type="number" min="1" value="1" style="max-width:120px" data-testid="product-qty">
          <button class="btn btn-primary btn-lg" data-testid="product-add-cart"><i class="ph ph-shopping-cart"></i> Adicionar ao carrinho</button>
        </div>
      </form>

      <div class="product-tabs">
        <h3>Especificações</h3>
        <ul class="specs-list">
          <?php foreach (explode(';', $product['specs'] ?: '') as $spec): if (!trim($spec)) continue; ?>
            <li><i class="ph ph-check-circle"></i><?= e(trim($spec)) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>

  <?php if (!empty($parts)): ?>
  <section style="margin-top:4rem">
    <div class="section-head"><span class="eyebrow">Compatível</span><h2>Peças e consumíveis para este modelo</h2></div>
    <div class="grid grid-cols-4">
      <?php foreach ($parts as $p): ?>
        <article class="prod-card" data-testid="part-card-<?= (int)$p['id'] ?>">
          <div class="prod-thumb"><img src="<?= e($p['image_url'] ?: 'https://images.unsplash.com/photo-1581092334597-94d3c4232e6e?w=600') ?>" alt=""></div>
          <div class="prod-body">
            <span class="prod-cat">Peça</span>
            <h4><?= e($p['name']) ?></h4>
            <small class="muted"><?= e($p['compatible_models']) ?></small>
            <div class="price"><?= money((float)$p['price']) ?></div>
            <form method="POST" action="/cart/add">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <input type="hidden" name="type" value="part">
              <input type="hidden" name="back" value="/product?id=<?= (int)$product['id'] ?>">
              <button class="btn btn-primary btn-sm btn-block" data-testid="part-add-<?= (int)$p['id'] ?>"><i class="ph ph-shopping-cart"></i> Adicionar</button>
            </form>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>
</div>
