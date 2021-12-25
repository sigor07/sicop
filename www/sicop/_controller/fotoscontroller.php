<?php

/**
 * Description of fotosController
 *
 * @author Rafael
 *
 * @since 06/02/2012
 *
 */
class fotosController extends sicopController {

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

        if ( !( self::$instance instanceof fotosController ) ) {
            self::$instance = new fotosController();
        }

        return self::$instance;

    }

    public function __clone() {
        trigger_error( 'Clone is not allowed.', E_USER_ERROR );

    }

}

?>
