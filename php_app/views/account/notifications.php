<div class="dash"><?php $user = current_user(); require __DIR__ . '/../_sidebar.php'; ?>
<div class="dash-main">
  <div class="dash-head"><div><h1>Notificações</h1><p>O seu histórico de alertas e mensagens automáticas.</p></div></div>
  <div class="card">
    <?php if (empty($notifs)): ?>
      <p class="muted">Sem notificações.</p>
    <?php else: ?>
      <?php foreach ($notifs as $n): ?>
        <div style="padding:1rem 0;border-bottom:1px solid var(--border-color);display:flex;gap:1rem" data-testid="notif-<?= (int)$n['id'] ?>">
          <span class="cat-icon" style="background:var(--primary-soft);color:var(--primary);width:42px;height:42px;font-size:1.1rem;flex-shrink:0"><i class="ph ph-bell"></i></span>
          <div style="flex:1">
            <strong><?= e($n['title']) ?></strong>
            <p class="muted" style="margin:.25rem 0"><?= nl2br(e($n['body'])) ?></p>
            <small class="muted"><?= fmt_date($n['created_at']) ?></small>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div></div>
