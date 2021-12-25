<?php

/**
 * classe com valores comuns a todas as classes de acesso ao banco
 *
 * @author Rafael
 * @since 26/03/2012
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
class model {

    /**
     * o nome da tabela
     * @access protected
     * @var string
     */
    protected $_table_name;

    /**
     * os campos usados na inclusão de dados
     * @access public
     * @var bool
     */
    protected $_fields_add = array(
        '`user_add`',
        '`data_add`',
        '`ip_add`'
    );

    /**
     * os campos usados na atualização de dados
     * @access public
     * @var bool
     */
    protected $_fields_up = array(
        'user_up',
        'data_up',
        'ip_up'
    );

    /**
     * os dados do usuário usado nas inserções e atualizações
     * @access protected
     * @var array
     */
    protected $_user_data = array();

    /**
     * os nomes dos campos
     * @access protected
     * @var string
     */
    protected $_campos;

    /**
     * os valores dos campos
     * @access protected
     * @var string
     */
    protected $_valores;

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
     * insere valores no banco, usando um array de valores
     * @param array $values os valores que serão inseridos. os indices devem ser os nomes dos campos
     * @return int o id da inserção, ou false em caso de falha
     */
    protected function insert( $fieds, $values ) {

        $this->handleFieldsInsert( $fieds, $values );

        $query = "INSERT
                    `$this->_table_name`
                    (
                      $this->_campos
                    )
                    VALUES
                    (
                      $this->_valores
                    )";

        // instancia a classe
        $model = SicopModel::getInstance();

        // executa a query
        $query = $model->query( $query );

        // se a query falhar, retorna false
        if ( !$query ) return false;

        // pega o id da inserção
        $last_id = $model->lastInsertId();

        // fecha a conexão com o banco
        $model->closeConnection();

        return $last_id;

    }

    /**
     * exclui valores no banco, usando um array de valores
     * @param array $uid o id do que será excluido
     * @return bool o retorno da query de exclusão
     */
    protected function delete( $uid ) {

        $query = 'DELETE FROM `' . $this->_table_name . '` WHERE `' . $this->_fields[0] . "` = $uid LIMIT 1";

        // instancia a classe
        $model = SicopModel::getInstance();

        // executa a query
        $query = $model->query( $query );

        // se a query falhar, retorna false
        if ( !$query ) return false;

        // pega o id da inserção
        $last_id = $model->lastInsertId();

        // fecha a conexão com o banco
        $model->closeConnection();

        return $last_id;

    }

    /**
     * formata o user_id, a função NOW() do SQL e o
     * ip para serem inserido junto com as consultas
     * seta o valor na propriedade _user_data
     */
    public function setUserForModel() {

        $user = new userAutController();
        $user_id = $user->getUidFromSession();

        if ( empty ( $user_id ) ) return false;

        $ip  = $_SERVER['REMOTE_ADDR'];

        //$str = "'$user_id', NOW(), '$ip'";

        $data = array(
            "'$user_id'",
            'NOW()',
            "'$ip'"
        );

        $this->__set( '_user_data', $data );

    }

    /**
     * cria a associação campo => valor para inserções
     * precisa receber dois arrays, e compara o valor de $fields com
     * o indice de $values, para formar a associação
     * @param array $fields array contendo os nomes dos campos
     * @param array $values array contendo o indice com os nomes dos campos e os respectivos valores
     */
    public function handleFieldsInsert( $fields, $values ) {

        $campos = array();
        $valores = array();

        foreach ( $fields as $field ) {

            /**
             * dentro de valores, deve haver o indice com o mesmo
             * nome do campo da tabela
             *
             * se dentro de "values" existir o indice "field"
             */
            if ( isset( $values["$field"] ) ) {

                // armazena o nome do campo
                $campos[] = '`' . $field . '`';

                // pega o valor
                $value = $values["$field"];

                // armazena o valor correspondente ao campo
                $valores[] = is_null( $value ) ? 'NULL' : "'" . $value . "'";

            }

        }

        // montando os campos
        $campos = implode( ',', $campos ) . ',' . implode( ',', $this->_fields_add );

        // configurando os valores user_add, data_add, ip_add
        $this->setUserForModel();

        // montando os valores
        $valores = implode( ',', $valores ) . ','  . implode( ',',  $this->_user_data );

        // gravando as variavies em suas respectivas propriedades
        $this->__set( '_campos', $campos );
        $this->__set( '_valores', $valores );


    }

    /**
     * cria a associação campo => valor para atualizações
     * precisa receber dois arrays, e compara o valor de $fields com
     * o indice de $values, para formar a associação
     * @param array $fields array contendo os nomes dos campos
     * @param array $values array contendo o indice com os nomes dos campos e os respectivos valores
     */
    public function handleFieldsUpdate( $fields, $values ) {

        $campos = array();

        foreach ( $fields as $field ) {

            /**
             * dentro de valores, deve haver o indice com o mesmo
             * nome do campo da tabela
             *
             * se dentro de "values" existir o indice "field"
             */
            if ( isset( $values["$field"] ) ) {

                // pega o valor
                $value = $values["$field"];

                // armazena o valor correspondente ao campo
                $value = is_null( $value ) ? 'NULL' : "'" . $value . "'";

                // armazena o nome do campo
                $campos[] = '`' . $field . '` = ' . $value;

            }

        }

        // configurando os valores user_up, data_up, ip_up
        $this->setUserForModel();

        $user_up = array();

        // percorrendo os arrays para montar o user_up, data_up, ip_up
        for ( $i = 0; $i < 3; $i++ ) {

            $user_up[] = '`' . $this->_fields_up["$i"] . '` = ' . $this->_user_data["$i"];

        }

        // montando os campos
        $campos = implode( ',', $campos ) . ',' . implode( ',', $user_up );

        // gravando as variavies em suas respectivas propriedades
        $this->__set( '_campos', $campos );

    }

}

?>
