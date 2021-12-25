<?php

/**
 * O model principal do sistema <br/>
 * faz a conexão com o banco de dados e executa querys
 *
 * @author Rafael
 * @link http://forum.imasters.com.br/topic/353635-classe-mysqli-mais-uma/
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
 * **************************************************************************** *
 */
class SicopModel {

    /**
     * variavel para executar o debug da conexão
     * @access private
     * @var bool
     */
    private $_debug = false;

    /**
     * a instancia da classe
     * @var string
     * @access private
     */
    private static $db;

    /**
     * configurações da conexão
     * @var mixed
     * @access private
     */
    private static $_config = null;

    /**
     * identificador se está conectado
     * @var bool
     * @access private
     */
    private $_connection = null;

    /**
     * mensagem de erro da útima query executada
     * @var string
     * @access protected
     */
    protected $_errMsg = '';

    /**
     * mensagem de erro do MySql da útima query executada
     * @var string
     * @access protected
     */
    protected $_error = '';

    /**
     * número do erro do MySql da útima query executada
     * @var int
     * @access protected
     */
    protected $_errno = '';

    /**
     * tempo de execução da última consulta
     * @var float
     * @access protected
     */
    protected $_queryTime = '';

    /**
     * o status
     * @access public
     * @var bool
     */
    private $_status;

    /**
     * previne o construct
     */
    private function __construct() {

    }

    /**
     * previne a clonagem
     */
    private function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    public static function getInstance() {

        if ( func_num_args() == 4 ) {
            self::$_config = func_get_args();
        } else {
            self::$_config[0] = defined( 'SICOP_DB_SERVER' ) ? SICOP_DB_SERVER : 'localhost';
            self::$_config[1] = defined( 'SICOP_DB_USER' ) ? SICOP_DB_USER : 'cdrio';
            self::$_config[2] = defined( 'SICOP_DB_PASS' ) ? SICOP_DB_PASS : 'poderozo';
            self::$_config[3] = defined( 'SICOP_DB' ) ? SICOP_DB : 'bd';
        }

        if ( !( self::$db instanceof SicopModel ) ) {
            self::$db = new SicopModel();
        }

        return self::$db;

    }

    // setter
    public function __set( $name, $value ) {
        return $this->$name = $value;
    }

    // getter
    public function __get( $name ) {
        return $this->$name;
    }

    protected function _connect() {

        if ( $this->_connection ) {
            return;
        }

        try {

            if ( !extension_loaded( 'mysqli' ) ) {
                throw new Exception( 'É necessario a extension Mysqli.' );
            }

            if ( is_array( self::$_config ) ) {
                $this->_connection = mysqli_init();
            }

            if ( !$this->_connection ) {
                throw new Exception('mysqli_init failed');
            }

            $_isConnected = @mysqli_real_connect(
                            $this->_connection,
                            self::$_config[0],
                            self::$_config[1],
                            self::$_config[2],
                            self::$_config[3]
            );

            if ( $_isConnected === false || mysqli_connect_errno() ) {

                $msg = 'Falha na conexão com o banco!';

                if ( $this->_debug ) {
                    $msg .= '<br/> Mensagem de erro do MySQLi: ' . mysqli_connect_error();
                }

                throw new Exception( $msg );

            }

            $this->_connection->set_charset( 'utf8' );


        } catch ( Exception $exc ) {

            echo $exc->getMessage();

            // se estiver em debug, vai disparar as mensagens complementares
            if ( $this->_debug ) {

                echo '<br/><br/> Trace:';
                echo '<br/>' . nl2br( $exc->getTraceAsString() );

                echo '<br/><br/> Código:';
                echo '<br/>' . nl2br( $exc->getCode() );

                echo '<br/><br/> Arquivo:';
                echo '<br/>' . nl2br( $exc->getFile() );

                echo '<br/><br/> Linha:';
                echo '<br/>' . nl2br( $exc->getLine() );

            }

            exit;

        }

    }

    public function getConnection() {
        $this->_connect();
        return $this->_connection;
    }

    public function isConnected() {
        return ( (bool)( $this->_connection instanceof mysqli ) );
    }

    public function closeConnection() {
        if ( $this->isConnected() ) {
            $this->_connection->close();
        }
        $this->_connection = null;
    }

    public function query( $sql = '' ) {

        $query = FALSE;

        if ( $sql != '' ) {

            // faz a conexão
            $this->_connect();

            // pega o tempo antes do inicio da execução da query
            $querytime_before = array_sum( explode( ' ', microtime() ) );

            // executa a query
            $query = $this->_connection->query( $sql );

            // pega o tempo depois da execução da query
            $querytime_after = array_sum( explode( ' ', microtime() ) );

            // calcula o tempo de execução da query
            $querytime = $querytime_after - $querytime_before;

            // grava o tempo de execução da query nas propriedades do objeto
            $this->setQueryTime( $querytime );

            // se a query retornou false, dispara um erro E_USER_NOTICE que será gravado no log
            if ( !$query ) {

                // seta a mensagem de erro da query
                $this->setErrorMsg();

                 // seta o número do erro da query
                $this->setErrorNum();

                // pega a mensagem de erro do mysql
                $errMsg = $this->_errMsg;

                // dispara o erro
                trigger_error( "Falha na consulta!!! \n\n $errMsg \n\n[ CONSULTA ]\n $sql" );

            }

        }

        return $query;

    }

    public function setErrorMsg() {

        $err_num = $this->_connection->errno;
        $err_str = $this->_connection->error;

        $quebra  = PHP_EOL;

        $this->_errMsg = '';

        if ( !empty( $err_num ) ) {
            $this->_errMsg  = '[ MENSAGEM MYSQL ]' . $quebra;
            $this->_errMsg .= '<b>Código do erro:</b> ' . $err_num . $quebra . '<b>Descrição:</b> ' . $err_str;
        }

        return $this->_errMsg;

    }

    public function setErrorNum() {

        $err_num = $this->_connection->errno;
        return $this->_errno = $err_num;

    }

    public function getErrorNum() {
        return $this->_errno;
    }

    public function getErrorMsg() {
        return $this->_errMsg;
    }

    public function setQueryTime( $querytime ) {
        return $this->_queryTime = $querytime;
    }

    public function getQueryTime() {
        return $this->_queryTime;
    }

    public function fetchOne( $sql = '' ) {

        $retorno = FALSE;

        if ( $sql != '' ) {

            $rs = $this->query( $sql );

            if ( $rs ) {

                $nr = $rs->num_rows;

                if ( $nr >= 1 ) {

                    $re = $rs->fetch_array();
                    $retorno = $re[0];

                }

            }

        }

        return $retorno;

    }

    public function getNumRows( $sql = '' ) {

        $re = FALSE;

        if ( $sql != '' ) {
            $rs = $this->query($sql);
            $re = $rs->num_rows;
        }

        return $re;

    }

    public function lastInsertId() {
        $mysqli = $this->_connection;
        return (int)$mysqli->insert_id;
    }

    public function affected_rows() {
        $mysqli = $this->_connection;
        return (int)$mysqli->affected_rows;
    }

    public function escape_string( $string ) {

        if ( !empty( $string ) ) {

            $this->_connect();
            $string = $this->_connection->real_escape_string( $string );

        }

        return $string;

    }

    public function transaction(){
        return $this->query( 'START TRANSACTION' );
    }

    public function commit(){
        return $this->query( 'COMMIT' );
    }

    public function rollback(){
        return $this->query( 'ROLLBACK' );
    }

    /**
     * verifica se o sistema está ativo ou não
     * @return int 1 se o sistema está ativo ou 0 caso não
     */
    public function ckSysStats() {

        $query = 'SELECT COUNT( `idup` ) FROM `sicop_unidade` WHERE `idup` = 1 AND `ativo` = 1 LIMIT 1';

        // pegar a instancia da conexão
        self::getInstance();
        $sts = $this->fetchOne( $query );
        $this->closeConnection();

        return $sts;

    }

    /**
     * grava uma mensagem no log
     * @param string $msg a mensagem que será gravada
     * @param int $uid o id do usuário
     * @return true em caso de sucesso ou false caso contrário
     */
    public function writeToLog( $msg, $uid ) {

        // pegando o ip do visitante
        $ip = ( !empty( $_SERVER['REMOTE_ADDR'] ) ) ? "'" . $_SERVER['REMOTE_ADDR'] . "'" : 'NULL';

        // pegando o id do usuário
        if ( empty( $uid ) ) $uid = 'NULL';

        // pegar a instancia da conexão
        self::getInstance();

        // escapa a mensagem
        $msg = $this->escape_string( $msg );

        // Monta a query para inserir o log no sistema
        $query = "INSERT INTO `logs` ( `ip`, `id_user`, `mensagem` ) VALUES ( $ip, $uid, '$msg' )";

        // executa a query
        $query = $this->query( $query );

        // fecha a conexão com o banco
        $this->closeConnection();

        return $query;

    }


}

?>
