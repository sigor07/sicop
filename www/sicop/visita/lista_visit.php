<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_rol   = get_session( 'n_rol', 'int' );
$imp_rol = get_session( 'imp_rol', 'int' );
$n_rol_n = 2;

if ( $n_rol < $n_rol_n or $imp_rol < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'IMPRESSÃO DE CARTEIRINHAS - ROL DE VISITAS';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$field = '';

if ( !empty( $_GET['campobusca'] ) ) {
    $field = $_GET['campobusca'];
}

$desc_pag = 'Gerar lista de visitantes';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if (!empty($qs)) {
    $pag_atual .=  '?' . $qs;
}

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();

?>

            <script type="text/JavaScript">
                function R_enviar(x) {
                    element = id( 'idvisit' );
                    element.value = x
                }
            </script>

            <p class="descript_page">GERAR LISTA DE VISITANTES</p>

            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="busca_v" id="busca_v" onSubmit="upperMe(campobusca);" >

                <p class="table_leg">Digite o NOME ou o IDENTIFICADOR do vistante:</p>

                <div class="form_one_field">
                    <input name="campobusca" type="text" class="CaixaTexto" id="campobusca" onkeypress="return blockChars(event, 4);" value="<?php echo $field ?>" size="50" />
                </div>

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="busca" id="busca" value="Buscar" />
                </div>

                <input type='hidden' name='proced' id='proced' value=1 />

            </form>

            <script type="text/javascript">id("campobusca").focus(); id("campobusca").select();</script>


<?php

//if($_SERVER['REQUEST_METHOD'] == 'POST') {

$proced = !empty( $_GET['proced'] ) ? (int)$_GET['proced'] : '';

// se estiver setado o procedimento...
if ( !empty( $proced ) ) {

    if ( isset( $proced ) and $proced == '1' ) {

        $valorbusca = '';
        if ( isset( $_GET['campobusca'] ) ) {
            $valorbusca = tratabusca( $_GET['campobusca'] );
        }

        if ( !empty( $valorbusca ) ) {

            $query_visit = "SELECT
                              `idvisita`
                            FROM
                              `visitas`
                            WHERE
                              `nome_visit` LIKE '%$valorbusca%' OR `idvisita` = '$valorbusca'";

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $query_visit = $model->query( $query_visit );

            // fechando a conexao
            $model->closeConnection();

            if( !$query_visit ) {

                echo msg_js( 'FALHA!!!', 1 );
                exit;

            }

            $cont = $query_visit->num_rows;

            // se retornar mais de 1 resultado, gera a tabela abaixo para escolher quem vai ser adicionado.
            if( $cont > 1 ) {
                $idvisit_array = array();
                while( $dados_visit = $query_visit->fetch_assoc() ) {
                    $idvisit_array[] = $dados_visit['idvisita'];
                }

                $idvisit_in = implode( ',' , $idvisit_array );

                $q_s_visit = "SELECT
                                `visitas`.`idvisita`,
                                `visitas`.`nome_visit`,
                                `visitas`.`rg_visit`,
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
                                `visitas`
                                INNER JOIN `detentos` ON `visitas`.`cod_detento` = `detentos`.`iddetento`
                                LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                                LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                                LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                                LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                                LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
                              WHERE
                                `visitas`.`idvisita` IN( $idvisit_in )
                              ORDER BY
                                `visitas`.`nome_visit`";

                // instanciando o model
                $model = SicopModel::getInstance();

                // executando a query
                $q_s_visit = $model->query( $q_s_visit );

                // fechando a conexao
                $model->closeConnection();

                if( !$q_s_visit ) {

                    echo msg_js( 'FALHA!!!', 1 );
                    exit;

                }

                ?>

            <p class="table_leg">Resultado da busca</p>

            <form action='<?php echo $_SERVER['PHP_SELF'];?>' method='get' name='form'>
                <table class="lista_busca">
                    <tr>
                        <th class="num_od">N</th>
                        <th class="visit_nome_busca">Visitante</th>
                        <th class="visit_rg">R.G.</th>
                        <th class="nome_det_small">Detento</th>
                        <th class="matr_det">Matrícula </th>
                        <th class="raio_det"><?php echo SICOP_RAIO ?></th>
                        <th class="cela_det"><?php echo SICOP_CELA ?></th>
                        <th class="tb_bt">&nbsp;</th>
                    </tr>
                        <?php
                        $i = 1;

                        while( $dados = $q_s_visit->fetch_assoc() ) {

                            $tipo_mov_in  = $dados['tipo_mov_in'];
                            $tipo_mov_out = $dados['tipo_mov_out'];
                            $iddestino    = $dados['iddestino'];

                            $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );

                            ?>
                    <tr class="even">
                        <td class="num_od"><?php echo $i++; ?></td>
                        <td class="visit_nome_busca"><a href="<?php echo SICOP_ABS_PATH ?>visita/detalvisit.php?idvisit=<?php echo $dados['idvisita'];?>"><?php echo highlight($valorbusca, $dados['nome_visit']);?></a></td>
                        <td class="visit_rg"><?php echo $dados['rg_visit'];?></td>
                        <td class="nome_det_small"><a href="<?php echo SICOP_ABS_PATH ?>detento/detalhesdet.php?iddet=<?php echo $dados['iddetento'] /*alphaID($dados['iddetento'])*/;?>" title="Pai: <?php echo $dados['pai_det'];?>&#13;Mãe: <?php echo $dados['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat']; ?>"> <?php echo $dados['nome_det'];?></a></td>
                        <td class="matr_det <?php echo $det['css_class']; ?>"><?php if ( !empty( $dados['matricula'] ) ) echo formata_num( $dados['matricula'] );?></td>
                        <td class="raio_det <?php echo $det['css_class']; ?>"><?php echo $dados['raio'];?></td>
                        <td class="cela_det <?php echo $det['css_class']; ?>"><?php echo $dados['cela'];?></td>
                        <td class="tb_bt"><input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>s_add.png" name="submit" onClick="R_enviar('<?php echo $dados['idvisita'] ;?>');" /></td>
                    </tr>
                    <?php } // fim do while ?>
                </table>
                <input type='hidden' name='proced' id='proced' value=3 />
                <input type='hidden' name='idvisit' id='idvisit' value="" />
            </form>
                <?php

                // se retronar apenas 1, ja adiciona ele direto na lista
            } else if ( $cont == 1) {

                $dados_visit = $query_visit->fetch_assoc();
                $idvisit = $dados_visit['idvisita'];

                // se não retornar niguem, mostra a msg
            } else if ( $cont < 1) {

                echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';

            }

        }

        $visit =  !empty( $_SESSION['visit'] ) ? $_SESSION['visit'] : array();

        // este $idvisit é o que está no if de retornar apenas 1 resultado
        if ( !empty( $idvisit ) ) { //se não estiver vazio...
            if ( !in_array( $idvisit, $visit ) ) { //se não estiver no array
                $visit[] = $idvisit;
            }
        }

        $_SESSION['visit'] = $visit;

    } else if ( isset( $proced ) and $proced == 2 ) { //excluir elemento do array

        $idvisit = (int)$_GET['idvisit'];
        $visit = $_SESSION['visit'];

        $key = NULL;

        // se estiver no array, ele vai pegar o indice do elemento
        if ( in_array( $idvisit, $visit ) ) {
            $key = array_search( $idvisit, $visit );
        }

        // se o indice não for null, vai apargar o elemento pelo indice
        if ( $key !== NULL ) {
            unset( $visit[$key] );
        }

        $_SESSION['visit'] = $visit;

    } else if (isset($proced) and $proced == '3') { // incluir elemento no array

        $idvisit = empty( $_GET['idvisit'] ) ? '' : (int)$_GET['idvisit'];
        $visit =  !empty( $_SESSION['visit'] ) ? $_SESSION['visit'] : array();

        if ( !empty( $idvisit ) ) {
            if ( !in_array( $idvisit, $visit ) ) {
                $visit[] = $idvisit;
            }
        }

        $_SESSION['visit'] = $visit;

    } else if (isset($proced) and $proced == '4') { // imprimir

        $imp = empty( $_GET['imp'] ) ? '' : $_GET['imp'];
        $limpar = empty( $_GET['limpar'] ) ? '' : $_GET['limpar'];

        if ( !empty( $imp ) ) {

            $idvisit = empty( $_SESSION['visit'] ) ? '' : $_SESSION['visit'];

            if ( empty( $idvisit ) ) {
                $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Variável SESSION vazia. (IMPRESSÃO DE CARTEIRINHA DE VISITANTE).\n\n Página: $pag";
                salvaLog( $mensagem );
                echo msg_js( 'Falha ao imprimir!', 1 );
                exit;
            }

            // monta a variavel para o comparador IN()
            $v_idvisit = '';
            foreach ( $idvisit as $indice => $valor ) {
                $valor = (int)$valor;
                if ( empty( $valor ) ) continue;
                $v_idvisit .= (int)$valor . ',';
            }

            if ( empty( $v_idvisit ) ) {
                $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Após validação, o array ficou vazio. (IMPRESSÃO DE CARTEIRINHA DE VISITANTE).\n\n Página: $pag";
                salvaLog( $mensagem );
                echo msg_js( 'Falha ao imprimir!', 1 );
                exit;
            }

            $v_idvisit = substr($v_idvisit, 0, -1);

            if ( isset( $_SESSION['idvisit'] ) ) unset( $_SESSION['idvisit'] );

            $_SESSION['idvisit'] = $v_idvisit;

            ?>
            <script type="text/javascript">javascript: ow('../print/cartao_visit.php', '600', '600'); focus(); history.go(-1);</script>
            <?php

            $d_visit_print = dados_visit_wl( " IN( $v_idvisit ) " );

            $mensagem = "[ IMPRESSÃO DE CARTÃO DE VISITANTE ]\n Impressão de cartão de identificação de visitante.\n\n $d_visit_print ";
            salvaLog($mensagem);

        } else if ( !empty( $limpar ) ) {

            if ( isset( $_SESSION['visit'] ) ) unset( $_SESSION['visit'] );

        }

    }

}

if ( !empty( $_SESSION['visit'] ) ) {

    $id_in = implode(',' , $_SESSION['visit']);

    $query = "SELECT
                `visitas`.`idvisita`,
                `visitas`.`nome_visit`,
                `visitas`.`rg_visit`,
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
                `visitas`
                INNER JOIN `detentos` ON `visitas`.`cod_detento` = `detentos`.`iddetento`
                LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
              WHERE
                `visitas`.`idvisita` IN( $id_in )
              ORDER BY
                `visitas`.`nome_visit`";

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
                    <tr>
                        <th class="num_od">N</th>
                        <th class="visit_id">ID</th>
                        <th class="visit_nome_busca">Visitante</th>
                        <th class="visit_rg">R.G.</th>
                        <th class="nome_det_small"><?php echo SICOP_DET_DESC_FU; ?></th>
                        <th class="matr_det">Matrícula </th>
                        <th class="raio_det"><?php echo SICOP_RAIO ?></th>
                        <th class="cela_det"><?php echo SICOP_CELA ?></th>
                        <th class="tb_bt">&nbsp;</th>
                    </tr>
                        <?php
                        $i = 1;

                        while( $dados = $query->fetch_assoc() ) {

                            $tipo_mov_in  = $dados['tipo_mov_in'];
                            $tipo_mov_out = $dados['tipo_mov_out'];
                            $iddestino    = $dados['iddestino'];

                            $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );

                            ?>
                    <tr class="even">
                        <td class="num_od"><?php echo $i++; ?></td>
                        <td class="visit_id"><?php echo $dados['idvisita']; ?></td>
                        <td class="visit_nome_busca"><a href="<?php echo SICOP_ABS_PATH ?>visita/detalvisit.php?idvisit=<?php echo $dados['idvisita'];?>"><?php echo $dados['nome_visit'];?></a></td>
                        <td class="visit_rg"><?php echo $dados['rg_visit'];?></td>
                        <td class="nome_det_small"><a href="<?php echo SICOP_ABS_PATH ?>detento/detalhesdet.php?iddet=<?php echo $dados['iddetento'] /*alphaID($dados['iddetento'])*/;?>" title="Pai: <?php echo $dados['pai_det'];?>&#13;Mãe: <?php echo $dados['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat']; ?>"> <?php echo $dados['nome_det'];?></a></td>
                        <td class="matr_det <?php echo $det['css_class']; ?>"><?php if ( !empty( $dados['matricula'] ) ) echo formata_num( $dados['matricula'] );?></td>
                        <td class="raio_det <?php echo $det['css_class']; ?>"><?php echo $dados['raio'];?></td>
                        <td class="cela_det <?php echo $det['css_class']; ?>"><?php echo $dados['cela'];?></td>
                        <td class="tb_bt"><input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" name="submit" onClick="R_enviar('<?php echo $dados['idvisita'] ;?>');" /></td>
                    </tr>
                    <?php } // fim do while ?>
                </table>

                <input type='hidden' name='proced' id='proced' value=2 />
                <input type='hidden' name='idvisit' id='idvisit' value="<?php //echo $id_in ?>" />

            </form>

            <p class="bt_leg">COM OS ELEMENTOS DA LISTA</p>

            <form action='<?php echo $_SERVER['PHP_SELF'];?>' method='get' name='form'>

                <div class="form_bts">
                    <input class="form_bt" name="imp" type="submit" value="Imprimir carteirinha" />
                    <input class="form_bt" name="limpar" type="submit" value="Limpar lista" />
                </div>

                <input type='hidden' name='proced' id='proced' value=4 />

            </form>

            <?php }?>

<?php include 'footer.php';?>