<div class="container">
  <div class="section-head">
    <span class="eyebrow">Peças e Consumíveis</span>
    <h1>Peças originais por modelo</h1>
    <p>Encontre peças de substituição compatíveis com o seu equipamento Meireles.</p>
  </div>
  <form class="filter-bar" method="get" action="/parts">
    <input type="text" class="input" name="q" placeholder="Pesquisar por nome, modelo ou SKU" value="<?= e($_GET['q'] ?? '') ?>" style="flex:1" data-testid="parts-search">
    <button class="btn btn-primary" data-testid="parts-search-btn"><i class="ph ph-magnifying-glass"></i> Pesquisar</button>
  </form>
  <?php if (empty($parts)): ?>
    <div class="empty-state card"><h3>Sem peças.</h3></div>
  <?php else: ?>
  <div class="grid grid-cols-4">
    <?php foreach ($parts as $p): ?>
      <article class="prod-card">
        <div class="prod-thumb"><img src="<?= e($p['image_url']) ?>" alt=""></div>
        <div class="prod-body">
          <span class="prod-cat">SKU <?= e($p['sku']) ?></span>
          <h4><?= e($p['name']) ?></h4>
          <small class="muted">Compat.: <?= e($p['compatible_models']) ?></small>
          <small class="muted">Stock: <?= (int)$p['stock'] ?></small>
          <div class="price"><?= money((float)$p['price']) ?></div>
          <form method="POST" action="/cart/add">
            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
            <input type="hidden" name="type" value="part">
            <input type="hidden" name="back" value="/parts">
            <button class="btn btn-primary btn-sm btn-block" data-testid="part-add-<?= (int)$p['id'] ?>"><i class="ph ph-shopping-cart"></i> Adicionar</button>
          </form>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
