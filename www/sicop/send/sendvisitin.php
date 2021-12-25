<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';
$mensagem = '';

$is_post = is_post();

if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página de manipulação de registro de entrada de visitantes.';
    get_msg( $msg, 1 );

    redir( 'home' );
    exit;

}

extract( $_POST, EXTR_OVERWRITE );

$targ   = empty( $targ ) ? 0 : 1;
$proced = empty( $proced ) ? '' : (int) $proced; // NÚMERO DE PROCEDIMENTO: 2 = EXCLUSÃO; 3 = CADASTRAMENTO

if ( empty( $proced ) or $proced > 3 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Número de procedimento em branco ou inválido. Operação cancelada ( REGISTRO DE ENTRADA DE VISITANTES ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$user  = get_session( 'user_id', 'int' );
$ip    = "'" . $_SERVER['REMOTE_ADDR'] . "'";
$n_ret = 1;

if ( $proced == 1 ) { //SAÍDA
/*
 * -------------------------------------------------------------------
 * PARTE RESPONSAVEL PELA SAÍDA
 * -------------------------------------------------------------------
 */

    $num_seq = empty( $num_seq ) ? '' : (int)$num_seq;
    if ( empty( $num_seq ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'Número de sequência de entrada em branco. Operação cancelada ( REGISTRO DE SAÍDA DE VISITANTES ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    //
    $q_reg = "SELECT `cod_visita` FROM `visita_mov` WHERE `visita_mov`.`num_seq` = $num_seq AND DATE( `visita_mov`.`data_in` ) = DATE( NOW() )";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_reg = $model->query( $q_reg );

    // fechando a conexao
    $model->closeConnection();

    $idv = '';
    while ( $d_visit = $q_reg->fetch_assoc() ) {
        $idv .= $d_visit['cod_visita'] . ',';
    }
    $idv = substr( $idv, 0, -1 );

    // pegar os dados dos visitantes
    $where_visit = "IN( $idv )";
    $visita = dados_visit_wl( $where_visit );

    // pegar os dados do preso
    $where_det = "( SELECT `cod_detento` FROM `visitas` WHERE `idvisita` IN( $idv ) LIMIT 1 )";
    $detento = dados_det( $where_det );

    $q_visit_out = "UPDATE
                      `visita_mov`
                    SET
                      `data_out` = NOW(),
                      `user_out` = $user,
                      `ip_out` = $ip
                    WHERE
                      `visita_mov`.`num_seq` = $num_seq
                      AND
                      DATE( `visita_mov`.`data_in` ) = DATE( NOW() )";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_visit_out = $model->query( $q_visit_out );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( !$q_visit_out ) {

        $success = FALSE;

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = " Erro de registro de saída de visitante. Sequência: $num_seq. \n\n $visita \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'REGISTRO DE SAÍDA DE VISITANTE';
    $msg['text']     = "Registro de saída de visitante. Número de sequência: $num_seq. \n\n $visita \n\n $detento";
    get_msg( $msg, 1 );

    echo msg_js( '', 2 );

    exit;


/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELA SAÍDA
 * -------------------------------------------------------------------
 */
} else if ( $proced == 2 ) { //EXCLUSÃO
/*
 * -------------------------------------------------------------------
 * PARTE RESPONSAVEL PELA EXCLUSÃO
 * -------------------------------------------------------------------
 */

    $n_ret = 3;
    $num_seq = empty( $num_seq ) ? '' : (int)$num_seq;
    if ( empty( $num_seq ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'Número de sequência de entrada em branco. Operação cancelada ( EXCLUSÃO DE REGISTRO DE ENTRADA DE VISITANTES ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    //
    $q_reg = "SELECT `cod_visita` FROM `visita_mov` WHERE `visita_mov`.`num_seq` = $num_seq AND DATE( `visita_mov`.`data_in` ) = DATE( NOW() )";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_reg = $model->query( $q_reg );

    // fechando a conexao
    $model->closeConnection();

    $idv = '';
    while ( $d_visit = $q_reg->fetch_assoc() ) {
        $idv .= $d_visit['cod_visita'] . ',';
    }
    $idv = substr( $idv, 0, -1 );

    // pegar os dados dos visitantes
    $where_visit = "IN( $idv )";
    $visita = dados_visit_wl( $where_visit );

    // pegar os dados do preso
    $where_det = "( SELECT `cod_detento` FROM `visitas` WHERE `idvisita` IN( $idv ) LIMIT 1 )";
    $detento = dados_det( $where_det );

    $query_visit_in = "DELETE FROM `visita_mov` WHERE `num_seq` = $num_seq AND DATE( `data_in` ) = DATE( NOW() )";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_visit_in = $model->query( $query_visit_in );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( $query_visit_in ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']     = 'desc';
        $msg['entre_ch'] = 'EXCLUSÃO DE REGISTRO DE ENTRADA DE VISITANTE';
        $msg['text']     = "Exclusão de registro de entrada de visitante. Número de sequência: $num_seq. \n\n $visita \n\n $detento";

        $mensagem = get_msg( $msg );

    } else {

        $success = FALSE;

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = " Erro de exclusão de entrada de visitante. Sequência: $num_seq. \n\n $visita \n\n $detento.";
        $msg['linha'] = __LINE__;

        $mensagem = get_msg( $msg );

    }

    salvaLog( $mensagem );

    $msg_saida = '';

    if ( !$success ) {

        $msg_saida = 'FALHA';

    }

    echo msg_js( $msg_saida, $n_ret );
    exit;


/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELA EXCLUSÃO
 * -------------------------------------------------------------------
 */
} else if ( $proced == 3 ) { //CADASTRAMENTO
/*
 * -------------------------------------------------------------------
 * PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */

    $idvisit = empty( $idvisit ) ? '' : $idvisit;
    if ( empty( $idvisit ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'Identificador do visitante em branco. Operação cancelada ( CADASTRAMENTO DE REGISTRO DE ENTRADA DE VISITANTES ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $jumbo = empty($jumbo) ? 0 : (int) $jumbo;

    //$idv = implode(", ", $_POST['idvisit']);
    // gerar o número de quantas paginas vai retornar apos o cadastramento
    // se ouver mais de 1 vista, tem que voltar 3, senão volta 2
    $n_visit = count( $idvisit );
    $n_ret = 2;
    if ($n_visit > 1) $n_ret = 3;

    // variavel para ser usada no comparador IN()
    $idv = '';
    foreach ( $idvisit as $indice => $valor ) {
        if ( (int)$valor == NULL ) continue;
        $idv .= (int)$valor . ',';
    }

    // verifica se após o foreach, a variavel ficou fazia
    if ( empty( $idv ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'Após validação, a variável ficou fazia. Operação cancelada ( CADASTRAMENTO DE REGISTRO DE ENTRADA DE VISITANTES - variavel idv ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', $n_ret );
        exit;

    }

    // retirar a ultima virgula
    $idv = substr( $idv, 0, -1 );

    // contar quantos visitantes com 12 anos ou mais estao entrando
    $q_visit_in_adult = "SELECT COUNT( `idvisita` ) AS `total` FROM `visitas` WHERE `idvisita` IN( $idv ) AND FLOOR(DATEDIFF(CURDATE(), `nasc_visit`)/365.25) >= 12";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    (int)$adult_entrando = $model->fetchOne( $q_visit_in_adult );

    // contar quantos visitantes com 12 anos ou mais já entraram
    $q_visit_on_adult = "SELECT
                           COUNT( `visitas`.`idvisita` )
                         FROM
                           `visita_mov`
                           INNER JOIN `visitas` ON `visita_mov`.`cod_visita` = `visitas`.`idvisita`
                         WHERE
                           DATE( `visita_mov`.`data_in` ) = DATE( NOW() )
                           AND
                           `visitas`.`cod_detento` = ( SELECT `cod_detento` FROM `visitas` WHERE `idvisita` IN( $idv ) LIMIT 1 )
                           AND
                           FLOOR( DATEDIFF( CURDATE(), `visitas`.`nasc_visit` )/365.25) >= 12";

    // executando a query
    (int)$adult_entrou = $model->fetchOne( $q_visit_on_adult );

    // se o número de maiores de 12 anos que ja entraram mais o número de maiores de 12 anos que vão entrar for maior do que 2
    if ( ( $adult_entrando + $adult_entrou ) > 2 ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'Tentativa de registro de mais de 2 visitantes adultos para ' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . '. Operação cancelada ( CADASTRAMENTO DE REGISTRO DE ENTRADA DE VISITANTES ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'Número de visitantes adultos excede o limite de 2 por ' . SICOP_DET_DESC_L . '!\nA entrada não foi autorizada para o(s) visitante(s)', $n_ret );
        exit;

    }

    // pegar os dados dos visitantes que estão entrando
    $where_visit = "IN( $idv )";
    $visita = dados_visit_wl( $where_visit );

    // pegar os dados do preso
    $where_det = "( SELECT `cod_detento` FROM `visitas` WHERE `idvisita` IN( $idv ) LIMIT 1 )";
    $detento = dados_det( $where_det );

    // pegar o id do raio do detento
    $query_det = "SELECT
                    `raio`.`idraio`
                  FROM
                    `detentos`
                    LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                    LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
                  WHERE
                    `iddetento` = ( SELECT `cod_detento` FROM `visitas` WHERE `idvisita` IN( $idv ) LIMIT 1 )
                  LIMIT 1";

    // salva o raio para gravar no registro de entrada do visitante
    (int)$raio_det = $model->fetchOne( $query_det );


    $q_lock = 'LOCK TABLES `visitas` WRITE, `visita_mov` WRITE, `visita_mov` `vm` WRITE, `visita_susp` WRITE';

    // executa o travamento das tabelas
    $q_lock = $model->query( $q_lock );

    if ( !$q_lock ) {
        echo msg_js( 'FALHA!', $n_ret );
        exit;
    }


    // pegar o numero da sequencia de entrada
    $query_num_seq = 'SELECT IFNULL( MAX( `vm`.`num_seq` ), 0 ) + 1 AS num_seq FROM `visita_mov` `vm` WHERE DATE( `data_in` ) = DATE( NOW() ) LIMIT 1';
    $num_seq = $model->fetchOne( $query_num_seq );

    if ( !$num_seq ) {
        echo msg_js( 'FALHA!', $n_ret );
        exit;
    }

    // montar a clausa VALUES da query de inserção
    $valores_in = '';
    $jumbo_v = $jumbo;
    foreach ( $idvisit as $indice => $valor ) {

        if ( (int)$valor == NULL ) continue;
        $idvisita = (int)$valor;

        $q_v_adult = "SELECT COUNT( `idvisita` ) AS `adult` FROM `visitas` WHERE `idvisita` = $idvisita AND FLOOR( DATEDIFF( CURDATE(), `nasc_visit` )/365.25) >= 18";
        $adult     = $model->fetchOne( $q_v_adult );

        $v_adult = 'FALSE';
        $jumbo_v = 0;

        if ( $adult == 1 ) {

            $v_adult = 'TRUE';

            if ( $jumbo == 1 ) $jumbo_v = 1;

        }

        $valores_in .= " ( $idvisita, $num_seq, $jumbo_v, $v_adult, $raio_det, NOW(), $user, $ip ),";
    }

    // verifica se após a validação, a variavel ficou fazia
    if ( empty( $valores_in ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'Após validação, a variável ficou fazia. Operação cancelada ( CADASTRAMENTO DE REGISTRO DE ENTRADA DE VISITANTES - VARIAVEL valores_in ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', $n_ret );
        exit;

    }

    // retirar a última virgula
    $valores_in = substr( $valores_in, 0, -1 );

    $query_visit_in = "INSERT INTO
                         `visita_mov`
                         (
                           `cod_visita`,
                           `num_seq`,
                           `jumbo`,
                           `adulto`,
                           `raio_det`,
                           `data_in`,
                           `user_in`,
                           `ip_in`
                          )
                       VALUES
                         $valores_in";

    // executando a query
    $query_visit_in = $model->query( $query_visit_in );

    $success = TRUE;
    if( $query_visit_in ) {

        $lastid = $model->lastInsertId();

        $msg = array();
        $msg['tipo']     = 'desc';
        $msg['entre_ch'] = 'REGISTRO DE ENTRADA DE VISITANTE';
        $msg['text']     = "Cadastro de entrada de visitante: ID: $lastid; Número de sequência: $num_seq. \n\n $visita \n\n $detento";

        $mensagem = get_msg( $msg );

    } else {

        $success = FALSE;

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de cadastramento de entrada de visitante.\n\n $visita \n\n $detento.";
        $msg['linha'] = __LINE__;

        $mensagem = get_msg( $msg );

    }

    $q_unlock = 'UNLOCK TABLES';

    // executa o destravamento das tabelas
    $q_unlock = $model->query( $q_unlock );

    // fechando a conexao
    $model->closeConnection();

    salvaLog( $mensagem );

    if ( $success ) {

        ?>
        <script type="text/javascript" src="<?php echo SICOP_ABS_PATH ?>js/funcoes.js"></script>
        <script type="text/javascript">
            javascript: ow ('../print/ent_visit.php?num_seq=<?php echo $num_seq; ?>', '600', '600');
            location.href='../buscadet.php?proced=regrol';
        </script>
        <?php

    } else {

        echo msg_js( 'FALHA!', $n_ret );

    }

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */
}
?>
</body>
</html>
