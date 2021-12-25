<?php

/**
 * controller para os numeradores
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
class Numero {

    /**
     * o id do número
     * @access public
     * @var string
     */
    public $_nid;

    /**
     * o tipo do número
     * * 1 = apcc
     * * 2 = fax
     * * 3 = notes
     * * 4 = ofício
     * * 5 = requisição
     * * 6 = remessa
     * @access protected
     * @var string
     */
    protected $_type;


    /**
     * o número
     * @access public
     * @var int
     */
    public $_num;

    /**
     * o ano do número
     * @access public
     * @var int
     */
    public $_ano;

    /**
     * o número número formatado
     * @access public
     * @var string
     */
    public $_numF;

    /**
     * construtor da classe
     */
    public function __construct() {

    }

    // setter
    public function __set( $name, $value ) {
        return $this->$name = $value;
    }

    // getter
    public function __get( $name ) {
        return $this->$name;
    }

    /**
     * gera um novo número e já grava no banco
     * @param string $coment um comentário sobre o número solicitado. Se ficar em branco, será enviado 'NULL' para o banco
     * @return string o número no formato num/ano no caso de sucesso, ou false, no caso de falha
     */
    public function getNewNum( $coment = NULL ) {

        $iduser  = SicopController::getSession( 'user_id', 'int' );
        $idsetor = SicopController::getSession( 'idsetor', 'int' );

        if ( empty ( $iduser ) or empty ( $idsetor ) ) return FALSE;

        $coment = empty( $coment ) ? 'NULL' : "'" . SicopModel::getInstance()->escape_string( $coment ) . "'";

        // pegando o tipo do número
        $type = $this->_type;

        // instanciando o model
        $nummodel = new NumeroModel( $type );

        // pegando os dados
        $nid = $nummodel->newNum( $iduser, $idsetor, $coment );

        if ( !$nid ) return FALSE;

        $this->__set( '_nid', $nid );

        return $this->findNum();

    }

    /**
     * busca um número no banco
     * @param int $uid o identificador do número. se for empty, tentará pegar a propriedade _nid. Se também for empty, retorna false
     * @return string o número no formato num/ano no caso de sucesso, ou false, no caso de falha
     */
    public function findNum( $uid = '' ) {

        $uid = (int)$uid;

        if ( empty ( $uid ) ) $uid = $this->__get ( '_nid' );

        if ( empty ( $uid ) ) return FALSE;

        // pegando o tipo do número
        $type = (int)$this->_type;

        if ( empty ( $type ) ) return FALSE;

        // instanciando o model
        $nummodel = new NumeroModel( $type );

        // pegando os dados
        $query = $nummodel->getNum( $uid );

        if ( !$query ) return FALSE;

        $dados = '';
        $dados = $query->fetch_object();

        // setando a propriedade _num
        $this->__set( '_num', $dados->numero );

        // setando a propriedade _ano
        $this->__set( '_ano', $dados->ano );

        // formatando e setando a propriedade _numF
        $this->formatNum( $dados->numero, $dados->ano );

        // retornando o número formatado
        return $this->__get( '_numF' );

    }

    private function formatNum( $num, $ano ) {

        $num_f = $num . '/' . $ano;

        $this->__set( '_numF', $num_f );

    }


}

?>
