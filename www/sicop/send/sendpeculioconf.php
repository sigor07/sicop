<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

    extract($_POST, EXTR_OVERWRITE);

    $user        = get_session( 'user_id', 'int' );
    $ip          = "'" . $_SERVER['REMOTE_ADDR'] . "'";

    if ( empty( $idpeculio ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n O usuário não marcou nenhum pertence ( CONFIRMAÇÃO DE PECÚLIO ).\n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!!!', 1 );
        exit;
    }

    // monta a variavel para o comparador IN()
    $v_pec = '';
    foreach ( $idpeculio as $indice => $valor ) {
        $valor = (int)$valor;
        if ( empty( $valor ) ) continue;
        $v_pec .= (int)$valor . ',';
    }

    if ( empty( $v_pec ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Após validação, o array ficou vazio. ( CONFIRMAÇÃO DE PECÚLIO ).\n\nPágina: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!!!', 1 );
        exit;
    }

    $v_pec = substr($v_pec, 0, -1);

    if ( !empty( $cnf ) ) { // cnf  = confimar pertences

        $q_peculio = "UPDATE
                        `peculio`
                      SET
                        `confirm` = TRUE,
                        `user_conf` = $user,
                        `data_conf` = NOW(),
                        `ip_conf` = $ip
                      WHERE
                        `idpeculio` IN ( $v_pec )";

        $q_s_pec = "SELECT
                      `detentos`.`nome_det`,
                      `detentos`.`matricula`,
                      `peculio`.`idpeculio`,
                      `peculio`.`descr_peculio`,
                      `tipopeculio`.`tipo_peculio`
                    FROM
                      `peculio`
                      INNER JOIN `detentos` ON `peculio`.`cod_detento` = `detentos`.`iddetento`
                      INNER JOIN `tipopeculio` ON `peculio`.`cod_tipo_peculio` = `tipopeculio`.`idtipopeculio`
                    WHERE
                      `idpeculio` IN ( $v_pec )";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_s_pec = $model->query( $q_s_pec );

        // fechando a conexao
        $model->closeConnection();

        $peculio = '';
        while ( $d_s_pec = $q_s_pec->fetch_assoc() ) {
            $nome_det = $d_s_pec['nome_det'];
            $matricula = !empty( $d_s_pec['matricula'] ) ? formata_num( $d_s_pec['matricula'] ) : '';
            $idp = $d_s_pec['idpeculio'];
            $descr_peculio = $d_s_pec['descr_peculio'];
            $tipo_peculio = $d_s_pec['tipo_peculio'];
            $peculio .= '<b>' . SICOP_DET_DESC_FU . ":</b> $nome_det; <b>Matrícula:</b> $matricula; <b>ID do pertence:</b> $idp; <b>Tipo:</b> $tipo_peculio; <b>Descrição:</b> $descr_peculio \n";
        }

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_peculio = $model->query( $q_peculio );

        // fechando a conexao
        $model->closeConnection();

        if( $q_peculio ) {

            $mensagem = "[ CONFIRMAÇÃO DE PECÚLIO ]\n Confirmação de pertence/peculio. \n \n[ PERTENCES ]\n $peculio \n";
            salvaLog($mensagem);
            echo msg_js( '', 1 );
            exit;

        } else {

            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de cadastramento de pecúlio.\n \n[ PERTENCES ]\n $peculio \n";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!!!', 1 );
            exit;

        }

    } else if ( !empty( $irp ) ) { // irp  = imprimir relação de pertences

        if ( isset( $_SESSION['v_pec'] ) ) unset( $_SESSION['v_pec'] );

        $_SESSION['v_pec'] = $v_pec;
        ?>
        <script type="text/javascript" src="<?php echo SICOP_ABS_PATH ?>js/funcoes.js"></script>
        <script type="text/javascript">javascript: ow ('../print/lista_pert.php', '600', '600'); history.go(-1);</script>
        <?php
        exit;

    } else {

        $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de confirmação de pertences.\n\n Página: $pag";
        salvaLog($mensagem);
        header('Location: ../home.php');
        exit;

    }


//------------------------------------------------------------------------------------------------------------------------------*/

} else {

    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de confirmação de pertences.\n\n Página: $pag";
    salvaLog($mensagem);
    header('Location: ../home.php');
    exit;

}
?>
</body>
</html>
