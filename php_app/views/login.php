<div class="container auth-wrap">
  <div class="card">
    <h1>Entrar</h1>
    <p class="muted">Aceda à sua conta Meireles Connect.</p>
    <form method="POST" action="/login" class="form" data-testid="login-form">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <div class="form-group">
        <label>Email</label>
        <input class="input" type="email" name="email" required autocomplete="email" data-testid="login-email">
      </div>
      <div class="form-group">
        <label>Palavra-passe</label>
        <input class="input" type="password" name="password" required autocomplete="current-password" data-testid="login-password">
      </div>
      <button type="submit" class="btn btn-primary btn-block" data-testid="login-submit">Entrar</button>
    </form>
    <p class="text-center" style="margin-top:1.5rem">Ainda não tem conta? <a href="/register" data-testid="register-link">Criar conta</a></p>
    <div class="auth-hint">
      <strong>Contas de demonstração:</strong><br>
      Admin: <code>admin@meireles.pt</code> / <code>admin123</code><br>
      Loja: <code>loja@meireles.pt</code> / <code>loja123</code><br>
      CAT: <code>cat@meireles.pt</code> / <code>cat123</code><br>
      Cliente: <code>cliente@meireles.pt</code> / <code>cliente123</code>
    </div>
  </div>
</div>
