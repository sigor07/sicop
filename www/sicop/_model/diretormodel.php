<?php

/**
 * Description of DiretoresModel
 *
 * @author Rafael
 * @since 16/02/2012
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
class DiretorModel {

    /**
     * construtor da classe
     */
    public function __construct() {

    }

    public function getDiretor( $uid ) {

        $uid = (int)$uid;

        if ( empty ( $uid ) ) return FALSE;

        $query = "SELECT
                    `diretor`,
                    `titulo_diretor`
                   FROM
                     `diretores_n`
                   WHERE
                     `iddiretoresn` = $uid
                   LIMIT 1";

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

    public function getDiretoresTitulo() {

        $query = "SELECT `idsetor`, `titulo_diretor` FROM `sicop_setor` ORDER BY `titulo_diretor`";

        $model = SicopModel::getInstance();

        // executa a query
        $query = $model->query( $query );

        // fecha a conexão com o banco
        $model->closeConnection();

        return $query;

    }




}

?>
