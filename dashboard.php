<?php
// paginas/dashboard.php
$movs     = getMovimentacoes($_SESSION['usuario_id']);
$entradas = calcularTotal($movs, 'entrada');
$saidas   = calcularTotal($movs, 'saida');
$saldo    = calcularSaldo($movs);
$totCat   = totaisPorCategoria($movs);

// Últimas 5 movimentações
$ultimas = array_slice(array_reverse($movs), 0, 5);

// Alerta se saldo negativo
$alertaSaldo = $saldo < 0;
?>

<div class="page-header page-header-row">
    <div>
        <h1>Dashboard</h1>
        <p>Olá, <?= limpar($_SESSION['usuario_nome']) ?>. Aqui está seu resumo financeiro.</p>
    </div>
    <a href="index.php?pagina=nova_movimentacao" class="btn btn-primario">+ Nova Movimentação</a>
</div>

<?php if ($alertaSaldo): ?>
<div class="alerta alerta-erro">
    ⚠️ Atenção: seu saldo está negativo! Revise seus gastos.
</div>
<?php endif; ?>

<?php if (empty($movs)): ?>
<div class="alerta alerta-aviso">
    Você ainda não tem movimentações registradas. <a href="index.php?pagina=nova_movimentacao">Adicione a primeira</a>.
</div>
<?php endif; ?>

<!-- Cards de resumo -->
<div class="cards-grid">
    <div class="card">
        <div class="card-icon">💰</div>
        <div class="card-label">Total de Entradas</div>
        <div class="card-valor verde"><?= formatarMoeda($entradas) ?></div>
        <div class="card-detalhe"><?= count(array_filter($movs, fn($m) => $m['tipo'] === 'entrada')) ?> registros</div>
    </div>

    <div class="card">
        <div class="card-icon">💸</div>
        <div class="card-label">Total de Saídas</div>
        <div class="card-valor vermelho"><?= formatarMoeda($saidas) ?></div>
        <div class="card-detalhe"><?= count(array_filter($movs, fn($m) => $m['tipo'] === 'saida')) ?> registros</div>
    </div>

    <div class="card">
        <div class="card-icon">⚖️</div>
        <div class="card-label">Saldo Atual</div>
        <div class="card-valor <?= $saldo >= 0 ? 'ciano' : 'vermelho' ?>"><?= formatarMoeda($saldo) ?></div>
        <div class="card-detalhe"><?= $saldo >= 0 ? 'Positivo ✓' : 'Negativo ✗' ?></div>
    </div>

    <div class="card">
        <div class="card-icon">📂</div>
        <div class="card-label">Categorias Usadas</div>
        <div class="card-valor"><?= count($totCat) ?></div>
        <div class="card-detalhe">de <?= count(getCategorias()) ?> disponíveis</div>
    </div>
</div>

<!-- Grid: últimas movimentações + gastos por categoria -->
<div style="display:grid; grid-template-columns:1fr 320px; gap:20px; align-items:start;">

    <!-- Últimas movimentações -->
    <div class="tabela-container">
        <div class="tabela-header">
            <h2>Últimas Movimentações</h2>
            <a href="index.php?pagina=movimentacoes" class="btn btn-secundario btn-sm">Ver todas</a>
        </div>
        <?php if (!empty($ultimas)): ?>
        <table>
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Categoria</th>
                    <th>Data</th>
                    <th>Valor</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ultimas as $mov): ?>
                <tr>
                    <td><strong><?= limpar($mov['descricao']) ?></strong></td>
                    <td><?= limpar($mov['categoria']) ?></td>
                    <td><?= formatarData($mov['data']) ?></td>
                    <td><?= formatarMoeda($mov['valor']) ?></td>
                    <td>
                        <span class="badge badge-<?= $mov['tipo'] ?>">
                            <?= $mov['tipo'] === 'entrada' ? '↑' : '↓' ?>
                            <?= ucfirst($mov['tipo']) ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p style="padding:24px; color:var(--text3); font-size:.88rem;">Nenhuma movimentação encontrada.</p>
        <?php endif; ?>
    </div>

    <!-- Gastos por categoria -->
    <div class="tabela-container">
        <div class="tabela-header">
            <h2>Gastos por Categoria</h2>
        </div>
        <?php if (!empty($totCat)): ?>
        <div style="padding:16px 20px;">
            <?php
            $maiorValor = max($totCat);
            foreach ($totCat as $cat => $val):
                $pct = $maiorValor > 0 ? round(($val / $maiorValor) * 100) : 0;
            ?>
            <div style="margin-bottom:14px;">
                <div style="display:flex; justify-content:space-between; font-size:.82rem; color:var(--text2); margin-bottom:4px;">
                    <span><?= limpar($cat) ?></span>
                    <span><?= formatarMoeda($val) ?></span>
                </div>
                <div style="height:6px; background:var(--bg3); border-radius:99px; overflow:hidden;">
                    <div style="height:100%; width:<?= $pct ?>%; background:var(--danger); border-radius:99px; transition:.4s ease;"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="padding:24px; color:var(--text3); font-size:.88rem;">Nenhum gasto registrado.</p>
        <?php endif; ?>
    </div>

</div>
