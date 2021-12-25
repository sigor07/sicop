<?php

/**
 * Impressão de documentos de detento multiplos de detento em PDF
 *
 * @author Rafael
 * @since 29/02/2012
 *
 * ****************************************************************************
 *
 * SICOP - Sistema de Controle de Prisional
 *
 * Sistema para controle e gerenciamento de unidades prisionais
 *
 * @author  JOSÉ RAFAEL GONÇALVES - AGENTE DE SEGURANÇA III
 * @local   CENTRO DE DETENÇÃO PROVISÓRIA DE SÃO JOSÉ DO RIO PRETO - SP
 * @since   03/01/2011
 *
 * ****************************************************************************
 */

require '../init/config.php';

// instanciando a classe
$sys = new SicopController();

// checando se o sistema esta ativo
$sys->ckSys();


// instanciando a classe
$user = new userAutController();

// validando o usuário e o nível de acesso
$arr_perm = array(
    'imp_incl',
    'imp_det'
);
$user->validateUser( $arr_perm, 1, 'af', 7 );

// checando se o acesso foi via post
$sys->ckPost( 3 );

$op = array(
    'method'         => 'post',       // metodo que a variável será recebida
    'name'           => 'iddet',      // nome da variável
    'minLeng'        => 1,            // comprimento mínimo
    'required'       => true,         // se é requerida ou não
    'zero_ok'        => false,        // em caso de requerida, se pode ser o número 0 (zero)
    'return_type'    => 3             // tipo de retorno em caso de erro
);
$iddet = $sys->validate( $op );

$op = array(
    'method'         => 'post',       // metodo que a variável será recebida
    'name'           => 'type',       // nome da variável
    'minLeng'        => 1,            // comprimento mínimo
    'required'       => true,         // se é requerida ou não
    'zero_ok'        => false,        // em caso de requerida, se pode ser o número 0 (zero)
    'return_type'    => 3             // tipo de retorno em caso de erro
);
$type = $sys->validate( $op );

// transformando em array
$iddet_in = explode( ',', $iddet );

$detento = new Detento();

$d_det = '';
$desc_doc = '';

if ( $type == 'cartao' ){

    $d_det    = $detento->findCartao( $iddet_in );
    $desc_doc = 'CARTÃO DE IDENTIFICAÇÃO';

} else if ( $type == 'plan' ){

    $d_det    = $detento->findIdentM( $iddet_in );
    $desc_doc = 'PLANILHA DE IDENTIFICAÇÃO';

} else { // quali

    $d_det       = $detento->findQualiM( $iddet_in );
    $desc_doc    = 'FICHA QUALIFICATIVA';

}

//$d_det = $detento->findQuali();

if ( !$d_det ) {

    echo $sys->msgJS( 'FALHA!!!', 'f' );
    exit;

}


// pegar os dados do preso
$d_det_f = $detento->dadosDetFM( $iddet_in );

// montar a mensagem q será salva no log
$msg = sysmsg::create_msg();
$msg->add_chaves( "IMPRESSÃO DE $desc_doc - PDF", 0, 2 );
$msg->set_msg( $d_det_f );
$msg->get_msg();


require_once('classes/tcpdf/pdfquali.php');

// definir a fonte
$font = 'helvetica';

// create new PDF document
$pdf = new PDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );

$pdf->setFontSubsetting(false);

// set document information PDF_CREATOR
$pdf->SetCreator( SICOP_SYS_NAME );
$pdf->SetAuthor( SICOP_SYS_NAME );
$pdf->SetTitle( $desc_doc );
$pdf->SetSubject( $desc_doc );
$pdf->SetKeywords( SICOP_SYS_KW );

// set default monospaced font
$pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );

// margens do cabeçalho e rodapé
$pdf->SetHeaderMargin( PDF_MARGIN_HEADER );
$pdf->SetFooterMargin( PDF_MARGIN_FOOTER );

//set auto page breaks
$pdf->SetAutoPageBreak( TRUE, 15 );

//set image scale factor
$pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );

//set some language-dependent strings
//$pdf->setLanguageArray( $l );
//$pdf->setLanguageArray();

// ---------------------------------------------------------

$pdf->SetDisplayMode( 'fullwidth', 'continuous', 'UseNone' );

if ( $type == 'quali' ){

    //set margins - left, top and right
    $pdf->SetMargins( 10, 35, 10 );

    foreach ( $d_det as &$value ) {

        // adicionar uma página
        $pdf->AddPage();

        // pegando a ficha de identificação do detento
        $pdf->getQuali( $value );

        // quebra de linha
        $pdf->Ln();

        // pegando a planilha de digitais
        $pdf->getDigital(1);


    }

}

if ( $type == 'plan' ){

    //set margins - left, top and right
    $pdf->SetMargins( 10, 35, 10 );

    foreach ( $d_det as &$value ) {

        // adicionar uma página
        $pdf->AddPage();

        // pegando a ficha de identificação do detento
        $pdf->getFichaIdent( $value );

        // quebra de linha
        $pdf->Ln();

        // pegando a planilha de digitais
        $pdf->getDigital( 1, 0 );

        // pegando a planilha de digitais
        $pdf->getDigital( 2, 0 );

    }

}

if ( $type == 'cartao' ){

    //set margins - left, top and right
    $pdf->SetMargins( 10, 10, 10 );

    // removemos o header
    $pdf->setPrintHeader(false);

    // remover o footer
    $pdf->setPrintFooter(false);

    // adicionar uma página
    $pdf->AddPage();

    $i = 0;
    foreach ( $d_det as &$value ) {

        $i++;

        // pegando a ficha de identificação do detento
        $pdf->getCartaoIdent( $value );

        if ( ( ( $i % 2 ) == 0 ) and ( ( $i % 10 ) != 0 ) ) {
            // quebra de linha
            $pdf->Ln(54);
        }

        if ( ( $i % 10 ) == 0 ) {
            // adicionar uma página
            $pdf->AddPage();
        }


    }

}

// reset pointer to the last page
$pdf->lastPage();
// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output( 'det_docs.pdf', 'I' );

//============================================================+
// END OF FILE
//============================================================+
?>