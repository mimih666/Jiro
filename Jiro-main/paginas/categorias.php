<?php
// paginas/categorias.php

$categorias = getCategorias();  // ⭐ Sempre pega da sessão

$mensagem   = '';
$tipoAlerta = '';

// Processar adição de nova categoria (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nova = limpar($_POST['nova_categoria'] ?? '');

    if (empty($nova)) {
        $mensagem   = 'Informe o nome da categoria.';
        $tipoAlerta = 'erro';
    } elseif (in_array($nova, $categorias)) {
        $mensagem   = "A categoria \"$nova\" já existe.";
        $tipoAlerta = 'aviso';
    } else {
        // ⭐ Adicionar a categoria
        if (adicionarCategoria($nova)) {
            $categorias = getCategorias();  // Recarregar
            $mensagem   = "Categoria \"$nova\" adicionada com sucesso!";
            $tipoAlerta = 'ok';
        } else {
            $mensagem   = "Erro ao adicionar categoria.";
            $tipoAlerta = 'erro';
        }
    }
}
?>

<div class="page-header page-header-row">
    <div>
        <h1>Categorias</h1>
        <p>Gerencie as categorias para organizar suas movimentações.</p>
    </div>
</div>

<?php if ($mensagem): ?>
    <div class="alerta alerta-<?= $tipoAlerta ?>"><?= $mensagem ?></div>
<?php endif; ?>

<div style="display:grid; grid-template-columns:1fr 340px; gap:24px; align-items:start;">

    <!-- Lista de categorias -->
    <div class="tabela-container">
        <div class="tabela-header">
            <h2>Categorias Disponíveis (<?= count($categorias) ?>)</h2>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome da Categoria</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $i => $cat): ?>
                <tr>
                    <td style="color:var(--text3);"><?= $i + 1 ?></td>
                    <td><strong><?= limpar($cat) ?></strong></td>
                    <td>
                        <div class="flex gap-8">
                            <!-- Editar (simulado) -->
                            <a href="index.php?pagina=categorias&acao=editar&cat=<?= urlencode($cat) ?>"
                               class="btn btn-secundario btn-sm">Editar</a>
                            <!-- Excluir (simulado) -->
                            <a href="index.php?pagina=categorias&acao=excluir&cat=<?= urlencode($cat) ?>"
                               class="btn btn-perigo btn-sm"
                               onclick="return confirm('Excluir a categoria \'<?= $cat ?>\'?');">Excluir</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Formulário de nova categoria -->
    <div class="form-card" style="max-width:100%;">
        <h2 style="font-family:var(--font-display); font-size:1rem; font-weight:600; margin-bottom:20px;">
            Adicionar Categoria
        </h2>

        <form method="POST" action="index.php?pagina=categorias">
            <div class="form-group">
                <label for="nova_categoria">Nome da Categoria</label>
                <input
                    type="text"
                    id="nova_categoria"
                    name="nova_categoria"
                    placeholder="Ex.: Viagens, Pets..."
                    maxlength="50"
                    required
                >
            </div>
            <button type="submit" class="btn btn-primario" style="width:100%; justify-content:center;">
                + Adicionar
            </button>
        </form>

        <div class="alerta alerta-aviso mt-16" style="font-size:.8rem;">
            ℹ️ Na fase 2, edição e exclusão serão persistidas no banco de dados.
        </div>
    </div>

</div>

<?php
// Processar editar/excluir via GET (simulado)
if (isset($_GET['acao']) && isset($_GET['cat'])) {
    $acao = limpar($_GET['acao']);
    $cat  = urldecode(limpar($_GET['cat']));
    if ($acao === 'editar') {
        echo "<div class='alerta alerta-aviso mt-24'>✏️ Edição de \"$cat\" disponível na 2ª entrega (banco de dados).</div>";
    } elseif ($acao === 'excluir') {
        echo "<div class='alerta alerta-ok mt-24'>🗑️ Exclusão de \"$cat\" simulada (será persistida na 2ª entrega).</div>";
    }
}
?>