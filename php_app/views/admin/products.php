<div class="dash"><?php $user = current_user(); require __DIR__ . '/../_sidebar.php'; ?>
<div class="dash-main">
  <div class="dash-head">
    <div><h1>Catálogo de Produtos</h1><p>Adicione, edite ou ajuste o catálogo Meireles.</p></div>
    <button class="btn btn-primary" onclick="document.getElementById('p-modal').classList.add('open')" data-testid="product-new-btn"><i class="ph ph-plus"></i> Novo produto</button>
  </div>
  <div class="table-wrap"><table class="table" data-testid="admin-products-table">
    <thead><tr><th>SKU</th><th>Nome</th><th>Categoria</th><th>Modelo</th><th class="text-right">Preço</th><th>Stock</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr><td><?= e($r['sku']) ?></td><td><?= e($r['name']) ?></td><td><?= e($r['cat_name']) ?></td>
        <td><?= e($r['model']) ?></td><td class="text-right"><?= money((float)$r['price']) ?></td>
        <td><?= (int)$r['stock'] ?></td>
        <td><button class="btn btn-outline btn-sm" onclick='editProd(<?= json_encode($r, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>)' data-testid="product-edit-<?= (int)$r['id'] ?>"><i class="ph ph-pencil"></i></button></td></tr>
    <?php endforeach; ?>
    </tbody></table></div>

  <div id="p-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:200;align-items:center;justify-content:center" class="modal">
    <div class="card" style="max-width:700px;width:95%;max-height:90vh;overflow:auto">
      <h3 id="pmodal-title">Novo produto</h3>
      <form method="POST" action="/admin/products/save" class="form" data-testid="product-form">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" id="p-id" value="">
        <div class="form-row">
          <div class="form-group"><label>SKU</label><input class="input" name="sku" id="p-sku" required></div>
          <div class="form-group"><label>Nome</label><input class="input" name="name" id="p-name" required></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Marca</label><input class="input" name="brand" id="p-brand" value="Meireles"></div>
          <div class="form-group"><label>Modelo</label><input class="input" name="model" id="p-model" required></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Categoria</label>
            <select class="select" name="category_id" id="p-cat">
              <?php foreach ($cats as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
            </select></div>
          <div class="form-group"><label>Garantia (meses)</label><input class="input" type="number" name="warranty_months" id="p-warranty" value="24"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Preço (€)</label><input class="input" type="number" step="0.01" name="price" id="p-price" required></div>
          <div class="form-group"><label>Stock</label><input class="input" type="number" name="stock" id="p-stock" required></div>
        </div>
        <div class="form-group"><label>URL Imagem</label><input class="input" name="image_url" id="p-img"></div>
        <div class="form-group"><label>Descrição</label><textarea class="textarea" name="description" id="p-desc"></textarea></div>
        <div class="form-group"><label>Especificações <span class="muted">(separadas por ;)</span></label><textarea class="textarea" name="specs" id="p-specs"></textarea></div>
        <div class="flex gap-2" style="margin-top:1rem">
          <button class="btn btn-primary" data-testid="product-save-btn">Guardar</button>
          <button type="button" class="btn btn-ghost" onclick="document.getElementById('p-modal').classList.remove('open')">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
  <script>
    function editProd(p){
      document.getElementById('pmodal-title').textContent='Editar '+p.sku;
      ['id','sku','name','brand','model','price','stock','description','specs','warranty_months'].forEach(k=>{document.getElementById('p-'+k.substring(0,5).replace('descr','desc').replace('warra','warranty').replace('image','img').replace('categ','cat'))});
      document.getElementById('p-id').value=p.id;
      document.getElementById('p-sku').value=p.sku;
      document.getElementById('p-name').value=p.name;
      document.getElementById('p-brand').value=p.brand;
      document.getElementById('p-model').value=p.model;
      document.getElementById('p-cat').value=p.category_id;
      document.getElementById('p-price').value=p.price;
      document.getElementById('p-stock').value=p.stock;
      document.getElementById('p-img').value=p.image_url||'';
      document.getElementById('p-desc').value=p.description||'';
      document.getElementById('p-specs').value=p.specs||'';
      document.getElementById('p-warranty').value=p.warranty_months;
      document.getElementById('p-modal').classList.add('open');
    }
  </script>
</div></div>
