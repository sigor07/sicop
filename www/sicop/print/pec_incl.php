<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';

$pag  = link_pag();
$tipo = '';

$imp_peculio = get_session( 'imp_peculio', 'int' );
$imp_incl    = get_session( 'imp_incl', 'int' );
$n_imp_n     = 1;

$titulo        = get_session( 'titulo' );
$secretaria    = get_session( 'secretaria' );
$coordenadoria = get_session( 'coordenadoria' );
$unidadecurto  = get_session( 'unidadecurto' );
$endereco      = get_session( 'endereco' );
$cidade        = get_session( 'cidade' );
$iduser        = get_session( 'user_id' );
$ip            = $_SERVER['REMOTE_ADDR'];
$maquina       = substr( $ip, strrpos( $ip, '.' ) + 1 );

$motivo_pag = 'IMPRESSÃO DE PECÚLIO DA INCLUSÃO';

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

$idpec_g = get_get( 'idpec', 'int' );

$idpec_s = get_session( 'imp_pec' );

$idpec = empty( $idpec_g ) ? $idpec_s : $idpec_g;

if ( isset( $_SESSION['imp_pec'] ) ) unset( $_SESSION['imp_pec'] );

if ( empty( $idpec ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );

    exit;

}

$querydet = "SELECT
                `detentos`.`iddetento`,
                `detentos`.`nome_det`,
                `detentos`.`matricula`,
                `detentos`.`rg_civil`,
                `detentos`.`cod_cela`,
                `mov_det_in`.`data_mov` AS data_incl,
                DATE_FORMAT(`mov_det_in`.`data_mov`, '%d/%m/%Y') AS data_incl_f,
                `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
                `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
                `unidades_in`.`unidades` AS procedencia,
                `unidades_out`.`idunidades` AS iddestino,
                `cela`.`cela`,
                `raio`.`raio`
              FROM
                `detentos`
                LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
                LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
              WHERE
                `detentos`.`iddetento` = ( SELECT `cod_detento` FROM `peculio` WHERE `idpeculio` IN ( $idpec ) LIMIT 1 )
              LIMIT 1";

$q_pec = "SELECT
            `peculio`.`idpeculio`,
            `peculio`.`descr_peculio`,
            DATE_FORMAT( `peculio`.`data_add`, '%d/%m/%Y' ) AS data_add_f,
            `tipopeculio`.`tipo_peculio`
          FROM
            `peculio`
            INNER JOIN `tipopeculio` ON `peculio`.`cod_tipo_peculio` = `tipopeculio`.`idtipopeculio`
          WHERE
            `peculio`.`idpeculio` IN ( $idpec )
            AND
            `peculio`.`retirado` = FALSE
          ORDER BY
            `peculio`.`data_add`, `tipopeculio`.`tipo_peculio`";



// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$querydet = $model->query( $querydet );

// fechando a conexao
$model->closeConnection();

if( !$querydet ) {

    echo msg_js( 'FALHA!!!', 'f' );
    exit;

}

$contd = $querydet->num_rows;

if($contd < 1) {
    $mensagem = "A consulta retornou 0 ocorrencias.\n\n Página $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 'f' );
    exit;
}

$d_det = $querydet->fetch_assoc();

$tipo_mov_in  = $d_det['tipo_mov_in'];
$procedencia  = $d_det['procedencia'];
$data_incl    = $d_det['data_incl'];
$tipo_mov_out = $d_det['tipo_mov_out'];
$iddestino    = $d_det['iddestino'];

$det = manipula_sit_det_l( $tipo_mov_in, $procedencia, $data_incl, $tipo_mov_out, $iddestino );

$detento = $d_det['nome_det'];

$matricula = !empty( $d_det['matricula'] ) ? formata_num( $d_det['matricula'] ) : '';
$cela      = empty($d_det['cela']) ? '' : SICOP_CELA . ': '.$d_det['cela'];
$raio      = empty($d_det['raio']) ? '' : SICOP_RAIO . ': '.$d_det['raio'];


// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_pec = $model->query( $q_pec );

// fechando a conexao
$model->closeConnection();

if( !$q_pec ) {

    echo msg_js( 'FALHA!!!', 'f' );
    exit;

}

$cont_pec = $q_pec->num_rows;

if($cont_pec < 1) {
    $mensagem = "A consulta retornou 0 ocorrencias.\n\n Página $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 'f' );
    exit;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="pt-br" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="<?php echo SICOP_SYS_IMG_PATH; ?>favicon.ico" type="image/x-icon" />
        <title><?php echo $titulo ?></title>
        <link href="<?php echo SICOP_ABS_PATH ?>css/reset.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_cabv.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_p_pec.css" rel="stylesheet" type="text/css" />
    </head>

    <body onload="Javascript:window.print();self.window.close()">
    <!-- onload="Javascript:window.print();self.window.close()" -->

<style type="text/css" media="print">

._Header {
    position:fixed;
    top: 0;
    float: left;
    clear: none;
}

._Footer {
    position:fixed;
    bottom: 0;
    float: left;
    clear: none;
}

</style>


        <?php require 'cabecalho_v.php';?>


        <div class="corpo">
            <p align="center" class="par_forte_n">RELAÇÃO DE PERTENCES E PECÚLIO</p>
            <p class="par_corpo_medio">&nbsp;</p>
            <table width="645" align="center" cellspacing="0" class="det_pec" >
                <tr bgcolor="#FAFAFA">
                    <td width="312" height="15" ><b>Nome:</b> <?php echo $detento; ?></td>
                    <td width="157" height="15" ><b>Matrícula:</b> <?php echo $matricula; ?></td>
                    <td width="168" height="15" ><b>R.G.:</b> <?php echo!empty( $d_det['rg_civil'] ) ? formata_num( $d_det['rg_civil'] ) : '' ?></td>
                </tr>
                <tr bgcolor="#FAFAFA">
                    <td height="15" ><b>Situação atual:</b> <b><font color= "<?php echo $det['corfontd']; ?>" > <?php echo $det['sitat'] ?></font></b></td>
                    <td height="15" ><b><?php echo $raio; ?></b></td>
                    <td height="15" ><b><?php echo $cela; ?></b></td>
                </tr>
                <tr bgcolor="#FAFAFA">
                    <td height="15" ><b>Procedência:</b> <?php echo $det['procedencia'] ?></td>
                    <td height="15" colspan="2" ><b>Data da inclusão:</b> <?php echo $det['data_incl'] ?></td>
                </tr>
            </table>

            <p class="par_corpo_medio">&nbsp;</p>
            <p class="par_corpo_medio">&nbsp;</p>

            <table width="645" align="center" border="1" cellspacing="0" class="pec_r" >
                <tr >
                    <td width="82" align="center" height="20"><b>DATA</b></td>
                    <td height="20" align="center"><b>TIPO</b></td>
                    <td width="453" align="center" ><b>DESCRIÇÃO</b></td>
                </tr>
                <?php while ( $d_pec = $q_pec->fetch_assoc() ) { ?>
                <tr bgcolor="#FAFAFA" class="even">
                    <td align="center" height="20"><?php echo $d_pec['data_add_f'] ?></td>
                    <td width="102" height="20"><?php echo $d_pec['tipo_peculio'] ?></td>
                    <td><?php echo nl2br( $d_pec['descr_peculio'] ) ?></td>
                </tr>
                <?php } // fim do while ?>
            </table>

            <p class="par_corpo_medio">&nbsp;</p>

            <p class="par_data"><?php echo $cidade; ?>, ______/______/____________ <?php //echo date( 'd/m/Y' );?></p>

            <p class="par_corpo_medio">&nbsp;</p>

            <div class="ass_func">
                <p class="par_ass">_____________________________________</p>
                <p class="par_ass">Servidor Responsável</p>
            </div>


            <div class="ass_det">
                <p class="par_ass">_____________________________________</p>
                <p class="par_ass"><?php echo SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_L  ?> (nome legível)</p>
            </div>

            <p class="par_corpo_medio">&nbsp;</p>
            <p class="par_corpo_medio">&nbsp;</p>

            <p class="par_data_e">Entrada no setor de pecúlio em ______/______/____________ &nbsp;&nbsp;&nbsp; Conferido por __________________ </p>

            <p class="par_corpo_medio">&nbsp;</p>
            <span class="_Footer">
              <div class="rodape">
                  <p align="right" class="par_min">Usuário: <?php echo $iduser ?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' ) ?></p>
                  <hr align="center" width="645" size="0" noshade="noshade" color="#000000" />
                  <p align="center"><?php echo $endereco ?></p>
              </div>
            </span>
        </div>
    </body>
</html>