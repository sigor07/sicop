<?php

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_bonde   = SicopController::getSession( 'n_bonde', 'int' );
$n_bonde_n = 3;

$motivo_pag = 'CADASTRAMENTO DE BONDE';

if ( $n_bonde < $n_bonde_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idbonde   = get_get( 'idbonde', 'int' );
$idb_local = get_get( 'idb_local', 'int' );

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Cadastrar bonde';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) {
    $pag_atual .= '?' . $qs;
}

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>

<script type="text/JavaScript">
    function R_enviar(x) {
        element = id( 'iddet' );
        element.value = x
    }

    function r_send_idbd( x ) {
        element = id( 'idbd' );
        element.value = x
    }
</script>

            <p class="descript_page">CADASTRAR BONDE</p>

            <?php if ( empty( $idbonde ) ) { ?>
            <form action="<?php echo SICOP_ABS_PATH ;?>send/sendbonde.php" method="post" name="cadbond" id="cadbond">

                <table class="bonde_add">
                    <tr>
                        <td class="data_bonde_leg">Data:</td>
                        <td class="data_bonde_field"><input name="bonde_data" type="text" class="CaixaTexto" id="bonde_data" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" size="12" maxlength="10" />&nbsp;&nbsp;<a href="#" onclick="javascript: datahoje('bonde_data'); return false;" >hoje</a></td>
                    </tr>
                </table><!-- fim da table."bonde_add" -->

                <input name="proced" type="hidden" id="proced" value="3">

                <div class="form_bts">
                    <input class="form_bt" name="cadbond" type="submit" value="Cadastrar"  onclick="return valida_bonde(1);" />
                    <input class="form_bt" name="" type="button" onClick="history.go(-1)" value="Cancelar" />
                </div>

            </form><!-- fim do form name="cadbond" -->

            <input name="datahj" type="hidden" id="datahj" value="<?php echo date('d/m/Y') ?>" />

            <script type="text/javascript">

                $(function() {
                    $( "#bonde_data" ).focus();
                    $( "#bonde_data" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

            <?php

            } else { // do if que verifica o $idbonde

                $q_bonde = "SELECT
                              `idbonde`,
                              DATE_FORMAT(`bonde_data`, '%d/%m/%Y') AS bonde_data_f
                            FROM
                              `bonde`
                            WHERE
                              `idbonde` = $idbonde
                            LIMIT 1";

                $sit_pag = 'BONDE';

                // instanciando o model
                $model = SicopModel::getInstance();

                // executando a query
                $q_bonde = $model->query( $q_bonde );

                // fechando a conexao
                $model->closeConnection();

                // se a query retornar false, é por que nao foi executada corretamente
                if( !$q_bonde ) {

                    echo msg_js( '', 1 );
                    exit;

                }

                $cont_bonde = $q_bonde->num_rows;

                //se o número de ocorrências for igual a 0 finaliza com o exit
                if( $cont_bonde < 1 ) {
                    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ( $sit_pag ).\n\n Página: $pag";
                    salvaLog($mensagem);
                    echo '<script type="text/javascript">history.go(-1);</script>';
                    exit;
                }

                $d_bonde = $q_bonde->fetch_assoc();
                $bonde_data = $d_bonde['bonde_data_f'];

                ?>

            <p class="common">Data do bonde: <?php echo $bonde_data ?></p>

                <?php
                if ( empty( $idb_local ) ) {

                    $q_unid_bonde = "SELECT `cod_unidade` FROM `bonde_locais` WHERE `cod_bonde` = $idbonde";

                    $q_unidade = "SELECT
                                    `unidades`.`idunidades`,
                                    `unidades`.`unidades`
                                  FROM
                                    unidades
                                  WHERE
                                    `er` = TRUE
                                    AND
                                    `unidades`.`idunidades` NOT IN( $q_unid_bonde )
                                  ORDER BY
                                    `unidades`.`unidades`";

                    $sit_pag = 'BONDE - UNIDADES DE DESTINO';

                    // instanciando o model
                    $model = SicopModel::getInstance();

                    // executando a query
                    $q_unidade = $model->query( $q_unidade );

                    // fechando a conexao
                    $model->closeConnection();

                    // se a query retornar false, é por que nao foi executada corretamente
                    if( !$q_unidade ) {

                        echo msg_js( '', 1 );
                        exit;

                    }

                    $cont_udst = $q_unidade->num_rows;

                    //se o número de ocorrências for igual a 0 finaliza com o exit
                    if( $cont_udst < 1 ) {
                        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ( $sit_pag ).\n\n Página: $pag";
                        salvaLog($mensagem);
                        echo '<script type="text/javascript">history.go(-1);</script>';
                        exit;
                    }

                    $q_unid_atual_bonde = "SELECT
                                             `bonde_locais`.`idblocal`,
                                             `unidades`.`unidades`
                                           FROM
                                             `bonde_locais`
                                             INNER JOIN `unidades` ON `unidades`.`idunidades` = `bonde_locais`.`cod_unidade`
                                           WHERE
                                             `bonde_locais`.`cod_bonde` = $idbonde";

                    // instanciando o model
                    $model = SicopModel::getInstance();

                    // executando a query
                    $q_unid_atual_bonde = $model->query( $q_unid_atual_bonde );

                    // fechando a conexao
                    $model->closeConnection();

                    $cont_q_unid_atual_bond = $q_unid_atual_bonde->num_rows;

                    if( $cont_q_unid_atual_bond >= 1 ) {

                ?>
            <p class="table_leg">Destinos cadastrados: </p>

            <table width="250" class="bonde_add">

            <?php while( $d_uba = $q_unid_atual_bonde->fetch_assoc() ) { ?>
                <tr class="even_gr">
                    <td class="drop_field"><a href="<?php echo $_SERVER['PHP_SELF'];?>?idbonde=<?php echo $idbonde;?>&idb_local=<?php echo $d_uba['idblocal'];?>" title="Clique para ver <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s que estão cadastrados neste destino"><?php echo $d_uba['unidades'];?></a></td>
                    <?php if ($n_bonde >= 3 ) {  ?>
                    <td class="tb_bt"><a href='javascript:void(0)' onclick='drop_local_bonde(<?php echo $d_uba['idblocal']; ?>)' title="Excluir este destino"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir este destino" class="icon_button" /></a></td>
                    <?php }; ?>
                </tr>
            <?php } // fim do while( $d_uba... ?>

            </table><!-- fim da table."bonde_add" -->

            <?php } // fim do if( $cont_q_unid_atual_bond >= 1 ) ?>


            <form action="<?php echo SICOP_ABS_PATH ;?>send/sendbonde.php" method="post" name="cadbondlocal" id="cadbondlocal">

                <table width="420" class="bonde_add">
                    <tr>
                        <td class="leg">Destino:</td>
                        <td class="field">
                            <select name="local_bonde" class="CaixaTexto" id="local_bonde">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $d_udst = $q_unidade->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_udst['idunidades']; ?>"><?php echo $d_udst['unidades']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                </table><!-- fim da table."bonde_add" -->

                <input name="idbonde" type="hidden" id="idbonde" value="<?php echo $idbonde; ?>" />
                <input name="proced" type="hidden" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" name="cadbondlocal" type="submit" value="Cadastrar"  onclick="return valida_bonde(2);" />
                    <input class="form_bt" name="" type="button" onClick="history.go(-1)" value="Cancelar" />
                </div>

            </form><!-- fim do form name="cadbondlocal" -->

            <?php
            } else { // do if que verifica o $idb_local

                $q_local_bonde = "SELECT
                                    `bonde_locais`.`idblocal`,
                                    `unidades`.`unidades`
                                  FROM
                                    `bonde_locais`
                                    INNER JOIN `unidades` ON `unidades`.`idunidades` = `bonde_locais`.`cod_unidade`
                                  WHERE
                                    `bonde_locais`.`idblocal` = $idb_local
                                  LIMIT 1";

                $sit_pag = 'BONDE - UNIDADE DE DESTINO';

                // instanciando o model
                $model = SicopModel::getInstance();

                // executando a query
                $q_local_bonde = $model->query( $q_local_bonde );

                // fechando a conexao
                $model->closeConnection();

                // se a query retornar false, é por que nao foi executada corretamente
                if( !$q_local_bonde ) {

                    echo msg_js( '', 1 );
                    exit;

                }

                $cont_local_bonde = $q_local_bonde->num_rows;

                if( $cont_local_bonde < 1 ) { //se o número de ocorrências for igual a 0 finaliza com o exit
                    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ( $sit_pag ).\n\n Página: $pag";
                    salvaLog($mensagem);
                    echo '<script type="text/javascript">history.go(-1);</script>';
                    exit;
                }

                $d_local_bonde = $q_local_bonde->fetch_assoc();
                $local_bonde = $d_local_bonde['unidades'];

            ?>

            <p class="common">Destino: <?php echo $local_bonde ?></p>

            <p class="sub_title_page">PESQUISAR <?php echo SICOP_DET_DESC_U ;?></p>

            <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="get" name="busca_det" id="busca_det" >

                <p class="table_leg">Digite o NOME ou a MATRÍCULA d<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?>:</p>

                <div class="form_one_field">
                    <input name="campobusca" type="text" class="CaixaTexto" id="campobusca" onkeypress="return blockChars(event, 4);" size="50" />
                </div>

                <input type='hidden' name='proced' id='proced' value=1 />
                <input type="hidden" name="idbonde" id="idbonde" value="<?php echo $idbonde; ?>" />
                <input type="hidden" name="idb_local" id="idb_local" value="<?php echo $idb_local; ?>" />

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

            <form action='<?php echo $_SERVER['PHP_SELF'];?>' method='get' name='add_det_bonde'>

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
                        <td class="nome_det" title="Pai: <?php echo $d_det['pai_det'];?>&#13;Mãe: <?php echo $d_det['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat'];?>"><a href="detento/detalhesdet.php?iddet=<?php echo $d_det['iddetento'] /*alphaID($d_det['iddetento'])*/;?>"> <?php echo $d_det['nome_det'];?></a></td>
                        <td class="matr_det <?php echo $det['css_class']; ?>"><?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] );?></td>
                        <td class="raio_det <?php echo $det['css_class']; ?>"><?php echo $d_det['raio'];?></td>
                        <td class="cela_det <?php echo $det['css_class']; ?>"><?php echo $d_det['cela'];?></td>
                        <td class="tb_bt"><input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>s_add.png" name="add_det_bonde" onClick="R_enviar('<?php echo $d_det['iddetento'] ;?>');" /></td>
                    </tr>

                    <?php } // fim do while( $d_det... ?>

                </table><!-- /table.lista_busca -->

                <input type='hidden' name='proced' id='proced' value=1 />
                <input type="hidden" name="idbonde" id="idbonde" value="<?php echo $idbonde; ?>" />
                <input type="hidden" name="idb_local" id="idb_local" value="<?php echo $idb_local; ?>" />
                <input type='hidden' name='iddet' id='iddet' value="" />

            </form><!-- fim do form name='add_det_bonde' -->
                            <?php

                        // se não retornar niguem, mostra a msg
                        } else if ( $cont_query_det < 1) { // do if( $cont > 1 )

                            echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';

                        } // fim do if( $cont > 1 )

                    } // fim do if ( !empty( $valorbusca ) )


                    $add_det_bonde = get_get( 'add_det_bonde_x' );

                    if ( $cont_query_det == 1 or !empty( $add_det_bonde ) ) {

                        $iddet = '';

                        if ( $cont_query_det == 1 ) {

                            $dados_det = $query_det->fetch_assoc();
                            $iddet = $dados_det['iddetento'];

                        }

                        if ( !empty( $add_det_bonde ) ) {
                            $iddet = get_get( 'iddet', 'int' );
                        }

                        if ( !empty( $iddet ) ) {

                            /**
                             * @var $ck_det_bonde string dadfadf
                             */
                            $ck_det_bonde = "SELECT
                                               `bonde_det`.`cod_detento`,
                                               `detentos`.`nome_det`,
                                               `detentos`.`matricula`
                                             FROM
                                               `bonde`
                                               INNER JOIN `bonde_locais` ON `bonde_locais`.`cod_bonde` = `bonde`.`idbonde`
                                               INNER JOIN `bonde_det` ON `bonde_det`.`cod_bonde_local` = `bonde_locais`.`idblocal`
                                               INNER JOIN `detentos` ON `bonde_det`.`cod_detento` = `detentos`.`iddetento`
                                             WHERE
                                               `bonde`.`idbonde` = $idbonde
                                               AND
                                               `bonde_det`.`cod_detento` = $iddet";

                            // instanciando o model
                            $model = SicopModel::getInstance();

                            // executando a query
                            $ck_det_bonde = $model->query( $ck_det_bonde );

                            // fechando a conexao
                            $model->closeConnection();

                            $cont_ck_det_bonde = $ck_det_bonde->num_rows;

                            if ( $cont_ck_det_bonde < 1 ) {

                                $user = SicopController::getSession( 'user_id', 'int' );
                                $ip   = "'" . $_SERVER['REMOTE_ADDR'] . "'";

                                $q_in_det_bonde = "INSERT INTO `bonde_det`
                                                     (`cod_bonde_local`,
                                                      `cod_detento`,
                                                      `user_add`,
                                                      `data_add`,
                                                      `ip_add`)
                                                   VALUES
                                                     ($idb_local,
                                                      $iddet,
                                                      $user,
                                                      NOW(),
                                                      $ip)";

                                // instanciando o model
                                $model = SicopModel::getInstance();

                                // executando a query
                                $model->query( $q_in_det_bonde );

                                // fechando a conexao
                                $model->closeConnection();

                            } else { // do if ( $cont_ck_det_bonde < 1 )

                                $d_ck_det_bonde = $ck_det_bonde->fetch_assoc();

                      ?>

                <p class="common">

                    <?php echo SICOP_DET_PRON_FU . ' ' . SICOP_DET_DESC_L ;?> já esta cadastrad<?php echo SICOP_DET_ART_L; ?> neste bonde!<br/>
                    <?php echo $d_ck_det_bonde['nome_det'] ?>, matrícula <?php if ( !empty( $d_ck_det_bonde['matricula'] ) ) echo formata_num( $d_ck_det_bonde['matricula'] ) ?>

                </p>

                      <?php
                            } // fim do if ( $cont_ck_det_bonde < 1 )

                        } // fim do if ( !empty( $iddet ) )

                    } // fim do if ( $cont_query_det == 1 or !empty( $add_det_bonde ) )

                } else if ( $proced == 2 ) { //excluir elemento da lista de bonde

                    $idbd = get_get( 'idbd', 'int' );
                    $q_del_det_bonde = "DELETE FROM `bonde_det` WHERE `idbd` = $idbd LIMIT 1";

                    // instanciando o model
                    $model = SicopModel::getInstance();

                    // executando a query
                    $model->query( $q_del_det_bonde );

                    // fechando a conexao
                    $model->closeConnection();

                }

            } // fim do if ( !empty( $proced ) )

            $sq_s_det_bonde = "SELECT
                                 `bonde_det`.`idbd`
                               FROM
                                 `bonde`
                                 INNER JOIN `bonde_locais` ON `bonde_locais`.`cod_bonde` = `bonde`.`idbonde`
                                 INNER JOIN `bonde_det` ON `bonde_det`.`cod_bonde_local` = `bonde_locais`.`idblocal`
                               WHERE
                                 `bonde`.`idbonde` = $idbonde
                                 AND
                                 `bonde_locais`.`idblocal` = $idb_local";

            $q_s_det_bonde = "SELECT
                                `bonde_det`.`idbd`,
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
                                INNER JOIN bonde_det ON `bonde_det`.`cod_detento` = detentos.iddetento
                                LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                                LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                                LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                                LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                                LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
                              WHERE
                                `bonde_det`.`idbd` IN( $sq_s_det_bonde )
                              ORDER BY
                                `detentos`.`nome_det`";

            //echo nl2br($q_s_det_bonde);
            //exit;

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $q_s_det_bonde = $model->query( $q_s_det_bonde );

            // fechando a conexao
            $model->closeConnection();

            $cont_q_s_det_bonde = $q_s_det_bonde->num_rows;

            if ( $cont_q_s_det_bonde >= 1 ) {

                ?>
            <p class="table_leg">Lista atual</p>

<!--            <p align="center"><?php //echo 'oi' . get_get( 'add_det_bonde_x' )?></p>-->

            <form action='<?php echo $_SERVER['PHP_SELF'];?>' method='get' name='del_det_bonde'>

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

                        while( $d_det = $q_s_det_bonde->fetch_assoc() ) {

                            $tipo_mov_in  = $d_det['tipo_mov_in'];
                            $tipo_mov_out = $d_det['tipo_mov_out'];
                            $iddestino    = $d_det['iddestino'];

                            $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );

                            ?>

                    <tr class="even">
                        <td class="num_od"><?php echo $i++; ?></td>
                        <td class="nome_det" title="Pai: <?php echo $d_det['pai_det'];?>&#13;Mãe: <?php echo $d_det['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat'];?>"><a href="detento/detalhesdet.php?iddet=<?php echo $d_det['iddetento'] /*alphaID($d_det['iddetento'])*/;?>"> <?php echo $d_det['nome_det'];?></a></td>
                        <td class="matr_det <?php echo $det['css_class']; ?>"><?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] );?></td>
                        <td class="raio_det <?php echo $det['css_class']; ?>"><?php echo $d_det['raio'];?></td>
                        <td class="cela_det <?php echo $det['css_class']; ?>"><?php echo $d_det['cela'];?></td>
                        <td class="tb_bt"><input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" name="submit" onClick="r_send_idbd('<?php echo $d_det['idbd'] ;?>');" /></td>
                    </tr>

                    <?php } // fim do while $d_det... ?>

                </table><!-- /table.lista_busca -->

                <input type='hidden' name='proced' id='proced' value="2" />
                <input name="idbonde" type="hidden" id="idbonde" value="<?php echo $idbonde; ?>" />
                <input name="idb_local" type="hidden" id="idb_local" value="<?php echo $idb_local; ?>" />
                <input type='hidden' name='idbd' id='idbd' value="" />

            </form><!-- fim do form name='del_det_bonde' -->

            <p class="bt_leg">PRÓXIMO PASSO:</p>

            <form action='<?php echo $_SERVER['PHP_SELF'];?>' method='get' name='elemt_list'>

                <div class="form_bts">

                    <input class="form_bt" name="add_local" type="button" value="Adicionar outro local" onclick="javascript: location.href='<?php echo $_SERVER['PHP_SELF'];?>?idbonde=<?php echo $idbonde;?>';" />
                    <input class="form_bt" name="lista_bonde" type="button" value="Listar os bondes cadastrados" onclick="javascript: location.href='lista_bonde.php';" />
                    <input class="form_bt" name="lista_det_bonde" type="button" value="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste bonde" onclick="javascript: location.href='detal_bonde.php?idbonde=<?php echo $idbonde;?>';" />

                </div>

                <input name="idbonde" type="hidden" id="idbonde" value="<?php echo $idbonde; ?>" />
                <input name="idb_local" type="hidden" id="idb_local" value="<?php echo $idb_local; ?>" />
                <input type='hidden' name='proced' id='proced' value="4" />

            </form><!-- fim do form name='elemt_list' -->

            <?php } // fim do if ( $cont_q_s_det_bonde >= 1 ) ?>

            <?php } // fim do if que verifica o $idb_local ?>

            <?php } // fim do if que verifica o $idbonde ?>

<?php include 'footer.php'; ?>