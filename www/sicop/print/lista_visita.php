<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';

$pag  = link_pag();
$tipo = '';

$imp_chefia   = get_session( 'imp_chefia', 'int' );
$imp_cadastro = get_session( 'imp_cadastro', 'int' );
$iduser       = get_session( 'user_id', 'int' );
$n_prin_n     = 1;

$motivo_pag = 'IMPRESSÃO DE LISTA DE ' . SICOP_DET_DESC_U . 'S PARA VISITA';

if ( $imp_chefia < $n_prin_n and $imp_cadastro < $n_prin_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 'f' );

    exit;

}

$titulo  = $_SESSION['titulo'];
$ip      = $_SERVER['REMOTE_ADDR'];
$maquina = substr( $ip, strrpos( $ip, '.' ) + 1 );

$idraio = empty( $_GET['n_raio'] ) ? '' : (int)$_GET['n_raio'];
$op     = !empty($_GET['op']) ? tratabusca($_GET['op']) : '';

if ( empty( $idraio ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );

    exit;

}

    $ordpor = 'nome';

    if ( !empty( $op ) ) {
        $ordpor = $op;
    }

    switch($ordpor) {
        default:
        case 'nome':
            $ordbusca = '`detentos`.`nome_det`';
            break;
        case 'matr':
            $ordbusca = '`detentos`.`matricula`';
            break;
        case 'cela':
            $ordbusca = "`detentos`.`cod_cela`, `detentos`.`nome_det`";
            break;
    }


$q_lista = "SELECT
              `detentos`.`nome_det`,
              `detentos`.`matricula`,
              `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
              `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
              `unidades_in`.`unidades` AS procedencia,
              `unidades_out`.`idunidades` AS iddestino,
              `raio`.`raio`,
              `cela`.`cela`
            FROM
              `detentos`
              LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
              LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
              LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
              LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
              INNER JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
              INNER JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
            WHERE
              ( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 OR `mov_det_out`.`cod_tipo_mov` = 6 )
              AND
              ( `mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 2 OR `mov_det_in`.`cod_tipo_mov` = 3 )
              AND
              `raio`.`idraio` = $idraio
            ORDER BY
              $ordbusca";

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
    $mensagem = "A consulta retornou 0 ocorrencias.\n\n Página $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 'f' );
    exit;
}

$mensagem = "[ IMPRESSÃO DE LISTA PARA VISITA ] \n\n " . SICOP_RAIO . ": $idraio.";
salvaLog($mensagem);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="pt-br" xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" Accept-Language="pt-br"/>
        <link rel="shortcut icon" href="<?php echo SICOP_SYS_IMG_PATH; ?>favicon.ico" type="image/x-icon" />
        <title><?php echo $titulo ?></title>
        <link href="<?php echo SICOP_ABS_PATH ?>css/reset.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_p_lista.css" rel="stylesheet" type="text/css" />
    </head>

    <body onload="Javascript:window.print();self.window.close()">
    <!-- onload="Javascript:window.print();self.window.close()" -->

    <div class="corpo">

        <p class="par_corpo">LISTA DE <?php echo SICOP_DET_DESC_U; ?>S</p>


        <table width="650" align="center" class="bordasimples_f" border="1" cellpadding="1" cellspacing="0">
            <thead>
              <tr>
                <th scope="col" width="40" height="15" align="center">N</th>
                <th scope="col" width="296" align="center"><?php echo SICOP_DET_DESC_FU; ?></th>
                <th scope="col" width="80" align="center">Matrícula</th>
                <th scope="col" width="40" align="center"><?php echo SICOP_RAIO ?></th>
                <th scope="col" width="40" align="center"><?php echo SICOP_CELA ?></th>
                <th scope="col" width="40" align="center">H</th>
                <th scope="col" width="40" align="center">M</th>
                <th scope="col" width="40" align="center">C</th>
              </tr>
            </thead>

                <?php

                    $i = 0;

                    while($d_det = $q_lista->fetch_assoc()) {

                        ?>
          <tbody>
              <tr>
                <td height="18" align="center"><?php echo ++$i ?></td>
                <td align="left" class="espaco_td"><?php echo $d_det['nome_det']?></td>
                <td align="center"><?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] );?></td>
                <td align="center"><?php echo $d_det['raio']?></td>
                <td align="center"><?php echo $d_det['cela']?></td>
                <td align="left">&nbsp;</td>
                <td align="right">&nbsp;</td>
                <td align="right">&nbsp;</td>
              </tr>
            </tbody>
            <?php if ( $i%45 == 0 and $cont_l != 45  ) { ?>
            </table>
            <p align="right" class="par_min">Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>
            <div style="page-break-before: always;">&nbsp;</div>
            <table width="650" align="center" class="bordasimples_f" border="1" cellpadding="1" cellspacing="0"  >
            <thead>
              <tr>
                <th scope="col" width="40" height="15" align="center">N</th>
                <th scope="col" width="296" align="center"><?php echo SICOP_DET_DESC_FU; ?></th>
                <th scope="col" width="80" align="center">Matrícula</th>
                <th scope="col" width="40" align="center"><?php echo SICOP_RAIO ?></th>
                <th scope="col" width="40" align="center"><?php echo SICOP_CELA ?></th>
                <th scope="col" width="40" align="center">H</th>
                <th scope="col" width="40" align="center">M</th>
                <th scope="col" width="40" align="center">C</th>
              </tr>
            </thead>
            <?php
                    }
                }
                ?>
        </table>
<?php if ( $i > 45  ) { ?>
         <p align="right" class="par_min">Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>
<?php } ?>
      </div>
    </body>
</html>