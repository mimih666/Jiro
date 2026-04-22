<?php
// ============================================================
//  funcoes.php — Funções centrais do sistema JIRO
// ============================================================

// ------------------------------------------------------------
// USUÁRIOS (simulados em array — futuramente MySQL)
// ------------------------------------------------------------
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

// ------------------------------------------------------------
// CATEGORIAS PADRÃO
// ------------------------------------------------------------
function getCategorias() {
    return [
        'Alimentação',
        'Transporte',
        'Lazer',
        'Saúde',
        'Educação',
        'Moradia',
        'Roupas',
        'Tecnologia',
        'Outros',
    ];
}

// ------------------------------------------------------------
// MOVIMENTAÇÕES (simuladas — futuramente MySQL)
// ------------------------------------------------------------
function getMovimentacoes($usuario_id = null) {
    $todas = [
        [
            'id'         => 1,
            'usuario_id' => 2,
            'descricao'  => 'Salário',
            'valor'      => 3500.00,
            'tipo'       => 'entrada',
            'categoria'  => 'Outros',
            'data'       => '2025-04-01',
        ],
        [
            'id'         => 2,
            'usuario_id' => 2,
            'descricao'  => 'Mercado',
            'valor'      => 320.50,
            'tipo'       => 'saida',
            'categoria'  => 'Alimentação',
            'data'       => '2025-04-05',
        ],
        [
            'id'         => 3,
            'usuario_id' => 2,
            'descricao'  => 'Uber',
            'valor'      => 45.00,
            'tipo'       => 'saida',
            'categoria'  => 'Transporte',
            'data'       => '2025-04-07',
        ],
        [
            'id'         => 4,
            'usuario_id' => 3,
            'descricao'  => 'Freela design',
            'valor'      => 800.00,
            'tipo'       => 'entrada',
            'categoria'  => 'Outros',
            'data'       => '2025-04-10',
        ],
        [
            'id'         => 5,
            'usuario_id' => 3,
            'descricao'  => 'Farmácia',
            'valor'      => 78.90,
            'tipo'       => 'saida',
            'categoria'  => 'Saúde',
            'data'       => '2025-04-12',
        ],
    ];

    if ($usuario_id !== null) {
        $filtradas = [];
        foreach ($todas as $mov) {
            if ($mov['usuario_id'] == $usuario_id) {
                $filtradas[] = $mov;
            }
        }
        return $filtradas;
    }

    return $todas;
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
