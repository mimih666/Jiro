<?php
// paginas/movimentacoes.php
$movs = getMovimentacoes($_SESSION['usuario_id']);

// Filtro por tipo via GET
$filtroTipo = isset($_GET['tipo']) ? limpar($_GET['tipo']) : '';
if ($filtroTipo !== '') {
    $movs = array_filter($movs, function($m) use ($filtroTipo) {
        return $m['tipo'] === $filtroTipo;
    });
}

// Filtro por categoria via GET
$filtroCategoria = isset($_GET['categoria']) ? limpar($_GET['categoria']) : '';
if ($filtroCategoria !== '') {
    $movs = array_filter($movs, function($m) use ($filtroCategoria) {
        return $m['categoria'] === $filtroCategoria;
    });
}

$categorias = getCategorias();
?>

<div class="page-header page-header-row">
    <div>
        <h1>Movimentações</h1>
        <p>Histórico completo das suas entradas e saídas.</p>
    </div>
    <a href="index.php?pagina=nova_movimentacao" class="btn btn-primario">+ Nova Movimentação</a>
</div>

<!-- Filtros via GET -->
<form method="GET" action="index.php" style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:24px;">
    <input type="hidden" name="pagina" value="movimentacoes">

    <select name="tipo" style="background:var(--card); border:1px solid var(--border); color:var(--text); padding:8px 12px; border-radius:var(--radius); font-family:var(--font-body); font-size:.85rem;">
        <option value="">Todos os tipos</option>
        <option value="entrada" <?= $filtroTipo === 'entrada' ? 'selected' : '' ?>>Entradas</option>
        <option value="saida"   <?= $filtroTipo === 'saida'   ? 'selected' : '' ?>>Saídas</option>
    </select>

    <select name="categoria" style="background:var(--card); border:1px solid var(--border); color:var(--text); padding:8px 12px; border-radius:var(--radius); font-family:var(--font-body); font-size:.85rem;">
        <option value="">Todas as categorias</option>
        <?php foreach ($categorias as $cat): ?>
            <option value="<?= $cat ?>" <?= $filtroCategoria === $cat ? 'selected' : '' ?>><?= $cat ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-secundario">Filtrar</button>
    <?php if ($filtroTipo || $filtroCategoria): ?>
        <a href="index.php?pagina=movimentacoes" class="btn btn-secundario">Limpar</a>
    <?php endif; ?>
</form>

<!-- Tabela de movimentações -->
<div class="tabela-container">
    <div class="tabela-header">
        <h2>
            <?php
            if ($filtroTipo || $filtroCategoria) {
                echo 'Resultados filtrados (' . count($movs) . ')';
            } else {
                echo 'Todas as movimentações (' . count($movs) . ')';
            }
            ?>
        </h2>
        <a href="index.php?pagina=relatorio" class="btn btn-secundario btn-sm">Ver relatório</a>
    </div>

    <?php if (!empty($movs)): ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Descrição</th>
                <th>Categoria</th>
                <th>Data</th>
                <th>Tipo</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; foreach ($movs as $mov): ?>
            <tr>
                <td style="color:var(--text3);"><?= $i++ ?></td>
                <td><strong><?= limpar($mov['descricao']) ?></strong></td>
                <td><?= limpar($mov['categoria']) ?></td>
                <td><?= formatarData($mov['data']) ?></td>
                <td>
                    <span class="badge badge-<?= $mov['tipo'] ?>">
                        <?= $mov['tipo'] === 'entrada' ? '↑ Entrada' : '↓ Saída' ?>
                    </span>
                </td>
                <td style="font-weight:600; color:<?= $mov['tipo'] === 'entrada' ? 'var(--accent)' : 'var(--danger)' ?>;">
                    <?= $mov['tipo'] === 'entrada' ? '+' : '-' ?><?= formatarMoeda($mov['valor']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="padding:32px 24px; color:var(--text3); text-align:center;">
        Nenhuma movimentação encontrada com os filtros aplicados.
    </p>
    <?php endif; ?>
</div>
