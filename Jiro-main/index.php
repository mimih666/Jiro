<?php

// ============================================================
//  index.php — Arquivo principal do JIRO (controlador central)
// ============================================================
session_start();

require_once 'funcoes.php';

// ⭐ INICIALIZAR SESSÕES
if (!isset($_SESSION['movimentacoes'])) {
    $_SESSION['movimentacoes'] = [];
}

// ⭐ INICIALIZAR CATEGORIAS NA SESSÃO
if (!isset($_SESSION['categorias'])) {
    $_SESSION['categorias'] = CATEGORIAS_PADRAO;
}

// --- Logout ---
if (isset($_GET['acao']) && $_GET['acao'] === 'logout') {
    logout();
    header('Location: index.php?pagina=login');
    exit;
}

// --- Página solicitada (GET) ---
$pagina = isset($_GET['pagina']) ? limpar($_GET['pagina']) : 'login';

// --- Redirecionar usuário já logado para o dashboard ---
if ($pagina === 'login' && estaLogado()) {
    header('Location: index.php?pagina=dashboard');
    exit;
}

// --- Páginas que exigem login ---
$paginasProtegidas = ['dashboard', 'movimentacoes', 'nova_movimentacao', 'categorias', 'relatorio'];
$paginasAdmin      = ['admin', 'admin_usuarios', 'admin_estatisticas', 'admin_uso'];

if (in_array($pagina, $paginasProtegidas)) {
    exigirLogin();
}
if (in_array($pagina, $paginasAdmin)) {
    exigirAdmin();
}

// --- Processar login (POST) ---
$erroLogin = '';
if ($pagina === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? limpar($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';

    $usuario = validarLogin($email, $senha);
    if ($usuario) {
        iniciarSessao($usuario);
        header('Location: index.php?pagina=dashboard');
        exit;
    } else {
        $erroLogin = 'E-mail ou senha incorretos.';
    }
}

// --- Definir título da página ---
$titulos = [
    'login'              => 'Login',
    'dashboard'          => 'Dashboard',
    'movimentacoes'      => 'Movimentações',
    'nova_movimentacao'  => 'Nova Movimentação',
    'categorias'         => 'Categorias',
    'relatorio'          => 'Relatório',
    'admin'              => 'Painel Admin',
    'admin_usuarios'     => 'Usuários',
    'admin_estatisticas' => 'Estatísticas',
    'admin_uso'          => 'Uso do Sistema',
];
$titulo = isset($titulos[$pagina]) ? $titulos[$pagina] . ' — Jiro' : 'Jiro';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="pagina-<?= $pagina ?>">

<?php if (estaLogado()): ?>
    <?php require_once 'paginas/menu.php'; ?>
<?php endif; ?>

<main class="conteudo<?= estaLogado() ? ' com-menu' : '' ?>">
<?php

switch ($pagina) {
    case 'login':
        require_once 'paginas/login.php';
        break;
    case 'dashboard':
        require_once 'paginas/dashboard.php';
        break;
    case 'movimentacoes':
        require_once 'paginas/movimentacoes.php';
        break;
    case 'nova_movimentacao':
        require_once 'paginas/nova_movimentacao.php';
        break;
    case 'categorias':
        require_once 'paginas/categorias.php';
        break;
    case 'relatorio':
        require_once 'paginas/relatorio.php';
        break;
    case 'admin':
        require_once 'paginas/admin.php';
        break;
    case 'admin_usuarios':
        require_once 'paginas/admin_usuarios.php';
        break;
    case 'admin_estatisticas':
        require_once 'paginas/admin_estatisticas.php';
        break;
    case 'admin_uso':
        require_once 'paginas/admin_uso.php';
        break;
    default:
        echo '<div class="erro-404"><h2>Página não encontrada.</h2><a href="index.php?pagina=dashboard">Voltar ao início</a></div>';
}
?>
</main>

</body>
</html>