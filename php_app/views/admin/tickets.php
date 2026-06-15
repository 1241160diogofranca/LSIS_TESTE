<div class="dash"><?php $user = current_user(); require __DIR__ . '/../_sidebar.php'; ?>
<div class="dash-main">
  <div class="dash-head"><div><h1>Pedidos de Assistência</h1><p>Atribuição e supervisão.</p></div>
    <a href="/admin/export?type=tickets" class="btn btn-outline" data-testid="export-tickets-btn"><i class="ph ph-download"></i> Exportar CSV</a>
  </div>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>#</th><th>Cliente</th><th>Título</th><th>Estado</th><th>Prioridade</th><th>CAT atribuído</th><th>Aberto</th><th>Atribuir</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): [$lbl,$cls]=status_label($r['status']); ?>
      <tr><td>#<?= (int)$r['id'] ?></td><td><?= e($r['client']) ?></td><td><?= e($r['title']) ?></td>
        <td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td><td><?= e($r['priority']) ?></td>
        <td><?= e($r['cat_name'] ?? '-') ?></td><td><?= fmt_date($r['opened_at'],'d/m/Y') ?></td>
        <td>
          <form method="POST" action="/admin/tickets/assign" class="flex gap-2">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="ticket_id" value="<?= (int)$r['id'] ?>">
            <select class="select" name="cat_id" style="padding:.4rem .6rem;font-size:.85rem">
              <option value="">— CAT —</option>
              <?php foreach ($cats as $c): ?><option value="<?= (int)$c['id'] ?>" <?= ((int)$r['cat_id']===(int)$c['id'])?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
            </select>
            <button class="btn btn-primary btn-sm" data-testid="ticket-assign-<?= (int)$r['id'] ?>">OK</button>
          </form>
        </td></tr>
    <?php endforeach; ?>
    </tbody></table></div>
</div></div>
