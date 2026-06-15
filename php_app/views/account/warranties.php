<div class="dash"><?php $user = current_user(); require __DIR__ . '/../_sidebar.php'; ?>
<div class="dash-main">
  <div class="dash-head"><div><h1>Garantias</h1><p>Ative e consulte as garantias dos seus equipamentos Meireles.</p></div></div>
  <div class="cart-wrap">
    <div class="card">
      <h3>As suas garantias</h3>
      <?php if (empty($warranties)): ?>
        <p class="muted">Sem garantias ativadas ainda.</p>
      <?php else: ?>
        <table class="table" data-testid="warranties-table">
          <thead><tr><th>Produto</th><th>S/N</th><th>Compra</th><th>Expira</th><th>Estado</th></tr></thead>
          <tbody>
          <?php foreach ($warranties as $w): [$lbl,$cls]=status_label($w['status']); ?>
            <tr><td><?= e($w['product_name']) ?></td><td><?= e($w['serial_number']) ?></td>
              <td><?= fmt_date($w['purchase_date'],'d/m/Y') ?></td>
              <td><?= fmt_date($w['expiry_date'],'d/m/Y') ?></td>
              <td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td></tr>
          <?php endforeach; ?>
          </tbody></table>
      <?php endif; ?>
    </div>
    <aside class="card" style="align-self:start">
      <h3>Ativar nova garantia</h3>
      <form method="POST" action="/account/warranties/activate" enctype="multipart/form-data" class="form" data-testid="warranty-form">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div class="form-group"><label>Produto</label>
          <select name="product_id" class="select" required data-testid="warranty-product">
            <option value="">Selecione...</option>
            <?php foreach ($products as $p): ?>
              <option value="<?= (int)$p['id'] ?>"><?= e($p['name']) ?> (<?= e($p['model']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label>Número de série</label>
          <input class="input" name="serial" required placeholder="Ex: ME-2025-0001" data-testid="warranty-serial"></div>
        <div class="form-group"><label>Data de compra</label>
          <input class="input" type="date" name="purchase_date" required max="<?= date('Y-m-d') ?>" data-testid="warranty-date"></div>
        <div class="form-group"><label>Prova de compra (fatura)</label>
          <input class="input" type="file" name="proof" accept="image/*,application/pdf" data-testid="warranty-proof">
          <div class="hint">Opcional. Sem fatura, a garantia fica em estado "Aguarda documento".</div></div>
        <button class="btn btn-primary btn-block" data-testid="warranty-submit"><i class="ph ph-shield-check"></i> Ativar garantia</button>
      </form>
    </aside>
  </div>
</div></div>
