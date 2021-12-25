<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';

$pag  = link_pag();
$tipo = '';

$imp_chefia   = get_session( 'imp_chefia' , 'int' );
$imp_cadastro = get_session( 'imp_cadastro' , 'int' );
$iduser       = get_session( 'user_id', 'int' );
$n_imp_n      = 1;

$motivo_pag = 'IMPRESSÃO DE LISTA DE ' . SICOP_DET_DESC_U . 'S';

if ( $imp_chefia < $n_imp_n and $imp_cadastro < $n_imp_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 'f' );

    exit;

}

$titulo  = get_session( 'titulo' );
$ip      = $_SERVER['REMOTE_ADDR'];
$maquina = substr( $ip, strrpos( $ip, '.' ) + 1 );

$data_in_ini  = get_get( 'data_in_ini', 'busca' );
$data_in_fim  = get_get( 'data_in_fim', 'busca' );
$data_out_ini = get_get( 'data_out_ini', 'busca' );
$data_out_fim = get_get( 'data_out_fim', 'busca' );
$unidade      = get_get( 'unidade', 'int' );
$n_cela       = get_get( 'n_cela', 'int' );
$n_raio       = get_get( 'n_raio', 'int' );
$tipo_sit     = get_get( 'tipo_sit', 'int' );
$op           = get_get( 'op', 'busca' );

$where = '';

$tipo_fon = get_get( 'tipo_fon', 'int' );
$tipo_fon = !empty( $tipo_fon ) ? (int)$tipo_fon : '';

if ( !empty( $_GET['campobusca'] ) ) {

    $valorbusca = $_GET['campobusca'];
    $valorbusca = tratabusca($valorbusca);

    if ( $tipo_fon == 1 ) {

        $where .= "WHERE ( `detentos`.`nome_det` LIKE '%$valorbusca%'
                          OR
                          `detentos`.`matricula` LIKE '$valorbusca%'
                          OR
                          ( `aliases`.`cod_tipoalias` = 4 AND `aliases`.`alias_det` LIKE '%$valorbusca%' ) )";

    } else {

        $valorbusca = preg_replace( '/\s?\b\w{1,2}\b/' , null , $valorbusca ); // remover palavras com 2 letras ou menos

        if ( empty($valorbusca) ) {
            echo '<script type="text/javascript">self.window.close();</script>';
            exit;
        }

        $arr_busca = explode( ' ', $valorbusca );

        $where .= 'WHERE (';
        foreach( $arr_busca as $indice => $valor ) {
            if ($valor == NULL) continue;
            $where .= " `detentos`.`nome_det` LIKE '%$valor%' AND";
        }

        $where_alias = '(';
        foreach( $arr_busca as $indice => $valor ) {
            if ($valor == NULL) continue;
            $where_alias .= " `aliases`.`alias_det` LIKE '%$valor%' AND";
        }

    }

    if ( $tipo_fon == 2 ) {

        if ( !empty( $where ) ) {
            $where = substr($where, 0, -3); //remover o ultimo 'AND'
            $where = $where . " OR `detentos`.`matricula` LIKE '$valorbusca%' ";
        }

        $where_alias_f = '';
        if ( !empty( $where_alias ) ) {
            $where_alias_f = substr($where_alias, 0, -3); //remover o ultimo 'AND'
            $where_alias_f =  " OR ( `aliases`.`cod_tipoalias` = 4 AND " . $where_alias_f . ' ) )';
        }

        $where = $where . $where_alias_f . ' )';

    }

}

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

if ( !empty( $data_in_ini ) or !empty( $data_in_fim ) ){

    if ( !empty( $data_in_ini ) and  !empty( $data_in_fim ) ){

        $clausula_data_out = "`mov_det_in`.`data_mov` BETWEEN STR_TO_DATE('$data_in_ini', '%d/%m/%Y') AND STR_TO_DATE('$data_in_fim', '%d/%m/%Y')";

        if ( !empty( $where ) ){
            $where .= ' AND ' . $clausula_data_out;
        } else {
            $where .= 'WHERE ' . $clausula_data_out;
        }

    } else {

        $data_in = !empty( $data_in_ini ) ? $data_in_ini : $data_in_fim;

        $clausula_data_out = "`mov_det_in`.`data_mov` = STR_TO_DATE( '$data_in', '%d/%m/%Y' )";

        if ( !empty( $where ) ){
            $where .= ' AND ' . $clausula_data_out;
        } else {
            $where .= 'WHERE ' . $clausula_data_out;
        }

    }

}

if ( !empty( $data_out_ini ) or !empty( $data_out_fim ) ){

    if ( !empty( $data_out_ini ) and  !empty( $data_out_fim ) ){

        $clausula_data_out = "`mov_det_out`.`data_mov` BETWEEN STR_TO_DATE('$data_out_ini', '%d/%m/%Y') AND STR_TO_DATE('$data_out_fim', '%d/%m/%Y')";

        if ( !empty( $where ) ){
            $where .= ' AND ' . $clausula_data_out;
        } else {
            $where .= 'WHERE ' . $clausula_data_out;
        }

    } else {

        $data_out = !empty( $data_out_ini ) ? $data_out_ini : $data_out_fim;

        $clausula_data_out = "`mov_det_out`.`data_mov` = STR_TO_DATE( '$data_out', '%d/%m/%Y' )";

        if ( !empty( $where ) ){
            $where .= ' AND ' . $clausula_data_out;
        } else {
            $where .= 'WHERE ' . $clausula_data_out;
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

$ordpor = 'nomea';

if ( !empty( $op ) ) {
    $ordpor = $op;
}

switch($ordpor) {
    default:
    case 'nomea':
        $ordbusca = '`detentos`.`nome_det` ASC';
        break;
    case 'nomed':
        $ordbusca = '`detentos`.`nome_det` DESC';
        break;
    case 'matra':
        $ordbusca = '`detentos`.`matricula` ASC';
        break;
    case 'matrd':
        $ordbusca = '`detentos`.`matricula` DESC';
        break;
    case 'proca':
        $ordbusca = '`unidades_in`.`unidades` ASC, `detentos`.`nome_det` ASC';
        break;
    case 'procd':
        $ordbusca = '`unidades_in`.`unidades` DESC, `detentos`.`nome_det` ASC';
        break;
    case 'dataa':
        $ordbusca = '`mov_det_in`.`data_mov` ASC, `detentos`.`nome_det` ASC';
        break;
    case 'datad':
        $ordbusca = '`mov_det_in`.`data_mov` DESC, `detentos`.`nome_det` ASC';
        break;
    case 'raioa':
        $ordbusca = '`detentos`.`cod_cela` ASC, `detentos`.`nome_det` ASC';
        break;
    case 'raiod':
        $ordbusca = '`detentos`.`cod_cela` DESC, `detentos`.`nome_det` ASC';
        break;
}

$q_lista = "SELECT DISTINCT
              `detentos`.`nome_det`,
              `detentos`.`matricula`,
              `mov_det_in`.`data_mov` AS data_incl,
              DATE_FORMAT(`mov_det_in`.`data_mov`, '%d/%m/%Y') AS data_incl_f,
              `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
              `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
              `unidades_in`.`unidades` AS procedencia,
              `unidades_out`.`idunidades` AS iddestino,
              `raio`.`raio`,
              `cela`.`cela`
            FROM
              `detentos`
              LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
              LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
              LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
              LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
              LEFT JOIN `aliases` ON `detentos`.`iddetento` = `aliases`.`cod_detento`
              LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
              LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
            $where
            ORDER BY
              $ordbusca";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_lista = $model->query( $q_lista );

// fechando a conexao
$model->closeConnection();

if( !$q_lista ) {

    echo msg_js( 'FALHA!!!', 'f' );
    exit;

}

$cont_l = $q_lista->num_rows;

if( $cont_l < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrências ( IMPRESSÃO DA LISTA DE BUSCA ).\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 'f' );
    exit;
}

/*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
$valor_user = valor_user( $_GET );

$mensagem = '[ IMPRESSÃO DE LISTA DE ' . SICOP_DET_DESC_U . "S ]\n Impressão da lista de " . SICOP_DET_DESC_L . "s.\n\n $valor_user \n";
salvaLog($mensagem);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="pt-br" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" Accept-Language="pt-br"/>
        <link rel="shortcut icon" href="<?php echo SICOP_SYS_IMG_PATH; ?>favicon.ico" type="image/x-icon" />
        <title><?php echo $titulo ?></title>
        <link href="<?php echo SICOP_ABS_PATH ?>css/reset.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_p_lista.css" rel="stylesheet" type="text/css" />
    </head>

    <body onload="Javascript:window.print();self.window.close()">
    <!-- onload="Javascript:window.print();self.window.close()" -->

        <div class="corpo">
            <p class="par_corpo">LISTA DE <?php echo SICOP_DET_DESC_U; ?>S</p>

            <table width="650" align="center" class="bordasimples_f" border="1" cellpadding="1" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col" width="40" height="15" align="center">N</th>
                        <th scope="col" width="230" align="center"><?php echo SICOP_DET_DESC_FU; ?></th>
                        <th scope="col" width="75" align="center">Matrícula</th>
                        <th scope="col" width="30" align="center"><?php echo SICOP_RAIO_AB; ?></th>
                        <th scope="col" width="20" align="center"><?php echo SICOP_CELA_AB; ?></th>
                        <th scope="col" width="155" align="center">Procedência</th>
                        <th scope="col" width="70" align="center">Inclusão</th>
                    </tr>
                </thead>
                <?php

                    $i = 0;

                    while( $d_det = $q_lista->fetch_assoc() ) {

                        ?>
                <tbody>
                    <tr>
                        <td height="18" align="center"><?php echo ++$i ?></td>
                        <td align="left" class="espaco_td"><?php echo $d_det['nome_det']?></td>
                        <td align="center"><?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] );?></td>
                        <td align="center"><?php echo $d_det['raio']?></td>
                        <td align="center"><?php echo $d_det['cela']?></td>
                        <td align="left" class="espaco_td"><?php echo $d_det['procedencia']?></td>
                        <td align="right" class="espaco_td"><?php echo $d_det['data_incl_f']?></td>
                    </tr>
                </tbody>
            <?php if ( ( $i%45 == 0 and $i != $cont_l ) and $cont_l != 45  ) { ?>
            </table>

            <p align="right" class="par_min">Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>

            <div style="page-break-before: always;">&nbsp;</div>

            <table width="650" align="center" class="bordasimples_f" border="1" cellpadding="1" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col" width="40" height="15" align="center">N</th>
                        <th scope="col" width="230" align="center"><?php echo SICOP_DET_DESC_FU; ?></th>
                        <th scope="col" width="75" align="center">Matrícula</th>
                        <th scope="col" width="30" align="center"><?php echo SICOP_RAIO_AB; ?></th>
                        <th scope="col" width="20" align="center"><?php echo SICOP_CELA_AB; ?></th>
                        <th scope="col" width="155" align="center">Procedência</th>
                        <th scope="col" width="70" align="center">Inclusão</th>
                    </tr>
                </thead>
            <?php } ?>
            <?php } ?>

            </table>

            <p align="right" class="par_min">Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>

        </div>
    </body>
</html>