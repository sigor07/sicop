<?php

/**
 * monta as listas de detentos para as páginas ajax
 *
 * @author Rafael
 */
class montalista {

    /**
     * a instância da classe
     * @access private
     * @var string
     */
    private static $lista;

    /**
     * configuração: se será pego automáticamete a lista da session
     * @access private
     * @var bool
     */
    private static $get_list = false;

    /**
     * configuração: se será salva automáticamete a lista na session
     * @access private
     * @var bool
     */
    private static $set_list = false;


    /**
     * a relação de detentos salva nas sessions
     * @access private
     * @var array
     */
    private $lista_atual = array();

    /**
     * o nome da chave da session
     * @access private
     * @var string
     */
    private static $session_name = 'user_list';


    private function __construct() {

        if ( self::$get_list ) {
            $this->get_lista_atual();
        }

    }

    /**
     * instanciador da classe
     * @param bool $get_list se vai pegar a lista da session ou não
     * @param bool $set_list se vai setar a lista na session ou não
     * @param string $session_name o nome da chave da session
     * @return bool a classe instânciada
     */
    public static function create_list( $get_list = true, $set_list = true, $session_name = 'user_list' ) {

        if ( $get_list ) {
            self::$get_list = true;
        }

        if ( $set_list ) {
            self::$set_list = true;
        }

        if ( !empty ( $session_name ) ) {
            self::$session_name = $session_name;
        }

        if ( !( self::$lista instanceof montalista ) ) {
            self::$lista = new montalista();
        }

        return self::$lista;

    }

    /**
     * pega a lista da session
     * @return string a lista com o id acrescentado
     */
    function get_lista_atual() {

        $session_name = self::$session_name;

        $lista = get_session( "$session_name" );

        if ( !is_array( $lista ) ) {
            $lista = explode( ',', $lista);
        }

        return $this->lista_atual = $lista;

    }

    /**
     * salva a lista na session
     * @return string a lista com o id acrescentado
     */
    function set_lista_atual() {

        $session_name = self::$session_name;

        $lista = $this->lista_atual;

        if ( !is_array( $lista ) ) {
            $lista = explode( ',', $lista);
        }

        return $_SESSION["$session_name"] = $lista;

    }

    /**
     * adiciona um id na lista
     * @param int $id o id do detento que será adicionado na lista
     * @return string a lista com o id acrescentado
     */
    function add( $id ) {

        $id = (int)$id;

        if ( !empty ( $id ) ) {
            if ( !in_array( $id, $this->lista_atual ) ) {
                $this->lista_atual[] = $id;
            }
        }

        if ( self::$set_list ) $this->set_lista_atual();

        return $this->lista_atual;

    }

    /**
     * remove um id na lista
     * @param int $id o id do detento que será removido da lista
     * @return string a lista com o id removido
     */
    function rem( $id ) {

        $id = (int)$id;
        $key = NULL;

        // se estiver no array, ele vai pegar o indice do elemento
        if ( in_array( $id, $this->lista_atual ) ) {
            $key = array_search( $id, $this->lista_atual );
        }

        // se o indice não for null, vai apargar o elemento pelo indice
        if ( $key !== NULL ) {
            unset( $this->lista_atual["$key"] );
        }

        if ( self::$set_list ) $this->set_lista_atual();

        return $this->lista_atual;

    }

    /**
     * formata a lista de array para string
     * @return string a lista formatada
     */
    function get_str() {

        $lista = $this->lista_atual;

        if ( empty ( $lista ) ) return;

        if ( is_array( $lista ) ) {
            $lista = implode( ',', $lista );
        }

        return $lista;

    }

}

?>
