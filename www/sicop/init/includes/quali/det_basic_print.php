<?php

$motivo_pag = 'QUALIFICATIVA DE DETENTO PARA IMPRESSÃO - BÁSICA';

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
            `detentos`.`nome_det`,
            `detentos`.`matricula`,
            `detentos`.`rg_civil`,
            DATE_FORMAT(`detentos`.`nasc_det`, '%d/%m/%Y') AS `nasc_det_f`,
            FLOOR(DATEDIFF(CURDATE(), `detentos`.`nasc_det`) / 365.25) AS `idade_det`,
            `detentos`.`pai_det`,
            `detentos`.`mae_det`,
            `cidades`.`nome` AS `cidade`,
            `estados`.`sigla` AS `estado`,
            `cela`.`cela`,
            `raio`.`raio`
          FROM
            `detentos`
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

$nome_det = $d_det['nome_det'];
$matr     = !empty ( $d_det['matricula'] ) ? formata_num( $d_det['matricula'] ) : '&nbsp;';

?>
<table width="650" align="center" cellpadding="1" cellspacing="0" class="detento">
    <tr >
        <td colspan="3"><b><?php echo SICOP_DET_DESC_FU; ?>:</b> <?php echo $nome_det;?></td>
    </tr>
    <tr>
        <td width="150"><b>RG:</b> <?php echo !empty ( $d_det['rg_civil'] ) ? formata_num( $d_det['rg_civil'] ) : '&nbsp;'; ?></td>
        <td width="150"><b>Matrícula:</b> <?php echo $matr ?></td>
        <td width="342"><b>Nascimento:</b> <?php echo empty( $d_det['nasc_det_f'] ) ? '' : $d_det['nasc_det_f'] . ' - ' . $d_det['idade_det'] . ' anos'; ?></td>
    </tr>
    <tr >
        <td colspan="3"><b>Pai:</b> <?php echo $d_det['pai_det'] ?></td>
    </tr>
    <tr >
        <td colspan="3"><b>Mãe:</b> <?php echo $d_det['mae_det'] ?></td>
    </tr>
    <tr >
        <td colspan="3"><b>Cidade:</b> <?php echo $d_det['cidade'] . ' - ' . $d_det['estado'] ?></td>
    </tr>
</table>