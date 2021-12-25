<?php

/**
 * gera uma mensagem personalizada para ser usada nos logs
 *
 * @author Rafael
 *
 * data 15/10/2011
 *
 */

define( 'SM_TYPE_ATEN', -1 );
define( 'SM_TYPE_PERM', -2 );
define( 'SM_TYPE_ERR', -3 );

define( 'SM_QUERY_NO_RESULT', -1 );
define( 'SM_QUERY_FAIL', -2 );
define( 'SM_NO_PERM', -3 );
define( 'SM_NO_LOGIN', -4 );
define( 'SM_DIRECT_ACCESS', -5 );
define( 'SM_ACCESS_PAGE', -6 );
define( 'SM_INVALID_PROCED', -7 );

class sysmsg {

    /**
     * contem a configuração da quebra de página
     * @access private
     * @var string
     */
    private static $quebra = PHP_EOL;

    /**
     * a instância da classe
     * @access private
     * @var string
     */
    private static $msg;

    /**
     * a mensagem formatada
     * @access private
     * @var string
     */
    private $msg_f;

    /**
     * configuração: se será colocado automaticamente a data e hora na mensagem
     * @access private
     * @var bool
     */
    private static $auto_date_time = false;

    /**
     * configuração: se será colocado automaticamente o endereço da página atual
     * @access private
     * @var bool
     */
    private static $auto_get_page  = false;

    /**
     * configuração: se será colocado automaticamente o referer. Só será colocado se existir
     * @access private
     * @var bool
     */
    private static $auto_get_refer = false;

    /**
     * configuração: se será salva automaticamente a mensagem. Se for true, não há retorno da mensagem
     * @access private
     * @var bool
     */
    private static $auto_save      = false;

    /**
     * array contendo os tipos de mensagens
     * @access private
     * @var array
     */
    private static $msg_type = array(
        0            => '',
        SM_TYPE_ATEN => '<span class="desc_atencao">*** ATENÇÃO ***</span> -> ',
        SM_TYPE_PERM => '<span class="desc_atencao">*** ATENÇÃO ***</span> -> ',
        SM_TYPE_ERR  => '[ <span class="desc_erro">*** ERRO ***</span> ]'
    );

    /**
     * array contendo textos pré definidos
     * @access private
     * @var array
     */
    private static $msg_predf = array(
        0                  => '',
        SM_QUERY_NO_RESULT => 'A consulta retornou 0 ocorrências. ',
        SM_QUERY_FAIL      => 'Falha na consulta. ',
        SM_NO_PERM         => 'Tentativa de acesso à página sem permissões. ',
        SM_NO_LOGIN        => 'Tentativa de acesso à página sem estar logado. ',
        SM_DIRECT_ACCESS   => 'Tentativa de acesso direto à página. ',
        SM_ACCESS_PAGE     => 'Acesso à página.',
        SM_INVALID_PROCED  => 'Número de procedimento em branco ou inválido.'
    );

    private function __construct() {

    }

    /**
     * construtor da classe
     * @param bool $auto_date_time se será colocado automaticamente a data e hora na mensagem
     * @param bool $auto_get_page se será colocado automaticamente o endereço da página atual
     * @param bool $auto_get_refer se será colocado automaticamente o referer. Só será colocado se existir
     * @param bool $auto_save se será salva automaticamente a mensagem. Se for true, não há retorno da mensagem
     * @return bool a classe instânciada
     */
    public static function create_msg( $auto_date_time = TRUE, $auto_get_page = TRUE, $auto_get_refer = TRUE, $auto_save = TRUE ) {

        if ( !( self::$msg instanceof sysmsg ) ) {
            self::$msg = new sysmsg();
        }

        if ( $auto_date_time ) {
            self::$auto_date_time = TRUE;
        }

        if ( $auto_get_page ) {
            self::$auto_get_page = TRUE;
        }

        if ( $auto_get_refer ) {
            self::$auto_get_refer = TRUE;
        }

        if ( $auto_save ) {
            self::$auto_save = TRUE;
        }

        return self::$msg;

    }

//    function __set( $name, $value ) {
//        ;
//    }

    /**
     * define a mensagem dentre os tipos pré definidos.
     * deve ser chamda antes de qualquer outro metodo pois apaga
     * o que está contido dentro da msg, criando um novo cabeçalho
     * @param constant $msg_type
     * @return string o novo tipo de mensagem
     */
    function set_msg_type( $msg_type = 0 ) {
        $msg = self::$msg_type[$msg_type];
        return $this->msg_f = $msg;
    }

    /**
     * acrescenta texto a mensagem
     * @param string $msg o texto que será acrescentado
     * @return string a mensagem com o texto acrescentado
     */
    function set_msg( $msg ) {
        return $this->msg_f .= (string)$msg;
    }

    /**
     * faz a multiplicação das quebras de linha.
     * este metodo é interno, não devendo ser chamado externamente.
     * @param int $num_quebras o numero de quebras de linha que serão formadadas/multiplicadas
     * @return string as quebras
     * @access private
     */
    private function format_quebras( $num_quebras = 1 ) {

        $quebras = '';

        $num_quebras = (int)$num_quebras;

        if ( empty ( $num_quebras ) ) {
            return $quebras;
        }

        $quebra = self::$quebra;

        for ( $index = 0; $index < $num_quebras; $index++ ) {
            $quebras .= $quebra;
        }

        return $quebras;

    }

    /**
     * adiciona quebras de linha à mensagem
     * @param int $num_quebras o número de quebras que serão acrescentadas
     * @return string a mensagem com as quebras acrescentadas
     */
    function add_quebras( $num_quebras = 0 ) {

        if ( empty ( $num_quebras ) ) {
            return;
        }

        $quebras = $this->format_quebras( $num_quebras );

        return $this->msg_f .= $quebras;


    }

    /**
     * adiciona texto à mensagem, delimitado por chaves
     * @param string $msg a mensagem que será colocada entre chaves
     * @param int $quebras_before a quantidade de quebras de linha colocadas antes do texto
     * @param int $quebras_after  a quantidade de quebras de linha colocadas depois do texto, padrão 2
     * @return string a mensagem com o texto entre chaves acrescentado
     */
    function add_chaves( $msg, $quebras_before = 0, $quebras_after = 2 ) {

        $this->add_quebras( $quebras_before );
        $this->msg_f .= '[ ' . $msg . ' ]';
        $this->add_quebras( $quebras_after );

        return $this->msg_f;

    }

    /**
     * adiciona texto à mensagem, delimitado por parenteses
     * @param string $msg a mensagem que será colocada entre parenteses
     * @param int $quebras_before a quantidade de quebras de linha colocadas antes do texto
     * @param int $quebras_after  a quantidade de quebras de linha colocadas depois do texto
     * @return string a mensagem com o texto entre parenteses acrescentado
     */
    function add_parenteses( $msg, $quebras_before = 0, $quebras_after = 0 ) {

        $this->add_quebras( $quebras_before );
        $this->msg_f .= '( ' . $msg . ' )';
        $this->add_quebras( $quebras_after );

        return $this->msg_f;

    }

    /**
     * adiciona data e hora na mensagem
     * @param int $quebras_before a quantidade de quebras de linha colocadas antes do texto, padrao 2
     * @param int $quebras_after  a quantidade de quebras de linha colocadas depois do texto
     * @return string a mensagem com data e hora acrescentados
     */
    function add_date_time( $quebras_before = 2, $quebras_after = 0 ) {

        $this->add_quebras( $quebras_before );
        $this->msg_f .= 'Data: ' . date('d/m/Y \à\s H:i:s');
        $this->add_quebras( $quebras_after );

        return $this->msg_f;

    }

    /**
     * adiciona o link da página atual na mensagem
     * @param int $quebras_before a quantidade de quebras de linha colocadas antes do texto, padrao 2
     * @param int $quebras_after  a quantidade de quebras de linha colocadas depois do texto
     * @return string a mensagem com o link acrescentado
     */
    function add_page( $quebras_before = 2, $quebras_after = 0 ) {

        $pag = SicopController::linkPag();

        if ( !empty( $pag ) ) {
            $this->add_quebras( $quebras_before );
            $this->msg_f .= 'Página: ' . $pag;
            $this->add_quebras( $quebras_after );
        }

        return $this->msg_f;

    }

    /**
     * adiciona o link do referer na mensagem
     * @param int $quebras_before a quantidade de quebras de linha colocadas antes do texto, padrao 1
     * @param int $quebras_after  a quantidade de quebras de linha colocadas depois do texto
     * @return string a mensagem com o link acrescentado
     */
    function add_refer( $quebras_before = 1, $quebras_after = 0 ) {

        if ( !empty( $_SERVER['HTTP_REFERER'] ) ) {

            $ref = SicopController::linkPag( $_SERVER['HTTP_REFERER'], $_SERVER['HTTP_REFERER'], 1 );
            $ref = "Referer: $ref";

            $this->add_quebras( $quebras_before );
            $this->msg_f .= $ref;
            $this->add_quebras( $quebras_after );

        }

        return $this->msg_f;

    }

    /**
     * salva a mensagem no banco de dados
     * @return bool o retorno da função salvaLog()
     */
    function save_msg() {

        $msg = $this->msg_f;

        $iduser = SicopController::getSession( 'user_id', 'int' );

        $model   = SicopModel::getInstance();
        $retorno = $model->writeToLog( $msg, $iduser );

        return $retorno;// salvaLog( $msg );

    }

    /**
     * adiciona textos pré-definidos a mensagem
     * @param int $cod_msg o codigo dos textos, veja $msg_predf
     * @return string a mensagem com o texto predefinido
     */
    function set_msg_pre_def( $cod_msg ) {
        $msg = self::$msg_predf[$cod_msg];
        return $this->msg_f .= $msg;
    }


    /**
     * retorna a mensagem
     * @return string a mensagem ou a saída do metodo save_msg()
     */
    function get_msg() {

        if ( self::$auto_get_page ) {
            $this->add_page();
        }

        if ( self::$auto_get_refer ) {
            $this->add_refer();
        }

        if ( self::$auto_date_time ) {
            $this->add_date_time();
        }

        if ( self::$auto_save ) {
           return $this->save_msg();
        }

        $msg = nl2br( $this->msg_f );
        return $msg;

    }

}
?>
