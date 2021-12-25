<?php

$motivo_pag = 'QUALIFICATIVA DE DETENTO - CADASTRO';

if ( basename( $_SERVER['PHP_SELF'] ) == basename( __FILE__ ) ) {

    if ( !isset( $_SESSION ) ) session_start();

    require 'funcoes_init.php';

    $pag = link_pag();

    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto ao arquivo ( $motivo_pag ).\n\n Página: $pag";
    salvaLog( $mensagem );

    redir( 'home' );

    exit;

}

$query = "SELECT
            `detentos`.`iddetento`,
            `detentos`.`nome_det`,
            `detentos`.`matricula`,
            `detentos`.`rg_civil`,
            `detentos`.`execucao`,
            `detentos`.`vulgo`,
            `detentos`.`nasc_det`,
            DATE_FORMAT(`detentos`.`nasc_det`, '%d/%m/%Y') AS nasc_det,
            FLOOR(DATEDIFF(CURDATE(), `detentos`.`nasc_det`) / 365.25) AS idade_det,
            `detentos`.`pai_det`,
            `detentos`.`mae_det`,
            `cidades`.`nome` AS cidade,
            `estados`.`sigla` AS estado,
            `mov_det_in`.`data_mov` AS data_incl,
            DATE_FORMAT(`mov_det_in`.`data_mov`, '%d/%m/%Y') AS data_incl_f,
            `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
            `mov_det_out`.`data_mov` AS data_excl,
            DATE_FORMAT(`mov_det_out`.`data_mov`, '%d/%m/%Y') AS data_excl_f,
            `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
            `unidades_in`.`unidades` AS procedencia,
            `unidades_out`.`unidades` AS destino,
            `unidades_out`.`idunidades` AS iddestino,
            `cela`.`cela`,
            `raio`.`raio`
          FROM
            `detentos`
            LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
            LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
            LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
            LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
            LEFT JOIN `cidades` ON `detentos`.`cod_cidade` = `cidades`.`idcidade`
            LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado`
            LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
            LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
          WHERE
            `detentos`.`iddetento` = $iddet
          LIMIT 1";

$db = SicopModel::getInstance();
$query = $db->query( $query );
if ( !$query ) {

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();
    $db->closeConnection();

    // gerar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ERR );
    $msg->add_quebras( 1 );
    $msg->set_msg_pre_def( SM_QUERY_FAIL );
    $msg->add_parenteses( $motivo_pag );
    $msg->add_quebras( 2 );
    $msg->set_msg( $msg_err_mysql );
    $msg->get_msg();

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$db->closeConnection();

$cont = $query->num_rows;
if ( $cont < 1 ) {

    // gerar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ERR );
    $msg->add_quebras( 1 );
    $msg->set_msg( "A consulta retornou 0 ocorrências" );
    $msg->add_parenteses( $motivo_pag );
    $msg->add_quebras( 2 );
    $msg->get_msg();

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$d_det = $query->fetch_assoc();

$tipo_mov_in = $d_det['tipo_mov_in'];
$procedencia = $d_det['procedencia'];
$data_incl = $d_det['data_incl'];
$tipo_mov_out = $d_det['tipo_mov_out'];
$iddestino = $d_det['iddestino'];
$destino = $d_det['destino'];
$data_excl = $d_det['data_excl'];

$det = manipula_sit_det_c( $tipo_mov_in, $procedencia, $data_incl, $tipo_mov_out, $iddestino, $destino, $data_excl );

$iddetento = $d_det['iddetento'];
$nome_det = $d_det['nome_det'];

$detento = '<a href="' . SICOP_ABS_PATH . 'detento/detalhesdet.php?iddet=' . $iddetento . '" title="Clique aqui para abrir a qualificativa deste detento">' . $nome_det . '</a>';

if ( isset( $targ ) and $targ == 1 ) {
    $detento = $nome_det;
}
?>
<p class="table_leg"><?php echo SICOP_DET_DESC_FU; ?></p>
<table class="detal_det_min">
    <tr>
        <td class="mid" colspan="2">Nome: <?php echo $detento; ?></td>
        <td class="mid" colspan="2">Data de nascimento: <?php echo (empty( $d_det['nasc_det'] )) ? '' : $d_det['nasc_det'] . " - " . $d_det['idade_det'] . " anos"; // echo pegaIdade($d_det['data_nasc'])   ?></td>
    </tr>
    <tr>
        <td class="mini">Matrícula: <?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] ) ?></td>
        <td class="mini">Execução: <?php echo!empty( $d_det['execucao'] ) ? number_format( $d_det['execucao'], 0, '', '.' ) : 'N/C' ?></td>
        <td class="mid" colspan="2">Cidade: <?php echo $d_det['cidade'] . ' - ' . $d_det['estado'] ?></td>
    </tr>
    <tr>
        <td class="mini">RG Civil: <?php if ( !empty( $d_det['rg_civil'] ) ) echo formata_num( $d_det['rg_civil'] ) ?></td>
        <td class="mini">ID no sistema: <?php echo $d_det['iddetento'] ?></td>
        <td class="mid" colspan="2">Pai: <?php echo $d_det['pai_det'] ?></td>
    </tr>
    <tr>
        <td class="mid" colspan="2">Vulgo(s): <?php echo $d_det['vulgo'] ?></td>
        <td class="mid" colspan="2">Mãe: <?php echo $d_det['mae_det'] ?></td>
    </tr>
    <tr>
        <td class="mid" colspan="2">Situação atual: <b><span class="<?php echo $det['css_class'];?>"><?php echo $det['sitat']; ?></span></b></td>
        <td class="mini_rc"><?php echo empty( $d_det['raio'] ) ? '' : SICOP_RAIO . ': ' . $d_det['raio']; ?></td>
        <td class="mini_rc"><?php echo empty( $d_det['cela'] ) ? '' : SICOP_CELA . ': ' . $d_det['cela']; ?></td>
    </tr>
    <tr>
        <td class="mid" colspan="2"><?php echo $det['data_incl']; ?></td>
        <td class="mid" colspan="2"><?php echo $det['data_excl']; ?></td>
    </tr>
    <tr>
        <td class="mid" colspan="2"><?php echo $det['procedencia']; ?></td>
        <td class="mid" colspan="2"><?php echo $det['destino']; ?></td>
    </tr>
</table>
