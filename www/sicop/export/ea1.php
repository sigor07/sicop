<?php
    setlocale(LC_ALL, 'pt_BR',"ptb",'pt-BR','pt-br','PT-BR');
    date_default_timezone_set('America/Sao_Paulo');
/** Error reporting */
error_reporting(E_ALL);

/** Include path **/
//ini_set('include_path', ini_get('include_path').';../Classes/');

/** PHPExcel */
require_once '../init/Classes/PHPExcel.php';

require_once '../init/funcoes.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

    // Set properties
    $objPHPExcel->getProperties()->setCreator("Professor X");

    /*$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                                 ->setLastModifiedBy("Maarten Balliauw")
                                 ->setTitle("Office 2007 XLSX Test Document")
                                 ->setSubject("Office 2007 XLSX Test Document")
                                 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                                 ->setKeywords("office 2007 openxml php")
                                 ->setCategory("Test result file");*/

    $conn = mysql_connect("localhost", "cdrio", "poderozo") or die ('Não foi possivel conectar ao banco de dados! Erro: ' . mysql_error());
    if($conn){
        mysql_select_db("bd", $conn);
    }
    $consulta = "SELECT
                    `detentos`.`iddetento`,
                    `detentos`.`nome_det`,
                    `detentos`.`matricula`,
                    `detentos`.`sit_det`,
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
                    WHERE
                      `audiencias`.`idaudiencia` IN( 12034, 11364, 11830 )
                    ORDER BY
                      `audiencias`.`data_aud` ASC, `audiencias`.`cidade_aud` ASC, `audiencias`.`local_aud` ASC, `audiencias`.`hora_aud` ASC ";

    $resultado = mysql_query($consulta);
    if($resultado==true){
        $i=1; //linha da planilha
        $n=0; //numero de sequencia

        $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //$objPHPExcel->getActiveSheet()->getStyle('A')->getFont()->setName('Arial');
        //$objPHPExcel->getActiveSheet()->getStyle('A')->getFont()->setSize(10);

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, 'N')
                    ->setCellValue('B'.$i, 'NOME')
                    ->setCellValue('C'.$i, 'MATRICULA')
                    ->setCellValue('D'.$i, 'LOCAL DE APRESENTAÇÃO')
                    ->setCellValue('E'.$i, 'CIDADE')
                    ->setCellValue('F'.$i, 'DATA')
                    ->setCellValue('G'.$i, 'HORA')
                    ->setCellValue('H'.$i, 'Nº PROCESSO');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

        while($linha = mysql_fetch_array($resultado)){
            ++$i;
            ++$n;
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$i, $n)
                        ->setCellValue('B'.$i, $linha['nome_det'])
                        ->setCellValue('C'.$i, !empty( $linha['matricula'] ) ? formata_num( $linha['matricula'] ) : '')
                        ->setCellValue('D'.$i, $linha['local_aud'])
                        ->setCellValue('E'.$i, $linha['cidade_aud'])
                        ->setCellValue('F'.$i, $linha['data_aud_f'])
                        ->setCellValue('G'.$i, $linha['hora_aud_f'])
                        ->setCellValue('H'.$i, $linha['num_processo']);

            if ($linha['sit_det'] == SICOP_SIT_DET_TRADA){//TRANSITO DA CASA
                $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ));
            }
            if ($linha['sit_det'] == SICOP_SIT_DET_TRANA){//TRANSITO NA CASA
                $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_BLUE ));
            }
            if ($linha['sit_det'] == SICOP_SIT_DET_TRANADA){//TRANSITO NA CASA E DA CASA
                $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ));
            }
            if ($linha['sit_det'] == SICOP_SIT_DET_TRANSF){//TRANSFERIDO
                $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKYELLOW ));
            }
            if ($linha['sit_det'] == SICOP_SIT_DET_EXCLUIDO){//EXCLUIDO (ALVARA)
                $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_GREEN ));
            }
            if ($linha['sit_det'] == SICOP_SIT_DET_EVADIDO){//EVADIDO
                $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_LIGHTBLUE ));
            }
            if ($linha['sit_det'] == SICOP_SIT_DET_FALECIDO){//FALECIDO
                $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_PURPLE ));
            }
            if ($linha['sit_det'] == SICOP_SIT_DET_ACEHGAR){//A CHEGAR
                $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_VIOLET ));
            }

            //$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setShrinkToFit(true);

        }

        $styleThinBlackBorderOutline = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                ),
            ),
        );
        $objPHPExcel->getActiveSheet()->getStyle('A1:H'.$i)->applyFromArray($styleThinBlackBorderOutline);

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToPage(true);

        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.4);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.4);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.4);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.4);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0.4);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.4);

        $objPHPExcel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setVerticalCentered(false);


    }


// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('LISTA DE AUDIENCIAS');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="lista_audiencias.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

//excel 2007
/*header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="lista_audiencias.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');*/

exit;
?>
