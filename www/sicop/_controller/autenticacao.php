<?php

/**
 * Description of autenticacao
 *
 * @author Rafael
 *
 * @since 19/01/2012
 *
 */

class autenticacao {

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

        if ( !( self::$instance instanceof autenticacao ) ) {
            self::$instance = new autenticacao();
        }

        return self::$instance;

    }

    public function getUserLevelFromSession( $session_key ) {
        return $this->userSession = !empty( $_SESSION["$session_key"] ) ? (int)$_SESSION["$session_key"] : NULL;
    }

    public function __clone() {
        trigger_error( 'Clone is not allowed.', E_USER_ERROR );

    }

}

?>
