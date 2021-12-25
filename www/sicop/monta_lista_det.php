<?php
if ( !isset( $_SESSION ) ) session_start();

require 'init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$tipo_lista = get_get( 'tipo_lista', 'busca' );

$n_imp_n  = 1;
$motivo   = '';
$desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's';

switch ( $tipo_lista ) {
    default:
    case '':
        $tipo_lista = '';
        break;
    case 'foto_det':
        $imp_det = get_session( 'imp_det', 'int' );
        if ( $imp_det < $n_imp_n ) {
            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'GERAR LISTA PARA IMPRESSÃO DE FOTOS';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;
        }
        $motivo = 'PARA IMPRESSÃO DE FOTOS';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's para impressão de fotos';
        break;
    case 'recibo_cad':
        $imp_cadastro = get_session( 'imp_cadastro', 'int' );
        if ( $imp_cadastro < $n_imp_n ) {
            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'GERAR LISTA PARA IMPRESSÃO DE RECIBO DE ESCOLTA - CADASTRO';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;
        }
        $motivo = 'PARA IMPRESSÃO DE RECIBO DE ESCOLTA';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's para impressão de recibo de escolta';
        break;
    case 'termo_ab':
        $imp_pront = get_session( 'imp_pront', 'int' );
        if ( $imp_pront < $n_imp_n ) {
            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'GERAR LISTA PARA IMPRESSÃO DE TERMOS DE ABERTURA';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;
        }
        $motivo = 'PARA IMPRESSÃO DE TERMOS DE ABERTURA';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's para impressão de termos de abertura';
        break;
    case 'export':
        $imp_chefia   = get_session( 'imp_chefia', 'int' );
        $imp_cadastro = get_session( 'imp_cadastro', 'int' );
        if ( $imp_chefia < $n_imp_n and $imp_cadastro < $n_imp_n ) {
            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'GERAR LISTA PARA IMPRESSÃO OU EXPORTAÇÃO';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;
        }
        $motivo = 'PARA IMPRESSÃO OU EXPORTAÇÃO';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's para impressão ou exportação';
        break;
}

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );


require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add($desc_pag, $pag_atual, 3);
$trail->output();
?>

            <script type="text/JavaScript">
                function R_enviar(x) {
                    element = id( 'iddet' );
                    element.value = x
                }
            </script>

            <p class="descript_page">PESQUISAR <?php echo SICOP_DET_DESC_U; ?>S <?php echo $motivo; ?></p>

            <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="get" name="busca_det" id="busca_det" >

                <p class="table_leg">Digite o NOME ou a MATRÍCULA d<?php echo SICOP_DET_ART_L; ?> <?php echo SICOP_DET_DESC_L;?>:</p>

                <div class="form_one_field">
                    <input name="campobusca" type="text" class="CaixaTexto" id="campobusca" onkeypress="return blockChars(event, 4);" size="50" />
                </div>

                <input type='hidden' name='proced' id='proced' value="1" />
                <input type='hidden' name='tipo_lista' id='tipo_lista' value='<?php echo $tipo_lista; ?>' />


                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="busca" value="Buscar" />
                </div>

            </form>

            <script type="text/javascript">id('campobusca').focus();</script>

            <?php

            //if($_SERVER['REQUEST_METHOD'] == 'POST') {

            $proced = !empty( $_GET['proced'] ) ? (int)$_GET['proced'] : '';

            // se estiver setado o procedimento...
            if ( !empty( $proced ) ) {

                if ( isset( $proced ) and $proced == '1' ) {

                    $valorbusca = '';
                    if ( isset( $_GET['campobusca'] ) ) {
                        $valorbusca = tratabusca( $_GET['campobusca'] );
                        //$matr = (int)$matr;
                    }

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

                        if( !$query_det ) {

                            echo msg_js( 'FALHA!!!', 1 );
                            exit;

                        }

                        $cont = $query_det->num_rows;

                        // se retornar mais de 1 resultado, gera a tabela abaixo para escolher quem vai ser adicionado.
                        if( $cont > 1 ) {
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
                                              detentos
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

                            if( !$q_det_select ) {

                                echo msg_js( 'FALHA!!!', 1 );
                                exit;

                            }

                            ?>

            <p class="p_q_info">Resultado da busca</p>

            <form action='<?php echo $_SERVER['PHP_SELF'];?>' method='get' name='form'>

                <table class="lista_busca">
                    <tr class="cab">
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
                        <td class="tb_bt"><input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>s_add.png" name="submit" onClick="R_enviar('<?php echo $d_det['iddetento'] ;?>');" /></td>
                    </tr>
                                        <?php } // fim do while ?>
                </table>

                <input type='hidden' name='proced' id='proced' value="3" />
                <input type='hidden' name='tipo_lista' id='tipo_lista' value='<?php echo $tipo_lista; ?>' />
                <input type='hidden' name='iddet' id='iddet' value="" />

            </form>
                        <?php

                        // se retronar apenas 1, ja adiciona ele direto na lista
                        } else if ( $cont == 1) {

                            $dados_det = $query_det->fetch_assoc();
                            $iddet = $dados_det['iddetento'];

                        // se não retornar niguem, mostra a msg
                        } else if ( $cont < 1) {

                            echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';

                        } // fim do if( $cont > 1 )

                    } // if ( !empty( $valorbusca ) )

                    $det =  !empty( $_SESSION['det'] ) ? $_SESSION['det'] : array();
                    //$det = $_SESSION['det'];

                    // este $iddet é o que está no if de retornar apenas 1 resultado
                    if ( !empty( $iddet ) ) { //se não estiver vazio...
                        if ( !in_array( $iddet, $det ) ) { //se não estiver no array
                            $det[] = $iddet;
                        }
                    }

                    $_SESSION['det'] = $det;

                } else if ( isset( $proced ) and $proced == 2 ) { //excluir elemento do array

                    $iddet = (int)$_GET['iddet'];
                    $det = $_SESSION['det'];

                    $key = NULL;

                    // se estiver no array, ele vai pegar o indice do elemento
                    if ( in_array( $iddet, $det ) ) {
                        $key = ( array_search( $iddet, $det ) );
                    }

                    // se o indice não for null, vai apargar o elemento pelo indice
                    if ( $key !== NULL ) {
                        unset( $det["$key"] );
                    }

                    $_SESSION['det'] = $det;

                } else if ( isset( $proced ) and $proced == 3 ) {

                    $iddet = (int)$_GET['iddet'];
                    $det   = !empty( $_SESSION['det'] ) ? $_SESSION['det'] : array();

                    if ( !empty( $iddet ) ) {
                        if ( !in_array( $iddet, $det ) ) {
                            $det[] = $iddet;
                        }
                    }

                    $_SESSION['det'] = $det;

                } else if ( isset( $proced ) and $proced == 4 ) { // imprimir

                    $imp_foto_det = get_get ( 'imp_foto_det' );
                    $imp_termo_ab = get_get ( 'imp_termo_ab' );
                    $imp_lista    = get_get ( 'imp_lista' );
                    $exp_lista    = get_get ( 'exp_lista' );
                    $imp_recibo   = get_get ( 'imp_recibo' );
                    $limpar       = get_get ( 'limpar' );

                    if ( !empty( $imp_foto_det ) or !empty( $imp_termo_ab ) or !empty( $imp_lista ) or !empty( $imp_recibo ) ) {

                        $iddet = get_session( 'det' );

                        $situacao = '';
                        $pag_imp  = '';

                        if ( !empty( $imp_termo_ab ) ) {
                            $situacao = 'IMPRESSÃO DE TERMOS DE ABERTURA DO PROTUÁRIO';
                            $pag_imp = 'termo_ab_pront';
                        }

                        if ( !empty( $imp_foto_det ) ) {
                            $situacao = 'IMPRESSÃO DE FOTOS DE ' . SICOP_DET_DESC_U . 'S';
                            $pag_imp = 'foto_det';
                        }

                        if ( !empty( $imp_lista ) ) {
                            $situacao = 'IMPRESSÃO DE LISTA DE ' . SICOP_DET_DESC_U . 'S';
                            $pag_imp = 'lista_busca';
                        }

                        if ( !empty( $imp_recibo ) ) {
                            $situacao = 'IMPRESSÃO DE RECIBO DE ESCOLTA - CADASTRO';
                            $pag_imp = 'rec_escolta_cad';
                        }

                        if ( empty( $iddet ) ) {
                            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Variável SESSION vazia. ( $situacao ). \n\n Página: $pag";
                            salvaLog( $mensagem );
                            echo msg_js( 'FALHA!', 1 );
                            exit;
                        }

                        // monta a variavel para o comparador IN()
                        $v_iddet = '';
                        foreach ( $iddet as &$valor ) {
                            $valor = (int)$valor;
                            if ( empty( $valor ) ) continue;
                            $v_iddet .= (int)$valor . ',';
                        }

                        if ( empty( $v_iddet ) ) {
                            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Após validação, o array ficou vazio. ( $situacao ). \n\n Página: $pag";
                            salvaLog( $mensagem );
                            echo msg_js( 'FALHA!', 1 );
                            exit;
                        }

                        $v_iddet = substr($v_iddet, 0, -1);

                        if ( isset( $_SESSION['iddet'] ) ) unset( $_SESSION['iddet'] );

                        $_SESSION['iddet'] = $v_iddet;

                        $_SESSION['rec_cad_unidade'];
                        if ( !empty ( $_GET['unidade'] ) ) {
                            $_SESSION['rec_cad_unidade'] = $_GET['unidade'];
                        }

                        ?>
                        <script type="text/javascript">javascript: ow('print/<?php echo $pag_imp; ?>.php', '600', '600'); focus(); history.go(-1);</script>
                        <?php

                        $d_det_print = dados_det_wl( "IN( $v_iddet )" );

                        $mensagem = "[ $situacao ]\n";

                        if ( !empty( $imp_termo_ab ) ) {
                            $mensagem .= " Impressão de termos do protuário. \n \n [ " . SICOP_DET_DESC_U . "S ]\n $d_det_print ";
                        }

                        if ( !empty( $imp_foto_det ) ) {
                            $mensagem .= " Impressão de fotos de " . SICOP_DET_DESC_L . "s. \n \n [ FOTOS IMPRESSAS ]\n $d_det_print ";
                        }

                        if ( !empty( $imp_lista ) ) {
                            $mensagem .= " Impressão de lista de " . SICOP_DET_DESC_L . "s. \n \n [ " . SICOP_DET_DESC_U . "S ]\n $d_det_print ";
                        }

                        salvaLog( $mensagem );

                    } else if ( !empty( $exp_lista ) ) {

                        $iddet = get_session( 'det' );

                        if ( empty( $iddet ) ) {
                            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Variável SESSION vazia. ( EXPORTAÇÃO DE LISTA DE " . SICOP_DET_DESC_U . "S ). \n\n Página: $pag";
                            salvaLog( $mensagem );
                            echo msg_js( 'Falha!', 1 );
                            exit;
                        }

                        // monta a variavel para o comparador IN()
                        $v_iddet = '';
                        foreach ( $iddet as &$valor ) {
                            $valor = (int)$valor;
                            if ( empty( $valor ) ) continue;
                            $v_iddet .= (int)$valor . ',';
                        }

                        if ( empty( $v_iddet ) ) {
                            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Após validação, o array ficou vazio. ( EXPORTAÇÃO DE LISTA DE " . SICOP_DET_DESC_U . "S ). \n\n Página: $pag";
                            salvaLog( $mensagem );
                            echo msg_js( 'FALHA!', 1 );
                            exit;
                        }

                        $v_iddet = substr($v_iddet, 0, -1);

                        if ( isset( $_SESSION['iddet'] ) ) unset( $_SESSION['iddet'] );

                        $_SESSION['iddet'] = $v_iddet;
                        ?>
                        <script type="text/javascript">javascript: ow('export/exp_busca.php', '600', '600'); focus(); history.go(-1);</script>
                        <?php

                        $d_det_print = dados_det_wl( "IN( $v_iddet )" );

                        $mensagem = "[ EXPORTAÇÃO DE LISTA DE " . SICOP_DET_DESC_U . "S ]\n";
                        $mensagem .= " Exportação da lista de " . SICOP_DET_DESC_L . "s.\n\n [ " . SICOP_DET_DESC_U . "S ]\n $d_det_print ";
                        salvaLog( $mensagem );

                    } else if ( !empty( $limpar ) ) {

                        if ( isset( $_SESSION['det'] ) ) unset( $_SESSION['det'] );

                    }

                }

            }


            if ( !empty( $_SESSION['det'] ) ) {

                $id_in = implode(',' , $_SESSION['det']);

                $query = "SELECT
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
                            `detentos`.`iddetento` IN( $id_in )
                          ORDER BY
                            `detentos`.`nome_det`";

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

                ?>

            <p class="table_leg">Lista atual</p>

            <form action='<?php echo $_SERVER['PHP_SELF'];?>' method='get' name='form'>

                <table class="lista_busca">
                    <tr class="cab">
                        <th class="num_od">N</th>
                        <th class="nome_det"><?php echo SICOP_DET_DESC_FU; ?></th>
                        <th class="matr_det">Matrícula</th>
                        <th class="raio_det"><?php echo SICOP_RAIO ?></th>
                        <th class="cela_det"><?php echo SICOP_CELA ?></th>
                        <th class="tb_bt">&nbsp;</th>
                    </tr>
                        <?php
                        $i = 1;

                        while( $d_det = $query->fetch_assoc() ) {

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
                        <td class="tb_bt"><input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" name="submit" onClick="R_enviar('<?php echo $d_det['iddetento'] ;?>');" /></td>
                    </tr>
                            <?php } // fim do while ?>
                </table>

                <input type='hidden' name='proced' id='proced' value="2" />
                <input type='hidden' name='tipo_lista' id='tipo_lista' value='<?php echo $tipo_lista; ?>' />
                <input type='hidden' name='iddet' id='iddet' value="" />

            </form>

            <p class="bt_leg">COM OS ELEMENTOS DA LISTA</p>

            <form action='<?php echo $_SERVER['PHP_SELF'];?>' method='get' name='form'>

                <?php if ( $tipo_lista == 'recibo_cad' ) { ?>

                <p style="text-align: center;">
                    Unidade: <input name="unidade" type="text" class="CaixaTexto" id="unidade" onkeypress="return blockChars(event, 4);" value="" size="50" />
                </p>

                <?php } ?>

                <div class="form_bts">
                    <?php if ( $tipo_lista == 'foto_det' ) { ?>
                    <input class="form_bt" name="imp_foto_det" type="submit" value="Imprimir fotos" />
                    <?php } ?>
                    <?php if ( $tipo_lista == 'recibo_cad' ) { ?>
                    <input class="form_bt" name="imp_recibo" type="submit" value="Imprimir" />
                    <?php } ?>
                    <?php if ( $tipo_lista == 'termo_ab' ) { ?>
                    <input class="form_bt" name="imp_termo_ab" type="submit" value="Imprimir termos de abertura" />
                    <?php } ?>
                    <?php if ( $tipo_lista == 'export' ) { ?>
                    <input class="form_bt" name="imp_lista" type="submit" value="Imprimir" />
                    <input class="form_bt" name="exp_lista" type="submit" value="Exportar" />
                    <?php } ?>
                    <input class="form_bt" name="limpar" type="submit" value="Limpar lista" />
                </div>

                <input type='hidden' name='tipo_lista' id='tipo_lista' value='<?php echo $tipo_lista; ?>' />
                <input type='hidden' name='proced' id='proced' value="4" />

            </form>

            <?php }?>
            <!--</form>

            <script type="text/javascript">
                function enviarExc(id) {
                    saida.innerHTML = "<form action='lista.php' method='post' name='form'><input type='hidden' name='proced' id='proced' value=2><input type='hidden' name='iddet' id='iddet' value="+id+"></form>";
                    document.form.submit();
                }
            </script>

            <script type="text/javascript">
                function enviarAdd(id) {
                    saida.innerHTML = "<form action='lista.php' method='post' name='form'><input type='hidden' name='proced' id='proced' value=3><input type='hidden' name='iddet' id='iddet' value="+id+"></form>";
                    document.form.submit();
                }
            </script>

<?php include 'footer.php'; ?>