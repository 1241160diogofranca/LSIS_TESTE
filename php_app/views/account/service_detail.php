<div class="dash"><?php $user = current_user(); require __DIR__ . '/../_sidebar.php'; ?>
<div class="dash-main">
  <a href="/account/service" class="muted"><i class="ph ph-arrow-left"></i> Voltar</a>
  <div class="dash-head" style="margin-top:1rem">
    <div><h1>Pedido #<?= (int)$ticket['id'] ?></h1><p><?= e($ticket['title']) ?></p></div>
    <?php [$lbl,$cls]=status_label($ticket['status']); ?>
    <span class="badge <?= $cls ?>" style="font-size:.9rem;padding:.5rem 1rem"><?= $lbl ?></span>
  </div>
  <div class="card">
    <p><strong>Produto:</strong> <?= e($ticket['product_name'] ?? '-') ?></p>
    <p><strong>CAT atribuído:</strong> <?= e($ticket['cat_name'] ?? 'A atribuir') ?></p>
    <p><strong>Aberto em:</strong> <?= fmt_date($ticket['opened_at']) ?> · <strong>Prioridade:</strong> <?= e($ticket['priority']) ?></p>
    <hr>
    <h3>Descrição</h3><p><?= nl2br(e($ticket['description'])) ?></p>
    <?php if ($ticket['photo_filename']): ?>
      <p class="muted"><i class="ph ph-image"></i> Foto anexada: <?= e($ticket['photo_filename']) ?></p>
    <?php endif; ?>
    <?php if ($ticket['diagnosis']): ?><hr><h3>Diagnóstico</h3><p><?= nl2br(e($ticket['diagnosis'])) ?></p><?php endif; ?>
    <?php if ($ticket['intervention']): ?><h3>Intervenção</h3><p><?= nl2br(e($ticket['intervention'])) ?></p><?php endif; ?>
    <?php if ($ticket['parts_used']): ?><h3>Peças aplicadas</h3><p><?= nl2br(e($ticket['parts_used'])) ?></p><?php endif; ?>
    <?php if ($ticket['closed_at']): ?><div class="badge ok" style="padding:.5rem 1rem"><i class="ph ph-check-circle"></i> Fechado em <?= fmt_date($ticket['closed_at']) ?></div><?php endif; ?>
  </div>
</div></div>
