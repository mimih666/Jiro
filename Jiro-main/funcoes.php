<?php
// ============================================================
//  funcoes.php — Funções centrais do sistema JIRO (CORRIGIDO)
// ============================================================

// CATEGORIAS PADRÃO (constante)
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

// ============================================================
// USUÁRIOS (simulados em array — futuramente MySQL)
// ============================================================
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

// ============================================================
// CATEGORIAS (com suporte a sessão)
// ============================================================

/**
 * Retorna categorias da sessão ou padrão.
 */
function getCategorias() {
    // ⭐ Se existir na sessão, use
    if (isset($_SESSION['categorias']) && is_array($_SESSION['categorias'])) {
        return $_SESSION['categorias'];
    }
    
    // Se não, retorne o padrão e SALVE NA SESSÃO
    $_SESSION['categorias'] = CATEGORIAS_PADRAO;
    return $_SESSION['categorias'];
}

/**
 * Adiciona uma nova categoria na sessão.
 */
function adicionarCategoria($nome) {
    $nome = limpar($nome);
    
    if (empty($nome)) {
        return false;
    }
    
    $categorias = getCategorias();
    
    if (in_array($nome, $categorias)) {
        return false;  // Já existe
    }
    
    $_SESSION['categorias'][] = $nome;
    return true;
}

// ------------------------------------------------------------
// MOVIMENTAÇÕES (simuladas — futuramente MySQL)
// ------------------------------------------------------------
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

// ============================================================
// ADICIONAR/EDITAR/DELETAR MOVIMENTAÇÕES
// ============================================================

/**
 * Adiciona uma nova movimentação à sessão.
 * Retorna true em caso de sucesso.
 */
function adicionarMovimentacao($usuario_id, $tipo, $valor, $categoria, $data, $descricao) {
    
    // 🔒 Validações
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
    
    // 🔒 Inicializa se não existir
    if (!isset($_SESSION['movimentacoes'])) {
        $_SESSION['movimentacoes'] = [];
    }
    
    // Cria ID único para a movimentação
    $proximoId = 1;
    if (!empty($_SESSION['movimentacoes'])) {
        $ids = array_column($_SESSION['movimentacoes'], 'id');
        if (!empty($ids)) {
            $proximoId = max($ids) + 1;
        }
    }
    
    // Adiciona à sessão
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

/**
 * Deleta uma movimentação pelo ID.
 */
function deletarMovimentacao($id) {
    if (!isset($_SESSION['movimentacoes'])) {
        return false;
    }
    
    foreach ($_SESSION['movimentacoes'] as $key => $mov) {
        if ($mov['id'] == $id) {
            unset($_SESSION['movimentacoes'][$key]);
            // Reindexar array
            $_SESSION['movimentacoes'] = array_values($_SESSION['movimentacoes']);
            return true;
        }
    }
    
    return false;
}

/**
 * Edita uma movimentação existente.
 */
function editarMovimentacao($id, $tipo, $valor, $categoria, $data, $descricao) {
    if (!isset($_SESSION['movimentacoes'])) {
        return false;
    }
    
    // 🔒 Validações
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

// ------------------------------------------------------------
// AUTENTICAÇÃO
// ------------------------------------------------------------

/**
 * Valida login por e-mail e senha.
 * Retorna o array do usuário em caso de sucesso, ou false.
 */
function validarLogin($email, $senha) {
    if (empty($email) || empty($senha)) {
        return false;
    }

    $usuarios = getUsuarios();
    $hash     = md5($senha);

    foreach ($usuarios as $usuario) {
        if ($usuario['email'] === $email && $usuario['senha'] === $hash) {
            if (!$usuario['ativo']) {
                return false;  // conta desativada
            }
            return $usuario;
        }
    }

    return false;
}

/**
 * Inicia sessão para o usuário autenticado.
 */
function iniciarSessao($usuario) {
    $_SESSION['usuario_id']     = $usuario['id'];
    $_SESSION['usuario_nome']   = $usuario['nome'];
    $_SESSION['usuario_email']  = $usuario['email'];
    $_SESSION['usuario_perfil'] = $usuario['perfil'];
    $_SESSION['logado']         = true;
}

/**
 * Encerra a sessão do usuário.
 */
function logout() {
    session_unset();
    session_destroy();
}

/**
 * Verifica se há um usuário logado. Redireciona para login se não houver.
 */
function exigirLogin() {
    if (empty($_SESSION['logado'])) {
        header('Location: index.php?pagina=login');
        exit;
    }
}

/**
 * Verifica se o usuário logado é administrador. Redireciona se não for.
 */
function exigirAdmin() {
    exigirLogin();
    if ($_SESSION['usuario_perfil'] !== 'admin') {
        header('Location: index.php?pagina=dashboard');
        exit;
    }
}

/**
 * Retorna true se houver sessão ativa.
 */
function estaLogado() {
    return !empty($_SESSION['logado']);
}

/**
 * Retorna true se o usuário logado for admin.
 */
function ehAdmin() {
    return estaLogado() && $_SESSION['usuario_perfil'] === 'admin';
}

// ------------------------------------------------------------
// CÁLCULOS FINANCEIROS
// ------------------------------------------------------------

/**
 * Soma todos os valores de um tipo ('entrada' ou 'saida') nas movimentações.
 */
function calcularTotal($movimentacoes, $tipo) {
    $total = 0;
    foreach ($movimentacoes as $mov) {
        if ($mov['tipo'] === $tipo) {
            $total += $mov['valor'];
        }
    }
    return $total;
}

/**
 * Retorna o saldo (entradas - saídas).
 */
function calcularSaldo($movimentacoes) {
    return calcularTotal($movimentacoes, 'entrada') - calcularTotal($movimentacoes, 'saida');
}

/**
 * Agrupa totais de saída por categoria.
 * Retorna array ['Categoria' => total].
 */
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

// ------------------------------------------------------------
// VALIDAÇÃO DE FORMULÁRIOS
// ------------------------------------------------------------

/**
 * Sanitiza string de entrada.
 */
function limpar($valor) {
    return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
}

/**
 * Valida se um valor numérico é positivo.
 */
function validarValor($valor) {
    return is_numeric($valor) && $valor > 0;
}

/**
 * Valida formato de data (YYYY-MM-DD).
 */
function validarData($data) {
    $d = DateTime::createFromFormat('Y-m-d', $data);
    return $d && $d->format('Y-m-d') === $data;
}

// ------------------------------------------------------------
// FORMATAÇÃO
// ------------------------------------------------------------

/**
 * Formata valor como moeda brasileira.
 */
function formatarMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

/**
 * Formata data de Y-m-d para d/m/Y.
 */
function formatarData($data) {
    $d = DateTime::createFromFormat('Y-m-d', $data);
    return $d ? $d->format('d/m/Y') : $data;
}

// ------------------------------------------------------------
// ESTATÍSTICAS DO ADMIN
// ------------------------------------------------------------

/**
 * Retorna array com estatísticas gerais do sistema (anônimas).
 */
function getEstatisticasSistema() {
    $usuarios      = getUsuarios();
    $movimentacoes = getMovimentacoes();
    $categorias    = getCategorias();

    $totalUsuarios      = count($usuarios) - 1; // exclui o admin
    $totalMovimentacoes = count($movimentacoes);

    // Média de movimentações por usuário (excluindo admin)
    $mediaMovPorUsuario = $totalUsuarios > 0
        ? round($totalMovimentacoes / $totalUsuarios, 1)
        : 0;

    // Categoria mais usada
    $contCat = [];
    foreach ($movimentacoes as $mov) {
        $cat = $mov['categoria'];
        $contCat[$cat] = isset($contCat[$cat]) ? $contCat[$cat] + 1 : 1;
    }
    arsort($contCat);
    $categoriaMaisUsada = !empty($contCat) ? array_key_first($contCat) : '—';

    // Usuários ativos (com ao menos uma movimentação)
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

    // 🚫 verifica movimentações
    if (isset($_SESSION['movimentacoes'])) {
        foreach ($_SESSION['movimentacoes'] as $m) {
            if ($m['usuario_id'] == $id) {
                return false; // não exclui
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

