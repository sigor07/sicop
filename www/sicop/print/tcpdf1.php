<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';

$titulo = get_session ( 'titulo' );

//============================================================+
// File name   : example_039.php
// Begin       : 2008-10-16
// Last Update : 2010-08-08
//
// Description : Example 039 for TCPDF class
//               HTML justification
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com s.r.l.
//               Via Della Pace, 11
//               09044 Quartucciu (CA)
//               ITALY
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: HTML justification
 * @author Nicola Asuni
 * @since 2008-10-18
 */
//require_once('../init/tcpdf/config/lang/bra.php');
//require_once('../init/tcpdf/tcpdf.php');


require_once('../init/tcpdf/pdf.php');

// create new PDF document
$pdf = new PDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );

$pdf->setFontSubsetting(false);

// set document information PDF_CREATOR
$pdf->SetCreator( 'SICOP - Sistema de Controle Prisional' );
$pdf->SetAuthor( 'SICOP - Sistema de Controle Prisional' );
$pdf->SetTitle( 'Ofício' );
$pdf->SetSubject( 'Ofício' );
$pdf->SetKeywords( '' );

// set default header data
//$pdf->SetHeaderData( PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 039', PDF_HEADER_STRING );

// set default monospaced font
$pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );

// set header and footer fonts
//$pdf->setHeaderFont( Array( PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN ) );
//$pdf->setFooterFont( Array( PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA ) );

//set margins
//$pdf->SetMargins( PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT );
$pdf->SetMargins( 10, 33, 10 );
$pdf->SetHeaderMargin( PDF_MARGIN_HEADER );
$pdf->SetFooterMargin( PDF_MARGIN_FOOTER );

//set auto page breaks
//$pdf->SetAutoPageBreak( TRUE, PDF_MARGIN_BOTTOM );
$pdf->SetAutoPageBreak( TRUE, 15 );

//set image scale factor
$pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );

// desabilitar o footer
//$pdf->setPrintFooter(false);

//set some language-dependent strings
$pdf->setLanguageArray( $l );

// ---------------------------------------------------------

$pdf->SetDisplayMode( 'real', 'SinglePage', 'UseNone' );

// instrui a colocar os números de página no rodapé
define( 'ADD_NUM_PAG', TRUE );

// add a page
$pdf->AddPage();

// set font
$pdf->SetFont( 'helvetica', 'B', 20 );

$pdf->Write( 0, 'Example of HTML Justification', '', 0, 'L', true, 0, false, false, 0 );

// create some HTML content
$html = '<p style="text-align:justify; text-indent: 250px;">a <u>abc</u> abcdefghijkl abcdef abcdefg <b>abcdefghi</b> a abc abcd
    abcdef abcdefg <b>abcdefghi</b> a abc abcd abcdef abcdefg <b>abcdefghi</b> a abc abcd abcdef abcdefg <b>abcdefghi</b> a
    <u>abc</u> abcd abcdef abcdefg <b>abcdefghi</b> a abc abcd abcdef abcdefg <b>abcdefghi</b> a abc abcd abcdef abcdefg
    <b>abcdefghi</b> a abc abcd abcdef abcdefg <b>abcdefghi</b> a abc abcd abcdef abcdefg <b>abcdefghi</b> a abc abcd
    abcdef abcdefg abcdefghi a abc abcd <a href="http://tcpdf.org">abcdef abcdefg</a> start a abc before
    <span style="background-color:yellow">yellow color</span> after a abc abcd abcdef abcdefg abcdefghi a abc abcd end
    abcdefg abcdefghi a abc abcd abcdef abcdefg abcdefghi a abc abcd abcdef abcdefg abcdefghi a abc abcd abcdef abcdefg
    abcdefghi a abc abcd abcdef abcdefg abcdefghi a abc abcd abcdef abcdefg abcdefghi a abc abcd abcdef abcdefg abcdefghi
    a abc abcd abcdef abcdefg abcdefghi<br />abcd abcdef abcdefg abcdefghi<br />abcd abcde abcdef
    </p>';

// set core font
$pdf->SetFont( 'helvetica', '', 10 );

// output the HTML content
$pdf->writeHTML( $html, true, 0, true, true );

$pdf->Ln();

// set UTF-8 Unicode font
$pdf->SetFont( 'dejavusans', '', 15 );

// output the HTML content
$pdf->writeHTML( $html, true, 0, true, true );

$pdf->Ln( 5 );

$novo = '
<p style="text-align:justify; text-indent: 250px;">Nesta segunda-feira, a situação começou a se normalizar, mas ainda há registro de problemas. Até as 10h, dos 623 vôos previstos nos 13 principais aeroportos brasileiros, 126 tiveram atrasos de mais de uma hora, segundo balanço divulgado pela Infraero, a estatal que administra os terminais aéreos. O número equivale a 20,2% do total. Quarenta e seis decolagens foram canceladas (7,3%).
Os terminais que tiveram maiores percentuais de atrasos foram os do Recife (PE) e de Fortaleza (CE). Na Capital de Pernambuco, oito dos 24 vôos marcados até as 10h atrasaram mais de uma hora (33,3% do total). No terminal cearense, oito das 25 partidas ocorreram fora
O terminal que registrou maior índice de cancelamentos foi o de Curitiba (PR). Das 22 decolagens programadas, quatro foram canceladas (18,1%).
A assessoria de Infraero informa que os atrasos são conseqüência dos transtornos do fim de semana. Muitos vôos tiveram que ser remarcados para o início desta semana.
Previsão - O presidente da Infraero, brigadeiro José Carlos Pereira, também foi prejudicado pela crise aérea.
</p>';

//Ele tinha uma viagem marcada de Brasília para o Rio às 7h desta segunda, mas o avião só decolou às 9h59.
//Apesar do transtorno, ele disse que as operações estão ocorrendo normalmente nos principais aeroportos do país e a situação deve se normalizar até as 14h

$novo = nl2br( $novo );

// set core font
$pdf->SetFont( 'helvetica', '', 10 );

// output the HTML content
$pdf->writeHTML( $novo, true, 0, true, true );

$pdf->writeHTML( $novo, true, 0, true, true );

$txt = 'CDP DE SÃO JOSE DO RIO PRETO - SP

teste';

//MultiCell( $w, $h, $txt, $border=0, $align='J', $fill=false, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign='T', $fitcell=false ) {

// para centralizar uma cell ou multicell usar o seguinte:
// pegar o tamanho da página (210), menos o tamanho da cell ou multicell, e dividir por 2
// o valor colocar no parametro $x;
// $x = ( 210 - $w_cell )/2

$pdf->MultiCell( 100, 7, $txt, 1, 'C', 0, 1, 55, '', false );
//$pdf->MultiCell(  80, 5, $txt, 1, 'J', 1, 1, '', '', true );

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------
//Close and output PDF document
$pdf->Output( 'of_esc.pdf', 'I' );

//============================================================+
// END OF FILE
//============================================================+
?>