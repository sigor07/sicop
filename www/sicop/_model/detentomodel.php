<?php

/**
 * classe model para detentos
 *
 * @author Rafael
 * @since 29/02/2012
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
class DetentoModel {

    /**
     * o id do detento
     * @access private
     * @var string
     */
    private $_iddet;

    /**
     * query para as qualificativas
     * @var constant
     */
    const q_quali = "SELECT
                       `detentos`.`iddetento`,
                       `detentos`.`nome_det`,
                       `detentos`.`matricula`,
                       `detentos`.`rg_civil`,
                       `detentos`.`execucao`,
                       `detentos`.`cpf`,
                       `detentos`.`vulgo`,
                       `detentos`.`profissao`,
                       `detentos`.`pai_det`,
                       `detentos`.`mae_det`,
                       `detentos`.`primario`,
                       `detentos`.`prisoes_ant`,
                       `detentos`.`fuga`,
                       `detentos`.`local_fuga`,
                       `detentos`.`estatura`,
                       `detentos`.`peso`,
                       `detentos`.`defeito_fisico`,
                       `detentos`.`sinal_nasc`,
                       `detentos`.`cicatrizes`,
                       `detentos`.`tatuagens`,
                       `detentos`.`resid_det`,
                       `detentos`.`possui_adv`,
                       `detentos`.`caso_emergencia`,
                       `detentos`.`obs_artigos`,
                       `detentos`.`funcionario`,
                       `detentos`.`dados_prov`,
                       `detentos`.`jaleco`,
                       `detentos`.`calca`,
                       DATE_FORMAT(`detentos`.`nasc_det`, '%d/%m/%Y') AS nasc_det_f,
                       FLOOR(DateDiff(CurDate(), `detentos`.`nasc_det`) / 365.25) AS idade_det,
                       DATE_FORMAT(`detentos`.`data_prisao`, '%d/%m/%Y') AS data_prisao,
                       `tipocutis`.`cutis`,
                       `tipocabelos`.`cabelos`,
                       `tipoartigo`.`artigo`,
                       `tiponacionalidade`.`nacionalidade`,
                       `sicop_users`.`iniciais`,
                       `tiporeligiao`.`religiao`,
                       `tipoescolaridade`.`escolaridade`,
                       `tipoolhos`.`olhos`,
                       `tipoestadocivil`.`est_civil`,
                       `tiposituacaoprocessual`.`sit_proc`,
                       `cidades`.`nome` AS cidade,
                       `estados`.`sigla` AS estado,
                       `mov_det_in`.`data_mov`,
                       `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
                       DATE_FORMAT( `mov_det_in`.`data_mov`, '%d/%m/%Y' ) AS data_incl_f,
                       `unidades`.`unidades` AS `procedencia`,
                       `det_fotos`.`foto_det_g`,
                       `det_fotos`.`foto_det_p`
                     FROM
                       `detentos`
                       LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                       LEFT JOIN `unidades` ON `mov_det_in`.`cod_local_mov` = `unidades`.`idunidades`
                       LEFT JOIN `tipocutis` ON `detentos`.`cod_cutis` = `tipocutis`.`idcutis`
                       LEFT JOIN `tipocabelos` ON `detentos`.`cod_cabelos` = `tipocabelos`.`idcabelos`
                       LEFT JOIN `tipoescolaridade` ON `detentos`.`cod_instrucao` = `tipoescolaridade`.`idescolaridade`
                       LEFT JOIN `tipoestadocivil` ON `detentos`.`cod_est_civil` = `tipoestadocivil`.`idest_civil`
                       LEFT JOIN `tipoolhos` ON `detentos`.`cod_olhos` = `tipoolhos`.`idolhos`
                       LEFT JOIN `tiporeligiao` ON `detentos`.`cod_religiao` = `tiporeligiao`.`idreligiao`
                       LEFT JOIN `tiposituacaoprocessual` ON `detentos`.`cod_sit_proc` = `tiposituacaoprocessual`.`idsit_proc`
                       LEFT JOIN `cidades` ON `detentos`.`cod_cidade` = `cidades`.`idcidade`
                       LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado`
                       LEFT JOIN `tipoartigo` ON `detentos`.`cod_artigo` = `tipoartigo`.`idartigo`
                       LEFT JOIN `tiponacionalidade` ON `detentos`.`cod_nacionalidade` = `tiponacionalidade`.`idnacionalidade`
                       LEFT JOIN `sicop_users` ON `detentos`.`funcionario` = `sicop_users`.`iduser`
                       LEFT JOIN `det_fotos` ON `det_fotos`.`id_foto` = `detentos`.`cod_foto`";

    /**
     * query para as planilhas de identificação
     * @var constant
     */
    const q_ident = "SELECT
                       `tipoartigo`.`artigo`,
                       `tipocutis`.`cutis`,
                       `detentos`.`iddetento`,
                       `detentos`.`nome_det`,
                       `detentos`.`matricula`,
                       `detentos`.`rg_civil`,
                       `detentos`.`pai_det`,
                       `detentos`.`mae_det`,
                       DATE_FORMAT(`detentos`.`nasc_det`, '%d/%m/%Y') AS `nasc_det_f`
                     FROM
                       `detentos`
                       LEFT JOIN `tipocutis` ON `detentos`.`cod_cutis` = `tipocutis`.`idcutis`
                       LEFT JOIN `tipoartigo` ON `detentos`.`cod_artigo` = `tipoartigo`.`idartigo`";

    /**
     * query para os cartões de identificação
     * @var constant
     */
    const q_cartao = "SELECT
                        `detentos`.`iddetento`,
                        `detentos`.`nome_det`,
                        `detentos`.`matricula`,
                        `detentos`.`rg_civil`,
                        `detentos`.`vulgo`,
                        `detentos`.`pai_det`,
                        `detentos`.`mae_det`,
                        `detentos`.`jaleco`,
                        `detentos`.`calca`,
                        DATE_FORMAT( `detentos`.`nasc_det`, '%d/%m/%Y' ) AS nasc_det_f,
                        FLOOR( DATEDIFF( CURDATE(), `detentos`.`nasc_det` ) / 365.25) AS idade_det,
                        `mov_det_in`.`data_mov`,
                        DATE_FORMAT(`mov_det_in`.`data_mov`, '%d/%m/%Y') AS data_incl_f,
                        `unidades`.`unidades` AS `procedencia`,
                        `det_fotos`.`foto_det_g`,
                        `det_fotos`.`foto_det_p`
                      FROM
                        `detentos`
                        LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                        LEFT JOIN `unidades` ON `mov_det_in`.`cod_local_mov` = `unidades`.`idunidades`
                        LEFT JOIN `det_fotos` ON `det_fotos`.`id_foto` = `detentos`.`cod_foto`";


    const q_print_quali_basic = "SELECT
                                   `detentos`.`nome_det`,
                                   `detentos`.`matricula`,
                                   `detentos`.`rg_civil`,
                                   `detentos`.`execucao`,
                                   `detentos`.`cpf`,
                                   DATE_FORMAT(`detentos`.`nasc_det`, '%d/%m/%Y') AS `nasc_det_f`,
                                   FLOOR(DATEDIFF(CURDATE(), `detentos`.`nasc_det`) / 365.25) AS `idade_det`,
                                   `detentos`.`pai_det`,
                                   `detentos`.`mae_det`,
                                   `cidades`.`nome` AS `cidade`,
                                   `estados`.`sigla` AS `estado`,
                                   `cela`.`cela`,
                                   `raio`.`raio`
                                 FROM
                                   `detentos`
                                   LEFT JOIN `cidades` ON `detentos`.`cod_cidade` = `cidades`.`idcidade`
                                   LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado`
                                   LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                                   LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`";


    /**
     * construtor da classe
     */
    public function __construct( $iddet = '' ) {

        $iddet = (int)$iddet;

        if ( !empty ( $iddet ) ) {
            $this->__set( '_iddet', $iddet );
        }

    }

    public function __set( $name, $value ) {
        return $this->$name = $value;
    }

    public function __get( $name ) {
        return $this->$name;
    }

    public function upFunc( $iduser ){

        $iddet = $this->_iddet;

        if ( empty ( $iddet ) ) return FALSE;

        $query = "UPDATE `detentos` SET `funcionario` = $iduser WHERE `iddetento` = $iddet LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $query = $model->query( $query );

        // fechando a conexao
        $model->closeConnection();

        return $query;

    }

    public function getDetQuali( $iddet_in = '' ){

        $iddet = $iddet_in;

        if ( empty ( $iddet ) ) $iddet = (int)$this->_iddet;

        if ( empty ( $iddet ) ) return FALSE;

        $query  = self::q_quali;
        $query .= "WHERE
                     `detentos`.`iddetento` IN( $iddet )
                   ORDER BY
                     `detentos`.`nome_det`";

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

    public function getDetIndent( $iddet_in = '' ){

        $iddet = $iddet_in;

        if ( empty ( $iddet ) ) $iddet = (int)$this->_iddet;

        if ( empty ( $iddet ) ) return FALSE;

        $query  = self::q_ident;
        $query .= "WHERE
                     `detentos`.`iddetento` IN ( $iddet )
                   ORDER BY
                     `detentos`.`nome_det`";

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

    public function getDetCartao( $iddet_in = '' ){

        $iddet = $iddet_in;

        if ( empty ( $iddet ) ) $iddet = (int)$this->_iddet;

        if ( empty ( $iddet ) ) return FALSE;

        $query  = self::q_cartao;
        $query .= "WHERE
                     `detentos`.`iddetento` IN( $iddet )
                   ORDER BY
                     `detentos`.`nome_det`";

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

    public function getDetBasic( $iddet = '' ){

        $iddet = (int)$iddet;

        if ( empty ( $iddet ) ) $iddet = (int)$this->_iddet;

        if ( empty ( $iddet ) ) return FALSE;

        $query  = self::q_print_quali_basic;
        $query .= "WHERE
                     `detentos`.`iddetento` = $iddet
                   LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $query = $model->query( $query );

        // fechando a conexao
        $model->closeConnection();

        if ( !$query ) return FALSE;

        $num_rows = $query->num_rows;

        if ( $num_rows != 1 ) return FALSE;

        return $query;

    }

    public function getDadosDet( $param = '' ){

        if ( empty( $param ) ) $param = (int)$this->_iddet;

        if ( empty( $param ) ) return FALSE;

        // instanciando o model
        $model = SicopModel::getInstance();

        $param = $model->escape_string( $param );

        $query = "SELECT
                    `iddetento`,
                    `nome_det`,
                    `matricula`
                  FROM
                    `detentos`
                  WHERE
                    `iddetento` IN( $param )";

        // executando a query
        $query = $model->query( $query );

        // fechando a conexao
        $model->closeConnection();

        if ( !$query ) return false;

        $cont = $query->num_rows;

        if ( $cont < 1 ) return false;

        return $query;

    }

    public function getPenas(){

        $iddet = $this->_iddet;

        if ( empty ( $iddet ) ) return FALSE;

        $query = "SELECT
                    SUM( `gra_p_ano` ) AS `ano`,
                    SUM( `gra_p_mes` ) AS `mes`,
                    SUM( `gra_p_dia` ) AS `dia`
                  FROM
                    `grade`
                  WHERE
                    `cod_detento` = $iddet
                    AND
                    `gra_campo_x` = false
                    AND
                    `gra_preso` = true";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $query = $model->query( $query );

        // fechando a conexao
        $model->closeConnection();

        if ( !$query ) return FALSE;

        $cont = $query->num_rows;

        if ( $cont < 1 ) return FALSE;

        return $query;


    }






}

?>
