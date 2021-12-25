<?php

/**
 * Impressão de oficios de apresentação em PDF
 *
 * @author Rafael
 * @since 06/03/2012
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
$user->validateUser( 'imp_cadastro', 1, '', 7 );

// checando se o acesso foi via post
$sys->ckPost( 3 );

$op = array(
    'method'         => 'post',       // metodo que a variável será recebida
    'name'           => 'idaud',      // nome da variável
    'minLeng'        => 1,            // comprimento mínimo
    'required'       => true,         // se é requerida ou não
    'zero_ok'        => false,        // em caso de requerida, se pode ser o número 0 (zero)
    'return_type'    => 3             // tipo de retorno em caso de erro
);
$idaud = $sys->validate( $op );

// transformando em array
$idaud = explode( ',', $idaud );

$iduser      = $sys->getSession( 'user_id', 'int' );
$sigla_setor = $sys->getSession( 'sigla_setor' );
$iniciais    = $sys->getSession( 'iniciais' );
$cidade      = $sys->getSession( 'cidade' );


$aud = new Audiencia();

$d_aud = $aud->findAudPrint( $idaud );

//$d_det = $detento->findQuali();

if ( !$d_aud ) {

    echo $sys->msgJS( 'FALHA!!!', 'f' );
    exit;

}


require_once('classes/tcpdf/pdf.php');

// definir a fonte
$font = 'helvetica';

// create new PDF document
$pdf = new PDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );

$pdf->setFontSubsetting(false);

$desc_doc = 'Ofícios de apresentação de detentos';

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

//set margins - left, top and right
$pdf->SetMargins( 20, PDF_MARGIN_TOP, 20 );

// configurar a fonte
$pdf->SetFont( $font, 'N', 9.5 );


$diretor_g = $sys->getSession( 'diretor_geral' );

$diretor = new Diretor( $diretor_g );

$dg = $diretor->findDiretor();



// data
$data = $cidade . ', ' . $sys->dataF();

$num = new NumeroOf();
$det = new Detento();

$det_pieces = array();

foreach ( $d_aud as $value ) {

    $det_pieces[] = $value->cod_detento;

    // adicionar uma página
    $pdf->AddPage();

    // adicionando uma quebra de linha
    //$pdf->Ln(5);

    $num_of = '';
    if ( empty( $value->cod_num_of ) ) {

        // comentário do número do ofício
        $detento = $det->dadosDetF( $value->cod_detento );
        $coment = "[ OFÍCIOS DE APRESENTAÇÃO DE DETENTO ]\n\n $detento";

        // pegando o número do ofício
        $num_of = $num->getNewNum( $coment );

        $cod_of = $num->__get( '_nid' );

        $aud->upCodOf( $value->idaudiencia, $cod_of );

    } else {

        // pegando o número do ofício
        $num_of = $num->findNum( $value->cod_num_of );

    }

    // escrevendo o número do ofício
    $txt = "Ofício nº $num_of  - $sigla_setor  - $iniciais";
    $pdf->Cell( 0, '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

    // adicionando uma quebra de linha
    $pdf->Ln();
    $pdf->Ln();

    // escrevendo a data
    $pdf->Cell( 0, '', $data, 0, 1, 'R', 0, '', 0, false, 'T', 'M' );

    // adicionando uma quebra de linha
    $pdf->Ln();
    $pdf->Ln();

    $d_of_model = $aud->findOfModel( $value->tipo_aud );

    // pegando a ficha de identificação do detento
    $pdf->getOfApr( $value, $d_of_model, $diretor );


}

$dets = implode( ',', $det_pieces );

// pegar os dados do preso
$d_det_f = $det->dadosDetFM( $dets );

// montar a mensagem q será salva no log
$msg = sysmsg::create_msg();
$msg->add_chaves( 'IMPRESSÃO DE OFÍCIOS DE APRESENTAÇÃO - PDF', 0, 2 );
$msg->set_msg( $d_det_f );
$msg->get_msg();

// reset pointer to the last page
$pdf->lastPage();
// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output( 'det_docs.pdf', 'I' );

//============================================================+
// END OF FILE
//============================================================+
?>