<style>
  html, body {
    margin: 0;
    padding: 0;
    height: 100%;
    overflow: hidden; /* sem scroll */
  }

  .login-wrapper {
    min-height: 100vh;
    width: 100%;
    margin: 0;
    position: fixed; /* fixa a página */
    top: 0;
    left: 0;
  }
</style>

<div class="login-wrapper">

  <div class="login-hero">
  <img src="/Jiro-main/assets/logojiro.png" style="width:450px;">

    <p class="hero-tagline">Controle financeiro sem complicação.</p>
    <p class="hero-desc">
      Organize suas finanças de forma simples, rápida e inteligente.
    </p>

    <div class="hero-features">
      <div class="hero-feature">Controle de entradas e saídas</div>
      <div class="hero-feature">Relatórios inteligentes</div>
      <div class="hero-feature">Visual moderno e intuitivo</div>
    </div>
  </div>

  <div class="login-form-area">
    <h2>Entrar</h2>
    <p class="sub">Acesse sua conta para continuar</p>

    <form method="POST">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>

      <div class="form-group">
        <label>Senha</label>
        <input type="password" name="senha" required>
      </div>

      <button class="btn btn-primario">Entrar</button>
    </form>

    <div class="login-dica">
      <p>
        <strong>Admin:</strong> admin@jiro.com / admin123<br>
        <strong>Usuário:</strong> joao@email.com / joao123<br>
        <strong>Usuário:</strong> maria@email.com / maria123
      </p>
    </div>
  </div>

</div>