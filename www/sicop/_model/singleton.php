<?php

/**
 * classe de abstração para singleton
 * Data 10/02/2012
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
abstract class Singleton {

    protected static $_instance = NULL;

    /**
     * Prevent direct object creation
     */
    final private function __construct() {

    }

    /**
     * Prevent object cloning
     */
    final private function __clone() {

    }

    /**
     * Returns new or existing Singleton instance
     * @return Singleton
     */
    final public static function getInstance() {
        if ( null !== static::$_instance ) {
            return static::$_instance;
        }
        static::$_instance = new static();
        return static::$_instance;

    }

}

?>
