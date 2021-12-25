<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';

$pag  = link_pag();
$tipo = '';
$l    = '';

$imp_cadastro  = get_session( 'imp_cadastro', 'int' );
$imp_chefia    = get_session( 'imp_chefia', 'int' );

$iniciais      = get_session( 'iniciais' );
$diretor_s     = get_session( 'diretor_seg', 'int' );
$titulo        = get_session( 'titulo' );
$cidade        = get_session( 'cidade' );
$iduser        = get_session( 'user_id', 'int' );
$ip            = $_SERVER['REMOTE_ADDR'];
$maquina       = substr( $ip, strrpos( $ip, '.' ) + 1 );

$motivo_pag    = 'IMPRESSÃO DE ORDEM DE SAÍDA - PDF';

if ( $imp_cadastro < 1 and $imp_chefia < 1 ) {

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

$is_post = is_post();
if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );

    exit;

}

extract( $_POST, EXTR_OVERWRITE );

$id_ord_saida = empty( $id_ord_saida ) ? '' : (int)$id_ord_saida;
//$id_ord_saida = get_get( 'id_ord_saida', 'int' );
if ( empty( $id_ord_saida ) ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Identificador da ordem de saída em branco ou inválido ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );

    exit;

}

$q_ord_saida = "SELECT
                  `ordens_saida_det`.`cod_detento`,
                  `ordens_saida_det`.`id_ord_saida_det`,
                  `ordens_saida_locais`.`id_local_ord_saida`,
                  DATE_FORMAT( `ordens_saida_locais`.`local_hora`, '%H:%i' ) AS `local_hora_f`,
                  `ordens_saida_tipo`.`tipo`,
                  `locais_apr`.`local_apr`,
                  `locais_apr`.`local_end`,
                  `detentos`.`nome_det`,
                  `detentos`.`matricula`,
                  `detentos`.`rg_civil`,
                  `detentos`.`pai_det`,
                  `detentos`.`mae_det`,
                  `tipoartigo`.`artigo`,
                  `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
                  `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
                  `unidades_out`.`idunidades` AS iddestino,
                  `cela`.`cela`,
                  `raio`.`raio`
                FROM
                  `ordens_saida`
                  INNER JOIN `ordens_saida_locais` ON `ordens_saida_locais`.`cod_ord_saida` = `ordens_saida`.`id_ord_saida`
                  LEFT JOIN `ordens_saida_det` ON `ordens_saida_det`.`cod_local_ord_saida` = `ordens_saida_locais`.`id_local_ord_saida`
                  LEFT JOIN `ordens_saida_tipo` ON `ordens_saida_tipo`.`id_tipo` = `ordens_saida_det`.`cod_tipo`
                  LEFT JOIN `detentos` ON `ordens_saida_det`.`cod_detento` = `detentos`.`iddetento`
                  LEFT JOIN `locais_apr` ON `ordens_saida_locais`.`cod_local` = `locais_apr`.`idlocal`
                  LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                  LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                  LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                  LEFT JOIN `tipoartigo` ON `detentos`.`cod_artigo` = `tipoartigo`.`idartigo`
                  LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                  LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
                WHERE
                  `ordens_saida`.`id_ord_saida` = $id_ord_saida
                ORDER BY
                  `locais_apr`.`local_apr`, `detentos`.`nome_det`";

$db = SicopModel::getInstance();
$q_ord_saida = $db->query( $q_ord_saida );

if ( !$q_ord_saida ) {

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();
    $db->closeConnection();

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Falha na consulta ( $motivo_pag ).\n\n $msg_err_mysql";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$cont_ord_saida = $q_ord_saida->num_rows;
if ( $cont_ord_saida < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "A consulta retornou 0 ocorrências ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 'f' );
    exit;

}

$q_data_ord_saida = "SELECT
                       DATE_FORMAT( `ordens_saida`.`ord_saida_data`, '%d/%m/%Y' ) AS ord_saida_data_f,
                       DATE_FORMAT( `ordens_saida`.`ord_saida_hora`, '%H:%i' ) AS `ord_saida_hora_f`,
                       `ordens_saida`.`finalidade`,
                       `ordens_saida`.`responsavel_escolta`,
                       `ordens_saida`.`retorno`
                     FROM
                       `ordens_saida`
                     WHERE
                       `ordens_saida`.`id_ord_saida` = $id_ord_saida";

$q_data_ord_saida = $db->query( $q_data_ord_saida );

if ( !$q_data_ord_saida ) {

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();
    $db->closeConnection();

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Falha na consulta ( $motivo_pag ).\n\n $msg_err_mysql";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$cont = $q_data_ord_saida->num_rows;
if ( $cont < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "A consulta retornou 0 ocorrências ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 'f' );
    exit;

}

$d_data_ord_saida    = $q_data_ord_saida->fetch_assoc();

$data_ord_saida_f    = $d_data_ord_saida['ord_saida_data_f'];
$hora_ord_saida_f    = $d_data_ord_saida['ord_saida_hora_f'];
$finalidade          = empty( $d_data_ord_saida['finalidade'] ) ? 'ATENDIMENTO EXTERNO' : $d_data_ord_saida['finalidade'];
$responsavel_escolta = $d_data_ord_saida['responsavel_escolta'];
$retorno             = empty( $d_data_ord_saida['retorno'] ) ? false : true ;

$query_ds = "SELECT
               `diretor`,
               `titulo_diretor`
             FROM
               `diretores_n`
             WHERE
               `iddiretoresn` = $diretor_s
             LIMIT 1";

$query_ds = $db->query( $query_ds );

if ( !$query_ds ) {

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();
    $db->closeConnection();

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Falha na consulta ( $motivo_pag ).\n\n $msg_err_mysql";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$cont = $query_ds->num_rows;
if ( $cont < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "A consulta retornou 0 ocorrências ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 'f' );
    exit;

}

$d_ds = $query_ds->fetch_assoc();

// montar a mensagem q será salva no log
$msg = array();
$msg['tipo'] = 'desc';
$msg['entre_ch'] = $motivo_pag;
$msg['text'] = "Impressão de ordem de saida. \n\n <b>ID:</b> $id_ord_saida\n <b>Data do pedido:</b> $data_ord_saida_f";

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
$pdf->SetTitle( 'Ordem de Saída' );
$pdf->SetSubject( 'Ordem de Saída' );
$pdf->SetKeywords( SICOP_SYS_KW );



// set default monospaced font
$pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );

//set margins
$pdf->SetMargins( 20, PDF_MARGIN_TOP, 20 );
$pdf->SetHeaderMargin( PDF_MARGIN_HEADER );
$pdf->SetFooterMargin( PDF_MARGIN_FOOTER );

//set auto page breaks
//$pdf->SetAutoPageBreak( TRUE, PDF_MARGIN_BOTTOM );
$pdf->SetAutoPageBreak( TRUE, 15 );

//set image scale factor
$pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );

// desabilitar o rodapé
//$pdf->setPrintFooter(false);

//set some language-dependent strings
$pdf->setLanguageArray( $l );

// ---------------------------------------------------------

$pdf->SetDisplayMode( 'fullwidth', 'continuous', 'UseNone' );

// instrui a colocar os números de página no rodapé
define( 'ADD_NUM_PAG', TRUE );

// adicionar uma página
$pdf->AddPage();

// configurar a fonte
$pdf->SetFont( $font, 'B', 14 );

$pdf->Cell( 0, '', 'ORDEM DE SAÍDA', 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

// configurar a fonte
$pdf->SetFont( $font, '', 9.5 );
$txt = $cidade . ', ' . data_f();

$pdf->Ln();

$pdf->Cell( 0, '', $txt, 0, 1, 'R', 0, '', 0, false, 'T', 'M' );

//$pdf->Ln( 34 );
$pdf->Ln();

$txt = PAR_INDET_INI . "Autorizo " . SICOP_DET_ART_L . "(s) " . SICOP_DET_DESC_L . "(s) abaixo a deixar(em) esta Unidade, com destino
        aos locais a seguir delineado(s), devidamente escoltad" . SICOP_DET_ART_L . "(s), observando
        as cautelas de praxe, na data de
        <b>$data_ord_saida_f</b>";

if ( !empty ( $hora_ord_saida_f ) ) $txt .= " <b>às $hora_ord_saida_f</b>";

$txt .= '.' . PAR_INDET_FIM;

$pdf->writeHTML( $txt, true, 0, true, true );

$pdf->Cell( 18, '', 'Finalidade: ', 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

// configurar a fonte
$pdf->SetFont( $font, 'B', 9.5 );

$pdf->Cell( 0, '', $finalidade, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );


$pdf->Ln( 3 );

// configurar a fonte
$pdf->SetFont( $font, 'B', 9.5 );

$i        = 0;               // incremento
$total    = $cont_ord_saida; // total de detentos
$dest_ant = '';              // destino anterior, para montar as localidades
$i_locais = 0;
$y_limit  = 275;             // limite do fim da página

while( $d_ord_saida = $q_ord_saida->fetch_assoc() ) {

    ++$i;

    $cur_y    = (int)$pdf->GetY();
    $add_page = FALSE;

    /**
     * diferença para calular a quebra de página
     * se for a última ocorrencia, a quebra é maior,
     * para caber a assinatura do diretor
     */
    $dif = 27;
    if ( $i == $total ) {
        $dif = 55;
    }

    if ( ( $y_limit - $cur_y ) < $dif ) {
        $add_page = TRUE;
    }

    if ( $add_page ) {

        $pdf->add_page_continue();

    }

    $quebra_local = FALSE;
    if ( $d_ord_saida['local_apr'] != $dest_ant ){
        $quebra_local = TRUE;
        ++$i_locais;
    }

    if ( $quebra_local ) {

        $pdf->Ln( 3 );

        // configurar a fonte
        $pdf->SetFont( $font, 'B', 9.5 );

        $txt = '*** ' . $d_ord_saida['local_apr'];
        if ( !empty ( $d_ord_saida['local_hora_f'] ) ) $txt .= ' ( ' . $d_ord_saida['local_hora_f'] . ' )';
        $txt .= ':';

        $pdf->Cell( 0, '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

        if ( !empty( $d_ord_saida['local_end'] ) ) {

            // configurar a fonte
            $pdf->SetFont( $font, 'N', 7 );

            $txt = 'End: ' . $d_ord_saida['local_end'];
            $pdf->Cell( 0, '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

        }

    }

    $pdf->add_det_os( $d_ord_saida, $i );

    $cur_y = $pdf->GetY();
    $pdf->SetY( $cur_y + 0.9 );

    $dest_ant = $d_ord_saida['local_apr'];

} // /while( $d_ord_saida = $q_ord_saida->fetch_assoc() ) {



// CONFIGURAR O FIM DA LISTA

$pdf->ln( 3 );

// CONFIGURAR A FONTE
$pdf->SetFont( $font, 'BI', 9.5 );
// CONFIGURAR A BORDA
$border = array('TB' => array('width' => 0.4, 'dash' => 4, ));
$txt    = '*** FIM DA LISTA ***';
$pdf->Cell( 0, 6, $txt, $border, 1, 'C', 0, '', 0, false, 'T', 'M' );

//-------------------------------------------


if ( !empty ( $responsavel_escolta ) ) {

    $cur_y = $pdf->GetY();
    $pdf->SetY( $cur_y + 1 );

    // CONFIGURAR A FONTE
    $pdf->SetFont( $font, 'B', 9.5 );

    $pdf->Cell( 63, 0, 'Responsável pela condução e escolta: ', 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

    // CONFIGURAR A FONTE
    $pdf->SetFont( $font, 'N', 9.5 );
    $pdf->Cell( '', 0, $responsavel_escolta, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

} // /if ( !empty ( $responsavel_escolta ) ) {


if ( $retorno ) {

    if ( empty ( $responsavel_escolta ) ) {
        $cur_y = $pdf->GetY();
        $pdf->SetY( $cur_y + 1 );
    }

    // CONFIGURAR A FONTE
    $pdf->SetFont( $font, 'B', 9.5 );

    $pdf->Cell( 9, 0, 'Obs: ', 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

    // CONFIGURAR A FONTE
    $pdf->SetFont( $font, 'N', 9.5 );
    $pdf->Cell( '', 0, SICOP_DET_ART_U . 's ' . SICOP_DET_DESC_L . 's deverão retornar a esta unidade logo após o término dos atendimentos.', 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

}


$pdf->ln( 12 );

// ASSINATURA DO DIRETOR

// CONFIGURAR A FONTE
$pdf->SetFont( $font, 'I', 9.5 );

$pdf->Cell( '', 0, $d_ds['diretor'], 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

// CONFIGURAR A FONTE
$pdf->SetFont( $font, 'N', 9.5 );

$pdf->Cell( '', 0, $d_ds['titulo_diretor'], 0, 0, 'C', 0, '', 0, false, 'T', 'M' );

//-------------------------------------------


// reset pointer to the last page
$pdf->lastPage();
// ---------------------------------------------------------

$data_ord_saida_f    = str_replace( '/', '_', $data_ord_saida_f );
$hora_ord_saida_f    = str_replace( ':', '_', $hora_ord_saida_f );

$data_hora = $data_ord_saida_f . '_' . $hora_ord_saida_f;

//Close and output PDF document
$pdf->Output( "ordem_saida_$data_hora.pdf", 'I' );

//============================================================+
// END OF FILE
//============================================================+
?>