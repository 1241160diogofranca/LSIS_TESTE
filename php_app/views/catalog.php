<div class="container">
  <div class="section-head">
    <span class="eyebrow">Catálogo</span>
    <h1>Equipamentos Meireles</h1>
    <p>Filtre por categoria, marca ou preço e encontre o equipamento perfeito para si.</p>
  </div>

  <form class="filter-bar" method="get" action="/catalog" data-testid="filter-form">
    <input type="text" class="input" name="q" placeholder="Pesquisar por nome, modelo ou SKU" value="<?= e($q) ?>" data-testid="filter-q" style="flex:1;min-width:200px">
    <select name="brand" class="select" data-testid="filter-brand" style="max-width:200px">
      <option value="">Todas as marcas</option>
      <?php foreach ($brands as $b): ?><option <?= $brand===$b?'selected':'' ?>><?= e($b) ?></option><?php endforeach; ?>
    </select>
    <div class="flex gap-2" style="min-width:240px;flex:1;">
      <label class="muted" style="white-space:nowrap;font-size:.85rem">Até <span id="price-label"><?= (int)$maxp ?> €</span></label>
      <input type="range" name="max" min="100" max="2000" step="50" value="<?= (int)$maxp ?>" oninput="updatePriceLabel(this.value)" data-testid="filter-price">
    </div>
    <button class="btn btn-primary" type="submit" data-testid="filter-apply"><i class="ph ph-funnel"></i> Filtrar</button>
    <a class="btn btn-ghost btn-sm" href="/catalog" data-testid="filter-clear">Limpar</a>
  </form>

  <div class="chips" style="margin-bottom:1.5rem">
    <a class="chip <?= $cat_slug==='' ? 'active' : '' ?>" href="/catalog" data-testid="chip-all">Todos</a>
    <?php foreach ($categories as $c): ?>
      <a class="chip <?= $cat_slug===$c['slug']?'active':'' ?>" href="/catalog?cat=<?= e($c['slug']) ?>" data-testid="chip-<?= e($c['slug']) ?>"><?= e($c['name']) ?></a>
    <?php endforeach; ?>
  </div>

  <?php if (empty($products)): ?>
    <div class="empty-state card"><h3>Sem resultados</h3><p>Tente outros filtros.</p></div>
  <?php else: ?>
  <div class="grid grid-cols-4" data-testid="catalog-grid">
    <?php foreach ($products as $p): ?>
      <article class="prod-card" data-testid="prod-card-<?= (int)$p['id'] ?>">
        <a class="prod-thumb" href="/product?id=<?= (int)$p['id'] ?>">
          <img src="<?= e($p['image_url']) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
        </a>
        <div class="prod-body">
          <span class="prod-cat"><?= e($p['cat_name']) ?> · <?= e($p['brand']) ?></span>
          <h4><a href="/product?id=<?= (int)$p['id'] ?>" style="color:inherit"><?= e($p['name']) ?></a></h4>
          <small class="muted"><?= e($p['model']) ?> · <?= (int)$p['stock'] ?> em stock</small>
          <div class="price"><?= money((float)$p['price']) ?></div>
          <div class="actions">
            <a class="btn btn-outline btn-sm" href="/product?id=<?= (int)$p['id'] ?>">Ver</a>
            <form method="POST" action="/cart/add" style="flex:1">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <input type="hidden" name="type" value="product">
              <input type="hidden" name="back" value="/catalog?cat=<?= e($cat_slug) ?>">
              <button class="btn btn-primary btn-sm btn-block" data-testid="prod-add-cart-<?= (int)$p['id'] ?>"><i class="ph ph-shopping-cart"></i> Adicionar</button>
            </form>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
