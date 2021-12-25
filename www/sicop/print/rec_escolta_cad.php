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
$sigla_setor   = get_session( 'sigla_setor' );
$titulo        = get_session ( 'titulo' );
$cidade        = get_session( 'cidade' );
$iduser        = get_session( 'user_id', 'int' );
$ip            = $_SERVER['REMOTE_ADDR'];
$maquina       = substr( $ip, strrpos( $ip, '.' ) + 1 );

$motivo_pag    = 'IMPRESSÃO DE PEDIDO DE ESCOLTA - PDF';

if ( $imp_cadastro < 1 and $imp_chefia < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 'f' );

    exit;

}

$iddet_s = empty( $_SESSION['iddet'] ) ? '' : $_SESSION['iddet'];

$unidade_dest = empty( $_SESSION['rec_cad_unidade'] ) ? '_______________________' : $_SESSION['rec_cad_unidade'];

if ( isset( $_SESSION['iddet'] ) ) unset( $_SESSION['iddet'] );

if ( isset( $_SESSION['rec_cad_unidade'] ) ) unset( $_SESSION['rec_cad_unidade'] );

if ( empty( $iddet_s ) ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );

    exit;

}

$query = "SELECT
            `detentos`.`iddetento`,
            `detentos`.`nome_det`,
            `detentos`.`matricula`,
            `detentos`.`rg_civil`,
            `detentos`.`execucao`,
            `detentos`.`vulgo`,
            `detentos`.`pai_det`,
            `detentos`.`mae_det`,
            `tipoartigo`.`artigo`,
            `cidades`.`nome` AS cidade,
            `estados`.`sigla` AS estado,
            `unidades_in`.`unidades` AS procedencia,
            `det_fotos`.`foto_det_g`,
            `det_fotos`.`foto_det_p`
          FROM
            `detentos`
            LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
            LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
            LEFT JOIN `tipoartigo` ON `detentos`.`cod_artigo` = `tipoartigo`.`idartigo`
            LEFT JOIN `cidades` ON `detentos`.`cod_cidade` = `cidades`.`idcidade`
            LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado`
            LEFT JOIN `det_fotos` ON `det_fotos`.`id_foto` = `detentos`.`cod_foto`
          WHERE

           `detentos`.`iddetento` IN( $iddet_s )
          ORDER BY
            `detentos`.`nome_det`";

/*,21,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47    21 ,27,28,29, 11178, 612, 4287     $iddet -- `detentos`.`iddetento` IN( $iddet ) */

$db = SicopModel::getInstance();
$query = $db->query( $query );

if ( !$query ) {

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

$cont = $query->num_rows;
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

// montar a mensagem q será salva no log
$msg = array();
$msg['tipo'] = 'desc';
$msg['entre_ch'] = $motivo_pag;
$msg['text'] = "Impressão de recibo de escolta - CADASTRO.";

get_msg( $msg, 1 );

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
$pdf->SetTitle( 'Recibo de escolta - CADASTRO' );
$pdf->SetSubject( 'Recibo de escolta - CADASTRO' );
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

$pdf->Cell( 0, '', 'RECIBO DE SENTENCIADOS', 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

// configurar a fonte
$pdf->SetFont( $font, '', 9.5 );
$txt = $cidade . ', ' . data_f();

$pdf->Ln();

$pdf->Cell( 0, '', $txt, 0, 1, 'R', 0, '', 0, false, 'T', 'M' );

$pdf->Ln();

// configurar a fonte
$pdf->SetFont( $font, 'B', 9.5 );

$i        = 0;               // incremento
$total    = $cont;           // total de detentos
$destino  = '';              // destino
$i_locais = 0;
$y_limit  = 260;             // limite do fim da página

while( $d_det = $query->fetch_assoc() ) {

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

    $pdf->add_det_foto( $d_det, $i );

    $cur_y = $pdf->GetY();
    $pdf->SetY( $cur_y + 1 );

} // /while( $d_det = $q_ord_saida->fetch_assoc() ) {



// CONFIGURAR O FIM DA LISTA
$pdf->ln( 3 );

// ADICIONAR O *** FIM DA LISTA ***
$pdf->add_end_list();


//-------------------------------------------

$cur_y    = $pdf->GetY();
$dif      = 60;
$add_page = FALSE;
if ( ( $y_limit - $cur_y ) < $dif ) {
    $add_page = TRUE;
}

if ( $add_page ) {

    // adicionar uma página
    $pdf->AddPage();

    $pdf->ln( 3 );

}

if ( !$add_page ) {
    $pdf->SetY( $cur_y + 2 );
}

$pdf->ln( 12 );

// DESTINATÁRIO

// configurar a fonte
$pdf->SetFont( $font, 'B', 9.5 );

$y_1linha = $pdf->GetY();

$pdf->Cell( '', 0, 'UNIDADE DE DESTINO: ', 0, 0, 'L', 0, '', 0, false, 'T', 'M' );
$pdf->ln( 6 );
$pdf->Cell( '', 0, $unidade_dest, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

$pdf->SetY( $y_1linha );
$pdf->SetX( 120 );
$pdf->Cell( '', 0, 'RECEBI EM: _____/_____/__________', 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

$pdf->ln( 6 );

$pdf->SetX( 120 );
$pdf->Cell( '', 0, 'NOME (LEGÍVEL): ____________________', 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

$pdf->ln( 6 );

$pdf->SetX( 120 );
$pdf->Cell( '', 0, 'RG: ____________________', 0, 0, 'L', 0, '', 0, false, 'T', 'M' );


//-------------------------------------------

//$fim_y = $pdf->GetY();
//$pdf->Cell( '', 0, $cur_y, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );
//$pdf->Cell( '', 0, $fim_y, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

// reset pointer to the last page
$pdf->lastPage();
// ---------------------------------------------------------


//$data_esc_f = str_replace( '/', '_', $data_esc_f );
//$hora_esc_f = str_replace( ':', '_', $hora_esc_f );

$data_hora = ''; // $data_esc_f . '_' . $hora_esc_f;

//Close and output PDF document
$pdf->Output( "ordem_escolta_$data_hora.pdf", 'I' );

//============================================================+
// END OF FILE
//============================================================+
?>