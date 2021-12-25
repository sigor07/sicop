<?php

/**
 * Description of getvars
 *
 * @author Rafael
 *
 * @since 16/01/2012
 *
 */
class getvars {

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

        if ( !( self::$instance instanceof getvars ) ) {
            self::$instance = new getvars();
        }

        return self::$instance;

    }

    public function valida_int( $var ) {

        $var = (int)$var;

        return $var;

    }

    public function valida_str( $var ) {

        $var = (int)$var;

        return $var;

    }

    public static function get_post( $key ) {

        return !empty( $_POST["$key"] ) ? $_POST["$key"] : NULL;

    }

    public static function get_get( $key ) {

        return !empty( $_GET["$key"] ) ? $_GET["$key"] : NULL;

    }

     public static function get_session( $key ) {

        return !empty( $_SESSION["$key"] ) ? $_SESSION["$key"] : NULL;

    }

    public function to_sql( $str ) {

        return is_null( $str ) ? 'NULL' : "'" . $str . "'";

    }




    public function __clone() {
        trigger_error( 'Clone is not allowed.', E_USER_ERROR );

    }

}

?>
