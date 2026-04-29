<?php

$categorias = getCategorias();

$mensagem = '';
$tipoAlerta = '';
$dadosForm = [
    'descricao' => '',
    'valor' => '',
    'tipo' => 'saida',
    'categoria' => '',
    'data' => date('Y-m-d')
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = limpar($_POST['descricao'] ?? '');
    $valor     = $_POST['valor'] ?? '';
    $tipo      = limpar($_POST['tipo'] ?? '');
    $categoria = limpar($_POST['categoria'] ?? '');
    $data      = limpar($_POST['data'] ?? '');

    $dadosForm = compact('descricao', 'valor', 'tipo', 'categoria', 'data');

    $erros = [];

    if (empty($descricao)) $erros[] = 'A descrição é obrigatória.';
    if (!validarValor($valor)) $erros[] = 'Informe um valor válido.';
    if (!in_array($tipo, ['entrada','saida'])) $erros[] = 'Tipo inválido.';
    if (empty($categoria)) $erros[] = 'Selecione uma categoria.';
    if (!validarData($data)) $erros[] = 'Data inválida.';

    if (empty($erros)) {

    $movimentacoes = $_SESSION['movimentacoes'] ?? [];
    $proximoId = empty($movimentacoes) ? 1 : max(array_column($movimentacoes, 'id')) + 1;

        $nova = [
    'id' => $proximoId, 
    'descricao' => $descricao,
    'categoria' => $categoria,
    'data' => $data,
    'tipo' => $tipo,
    'valor' => $valor,
    'usuario_id' => $_SESSION['usuario_id']
];

        $_SESSION['movimentacoes'][] = $nova;

        $mensagem = "Movimentação \"$descricao\" salva!";
        $tipoAlerta = 'ok';

        $dadosForm = ['descricao' => '', 'valor' => '', 'tipo' => 'saida', 'categoria' => '', 'data' => date('Y-m-d')];

    } else {
        $mensagem = implode('<br>', $erros);
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
        <div class="form-group">
    <label>Tipo de Movimentação</label>

    <div style="display:flex; gap:12px;">
        <?php
        $tiposOpcao = ['saida' => '↓ Saída', 'entrada' => '↑ Entrada'];
        $tipoAtual = $dadosForm['tipo'] ?? 'saida';

        foreach ($tiposOpcao as $val => $label):
            $id = 'tipo_' . $val;
            $selecionado = $tipoAtual === $val;
        ?>
            <input 
        type="radio" 
        id="<?= $id ?>" 
        name="tipo" 
        value="<?= $val ?>" 
        <?= $tipoAtual === $val ? 'checked' : '' ?>
        class="radio-hidden"
    >


            <label for="<?= $id ?>" class="radio-card <?= $val ?> <?= $selecionado ? 'ativo' : '' ?>">
    <span><?= $label ?></span>
</label>

        <?php endforeach; ?>
    </div>
</div>
        
        <div class="form-group">
            <label for="descricao">Descrição</label>
            <input
                type="text"
                id="descricao"
                name="descricao"
                placeholder="Ex.: Mercado, Salário, Uber..."
                value="<?= $dadosForm['descricao'] ?? '' ?>"
                maxlength="100"
                required
            >
        </div>

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

        <div class="flex gap-12" style="margin-top:28px;">
            <button type="submit" class="btn btn-primario">Salvar Movimentação</button>
            <a href="index.php?pagina=movimentacoes" class="btn btn-secundario">Cancelar</a>
        </div>

    </form>
</div>

