<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_export.php';

/*$imp_cadastro  = get_session( 'imp_cadastro', 'int' );

if ($imp_cadastro < 1) {
    require 'cab_simp.php';
    $tipo=3;
    require '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
    salvaLog($mensagem);
    exit;
}*/

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    extract($_POST, EXTR_OVERWRITE);

    $campos = '';
    $join = '';
    $cont_campos = 3; // nome, matricula e o número na lista

    if ( !empty( $rg ) ){
        $campos .= ", `detentos`.`rg_civil` ";
        $cont_campos += 1;
    }

    if ( !empty( $rc_ck ) ){
        $campos .= ", `cela`.`cela`
                    , `raio`.`raio` ";

        $join .= " LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                   LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio` ";
        $cont_campos += 2; // 2 campos - 1 para o raio e 1 para a cela
    }

    if ( !empty( $proc_ck ) ){
        $campos .= ", `unidades_in`.`unidades` AS procedencia ";
        $cont_campos += 1;
    }

    if ( !empty( $data_in_ck ) ){
        $campos .= ", DATE_FORMAT(`mov_det_in`.`data_mov`, '%d/%m/%Y') AS data_incl_f ";
        $cont_campos += 1;
    }


    if ( !empty( $dest_ck ) ){
        $campos .= ", `unidades_out`.`unidades` AS destino ";
        $cont_campos += 1;
    }

    if ( !empty( $data_out_ck ) ){
        $campos .= ", DATE_FORMAT(`mov_det_out`.`data_mov`, '%d/%m/%Y') AS data_excl_f ";
        $cont_campos += 1;
    }

/*    if ( !empty( $dest_ck ) or !empty( $data_out_ck )  ){

        $join_mov = " LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov` ";

        if ( !empty( $dest_ck ) ){
            $join_mov = " LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                          LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades` ";
        }

        $join .= $join_mov;

    }*/

    if ( !empty( $pai ) ){
        $campos .= ", `detentos`.`pai_det` ";
        $cont_campos += 1;
    }

    if ( !empty( $mae ) ){
        $campos .= ", `detentos`.`mae_det` ";
        $cont_campos += 1;
    }

    if ( !empty( $data_nasc ) ){
        $campos .= ", DATE_FORMAT(`detentos`.`nasc_det`, '%d/%m/%Y') AS nasc_det
                    , FLOOR(DATEDIFF(CURDATE(), `detentos`.`nasc_det`) / 365.25) AS idade_det ";
        $cont_campos += 2; //2 campos - 1 para a data de nascimento e outro para a idade
    }

    if ( !empty( $nat ) ){
        $campos .= ", `cidades`.`nome` AS cidade
                    , `estados`.`sigla` AS estado ";

        $join .= " LEFT JOIN `cidades` ON `detentos`.`cod_cidade` = `cidades`.`idcidade`
                   LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado` ";
        $cont_campos += 1;

    }

    $where = '';

    if ( !empty( $unidade ) ){
        if ( !empty( $where ) ){
            $where .= " AND ( `unidades_in`.`idunidades` = $unidade )";
        } else {
            $where .= "WHERE ( `unidades_in`.`idunidades` = $unidade )";
        }
    }

    if ( !empty( $n_cela ) or !empty( $n_raio ) ){
        if ( empty( $n_cela ) ){
            if ( !empty( $where ) ){
                $where .= " AND ( `raio`.`idraio` = $n_raio )";
            } else {
                $where .= "WHERE ( `raio`.`idraio` = $n_raio )";
            }
        } else {
            if ( !empty( $where ) ){
                $where .= " AND ( `detentos`.`cod_cela` = $n_cela )";
            } else {
                $where .= "WHERE ( `detentos`.`cod_cela` = $n_cela )";
            }
        }
    }

    if ( !empty( $data_in_ini ) or !empty( $data_in_fim )){
        if ( !empty( $data_in_ini ) and  !empty( $data_in_fim )){
            if ( !empty( $where ) ){
                $where .= " AND `mov_det_in`.`data_mov` BETWEEN STR_TO_DATE('$data_in_ini', '%d/%m/%Y') AND STR_TO_DATE('$data_in_fim', '%d/%m/%Y')";
            } else {
                $where .= "WHERE `mov_det_in`.`data_mov` BETWEEN STR_TO_DATE('$data_in_ini', '%d/%m/%Y') AND STR_TO_DATE('$data_in_fim', '%d/%m/%Y')";
            }
        } else {
            if ( !empty( $where ) ){
                $where .= " AND `mov_det_in`.`data_mov` = IF(STR_TO_DATE('$data_in_ini', '%d/%m/%Y'), STR_TO_DATE('$data_in_ini', '%d/%m/%Y'), STR_TO_DATE('$data_in_fim', '%d/%m/%Y'))";
            } else {
                $where .= "WHERE `mov_det_in`.`data_mov` = IF(STR_TO_DATE('$data_in_ini', '%d/%m/%Y'), STR_TO_DATE('$data_in_ini', '%d/%m/%Y'), STR_TO_DATE('$data_in_fim', '%d/%m/%Y'))";
            }
        }
    }

    if ( !empty( $data_out_ini ) or !empty( $data_out_fim )){
        if ( !empty( $data_out_ini ) and  !empty( $data_out_fim )){
            if ( !empty( $where ) ){
                $where .= " AND `mov_det_out`.`data_mov` BETWEEN STR_TO_DATE('$data_out_ini', '%d/%m/%Y') AND STR_TO_DATE('$data_out_fim', '%d/%m/%Y')";
            } else {
                $where .= "WHERE `mov_det_out`.`data_mov` BETWEEN STR_TO_DATE('$data_out_ini', '%d/%m/%Y') AND STR_TO_DATE('$data_out_fim', '%d/%m/%Y')";
            }
        } else {
            if ( !empty( $where ) ){
                $where .= " AND `mov_det_out`.`data_mov` = IF(STR_TO_DATE('$data_out_ini', '%d/%m/%Y'), STR_TO_DATE('$data_out_ini', '%d/%m/%Y'), STR_TO_DATE('$data_out_fim', '%d/%m/%Y'))";
            } else {
                $where .= "WHERE `mov_det_out`.`data_mov` = IF(STR_TO_DATE('$data_out_ini', '%d/%m/%Y'), STR_TO_DATE('$data_out_ini', '%d/%m/%Y'), STR_TO_DATE('$data_out_fim', '%d/%m/%Y'))";
            }
        }
    }

    if ( !empty( $tipo_sit ) ){
        if ( $tipo_sit == 1 ){
            if ( !empty( $where ) ){
                $where .= " AND (`detentos`.`sit_det` = 11 OR `detentos`.`sit_det` = 112 OR `detentos`.`sit_det` = 113 OR `detentos`.`sit_det` = 114)";
            } else {
                $where .= "WHERE (`detentos`.`sit_det` = 11 OR `detentos`.`sit_det` = 112 OR `detentos`.`sit_det` = 113 OR `detentos`.`sit_det` = 114)";
            }
        } else if ( $tipo_sit == 2 ){
            if ( !empty( $where ) ){
                $where .= " AND (`detentos`.`sit_det` = 11 OR `detentos`.`sit_det` = 112 OR `detentos`.`sit_det` = 114)";
            } else {
                $where .= "WHERE (`detentos`.`sit_det` = 11 OR `detentos`.`sit_det` = 112 OR `detentos`.`sit_det` = 114)";
            }
        } else if ( $tipo_sit == 3 ){
            if ( !empty( $where ) ){
                $where .= " AND (`detentos`.`sit_det` = 11 OR `detentos`.`sit_det` = 113 OR `detentos`.`sit_det` = 114)";
            } else {
                $where .= "WHERE (`detentos`.`sit_det` = 11 OR `detentos`.`sit_det` = 113 OR `detentos`.`sit_det` = 114)";
            }
        } else if ( $tipo_sit == 4 ){
            if ( !empty( $where ) ){
                $where .= " AND (`detentos`.`sit_det` = 112 OR `detentos`.`sit_det` = 114)";
            } else {
                $where .= "WHERE (`detentos`.`sit_det` = 112 OR `detentos`.`sit_det` = 114)";
            }
        } else if ( $tipo_sit == 5 ){
            if ( !empty( $where ) ){
                $where .= " AND (`detentos`.`sit_det` = 113 OR `detentos`.`sit_det` = 114)";
            } else {
                $where .= "WHERE (`detentos`.`sit_det` = 113 OR `detentos`.`sit_det` = 114)";
            }
        } else if ( $tipo_sit == 6 ){
            if ( !empty( $where ) ){
                $where .= ' AND (`detentos`.`sit_det` = 12)';
            } else {
                $where .= 'WHERE (`detentos`.`sit_det` = 12)';
            }
        } else if ( $tipo_sit == 7 ){
            if ( !empty( $where ) ){
                $where .= ' AND (`detentos`.`sit_det` = 13)';
            } else {
                $where .= 'WHERE (`detentos`.`sit_det` = 13)';
            }
        } else if ( $tipo_sit == 8 ){
            if ( !empty( $where ) ){
                $where .= ' AND (`detentos`.`sit_det` = 14)';
            } else {
                $where .= 'WHERE (`detentos`.`sit_det` = 14)';
            }
        } else if ( $tipo_sit == 9 ){
            if ( !empty( $where ) ){
                $where .= ' AND (`detentos`.`sit_det` = 15)';
            } else {
                $where .= 'WHERE (`detentos`.`sit_det` = 15)';
            }
        } else if ( $tipo_sit == 10 ){
            if ( !empty( $where ) ){
                $where .= ' AND ( ISNULL( `detentos`.`sit_det` ) )';
            } else {
                $where .= 'WHERE ( ISNULL( `detentos`.`sit_det` ) )';
            }
        }

    }

    $ordpor = 'nome';

    if ( !empty( $op ) ) {
        $ordpor    = $op;
    }

    switch($ordpor) {
        default:
        case 'nome':
            $ordbusca = "`detentos`.`nome_det`";
            break;
        case 'matr':
            $ordbusca = "`detentos`.`matricula`";
            break;
        case 'proc':
            $ordbusca = "`unidades_in`.`unidades`, `detentos`.`nome_det`";
            break;
        case 'incl':
            $ordbusca = "`mov_det_in`.`data_mov`, `detentos`.`nome_det`";
            break;
        case 'raio':
            $ordbusca = "`raio`.`idraio`, `detentos`.`cod_cela`, `detentos`.`nome_det`";
            break;
    }

    $letras = array(
                0 => '',
                1 => 'A',
                2 => 'B',
                3 => 'C',
                4 => 'D',
                5 => 'E',
                6 => 'F',
                7 => 'G',
                8 => 'H',
                9 => 'I',
                10 => 'J',
                11 => 'K',
                12 => 'L',
                13 => 'M',
                14 => 'N',
                15 => 'O',
                16 => 'P',
                17 => 'Q',
                18 => 'R',
                19 => 'S',
                20 => 'T',
                21 => 'U',
                22 => 'V',
                23 => 'X',
                24 => 'Y',
                25 => 'W',
                26 => 'Z');

    $last_col = $letras["$cont_campos"];

/** PHPExcel */
require_once 'classes/excel/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

    // Set properties
    $objPHPExcel->getProperties()->setCreator("SICOP - Sistema de Controle de Presos Provisórios")
                                 ->setLastModifiedBy("SICOP - Sistema de Controle de Presos Provisórios")
                                 ->setTitle("Lista de audiências")
                                 ->setSubject("Lista de audiências")
                                 ->setDescription("Lista de audiências.");

    $consulta = "SELECT
                   `detentos`.`iddetento`
                   , `detentos`.`nome_det`
                   , `detentos`.`matricula`
                   , `detentos`.`sit_det`
                   $campos
                 FROM
                   `detentos`
                    LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                    LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                    LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
                    LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                    $join
                 $where
                 ORDER BY
                   $ordbusca";



    $resultado = mysql_query($consulta);
    if($resultado==true){
        $i=1; //linha da planilha
        $n=0; //numero de sequencia

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, 'N')
                    //->setCellValue('B'.$i, 'ID')
                    ->setCellValue('B'.$i, 'NOME')
                    ->setCellValue('C'.$i, 'MATRICULA');


        $col_atual     = 3; // COLUNA C
        $col_rg        = '';
        $col_raio      = '';
        $col_cela      = '';
        $col_proc      = '';
        $col_data_in   = '';
        $col_dest      = '';
        $col_data_out  = '';
        $col_pai       = '';
        $col_mae       = '';
        $col_data_nasc = '';
        $col_idade     = '';
        $col_nat       = '';

        if ( !empty( $rg ) ){

            $col_atual += 1; // acrecenta +1 coluna
            $col_rg = $letras["$col_atual"]; // indica a classe qual é a coluna do item
            $objPHPExcel->getActiveSheet()->setCellValue( $col_rg . $i, 'R.G.' ); // define a label da primeira linha

        }

        if ( !empty( $rc_ck ) ){

            $col_atual += 1;
            $col_raio = $letras["$col_atual"];
            $objPHPExcel->getActiveSheet()->setCellValue( $col_raio . $i, mb_strtoupper( SICOP_RAIO ) );

            $col_atual += 1;
            $col_cela = $letras["$col_atual"];
            $objPHPExcel->getActiveSheet()->setCellValue( $col_cela . $i, mb_strtoupper( SICOP_CELA ) );

        }

        if ( !empty( $proc_ck ) ){

            $col_atual += 1;
            $col_proc = $letras["$col_atual"];
            $objPHPExcel->getActiveSheet()->setCellValue( $col_proc . $i, 'PROCEDÊNCIA' );

        }

        if ( !empty( $data_in_ck ) ){

            $col_atual += 1;
            $col_data_in = $letras["$col_atual"];
            $objPHPExcel->getActiveSheet()->setCellValue( $col_data_in . $i, 'DATA DA INCLUSÃO' );

        }

        if ( !empty( $dest_ck ) ){

            $col_atual += 1;
            $col_dest = $letras["$col_atual"];
            $objPHPExcel->getActiveSheet()->setCellValue( $col_dest . $i, 'DESTINO' );

        }

        if ( !empty( $data_out_ck ) ){

            $col_atual += 1;
            $col_data_out = $letras["$col_atual"];
            $objPHPExcel->getActiveSheet()->setCellValue( $col_data_out . $i, 'DATA DA EXCLUSÃO' );

        }

        if ( !empty( $pai ) ){

            $col_atual += 1;
            $col_pai = $letras["$col_atual"];
            $objPHPExcel->getActiveSheet()->setCellValue( $col_pai . $i, 'NOME DO PAI' );

        }

        if ( !empty( $mae ) ){

            $col_atual += 1;
            $col_mae = $letras["$col_atual"];
            $objPHPExcel->getActiveSheet()->setCellValue( $col_mae . $i, 'NOME DA MÃE' );

        }

        if ( !empty( $data_nasc ) ){

            $col_atual += 1;
            $col_data_nasc = $letras["$col_atual"];
            $objPHPExcel->getActiveSheet()->setCellValue( $col_data_nasc . $i, 'NASCIMENTO' );

            $col_atual += 1;
            $col_idade = $letras["$col_atual"];
            $objPHPExcel->getActiveSheet()->setCellValue( $col_idade . $i, 'IDADE' );

        }

        if ( !empty( $nat ) ){

            $col_atual += 1;
            $col_nat = $letras["$col_atual"];
            $objPHPExcel->getActiveSheet()->setCellValue( $col_nat . $i, 'NATURALIDADE' );

        }

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);

        if ( !empty( $col_rg ) ){

            $objPHPExcel->getActiveSheet()->getColumnDimension( $col_rg )->setAutoSize(true);

        }

        if ( !empty( $col_raio ) ){

            $objPHPExcel->getActiveSheet()->getColumnDimension( $col_raio )->setAutoSize(true);

        }

        if ( !empty( $col_cela ) ){

            $objPHPExcel->getActiveSheet()->getColumnDimension( $col_cela )->setAutoSize(true);

        }

        if ( !empty( $col_proc ) ){

            $objPHPExcel->getActiveSheet()->getColumnDimension( $col_proc )->setAutoSize(true);

        }

        if ( !empty( $col_data_in ) ){

            $objPHPExcel->getActiveSheet()->getColumnDimension( $col_data_in )->setAutoSize(true);

        }

        if ( !empty( $col_dest ) ){

            $objPHPExcel->getActiveSheet()->getColumnDimension( $col_dest )->setAutoSize(true);

        }

        if ( !empty( $col_data_out ) ){

            $objPHPExcel->getActiveSheet()->getColumnDimension( $col_data_out )->setAutoSize(true);

        }

        if ( !empty( $col_pai ) ){

            $objPHPExcel->getActiveSheet()->getColumnDimension( $col_pai )->setAutoSize(true);

        }

        if ( !empty( $col_mae ) ){

            $objPHPExcel->getActiveSheet()->getColumnDimension( $col_mae )->setAutoSize(true);

        }

        if ( !empty( $col_data_nasc ) ){

            $objPHPExcel->getActiveSheet()->getColumnDimension( $col_data_nasc )->setAutoSize(true);

        }

        if ( !empty( $col_idade ) ){

            $objPHPExcel->getActiveSheet()->getColumnDimension( $col_idade )->setAutoSize(true);

        }

        if ( !empty( $col_nat ) ){

            $objPHPExcel->getActiveSheet()->getColumnDimension( $col_nat )->setAutoSize(true);

        }

        while($linha = mysql_fetch_array($resultado)){
            ++$i;
            ++$n;
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$i, $n)
                        //->setCellValue('B'.$i, $linha['iddetento'])
                        ->setCellValue('B'.$i, $linha['nome_det'])
                        ->setCellValue('C'.$i, !empty( $linha['matricula'] ) ? formata_num( $linha['matricula'] ) : '' );

            if ( !empty( $col_rg ) ){

                $objPHPExcel->getActiveSheet()->setCellValue( $col_rg . $i, !empty( $linha['rg_civil'] ) ? formata_num( $linha['rg_civil'] ) : '' );

            }

            if ( !empty( $col_raio ) ){

                $objPHPExcel->getActiveSheet()->setCellValue( $col_raio . $i, $linha['raio'] );

            }

            if ( !empty( $col_cela ) ){

                $objPHPExcel->getActiveSheet()->setCellValue( $col_cela . $i, $linha['cela'] );

            }

            if ( !empty( $col_proc ) ){

                $objPHPExcel->getActiveSheet()->setCellValue( $col_proc . $i, $linha['procedencia'] );

            }

            if ( !empty( $col_data_in ) ){

                $objPHPExcel->getActiveSheet()->setCellValue( $col_data_in . $i, $linha['data_incl_f'] );

            }

            if ( !empty( $col_dest ) ){

                $objPHPExcel->getActiveSheet()->setCellValue( $col_dest . $i, $linha['destino'] );

            }

            if ( !empty( $col_data_out ) ){

                $objPHPExcel->getActiveSheet()->setCellValue( $col_data_out . $i, $linha['data_excl_f'] );

            }

            if ( !empty( $col_pai ) ){

                $objPHPExcel->getActiveSheet()->setCellValue( $col_pai . $i, $linha['pai_det'] );

            }

            if ( !empty( $col_mae ) ){

                $objPHPExcel->getActiveSheet()->setCellValue( $col_mae . $i, $linha['mae_det'] );

            }

            if ( !empty( $col_data_nasc ) ){

                $objPHPExcel->getActiveSheet()->setCellValue( $col_data_nasc . $i, $linha['nasc_det'] );

            }

            if ( !empty( $col_idade ) ){

                $objPHPExcel->getActiveSheet()->setCellValue( $col_idade . $i, $linha['idade_det'] );

            }

            if ( !empty( $col_nat ) ){

                $objPHPExcel->getActiveSheet()->setCellValue( $col_nat . $i, !empty( $linha['cidade'] ) ? $linha['cidade'] . ' - ' . $linha['estado'] : '' );

            }

            if ($linha['sit_det'] == SICOP_SIT_DET_TRADA){//TRANSITO DA CASA
                $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ));
            }
            if ($linha['sit_det'] == SICOP_SIT_DET_TRANA){//TRANSITO NA CASA
                $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_BLUE ));
            }
            if ($linha['sit_det'] == SICOP_SIT_DET_TRANADA){//TRANSITO NA CASA E DA CASA
                $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ));
            }
            if ($linha['sit_det'] == SICOP_SIT_DET_TRANSF){//TRANSFERIDO
                $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKYELLOW ));
            }
            if ($linha['sit_det'] == SICOP_SIT_DET_EXCLUIDO){//EXCLUIDO (ALVARA)
                $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_GREEN ));
            }
            if ($linha['sit_det'] == SICOP_SIT_DET_EVADIDO){//EVADIDO
                $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_LIGHTBLUE ));
            }
            if ($linha['sit_det'] == SICOP_SIT_DET_FALECIDO){//FALECIDO
                $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_PURPLE ));
            }
            if ($linha['sit_det'] == SICOP_SIT_DET_ACEHGAR){//A CHEGAR
                $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_VIOLET ));
            }

        }// fim do while

        // ALINHAMENTO DAS CELULAS
        //$objPHPExcel->getActiveSheet()->getStyle('A1:' . $last_col . '1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1:A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C1:C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        if ( !empty( $col_rg ) ){

            $objPHPExcel->getActiveSheet()->getStyle( $col_rg . '1:' . $col_rg . $i )->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        }

        if ( !empty( $col_raio ) ){

            $objPHPExcel->getActiveSheet()->getStyle( $col_raio . '1:' . $col_raio . $i )->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        }

        if ( !empty( $col_cela ) ){

            $objPHPExcel->getActiveSheet()->getStyle( $col_cela . '1:' . $col_cela . $i )->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        }

        if ( !empty( $col_proc ) ){

            $objPHPExcel->getActiveSheet()->getStyle( $col_proc.'1' )->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        }

        if ( !empty( $col_data_in ) ){

            $objPHPExcel->getActiveSheet()->getStyle( $col_data_in . '1:' . $col_data_in . $i )->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        }

        if ( !empty( $col_dest ) ){

            $objPHPExcel->getActiveSheet()->getStyle( $col_dest . '1' )->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        }

        if ( !empty( $col_data_out ) ){

            $objPHPExcel->getActiveSheet()->getStyle( $col_data_out . '1:' . $col_data_out . $i )->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        }

        if ( !empty( $col_pai ) ){

            $objPHPExcel->getActiveSheet()->getStyle( $col_pai . '1' )->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        }

        if ( !empty( $col_mae ) ){

            $objPHPExcel->getActiveSheet()->getStyle( $col_mae . '1' )->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        }

        if ( !empty( $col_data_nasc ) ){

            $objPHPExcel->getActiveSheet()->getStyle( $col_data_nasc . '1:' . $col_data_nasc . $i )->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        }

        if ( !empty( $col_idade ) ){

            $objPHPExcel->getActiveSheet()->getStyle( $col_idade . '1:' . $col_idade . $i )->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        }

        if ( !empty( $col_nat ) ){

            $objPHPExcel->getActiveSheet()->getStyle( $col_nat . '1' )->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        }

    }


// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('LISTA DE DETENTOS');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->getCell('A1');


// Redirect output to a client's web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="lista_detentos.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

} else {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de exportação da lista para excel.\n\n Página: $pag";
    salvaLog($mensagem);
    header('Location: ../home.php');
    exit;
}
?>
