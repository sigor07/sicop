<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_export.php';

$pag  = link_pag();
$tipo = '';
$motivo_pag = 'EXPORTAÇÃO DA LISTA DE ' . SICOP_DET_DESC_U . 'S PARA EXCEL';

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
if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );

    exit;

}

/* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
$valor_user = valor_user( $_POST );

$mensagem = "[ EXPORTAÇÃO DE LISTA DE " . SICOP_DET_DESC_U . "S PARA EXCEL ]\n Exportação de lista de " . SICOP_DET_DESC_L . "s para excel.\n\n $valor_user \n";
salvaLog( $mensagem );

$rg           = '';
$exec_ck      = '';
$rc_ck        = '';
$proc_ck      = '';
$data_in_ck   = '';
$dest_ck      = '';
$data_out_ck  = '';
$pai          = '';
$mae          = '';
$data_nasc    = '';
$nat          = '';
$unidade      = '';
$n_cela       = '';
$n_raio       = '';
$data_in_ini  = '';
$data_in_fim  = '';
$data_out_ini = '';
$data_out_fim = '';
$tipo_sit     = '';
$op           = '';

extract( $_POST, EXTR_OVERWRITE );

$campos = '';
$join = '';
$cont_campos = 3; // nome, matricula e o número na lista

if ( !empty( $rg ) ){
    $campos .= ', `detentos`.`rg_civil` ';
    $cont_campos += 1;
}

if ( !empty( $exec_ck ) ){
    $campos .= ', `detentos`.`execucao` ';
    $cont_campos += 1;
}

if ( !empty( $rc_ck ) ){
    $campos .= ', `cela`.`cela`
                , `raio`.`raio` ';

    $join .= ' LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
               LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio` ';
    $cont_campos += 2; // 2 campos => 1 para o raio e 1 para a cela
}

if ( !empty( $proc_ck ) ){
    $campos .= ', `unidades_in`.`unidades` AS procedencia ';
    $cont_campos += 1;
}

if ( !empty( $data_in_ck ) ){
    $campos .= ', DATE_FORMAT(`mov_det_in`.`data_mov`, "%d/%m/%Y") AS data_incl_f ';
    $cont_campos += 1;
}

/*    if ( !empty( $proc_ck ) or !empty( $data_in_ck )  ){

    $join_mov = " LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov` ";

    if ( !empty( $proc_ck ) ){
        $join_mov = " LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                      LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades` ";
    }

    $join .= $join_mov;

}*/

if ( !empty( $dest_ck ) ){
    $campos .= ', `unidades_out`.`unidades` AS destino ';
    $cont_campos += 1;
}

if ( !empty( $data_out_ck ) ){
    $campos .= ', DATE_FORMAT(`mov_det_out`.`data_mov`, "%d/%m/%Y") AS data_excl_f ';
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
    $campos .= ', `detentos`.`pai_det` ';
    $cont_campos += 1;
}

if ( !empty( $mae ) ){
    $campos .= ', `detentos`.`mae_det` ';
    $cont_campos += 1;
}

if ( !empty( $data_nasc ) ){
    $campos .= ', DATE_FORMAT(`detentos`.`nasc_det`, "%d/%m/%Y") AS nasc_det
                , FLOOR( DATEDIFF( CURDATE(), `detentos`.`nasc_det` ) / 365.25 ) AS idade_det ';
    $cont_campos += 2; //2 campos - 1 para a data de nascimento e outro para a idade
}

if ( !empty( $nat ) ){
    $campos .= ', `cidades`.`nome` AS cidade
                , `estados`.`sigla` AS estado ';

    $join .= ' LEFT JOIN `cidades` ON `detentos`.`cod_cidade` = `cidades`.`idcidade`
               LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado` ';
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

    if( empty( $rc_ck ) ) {

        $join .= " LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                   LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio` ";

    }

}

if ( !empty( $data_in_ini ) or !empty( $data_in_fim ) ) {
    if ( !empty( $data_in_ini ) and !empty( $data_in_fim ) ) {
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


$clausula = '';

if ( !empty( $tipo_sit ) ){

    $clausula = get_where_det( $tipo_sit );

}

if ( !empty( $clausula ) ){

    if ( !empty( $where ) ){
        $where .= ' AND ' . $clausula;
    } else {
        $where .= 'WHERE ' . $clausula;
    }

}

$ordpor = 'nome';

if ( !empty( $op ) ) {
    $ordpor = $op;
}

$ordbusca = '';
switch ( $ordpor ) {
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
    case 'dest':
        $ordbusca = "`unidades_out`.`unidades`, `detentos`.`nome_det`";
        break;
    case 'excl':
        $ordbusca = "`mov_det_out`.`data_mov`, `detentos`.`nome_det`";
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
$objPHPExcel->getProperties()->setCreator( "SICOP - Sistema de Controle de Prisional" )
                             ->setLastModifiedBy( "SICOP - Sistema de Controle de Prisional" )
                             ->setTitle( 'Lista de ' . SICOP_DET_DESC_L . 's' )
                             ->setSubject( 'Lista de ' . SICOP_DET_DESC_L . 's' )
                             ->setDescription( 'Lista de ' . SICOP_DET_DESC_L . 's' );

$query = "SELECT
            `detentos`.`iddetento`
            , `detentos`.`nome_det`
            , `detentos`.`matricula`
            , `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in
            , `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out
            , `unidades_out`.`idunidades` AS iddestino
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

$i=1; //linha da planilha
$n=0; //numero de sequencia

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 'N')
            //->setCellValue('B'.$i, 'ID')
            ->setCellValue('B'.$i, 'NOME')
            ->setCellValue('C'.$i, 'MATRICULA');


$col_atual     = 3; // COLUNA C
$col_rg        = '';
$col_exec      = '';
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

if ( !empty( $exec_ck ) ){

    $col_atual += 1; // acrecenta +1 coluna
    $col_exec = $letras["$col_atual"]; // indica a classe qual é a coluna do item
    $objPHPExcel->getActiveSheet()->setCellValue( $col_exec . $i, 'EXECUÇÃO' ); // define a label da primeira linha

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

$objPHPExcel->getActiveSheet()->getColumnDimension( 'A' )->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension( 'B' )->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension( 'C' )->setAutoSize( true );

if ( !empty( $col_rg ) ) {

    $objPHPExcel->getActiveSheet()->getColumnDimension( $col_rg )->setAutoSize( true );

}

if ( !empty( $col_exec ) ) {

    $objPHPExcel->getActiveSheet()->getColumnDimension( $col_exec )->setAutoSize( true );

}

if ( !empty( $col_raio ) ) {

    $objPHPExcel->getActiveSheet()->getColumnDimension( $col_raio )->setAutoSize( true );

}

if ( !empty( $col_cela ) ) {

    $objPHPExcel->getActiveSheet()->getColumnDimension( $col_cela )->setAutoSize( true );

}

if ( !empty( $col_proc ) ) {

    $objPHPExcel->getActiveSheet()->getColumnDimension( $col_proc )->setAutoSize( true );

}

if ( !empty( $col_data_in ) ) {

    $objPHPExcel->getActiveSheet()->getColumnDimension( $col_data_in )->setAutoSize( true );

}

if ( !empty( $col_dest ) ) {

    $objPHPExcel->getActiveSheet()->getColumnDimension( $col_dest )->setAutoSize( true );

}

if ( !empty( $col_data_out ) ) {

    $objPHPExcel->getActiveSheet()->getColumnDimension( $col_data_out )->setAutoSize( true );

}

if ( !empty( $col_pai ) ) {

    $objPHPExcel->getActiveSheet()->getColumnDimension( $col_pai )->setAutoSize( true );

}

if ( !empty( $col_mae ) ) {

    $objPHPExcel->getActiveSheet()->getColumnDimension( $col_mae )->setAutoSize( true );

}

if ( !empty( $col_data_nasc ) ) {

    $objPHPExcel->getActiveSheet()->getColumnDimension( $col_data_nasc )->setAutoSize( true );

}

if ( !empty( $col_idade ) ) {

    $objPHPExcel->getActiveSheet()->getColumnDimension( $col_idade )->setAutoSize( true );

}

if ( !empty( $col_nat ) ) {

    $objPHPExcel->getActiveSheet()->getColumnDimension( $col_nat )->setAutoSize( true );

}

//PHPExcel_Settings::setLocale('pt_br');

while ( $linha = $query->fetch_object() ) {
    ++$i;
    ++$n;
    $objPHPExcel->setActiveSheetIndex( 0 )
                ->setCellValue( 'A' . $i, $n )
                //->setCellValue('B'.$i, $linha->iddetento)
                ->setCellValue( 'B' . $i, $linha->nome_det )
                ->setCellValue( 'C' . $i, !empty( $linha->matricula ) ? formata_num( $linha->matricula ) : ''  );

    if ( !empty( $col_rg ) ){

        $objPHPExcel->getActiveSheet()->setCellValue( $col_rg . $i, !empty( $linha->rg_civil ) ? formata_num( $linha->rg_civil ) : '' );

    }

    if ( !empty( $col_exec ) ){

        $objPHPExcel->getActiveSheet()->setCellValue( $col_exec . $i, $linha->execucao );
        //$objPHPExcel->getActiveSheet()->setCellValue( $col_exec . $i, !empty( $linha->execucao ) ? number_format( $linha->execucao, 0, '', '.' ) : '' );
        //$objPHPExcel->getActiveSheet()->getStyle( $col_exec . $i )->getNumberFormat()->setFormatCode('000,000');

    }

    if ( !empty( $col_raio ) ){

        $objPHPExcel->getActiveSheet()->setCellValue( $col_raio . $i, $linha->raio );

    }

    if ( !empty( $col_cela ) ){

        $objPHPExcel->getActiveSheet()->setCellValue( $col_cela . $i, $linha->cela );

    }

    if ( !empty( $col_proc ) ){

        $objPHPExcel->getActiveSheet()->setCellValue( $col_proc . $i, $linha->procedencia );

    }

    if ( !empty( $col_data_in ) ){

        $objPHPExcel->getActiveSheet()->setCellValue( $col_data_in . $i, $linha->data_incl_f );

    }

    if ( !empty( $col_dest ) ){

        $objPHPExcel->getActiveSheet()->setCellValue( $col_dest . $i, $linha->destino );

    }

    if ( !empty( $col_data_out ) ){

        $objPHPExcel->getActiveSheet()->setCellValue( $col_data_out . $i, $linha->data_excl_f );

    }

    if ( !empty( $col_pai ) ){

        $objPHPExcel->getActiveSheet()->setCellValue( $col_pai . $i, $linha->pai_det );

    }

    if ( !empty( $col_mae ) ){

        $objPHPExcel->getActiveSheet()->setCellValue( $col_mae . $i, $linha->mae_det );

    }

    if ( !empty( $col_data_nasc ) ){

        $objPHPExcel->getActiveSheet()->setCellValue( $col_data_nasc . $i, $linha->nasc_det );

    }

    if ( !empty( $col_idade ) ){

        $objPHPExcel->getActiveSheet()->setCellValue( $col_idade . $i, $linha->idade_det );

    }

    if ( !empty( $col_nat ) ){

        $objPHPExcel->getActiveSheet()->setCellValue( $col_nat . $i, !empty( $linha->cidade ) ? $linha->cidade . ' - ' . $linha->estado : '' );

    }

    $tipo_mov_in  = $linha->tipo_mov_in;
    $tipo_mov_out = $linha->tipo_mov_out;
    $iddestino    = $linha->iddestino;
    $sit_det      = manipula_sit_det_id( $tipo_mov_in, $tipo_mov_out, $iddestino );

    if ( $sit_det == SICOP_SIT_DET_TRADA or $sit_det == SICOP_SIT_DET_TRANADA ){//TRANSITO DA CASA
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

}// fim do while

// ALINHAMENTO DAS CELULAS
//$objPHPExcel->getActiveSheet()->getStyle('A1:' . $last_col . '1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle( 'A1:A' . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
$objPHPExcel->getActiveSheet()->getStyle( 'B1' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
$objPHPExcel->getActiveSheet()->getStyle( 'C1:C' . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );

if ( !empty( $col_rg ) ){

    $objPHPExcel->getActiveSheet()->getStyle( $col_rg . '1:' . $col_rg . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );

}

if ( !empty( $col_exec ) ) {

    $objPHPExcel->getActiveSheet()->getStyle( $col_exec . '1:' . $col_exec . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
    $objPHPExcel->getActiveSheet()->getStyle( $col_exec . '2:' . $col_exec . $i )->getNumberFormat()->setFormatCode('000,000');

}

if ( !empty( $col_raio ) ) {

    $objPHPExcel->getActiveSheet()->getStyle( $col_raio . '1:' . $col_raio . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
}

if ( !empty( $col_cela ) ) {

    $objPHPExcel->getActiveSheet()->getStyle( $col_cela . '1:' . $col_cela . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
}

if ( !empty( $col_proc ) ) {

    $objPHPExcel->getActiveSheet()->getStyle( $col_proc . '1' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
}

if ( !empty( $col_data_in ) ) {

    $objPHPExcel->getActiveSheet()->getStyle( $col_data_in . '1:' . $col_data_in . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
}

if ( !empty( $col_dest ) ) {

    $objPHPExcel->getActiveSheet()->getStyle( $col_dest . '1' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
}

if ( !empty( $col_data_out ) ) {

    $objPHPExcel->getActiveSheet()->getStyle( $col_data_out . '1:' . $col_data_out . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
}

if ( !empty( $col_pai ) ) {

    $objPHPExcel->getActiveSheet()->getStyle( $col_pai . '1' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
}

if ( !empty( $col_mae ) ) {

    $objPHPExcel->getActiveSheet()->getStyle( $col_mae . '1' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
}

if ( !empty( $col_data_nasc ) ) {

    $objPHPExcel->getActiveSheet()->getStyle( $col_data_nasc . '1:' . $col_data_nasc . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
}

if ( !empty( $col_idade ) ) {

    $objPHPExcel->getActiveSheet()->getStyle( $col_idade . '1:' . $col_idade . $i )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
}

if ( !empty( $col_nat ) ) {

    $objPHPExcel->getActiveSheet()->getStyle( $col_nat . '1' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
}

// COLOCANDO O ALINHAMENTO VERTICAL DA CELULAS CENTRALIZADO
$objPHPExcel->getActiveSheet()->getStyle( 'A1:' . $last_col . $i )->getAlignment()->setVertical( PHPExcel_Style_Alignment::VERTICAL_CENTER );

// COLOCANDO A FONTE EM NEGRITO DA PRIMEIRA LINHA
$objPHPExcel->getActiveSheet()->getStyle( 'A1:' . $last_col . '1' )->getFont()->setBold( true );

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
$objPHPExcel->getActiveSheet()->setTitle( 'LISTA DE ' . SICOP_DET_DESC_U . 'S' );

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex( 0 );
$objPHPExcel->getActiveSheet()->getCell( 'A1' );

// Redirect output to a client's web browser (Excel5)
header( 'Content-Type: application/vnd.ms-excel' );
header( 'Content-Disposition: attachment;filename="lista_' . SICOP_DET_DESC_L . 's.xls"' );
header( 'Cache-Control: max-age=0' );

$objWriter = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel5' );
$objWriter->save( 'php://output' );
exit;

?>
