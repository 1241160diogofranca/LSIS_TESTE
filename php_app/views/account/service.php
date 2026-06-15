<div class="dash"><?php $user = current_user(); require __DIR__ . '/../_sidebar.php'; ?>
<div class="dash-main">
  <div class="dash-head"><div><h1>Assistência Técnica</h1><p>Abra novos pedidos e acompanhe o estado dos seus processos.</p></div></div>
  <div class="cart-wrap">
    <div class="card">
      <h3>Os seus pedidos</h3>
      <?php if (empty($tickets)): ?>
        <p class="muted">Sem pedidos ainda.</p>
      <?php else: ?>
        <table class="table" data-testid="tickets-table">
          <thead><tr><th>#</th><th>Título</th><th>Produto</th><th>Estado</th><th>CAT</th><th>Aberto</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($tickets as $t): [$lbl,$cls]=status_label($t['status']); ?>
            <tr><td>#<?= (int)$t['id'] ?></td>
              <td><?= e($t['title']) ?></td>
              <td><?= e($t['product_name'] ?? '-') ?></td>
              <td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td>
              <td><?= e($t['cat_name'] ?? '-') ?></td>
              <td><?= fmt_date($t['opened_at'],'d/m/Y') ?></td>
              <td><a class="btn btn-outline btn-sm" href="/account/service/view?id=<?= (int)$t['id'] ?>" data-testid="ticket-view-<?= (int)$t['id'] ?>">Ver</a></td></tr>
          <?php endforeach; ?>
          </tbody></table>
      <?php endif; ?>
    </div>
    <aside class="card" style="align-self:start">
      <h3>Novo pedido</h3>
      <form method="POST" action="/account/service/open" enctype="multipart/form-data" class="form" data-testid="ticket-form">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div class="form-group"><label>Título</label><input class="input" name="title" required data-testid="ticket-title"></div>
        <div class="form-group"><label>Produto associado</label>
          <select class="select" name="product_id" data-testid="ticket-product">
            <option value="">— Nenhum —</option>
            <?php foreach ($products as $p): ?><option value="<?= (int)$p['id'] ?>"><?= e($p['name']) ?></option><?php endforeach; ?>
          </select></div>
        <div class="form-group"><label>Prioridade</label>
          <select class="select" name="priority" data-testid="ticket-priority"><option value="low">Baixa</option><option value="normal" selected>Normal</option><option value="high">Alta</option></select></div>
        <div class="form-group"><label>Descrição do problema</label>
          <textarea class="textarea" name="description" required placeholder="Descreva o problema com o máximo de detalhe..." data-testid="ticket-description"></textarea></div>
        <div class="form-group"><label>Foto do defeito (opcional)</label>
          <input class="input" type="file" name="photo" accept="image/*" data-testid="ticket-photo"></div>
        <button class="btn btn-primary btn-block" data-testid="ticket-submit"><i class="ph ph-wrench"></i> Abrir pedido</button>
      </form>
    </aside>
  </div>
</div></div>
