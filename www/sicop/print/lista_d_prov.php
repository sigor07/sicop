<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';

$pag  = link_pag();
$tipo = '';

$imp_cadastro = get_session( 'imp_cadastro', 'int' );
$n_imp_n      = 1;

$motivo_pag = 'IMPRESSÃO DE LISTA DE ' . SICOP_DET_DESC_U . 'S COM DADOS PROVISÓRIOS';

if ( $imp_cadastro < $n_imp_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 'f' );

    exit;

}

$titulo  = get_session( 'titulo' );
$iduser  = get_session( 'user_id' );
$ip      = $_SERVER['REMOTE_ADDR'];
$maquina = substr( $ip, strrpos( $ip, '.' ) + 1 );

$q_lista = "SELECT
                `detentos`.`nome_det`,
                `detentos`.`matricula`,
                `detentos`.`rg_civil`,
                DATE_FORMAT(`detentos`.`nasc_det`, '%d/%m/%Y') AS nasc_det,
                `detentos`.`pai_det`,
                `detentos`.`mae_det`
              FROM
                `detentos`
              WHERE
                `detentos`.`dados_prov` = TRUE
              ORDER BY
                `detentos`.`nome_det`";

//echo nl2br($q_lista);
//exit;

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_lista = $model->query( $q_lista );

// fechando a conexao
$model->closeConnection();

if( !$q_lista ) {

    echo msg_js( 'FALHA!!!', 'f' );
    exit;

}

$cont_l = $q_lista->num_rows;

if( $cont_l < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrências ( IMPRESSÃO DA LISTA DE DETENTOS COM DADOS PROVISÓRIOS ).\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 'f' );
    exit;
}

$mensagem = "[ IMPRESSÃO DE LISTA DE DADOS PROVISÓRIOS ]\n Impressão da lista de " . SICOP_DET_DESC_L . "s com dados provisórios. \n\n Número de " . SICOP_DET_DESC_L . "s impressos: $cont_l \n";
salvaLog($mensagem);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="pt-br" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="<?php echo SICOP_SYS_IMG_PATH; ?>favicon.ico" type="image/x-icon" />
        <title><?php echo $titulo ?></title>
        <link href="<?php echo SICOP_ABS_PATH ?>css/reset.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_p_lista.css" rel="stylesheet" type="text/css" />
    </head>

    <body onload="Javascript:window.print();self.window.close()">
    <!-- onload="Javascript:window.print();self.window.close()" -->

    <div class="corpo">
    <p class="par_corpo">LISTA DE <?php echo SICOP_DET_DESC_U; ?>S COM DADOS PROVISÓRIOS</p>
    <p class="par_corpo">Data: <?php echo date('d/m/Y'); ?></p>
    <br />
      <?php

          $i = 0;

          while ( $d_det = $q_lista->fetch_assoc() ) {

              ?>

        <table width="650" align="center" class="bordasimples_f" border="1" cellspacing="0">
            <tr>
                <td width="30" rowspan="4" align="center"><?php echo++$i; ?></td>
                <td width="312" height="15"><div class="espaco"><b><?php echo SICOP_DET_DESC_FU; ?>:</b> <?php echo $d_det['nome_det']; ?></div></td>
                <td width="130"><div class="espaco"><b>Matrícula:</b> <?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] ); ?></div></td>
                <td width="160"><div class="espaco"><b>Nascimento:</b> <?php echo $d_det['nasc_det'] ?></div></td>
            </tr>
            <tr>
                <td colspan="3" height="15"><div class="espaco"><b>Pai:</b> <?php echo $d_det['pai_det']; ?></div></td>
            </tr>
            <tr>
                <td colspan="3" height="15"><div class="espaco"><b>Mãe:</b> <?php echo $d_det['mae_det']; ?></div></td>
            </tr>
            <tr>
                <td colspan="3" height="20"><div class="espaco"><b>R.G:</b> <?php if ( !empty( $d_det['rg_civil'] ) ) echo formata_num( $d_det['rg_civil'] ); ?></div></td>
            </tr>
        </table>
        <?php if ( $i%10 != 0 and $i != $cont_l ) { ?>
        <hr size="1" noshade="noshade" color="#000000" />
        <?php } ?>

            <?php if ( $i%10 == 0 and $cont_l != 10 ) { ?>
        <p align="right" class="par_min">Lista de <?php echo SICOP_DET_DESC_L; ?>s com dados provisóriso - Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>
        <div style="page-break-before: always;">&nbsp;</div>
            <?php } ?>
        <?php } ?>

        <p align="right" class="par_min">Lista de <?php echo SICOP_DET_DESC_L; ?>s com dados provisóriso - Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>


    </div>
    </body>
</html>