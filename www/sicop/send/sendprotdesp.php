<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    extract($_POST, EXTR_OVERWRITE);

    $user      = get_session( 'user_id', 'int' );
    $ip        = "'" . $_SERVER['REMOTE_ADDR'] . "'";

	if ( empty( $prot ) ) {
		$mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n O usuário não marcou nenhum documento (DESPACHO DO PROTOCOLO).\n\n Página: $pag";
		salvaLog( $mensagem );
		echo msg_js( 'FALHA!', 1 );
		exit;
	}

	// monta a variavel para o comparador IN()
	$v_prot = '';
	foreach ( $prot as $indice => $valor ) {
		$valor = (int)$valor;
		if ( empty( $valor ) ) continue;
		$v_prot .= (int)$valor . ',';
	}

	if ( empty( $v_prot ) ) {
		$mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Após validação, o array ficou vazio. (DESPACHO DO PROTOCOLO).\n\n Página: $pag";
		salvaLog( $mensagem );
		echo msg_js( 'FALHA!', 1 );
		exit;
	}

	// RETIRAR A ULTIMA VIRGULA
	$v_prot = substr($v_prot, 0, -1);

	$marq_como = '';

	if ( !empty( $dps ) ) { // dps  = despachar para os setores

		$q_up_prot = "UPDATE
                                `protocolo`
                              SET
                                `prot_despachado` = TRUE,
                                `prot_data_hora_desp` = NOW(),
                                `user_up` = $user,
                                `data_up` = NOW(),
                                `ip_up` = $ip
                              WHERE
                                `idprot` IN( $v_prot )";

		$marq_como = 'DESPACHADO(S)';

	} else if ( !empty( $rec ) ) { // irc  = marcar como recebido / receber

		$q_up_prot = "UPDATE
                                `protocolo`
                              SET
                                `prot_user_rec` = $user,
                                `prot_data_hora_rec` = NOW(),
                                `user_up` = $user,
                                `data_up` = NOW(),
                                `ip_up` = $ip
                              WHERE
                                `idprot` IN( $v_prot )";

		$marq_como = 'RECEBIDO(S)';

	} else if ( !empty( $crb ) ) { // crb = cancelar recebimento

		$q_up_prot = "UPDATE
                                `protocolo`
                              SET
                                `prot_user_rec` = NULL,
                                `prot_data_hora_rec` = NULL,
                                `user_up` = $user,
                                `data_up` = NOW(),
                                `ip_up` = $ip
                              WHERE
                                `idprot` IN( $v_prot )";

		$marq_como = 'CANCELADO O RECEBIMENTO';

	} else if ( !empty( $cdp ) ) { // cdp = cancelar despacho

		$q_up_prot = "UPDATE
                                `protocolo`
                              SET
                                `prot_despachado` = FALSE,
                                `prot_data_hora_desp` = NULL,
                                `prot_user_rec` = NULL,
                                `prot_data_hora_rec` = NULL,
                                `user_up` = $user,
                                `data_up` = NOW(),
                                `ip_up` = $ip
                              WHERE
                                `idprot` IN( $v_prot )";

		$marq_como = 'CANCELADO O RECEBIMENTO';

	}

	$q_s_prot = "SELECT
                       `protocolo`.`idprot`,
                       `protocolo`.`prot_num`,
                       `protocolo`.`prot_ano`,
                       `protocolo`.`prot_assunto`,
                       `protocolo`.`prot_origem`,
                       DATE_FORMAT ( `protocolo`.`prot_data_in`, '%d/%m/%Y' ) AS prot_data_in_f,
                       DATE_FORMAT ( `protocolo`.`prot_hora_in`, '%H:%i' ) AS prot_hora_in_f,
                       `tipo_prot_doc`.`tipo_doc`
                     FROM
                       `protocolo`
                       LEFT JOIN `tipo_prot_doc` ON `protocolo`.`prot_cod_tipo_doc` = `tipo_prot_doc`.`id_tipo_doc`
                     WHERE
                       `protocolo`.`idprot` IN( $v_prot )";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_s_prot = $model->query( $q_s_prot );

	$d_prot_up = '';
	while( $d_s_prot = $q_s_prot->fetch_assoc() ) {
		$idprot         = $d_s_prot['idprot'];
		$prot_num       = $d_s_prot['prot_num'];
		$prot_ano       = $d_s_prot['prot_ano'];
		$prot_assunto   = $d_s_prot['prot_assunto'];
		$prot_origem    = $d_s_prot['prot_origem'];
		$prot_data_in_f = $d_s_prot['prot_data_in_f'];
		$prot_hora_in_f = $d_s_prot['prot_hora_in_f'];
		$tipo_doc       = $d_s_prot['tipo_doc'];
		$d_prot_up .= "<b>ID:</b> $idprot, <b>Número:</b> $prot_num/$prot_ano, <b>Tipo de documento:</b> $tipo_doc, <b>Assunto:</b> $prot_assunto; <b>Origem:</b> $prot_origem, <b>Data / hora:</b> $prot_data_in_f às $prot_hora_in_f; \n";
	}


//------------------------------------------------------------------------------------------------------------------------------

    // executando a query
    $q_up_prot = $model->query( $q_up_prot );

    if( $q_up_prot ) {

        $mensagem = "[ ATUALIZAÇÃO DE DOCUMENTOS ]\n Atualização de documentos do protocolo. \n\n [ DADOS DOS DOCUMENTOS ATUALIZADOS ]\n $d_prot_up \n Marcados como: $marq_como";
        salvaLog($mensagem);
        echo msg_js( '', 1 );

    } else {

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de atualização de documentos do protocolo.\n\n [ DADOS DOS DOCUMENTOS ]\n $d_prot_up \n <b>Marcados como:</b> $marq_como \n\n";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!', 1 );

    }

    // fechando a conexao
    $model->closeConnection();

    exit;

//------------------------------------------------------------------------------------------------------------------------------*/

} else {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de manipulação de despachos de protocolo.\n\n Página: $pag";
    salvaLog($mensagem);
    header('Location: ../home.php');
    exit;
}
?>
</body>
</html>
