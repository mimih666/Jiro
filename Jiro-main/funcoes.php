<?php
define('CATEGORIAS_PADRAO', [
    'Alimentação',
    'Transporte',
    'Lazer',
    'Saúde',
    'Educação',
    'Moradia',
    'Roupas',
    'Tecnologia',
    'Outros',
]);

function getUsuarios() {
    return [
        [
            'id'     => 1,
            'nome'   => 'Administrador',
            'email'  => 'admin@jiro.com',
            'senha'  => md5('admin123'),
            'perfil' => 'admin',
            'ativo'  => true,
        ],
        [
            'id'     => 2,
            'nome'   => 'João Silva',
            'email'  => 'joao@email.com',
            'senha'  => md5('joao123'),
            'perfil' => 'usuario',
            'ativo'  => true,
        ],
        [
            'id'     => 3,
            'nome'   => 'Maria Oliveira',
            'email'  => 'maria@email.com',
            'senha'  => md5('maria123'),
            'perfil' => 'usuario',
            'ativo'  => true,
        ],
    ];
}

function getCategorias() {
    if (isset($_SESSION['categorias']) && is_array($_SESSION['categorias'])) {
        return $_SESSION['categorias'];
    }
    
    $_SESSION['categorias'] = CATEGORIAS_PADRAO;
    return $_SESSION['categorias'];
}

function adicionarCategoria($nome) {
    $nome = limpar($nome);
    
    if (empty($nome)) {
        return false;
    }
    
    $categorias = getCategorias();
    
    if (in_array($nome, $categorias)) {
        return false;
    }
    
    $_SESSION['categorias'][] = $nome;
    return true;
}

function getMovimentacoes($usuario_id = null) {

    $movs = $_SESSION['movimentacoes'] ?? [];

    if ($usuario_id !== null) {
        $filtradas = [];

        foreach ($movs as $mov) {

            // 🔒 evita erro
            if (
                isset($mov['usuario_id']) &&
                $mov['usuario_id'] == $usuario_id
            ) {
                $filtradas[] = $mov;
            }
        }

        return $filtradas;
    }

    return $movs;
}

function adicionarMovimentacao($usuario_id, $tipo, $valor, $categoria, $data, $descricao) {
    
    if (empty($usuario_id) || empty($tipo) || empty($valor) || empty($categoria) || empty($data) || empty($descricao)) {
        return false;
    }
    
    if (!in_array($tipo, ['entrada', 'saida'])) {
        return false;
    }
    
    if (!validarValor($valor)) {
        return false;
    }
    
    if (!validarData($data)) {
        return false;
    }
    
    if (!isset($_SESSION['movimentacoes'])) {
        $_SESSION['movimentacoes'] = [];
    }
    
    $proximoId = 1;
    if (!empty($_SESSION['movimentacoes'])) {
        $ids = array_column($_SESSION['movimentacoes'], 'id');
        if (!empty($ids)) {
            $proximoId = max($ids) + 1;
        }
    }
    
    $_SESSION['movimentacoes'][] = [
        'id'          => $proximoId,
        'usuario_id'  => $usuario_id,
        'tipo'        => $tipo,
        'valor'       => (float) $valor,
        'categoria'   => $categoria,
        'data'        => $data,
        'descricao'   => $descricao,
    ];
    
    return true;
}

function deletarMovimentacao($id) {
    if (!isset($_SESSION['movimentacoes'])) {
        return false;
    }
    
    foreach ($_SESSION['movimentacoes'] as $key => $mov) {
        if ($mov['id'] == $id) {
            unset($_SESSION['movimentacoes'][$key]);
            $_SESSION['movimentacoes'] = array_values($_SESSION['movimentacoes']);
            return true;
        }
    }
    
    return false;
}

function editarMovimentacao($id, $tipo, $valor, $categoria, $data, $descricao) {
    if (!isset($_SESSION['movimentacoes'])) {
        return false;
    }
    
    if (!validarValor($valor) || !validarData($data)) {
        return false;
    }
    
    foreach ($_SESSION['movimentacoes'] as &$mov) {
        if ($mov['id'] == $id) {
            $mov['tipo']      = $tipo;
            $mov['valor']     = (float) $valor;
            $mov['categoria'] = $categoria;
            $mov['data']      = $data;
            $mov['descricao'] = $descricao;
            return true;
        }
    }
    
    return false;
}

function validarLogin($email, $senha) {
    if (empty($email) || empty($senha)) {
        return false;
    }

    $usuarios = getUsuarios();
    $hash     = md5($senha);

    foreach ($usuarios as $usuario) {
        if ($usuario['email'] === $email && $usuario['senha'] === $hash) {
            if (!$usuario['ativo']) {
                return false; 
            }
            return $usuario;
        }
    }

    return false;
}

function iniciarSessao($usuario) {
    $_SESSION['usuario_id']     = $usuario['id'];
    $_SESSION['usuario_nome']   = $usuario['nome'];
    $_SESSION['usuario_email']  = $usuario['email'];
    $_SESSION['usuario_perfil'] = $usuario['perfil'];
    $_SESSION['logado']         = true;
}

function logout() {
    session_unset();
    session_destroy();
}

function exigirLogin() {
    if (empty($_SESSION['logado'])) {
        header('Location: index.php?pagina=login');
        exit;
    }
}

function exigirAdmin() {
    exigirLogin();
    if ($_SESSION['usuario_perfil'] !== 'admin') {
        header('Location: index.php?pagina=dashboard');
        exit;
    }
}

function estaLogado() {
    return !empty($_SESSION['logado']);
}

function ehAdmin() {
    return estaLogado() && $_SESSION['usuario_perfil'] === 'admin';
}

function calcularTotal($movimentacoes, $tipo) {
    $total = 0;
    foreach ($movimentacoes as $mov) {
        if ($mov['tipo'] === $tipo) {
            $total += $mov['valor'];
        }
    }
    return $total;
}

function calcularSaldo($movimentacoes) {
    return calcularTotal($movimentacoes, 'entrada') - calcularTotal($movimentacoes, 'saida');
}

function totaisPorCategoria($movimentacoes) {
    $totais = [];
    foreach ($movimentacoes as $mov) {
        if ($mov['tipo'] === 'saida') {
            $cat = $mov['categoria'];
            if (!isset($totais[$cat])) {
                $totais[$cat] = 0;
            }
            $totais[$cat] += $mov['valor'];
        }
    }
    arsort($totais);
    return $totais;
}

function limpar($valor) {
    return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
}

function validarValor($valor) {
    return is_numeric($valor) && $valor > 0;
}

function validarData($data) {
    $d = DateTime::createFromFormat('Y-m-d', $data);
    return $d && $d->format('Y-m-d') === $data;
}

function formatarMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function formatarData($data) {
    $d = DateTime::createFromFormat('Y-m-d', $data);
    return $d ? $d->format('d/m/Y') : $data;
}

function getEstatisticasSistema() {
    $usuarios      = getUsuarios();
    $movimentacoes = getMovimentacoes();
    $categorias    = getCategorias();

    $totalUsuarios      = count($usuarios) - 1;
    $totalMovimentacoes = count($movimentacoes);

    $mediaMovPorUsuario = $totalUsuarios > 0
        ? round($totalMovimentacoes / $totalUsuarios, 1)
        : 0;

    $contCat = [];
    foreach ($movimentacoes as $mov) {
        $cat = $mov['categoria'];
        $contCat[$cat] = isset($contCat[$cat]) ? $contCat[$cat] + 1 : 1;
    }
    arsort($contCat);
    $categoriaMaisUsada = !empty($contCat) ? array_key_first($contCat) : '—';

    $usuariosComMov = [];
    foreach ($movimentacoes as $mov) {
        $usuariosComMov[$mov['usuario_id']] = true;
    }
    $usuariosAtivos = count($usuariosComMov);

    return [
        'total_usuarios'       => $totalUsuarios,
        'total_movimentacoes'  => $totalMovimentacoes,
        'media_mov_usuario'    => $mediaMovPorUsuario,
        'categoria_mais_usada' => $categoriaMaisUsada,
        'usuarios_ativos'      => $usuariosAtivos,
        'contagem_categorias'  => $contCat,
    ];
}

function excluirCategoria($nome) {
    if (!isset($_SESSION['categorias'])) {
        return false;
    }

    $categorias = $_SESSION['categorias'];

    $categorias = array_filter($categorias, function($c) use ($nome) {
        return strtolower($c) !== strtolower($nome);
    });

    $_SESSION['categorias'] = array_values($categorias);

    return true;
}
function excluirUsuario($id) {

    if (!isset($_SESSION['usuarios'])) {
        return false;
    }

    if (isset($_SESSION['movimentacoes'])) {
        foreach ($_SESSION['movimentacoes'] as $m) {
            if ($m['usuario_id'] == $id) {
                return false;
            }
        }
    }

    $usuarios = $_SESSION['usuarios'];

    $usuarios = array_filter($usuarios, function($u) use ($id) {
        return $u['id'] != $id;
    });

    $_SESSION['usuarios'] = array_values($usuarios);

    return true;
}

?>

