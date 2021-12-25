<?php

/**
 * Description of validastr
 *
 * @author Rafael
 *
 * @since 16/01/2012
 *
 */
class validastr {

    /**
     * a instância da classe
     * @access private
     * @var string
     */
    private static $instance;

    /**
     * a string a ser tratada
     * @access private
     * @var string
     */
    private $str;

    /**
     * construtor da classe
     */
    private function __construct( $msg ) {

        $this->str = $msg;

    }

    /**
     * instanciador da classe
     * @return bool a classe instânciada
     */
    public static function getInstance( $msg ) {

        if ( !( self::$instance instanceof validastr ) ) {
            self::$instance = new validastr( $msg );
        }

        return self::$instance;

    }

    public function rem_double_space() {

        return preg_replace( '/[ ]{2,}/', ' ', $this->str );

    }

    public function trim_space() {

        return trim( $this->str );

    }

    public function rem_space() {
        $this->rem_double_space();
        $this->trim_space();
        return;
    }

    public function remove_acentos() {

        $string = $this->str;

        // Remove acentos sobre a string
        mb_internal_encoding( 'UTF-8' );
        mb_regex_encoding( 'UTF-8' );
        $string = mb_ereg_replace( '[ÁÀÂÃÄ]', 'A', $string );
        $string = mb_ereg_replace( '[áàâãäªa]', 'a', $string );
        $string = mb_ereg_replace( '[ÉÈÊË]', 'E', $string );
        $string = mb_ereg_replace( '[éèêëe]', 'e', $string );
        $string = mb_ereg_replace( '[ÍÌÎÏ]', 'I', $string );
        $string = mb_ereg_replace( '[íìîïi]', 'i', $string );
        $string = mb_ereg_replace( '[ÓÒÔÕÖ]', 'O', $string );
        $string = mb_ereg_replace( '[óòôõöºo]', 'o', $string );
        $string = mb_ereg_replace( '[ÚÙÛÜ]', 'U', $string );
        $string = mb_ereg_replace( '[úùûüu]', 'u', $string );
        $string = mb_ereg_replace( '[Ç]', 'C', $string );
        $string = mb_ereg_replace( '[ç]', 'c', $string );
        $string = mb_ereg_replace( '[´`~^¨]', '', $string );
        $string = mb_ereg_replace( '&acute;', '', $string );
        //$string = strtoupper($string);

        $this->str = $string;

        return $this->str;

    }

    public function l_case() {

        return mb_strtolower( $this->str, 'UTF-8' );

    }

    public function u_case() {

        return mb_strtoupper( $this->str, 'UTF-8' );

    }

    public function escape_str() {

        $db        = SicopModel::getInstance();
        $this->str = $db->escape_string( $this->str );
        $db->closeConnection();

        return $this->str;

    }

    public function __clone() {
        trigger_error( 'Clone is not allowed.', E_USER_ERROR );
    }

}

?>
