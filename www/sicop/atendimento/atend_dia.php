<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_chefia   = get_session( 'n_chefia', 'int' );
$n_chefia_n = 2;

if ( $n_chefia < $n_chefia_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'ATENDIMENTOS DO DIA';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$query = "( SELECT
              `detentos`.`iddetento`,
              `detentos`.`nome_det`,
              `detentos`.`matricula`,
              `raio`.`raio`,
              `cela`.`cela`,
              DATE_FORMAT ( `audiencias`.`hora_aud`, '%H:%i' ) AS `hora_aud_f`,
              `audiencias`.`local_aud`,
              `audiencias`.`cidade_aud`,
              `audiencias`.`tipo_aud`
            FROM
              `detentos`
              LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
              LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
              LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
              LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
              INNER JOIN `audiencias` ON `audiencias`.`cod_detento` = `detentos`.`iddetento`
            WHERE
              `audiencias`.`data_aud` = DATE( NOW() )
              AND
              `audiencias`.`hora_aud` <= '12:00'
              AND
              ( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 )
              AND
              ( `mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 2 OR `mov_det_in`.`cod_tipo_mov` = 3 )
            ORDER BY
              `audiencias`.`cidade_aud`, `audiencias`.`tipo_aud`, `audiencias`.`hora_aud`, `audiencias`.`local_aud` LIMIT 10000 )

          UNION ALL

          ( SELECT
              `detentos`.`iddetento`,
              `detentos`.`nome_det`,
              `detentos`.`matricula`,
              `raio`.`raio`,
              `cela`.`cela`,
              DATE_FORMAT ( `audiencias`.`hora_aud`, '%H:%i' ) AS `hora_aud_f`,
              `audiencias`.`local_aud`,
              `audiencias`.`cidade_aud`,
              `audiencias`.`tipo_aud`
            FROM
              `detentos`
              LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
              LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
              LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
              LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
              INNER JOIN `audiencias` ON `audiencias`.`cod_detento` = `detentos`.`iddetento`
            WHERE
              `audiencias`.`data_aud` = DATE(NOW())
              AND
              `audiencias`.`hora_aud` > '12:00'
              AND
              ( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 )
              AND
              ( `mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 2 OR `mov_det_in`.`cod_tipo_mov` = 3 )
            ORDER BY
              `audiencias`.`cidade_aud`, `audiencias`.`tipo_aud`, `audiencias`.`local_aud`, `audiencias`.`hora_aud` LIMIT 10000 )";


$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Atendimentos do dia';

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>
        <script type="text/javascript" src="<?php echo SICOP_ABS_PATH ?>js/funcoes.js"></script>
        <div class="no_print">

            <p align="center" class="paragrafo14Italico">ATENDIMENTOS DO DIA</p>




<?php include 'footer.php'; ?>