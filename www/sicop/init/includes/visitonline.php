<?php
/**
* Sistema de contador de visitantes online
*
* @author Thiago Belem <contato@thiagobelem.net>
* @link http://thiagobelem.net/
*
* @version 1.0
* @package VisitantesOnline
*/

//     Configurações do Script
// ==============================
$_VO = '';
$_VO['registraAuto'] = true; // Registra os visitantes automaticamente?
$_VO['conectaMySQL'] = FALSE; // Abre uma conexão com o servidor MySQL?

$_VO['tempo_site'] = 10; // Quantos minutos a visita dura --> alterado de 20min para 10min

$_VO['servidor'] = 'localhost'; // Servidor MySQL
$_VO['usuario'] = 'cdrio'; // Usuário MySQL
$_VO['senha'] = 'poderozo'; // Senha MySQL
$_VO['banco'] = 'bd'; // Banco de dados MySQL

$_VO['tabela_v'] = 'visitas_online'; // Tabela onde os visitantes online serão salvos
$_VO['tabela_r'] = 'visitas_record'; // Tabela onde os recordes de visitas serão salvos
// ==============================

// ======================================
//  ~ Não edite a partir deste ponto ~
// ======================================

// Verifica se precisa fazer a conexão com o MySQL
if ($_VO['conectaMySQL'] == true) {
    $_VO['link'] = mysql_connect($_VO['servidor'], $_VO['usuario'], $_VO['senha']) or die("MySQL: Não foi possível conectar-se ao servidor [".$_VO['servidor']."].");
    mysql_select_db($_VO['banco'], $_VO['link']) or die("MySQL: Não foi possível conectar-se ao banco de dados [".$_VO['banco']."].");
}

/**
* Gera o identificador do visitante baseado no IP e na hora
*/
function geraIdentificador() {
    global $_VO;

    $user = get_session( 'user_id', 'int' );

    return sha1( $_SERVER['REMOTE_ADDR'] . ' - ' . $user . ' - ' . microtime() );

}

/**
* Registra uma visita e/ou pageview para o visitante
*&nbsp; Esta funçaõ será chamada automaticamente dependendo de $_VO['registraAuto']
*/
setlocale( LC_ALL, 'pt_BR', 'ptb', 'pt-BR', 'pt-br', 'PT-BR', 'pt_BR.utf-8', 'portuguese-brazil', 'bra' );
date_default_timezone_set( 'America/Sao_Paulo' );

function registraVisitON() {
    global $_VO;

    $identificador = '';
    $novo          = '';
    $resultado     = '';
    $atualizado    = '';

    // Verifica se os headers já foram enviados. Caso tenham, é gerada uma mensagem de erro
    if ( headers_sent() ) {
        trigger_error( '[VisitantesOnline] Por favor, insira o arquivo antes de qualquer HTML', E_USER_ERROR );
        return;
    }

    // Verifica se é um visitante que já está no site
/*    $last_time = !empty( $_SESSION['VO_last_time'] ) ? $_SESSION['VO_last_time'] : 0;
    $end_time = strtotime( 'now - ' . $_VO['tempo_site'] . ' MINUTES');
    if ( $last_time >= $end_time ) {
        $novo = false;
        $identificador = $_SESSION['VO_id'];
    } else {
        $novo = true;
        $identificador = geraIdentificador();
    }*/

    //
    //`hora` <= ( NOW() - INTERVAL ".$_VO['tempo_site']." MINUTE))
    $last_time = get_session( 'VO_last_time', 'int' );
    $end_time = strtotime( date( 'Y-m-d H:i:s', $last_time ) . ' + ' . 10 . ' MINUTES' );
    $act_time = strtotime( 'now' );
    if ( $act_time <= $end_time ) {
        $novo = false;
        $identificador = $_SESSION['VO_id'];
    } else {
        $novo = true;
        $identificador = geraIdentificador();
    }


    //capturar o endereço que esta no navegador
    $url = $_SERVER['SERVER_ADDR'] . $_SERVER['PHP_SELF'];
    $qs = $_SERVER['QUERY_STRING'];

    if ( !empty( $qs ) ) $url .=  '?' . $qs;

    $pag_atual = '<a href="http://' . $url . '">' . $url . '</a>';

    $db = SicopModel::getInstance();

    // Se o visitante não é novo, tenta atualizar o registro dele na tabela
    if ( !$novo ) {
        $query = 'UPDATE `' . $_VO['tabela_v'] . "` SET `url` = '$pag_atual', `hora` = NOW() WHERE `identificador` = '$identificador' LIMIT 1";
        $resultado  = $db->query( $query );
        $atualizado = (bool)$db->affected_rows();
    }

    // Deleta todos os visitantes com mais de 10min no site, exceto o atual
    $query = 'DELETE FROM `' . $_VO['tabela_v'] . '` WHERE ( `hora` <= ( NOW() - INTERVAL ' . $_VO['tempo_site'] . " MINUTE ) ) AND `identificador` != '$identificador'";
    $db->query( $query );

    // Se o visitante é novo OU se o registro dele ele não foi atualizado, insere um novo registro na tabela
    if ( $novo OR !$atualizado ) {

        $userid = get_session( 'user_id', 'int' );

        if ( !empty ( $userid ) ) {

            $query = 'REPLACE INTO `' . $_VO['tabela_v'] . "` VALUES ( NULL, '" . $_SERVER['REMOTE_ADDR'] . "', '$identificador', $userid, '$pag_atual', NOW() )";
            $db->query( $query );

        }

    }

    // Verifica se é preciso atualizar o recorde de visitas
    $recorde = visitantesRecorde(); // Pega o recorde de visitantes
    $online  = visitantesOnline();  // Pega o n° de visitantes atual
    if ( $recorde[1] < $online ) {
        $query = 'REPLACE INTO `' . $_VO['tabela_r'] . '` SET `data` = NOW(), `visitantes` = ' . $online;
        $db->query( $query );
    }

    $db->closeConnection();

    // Atualiza o cookie com o identificador do visitante
    $_SESSION['VO_last_time'] = strtotime( 'now' );
    $_SESSION['VO_id'] = $identificador;

    return;

}

/**
* Função que retorna o total de visitantes online
*/
function visitantesOnline() {
    global $_VO;

    // Faz a consulta no MySQL em função dos argumentos
    $sql       = 'SELECT COUNT(*) FROM `' . $_VO['tabela_v'] . '`';
    $db        = SicopModel::getInstance();
    $resultado = $db->fetchOne( $sql );
    $db->closeConnection();

    // Retorna o valor encontrado ou zero
    return !empty( $resultado ) ? (int)$resultado : 0;

}

/**
* Função que retorna a data e o recorde de visitantes online
*/
function visitantesRecorde( $formato = 'd/m/Y' ) {
    global $_VO;

    // Faz a consulta no MySQL em função dos argumentos
    $query = 'SELECT `data`, `visitantes` FROM `' . $_VO['tabela_r'] . '` ORDER BY `visitantes` DESC LIMIT 1';
    $db    = SicopModel::getInstance();
    $query = $db->query( $query );

    $dados = '';
    $dados = $query->fetch_object();

    // Retorna o valor encontrado ou zero
    return !empty( $dados ) ? array( date( $formato, strtotime( $dados->data ) ), (int)$dados->visitantes ) : array( date( $formato ), 0 );
    exit;
}

if ( $_VO['registraAuto'] == true ) {
    registraVisitON();
}
?>
