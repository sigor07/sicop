<?php

/**
 * descrição do arquivo
 * @since 17/02/2012
 *
 * ****************************************************************************
 *
 * SICOP - Sistema de Controle de Prisional
 *
 * Sistema para controle e gerenciamento de unidades prisionais
 *
 * @author  JOSÉ RAFAEL GONÇALVES - AGENTE DE SEGURANÇA III
 * @local   CENTRO DE DETENÇÃO PROVISÓRIA DE SÃO JOSÉ DO RIO PRETO - SP
 * @since   03/01/2011
 *
 * ****************************************************************************
 */

if ( !isset( $_SESSION ) ) session_start();

error_reporting(E_ALL);
ini_set('display_errors', TRUE);

require '../init/config.php';
//require 'incl_ajax.php';

$pag = SicopController::linkPag();
$tipo = '';
$mensagem = '';

/* colocar o tipo de página, o setor que ela acessa
 * ex: PECÚLIO, CADASTRO, INCLUSÃO...
 */
$tipo_pag = 'MODELOS DE OFÍCIO';

/*
 * define a variavel $n_acesso de acordo com o setor
 */

// instanciando a classe
$user = new userAutController();

// checando se o sistema esta ativo
$user->ckSys();

// validando o usuário e o nível de acesso
$user->validateUser( 'n_adm', 3, '', 2 );

// instanciando o controller
$control = new SicopController();

// verificando se o acesso foi por post
$control->ckPost( 1 );

// checando o $proced
$proced = $control->getPost( 'proced', 'int' );
$control->ckProced( $proced, 3, 1 );


if ( $proced == 1 ) { // ATUALIZAÇÃO
    /*
     * -------------------------------------------------------------------
     * PARTE RESPONSAVEL PELA ATUALIZAÇÃO
     * -------------------------------------------------------------------
     */

    /*
     * aqui coloca a função da página
     */
    $proced_tipo_pag = 'ATUALIZAÇÃO - ' . $tipo_pag;


    /*
     *
     *
     *
     * ESPAÇO PARA COLOCAR A QUERY E VALIDAÇÕES
     *
     *
     *
     */

    $db = SicopModel::getInstance();
    $query = $db->query( $query );

    $success = TRUE;
    if ( !$query ) {

        $success = FALSE;

        // pegar a mensagem de erro mysql
        $msg_err_mysql = $db->getErrorMsg();
        $db->closeConnection();

        // pegar os valores inseridos no fomulário
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = sysmsg::create_msg();
        $msg->set_msg_type( SM_TYPE_ERR );
        $msg->add_quebras( 1 );
        $msg->set_msg( 'Erro de atualização' );
        $msg->add_parenteses( $tipo_pag );
        $msg->add_quebras( 2 );
        $msg->set_msg( $detento );
        $msg->add_quebras( 2 );
        $msg->set_msg( $valor_user );
        $msg->add_quebras( 2 );
        $msg->set_msg( $msg_err_mysql );
        $msg->get_msg();

        echo msg_js( 'FALHA!', $ret );

        exit;
    }

    // montar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->add_chaves( 'ATUALIZAÇÃO DE ', 0, 1 );
    $msg->set_msg( "Atualização de ." );
    $msg->add_quebras( 2 );
    $msg->set_msg( /* dados do que foi manipulado */ );
    $msg->add_quebras( 2 );
    $msg->set_msg( $detento );
    $msg->get_msg();

    echo msg_js( '', $ret );

    exit;

    /*
     * -------------------------------------------------------------------
     * FIM DA PARTE RESPONSAVEL PELA ATUALIZAÇÃO
     * -------------------------------------------------------------------
     */
}

if ( $proced == 2 ) { //EXCLUSÃO
    /*
     * -------------------------------------------------------------------
     * PARTE DO CODIGO RESPONSAVEL PELA EXCLUSÃO
     * -------------------------------------------------------------------
     */

    /*
     * aqui coloca a função da página
     */
    $proced_tipo_pag = 'EXCLUSÃO - ' . $tipo_pag;

    if ( empty( $n_acesso ) or $n_acesso < 4 ) {

        // montar a mensagem q será salva no log
        $msg = sysmsg::create_msg();
        $msg->set_msg_type( SM_TYPE_ATEN );
        $msg->set_msg_pre_def( SM_NO_PERM );
        $msg->add_parenteses( $proced_tipo_pag );
        $msg->get_msg();

        echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

        exit;
    }

    /*
     *
     *
     *
     * ESPAÇO PARA COLOCAR A QUERY E VALIDAÇÕES
     *
     *
     *
     */

    $db = SicopModel::getInstance();
    $query = $db->query( $query );

    $success = TRUE;
    if ( !$query ) {

        $success = FALSE;

        // pegar a mensagem de erro mysql
        $msg_err_mysql = $db->getErrorMsg();
        $db->closeConnection();

        // pegar os valores inseridos no fomulário
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = sysmsg::create_msg();
        $msg->set_msg_type( SM_TYPE_ERR );
        $msg->add_quebras( 1 );
        $msg->set_msg( 'Erro de exclusão' );
        $msg->add_parenteses( $tipo_pag );
        $msg->add_quebras( 2 );
        $msg->set_msg( $detento );
        $msg->add_quebras( 2 );
        $msg->set_msg( $valor_user );
        $msg->add_quebras( 2 );
        $msg->set_msg( $msg_err_mysql );
        $msg->get_msg();

        echo msg_js( 'FALHA!', $ret );

        exit;
    }

    // montar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->add_chaves( 'EXCLUSÃO DE ', 0, 1 );
    $msg->set_msg( "Exclusão de ." );
    $msg->add_quebras( 2 );
    $msg->set_msg( /* dados do que foi manipulado */ );
    $msg->add_quebras( 2 );
    $msg->set_msg( $detento );
    $msg->get_msg();

    echo msg_js( '', $ret );

    exit;

    /*
     * -------------------------------------------------------------------
     * FIM DA PARTE DO CODIGO RESPONSAVEL PELA EXCLUSÃO
     * -------------------------------------------------------------------
     */
}


/**
 * -------------------------------------------------------------------
 * PARTE DO CODIGO RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */
if ( $proced == 3 ) { //CADASTRAMENTO


    /*
     * aqui coloca a função da página
     */
    $proced_tipo_pag = 'CADASTRAMENTO - ' . $tipo_pag;

    //sleep(3);

    $values = array();

    $values['cod_setor'] = $user->getIdsetorFromSession();

    $op = array(
        'method'         => 'post',        // metodo que a variável será recebida
        'name'           => 'nome_doc',    // nome da variável
        'modo_validacao' => 'string',      // modo de validação
        'maxLeng'        => 80,            // comprimento máximo
        'minLeng'        => 5,             // comprimento mínimo
        'required'       => true,          // se é requerida ou não
        'return_type'    => 1              // tipo de retorno em caso de erro
    );
    $values['nome_doc'] = $control->validate( $op );

    $op = array(
        'method'         => 'post',       // metodo que a variável será recebida
        'name'           => 'ref_doc',    // nome da variável
        'modo_validacao' => 'int',        // modo de validação
        'maxLeng'        => 1,            // comprimento máximo
        'minLeng'        => 1,            // comprimento mínimo
        'required'       => true,         // se é requerida ou não
        'zero_ok'        => true,         // em caso de requerida, se pode ser o número 0 (zero)
        'return_type'    => 1             // tipo de retorno em caso de erro
    );
    $values['ref_doc'] = $control->validate( $op );


    $op = array(
        'method'         => 'post',       // metodo que a variável será recebida
        'name'           => 'local_data', // nome da variável
        'modo_validacao' => 'int',        // modo de validação
        'maxLeng'        => 1,            // comprimento máximo
        'minLeng'        => 1,            // comprimento mínimo
        'required'       => true,         // se é requerida ou não
        'zero_ok'        => true,         // em caso de requerida, se pode ser o número 0 (zero)
        'return_type'    => 1             // tipo de retorno em caso de erro
    );
    $values['local_data'] = $control->validate( $op );

    $op = array(
        'method'         => 'post',       // metodo que a variável será recebida
        'name'           => 'tipo_quali', // nome da variável
        'modo_validacao' => 'int',        // modo de validação
        'maxLeng'        => 1,            // comprimento máximo
        'minLeng'        => 1,            // comprimento mínimo
        'required'       => true,         // se é requerida ou não
        'zero_ok'        => true,         // em caso de requerida, se pode ser o número 0 (zero)
        'return_type'    => 1             // tipo de retorno em caso de erro
    );
    $values['tipo_quali'] = $control->validate( $op );

    $op = array(
        'method'         => 'post',      // metodo que a variável será recebida
        'name'           => 'saud_sup',  // nome da variável
        'modo_validacao' => 'basico',    // modo de validação
        'maxLeng'        => 50,          // comprimento máximo
        'minLeng'        => 5,           // comprimento mínimo
        'required'       => true,        // se é requerida ou não
        'return_type'    => 1            // tipo de retorno em caso de erro
    );
    $values['saud_sup'] = $control->validate( $op );

    $op = array(
        'method'         => 'post',      // metodo que a variável será recebida
        'name'           => 'texto_doc', // nome da variável
        'modo_validacao' => 'basico',    // modo de validação
        'minLeng'        => 5,           // comprimento mínimo
        'required'       => true,        // se é requerida ou não
        'return_type'    => 1            // tipo de retorno em caso de erro
    );
    $values['texto_doc'] = $control->validate( $op );

    $op = array(
        'method'         => 'post',       // metodo que a variável será recebida
        'name'           => 'protesto',   // nome da variável
        'modo_validacao' => 'int',        // modo de validação
        'maxLeng'        => 1,            // comprimento máximo
        'minLeng'        => 1,            // comprimento mínimo
        'required'       => true,         // se é requerida ou não
        'zero_ok'        => true,         // em caso de requerida, se pode ser o número 0 (zero)
        'return_type'    => 1             // tipo de retorno em caso de erro
    );
    $values['protesto'] = $control->validate( $op );

    $op = array(
        'method'         => 'post',       // metodo que a variável será recebida
        'name'           => 'trat_doc',   // nome da variável
        'modo_validacao' => 'int',        // modo de validação
        'maxLeng'        => 1,            // comprimento máximo
        'minLeng'        => 1,            // comprimento mínimo
        'required'       => true,         // se é requerida ou não
        'zero_ok'        => true,         // em caso de requerida, se pode ser o número 0 (zero)
        'return_type'    => 1             // tipo de retorno em caso de erro
    );
    $values['trat_doc'] = $control->validate( $op );


    $op = array(
        'method'         => 'post',       // metodo que a variável será recebida
        'name'           => 'ass_doc',   // nome da variável
        'modo_validacao' => 'int',        // modo de validação
        'maxLeng'        => 2,            // comprimento máximo
        'minLeng'        => 1,            // comprimento mínimo
        'required'       => true,         // se é requerida ou não
        'zero_ok'        => true,         // em caso de requerida, se pode ser o número 0 (zero)
        'return_type'    => 1             // tipo de retorno em caso de erro
    );
    $values['ass_doc'] = $control->validate( $op );

    $op = array(
        'method'         => 'post',          // metodo que a variável será recebida
        'name'           => 'senhoria_doc',  // nome da variável
        'modo_validacao' => 'basico',        // modo de validação
        'maxLeng'        => 50,              // comprimento máximo
        'minLeng'        => 5,               // comprimento mínimo
        'required'       => true,            // se é requerida ou não
        'return_type'    => 1                // tipo de retorno em caso de erro
    );
    $values['senhoria_doc'] = $control->validate( $op );

    $op = array(
        'method'         => 'post',          // metodo que a variável será recebida
        'name'           => 'nome_dest_doc', // nome da variável
        'modo_validacao' => 'basico',        // modo de validação
        'maxLeng'        => 50,              // comprimento máximo
        //'minLeng'        => 5,               // comprimento mínimo
        //'required'       => true,            // se é requerida ou não
        'return_type'    => 1                // tipo de retorno em caso de erro
    );
    $values['nome_dest_doc'] = $control->validate( $op );

    $op = array(
        'method'         => 'post',          // metodo que a variável será recebida
        'name'           => 'cargo_doc',     // nome da variável
        'modo_validacao' => 'basico',        // modo de validação
        'maxLeng'        => 50,              // comprimento máximo
        'minLeng'        => 5,               // comprimento mínimo
        'required'       => true,            // se é requerida ou não
        'return_type'    => 1                // tipo de retorno em caso de erro
    );
    $values['cargo_doc'] = $control->validate( $op );

    $op = array(
        'method'         => 'post',          // metodo que a variável será recebida
        'name'           => 'cidade_doc',    // nome da variável
        'modo_validacao' => 'basico',        // modo de validação
        'maxLeng'        => 50,              // comprimento máximo
        'minLeng'        => 5,               // comprimento mínimo
        'required'       => true,            // se é requerida ou não
        'return_type'    => 1                // tipo de retorno em caso de erro
    );
    $values['cidade_doc'] = $control->validate( $op );

    $op = array(
        'method'         => 'post',          // metodo que a variável será recebida
        'name'           => 'recibo_doc',    // nome da variável
        'modo_validacao' => 'int',           // modo de validação
        'maxLeng'        => 1,               // comprimento máximo
        'minLeng'        => 1,               // comprimento mínimo
        'required'       => true,            // se é requerida ou não
        'zero_ok'        => true,            // em caso de requerida, se pode ser o número 0 (zero)
        'return_type'    => 1                // tipo de retorno em caso de erro
    );
    $values['recibo_doc'] = $control->validate( $op );

    $model   = new OficioModel();
    $last_id = $model->insertModel( $values );


    $success = TRUE;
    // se não tiver $last_id é porque a query de inserção falhou
    if ( !$last_id ) {

        $success = FALSE;

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = $control->valorUser( $_POST );

        // montar a mensagem q será salva no log
        $msg = sysmsg::create_msg();
        $msg->set_msg_type( SM_TYPE_ERR );
        $msg->add_quebras( 1 );
        $msg->set_msg( 'Erro de cadastramento ' );
        $msg->add_parenteses( $proced_tipo_pag );
        $msg->add_quebras( 2 );
        $msg->set_msg( $valor_user );
        $msg->get_msg();

        echo 0;

        exit;
    }

    // montar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->add_chaves( 'CADASTRAMENTO DE MODELO DE DOCUMENTO', 0, 1 );
    $msg->set_msg( 'Cadastramento de modelo de ofício.' );
    //$msg->add_quebras( 2 );
    //$msg->set_msg( /* dados do que foi manipulado */ );
    $msg->get_msg();

    echo 1;

    exit;

    /*
     * -------------------------------------------------------------------
     * FIM DA PARTE RESPONSAVEL PELO CADASTRAMENTO
     * -------------------------------------------------------------------
     */
}
?>
