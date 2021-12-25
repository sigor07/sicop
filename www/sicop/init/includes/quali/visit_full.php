<?php

// sub-query utilizada para retornar apenas 1 suspenção
$sub_query_v = "SELECT
                  visita_susp.id_visit_susp
                FROM
                  visita_susp
                WHERE
                  visita_susp.cod_visita = visitas.idvisita
                ORDER BY
                  revog, data_inicio DESC
                LIMIT 1";

$query_visit = "SELECT
                  `visitas`.`idvisita`,
                  `visitas`.`nome_visit`,
                  `visitas`.`rg_visit`,
                  `visitas`.`sexo_visit`,
                  `visitas`.`nasc_visit`,
                  DATE_FORMAT( `visitas`.`nasc_visit`, '%d/%m/%Y' ) AS nasc_visit_f,
                  FLOOR( DATEDIFF( CURDATE(), visitas.nasc_visit )/365.25 ) AS idade_visit,
                  `visitas`.`resid_visit`,
                  `visitas`.`telefone_visit`,
                  `visitas`.`pai_visit`,
                  `visitas`.`mae_visit`,
                  `visitas`.`telefone_visit`,
                  `visitas`.`defeito_fisico`,
                  `visitas`.`sinal_nasc`,
                  `visitas`.`cicatrizes`,
                  `visitas`.`tatuagens`,
                  `cidades`.`nome` AS cidade_visit,
                  `estados`.`sigla` AS estado_visit,
                  `visita_fotos`.`foto_visit_g`,
                  `visita_fotos`.`foto_visit_p`,
                  DATE_FORMAT( `visita_susp`.`data_inicio`, '%d/%m/%Y' ) AS data_inicio_f,
                  visita_susp.periodo,
                  visita_susp.motivo,
                  visita_susp.revog,
                  ADDDATE( visita_susp.data_inicio, visita_susp.periodo ) AS data_fim,
                  DATE_FORMAT( ADDDATE( visita_susp.data_inicio, visita_susp.periodo ), '%d/%m/%Y' ) AS data_fim_f
                FROM
                  `visitas`
                  LEFT JOIN `cidades` ON `visitas`.`cod_cidade_v` = `cidades`.`idcidade`
                  LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado`
                  LEFT JOIN `visita_fotos` ON `visita_fotos`.`id_foto` = `visitas`.`cod_foto`
                  LEFT JOIN visita_susp ON visitas.idvisita = visita_susp.cod_visita
                WHERE
                  `visitas`.`idvisita` = $idvisit
                  AND
                  ( visita_susp.id_visit_susp = ( $sub_query_v ) OR ISNULL( visita_susp.id_visit_susp ) )
                LIMIT 1";

$motivo_pag = 'DETALHES DO VISITANTE - COMPLETA';

$db = SicopModel::getInstance();
$query_visit = $db->query( $query_visit );
if ( !$query_visit ) {

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

$cont = $query_visit->num_rows;
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

$d_visit = $query_visit->fetch_assoc();

$foto_g = $d_visit['foto_visit_g'];
$foto_p = $d_visit['foto_visit_p'];

$foto_visit = ck_pic( $foto_g, $foto_p, false, 2 );

$amplia         = true;
if ( empty( $foto_g ) ) {
    $amplia = false;
}

$revog    = $d_visit['revog'];
$data_fim = $d_visit['data_fim'];

$susp = get_sit_visita( $revog, $data_fim );

$suspenso = $susp['suspenso'];
$excluido = $susp['excluido'];

$inativo = false;
//if ( $num_pass_visit != $n_pass_det ) {
//    $inativo = true;
//}

?>
<p class="table_leg">Visitante</p>

<table class="detal_visit">
    <tr>
        <td class="mid">Visitante: <?php echo $d_visit['nome_visit'] ?></td>
        <td class="mini">RG: <?php echo $d_visit['rg_visit'] ?></td>
        <td class="mini" rowspan="8" align="center">
            <?php if ( $amplia ){ ?>
            <a id="link_foto_visit" href="<?php echo SICOP_VISIT_IMG_PATH . $foto_g ?>" title="<?php echo $d_visit['nome_visit']; if ( !empty( $d_visit['rg_visit'] ) ) echo ' - ' . $d_visit['rg_visit']; ?>">
            <?php }; ?>
            <img src="<?php echo $foto_visit ?>" alt="" class="foto_visit" />
            <?php if ( $amplia ){ ?></a><?php } ?>

        </td>
    </tr>

    <tr>
        <td class="mid">Data de Nascimento: <?php echo ( empty( $d_visit['nasc_visit_f'] ) ) ? '' : $d_visit['nasc_visit_f']  . ' - ' .$d_visit['idade_visit'] . ' anos';// echo pegaIdade($d_visit['data_nasc'])  ?></td>
        <td class="mini">Sexo: <?php echo $d_visit['sexo_visit'] ?></td>
    </tr>
    <tr>
        <td class="mid"></td>
        <td class="mini">ID no sistema: <?php echo $d_visit['idvisita'] ?></td>
    </tr>
    <tr>
        <td class="mid">Endereço: <?php echo $d_visit['resid_visit'] ?></td>
        <td class="mini"></td>
    </tr>
    <tr>
        <td class="mid">Telefone: <?php echo preg_replace( '/([0-9]{2})([0-9]{4})([0-9]{4})/', '(\\1) \\2-\\3', $d_visit['telefone_visit'] ) ?></td>
        <td class="mini"></td>
    </tr>
    <tr>
        <td class="mid">Naturalidade: <?php echo $d_visit['cidade_visit'] ?> - <?php echo $d_visit['estado_visit'] ?></td>
        <td class="mini">&nbsp;</td>
    </tr>
    <tr>
        <td class="mid">Pai: <?php echo $d_visit['pai_visit'] ?></td>
        <td class="mini">&nbsp;</td>
    </tr>
    <tr>
        <td class="mid">Mãe: <?php echo $d_visit['mae_visit'] ?></td>
        <td class="mini">&nbsp;</td>
    </tr>
    <tr>
        <td class="mid">Defeito(s) físico(s): <?php echo $d_visit['defeito_fisico'] ?></td>
        <td class="mid" colspan="2">Sinal(is) de nascimento: <?php echo $d_visit['sinal_nasc'] ?></td>
    </tr>
    <tr>
        <td class="mid">Cicatriz(es): <?php echo $d_visit['cicatrizes'] ?></td>
        <td class="mid" colspan="2">Tatuagem(ns): <?php echo $d_visit['tatuagens'] ?></td>
    </tr>
    <tr>
        <td class="mid">Situação atual do visitante: <span class="<?php echo $susp['css_dest'] ?>"><?php echo $susp['sit_v']; ?></span><?php if ( $inativo ) {?> (Inativo no rol) <?php }; ?></td>
        <td align="center" ><?php if ( $suspenso || $excluido ) { ?> A partir de <?php echo $d_visit['data_inicio_f'] ?> <?php } ?></td>
        <td align="center" ><?php if ( $suspenso ) { ?> Até <?php echo $d_visit['data_fim_f'] ?> <?php } ?></td>
    </tr>
    <?php if ( $suspenso || $excluido ) { ?>
    <tr>
        <td class="great_mot" colspan="3">Motivo: <?php echo $d_visit['motivo'] ?></td>
    </tr>
    <?php } ?>
</table>