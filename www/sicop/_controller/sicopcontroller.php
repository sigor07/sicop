<?php

/**
 * o controller principal do sistema
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
 *
 */
class SicopController {

    /**
     * construtor da classe
     */
    public function __construct() {

    }

    /**
     * pega valores das sessions, com validação
     * @param string $key a chave que será buscada
     * @param string $modo o tipo de validação que será usado. se deixado em branco retorna o dado sem validação
     * @return o valor da chave ou NULL caso a chave não seja encontrada
     */
    public static function getSession( $key, $modo = '', $null_str = false ) {

        $valor_session = !empty( $_SESSION["$key"] ) ? $_SESSION["$key"] : NULL;

        $valor_session = self::handleString( $valor_session, $modo, $null_str );

        return $valor_session;


    }

    /**
     * redireciona usando o header do php
     * @param string $file o nome do arquivo/página para onde será redirecionado
     * @param string $qs a query_string, se houver, que será usada no redirecionamento
     * @return o redirecionamento
     */
    public function redir( $file = 'index', $qs = '' ) {

        $valid = array(
            'index',
            'home'
        );

        if ( !in_array( $file, $valid ) or empty( $file ) ) {
            $file = 'index';
        }

        unset( $valid );

        if ( empty( $_SERVER['SERVER_NAME'] ) ) return false;

        $ext_pos = mb_strpos( $file, '.php' );
        if ( $ext_pos === false ) {
            $file .= '.php';
        }

        $patch = SICOP_ABS_PATH;

        $caminho = $_SERVER['SERVER_NAME'] . $patch . $file;

        $http_pos  = mb_strpos( $caminho, 'http://' );
        if ( $http_pos === false ) {
            $caminho = 'http://' . $caminho;
        }

        if ( !empty( $qs ) ) $caminho .= '?' . $qs;

        return header( "Location: $caminho" );

    }

    /**
     * monta uma mensagem javascript, com alert(), com retorno, ou ambos
     * @param string $msg a mensagem que será usada no alert(). se deixada em branco, não executa o alert().
     * @param string $num_ret o número de páginas que vai retornar no histórico, ou 'f' para fechar a janela,
     *                        e/ou 'r' para window.opener.location.reload();
     * @return string a mensagem javascript pronta para ser inserida no html
     */
    public function msgJS( $msg, $num_ret = 0 ) {

        if ( empty( $num_ret ) and empty( $msg ) ) {
            return FALSE;
        }

        $js_ini = '<script type="text/javascript">';
        $js_fim = '</script>';
        $msg_alert = '';
        $retono = '';

        if ( !empty( $num_ret ) ) {

            $retono = '';
            if ( is_numeric( $num_ret ) ) {

                $retono = 'history.go(-' . $num_ret . ');';

            } else {

                $num_ret = mb_strtolower( $num_ret );
                $pos_f   = mb_strpos( $num_ret, 'f' );
                $pos_r   = mb_strpos( $num_ret, 'r' );

                if ( $pos_r !== false ) {
                    $retono .= 'window.opener.location.reload();';
                }

                if ( $pos_f !== false ) {
                    $retono .= 'self.window.close();';
                }

            }

        }

        if ( !empty( $msg ) ) {
            $msg_alert = 'alert("' . $msg . '");';
        }

        $msg_js_f = $js_ini . $msg_alert . $retono . $js_fim;
        return $msg_js_f;

    }

    /**
     * remove acentos das strings
     * @param string $string a string a ser tratada
     * @return string a string tratada
     */
    public static function removeAcentos( $string ) {

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

        return $string;

    }

    /**
     * Função para tratar valores inseridos em formulários
     * remove acentos, espaçamento duplo, pontos e traços e
     * espaços no começo/fim da string
     * @param $str string - a string que será tratada.
     * @param $case_s string - para determinar se a string vai passar para maiúscula ('U'), minúscula ('L'), ou não tratar ('N')
     * @param $acentos bool - true quer dizer que vai remover os acentos, false nao remove
     * @return string a string tratada
     */
    public static function trataString( $str, $case_s = 'U', $remove_acentos = true ) { // $acentos = true quer dizer que vai remover os acentos, false nao remove

        $strf = $str;

        //retira mais de 2 espaços
        $strf = preg_replace( '/[ ]{2,}/', ' ', $strf );

        //remove espaço do começo e fim da string
        $strf = trim( $strf );

        // para remover acentos
        if ( $remove_acentos ) {
            $strf = self::removeAcentos( $strf );
        }

        switch ( $case_s ) {
            case 'L':
                $strf = mb_strtolower( $strf, 'UTF-8' );
                break;
            //default:
            case 'U':
                $strf = mb_strtoupper( $strf, 'UTF-8' );
                break;
        }

        $strf = preg_replace( '/&NBSP;/', '&nbsp;', $strf );

        $db   = SicopModel::getInstance();
        $strf = $db->escape_string( $strf );
        $db->closeConnection();

        return $strf;

    }

    /**
     * Função para tratar valores inseridos em formulários de busca e url
     * remove acentos, espaçamento duplo, pontos e traços, e espaços no começo/fim da string
     * @param $str string - string que será tratada
     * @return a string tratada
     */
    public static function trataBusca( $str ) {

        //retira mais de 2 espaços
        $strf = preg_replace( '/\s{2,}/', ' ', $str );

        //retira ponto e traço
        $strf = preg_replace( '/[-.,]/', '', $strf );

        //remove espaço do começo e fim da string
        $strf = trim( $strf );

        //passa por uma funçao para remover acentos
        $strf = self::removeAcentos( $strf );

        $db  = SicopModel::getInstance();
        $strf = $db->escape_string( $strf );
        $db->closeConnection();

        return $strf;
    }

    /**
     * Função para tratar valores inseridos em formulários de busca e url
     * remove espaçamento duplo, e espaços no começo/fim da string
     * @param $str string - string que será tratada
     * @return a string tratada
     */
    public static function trataBasico( $str ) {

        //retira mais de 2 espaços
        $str = preg_replace( '/[ ]{2,}/', ' ', $str );

        //remove espaço do começo e fim da string
        $str = trim( $str );

        $db  = SicopModel::getInstance();
        $str = $db->escape_string( $str );
        $db->closeConnection();

        return $str;

    }


    public function anti_injection( $sql ) {

        // remove palavras que contenham sintaxe sql
        $sql = preg_replace( sql_regcase( "/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/" ), '', $sql );

        //limpa espaços vazio
        $sql = trim( $sql );

        //tira tags html e php
        $sql = strip_tags( $sql );

        //Adiciona barras invertidas a uma string
        $sql = addslashes( $sql );

        return $sql;

    }


    /**
     * Função para tratar valores recebidos via get
     * Remove espaçamento duplo, e espaços no começo/fim.
     * @param  $key  string  indice do get
     * @param  $modo  string  modo como o dado será tradado
     * @return string o get tratado
     */
    public function getGet( $key, $modo = '', $null_str = false ) {

        $valor_get = !empty( $_GET["$key"] ) ? $_GET["$key"] : NULL;

        $valor_get = $this->handleString( $valor_get, $modo, $null_str );

        return $valor_get;

    }

    /**
     * Função para tratar valores recebidos via post
     * Remove espaçamento duplo, e espaços no começo/fim.
     * @param  $key  string  indice do post
     * @param  $modo  string  modo como o dado será tradado
     * @return string o post tratado
     */
    public function getPost( $key, $modo = '', $null_str = false ) {

        $valor_post = !empty( $_POST["$key"] ) ? $_POST["$key"] : NULL;

        $valor_post = $this->handleString( $valor_post, $modo, $null_str );

        return $valor_post;

    }

    public static function handleString( $str = '', $modo = '', $null_str = false ) {

        switch ( $modo ) {

            default:
            case '':
                $str = !empty( $str ) ? $str : NULL;
                break;

            case 'busca':
                $str = !empty( $str ) ? self::trataBusca( $str ) : NULL;
                break;

            case 'int':
                $str = (int)$str;
                break;

            case 'string':
                $str = !empty( $str ) ? self::trataString( $str ) : NULL;
                break;

            case 'basico':
                $str = !empty( $str ) ? self::trataBasico( $str ) : NULL;
                break;

            case 'escape':
                if ( !empty( $str ) ) {
                    $db  = SicopModel::getInstance();
                    $str = $db->escape_string( $str );
                    $db->closeConnection();
                }
                break;

        }

        if ( $null_str ) {
            if ( is_null( $str ) ) {
                $str = 'NULL';
            }
        }

        return $str;

    }

    /**
     * checa se o sistema está ativo
     * acessa o model para verificação
     * @return bool true se o sistema está ativo ou redireciona para a página de login
     */
    public static function ckSys() {

        //$iduser = $this->getSession( 'user_id', 'int' );

        $iduser = self::getSession( 'user_id', 'int' );

        if ( $iduser != 1 ) {

            $sys = SicopModel::getInstance();
            $ck_sys = $sys->ckSysStats();

            if ( $ck_sys != 1 ) {

                // Destrói a sessão
                session_destroy();

                $qs = 'sys_out=1';

                $this->redir( 'index', $qs );

                exit;
            }

        }

        return true;

    }

    /**
     * formata uma data para o padrão brasileiro, em extenso.
     * @param date $timestamp um timestamp para verificação, se não for informado, será utilizado o atual
     * @return string a data formatada
     */
    public static function dataF( $timestamp = NULL ) {

        if ( empty( $timestamp ) ) $timestamp = time();

        $dia = date( 'd', $timestamp );
        $mes = date( 'm', $timestamp );
        $ano = date( 'Y', $timestamp );

        $mes = self::getMesFromInt( $mes );

        $data = $dia . ' de ' . $mes . ' de ' . $ano;

        return $data;
    }

    /**
     * formata um mes, incluindo o ano, ou não.
     * @param date $timestamp um timestamp para verificação, se não for informado, será utilizado o atual
     * @param int $num_mes um mes para verificação, se não for informado, será utilizado o timestamp, ou o informado ou atual
     * @param bool $incl_ano se vai incluir o ano na formatação, se for informado o timestamp, pega o ano do timestamp, se não pega o ano atual
     * @param bool $reduzir se for true, retorna o mes com 3 letras.
     * @return string o mes formatado
     */
    public static function getMesF( $timestamp = NULL, $num_mes = NULL, $incl_ano = false, $reduzir = false ) {

        $num_mes = (int)$num_mes;
        $mes     = '';

        if ( !empty( $num_mes ) ) {

            $mes = (int)$num_mes;

            if ( $num_mes < 1 or $num_mes > 12 ) {

                $mes = date( 'm', time() );

            }

        } else {

            if ( empty( $timestamp ) ) $timestamp = time();

            $mes = date( 'm', $timestamp );

        }

        $mes = $this->getMesFromInt( $mes );

        if ( $reduzir ) {
            $mes = substr( $mes, 0, 3 );
        }

        if ( $incl_ano ) {

            if ( empty( $timestamp ) ) $timestamp = time();

            $ano = date( 'Y', $timestamp );

            $mes = $mes . '/' . $ano;

        }

        return $mes;

    }

    /**
     * retorna o nome do mes, baseado em um número de 1 à 12
     * @param int $mes o número do mes
     * @return string o respectivo mes
     */
    protected static function getMesFromInt( $mes ) {

        $mes = (int)$mes;

        if ( empty( $mes ) or $mes > 12 or $mes < 1 ) {
            return false;
        }

        switch ( $mes ) {
            case '01':
                $mes = 'janeiro';
                break;
            case '02':
                $mes = 'fevereiro';
                break;
            case '03':
                $mes = 'março';
                break;
            case '04':
                $mes = 'abril';
                break;
            case '05':
                $mes = 'maio';
                break;
            case '06':
                $mes = 'junho';
                break;
            case '07':
                $mes = 'julho';
                break;
            case '08':
                $mes = 'agosto';
                break;
            case '09':
                $mes = 'setembro';
                break;
            case '10':
                $mes = 'outubro';
                break;
            case '11':
                $mes = 'novembro';
                break;
            case '12':
                $mes = 'dezembro';
                break;
        }

        return $mes;

    }

    /**
     * formata o dia da semana
     * @param timestamp $timestamp um timestamp para verificação, se não for informado, será utilizado o atual
     * @param bool $comp se vai adicionar o complemento '-feira', nos dias de segunda a sexta
     * @return string o dia da semana formatado
     */
    public static function diaSemanaF( $timestamp = NULL, $comp = NULL ) {

        if ( empty( $timestamp ) ) $timestamp = time();

        $dia_semana = '';
        $n_dia_semana = date( 'w', $timestamp );

        switch ( $n_dia_semana ) {
            case '0':
                $dia_semana = 'domingo';
                break;
            case '1':
                $dia_semana = 'segunda';
                break;
            case '2':
                $dia_semana = 'terça';
                break;
            case '3':
                $dia_semana = 'quarta';
                break;
            case '4':
                $dia_semana = 'quinta';
                break;
            case '5':
                $dia_semana = 'sexta';
                break;
            case '6':
                $dia_semana = 'sábado';
                break;
        }

        if ( !empty( $comp ) ) {
            if ( $n_dia_semana != 0 and $n_dia_semana != 6 ) {
                $dia_semana .= '-feira';
            }
        }

        return $dia_semana;

    }

    /**
     * verificar mensagens não lidas
     * acessa o model para verificação
     * @return string o link formatado para as msg
     */
    public static function ckMsg() {


        $n_msg  = self::getSession( 'n_msg', 'int' );

        $rmsg = '<img src="' . SICOP_SYS_IMG_PATH . 'msg_read.png" width="20" height="13" style="vertical-align: middle" />';

        if ( $n_msg >= 2 ) {

            $iduser = self::getSession( 'user_id', 'int' );

            if ( empty ( $iduser ) ) return $rmsg;

            $user     = UserModel::getInstance();
            $cont_msg = $user->getUnreadMsg( $iduser );

            $rmsg = '<a href="' . SICOP_ABS_PATH . 'msg/msg.php"><img id="msg" src="' . SICOP_SYS_IMG_PATH . 'msg_read.png" width="20" height="13" /></a>';

            // se o número de ocorrências for maior ou igual a 1, mostra a mensagem
            if ( $cont_msg >= 1 ) {
                $rmsg = '<a href="' . SICOP_ABS_PATH . 'msg/msg.php" title="Você possui ' . $cont_msg . ' mensagem(ns)"><img src="' . SICOP_SYS_IMG_PATH . 'msg_new.png" alt="Você possui ' . $cont_msg . ' mensagem(ns)" width="20" height="13" style="vertical-align: middle" /></a>';
            }

        }

        return $rmsg;

    }

    /*
     * Cria um link
     * @param string $title descrição do link, o que fica entre as tags <a>, se ficar em branco será usado o endereço da página
     * @param string $href o endereço depois de /sicop/<o que fica aqui>. se ficar em branco, pega o endereço da página atual
     * @param boll $full se for empty preenche com os parametros $_SERVER['SERVER_NAME']. Senão Pegará somente o que foi
     *                   passado pelo usuário
     * @return  string  o link formatado.
     */
    public static function linkPag( $title = '', $href = '', $full = '' ) {

        $pag = '';

        if ( empty( $_SERVER['SERVER_NAME'] ) ) return $pag;

        if ( empty( $href ) ) {

            if ( !empty( $_SERVER['REQUEST_URI'] ) ) {

                $caminho   = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

                $http_pos  = mb_strpos( $caminho, 'http://' );
                if ( $http_pos === false ) {
                    $caminho = 'http://' . $caminho;
                }

                $descricao = $caminho;
                if ( !empty( $title ) ) $descricao = $title;

                $pag = "<a href='$caminho' title='$caminho'>$descricao</a>";

            }

        } else {

            $caminho = $href;

            if ( empty( $full ) ) {

                $patch = SICOP_ABS_PATH;

                $caminho = $_SERVER['SERVER_NAME'] . $patch . $href;

            }

            $http_pos  = mb_strpos( $caminho, 'http://' );
            if ( $http_pos === false ) {
                $caminho = 'http://' . $caminho;
            }

            $descricao = $caminho;
            if ( !empty( $title ) ) $descricao = $title;

            $pag = "<a href='$caminho' title='$caminho'>$descricao</a>";


        }

        return $pag;

    }

    public static function salvaLog( $msg ) {

        $iduser = self::getSession( 'user_id', 'int' );

        $model   = SicopModel::getInstance();
        $retorno = $model->writeToLog( $msg, $iduser );
        $model->closeConnection();

        return $retorno;

    }

    public static function formataNum( $numero, $num_dig = 1 ) {

        $num_f = '';

        if ( !empty( $numero ) ) {

            $num = substr( $numero, 0, -$num_dig );
            $dig = substr( $numero, -$num_dig );

            if ( strlen( $num ) > 1 ) {
                $num = number_format( $num, 0, '', '.' );
            }

            $num_f = $num . '-' . $dig;

        }

        return $num_f;

    }


    /**
     * verifica se uma página foi requisitada via post
     * @return boolean true caso a página tenha sido requisitada via post, ou false, caso contrário
     */
    public static function isPost() {

        $ck = FALSE;
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            $ck = TRUE;
        }

        return $ck;

    }

    /**
     * verifica se o acesso foi por post e caso negativo
     * exibe uma mensagem e retorna
     * @return bool o retorno de isPost()
     */
    public function ckPost( $return_type ) {

        $is_post = self::isPost();

        if ( !$is_post ) {

            // montar a mensagem q será salva no log
            $msg = sysmsg::create_msg();
            $msg->set_msg_type( SM_TYPE_ATEN );
            $msg->set_msg_pre_def( SM_DIRECT_ACCESS );
            $msg->get_msg();

            echo $this->handleReturn( $return_type );

            exit;

        }

        return $is_post;

    }

    /**
     * para verificar o número do procedimento nas páginas send
     * @param int $proced o número do procedimento recebido pela página
     * @param int $max_proced o maior valor do procedimento
     * @param int $return_type o tipo de retorno, retornado atraves de handleReturn()
     */
    public function ckProced( $proced, $max_proced, $return_type ) {

        $proced      = (int)$proced;
        $max_proced  = (int)$max_proced;
        $return_type = (int)$return_type;

        if ( empty( $proced ) or $proced > $max_proced or $proced < 1 ) {

            // montar a mensagem q será salva no log
            $msg = sysmsg::create_msg();
            $msg->set_msg_type( SM_TYPE_ERR );
            $msg->add_quebras( 1 );
            $msg->set_msg_pre_def( SM_INVALID_PROCED );
            $msg->get_msg();

            echo $this->handleReturn( $return_type );

            exit;

        }

    }

    /**
     * formata as saídas dos retornos
     * @param int $type o tipo de retorno 1 - retorna 0; 2 - retorna uma mensagen javascript, com retorno 1
     * @return string o tipo de retorno formatado
     */
    public function handleReturn( $type ) {

        $type = (int)$type;
        if ( empty ($type) ) return 0;

        $retorno = '';

        switch ( $type ) {
            default:
            case 1:
                $retorno = 0;
                break;

            case 2:
                $retorno = $this->msgJS( 'FALHA!!!', 1 );
                break;

            case 3:
                $retorno = $this->msgJS( 'FALHA!!!', 'f' );
                break;

            case 4:
                $retorno = '<p class="q_error">FALHA!</p>';
                break;

            case 5:
                $retorno = '<p class="q_error">Você não tem permissões para acessar esta página!</p>';
                break;

            case 6:
                $retorno = $this->msgJS( 'Você não tem permissões para acessar esta página!', 1 );
                break;

            case 7:
                $retorno = $this->msgJS( 'Você não tem permissões para acessar esta página!', 'f' );
                break;

        }

        return $retorno;

    }


    /**
     * formata o user_id, a função NOW() do SQL e o ip para serem inserido junto com as consultas
     * @return string o user, date e ip formatados para SQL
     */
    public static function getUserForModel() {

        $user = new userAutController();
        $user_id = $user->getUidFromSession();

        if ( empty ( $user_id ) ) return false;

        $ip   = $_SERVER['REMOTE_ADDR'];

        $str = "'$user_id', NOW(), '$ip'";

        return $str;

    }

    /**
     * valida campos vindo de formulários atraves de post, get, ou session
     * valida requerido, comprimento, vazio
     * @param mixed $option um array com as opções de validação
     * @return string a string validada, ou uma mensagem formatada pela função handleReturn()
     */
    public function validate( $option = array() ) {

        $required = !empty ( $option['required'] ) ? TRUE : FALSE;

        $null_str = true;
        // se o campo for requerido, ele deverá retornar um tipo de dados NULL, ao invés de string 'NULL'
        //if ( $required ) {
            $null_str = false;
        //}

        $sucess = true;

        // msg de erro
        $msg_err = '';

        $modo_validacao = !empty ( $option['modo_validacao'] ) ? $option['modo_validacao'] : '';

        // str contendo o valor tratado
        $str = '';

        switch ( $option['method'] ) {
            case 'p':
            case 'post':
                $str = $this->getPost( $option['name'], $modo_validacao, $null_str );
                break;
            case 'g':
            case 'get':
                $str = $this->getGet( $option['name'], $modo_validacao, $null_str );
                break;
            case 's':
            case 'session':
                $str = $this->getSession( $option['name'], $modo_validacao, $null_str );
                break;

        }

        if ( $sucess ) {

            if ( $required and empty( $str ) ) {

                $zero_ok = empty( $option['zero_ok'] ) ? false : true;

                if ( ( $zero_ok and $str !== 0 ) or !$zero_ok ) {

                    $sucess = false;
                    $msg_err = 'Campo requerido em branco! ';

                }// if ( ( $zero_ok and $str !== 0 ) or !$zero_ok ) {


            } // if ( $required and empty( $str ) ) {

        } // if ( $sucess ) {

        if ( $sucess ) {

            if ( !empty( $option['maxLeng'] ) or !empty( $option['minLeng'] )  ) {

                $leng = strlen($str);

                if ( !empty( $option['maxLeng'] ) ) {

                    if ( $leng > $option['maxLeng'] ) {

                        $sucess = false;

                        $msg_err = 'Campo com mais caracteres do que o permitido! ';

                    } // if ( $leng > $option['maxLeng'] ) {

                } // if ( !empty( $option['maxLeng'] ) ) {

                if ( !empty( $option['minLeng'] ) ) {

                    if ( $leng < $option['minLeng'] ) {

                        $sucess = false;

                        $msg_err = 'Campo com menos caracteres do que o permitido! ';

                    } // if ( $leng < $option['minLeng'] ) {

                } // if ( !empty( $option['minLeng'] ) ) {

            } // if ( !empty( $option['maxLeng'] ) or !empty( $option['minLeng'] )  ) {

        } // if ( $sucess ) {

        if ( !$sucess ) {

            // montar a mensagem q será salva no log
            $msg = sysmsg::create_msg();
            $msg->set_msg_type( SM_TYPE_ERR );
            $msg->add_quebras( 1 );
            $msg->set_msg( $msg_err );
            $msg->add_parenteses( $option['name'] );
            $msg->get_msg();

            if ( empty( $option['return_type'] ) ) {
                $option['return_type'] = 1;
            }

            echo $this->handleReturn( $option['return_type'] );

            exit;

        } // if ( $sucess ) {

        return $str;

    }

    public static function tratasn( $str ) {

        if ( $str == 0 ) {
            $str = 'NÃO';
        } else if ( $str == 1 ) {
            $str = 'SIM';
        } else if ( empty( $str ) ) {
            $str = '';
        }

        return $str;

    }

    /**
     * Função para pegar os valores inseridos pelo usuário em um post ou get
     * @param  $arr_user  array  o array post ou get, ou outro array que contenha valores  
     * @return  string  contendo uma mensagem formatada
     */
    public static function valorUser ( $arr_user ) {

        if ( !is_array( $arr_user ) ) return false;

        $valor_user = '';
        $cont = count( $arr_user );
        $i = 0 ;
        foreach ( $arr_user as $indice => $valor ) {
            ++$i;
            if ( $valor == NULL ) continue;
            $valor_user .= "<b>$indice</b> = $valor";
            if ( $i != $cont ) $valor_user .= '<br />';
        }

        if ( empty( $valor_user ) ) return false;

        $db  = SicopModel::getInstance();
        $valor_user = $db->escape_string( $valor_user );
        $db->closeConnection();

        $msg_valor_user  = '[ VALORES INSERIDOS PELO USUÁRIO ]' . PHP_EOL;
        $msg_valor_user .= $valor_user;

        return $msg_valor_user;

    }


}

?>
