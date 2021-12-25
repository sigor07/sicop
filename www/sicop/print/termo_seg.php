<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';

$pag = link_pag();
$tipo = '';

$iniciais      = get_session( 'iniciais' );
$sigla_setor   = get_session( 'sigla_setor' );
$diretor_g     = get_session( 'diretor_geral' );
$diretor_s     = get_session( 'diretor_seg' );
$titulo        = get_session( 'titulo' );
$secretaria    = get_session( 'secretaria' );
$coordenadoria = get_session( 'coordenadoria' );
$unidadecurto  = get_session( 'unidadecurto' );
$endereco      = get_session( 'endereco_sort' );
$cidade        = get_session( 'cidade' );
$iduser        = get_session( 'user_id', 'int' );
$ip            = $_SERVER['REMOTE_ADDR'];
$maquina       = substr( $ip, strrpos( $ip, '.' ) + 1 );

$motivo_pag = 'IMPRESSÃO DO TERMO DE SEGURO';

$imp_chefia = get_session( 'imp_chefia', 'int' );
if ( empty( $imp_chefia ) or $imp_chefia < 1 ) {

    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg         = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso à página SEM PERMISSÕES ( $motivo_pag ).";
    get_msg( $msg, 1 );

    exit;

}

$is_post = is_post();
if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página de $motivo_pag.";
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );
    exit;

}

extract( $_POST, EXTR_OVERWRITE );

$iddet = empty( $iddet ) ? '' : (int)$iddet;

if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. Operação cancelada ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );
    exit;

}

$mot_termo = empty( $mot_termo ) ? '' : (int)$mot_termo;
if ( empty( $mot_termo ) || $mot_termo > 3 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Identificador do motivo do termo em branco. Operação cancelada ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );
    exit;

}

$unid_dest = empty( $unid_dest ) ? '' : $unid_dest;
if ( $mot_termo == 3 and empty( $unid_dest ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Unidade de destino em branco. Operação cancelada ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );
    exit;

}


$mot_imp = 'APTO AO CONVÍVIO';
if ( $mot_termo == 2 ) {
    $mot_imp = 'INAPTO AO CONVÍVIO';
} else if ( $mot_termo == 3 ) {
    $mot_imp = 'INAPTO AO CONVÍVIO E TRANSFERÊNCIA';
}

// pegar os dados do detento
$detento = dados_det( $iddet );

$msg = array();
$msg['tipo'] = 'desc';
$msg['entre_ch'] = $motivo_pag;
$msg['text'] = "Impressão do termo de seguro ( $mot_imp ). \n\n $detento";

get_msg( $msg, 1 );

$testemunha = empty( $testemunha ) ? '' : $testemunha;
$escrivao   = empty( $escrivao ) ? '' : $escrivao;
$nome_det   = '';
$matr       = '';


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="pt-br" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="<?php echo SICOP_SYS_IMG_PATH; ?>favicon.ico" type="image/x-icon" />
        <title><?php echo $titulo ?></title>
        <link href="<?php echo SICOP_ABS_PATH ?>css/reset.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_cabv.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_po.css" rel="stylesheet" type="text/css" />
    </head>

    <body onload="Javascript:window.print();self.window.close()">
    <!-- onload="Javascript:window.print();self.window.close()" -->

        <?php require 'cabecalho_v.php'; ?>
        <div class="corpo">

            <p class="par_corpo">&nbsp;</p>

            <p class="par_forte_n" align="center">TERMO DE DECLARAÇOES</p>

            <p class="par_corpo_menor">&nbsp;</p>
            <p class="par_corpo_menor">&nbsp;</p>

            <p class="par_corpo">Na presente data, na sala da Chefia desta unidade prisional, onde se achava presente o Sr. Diretor do Núcleo de Segurança, compareceu <?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?> abaixo qualificad<?php echo SICOP_DET_ART_L; ?>:</p>

            <p class="par_corpo_menor">&nbsp;</p>
            <p class="par_corpo_menor">&nbsp;</p>

            <?php require 'quali/det_basic_print.php'; ?>

            <p class="par_corpo_menor">&nbsp;</p>
            <p class="par_corpo_menor">&nbsp;</p>
            <p class="par_corpo">
                O qual, de livre e espontânea vontade, declarou que
            <?php if ( $mot_termo == 1 ) { ?>
                <b>está apto ao convívio normal com <?php echo SICOP_DET_ART_L; ?>s demais <?php echo SICOP_DET_DESC_L; ?>s desta unidade no pavilhões convencionais e que assume toda
                e qualquer responsabilidade por qualquer problema de convivência que por ventura venha ter</b>.
            <?php } else if ( $mot_termo == 2 || $mot_termo == 3 )  { ?>
                se encontra com dificuldades em se relacionar com <?php echo SICOP_DET_ART_L; ?>s demais <?php echo SICOP_DET_DESC_L; ?>s aqui custodiad<?php echo SICOP_DET_ART_L; ?>s. Desta forma, <b>necessita de
                MEDIDA PREVENTIVA DE SEGURANÇA PESSOAL</b> (Art 50, §1º e §2º, da resolução SAP-144, de 29 de junho de 2010),
                para se isolar temporariamente do convívio com <?php echo SICOP_DET_ART_L; ?>s demais <?php echo SICOP_DET_DESC_L; ?>s<?php if ( $mot_termo == 3 ) { ?>
                e, devido aos fatos narrados, de remoção para a(o) <?php echo $unid_dest; ?><?php } ?>.
            <?php } ?>
                Declarou ainda que não possui nada contra a diretoria e funcionários desta unidade.
            </p>
            <p class="par_corpo">
                Nada mais disse e nem lhe foi perguntado, lido e achado conforme, vai devidamente assinado pelo Sr. Diretor,
                pelo declarante e pelo servidor que digitou.
            </p>

            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>
            <p align="right"><?php echo $cidade; ?>, <?php echo data_f() ?> </p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>

            <div class="ass_termo_seg_dir">
                <p class="par_ass">________________________________________</p>
                <p class="par_ass"><?php echo $nome_det; ?><br /><?php echo $matr; ?></p>
                <p class="par_ass">Declarante</p>
            </div>

            <div class="ass_termo_seg_esq">
                <p class="par_ass">________________________________________</p>
                <p class="par_ass">Diretor do Núcleo de Segurança</p>
            </div>

            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>

            <div class="ass_termo_seg_dir">
                <p class="par_ass">________________________________________</p>
                <p class="par_ass"><?php echo $testemunha; ?></p>
                <p class="par_ass">Testemunha</p>
            </div>

            <div class="ass_termo_seg_esq">
                <p class="par_ass">________________________________________</p>
                <p class="par_ass"><?php echo $escrivao; ?></p>
                <p class="par_ass">Servidor</p>
            </div>

            <p class="par_corpo">&nbsp;</p>

            <span class="_Footer">
                  <div class="rodape_termo_seg">
                      <p align="right" class="par_min">Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>
                      <hr align="center" width="645" size="0" noshade="noshade" color="#000000" />
                      <p align="center"><?php echo $endereco ?></p>
                  </div>
            </span>

        </div>
    </body>
</html>