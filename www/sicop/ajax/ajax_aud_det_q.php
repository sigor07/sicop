<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$tipo_pag = 'AUDIÊNCIAS DE DETENTO - AJAX';
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

$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_cad_n    = 2;

if ( $n_cadastro < $n_cad_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $tipo_pag;
    get_msg( $msg, 1 );

    echo $msg_falha;

    exit;

}

$iddet = get_post( 'iddet', 'int' );
if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página. Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. ( $tipo_pag )";
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$q_aud = "SELECT
            `idaudiencia`,
            `cod_detento`,
            `data_aud`,
            `hora_aud`,
            `local_aud`,
            `cidade_aud`,
            `tipo_aud`,
            `num_processo`,
            `sit_aud`,
            DATE_FORMAT(`data_aud`, '%d/%m/%Y') AS `data_aud_f`,
            DATE_FORMAT(`hora_aud`, '%H:%i') AS `hora_aud_f`
          FROM
            `audiencias`
          WHERE
            `cod_detento` = $iddet
          ORDER BY
            `data_aud` DESC, `hora_aud`";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_aud = $model->query( $q_aud );

// fechando a conexao
$model->closeConnection();

if ( !$q_aud ) {

    echo $msg_falha;
    exit;

}

$cont = $q_aud->num_rows;
if ( $cont < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "A consulta retornou 0 ocorrências ( $tipo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

header( "Content-Type: text/html; charset=utf-8" );

//Evitando cache de arquivo
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0' );
header( 'Pragma: no-cache' );
header( 'Expires: 0' );

?>

    <table class="lista_busca">
        <tr>
            <th class="local_aud_hist">LOCAL DE APRESENTAÇÃO</th>
            <th class="cidade_aud_hist">CIDADE</th>
            <th class="data_hora_aud">DATA / HORA</th>
            <th class="n_process">Nº DO PROCESSO</th>
        </tr>
        <?php
        while ( $dadosa = $q_aud->fetch_assoc() ) {

            $aud = trata_sit_aud( $dadosa['sit_aud'] );

            ?>
        <tr class="even" title="Situação da audiência: <?php echo $aud['sitaud']; ?>">
            <td class="local_aud_hist"><a href="<?php echo SICOP_ABS_PATH ?>cadastro/detalaud.php?idaud=<?php echo $dadosa['idaudiencia'] ?>" ><?php echo $dadosa['local_aud'] ?></a></td>
            <td class="cidade_aud_hist <?php echo $aud['css_class']; ?>"><?php echo $dadosa['cidade_aud'] ?></td>
            <td class="data_hora_aud <?php echo $aud['css_class']; ?>"><?php echo $dadosa['data_aud_f'] . ' às ' . $dadosa['hora_aud_f']?></td>
            <td class="n_process <?php echo $aud['css_class']; ?>"><?php echo $dadosa['num_processo'] ?></td>
        </tr>
        <?php } // fim do while ?>
    </table>


