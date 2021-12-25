<?php

/**
 * Description of audienciasmodel
 *
 * @author Rafael
 * @since 06/03/2012
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
class AudienciaModel {

    /**
     * o id do detento
     * @access private
     * @var string
     */
    private $_uid;

    /**
     * query para as impressõe dos oficios das audiências
     * @var constant
     */
    const q_aud_print = "SELECT
                            `audiencias`.`idaudiencia`,
                            `audiencias`.`cod_detento`,
                            `audiencias`.`cod_num_of`,
                            `audiencias`.`data_aud`,
                            DATE_FORMAT(`audiencias`.`data_aud`, '%d/%m/%Y') AS data_aud_f,
                            `audiencias`.`hora_aud`,
                            DATE_FORMAT(`audiencias`.`hora_aud`, '%H:%i') AS hora_aud_f,
                            `audiencias`.`local_aud`,
                            `audiencias`.`cidade_aud`,
                            `audiencias`.`tipo_aud`,
                            `audiencias`.`num_processo`,
                            `detentos`.`nome_det`,
                            `detentos`.`cod_artigo`,
                            `detentos`.`matricula`,
                            `detentos`.`rg_civil`,
                            `detentos`.`execucao`,
                            `detentos`.`cpf`,
                            `detentos`.`nasc_det`,
                            DATE_FORMAT(`detentos`.`nasc_det`, '%d/%m/%Y') AS nasc_det_f,
                            FLOOR(DateDiff(CurDate(), `detentos`.`nasc_det`) / 365.25) AS idade_det,
                            `detentos`.`pai_det`,
                            `detentos`.`mae_det`,
                            `tipoartigo`.`artigo`,
                            `cidades`.`nome` AS cidade,
                            `estados`.`sigla` AS estado,
                            `det_fotos`.`foto_det_g`,
                            `det_fotos`.`foto_det_p`
                          FROM
                            `audiencias`
                            INNER JOIN `detentos` ON `audiencias`.`cod_detento` = `detentos`.`iddetento`
                            LEFT JOIN `tipoartigo` ON `detentos`.`cod_artigo` = `tipoartigo`.`idartigo`
                            LEFT JOIN `cidades` ON `detentos`.`cod_cidade` = `cidades`.`idcidade`
                            LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado`
                            LEFT JOIN `det_fotos` ON `det_fotos`.`id_foto` = `detentos`.`cod_foto`";

    /**
     * query para as impressõe dos oficios das audiências
     * @var constant
     */
    const q_of_model = 'SELECT
                          `idmodel`,
                          `dest_sup`,
                          `corpo`,
                          `referente`,
                          `prostetos`,
                          `tratamento`,
                          `dest_inf`
                        FROM
                          `model_of_apr`';


    /**
     * construtor da classe
     */
    public function __construct( $uid = '' ) {

        $uid = (int)$uid;

        if ( !empty ( $uid ) ) {
            $this->__set( '_uid', $uid );
        }

    }

    public function __set( $name, $value ) {
        return $this->$name = $value;
    }

    public function __get( $name ) {
        return $this->$name;
    }

    public function getAudPrintOf( $uid_in = '' ){

        $uid = $uid_in;

        if ( empty ( $uid ) ) $uid = (int)$this->_uid;

        if ( empty ( $uid ) ) return FALSE;

        $query  = self::q_aud_print;
        $query .= "WHERE
                     `audiencias`.`idaudiencia` IN ( $uid )
                   ORDER BY
                     `audiencias`.`data_aud` ASC, `audiencias`.`cidade_aud` ASC, `audiencias`.`local_aud` ASC, `audiencias`.`hora_aud` ASC";

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

    public function getOfModel( $uid = '' ){

        $uid = (int)$uid;

        if ( empty ( $uid ) ) return FALSE;

        $query  = self::q_of_model;
        $query .= "WHERE
                     `idmodel` = $uid
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

    public function upCodOf( $uid, $cod_num_of ) {

        $uid = (int)$uid;

        if ( empty ( $uid ) ) $uid = (int)$this->_uid;

        if ( empty ( $uid ) ) return FALSE;

        $query  = "UPDATE
                     `audiencias`
                   SET
                     `cod_num_of` = $cod_num_of
                   WHERE
                     `idaudiencia` = $uid";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $query = $model->query( $query );

        // fechando a conexao
        $model->closeConnection();

        if ( !$query ) return FALSE;

        return $query;

    }



}

?>
