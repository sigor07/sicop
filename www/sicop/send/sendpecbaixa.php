<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    extract($_POST, EXTR_OVERWRITE);

    $user        = get_session( 'user_id', 'int' );
    $ip          = "'" . $_SERVER['REMOTE_ADDR'] . "'";

    $msg_f = 'FALHA!';

    $iddet = empty($iddet) ? '' : (int)$iddet;

    if ( empty( $iddet ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. Operação cancelada (BAIXA PECÚLIO).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!', 2 );
        exit;
    }

    if ( empty( $idpec ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n O usuário não marcou nenhum pertence (BAIXA DE PERTENCES/PECÚLIO).\n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'Você deve marcar pelo menos um item!', 1 );
        exit;
    }

    // pegar os dados do preso
    $detento = dados_det( $iddet );

    $obs_ret = empty($obs_ret) ? 'NULL' : "'" . tratastring($obs_ret) . "'";

    if ( !empty( $idpec ) ) {
        // monta a variavel para o comparador IN()
        $v_idpec = '';
        foreach ( $idpec as $indice => $valor ) {
            if ( (int)$valor == NULL ) continue;
            $v_idpec .= (int)$valor . ',';
        }

        if ( empty( $v_idpec ) ) {
            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Após validação, o array ficou vazio. (BAIXA DE PERTENCES/PECÚLIO).\n\n Página: $pag";
            salvaLog( $mensagem );
            echo msg_js( 'FALHA!', 1 );
            exit;
        }

        $v_idpec = substr($v_idpec, 0, -1);

        $q_pec = "UPDATE
                    `peculio`
                  SET
                    `retirado` = 1,
                    `obs_ret` = $obs_ret,
                    `user_up` = $user,
                    `data_up` = NOW(),
                    `ip_up` = $ip
                  WHERE
                    `idpeculio` IN($v_idpec)";
    }

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_pec = $model->query( $q_pec );

    // fechando a conexao
    $model->closeConnection();

    if( $q_pec ) {

        // pegar os dados do pertence
        $q_s_pec = "SELECT
                      `peculio`.`idpeculio`,
                      `peculio`.`descr_peculio`,
                      `tipopeculio`.`tipo_peculio`
                    FROM
                      `peculio`
                      INNER JOIN `tipopeculio` ON `peculio`.`cod_tipo_peculio` = `tipopeculio`.`idtipopeculio`
                    WHERE
                      `peculio`.`idpeculio` IN($v_idpec)";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_s_pec = $model->query( $q_s_pec );

        // fechando a conexao
        $model->closeConnection();

        $peculio = '';
        while( $d_s_pec = $q_s_pec->fetch_assoc() ) {
            $idp = $d_s_pec['idpeculio'];
            $descr_peculio = $d_s_pec['descr_peculio'];
            $tipo_peculio = $d_s_pec['tipo_peculio'];
            $peculio .= "<b>ID:</b> $idp; <b>Tipo:</b> $tipo_peculio; <b>Descrição:</b> $descr_peculio \n";
        }

        $mensagem = "[ BAIXA DE PERTENCES ]\n Baixa em pertences; \n\n [ PERTENCES MARCADOS ]\n $peculio \n $detento";
        salvaLog($mensagem);

        echo msg_js( '', 2 );

    } else {

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro em baixa de pertences.\n\n $detento.";
        salvaLog($mensagem);

        echo msg_js( 'FALHA', 1 );

    }

    exit;

} else {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de baixa de pertences.\n\n Página: $pag";
    salvaLog($mensagem);
    redir( 'home' );
    exit;
}
?>
</body>
</html>