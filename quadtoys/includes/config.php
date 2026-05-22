<?php
/**
 * Configuração de conexão com o banco de dados.
 *
 * INSTRUÇÕES:
 * - Para rodar LOCALMENTE (XAMPP/WAMP/MAMP), use as configurações em "AMBIENTE LOCAL".
 * - Para rodar no INFINITYFREE, preencha as credenciais em "AMBIENTE INFINITYFREE"
 *   que aparecem no painel: Client Area > MySQL Databases.
 * - Basta mudar a variável $AMBIENTE abaixo para "local" ou "producao".
 */

// Mude para 'producao' antes de fazer upload pro InfinityFree
$AMBIENTE = 'local';

if ($AMBIENTE === 'local') {
    // ==== AMBIENTE LOCAL (XAMPP) ====
    $db_host = 'localhost';
    $db_name = 'colecionaveis';
    $db_user = 'root';
    $db_pass = '';
    $db_charset = 'utf8mb4';
} else {
    // ==== AMBIENTE INFINITYFREE ====
    // Substitua pelos valores reais do painel do InfinityFree
    $db_host = 'sqlXXX.infinityfree.com';   // ex: sql100.infinityfree.com
    $db_name = 'if0_XXXXXXXX_colecionaveis'; // ex: if0_12345678_colecionaveis
    $db_user = 'if0_XXXXXXXX';               // ex: if0_12345678
    $db_pass = 'SUA_SENHA_AQUI';             // a senha que voce definiu
    $db_charset = 'utf8mb4';
}

// String de conexão (DSN) — não precisa mexer aqui
$dsn = "mysql:host={$db_host};dbname={$db_name};charset={$db_charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $conn = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // Em produção, NÃO mostre detalhes do erro. Em desenvolvimento, mostre.
    if ($AMBIENTE === 'local') {
        die('Erro de conexão com o banco: ' . htmlspecialchars($e->getMessage()));
    } else {
        error_log('Falha na conexão com o banco: ' . $e->getMessage());
        die('Erro interno. Tente novamente mais tarde.');
    }
}

// Iniciar a sessão sempre que esse arquivo for incluído (se ainda não estiver iniciada)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
