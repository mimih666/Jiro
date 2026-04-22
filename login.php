<?php
// paginas/login.php
// Variável $erroLogin vem do index.php (POST handler)
?>
<div class="login-wrapper">

    <!-- ——— Hero lateral ——— -->
    <div class="login-hero">
        <div class="hero-logo">Jiro</div>
        <p class="hero-tagline">Controle financeiro sem complicação.</p>
        <p class="hero-desc">
            Organize suas receitas, despesas e categorias em um só lugar.
            Visualize relatórios e tome decisões mais inteligentes.
        </p>
        <div class="hero-features">
            <div class="hero-feature">Registro de receitas e despesas</div>
            <div class="hero-feature">Categorias personalizadas</div>
            <div class="hero-feature">Relatórios e gráficos mensais</div>
            <div class="hero-feature">Painel administrativo completo</div>
        </div>
    </div>

    <!-- ——— Formulário de login ——— -->
    <div class="login-form-area">
        <h2>Bem-vindo de volta</h2>
        <p class="sub">Faça login para acessar sua conta.</p>

        <?php if (!empty($erroLogin)): ?>
            <div class="alerta alerta-erro"><?= $erroLogin ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?pagina=login">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="seu@email.com"
                    value="<?= isset($_POST['email']) ? limpar($_POST['email']) : '' ?>"
                    required
                    autocomplete="email"
                >
            </div>

            <div class="form-group">
                <label for="senha">Senha</label>
                <input
                    type="password"
                    id="senha"
                    name="senha"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                >
            </div>

            <button type="submit" class="btn btn-primario" style="width:100%; justify-content:center; margin-top:8px;">
                Entrar no Jiro
            </button>
        </form>

        <!-- Dica de acesso para fins acadêmicos -->
        <div class="login-dica">
            <p>
                <strong>Admin:</strong> admin@jiro.com &nbsp;/&nbsp; admin123<br>
                <strong>Usuário:</strong> joao@email.com &nbsp;/&nbsp; joao123<br>
                <strong>Usuário:</strong> maria@email.com &nbsp;/&nbsp; maria123
            </p>
        </div>
    </div>

</div>
