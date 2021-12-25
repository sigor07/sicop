<?php

/**
 * relação de documentos necessários para visitação que será entregue ao
 * detento
 *
 * @author Rafael
 * @since 27/02/2012
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
require 'incl_print.php';


// instanciando a classe
$user = new userAutController();

// validando o usuário e o nível de acesso
$user->validateUser( 'imp_incl', 1, '', 7 );

// instanciando a classe
$sys = new SicopController();

// checando se o sistema esta ativo
$sys->ckSys();

// checando se o acesso foi via post
//$sys->ckPost( 3 );

// gravando o acesso no log
$pag = $sys->linkPag();
$mensagem = "Acesso à página $pag";
$user->salvaLog( $mensagem );


$l    = '';

$titulo        = $sys->getSession( 'titulo' );
$cidade        = $sys->getSession( 'cidade' );
$endereco_sort = $sys->getSession( 'endereco_sort' );
$unidadelongo  = $sys->getSession( 'unidadelongo' );
$iduser        = $sys->getSession( 'user_id', 'int' );
$ip            = $_SERVER['REMOTE_ADDR'];
$maquina       = substr( $ip, strrpos( $ip, '.' ) + 1 );

$motivo_pag    = 'IMPRESSÃO DE RELAÇÃO DE DOCUMENTOS PARA VISITA - DETENTOS - PDF';

// montar a mensagem q será salva no log
$msg = array();
$msg['tipo'] = 'desc';
$msg['entre_ch'] = $motivo_pag;
$msg['text'] = 'Impressão da relação de documentos necessários para visita';

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
$pdf->SetTitle( 'Relação de documentos para visita' );
$pdf->SetSubject( 'Relação de documentos para visita' );
$pdf->SetKeywords( SICOP_SYS_KW );

// set default monospaced font
$pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

//set margins - left, top and right
$pdf->SetMargins( 20, 10, 20 );
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

//$pdf->SetDisplayMode( 'real', 'continuous', 'UseNone' );
//$pdf->SetDisplayMode( 'fullpage', 'continuous', 'UseNone' );
$pdf->SetDisplayMode( 'fullwidth', 'continuous', 'UseNone' );

// instrui a colocar os números de página no rodapé
define( 'ADD_NUM_PAG', TRUE );

// adicionar uma página
$pdf->AddPage();

// configurar a fonte
$pdf->SetFont( $font, 'B', 12 );

$txt = $unidadelongo;
$pdf->Cell( 0, '', $txt, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

// configurar a fonte
$pdf->SetFont( $font, 'n', 9 );

$txt = $endereco_sort;
$pdf->Cell( 0, '', $txt, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

// quebra de linha
$pdf->Ln();

// configurar a fonte
$pdf->SetFont( $font, 'b', 12 );

$txt = 'DOCUMENTOS NECESSÁRIOS PARA REGULARIZAÇÃO DA VISITA';
$pdf->Cell( 0, '', $txt, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

// quebra de linha
$pdf->Ln();

// configurar a fonte
$pdf->SetFont( $font, 'b', 9 );

$txt = 'Xerox do R.G. autenticado.';
$pdf->Cell( 0, '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

// quebra de linha
$pdf->Ln();

$txt = 'Xerox do C.P.F. autenticado.';
$pdf->Cell( 0, '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

// quebra de linha
$pdf->Ln();

$txt = 'Uma foto 3x4.';
$pdf->Cell( 0, '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

// quebra de linha
$pdf->Ln();

// configurar a fonte
$pdf->SetFont( $font, 'N', 9 );

$txt = '<b>Xerox do comprovante de residência recente autenticado (agua, energia ou telefone) dos últimos 6(seis) meses.</b> Se a conta estiver em nome de terceiros,
        trazer também uma declaração de residente no imóvel autenticada pelo titular da conta.';
$pdf->writeHTML( $txt, true, 0, true, true );

// quebra de linha
$pdf->Ln();


$txt = '<b>Certidão de Antecedentes Criminais.</b>';
$pdf->writeHTML( $txt, true, 0, true, true );

// quebra de linha
$pdf->Ln();


$txt = '<b>*** ESPOSA E AMÁSIA *** Xerox da Certidão de Casamento</b> ou, no caso de União Estável, <b>Declaração de União Estável</b> com ' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ', assinada e
    autenticada por 2 (duas) testemunhas e a declarante.';
$pdf->writeHTML( $txt, true, 0, true, true );

// quebra de linha
$pdf->Ln();


$txt = '<b>Menores (SOMENTE FILHOS D' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U . '):</b> Xerox simples do R.G. ou Certidão de Nascimento e uma foto 3x4.';
$pdf->writeHTML( $txt, true, 0, true, true );

// quebra de linha
$pdf->Ln();


$txt = '<b>Avô(ó):</b> Certidão de Nascimento d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ' ou outro documento que comprove o parentesco com o detento';
$pdf->writeHTML( $txt, true, 0, true, true );

// quebra de linha
$pdf->Ln();


// configurar a fonte
$pdf->SetFont( $font, 'b', 11 );

$txt = 'OBSERVAÇÕES';
$pdf->Cell( 0, '', $txt, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

// configurar a fonte
$pdf->SetFont( $font, 'N', 9 );

// quebra de linha
$pdf->Ln();


$txt = '* Os documentos acima deverão ser providenciados <b>ANTES DA PRIMEIRA VISITA</b>, o que, caso não ocorra, acarretará na SUSPENSÃO do visitante, até que a situação esteja regularizada.';
$pdf->writeHTML( $txt, true, 0, true, true );

// quebra de linha
$pdf->Ln();


$txt = '* Para o envio de correspondência, colocar o NOME COMPLETO d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ', matrícula, ' . mb_strtolower( SICOP_RAIO ) . ' e ' . mb_strtolower( SICOP_CELA ) . '.';
$pdf->writeHTML( $txt, true, 0, true, true );

// quebra de linha
$pdf->Ln();


$txt = '* Somente serão aceitos SEDEX cujo o nome do remetente conste no ROL DE VISITAS d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ' e que esteja com a situação regularizada, sendo limitado a <b>1 (UM) SEDEX POR SEMANA</b>.';
$pdf->writeHTML( $txt, true, 0, true, true );

// quebra de linha
$pdf->Ln();


$txt = '* Para solicitação de ATESTADOS DE PERMANÊNCIA CARCERÁRIA, <b>PRIMEIRAMENTE</b> entre em contato com o setor responsável da unidade.';
$pdf->writeHTML( $txt, true, 0, true, true );

// quebra de linha
$pdf->Ln();

// reset pointer to the last page
$pdf->lastPage();
// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output( "relacao_doc_visit.pdf", 'I' );

//============================================================+
// END OF FILE
//============================================================+
?>