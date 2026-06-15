<div class="container auth-wrap">
  <div class="card">
    <h1>Criar conta</h1>
    <p class="muted">Junte-se ao portal Meireles Connect.</p>
    <form method="POST" action="/register" class="form" data-testid="register-form">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <div class="form-group"><label>Nome completo</label>
        <input class="input" type="text" name="name" required data-testid="register-name"></div>
      <div class="form-group"><label>Email</label>
        <input class="input" type="email" name="email" required data-testid="register-email"></div>
      <div class="form-group"><label>Telefone <span class="muted">(opcional)</span></label>
        <input class="input" type="tel" name="phone" data-testid="register-phone"></div>
      <div class="form-group"><label>Palavra-passe</label>
        <input class="input" type="password" name="password" required minlength="6" data-testid="register-password">
        <div class="hint">Mínimo 6 caracteres.</div>
      </div>
      <button class="btn btn-primary btn-block" data-testid="register-submit">Criar conta</button>
    </form>
    <p class="text-center" style="margin-top:1.5rem">Já tem conta? <a href="/login">Entrar</a></p>
  </div>
</div>
