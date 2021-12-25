<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_rol   = get_session( 'n_rol', 'int' );
$imp_rol = get_session( 'imp_rol', 'int' );
$n_rol_n = 2;

if ( $n_rol < $n_rol_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'ROL DE VISITAS';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$iddet = get_get( 'iddet', 'int' );

if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página. Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. ( ROL DE VISITAS )";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$queryvis = "SELECT
                `visitas`.`idvisita`,
                `visitas`.`cod_detento`,
                `visitas`.`nome_visit`,
                `visitas`.`sexo_visit`,
                `visitas`.`nasc_visit`,
                DATE_FORMAT(`visitas`.`nasc_visit`, '%d/%m/%Y') AS nasc_visit_f,
                FLOOR(DateDiff(CurDate(), `visitas`.`nasc_visit`) / 365.25) AS idade_visit,
                `tipoparentesco`.`parentesco`
             FROM
                `visitas`
                LEFT JOIN `tipoparentesco` ON `visitas`.`cod_parentesco` = `tipoparentesco`.`idparentesco`
             WHERE
                `visitas`.`cod_detento` = $iddet
                AND
                `visitas`.`num_in` = (SELECT `n_p_trans` FROM `detentos` WHERE `iddetento` = $iddet LIMIT 1)
             ORDER BY
                `visitas`.`nome_visit` ASC";

$q_vis_old = "SELECT
                `visitas`.`idvisita`,
                `visitas`.`cod_detento`,
                `visitas`.`nome_visit`,
                `visitas`.`sexo_visit`,
                `visitas`.`nasc_visit`,
                DATE_FORMAT(`visitas`.`nasc_visit`, '%d/%m/%Y') AS nasc_visit_f,
                FLOOR(DateDiff(CurDate(), `visitas`.`nasc_visit`) / 365.25) AS idade_visit,
                `tipoparentesco`.`parentesco`
             FROM
                `visitas`
                LEFT JOIN `tipoparentesco` ON `visitas`.`cod_parentesco` = `tipoparentesco`.`idparentesco`
             WHERE
                `visitas`.`cod_detento` = $iddet
                AND
                `visitas`.`num_in` != (SELECT `n_p_trans` FROM `detentos` WHERE `iddetento` = $iddet LIMIT 1)
             ORDER BY
                `visitas`.`nome_visit` ASC";

$query_obs = "SELECT
                `id_obs_rol`,
                `cod_detento`,
                `obs_rol`,
                `user_add`,
                DATE_FORMAT(`data_add`, '%d/%m/%Y') AS data_add_f,
                DATE_FORMAT(`data_add`, '%d/%m/%Y às %H:%i') AS data_add_fc,
                `data_add`,
                `user_up`,
                DATE_FORMAT(`data_up`, '%d/%m/%Y às %H:%i') AS data_up_f,
                `data_up`
              FROM
                `obs_rol`
              WHERE
                `cod_detento` = $iddet
              ORDER BY
                `data_add` DESC
              LIMIT 10";

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Rol de visitas';

// adicionando o javascript
$cab_js = 'ajax/ajax_rol.js';
set_cab_js( $cab_js );

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 5 );
$trail->output();
?>

            <p class="descript_page">ROL DE VISITAS</p>

            <?php if ( $imp_rol >= 1 or $n_rol >= 3  ) {?>
            <p class="link_common">
                <?php if ( $imp_rol >= 1 ) {?>
                <a href="#" onClick="javascript: ow('../print/rol_visit.php?iddet=<?php echo $iddet;?>', '600', '400'); return false" title="Imprimir o rol de visitas d<?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L  ?>">Imprimir rol</a>
                <?php }?>
                <?php if ( $n_rol >= 3 ) {?>
                | <a href="alt_perm.php?iddet=<?php echo $iddet;?>" title="Alterar as permissões de acesso de visitantes e de recebimento de sedex d<?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L  ?>">Alterar permissões</a>
                <?php }?>
            </p>
            <?php }?>

            <?php include 'quali/det_full.php'; ?>

            <div class="linha">
                VISITAS CADASTRADAS (ativa - <font color="#FF0000">excluida</font> - <font color="#CC9900">suspensa</font>) <?php if ($n_rol >= 3 and !empty($d_det['matricula'])) {  ?> - <a href="cadastravisit.php?iddet=<?php echo $iddet ?>">Cadastrar visitante</a><?php }; ?>
                <hr />
            </div>
            <?php

                // instanciando o model
                $model = SicopModel::getInstance();

                // executando a query
                $queryvis = $model->query( $queryvis );

                // fechando a conexao
                $model->closeConnection();

                $contv = 0;

                if( $queryvis ) $contv = $queryvis->num_rows;

                if ( $contv < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                    echo '<p class="p_q_no_result">Não há visitas cadastradas.</p>';
                } else {
                ?>
            <table class="lista_busca">
                <tr >
                    <th class="visit_id">ID</th>
                    <th class="visit_nome">NOME DO VISITANTE</th>
                    <th class="visit_data_nasc">NASCIMENTO</th>
                    <th class="visit_parent">PARENTESCO</th>
                    <th class="visit_sexo">SEXO</th>
                    <th class="tb_bt">&nbsp;</th>
                    <th class="tb_bt">&nbsp;</th>
                </tr>
                <?php
                    while( $dadosv = $queryvis->fetch_assoc() ) {

                        $idvisita = $dadosv['idvisita'];

                        $visit = manipula_sit_visia( $idvisita );

                        $suspenso    = false;
                        $visit_class = 'visit_ativa';
                        $sit_v_atual = 'ATIVA';

                        if ( $visit ) {

                            $suspenso    = $visit['suspenso'];
                            $visit_class = $visit['css_class'];
                            $sit_v_atual = $visit['sit_v'];

                        }
                    ?>
                <tr class="even" title="Situação do visitante: <?php echo $sit_v_atual; ?>">
                    <td class="visit_id"><?php echo $dadosv['idvisita'] ?></td>
                    <td class="visit_nome"><a href="detalvisit.php?idvisit=<?php echo $dadosv['idvisita'] ?>" ><?php echo $dadosv['nome_visit'] ?></a></td>
                    <td class="visit_data_nasc <?php echo $visit_class; ?>"><?php echo $dadosv['nasc_visit_f'] ?><?php echo !is_null( $dadosv['idade_visit'] ) ? ' - ' . $dadosv['idade_visit'] . ' anos'  : ''; ?></td>
                    <td class="visit_parent <?php echo $visit_class; ?>"><?php echo $dadosv['parentesco'] ?></td>
                    <td class="visit_sexo <?php echo $visit_class; ?>"><?php echo $dadosv['sexo_visit'] ?></td>
                    <td class="tb_bt"><?php if ( $n_rol >= 3 ) {  ?><a href="editvisit.php?idvisit=<?php echo $dadosv['idvisita']; ?>" title="Alterar dados deste visitante" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="Alterar dados deste visitante" class="icon_button" /></a><?php }; ?></td>
                    <td class="tb_bt"><?php if ( $n_rol >= 4 ) {  ?><a href='javascript:void(0)' onclick='drop_visit(<?php echo $iddet; ?>, <?php echo $dadosv['idvisita']; ?>)' title="Excluir este visitante"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir este visitante" class="icon_button" /></a><?php }; ?></td>
                </tr>
                <?php } // fim do while ?>
            </table>
            <?php } // fim do if que conta o número de ocorrencias ?>


            <div id="obs"></div>
            <div class="linha">
                OBSERVAÇÕES<?php if ( $n_rol >= 3 ) {  ?> - <a href="cadobsrol.php?iddet=<?php echo $d_det['iddetento'] ?>" title="Adicionar uma observação para este rol">Adicionar observação</a> <a href="#" title="Abrir em outra janela" onClick="javascript: ow('cadobsrol.php?iddet=<?php echo $d_det['iddetento']; ?>&targ=1', '800', '450'); return false" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>user_popup.png" alt="Abrir em outra janela" class="icon_popup" /></a><?php }; ?>
                <hr />
            </div>
            <?php

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $query_obs = $model->query( $query_obs );

            // fechando a conexao
            $model->closeConnection();

            $cont_obs = 0;

            if( $query_obs ) $cont_obs = $query_obs->num_rows;

            if( $cont_obs < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não há observações.</p>';
            } else {
                ?>
            <table class="lista_busca">
                <tr >
                    <th class="desc_data">DATA</th>
                    <th class="desc_obs">OBSERVAÇÃO</th>
                    <th class="tb_bt">&nbsp;</th>
                    <th class="tb_bt">&nbsp;</th>
                </tr>
                <?php while ( $dados_obs = $query_obs->fetch_assoc() ) { ?>
                <tr class="even">
                    <td class="desc_data"><?php echo $dados_obs['data_add_f'] ?></td>
                    <td class="desc_obs"><?php echo nl2br($dados_obs['obs_rol']) ?></td>
                    <!--<td ><div align="justify"><pre class="paragrafo10"><?php //echo nl2br($dados_obs['obs_visit'] = preg_replace('/[\t]/','&#09;',$dados_obs['obs_visit'])) ?></pre></div></td>-->
                    <td class="tb_bt">
                    <?php if ($n_rol >= 3) {  ?>
                        <a href="editobsrol.php?idobs=<?php echo $dados_obs['id_obs_rol']; ?>" title="Alterar esta observação" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="" /></a>
                    <?php }; ?>
                    </td>
                    <td class="tb_bt">
                    <?php if ($n_rol >= 4) {  ?>
                        <a href='javascript:void(0)' onclick='drop( "id_obs_rol", "<?php echo $dados_obs['id_obs_rol']; ?>", "sendrolobs", "drop_obs_rol", "2")' title="Excluir esta observação"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir esta observação" class="icon_button" /></a>
                    <?php }; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="desc_user"><span class="paragrafo9">Cadastrado em <?php echo $dados_obs['data_add_fc'] ?>, usuário <?php echo $dados_obs['user_add'] ?><?php if ($dados_obs['user_up'] and $dados_obs['data_up_f']) {?> - Atualizado em <?php echo $dados_obs['data_up_f'] ?>, usuário <?php echo $dados_obs['user_up'] ?> <?php }?></span></td>
                </tr>
                <?php
                    }
                }
                ?>
            </table>

            <div class="linha">
                VISITAS DE OUTRAS PASSAGENS (ativa - <font color="#FF0000">excluida</font> - <font color="#CC9900">suspensa</font>)
                <hr />
            </div>
            <?php

                // instanciando o model
                $model = SicopModel::getInstance();

                // executando a query
                $q_vis_old = $model->query( $q_vis_old );

                // fechando a conexao
                $model->closeConnection();

                $cont_v_old = 0;

                if( $q_vis_old ) $cont_v_old = $q_vis_old->num_rows;

                if ( $cont_v_old < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                    echo '<p class="p_q_no_result">Não há visitas de passagens anteriores.</p>';
                } else {
                    ?>

            <table class="lista_busca">
                <tr >
                    <th class="visit_id">ID</th>
                    <th class="visit_nome">NOME DO VISITANTE</th>
                    <th class="visit_data_nasc">NASCIMENTO</th>
                    <th class="visit_parent">PARENTESCO</th>
                    <th class="visit_sexo">SEXO</th>
                    <th class="tb_bt">&nbsp;</th>
                    <th class="tb_bt">&nbsp;</th>
                </tr>
                <?php
                    while ( $d_v_old = $q_vis_old->fetch_assoc() ) {

                        $idvisita = $d_v_old['idvisita'];

                        $visit = manipula_sit_visia( $idvisita );

                        $suspenso    = false;
                        $visit_class = 'visit_ativa';
                        $sit_v_old   = 'ATIVA';

                        if ( $visit ) {

                            $suspenso    = $visit['suspenso'];
                            $visit_class = $visit['css_class'];
                            $sit_v_old   = $visit['sit_v'];

                        }
                    ?>
                <tr class="even" title="Situação do visitante: <?php echo $sit_v_old; ?> (inativo no rol)">
                    <td class="visit_id"><?php echo $d_v_old['idvisita'] ?></td>
                    <td class="visit_nome"><a href="detalvisit.php?idvisit=<?php echo $d_v_old['idvisita'] ?>"><?php echo $d_v_old['nome_visit'] ?></a></td>
                    <td class="visit_data_nasc <?php echo $visit_class; ?>"><?php echo $d_v_old['nasc_visit_f'] ?><?php echo !is_null( $d_v_old['idade_visit'] ) ? ' - ' . $d_v_old['idade_visit'] . ' anos'  : ''; ?></td>
                    <td class="visit_parent <?php echo $visit_class; ?>"><?php echo $d_v_old['parentesco'] ?></td>
                    <td class="visit_sexo <?php echo $visit_class; ?>"><?php echo $d_v_old['sexo_visit'] ?></td>
                    <td class="tb_bt"><?php if ( $n_rol >= 3 ) { ?><a href='javascript:void(0)' onclick='reat_visit(<?php echo $iddet; ?>, <?php echo $d_v_old['idvisita']; ?>)' title="Reativar este visitante"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>refresh.png" alt="Reativar este visitante" class="icon_button" /></a><?php }; ?></td>
                    <td class="tb_bt"><?php if ( $n_rol >= 4 ) { ?><a href='javascript:void(0)' onclick='drop_visit(<?php echo $iddet; ?>, <?php echo $d_v_old['idvisita']; ?>)' title="Excluir este visitante"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir este visitante" class="icon_button" /></a><?php }; ?></td>
                </tr>
                <?php } // fim do while ?>
            </table>
            <?php } // fim do if que conta o número de ocorrencias ?>

<?php include 'footer.php'; ?>