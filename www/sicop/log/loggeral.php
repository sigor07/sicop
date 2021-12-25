<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_admsist        = get_session( 'n_admsist', 'int' );
$nivel_necessario = 2;

if ( $n_admsist < $nivel_necessario ) {
    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
    salvaLog( $mensagem );
    exit;
}

if (isset($_GET['limit']) and $_GET['limit'] == 'yes' ) {

    $limit = 'LIMIT 1000';

    $query_log = "SELECT
                  `logs`.`id`,
                  `logs`.`ip`,
                  `logs`.`mensagem`,
                  `logs`.`hora`,
                  DATE_FORMAT( `logs`.`hora`, '%d/%m/%Y às %H:%i:%s' ) AS data_f,
                  `logs`.`id_user`,
                  `sicop_users`.`nome_cham`
                FROM
                  `logs`
                  LEFT JOIN `sicop_users` ON `logs`.`id_user` = `sicop_users`.`iduser`
                ORDER BY
                  `logs`.`hora` DESC
                $limit";

} else {

    $_BS['PorPagina'] = 1000;

    // Monta a consulta MySQL para saber quantos registros serão encontrados
    $sql = 'SELECT COUNT(*) AS total FROM `logs`';

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    (int)$total = $model->fetchOne( $sql );

    // fechando a conexao
    $model->closeConnection();

    // Calcula o máximo de paginas
    $paginas = (($total % $_BS['PorPagina']) > 0) ? (int)($total / $_BS['PorPagina']) + 1 : ($total / $_BS['PorPagina']);

    $pagina = 1;
    $g_pag = get_get( 'pagina', 'int' );
    if ( !empty( $g_pag ) ) {
        $pagina = $g_pag;
    }

    $pagina = max( min( $paginas, $pagina ), 1 );
    $inicio = ($pagina - 1) * $_BS['PorPagina'];

        $query_log = "SELECT
                          `logs`.`id`,
                          `logs`.`ip`,
                          `logs`.`mensagem`,
                          `logs`.`hora`,
                          DATE_FORMAT( `logs`.`hora`, '%d/%m/%Y às %H:%i:%s' ) AS data_f,
                          `logs`.`id_user`,
                          `sicop_users`.`nome_cham`
                        FROM
                          `logs`
                          LEFT JOIN `sicop_users` ON `logs`.`id_user` = `sicop_users`.`iduser`
                        ORDER BY
                          `logs`.`hora` DESC
                        LIMIT $inicio, ".$_BS['PorPagina'];
}

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_log = $model->query( $query_log );

// fechando a conexao
$model->closeConnection();

if ( !$query_log ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$count = $query_log->num_rows;

/*    $mensagem = "Acesso à página $pag";
    salvaLog($mensagem);*/
$desc_pag = 'Log geral';

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

            <p class="descript_page">LOG DE REGISTRO GERAL</p>

            <p class="link_common">Ocorrências com mais de 30 dias serão automaticamente excluidas.<br />
                <?php if ( isset( $_GET['limit'] ) and $_GET['limit'] == 'yes' ) { ?>
                Mostrando as 1000 últimas ocorrencias. <a href="loggeral.php" >Mostrar todas</a>
                <?php } else {?>
                Mostrando todas as alterações registradas. <a href="loggeral.php?limit=yes" >Mostrar apenas as 1000 últimas ocorrencias.</a>
                <?php } ?>
            </p>

            <?php

            if( empty( $count ) or $count < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';
                include 'footer.php';
                exit;
            }

            ?>

            <?php if ( empty( $_GET['limit'] ) ) { ?>
            <p class="table_leg">Mostrando de <?php echo min( $total, ( $inicio + 1 ) ) . ' à ' . min( $total, ( $inicio + $_BS['PorPagina'] ) ) . ' de ' . $total ?> resultados encontrados.</p>
            <?php } ?>

            <p class="table_leg_log">
                <?php
                if ( empty( $_GET['limit'] ) ) {
                    if ( $total > 0 ) {
                        for ( $n = 1; $n <= $paginas; $n++ ) {
                            if ( $n != $pagina ) {
                                echo '<a href="?pagina=' . $n . '">' . $n . '</a>';
                                if ( $n != $paginas ) echo ' | ';
                            } else {
                                echo '<font color="#FF0000">' . $n . '</font>';
                                if ( $n != $paginas ) echo ' | ';
                            }
                        }
                    }
                }
                ?>
            </p>

            <!--bordercolor="#000000" frame="border" rules="all"-->

            <table class="lista_log">
                <tr class="cab">
                    <th class="log_user" scope="col">Usuário</th>
                    <th class="log_data" scope="col">Data / hora</th>
                    <th class="log_msg" scope="col">Mensagem</th>
                    <th class="log_ip" scope="col">I.P.</th>
                </tr>

                <?php while ( $log = $query_log->fetch_assoc() ) { ?>

                <tr class="even">
                    <td class="log_user"><a href="<?php echo SICOP_ABS_PATH ?>user/user.php?iduser=<?php echo $log['id_user'] ?>"><?php echo $log['nome_cham'] ?></a></td>
                    <td class="log_data"><?php echo $log['data_f'] ?></td>
                    <td class="log_msg"><?php echo nl2br( $log['mensagem'] ); ?></td>
                    <td class="log_ip"><?php echo $log['ip'] ?></td>
                </tr>

                <?php } ?>

            </table>

            <p class="table_leg_log">
                <?php
                if ( empty( $_GET['limit'] ) ) {
                    if ( $total > 0 ) {
                        for ( $n = 1; $n <= $paginas; $n++ ) {
                            if ( $n != $pagina ) {
                                echo '<a href="?pagina=' . $n . '">' . $n . '</a>';
                                if ( $n != $paginas ) echo ' | ';
                            } else {
                                echo '<font color="#FF0000">' . $n . '</font>';
                                if ( $n != $paginas ) echo ' | ';
                            }
                        }
                    }
                }
                ?>
            </p>

<?php include 'footer.php'; ?>
