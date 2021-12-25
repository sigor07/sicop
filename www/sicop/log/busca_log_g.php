<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
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

$campo_user = get_get( 'campo_user', 'int' );
$data_in    = get_get( 'data_in', 'busca' );
$hora_in    = get_get( 'hora_in', 'busca' );
$data_out   = get_get( 'data_out', 'busca' );
$hora_out   = get_get( 'hora_out', 'busca' );
$campo_alt  = get_get( 'campo_alt', 'busca' );
$campo_ip   = get_get( 'campo_ip', 'string' );

$q_string = '';

if( !empty( $_GET['busca'] ) ) {

    $where = '';

    if ( !empty( $campo_user ) ){
        if ( !empty( $where ) ){
            $where .= " AND ( `logs`.`id_user` = $campo_user )";
        } else {
            $where .= "WHERE ( `logs`.`id_user` = $campo_user )";
        }
    }

    if ( !empty( $data_in ) or !empty( $data_out )){

        if ( !empty( $data_in ) and  !empty( $data_out )){

/*            // cria o padrao de formatação para o STR_TO_DATE
            $p_std_in = '%d/%m/%Y %H:%i';

            if ( empty( $hora_in ) ){
                $p_std_in = '%d/%m/%Y';
            }

            // cria o padrao de formatação para o STR_TO_DATE
            $p_std_out = '%d/%m/%Y %H:%i';

            if ( empty( $hora_out ) ){
                $p_std_out = '%d/%m/%Y';
            }*/

            $data_in_f = $data_in . ' ' . $hora_in;
            $data_out_f = $data_out . ' ' . $hora_out;

            $clausula_data = "`logs`.`hora` BETWEEN STR_TO_DATE( '$data_in_f', '%d/%m/%Y %H:%i' ) AND STR_TO_DATE( '$data_out_f', '%d/%m/%Y %H:%i' )";

            if ( !empty( $where ) ){
                $where .= " AND " . $clausula_data;
            } else {
                $where .= "WHERE " . $clausula_data;
            }

        } else {

            $hora_f = !empty( $hora_in ) ? $hora_in : $hora_out;
            $data_f = !empty( $data_in ) ? $data_in : $data_out;

            //$clausula_data = "$sql_campo_data = IF( STR_TO_DATE( '$data_in_f', '%d/%m/%Y %H:%i' ), STR_TO_DATE( '$data_in_f', '%d/%m/%Y %H:%i' ), STR_TO_DATE( '$data_out_f', '%d/%m/%Y %H:%i' ) )";
            $clausula_data = "DATE( `logs`.`hora` ) = STR_TO_DATE( '$data_f', '%d/%m/%Y' ) ";

            $clausula_hora = '';
            if ( !empty( $hora_f ) ){
                $clausula_hora = "AND ( HOUR( `logs`.`hora` ) = HOUR( '$hora_f' ) AND MINUTE( `logs`.`hora` ) = MINUTE( '$hora_f' ) ) ";
            }

            if ( !empty( $where ) ){
                $where .= ' AND ' . $clausula_data . $clausula_hora;
            } else {
                $where .= 'WHERE ' . $clausula_data . $clausula_hora;
            }

        }

    }

    if ( !empty( $campo_alt ) ) {
        if ( !empty( $where ) ) {
            $where .= " AND ( `logs`.`mensagem` LIKE '%$campo_alt%' )";
        } else {
            $where .= "WHERE ( `logs`.`mensagem` LIKE '%$campo_alt%' )";
        }
    }

    if ( !empty( $campo_ip ) ) {
        if ( !empty( $where ) ) {
            $where .= " AND ( `logs`.`ip` LIKE '$campo_ip%' )";
        } else {
            $where .= "WHERE ( `logs`.`ip` LIKE '$campo_ip%' )";
        }
    }

    $_BS = '';

    $_BS['PorPagina'] = 1000;

    // Monta a consulta MySQL para saber quantos registros serão encontrados
    $sql = "SELECT COUNT(*) AS total FROM `logs` $where";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    (int)$total = $model->fetchOne( $sql );

    // fechando a conexao
    $model->closeConnection();

    if ( $total === false ) {

        echo msg_js( 'FALHA!!!', 1 );
        exit;

    }

    // Calcula o máximo de paginas
    $paginas = (($total % $_BS['PorPagina']) > 0) ? (int)($total / $_BS['PorPagina']) + 1 : ($total / $_BS['PorPagina']);

    $pagina = 1;
    $g_pag = get_get( 'pagina', 'int' );
    if ( !empty( $g_pag ) ) {
        $pagina = $g_pag;
    }

    $pagina = max( min( $paginas, $pagina ), 1);
    $inicio = ( $pagina - 1 ) * $_BS['PorPagina'];

    $query_log = "SELECT
                      `logs`.`id`,
                      `logs`.`ip`,
                      `logs`.`mensagem`,
                      `logs`.`hora`,
                      Date_Format(`logs`.`hora`, '%d/%m/%Y às %H:%i:%s') AS data_f,
                      `logs`.`id_user`,
                      `sicop_users`.`nome_cham`
                    FROM
                      `logs`
                      LEFT JOIN `sicop_users` ON `logs`.`id_user` = `sicop_users`.`iduser`
                    $where
                    ORDER BY
                      `logs`.`hora` DESC
                    LIMIT $inicio, ".$_BS['PorPagina'];

//    echo $inicio . '<br/>' . $pagina . '<br/>' . $g_pag . '<br/>';
//    echo nl2br($query_log);
//    exit;


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

    parse_str( $_SERVER[ 'QUERY_STRING' ], $q_string );

    if ( isset( $q_string['pagina'] ) ) {
        unset( $q_string['pagina'] );
    }

}

/*    $mensagem = "Acesso à página $pag";
    salvaLog($mensagem);*/

$q_user = 'SELECT `iduser`, `nome_cham` FROM `sicop_users` ORDER BY `nome_cham`';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_user = $model->query( $q_user );

// fechando a conexao
$model->closeConnection();

$desc_pag = 'Log geral';

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>

            <p class="descript_page">PESQUISAR NO LOG DE REGISTRO GERAL</p>

            <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="get" name="buscadet" id="buscadet" >
                <table class="busca_form"><!--remacc(campobusca);-->
                    <tr>
                        <td class="bf_legend">Usuário:</td>
                        <td class="bf_field">
                            <select name="campo_user" class="CaixaTexto" id="campo_user">
                                <option value="" >Selecione...</option>
                                <?php while ( $d_user = $q_user->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_user['iduser']; ?>" <?php echo $d_user['iduser'] == $campo_user ? 'selected="selected"' : ''; ?>><?php echo $d_user['nome_cham']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="bf_legend">Entre:</td>
                        <td class="bf_field"><input name="data_in" type="text" class="CaixaTexto" id="data_in" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_in ?>" size="12" maxlength="10" /> às <input name="hora_in" type="text" class="CaixaTexto" id="hora_in" onblur="verifica_hora(this, this.value)" onkeypress="mascara_hora(this, this.value); return blockChars(event, 2);" value="<?php echo $hora_in; ?>" size="5" maxlength="5" /></td>
                    </tr>
                    <tr>
                        <td class="bf_legend">e:</td>
                        <td class="bf_field"><input name="data_out" type="text" class="CaixaTexto" id="data_out" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_out ?>" size="12" maxlength="10" /> às <input name="hora_out" type="text" class="CaixaTexto" id="hora_out" onblur="verifica_hora(this, this.value)" onkeypress="mascara_hora(this, this.value); return blockChars(event, 2);" value="<?php echo $hora_out; ?>" size="5" maxlength="5" /></td>
                    </tr>
                    <tr>
                        <td class="bf_legend">Alteração:</td>
                        <td class="bf_field"><input name="campo_alt" type="text" class="CaixaTexto" id="campo_alt" onkeypress="return blockChars(event, 4);" value="<?php echo $campo_alt ?>" size="50" /></td>
                    </tr>
                    <tr>
                        <td class="bf_legend">IP:</td>
                        <td class="bf_field"><input name="campo_ip" type="text" class="CaixaTexto" id="campo_ip" onkeypress="return blockChars(event, 5);" value="<?php echo $campo_ip ?>" size="15" /></td>
                    </tr>
                </table>

                <input name="busca" type="hidden" id="busca" value="busca" />


                <div class="form_bts">

                    <input class="form_bt" type="submit" name="buscar" id="buscar" value="Buscar" />

                </div>

            </form>

            <script type="text/javascript">

                $(function() {
                    $( "#campo_user" ).focus();
                    $( "#data_in, #data_out" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

            <?php

            if ( empty( $_GET['busca'] ) ) {
                include 'footer.php';
                exit;
            }

            if( empty( $total ) or $total < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';
                include 'footer.php';
                exit;
            }

            ?>

            <?php if ( empty( $_GET['limit'] ) ) {?>
            <p class="table_leg">Mostrando de <?php echo min( $total, ( $inicio + 1 ) ) . ' à ' . min( $total, ( $inicio + $_BS['PorPagina'] ) ) . ' de ' . $total ?> resultados encontrados.</p>
            <?php } ?>

            <p class="table_leg_log">
            <?php
            if ( empty( $_GET['limit'] ) ){
                if ( $total > 0 ) {
                    for($n = 1; $n <= $paginas; $n++) {
                        if ( $n != $pagina ){
                            echo '<a href="?' . http_build_query( $q_string ) . '&pagina='.$n.'">'.$n.'</a>';
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
                                echo '<a href="?' . http_build_query( $q_string ) . '&pagina=' . $n . '">' . $n . '</a>';
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
