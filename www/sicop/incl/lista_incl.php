<?php

if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$tipo         = '';
$img_sys_path = SICOP_SYS_IMG_PATH;

$n_incl   = get_session( 'n_incl', 'int' );
$n_incl_n = 2;
if ($n_incl < $n_incl_n) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'LISTA DE INCLUSÃO';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$pag  = link_pag();
$ordpor = 'proca';
$q_string = '';

$n_det_alt = get_session( 'n_det_alt', 'int' );
$imp_incl  = get_session( 'imp_incl', 'int' );

$data_sf  = '';
$link     = '';
$cont_det = '';

if( !empty( $_GET['data'] ) ) {

    $mensagem = "Acesso à página $pag";
    salvaLog($mensagem);

    $data_sf = $_GET['data'];
    $data = "'" . tratabusca( $data_sf ) . "'";

    $link = SICOP_ABS_PATH . 'detento/detalhesdet.php';

    $ordpor = 'proca';

    if ( !empty( $_GET['op'] ) ) {
        $ordpor = $_GET['op'];
        $ordpor = tratabusca($ordpor);
    }

    $ordbusca = "`detentos`.`nome_det` ASC";
    switch($ordpor) {
        default:
        case 'nomea':
            $ordbusca = "`detentos`.`nome_det` ASC";
            break;
        case 'nomed':
            $ordbusca = "`detentos`.`nome_det` DESC";
            break;
        case 'matra':
            $ordbusca = "`detentos`.`matricula` ASC";
            break;
        case 'matrd':
            $ordbusca = "`detentos`.`matricula` DESC";
            break;
        case 'proca':
            $ordbusca = "`unidades_in`.`unidades` ASC, `detentos`.`nome_det` ASC";
            break;
        case 'procd':
            $ordbusca = "`unidades_in`.`unidades` DESC, `detentos`.`nome_det` ASC";
            break;
    }

    $q_det = "SELECT
                `detentos`.`iddetento`,
                `detentos`.`nome_det`,
                `detentos`.`matricula`,
                `detentos`.`pai_det`,
                `detentos`.`mae_det`,
                `detentos`.`cod_foto`,
                `det_fotos`.`foto_det_g`,
                `det_fotos`.`foto_det_p`,
                `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
                `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
                `unidades_out`.`idunidades` AS iddestino,
                `unidades_in`.`unidades`
              FROM
                `detentos`
                INNER JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                INNER JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
                LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                LEFT JOIN `det_fotos` ON `det_fotos`.`id_foto` = `detentos`.`cod_foto`
              WHERE
                ( `mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 2 OR `mov_det_in`.`cod_tipo_mov` = 3 )
                AND
                `mov_det_in`.`data_mov` = STR_TO_DATE($data, '%d/%m/%Y')
              ORDER BY
                $ordbusca";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_det = $model->query( $q_det );

    // fechando a conexao
    $model->closeConnection();

    $cont_det = 0;

    if( $q_det ) $cont_det = $q_det->num_rows;

    parse_str( $_SERVER['QUERY_STRING'], $q_string );

    if ( isset( $q_string['op'] ) ) {
        unset( $q_string['op'] );
    }

}

$desc_pag = 'Lista de inclusão';

// adicionando o javascript
$cab_js   = array();
$cab_js[] = 'valida.js';
$cab_js[] = 'ajax/ajax_lista_inc.js';
$cab_js[] = 'ajax/jq_print_det.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3);
$trail->output();
?>

            <p class="descript_page">LISTAR <?php echo SICOP_DET_DESC_U; ?>S POR DATA DE INCLUSÃO</p>

            <form action="lista_incl.php" method="get" name="relat_entr" id="relat_entr">

                <p class="table_leg">Digite ou escolha a data:</p>

                <div class="form_one_field">
                    <input name="data" type="text" class="CaixaTexto" id="data" onBlur="verifica_data(this, this.value)" onKeyPress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_sf ?>" size="12" maxlength="10" />&nbsp;&nbsp;<a href="#" onClick="javascript: datahoje('data'); return false;" >hoje</a>
                </div>

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="buscar" id="buscar" value="Buscar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <input name="datahj" type="hidden" id="datahj" value="<?php echo date('d/m/Y') ?>" />

            <script type="text/javascript">

                $(function() {
                    $( "#data" ).focus();
                    $( "#data" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

            <?php if ( !empty( $_GET['data'] ) ) { ?>

            <?php

            if ( empty( $cont_det ) or $cont_det < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';
                include 'footer.php';
                exit;
            }

            ?>
            <?php if ( $imp_incl >= 1 ) { ?>
            <p class="link_common" style="margin-top: 5px;">
                <a href="#" title="Imprimir esta lista" onclick="javascript: ow('<?php echo SICOP_ABS_PATH; ?>print/lista_casa.php?data_in_ini=<?php echo $data_sf ?>&op=<?php echo $ordpor; ?>', '600', '600'); return false" >Imprimir esta lista</a> - <a href="<?php echo SICOP_ABS_PATH; ?>peculio/lista_pert_conf.php">Pertences não confirmados</a>
            </p>
            <?php } ?>

            <?php if ( $n_det_alt >= 1 ){?>
            <form action="" method="post" name="senddet" id="senddet">
            <?php } ?>

                <table class="lista_busca grid" id="tb_order">
                    <thead>
                        <tr>
                            <th class="num_od_sml">N</th>
                            <th class="nome_det_small"><?php echo SICOP_DET_DESC_FU; ?></th>
                            <th class="matr_det_large">Matrícula</th>
                            <th class="local_mov">Procedência</th>
                            <th class="tb_ck">&nbsp;</th>
                            <th class="tb_ck">&nbsp;</th>
                            <th class="tb_ck">&nbsp;</th>
                            <th class="tb_ck">&nbsp;</th>
                            <th class="tb_ck">&nbsp;</th>
                            <th class="tb_ck">&nbsp;</th>
                            <th class="tb_ck">&nbsp;</th>
                            <th class="tb_ck">&nbsp;</th>
                            <th class="tb_ck">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;

                        while( $d_det = $q_det->fetch_assoc() ) {

                            $tipo_mov_in  = $d_det['tipo_mov_in'];
                            $tipo_mov_out = $d_det['tipo_mov_out'];
                            $iddestino    = $d_det['iddestino'];

                            $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );

                            $img_det_path = SICOP_DET_IMG_PATH;

                            ?>
                        <tr class="even">
                            <td class="num_od_sml"><?php echo $i++; ?></td>
                            <td class="nome_det_small" title="Pai: <?php echo $d_det['pai_det'];?>&#13;Mãe: <?php echo $d_det['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat'];?>"><a href="<?php echo $link; ?>?iddet=<?php echo $d_det['iddetento'] /*alphaID($d_det['iddetento'])*/;?>" > <?php echo $d_det['nome_det']; ?></a></td>
                            <td class="matr_det_large <?php echo $det['css_class'];?>"><span><?php echo !empty( $d_det['matricula'] ) ? formata_num( $d_det['matricula'] ) : '&nbsp;'; ?></span> <?php if ( !empty( $d_det['matricula'] ) ) { ?>&nbsp;&nbsp;<a href="#" title="Copiar a matrícula para a área de transferência" onClick="copy_matr('<?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] );?>'); return false" ><img src="<?php echo $img_sys_path; ?>b_copy.png" alt="Copiar a matrícula para a área de transferência" class="icon_view" /></a>  <a href="#" title="Copiar a matrícula para a área de transferência SEM OS PONTOS E TRAÇO" onClick="copy_matr('<?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] );?>', 1); return false" ><img src="<?php echo $img_sys_path; ?>b_copy_g.png" alt="Copiar a matrícula para a área de transferência SEM OS PONTOS E TRAÇO" class="icon_view" /></a><?php } ?></td>
                            <td class="local_mov <?php echo $det['css_class']; ?>"><?php echo $d_det['unidades'];?></td>
                            <td class="tb_ck"><?php if ( $n_det_alt >= 1 ) {  ?> <a href="<?php echo SICOP_ABS_PATH; ?>detento/editdet.php?iddet=<?php echo $d_det['iddetento']; ?>" title="Alterar dados" ><img src="<?php echo $img_sys_path; ?>b_edit.png" alt="Alterar dados" class="icon_button" /></a> <?php }; ?></td>
                            <td class="tb_ck"><?php if ( $n_det_alt >= 1 and !empty( $d_det['matricula'] ) ) {  ?> <a href="#" title="Ver/cadastrar o pecúlio d<?php echo SICOP_DET_PRON_L; ?> <?php echo SICOP_DET_DESC_L; ?>" onclick="javascript: ow('cadpec.php?iddet=<?php echo $d_det['iddetento']; ?>&targ=1', '830', '600'); return false" ><img src="<?php echo $img_sys_path; ?>cifra_n.png" alt="Ver/cadastrar o pecúlio d<?php echo SICOP_DET_PRON_L; ?> <?php echo SICOP_DET_DESC_L; ?>" class="icon_button" /></a> <?php }; ?></td>

                            <td class="tb_ck"><?php if ( !empty( $d_det['cod_foto'] ) ) {  ?> <a href="<?php echo $img_det_path . $d_det['foto_det_g'];?>" title="<?php echo $d_det['nome_det']; echo !empty( $d_det['matricula'] ) ? ' - ' . formata_num( $d_det['matricula'] ) : '&nbsp;'; ?>" class="link_foto_det"><img src="<?php echo $img_sys_path; ?>eye.png" alt="Ver a foto d<?php echo SICOP_DET_PRON_L; ?> <?php echo SICOP_DET_DESC_L; ?>" class="icon_button" /></a> <?php }; ?></td>

                            <td class="tb_ck">

                            <?php if ( $n_det_alt >= 1 and !empty( $d_det['matricula'] ) ) {  ?> <input type="image" height="16" src="<?php echo $img_sys_path; ?>camera.png" name="alter_foto_det[]" value="<?php echo $d_det['iddetento'] ;?>" title="Cadastrar/Alterar a foto d<?php echo SICOP_DET_PRON_L; ?> <?php echo SICOP_DET_DESC_L; ?>" /> <?php }; ?>

                            </td>

                            <td class="tb_ck">

                            <?php if ( $n_det_alt >= 1 and !empty( $d_det['matricula'] ) ) {  ?><a href="<?php echo SICOP_ABS_PATH; ?>incl/foto_esp.php?iddet=<?php echo $d_det['iddetento']; ?>" title="Cadastrar fotos especiais d<?php echo SICOP_DET_PRON_L; ?> <?php echo SICOP_DET_DESC_L; ?>" ><img src="<?php echo $img_sys_path; ?>add_foto_esp.png" alt="Cadastrar fotos especiais d<?php echo SICOP_DET_PRON_L; ?> <?php echo SICOP_DET_DESC_L; ?>" class="icon_button" /></a> <?php }; ?>

                            </td>

                            <td class="tb_ck">
                                <img src="<?php echo SICOP_SYS_IMG_PATH; ?>print.png" alt="Impressões d<?php echo SICOP_DET_PRON_L; ?> <?php echo SICOP_DET_DESC_L; ?>" class="icon_button print_doc_det" title="Impressões d<?php echo SICOP_DET_PRON_L; ?> <?php echo SICOP_DET_DESC_L; ?>" />
                                <input type="hidden" name="print_det[]" value="<?php echo $d_det['iddetento'] ;?>" />
                            </td>
                            <td class="tb_ck">
                                <input type="image" height="16" src="<?php echo $img_sys_path; ?>p_img.png" name="print_pec_det[]" value="<?php echo $d_det['iddetento'] ;?>" title="Imprimir a lista do pecúlio d<?php echo SICOP_DET_PRON_L; ?> <?php echo SICOP_DET_DESC_L; ?>" />
                            </td>
                            <td class="tb_ck">
                                <?php if ( $n_det_alt >= 1 ) {?>
                                <input name="iddet[]" type="checkbox" class="mark_row" value="<?php echo $d_det['iddetento'];?>" />
                                <?php }?>
                            </td>
                        </tr>
                        <?php } // fim do while ?>
                    </tbody>
                    <?php if ( $n_det_alt >= 1 ) { ?>
                    <tfoot>
                        <tr>
                            <td  class="tb_ck_leg" colspan="11">todos:</td>
                            <td class="tb_ck"><input type="checkbox" name="todos" value="todos" /></td>
                        </tr>
                    </tfoot>
                    <?php } ?>
                </table>

                <p class="bt_leg">COM MARCADOS</p>

                <input name="proced" type="hidden" id="proced" value="1" />

                <div class="form_bts">
                    <input class="form_bt" name="quali" id="print_quali_m" type="button" value="Imprimir ficha qualificativa" />
                    <input class="form_bt" name="ficha" id="print_ficha_m" type="button" value="Imprimir ficha de identificação" />
                    <input class="form_bt" name="cartao" id="print_cartao_m" type="button" value="Imprimir cartão de identidade" />
                </div>

                <div class="form_bts">
                    <input class="form_bt" name="doc_rol" id="print_doc_rol" type="button" value="Imprimir documentos do rol de visitas" />
                </div>

            </form>

            <script type="text/javascript">

                $(function() {

                    // instanciar o tablesorter plugin
                    $("#tb_order").tablesorter({
                        widgets: ['indexFirstColumn', 'columnHighlight'],
                        // passar os argumentos dos headers
                        headers: {
                            0: {
                                sorter: false // desabilitar a ordenação na coluna dos números de ordem
                            },
                            2: {
                                sorter: 'digit' // forçar a ordenação pelo parser digit
                            },
                            4: {
                                sorter: false // desabilitar a ordenação na coluna do icone de edição de dados do detento
                            },
                            5: {
                                sorter: false // desabilitar a ordenação na coluna do icone do pecúlio
                            },
                            6: {
                                sorter: false // desabilitar a ordenação na coluna do icone de visualização de foto do detento
                            },
                            7: {
                                sorter: false // desabilitar a ordenação na coluna do icone de alteração de foto do detento
                            },
                            8: {
                                sorter: false // desabilitar a ordenação na coluna do link de impressão de qualificativa
                            },
                            9: {
                                sorter: false // desabilitar a ordenação na coluna do link de impressão de ficha de identificação
                            },
                            10: {
                                sorter: false // desabilitar a ordenação na coluna do link de impressão de cartão de identidade
                            },
                            11: {
                                sorter: false // desabilitar a ordenação na coluna do link de impressão de pecúlio
                            },
                            12: {
                                sorter: false // desabilitar a ordenação na coluna do checkbox
                            }
                        },
                        // ordenar a 4ª coluna (procedência) asc
                        sortList: [[3,0]],
                        highlightClass: 'ord',
                        dateFormat: 'dd/mm/yy'
                    });
                });

            </script>

            <?php } ?>

<?php include 'footer.php';?>