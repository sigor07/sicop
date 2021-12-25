<?php

/**
 * model para os númeradores
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
class NumeroModel {

    /**
     * o id do número
     * @access public
     * @var string
     */
    public $nid;

    /**
     * o número
     * @access public
     * @var int
     */
    public $num;

    /**
     * o ano do número
     * @access public
     * @var int
     */
    public $ano;

    /**
     * o nome da tabela do número
     * @access private
     * @var string
     */
    private $_table;

    /**
     * o nome da tabela temporária que será usada para
     * a consulta na inserção
     * @access private
     * @var string
     */
    private $_table_temp = 'num';

    /**
     * o nome do campo do número
     * @access private
     * @var string
     */
    private $_field_num;

    /**
     * o nome do campo do id do número
     * @access private
     * @var string
     */
    private $_field_id;



    /**
     * construtor da classe
     */
    public function __construct( $type = '' ) {

        $type = (int)$type;

        if ( empty ( $type ) ) $type = 1;

        $this->setTableNames( $type );

    }

    public function __set( $name, $value ) {
        return $this->$name = $value;
    }

    public function __get( $name ) {
        return $this->$name;
    }

    public function newNum( $iduser, $idsetor, $coment = null ) {

        if ( empty ( $coment ) ) $coment = 'NULL';

        $tabela     = $this->_table;
        $tabela_tmp = $this->_table_temp;
        $campo_num  = $this->_field_num;

        $query = "INSERT INTO
                    `$tabela`
                    (
                      `$campo_num`,
                      `ano`,
                      `iduser`,
                      `idsetor`,
                      `coment`
                    )
                  VALUES
                    (
                      ( SELECT IFNULL( MAX( `$tabela_tmp`.`$campo_num` ), 0 ) FROM `$tabela` `$tabela_tmp` WHERE `ano` = YEAR( NOW() ) ) + 1,
                      YEAR( NOW() ),
                      $iduser,
                      $idsetor,
                      $coment
                    )";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $query = $model->query( $query );

        if ( !$query ) return FALSE;

        $last_id = $model->lastInsertId();

        // fechando a conexao
        $model->closeConnection();

        return $last_id;

    }

    public function getNum( $uid ) {

        $uid = (int)$uid;

        if ( empty ( $uid ) ) return FALSE;

        $tabela     = $this->_table;
        $campo_num  = $this->_field_num;
        $campo_id   = $this->_field_id;

        $query = "SELECT `$campo_num` AS numero, `ano` FROM `$tabela` WHERE `$campo_id` = $uid";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $query = $model->query( $query );

        // fechando a conexao
        $model->closeConnection();

        if ( !$query ) return FALSE;

        $num_rows = $query->num_rows;

        if ( $num_rows < 1 ) return FALSE;

        return $query;

    }

    private function setTableNames( $type ) {

        $tabela     = '';
        $campo_num  = '';
        $campo_id   = '';

        switch ( $type ) {
            default:
            case 1:
                $tabela     = 'numeroapcc';
                $campo_num  = 'numero_apcc';
                $campo_id   = 'idnumapcc';
                break;

            case 2:
                $tabela     = 'numerofax';
                $campo_num  = 'numero_fax';
                $campo_id   = 'idnumfax';
                break;

            case 3:
                $tabela     = 'numeronotes';
                $campo_num  = 'numero_notes';
                $campo_id   = 'idnumnotes';
                break;

            case 4:
                $tabela     = 'numeroof';
                $campo_num  = 'numero_of';
                $campo_id   = 'idnumof';
                break;

            case 5:
                $tabela     = 'numeroreq';
                $campo_num  = 'numero_req';
                $campo_id   = 'idnumreq';
                break;

            case 6:
                $tabela     = 'numerorms';
                $campo_num  = 'numero_rms';
                $campo_id   = 'idnumrms';
                break;

        }

        $this->__set( '_table', $tabela );

        $this->__set( '_field_num', $campo_num );

        $this->__set( '_field_id', $campo_id );

    }



}

?>
