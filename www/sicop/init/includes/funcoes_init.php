<?php

/**
 * Função para salvar mensagens de LOG no MySQL
 * @param string $mensagem - A mensagem a ser salva
 * @return bool - Se a mensagem foi salva ou não (true/false)
 */
function salvaLog( $mensagem ) {

    //$pag = link_pag();

    setlocale( LC_ALL, 'pt_BR', 'ptb', 'pt-BR', 'pt-br', 'PT-BR', 'pt_BR.utf-8', 'portuguese-brazil', 'bra' );
    date_default_timezone_set( 'America/Sao_Paulo' );

    $ip = ( !empty( $_SERVER['REMOTE_ADDR'] ) ) ? "'" . $_SERVER['REMOTE_ADDR'] . "'" : 'NULL'; // Salva o IP do visitante

    $user = get_session( 'user_id', 'int' );
    if ( empty( $user ) ) $user = 'NULL';

    $db       = SicopModel::getInstance();
    $mensagem = $db->escape_string( $mensagem );

    // Monta a query para inserir o log no sistema
    $sql = "INSERT INTO `logs` ( `ip`, `id_user`, `mensagem` ) VALUES ( $ip, $user, '$mensagem' )";


    $sql = $db->query( $sql );
    $db->closeConnection();

    $retorno = false;

    if ( $sql ) {
        $retorno =  true;
    }

    return $retorno;

}

function aut_session() {

    $iduser = get_session( 'user_id', 'int' );

    // Verifica se não há a variável da sessão que identifica o usuário
    if ( empty( $iduser ) ) {

        // Destrói a sessão por segurança
        session_destroy();

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'atn';
        $msg['text'] = 'Tentativa de acesso à página sem estar logado.';
        get_msg( $msg, 1 );

        redir( 'index' );

        exit;

    }

    $query = "SELECT COUNT( `iduser` ) FROM `sicop_users` WHERE `iduser` = $iduser AND `ativo` = 1 LIMIT 1";

    $db = SicopModel::getInstance();
    $cont_user = $db->fetchOne( $query );
    $db->closeConnection();

    if ( $cont_user != 1 ) {

        // Destrói a sessão
        session_destroy();


        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'atn';
        $msg['text'] = 'Sessão desativada pelo fato do usuário estar inativo ou ter sido bloqueado.';
        get_msg( $msg, 1 );

        redir( 'index' );

        exit;

    }

}

function ck_sys() {

    $iduser = get_session( 'user_id', 'int' );

    if ( $iduser != 1 ) {

        $query = 'SELECT COUNT( `idup` ) FROM `sicop_unidade` WHERE `idup` = 1 AND `ativo` = 1 LIMIT 1';

        $db = SicopModel::getInstance();
        $cont_sys = $db->fetchOne( $query );
        $db->closeConnection();

        if ( $cont_sys != 1 ) {

            // Destrói a sessão
            session_destroy();

            $qs = 'sys_out=1';

            redir( 'index', $qs );

            exit;
        }

    }

}

function remove_acentos( $string ) {
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
function tratastring( $str, $case_s = 'U', $acentos = true ) { // $acentos = true quer dizer que vai remover os acentos, false nao remove
    $strf = $str;

    $strf = preg_replace( '/[ ]{2,}/', ' ', $strf ); //retira mais de 2 espaços
    //$strf = trim($strf,' '); //remove espaço do começo e fim da string

    $strf = trim( $strf ); //remove espaço do começo e fim da string

    if ( $acentos ) {
        $strf = remove_acentos( $strf ); //passa por uma funçao para remover acentos
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

    $db  = SicopModel::getInstance();
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
function tratabusca( $str ) {

    $strf = preg_replace( '/\s{2,}/', ' ', $str ); //retira mais de 2 espaços

    $strf = preg_replace( '/[-.,]/', '', $strf ); //retira ponto e traço

    $strf = trim( $strf ); //remove espaço do começo e fim da string

    $strf = remove_acentos( $strf ); //passa por uma funçao para remover acentos

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
function tratabasico( $str ) {

    $str = preg_replace( '/[ ]{2,}/', ' ', $str ); //retira mais de 2 espaços

    $str = trim( $str ); //remove espaço do começo e fim da string

    $db  = SicopModel::getInstance();
    $str = $db->escape_string( $str );
    $db->closeConnection();

    return $str;
}

/**
 * Função para tratar valores recebidos via get
 * Remove espaçamento duplo, e espaços no começo/fim.
 * @param  $key  string  indice do get
 * @param  $modo  string  modo como o dado será tradado
 * @return string o get tratado
 */
function get_get( $key, $modo = '' ) {

    $valor_get = '';

    switch ( $modo ) {
        default:
        case '':
            $valor_get = !empty( $_GET["$key"] ) ? $_GET["$key"] : NULL;
            break;
        case 'busca':
            $valor_get = !empty( $_GET["$key"] ) ? tratabusca( $_GET["$key"] ) : NULL;
            break;
        case 'int':
            $valor_get = !empty( $_GET["$key"] ) ? (int)$_GET["$key"] : NULL;
            break;
        case 'string':
            $valor_get = !empty( $_GET["$key"] ) ? tratastring( $_GET["$key"] ) : NULL;
            break;
        case 'escape':

            $valor_get = NULL;

            if ( !empty( $_GET["$key"] ) ) {

                $db  = SicopModel::getInstance();
                $valor_get = $db->escape_string( $_GET["$key"] );
                $db->closeConnection();

            }
            break;

    }

    return $valor_get;

}

/**
 * Função para tratar valores recebidos via post
 * Remove espaçamento duplo, e espaços no começo/fim.
 * @param  $key  string  indice do post
 * @param  $modo  string  modo como o dado será tradado
 * @return string o post tratado
 */
function get_post( $key, $modo = '', $null_str = false ) {

    $valor_post = '';

    switch ( $modo ) {
        default:
        case '':
            $valor_post = !empty( $_POST["$key"] ) ? $_POST["$key"] : NULL;
            break;
        case 'busca':
            $valor_post = !empty( $_POST["$key"] ) ? tratabusca( $_POST["$key"] ) : NULL;
            break;
        case 'int':
            $valor_post = !empty( $_POST["$key"] ) ? (int)$_POST["$key"] : NULL;
            break;
        case 'string':
            $valor_post = !empty( $_POST["$key"] ) ? tratastring( $_POST["$key"] ) : NULL;
            break;
        case 'escape':
            $valor_post = NULL;
            if ( !empty( $_POST["$key"] ) ) {

                $db  = SicopModel::getInstance();
                $valor_post = $db->escape_string( $_POST["$key"] );
                $db->closeConnection();

            }
            break;
    }

    if ( $null_str ) {
        if ( is_null( $valor_post ) ) {
            $valor_post = 'NULL';
        }
    }

    return $valor_post;
}

/**
 * Função para tratar valores recebidos de sessions
 * Remove espaçamento duplo, e espaços no começo/fim da session.
 * @param  $key  string  indice da session
 * @param  $modo  string  modo como o dado será tradado
 * @return string a session tratada
 */
function get_session( $key, $modo = '' ) {

    $valor_session = '';

    switch ( $modo ) {
        default:
        case '':
            $valor_session = !empty( $_SESSION["$key"] ) ? $_SESSION["$key"] : NULL;
            break;
        case 'busca':
            $valor_session = !empty( $_SESSION["$key"] ) ? tratabusca( $_SESSION["$key"] ) : NULL;
            break;
        case 'int':
            $valor_session = !empty( $_SESSION["$key"] ) ? (int)$_SESSION["$key"] : NULL;
            break;
        case 'string':
            $valor_session = !empty( $_SESSION["$key"] ) ? tratastring( $_SESSION["$key"] ) : NULL;
            break;
    }

    return $valor_session;
}

/**
 * Verifica se o metodo de requisição da página é POST
 * @return bool verdadeiro se for post
 */
function is_post() {

    $ck = FALSE;
    if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
        $ck = TRUE;
    }

    return $ck;

}

//function set_cab_css( $href ) {
//
//    if ( empty( $href ) ) return false;
//
//    $caminho = SICOP_ABS_PATH . 'css/';
//    if ( !isset( $_SESSION ) ) session_start();
//
//    if ( is_array( $href ) ) {
//
//        foreach ( $href as &$value ) {
//
//            $css_path = $caminho . $value;
//
//            $css = '<link href="' . $css_path . '" rel="stylesheet" type="text/css" />';
//
//            $_SESSION['cab_css'][] = $css;
//
//        }
//
//    } else {
//
//        $css_path = $caminho . $href;
//
//        $css = $css = '<link href="' . $css_path . '" rel="stylesheet" type="text/css" />';
//
//        $_SESSION['cab_css'][] = $css;
//
//    }
//
//    return true;
//
//}
//
//function get_cab_css() {
//
//    if ( empty( $_SESSION['cab_css'] ) ) {
//        if ( isset ( $_SESSION['cab_css'] ) ) unset ( $_SESSION['cab_css'] );
//        return false;
//    }
//
//    $css = '';
//    $cab_css = $_SESSION['cab_css'];
//
//    if ( isset ( $_SESSION['cab_css'] ) ) unset ( $_SESSION['cab_css'] );
//
//    $i = 0;
//    foreach ( $cab_css as &$value ) {
//
//        ++$i;
//
//        if ( $i == 1 ) {
//            $css .= $value . PHP_EOL;
//            continue;
//        }
//
//        $css .= '        ' . $value . PHP_EOL;
//
//    }
//
//    return $css;
//
//}

function set_cab_js( $href ) {

    if ( empty( $href ) ) return false;

    //pegando o caminho da pasta o js
    $caminho = SICOP_ABS_PATH . 'js/';

    if ( !isset( $_SESSION ) ) session_start();

    if ( is_array( $href ) ) {

        foreach ( $href as &$value ) {

            $js_path = $caminho . $value;

            $js = '<script type="text/javascript" src="' . $js_path . '"></script>';

            $_SESSION['cab_js'][] = $js;

        }

    } else {

        $js_path = $caminho . $href;

        $js = '<script type="text/javascript" src="' . $js_path . '"></script>';

        $_SESSION['cab_js'][] = $js;

    }

    return true;

}

function get_cab_js() {

    if ( empty( $_SESSION['cab_js'] ) ) {
        if ( isset ( $_SESSION['cab_js'] ) ) unset ( $_SESSION['cab_js'] );
        return false;
    }

    $js = '';
    $cab_js = $_SESSION['cab_js'];

    if ( isset ( $_SESSION['cab_js'] ) ) unset ( $_SESSION['cab_js'] );

    $i = 0;
    foreach ( $cab_js as &$value ) {

        ++$i;

        // se for a 1ª linha, não coloca a tabulação
        if ( $i == 1 ) {
            $js .= $value . PHP_EOL;
            continue;
        }

        $js .= '        ' . $value . PHP_EOL;

    }

    return $js;

}

/*
 * para checar se o detento já tem matrícula.
 * retorna true em caso de já possuir matrícula, ou false se não possuir.
 * @param  $iddet  int  o identificador do detento q se quer verificar a matrícula.
 * @return  bool  em caso de já possuir matrícula, ou false se não possuir.
 */
function ck_matr( $iddet = '' ) {

    $iddet = (int)$iddet;

    if ( empty( $iddet ) ) return false;

    $query = "SELECT SQL_NO_CACHE `matricula` FROM `detentos` WHERE `iddetento` = $iddet";

    $db    = SicopModel::getInstance();
    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    $cont = $query->num_rows;

    if ( $cont != 1 ) return false;

    $dados = '';
    $dados = $query->fetch_object();
    $matr  = $dados->matricula;

    if ( empty( $matr ) ) return false;

    return true;

}

/*
 * para checar se a matrícula já existe no sistema
 * retorna true em caso de existir ou falha na consulta, ou false se não existir
 * @param  $matr  int  a matrícula q se quer verificar. Pode conter os pontos e o traço
 * @return  bool  true em caso de existir ou falha na consulta, ou false se não existir.
 */
function ck_matr_exist( $matr = '' ) {

    $matr = (int)preg_replace( '/[-.]/', '', $matr );

    if ( empty( $matr ) ) return true;

    $query = "SELECT SQL_NO_CACHE `iddetento` FROM `detentos` WHERE `matricula` = $matr";

    $db    = SicopModel::getInstance();
    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return true;

    $cont = $query->num_rows;

    if ( $cont != 1 ) return false;

    return true;

}

/*
 * para checar se o cpf já existe no sistema
 * retorna true em caso de existir ou falha na consulta, ou false se não existir
 * @param  $matr  int  a matrícula q se quer verificar. Pode conter os pontos e o traço
 * @return  bool  true em caso de existir ou falha na consulta, ou false se não existir.
 */
function ck_cpf_exist( $cpf = '' ) {

    $cpf = (float)preg_replace( '/[-.]/', '', $cpf );

    if ( empty( $cpf ) ) return true;

    $query = "SELECT SQL_NO_CACHE `iddetento` FROM `detentos` WHERE `cpf` = $cpf";

    $db    = SicopModel::getInstance();
    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return true;

    $cont = $query->num_rows;

    if ( $cont != 1 ) return false;

    return true;

}

/*
 * para checar se o rg do visitante já existe cadastrado
 * retorna true em caso de existir ou falha na consulta, ou false se não existir
 * @param  $matr  int  a matrícula q se quer verificar. Pode conter os pontos e o traço
 * @return  bool  true em caso de existir ou falha na consulta, ou false se não existir.
 */
function ck_rgv_exist( $rg = '' ) {

    $rg = (int)preg_replace( '/[-.]/', '', $rg );

    if ( empty( $rg ) ) return true;

    $query = "SELECT SQL_NO_CACHE `idvisita` FROM `visitas` WHERE `rg_visit` = $rg";

    //return $query;

    $db    = SicopModel::getInstance();
    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return true;

    $cont = $query->num_rows;

    if ( $cont < 1 ) return false;

    return true;

}

/*
 * para retornar o caminho absoluto das imagens
 * retorna apenas o camino.
 * @param  $file_flag  int  indicador de qual pasta virá os arquivos: 1 = detentos; 2 = visitas
 * @return  string  o caminho absoluto.
 */
//function get_pics_path( $file_flag = 1 ) {
//
//    $pasta = 'detentos';
//    if ( $file_flag == 2 ) {
//        $pasta = 'visitas';
//    } else if ( $file_flag == 3 ) {
//        $pasta = 'system';
//    }
//
////    $caminho  = $_SERVER['SERVER_NAME'] . "/sicop_pics/$pasta/";
////    $http_pos = mb_strpos( $caminho, 'http://' );
////    if ( $http_pos === false ) $caminho = 'http://' . $caminho;
//
//    $caminho = "/sicop_pics/$pasta/";
//
//    return $caminho;
//
//}
//
//function get_pics_folder( $file_flag = 1 ) {
//
//    $img_path = get_pics_path( $file_flag );
//    $pasta    = $_SERVER['DOCUMENT_ROOT'] . $img_path;
//
//    return $pasta;
//
//}

function ck_pic( $foto_g, $foto_p, $reverse = false, $file_flag = 1, $pdf = false ) {

    $file_flag = (int)$file_flag;

    if ( empty ( $file_flag ) or $file_flag > 2 ) {
        $file_flag = 1;
    }

    $img_path_comp = '';

    if ( $pdf ) {
        $img_path_comp = SICOP_DOC_ROOT;
    }

    $img_path = $img_path_comp . SICOP_DET_IMG_PATH;
    $pasta    = SICOP_DET_FOLDER;

    if ( $file_flag == 2 ) {

        $img_path = $img_path_comp . SICOP_VISIT_IMG_PATH;
        $pasta    = SICOP_VISIT_FOLDER;

    }

    $no_photo    = 'nophoto.jpg';

    $file_foto_p = $pasta . $foto_p;

    $file_foto_g = $pasta . $foto_g;

    $foto_det    = $img_path . $foto_p;

    // se não for reverse retorna a foto pequena...
    if ( !$reverse ) {

        if ( empty( $foto_p ) || !is_file( $file_foto_p ) ) {

            $foto_det = $img_path . $foto_g;

            if ( empty( $foto_g ) || !is_file( $file_foto_g ) ) {

                $img_path = $img_path_comp . SICOP_SYS_IMG_PATH;
                $foto_det = $img_path . $no_photo;

            }

        }

    } else { // se for reverse retorna a foto grande

        $foto_det    = $img_path . $foto_g;

        if ( empty( $foto_g ) || !is_file( $file_foto_g ) ) {

            $foto_det = $img_path . $foto_p;

            if ( empty( $foto_p ) || !is_file( $file_foto_p ) ) {

                $img_path = SICOP_SYS_IMG_PATH;
                $foto_det = $img_path . $no_photo;

            }

        }

    }

    return $foto_det;

}

/*
 * Função para atualizar a foto, para a útima armazenada do banco
 * funcina com detentos e visitantes
 * @param  $uid        int  identificador do detento ou visitante
 * @param  $file_flag  int  indicador de qual pasta virá os arquivos: 1 = detentos; 2 = visitas
 * @return  bool  true se conseguiu atualizar ou false caso contrário.
 */
function set_last_pic( $uid, $file_flag = 1 ) {

    $uid = (int)$uid;
    if ( empty( $uid ) ) return false;

    $file_flag = (int)$file_flag;
    if ( empty( $file_flag ) ) return false;

    $tabela_principal = 'detentos';
    $uid_principal    = 'iddetento';
    $tabela_fotos     = 'det_fotos';
    $uid_fotos        = 'cod_detento';

    if ( $file_flag == 2 ) {

        $tabela_principal = 'visitas';
        $uid_principal    = 'idvisita';
        $tabela_fotos     = 'visita_fotos';
        $uid_fotos        = 'cod_visita';

    }

    $query = "UPDATE
                `$tabela_principal`
              SET
                `cod_foto` = ( SELECT `$tabela_fotos`.`id_foto` FROM `$tabela_fotos` WHERE `$tabela_fotos`.`$uid_fotos` = `$tabela_principal`.`$uid_principal` ORDER BY `data_add` DESC LIMIT 1 )
              WHERE
                `$uid_principal` = $uid
              LIMIT 1";

    $db  = SicopModel::getInstance();
    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    return true;

}


function get_pic_file_id( $pid = '', $file_flag = 1 ) {

    $query = "SELECT
                `foto_g`,
                `foto_p`
              FROM
                `det_fotos`
              WHERE
                `id_foto` = $pid
              LIMIT 1";

    $db  = SicopModel::getInstance();
    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    $cont = $query->num_rows;

    if ( $cont < 1 ) return false;

}

function get_pic_file( $uid = '', $date_limit = '', $file_flag = 1 ) {

    $uid = (int)$uid;
    if ( empty( $uid ) ) return false;

    $data_myslq = false;

    if ( !empty( $date_limit ) ) {

        if ( validaData( $date_limit, 'AAAA-MM-DD' ) ){
            $data_myslq = true;
        }

        if ( !$data_myslq ) {

            if ( !validaData( $date_limit, 'DD/MM/AAAA' ) ){
                $date_limit = false;
            }

        }

    }

    $data_value = '';
    if ( !empty( $date_limit ) ) {

        $data_value = "AND DATE( `data_add` ) >= '$date_limit'";

        if ( !$data_myslq ) {
            $data_value = "AND DATE( `data_add` ) >= STR_TO_DATE( '$date_limit', '%d/%m/%Y' )";
        }

    }

    $query = "SELECT
                `id_dp`,
                `cod_detento`,
                `dp_file`,
                `dp_file_p`
              FROM
                `detentos_pics`
              WHERE
                `cod_detento` = $uid
                $data_value
              ORDER BY
                `data_add` DESC
              LIMIT 1";

    $db  = SicopModel::getInstance();
    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    $cont = $query->num_rows;

    if ( $cont < 1 ) return false;

}

/*
 * Cria um link
 * @param  $title  string  descrição do link, o que fica entre as tags <a>, se ficar em branco será usado o endereço da página
 * @param  $href  string  o endereço depois de /sicop/<o que fica aqui>. se ficar em branco, pega o endereço da página atual
 * @param  $full  boll  se for empty preenche com os parametros $_SERVER['SERVER_NAME']. Senão Pegará somente o que foi
 *                      passado pelo usuário
 * @return  string  o link formatado.
 */
function link_pag( $title = '', $href = '', $full = '' ) {

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

/*
 * Função para redirecionar usando header
 * @param  $file  string  o arquivo, ou o caminho apos /sicop/ SEM o .php
 * @param  $qs  string  a query_string, se houver, para ser usada no redirecionamento.
 * @return  o redirecionamento.
 */
function redir( $file = 'index', $qs = '' ) {

    if ( empty( $file ) ) return false;

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

/*
 * Função para formatar a mensagem que será salva no log.
 * @param  $msg  array  um array contendo as informações para a formatação
 * @param  $save  bool  se não for empty, a mensagem gerada será salva automaticamente com a função salvaLog
 * @return  string a mensagem formatada ou o retorno de salvaLog, se a mensagem foi salva.
 */
function get_msg ( $msg, $save = '' ) {

    setlocale( LC_ALL, 'pt_BR', 'ptb', 'pt-BR', 'pt-br', 'PT-BR', 'pt_BR.utf-8', 'portuguese-brazil', 'bra' );
    date_default_timezone_set( 'America/Sao_Paulo' );

    $quebra   = PHP_EOL;
    $tipo     = !empty( $msg['tipo'] ) ? $msg['tipo'] : '' ;
    $msg_text = !empty( $msg['text'] ) ? $msg['text'] : '' ;
    $entre_ch = !empty( $msg['entre_ch'] ) ? $msg['entre_ch'] : '' ;
    $linha    = !empty( $msg['linha'] ) ? $msg['linha'] : '' ;

    $mensagem = '';

    if ( $tipo == 'atn' ) {

        $mensagem .= '<span class="desc_atencao">*** ATENÇÃO ***</span> -> ';

    } else if ( $tipo == 'perm' ) {

        $mensagem .= '<span class="desc_atencao">*** ATENÇÃO ***</span> -> Tentativa de acesso à página sem permissões. ';

        if ( !empty ( $entre_ch ) ) {

            $mensagem .= "( $entre_ch )";

        }

        $mensagem .= $quebra . $quebra;

    } else if ( $tipo == 'err' ) {

        $mensagem .= '[ <span class="desc_erro">*** ERRO ***</span> ]' . $quebra;

    } else if ( $tipo == 'desc' ) {

        if ( empty ( $entre_ch ) ) return false;

        $mensagem .= "[ $entre_ch ]" . $quebra;

    } else {

        return false;

    }

    if ( !empty( $msg_text ) ) {
        $mensagem .= $msg_text;
        $mensagem .= $quebra;
        $mensagem .= $quebra;
    }

    if ( !empty( $linha ) ) {

        $mensagem .= "Linha: $linha";
        $mensagem .= $quebra;
        $mensagem .= $quebra;

    }

    $mensagem .= 'Página: ' . link_pag();
    $mensagem .= $quebra;

    if ( !empty( $_SERVER['HTTP_REFERER'] ) ) {

        $ref = link_pag( $_SERVER['HTTP_REFERER'], $_SERVER['HTTP_REFERER'], 1 );
        $ref = "Referer: $ref";
        $mensagem .= $ref;
        $mensagem .= $quebra;

    }

    $mensagem .= $quebra;
    $mensagem .= 'Data: ' . date('d/m/Y \à\s H:i:s');
    $mensagem .= $quebra;

    if ( !empty( $save ) ) {

        return salvaLog( $mensagem );

    } else {

        return $mensagem;

    }

}

/*
 * Função para pegar os valores inseridos pelo usuário em um post ou get
 * @param  $arr_user  array  o array post ou get, ou outro array que contenha valores  
 * @return  string  contendo uma mensagem formatada
 */
function valor_user ( $arr_user ) {

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


/**
 * função para abreviar nomes.
 * so abrevia nomes maiores do que $limit, e enquanto for maior do que $limit.
 * @param string $texto o texto de entrada, para análise
 * @param int $limit o tamanho máximo que o texto pode ter para não ser abreviado
 * @return string o texo abreviado, caso o comprimento seja maior que $limit, ou o texto sem alteração
 */
function abrevia_texto( $texto, $limit ) {

    // retirar espaços do começo e fim de $texto
    $texto = trim( $texto );

    // verificar o comprimento de $texto;
    $length = strlen( $texto );

    // se o $texto for menor do que $limit, retrona $texto
    if ( $length <= $limit ) return $texto;

    // para armazenar o nome completo, com as abreviações
    $nome_abrev = '';

    // para armazenar os nomes que foram abreviados
    $abrev = ' ';

    // divide $texto em um array
    $pcs_texto = explode( ' ', $texto );

    /**
     * retira o primeiro nome e guarda em uma variavel
     * para não entrar na manipulação das abreviações
     */
    $primeiro_nome = array_shift( $pcs_texto );

    /**
     * retira o útimo nome e guarda em uma variavel
     * para não entrar na manipulação das abreviações
     */
    $ultimo_nome   = array_pop( $pcs_texto );

    // acrescenta um espaço no começo de $ultimo_nome
    $ultimo_nome   = ' ' . $ultimo_nome;

    foreach ( $pcs_texto as &$value ) {

        /**
         * retira palavras com menos de 2 caracteres para não
         * aparecerem nas abreviações
         */
        if ( strlen( $value ) <= 2 ) continue;

        // pega a primeira letra da palavra, que será a abreviação
        $letra = substr( $value, 0, 1 );

        // acrescenta nos nomes abreviados
        $abrev .= $letra . '. ';

        /**
         * elimina do array o nome que acabou de ser tratado
         * para que não apareca no implode. no caso, é sempre
         * o primeiro nome
         */
        array_shift( $pcs_texto );

        /**
         * grava todos os nomes abreviados junto com o primeiro e
         * último nomes para ser feito o cálculo do comprimento de
         * da string
         */
        $nome_abrev = $abrev . implode( ' ', $pcs_texto );

        /**
         * verifica se a string já possui menos que $limit
         * casso possua, encerra a execução do loop
         */
        $length = strlen( $primeiro_nome . $nome_abrev . $ultimo_nome );
        if ( $length <= $limit ) break;

    }

    // monta o nome e retorna
    $nome_abrev = $primeiro_nome . $nome_abrev . $ultimo_nome;
    return $nome_abrev;

}


function get_html_bt( $bt_value = '', $bt_id = '', $bt_name = '', $bt_type = '', $bt_class = '', $bt_onclick = '' ) {

    $class   = 'class="form_bt"';
    $type    = 'type="button"';
    $name    = '';
    $id      = '';
    $value   = '';
    $onclick = '';

    if ( !empty ( $bt_class ) )   $class   = 'class="' . $bt_onclick . '"';
    if ( !empty ( $bt_type ) )    $type    = 'type="' . $bt_type . '"';
    if ( !empty ( $bt_name ) )    $name    = 'name="' . $bt_name . '"';
    if ( !empty ( $bt_id ) )      $id      = 'id="' . $bt_id . '"';
    if ( !empty ( $bt_value ) )   $value   = 'value="' . $bt_value . '"';
    if ( !empty ( $bt_onclick ) ) $onclick = 'onclick="' . $bt_onclick . '"';

    return "<input $class $type $name $id $value $onclick />";

}

?>