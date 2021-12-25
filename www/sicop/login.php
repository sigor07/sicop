<?php
if ( !isset( $_SESSION ) ) session_start();

//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);

$secretaria = '';
$coordenadoria = '';
$unidadecurto = '';
$unidadelongo = '';
$endereco = '';
$endereco_sort = '';
$cidade = '';
$email = '';
$titulo = '';
$datacriacao = '';
$dataatualizacao = '';

require 'init/config.php';
require 'funcoes_init.php';
require 'contadorVisitas.php';
require 'funcoes.php';
require 'layout.php';
require 'cab_simp.php';

$pag  = link_pag();
$motivo_pag = 'LOGIN DE USUÁRIO';

$is_post = is_post();
if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    redir();

    exit;
}

// Verifica se houve POST e se o usuário ou a senha é(são) vazio(s)
if ( empty( $_POST['login'] ) OR empty( $_POST['senha'] ) ) {
    redir();
    exit;
}

$db = SicopModel::getInstance();

$usuario = $db->escape_string( $_POST['login'] );
$senha = $db->escape_string( $_POST['senha'] );

// Validação do usuário/senha digitados
//$sql = "SELECT * FROM sicop_users WHERE usuario = '$usuario' AND senha = SHA1('$senha') AND  ativo = 1 LIMIT 1";

$query = "SELECT
            `iduser`,
            `nomeuser`,
            `nome_cham`,
            `email`,
            `cargo`,
            `cod_setor`,
            `iniciais`,
            `ativo`
          FROM
            `sicop_users`
          WHERE
            `usuario` = '$usuario'
            AND
            `senha` = SHA1('$senha')
          LIMIT 1";

$query = $db->query( $query );

$success = TRUE;

if ( !$query ) {

    $success = FALSE;

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Falha na consulta ( $motivo_pag ). \n\n Usuário digitado: $usuario - Senha Digitada: $senha \n\n $msg_err_mysql";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

}

$db->closeConnection();

if ( $success ) {

    $cont = $query->num_rows;
    if ( $cont != 1 ) {

        $success = FALSE;

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "A consulta retornou 0 ocorrências ( $motivo_pag ). \n\n Usuário digitado: $usuario - Senha Digitada: $senha";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

    }

}

$dados   = '';
$user_id = '';
$d_user  = '';

if ( $success ) {

    $dados = $query->fetch_assoc();

    $user_id = $dados['iduser'];
    $d_user  = dados_user( $user_id );

    if ( $dados['ativo'] != 1 ) {

        $success = FALSE;

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'atn';
        $msg['text'] = "Usuário desativado tentando logar. ( $motivo_pag ). \n\n $d_user";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

    }

}

if ( !$success ) {

    $login_count = get_session( 'login_count', 'int' );

    if ( $login_count >= 5 ) {
        $login_count = 0;
    }

    ++$login_count;

    $_SESSION['login_count'] = $login_count;

    echo $_SESSION['login_count'];

    echo msg_js( 'Login inválido! Verifique!', 1 );
    exit;

}

if ( isset( $_SESSION['login_count'] ) ) {
    unset( $_SESSION['login_count'] );
}

// Se a sessão não existir, inicia uma
if ( !isset( $_SESSION ) ) session_start();

$user_id = $dados['iduser'];

// Salva os dados encontrados na sessão
$_SESSION['user_id']   = $user_id;
$_SESSION['user_nome'] = $dados['nomeuser'];
$_SESSION['nome_cham'] = $dados['nome_cham'];
$_SESSION['iniciais']  = $dados['iniciais'];
$_SESSION['idsetor']   = $dados['cod_setor'];

$cod_setor = $dados['cod_setor'];

$q_setor = "SELECT `sigla_setor` FROM `sicop_setor` WHERE `idsetor` = $cod_setor LIMIT 1";

$sigla_setor = $db->fetchOne( $q_setor );
$db->closeConnection();

$_SESSION['sigla_setor'] = $sigla_setor;

$q_perm = "SELECT
             `sicop_users_perm`.`idpermissao`,
             `sicop_users_perm`.`cod_nivel`,
             `sicop_n_setor`.`n_setor`
           FROM
             `sicop_users_perm`
             INNER JOIN `sicop_n_setor` ON `sicop_users_perm`.`cod_n_setor` = `sicop_n_setor`.`id_n_setor`
           WHERE
             `sicop_users_perm`.`cod_user` = $user_id
           ORDER BY
             `sicop_n_setor`.`n_setor_nome`";

$q_perm = $db->query( $q_perm );

if ( !$q_perm ) {

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Falha na consulta de permissões ( $motivo_pag ).\n\n $msg_err_mysql";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$db->closeConnection();

/*
--------------------------------------------------
SESSIONS RESPONSAVEIS PELAS PERMISSÕES DE ACESSO
--------------------------------------------------
*/
while ( $d_perm = $q_perm->fetch_assoc() ) {

    $n_setor = $d_perm['n_setor'];
    $nivel   = $d_perm['cod_nivel'];

    $_SESSION["$n_setor"] = $nivel;

}

/*
----------------------------------
SESSIONS RESPONSAVEIS PELO LAYOUT
----------------------------------
*/
$_SESSION['secretaria']      = $secretaria;
$_SESSION['coordenadoria']   = $coordenadoria;
$_SESSION['unidadecurto']    = $unidadecurto;
$_SESSION['unidadelongo']    = $unidadelongo;
$_SESSION['endereco']        = $endereco;
$_SESSION['endereco_sort']   = $endereco_sort;
$_SESSION['cidade']          = $cidade;
$_SESSION['email']           = $email;
$_SESSION['titulo']          = $titulo;
$_SESSION['datacriacao']     = $datacriacao;
$_SESSION['dataatualizacao'] = $dataatualizacao;

/*
----------------------------------
SESSIONS RESPONSAVEIS PELOS DIRETORES
----------------------------------
*/
$q_diretores = 'SELECT `diretor_geral`, `diretor_seg`, `diretor_pront`, `diretor_saude`, `diretor_rh`, `diretor_ca` FROM `diretores` WHERE `iddiretores` = 1';
$q_diretores = $db->query( $q_diretores );
$db->closeConnection();

$diretores   = $q_diretores->fetch_assoc();

$_SESSION['diretor_geral'] = $diretores['diretor_geral'];
$_SESSION['diretor_seg']   = $diretores['diretor_seg'];
$_SESSION['diretor_pront'] = $diretores['diretor_pront'];
$_SESSION['diretor_saude'] = $diretores['diretor_saude'];
$_SESSION['diretor_rh']    = $diretores['diretor_rh'];
$_SESSION['diretor_ca']    = $diretores['diretor_ca'];

$mensagem = "[ LOGIN EFETUADO ] \n Login efetuado com sucesso. \n\n $d_user";
salvaLog( $mensagem );

// query para registro e contagem de logins do usuário
$querynumlog = "UPDATE `sicop_users` SET `numlogins` = `numlogins` + 1, `prelastlogin` = `datalastlogin`, `datalastlogin` = NOW() WHERE `iduser` = $user_id LIMIT 1";

// querys de manutenção
$q_limpa_loggeral = 'DELETE FROM `logs` WHERE DATEDIFF(CURDATE(),`hora`) > 360';
$q_limpa_logalt   = 'DELETE FROM `log_alt` WHERE DATEDIFF(CURDATE(),`data`) > 360';

$db->query( $querynumlog );
$db->query( $q_limpa_loggeral );
$db->query( $q_limpa_logalt );
$db->closeConnection();

//depur( $_SESSION );

// Redireciona o visitante
redir( 'home' );
exit;

?>
</body>
</html>