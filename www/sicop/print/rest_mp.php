<?php

/**
 * Impressão de restituição de mandado em PDF
 *
 * @author Rafael
 * @since 19/04/2012
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
$user->validateUser( 'imp_pront', 1, '', 7 );

// checando se o acesso foi via post
$sys->ckPost( 3 );

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

$tipo_rest = empty( $_POST['tipo_rest'] ) ? 1 : (int)$_POST['tipo_rest'];
$sit_alv   = empty( $_POST['sit_alv'] ) ? 1 : (int)$_POST['sit_alv'];

$op = array(
    'method'         => 'post',       // metodo que a variável será recebida
    'name'           => 'referente',  // nome da variável
    'modo_validacao' => 'string',     // modo de validação
    'minLeng'        => 1,            // comprimento mínimo
    'required'       => true,         // se é requerida ou não
    'zero_ok'        => false,        // em caso de requerida, se pode ser o número 0 (zero)
    'return_type'    => 3             // tipo de retorno em caso de erro
);
$referente = $sys->validate( $op );

$op = array(
    'method'         => 'post',       // metodo que a variável será recebida
    'name'           => 'dest',       // nome da variável
    'modo_validacao' => 'string',     // modo de validação
    'minLeng'        => 1,            // comprimento mínimo
    'required'       => true,         // se é requerida ou não
    'zero_ok'        => false,        // em caso de requerida, se pode ser o número 0 (zero)
    'return_type'    => 3             // tipo de retorno em caso de erro
);
$dest = $sys->validate( $op );

$op = array(
    'method'         => 'post',       // metodo que a variável será recebida
    'name'           => 'cidade',     // nome da variável
    'modo_validacao' => 'string',     // modo de validação
    'minLeng'        => 1,            // comprimento mínimo
    'required'       => true,         // se é requerida ou não
    'zero_ok'        => false,        // em caso de requerida, se pode ser o número 0 (zero)
    'return_type'    => 3             // tipo de retorno em caso de erro
);
$cidade_rest = $sys->validate( $op );

$iduser      = $sys->getSession( 'user_id', 'int' );
$sigla_setor = $sys->getSession( 'sigla_setor' );
$iniciais    = $sys->getSession( 'iniciais' );
$cidade      = $sys->getSession( 'cidade' );


require_once('classes/tcpdf/pdf.php');

// definir a fonte
$font = 'helvetica';

// altura padrão das células
$cell_h = 6;

// create new PDF document
$pdf = new PDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );

$pdf->setFontSubsetting(false);

$desc_doc = 'Restituição de mandados';

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

//$d_det = $det->findDetBasic( $iddet );

// adicionar uma página
$pdf->AddPage();

// comentário do número do ofício
$detento = $det->dadosDetF( $iddet );
$coment = "[ RESTITUIÇÃO DE MANDADOS ]\n\n $detento";

// pegando o número do ofício
$num_of = $num->getNewNum( $coment );


// montar a mensagem q será salva no log
$msg = sysmsg::create_msg();
$msg->add_chaves( 'IMPRESSÃO DE RESTITUIÇÃO DE MANDADOS - PDF', 0, 2 );
$msg->set_msg( "Impressão de restituição de mandados e alvarás.\n\n Número do ofício: $num_of \n\n $detento" );
$msg->get_msg();



// escrevendo o número do ofício
$txt = "Ofício nº $num_of  - $sigla_setor  - $iniciais";
$pdf->Cell( 0, '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

// escrevendo o referente
$txt = "Referente a(o) $referente";
$pdf->Cell( 0, '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

// data
$data = $cidade . ', ' . $sys->dataF();

// adicionando uma quebra de linha
$pdf->Ln();
$pdf->Ln();

// escrevendo a data
$pdf->Cell( 0, '', $data, 0, 1, 'R', 0, '', 0, false, 'T', 'M' );

// adicionando uma quebra de linha
$pdf->Ln();
$pdf->Ln();

$pdf->getQualiDetBasic( $iddet );


// adicionando uma quebra de linha
$pdf->Ln();
$pdf->Ln();

// tratamento superior
$txt = PAR_INDET_INI . 'Senhor Juíz(a),' . PAR_INDET_FIM;
$pdf->MultiCell( 170, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

// adicionando uma quebra de linha
$pdf->Ln();
$pdf->Ln( $cell_h );

// corpo
$txt = PAR_INDET_INI;

if ( $tipo_rest == 1 ) {

    $txt .= 'Encaminho cópia em anexo, do Mandado de Prisão expedido por esse Douto Juízo, ';
    $txt .= 'referente ao processo acima descrito, devidamente cumprido em desfavor d' . SICOP_DET_ART_L  .  ' referid' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . '.';

} else {

    $cump = 'SEM';
    if ( $sit_alv == 2 ) {
        $cump = 'COM';
    }

    $txt .= 'Encaminho copia do Alvará de Soltura, expedido por esse Douto Juizo, ';
    $txt .= 'referente ao processo acima descrito, em favor d' . SICOP_DET_ART_L  .  ' referid' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ", o qual foi cumprido $cump IMPEDIMENTO.";

}

$txt .= PAR_INDET_FIM;

$pdf->MultiCell( 170, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

// adicionando uma quebra de linha
$pdf->Ln();
$pdf->Ln( $cell_h );

// protestos
$txt = PAR_INDET_INI . 'Aproveitando o ensejo, renovo protestos de elevada estima e distinta consideração.' . PAR_INDET_FIM;
$pdf->MultiCell( 170, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

// adicionando uma quebra de linha
$pdf->Ln();
$pdf->Ln( $cell_h );

// protestos
$txt = PAR_INDET_INI . 'Respeitosamente,' . PAR_INDET_FIM;
$pdf->MultiCell( 170, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

// adicionando uma quebra de linha
$pdf->Ln();

// posicionando o ponteiro
$pdf->SetY( $pdf->GetY() + 20 );

// configurar a fonte
$pdf->SetFont( $font, 'I', 9.5 );

// nome do diretor
$txt = $diretor->_nome;
$pdf->MultiCell( 170, $cell_h, $txt, 0, 'C', false, '', '', '', true, 0, true, true, 0, 'C' );


// adicionando uma quebra de linha
$pdf->Ln();

// configurar a fonte
$pdf->SetFont( $font, 'N', 9.5 );

// titulo do diretor
$txt = $diretor->_titulo;
$pdf->MultiCell( 170, $cell_h, $txt, 0, 'C', false, '', '', '', true, 0, true, true, 0, 'C' );

// devolver assinado
$pdf->addDevAss();


// posicionando o ponteiro
$pdf->SetY( -40 );

// destino
$txt = 'A Sua Excelência o(a) Senhor(a)';
$pdf->MultiCell( 115, '', $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

// adicionando uma quebra de linha
$pdf->Ln();

$txt = "Juiz(a) de Direito da $dest de";
$pdf->MultiCell( 115, '', $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

// adicionando uma quebra de linha
$pdf->Ln();

$txt = $cidade_rest;
$pdf->MultiCell( 115, '', $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );




// reset pointer to the last page
$pdf->lastPage();
// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output( 'rest_mp.pdf', 'I' );

//============================================================+
// END OF FILE
//============================================================+
?>