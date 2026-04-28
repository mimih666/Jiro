<?php
$categorias = getCategorias() ?? [];

$mensagem   = '';
$tipoAlerta = '';

// ===== ADICIONAR =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nova = limpar($_POST['nova_categoria'] ?? '');

    if (empty($nova)) {
        $mensagem   = 'Informe o nome da categoria.';
        $tipoAlerta = 'erro';

    } elseif (in_array(strtolower($nova), array_map('strtolower', $categorias))) {
        $mensagem   = "A categoria \"$nova\" já existe.";
        $tipoAlerta = 'aviso';

    } else {
        if (adicionarCategoria($nova)) {
            $categorias = getCategorias();
            $mensagem   = "Categoria \"$nova\" adicionada com sucesso!";
            $tipoAlerta = 'ok';
        } else {
            $mensagem   = "Erro ao adicionar categoria.";
            $tipoAlerta = 'erro';
        }
    }
}

// ===== EXCLUIR =====
if (isset($_GET['acao']) && $_GET['acao'] === 'excluir' && isset($_GET['cat'])) {
    $cat = limpar(urldecode($_GET['cat']));

    if (excluirCategoria($cat)) {
        $categorias = getCategorias();
        $mensagem   = "Categoria \"$cat\" excluída com sucesso!";
        $tipoAlerta = 'ok';
    } else {
        $mensagem   = "Erro ao excluir categoria.";
        $tipoAlerta = 'erro';
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

    <!-- LISTA -->
    <div class="tabela-container">
        <div class="tabela-header">
            <h2>Categorias Disponíveis (<?= count($categorias) ?>)</h2>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($categorias)): ?>
                    <tr>
                        <td colspan="3" style="text-align:center; color:var(--text3);">
                            Nenhuma categoria cadastrada.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($categorias as $i => $cat): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><strong><?= limpar($cat) ?></strong></td>
                    <td>
                        <div class="flex gap-8">

                            <!-- EXCLUIR REAL -->
                            <a href="index.php?pagina=categorias&acao=excluir&cat=<?= urlencode($cat) ?>"
                               class="btn btn-perigo btn-sm"
                               onclick="return confirm('Excluir a categoria <?= addslashes($cat) ?>?');">
                               Excluir
                            </a>

                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- FORM -->
    <div class="form-card">
        <h2 style="margin-bottom:20px;">Adicionar Categoria</h2>

        <form method="POST">
            <div class="form-group">
                <label>Nome da Categoria</label>
                <input
                    type="text"
                    name="nova_categoria"
                    placeholder="Ex: Viagens, Pets..."
                    maxlength="50"
                    required
                >
            </div>

            <button class="btn btn-primario" style="width:100%;">
                + Adicionar
            </button>
        </form>
    </div>

</div>