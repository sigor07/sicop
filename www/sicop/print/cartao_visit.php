<?php
if ( !isset( $_SESSION ) ) session_start();

    require '../init/config.php';
require 'incl_print.php';

$pag = link_pag();
$tipo = '';

$imp_rol = get_session( 'imp_rol', 'int' );
$n_rol_n = 1;

$titulo       = get_session( 'titulo' );
$unidadecurto = get_session( 'unidade_sort' );
$cidade       = get_session( 'cidade' );
$iduser       = get_session( 'user_id', 'int' );

$motivo_pag = 'IMPRESSÃO DE CARTÃO DE IDENTIFICAÇÃO DE VISITANTE';

if ( $imp_rol < $n_rol_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 'f' );

    exit;

}

$idvisit_g = get_get( 'idvisit', 'int' );

$idvisit_s = get_session( 'idvisit' );

$idvisit = empty( $idvisit_g ) ? $idvisit_s : $idvisit_g;

if ( empty( $idvisit ) ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );

    exit;

}

$q_visit = "SELECT
              `visitas`.`idvisita`,
              `visitas`.`nome_visit`,
              `visitas`.`rg_visit`,
              `visitas`.`sexo_visit`,
              `visitas`.`nasc_visit`,
              DATE_FORMAT( `visitas`.`nasc_visit`, '%d/%m/%Y' ) AS nasc_visit_f,
              `tipoparentesco`.`parentesco`,
              `detentos`.`nome_det`,
              `detentos`.`matricula`,
              `visita_fotos`.`foto_visit_g`,
              `visita_fotos`.`foto_visit_p`
            FROM
              `visitas`
              LEFT JOIN `tipoparentesco` ON `visitas`.`cod_parentesco` = `tipoparentesco`.`idparentesco`
              LEFT JOIN `detentos` ON `visitas`.`cod_detento` = `detentos`.`iddetento`
              LEFT JOIN `visita_fotos` ON `visita_fotos`.`id_foto` = `visitas`.`cod_foto`
            WHERE
              `visitas`.`idvisita` IN( $idvisit )
            ORDER BY
              `visitas`.`nome_visit`";
            /*,21,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47 */

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_visit = $model->query( $q_visit );

// fechando a conexao
$model->closeConnection();

if( !$q_visit ) {

    echo msg_js( 'FALHA!!!', 'f' );
    exit;

}

$contv = $q_visit->num_rows;

if( $contv < 1 ) {

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ( IMPRESSÃO DE CARTÃO DE IDENTIDADE DE VISITANTES ).\n\n Página: $pag";
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
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_p_quali.css" rel="stylesheet" type="text/css" />
    </head>

    <body onload="Javascript:window.print();self.window.close()">
    <!-- onload="Javascript:window.print();self.window.close()" -->
    <div class="corpo_cartao">
    <?php
        $i = 0;
        while( $d_visit = $q_visit->fetch_assoc() ) {
        ++$i;

        if ( !empty( $idvisit_g ) ){
            $nome_visit = $d_visit['nome_visit'];
            $idv = $d_visit['idvisita'];
            $mensagem = "[ IMPRESSÃO DE CARTÃO DE IDENTIDADE DE VISITANTE ] \n\n <b>ID:</b> $idv, <b>Visitante:</b> $nome_visit";
            salvaLog($mensagem);
        }

        $foto_g = $d_visit['foto_visit_g'];
        $foto_p = $d_visit['foto_visit_p'];

        $foto_visit = ck_pic( $foto_g, $foto_p, false, 2 );

    ?>
      <?php if ( $i == 1 ) { ?>
      <div class="quebra_pag">&nbsp;</div>
      <?php } ?>
         <div class="cartao_visit">
             <div class="cab_visit"><p class="par_cab_visit"><?php echo $unidadecurto; ?></p></div>

            <div class="cab_visit"><p class="par_cab_id">CARTEIRA DE IDENTIFICAÇÃO DE VISITANTE</p></div>

            <div class="imagem_visit_cartao"><img src="<?php echo $foto_visit ?>" alt="" width="128" height="170" /></div>

            <div class="linha_curta_visit"><p class="par_cartao"><b>Visitante:</b> <?php echo $d_visit['nome_visit'];?></p></div>

            <div class="linha_curta_visit"><p class="par_cartao"><b>R.G.:</b> <?php echo $d_visit['rg_visit']; ?></p></div>

            <div class="linha_curta_visit"><p class="par_cartao"><b>ID no sistema:</b> <?php echo $d_visit['idvisita']; ?></p></div>

            <p class="par_cartao"><b>Nascimento:</b> <?php echo empty($d_visit['nasc_visit_f']) ? '' : $d_visit['nasc_visit_f'];?></p>

            <p class="par_cartao"><b>Parentesco:</b> <?php echo $d_visit['parentesco'] ?></p>

            <div class="linha_curta_visit"><p class="par_cartao"><b>Sexo:</b> <?php echo $d_visit['sexo_visit'] ?></p></div>

            <div class="linha_curta_visit"><p class="par_cartao"><b><?php echo SICOP_DET_DESC_FU; ?>:</b> <?php echo $d_visit['nome_det'] ?></p></div>

            <div class="linha_curta_visit"><p class="par_cartao"><b>Matrícula:</b> <?php if ( !empty( $d_visit['matricula'] ) ) echo formata_num( $d_visit['matricula'] ) ?></p></div>

            <div class="linha_longa"><p class="par_cartao_data_visit"><?php echo $cidade; ?>, <?php echo data_f()?> </p></div>
        </div>
        <?php if ( $i%8 == 0 and $contv != $i ) { ?>
        <div class="quebra_pag" style="page-break-before: always;">&nbsp;</div>
        <?php } ?>
        <?php } ?>

        </div>
    </body>
</html>