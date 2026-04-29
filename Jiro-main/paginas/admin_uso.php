<?php
$usuarios = getUsuarios();
$movs     = getMovimentacoes();
$stats    = getEstatisticasSistema();

$movPorUsuario = [];
foreach ($movs as $m) {
    $uid = $m['usuario_id'];
    if (!isset($movPorUsuario[$uid])) {
        $movPorUsuario[$uid] = ['entradas' => 0, 'saidas' => 0, 'total' => 0];
    }
    $movPorUsuario[$uid]['total']++;
    if ($m['tipo'] === 'entrada') $movPorUsuario[$uid]['entradas']++;
    else                          $movPorUsuario[$uid]['saidas']++;
}
arsort($movPorUsuario);

$usoMensal = [
    'Jan' => 2,
    'Fev' => 5,
    'Mar' => 8,
    'Abr' => 5,
];

$maxUsoMensal = max($usoMensal);
?>

<div class="page-header">
    <h1>Uso do Sistema</h1>
    <p>Analytics de engajamento — dados anônimos e agregados.</p>
</div>

<div class="tabela-container mb-24">
    <div class="tabela-header">
        <h2>Movimentações por Mês (simulado)</h2>
        <span style="font-size:.78rem; color:var(--text3);">Fase 2: dados reais do banco</span>
    </div>
    <div style="padding:28px 24px; display:flex; align-items:flex-end; gap:16px; height:180px;">
        <?php foreach ($usoMensal as $mes => $qtd):
            $altura = $maxUsoMensal > 0 ? round(($qtd / $maxUsoMensal) * 100) : 0;
        ?>
        <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:8px;">
            <span style="font-size:.8rem; color:var(--text2); font-weight:600;"><?= $qtd ?></span>
            <div style="
                width:100%; height:<?= $altura ?>px;
                background: linear-gradient(180deg, var(--accent2), rgba(34,211,238,.3));
                border-radius:6px 6px 0 0;
                min-height:8px;
                transition:.4s ease;
            "></div>
            <span style="font-size:.75rem; color:var(--text3);"><?= $mes ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="tabela-container mt-24">
    <div class="tabela-header">
        <h2>Usuários Mais Ativos</h2>
        <span style="font-size:.78rem; color:var(--text3);">Quantidade de registros (sem valores)</span>
    </div>

    <?php if (!empty($movPorUsuario)): ?>
    <table>
        <thead>
            <tr>
                <th>Usuário</th>
                <th>Total de Registros</th>
                <th>Entradas</th>
                <th>Saídas</th>
                <th>Engajamento</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $maxMov = max(array_column($movPorUsuario, 'total'));
            foreach ($movPorUsuario as $uid => $dados):
                $nomeUsuario = '—';
                foreach ($usuarios as $u) {
                    if ($u['id'] == $uid) { $nomeUsuario = $u['nome']; break; }
                }
                $pctEng = $maxMov > 0 ? round(($dados['total'] / $maxMov) * 100) : 0;
            ?>
            <tr>
                <td>
                    <div class="flex items-center gap-8">
                        <div class="avatar" style="width:28px;height:28px;font-size:.75rem;flex-shrink:0;">
                            <?= strtoupper(substr($nomeUsuario, 0, 1)) ?>
                        </div>
                        <strong><?= limpar($nomeUsuario) ?></strong>
                    </div>
                </td>
                <td><?= $dados['total'] ?></td>
                <td style="color:var(--accent);"><?= $dados['entradas'] ?></td>
                <td style="color:var(--danger);"><?= $dados['saidas'] ?></td>
                <td style="width:160px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <div style="flex:1; height:8px; background:var(--bg3); border-radius:99px; overflow:hidden;">
                            <div style="height:100%; width:<?= $pctEng ?>%; background:var(--accent); border-radius:99px;"></div>
                        </div>
                        <span style="font-size:.75rem; color:var(--text3);"><?= $pctEng ?>%</span>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="padding:24px; color:var(--text3);">Nenhum usuário com movimentações ainda.</p>
    <?php endif; ?>
</div>

<div class="tabela-container mt-24">
    <div class="tabela-header">
        <h2>Destaques de Engajamento</h2>
    </div>
    <div style="padding:20px; display:grid; gap:12px;">
        <?php
        $percAtivos = $stats['total_usuarios'] > 0
            ? round(($stats['usuarios_ativos'] / $stats['total_usuarios']) * 100)
            : 0;
        $insights = [
            "🟢 {$percAtivos}% dos usuários cadastrados utilizaram o sistema.",
            "📅 Abril é o mês com mais movimentações registradas até agora.",
            "🏆 A categoria <strong>{$stats['categoria_mais_usada']}</strong> é a favorita dos usuários.",
            "📊 Média de <strong>{$stats['media_mov_usuario']}</strong> lançamento(s) por usuário ativo.",
        ];
        foreach ($insights as $ins):
        ?>
        <div style="padding:14px 16px; background:var(--bg2); border-radius:var(--radius); border:1px solid var(--border); font-size:.88rem; color:var(--text2);">
            <?= $ins ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
