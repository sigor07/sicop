<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';
$motivo_pag = 'DETALHES DO USUÁRIO';

$iduser = get_session( 'user_id', 'int' );

$targ = get_get( 'targ', 'int' );
$botao_canc = 'history.go(-1)';
$botao_value = 'Cancelar';

if ( $targ == 1) {
    $botao_canc = 'self.window.close()';
    $botao_value = 'Fechar';
}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$query =  "SELECT
                `sicop_users`.`iduser`,
                `sicop_users`.`nomeuser`,
                `sicop_users`.`usuario`,
                `sicop_users`.`senha`,
                `sicop_users`.`email`,
                `sicop_users`.`cargo`,
                `sicop_users`.`iniciais`,
                `sicop_users`.`rsuser`,
                `sicop_users`.`numlogins`,
                DATE_FORMAT( `sicop_users`.`data_add`, '%d/%m/%Y às %H:%i' ) AS data_add,
                DATE_FORMAT( `sicop_users`.`prelastlogin`, '%d/%m/%Y às %H:%i' ) AS prelastlogin,
                `sicop_users`.`ativo`,
                `sicop_setor`.`setor`
              FROM
                `sicop_users`
                INNER JOIN `sicop_setor` ON `sicop_users`.`cod_setor` = `sicop_setor`.`idsetor`
              WHERE
                `sicop_users`.`iduser` = '$iduser'";

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

if ( $cont < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrencias (USUÁRIOS).\n\n Página $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$dados = $query->fetch_assoc();


if ( $targ == 1 ) {
    require 'cab_simp.php';
} else {
    require 'cab.php';
}
?>

            <p class="descript_page">DETALHES DO USUÁRIO</p>

            <p class="table_leg">Dados do usuário:</p>

            <table class="detal_user">
                <tr class="even">
                    <td class="detal_user_leg">Nome do usuário:</td>
                    <td class="detal_user_field"><?php echo $dados['nomeuser'] ?></td>
                </tr>
                <tr class="even">
                  <td class="detal_user_leg">ID:</td>
                  <td class="detal_user_field"><?php echo $dados['iduser'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">Nome de acesso:</td>
                    <td class="detal_user_field"><?php echo $dados['usuario'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">Senha:</td>
                    <td class="detal_user_field">Oculta, por motivos de segurança. <?php if ( $targ != 1 ){ ?> <a href="alterasenha.php">Alterar</a><?php }; ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">E-mail:</td>
                    <td class="detal_user_field"><?php echo $dados['email'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">Cargo:</td>
                    <td class="detal_user_field"><?php echo $dados['cargo'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">Setor:</td>
                    <td class="detal_user_field"><?php echo $dados['setor'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">Iniciais:</td>
                    <td class="detal_user_field"><?php echo $dados['iniciais'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">R.S.:</td>
                    <td class="detal_user_field"><?php echo $dados['rsuser'] ?></td>
                </tr>
                <tr class="even">
                    <td class="detal_user_leg">Data do cadastro:</td>
                    <td class="detal_user_field"><?php echo $dados['data_add'] ?></td>
                </tr>
                <tr class="even">
                  <td class="detal_user_leg">Último login:</td>
                  <td class="detal_user_field"><?php echo $dados['prelastlogin'] ?></td>
                </tr>
                <tr class="even">
                  <td class="detal_user_leg">Número de logins:</td>
                  <td class="detal_user_field"><?php echo $dados['numlogins'] ?></td>
                </tr>
            </table>

            <p class="table_leg">Permissões de Acesso:</p>

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
                    </tr>

                    <?php } // fim do while ( $d_perm...  ?>
                </table><!--/table.detal_user_perm-->
            <?php } // fim do if que conta o número de ocorrencias?>




            <p class="table_leg">Permissões de impressão:</p>

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
                </tr>

                <?php while ( $d_perm_imp = $q_perm_imp->fetch_assoc() ) { ?>

                <tr class="even">
                    <td class="detal_user_setor"><?php echo $d_perm_imp['n_setor_nome']?></td>
                </tr>

                <?php } // fim do while ( $d_perm...  ?>

            </table><!--/table.detal_user_perm-->

            <?php } // fim do if que conta o número de ocorrencias?>


            <p class="table_leg">Permissões de específicas:</p>

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
                </tr>

                <?php while ( $d_perm_esp = $q_perm_esp->fetch_assoc() ) { ?>

                <tr class="even">
                    <td class="detal_user_setor"><?php echo $d_perm_esp['n_setor_nome']?></td>
                </tr>

                <?php } // fim do while ( $d_perm_esp... ?>

            </table><!--/table.detal_user_perm-->

            <?php } // fim do if que conta o número de ocorrencias?>

<?php include 'footer.php'; ?>