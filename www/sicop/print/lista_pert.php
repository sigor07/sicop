<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';

$pag  = link_pag();
$tipo = '';

$imp_incl    = get_session( 'imp_incl', 'int' );
$imp_peculio = get_session( 'imp_peculio', 'int' );
$n_imp_n     = 1;

$motivo_pag = 'IMPRESSÃO DE LISTA DE PERTENCES PENDENTES';

if ( $imp_incl < $n_imp_n and $imp_peculio < $n_imp_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 'f' );

    exit;

}

$v_pec = get_session( 'v_pec' );

if ( isset( $_SESSION['v_pec'] ) ) unset( $_SESSION['v_pec'] );

if ( empty( $v_pec ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 'f' );

    exit;

}

$titulo  = get_session( 'titulo' );
$iduser  = get_session( 'user_id' );
$ip      = $_SERVER['REMOTE_ADDR'];
$maquina = substr( $ip, strrpos( $ip, '.' ) + 1 );

$q_lista = "SELECT
              `peculio`.`idpeculio`,
              `peculio`.`descr_peculio`,
              `peculio`.`confirm`,
              `peculio`.`data_add`,
              DATE_FORMAT( `peculio`.`data_add`,'%d/%m/%Y' ) AS data_add_f,
              `detentos`.`iddetento`,
              `detentos`.`nome_det`,
              `detentos`.`matricula`,
              `detentos`.`pai_det`,
              `detentos`.`mae_det`,
              `tipopeculio`.`tipo_peculio`,
              `sicop_users`.`nome_cham`
            FROM
              `peculio`
              INNER JOIN `detentos` ON `peculio`.`cod_detento` = `detentos`.`iddetento`
              INNER JOIN `tipopeculio` ON `peculio`.`cod_tipo_peculio` = `tipopeculio`.`idtipopeculio`
              LEFT JOIN `sicop_users` ON `peculio`.`user_add` = `sicop_users`.`iduser`
            WHERE
              `peculio`.`idpeculio` IN( $v_pec )
            ORDER BY
              `detentos`.`nome_det`";

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

if ( $cont_l < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrencias.\n\n Página $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 'f' );
    exit;
}

$mensagem = "[ IMPRESSÃO DE LISTA DE PERTENCES PENDENTES ]\n Impressão da lista de pertences pendentes. \n\n Número de itens impressos: $cont_l \n";
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
            <p class="par_corpo">LISTA DE PERTENCES PENDENTES</p>
            <p class="par_corpo">Data: <?php echo date('d/m/Y'); ?></p>
            <p class="par_corpo">&nbsp;</p>
              <?php

                  $i = 0;

                  while ( $d_det = $q_lista->fetch_assoc() ) {

                      ?>

            <table width="650" align="center" class="bordasimples_f" border="1" cellspacing="0">
                <tr>
                    <td width="30" rowspan="2" align="center"><?php echo++$i; ?></td>
                    <td width="312" height="20"><div class="espaco"><b><?php echo SICOP_DET_DESC_FU; ?>:</b> <?php echo $d_det['nome_det']; ?></div></td>
                    <td width="130" ><div class="espaco"><b>Matrícula:</b> <?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] ); ?></div></td>
                    <td width="160" ><div class="espaco"><b>Usuário:</b> <?php echo $d_det['nome_cham'] ?></div></td>
                </tr>
                <tr>
                    <td colspan="3" class="nopading">
                        <div id="tipo_pec"><b>Tipo:</b> <?php echo $d_det['tipo_peculio']; ?></div>
                        <div id="desc_pec"><b>Descrição:</b> <?php echo $d_det['descr_peculio']; ?></div>
                    </td>
                </tr>
            </table>
            <?php if ( $i%16 != 0 and $i != $cont_l  ) { ?>
            <br />
            <?php } ?>

                <?php if ( $i%16 == 0 and $cont_l != 16  ) { ?>
                    <p align="right" class="par_min">Lista de pertences pendentes - Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>
                    <div style="page-break-before: always;">&nbsp;</div>
                <?php } ?>
            <?php } ?>
            <p align="right" class="par_min">Lista de pertences pendentes - Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>
        </div>
    </body>
</html>