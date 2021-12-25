<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_cadastro = SicopController::getSession( 'n_cadastro', 'int' );
$n_cad_n    = 3;

if ( $n_cadastro < $n_cad_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'PEDIDOS DE ESCOLTA';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idescolta  = get_get( 'idescolta', 'int' );
$idlocalesc = get_get( 'idlocalesc', 'int' );

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Cadastrar pedido de escolta';

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>
        <link href="../js/datepicker/css/datepicker.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="../js/datepicker/js/datepicker.js"></script>
        <script type="text/javascript" src="../js/funcoes.js"></script>
        <script type="text/javascript" src="../js/valida.js"></script>
        <script type="text/JavaScript">
            function R_enviar( x ) {
                element = id( 'iddet' );
                element.value = x
            }

            function r_send_ided( x ) {
                element = id( 'ided' );
                element.value = x
            }
        </script>
        <div class="no_print">


            <p align="center" class="paragrafo14Italico">CADASTRAR PEDIDO DE ESCOLTA</p>
            <?php if ( empty( $idescolta ) ) { ?>
            <form action="../send/sendpesc.php" method="post" name="cadpesc" id="cadesc">

                <p>&nbsp;</p>

                <table class="bonde_add">
                    <tr>
                        <td class="data_esc_leg">Data:</td>
                        <td class="data_esc_field"><input name="escolta_data" type="text" class="CaixaTexto" id="escolta_data" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" size="12" maxlength="10" />&nbsp;&nbsp;<a href="#" onclick="javascript: datahoje('escolta_data'); return false;" >hoje</a></td>
                    </tr>
                    <tr>
                        <td class="data_esc_leg">Hora:</td>
                        <td class="data_esc_field"><input name="escolta_hora" type="text" class="CaixaTexto" id="escolta_hora" onkeypress="mascara_hora(this, this.value); return blockChars(event, 2);" size="5" maxlength="5" /></td>
                    </tr>
                    <tr>
                        <td class="data_esc_leg">Finalidade:</td>
                        <td class="data_esc_field"><input name="finalidade" type="text" class="CaixaTexto" id="finalidade" onkeypress="return blockChars(event, 4);" size="60" maxlength="50" /></td>
                    </tr>
                </table><!-- fim da table."bonde_add" -->

                <p>&nbsp;</p>
                <input type="hidden" name="proced" id="proced" value="3">
                <p align="center">
                    <input name="cadesc" type="submit" value="Cadastrar"  onclick="return valida_escolta(1);" />&nbsp;&nbsp;&nbsp;
                    <input name="" type="button" onClick="history.go(-1)" value="Cancelar" />
                </p>

          </form><!-- fim do form name="cadesc" -->

          <input name="datahj" type="hidden" id="datahj" value="<?php echo date('d/m/Y') ?>">
          <script type="text/javascript">id("escolta_data").focus();</script>
          <script type="text/javascript">
                // <![CDATA[
                var opts = {
                    formElements:{"escolta_data":"d-sl-m-sl-Y"},
                    statusFormat:"l-cc-sp-d-sp-F-sp-Y",
                    fillGrid:true,
                    constrainSelection:false,
                    noFadeEffect:true
                };
                datePickerController.createDatePicker(opts);
                // ]]>
            </script>

            <?php

            } else { // do if que verifica o $idescolta

                $q_escolta = "SELECT
                                DATE_FORMAT( `escolta_data`, '%d/%m/%Y' ) AS `escolta_data_f`,
                                DATE_FORMAT( `escolta_hora`, '%H:%i' ) AS `escolta_hora_f`
                              FROM
                                `ordens_escolta`
                              WHERE
                                `idescolta` = $idescolta
                              LIMIT 1";

                $sit_pag = 'PEDIDO DE ESCOLTA';

                $q_escolta = mysql_query( $q_escolta );

                if ( !$q_escolta ) {

                    // pegar a mensagem de erro mysql
                    $msg_err_mysql = get_err_mysql();

                    // montar a mensagem q será salva no log
                    $msg = array();
                    $msg['tipo']  = 'err';
                    $msg['text']  = "Falha na consulta ( $sit_pag ).\n\n $msg_err_mysql";
                    $msg['linha'] = __LINE__;
                    get_msg( $msg, 1 );

                    echo msg_js( '', 1 );
                    exit;

                }

                $cont_escolta = mysql_num_rows( $q_escolta );

                if( $cont_escolta < 1 ) {

                    // montar a mensagem q será salva no log
                    $msg = array();
                    $msg['tipo']  = 'err';
                    $msg['text']  = "A consulta retornou 0 ocorrências ( $sit_pag ).";
                    $msg['linha'] = __LINE__;
                    get_msg( $msg, 1 );

                    echo msg_js( '', 1 );
                    exit;

                }

                $d_escolta    = mysql_fetch_assoc( $q_escolta );
                $escolta_data = $d_escolta['escolta_data_f'];
                $escolta_hora = $d_escolta['escolta_hora_f'];
                ?>

            <p align="center">Data/hora da escolta: <?php echo $escolta_data ?><?php if ( !empty ( $escolta_hora ) ) { ?> às <?php echo $escolta_hora; ?><?php } ?></p>

                <?php
                if ( empty( $idlocalesc ) ) {

                    $q_local_esc = "SELECT `cod_local` FROM `ordens_escolta_locais` WHERE `cod_escolta` = $idescolta";

                    $q_local_apr = "SELECT
                                      `locais_apr`.`idlocal`,
                                      `locais_apr`.`local_apr`,
                                      `locais_apr`.`local_end`
                                    FROM
                                      `locais_apr`
                                    WHERE
                                      `locais_apr`.`idlocal` NOT IN( $q_local_esc )
                                    ORDER BY
                                      `locais_apr`.`local_apr`";

                    $sit_pag = 'PEDIDO DE ESCOLTA - LOCAL';

                    $q_local_apr = mysql_query( $q_local_apr );
                    if ( !$q_local_apr ) {

                        // pegar a mensagem de erro mysql
                        $msg_err_mysql = get_err_mysql();

                        // montar a mensagem q será salva no log
                        $msg = array();
                        $msg['tipo']  = 'err';
                        $msg['text']  = "Falha na consulta ( $sit_pag ).\n\n $msg_err_mysql";
                        $msg['linha'] = __LINE__;
                        get_msg( $msg, 1 );

                        echo msg_js( '', 1 );
                        exit;

                    }

                    $cont_l_esc = mysql_num_rows( $q_local_apr );

                    if( $cont_l_esc < 1 ) {

                        // montar a mensagem q será salva no log
                        $msg = array();
                        $msg['tipo']  = 'err';
                        $msg['text']  = "A consulta retornou 0 ocorrências ( $sit_pag ).";
                        $msg['linha'] = __LINE__;
                        get_msg( $msg, 1 );

                        echo msg_js( '', 1 );
                        exit;

                    }

                    $q_local_atual_esc = "SELECT
                                             `ordens_escolta_locais`.`id_local_escolta`,
                                             `locais_apr`.`local_apr`
                                           FROM
                                             `ordens_escolta_locais`
                                             INNER JOIN `locais_apr` ON `locais_apr`.`idlocal` = `ordens_escolta_locais`.`cod_local`
                                           WHERE
                                             `ordens_escolta_locais`.`cod_escolta` = $idescolta";

                    $q_local_atual_esc = mysql_query( $q_local_atual_esc );

                    $cont_q_local_atual_esc = mysql_num_rows( $q_local_atual_esc );

                    if( $cont_q_local_atual_esc >= 1 ) {
                ?>
                <p align="center">Destinos cadastrados: </p>

                <table width="250" class="bonde_add">
                <?php while( $d_l_esc_atual = mysql_fetch_assoc( $q_local_atual_esc ) ) { ?>
                    <tr class="even_gr">
                        <td class="drop_field"><a href="<?php echo $_SERVER['PHP_SELF'];?>?idescolta=<?php echo $idescolta;?>&idlocalesc=<?php echo $d_l_esc_atual['id_local_escolta'];?>" title="Clique para ver <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s que estão cadastrados neste destino"><?php echo $d_l_esc_atual['local_apr'];?></a></td>
                        <?php if ( $n_cadastro >= 3 ) {  ?>
                        <td class="tb_bt"><a href='javascript:void(0)' onclick='drop_escolta( <?php echo $d_l_esc_atual['id_local_escolta']; ?>, 1 )' title="Excluir este destino"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir este destino" class="icon_button" /></a></td>
                        <?php }; ?>
                    </tr>
                <?php } // fim do while( $d_l_esc_atual = mysql_fetch_assoc( $q_local_atual_esc ) ) ?>
                </table><!-- fim da table."bonde_add" -->
                <?php } // fim do if( $cont_q_local_atual_esc >= 1 ) ?>
            <br />

            <form action="../send/sendpesc.php" method="post" name="cadlocalesc" id="cadlocalesc">

                <table width="420" class="bonde_add">
                    <tr>
                        <td class="leg">Destino:</td>
                        <td class="field">
                            <select name="local_esc" class="CaixaTexto" id="local_esc">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $d_local_esc = mysql_fetch_assoc( $q_local_apr ) ) { ?>
                                <option value="<?php echo $d_local_esc['idlocal']; ?>"><?php echo $d_local_esc['local_apr']; ?></option>
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

                <p>&nbsp;</p>

                <input type="hidden" name="idescolta" id="idescolta" value="<?php echo $idescolta; ?>">
                <input type="hidden" name="proced" id="proced" value="3">

                <p align="center">
                    <input name="cadlocalesc" type="submit" value="Cadastrar"  onclick="return valida_escolta(2);" />&nbsp;&nbsp;&nbsp;
                    <input name="" type="button" onClick="history.go(-1)" value="Cancelar" />
                </p>

            </form><!-- fim do form name="cadlocalesc" -->

            <p align="center"><a href="add_local_escolta.php">Cadastrar localidade</a></p>

            <?php
            } else { // do if que verifica o $idlocalesc

                $q_local_esc = "SELECT
                                  `ordens_escolta_locais`.`id_local_escolta`,
                                  `locais_apr`.`local_apr`,
                                  `locais_apr`.`local_end`
                                FROM
                                  `ordens_escolta_locais`
                                  INNER JOIN `locais_apr` ON `locais_apr`.`idlocal` = `ordens_escolta_locais`.`cod_local`
                                WHERE
                                  `ordens_escolta_locais`.`id_local_escolta` = $idlocalesc
                                LIMIT 1";

                $sit_pag = 'PEDIDO DE ESCOLTA - LOCAL DE APRESENTAÇÃO';

                $q_local_esc = mysql_query( $q_local_esc );
                if ( !$q_local_esc ) {

                    // pegar a mensagem de erro mysql
                    $msg_err_mysql = get_err_mysql();

                    // montar a mensagem q será salva no log
                    $msg = array();
                    $msg['tipo']  = 'err';
                    $msg['text']  = "Falha na consulta ( $sit_pag ).\n\n $msg_err_mysql";
                    $msg['linha'] = __LINE__;
                    get_msg( $msg, 1 );

                    echo msg_js( '', 1 );
                    exit;

                }

                $cont_local_esc = mysql_num_rows( $q_local_esc );

                if( $cont_local_esc < 1 ) {

                    // montar a mensagem q será salva no log
                    $msg = array();
                    $msg['tipo']  = 'err';
                    $msg['text']  = "A consulta retornou 0 ocorrências ( $sit_pag ).";
                    $msg['linha'] = __LINE__;
                    get_msg( $msg, 1 );

                    echo msg_js( '', 1 );
                    exit;

                }

                $d_local_esc = mysql_fetch_assoc( $q_local_esc );
                $local_esc = $d_local_esc['local_apr'];

            ?>

            <p align="center">Destino: <?php echo $local_esc ?></p>

            <br />

            <p align="center" class="paragrafo12Italico">PESQUISAR <?php echo SICOP_DET_DESC_U ;?></p>
            <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="get" name="busca_det" id="busca_det" >
                <table width="376" align="center">
                    <tr>
                        <td width="370" align="center">Digite o NOME ou a MATRÍCULA d<?php echo SICOP_DET_ART_L . SICOP_DET_DESC_L; ?>:</td>
                    </tr>
                    <tr>
                        <td align="center"><input name="campobusca" type="text" class="CaixaTexto" id="campobusca" onkeypress="return blockChars(event, 4);" size="50" /></td>
                    </tr>
                </table>
                <table width="369" align="center" cellspacing="5">
                    <tr>
                        <td width="365" align="center">
                            <input type='hidden' name='proced' id='proced' value=1>
                            <input type="hidden" name="idescolta" id="idescolta" value="<?php echo $idescolta; ?>">
                            <input type="hidden" name="idlocalesc" id="idlocalesc" value="<?php echo $idlocalesc; ?>">
                            <input type="submit" name="submit" id="busca" value="Buscar" />
                        </td>
                    </tr>
                </table>
            </form>
            <script type="text/javascript">id("campobusca").focus();</script>
            <br />

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

                        $query_det = mysql_query($query_det );
                        $cont_query_det = mysql_num_rows($query_det);

                        // se retornar mais de 1 resultado, gera a tabela abaixo para escolher quem vai ser adicionado.
                        if( $cont_query_det > 1 ) {
                            $iddet_array = array();
                            while( $dados_det = mysql_fetch_assoc( $query_det ) ) {
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

                            $q_det_select = mysql_query( $q_det_select );

                            ?>
            <p align="center">Resultado da busca</p>
            <form action='<?php echo $_SERVER['PHP_SELF'];?>' method='get' name='add_det_esc'>
                <table width="593" align="center" class="space">
                    <tr>
                        <th width="40" align="center">N</th>
                        <th width="300" align="center"><?php echo SICOP_DET_DESC_FU; ?></th>
                        <th width="95" align="center">Matrícula </th>
                        <th width="60" align="center"><?php echo SICOP_RAIO ?></th>
                        <th width="40" align="center"><?php echo SICOP_CELA ?></th>
                        <th width="30" align="center">&nbsp;</th>
                    </tr>
                                    <?php
                                    $i = 1;

                                    while( $d_det = mysql_fetch_assoc( $q_det_select ) ) {

                                        $tipo_mov_in  = $d_det['tipo_mov_in'];
                                        $tipo_mov_out = $d_det['tipo_mov_out'];
                                        $iddestino    = $d_det['iddestino'];

                                        $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );

                                        ?>
                    <tr class="even">
                        <td align="center"><?php echo $i++; ?></td>
                        <td align="left" title="Pai: <?php echo $d_det['pai_det'];?>&#13;Mãe: <?php echo $d_det['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat'];?>"><a href="../detento/detalhesdet.php?iddet=<?php echo $d_det['iddetento'] /*alphaID($d_det['iddetento'])*/;?>"> <?php echo $d_det['nome_det'];?></a></td>
                        <td align="center"><font color="<?php echo $det['corfontd'];?>"><?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] );?></font></td>
                        <td align="center"><font color="<?php echo $det['corfontd'];?>"><?php echo $d_det['raio'];?></font></td>
                        <td align="center"><font color="<?php echo $det['corfontd'];?>"><?php echo $d_det['cela'];?></font></td>
                        <td align="center"><input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>s_add.png" name="add_det_esc" onClick="R_enviar('<?php echo $d_det['iddetento'] ;?>');" /></td>
                    </tr>
                                        <?php
                                    } // fim do while( $d_det = mysql_fetch_assoc( $q_det_select ) )
                                    ?>
                </table>

                <input type="hidden" name="proced" id="proced" value=1>
                <input type="hidden" name="idescolta" id="idescolta" value="<?php echo $idescolta; ?>">
                <input type="hidden" name="idlocalesc" id="idlocalesc" value="<?php echo $idlocalesc; ?>">
                <input type="hidden" name="iddet" id="iddet" value="">

            </form><!-- fim do form name='add_det_esc' -->
                            <?php

                        // se não retornar niguem, mostra a msg
                        } else if ( $cont_query_det < 1) { // do if( $cont > 1 )

                            echo '<p>&nbsp;</p><p align="center" class="paragrafo12Italico">Não foi encontrado nenhuma ocorrência.</p>';

                        } // fim do if( $cont > 1 )

                    } // fim do if ( !empty( $valorbusca ) )


                    $add_det_esc = get_get( 'add_det_esc_x' );

                    if ( $cont_query_det == 1 or !empty( $add_det_esc ) ) {

                        $iddet = '';

                        if ( $cont_query_det == 1 ) {

                            $dados_det = mysql_fetch_assoc( $query_det );
                            $iddet = $dados_det['iddetento'];

                        }

                        if ( !empty( $add_det_esc ) ) {
                            $iddet = get_get( 'iddet', 'int' );
                        }

                        if ( !empty( $iddet ) ) {

                            /**
                             * @var $ck_det_bonde string dadfadf
                             */
                            $ck_det_esc = "SELECT
                                             `ordens_escolta_det`.`cod_detento`,
                                             `detentos`.`nome_det`,
                                             `detentos`.`matricula`
                                           FROM
                                             `ordens_escolta`
                                             INNER JOIN `ordens_escolta_locais` ON `ordens_escolta_locais`.`cod_escolta` = `ordens_escolta`.`idescolta`
                                             INNER JOIN `ordens_escolta_det` ON `ordens_escolta_det`.`cod_local_escolta` = `ordens_escolta_locais`.`id_local_escolta`
                                             INNER JOIN `detentos` ON `ordens_escolta_det`.`cod_detento` = `detentos`.`iddetento`
                                           WHERE
                                             `ordens_escolta`.`idescolta` = $idescolta
                                             AND
                                             `ordens_escolta_det`.`cod_detento` = $iddet";

                            $ck_det_esc = mysql_query( $ck_det_esc );
                            $cont_ck_det_esc = mysql_num_rows( $ck_det_esc );

                            if ( $cont_ck_det_esc < 1 ) {

                                $add_det_time = get_get( 'add_det_time', 'int' );

                                if ( empty( $add_det_time ) ) {

                                    $q_s_det_esc = "SELECT
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
                                                      `detentos`.`iddetento` = $iddet
                                                    LIMIT 1";

                                    $q_s_det_esc = mysql_query( $q_s_det_esc );


                                    $d_det = mysql_fetch_assoc( $q_s_det_esc );

                                    $tipo_mov_in  = $d_det['tipo_mov_in'];
                                    $tipo_mov_out = $d_det['tipo_mov_out'];
                                    $iddestino    = $d_det['iddestino'];
                                    $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );

?>

                                <form action='<?php echo $_SERVER['PHP_SELF'];?>' method='get' name='del_det_esc'>

                                    <table align="center" class="edit">
                                        <tr>
                                            <th width="300" align="center"><?php echo SICOP_DET_DESC_FU; ?></th>
                                            <th width="95" align="center">Matrícula </th>
                                            <th width="30" align="center">&nbsp;</th>
                                        </tr>

                                        <tr>
                                            <td align="left" title="Pai: <?php echo $d_det['pai_det'];?>&#13;Mãe: <?php echo $d_det['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat'];?>"><a href="../detento/detalhesdet.php?iddet=<?php echo $d_det['iddetento'] /*alphaID($d_det['iddetento'])*/;?>"> <?php echo $d_det['nome_det'];?></a></td>
                                            <td align="center"><font color="<?php echo $det['corfontd'];?>"><?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] );?></font></td>
                                            <td align="center"><input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" name="submit" onClick="r_send_ided('<?php //echo $d_det['id_escolta_det'] ;?>');" /></td>
                                        </tr>
                                        <tr>
                                            <td align="left" colspan="3">Horário: <input name="hora_v" type="text" class="CaixaTexto" id="local_hora" onkeypress="mascara_hora(this, this.value); return blockChars(event, 2);" size="5" maxlength="5" /></td>
                                        </tr>
                                    </table>

                                    <input type="hidden" name="proced" id="proced" value=1>
                                    <input type="hidden" name="idescolta" id="idescolta" value="<?php echo $idescolta; ?>" />
                                    <input type="hidden" name="idlocalesc" id="idlocalesc" value="<?php echo $idlocalesc; ?>" />
                                    <input type="hidden" name="add_det_time" id="add_det_time" value="1" />
                                    <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet; ?>" />

                                    <p align="center"><input type="submit" name="submit" id="busca" value="Buscar" /></p>

                                </form>

<?php

                                } else {



                                    $user = SicopController::getSession( 'user_id', 'int' );
                                    $ip   = "'" . $_SERVER['REMOTE_ADDR'] . "'";

                                    $hora_v = get_get( 'hora_v', 'busca' );

                                    $q_in_det_esc = "INSERT INTO
                                                       `ordens_escolta_det`
                                                       (
                                                         `cod_local_escolta`,
                                                         `cod_detento`,
                                                         `hora`,
                                                         `user_add`,
                                                         `data_add`,
                                                         `ip_add`
                                                       )
                                                     VALUES
                                                       (
                                                         $idlocalesc,
                                                         $iddet,
                                                         $hora_v,
                                                         $user,
                                                         NOW(),
                                                         $ip
                                                       )";

                                    mysql_query( $q_in_det_esc );

                                }

                            } else { // do if ( $cont_ck_det_esc < 1 )

                                $d_ck_det_esc = mysql_fetch_assoc( $ck_det_esc );

                      ?>

                <p align="center">

                    Este detento já esta cadastrado neste pedido de escolta!<br/>
                    <?php echo $d_ck_det_esc['nome_det'] ?>, matrícula <?php if ( !empty( $d_ck_det_esc['matricula'] ) ) echo formata_num( $d_ck_det_esc['matricula'] ) ?>

                </p>

                      <?php
                            } // fim do if ( $cont_ck_det_esc < 1 )

                        } // fim do if ( !empty( $iddet ) )

                    } // fim do if ( $cont_query_det == 1 or !empty( $add_det_esc ) )

                } else if ( $proced == 2 ) { //excluir elemento do pedido de escolta

                    $ided = get_get( 'ided', 'int' );
                    $q_del_det_esc = "DELETE FROM `ordens_escolta_det` WHERE `id_escolta_det` = $ided LIMIT 1";

                    mysql_query( $q_del_det_esc );

                }

            } // fim do if ( !empty( $proced ) )

            $sq_s_det_esc = "SELECT
                               `ordens_escolta_det`.`id_escolta_det`
                             FROM
                               `ordens_escolta`
                               INNER JOIN `ordens_escolta_locais` ON `ordens_escolta_locais`.`cod_escolta` = `ordens_escolta`.`idescolta`
                               INNER JOIN `ordens_escolta_det` ON `ordens_escolta_det`.`cod_local_escolta` = `ordens_escolta_locais`.`id_local_escolta`
                             WHERE
                               `ordens_escolta`.`idescolta` = $idescolta
                               AND
                               `ordens_escolta_locais`.`id_local_escolta` = $idlocalesc";

            $q_s_det_esc = "SELECT
                              `ordens_escolta_det`.`id_escolta_det`,
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
                              INNER JOIN `ordens_escolta_det` ON `ordens_escolta_det`.`cod_detento` = `detentos`.`iddetento`
                              LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                              LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                              LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                              LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                              LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
                            WHERE
                              `ordens_escolta_det`.`id_escolta_det` IN( $sq_s_det_esc )
                            ORDER BY
                              `detentos`.`nome_det`";

            $q_s_det_esc = mysql_query( $q_s_det_esc );
            $cont_q_s_det_esc = mysql_num_rows( $q_s_det_esc );

            if ( $cont_q_s_det_esc >= 1 ) {

                ?>
            <p align="center">Lista atual</p>
            <p align="center"><?php //echo 'oi' . get_get( 'add_det_bonde_x' )?></p>
            <form action='<?php echo $_SERVER['PHP_SELF'];?>' method='get' name='del_det_esc'>
                <table width="593" align="center" class="space">
                    <tr>
                        <th width="40" align="center">N</th>
                        <th width="300" align="center"><?php echo SICOP_DET_DESC_FU; ?></th>
                        <th width="95" align="center">Matrícula </th>
                        <th width="60" align="center"><?php echo SICOP_RAIO ?></th>
                        <th width="40" align="center"><?php echo SICOP_CELA ?></th>
                        <th width="30" align="center">&nbsp;</th>
                    </tr>
                        <?php
                        $i = 1;

                        while( $d_det = mysql_fetch_assoc( $q_s_det_esc ) ) {

                            $tipo_mov_in  = $d_det['tipo_mov_in'];
                            $tipo_mov_out = $d_det['tipo_mov_out'];
                            $iddestino    = $d_det['iddestino'];

                            $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );

                            ?>
                    <tr class="even">
                        <td align="center"><?php echo $i++; ?></td>
                        <td align="left" title="Pai: <?php echo $d_det['pai_det'];?>&#13;Mãe: <?php echo $d_det['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat'];?>"><a href="../detento/detalhesdet.php?iddet=<?php echo $d_det['iddetento'] /*alphaID($d_det['iddetento'])*/;?>"> <?php echo $d_det['nome_det'];?></a></td>
                        <td align="center"><font color="<?php echo $det['corfontd'];?>"><?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] );?></font></td>
                        <td align="center"><font color="<?php echo $det['corfontd'];?>"><?php echo $d_det['raio'];?></font></td>
                        <td align="center"><font color="<?php echo $det['corfontd'];?>"><?php echo $d_det['cela'];?></font></td>
                        <td align="center"><input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" name="submit" onClick="r_send_ided('<?php echo $d_det['id_escolta_det'] ;?>');" /></td>
                    </tr>
                            <?php
                        } // fim do while $d_det = mysql_fetch_assoc( $q_s_det_esc ) )
                        ?>
                </table>

                <input type='hidden' name='proced' id='proced' value=2>
                <input type="hidden" name="idescolta" id="idescolta" value="<?php echo $idescolta; ?>">
                <input type="hidden" name="idlocalesc" id="idlocalesc" value="<?php echo $idlocalesc; ?>">
                <input type='hidden' name='ided' id='ided' value="">

            </form><!-- fim do form name='del_det_esc' -->

            <p align="center" class="paragrafo12">PRÓXIMO PASSO:</p>

            <form action='<?php echo $_SERVER['PHP_SELF'];?>' method='get' name='elemt_list'>

                <p align="center">
                <?php //if ( $tipo_lista == 'foto_det' ) { ?>
                <input name="add_local" type="button" value="Adicionar outro local" onclick="javascript: location.href='<?php echo $_SERVER['PHP_SELF'];?>?idescolta=<?php echo $idescolta;?>';" />&nbsp;&nbsp;&nbsp;
                <?php //} ?>
                <?php //if ( $tipo_lista == 'termo_ab' ) { ?>
                <input name="lista_esc" type="button" value="Listar os pedidos de escolta" onclick="javascript: location.href='lista_escolta.php';" />&nbsp;&nbsp;&nbsp;
                <?php //} ?>
                <input name="lista_det_esc" type="button" value="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste pedido de escolta" onclick="javascript: location.href='detal_escolta.php?idescolta=<?php echo $idescolta;?>';" />&nbsp;&nbsp;&nbsp;
                </p>
                <input type="hidden" name="idescolta" id="idescolta" value="<?php echo $idescolta; ?>">
                <input type="hidden" name="idlocalesc" id="idlocalesc" value="<?php echo $idlocalesc; ?>">
                <input type='hidden' name='proced' id='proced' value=4>

            </form><!-- fim do form name='elemt_list' -->

            <?php

            } // fim do if ( $cont_q_s_det_esc >= 1 )

            ?>

            <?php } // fim do if que verifica o $idlocalesc ?>

            <?php } // fim do if que verifica o $idescolta ?>

<?php include 'footer.php'; ?>