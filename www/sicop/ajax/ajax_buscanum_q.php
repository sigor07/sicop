<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$tipo_pag  = 'BUSCA DE NÚMEROS - AJAX';
$msg_falha = '<p class="q_error">FALHA!</p>';

$is_post = is_post();
if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $tipo_pag ).";
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$tipo = get_post( 'tipo', 'int' );
if ( empty( $tipo ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Identificador do tipo em branco. ( $tipo_pag )";
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$user = get_post( 'user', 'int' );
$num  = get_post( 'num', 'int' );
$ano  = get_post( 'ano', 'int' );


$_SESSION['buscanum_exec'] = 1;
$_SESSION['buscanum_user'] = $user;
$_SESSION['buscanum_num']  = $num;
$_SESSION['buscanum_ano']  = $ano;
$_SESSION['buscanum_tipo'] = $tipo;

$tabela    = '';
$campo_id  = '';
$campo_num = '';

switch ( $tipo ) {
    default:
    case 1:
        $tabela    = '`numeroof`';
        $campo_id  = '`idnumof`';
        $campo_num = '`numero_of`';
        break;
    case 2:
        $tabela    = '`numerofax`';
        $campo_id  = '`idnumfax`';
        $campo_num = '`numero_fax`';
        break;
    case 3:
        $tabela    = '`numerorms`';
        $campo_id  = '`idnumrms`';
        $campo_num = '`numero_rms`';
        break;
    case 4:
        $tabela    = '`numeronotes`';
        $campo_id  = '`idnumnotes`';
        $campo_num = '`numero_notes`';
        break;
    case 5:
        $tabela    = '`numeroreq`';
        $campo_id  = '`idnumreq`';
        $campo_num = '`numero_req`';
        break;

}

$where = '';

if ( !empty( $user ) ) {
    if ( !empty( $where ) ) {
        $where .= " AND ( $tabela.`iduser` = $user )";
    } else {
        $where = "WHERE ( $tabela.`iduser` = $user )";
    }
}

if ( !empty( $num ) ) {
    if ( !empty( $where ) ) {
        $where .= " AND ( $tabela.$campo_num = $num )";
    } else {
        $where = "WHERE ( $tabela.$campo_num = $num )";
    }
}

if ( !empty( $ano ) ) {
    if ( !empty( $where ) ) {
        $where .= " AND ( $tabela.`ano` = $ano )";
    } else {
        $where = "WHERE ( $tabela.`ano` = $ano )";
    }
}

$q_num = "SELECT
            $tabela.$campo_id AS `uid`,
            $tabela.$campo_num AS `num`,
            $tabela.`ano`,
            `sicop_users`.`nome_cham`,
            `sicop_setor`.`sigla_setor`,
            $tabela.`coment`,
            DATE_FORMAT( $tabela.`dataadd`, '%d/%m/%Y às %H:%i' ) AS `dataadd`
          FROM
            $tabela
            LEFT JOIN `sicop_setor` ON $tabela.`idsetor` = `sicop_setor`.`idsetor`
            LEFT JOIN `sicop_users` ON $tabela.`iduser` = `sicop_users`.`iduser`
          $where
          ORDER BY
            $tabela.`ano` DESC, $tabela.$campo_num DESC";

//depur($_POST);
//echo '<p class="p_q_no_result">' . $q_num . '</p>';
//exit;

$db = SicopModel::getInstance();

$q_num = $db->query( $q_num );

$querytime = $db->getQueryTime();

if ( !$q_num ) {

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();
    $db->closeConnection();

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Falha na consulta ( $tipo_pag ).\n\n $msg_err_mysql";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$db->closeConnection();

$cont = $q_num->num_rows;

header( "Content-Type: text/html; charset=utf-8" );

//Evitando cache de arquivo
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0' );
header( 'Pragma: no-cache' );
header( 'Expires: 0' );

if ( $cont < 1 ) {
    echo '<p class="p_q_no_result">Não foi encontrada nenhuma ocorrência.</p>';
    exit;
}

?>


            <p class="p_q_info">Essa consulta retornou <?php echo $cont ?> registros (<?php echo round($querytime, 2) ?> seg).</p>

            <table class="lista_busca">
                <tr>
                    <th class="n_ano">Número / ano</th>
                    <th class="user_log">Usuário</th>
                    <th class="setor_num">Setor</th>
                    <th class="desc_num">Descrição</th>
                    <th class="desc_data_long">Data / hora</th>
                </tr>
                <?php while ( $d_num = $q_num->fetch_assoc() ) { ?>
                <tr class="even">
                    <td class="n_ano"><?php echo $d_num['num'] . '/' . $d_num['ano']; ?></td>
                    <td class="user_log"><?php echo $d_num['nome_cham']; ?></td>
                    <td class="setor_num"><?php echo $d_num['sigla_setor']; ?></td>
                    <td class="desc_num"><?php echo nl2br( $d_num['coment'] ); ?></td>
                    <td class="desc_data_long"><?php echo $d_num['dataadd']; ?></td>
                </tr>
                <?php } ?>
            </table>
