<div class="dash"><?php $user = current_user(); require __DIR__ . '/../_sidebar.php'; ?>
<div class="dash-main">
  <div class="dash-head">
    <div><h1>Utilizadores</h1><p>Gestão de contas, perfis e permissões.</p></div>
    <button class="btn btn-primary" onclick="document.getElementById('user-modal').classList.add('open')" data-testid="user-new-btn"><i class="ph ph-plus"></i> Novo utilizador</button>
  </div>
  <div class="table-wrap"><table class="table" data-testid="users-table">
    <thead><tr><th>#</th><th>Nome</th><th>Email</th><th>Role</th><th>Loja/CAT</th><th>Criado</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr><td><?= (int)$r['id'] ?></td><td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td>
        <td><span class="badge info"><?= e($r['role']) ?></span></td>
        <td><?= e($r['store_name'] ?: $r['cat_name'] ?: '-') ?></td>
        <td><?= fmt_date($r['created_at'],'d/m/Y') ?></td>
        <td><button class="btn btn-outline btn-sm" onclick='editUser(<?= json_encode($r, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>)' data-testid="user-edit-<?= (int)$r['id'] ?>"><i class="ph ph-pencil"></i></button></td></tr>
    <?php endforeach; ?>
    </tbody></table></div>

  <div id="user-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:200;align-items:center;justify-content:center" class="modal">
    <div class="card" style="max-width:560px;width:90%;max-height:90vh;overflow:auto">
      <h3 id="modal-title">Novo utilizador</h3>
      <form method="POST" action="/admin/users/save" class="form" data-testid="user-form">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" id="u-id" value="">
        <div class="form-row">
          <div class="form-group"><label>Nome</label><input class="input" name="name" id="u-name" required></div>
          <div class="form-group"><label>Email</label><input class="input" name="email" id="u-email" type="email" required></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Role</label>
            <select class="select" name="role" id="u-role">
              <option value="consumer">Cliente</option><option value="store_manager">Loja</option>
              <option value="cat">CAT</option><option value="admin">Admin</option>
            </select></div>
          <div class="form-group"><label>Palavra-passe <span class="muted">(vazio = manter)</span></label>
            <input class="input" type="password" name="password" id="u-pw"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Loja</label>
            <select class="select" name="store_id" id="u-store"><option value="">—</option>
              <?php foreach ($stores as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?>
            </select></div>
          <div class="form-group"><label>CAT</label>
            <select class="select" name="cat_id" id="u-cat"><option value="">—</option>
              <?php foreach ($cats as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
            </select></div>
        </div>
        <div class="flex gap-2" style="margin-top:1rem">
          <button class="btn btn-primary" data-testid="user-save-btn">Guardar</button>
          <button type="button" class="btn btn-ghost" onclick="document.getElementById('user-modal').classList.remove('open')">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
  <style>.modal.open{display:flex !important;}</style>
  <script>
    function editUser(u){
      document.getElementById('modal-title').textContent='Editar #'+u.id;
      document.getElementById('u-id').value=u.id;
      document.getElementById('u-name').value=u.name;
      document.getElementById('u-email').value=u.email;
      document.getElementById('u-role').value=u.role;
      document.getElementById('u-store').value=u.store_id||'';
      document.getElementById('u-cat').value=u.cat_id||'';
      document.getElementById('u-pw').value='';
      document.getElementById('user-modal').classList.add('open');
    }
  </script>
</div></div>
