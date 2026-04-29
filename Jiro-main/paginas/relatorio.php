<?php
$movs = getMovimentacoes($_SESSION['usuario_id']);

// 🔒 garante array válido
if (!is_array($movs)) {
    $movs = [];
}

$movs = array_filter($movs, function($m) {
    return isset($m['tipo'], $m['valor'], $m['categoria'], $m['data'], $m['descricao']);
});

$entradas = calcularTotal($movs, 'entrada');
$saidas   = calcularTotal($movs, 'saida');
$saldo    = calcularSaldo($movs);
$totCat   = totaisPorCategoria($movs);

$totalMov = count($movs);
$percSaida = $entradas > 0 ? round(($saidas / $entradas) * 100) : 0;

$qtdEntradas = count(array_filter($movs, function($m) {
    return isset($m['tipo']) && $m['tipo'] === 'entrada';
}));

$qtdSaidas = count(array_filter($movs, function($m) {
    return isset($m['tipo']) && $m['tipo'] === 'saida';
}));
?>

<div class="page-header">
    <h1>Relatório Financeiro</h1>
    <p>Resumo completo do seu período financeiro.</p>
</div>

<div class="cards-grid">
    <div class="card">
        <div class="card-icon">📥</div>
        <div class="card-label">Entradas</div>
        <div class="card-valor verde"><?= formatarMoeda($entradas) ?></div>
        <div class="card-detalhe"><?= $qtdEntradas ?> lançamento(s)</div>
    </div>

    <div class="card">
        <div class="card-icon">📤</div>
        <div class="card-label">Saídas</div>
        <div class="card-valor vermelho"><?= formatarMoeda($saidas) ?></div>
        <div class="card-detalhe"><?= $qtdSaidas ?> lançamento(s)</div>
    </div>

    <div class="card">
        <div class="card-icon">💼</div>
        <div class="card-label">Saldo Final</div>
        <div class="card-valor <?= $saldo >= 0 ? 'ciano' : 'vermelho' ?>">
            <?= formatarMoeda($saldo) ?>
        </div>
        <div class="card-detalhe">
            <?= $saldo >= 0 ? '👍 Positivo' : '⚠️ Negativo' ?>
        </div>
    </div>

    <div class="card">
        <div class="card-icon">📊</div>
        <div class="card-label">Gasto vs Recebido</div>
        <div class="card-valor"><?= $percSaida ?>%</div>
        <div class="card-detalhe">das entradas foram gastas</div>
    </div>
</div>

<?php if ($percSaida > 90): ?>
<div class="alerta alerta-erro">
    <strong>Alerta:</strong> Você gastou <?= $percSaida ?>% de tudo que recebeu!
</div>
<?php elseif ($percSaida > 70): ?>
<div class="alerta alerta-aviso">
    <strong>Atenção:</strong> Seus gastos estão em <?= $percSaida ?>%.
</div>
<?php elseif ($totalMov > 0): ?>
<div class="alerta alerta-ok">
    <strong>Ótimo:</strong> Você está controlando bem seus gastos!
</div>
<?php endif; ?>

<?php if (!empty($totCat)): ?>
<div class="tabela-container mt-32">
    <div class="tabela-header">
        <h2>Gastos por Categoria</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>Categoria</th>
                <th>Total</th>
                <th>%</th>
                <th>Barra</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($totCat as $cat => $val):
                $pct = $saidas > 0 ? round(($val / $saidas) * 100, 1) : 0;
            ?>
            <tr>
                <td><strong><?= limpar($cat) ?></strong></td>
                <td style="color:var(--danger);"><?= formatarMoeda($val) ?></td>
                <td><?= $pct ?>%</td>
                <td style="width:200px;">
                    <div style="height:8px; background:var(--bg3); border-radius:99px;">
                        <div style="width:<?= $pct ?>%; height:100%; background:var(--danger);"></div>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<div class="tabela-container mt-24">
    <div class="tabela-header">
        <h2>Extrato (<?= $totalMov ?> registros)</h2>
    </div>

    <?php if (!empty($movs)): ?>
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Descrição</th>
                <th>Categoria</th>
                <th>Tipo</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($movs as $mov):
                $tipo  = $mov['tipo'] ?? '';
                $valor = $mov['valor'] ?? 0;
            ?>
            <tr>
                <td><?= formatarData($mov['data']) ?></td>
                <td><strong><?= limpar($mov['descricao']) ?></strong></td>
                <td><?= limpar($mov['categoria']) ?></td>
                <td>
                    <span class="badge badge-<?= $tipo ?>">
                        <?= $tipo === 'entrada' ? '↑ Entrada' : '↓ Saída' ?>
                    </span>
                </td>
                <td style="font-weight:600; color:<?= $tipo === 'entrada' ? 'var(--accent)' : 'var(--danger)' ?>;">
                    <?= $tipo === 'entrada' ? '+' : '-' ?><?= formatarMoeda($valor) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p style="padding:32px; text-align:center;">Nenhuma movimentação encontrada.</p>
    <?php endif; ?>
</div>