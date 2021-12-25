<?php

setlocale( LC_ALL, 'pt_BR', 'ptb', 'pt-BR', 'pt-br', 'PT-BR', 'pt_BR.ISO-8859-1', 'pt_BR.utf-8', 'portuguese-brazil', 'bra' );
date_default_timezone_set( 'America/Sao_Paulo' );

//set_error_handler('manipuladorErros');

function manipulador_erros( $errno, $errstr='', $errfile='', $errline='' ) {

    if ( error_reporting() == 0 ) return;

    //global $_CONFIG;

    $_CONFIG['errorHandler']['siteName'] = 'SICOP';

    // Verifica se não foi chamada por uma 'exception'
    if ( func_num_args() == 5 ) {
        $exception = null;
        list($errno, $errstr, $errfile, $errline) = func_get_args();
        //$backtrace = array_reverse(debug_backtrace());
    } else {
        $exc = func_get_arg( 0 );
        $errno = $exc->getCode(); // Nome do erro
        $errstr = $exc->getMessage(); // Descrição do erro
        $errfile = $exc->getFile(); // Arquivo
        $errline = $exc->getLine(); // Linha
        //$backtrace = $exc->getTrace();
    }
    // A variável $backtrace pode ser usada para fazer um Back Trace do erro
    // "Nome" de cada tipo de erro
    $errorType = array(
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING',
        E_PARSE => 'PARSING ERROR',
        E_NOTICE => 'NOTICE',
        E_CORE_ERROR => 'CORE ERROR',
        E_CORE_WARNING => 'CORE WARNING',
        E_COMPILE_ERROR => 'COMPILE ERROR',
        E_COMPILE_WARNING => 'COMPILE WARNING',
        E_USER_ERROR => 'USER ERROR',
        E_USER_WARNING => 'USER WARNING',
        E_USER_NOTICE => 'USER NOTICE',
        E_STRICT => 'STRICT NOTICE',
        E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR'
    );


    $err = 'CAUGHT EXCEPTION';

    // Define o "nome" do erro atual
    if ( array_key_exists( $errno, $errorType ) ) {
        $err = $errorType[$errno];
    }

    // Se está ativo o LOG de erros, salva uma mensagem no log, usando o formato padrão
    if ( ini_get( 'log_errors' ) )
        error_log( sprintf( "PHP %s:  %s in %s on line %d", $err, $errstr, $errfile, $errline ) );

    $quebra = PHP_EOL;

    $msg_title = 'ERRO NO PHP';
    if ( $errno == 1024 ) {
        $msg_title = '*** ERRO ***';
    }

    // Mensagem para o email
    $mensagem = '';
    $mensagem .= '[ <span class="desc_erro_php">' . $msg_title . '</span> ]' . $quebra;
    $mensagem .= 'Site: ' . $_CONFIG['errorHandler']['siteName'] . $quebra;
    $mensagem .= 'Versão do PHP: ' . phpversion() . $quebra;
    $mensagem .= 'Tipo de erro: ' . $err . $quebra;
    $mensagem .= 'Arquivo: ' . $errfile . $quebra;
    $mensagem .= 'Linha: ' . $errline . $quebra;
    $mensagem .= '<b>Descrição:</b> ' . $errstr . $quebra;

    if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
        $mensagem .= $quebra;
        $mensagem .= '[ DADOS DO VISITANTE ]' . $quebra;
        $mensagem .= 'IP: ' . $_SERVER['REMOTE_ADDR'] . $quebra;
        if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
            $mensagem .= 'User Agent: ' . $_SERVER['HTTP_USER_AGENT'] . $quebra;
        }
    }

    if ( isset( $_SERVER['REQUEST_URI'] ) ) {

        $url = preg_match( '/HTTPS/i', $_SERVER["SERVER_PROTOCOL"] ) ? 'https' : 'http';
        $url .= '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $url = link_pag( $url, $url, 1 );
        $mensagem .= $quebra;
        $mensagem .= 'URL: ' . $url . $quebra;

    }

    if ( isset( $_SERVER['HTTP_REFERER'] ) ) {

        $ref = link_pag( $_SERVER['HTTP_REFERER'], $_SERVER['HTTP_REFERER'], 1 );
        $ref = "Referer: $ref";
        $mensagem .= $ref . $quebra;

    }

    $mensagem .= $quebra;
    $mensagem .= 'Data: ' . date( 'd/m/Y \à\s H:i:s' ) . $quebra;
    $mensagem .= $quebra;

    $user_id = get_session( 'user_id', 'int' );
    if ( !empty( $user_id ) ) {
        $mensagem .= 'Usuário: ' . $_SESSION['nome_cham'] . '. ID: ' . $user_id . $quebra;
    } else {
        $mensagem .= 'Não havia usuário conectado' . $quebra;
    }

    $mensagem .= $quebra;
    salvaLog( $mensagem ); //GRAVA O ERRO NO LOG

}

?>