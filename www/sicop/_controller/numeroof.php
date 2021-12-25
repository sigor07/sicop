<?php

/**
 * controller para os números de ofício
 *
 * @author Rafael
 * @since 12/03/2012
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
class NumeroOf extends Numero{

    /**
     * construtor da classe
     */
    public function __construct() {

        $this->__set( '_type', 4 );

    }

}

?>
