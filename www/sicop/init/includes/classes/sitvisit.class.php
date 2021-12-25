<?php

/**
 * classe para manipular a situação dos visitantes
 *
 * @author Rafael
 *
 * @since 13/01/2012
 *
 */
class sitvisit {

    /**
     * a instância da classe
     * @access private
     * @var string
     */
    private static $instance;

    /**
     * para verificar se o visitante está suspenso
     * @access public
     * @var bool
     */
    public $suspenso  = false;

    /**
     * para verificar se o visitante está excluido
     * @access public
     * @var bool
     */
    public $excluido  = false;

    /**
     * a cor da fonte para exibição
     * @deprecated
     * @access public
     * @var string
     */
    public $corfontv  = '#000000';

    /**
     * a classe para css
     * @access public
     * @var string
     */
    public $css_class = 'visit_ativa';

    /**
     * a classe para css complementar
     * @access public
     * @var string
     */
    public $css_dest  = 'visit_ativa_destaque';

    /**
     * a informação da situação do visitante
     * @access public
     * @var string
     */
    public $sit_v     = 'ATIVA';

    /**
     * data inicial da suspensão, se houver
     * @access public
     * @var string
     */
    public $data_ini  = '';

    /**
     * data final da suspensão, se houver
     * @access public
     * @var string
     */
    public $data_fim  = '';

    /**
     * motivo da suspensão, se houver
     * @access public
     * @var string
     */
    public $motivo    = '';

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

        if ( !( self::$instance instanceof sitvisit ) ) {
            self::$instance = new sitvisit();
        }

        return self::$instance;

    }

    function get_sit_visit( $id ) {

        $id = (int)$id;

        if ( empty ( $id ) ) {
            trigger_error ( 'Identificador do visitante em branco ou inválido!!!' );
            return false;
        }

        $query = "SELECT
                    DATE_FORMAT ( `data_inicio`, '%d/%m/%Y' ) AS `data_inicio_f`,
                    `periodo`,
                    DATE_FORMAT ( ADDDATE( `data_inicio`, `periodo` ), '%d/%m/%Y' ) AS `data_fim`,
                    `motivo`
                  FROM
                    `visita_susp`
                  WHERE
                    (
                      `cod_visita` = $id
                      AND
                      ( ( CURDATE() BETWEEN `data_inicio` AND ADDDATE( `data_inicio`, `periodo` ) )
                        OR
                      ( CURDATE() >= `data_inicio` AND ISNULL( ADDDATE( `data_inicio`, `periodo` ) ) ) )
                      AND
                      `revog` = FALSE
                    )
                  ORDER BY
                    ADDDATE( `data_inicio`, `periodo` ) ASC
                  LIMIT 1";

        $db    = SicopModel::getInstance();
        $query = $db->query( $query );

        if ( !$query ) {
            // pegar a mensagem de erro mysql
            $msg_err_mysql = $db->getErrorMsg();
            trigger_error ( "Falha na consulta de verificação da situação do visitante!!! \n\n $msg_err_mysql" );
        }

        $db->closeConnection();

        $cont = 0;
        if ( $query ) $cont = $query->num_rows;

        if ( $cont != 1 ) return;

        $dados = '';
        $dados = $query->fetch_object();


        $this->suspenso  = true;
        $this->excluido  = false;
        $this->corfontv  = '#CC9900';
        $this->css_class = 'visit_susp';
        $this->css_dest  = 'visit_susp_destaque';
        $this->sit_v     = 'SUSPENSA';
        $this->data_ini  = $dados->data_inicio_f;
        $this->motivo    = $dados->motivo;
        $this->data_fim  = $dados->data_fim;

        $periodo = $dados->periodo;

        if ( empty( $periodo ) ) { //se o periodo estiver vazio, é visita excluida

            $this->suspenso  = false;
            $this->excluido  = true;
            $this->corfontv  = '#FF0000';
            $this->css_class = 'visit_excl';
            $this->css_dest  = 'visit_excl_destaque';
            $this->sit_v     = 'EXCLUIDA';
            $this->data_fim  = '';

        }

        return TRUE;

    }

    public function __clone() {
        trigger_error( 'Clone is not allowed.', E_USER_ERROR );
    }

}

?>
