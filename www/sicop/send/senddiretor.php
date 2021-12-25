<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    extract($_POST, EXTR_OVERWRITE);

    $proced           = (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO
    $iddir            = empty($iddir) ? '' : (int)$iddir;
    $diretor          = empty($diretor) ? 'NULL' : "'".tratastring($diretor)."'";
    $titulo_diretor   = empty($titulo_diretor) ? 'NULL' : "'".tratastring($titulo_diretor, 'N', false)."'";
    $setor            = empty($setor) ? 'NULL' : "'".(int)$setor."'";
    $ativo            = empty($ativo) ? 0 : 1;
    $user             = get_session( 'user_id', 'int' );
    $url_dest         = retira_cerquilha(returnHistory());

    $msg_f_atu = 'FALHA ao atualizar!';
    $msg_f_exc = 'FALHA ao excluir!';
    $msg_f_cad = 'FALHA ao cadastrar!';

    if (!empty($iddir)){

        $q_diretor = "SELECT `diretor`, `titulo_diretor`, sicop_setor.`sigla_setor`
                      FROM `diretores_n`
                      INNER JOIN sicop_setor ON diretores_n.setor = sicop_setor.idsetor
                      WHERE `iddiretoresn` = $iddir LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_diretor = $model->query( $q_diretor );

        // fechando a conexao
        $model->closeConnection();

        $d_dir     = $q_diretor->fetch_assoc();
        $nome_dir  = $d_dir['diretor'] . ', ' . $d_dir['titulo_diretor'];
        $setor_q   = $d_dir['sigla_setor'];

    }

    /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
    $valor_user = valor_user( $_POST );

    if (isset($proced) and $proced == '1'){ // ATUALIZAÇÃO
/*
-----------------------------------------------------------
PARTE DO CODIGO RESPONSAVEL PELA ATUALIZAÇÃO DO DIRETOR
-----------------------------------------------------------
*/

        if (empty($iddir)){
            $mensagem = "ERRO -> Identificador do diretor em branco. Operação cancelada (ATUALIZAÇÃO).\n\n Página: $pag";
            salvaLog($mensagem);
            ?>
            <script type="text/javascript"> alert("<?php echo $msg_f_atu; ?>");</script>
            <script type="text/javascript"> location.href='<?php echo $url_dest ?>'; </script>
            <?php
            exit;
        }

        $query_dir = "UPDATE `diretores_n` SET
                             `diretor` = $diretor,
                             `titulo_diretor` = $titulo_diretor,
                             `setor` = $setor,
                             `ativo` = $ativo,
                             `user_up` = $user,
                              `data_up` = NOW()
                        WHERE `iddiretoresn` = $iddir LIMIT 1";

/*
-------------------------------------------------------------------
FIM DA PARTE DO CODIGO RESPONSAVEL PELA ATUALIZAÇÃO DO DIRETOR
-------------------------------------------------------------------
*/
    } else if (isset($proced) and $proced == '2'){ //EXCLUSÃO
/*
-------------------------------------------------------------------
PARTE DO CODIGO RESPONSAVEL PELA EXCLUSÃO DO DIRETOR
-------------------------------------------------------------------
*/

        if (empty($iddir)){
            $mensagem = "ERRO -> Identificador do diretor em branco. Operação cancelada (EXCLUSÃO).\n\n Página: $pag";
            salvaLog($mensagem);
            ?>
            <script type="text/javascript"> alert("<?php echo $msg_f_exc; ?>");</script>
            <script type="text/javascript"> location.href='<?php echo $url_dest ?>'; </script>
            <?php
            exit;
        }

        $query_dir = "DELETE FROM `diretores_n` WHERE `iddiretoresn` = $iddir LIMIT 1";

/*
-------------------------------------------------------------------
FIM DA PARTE DO CODIGO RESPONSAVEL PELA EXCLUSÃO DO DIRETOR
-------------------------------------------------------------------
*/
    } else if (isset($proced) and $proced == '3'){ //CADASTRAMENTO
/*
-------------------------------------------------------------------
PARTE DO CODIGO RESPONSAVEL PELO CADASTRAMENTO DO DIRETOR
-------------------------------------------------------------------
*/

        $query_dir = "INSERT INTO `diretores_n`
                                 (`diretor`,
                                  `titulo_diretor`,
                                  `setor`,
                                  `ativo`,
                                  `user_up`,
                                    `data_up`)
                            VALUES
                                 ($diretor,
                                  $titulo_diretor,
                                  $setor,
                                  $ativo,
                                  $user,
                                  NOW())";

/*
-------------------------------------------------------------------
FIM DA PARTE DO CODIGO RESPONSAVEL PELO CADASTRAMENTO DO DIRETOR
-------------------------------------------------------------------
*/
    } else if (empty($proced)) { //SE NÃO HOUVER NUMERO DE PROCEDIMENTO OU ELE NÃO FOR UM INTEIRO
        $mensagem = "ERRO -> Número de procedimento em branco ou inválido (DIRETORES).\n\n Página: $pag";
        salvaLog($mensagem);
        ?>
        <script type="text/javascript"> alert("FALHA de procedimento!");</script>
        <script type="text/javascript">location.href='<?php echo $url_dest ?>'; </script>
        <?php
        exit;
    }

//------------------------------------------------------------------------------------------------------------------------------

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_dir = $model->query( $query_dir );

    if ( $query_dir ) {

        $lastid = $model->lastInsertId();

        if ( isset( $proced ) and $proced == '1' ) {
            $mensagem = "[ ATUALIZAÇÃO DE DIRETOR ]\n Atualização de dados do diretor. \n\n $valor_user \n [ DIRETOR ]\n Nome: $nome_dir, setor $setor_q, ID $iddir.";
        } else if ( isset( $proced ) and $proced == '2' ) {
            $mensagem = "[ EXCLUSÃO DE DIRETOR ]\n Exclusão de diretor.\n\n [ DIRETOR ]\n Nome: $nome_dir, setor $setor_q, ID $iddir.";
        } else if ( isset( $proced ) and $proced == '3' ) {
            $mensagem = "[ CADASTRAMENTO DE DIRETOR ]\n Cadastro de diretor: ID: $lastid. \n\n $valor_user \n";
        }

        salvaLog( $mensagem );
        header( 'Location: ' . $url_dest );
        exit;

    } else {

        if ( isset( $proced ) and $proced == '1' ) {
            $mensagem = "[ *** ERRO *** ]\n Erro de atualização de dados do diretor.\n\n $valor_user.";
            $alerta = $msg_f_atu;
        } else if ( isset( $proced ) and $proced == '2' ) {
            $mensagem = "[ *** ERRO *** ]\n Erro de exclusão de diretor.";
            $alerta = $msg_f_exc;
        } else if ( isset( $proced ) and $proced == '3' ) {
            $mensagem = "[ *** ERRO *** ]\n Erro de cadastramento de diretor.\n\n $valor_user";
            $alerta = $msg_f_cad;
        }

        salvaLog( $mensagem );
        ?>
        <script type="text/javascript"> alert("<?php echo $alerta ?>");</script>
        <script type="text/javascript">location.href='<?php echo $url_dest ?>'; </script>
        <?php
        exit;

    }

    // fechando a conexao
    $model->closeConnection();

//------------------------------------------------------------------------------------------------------------------------------*/

} else {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de manipulação de dados de diretores.\n\n Página: $pag";
    salvaLog( $mensagem );
    header( 'Location: ../home.php' );
    exit;
}
?>
</body>
</html>