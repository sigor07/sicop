<?php

$motivo_pag = 'QUALIFICATIVA DE DETENTO - TOP';

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
            `tipoartigo`.`artigo`,
            `tiponacionalidade`.`nacionalidade`,
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
            `raio`.`raio`,
            `det_fotos`.`foto_det_g`,
            `det_fotos`.`foto_det_p`
          FROM
            `detentos`
            LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
            LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
            LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
            LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
            LEFT JOIN `cidades` ON `detentos`.`cod_cidade` = `cidades`.`idcidade`
            LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado`
            LEFT JOIN `tipoartigo` ON `detentos`.`cod_artigo` = `tipoartigo`.`idartigo`
            LEFT JOIN `tiponacionalidade` ON `detentos`.`cod_nacionalidade` = `tiponacionalidade`.`idnacionalidade`
            LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
            LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
            LEFT JOIN `det_fotos` ON `det_fotos`.`id_foto` = `detentos`.`cod_foto`
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

$foto_g = $d_det['foto_det_g'];
$foto_p = $d_det['foto_det_p'];

$foto_det = ck_pic( $foto_g, $foto_p, false, 1 );

$amplia = true;
if ( empty( $foto_g ) ) {
    $amplia = false;
}

$tipo_mov_in = $d_det['tipo_mov_in'];
$procedencia = $d_det['procedencia'];
$data_incl = $d_det['data_incl'];
$tipo_mov_out = $d_det['tipo_mov_out'];
$iddestino = $d_det['iddestino'];
$destino = $d_det['destino'];
$data_excl = $d_det['data_excl'];

$det = manipula_sit_det_c( $tipo_mov_in, $procedencia, $data_incl, $tipo_mov_out, $iddestino, $destino, $data_excl );

?>

<p class="table_leg"><?php echo SICOP_DET_DESC_FU; ?></p>

<table class="detal_det_min">
    <tr>
        <td class="mid"><?php echo SICOP_DET_DESC_FU; ?>: <a href="<?php echo SICOP_ABS_PATH; ?>detento/detalhesdet.php?iddet=<?php echo $iddet; ?>" title="Clique aqui para abrir a qualificativa deste detento"><?php echo $d_det['nome_det'] ?></a></td>
        <td class="mini">Matrícula: <?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] ) ?></td>
        <td class="mini" rowspan="8" align="center">
            <?php if ( $amplia ){ ?>
            <a id="link_foto_det" href="<?php echo SICOP_DET_IMG_PATH . $foto_g; ?>" title="<?php echo $d_det['nome_det']; if ( !empty( $d_det['matricula'] ) ) echo ' - ' . formata_num( $d_det['matricula'] ) ?>">
            <?php }; ?>
            <img src="<?php echo $foto_det ?>" alt="" class="foto_det" />
            <?php if ( $amplia ){ ?></a><?php } ?>
        </td>
    </tr>
    <tr>
        <td class="mid">Artigo: <?php echo $d_det['artigo'] ?></td>
        <td class="mini">RG Civil: <?php if ( !empty( $d_det['rg_civil'] ) ) echo formata_num( $d_det['rg_civil'] ) ?></td>
    </tr>
    <tr>
        <td class="mid">Vulgo(s): <?php echo $d_det['vulgo'] ?></td>
        <td class="mini">Execução: <?php echo !empty( $d_det['execucao'] ) ? number_format( $d_det['execucao'], 0, '', '.' ) : 'N/C' ?></td>
    </tr>
    <tr>
        <td class="mid">Nacionalidade: <?php echo $d_det['nacionalidade'] ?></td>
        <td class="mini">ID no sistema: <?php echo $d_det['iddetento'] ?></td>
    </tr>
    <tr>
        <td class="mid">Data de nascimento: <?php echo (empty( $d_det['nasc_det'] )) ? '' : $d_det['nasc_det'] . ' - ' . $d_det['idade_det'] . ' anos'; // echo pegaIdade($d_det['data_nasc'])   ?></td>
        <td class="mini"><?php echo empty( $d_det['d_det_prov'] ) ? '' : 'Dados provisórios na PRODESP'; ?></td>
    </tr>
    <tr>
        <td class="mid">Cidade: <?php echo $d_det['cidade'] . " - " . $d_det['estado'] ?></td>
        <td class="mini"></td>
    </tr>
    <tr>
        <td class="mid">Pai: <?php echo $d_det['pai_det'] ?></td>
        <td class="mini">&nbsp;</td>
    </tr>
    <tr>
        <td class="mid">Mãe: <?php echo $d_det['mae_det'] ?></td>
        <td class="mini">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="3" class="quebra_table">MOVIMENTAÇÃO E LOCALIZAÇÃO</td>
    </tr>
    <tr>
        <td class="mid">Situação atual: <b><span style="font-size: 12px;" class="<?php echo $det['css_class'];?>"><?php echo $det['sitat'] ?></span></b></td>
        <td class="mini_rc"><?php echo empty( $d_det['raio'] ) ? '' : SICOP_RAIO . ': ' . $d_det['raio']; ?></td>
        <td class="mini_rc"><?php echo empty( $d_det['cela'] ) ? '' : SICOP_CELA . ': ' . $d_det['cela']; ?></td>
    </tr>
    <tr>
      <td class="mid"><?php echo $det['data_incl'] ?></td>
      <td class="mid" colspan="2"><?php echo $det['data_excl'] ?></td>
    </tr>
    <tr>
        <td class="mid"><?php echo $det['procedencia'] ?></td>
        <td class="mid" colspan="2"><?php echo $det['destino'] ?></td>
    </tr>
</table>