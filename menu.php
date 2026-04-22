<?php
// paginas/menu.php
$paginaAtual = isset($_GET['pagina']) ? limpar($_GET['pagina']) : 'dashboard';

function menuLink($label, $pagina, $icone, $paginaAtual) {
    $ativo = ($paginaAtual === $pagina) ? ' ativo' : '';
    echo "<a href=\"index.php?pagina={$pagina}\" class=\"{$ativo}\">";
    echo "<span class=\"icone\">{$icone}</span> {$label}";
    echo "</a>";
}

$inicial = strtoupper(substr($_SESSION['usuario_nome'], 0, 1));
?>

<nav class="menu">

    <!-- Logo -->
    <div class="menu-logo">
        <span class="logo-mark">Jiro</span>
        <span class="logo-sub">Gestão Financeira</span>
    </div>

    <!-- Navegação -->
    <div class="menu-nav">

        <div class="menu-section">Principal</div>
        <?php menuLink('Dashboard',       'dashboard',           '◈', $paginaAtual); ?>
        <?php menuLink('Movimentações',   'movimentacoes',       '⇅', $paginaAtual); ?>
        <?php menuLink('Nova Entrada/Saída','nova_movimentacao', '+', $paginaAtual); ?>

        <div class="menu-section">Organização</div>
        <?php menuLink('Categorias',      'categorias',          '⊞', $paginaAtual); ?>
        <?php menuLink('Relatório',       'relatorio',           '▦', $paginaAtual); ?>

        <?php if (ehAdmin()): ?>
        <div class="menu-section">Administrador</div>
        <?php menuLink('Painel Admin',        'admin',              '⚙', $paginaAtual); ?>
        <?php menuLink('Usuários',            'admin_usuarios',     '👥', $paginaAtual); ?>
        <?php menuLink('Estatísticas',        'admin_estatisticas', '📊', $paginaAtual); ?>
        <?php menuLink('Uso do Sistema',      'admin_uso',          '📈', $paginaAtual); ?>
        <?php endif; ?>

    </div>

    <!-- Rodapé do menu -->
    <div class="menu-footer">
        <div class="menu-user">
            <div class="avatar"><?= $inicial ?></div>
            <div class="user-info">
                <span><?= limpar($_SESSION['usuario_nome']) ?></span>
                <small><?= limpar($_SESSION['usuario_perfil']) ?></small>
            </div>
        </div>
        <a href="index.php?acao=logout" class="btn-logout">
            ⏻ Sair
        </a>
    </div>

</nav>
