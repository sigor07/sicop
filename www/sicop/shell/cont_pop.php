<?php

setlocale( LC_ALL, 'pt_BR', 'ptb', 'pt-BR', 'pt-br', 'PT-BR', 'pt_BR.ISO-8859-1', 'pt_BR.utf-8', 'portuguese-brazil', 'bra' );
date_default_timezone_set( 'America/Sao_Paulo' );

include '/var/www/sicop/init/config.php';

require 'funcoes_init.php';
require 'funcoes.php';
include 'manipula_erro.php';

set_error_handler( 'manipuladorErros' );

set_time_limit( 0 );

$where_total   = get_where_det( 1 );
$where_na      = get_where_det( 2 );
$where_da      = get_where_det( 3 );
$where_trana   = get_where_det( 4 );
$where_trada   = get_where_det( 5 );
$where_tranada = get_where_det( 11 );
$where_nada    = get_where_det( 12 );

$q_pop_total = "SELECT
                  COUNT(*) AS pop_total
                FROM
                  `detentos`
                  LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                  LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                WHERE
                  $where_total";

$q_pop_trans_na = "SELECT
                     COUNT(*) AS transna
                   FROM
                     `detentos`
                     LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                     LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                   WHERE
                     $where_trana";

$q_pop_trans_da = "SELECT
                     COUNT(*) AS transda
                   FROM
                     `detentos`
                     LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                     LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                   WHERE
                     $where_trada";

$q_pop_trans_nada = "SELECT
                       COUNT(*) AS transnada
                     FROM
                       `detentos`
                       LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                       LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                     WHERE
                       $where_tranada";

$q_pop_nada = "SELECT
                 COUNT(*) AS pop_nada
               FROM
                 `detentos`
                 LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                 LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
               WHERE
                 $where_nada";

$q_pop_na = "SELECT
               COUNT(*) AS pop_na
             FROM
               `detentos`
               LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
               LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
             WHERE
               $where_na";

$q_pop_da = "SELECT
               COUNT(*) AS pop_da
             FROM
               `detentos`
               LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
               LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
             WHERE
               $where_da";


$q_pop = "INSERT INTO
            `cont_pop`
            (
              `cp_data_hora`,
              `cp_trans_na`,
              `cp_trans_da`,
              `cp_trans_nada`,
              `cp_pop_nada`,
              `cp_pop_na`,
              `cp_pop_da`,
              `cp_pop_total`
            )
            VALUES
              (
                NOW(),
                ( $q_pop_trans_na ),
                ( $q_pop_trans_da ),
                ( $q_pop_trans_nada ),
                ( $q_pop_nada ),
                ( $q_pop_na ),
                ( $q_pop_da ),
                ( $q_pop_total )
              )";

$dh = date( 'd/m/Y \à\s H:i:s' );

$db = SicopModel::getInstance();

$q_pop = $db->query( $q_pop );

$querytime = $db->getQueryTime();

$querytime = round( $querytime, 2 );

if ( $q_pop ) {

    $db->closeConnection();

    // gerar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->add_chaves( 'CONTAGEM POPULACIONAL REGISTRADA', 0, 1 );
    $msg->set_msg( 'Contagem populacional registrada com sucesso' );
    $msg->add_chaves( 'TEMPO GASTO PARA REALIZAR A CONSULTA', 2, 1 );
    $msg->set_msg( "$querytime seg" );
    $msg->get_msg();

    exit;

} else {

    $db->closeConnection();

    // gerar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ERR );
    $msg->add_quebras( 1 );
    $msg->set_msg( 'Falha no registro da contagem populacional' );
    $msg->add_quebras( 2 );
    $msg->get_msg();

    exit;

}
?>