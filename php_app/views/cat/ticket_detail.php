<div class="dash"><?php $user = current_user(); require __DIR__ . '/../_sidebar.php'; ?>
<div class="dash-main">
  <a href="/cat/tickets" class="muted"><i class="ph ph-arrow-left"></i> Voltar</a>
  <div class="dash-head" style="margin-top:1rem">
    <div><h1>Pedido #<?= (int)$ticket['id'] ?></h1><p><?= e($ticket['title']) ?></p></div>
    <?php [$lbl,$cls]=status_label($ticket['status']); ?>
    <span class="badge <?= $cls ?>" style="padding:.5rem 1rem"><?= $lbl ?></span>
  </div>
  <div class="cart-wrap">
    <div class="card">
      <h3>Detalhes do cliente</h3>
      <p><strong>Cliente:</strong> <?= e($ticket['client']) ?><br>
         <strong>Email:</strong> <?= e($ticket['client_email']) ?><br>
         <strong>Tel:</strong> <?= e($ticket['client_phone'] ?: '-') ?></p>
      <hr>
      <h3>Equipamento</h3>
      <p><?= e($ticket['product_name'] ?? '-') ?> <?= $ticket['model'] ? '· '.e($ticket['model']) : '' ?></p>
      <hr>
      <h3>Descrição do cliente</h3>
      <p><?= nl2br(e($ticket['description'])) ?></p>
      <?php if ($ticket['photo_filename']): ?><p class="muted"><i class="ph ph-image"></i> Anexo: <?= e($ticket['photo_filename']) ?></p><?php endif; ?>
    </div>
    <aside class="card">
      <h3>Intervenção</h3>
      <form method="POST" action="/cat/ticket/update" class="form" data-testid="cat-ticket-form">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="ticket_id" value="<?= (int)$ticket['id'] ?>">
        <div class="form-group"><label>Estado</label>
          <select class="select" name="status" data-testid="cat-status">
            <?php foreach (['assigned'=>'Atribuído','in_progress'=>'Em curso','awaiting_parts'=>'Aguarda peças','closed'=>'Fechado','cancelled'=>'Cancelado'] as $k=>$v): ?>
              <option value="<?= $k ?>" <?= $ticket['status']===$k?'selected':'' ?>><?= $v ?></option>
            <?php endforeach; ?>
          </select></div>
        <div class="form-group"><label>Diagnóstico</label>
          <textarea class="textarea" name="diagnosis" data-testid="cat-diagnosis"><?= e($ticket['diagnosis']) ?></textarea></div>
        <div class="form-group"><label>Intervenção realizada</label>
          <textarea class="textarea" name="intervention" data-testid="cat-intervention"><?= e($ticket['intervention']) ?></textarea></div>
        <div class="form-group"><label>Peças aplicadas</label>
          <textarea class="textarea" name="parts_used" data-testid="cat-parts"><?= e($ticket['parts_used']) ?></textarea></div>
        <button class="btn btn-primary btn-block" data-testid="cat-update-btn"><i class="ph ph-floppy-disk"></i> Guardar e notificar cliente</button>
      </form>
    </aside>
  </div>
</div></div>
