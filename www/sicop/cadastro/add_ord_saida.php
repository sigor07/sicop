<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_cad_n    = 3;

if ( $n_cadastro < $n_cad_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'ORDENS DE SAÍDA';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$id_ord_saida  = get_get( 'id_ord_saida', 'int' );
$idlocalos = get_get( 'idlocalos', 'int' );

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Cadastrar ordem de saída';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>

        <script type="text/JavaScript">
            function R_enviar( x ) {
                element = id( 'iddet' );
                element.value = x
            }

            function r_send_idosd( x ) {
                element = id( 'idosd' );
                element.value = x
            }
        </script>
        <div class="no_print">


            <p class="descript_page">CADASTRAR ORDEM DE SAÍDA</p>
            <?php if ( empty( $id_ord_saida ) ) { ?>
            <form action="<?php echo SICOP_ABS_PATH ?>send/sendordsaida.php" method="post" name="cados" id="cados">

                <table class="bonde_add">
                    <tr>
                        <td class="data_esc_leg">Data:</td>
                        <td class="data_esc_field"><input name="ord_saida_data" type="text" class="CaixaTexto" id="ord_saida_data" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" size="12" maxlength="10" />&nbsp;&nbsp;<a href="#" onclick="javascript: datahoje('ord_saida_data'); return false;" >hoje</a></td>
                    </tr>
                    <tr>
                        <td class="data_esc_leg">Hora:</td>
                        <td class="data_esc_field"><input name="ord_saida_hora" type="text" class="CaixaTexto" id="ord_saida_hora" onkeypress="mascara_hora(this, this.value); return blockChars(event, 2);" size="5" maxlength="5" /></td>
                    </tr>
                    <tr>
                        <td class="data_esc_leg">Finalidade:</td>
                        <td class="data_esc_field"><input name="finalidade" type="text" class="CaixaTexto" id="finalidade" onkeypress="return blockChars(event, 4);" size="60" maxlength="50" /></td>
                    </tr>
                    <tr>
                        <td class="data_esc_leg">Escolta:</td>
                        <td class="data_esc_field"><input name="escolta" type="text" class="CaixaTexto" id="escolta" onkeypress="return blockChars(event, 4);" size="60" maxlength="50" /></td>
                    </tr>
                    <tr>
                        <td class="data_esc_leg">Com retorno:</td>
                        <td class="data_esc_field">
                            <input name="retorno" type="checkbox" id="retorno" value="1"/>
                        </td>
                    </tr>
                </table><!-- fim da table."bonde_add" -->

                <input type="hidden" name="proced" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" name="cados" type="submit" value="Cadastrar"  onclick="return valida_ord_saida(1);" />
                    <input class="form_bt" name="" type="button" onClick="history.go(-1)" value="Cancelar" />
                </div>

            </form><!-- fim do form name="cados" -->

            <input name="datahj" type="hidden" id="datahj" value="<?php echo date('d/m/Y') ?>" />

            <script type="text/javascript">

                $(function() {
                    $( "#ord_saida_data" ).focus();
                    $( "#ord_saida_data" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

            <?php

            } else { // do if que verifica o $id_ord_saida

                $q_ordsaida = "SELECT
                                DATE_FORMAT( `ord_saida_data`, '%d/%m/%Y' ) AS `ord_saida_data_f`,
                                DATE_FORMAT( `ord_saida_hora`, '%H:%i' ) AS `ord_saida_hora_f`
                              FROM
                                `ordens_saida`
                              WHERE
                                `id_ord_saida` = $id_ord_saida
                              LIMIT 1";

                $sit_pag = 'ORDEM DE SAÍDA';

                // instanciando o model
                $model = SicopModel::getInstance();

                // executando a query
                $q_ordsaida = $model->query( $q_ordsaida );

                // fechando a conexao
                $model->closeConnection();

                // se a query retornar false, é por que nao foi executada corretamente
                if ( !$q_ordsaida ) {

                    echo msg_js( '', 1 );
                    exit;

                }

                $cont_ordsaida = $q_ordsaida->num_rows;

                if( $cont_ordsaida < 1 ) {

                    // montar a mensagem q será salva no log
                    $msg = array();
                    $msg['tipo']  = 'err';
                    $msg['text']  = "A consulta retornou 0 ocorrências ( $sit_pag ).";
                    $msg['linha'] = __LINE__;
                    get_msg( $msg, 1 );

                    echo msg_js( '', 1 );
                    exit;

                }

                $d_ordsaida     = $q_ordsaida->fetch_assoc();
                $ord_saida_data = $d_ordsaida['ord_saida_data_f'];
                $ord_saida_hora = $d_ordsaida['ord_saida_hora_f'];
                ?>

            <p class="common">Data/hora da ordem de saída: <?php echo $ord_saida_data ?><?php if ( !empty ( $ord_saida_hora ) ) { ?> às <?php echo $ord_saida_hora; ?><?php } ?></p>

                <?php
                if ( empty( $idlocalos ) ) {

                    $q_local_ord_saida = "SELECT `cod_local` FROM `ordens_saida_locais` WHERE `cod_ord_saida` = $id_ord_saida";

                    $q_local_apr = "SELECT
                                      `locais_apr`.`idlocal`,
                                      `locais_apr`.`local_apr`,
                                      `locais_apr`.`local_end`
                                    FROM
                                      `locais_apr`
                                    WHERE
                                      `locais_apr`.`idlocal` NOT IN( $q_local_ord_saida )
                                    ORDER BY
                                      `locais_apr`.`local_apr`";

                    $sit_pag = 'ORDEM DE SAÍDA - LOCAL';

                    // instanciando o model
                    $model = SicopModel::getInstance();

                    // executando a query
                    $q_local_apr = $model->query( $q_local_apr );

                    // fechando a conexao
                    $model->closeConnection();

                    // se a query retornar false, é por que nao foi executada corretamente
                    if ( !$q_local_apr ) {

                        echo msg_js( '', 1 );
                        exit;

                    }

                    $cont_l_ord_saida = $q_local_apr->num_rows;

                    if( $cont_l_ord_saida < 1 ) {

                        // montar a mensagem q será salva no log
                        $msg = array();
                        $msg['tipo']  = 'err';
                        $msg['text']  = "A consulta retornou 0 ocorrências ( $sit_pag ).";
                        $msg['linha'] = __LINE__;
                        get_msg( $msg, 1 );

                        echo msg_js( '', 1 );
                        exit;

                    }

                    $q_local_atual_ord_saida = "SELECT
                                                  `ordens_saida_locais`.`id_local_ord_saida`,
                                                  `locais_apr`.`local_apr`
                                                FROM
                                                  `ordens_saida_locais`
                                                  INNER JOIN `locais_apr` ON `locais_apr`.`idlocal` = `ordens_saida_locais`.`cod_local`
                                                WHERE
                                                  `ordens_saida_locais`.`cod_ord_saida` = $id_ord_saida";

                    // instanciando o model
                    $model = SicopModel::getInstance();

                    // executando a query
                    $q_local_atual_ord_saida = $model->query( $q_local_atual_ord_saida );

                    // fechando a conexao
                    $model->closeConnection();

                    $cont_q_local_atual_ord_saida = $q_local_atual_ord_saida->num_rows;

                    if( $cont_q_local_atual_ord_saida >= 1 ) {
                ?>

                <p class="table_leg">Destinos cadastrados:</p>

                <table width="250" class="bonde_add">
                <?php while( $d_l_ord_saida_atual = $q_local_atual_ord_saida->fetch_assoc() ) { ?>
                    <tr class="even_gr">
                        <td class="drop_field"><a href="<?php echo $_SERVER['PHP_SELF'];?>?id_ord_saida=<?php echo $id_ord_saida;?>&idlocalos=<?php echo $d_l_ord_saida_atual['id_local_ord_saida'];?>" title="Clique para ver <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s que estão cadastrados neste destino"><?php echo $d_l_ord_saida_atual['local_apr'];?></a></td>
                        <?php if ( $n_cadastro >= 3 ) {  ?>
                        <td class="tb_bt"><a href='javascript:void(0)' onclick='drop_ord_saida( <?php echo $d_l_ord_saida_atual['id_local_ord_saida']; ?>, 1 )' title="Excluir este destino"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir este destino" class="icon_button" /></a></td>
                        <?php }; ?>
                    </tr>
                <?php } // fim do while( $d_l_ord_saida_atual... ?>
                </table><!-- fim da table."bonde_add" -->
                <?php } // fim do if( $cont_q_local_atual_ord_saida >= 1 ) ?>

            <form action="<?php echo SICOP_ABS_PATH ;?>send/sendordsaida.php" method="post" name="cadlocalos" id="cadlocalos">

                <table width="420" class="bonde_add">
                    <tr>
                        <td class="leg">Destino:</td>
                        <td class="field">
                            <select name="local_ord_saida" class="CaixaTexto" id="local_ord_saida">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $d_local_ord_saida = $q_local_apr->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_local_ord_saida['idlocal']; ?>"><?php echo $d_local_ord_saida['local_apr']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="leg">Horário:</td>
                        <td class="field">
                            <input name="local_hora" type="text" class="CaixaTexto" id="local_hora" onkeypress="mascara_hora(this, this.value); return blockChars(event, 2);" size="5" maxlength="5" />
                        </td>
                    </tr>
                </table><!-- fim da table."bonde_add" -->

                <input type="hidden" name="id_ord_saida" id="id_ord_saida" value="<?php echo $id_ord_saida; ?>" />
                <input type="hidden" name="proced" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" name="cadlocalos" type="submit" value="Cadastrar"  onclick="return valida_ord_saida(2);" />
                    <input class="form_bt" name="" type="button" onClick="history.go(-1)" value="Cancelar" />
                </div>

            </form><!-- fim do form name="cadlocalos" -->

            <p class="link_common"><a href="add_local_escolta.php">Cadastrar localidade</a></p>

            <?php
            } else { // do if que verifica o $idlocalos

                $q_local_ord_saida = "SELECT
                                        `ordens_saida_locais`.`id_local_ord_saida`,
                                        `locais_apr`.`local_apr`,
                                        `locais_apr`.`local_end`
                                      FROM
                                        `ordens_saida_locais`
                                        INNER JOIN `locais_apr` ON `locais_apr`.`idlocal` = `ordens_saida_locais`.`cod_local`
                                      WHERE
                                        `ordens_saida_locais`.`id_local_ord_saida` = $idlocalos
                                      LIMIT 1";

                $sit_pag = 'ORDEM DE SAÍDA - LOCAL DE APRESENTAÇÃO';

                // instanciando o model
                $model = SicopModel::getInstance();

                // executando a query
                $q_local_ord_saida = $model->query( $q_local_ord_saida );

                // fechando a conexao
                $model->closeConnection();

                // se a query retornar false, é por que nao foi executada corretamente
                if ( !$q_local_ord_saida ) {

                    echo msg_js( '', 1 );
                    exit;

                }

                $cont_local_ord_saida = $q_local_ord_saida->num_rows;

                if( $cont_local_ord_saida < 1 ) {

                    // montar a mensagem q será salva no log
                    $msg = array();
                    $msg['tipo']  = 'err';
                    $msg['text']  = "A consulta retornou 0 ocorrências ( $sit_pag ).";
                    $msg['linha'] = __LINE__;
                    get_msg( $msg, 1 );

                    echo msg_js( '', 1 );
                    exit;

                }

                $d_local_ord_saida = $q_local_ord_saida->fetch_assoc();
                $local_ord_saida = $d_local_ord_saida['local_apr'];

            ?>

            <p class="common">Destino: <?php echo $local_ord_saida ?></p>

            <p class="sub_title_page">PESQUISAR <?php echo SICOP_DET_DESC_U ;?></p>

            <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="get" name="busca_det" id="busca_det" >

                <p class="table_leg">Digite o NOME ou a MATRÍCULA d<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?>:</p>

                <div class="form_one_field">
                    <input name="campobusca" type="text" class="CaixaTexto" id="campobusca" onkeypress="return blockChars(event, 4);" size="50" />
                </div>

                <input type='hidden' name='proced' id='proced' value="1" />
                <input type="hidden" name="id_ord_saida" id="id_ord_saida" value="<?php echo $id_ord_saida; ?>" />
                <input type="hidden" name="idlocalos" id="idlocalos" value="<?php echo $idlocalos; ?>" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="busca" value="Buscar" />
                </div>

            </form>

            <script type="text/javascript">id("campobusca").focus();</script>

            <?php

            $proced = get_get('proced', 'int');

            // se estiver setado o procedimento...
            if ( !empty( $proced ) ) {

                if ( $proced == 1 ) {

                    $cont_query_det = '';

                    $valorbusca = get_get('campobusca', 'busca');

                    if ( !empty( $valorbusca ) ) {

                        $query_det = "SELECT
                                        `iddetento`
                                      FROM
                                        `detentos`
                                      WHERE
                                        `nome_det` LIKE '%$valorbusca%' OR `matricula` LIKE '$valorbusca%'";

                        // instanciando o model
                        $model = SicopModel::getInstance();

                        // executando a query
                        $query_det = $model->query( $query_det );

                        // fechando a conexao
                        $model->closeConnection();

                        $cont_query_det = $query_det->num_rows;

                        // se retornar mais de 1 resultado, gera a tabela abaixo para escolher quem vai ser adicionado.
                        if( $cont_query_det > 1 ) {
                            $iddet_array = array();
                            while( $dados_det = $query_det->fetch_assoc() ) {
                                $iddet_array[] = $dados_det['iddetento'];
                            }

                            $iddet_in = implode( ',' , $iddet_array );

                            $q_det_select = "SELECT
                                              `detentos`.`iddetento`,
                                              `detentos`.`nome_det`,
                                              `detentos`.`matricula`,
                                              `detentos`.`pai_det`,
                                              `detentos`.`mae_det`,
                                              `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
                                              `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
                                              `unidades_out`.`idunidades` AS iddestino,
                                              `cela`.`cela`,
                                              `raio`.`raio`
                                            FROM
                                              `detentos`
                                              LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                                              LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                                              LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                                              LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                                              LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
                                            WHERE
                                              `detentos`.`iddetento` IN( $iddet_in )
                                            ORDER BY
                                              `detentos`.`nome_det`";

                            // instanciando o model
                            $model = SicopModel::getInstance();

                            // executando a query
                            $q_det_select = $model->query( $q_det_select );

                            // fechando a conexao
                            $model->closeConnection();

                            ?>

            <p class="p_q_info">Resultado da busca</p>

            <form action='<?php echo $_SERVER['PHP_SELF'];?>' method='get' name='add_det_ord_saida'>

                <table class="lista_busca">
                    <tr>
                        <th class="num_od">N</th>
                        <th class="nome_det"><?php echo SICOP_DET_DESC_FU; ?></th>
                        <th class="matr_det">Matrícula</th>
                        <th class="raio_det"><?php echo SICOP_RAIO ?></th>
                        <th class="cela_det"><?php echo SICOP_CELA ?></th>
                        <th class="tb_bt">&nbsp;</th>
                    </tr>
                                    <?php
                                    $i = 1;

                                    while( $d_det = $q_det_select->fetch_assoc() ) {

                                        $tipo_mov_in  = $d_det['tipo_mov_in'];
                                        $tipo_mov_out = $d_det['tipo_mov_out'];
                                        $iddestino    = $d_det['iddestino'];

                                        $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );

                                        ?>

                    <tr class="even">
                        <td class="num_od"><?php echo $i++; ?></td>
                        <td class="nome_det" title="Pai: <?php echo $d_det['pai_det'];?>&#13;Mãe: <?php echo $d_det['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat'];?>"><a href="<?php echo SICOP_ABS_PATH; ?>detento/detalhesdet.php?iddet=<?php echo $d_det['iddetento'] /*alphaID($d_det['iddetento'])*/;?>"> <?php echo $d_det['nome_det'];?></a></td>
                        <td class="matr_det <?php echo $det['css_class']; ?>"><?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] );?></td>
                        <td class="raio_det <?php echo $det['css_class']; ?>"><?php echo $d_det['raio'];?></td>
                        <td class="cela_det <?php echo $det['css_class']; ?>"><?php echo $d_det['cela'];?></td>
                        <td class="tb_bt"><input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>s_add.png" name="add_det_ord_saida" onClick="R_enviar('<?php echo $d_det['iddetento'] ;?>');" /></td>
                    </tr>
                                        <?php } // fim do while( $d_det... ?>
                </table>

                <input type="hidden" name="proced" id="proced" value="1" />
                <input type="hidden" name="id_ord_saida" id="id_ord_saida" value="<?php echo $id_ord_saida; ?>" />
                <input type="hidden" name="idlocalos" id="idlocalos" value="<?php echo $idlocalos; ?>" />
                <input type="hidden" name="iddet" id="iddet" value="" />

            </form><!-- fim do form name='add_det_ord_saida' -->
                            <?php

                        // se não retornar niguem, mostra a msg
                        } else if ( $cont_query_det < 1) { // do if( $cont > 1 )

                            echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';

                        } // fim do if( $cont > 1 )

                    } // fim do if ( !empty( $valorbusca ) )


                    $add_det_ord_saida = get_get( 'add_det_ord_saida_x' );

                    if ( $cont_query_det == 1 or !empty( $add_det_ord_saida ) ) {

                        $iddet = '';

                        if ( $cont_query_det == 1 ) {

                            $dados_det = $query_det->fetch_assoc();
                            $iddet = $dados_det['iddetento'];

                        }

                        if ( !empty( $add_det_ord_saida ) ) {
                            $iddet = get_get( 'iddet', 'int' );
                        }

                        if ( !empty( $iddet ) ) {

                            /**
                             * @var $ck_det_bonde string dadfadf
                             */
                            $ck_det_ord_saida = "SELECT
                                             `ordens_saida_det`.`cod_detento`,
                                             `detentos`.`nome_det`,
                                             `detentos`.`matricula`
                                           FROM
                                             `ordens_saida`
                                             INNER JOIN `ordens_saida_locais` ON `ordens_saida_locais`.`cod_ord_saida` = `ordens_saida`.`id_ord_saida`
                                             INNER JOIN `ordens_saida_det` ON `ordens_saida_det`.`cod_local_ord_saida` = `ordens_saida_locais`.`id_local_ord_saida`
                                             INNER JOIN `detentos` ON `ordens_saida_det`.`cod_detento` = `detentos`.`iddetento`
                                           WHERE
                                             `ordens_saida`.`id_ord_saida` = $id_ord_saida
                                             AND
                                             `ordens_saida_det`.`cod_detento` = $iddet";

                            // instanciando o model
                            $model = SicopModel::getInstance();

                            // executando a query
                            $ck_det_ord_saida = $model->query( $ck_det_ord_saida );

                            // fechando a conexao
                            $model->closeConnection();

                            $cont_ck_det_ord_saida = $ck_det_ord_saida->num_rows;

                            if ( $cont_ck_det_ord_saida < 1 ) {

                                $user = get_session( 'user_id', 'int' );
                                $ip   = "'" . $_SERVER['REMOTE_ADDR'] . "'";

                                $q_in_det_ord_saida = "INSERT INTO
                                                   `ordens_saida_det`
                                                   (
                                                     `cod_local_ord_saida`,
                                                     `cod_detento`,
                                                     `user_add`,
                                                     `data_add`,
                                                     `ip_add`
                                                   )
                                                 VALUES
                                                   (
                                                     $idlocalos,
                                                     $iddet,
                                                     $user,
                                                     NOW(),
                                                     $ip
                                                   )";

                                // instanciando o model
                                $model = SicopModel::getInstance();

                                // executando a query
                                $model->query( $q_in_det_ord_saida );

                                // fechando a conexao
                                $model->closeConnection();

                            } else { // do if ( $cont_ck_det_ord_saida < 1 )

                                $d_ck_det_ord_saida = $ck_det_ord_saida->fetch_assoc();

                      ?>

                <p class="common">

                    <?php echo SICOP_DET_PRON_FU . ' ' . SICOP_DET_DESC_L ;?> já esta cadastrad<?php echo SICOP_DET_ART_L; ?> nesta ordem de saída!<br/>
                    <?php echo $d_ck_det_ord_saida['nome_det'] ?>, matrícula <?php if ( !empty( $d_ck_det_ord_saida['matricula'] ) ) echo formata_num( $d_ck_det_ord_saida['matricula'] ) ?>

                </p>

                      <?php
                            } // fim do if ( $cont_ck_det_ord_saida < 1 )

                        } // fim do if ( !empty( $iddet ) )

                    } // fim do if ( $cont_query_det == 1 or !empty( $add_det_ord_saida ) )

                } else if ( $proced == 2 ) { //excluir elemento da ordem de saída

                    $idosd = get_get( 'idosd', 'int' );
                    $q_del_det_ord_saida = "DELETE FROM `ordens_saida_det` WHERE `id_ord_saida_det` = $idosd LIMIT 1";

                    // instanciando o model
                    $model = SicopModel::getInstance();

                    // executando a query
                    $model->query( $q_del_det_ord_saida );

                    // fechando a conexao
                    $model->closeConnection();

                }

            } // fim do if ( !empty( $proced ) )

            $sq_s_det_ord_saida = "SELECT
                               `ordens_saida_det`.`id_ord_saida_det`
                             FROM
                               `ordens_saida`
                               INNER JOIN `ordens_saida_locais` ON `ordens_saida_locais`.`cod_ord_saida` = `ordens_saida`.`id_ord_saida`
                               INNER JOIN `ordens_saida_det` ON `ordens_saida_det`.`cod_local_ord_saida` = `ordens_saida_locais`.`id_local_ord_saida`
                             WHERE
                               `ordens_saida`.`id_ord_saida` = $id_ord_saida
                               AND
                               `ordens_saida_locais`.`id_local_ord_saida` = $idlocalos";

            $q_s_det_ord_saida = "SELECT
                              `ordens_saida_det`.`id_ord_saida_det`,
                              `detentos`.`iddetento`,
                              `detentos`.`nome_det`,
                              `detentos`.`matricula`,
                              `detentos`.`pai_det`,
                              `detentos`.`mae_det`,
                              `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
                              `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
                              `unidades_out`.`idunidades` AS iddestino,
                              `cela`.`cela`,
                              `raio`.`raio`
                            FROM
                              `detentos`
                              INNER JOIN `ordens_saida_det` ON `ordens_saida_det`.`cod_detento` = `detentos`.`iddetento`
                              LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                              LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                              LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                              LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                              LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
                            WHERE
                              `ordens_saida_det`.`id_ord_saida_det` IN( $sq_s_det_ord_saida )
                            ORDER BY
                              `detentos`.`nome_det`";

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $q_s_det_ord_saida = $model->query( $q_s_det_ord_saida );

            // fechando a conexao
            $model->closeConnection();

            $cont_q_s_det_ord_saida = $q_s_det_ord_saida->num_rows;

            if ( $cont_q_s_det_ord_saida >= 1 ) {

                ?>
            <p class="table_leg">Lista atual</p>

<!--            <p align="center"><?php //echo 'oi' . get_get( 'add_det_bonde_x' )?></p>-->

            <form action='<?php echo $_SERVER['PHP_SELF'];?>' method='get' name='del_det_ord_saida'>

                <table class="lista_busca">
                    <tr>
                        <th class="num_od">N</th>
                        <th class="nome_det"><?php echo SICOP_DET_DESC_FU; ?></th>
                        <th class="matr_det">Matrícula</th>
                        <th class="raio_det"><?php echo SICOP_RAIO ?></th>
                        <th class="cela_det"><?php echo SICOP_CELA ?></th>
                        <th class="tb_bt">&nbsp;</th>
                    </tr>

                        <?php
                        $i = 1;

                        while( $d_det = $q_s_det_ord_saida->fetch_assoc() ) {

                            $tipo_mov_in  = $d_det['tipo_mov_in'];
                            $tipo_mov_out = $d_det['tipo_mov_out'];
                            $iddestino    = $d_det['iddestino'];

                            $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );

                            ?>

                    <tr class="even">
                        <td class="num_od"><?php echo $i++; ?></td>
                        <td class="nome_det" title="Pai: <?php echo $d_det['pai_det'];?>&#13;Mãe: <?php echo $d_det['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat'];?>"><a href="<?php echo SICOP_ABS_PATH; ?>detento/detalhesdet.php?iddet=<?php echo $d_det['iddetento'] /*alphaID($d_det['iddetento'])*/;?>"> <?php echo $d_det['nome_det'];?></a></td>
                        <td class="matr_det <?php echo $det['css_class']; ?>"><?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] );?></td>
                        <td class="raio_det <?php echo $det['css_class']; ?>"><?php echo $d_det['raio'];?></td>
                        <td class="cela_det <?php echo $det['css_class']; ?>"><?php echo $d_det['cela'];?></td>
                        <td class="tb_bt"><input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" name="submit" onClick="r_send_idosd('<?php echo $d_det['id_ord_saida_det'] ;?>');" /></td>
                    </tr>
                            <?php } // fim do while $d_det... ?>
                </table>

                <input type='hidden' name='proced' id='proced' value="2" />
                <input type="hidden" name="id_ord_saida" id="id_ord_saida" value="<?php echo $id_ord_saida; ?>" />
                <input type="hidden" name="idlocalos" id="idlocalos" value="<?php echo $idlocalos; ?>" />
                <input type='hidden' name='idosd' id='idosd' value="" />

            </form><!-- fim do form name='del_det_ord_saida' -->

            <p class="bt_leg">PRÓXIMO PASSO:</p>

            <form action='<?php echo $_SERVER['PHP_SELF'];?>' method='get' name='elemt_list'>

                <div class="form_bts">

                    <input class="form_bt" name="add_local" type="button" value="Adicionar outro local" onclick="javascript: location.href='<?php echo $_SERVER['PHP_SELF'];?>?id_ord_saida=<?php echo $id_ord_saida;?>';" />&nbsp;&nbsp;&nbsp;
                    <input class="form_bt" name="lista_ord_saida" type="button" value="Listar as ordens de saída" onclick="javascript: location.href='lista_ord_saida.php';" />&nbsp;&nbsp;&nbsp;
                    <input class="form_bt" name="lista_det_ord_saida" type="button" value="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s desta ordem de saída" onclick="javascript: location.href='detal_ord_saida.php?id_ord_saida=<?php echo $id_ord_saida;?>';" />&nbsp;&nbsp;&nbsp;

                </div>

                <input type="hidden" name="id_ord_saida" id="id_ord_saida" value="<?php echo $id_ord_saida; ?>" />
                <input type="hidden" name="idlocalos" id="idlocalos" value="<?php echo $idlocalos; ?>" />
                <input type='hidden' name='proced' id='proced' value="4" />

            </form><!-- fim do form name='elemt_list' -->

            <?php } // fim do if ( $cont_q_s_det_ord_saida >= 1 ) ?>

            <?php } // fim do if que verifica o $idlocalos ?>

            <?php } // fim do if que verifica o $id_ord_saida ?>

<?php include 'footer.php'; ?>