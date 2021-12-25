<?php

/**
 * Impressão de documentos de detento de detento em PDF
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
$a = $sys->ckPost( 3 );
//echo $a;
//
//echo $_POST['iddet'];
//
//exit;

$op = array(
    'method'         => 'post',       // metodo que a variável será recebida
    'name'           => 'iddet',      // nome da variável
    'modo_validacao' => 'int',        // modo de validação
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

$op = array(
    'method'         => 'post',       // metodo que a variável será recebida
    'name'           => 'incl',       // nome da variável
    'modo_validacao' => 'int',        // modo de validação
);
$incl = $sys->validate( $op );

$op = array(
    'method'         => 'post',       // metodo que a variável será recebida
    'name'           => 'quant',      // nome da variável
    'modo_validacao' => 'int',        // modo de validação
);
$quant_quali = $sys->validate( $op );

$iduser   = $sys->getSession( 'user_id', 'int' );
$iniciais = $sys->getSession( 'iniciais' );
$cidade   = $sys->getSession( 'cidade' );

$detento = new Detento( $iddet );

$servidor = '';
if ( !empty( $incl ) ) {

    $servidor = $iniciais;

    $detento->upFunc( $iduser );

}

$d_det = '';
$desc_doc = '';

if ( $type == 'cartao' ){

    $d_det    = $detento->findCartao();
    $desc_doc = 'CARTÃO DE IDENTIFICAÇÃO';

} else if ( $type == 'plan' ){

    $d_det    = $detento->findIdent();
    $desc_doc = 'PLANILHA DE IDENTIFICAÇÃO';

} else { // se for quali ou all

    $d_det       = $detento->findQuali();
    $desc_doc    = 'FICHA QUALIFICATIVA';
    $quant_quali = !empty( $quant_quali ) ?  $quant_quali : 1;

    if ( $type == 'all' ){
        $desc_doc    = 'DOCUMENTOS DO DETENTO';
        $quant_quali = 3;
    }



}

//$d_det = $detento->findQuali();

if ( !$d_det ) {

    echo $sys->msgJS( 'FALHA!!!', 'f' );
    exit;

}


// pegar os dados do preso
$d_det_f = $detento->dadosDetF( $iddet );

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

if ( $type == 'quali' or $type == 'all' ){

    $i = 0;
    $limit = $quant_quali;

    for ( $i = 0; $i < $limit; $i++ ) {

        //set margins - left, top and right
        $pdf->SetMargins( 10, 35, 10 );

        // adicionar uma página
        $pdf->AddPage();

        // pegando a qualificativa do detento
        $pdf->getQuali( $d_det, $servidor );

        // quebra de linha
        $pdf->Ln();

        // pegando a planilha de digitais
        $pdf->getDigital(1);

    }


}

if ( $type == 'plan' or $type == 'all' ){

    //set margins - left, top and right
    $pdf->SetMargins( 10, 35, 10 );

    // adicionar uma página
    $pdf->AddPage();

    // pegando a ficha de identificação do detento
    $pdf->getFichaIdent( $d_det );

    // quebra de linha
    $pdf->Ln();

    // pegando a planilha de digitais
    $pdf->getDigital( 1, 0 );

    // pegando a planilha de digitais
    $pdf->getDigital( 2, 0 );

}

if ( $type == 'cartao' or $type == 'all' ){

    //set margins - left, top and right
    $pdf->SetMargins( 10, 10, 10 );

    // primeiro, removemos o header
    $pdf->setPrintHeader(false);

    // em seguida, adicionar uma página
    $pdf->AddPage();

    // só ai, remover o footer, para q não seja removido da página anterior
    $pdf->setPrintFooter(false);

    // se o tipo for cartão, os dados retornam de um array
    if ( $type == 'cartao' ){
        $d_det = $d_det[0];
    }

    // pegando a ficha de identificação do detento
    $pdf->getCartaoIdent( $d_det );

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