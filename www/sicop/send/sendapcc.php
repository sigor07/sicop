<?php

// @todo ARRUMAR ESSE ARQUIVO
// REFAZER ESSE ARQUIVO BASEADO NOS NOVOS METODOS


if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    extract($_POST, EXTR_OVERWRITE);

    $targ      = empty($targ) ? '0' : '1';
    $proced    = (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

    $user      = get_session( 'user_id', 'int' );
    $ip        = "'" . $_SERVER['REMOTE_ADDR'] . "'";
    $n_ret     = 3;

    $msg_f_atu = 'FALHA!';
    $msg_f_exc = 'FALHA ao excluir!';
    $msg_f_cad = 'FALHA ao cadastrar!';

    if (isset($proced) and $proced == '1'){ // ATUALIZAÇÃO
/*
-----------------------------------------------------------
PARTE DO CODIGO RESPONSAVEL PELA ATUALIZAÇÃO
-----------------------------------------------------------
*/
        $n_ret = 1;

        $idapcc = empty( $idapcc ) ? '' : (int)$idapcc;

        if ( empty( $idapcc ) ) {
            $mensagem = "ERRO -> Identificador do APCC em branco. Operação cancelada (ATUALIZAÇÃO DE APCC).\n\n Página: $pag";
            salvaLog( $mensagem );
            echo msg_js( $msg_f_atu, 1 );
            exit;
        }

        if ( empty( $idmovin ) ) {

            $mensagem = "ERRO -> O usuário não marcou nenhuma movimentação (ATUALIZAÇÃO DE APCC).\n\n Página: $pag";
            salvaLog( $mensagem );
            echo msg_js( 'Você deve marcar pelo menos uma movimentação!', 1 );
            exit;

        }

        // CHECA QUAIS MOVIMENTAÇÕES REGISTRADAS FORAM MARCADAS PELO USUÁRIO (SE ESTÃO NO ARRAY...)
        $q_ck_mov = "SELECT `id_apcc_mov`, `cod_movin` FROM `apcc_mov` WHERE `cod_apcc` = $idapcc";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_ck_mov = $model->query( $q_ck_mov );

        while( $d_ck = $q_ck_mov->fetch_assoc() ) {

            $ck_id_apcc_mov = $d_ck['id_apcc_mov'];
            $ck_idmovin     = $d_ck['cod_movin'];

            // SE NÃO ESTIVEREM MARCADAS, ELAS SERÃO EXCLUIDAS DA TABELA...
            if ( !in_array( $ck_idmovin, $idmovin ) ) {
                $q_del_mov = "DELETE FROM `apcc_mov` WHERE `id_apcc_mov` = $ck_id_apcc_mov LIMIT 1";
                // executando a query
                $model->query( $q_del_mov );
            }

        }

        // CHECA AS MOVIMENTAÇÕES QUE O USUÁRIO MARCOU SE EXITEM NA TABELA, SE NÃO EXITIREM, SERÃO INCLUIDAS
        foreach ( $idmovin as $indice => $valor ) {
            if ( (int)$valor == NULL ) continue;

            $q_ck_ar = "SELECT `id_apcc_mov`, `cod_movin` FROM `apcc_mov` WHERE `cod_apcc` = $idapcc AND `cod_movin` = $valor";

            // executando a query
            $q_ck_ar = $model->query( $q_ck_ar );

            $cont_ck_ar = $q_ck_ar->num_rows;

            if ( $cont_ck_ar < 1 ) {

                $q_mov_in = "SELECT
                                  id_mov,
                                  cod_detento,
                                  data_mov
                                FROM
                                  mov_det
                                WHERE
                                  id_mov = $valor
                                ORDER BY
                                  data_mov ASC, data_add ASC";

                // executando a query
                $q_mov_in = $model->query( $q_mov_in );

                $cont_mov_in = $q_mov_in->num_rows;

                $d_mov_in = $q_mov_in->fetch_assoc();

                $idmovin = $d_mov_in['id_mov'];
                $data_in = "'" . $d_mov_in['data_mov'] . "'";
                $iddet   = $d_mov_in['cod_detento'];

                $q_mov_out = "SELECT
                                  id_mov
                                FROM
                                  mov_det
                                WHERE
                                  (mov_det.cod_tipo_mov = 5 OR mov_det.cod_tipo_mov = 7) AND mov_det.data_mov >= $data_in AND mov_det.cod_detento = $iddet
                                ORDER BY
                                  mov_det.data_mov, mov_det.data_add
                                LIMIT 1";

                // executando a query
                $q_mov_out = $model->query( $q_mov_out );

                $cont_mov_out = $q_mov_out->num_rows;

                $idmovout     = 'NULL';

                if ( $cont_mov_out >= 1 ){
                    $d_mov_out = $q_mov_out->fetch_assoc();
                    $idmovout  = $d_mov_out['id_mov'];
                }

                $valor_mov_insert = "($idapcc, $idmovin, $idmovout)";

                $q_mov_apcc = "INSERT INTO `apcc_mov` (`cod_apcc`, `cod_movin`, `cod_movout`) VALUES $valor_mov_insert";

                // executando a query
                $model->query( $q_mov_apcc );

            }

        }

        $conduta  = empty( $conduta ) ? 'NULL' : (int)$conduta;
        $pda  = empty( $pda ) ? 'NULL' : "'" . $model->escape_string( $pda ) . "'";

        // QUERY PARA PEGAR O NÚMERO DO ATESTADO
        $q_num_apcc = "SELECT numeroapcc.numero_apcc, numeroapcc.ano FROM apcc INNER JOIN numeroapcc ON `apcc`.`cod_numapcc` = numeroapcc.idnumapcc WHERE apcc.idapcc = $idapcc";

        // executando a query
        $q_num_apcc = $model->query( $q_num_apcc );

        $d_num_apcc = $q_num_apcc->fetch_assoc();
        $num_apcc   = $d_num_apcc['numero_apcc'] . '/' . $d_num_apcc['ano'];

        // pegar os dados do preso
        $det_where = "( SELECT `cod_detento` FROM `apcc` WHERE `idapcc` = $idapcc LIMIT 1 )";
        $detento = dados_det( $det_where );

        $query_apcc = "UPDATE `apcc` SET
                              `cod_conduta` = $conduta,
                              `num_pda` = $pda,
                              `user_up` = $user,
                              `data_up` = NOW(),
                              `ip_up` = $ip
                        WHERE `idapcc` = $idapcc LIMIT 1;";

        // fechando a conexao
        $model->closeConnection();

/*
-------------------------------------------------------------------
FIM DA PARTE DO CODIGO RESPONSAVEL PELA ATUALIZAÇÃO
-------------------------------------------------------------------
*/
    } else if (isset($proced) and $proced == '2'){ //EXCLUSÃO
/*
-------------------------------------------------------------------
PARTE DO CODIGO RESPONSAVEL PELA EXCLUSÃO
-------------------------------------------------------------------
*/

        $idapcc = empty( $idapcc ) ? '' : (int)$idapcc;

        if ( empty( $idapcc ) ) {
            $mensagem = "ERRO -> Identificador do APCC em branco. Operação cancelada (EXCLUSÃO DE APCC).\n\n Página: $pag";
            salvaLog( $mensagem );
            echo msg_js( 'FALHA!!!', 1 );
            exit;
        }

        // QUERY PARA PEGAR O NÚMERO DO ATESTADO
        $q_num_apcc = "SELECT numeroapcc.idnumapcc, numeroapcc.numero_apcc, numeroapcc.ano FROM apcc INNER JOIN numeroapcc ON `apcc`.`cod_numapcc` = numeroapcc.idnumapcc WHERE apcc.idapcc = $idapcc";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_num_apcc = $model->query( $q_num_apcc );

        // fechando a conexao
        $model->closeConnection();

        $d_num_apcc = $q_num_apcc->fetch_assoc();
        $num_apcc   = $d_num_apcc['numero_apcc'] . '/' . $d_num_apcc['ano'];
        $idnumapcc  = $d_num_apcc['idnumapcc'];

        // pegar os dados do preso
        $det_where = "( SELECT `cod_detento` FROM `apcc` WHERE `idapcc` = $idapcc LIMIT 1 )";
        $detento = dados_det( $det_where );

        $query_apcc = "DELETE FROM `numeroapcc` WHERE `idnumapcc` = $idnumapcc LIMIT 1";

/*
-------------------------------------------------------------------
FIM DA PARTE DO CODIGO RESPONSAVEL PELA EXCLUSÃO
-------------------------------------------------------------------
*/
    } else if (isset($proced) and $proced == '3'){ //CADASTRAMENTO
/*
-------------------------------------------------------------------
PARTE DO CODIGO RESPONSAVEL PELO CADASTRAMENTO
-------------------------------------------------------------------
*/

        $iddet = empty( $iddet ) ? '' : (int)$iddet;

        if ( empty( $iddet ) ){
            $mensagem = "ERRO -> Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. Operação cancelada (CADASTRAMENTO DE APCC).\n\n Página: $pag";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!!!', 2 );
            exit;
        }

        if ( empty( $idmovin ) ) {
            $mensagem = "ERRO -> O usuário não marcou nenhuma movimentação (CADASTRAMENTO DE APCC).\n\n Página: $pag";
            salvaLog( $mensagem );
            echo msg_js( 'Você deve marcar pelo menos uma movimentação!', 2 );
            exit;
        }

        // monta a variavel para o comparador IN()
        $v_idmovin = '';
        foreach ( $idmovin as $indice => $valor ) {
            if ( (int)$valor == NULL ) continue;
            $v_idmovin .= (int)$valor . ',';
        }

        if ( empty( $v_idmovin ) ) {
            $mensagem = "ERRO -> Após validação, o array ficou vazio. (ATUALIZAÇÃO/IMPRESSÃO DE APCC).\n\n Página: $pag";
            salvaLog( $mensagem );
            echo msg_js( 'FALHA!!!', 1 );
            exit;
        }

        // instanciando o model
        $model = SicopModel::getInstance();

        $v_idmovin = substr($v_idmovin, 0, -1);

        $conduta  = empty( $conduta ) ? 'NULL' : (int)$conduta;
        $pda  = empty( $pda ) ? 'NULL' : "'" . $model->escape_string( $pda ) . "'";

        // fechando a conexao
        $model->closeConnection();

        // pegar os dados do preso
        $detento = dados_det( $iddet );


        $coment      = "[ ATESTADO DE PERMANENCIA ]\n\n $detento.";
        $num_apcc    = numera_apcc($coment);
        $idnumapcc   = $num_apcc['id'];
        $numero_apcc = $num_apcc['num'];

        $query_apcc = "INSERT INTO `apcc`
                            (`cod_detento`,
                             `cod_numapcc`,
                             `cod_conduta`,
                             `num_pda`,
                             `user_add`,
                             `data_add`,
                             `ip_add`)
                            VALUES
                            ($iddet,
                             $idnumapcc,
                             $conduta,
                             $pda,
                             $user,
                             NOW(),
                             $ip)";


/*
-------------------------------------------------------------------
FIM DA PARTE DO CODIGO RESPONSAVEL PELO CADASTRAMENTO
-------------------------------------------------------------------
*/
    } else if ( empty( $proced ) ) { //SE NÃO HOUVER NUMERO DE PROCEDIMENTO OU ELE NÃO FOR UM INTEIRO
        $mensagem = "ERRO -> Número de procedimento em branco ou inválido ( APCC ).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!!!', 1 );
        exit;
    }

//------------------------------------------------------------------------------------------------------------------------------

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_apcc = $model->query( $query_apcc );

    if( $query_apcc ) {

        $lastid_apcc = $model->lastInsertId();

        if (isset($proced) and $proced == 1){

            $mensagem = "[ ATUALIZAÇÃO DE APCC ]\n Atualização de APCC. Número $num_apcc \n\n $detento";

        } else if (isset($proced) and $proced == 2){

            $mensagem = "[ EXCLUSÃO DE APCC ]\n Exclusão de APCC: Número $num_apcc \n\n $detento";

            // essas duas querys são executadas para confirmar que foi excluido os atestados.
            // geralmente a integridade da FK ja exclui os dados.
            $query_del_apcc = "DELETE FROM `apcc` WHERE `idapcc` = $idapcc LIMIT 1";
            $query_del_mov_apcc = "DELETE FROM `apcc_mov` WHERE `cod_apcc` = $idapcc LIMIT 1";

            // executando a query
            $model->query( $query_del_apcc );
            $model->query( $query_del_mov_apcc );

        } else if (isset($proced) and $proced == 3){

            $valor_mov_insert = '';

            $q_mov_in = "SELECT
                              id_mov,
                              data_mov
                            FROM
                              mov_det
                            WHERE
                              id_mov IN($v_idmovin)
                            ORDER BY
                              data_mov ASC, data_add ASC";

            // executando a query
            $q_mov_in = $model->query( $q_mov_in );

            $cont_mov_in = $q_mov_in->num_rows;

            $i = 0;
            while( $d_mov_in = $q_mov_in->fetch_assoc() ) {

                ++$i;

                $idmovin     = $d_mov_in['id_mov'];
                $data_in     = "'" . $d_mov_in['data_mov'] . "'";

                $q_mov_out = "SELECT
                                  id_mov
                                FROM
                                  mov_det
                                WHERE
                                  (mov_det.cod_tipo_mov = 5 OR mov_det.cod_tipo_mov = 7) AND mov_det.data_mov >= $data_in AND mov_det.cod_detento = $iddet
                                ORDER BY
                                  mov_det.data_mov, mov_det.data_add
                                LIMIT 1";

                // executando a query
                $q_mov_out = $model->query( $q_mov_out );

                $cont_mov_out = $q_mov_out->num_rows;

                $idmovout     = 'NULL';

                if ( $cont_mov_out >= 1 ){
                    $d_mov_out = $q_mov_out->fetch_assoc();
                    $idmovout     = $d_mov_out['id_mov'];
                }

                $valor_mov_insert .= "($lastid_apcc, $idmovin, $idmovout),";

            }

            $valor_mov_insert = substr($valor_mov_insert, 0, -1);

            $q_mov_apcc = "INSERT INTO `apcc_mov` (`cod_apcc`, `cod_movin`, `cod_movout`) VALUES $valor_mov_insert";

            // executando a query
            $q_mov_apcc = $model->query( $q_mov_apcc );

            if( $q_mov_apcc ) {

                $mensagem = "[ CADASTRAMENTO DE APCC ]\n Cadastro de APCC: ID: $lastid_apcc; Número: $numero_apcc \n\n $detento";

            // SE NÃO CONSEGUIR GRAVAR A MOVIMENTAÇÃO
            } else {

                /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
                $valor_user = valor_user( $_POST );

                if ( !empty( $idnumapcc ) ) {
                    $q_del_num = "DELETE FROM `numeroapcc` WHERE `idnumapcc` = $idnumapcc";
                    // executando a query
                    $model->query( $q_del_num );
                }

                if ( !empty( $lastid_apcc ) ) {
                    $q_del_num = "DELETE FROM `apcc` WHERE `idapcc` = $lastid_apcc";
                    // executando a query
                    $model->query( $q_del_num );
                }

                $mensagem = "[ <font color='#FF0000'><b>*** ERRO ***</b></font> ]\n Erro de cadastramento de APCC - FALHA NA INSERÇÃO DE MOVIMENTAÇÃO.\n\n $detento \n\n $valor_user \n [ MENSAGEM MYSQL ]\n $erromysql.";
                $alerta = $msg_f_cad;

                salvaLog($mensagem);
                echo msg_js( $alerta, $n_ret );

                exit;

            }

        }
        salvaLog($mensagem);

        if (isset($proced) and $proced == '1'){ // atualização
            echo msg_js( '', 2 );
        } else if (isset($proced) and $proced == '2'){ // exclusão
            echo msg_js( '', $n_ret );
        } else if (isset($proced) and $proced == '3'){ // cadastramento
            ?>
            <script type="text/javascript" src="<?php echo SICOP_ABS_PATH ?>js/funcoes.js"></script>
            <script type="text/javascript"> javascript: ow ('../print/apcc.php?idapcc=<?php echo $lastid_apcc; ?>', '600', '600'); focus();</script>
            <script type="text/javascript"> location.href="<?php echo SICOP_ABS_PATH ?>buscadet.php?proced=cadapcc";</script>
            <?php
        }
        exit;
    } else {

        if (isset($proced) and $proced == 1){
            $mensagem = "[ <font color='#FF0000'><b>*** ERRO ***</b></font> ]\n Erro de atualização de APCC. Número $num_apcc \n\n $detento \n";
            $alerta = $msg_f_atu;
        } else if (isset($proced) and $proced == 2){
            $mensagem = "[ <font color='#FF0000'><b>*** ERRO ***</b></font> ]\n Erro de exclusão de APCC. Número $num_apcc \n\n $detento \n";
            $alerta = $msg_f_exc;
        } else if (isset($proced) and $proced == 3){

            /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
            $valor_user = valor_user( $_POST );

            if ( !empty( $idnumapcc ) ) {
                $q_del_num = "DELETE FROM `numeroapcc` WHERE `idnumapcc` = $idnumapcc";
                $model->query( $q_del_num );
            }

            $mensagem = "[ <font color='#FF0000'><b>*** ERRO ***</b></font> ]\n Erro de cadastramento de APCC.\n\n $detento \n\n $valor_user \n";
            $alerta = $msg_f_cad;

        }
        salvaLog($mensagem);
        echo msg_js( $alerta, $n_ret );
        exit;
    }

    // fechando a conexao
    $model->closeConnection();

//------------------------------------------------------------------------------------------------------------------------------*/

} else {
    $mensagem = "<font color='#FF0000'><b>*** ATENÇÃO ***</b></font> -> Tentativa de acesso direto à página de manipulação de APCC.\n\n Página: $pag";
    salvaLog($mensagem);
    header('Location: ../home.php');
    exit;
}
?>
</body>
</html>
