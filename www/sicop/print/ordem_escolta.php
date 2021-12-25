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
$diretor_g     = get_session( 'diretor_geral', 'int' );
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

$idescolta = empty( $idescolta ) ? '' : (int)$idescolta;
//$idescolta = get_get( 'idescolta', 'int' );
if ( empty( $idescolta ) ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Identificador da escolta em branco ou inválido ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );

    exit;

}

$q_esc = "SELECT
            `ordens_escolta_det`.`cod_detento`,
            `ordens_escolta_det`.`id_escolta_det`,
            DATE_FORMAT( `ordens_escolta_det`.`hora`, '%H:%i' ) AS `aud_hora_f`,
            `ordens_escolta_locais`.`id_local_escolta`,
            DATE_FORMAT( `ordens_escolta_locais`.`local_hora`, '%H:%i' ) AS `local_hora_f`,
            `ordens_escolta_tipo`.`tipo`,
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
            `unidades_out`.`idunidades` AS iddestino
          FROM
            `ordens_escolta`
            INNER JOIN `ordens_escolta_locais` ON `ordens_escolta_locais`.`cod_escolta` = `ordens_escolta`.`idescolta`
            LEFT JOIN `ordens_escolta_det` ON `ordens_escolta_det`.`cod_local_escolta` = `ordens_escolta_locais`.`id_local_escolta`
            LEFT JOIN `ordens_escolta_tipo` ON `ordens_escolta_tipo`.`id_tipo` = `ordens_escolta_det`.`cod_tipo`
            LEFT JOIN `detentos` ON `ordens_escolta_det`.`cod_detento` = `detentos`.`iddetento`
            LEFT JOIN `locais_apr` ON `ordens_escolta_locais`.`cod_local` = `locais_apr`.`idlocal`
            LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
            LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
            LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
            LEFT JOIN `tipoartigo` ON `detentos`.`cod_artigo` = `tipoartigo`.`idartigo`
          WHERE
            `ordens_escolta`.`idescolta` = $idescolta
          ORDER BY
            `locais_apr`.`local_apr`, `detentos`.`nome_det`";

$db = SicopModel::getInstance();
$q_esc = $db->query( $q_esc );

if ( !$q_esc ) {

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

$cont_q_esc = $q_esc->num_rows;
if ( $cont_q_esc < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "A consulta retornou 0 ocorrências ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 'f' );
    exit;

}

$q_data_esc = "SELECT
                 `ordens_escolta`.`cod_num_of`,
                 DATE_FORMAT( `ordens_escolta`.`escolta_data`, '%d/%m/%Y' ) AS escolta_data_f,
                 DATE_FORMAT( `ordens_escolta`.`escolta_hora`, '%H:%i' ) AS `escolta_hora_f`,
                 `ordens_escolta`.`finalidade`
               FROM
                 `ordens_escolta`
               WHERE
                 `ordens_escolta`.`idescolta` = $idescolta";

$q_data_esc = $db->query( $q_data_esc );

if ( !$q_data_esc ) {

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

$cont = $q_data_esc->num_rows;
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

$d_data_esc = $q_data_esc->fetch_assoc();

$data_esc_f = $d_data_esc['escolta_data_f'];
$hora_esc_f = $d_data_esc['escolta_hora_f'];
$finalidade = empty( $d_data_esc['finalidade'] ) ? 'ATENDIMENTO EXTERNO' : $d_data_esc['finalidade'];
$cod_num_of = $d_data_esc['cod_num_of'];

$num_of = '';
$num = new NumeroOf();

if ( empty( $cod_num_of ) ) {

    // comentário do número do ofício
    $coment = "[ OFÍCIOS DE SOLICITAÇÃO DE ESCOLTA ]\n\n Solicitação de escolta para apresentação judicial.";

    // pegando o número do ofício
    $num_of = $num->getNewNum( $coment );

    $cod_num_of = $num->__get( '_nid' );

    $q_up = "UPDATE
               `ordens_escolta`
             SET
               `ordens_escolta`.`cod_num_of` = $cod_num_of
             WHERE
               `ordens_escolta`.`idescolta` = $idescolta";

    $q_up = $db->query( $q_up );
    if ( !$q_up ) {

        // pegar a mensagem de erro mysql
        $msg_err_mysql = $db->getErrorMsg();
        $db->closeConnection();

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Falha na consulta de atualização do número de ofício ( $motivo_pag ).\n\n $msg_err_mysql";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( '', 'f' );
        exit;

    }

} else {

    // pegando o número do ofício
    $num_of = $num->findNum( $cod_num_of );

}

$query_dg = "SELECT
               `diretor`,
               `titulo_diretor`
              FROM
                `diretores_n`
              WHERE
                `iddiretoresn` = $diretor_g
              LIMIT 1";

$query_dg = $db->query( $query_dg );

if ( !$query_dg ) {

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

$contdg = $query_dg->num_rows;
if ( $contdg < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "A consulta retornou 0 ocorrências ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 'f' );
    exit;

}

$d_dg = $query_dg->fetch_assoc();

// montar a mensagem q será salva no log
$msg = array();
$msg['tipo'] = 'desc';
$msg['entre_ch'] = $motivo_pag;
$msg['text'] = "Impressão de pedido de escolta. \n\n <b>ID:</b> $idescolta\n <b>Data da ordem:</b> $data_esc_f";

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
$pdf->SetTitle( 'Ofício de solicitação de escolta' );
$pdf->SetSubject( 'Ofício de solicitação de escolta' );
$pdf->SetKeywords( SICOP_SYS_KW );

// set default monospaced font
$pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );

//set margins - left, top and right
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
$pdf->SetFont( $font, 'N', 9.5 );

$txt = "Ofício nº $num_of - $sigla_setor - $iniciais";
$pdf->Cell( 0, '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

$pdf->Ln();

$txt = $cidade . ', ' . data_f();
$pdf->Cell( 0, '', $txt, 0, 1, 'R', 0, '', 0, false, 'T', 'M' );

$pdf->Ln();

$txt = PAR_INDET_INI . "Senhor Comandante, <br /><br />
       Solicito ESCOLTA ARMADA para conduzir e apresentar " . SICOP_DET_ART_L . "(s) " . SICOP_DET_DESC_L . "(s)
       abaixo, aos locais a seguir delineado(s), na data de
       <b>$data_esc_f</b>";

if ( !empty ( $hora_esc_f ) ) $txt .= " <b>às $hora_esc_f</b>";

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
$total    = $cont_q_esc;     // total de detentos
$dest_ant = '';              // destino anterior, para montar as localidades
$i_locais = 0;
$y_limit  = 275;             // limite do fim da página

while( $d_esc = $q_esc->fetch_assoc() ) {

    ++$i;

    $cur_y    = (int)$pdf->GetY();
    $add_page = FALSE;

    /**
     * diferença para calular a quebra de página
     * se for a última ocorrencia, a quebra é maior,
     * para caber a assinatura do diretor
     */
    $dif = 27;
    //if ( $i == $total ) {
    //    $dif = 55;
    //}

    if ( ( $y_limit - $cur_y ) < $dif ) {
        $add_page = TRUE;
    }

    if ( $add_page ) {

        $pdf->add_page_continue();

    }

    $quebra_local = FALSE;
    if ( $d_esc['local_apr'] != $dest_ant ){
        $quebra_local = TRUE;
        ++$i_locais;
    }

    if ( $quebra_local ) {

        $pdf->Ln( 3 );

        // configurar a fonte
        $pdf->SetFont( $font, 'B', 9.5 );

        $txt = '*** ' . $d_esc['local_apr'];
        if ( !empty ( $d_esc['local_hora_f'] ) ) $txt .= ' ( ' . $d_esc['local_hora_f'] . ' )';
        $txt .= ':';

        $pdf->Cell( 0, '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

        if ( !empty( $d_esc['local_end'] ) ) {

            // configurar a fonte
            $pdf->SetFont( $font, 'N', 7 );

            $txt = 'End: ' . $d_esc['local_end'];
            $pdf->Cell( 0, '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

        }

    }

    $pdf->add_det( $d_esc, $i );

    $cur_y = $pdf->GetY();
    $pdf->SetY( $cur_y + 0.9 );

    $dest_ant = $d_esc['local_apr'];

} // /while( $d_esc = $q_ord_saida->fetch_assoc() ) {



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

// CONFIGURAR A FONTE
$pdf->SetFont( $font, 'N', 9.5 );

$txt = PAR_INDET_INI . "Aproveitando o ensejo, renovo protestos de elevada estima e distinta consideração.
       <br /><br />
       Atenciosamente," . PAR_INDET_FIM;

$pdf->writeHTML( $txt, true, 0, true, true );


$pdf->ln( 12 );

// ASSINATURA DO DIRETOR

// CONFIGURAR A FONTE
$pdf->SetFont( $font, 'I', 9.5 );

$pdf->Cell( '', 0, $d_dg['diretor'], 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

// CONFIGURAR A FONTE
$pdf->SetFont( $font, 'N', 9.5 );

$pdf->Cell( '', 0, $d_dg['titulo_diretor'], 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

//-------------------------------------------

$pdf->ln( 12 );

// DESTINATÁRIO

$pdf->Cell( '', 0, 'A Sua Senhoria o Senhor', 0, 1, 'L', 0, '', 0, false, 'T', 'M' );
$pdf->Cell( '', 0, 'Comandante do 17º BPM – I de', 0, 1, 'L', 0, '', 0, false, 'T', 'M' );
$pdf->Cell( '', 0, 'SÃO JOSÉ DO RIO PRETO – SP', 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

//-------------------------------------------

//$fim_y = $pdf->GetY();
//$pdf->Cell( '', 0, $cur_y, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );
//$pdf->Cell( '', 0, $fim_y, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

// reset pointer to the last page
$pdf->lastPage();
// ---------------------------------------------------------


$data_esc_f = str_replace( '/', '_', $data_esc_f );
$hora_esc_f = str_replace( ':', '_', $hora_esc_f );

$data_hora = $data_esc_f . '_' . $hora_esc_f;

//Close and output PDF document
$pdf->Output( "ordem_escolta_$data_hora.pdf", 'I' );

//============================================================+
// END OF FILE
//============================================================+
?>