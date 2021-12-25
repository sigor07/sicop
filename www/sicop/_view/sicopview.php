<?php

/**
 * View principal do sistema
 * Data 09/02/2012
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

class SicopView {

    protected $_header;
    protected $_css             = array();
    protected $_js              = array();
    protected $_js_user         = array();
    protected $_css_user        = array();
    protected $_browser_name    = '';
    protected $_browser_version = '';


    /**
     * construtor da classe
     */
    public function __construct(  ) {
        $this->setBrowserVersion();
    }

    /**
     * gera um header formatado. quando chamda, já inclui os js e css adicionados pelo usuário
     * @param string $title o valor da tag <title>
     * @param string $type o tipo de cabeçalho b = basico; c = completo
     * @return o cabeçalho formatado
     */
    public function getHeader( $title = '', $type = '' ) {

        // adiciona o js e o css comum tanto para o basico quanto para o completo
        $this->addCommon();

        $type = mb_strtolower( $type );

        if ( $type == 'b' ) {

            $this->addCssBasic();
            $this->addJSBasic();

        } else if ( $type == 'c' ) {

            $this->addCssComplete();
            $this->addJSComplete();

        }

        $this->_header  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . PHP_EOL;
        $this->_header .= '<html xmlns="http://www.w3.org/1999/xhtml">' . PHP_EOL;
        $this->_header .= $this->addHeadTag( $title );
        $this->_header .= '    <body>' . PHP_EOL;
        $this->_header .= '        <div class="no_print">' . PHP_EOL;

        //if ( $type == 'c' ) require 'menu.php';

        $option_js = array(
            'type' => 'hidden',
            'id' => 'js_caminho',
            'value' => SICOP_ABS_PATH
        );

        $option_js_img = array(
            'type' => 'hidden',
            'id' => 'js_caminho_img',
            'value' => SICOP_IMG_PATH
        );

        $this->_header .= '            ' . $this->getInput( $option_js ) . PHP_EOL;
        $this->_header .= '            ' . $this->getInput( $option_js_img ) . PHP_EOL;

        return $this->_header;


    }

    /**
     * dá require no arquivo footer.php, que contem o rodapé da página
     */
    public function getFooter() {

//        $footer  = '            <span id="saida"></span>' . PHP_EOL;
//        $footer .= '            <div id="ir_voltar"><a href="javascript: history.go(-1)" title="Voltar"><div id="ir_voltar_in"></div></a></div>' . PHP_EOL;
//        $footer .= '            <div id="irtopo"><a href="javascript: self.scrollTo(0,0)" title="Ir para o inicio"><div id="irtopo_in"></div></a></div>' . PHP_EOL;
//        $footer .= '            <p class="page_end">&nbsp;</p>' . PHP_EOL;
//        $footer .= '        </div><!-- /div class="no_print" -->' . PHP_EOL;
//        $footer .= '    </body>' . PHP_EOL;
//        $footer .= '</html>' . PHP_EOL;
//        return $footer;

        require 'footer.php';

    }

    /**
     * grava em um array os arquivos .css que serão incluidos no html
     * @param string $css o nome do arquivo. se não possuir o ".css", ele será adicionado a string
     * @param bool $user se o arquivo é fornecido pelo usuário. dentro da classe, essa função deve ser chamada com o argumento false
     *                   para que os arquivo adicionados pelo usuário sejam colocados no final
     * @return o array com o valor adicionado
     */
    public function setCss( $css, $user = true ) {

        $ext_pos = mb_strpos( $css, '.css' );
        if ( $ext_pos === false ) {
            $css .= '.css';
        }

        $array = $this->_css;
        if ( $user ) {
            $array = $this->_css_user;
        }

        if ( !in_array( $css, $array ) ) {
            $array[] = $css;
        }

        if ( $user ) {
            return $this->_css_user = $array;
        } else {
            return $this->_css = $array;

        }

    }

    /**
     * formata os arrays de css para o formato adequado para serem incluidos no html
     * @return string os arquivos css formatados para serem incluidos no html
     */
    public function getCss() {

        $css      = $this->_css;
        $css_user = $this->_css_user;

        $caminho = SICOP_ABS_PATH . 'css/';

        $css_f = '';

        if ( !empty( $css ) ) {

            foreach ( $css as $value ) {

                $css_path = $caminho . $value;

                $css_f .= '        <link href="' . $css_path . '" rel="stylesheet" type="text/css" />' . PHP_EOL;

            }

        }

        if ( !empty( $css_user ) ) {

            foreach ( $css_user as $value ) {

                $css_path = $caminho . $value;

                $css_f .= '        <link href="' . $css_path . '" rel="stylesheet" type="text/css" />' . PHP_EOL;

            }

        }

        return $css_f;

    }

    /**
     * grava em um array os arquivos .js que serão incluidos no html
     * @param string $js o nome do arquivo. se não possuir o ".js", ele será adicionado a string
     * @param bool $user se o arquivo é fornecido pelo usuário. dentro da classe, essa função deve ser chamada com o argumento false
     *                   para que os arquivo adicionados pelo usuário sejam colocados no final
     * @return o array com o valor adicionado
     */
    public function setJS( $js, $user = true ) {

        $ext_pos = mb_strpos( $js, '.js' );
        if ( $ext_pos === false ) {
            $js .= '.js';
        }

        $array = $this->_js;
        if ( $user ) {
            $array = $this->_js_user;
        }

        if ( !in_array( $js, $array ) ) {
            $array[] = $js;
        }

        if ( $user ) {
            return $this->_js_user = $array;
        } else {
            return $this->_js = $array;

        }


    }

    /**
     * formata os arrays de js para o formato adequado para serem incluidos no html
     * @return string os arquivos js formatados para serem incluidos no html
     */
    public function getJS() {

        $js = $this->_js;
        $js_user = $this->_js_user;

        $caminho = SICOP_ABS_PATH . 'js/';

        $js_f = '';
        if ( !empty ( $js ) ) {

            foreach ( $js as $value ) {

                $js_path = $caminho . $value;

                $js_f .= '        <script type="text/javascript" src="' . $js_path . '"></script>' . PHP_EOL;

            }

        }

        if ( !empty ( $js_user ) ) {

            foreach ( $js_user as $value ) {

                $js_path = $caminho . $value;

                $js_f .= '        <script type="text/javascript" src="' . $js_path . '"></script>' . PHP_EOL;

            }

        }

        return $js_f;

    }

    /**
     * adiciona o css e js comum tanto para o header basico, quanto para o completo
     */
    public function addCommon() {

        $this->setCss( 'reset', false );

        if ( ( $this->_browser_name == 'IE' and $this->_browser_version > 7 ) or $this->_browser_name != 'IE' ){
            $this->setCss( 'estilo_bts', false );
        }

        $this->setJS( 'jquery', false );
        $this->setJS( 'funcoes', false );

    }

    /**
     * adiciona o css para o header basico
     */
    public function addCssBasic() {

        $this->setCss( 'estilo_wb', false );

    }

    /**
     * adiciona o js para o header basico
     */
    public function addJSBasic() {


    }

    /**
     * adiciona o css para o header completo
     */
    public function addCssComplete() {

        $this->setCss( 'estilo', false );
        $this->setCss( 'superfish', false );

    }

    /**
     * adiciona o js para o header completo
     */
    public function addJSComplete() {

        $this->setJS( 'jquery.funcoes', false );
        $this->setJS( 'ajax/ajax_init', false );

    }

    /**
     * formata a tag <head>, adiciona o título, o css e o js
     * @param string $title o valor da tag <title>
     * @return string a tag <head> formatada
     */
    public function addHeadTag( $title ) {

        $control = new SicopController();
        $titulo_cab = $control->getSession( 'titulo' );

        $head  = '    <head>' . PHP_EOL;
        $head .= '        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . PHP_EOL;
        $head .= '        <link rel="shortcut icon" href="' . SICOP_SYS_IMG_PATH . 'favicon.ico" type="image/x-icon" />' . PHP_EOL;
        $head .= '        <title>' . (!empty( $title ) ? $title . ' | ' . $titulo_cab : $titulo_cab) . '</title>' . PHP_EOL;
        $head .= $this->getCss();
        $head .= $this->getJS();
        $head .= '        <noscript><meta http-equiv="Refresh" content="0; url=<?php echo $caminho; ?>atualiza_java.php" /></noscript>' . PHP_EOL;
        $head .= '        <!--[if IE 6]><script type="text/javascript">window.location.href="' . SICOP_ABS_PATH . 'atualiza_nav.php";</script><![endif]-->' . PHP_EOL;
        $head .= '    </head>' . PHP_EOL;

        return $head;

    }

    /**
     * gera um input, pronto para ser inserido no html
     * @param mixed $option um array contendo os atributos e valores do input
     * @return string
     */
    public function getInput( $option = array() ) {

        $input = '';

        if ( is_array( $option ) ) {

            if ( !empty ( $option ) ) {

                $input = '<input ';

                foreach ( $option as $key => $value ) {

                    $input .= $key . '="' . $value . '" ' ;

                }

                $input .= '/>';

            }

        }

        return $input;

    }

    public function setBrowserVersion() {

        $useragent              = $_SERVER['HTTP_USER_AGENT'];
        $this->_browser_version = 0;
        $this->_browser_name    = 'other';
        $matched = '';

        if ( preg_match( '|MSIE ([0-9]{1,2})|', $useragent, $matched ) ) {

            $this->_browser_version = $matched[1];
            $this->_browser_name    = 'IE';

        } elseif ( preg_match( '|Opera/([0-9].[0-9]{1,2})|', $useragent, $matched ) ) {

            $this->_browser_version = $matched[1];
            $this->_browser_name    = 'Opera';

        } elseif ( preg_match( '|Firefox/([0-9\.]+)|', $useragent, $matched ) ) {

            $this->_browser_version = $matched[1];
            $this->_browser_name    = 'Firefox';

        } elseif ( preg_match( '|Chrome/([0-9\.]+)|', $useragent, $matched ) ) {

            $this->_browser_version = $matched[1];
            $this->_browser_name    = 'Chrome';

        } elseif ( preg_match( '|Safari/([0-9\.]+)|', $useragent, $matched ) ) {

            $this->_browser_version = $matched[1];
            $this->_browser_name    = 'Safari';

        }



    }




}

?>
