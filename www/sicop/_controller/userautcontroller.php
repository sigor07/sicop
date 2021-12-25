<?php

/**
 * para autenticação, verificação e validação de usuários
 *
 * @author Rafael
 *
 * @since 06/02/2012
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
class UserAutController extends SicopController {

    /**
     * o id do usuário
     * @access private
     * @var int
     */
    private $_iduser;

    /**
     * construtor da classe
     */
    public function __construct() {

        $this->setIdUser();

    }

    /**
     * para definir a propriedade _iduser
     * @access public
     * @var int
     */
    public function setIdUser() {
        $this->_iduser = $this->getUidFromSession();
    }

    /**
     * para verificar se o usuário está ativo
     * acessa o model para verificação
     * @access public
     * @return boolean
     */
    public function ckUserActive() {

        $iduser = (int)$this->_iduser;

        if ( empty ( $iduser ) ) return false;

        $user  = UserModel::getInstance();
        $ativo = $user->getUserActive( $iduser );

        $retorno = false;
        if ( $ativo == 1 ) $retorno = true;

        return $retorno;

    }

    /**
     * para validar o acesso a uma determinada página.
     * @param string $setor o nome do setor que será comparado o nível de acesso
     * @param int $nivel_necessario no nível necessário para validação
     * @param string $type_ass SOMENTE SE $setor FOR ARRAY - o tipo de associação: af => all false - todas false - só retorna false se todas permissões forem false; of => one false - uma false - retorna false se uma das permissões for false, ou seja, devem ser todas verdadeiras --- um ou outro false = false | um e outro false = false
     * @param int $type_ret o tipo de retorno, em caso de erro, usando pela função handleReturn()
     */
    public function validateUser( $setor, $nivel_necessario = 2, $type_ass = 'af', $type_ret = 1 ) {

        $iduser = (int)$this->_iduser;

        /**
         * se $iduser for vazio, é porque não há usuário logado, então destroi a sessão e
         * redireciona para a página de login
         */
        if ( empty ( $iduser ) ) {
            $this->destroySession();
            exit;
        }

        // verificar se o usuário está ativo
        $this->ckUserActive( $iduser );

        $access = true;
        if ( is_array( $setor ) ) {

            foreach ( $setor as $value ) {

                // pega o nível do usuário no respectivo setor
                $nivel_user = $this->getUserLvl( $value );

                if ( $type_ass == 'of' ) {

                    if ( $nivel_user < $nivel_necessario ) {

                        // se qualquer nivel for < necessário, o usuário ja está bloqueado
                        $access = false;
                        break;

                    }

                } else { // se o $type_ass for all false...

                    $access = false;
                    
                    // se o nivel do usuário for >= necessario
                    if ( $nivel_user >= $nivel_necessario ) {

                        // se qualquer nivel for >= necessário, o usuário ja tem acesso
                        $access = true;
                        break;

                    }

                }

            }

        } else {

            // pega o nível do usuário no respectivo setor
            $nivel_user = $this->getUserLvl( $setor );

            if ( $nivel_user < $nivel_necessario ) {
                $access = false;
            }


        }
        // se o nível do usuário for menor do que o nível necessário...
        if ( !$access ) {

            // montar a mensagem q será salva no log
            $msg = sysmsg::create_msg();
            $msg->set_msg_type( SM_TYPE_ATEN );
            $msg->set_msg_pre_def( SM_NO_PERM );
            $msg->get_msg();

            $sys = new SicopController();
            echo $sys->handleReturn( $type_ret );

            exit;

        }

    }

    /**
     * para pegar o nível do usuário no setor nas sessions
     * @param string $setor o setor que se quer pegar o nível
     * @return int o nível do setor solicitado
     */
    public function getUserLvl( $setor ) {

        return $this->getSession( $setor, 'int' );

    }

    public function getUserLvlFromModel( $setor ) {

        $iduser = (int)$this->_iduser;

        if ( empty ( $iduser ) ) return false;

        $setor = $this->trataBusca( $setor );

        if ( empty ( $setor ) ) return false;

        $user  = UserModel::getInstance();
        $lvl = $user->getUserLvl( $iduser, $setor );

        return $lvl;

    }

    /**
     * verifica se o usuário está logado, verificadno se a propriedade $_iduser
     * não é vazia
     * @return o iduser ou false se o usuário não estiver logado
     */
    public function isLoged() {

        $iduser = (int)$this->_iduser;

        if ( empty ( $iduser ) ) {
            return false;
        } else {
            return $iduser;
        }

    }

    /**
     * pega o id do usuário diretamente da session
     * @return int o id do usuário ou vazio se não achar
     */
    public function getUidFromSession() {
        return $this->getSession( 'user_id', 'int' );
    }

    /**
     * pega o id do usuário diretamente da session
     * @return int o id do usuário ou vazio se não achar
     */
    public function getIdsetorFromSession() {
        return SicopController::getSession( 'idsetor', 'int' );
    }


    /**
     * destrói a session e redireciona o usuário para a página de login
     * @param string $msg a mensagem que será gravada no log
     */
    public function destroySession( $msg = '' ) {

        // Destrói a sessão
        session_destroy();

        $msg_error = 'Session destruida!!!';

        if ( !empty ( $msg ) ) {
            $msg_error = $msg;
        }

        // dispara um erro E_USER_NOTICE e manda uma msg junto
        // que será gravada no log
        trigger_error( $msg_error );

        $this->redir( 'index' );

        exit;

    }

}

?>
