<div class="dash"><?php $user = current_user(); require __DIR__ . '/../_sidebar.php'; ?>
<div class="dash-main">
  <div class="dash-head"><div><h1>Configurações</h1><p>Parâmetros globais do portal.</p></div></div>
  <div class="card" style="max-width:600px">
    <form method="POST" action="/admin/settings/save" class="form" data-testid="settings-form">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <div class="form-group">
        <label>Alerta de fim de garantia (dias antes)</label>
        <input class="input" type="number" min="1" name="warranty_alert_days" value="<?= e($settings['warranty_alert_days'] ?? '30') ?>" data-testid="settings-warranty-days">
      </div>
      <div class="form-group">
        <label>Custo fixo de envio (€)</label>
        <input class="input" type="number" step="0.01" min="0" name="shipping_flat_cost" value="<?= e($settings['shipping_flat_cost'] ?? '5.90') ?>" data-testid="settings-shipping">
      </div>
      <button class="btn btn-primary" data-testid="settings-save-btn"><i class="ph ph-floppy-disk"></i> Guardar</button>
    </form>
  </div>
</div></div>
