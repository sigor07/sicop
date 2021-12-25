<?php

/**
 * classe para tratamento de exceções
 *
 * @author Rafael
 * @since 11/04/2012
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
class ExceptionHandler {

    public static function printException( Exception $exception ) {

        $msg = '<b>Erro tipo Exception!</b> ';

        $err_code = $exception->getCode();

        if ( $err_code == 20 ) {
            $msg .= 'FALHA NA CONSULTA!!!';
        } else if ( $err_code == 1 ) {
            $msg .= 'FALHA NA CONEXÃO COM A BASE DE DADOS!!!';
        }


        if ( SICOP_DEBUG || LUMINE_DEBUG ) {

            $msg .= '<br/><br/><b>Classe que disparou a exceção:</b>';
            $msg .= '<br/>' . get_class( $exception );

            $msg .= '<br/><br/><b>Mensagem:</b>';
            $msg .= '<br/>' . $exception->getMessage();

            $msg .= '<br/><br/><b>Trace:</b>';
            $msg .= '<br/>' . nl2br( $exception->getTraceAsString() );

            $msg .= '<br/><br/><b>Código:</b>';
            $msg .= '<br/>' . nl2br( $exception->getCode() );

            $msg .= '<br/><br/><b>Arquivo:</b>';
            $msg .= '<br/>' . nl2br( $exception->getFile() );

            $msg .= '<br/><br/><b>Linha:</b>';
            $msg .= '<br/>' . nl2br( $exception->getLine() );

            $msg . '<br/>';
        }

        echo $msg;

        //print 'caught ' . get_class( $e ) . ', code: ' . $e->getCode() . "<br />Message: " . htmlentities( $e->getMessage() ) . "\n";

    }

    public static function handleException( Exception $e ) {

        self::printException( $e );

    }

}

?>
