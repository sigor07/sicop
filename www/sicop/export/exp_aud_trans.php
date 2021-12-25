<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_export.php';

$pag  = link_pag();
$tipo = '';
$motivo_pag = 'EXPORTAÇÃO DA LISTA DE AUDIÊNCIAS PARA TABELA DO TRANSITO';

$imp_cadastro  = get_session( 'imp_cadastro' );

if ( $imp_cadastro < 1 ) {

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
if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );

    exit;

}

$idaud = '';

extract( $_POST, EXTR_OVERWRITE );

if ( empty( $idaud ) ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Identtificador da audiência em branco. ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );

    exit;

}

// monta a variavel para o comparador IN()
$v_aud = '';
foreach ( $idaud as &$valor ) {
    $valor = (int)$valor;
    if ( empty( $valor ) )
        continue;
    $v_aud .= (int)$valor . ',';
}

if ( empty( $v_aud ) ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Após validação, o array ficou vazio. ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );

    exit;

}

$v_aud = substr( $v_aud, 0, -1 );


/** PHPExcel */
require_once 'classes/excel/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

    // Set properties
$objPHPExcel->getProperties()->setCreator("SICOP - Sistema de Controle de Prisional")
                             ->setLastModifiedBy("SICOP - Sistema de Controle de Prisional")
                             ->setTitle("Lista de audiências")
                             ->setSubject("Lista de audiências")
                             ->setDescription("Lista de audiências.");

$query = "SELECT
            `detentos`.`iddetento`,
            `detentos`.`nome_det`,
            `detentos`.`matricula`,
            `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
            `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
            `unidades_out`.`idunidades` AS iddestino,
            `audiencias`.`idaudiencia`,
            DATE_FORMAT ( `audiencias`.`data_aud`, '%d/%m/%Y' ) AS `data_aud_f`,
            DATE_FORMAT ( `audiencias`.`hora_aud`, '%H:%i' ) AS `hora_aud_f`,
            `audiencias`.`local_aud`,
            `audiencias`.`cidade_aud`,
            `audiencias`.`num_processo`,
            `audiencias`.`sit_aud`
          FROM
            `detentos`
            INNER JOIN `audiencias` ON `audiencias`.`cod_detento` = `detentos`.`iddetento`
            LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
            LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
            LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
           WHERE
            `audiencias`.`idaudiencia` IN( $v_aud )
          ORDER BY
            `audiencias`.`data_aud` ASC, `audiencias`.`cidade_aud` ASC, `audiencias`.`local_aud` ASC, `audiencias`.`hora_aud` ASC ";

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

$i=3; //linha da planilha
$n=0; //numero de sequencia

$objPHPExcel->getActiveSheet()->mergeCells( 'A1:H2' );
$objPHPExcel->getActiveSheet()->getStyle( 'A1' )->getAlignment()->setVertical( PHPExcel_Style_Alignment::VERTICAL_CENTER );
$objPHPExcel->getActiveSheet()->getStyle( 'A1' )->getFill()->setFillType( PHPExcel_Style_Fill::FILL_SOLID )->getStartColor()->setARGB( 'FFFF0000' );
$objPHPExcel->getActiveSheet()->getCell( 'A1' )->setValue( "TRÂNSITO DO CDP DE SÃO JOSE DO RIO PRETO\nNA PENITENCIÁRIA DE " );
$objPHPExcel->getActiveSheet()->getStyle( 'A1' )->getAlignment()->setWrapText( true );
$objPHPExcel->getActiveSheet()->getStyle( 'A1' )->getFont()->getColor()->setARGB( PHPExcel_Style_Color::COLOR_WHITE );

$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB(PHPExcel_Style_Color::COLOR_YELLOW);
//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_YELLOW);



$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue( 'A' . $i, 'N' )
            ->setCellValue( 'B' . $i, 'NOME' )
            ->setCellValue( 'C' . $i, 'MATRICULA' )
            ->setCellValue( 'D' . $i, 'LOCAL DE APRESENTAÇÃO' )
            ->setCellValue( 'E' . $i, 'CIDADE' )
            ->setCellValue( 'F' . $i, 'DATA' )
            ->setCellValue( 'G' . $i, 'HORA' )
            ->setCellValue( 'H' . $i, 'Nº PROCESSO' );

$objPHPExcel->getActiveSheet()->getColumnDimension( 'A' )->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension( 'B' )->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension( 'C' )->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension( 'D' )->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension( 'E' )->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension( 'F' )->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension( 'G' )->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension( 'H' )->setAutoSize( true );

while ( $linha = $query->fetch_object() ) {
    ++$i;
    ++$n;
    $objPHPExcel->setActiveSheetIndex( 0 )
                ->setCellValue( 'A' . $i, $n )
                ->setCellValue( 'B' . $i, $linha->nome_det )
                ->setCellValue( 'C' . $i, !empty( $linha->matricula ) ? formata_num( $linha->matricula ) : '' )
                ->setCellValue( 'D' . $i, $linha->local_aud )
                ->setCellValue( 'E' . $i, $linha->cidade_aud )
                ->setCellValue( 'F' . $i, $linha->data_aud_f )
                ->setCellValue( 'G' . $i, $linha->hora_aud_f )
                ->setCellValue( 'H' . $i, $linha->num_processo );

    $tipo_mov_in  = $linha->tipo_mov_in;
    $tipo_mov_out = $linha->tipo_mov_out;
    $iddestino    = $linha->iddestino;
    $sit_det      = manipula_sit_det_id( $tipo_mov_in, $tipo_mov_out, $iddestino );

    //SITUAÇÃO DO DETENTO
    if ( $sit_det == SICOP_SIT_DET_TRADA or $sit_det == SICOP_SIT_DET_TRANADA){//TRANSITO DA CASA ou TRANSITO NA CASA E DA CASA
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ));
    }
    if ( $sit_det == SICOP_SIT_DET_TRANA ){//TRANSITO NA CASA
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_BLUE ));
    }
    if ( $sit_det == SICOP_SIT_DET_TRANSF ){//TRANSFERIDO
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKYELLOW ));
    }
    if ( $sit_det == SICOP_SIT_DET_EXCLUIDO ){//EXCLUIDO (ALVARA)
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_GREEN ));
    }
    if ( $sit_det == SICOP_SIT_DET_EVADIDO ){//EVADIDO
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_LIGHTBLUE ));
    }
    if ( $sit_det == SICOP_SIT_DET_FALECIDO ){//FALECIDO
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_PURPLE ));
    }
    if ( $sit_det == SICOP_SIT_DET_ACEHGAR ){//A CHEGAR
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_VIOLET ));
    }

    // SITUAÇÃO DA AUDIENCIA
    if ($linha->sit_aud == 12){// CANCELADA
        $objPHPExcel->getActiveSheet()->getStyle('E'.$i.':H'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKYELLOW ));
    }
     if ($linha->sit_aud == 13){//A JUSTIFICADA
        $objPHPExcel->getActiveSheet()->getStyle('E'.$i.':H'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ));
    }
} // fim do while


// ALINHAMENTO DAS CELULAS
$objPHPExcel->getActiveSheet()->getStyle( 'A1:A' . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
$objPHPExcel->getActiveSheet()->getStyle( 'B3' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
$objPHPExcel->getActiveSheet()->getStyle( 'C1:H' . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );

// COLOCANDO O ALINHAMENTO VERTICAL DA CELULAS CENTRALIZADO
$objPHPExcel->getActiveSheet()->getStyle( 'A1:H' . $i )->getAlignment()->setVertical( PHPExcel_Style_Alignment::VERTICAL_CENTER );

// COLOCANDO A FONTE EM NEGRITO DA PRIMEIRA LINHA
$objPHPExcel->getActiveSheet()->getStyle( 'A1:H1' )->getFont()->setBold( true );

// DEFININDO BORDAS
$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);
$objPHPExcel->getActiveSheet()->getStyle( 'A1:H' . $i )->applyFromArray( $styleThinBlackBorderOutline );

// DEFININDO A ORIENTAÇÃO E TAMANHO DO PAPEL
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation( PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE );
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize( PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4 );

// DEFININDO A PLANILHA PARA CABER EM UMA PÁGINA
$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToPage( true );

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
$objPHPExcel->getActiveSheet()->setTitle( 'LISTA DE AUDIÊNCIAS - TRÂNSITO' );


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex( 0 );
$objPHPExcel->getActiveSheet()->getCell( 'A1' );

// Redirect output to a client’s web browser (Excel5)
header( 'Content-Type: application/vnd.ms-excel' );
header( 'Content-Disposition: attachment;filename="lista_audiencias_trans.xls"' );
header( 'Cache-Control: max-age=0' );

$objWriter = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel5' );
$objWriter->save( 'php://output' );

//excel 2007
/*header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="lista_audiencias.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');*/

exit;
?>
