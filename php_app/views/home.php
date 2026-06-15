<section class="hero">
  <div class="container hero-inner">
    <div>
      <span style="display:inline-block;background:rgba(255,255,255,.12);color:#6ec1ff;padding:.35rem .85rem;border-radius:999px;font-size:.8rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase;margin-bottom:1.5rem;" data-testid="hero-eyebrow">Portal Pós-Venda Oficial</span>
      <h1>O <em>Meireles</em> que vive consigo, agora num só lugar.</h1>
      <p>Catálogo completo, ativação de garantias, peças originais e assistência técnica num portal moderno, transparente e sempre disponível.</p>
      <div class="hero-cta">
        <a href="/catalog" class="btn btn-primary btn-lg" data-testid="hero-cta-catalog"><i class="ph ph-storefront"></i> Explorar catálogo</a>
        <a href="/register" class="btn btn-lg" style="background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.4);backdrop-filter:blur(8px)" data-testid="hero-cta-register"><i class="ph ph-user-plus"></i> Criar conta gratuita</a>
      </div>
      <div class="hero-stats">
        <div class="stat"><strong>50+</strong><span>Modelos disponíveis</span></div>
        <div class="stat"><strong>24-36m</strong><span>Garantia oficial</span></div>
        <div class="stat"><strong>3</strong><span>CAT Nacionais</span></div>
      </div>
    </div>
    <div class="hero-card">
      <h3>Tudo num só portal</h3>
      <ul>
        <li><i class="ph ph-shopping-bag"></i> Compre online com entrega ao domicílio</li>
        <li><i class="ph ph-shield-check"></i> Active a garantia em segundos</li>
        <li><i class="ph ph-wrench"></i> Abra pedidos de assistência técnica</li>
        <li><i class="ph ph-package"></i> Encomende peças originais por modelo</li>
        <li><i class="ph ph-bell"></i> Alertas automáticos de cada estado</li>
      </ul>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Categorias</span>
      <h2>Toda a gama Meireles à distância de um clique</h2>
      <p>Desde fogões clássicos a placas de indução de última geração, encontre o equipamento certo para a sua cozinha.</p>
    </div>
    <div class="grid grid-cols-auto">
      <?php foreach ($categories as $c): ?>
        <a class="cat-card" href="/catalog?cat=<?= e($c['slug']) ?>" data-testid="cat-card-<?= e($c['slug']) ?>">
          <span class="cat-icon"><i class="ph ph-<?= e($c['icon'] ?: 'cube') ?>"></i></span>
          <div>
            <strong><?= e($c['name']) ?></strong>
            <small><?= (int)$c['n'] ?> modelos</small>
          </div>
          <i class="ph ph-caret-right" style="margin-left:auto;color:var(--text-muted)"></i>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" style="background:var(--bg-subtle)">
  <div class="container">
    <div class="section-head flex-between" style="display:flex;justify-content:space-between;align-items:end;gap:1rem;flex-wrap:wrap">
      <div>
        <span class="eyebrow">Destaques</span>
        <h2>Os mais procurados</h2>
      </div>
      <a href="/catalog" class="btn btn-outline" data-testid="see-all-products">Ver todo o catálogo <i class="ph ph-arrow-right"></i></a>
    </div>
    <div class="grid grid-cols-4">
      <?php foreach ($featured as $p): ?>
        <article class="prod-card" data-testid="prod-card-<?= (int)$p['id'] ?>">
          <a class="prod-thumb" href="/product?id=<?= (int)$p['id'] ?>">
            <img src="<?= e($p['image_url']) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
          </a>
          <div class="prod-body">
            <span class="prod-cat"><?= e($p['cat_name']) ?></span>
            <h4><a href="/product?id=<?= (int)$p['id'] ?>" style="color:inherit"><?= e($p['name']) ?></a></h4>
            <small class="muted"><?= e($p['model']) ?> · <?= (int)$p['warranty_months'] ?> meses garantia</small>
            <div class="price"><?= money((float)$p['price']) ?></div>
            <div class="actions">
              <a class="btn btn-outline btn-sm" href="/product?id=<?= (int)$p['id'] ?>" data-testid="prod-view-<?= (int)$p['id'] ?>">Ver</a>
              <form method="POST" action="/cart/add" style="flex:1">
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                <input type="hidden" name="type" value="product">
                <input type="hidden" name="back" value="/">
                <button class="btn btn-primary btn-sm btn-block" data-testid="prod-add-cart-<?= (int)$p['id'] ?>"><i class="ph ph-shopping-cart"></i> Adicionar</button>
              </form>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 2rem;">
      <div class="card" style="display:flex; gap:1.5rem; padding:2rem;">
        <div style="flex-shrink:0">
          <span class="cat-icon" style="width:64px;height:64px;font-size:1.8rem"><i class="ph ph-shield-check"></i></span>
        </div>
        <div>
          <h3>Ativação de Garantia em 60 segundos</h3>
          <p class="muted">Registe o número de série do seu equipamento, indique a data de compra e anexe a fatura. A sua garantia fica imediatamente ativa.</p>
          <a href="/account/warranties" class="btn btn-outline" data-testid="home-cta-warranty">Ativar agora</a>
        </div>
      </div>
      <div class="card" style="display:flex; gap:1.5rem; padding:2rem;">
        <div style="flex-shrink:0">
          <span class="cat-icon" style="width:64px;height:64px;font-size:1.8rem;background:#FEE2E2;color:#B91C1C"><i class="ph ph-wrench"></i></span>
        </div>
        <div>
          <h3>Assistência Técnica Rápida</h3>
          <p class="muted">Abra um pedido, anexe fotografias do problema e acompanhe a evolução em tempo real, com notificações em cada etapa.</p>
          <a href="/account/service" class="btn btn-outline" data-testid="home-cta-service">Pedir assistência</a>
        </div>
      </div>
    </div>
  </div>
</section>
