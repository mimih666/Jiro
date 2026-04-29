<?php
$stats = getEstatisticasSistema();
?>

<div class="page-header">
    <h1>Painel do Administrador</h1>
    <p>Visão geral anônima do sistema Jiro.</p>
</div>

<div class="cards-grid">
    <div class="card">
        <div class="card-icon">👥</div>
        <div class="card-label">Usuários Cadastrados</div>
        <div class="card-valor ciano"><?= $stats['total_usuarios'] ?></div>
        <div class="card-detalhe"><?= $stats['usuarios_ativos'] ?> ativos (com movimentações)</div>
    </div>
    <div class="card">
        <div class="card-icon">⇅</div>
        <div class="card-label">Total de Movimentações</div>
        <div class="card-valor"><?= $stats['total_movimentacoes'] ?></div>
        <div class="card-detalhe">no sistema (todos os usuários)</div>
    </div>
    <div class="card">
        <div class="card-icon">📊</div>
        <div class="card-label">Média por Usuário</div>
        <div class="card-valor"><?= $stats['media_mov_usuario'] ?></div>
        <div class="card-detalhe">movimentações / usuário</div>
    </div>
    <div class="card">
        <div class="card-icon">🏷️</div>
        <div class="card-label">Categoria Mais Usada</div>
        <div class="card-valor" style="font-size:1.1rem;"><?= $stats['categoria_mais_usada'] ?></div>
        <div class="card-detalhe">no sistema</div>
    </div>
</div>

<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-top:8px;">

    <a href="index.php?pagina=admin_usuarios" style="text-decoration:none;">
        <div class="card" style="cursor:pointer; text-align:center; padding:28px 20px;">
            <div style="font-size:2rem; margin-bottom:10px;">👥</div>
            <div style="font-family:var(--font-display); font-weight:600; color:var(--text); margin-bottom:4px;">Usuários</div>
            <div style="font-size:.8rem; color:var(--text3);">Listar e gerenciar</div>
        </div>
    </a>

    <a href="index.php?pagina=admin_estatisticas" style="text-decoration:none;">
        <div class="card" style="cursor:pointer; text-align:center; padding:28px 20px;">
            <div style="font-size:2rem; margin-bottom:10px;">📊</div>
            <div style="font-family:var(--font-display); font-weight:600; color:var(--text); margin-bottom:4px;">Estatísticas</div>
            <div style="font-size:.8rem; color:var(--text3);">Dados gerais do sistema</div>
        </div>
    </a>

    <a href="index.php?pagina=admin_uso" style="text-decoration:none;">
        <div class="card" style="cursor:pointer; text-align:center; padding:28px 20px;">
            <div style="font-size:2rem; margin-bottom:10px;">📈</div>
            <div style="font-family:var(--font-display); font-weight:600; color:var(--text); margin-bottom:4px;">Uso do Sistema</div>
            <div style="font-size:.8rem; color:var(--text3);">Analytics e engajamento</div>
        </div>
    </a>

</div>

<?php if (!empty($stats['contagem_categorias'])): ?>
<div class="tabela-container mt-32">
    <div class="tabela-header">
        <h2>Categorias Mais Utilizadas no Sistema</h2>
        <span style="font-size:.78rem; color:var(--text3);">Dados anônimos</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Categoria</th>
                <th>Usos</th>
                <th>Distribuição</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $maxUso = max($stats['contagem_categorias']);
            foreach ($stats['contagem_categorias'] as $cat => $usos):
                $pct = $maxUso > 0 ? round(($usos / $maxUso) * 100) : 0;
            ?>
            <tr>
                <td><strong><?= limpar($cat) ?></strong></td>
                <td><?= $usos ?></td>
                <td style="width:220px;">
                    <div style="height:8px; background:var(--bg3); border-radius:99px; overflow:hidden;">
                        <div style="height:100%; width:<?= $pct ?>%; background:var(--accent2); border-radius:99px;"></div>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
