<?php

$query = "SELECT
            `visitas`.`idvisita`,
            `visitas`.`nome_visit`,
            `visitas`.`sexo_visit`
          FROM
            `visitas`
          WHERE
            `visitas`.`idvisita` = $idvisit
          LIMIT 1";

$motivo_pag = 'DETALHES DO VISITANTE - BÁSICA';

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

$d_visit = $query->fetch_assoc();

$targ = empty ( $targ ) ? 0 : 1;

?>

<table class="detal_visit_min">
    <tr>
        <td class="leg_min">Identificador (ID):</td>
        <td class="field_min"><?php echo $d_visit['idvisita']; ?></td>
    </tr>
    <tr>
        <td class="leg_min">Visitante:</td>
        <td class="field_min"><?php if ( $targ == 0 ) { ?><a href="<?php echo SICOP_ABS_PATH; ?>visita/detalvisit.php?idvisit=<?php echo $d_visit['idvisita']; ?>" title="Clique aqui para abrir os detalhes deste visitante"><?php }; ?><?php echo $d_visit['nome_visit']; ?></a></td>
    </tr>
    <tr>
        <td class="leg_min">Sexo:</td>
        <td class="field_min"><?php echo $d_visit['sexo_visit'] ?></td>
    </tr>
</table>

