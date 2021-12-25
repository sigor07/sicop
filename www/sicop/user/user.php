<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';
$motivo_pag = 'DETALHES DO USUÁRIO';

$n_admsist = get_session( 'n_admsist', 'int' );
$n_admsist_n = 2;

if ( $n_admsist < $n_admsist_n ) {

    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'DETALHES DO USUÁRIO';
    get_msg( $msg, 1 );

    exit;

}

$iduser = get_get( 'iduser', 'int' );

if ( empty( $iduser ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( IDENTIFICADOR DO USUÁRIO EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}



$query_user = "SELECT
                  `sicop_users`.`iduser`,
                  `sicop_users`.`nomeuser`,
                  `sicop_users`.`nome_cham`,
                  `sicop_users`.`usuario`,
                  `sicop_users`.`senha`,
                  `sicop_users`.`email`,
                  `sicop_users`.`cargo`,
                  `sicop_setor`.`setor`,
                  `sicop_users`.`iniciais`,
                  `sicop_users`.`rsuser`,
                  `sicop_users`.`ativo`,
                  `sicop_users`.`numlogins`,
                  DATE_FORMAT( `sicop_users`.`datalastlogin`, '%d/%m/%Y às %H:%i' ) AS data_ll,
                  DATE_FORMAT( `sicop_users`.`prelastlogin`, '%d/%m/%Y às %H:%i' ) AS prelastlogin,
                  `sicop_users`.`user_add`,
                  DATE_FORMAT( `sicop_users`.`data_add`, '%d/%m/%Y às %H:%i' ) AS data_add,
                  `sicop_users`.`user_up`,
                  DATE_FORMAT( `sicop_users`.`data_up`, '%d/%m/%Y às %H:%i' ) AS data_up
                FROM
                  `sicop_users`
                  INNER JOIN `sicop_setor` ON `sicop_users`.`cod_setor` = `sicop_setor`.`idsetor`
                WHERE
                  `sicop_users`.`iduser` = $iduser";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_user = $model->query( $query_user );

// fechando a conexao
$model->closeConnection();

if ( !$query_user ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Falha na consulta ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$cont = $query_user->num_rows;
if ( $cont < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "A consulta retornou 0 ocorrências ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$d_user = $query_user->fetch_assoc();

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Detalhes do usuário';

// adicionando o javascript
$cab_js = 'ajax/ajax_user.js';
set_cab_js( $cab_js );

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();

?>

            <input type="hidden" name="iduser" id="iduser" value="<?php echo $iduser; ?>" />

            <p class="descript_page">DETALHES DO USUÁRIO</p>

            <p class="table_leg">Dados do usuário:</p>

            <p class="link_common">
                <a href="javascript: history.go(-1)">Voltar</a>
                <?php if ( $n_admsist >= 3 ) { ?> | <a href="edituser.php?iduser=<?php echo $d_user['iduser']; ?>" title="Alterar dados deste usuário" >Alterar dados</a><?php }; ?>
                <?php if ( $n_admsist >= 4 and $d_user['iduser'] != 1 ) { ?> | <a href='javascript:void(0)' id="link_drop_user" title="Excluir este usuário">Excluir este usuário</a><?php }; ?>
            </p>

            <table class="detal_user">
                <tr class="even">
                    <td class="detal_user_leg">Nome do usuário:</td>
                    <td class="detal_user_field"><?php echo $d_user['nomeuser'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">Nome de acesso:</td>
                    <td><?php echo $d_user['usuario'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">Senha:</td>
                    <td class="detal_user_field">Oculta, por  segurança. <?php if ( $n_admsist >= 4 ) { ?> <a href='javascript:void(0)' id="link_reset_pass" title="Atualizar para a senha padrão">Definir senha padrão</a><input type="hidden" name="reset_pass" value="1" /><?php }; ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">E-mail:</td>
                    <td class="detal_user_field"><?php echo $d_user['email'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">Cargo:</td>
                    <td class="detal_user_field"><?php echo $d_user['cargo'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">Setor:</td>
                    <td class="detal_user_field"><?php echo $d_user['setor'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">Iniciais:</td>
                    <td class="detal_user_field"><?php echo $d_user['iniciais'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">R.S.:</td>
                    <td class="detal_user_field"><?php echo $d_user['rsuser'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">Data do cadastro:</td>
                    <td class="detal_user_field"><?php echo $d_user['data_add'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">Cadastrado por:</td>
                    <td class="detal_user_field"><?php echo $d_user['user_add'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">Última atualização:</td>
                    <td class="detal_user_field"><?php echo $d_user['data_up'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">Atualizado por:</td>
                    <td class="detal_user_field"><?php echo $d_user['user_up'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">Último login:</td>
                    <td class="detal_user_field"><?php echo $d_user['data_ll'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">Penúltimo login:</td>
                    <td class="detal_user_field"><?php echo $d_user['prelastlogin'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">Número de logins:</td>
                    <td class="detal_user_field"><?php echo $d_user['numlogins'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">Ativo:</td>
                    <td class="detal_user_field"><?php echo tratasn( $d_user['ativo'] ) ?></td>
                </tr>
            </table>

            <p>&nbsp;</p>

            <p class="table_leg">Permissões de Acesso:</p>

            <?php if ( $n_admsist >= 3 ) { ?>
            <p class="link_common">
                <a id="link_add_perm" href="javascript:void(0)">Adicionar permissão</a>
            </p>
            <?php } ?>


            <?php

            $q_perm = "SELECT
                         `sicop_users_perm`.`idpermissao`,
                         `sicop_n_setor`.`id_n_setor`,
                         `sicop_n_setor`.`n_setor_nome`,
                         `sicop_u_n`.`idnivel`,
                         `sicop_u_n`.`descnivel`,
                         `sicop_u_n`.`descnivel_visit`
                       FROM
                         `sicop_users_perm`
                         INNER JOIN `sicop_n_setor` ON `sicop_users_perm`.`cod_n_setor` = `sicop_n_setor`.`id_n_setor`
                         INNER JOIN `sicop_u_n` ON `sicop_users_perm`.`cod_nivel` = `sicop_u_n`.`idnivel`
                       WHERE
                         `sicop_users_perm`.`cod_user` = $iduser
                         AND
                         `sicop_n_setor`.`especifico` = FALSE
                         AND
                         `sicop_n_setor`.`impressao` = FALSE
                       ORDER BY
                         `sicop_n_setor`.`n_setor_nome`";

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $q_perm = $model->query( $q_perm );

            // fechando a conexao
            $model->closeConnection();

            if ( !$q_perm ) {

                // montar a mensagem q será salva no log
                $msg = array( );
                $msg['tipo'] = 'err';
                $msg['text'] = "Falha na consulta ( $motivo_pag ).";
                $msg['linha'] = __LINE__;
                get_msg( $msg, 1 );

                echo msg_js( 'FALHA!', 1 );
                exit;

            }
            ?>

            <div id="table_user_perm">

            <?php
            $cont_q_perm = $q_perm->num_rows;

            if ( $cont_q_perm < 1 ) {
                echo '<p class="p_q_no_result">Não há permissões cadastradas.</p>';
            } else {

            ?>

                <table class="detal_user_perm" >
                    <tr>
                        <th class="detal_user_setor">Setor</th>
                        <th class="detal_user_nivel">Permissão</th>
                        <?php if ( $n_admsist >= 4 ) { ?>
                        <th class="tb_bt">&nbsp;</th>
                        <th class="tb_bt">&nbsp;</th>
                        <?php } ?>
                    </tr>

                    <?php
                        while ( $d_perm = $q_perm->fetch_assoc() ) {

                            $nivel_access = $d_perm['descnivel'];
                            $visit        = '';

                            if ( $d_perm['id_n_setor'] == 38 ) {
                                $nivel_access = $d_perm['descnivel_visit'];
                                $visit        = 1;
                                //$nivel_access = AlteraVariavel_v( $d_perm['idnivel'] );
                            }
                    ?>

                    <tr class="even">
                        <td class="detal_user_setor"><?php echo $d_perm['n_setor_nome']?></td>
                        <td class="detal_user_nivel"><?php echo $nivel_access?></td>
                        <?php if ( $n_admsist >= 4 ) { ?>
                        <td class="tb_bt">
                            <input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" name="edit_user_perm[]" value="<?php echo $d_perm['idpermissao'] ;?>" title="Alterar permissão" />
                            <input type="hidden" name="visit" value="<?php echo $visit; ?>" />
                        </td>
                        <td class="tb_bt">
                            <input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" name="del_user_perm[]" value="<?php echo $d_perm['idpermissao'] ;?>" title="Excluir permissão" />
                            <input type="hidden" name="perm_type" value="1" />
                        </td>
                        <?php } ?>
                    </tr>

                    <?php } // fim do while ( $d_perm...  ?>

                </table><!--/table.detal_user_perm-->
            <?php } // fim do if que conta o número de ocorrencias?>
            </div><!-- /div#table_user_perm -->


            <p>&nbsp;</p>

            <p class="table_leg">Permissões de impressão:</p>

            <?php if ( $n_admsist >= 3 ) { ?>
            <p class="link_common">
                <a id="link_add_perm_imp" href="javascript:void(0)">Adicionar permissão</a>
            </p>
            <?php } ?>

            <div id="table_user_perm_imp">

            <?php

            $q_perm_imp = "SELECT
                             `sicop_users_perm`.`idpermissao`,
                             `sicop_n_setor`.`id_n_setor`,
                             `sicop_n_setor`.`n_setor_nome`
                           FROM
                             `sicop_users_perm`
                             INNER JOIN `sicop_n_setor` ON `sicop_users_perm`.`cod_n_setor` = `sicop_n_setor`.`id_n_setor`
                           WHERE
                             `sicop_users_perm`.`cod_user` = $iduser
                             AND
                             `sicop_n_setor`.`impressao` = TRUE
                           ORDER BY
                             `sicop_n_setor`.`n_setor_nome`";

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $q_perm_imp = $model->query( $q_perm_imp );

            // fechando a conexao
            $model->closeConnection();

            if ( !$q_perm_imp ) {

                // montar a mensagem q será salva no log
                $msg = array( );
                $msg['tipo'] = 'err';
                $msg['text'] = "Falha na consulta ( $motivo_pag ).";
                $msg['linha'] = __LINE__;
                get_msg( $msg, 1 );

                echo msg_js( 'FALHA!', 1 );
                exit;

            }

            $cont_q_perm_imp = $q_perm_imp->num_rows;

            if ( $cont_q_perm_imp < 1 ) {
                echo '<p class="p_q_no_result">Não há permissões cadastradas.</p>';
            } else {

            ?>
            <table class="detal_user_perm" >
                <tr>
                    <th class="detal_user_setor">Setor</th>
                    <?php if ( $n_admsist >= 4 ) { ?>
                    <th class="tb_bt">&nbsp;</th>
                    <?php } ?>
                </tr>

                <?php
                    while ( $d_perm_imp = $q_perm_imp->fetch_assoc() ) {
                ?>

                <tr class="even">
                    <td class="detal_user_setor"><?php echo $d_perm_imp['n_setor_nome']?></td>
                    <?php if ( $n_admsist >= 4 ) { ?>
                    <td class="tb_bt">
                        <input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" name="del_user_perm[]" value="<?php echo $d_perm_imp['idpermissao'] ;?>" title="Excluir permissão" />
                        <input type="hidden" name="perm_type" value="2" />
                    </td>
                    <?php } ?>
                </tr>

                <?php } // fim do while ( $d_perm...  ?>

            </table><!--/table.detal_user_perm-->

            <?php } // fim do if que conta o número de ocorrencias?>
            </div><!-- /div#table_user_perm_imp -->


            <p>&nbsp;</p>

            <p class="table_leg">Permissões de específicas:</p>

            <?php if ( $n_admsist >= 3 ) { ?>
            <p class="link_common">
                <a id="link_add_perm_esp" href="javascript:void(0)">Adicionar permissão</a>
            </p>
            <?php } ?>

            <div id="table_user_perm_esp">
            <?php

            $q_perm_esp = "SELECT
                             `sicop_users_perm`.`idpermissao`,
                             `sicop_n_setor`.`id_n_setor`,
                             `sicop_n_setor`.`n_setor_nome`
                           FROM
                             `sicop_users_perm`
                             INNER JOIN `sicop_n_setor` ON `sicop_users_perm`.`cod_n_setor` = `sicop_n_setor`.`id_n_setor`
                           WHERE
                             `sicop_users_perm`.`cod_user` = $iduser
                             AND
                             `sicop_n_setor`.`especifico` = TRUE
                           ORDER BY
                             `sicop_n_setor`.`n_setor_nome`";

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $q_perm_esp = $model->query( $q_perm_esp );

            // fechando a conexao
            $model->closeConnection();

            if ( !$q_perm_esp ) {

                // montar a mensagem q será salva no log
                $msg = array( );
                $msg['tipo'] = 'err';
                $msg['text'] = "Falha na consulta ( $motivo_pag ).";
                $msg['linha'] = __LINE__;
                get_msg( $msg, 1 );

                echo msg_js( 'FALHA!', 1 );
                exit;

            }

            $cont_q_perm_esp = $q_perm_esp->num_rows;

            if ( $cont_q_perm_esp < 1 ) {
                echo '<p class="p_q_no_result">Não há permissões cadastradas.</p>';
            } else {

            ?>
            <table class="detal_user_perm" >
                <tr>
                    <th class="detal_user_setor">Setor</th>
                    <?php if ( $n_admsist >= 4 ) { ?>
                    <th class="tb_bt">&nbsp;</th>
                    <?php } ?>
                </tr>

                <?php
                    while ( $d_perm_esp = $q_perm_esp->fetch_assoc() ) {
                ?>

                <tr class="even">
                    <td class="detal_user_setor"><?php echo $d_perm_esp['n_setor_nome']?></td>
                    <?php if ( $n_admsist >= 4 ) { ?>
                    <td class="tb_bt">
                        <input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" name="del_user_perm[]" value="<?php echo $d_perm_esp['idpermissao'] ;?>" title="Excluir permissão" />
                        <input type="hidden" name="perm_type" value="3" />
                    </td>
                    <?php } ?>
                </tr>

                <?php } // fim do while ( $d_perm_esp...  ?>

            </table><!--/table.detal_user_perm-->

            <?php } // fim do if que conta o número de ocorrencias?>
            </div><!-- /div#table_user_perm_esp -->

<?php include 'footer.php'; ?>
