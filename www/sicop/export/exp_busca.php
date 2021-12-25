<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_export.php';

$pag  = link_pag();
$tipo = '';
$motivo_pag = 'EXPORTAÇÃO DA BUSCA DE ' . SICOP_DET_DESC_U . 'S PARA EXCEL';

$imp_chefia   = get_session( 'imp_chefia', 'int' );
$imp_cadastro = get_session( 'imp_cadastro', 'int' );
$imp_pront    = get_session( 'imp_pront', 'int' );
$n_imp_n      = 1;

if ( $imp_chefia < $n_imp_n and $imp_cadastro < $n_imp_n and $imp_pront < $n_imp_n ) {


    require 'cab_simp.php';
    $tipo = 3;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg         = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso à página SEM PERMISSÕES ( $motivo_pag ).";
    get_msg( $msg, 1 );

    exit;

}

$is_post = is_post();
$iddet_s = get_session( 'iddet' );

if ( isset( $_SESSION['iddet'] ) ) unset( $_SESSION['iddet'] );

if ( !$is_post and empty( $iddet_s ) ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );

    exit;

}

$v_det    = $iddet_s;
$ordbusca = "`detentos`.`nome_det` ASC";

if ( $is_post ) {

    $iddet_p = '';
    $op      = '';
    extract( $_POST, EXTR_OVERWRITE );

    if ( empty( $iddet_p ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'atn';
        $msg['text'] = "Tentativa de acesso direto à página ( ARRAY iddet EM BRANCO - $motivo_pag ).";
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!!!', 1 );

        exit;

    }

    // monta a variavel para o comparador IN()
    $v_det = '';
    foreach ( $iddet_p as &$valor ) {
        $valor = (int)$valor;
        if ( empty( $valor ) ) continue;
        $v_det .= (int)$valor . ',';
    }

    if ( empty( $v_det ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Após validação, o array ficou vazio. ( $motivo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!!!', 1 );

        exit;

    }

    $v_det = substr( $v_det, 0, -1 );

    switch ( $op ) {
        default:
        case '':
        case 'nomea':
            $ordbusca = "`detentos`.`nome_det` ASC";
            break;
        case 'nomed':
            $ordbusca = "`detentos`.`nome_det` DESC";
            break;
        case 'matra':
            $ordbusca = "`detentos`.`matricula` ASC";
            break;
        case 'matrd':
            $ordbusca = "`detentos`.`matricula` DESC";
            break;
        case 'proca':
            $ordbusca = "`unidades_in`.`unidades` ASC, `detentos`.`nome_det` ASC";
            break;
        case 'procd':
            $ordbusca = "`unidades_in`.`unidades` DESC, `detentos`.`nome_det` ASC";
            break;
        case 'dataa':
            $ordbusca = "`mov_det_in`.`data_mov` ASC, `detentos`.`nome_det` ASC";
            break;
        case 'datad':
            $ordbusca = "`mov_det_in`.`data_mov` DESC, `detentos`.`nome_det` ASC";
            break;
        case 'raioa':
            $ordbusca = "`detentos`.`cod_cela` ASC, `detentos`.`nome_det` ASC";
            break;
        case 'raiod':
            $ordbusca = "`detentos`.`cod_cela` DESC, `detentos`.`nome_det` ASC";
            break;
    }

}

$query = "SELECT
            `detentos`.`iddetento`,
            `detentos`.`nome_det`,
            `detentos`.`matricula`,
            `mov_det_in`.`data_mov` AS data_incl,
            DATE_FORMAT(`mov_det_in`.`data_mov`, '%d/%m/%Y') AS data_incl_f,
            `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
            `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
            `unidades_in`.`unidades` AS procedencia,
            `unidades_out`.`idunidades` AS iddestino,
            `cela`.`cela`,
            `raio`.`raio`
          FROM
            `detentos`
            LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
            LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
            LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
             LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
            LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
            LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
          WHERE
            `detentos`.`iddetento` IN( $v_det )
          ORDER BY
            $ordbusca";

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

$db->closeConnection(); // fecho a conexao

$contd = $query->num_rows;
if ( $contd < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "A consulta retornou 0 ocorrências ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );
    exit;

}

/** PHPExcel */
require_once 'classes/excel/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set properties
$objPHPExcel->getProperties()->setCreator( 'SICOP - Sistema de Controle de Presos Provisórios' )
            ->setLastModifiedBy( 'SICOP - Sistema de Controle de Presos Provisórios' )
            ->setTitle( 'Resultado da busca' )
            ->setSubject( 'Resultado da busca' )
            ->setDescription( 'Resultado da busca.' );

$i = 1; //linha da planilha
$n = 0; //numero de sequencia

$objPHPExcel->setActiveSheetIndex( 0 )
            ->setCellValue( 'A' . $i, 'N' )
            ->setCellValue( 'B' . $i, 'NOME' )
            ->setCellValue( 'C' . $i, 'MATRICULA' )
            ->setCellValue( 'D' . $i, mb_strtoupper( SICOP_RAIO ) )
            ->setCellValue( 'E' . $i, mb_strtoupper( SICOP_CELA ) )
            ->setCellValue( 'F' . $i, 'PROCEDÊNCIA' )
            ->setCellValue( 'G' . $i, 'DATA DA INCLUSÃO' );

$objPHPExcel->getActiveSheet()->getColumnDimension( 'A' )->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension( 'B' )->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension( 'C' )->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension( 'D' )->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension( 'E' )->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension( 'F' )->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension( 'G' )->setAutoSize( true );

while ( $linha = $query->fetch_object() ) {
    ++$i;
    ++$n;
    $objPHPExcel->setActiveSheetIndex( 0 )
                ->setCellValue( 'A' . $i, $n )
                ->setCellValue( 'B' . $i, $linha->nome_det )
                ->setCellValue( 'C' . $i, !empty( $linha->matricula ) ? formata_num( $linha->matricula ) : '' )
                ->setCellValue( 'D' . $i, $linha->raio )
                ->setCellValue( 'E' . $i, $linha->cela )
                ->setCellValue( 'F' . $i, $linha->procedencia )
                ->setCellValue( 'G' . $i, $linha->data_incl_f );

    $tipo_mov_in  = $linha->tipo_mov_in;
    $tipo_mov_out = $linha->tipo_mov_out;
    $iddestino    = $linha->iddestino;
    $sit_det      = manipula_sit_det_id( $tipo_mov_in, $tipo_mov_out, $iddestino );

    //SITUAÇÃO DO DETENTO
    if ( $sit_det == SICOP_SIT_DET_TRADA or $sit_det == SICOP_SIT_DET_TRANADA ) {//TRANSITO DA CASA ou TRANSITO NA CASA E DA CASA
        $objPHPExcel->getActiveSheet()->getStyle( 'B' . $i . ':C' . $i )->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ) );
    }
    if ( $sit_det == SICOP_SIT_DET_TRANA ) {//TRANSITO NA CASA
        $objPHPExcel->getActiveSheet()->getStyle( 'B' . $i . ':C' . $i )->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_BLUE ) );
    }
    if ( $sit_det == SICOP_SIT_DET_TRANSF ) {//TRANSFERIDO
        $objPHPExcel->getActiveSheet()->getStyle( 'B' . $i . ':C' . $i )->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKYELLOW ) );
    }
    if ( $sit_det == SICOP_SIT_DET_EXCLUIDO ) {//EXCLUIDO (ALVARA)
        $objPHPExcel->getActiveSheet()->getStyle( 'B' . $i . ':C' . $i )->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_GREEN ) );
    }
    if ( $sit_det == SICOP_SIT_DET_EVADIDO ) {//EVADIDO
        $objPHPExcel->getActiveSheet()->getStyle( 'B' . $i . ':C' . $i )->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_LIGHTBLUE ) );
    }
    if ( $sit_det == SICOP_SIT_DET_FALECIDO ) {//FALECIDO
        $objPHPExcel->getActiveSheet()->getStyle( 'B' . $i . ':C' . $i )->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_PURPLE ) );
    }
    if ( $sit_det == SICOP_SIT_DET_ACEHGAR ) {//A CHEGAR
        $objPHPExcel->getActiveSheet()->getStyle( 'B' . $i . ':C' . $i )->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_VIOLET ) );
    }

} // fim do while

// ALINHAMENTO DAS CELULAS
$objPHPExcel->getActiveSheet()->getStyle( 'A1:A' . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
$objPHPExcel->getActiveSheet()->getStyle( 'B1' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
$objPHPExcel->getActiveSheet()->getStyle( 'C1:C' . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
$objPHPExcel->getActiveSheet()->getStyle( 'D1:D' . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
$objPHPExcel->getActiveSheet()->getStyle( 'E1:E' . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
$objPHPExcel->getActiveSheet()->getStyle( 'F1' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
$objPHPExcel->getActiveSheet()->getStyle( 'G1:G' . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );

// COLOCANDO O ALINHAMENTO VERTICAL DA CELULAS CENTRALIZADO
$objPHPExcel->getActiveSheet()->getStyle( 'A1:G' . $i )->getAlignment()->setVertical( PHPExcel_Style_Alignment::VERTICAL_CENTER );

// COLOCANDO A FONTE EM NEGRITO DA PRIMEIRA LINHA
$objPHPExcel->getActiveSheet()->getStyle( 'A1:G1' )->getFont()->setBold( true );

//// DEFININDO BORDAS
//$styleThinBlackBorderOutline = array(
//    'borders' => array(
//        'allborders' => array(
//            'style' => PHPExcel_Style_Border::BORDER_THIN,
//            'color' => array( 'argb' => 'FF000000' ),
//        ),
//    ),
//);
//$objPHPExcel->getActiveSheet()->getStyle( 'A1:G' . $i )->applyFromArray( $styleThinBlackBorderOutline );

// DEFININDO A ORIENTAÇÃO E TAMANHO DO PAPEL
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation( PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT );
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize( PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4 );

// DEFININDO A PLANILHA PARA CABER EM UMA PÁGINA DE LARGURA
$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth( 1 );
$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight( 0 );

// DEFININDO AS MARGENS
$objPHPExcel->getActiveSheet()->getPageMargins()->setTop( 0.4 );
$objPHPExcel->getActiveSheet()->getPageMargins()->setRight( 0.4 );
$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft( 0.4 );
$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom( 0.4 );
$objPHPExcel->getActiveSheet()->getPageMargins()->setFooter( 0.4 );
$objPHPExcel->getActiveSheet()->getPageMargins()->setHeader( 0.4 );

// DEFININDO O ALINHAMENTO DA PLANILHA NO PAPEL
$objPHPExcel->getActiveSheet()->getPageSetup()->setHorizontalCentered( true );
$objPHPExcel->getActiveSheet()->getPageSetup()->setVerticalCentered( false );

// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle( 'RESULTADO DA BUSCA' );

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex( 0 );
$objPHPExcel->getActiveSheet()->getCell( 'A1' );

// Redirect output to a client’s web browser (Excel5)
header( 'Content-Type: application/vnd.ms-excel' );
header( 'Content-Disposition: attachment;filename="lista_busca.xls"' );
header( 'Cache-Control: max-age=0' );

$objWriter = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel5' );
$objWriter->save( 'php://output' );

//excel 2007
/* header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  header('Content-Disposition: attachment;filename="lista_busca.xlsx"');
  header('Cache-Control: max-age=0');

  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
  $objWriter->save('php://output'); */

$valor_user = '';
if ( !empty ( $_POST ) ) {
    $valor_user = valor_user( $_POST );
}

if ( !empty ( $_GET ) ) {
    $valor_user = valor_user( $_GET );
}
if ( !empty ( $valor_user ) ) {

    // montar a mensagem q será salva no log
    $msg             = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'EXPORTAÇÃO DE LISTA DE ' . SICOP_DET_DESC_U . 'S PARA EXCEL';
    $msg['text']     = 'Exportação de lista de ' . SICOP_DET_DESC_L . "s para excel.\n\n $valor_user";
    get_msg( $msg, 1 );

}
if ( !$is_post ) echo msg_js ( '', 'f' );

exit;
?>
