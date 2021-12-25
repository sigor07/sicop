<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';
$q_string = '';

$imp_cadastro     = get_session( 'imp_cadastro', 'int' );
$n_det_alt        = get_session( 'n_det_alt', 'int' );
$n_det_alias      = get_session( 'n_det_alias', 'int' );

$n_cadastro       = get_session( 'n_cadastro', 'int' );
$nivel_necessario = 2;

$motivo_pag = 'LISTAR ' . SICOP_DET_ART_U . 'S COM DADOS PROVISÓRIOS';

if ( $n_cadastro < $nivel_necessario ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}


$ordpor = 'nomea';

if ( !empty( $_GET['op'] ) ) {
    $ordpor = get_get( 'op', 'busca' );
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
    case 'ra':
        $ordbusca = '`detentos`.`cod_cela` ASC, `detentos`.`nome_det` ASC';
        break;
    case 'rd':
        $ordbusca = '`detentos`.`cod_cela` DESC, `detentos`.`nome_det` ASC';
        break;
}

$query = "SELECT
            `detentos`.`iddetento`,
            `detentos`.`nome_det`,
            `detentos`.`matricula`,
            `detentos`.`pai_det`,
            `detentos`.`mae_det`,
            `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
            `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
            `unidades_out`.`idunidades` AS iddestino
          FROM
            `detentos`
            LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
            LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
            LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
          WHERE
            `detentos`.`dados_prov` = TRUE
          ORDER BY
            $ordbusca";

//echo nl2br($q_det);
//exit;

$db = SicopModel::getInstance();

$query = $db->query( $query );

$querytime = $db->getQueryTime();

if ( !$query ) {

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();
    $db->closeConnection();

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = 'Falha na consulta ( LISTA DE ' . SICOP_DET_DESC_U . "S COM DADOS PROVISÓRIOS ).\n\n $msg_err_mysql";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );


    echo msg_js( 'FALHA!', 1 );
    exit;

}

$db->closeConnection();

$cont = $query->num_rows;

parse_str( $_SERVER['QUERY_STRING'], $q_string );

if ( isset( $q_string['op'] ) ) {
    unset( $q_string['op'] );
}

$desc_pag = SICOP_DET_DESC_FU . 's com dados provisórios';

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add($desc_pag, $pag_atual, 3);
$trail->output();
?>

            <p class="descript_page">LISTAR <?php echo SICOP_DET_DESC_U; ?>S COM DADOS PROVISÓRIOS</p>

            <p class="p_q_info">Essa consulta retornou <?php echo $cont ?> registros (<?php echo round($querytime, 2) ?> seg). <?php if ( $imp_cadastro >= 1 ) { ?> <a href="javascript:void(0)" title="Imprimir a lista" onclick="javascript: ow('../print/lista_d_prov.php', '600', '600'); return false" >Imprimir</a><?php }; ?></p>

            <table class="lista_busca">

                <tr class="cab">
                    <th class="num_od">N</th>
                    <th class="nome_det"><?php echo SICOP_DET_DESC_FU; ?>
                        <?php echo link_ord_asc( $ordpor, 'nome', $q_string, 'nome d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L ) ?>
                        <?php echo link_ord_desc( $ordpor, 'nome', $q_string, 'nome d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L ) ?>
                    </th>
                    <th class="matr_det">Matrícula
                        <?php echo link_ord_asc( $ordpor, 'matr', $q_string, 'matrícula' ) ?>
                        <?php echo link_ord_desc( $ordpor, 'matr', $q_string, 'matrícula' ) ?>
                    </th>
                    <th class="tb_bt">&nbsp;</th>
                    <th class="tb_bt">&nbsp;</th>
                </tr>
                  <?php
                  $i = 1;

                  while( $d_det = $query->fetch_object() ) {

                      $tipo_mov_in  = $d_det->tipo_mov_in;
                      $tipo_mov_out = $d_det->tipo_mov_out;
                      $iddestino    = $d_det->iddestino;

                      $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );

                      ?>
                <tr class="even">
                    <td class="num_od"><?php echo $i++; ?></td>

                    <td class="nome_det <?php if ( stripos( $ordpor, 'nome' ) !== false ) echo 'ord';?>" title="Pai: <?php echo $d_det->pai_det;?>&#13;Mãe: <?php echo $d_det->mae_det;?>&#13;Situação atual: <?php echo $det['sitat'];?>"><a href="<?php echo SICOP_ABS_PATH; ?>detento/detalhesdet.php?iddet=<?php echo $d_det->iddetento; ?>"> <?php echo $d_det->nome_det;?></a></td>
                    <td class="matr_det <?php echo $det['css_class']; if ( stripos( $ordpor, 'matr' ) !== false ) echo ' ord';?>"><?php echo !empty( $d_det->matricula ) ? formata_num( $d_det->matricula ) : '&nbsp;';?></td>

                    <td class="tb_bt"><?php if ( $n_det_alias >= 1 ) {  ?> <a href='javascript:void(0)' title="Cadastrar alias" onClick="javascript: ow('<?php echo SICOP_ABS_PATH; ?>detento/cadaliasdet.php?iddet=<?php echo $d_det->iddetento; ?>&targ=1&noreload=1', '830', '600'); return false"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>add_alias.png" alt="Cadastrar alias" class="icon_button" /></a> <?php }; ?></td>
                    <td class="tb_bt"><?php if ( $n_det_alt >= 1 ) {  ?> <a href="<?php echo SICOP_ABS_PATH; ?>detento/editdet.php?iddet=<?php echo $d_det->iddetento; ?>" title="Alterar dados" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="Alterar dados" class="icon_button" /></a> <?php }; ?></td>
                </tr>
                <?php } // fim do while ?>
            </table>

<?php include 'footer.php'; ?>