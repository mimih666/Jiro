<?php
$stats    = getEstatisticasSistema();
$usuarios = getUsuarios();
$movs     = getMovimentacoes();
?>

<div class="page-header">
    <h1>Estatísticas do Sistema</h1>
    <p>Indicadores gerais e anônimos sobre o uso do Jiro.</p>
</div>

<div class="cards-grid">
    <div class="card">
        <div class="card-icon">👤</div>
        <div class="card-label">Usuários Totais</div>
        <div class="card-valor ciano"><?= $stats['total_usuarios'] ?></div>
    </div>
    <div class="card">
        <div class="card-icon">✅</div>
        <div class="card-label">Usuários Ativos</div>
        <div class="card-valor verde"><?= $stats['usuarios_ativos'] ?></div>
        <div class="card-detalhe">Com ao menos 1 movimentação</div>
    </div>
    <div class="card">
        <div class="card-icon">📋</div>
        <div class="card-label">Total de Movimentações</div>
        <div class="card-valor"><?= $stats['total_movimentacoes'] ?></div>
    </div>
    <div class="card">
        <div class="card-icon">∑</div>
        <div class="card-label">Média por Usuário</div>
        <div class="card-valor"><?= $stats['media_mov_usuario'] ?></div>
        <div class="card-detalhe">movimentações / usuário</div>
    </div>
</div>

<div class="tabela-container mt-24">
    <div class="tabela-header">
        <h2>Insights do Sistema</h2>
        <span style="font-size:.78rem; color:var(--text3);">Dados anônimos e agregados</span>
    </div>
    <div style="padding:24px; display:grid; gap:16px;">

        <?php
        $percAtivos = $stats['total_usuarios'] > 0
            ? round(($stats['usuarios_ativos'] / $stats['total_usuarios']) * 100)
            : 0;

        $insights = [
            [
                'icone' => '📊',
                'texto' => "{$percAtivos}% dos usuários registram movimentações ativamente.",
            ],
            [
                'icone' => '🏷️',
                'texto' => "A categoria mais utilizada no sistema é <strong>{$stats['categoria_mais_usada']}</strong>.",
            ],
            [
                'icone' => '⇅',
                'texto' => "Média de <strong>{$stats['media_mov_usuario']}</strong> lançamento(s) por usuário no sistema.",
            ],
            [
                'icone' => '📂',
                'texto' => count(getCategorias()) . " categorias disponíveis para classificação.",
            ],
        ];

        foreach ($insights as $ins):
        ?>
        <div class="card" style="flex-direction:row; align-items:center; gap:16px; padding:16px 20px;">
            <div style="font-size:1.5rem; flex-shrink:0;"><?= $ins['icone'] ?></div>
            <p style="font-size:.9rem; color:var(--text2);"><?= $ins['texto'] ?></p>
        </div>
        <?php endforeach; ?>

    </div>
</div>

<div class="tabela-container mt-24">
    <div class="tabela-header">
        <h2>Frequência de Uso por Categoria</h2>
    </div>
    <?php if (!empty($stats['contagem_categorias'])): ?>
    <table>
        <thead>
            <tr>
                <th>Posição</th>
                <th>Categoria</th>
                <th>Nº de Usos</th>
                <th>% do Total de Movs.</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $pos = 1;
            foreach ($stats['contagem_categorias'] as $cat => $usos):
                $pct = $stats['total_movimentacoes'] > 0
                    ? round(($usos / $stats['total_movimentacoes']) * 100, 1)
                    : 0;
            ?>
            <tr>
                <td style="color:var(--text3);"><?= $pos++ ?>º</td>
                <td><strong><?= limpar($cat) ?></strong></td>
                <td><?= $usos ?></td>
                <td><?= $pct ?>%</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="padding:24px; color:var(--text3);">Nenhuma movimentação registrada ainda.</p>
    <?php endif; ?>
</div>
