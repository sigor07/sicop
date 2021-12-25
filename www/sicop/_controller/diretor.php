<?php

/**
 * Description of diretor
 *
 * @author Rafael
 * @since 07/03/2012
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
class Diretor {


    /**
     * o id do diretor
     * @access public
     * @var string
     */
    public $_did;

    /**
     * o nome do diretor
     * @access public
     * @var string
     */
    public $_nome;

    /**
     * o título do diretor
     * @access public
     * @var string
     */
    public $_titulo;

    /**
     * construtor da classe
     */
    public function __construct( $uid ) {

        $uid = (int)$uid;

        if ( !empty ( $uid ) ) $this->__set( '_did', $uid );

    }

    public function __set( $name, $value ) {
        return $this->$name = $value;
    }

    public function __get( $name ) {
        return $this->$name;
    }

    public function findDiretor( $uid = '' ) {

        $uid = (int)$uid;

        if ( empty ( $uid ) ) $uid = $this->__get ( '_did' );

        if ( empty ( $uid ) ) return FALSE;

        // instanciando o model
        $diretormodel = new DiretorModel();

        // pegando os dados
        $query = $diretormodel->getDiretor( $uid );

        if ( !$query ) return FALSE;

        $dados = '';
        $dados = $query->fetch_object();

        $this->__set( '_nome', $dados->diretor );

        $this->__set( '_titulo', $dados->titulo_diretor );

        return $dados;

    }

    public static function findTitulos() {

        // instanciando o model
        $diretormodel = new DiretorModel();

        // pegando os dados
        $query = $diretormodel->getDiretoresTitulo();

        if ( !$query ) return FALSE;

        return $query;

    }






}

?>
