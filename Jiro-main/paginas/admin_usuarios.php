<?php
$usuarios  = getUsuarios();
$movs      = getMovimentacoes();
$mensagem = '';
$tipoAlerta = '';

if (isset($_GET['acao']) && $_GET['acao'] === 'excluir' && isset($_GET['id'])) {

    $id = (int) $_GET['id'];

    if ($id == $_SESSION['usuario_id']) {
        $mensagem = "Você não pode excluir seu próprio usuário.";
        $tipoAlerta = "erro";

    } else {
        if (excluirUsuario($id)) {
            $usuarios = getUsuarios();
            $mensagem = "Usuário excluído com sucesso!";
            $tipoAlerta = "ok";
        } else {
            $mensagem = "Não é possível excluir usuário com movimentações.";
            $tipoAlerta = "erro";
        }
    }
}

$contMov = [];
foreach ($movs as $m) {
    $uid = $m['usuario_id'];
    $contMov[$uid] = isset($contMov[$uid]) ? $contMov[$uid] + 1 : 1;
}
?>

<div class="page-header">
    <?php if (!empty($mensagem)): ?>
    <div class="alerta alerta-<?= $tipoAlerta ?>"><?= $mensagem ?></div>
<?php endif; ?>
    <h1>Usuários</h1>
    <p>Lista de usuários cadastrados no sistema. Dados financeiros individuais não são exibidos.</p>
</div>

<div class="tabela-container">
    <div class="tabela-header">
        <h2>Todos os Usuários (<?= count($usuarios) ?>)</h2>
        <span class="badge badge-admin">Admin</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Ações</th>
                <th>#</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Perfil</th>
                <th>Status</th>
                <th>Movimentações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
            <tr>
                <td style="color:var(--text3);"><?= $u['id'] ?></td>
                <td>
                    <div class="flex items-center gap-8">
                        <div class="avatar" style="width:28px;height:28px;font-size:.75rem;flex-shrink:0;">
                            <?= strtoupper(substr($u['nome'], 0, 1)) ?>
                        </div>
                        <strong><?= limpar($u['nome']) ?></strong>
                    </div>
                </td>
                <td><?= limpar($u['email']) ?></td>
                <td>
                    <span class="badge <?= $u['perfil'] === 'admin' ? 'badge-admin' : 'badge-entrada' ?>">
                        <?= ucfirst($u['perfil']) ?>
                    </span>
                </td>
                <td>
                    <?php if ($u['ativo']): ?>
                        <span class="badge badge-entrada">● Ativo</span>
                    <?php else: ?>
                        <span class="badge badge-saida">● Inativo</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php
                    if ($u['perfil'] === 'admin') {
                        echo '<span style="color:var(--text3);">—</span>';
                    } else {
                        $qtd = isset($contMov[$u['id']]) ? $contMov[$u['id']] : 0;
                        echo $qtd . ' registro(s)';
                    }
                    ?>
                </td>
                <td>
    <?php if ($u['perfil'] !== 'admin'): ?>
        <a href="index.php?pagina=admin_usuarios&acao=excluir&id=<?= $u['id'] ?>"
            class="btn btn-perigo btn-sm"
            onclick="return confirm('Excluir usuário <?= addslashes($u['nome']) ?>?');">
            Excluir
        </a>
    <?php else: ?>
        <span style="color:var(--text3);">—</span>
    <?php endif; ?>
</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="alerta alerta-aviso mt-24">
    🔒 Privacidade: apenas a <strong>quantidade</strong> de registros por usuário é exibida. Valores e detalhes financeiros permanecem privados.
</div>
