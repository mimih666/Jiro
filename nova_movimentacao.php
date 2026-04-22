<?php
// paginas/nova_movimentacao.php
$categorias = getCategorias();
$mensagem   = '';
$tipoAlerta = '';
$dadosForm  = ['descricao' => '', 'valor' => '', 'tipo' => 'saida', 'categoria' => '', 'data' => date('Y-m-d')];

// Processar formulário (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = limpar($_POST['descricao'] ?? '');
    $valor     = $_POST['valor'] ?? '';
    $tipo      = limpar($_POST['tipo'] ?? '');
    $categoria = limpar($_POST['categoria'] ?? '');
    $data      = limpar($_POST['data'] ?? '');

    // Preservar dados no form em caso de erro
    $dadosForm = compact('descricao', 'valor', 'tipo', 'categoria', 'data');

    // Validações
    $erros = [];
    if (empty($descricao))                  $erros[] = 'A descrição é obrigatória.';
    if (!validarValor($valor))              $erros[] = 'Informe um valor numérico positivo.';
    if (!in_array($tipo, ['entrada','saida'])) $erros[] = 'Tipo inválido.';
    if (empty($categoria))                  $erros[] = 'Selecione uma categoria.';
    if (!validarData($data))                $erros[] = 'Data inválida.';

    if (empty($erros)) {
        // Aqui os dados seriam salvos no banco (MySQL na 2ª entrega).
        // Por ora, apenas confirmamos o sucesso.
        $mensagem   = "✅ Movimentação \"" . $descricao . "\" de " . formatarMoeda((float)$valor) . " registrada com sucesso!";
        $tipoAlerta = 'ok';
        $dadosForm  = ['descricao' => '', 'valor' => '', 'tipo' => 'saida', 'categoria' => '', 'data' => date('Y-m-d')];
    } else {
        $mensagem   = implode('<br>', $erros);
        $tipoAlerta = 'erro';
    }
}
?>

<div class="page-header">
    <h1>Nova Movimentação</h1>
    <p>Registre uma entrada ou saída financeira.</p>
</div>

<?php if ($mensagem): ?>
    <div class="alerta alerta-<?= $tipoAlerta ?>"><?= $mensagem ?></div>
<?php endif; ?>

<div class="form-card">
    <form method="POST" action="index.php?pagina=nova_movimentacao">

        <!-- Tipo: entrada ou saída -->
        <div class="form-group">
            <label>Tipo de Movimentação</label>
            <div style="display:flex; gap:12px;">
                <?php
                $tiposOpcao = ['saida' => '↓ Saída', 'entrada' => '↑ Entrada'];
                foreach ($tiposOpcao as $val => $label):
                    $selecionado = $dadosForm['tipo'] === $val;
                ?>
                <label style="
                    flex:1; display:flex; align-items:center; justify-content:center; gap:8px;
                    padding:12px; border-radius:var(--radius); cursor:pointer;
                    border:2px solid <?= $selecionado ? ($val === 'entrada' ? 'var(--accent)' : 'var(--danger)') : 'var(--border)' ?>;
                    background:<?= $selecionado ? ($val === 'entrada' ? 'rgba(74,222,128,.08)' : 'rgba(248,113,113,.08)') : 'var(--bg2)' ?>;
                    font-size:.88rem; font-weight:600;
                    color:<?= $val === 'entrada' ? 'var(--accent)' : 'var(--danger)' ?>;
                ">
                    <input type="radio" name="tipo" value="<?= $val ?>" <?= $selecionado ? 'checked' : '' ?> style="display:none;">
                    <?= $label ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Descrição -->
        <div class="form-group">
            <label for="descricao">Descrição</label>
            <input
                type="text"
                id="descricao"
                name="descricao"
                placeholder="Ex.: Mercado, Salário, Uber..."
                value="<?= $dadosForm['descricao'] ?>"
                maxlength="100"
                required
            >
        </div>

        <!-- Valor e Data -->
        <div class="form-row">
            <div class="form-group">
                <label for="valor">Valor (R$)</label>
                <input
                    type="number"
                    id="valor"
                    name="valor"
                    placeholder="0,00"
                    step="0.01"
                    min="0.01"
                    value="<?= $dadosForm['valor'] ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="data">Data</label>
                <input
                    type="date"
                    id="data"
                    name="data"
                    value="<?= $dadosForm['data'] ?>"
                    required
                >
            </div>
        </div>

        <!-- Categoria -->
        <div class="form-group">
            <label for="categoria">Categoria</label>
            <select id="categoria" name="categoria" required>
                <option value="">Selecione uma categoria...</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat ?>" <?= $dadosForm['categoria'] === $cat ? 'selected' : '' ?>>
                        <?= $cat ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Botões -->
        <div class="flex gap-12" style="margin-top:28px;">
            <button type="submit" class="btn btn-primario">Salvar Movimentação</button>
            <a href="index.php?pagina=movimentacoes" class="btn btn-secundario">Cancelar</a>
        </div>

    </form>
</div>

<div class="alerta alerta-aviso mt-24" style="max-width:560px;">
    ℹ️ <strong>Fase 1:</strong> os dados são simulados em arrays. O banco de dados será integrado na 2ª entrega.
</div>
