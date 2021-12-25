<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';

$pag  = link_pag();
$tipo = '';

$imp_det = get_session( 'imp_det', 'int' );
$n_imp_n = 1;

$titulo        = $_SESSION['titulo'];
$secretaria    = $_SESSION['secretaria'];
$coordenadoria = $_SESSION['coordenadoria'];
$unidadecurto  = $_SESSION['unidadecurto'];
$endereco      = $_SESSION['endereco'];
$iduser        = get_session( 'user_id', 'int' );
$ip            = $_SERVER['REMOTE_ADDR'];
$maquina       = substr( $ip, strrpos( $ip, '.' ) + 1 );

$motivo_pag = 'IMPRESSÃO DE FOTO DE ' . SICOP_DET_DESC_U;

if ( $imp_det < $n_imp_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 'f' );

    exit;

}

$iddet_g = empty( $_GET['iddet'] ) ? '' : (int)$_GET['iddet'];

$iddet_s = empty( $_SESSION['iddet'] ) ? '' : $_SESSION['iddet'];

if ( isset( $_SESSION['iddet'] ) ) unset( $_SESSION['iddet'] );

$iddet = empty( $iddet_g ) ? $iddet_s : $iddet_g;

if ( empty( $iddet ) ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );

    exit;

}


$query_det = "SELECT
                `detentos`.`iddetento`,
                `detentos`.`nome_det`,
                `detentos`.`matricula`,
                `detentos`.`rg_civil`,
                `detentos`.`vulgo`,
                `detentos`.`pai_det`,
                `detentos`.`mae_det`,
                DATE_FORMAT(`detentos`.`nasc_det`, '%d/%m/%Y') AS nasc_det_f,
                FLOOR(DATEDIFF(CURDATE(), `detentos`.`nasc_det`) / 365.25) AS idade_det,
                `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
                `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
                `unidades_out`.`idunidades` AS iddestino,
                `tipoartigo`.`artigo`,
                `cidades`.`nome` AS cidade,
                `estados`.`sigla` AS estado,
                `det_fotos`.`foto_det_g`,
                `det_fotos`.`foto_det_p`
              FROM
                detentos
                LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                LEFT JOIN `tipoartigo` ON `detentos`.`cod_artigo` = `tipoartigo`.`idartigo`
                LEFT JOIN `cidades` ON `detentos`.`cod_cidade` = `cidades`.`idcidade`
                LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado`
                LEFT JOIN `det_fotos` ON `det_fotos`.`id_foto` = `detentos`.`cod_foto`
              WHERE
                `detentos`.`iddetento` IN( $iddet )
              ORDER BY
                `detentos`.`nome_det`";
            /*,21,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47 $iddet */

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_det = $model->query( $query_det );

// fechando a conexao
$model->closeConnection();

if( !$query_det ) {

    echo msg_js( 'FALHA!!!', 'f' );
    exit;

}

$contd = $query_det->num_rows;

if( $contd < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrencias ( $motivo_pag ).\n\n Página $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 'f' );
    exit;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="pt-br" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" Accept-Language="pt-br"/>
        <link rel="shortcut icon" href="<?php echo SICOP_SYS_IMG_PATH; ?>favicon.ico" type="image/x-icon" />
        <title><?php echo $titulo ?></title>
        <link href="<?php echo SICOP_ABS_PATH ?>css/reset.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_cabv.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_p_quali.css" rel="stylesheet" type="text/css" />
    </head>
        <body onload="Javascript:window.print();self.window.close()">
        <!-- onload="Javascript:window.print();self.window.close()" -->
        <div class="corpo">
          <?php
          $i = 0;
          while( $d_det = $query_det->fetch_assoc() ) {
          ++$i;

            $foto_g   = $d_det['foto_det_g'];
            $foto_p   = $d_det['foto_det_p'];

            $foto_det = ck_pic( $foto_g, $foto_p, true, 1 );

            $tipo_mov_in  = $d_det['tipo_mov_in'];
            $tipo_mov_out = $d_det['tipo_mov_out'];
            $iddestino    = $d_det['iddestino'];

            $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );

//            $tam_img = getimagesize( $foto_det );
//
//            $img_det_w = $tam_img[0];
//            $img_det_h = $tam_img[1];
//
//            $aspect = $img_det_w/$img_det_h;
//
//            $img_w = '263';
//            $img_h = '350';
//
//            if ( $aspect >= 1 ) {
//                $img_w = '467';
//                $img_h = '350';
//            }

          ?>
            <?php if ( $i == 1  ) { ?>
            <div>&nbsp;</div>
            <?php } ?>
          <?php require 'cabecalho_v.php';?>
          <p align="center" class="par_forte_n">&nbsp;</p>

          <div class="foto_det"><img src="<?php echo $foto_det ?>" alt="" height="350" /></div>

          <p><span class="par_forte_n">Nome:</span> <span class="par_forte"><?php echo $d_det['nome_det']; ?></span></p>
          <p><span class="par_forte_n">Matrícula:</span> <span class="par_forte"><?php echo !empty( $d_det['matricula'] ) ? formata_num( $d_det['matricula'] ) : ''; ?></span></p>
          <p><span class="par_forte_n">R.G.:</span> <span class="par_forte"><?php echo !empty( $d_det['rg_civil'] ) ? formata_num( $d_det['rg_civil'] ) : ''; ?></span></p>
          <p><span class="par_forte_n">Vulgo(s):</span> <span class="par_forte"><?php echo !empty( $d_det['vulgo'] ) ? $d_det['vulgo'] : 'N/C'; ?></span></p>
          <p><span class="par_forte_n">Nome do pai:</span> <span class="par_forte"><?php echo !empty( $d_det['pai_det'] ) ? $d_det['pai_det'] : 'N/C'; ?></span></p>
          <p><span class="par_forte_n">Nome da mãe:</span> <span class="par_forte"><?php echo $d_det['mae_det']; ?></span></p>
          <p><span class="par_forte_n">Nascimento:</span> <span class="par_forte"><?php echo empty($d_det['nasc_det_f']) ? '' : $d_det['nasc_det_f']. ' - ' .$d_det['idade_det'] . ' anos';  ?></span></p>
          <p><span class="par_forte_n">Natural de:</span> <span class="par_forte"><?php echo $d_det['cidade'] . ' - ' . $d_det['estado'] ?></span></p>
          <p><span class="par_forte_n">Situação atual:</span> <span class="par_forte"><?php echo $det['sitat'] ?></span></p>

              <?php If ( $contd != $i  ) { ?>
              <div style="page-break-before: always;">&nbsp;</div>
              <?php } ?>

          <?php } ?>
      <span class="_Footer">
            <div class="rodape">
                <p align="right" class="par_min_foto">Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>
                <hr align="center" width="645" size="0" noshade="noshade" color="#000000" />
                <p align="center"><?php echo $endereco ?></p>
            </div>
      </span>
    </div>
</body>
</html>