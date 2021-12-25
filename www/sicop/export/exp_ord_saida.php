<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_export.php';

$pag        = link_pag();
$tipo       = '';
$motivo_pag = 'EXPORTAÇÃO DE ORDENS DE SAÍDA E ESCOLTA';

$imp_chefia   = get_session( 'imp_chefia', 'int' );
$imp_cadastro = get_session( 'imp_cadastro', 'int' );
$imp_seg      = get_session( 'imp_seg', 'int' );
$n_imp_n      = 1;

if ( $imp_chefia < $n_imp_n and
     $imp_cadastro < $n_imp_n and
     $imp_seg < $n_imp_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$is_post = is_post();

if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$id_ord_saida = '';
$idescolta    = '';
$id           = '';

extract( $_POST, EXTR_OVERWRITE );

$id = !empty( $id_ord_saida ) ? $id_ord_saida : $idescolta;

if ( empty( $id ) ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo']  = 'err';
    $msg['text']  = "Identificador da ordem de saida em branco ou inválido ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );

    exit;

}

$tbl_ordem           = 'ordens_saida';
$tbl_ord_det         = 'ordens_saida_det';
$tbl_ord_locais      = 'ordens_saida_locais';
$field_id_ord        = 'id_ord_saida';
$field_cod_ord       = 'cod_ord_saida';
$field_id_local_ord  = 'id_local_ord_saida';
$field_cod_local_ord = 'cod_local_ord_saida';
$field_id_ord_det    = 'id_ord_saida_det';


if ( !empty( $idescolta ) ) {

    $tbl_ordem           = 'ordens_escolta';
    $tbl_ord_det         = 'ordens_escolta_det';
    $tbl_ord_locais      = 'ordens_escolta_locais';
    $field_id_ord        = 'idescolta';
    $field_cod_ord       = 'cod_escolta';
    $field_id_local_ord  = 'id_local_escolta';
    $field_cod_local_ord = 'cod_local_escolta';
    $field_id_ord_det    = 'id_escolta_det';

}


$query = "SELECT
            `$tbl_ord_det`.`$field_id_ord_det` AS `id_ord_det`,
            `$tbl_ord_locais`.`$field_id_local_ord` AS `id_local_ord`,
            `detentos`.`nome_det`,
            `detentos`.`matricula`,
            `detentos`.`rg_civil`,
            `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
            `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
            `unidades_out`.`idunidades` AS iddestino,
            `locais_apr`.`local_apr` AS destino,
            `locais_apr`.`local_end`,
            `cela`.`cela`,
            `raio`.`raio`
          FROM
            `$tbl_ordem`
            INNER JOIN `$tbl_ord_locais` ON `$tbl_ord_locais`.`$field_cod_ord` = `$tbl_ordem`.`$field_id_ord`
            LEFT JOIN `$tbl_ord_det` ON `$tbl_ord_det`.`$field_cod_local_ord` = `$tbl_ord_locais`.`$field_id_local_ord`
            LEFT JOIN `detentos` ON `$tbl_ord_det`.`cod_detento` = `detentos`.`iddetento`
            LEFT JOIN `locais_apr` ON `$tbl_ord_locais`.`cod_local` = `locais_apr`.`idlocal`
            LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
            LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
            LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
            LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
            LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
          WHERE
            `$tbl_ordem`.`$field_id_ord` = $id
          ORDER BY
            `locais_apr`.`local_apr`, `detentos`.`nome_det`";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query = $model->query( $query );

// fechando a conexao
$model->closeConnection();

if ( !$query ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Falha na consulta ( $motivo_pag )";
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
            ->setCellValue( 'D' . $i, 'RG' )
            ->setCellValue( 'E' . $i, mb_strtoupper( SICOP_RAIO ) )
            ->setCellValue( 'F' . $i, mb_strtoupper( SICOP_CELA ) );

$objPHPExcel->getActiveSheet()->getColumnDimension( 'A' )->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension( 'B' )->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension( 'C' )->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension( 'D' )->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension( 'E' )->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension( 'F' )->setAutoSize( true );

$dest_ant = '';

while ( $linha = $query->fetch_array() ) {

    // incrementador para saber em qual linha está
    ++$i;


    $quebra = FALSE;

    if ( $linha['destino'] != $dest_ant ){
        $quebra = TRUE;
    }

    if ( $quebra ) {
        $objPHPExcel->getActiveSheet()->mergeCells( 'A' . $i . ':F' . $i );
        $objPHPExcel->setActiveSheetIndex( 0 )->setCellValue( 'A' . $i, $linha['destino'] );
        $objPHPExcel->getActiveSheet()->getStyle( 'A' . $i )->getFont()->setBold( true );
        ++$i;
    }

    if ( empty( $linha['id_ord_det'] ) ) {
        $objPHPExcel->getActiveSheet()->mergeCells( 'A' . $i . ':F' . $i );
        $objPHPExcel->setActiveSheetIndex( 0 )->setCellValue( 'A' . $i, 'NÃO HÁ ' . SICOP_DET_DESC_U . 'S' );
        continue;
    }

    // número que fica antes do nome do detento na lista
    ++$n;

    $objPHPExcel->setActiveSheetIndex( 0 )
                ->setCellValue( 'A' . $i, $n )
                ->setCellValue( 'B' . $i, $linha['nome_det'] )
                ->setCellValue( 'C' . $i, !empty( $linha['matricula'] ) ? formata_num( $linha['matricula'] ) : '' )
                ->setCellValue( 'D' . $i, !empty( $linha['rg_civil'] ) ? formata_num( $linha['rg_civil'] ) : '' )
                ->setCellValue( 'E' . $i, $linha['raio'] )
                ->setCellValue( 'F' . $i, $linha['cela'] );

    $tipo_mov_in  = $linha['tipo_mov_in'];
    $tipo_mov_out = $linha['tipo_mov_out'];
    $iddestino    = $linha['iddestino'];
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

    $dest_ant = $linha['destino'];

} // fim do while

// ALINHAMENTO DAS CELULAS
$objPHPExcel->getActiveSheet()->getStyle( 'A1:A' . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
$objPHPExcel->getActiveSheet()->getStyle( 'B1' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
$objPHPExcel->getActiveSheet()->getStyle( 'C1:C' . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
$objPHPExcel->getActiveSheet()->getStyle( 'D1:D' . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
$objPHPExcel->getActiveSheet()->getStyle( 'E1:E' . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
$objPHPExcel->getActiveSheet()->getStyle( 'F1:F' . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );

// COLOCANDO O ALINHAMENTO VERTICAL DA CELULAS CENTRALIZADO
$objPHPExcel->getActiveSheet()->getStyle( 'A1:G' . $i )->getAlignment()->setVertical( PHPExcel_Style_Alignment::VERTICAL_CENTER );

// COLOCANDO A FONTE EM NEGRITO DA PRIMEIRA LINHA
$objPHPExcel->getActiveSheet()->getStyle( 'A1:F1' )->getFont()->setBold( true );

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
$objPHPExcel->getActiveSheet()->setTitle( 'ORDEM DE SAIDA ESCOLTA' );

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex( 0 );
$objPHPExcel->getActiveSheet()->getCell( 'A1' );

// Redirect output to a client’s web browser (Excel5)
header( 'Content-Type: application/vnd.ms-excel' );
header( 'Content-Disposition: attachment;filename="ord_esc.xls"' );
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

// montar a mensagem q será salva no log
$msg             = array();
$msg['tipo']     = 'desc';
$msg['entre_ch'] = 'EXPORTAÇÃO DE ORDEM DE SAÍDA / PEDIDO DE ESCOLTA PARA EXCEL';
$msg['text']     = "Exportação de ordem de saída / pedido de escolta para excel.\n\n $valor_user";
get_msg( $msg, 1 );

//echo msg_js ( '', 'f' );

exit;
?>
