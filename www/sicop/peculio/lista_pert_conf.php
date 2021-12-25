<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$imp_peculio     = get_session( 'imp_peculio', 'int' );
$imp_incl        = get_session( 'imp_incl', 'int' );
$n_peculio       = get_session( 'n_peculio', 'int' );
$n_incl          = get_session( 'n_incl', 'int' );
$n_peculio_baixa = get_session( 'n_peculio_baixa', 'int' );

$nivel_n = 2;

if ( $n_peculio < $nivel_n and $n_incl < $nivel_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'PERTENCES PENDENDO CONFIRMAÇÃO - PECÚLIO';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

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
    case 'valora':
        $ordbusca = '`peculio`.`valor` ASC, `detentos`.`nome_det` ASC';
        break;
    case 'valord':
        $ordbusca = '`peculio`.`valor` DESC, `detentos`.`nome_det` ASC';
        break;
    case 'dataa':
        $ordbusca = '`peculio`.`data_add` ASC, `detentos`.`nome_det` ASC';
        break;
    case 'datad':
        $ordbusca = '`peculio`.`data_add` DESC, `detentos`.`nome_det` ASC';
        break;
}

$where = '`peculio`.`confirm` = FALSE AND `peculio`.`retirado` = FALSE';


$query = "SELECT
              `peculio`.`idpeculio`,
              `peculio`.`descr_peculio`,
              `peculio`.`confirm`,
              `peculio`.`data_add`,
              DATE_FORMAT( `peculio`.`data_add`,'%d/%m/%Y' ) AS data_add_f,
              `detentos`.`iddetento`,
              `detentos`.`nome_det`,
              `detentos`.`matricula`,
              `detentos`.`pai_det`,
              `detentos`.`mae_det`,
              `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
              `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
              `unidades_out`.`idunidades` AS iddestino,
              `cela`.`cela`,
              `raio`.`raio`,
              `sicop_users`.`nome_cham`
            FROM
              `peculio`
              INNER JOIN `detentos` ON `peculio`.`cod_detento` = `detentos`.`iddetento`
              LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
              LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
              LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
              LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
              LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
              LEFT JOIN `sicop_users` ON `peculio`.`user_add` = `sicop_users`.`iduser`
            WHERE
              $where
            ORDER BY
              $ordbusca";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query = $model->query( $query );

// fechando a conexao
$model->closeConnection();

if( !$query ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont = $query->num_rows;

$querytime = $model->getQueryTime();

$desc_pag = 'Pertences pendendo confirmação';

// adicionando o javascript
$cab_js   = array();
$cab_js[] = 'valida.js';
$cab_js[] = 'jquery.markrows.js';
set_cab_js( $cab_js );

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>

            <p class="descript_page">PERTENCES PENDENDO CONFIRMAÇÃO</p>

            <?php
            if ( empty( $cont ) or $cont < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';
                include 'footer.php';
                exit;
            }
            ?>

            <p class="p_q_info">Essa consulta retornou <?php echo $cont ?> registros (<?php echo round( $querytime, 2 ) ?> seg).</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendpeculioconf.php" method="post" name="sendpeculioconf" id="sendpeculioconf" >

                <table class="lista_busca grid">
                    <thead>
                        <tr>
                            <th class="num_od">N</th>
                            <th class="nome_det"><?php echo SICOP_DET_DESC_FU; ?>
                                <?php echo link_ord_asc( $ordpor, 'nome', '', 'nome d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L ) ?>
                                <?php echo link_ord_desc( $ordpor, 'nome', '', 'nome d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L ) ?>
                            </th>
                            <th class="matr_det">Matrícula
                                <?php echo link_ord_asc( $ordpor, 'matr', '', 'matrícula' ) ?>
                                <?php echo link_ord_desc( $ordpor, 'matr', '', 'matrícula' ) ?>
                            </th>
                            <th class="descr_pec">Descrição do pertence</th>
                            <th class="desc_data">Data
                                <?php echo link_ord_asc( $ordpor, 'data', '', 'data' ) ?>
                                <?php echo link_ord_desc( $ordpor, 'data', '', 'data' ) ?>
                            </th>
                            <th class="user_pec">Usuário</th>
                            <th class="tb_ck">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                           $i = 0;

                            $corlinha = "#F0F0F0";

                            while ( $d_det = $query->fetch_assoc() ) {

                                $tipo_mov_in  = $d_det['tipo_mov_in'];
                                $tipo_mov_out = $d_det['tipo_mov_out'];
                                $iddestino    = $d_det['iddestino'];

                                $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );

                                ?>

                        <tr class="even" >
                            <td class="num_od"><?php echo ++$i ?></td>
                            <td class="nome_det <?php if ( stripos( $ordpor, 'nome' ) !== false ) echo 'ord';?>" title="Pai: <?php echo $d_det['pai_det'];?>&#13;Mãe: <?php echo $d_det['mae_det'];?>&#13;<?php echo SICOP_RAIO ?>: <?php echo $d_det['raio'];?>&#13;<?php echo SICOP_CELA ?>: <?php echo $d_det['cela'];?>&#13;Situação atual: <?php echo $det['sitat'];?>" ><a href="<?php echo SICOP_ABS_PATH; ?>detento/detalhesdet.php?iddet=<?php echo $d_det['iddetento'];?>" > <?php echo $d_det['nome_det'];?></a></td>
                            <td class="matr_det <?php echo $det['css_class']; if ( stripos( $ordpor, 'matr' ) !== false ) echo ' ord';?>"><font color="<?php echo $det['corfontd'];?>"><?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] );?></font></td>
                            <td class="descr_pec"><?php echo nl2br( $d_det['descr_peculio'] ) ?></td>
                            <td class="desc_data <?php if ( stripos( $ordpor, 'data' ) !== false ) echo 'ord';?>"><?php echo $d_det['data_add_f'] ?></td>
                            <td class="user_pec"><?php echo $d_det['nome_cham'] ?></td>
                            <td class="tb_ck"><input name="idpeculio[]" type="checkbox" id="idpeculio" class="mark_row" value="<?php echo $d_det['idpeculio'] ?>" /></td>
                        </tr>

                        <?php } ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td class="tb_ck_leg" colspan="6">todos:</td>
                            <td class="tb_ck"><input type="checkbox" name="todos" id="todos" value="todos" /></td>
                        </tr>
                    </tfoot>

                </table>

                <input name="proced" type="hidden" id="proced" value="1" />

                <p class="bt_leg">COM MARCADOS</p>

                <div class="form_bts">
                    <?php if ( $imp_peculio >= 1 || $imp_incl >= 1 ) { ?>
                    <input class="form_bt" name="irp" type="submit" value="Imprimir" onclick="return valida_pert_conf(1);" />
                    <?php } ?>
                    <?php if ( $n_peculio_baixa >= 1 ) { ?>
                    <input class="form_bt" name="cnf" type="submit" value="Confirmar" onclick="return valida_pert_conf(2);" />
                    <?php } ?>
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

<?php include 'footer.php'; ?>