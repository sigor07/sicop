<?php

/**
 * Description of UserModel
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
class UserModel {

    /**
     * a instância da classe
     * @access private
     * @var string
     */
    private static $instance;

    /**
     * construtor da classe
     */
    private function __construct() {

    }

    /**
     * instanciador da classe
     * @return bool a classe instânciada
     */
    public static function getInstance() {

        if ( !( self::$instance instanceof UserModel ) ) {
            self::$instance = new UserModel();
        }

        return self::$instance;

    }

    public function __clone() {
        trigger_error( 'Clone is not allowed.', E_USER_ERROR );

    }

    /**
     * retorna o campo ativo da tabela sicop_users
     * @param int $iduser o id do usuário
     * @return o valor do campo, ou false em caso de falha na consulta
     */
    public function getUserActive( $iduser ) {

        $iduser = (int)$iduser;

        if ( empty ( $iduser ) ) return false;

        $query = "SELECT `ativo` FROM `sicop_users` WHERE `iduser` = $iduser LIMIT 1";

        // pegar a instancia da conexão
        $model = SicopModel::getInstance();
        $cont_user = $model->fetchOne( $query );
        $model->closeConnection();

        return $cont_user;

    }

    /**
     * pega o nível de acesso do usuário, diretamente da tabela
     * @param int $iduser o id do usuário
     * @param string $setor o setor que se quer o nível de acesso
     * @return o valor do campo, ou false em caso de falha na consulta
     */
    public function getUserLvl( $iduser, $setor ) {

        $iduser = (int)$iduser;

        if ( empty ( $iduser ) or empty ( $setor ) ) return false;

        $query = "SELECT
                    `sicop_users_perm`.cod_nivel
                  FROM
                    `sicop_users_perm`
                    INNER JOIN `sicop_n_setor` ON `sicop_users_perm`.`cod_n_setor` = `sicop_n_setor`.`id_n_setor`
                  WHERE
                    `sicop_users_perm`.`cod_user` = $iduser
                    AND
                    `sicop_n_setor`.`n_setor` = '$setor'
                  LIMIT 1";

        // pegar a instancia da conexão
        $model = SicopModel::getInstance();
        $lvl = $model->fetchOne( $query );
        $model->closeConnection();

        return $lvl;

    }

    public function getUnreadMsg( $iduser ){

        $iduser = (int)$iduser;

        if ( empty ( $iduser ) ) return false;

        $query = "SELECT COUNT( `msg`.`idmsg` ) FROM `msg` WHERE `msg_para` = $iduser AND `msg_para_lida` = FALSE AND `msg_para_exc` = FALSE  AND `msg_block` = FALSE";

        $model = SicopModel::getInstance();
        $cont_msg = $model->fetchOne( $query );
        $model->closeConnection();

        return $cont_msg;

    }

}

?>
