<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_incl   = get_session( 'n_incl', 'int' );
$n_incl_n = 3;

$motivo_pag = 'VINCULAR TV';

if ( $n_incl < $n_incl_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idtv = get_post( 'idtv', 'int' );

if ( empty( $idtv ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( IDENTIFICADOR EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$q_tv = "SELECT
            `detentos_tv`.`idtv`,
            `detentos_tv`.`cod_detento`,
            `detentos_tv`.`cod_cela`,
            `detentos_tv`.`marca_tv`,
            `detentos_tv`.`cor_tv`,
            `detentos_tv`.`polegadas`,
            `detentos_tv`.`lacre_1`,
            `detentos_tv`.`lacre_2`,
            `cela`.`cela`,
            `raio`.`raio`
          FROM
            `detentos_tv`
            LEFT JOIN `cela` ON `detentos_tv`.`cod_cela` = `cela`.`idcela`
            LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
          WHERE
            `detentos_tv`.`idtv` = $idtv
          LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_tv = $model->query( $q_tv );

// fechando a conexao
$model->closeConnection();

if( !$q_tv ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_tv = $q_tv->num_rows;

if($cont_tv < 1) {
    $mensagem = "A consulta retornou 0 ocorrencias (TV).\n\n Página $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$d_tv = $q_tv->fetch_assoc();

$iddet_old = $d_tv['cod_detento'];

if ( isset( $_POST['matricula'] ) ) {
    $matr = tratabusca($_POST['matricula']);
    $matr = (int)$matr;
}

$iddet_new = '';
if ( !empty( $matr ) ) {

    $query_det = "SELECT `iddetento` FROM `detentos` WHERE `matricula` = $matr LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    (int)$iddet_new = $model->fetchOne( $query_det );

    // fechando a conexao
    $model->closeConnection();

}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Alterar TV';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if (!empty($qs)) {
    $pag_atual .=  '?' . $qs;
}

require 'cab.php';
$trail = new Breadcrumb();
$trail->add('Alterar TV', $pag_atual, 4);
$trail->output();

?>

            <p class="descript_page">ALTERAR <?php echo SICOP_DET_DESC_U; ?> RESPONSÁVEL PELA TV</p>

            <p class="table_leg">TV</p>

            <table class="lista_busca">
                <tr bgcolor="#FAFAFA">
                    <td width="179" height="20" align="center">Marca: <a href="detaltv.php?idtv=<?php echo $d_tv['idtv'];?>"><?php echo $d_tv['marca_tv'] ?></a></td>
                    <td width="180" align="center">Cor: <?php echo $d_tv['cor_tv'] ?></td>
                    <td width="179" align="center"><?php echo SICOP_RAIO ?>: <?php echo $d_tv['raio'] ?> - <?php echo SICOP_CELA ?>: <?php echo $d_tv['cela'] ?></td>
                    <td width="181" align="center">Lacres: <?php echo $d_tv['lacre_1'] ?> / <?php echo $d_tv['lacre_2'] ?></td>
                </tr>
            </table>

            <p class="table_leg">ATUAL RESPONSÁVEL</p>

            <?php
            $iddet = $iddet_old;
            if ( empty( $iddet ) ) {
                echo '<p class="p_q_no_result">Não há ' . SICOP_DET_DESC_L . ' responsável.</p>';
            } else {
                include 'quali/det_basic.php';
            }
            ?>

            <?php if ( empty( $matr ) ) { ?>

            <p class="descript_page">PESQUISAR <?php echo SICOP_DET_DESC_U; ?></p>

            <form action="vinculatv.php" method="post" name="vinculatv" id="vinculatv" onSubmit="return validavinculatv();">


                <p class="table_leg">Digite a MATRÍCULA d<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?> com o DÍGITO:</p>

                <div class="form_one_field">
                    <input name="matricula" type="text" class="CaixaTexto" id="matricula" onkeypress="return blockChars(event, 5);" size="11" maxlength="9" />
                </div>

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="busca" value="Buscar" />
                    <input class="form_bt" name="" type="button" onclick="javascript: location.href='detaltv.php?idtv=<?php echo $idtv ?>'" value="Cancelar" />
                </div>

                <p class="link_common" style="margin-top: 5px;"><a href="#" onClick="javascript: ow('<?php echo SICOP_ABS_PATH; ?>buscadetm.php', '800', '600'); return false" >Não lembro a matrícula/digito</a></p>

                <input name="idtv" type="hidden" id="idtv" value="<?php echo $idtv;?>" />

            </form>

            <script type="text/javascript">
                document.getElementById("matricula").focus();
            </script>
            <?php

            } else {

                if ( empty( $iddet_new ) or $iddet_new < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
            ?>

            <p class="p_q_no_result">Não foi encontrado.</p>

            <p class="link_common"><a href='javascript:void(0)' onclick='envia_tv(<?php echo $idtv;?>)'>Nova consulta</a></p>

            <?php } else { ?>

            <p class="table_leg">NOVO RESPONSÁVEL</p>

            <?php
            $iddet = $iddet_new;
            include 'quali/det_basic.php';
            ?>

            <?php

            $pode_vincular = true;
            $cela_possui = false;
            $detento_possui = false;

            if ( $iddet_new == $iddet_old ) {
                $pode_vincular = false;
                ?>
                <p class="table_leg"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_attention.png" alt="Atenção" class="icon_alert" />&nbsp;ATENÇÃO -> O novo e o atual responsável são a mesma pessoa.</p>
                <?php
            } else {

                $q_matr = "SELECT
                             `cod_cela`,
                             `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
                             `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
                             `unidades_out`.`idunidades` AS iddestino
                           FROM
                             `detentos`
                             LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                             LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                             LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                           WHERE
                             `iddetento` = $iddet_new";

                // instanciando o model
                $model = SicopModel::getInstance();

                // executando a query
                $q_matr = $model->query( $q_matr );

                // fechando a conexao
                $model->closeConnection();

                $d_matr = $q_matr->fetch_assoc();

                $idcela       = $d_matr['cod_cela'];
                $tipo_mov_in  = $d_matr['tipo_mov_in'];
                $tipo_mov_out = $d_matr['tipo_mov_out'];
                $iddestino    = $d_matr['iddestino'];
                $sit_det      = manipula_sit_det_id( $tipo_mov_in, $tipo_mov_out, $iddestino );

                if ( empty( $idcela ) ) {
                    $pode_vincular = false;
                    ?>
                    <p class="table_leg"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_attention.png" alt="Atenção" class="icon_alert" />&nbsp;ATENÇÃO -> Você não pode cadastrar TV para <?php echo SICOP_DET_DESC_L; ?>s que ainda não possuem cela..</p>
                    <?php
                }

                if ( $pode_vincular ) {

                    // verifica a situação do detento
                    if ( $sit_det == SICOP_SIT_DET_TRANSF ||   // TRANSFERIDO
                         $sit_det == SICOP_SIT_DET_EXCLUIDO ||   // EXCLUIDO (ALVARA)
                         $sit_det == SICOP_SIT_DET_EVADIDO ||   // EVADIDO
                         $sit_det == SICOP_SIT_DET_FALECIDO ||   // FALECIDO
                         $sit_det == SICOP_SIT_DET_ACEHGAR ) {   // A CHEGAR

                        $pode_vincular = false;
                        ?>
                        <p class="table_leg"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_attention.png" alt="Atenção" class="icon_alert" />&nbsp;ATENÇÃO -> <?php echo SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_L; ?> não está na unidade.</p>
                        <?php

                    }

                }

                if ( $pode_vincular ) {
                    $q_v_tv = "SELECT
                                  `detentos_tv`.`idtv`,
                                  `detentos_tv`.`marca_tv`,
                                  `detentos_tv`.`cor_tv`,
                                  `detentos_tv`.`lacre_1`,
                                  `detentos_tv`.`lacre_2`,
                                  `detentos`.`iddetento`,
                                  `detentos`.`nome_det`,
                                  `detentos`.`matricula`,
                                  `tb_cela_det`.`cela` AS cela_det,
                                  `tb_raio_det`.`raio` AS raio_det,
                                  `tb_cela_tv`.`cela` AS cela_tv,
                                  `tb_raio_tv`.`raio` AS raio_tv
                                FROM
                                  `detentos_tv`
                                  LEFT JOIN `detentos` ON `detentos_tv`.`cod_detento` = `detentos`.`iddetento`
                                  LEFT JOIN `cela` `tb_cela_det` ON `detentos`.`cod_cela` = `tb_cela_det`.`idcela`
                                  LEFT JOIN `raio` `tb_raio_det` ON `tb_cela_det`.`cod_raio` = `tb_raio_det`.`idraio`
                                  LEFT JOIN `cela` `tb_cela_tv` ON `detentos_tv`.`cod_cela` = `tb_cela_tv`.`idcela`
                                  LEFT JOIN `raio` `tb_raio_tv` ON `tb_cela_tv`.`cod_raio` = `tb_raio_tv`.`idraio`
                                WHERE
                                  `detentos_tv`.`cod_cela` = $idcela";

                    // instanciando o model
                    $model = SicopModel::getInstance();

                    // executando a query
                    $q_v_tv = $model->query( $q_v_tv );

                    // fechando a conexao
                    $model->closeConnection();

                    $cont_ctv = $q_v_tv->num_rows;

                    if ( $cont_ctv >= 1 ) {

                        $cela_possui = true;
                        $pode_vincular = false;

                        $d_v_tv = $q_v_tv->fetch_assoc();

                        $idtv_v_tv = $d_v_tv['idtv'];

                        // se for a mesma tv
                        if ( $idtv == $idtv_v_tv ) {
                            $cela_possui = false;
                            $pode_vincular = true;
                        }

                    }

                    $q_v_det = "SELECT
                                      `detentos_tv`.`idtv`,
                                      `detentos_tv`.`marca_tv`,
                                      `detentos_tv`.`cor_tv`,
                                      `detentos_tv`.`polegadas`,
                                      `detentos_tv`.`lacre_1`,
                                      `detentos_tv`.`lacre_2`,
                                      `cela`.`cela`,
                                      `raio`.`raio`
                                    FROM
                                     `detentos_tv`
                                      LEFT JOIN `cela` ON `detentos_tv`.`cod_cela` = `cela`.`idcela`
                                      LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
                                    WHERE
                                     `cod_detento` = $iddet_new";

                    // instanciando o model
                    $model = SicopModel::getInstance();

                    // executando a query
                    $q_v_det = $model->query( $q_v_det );

                    // fechando a conexao
                    $model->closeConnection();

                    $cont_dtv = $q_v_det->num_rows;
                    if ( $cont_dtv >= 1 ) {
                        $detento_possui = true;
                        $pode_vincular = false;
                    }
                }
            }

            if ( !$pode_vincular ) {
                if ( $detento_possui ) {

                    ?>

            <p class="table_leg"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_attention.png" alt="Atenção" class="icon_alert" />&nbsp;ATENÇÃO -> <?php echo SICOP_DET_PRON_FU . ' ' . SICOP_DET_DESC_L; ?> já possui TV cadastrada.</p>

            <table class="lista_busca">
                <tr align="center">
                    <td width="80" >MARCA</td>
                    <td width="80">COR </td>
                    <td width="130"><?php echo mb_strtoupper( SICOP_RAIO ) ?> / <?php echo mb_strtoupper( SICOP_CELA ) ?> DA TV</td>
                    <td width="100">LACRES</td>
                </tr>
                        <?php while( $d_v_det = $q_v_det->fetch_assoc() ) { ?>
                <tr bgcolor="#FAFAFA" height="20">
                    <td><a href="detaltv.php?idtv=<?php echo $d_v_det['idtv'];?>"><?php echo $d_v_det['marca_tv'] ?></a></td>
                    <td><?php echo $d_v_det['cor_tv'] ?></td>
                    <td align="center"><?php echo $d_v_det['raio'] ?> - <?php echo $d_v_det['cela'] ?></td>
                    <td align="center"><?php echo $d_v_det['lacre_1'] ?> / <?php echo $d_v_det['lacre_2'] ?></td>
                </tr>
                        <?php } ?>
            </table>

                    <?php }?>

                <?php if ( $cela_possui ) { ?>

            <p class="table_leg"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_attention.png" alt="Atenção" class="icon_alert" />&nbsp;ATENÇÃO -> A cela que <?php echo SICOP_DET_PRON_FU . ' ' . SICOP_DET_DESC_L; ?> está já possui TV cadastrada.</p>

            <table width="800" align="center" class="space">
                <tr align="center">
                    <td width="80" >MARCA</td>
                    <td width="80">COR </td>
                    <td width="100"><?php echo mb_strtoupper( SICOP_RAIO ) ?> / <?php echo mb_strtoupper( SICOP_CELA ) ?> DA TV</td>
                    <td width="90">LACRES</td>
                    <td width="219"><?php echo SICOP_DET_DESC_U; ?></td>
                    <td width="91">MATRICULA</td>
                    <td width="100"><?php echo mb_strtoupper( SICOP_RAIO ) ?> / <?php echo mb_strtoupper( SICOP_CELA ) ?> D<?php echo SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U; ?></td>
                </tr>
                <tr bgcolor="#FAFAFA" height="20">
                    <td><a href="detaltv.php?idtv=<?php echo $d_v_tv['idtv'];?>"><?php echo $d_v_tv['marca_tv'] ?></a></td>
                    <td><?php echo $d_v_tv['cor_tv'] ?></td>
                    <td align="center"><?php echo $d_v_tv['raio_tv'] ?> - <?php echo $d_v_tv['cela_tv'] ?></td>
                    <td align="center"><?php echo $d_v_tv['lacre_1'] ?> / <?php echo $d_v_tv['lacre_2'] ?></td>
                    <td><a href="<?php echo SICOP_ABS_PATH; ?>detento/detalhesdet.php?iddet=<?php echo $iddet;?>" title="Clique aqui para abrir a qualificativa d<?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>"><?php echo $d_v_tv['nome_det'] ?></a></td>
                    <td align="center"><?php if ( !empty( $d_v_tv['matricula'] ) ) echo formata_num( $d_v_tv['matricula'] ) ?></td>
                    <td align="center"><?php echo $d_v_tv['raio_det'] ?> - <?php echo $d_v_tv['cela_det'] ?></td>
                </tr>
            </table>

                    <?php }?>

            <?php if ( $cela_possui or $detento_possui or !$pode_vincular ) { ?>
            <p class="link_common"><a href='javascript:void(0)' onclick='envia_tv(<?php echo $idtv;?>)'>Nova consulta</a></p>
            <?php }?>

            <?php } else { ?>


            <p class="confirm_ask">Tem certeza de que deseja vincular esta TV a <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>?</p>

            <form action="<?php echo SICOP_ABS_PATH; ?>send/sendtv.php" method="post" name="vinculatv" id="vinculatv">

                <input name="idtv" type="hidden" id="idtv" value="<?php echo $idtv;?>" />
                <input name="iddet_old" type="hidden" id="iddet_old" value="<?php echo $iddet_old;?>" />
                <input name="iddet_new" type="hidden" id="iddet_new" value="<?php echo $iddet_new;?>" />
                <input name="proced" type="hidden" id="proced" value="4" />

                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" value="Vincular" />
                    <input class="form_bt" name="" type="button" onclick="javascript: location.href='detaltv.php?idtv=<?php echo $idtv ?>'" value="Cancelar" />
                </div>

            </form>
                    <?php
                }
            }
        }
    ?>

<?php include 'footer.php'; ?>