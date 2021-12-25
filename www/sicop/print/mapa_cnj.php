<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';

$pag  = link_pag();
$tipo = '';
$l    = '';

$imp_cadastro  = get_session( 'imp_cadastro', 'int' );
$imp_chefia    = get_session( 'imp_chefia', 'int' );
$imp_incl      = get_session( 'imp_incl', 'int' );

$titulo        = get_session ( 'titulo' );
$cidade        = get_session( 'cidade' );
$iduser        = get_session( 'user_id', 'int' );
$ip            = $_SERVER['REMOTE_ADDR'];
$maquina       = substr( $ip, strrpos( $ip, '.' ) + 1 );

$motivo_pag    = 'IMPRESSÃO DO MAPA POPULACIONAL - PDF';

if ( $imp_cadastro < 1 and $imp_chefia < 1 and $imp_incl < 1 ) {

    require 'cab_simp.php';
    $tipo = 3;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso à página SEM PERMISSÕES ( $motivo_pag ).";
    get_msg( $msg, 1 );

    exit;

}

//$is_post = is_post();
//if ( !$is_post ) {
//
//    // montar a mensagem q será salva no log
//    $msg = array( );
//    $msg['tipo'] = 'atn';
//    $msg['text'] = "Tentativa de acesso direto à página ( $motivo_pag ).";
//    get_msg( $msg, 1 );
//
//    echo msg_js( '', 'f' );
//
//    exit;
//
//}

$where_total   = get_where_det( 1 );
$where_na      = get_where_det( 2 );
$where_da      = get_where_det( 3 );
$where_trana   = get_where_det( 4 );
$where_trada   = get_where_det( 5 );
$where_tranada = get_where_det( 11 );
$where_nada    = get_where_det( 12 );

/*
 * montar as querys dos raios
 * dos detentos na casa
 */
// 8 raios + incl + pd + ph + ps
$raios     = 12;
$q_raio_na = array();

for ( $index = 1; $index <= $raios; $index++ ) {

    $q_raio_na["$index"] = "SELECT
                           COUNT(*) AS `total`
                         FROM
                           `detentos`
                           INNER JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                           INNER JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
                           LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                           LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                         WHERE
                           `raio`.`idraio` = $index
                           AND
                           $where_na";

}

/*
 * -----------------------------------------------------
 */



/*
 * montar as querys das celas
 * DETENTOS NA CASA
 */

// 8 raios + incl + pd + ph + ps
$raios       = 12;
$q_cela_na_r = array();

for ( $index = 1; $index <= $raios; $index++ ) {

    $q_cela_na_r["$index"] = "SELECT
                                `cela`.`idcela`,
                                `cela`.`cela`,
                                COUNT(*) AS `total`
                              FROM
                                `detentos`
                                INNER JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                                INNER JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
                                LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                                LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                              WHERE
                                `raio`.`idraio` = $index
                                AND
                                $where_na
                              GROUP BY
                                `cela`.`cela`";

}

/*
 * -----------------------------------------------------
 */


$q_pop = array();

$q_pop['total'] = "SELECT
                     COUNT(*) AS `total`
                   FROM
                     `detentos`
                     LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                     LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                   WHERE
                     $where_total";

$q_pop['transna'] = "SELECT
                       COUNT(*) AS `total`
                     FROM
                       `detentos`
                       LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                       LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                     WHERE
                       $where_trana";

$q_pop['transda'] = "SELECT
                       COUNT(*) AS `total`
                     FROM
                       `detentos`
                       LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                       LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                     WHERE
                       $where_trada";

$q_pop['transnada'] = "SELECT
                         COUNT(*) AS `total`
                       FROM
                         `detentos`
                         LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                         LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                       WHERE
                         $where_tranada";

$q_pop['nada'] = "SELECT
                    COUNT(*) AS `total`
                  FROM
                    `detentos`
                    LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                    LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                  WHERE
                    $where_nada";

$q_pop['na'] = "SELECT
                  COUNT(*) AS `total`
                FROM
                  `detentos`
                  LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                  LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                WHERE
                  $where_na";

$q_pop['da'] = "SELECT
                  COUNT(*) AS `total`
                FROM
                  `detentos`
                  LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                  LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                WHERE
                  $where_da";



/*
 * -----------------------------------------------------
 */


$db = SicopModel::getInstance();

$querytime_before = array_sum( explode( ' ', microtime() ) );

// executa as querys dos total dos raios na casa
foreach ( $q_raio_na as $key => $value ) {

    $q_raio_na["$key"] = $db->fetchOne( $q_raio_na["$key"] );

}

// executa as querys das celas na casa
foreach ( $q_cela_na_r as $key => $value ) {

    $q_cela_na_r["$key"] = $db->query( $q_cela_na_r["$key"] );

}

// executa as querys das populações
foreach ( $q_pop as $key => $value ) {

    $q_pop["$key"] = $db->fetchOne( $q_pop["$key"] );

}

$querytime_after = array_sum( explode( ' ', microtime() ) );

$querytime = $querytime_after - $querytime_before;

$db->closeConnection();

$raio_1_na    = $q_raio_na['1'];
$raio_2_na    = $q_raio_na['2'];
$raio_3_na    = $q_raio_na['3'];
$raio_4_na    = $q_raio_na['4'];
$raio_5_na    = $q_raio_na['5'];
$raio_6_na    = $q_raio_na['6'];
$raio_7_na    = $q_raio_na['7'];
$raio_8_na    = $q_raio_na['8'];
$raio_incl_na = $q_raio_na['9'];
$raio_pd_na   = $q_raio_na['10'];
$raio_ph_na   = $q_raio_na['11'];
$raio_ps_na   = $q_raio_na['12'];


$q_cela_na_r1   = $q_cela_na_r['1'];
$q_cela_na_r2   = $q_cela_na_r['2'];
$q_cela_na_r3   = $q_cela_na_r['3'];
$q_cela_na_r4   = $q_cela_na_r['4'];
$q_cela_na_r5   = $q_cela_na_r['5'];
$q_cela_na_r6   = $q_cela_na_r['6'];
$q_cela_na_r7   = $q_cela_na_r['7'];
$q_cela_na_r8   = $q_cela_na_r['8'];
$q_cela_na_incl = $q_cela_na_r['9'];
$q_cela_na_pd   = $q_cela_na_r['10'];
$q_cela_na_ph   = $q_cela_na_r['11'];
$q_cela_na_ps   = $q_cela_na_r['12'];


$pop_total     = $q_pop['total'];
$pop_transna   = $q_pop['transna'];
$pop_transda   = $q_pop['transda'];
$pop_transnada = $q_pop['transnada'];
$pop_nada      = $q_pop['nada'];
$pop_na        = $q_pop['na'];
$pop_da        = $q_pop['da'];


$data_sf = date( 'd/m/Y' );
$hora    = date( 'H:i' );

$porcentna = $pop_na / 768 * 100;
$porcentna = round( $porcentna, 0 );


// montar a mensagem q será salva no log
$msg = array();
$msg['tipo'] = 'desc';
$msg['entre_ch'] = $motivo_pag;
$msg['text'] = "Impressão do mapa populacional \n\n <b>Data/hora:</b> $data_sf às $hora";

get_msg( $msg, 1 );

//require_once('../init/tcpdf/config/lang/bra.php');
//require_once('../init/tcpdf/tcpdf.php');

require_once('classes/tcpdf/pdf.php');

// definir a fonte
$font = 'helvetica';
//$font = 'dejavusans';

// create new PDF document
$pdf = new PDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );

$pdf->setFontSubsetting(false);

// set document information PDF_CREATOR
$pdf->SetCreator( SICOP_SYS_NAME );
$pdf->SetAuthor( SICOP_SYS_NAME );
$pdf->SetTitle( 'Mapa populacional' );
$pdf->SetSubject( "Mapa populacional do dia $data_sf às $hora" );
$pdf->SetKeywords( SICOP_SYS_KW );

// defina a fonte nomoespaçada
$pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );

//define as margens
$pdf->SetMargins( 20, 35, 20 );
$pdf->SetHeaderMargin( PDF_MARGIN_HEADER );
$pdf->SetFooterMargin( PDF_MARGIN_FOOTER );

//define as quebras de páginas automáticas
//$pdf->SetAutoPageBreak( TRUE, PDF_MARGIN_BOTTOM );
$pdf->SetAutoPageBreak( TRUE, 15 );

// define o tipo de escala para as imagens
$pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );

// desabilitar o rodapé
//$pdf->setPrintFooter(false);

// define o idioma
$pdf->setLanguageArray( $l );

// ---------------------------------------------------------

// define o modo de exibição do pdf na tela
$pdf->SetDisplayMode( 'fullwidth', 'continuous', 'UseNone' );

// adicionar uma página
$pdf->AddPage();

// configurar a fonte
$pdf->SetFont( $font, 'B', 14 );

$txt = 'RELATÓRIO';
$pdf->Cell( 0, '', $txt, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

// adicionar quebra de linha
$pdf->Ln( 2 );

// configurar a fonte
$pdf->SetFont( $font, 'N', 9 );

$txt = "Gerado no dia $data_sf às $hora";
$pdf->Cell( 0, '', $txt, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

// adicionar quebra de linha
$pdf->Ln( 2 );



/**
 * DESENHAR OS QUADRADOS DOS TOTAIS
 */

// definindo a posição do ponteiro em relação a margem esquerda
$pdf->SetX(30);

$txt = "Transito na casa: $pop_transna";
$pdf->Cell( 50, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = "Transito da casa: $pop_transda";
$pdf->Cell( 50, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = "Transito na casa da casa: $pop_transnada";
$pdf->Cell( 50, '', $txt, 1, 1, 'C', 0, '', 0, false, 'T', 'M' );

// definindo a posição do ponteiro em relação a margem esquerda
$pdf->SetX(30);
$txt = "Na casa da casa: $pop_nada";
$pdf->Cell( 50, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = "Na casa: $pop_na";
$pdf->Cell( 50, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = "Da casa: $pop_da";
$pdf->Cell( 50, '', $txt, 1, 1, 'C', 0, '', 0, false, 'T', 'M' );

// definindo a posição do ponteiro em relação a margem esquerda
$pdf->SetX(30);
$txt = "População total: $pop_total";
$pdf->Cell( 150, '', $txt, 1, 1, 'C', 0, '', 0, false, 'T', 'M' );

/**
 * -----------------------------------------------------
 */


// adicionar quebra de linha
$pdf->Ln( 4 );


$txt = "Número de celas por pavilhão:";
$pdf->Cell( 50, '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

$txt = "Raios de 1 à 8: 8 celas.";
$pdf->Cell( 50, '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

$txt = "Inclusão: 3 celas.";
$pdf->Cell( 50, '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

$txt = "Pavilhão Disciplinar: 10 celas.";
$pdf->Cell( 50, '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

$txt = "Pavilhão hospitalar: 6 celas.";
$pdf->Cell( 50, '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

$txt = "Pavilhão de seguro: 11 celas.";
$pdf->Cell( 50, '', $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );


// adicionar quebra de linha
$pdf->Ln( 4 );

/**
 * CONTAGENS DAS CELAS
 */

// configurar a fonte
$pdf->SetFont( $font, 'N', 12 );

$txt = 'CONTAGEM';
$pdf->Cell( 0, '', $txt, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

// adicionar quebra de linha
$pdf->Ln( 2 );

// configurar a fonte
$pdf->SetFont( $font, 'B', 9 );

$w = 21; // largura
$h = 7;  // altura
$x = 21;

/**
 * TITULO DOS RAIOS
 */

// definindo a posição do ponteiro em relação a margem esquerda
$pdf->SetX( $x );
$txt = 'raio 1';
$pdf->Cell( $w, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = 'raio 2';
$pdf->Cell( $w, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = 'raio 3';
$pdf->Cell( $w, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = 'raio 4';
$pdf->Cell( $w, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = 'raio 5';
$pdf->Cell( $w, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = 'raio 6';
$pdf->Cell( $w, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = 'raio 7';
$pdf->Cell( $w, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = 'raio 8';
$pdf->Cell( $w, '', $txt, 1, 1, 'C', 0, '', 0, false, 'T', 'M' );

/**
 * /TITULO DOS RAIOS
 */


/**
 * CONTAGENS DAS CELAS
 */

// configurar a fonte
$pdf->SetFont( $font, 'N', 9 );

// definindo a posição do ponteiro em relação a margem esquerda
$pdf->SetX($x);


$limit = 8; // 8 celas
$txt   = '';
$y     = $pdf->GetY(); // pegando a altura do ponteiro


/**
 * RAIO 1
 */

$cont  = 1;

while ( $linha = $q_cela_na_r1->fetch_assoc() or $cont <= $limit ) {

    while ( $linha['cela'] != $cont ) {

        if ( $cont > $limit ) break;

        $quebra = 1;
        if ( $cont == $limit ) $quebra = 0;

        // definindo a posição do ponteiro em relação a margem esquerda
        $pdf->SetX($x);
        $txt = "cela $cont - 0";
        $pdf->Cell( $w, $h, $txt, 'LR', $quebra, 'C', 0, '', 0, false, 'T', 'M' );

        $cont++;

    }

    if ( $cont > $limit ) break;

    $quebra = 1;
    if ( $cont == $limit ) $quebra = 0;

    // definindo a posição do ponteiro em relação a margem esquerda
    $pdf->SetX($x);
    $txt = 'cela ' . $linha['cela'] . ' - ' . $linha['total'];
    $pdf->Cell( $w, $h, $txt, 'LR', $quebra, 'C', 0, '', 0, false, 'T', 'M' );
    $cont++;

}

/**
 * -----------------------------------------------------
 */

/**
 * RAIO 2
 */

// pegando a posição do ponteiro em relação a margem
$x = $pdf->GetX();

// definindo a posição do ponteiro em relação a margem superior
$pdf->SetY($y);

// resetar o contador
$cont  = 1;

while ( $linha = $q_cela_na_r2->fetch_assoc() or $cont <= $limit ) {

    while ( $linha['cela'] != $cont ) {

        if ( $cont > $limit ) break;

        $quebra = 1;
        if ( $cont == $limit ) $quebra = 0;

        // definindo a posição do ponteiro em relação a margem esquerda
        $pdf->SetX($x);
        $txt = "cela $cont - 0";
        $pdf->Cell( $w, $h, $txt, 'LR', $quebra, 'C', 0, '', 0, false, 'T', 'M' );

        $cont++;

    }

    if ( $cont > $limit ) break;

    $quebra = 1;
    if ( $cont == $limit ) $quebra = 0;

    // definindo a posição do ponteiro em relação a margem esquerda
    $pdf->SetX($x);
    $txt = 'cela ' . $linha['cela'] . ' - ' . $linha['total'];
    $pdf->Cell( $w, $h, $txt, 'LR', $quebra, 'C', 0, '', 0, false, 'T', 'M' );

    $cont++;

}

/**
 * -----------------------------------------------------
 */

/**
 * RAIO 3
 */

// pegando a posição do ponteiro em relação a margem
$x = $pdf->GetX();

// definindo a posição do ponteiro em relação a margem superior
$pdf->SetY($y);

// resetar o contador
$cont  = 1;

while ( $linha = $q_cela_na_r3->fetch_assoc() or $cont <= $limit ) {

    while ( $linha['cela'] != $cont ) {

        if ( $cont > $limit ) break;

        $quebra = 1;
        if ( $cont == $limit ) $quebra = 0;

        // definindo a posição do ponteiro em relação a margem esquerda
        $pdf->SetX($x);
        $txt = "cela $cont - 0";
        $pdf->Cell( $w, $h, $txt, 'LR', $quebra, 'C', 0, '', 0, false, 'T', 'M' );

        $cont++;

    }

    if ( $cont > $limit ) break;

    $quebra = 1;
    if ( $cont == $limit ) $quebra = 0;

    // definindo a posição do ponteiro em relação a margem esquerda
    $pdf->SetX($x);
    $txt = 'cela ' . $linha['cela'] . ' - ' . $linha['total'];
    $pdf->Cell( $w, $h, $txt, 'LR', $quebra, 'C', 0, '', 0, false, 'T', 'M' );

    $cont++;

}


/**
 * -----------------------------------------------------
 */

/**
 * RAIO 4
 */

// pegando a posição do ponteiro em relação a margem
$x = $pdf->GetX();

// definindo a posição do ponteiro em relação a margem superior
$pdf->SetY($y);

// resetar o contador
$cont  = 1;

while ( $linha = $q_cela_na_r4->fetch_assoc() or $cont <= $limit ) {

    while ( $linha['cela'] != $cont ) {

        if ( $cont > $limit ) break;

        $quebra = 1;
        if ( $cont == $limit ) $quebra = 0;

        // definindo a posição do ponteiro em relação a margem esquerda
        $pdf->SetX($x);
        $txt = "cela $cont - 0";
        $pdf->Cell( $w, $h, $txt, 'LR', $quebra, 'C', 0, '', 0, false, 'T', 'M' );

        $cont++;

    }

    if ( $cont > $limit ) break;

    $quebra = 1;
    if ( $cont == $limit ) $quebra = 0;

    // definindo a posição do ponteiro em relação a margem esquerda
    $pdf->SetX($x);
    $txt = 'cela ' . $linha['cela'] . ' - ' . $linha['total'];
    $pdf->Cell( $w, $h, $txt, 'LR', $quebra, 'C', 0, '', 0, false, 'T', 'M' );

    $cont++;

}


/**
 * -----------------------------------------------------
 */

/**
 * RAIO 5
 */

// pegando a posição do ponteiro em relação a margem
$x = $pdf->GetX();

// definindo a posição do ponteiro em relação a margem superior
$pdf->SetY($y);

// resetar o contador
$cont  = 1;

while ( $linha = $q_cela_na_r5->fetch_assoc() or $cont <= $limit ) {

    while ( $linha['cela'] != $cont ) {

        if ( $cont > $limit ) break;

        $quebra = 1;
        if ( $cont == $limit ) $quebra = 0;

        // definindo a posição do ponteiro em relação a margem esquerda
        $pdf->SetX($x);
        $txt = "cela $cont - 0";
        $pdf->Cell( $w, $h, $txt, 'LR', $quebra, 'C', 0, '', 0, false, 'T', 'M' );

        $cont++;

    }

    if ( $cont > $limit ) break;

    $quebra = 1;
    if ( $cont == $limit ) $quebra = 0;

    // definindo a posição do ponteiro em relação a margem esquerda
    $pdf->SetX($x);
    $txt = 'cela ' . $linha['cela'] . ' - ' . $linha['total'];
    $pdf->Cell( $w, $h, $txt, 'LR', $quebra, 'C', 0, '', 0, false, 'T', 'M' );

    $cont++;

}


/**
 * -----------------------------------------------------
 */

/**
 * RAIO 6
 */

// pegando a posição do ponteiro em relação a margem
$x = $pdf->GetX();

// definindo a posição do ponteiro em relação a margem superior
$pdf->SetY($y);

// resetar o contador
$cont  = 1;

while ( $linha = $q_cela_na_r6->fetch_assoc() or $cont <= $limit ) {

    while ( $linha['cela'] != $cont ) {

        if ( $cont > $limit ) break;

        $quebra = 1;
        if ( $cont == $limit ) $quebra = 0;

        // definindo a posição do ponteiro em relação a margem esquerda
        $pdf->SetX($x);
        $txt = "cela $cont - 0";
        $pdf->Cell( $w, $h, $txt, 'LR', $quebra, 'C', 0, '', 0, false, 'T', 'M' );

        $cont++;

    }

    if ( $cont > $limit ) break;

    $quebra = 1;
    if ( $cont == $limit ) $quebra = 0;

    // definindo a posição do ponteiro em relação a margem esquerda
    $pdf->SetX($x);
    $txt = 'cela ' . $linha['cela'] . ' - ' . $linha['total'];
    $pdf->Cell( $w, $h, $txt, 'LR', $quebra, 'C', 0, '', 0, false, 'T', 'M' );

    $cont++;

}


/**
 * -----------------------------------------------------
 */

/**
 * RAIO 7
 */

// pegando a posição do ponteiro em relação a margem
$x = $pdf->GetX();

// definindo a posição do ponteiro em relação a margem superior
$pdf->SetY($y);

// resetar o contador
$cont  = 1;

while ( $linha = $q_cela_na_r7->fetch_assoc() or $cont <= $limit ) {

    while ( $linha['cela'] != $cont ) {

        if ( $cont > $limit ) break;

        $quebra = 1;
        if ( $cont == $limit ) $quebra = 0;

        // definindo a posição do ponteiro em relação a margem esquerda
        $pdf->SetX($x);
        $txt = "cela $cont - 0";
        $pdf->Cell( $w, $h, $txt, 'LR', $quebra, 'C', 0, '', 0, false, 'T', 'M' );

        $cont++;

    }

    if ( $cont > $limit ) break;

    $quebra = 1;
    if ( $cont == $limit ) $quebra = 0;

    // definindo a posição do ponteiro em relação a margem esquerda
    $pdf->SetX($x);
    $txt = 'cela ' . $linha['cela'] . ' - ' . $linha['total'];
    $pdf->Cell( $w, $h, $txt, 'LR', $quebra, 'C', 0, '', 0, false, 'T', 'M' );

    $cont++;

}


/**
 * -----------------------------------------------------
 */

/**
 * RAIO 8
 */

// pegando a posição do ponteiro em relação a margem
$x = $pdf->GetX();

// definindo a posição do ponteiro em relação a margem superior
$pdf->SetY($y);

// resetar o contador
$cont  = 1;

while ( $linha = $q_cela_na_r8->fetch_assoc() or $cont <= $limit ) {

    while ( $linha['cela'] != $cont ) {

        if ( $cont > $limit ) break;

        // definindo a posição do ponteiro em relação a margem esquerda
        $pdf->SetX($x);
        $txt = "cela $cont - 0";
        $pdf->Cell( $w, $h, $txt, 'LR', 1, 'C', 0, '', 0, false, 'T', 'M' );

        $cont++;

    }

    if ( $cont > $limit ) break;

    // definindo a posição do ponteiro em relação a margem esquerda
    $pdf->SetX($x);
    $txt = 'cela ' . $linha['cela'] . ' - ' . $linha['total'];
    $pdf->Cell( $w, $h, $txt, 'LR', 1, 'C', 0, '', 0, false, 'T', 'M' );

    $cont++;

}

/**
 * -----------------------------------------------------
 */



/*
 * TOTAIS DOS RAIOS
 */

// configurar a fonte
$pdf->SetFont( $font, 'B', 9 );


$x = 21;

// definindo a posição do ponteiro em relação a margem esquerda
$pdf->SetX($x);
$txt = "Total: $raio_1_na";
$pdf->Cell( $w, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = "Total: $raio_2_na";
$pdf->Cell( $w, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = "Total: $raio_3_na";
$pdf->Cell( $w, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = "Total: $raio_4_na";
$pdf->Cell( $w, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = "Total: $raio_5_na";
$pdf->Cell( $w, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = "Total: $raio_6_na";
$pdf->Cell( $w, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = "Total: $raio_7_na";
$pdf->Cell( $w, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = "Total: $raio_8_na";
$pdf->Cell( $w, '', $txt, 1, 1, 'C', 0, '', 0, false, 'T', 'M' );


/*
 * DESENHANDO UMA LINHA EM BRANCO ABAIXO DOS TOTAIS
 */

// definindo a posição do ponteiro em relação a margem esquerda
$pdf->SetX($x);

$limit = 8;
$txt = '';

for ( $i = 1; $i <= $limit; $i++ ){

    $quebra = 0;
    if ( $cont == $limit ) $quebra = 1;

    $pdf->Cell( $w, $h, $txt, 1, $quebra, 'C', 0, '', 0, false, 'T', 'M' );
}


/*
 * -----------------------------------------------------
 */




/*
 * LINHA DA INCLUSÃO, PD, PH, PS
 */

// quebra de linha
$pdf->Ln( 10 );

// configurar a fonte
$pdf->SetFont( $font, 'B', 9 );

//largura da celula
$w = 42;

//posição do x
$x = 21;

// definindo a posição do ponteiro em relação a margem esquerda
$pdf->SetX( $x );

$txt = 'inclusão';
$pdf->Cell( $w, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = 'PD';
$pdf->Cell( $w, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = 'PH';
$pdf->Cell( $w, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = 'PS';
$pdf->Cell( $w, '', $txt, 1, 1, 'C', 0, '', 0, false, 'T', 'M' );


/*
 * -----------------------------------------------------
 */

// configurar a fonte
$pdf->SetFont( $font, 'N', 9 );

// pegando a altura do ponteiro, que está logo abaixo das linhas dos nomes do pavilhões
$y = $pdf->GetY();

// definindo a posição do ponteiro em relação a margem superior
$pdf->SetY( $y + 10 );

// definindo a posição do ponteiro em relação a margem esquerda
$pdf->SetX( $x );

//definindo a altura da linha que será desenhada
$h_line = $y + 70;

// estilo das linhas das bordas
$line_style = array( 'width' => 0.2 );

// desenhando a linha das bordas
$pdf->Line( $x , $y, $x, $h_line, $line_style );




/*
 * INCLUSÃO
 */

$cont  = 1;  // resetar o contador
$limit = 3;  // 3 celas
$h     = 15; // altura da linha

while ( $linha = $q_cela_na_incl->fetch_assoc() or $cont <= $limit ) {

    while ( $linha['cela'] != $cont ) {

        if ( $cont > $limit ) break;

        $quebra = 1;
        if ( $cont == $limit ) $quebra = 0;

        // definindo a posição do ponteiro em relação a margem esquerda
        $pdf->SetX($x);
        $txt = "cela $cont - 0";
        $pdf->Cell( $w, $h, $txt, 0, $quebra, 'C', 0, '', 0, false, 'T', 'M' );

        $cont++;

    }

    if ( $cont > $limit ) break;

    $quebra = 1;
    if ( $cont == $limit ) $quebra = 0;

    // definindo a posição do ponteiro em relação a margem esquerda
    $pdf->SetX($x);
    $txt = 'cela ' . $linha['cela'] . ' - ' . $linha['total'];
    $pdf->Cell( $w, $h, $txt, 0, $quebra, 'C', 0, '', 0, false, 'T', 'M' );

    $cont++;

}

/*
 * -----------------------------------------------------
 */



/*
 * PD
 */

// pegando a posição do ponteiro em relação a margem
$x = $pdf->GetX();

// definindo a posição do ponteiro em relação a margem superior
$pdf->SetY( $y + 3 );

// desenhando a linha das bordas
$pdf->Line( $x , $y, $x, $h_line, $line_style );

$cont  = 1;  // resetar o contador
$limit = 10; // 10 celas
$h     = 6;  // altura da linha

while ( $linha = $q_cela_na_pd->fetch_assoc() or $cont <= $limit ) {

    while ( $linha['cela'] != $cont ) {

        if ( $cont > $limit ) break;

        $quebra = 1;
        if ( $cont == $limit ) $quebra = 0;

        // definindo a posição do ponteiro em relação a margem esquerda
        $pdf->SetX($x);
        $txt = "cela $cont - 0";
        $pdf->Cell( $w, $h, $txt, 0, $quebra, 'C', 0, '', 0, false, 'T', 'M' );

        $cont++;

    }

    if ( $cont > $limit ) break;

    $quebra = 1;
    if ( $cont == $limit ) $quebra = 0;

    // definindo a posição do ponteiro em relação a margem esquerda
    $pdf->SetX($x);
    $txt = 'cela ' . $linha['cela'] . ' - ' . $linha['total'];
    $pdf->Cell( $w, $h, $txt, 0, $quebra, 'C', 0, '', 0, false, 'T', 'M' );

    $cont++;

}

/*
 * -----------------------------------------------------
 */



/*
 * PH
 */

// pegando a posição do ponteiro em relação a margem
$x = $pdf->GetX();

// definindo a posição do ponteiro em relação a margem superior
$pdf->SetY($y + 3);

// desenhando a linha das bordas
$pdf->Line( $x , $y, $x, $h_line, $line_style );

$cont  = 1;  // resetar o contador
$limit = 6;  // 6 celas
$h     = 10; // altura da linha

while ( $linha = $q_cela_na_ph->fetch_assoc() or $cont <= $limit ) {

    while ( $linha['cela'] != $cont ) {

        if ( $cont > $limit ) break;

        $quebra = 1;
        if ( $cont == $limit ) $quebra = 0;

        // definindo a posição do ponteiro em relação a margem esquerda
        $pdf->SetX($x);
        $txt = "cela $cont - 0";
        $pdf->Cell( $w, $h, $txt, 0, $quebra, 'C', 0, '', 0, false, 'T', 'M' );

        $cont++;

    }

    if ( $cont > $limit ) break;

    $quebra = 1;
    if ( $cont == $limit ) $quebra = 0;

    // definindo a posição do ponteiro em relação a margem esquerda
    $pdf->SetX($x);
    $txt = 'cela ' . $linha['cela'] . ' - ' . $linha['total'];
    $pdf->Cell( $w, $h, $txt, 0, $quebra, 'C', 0, '', 0, false, 'T', 'M' );

    $cont++;

}

/*
 * -----------------------------------------------------
 */


/*
 * PS
 */

// pegando a posição do ponteiro em relação a margem
$x = $pdf->GetX();

// definindo a posição do ponteiro em relação a margem superior
$pdf->SetY($y);

// desenhando a linha das bordas
$pdf->Line( $x , $y, $x, $h_line, $line_style );

$cont  = 1;  // resetar o contador
$limit = 11; // 1 celas
$h     = 6;  // altura da linha

while ( $linha = $q_cela_na_ps->fetch_assoc() or $cont <= $limit ) {

    while ( $linha['cela'] != $cont ) {

        if ( $cont > $limit ) break;

        $quebra = 1;
        if ( $cont == $limit ) $quebra = 0;

        // definindo a posição do ponteiro em relação a margem esquerda
        $pdf->SetX($x);
        $txt = "cela $cont - 0";
        $pdf->Cell( $w, $h, $txt, 0, $quebra, 'C', 0, '', 0, false, 'T', 'M' );

        $cont++;

    }

    if ( $cont > $limit ) break;

    $quebra = 1;
    if ( $cont == $limit ) $quebra = 0;

    // definindo a posição do ponteiro em relação a margem esquerda
    $pdf->SetX($x);
    $txt = 'cela ' . $linha['cela'] . ' - ' . $linha['total'];
    $pdf->Cell( $w, $h, $txt, 0, $quebra, 'C', 0, '', 0, false, 'T', 'M' );

    $cont++;

}

/*
 * -----------------------------------------------------
 */

// pegando a posição do ponteiro em relação a margem
$x = $pdf->GetX();

// desenhando a linha da última borda
$pdf->Line( $x , $y, $x, $h_line, $line_style );

// quebra de linha
$pdf->Ln();



/*
 * LINHA DOS TOTAIS DA INCLUSÃO, PD, PH, PS
 */

// configurar a fonte
$pdf->SetFont( $font, 'B', 9 );

$w = 42;
$x = 21;

// definindo a posição do ponteiro em relação a margem esquerda
$pdf->SetX( $x );
$txt = "Total: $raio_incl_na";
$pdf->Cell( $w, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = "Total: $raio_pd_na";
$pdf->Cell( $w, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = "Total: $raio_ph_na";
$pdf->Cell( $w, '', $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

$txt = "Total: $raio_ps_na";
$pdf->Cell( $w, '', $txt, 1, 1, 'C', 0, '', 0, false, 'T', 'M' );

/*
 * -----------------------------------------------------
 */


/*
 * SOMATÓRIO POR RAIOS PARES E ÍMPARES ( SOMENTE DE 1 À 8 )
 */

// quebra de linha
$pdf->Ln();

// configurar a fonte
$pdf->SetFont( $font, 'N', 9 );

$total_par   = 0;
$total_impar = 0;

foreach ( $q_raio_na as $raio => $contagem ){

    if  ( $raio > 8 ) break;

    if ( $raio % 2 == 0 )
        $total_par += $contagem;
    else
        $total_impar += $contagem;

}


$txt = "Total raios pares: $total_par";
$pdf->Cell( '', '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

$txt = "Total raios ímpares: $total_impar";
$pdf->Cell( '', '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );


/*
 * -----------------------------------------------------
 */



// reseta o ponteiro para a última página
$pdf->lastPage();
// ---------------------------------------------------------


$data_f = str_replace( '/', '_', $data_sf );
$hora_f = str_replace( ':', '_', $hora );

$data_hora = $data_f . '_' . $hora_f;

// fecha e manda para o navegaddor
$pdf->Output( "mapa_populacional_$data_hora.pdf", 'I' );

//============================================================+
// END OF FILE
//============================================================+

?>


