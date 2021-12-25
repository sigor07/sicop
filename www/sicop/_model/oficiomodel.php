<?php

/**
 * model para ofícios
 *
 * @author Rafael
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
class OficioModel extends model {

    /**
     * os campos deste model
     * @access private
     * @var array
     */
    protected $_fields = array(
        'cod_setor',
        'nome_doc',
        'ref_doc',
        'local_data',
        'tipo_quali',
        'saud_sup',
        'texto_doc',
        'protesto',
        'trat_doc',
        'ass_doc',
        'senhoria_doc',
        'nome_dest_doc',
        'cargo_doc',
        'cidade_doc',
        'recibo_doc'
    );

    /**
     * construtor da classe
     */
    public function __construct() {

        $this->__set( '_table_name', 'oficios_modelos' );

    }

    /**
     * insere valores no banco, usando um array de valores
     * @param array $values os valores que serão inseridos. os indices devem ser os nomes dos campos
     * @return int o id da inserção, ou false em caso de falha
     */
    public function insertModel( $values ) {

        $fieds = $this->_fields;

        return $this->insert( $fieds, $values );

    }

    public function getTiposRecibo() {

        $query = "SELECT `uid`, `tipo_recibo` FROM `tipo_recibo` ORDER BY `tipo_recibo`";

        $model = SicopModel::getInstance();

        // executa a query
        $query = $model->query( $query );

        // fecha a conexão com o banco
        $model->closeConnection();

        return $query;

    }

    public function getTiposTratamento() {

        $query = "SELECT `uid`, `tipo_tratamento` FROM `tipo_tratamento` ORDER BY `tipo_tratamento`";

        $model = SicopModel::getInstance();

        // executa a query
        $query = $model->query( $query );

        // fecha a conexão com o banco
        $model->closeConnection();

        return $query;

    }

    public function getModelos() {

        $user     = new userAutController();
        $user_id  = $user->getUidFromSession();
        $setor_id = $user->getIdsetorFromSession();

        //$this->_table_name;

        $query = "SELECT `id_of_model`, `nome_doc` FROM `oficios_modelos` WHERE `cod_setor` = $setor_id AND `user_add` = $user_id";

        $model = SicopModel::getInstance();

        // executa a query
        $query = $model->query( $query );

        // fecha a conexão com o banco
        $model->closeConnection();

        return $query;

    }

}

?>
