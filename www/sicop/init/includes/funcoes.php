<?php
if ( !isset( $_SESSION ) ) session_start();
$quebra = PHP_EOL;

function formata_num( $numero, $num_dig = 1 ) {

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

function formata_num_sedex( $numero ) {

    $num_f = '';

    if ( !empty( $numero ) ) {

        $num = substr( $numero, 2, -2 );
        $pre = substr( $numero, 0, 2 );
        $suf = substr( $numero, -2 );

        if ( strlen( $num ) > 1 ) {
            $num = preg_replace( '/([0-9]{3})([0-9]{3})([0-9]{3})/', '\\1.\\2.\\3', $num );
        }

//        if ( strlen( $num ) > 1 ) {
//            $num = number_format( $num, 0, '', '.' );
//        }
//
//        $num = str_pad( $num, 11, '0', STR_PAD_LEFT );

        $num_f = $pre . ' ' . $num . ' ' . $suf;

    }

    return $num_f;

}

function ck_msg() {

    $iduser = get_session( 'user_id', 'int' );
    $n_msg  = get_session( 'n_msg', 'int' );

    $rmsg = '<img src="' . SICOP_SYS_IMG_PATH . 'msg_read.png" width="20" height="13" style="vertical-align: middle" />';

    if ( $n_msg >= 2 ) {

        $qmsg = "SELECT `msg`.`idmsg` FROM `msg` WHERE `msg_para` = $iduser AND `msg_para_lida` = FALSE AND `msg_para_exc` = FALSE  AND `msg_block` = FALSE";

        $db   = SicopModel::getInstance();
        $qmsg = $db->query( $qmsg );
        $db->closeConnection();

        $cont_msg = $qmsg->num_rows;

        $rmsg = '<a href="' . SICOP_ABS_PATH . 'msg/msg.php"><img id="msg" src="' . SICOP_SYS_IMG_PATH . 'msg_read.png" width="20" height="13" /></a>';

        if ( $cont_msg >= 1 ) { // se o número de ocorrências for maior ou igual a 1, mostra a mensagem
            $rmsg = '<a href="' . SICOP_ABS_PATH . 'msg/msg.php" title="Você possui ' . $cont_msg . ' mensagem(ns)"><img src="' . SICOP_SYS_IMG_PATH . 'msg_new.png" alt="Você possui ' . $cont_msg . ' mensagem(ns)" width="20" height="13" style="vertical-align: middle" /></a>';
        }

    }

    return $rmsg;
}

function data_f( $timestamp = NULL ) {

    if ( empty( $timestamp ) )
        $timestamp = time();

    $dia = date( 'd', $timestamp );
    $mes = date( 'm', $timestamp );
    $ano = date( 'Y', $timestamp );

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

    $data = $dia . ' de ' . $mes . ' de ' . $ano;

    return $data;
}

function get_mes_f( $timestamp = NULL, $num_mes = NULL, $incl_ano = false, $reduzir = false ) {

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

function dia_semana_f( $timestamp = NULL, $comp = NULL ) {

    if ( empty( $timestamp ) )
        $timestamp = time();

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

function numera_of( $coment = NULL ) {

    $iduser  = get_session( 'user_id', 'int' );
    $idsetor = get_session( 'idsetor', 'int' );

    $db     = SicopModel::getInstance();
    $coment = empty( $coment ) ? 'NULL' : "'" . $db->escape_string( $coment ) . "'";

    $query_in = "INSERT INTO
                   `numeroof`
                   (
                     `numero_of`,
                     `ano`,
                     `iduser`,
                     `idsetor`,
                     `coment`
                   )
                 VALUES
                   (
                     ( SELECT IFNULL( MAX( `num`.`numero_of` ), 0 ) FROM `numeroof` `num` WHERE `ano` = YEAR( NOW() ) ) + 1,
                     YEAR( NOW() ),
                     $iduser,
                     $idsetor,
                     $coment
                   )";

    $db->query( $query_in );
    $id_l = $db->lastInsertId();

    $q_numof = "SELECT `numero_of`, `ano` FROM `numeroof` WHERE `idnumof` = $id_l";
    $q_numof = $db->query( $q_numof );

    $db->closeConnection();

    $d_numof = '';
    $d_numof = $q_numof->fetch_object();

    $num_of = array( );

    $num_of['num'] = $d_numof->numero_of . '/' . $d_numof->ano;

    $num_of['id'] = $id_l;

    return $num_of;
}

function numera_apcc( $coment = NULL ) {

    $iduser  = get_session( 'user_id', 'int' );
    $idsetor = get_session( 'idsetor', 'int' );

    $db     = SicopModel::getInstance();
    $coment = empty( $coment ) ? 'NULL' : "'" . $db->escape_string( $coment ) . "'";

    $query_in = "INSERT INTO
                   `numeroapcc`
                   (
                     `numero_apcc`,
                     `ano`,
                     `iduser`,
                     `idsetor`,
                     `coment`
                   )
                 VALUES
                   (
                     ( SELECT IFNULL( MAX( `num`.`numero_apcc` ), 0 ) FROM `numeroapcc` `num` WHERE `ano` = YEAR( NOW() ) ) + 1,
                     YEAR( NOW() ),
                     $iduser,
                     $idsetor,
                     $coment
                   )";

    $db->query( $query_in );
    $id_l = $db->lastInsertId();

    $q_numapcc = "SELECT `numero_apcc`, `ano` FROM `numeroapcc` WHERE `idnumapcc` = $id_l";
    $q_numapcc = $db->query( $q_numapcc );

    $db->closeConnection();

    $d_numapcc = '';
    $d_numapcc = $q_numapcc->fetch_object();

    $num_apcc = array( );

    $num_apcc['num'] = $d_numapcc->numero_apcc . '/' . $d_numapcc->ano;

    $num_apcc['id'] = $id_l;

    return $num_apcc;
}

/*
 * call this stuff at every page that user sees.
 * then you would just do something like that
 * $history=$_SESSION["history"];
 * header("Location : $history[Elements of array -2]");
 */

function keepHistory() {

    if ( !isset( $_SESSION ) ) session_start();
    if ( !isset( $_SESSION['history'] ) ) $_SESSION['history'] = '';

    $pag_atual = $_SERVER['PHP_SELF'];
    $qs = $_SERVER['QUERY_STRING'];

    if ( !empty( $qs ) ) {
        $pag_atual .= '?' . $qs;
    }

    $history = $_SESSION['history'];

    $fim_h = is_array( $history ) ? end( $history ) : $history;

    if ( $pag_atual != $fim_h ) {

        $history[] = $pag_atual;

        $cont_h = count( $history );

        if ( $cont_h > 10 ) {
            $rest = $cont_h - 10;
            $history = array_slice( $history, $rest );
        }

        $_SESSION['history'] = $history;
    }
}

function returnHistory( $retorno = 1 ) {

    $history = $_SESSION["history"];
    $cont_h = count( $history );
    $retorno += 1;
    return $history[$cont_h - $retorno];
}

function highlight( $palavra, $texto ) {

    if ( $palavra ) {

        mb_internal_encoding( 'UTF-8' );
        mb_regex_encoding( 'UTF-8' );

        $texto_sa = remove_acentos( $texto );
        $palavra_sa = remove_acentos( $palavra );

        $palavra_arr = explode( ' ', $palavra_sa );

        $texto_f = $texto_sa;

        /*
         * o '&' ( 'e' comercial ) suprime a necessidade de declarar o índice
         * entao
         * foreach ( $palavra_arr as $indice => $valor ) {
         * passa a ser
         * foreach ( $palavra_arr as &$valor ) {
         */
        foreach ( $palavra_arr as &$valor ) {

            if ( $valor == NULL )
                continue;

            $posicao = mb_strpos( $texto_sa, $valor );

            $palavra_orig = mb_substr( $texto, $posicao, mb_strlen( $valor ) );

            $texto_f = mb_ereg_replace( "$valor", "<span class='texto_busca'>$palavra_orig</span>", $texto_f );
        }

        return $texto_f;
    } else {

        return $texto;
    }
}

function highlight2( $t, $k, $c = 'texto_busca' ) {
//    return preg_replace( sprintf( '/\b(%s)\b/i', is_array( $k ) ? implode( '|', $k ) : $k ), sprintf( '<span class="%s">$1</span>', $c ), $t );
//    return preg_replace( sprintf( '/(%s)/i', is_array( $k ) ? implode( '|', $k ) : $k ), sprintf( '<span class="%s">$1</span>', $c ), $t );

    $pattern = sprintf( '/(%s)/i', is_array( $k ) ? implode( '|', $k ) : $k );
    $replacement = sprintf( '<span class="%s">$1</span>', $c );
    $string = $t;

    return preg_replace( $pattern, $replacement, $string );
}

function redirecionar( $url, $tempo ) {
    $url = str_replace( '&amp;', '&', $url );

    if ( $tempo > 0 ) {
        header( "Refresh: $tempo; URL=$url" );
    } else {
        @ob_flush();
        @ob_end_clean();
        header( "Location: $url" );
        exit;
    }
}

function pegaIdade( $dataNasc ) {

    if ( !$dataNasc ) return $idade = '';

    $dia = '';
    $mes = '';
    $ano = '';

    $diaNasc = '';
    $mesNasc = '';
    $anoNasc = '';

    list ( $dia, $mes, $ano ) = explode( '/', date( 'd/m/Y' ) );
    list ( $diaNasc, $mesNasc, $anoNasc ) = explode( '/', $dataNasc );
    $idade = $ano - $anoNasc;
    $idade = ( ($mes < $mesNasc ) OR ( ( $mes == $mesNasc ) AND ( $dia < $diaNasc ) ) ) ? --$idade : $idade;
    return ' - ' . $idade . ' anos';

}

/* function remove_acentos( $string ) {
  // Remove acentos sobre a string
  $string = preg_replace( "/[ÁÀÂÃÄ]/", "A", $string);
  $string = preg_replace( "/[áàâãäªa]/", "a", $string);
  $string = preg_replace( "/[ÉÈÊË]/", "E", $string);
  $string = preg_replace( "/[éèêëe]/", "e", $string);
  $string = preg_replace( "/[ÍÌÎÏ]/", "I", $string);
  $string = preg_replace( "/[íìîïi]/", "i", $string);
  $string = preg_replace( "/[ÓÒÔÕÖ]/", "O", $string);
  $string = preg_replace( "/[óòôõöºo]/", "o", $string);
  $string = preg_replace( "/[ÚÙÛÜ]/", "U", $string);
  $string = preg_replace( "/[úùûüu]/", "u", $string);
  $string = preg_replace( "/[Ç]/", "C", $string);
  $string = preg_replace( "/[ç]/", "c", $string);
  $string = preg_replace( "/[´`~^¨]/", "", $string);
  //$string = strtoupper($string);
  return $string;
  }

  function remove_acentos( $string ) {

  $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß',
  'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā',
  'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ',
  'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ',
  'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ',
  'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż',
  'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ',
  'ǿ', '´', '`', '~', '^', '¨', '\'', '\"');

  $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's',
  'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A',
  'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G',
  'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L',
  'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's',
  'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z',
  'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O',
  'o', '', '', '', '', '', '', '');

  return str_replace($a, $b, $string);

  } */

function gera_timestamp( $data ) {
    $partes = explode( '/', $data );
    return mktime( 0, 0, 0, $partes[1], $partes[0], $partes[2] );
}

function tratasn( $str ) {

    if ( $str == 0 ) {
        $str = 'NÃO';
    } else if ( $str == 1 ) {
        $str = 'SIM';
    } else if ( empty( $str ) ) {
        $str = '';
    }

    return $str;

}

function trata_tipo_aud( $str ) {

    switch ( $str ) {
        case '1':
            $str = 'JUDICIAL';
            break;
        case '2':
            $str = 'MÉDICA';
            break;
        case '3':
            $str = 'IML';
            break;
        case '4':
            $str = 'EXAME/PERÍCIA JUDICIAL';
            break;
        case '5':
            $str = 'DELEGACIA/CADEIA PÚBLICA';
            break;
        case '6':
            $str = 'PERÍCIA INSS';
            break;
        case '7':
            $str = 'NOTIFICAÇÃO/CITAÇÃO CADEIA PÚBLICA';
            break;
        case '8':
            $str = 'SEGURO DESEMPREGO / PIS/PASEP';
            break;
    }

    return $str;

}

function trata_sit_aud( $sit_aud ) {

    $aud = array( );

    $aud['sitaud']     = '';
    $aud['css_class']  = 'aud_just';
    $aud['corfontaud'] = '';


    switch ( $sit_aud ) {
        case '11': //audiencia ativa
            $aud['sitaud']     = 'ATIVA';
            $aud['css_class']  = 'aud_ativa';
            $aud['corfontaud'] = '#000000';
            break;
        case '12': //audiencia cancelada
            $aud['sitaud']     = 'CANCELADA';
            $aud['css_class']  = 'aud_cancel';
            $aud['corfontaud'] = '#CC9900';
            break;
        case '13': //audiencia justificada
            $aud['sitaud']     = 'JUSTIFICADA';
            $aud['css_class']  = 'aud_just';
            $aud['corfontaud'] = '#FF0000';
            break;
    }

    return $aud;

}

function trata_sit_pda( $str ) {

    switch ( $str ) {
        case '1':
            $str = 'Em andamento';
            break;
        case '2':
            $str = 'Concluído';
            break;
        case '3':
            $str = 'Sobrestado';
            break;
    }

    return $str;
}

function trata_sit_sedex( $str ) {

    switch ( $str ) {
        case '1':
            $str = 'RECEBIDO';
            break;
        case '2':
            $str = 'ENCAMINHADO P/ INCLUSÃO';
            break;
        case '3':
            $str = 'SEPARADO P/ DEVOLUÇÃO';
            break;
        case '4':
            $str = 'DEVOLVIDO';
            break;
        case '5':
            $str = 'ENTREGUE';
            break;
    }

    return $str;
}

function muda_cor_pda( $data_reabilitacao, $sit_pda ) {

    $cor = '#000000';

    if ( $data_reabilitacao > date( 'Y-m-d' ) ) { //mudar a cor das sindicâncias que ainda não passaram da data da reabilitação
        $cor = '#FF0000';
    } else if ( $sit_pda == 1 ) {
        $cor = '#CC9900';
    } else if ( $sit_pda == 3 ) {
        $cor = '#800080';
    } else {
        $cor = '#000000';
    }

    return $cor;
}

function format_num_pda( $num, $ano, $local = '' ) {

    $num = (int)$num;
    $ano = (int)$ano;

    if ( empty( $num ) or empty( $ano ) ) {
        return false;
    }

    $numpda = empty( $local ) ? $num . '/' . $ano : $num . '/' . $ano . '-' . $local;

    return $numpda;
}

function retira_cerquilha( $url ) {

    $n_url = strstr( $url, '#', true );

    if ( $n_url ) {
        return $n_url;
    } else {
        return $url;
    }
}

function tratasnv( $str ) {

    if ( empty( $str ) ) {
        $str = 'FALTA';
    } else if ( $str == '1' ) {
        $str = 'OK';
    } else if ( $str == '2' ) {
        $str = 'DESNEC';
    }

    return $str;
}

function manipula_sit_det_id( $tipo_mov_in, $tipo_mov_out, $iddestino ) {

    $sitat = '';

    if ( empty( $tipo_mov_in ) ) { // se não tiver $tipo_mov_in a situação é A CHEGAR

        $sitat = SICOP_SIT_DET_ACEHGAR;

    } else if ( !empty( $tipo_mov_in ) and ( empty( $tipo_mov_out ) or $tipo_mov_out == 4 ) ) { // se não tiver $tipo_mov_out ou for IE a situação é NA CASA OU TRANSITO NA CASA

        $sitat = SICOP_SIT_DET_NA;

        if ( $tipo_mov_in == 2 ) { // se o $tipo_mov_in for IT  a situação é TRANSITO NA CASA

            $sitat = SICOP_SIT_DET_TRANA;

        }

    } else if ( !empty( $tipo_mov_in ) and !empty( $tipo_mov_out ) ) { // se tiver $tipo_mov_out a situação é TRANSFERIDO, EXCLUIDO, TRANSITO DA CASA ou TRANSITO NA CASA DA CASA

        if ( $tipo_mov_out == 7 or $tipo_mov_out == 8 ) { // se o $tipo_mov_out for ER ou EE a situação é TRANSFERIDO

            $sitat = SICOP_SIT_DET_TRANSF;

        } else if ( $tipo_mov_out == 5 ) { // se o $tipo_mov_out for EX ...

            if ( $iddestino >= 200 and $iddestino < 300 ) {

                // evadido
                $sitat = SICOP_SIT_DET_EVADIDO;

            } else if ( $iddestino >= 300 and $iddestino < 400 ) {

                // falecido
                $sitat = SICOP_SIT_DET_FALECIDO;

            // $iddestino >= 100 and $iddestino < 200 ou > 400 ou < 100
            } else {

                // excluido
                $sitat = SICOP_SIT_DET_EXCLUIDO;

            }

        } else if ( $tipo_mov_out == 6 ) { // se o $tipo_mov_out for ET ...

            $sitat = SICOP_SIT_DET_TRADA;

            if ( $tipo_mov_in == 2 ) { // se o $tipo_mov_in for IT  a situação é TRANSITO NA CASA DA CASA

                $sitat = SICOP_SIT_DET_TRANADA;

            }

        }

    }

    return $sitat;

}

function manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino ) {

    $det = array();

    $det['sitat']     = '';
    $det['corfontd']  = '#000000';
    $det['css_class'] = 'det_nacasa';

    $sit_det = manipula_sit_det_id( $tipo_mov_in, $tipo_mov_out, $iddestino );

    switch ( $sit_det ) {

        case SICOP_SIT_DET_ACEHGAR:
            $det['corfontd']  = '#990000';
            $det['css_class'] = 'det_achegar';
            $det['sitat']     = 'A CHEGAR';
            break;

        default:
        case SICOP_SIT_DET_NA:
            $det['corfontd']  = '#000000';
            $det['css_class'] = 'det_nacasa';
            $det['sitat']     = 'NA CASA';
            break;

        case SICOP_SIT_DET_TRANA:
            $det['corfontd']  = '#0000FF';
            $det['css_class'] = 'det_trana';
            $det['sitat']     = 'TRANSITO NA CASA';
            break;

        case SICOP_SIT_DET_TRADA:
            $det['corfontd']  = '#FF0000';
            $det['css_class'] = 'det_trada';
            $det['sitat']     = 'TRANSITO DA CASA';
            break;

        case SICOP_SIT_DET_TRANADA:
            $det['corfontd']  = '#FF0000';
            $det['css_class'] = 'det_tranada';
            $det['sitat']     = 'TRANSITO NA CASA DA CASA';
            break;

        case SICOP_SIT_DET_TRANSF:
            $det['corfontd']  = '#CC9900';
            $det['css_class'] = 'det_transf';
            $det['sitat']     = 'TRANSFERIDO';
            break;

        case SICOP_SIT_DET_EVADIDO:
            $det['corfontd']  = '#0080C0';
            $det['css_class'] = 'det_evadido';
            $det['sitat']     = 'EVADIDO';
            break;

         case SICOP_SIT_DET_FALECIDO:
            $det['corfontd']  = '#800080';
            $det['css_class'] = 'det_falecido';
            $det['sitat']     = 'FALECIDO';
            break;

        case SICOP_SIT_DET_EXCLUIDO:
            $det['corfontd']  = '#009900';
            $det['css_class'] = 'det_excl';
            $det['sitat']     = 'EXCLUIDO';
            break;

    }

    return $det;

}

function manipula_sit_det_l( $tipo_mov_in, $procedencia, $data_incl, $tipo_mov_out, $iddestino ) {

    $det = array( );

    $det['sitat']       = '';
    $det['corfontd']    = '#000000';
    $det['css_class']   = 'det_nacasa';
    $det['procedencia'] = $procedencia;
    $det['data_incl']   = $data_incl;


    $sit_det = manipula_sit_det_id( $tipo_mov_in, $tipo_mov_out, $iddestino );

    switch ( $sit_det ) {

        case SICOP_SIT_DET_ACEHGAR:
            $det['corfontd']  = '#990000';
            $det['css_class'] = 'det_achegar';
            $det['sitat']     = 'A CHEGAR';
            break;

        default:
        case SICOP_SIT_DET_NA:
            $det['corfontd']    = '#000000';
            $det['css_class']   = 'det_nacasa';
            $det['sitat']       = 'NA CASA';
            break;

        case SICOP_SIT_DET_TRANA:
            $det['corfontd']    = '#0000FF';
            $det['css_class']   = 'det_trana';
            $det['sitat']       = 'TRANSITO NA CASA';
            break;

        case SICOP_SIT_DET_TRADA:
            $det['corfontd']  = '#FF0000';
            $det['css_class'] = 'det_trada';
            $det['sitat']     = 'TRANSITO DA CASA';
            break;

        case SICOP_SIT_DET_TRANADA:
            $det['corfontd']  = '#FF0000';
            $det['css_class'] = 'det_tranada';
            $det['sitat']     = 'TRANSITO NA CASA DA CASA';
            break;

        case SICOP_SIT_DET_TRANSF:
            $det['corfontd']  = '#CC9900';
            $det['css_class'] = 'det_transf';
            $det['sitat']     = 'TRANSFERIDO';
            break;

        case SICOP_SIT_DET_EVADIDO:
            $det['corfontd']  = '#0080C0';
            $det['css_class'] = 'det_evadido';
            $det['sitat']     = 'EVADIDO';
            break;

         case SICOP_SIT_DET_FALECIDO:
            $det['corfontd']  = '#800080';
            $det['css_class'] = 'det_falecido';
            $det['sitat']     = 'FALECIDO';
            break;

        case SICOP_SIT_DET_EXCLUIDO:
            $det['corfontd']  = '#009900';
            $det['css_class'] = 'det_excl';
            $det['sitat']     = 'EXCLUIDO';
            break;

    }

    if ( !empty( $det['data_incl'] ) ) {
        $timestamp = strtotime( $det['data_incl'] );
        $det['data_incl'] = date( 'd/m/Y', $timestamp );
    }

    return $det;

}

function manipula_sit_det_le( $tipo_mov_in, $procedencia, $data_incl, $tipo_mov_out, $iddestino, $destino, $data_excl ) {

    $det = array();

    $det['sitat']       = '';
    $det['corfontd']    = '#000000';
    $det['css_class']   = 'det_nacasa';
    $det['procedencia'] = $procedencia;
    $det['data_incl']   = $data_incl;
    $det['destino']     = $destino;
    $det['data_excl']   = $data_excl;

    $sit_det = manipula_sit_det_id( $tipo_mov_in, $tipo_mov_out, $iddestino );

    switch ( $sit_det ) {

        case SICOP_SIT_DET_ACEHGAR:
            $det['corfontd']  = '#990000';
            $det['css_class'] = 'det_achegar';
            $det['sitat']     = 'A CHEGAR';
            break;

        default:
        case SICOP_SIT_DET_NA:
            $det['corfontd']    = '#000000';
            $det['css_class']   = 'det_nacasa';
            $det['sitat']       = 'NA CASA';
            break;

        case SICOP_SIT_DET_TRANA:
            $det['corfontd']    = '#0000FF';
            $det['css_class']   = 'det_trana';
            $det['sitat']       = 'TRANSITO NA CASA';
            break;

        case SICOP_SIT_DET_TRADA:
            $det['corfontd']  = '#FF0000';
            $det['css_class'] = 'det_trada';
            $det['sitat']     = 'TRANSITO DA CASA';
            break;

        case SICOP_SIT_DET_TRANADA:
            $det['corfontd']  = '#FF0000';
            $det['css_class'] = 'det_tranada';
            $det['sitat']     = 'TRANSITO NA CASA DA CASA';
            break;

        case SICOP_SIT_DET_TRANSF:
            $det['corfontd']  = '#CC9900';
            $det['css_class'] = 'det_transf';
            $det['sitat']     = 'TRANSFERIDO';
            break;

        case SICOP_SIT_DET_EVADIDO:
            $det['corfontd']  = '#0080C0';
            $det['css_class'] = 'det_evadido';
            $det['sitat']     = 'EVADIDO';
            break;

         case SICOP_SIT_DET_FALECIDO:
            $det['corfontd']  = '#800080';
            $det['css_class'] = 'det_falecido';
            $det['sitat']     = 'FALECIDO';
            break;

        case SICOP_SIT_DET_EXCLUIDO:
            $det['corfontd']  = '#009900';
            $det['css_class'] = 'det_excl';
            $det['sitat']     = 'EXCLUIDO';
            break;

    }

    if ( !empty( $det['data_incl'] ) ) {

        $timestamp = strtotime( $det['data_incl'] );
        $det['data_incl'] = date( 'd/m/Y', $timestamp );

    }

    if ( !empty( $det['data_excl'] ) ) {

        $timestamp = strtotime( $det['data_excl'] );
        $det['data_excl'] = date( 'd/m/Y', $timestamp );

    }

    return $det;

}

function manipula_sit_det_c( $tipo_mov_in, $procedencia, $data_incl, $tipo_mov_out, $iddestino, $destino, $data_excl ) {

    $det = array( );

    $det['sitat']       = '';
    $det['corfontd']    = '#000000';
    $det['css_class']   = 'det_nacasa';
    $det['procedencia'] = !empty( $procedencia ) ? 'Procedência: ' . $procedencia : '';
    $det['data_incl']   = $data_incl;
    $det['destino']     = '';
    $det['data_excl']   = '';
    $rotulo_excl        = '';

    $sit_det = manipula_sit_det_id( $tipo_mov_in, $tipo_mov_out, $iddestino );

    switch ( $sit_det ) {

        case SICOP_SIT_DET_ACEHGAR:
            $det['corfontd']  = '#990000';
            $det['css_class'] = 'det_achegar';
            $det['sitat']     = 'A CHEGAR';
            break;

        default:
        case SICOP_SIT_DET_NA:
            $det['corfontd']    = '#000000';
            $det['css_class']   = 'det_nacasa';
            $det['sitat']       = 'NA CASA';
            break;

        case SICOP_SIT_DET_TRANA:
            $det['corfontd']    = '#0000FF';
            $det['css_class']   = 'det_trana';
            $det['sitat']       = 'TRANSITO NA CASA';
            break;

        case SICOP_SIT_DET_TRADA:
            $det['corfontd']  = '#FF0000';
            $det['css_class'] = 'det_trada';
            $det['sitat']     = 'TRANSITO DA CASA';
            $det['destino']   = 'Local de Transito: ' . $destino;
            $det['data_excl'] = $data_excl;
            $rotulo_excl      = 'Data do Transito: ';
            break;

        case SICOP_SIT_DET_TRANADA:
            $det['corfontd']  = '#FF0000';
            $det['css_class'] = 'det_tranada';
            $det['sitat']     = 'TRANSITO NA CASA DA CASA';
            $det['destino']   = 'Local de Transito: ' . $destino;
            $det['data_excl'] = $data_excl;
            $rotulo_excl      = 'Data do Transito: ';
            break;

        case SICOP_SIT_DET_TRANSF:
            $det['corfontd']  = '#CC9900';
            $det['css_class'] = 'det_transf';
            $det['sitat']     = 'TRANSFERIDO';
            $det['destino']   = 'Destino: ' . $destino;
            if ( empty( $destino ) ) $det['destino'] = 'Destino: ' . $procedencia;
            $det['data_excl'] = $data_excl;
            $rotulo_excl      = 'Data da Exclusão: ';
            break;

        case SICOP_SIT_DET_EVADIDO:
            $det['corfontd']  = '#0080C0';
            $det['css_class'] = 'det_evadido';
            $det['sitat']     = 'EVADIDO';
            $det['destino']     = 'Destino: ' . $destino;
            $det['data_excl']   = $data_excl;
            $rotulo_excl        = 'Data da Exclusão: ';
            break;

         case SICOP_SIT_DET_FALECIDO:
            $det['corfontd']  = '#800080';
            $det['css_class'] = 'det_falecido';
            $det['sitat']     = 'FALECIDO';
            $det['destino']     = 'Destino: ' . $destino;
            $det['data_excl']   = $data_excl;
            $rotulo_excl        = 'Data da Exclusão: ';
            break;

        case SICOP_SIT_DET_EXCLUIDO:
            $det['corfontd']  = '#009900';
            $det['css_class'] = 'det_excl';
            $det['sitat']     = 'EXCLUIDO';
            $det['destino']     = 'Destino: ' . $destino;
            $det['data_excl']   = $data_excl;
            $rotulo_excl        = 'Data da Exclusão: ';
            break;

    }

    if ( !empty( $det['data_incl'] ) ) {
        $timestamp = strtotime( $det['data_incl'] );
        $det['data_incl'] = 'Data da inclusão: ' . date( 'd/m/Y', $timestamp );
    }

    if ( !empty( $det['data_excl'] ) ) {
        $timestamp = strtotime( $det['data_excl'] );
        $det['data_excl'] = $rotulo_excl . date( 'd/m/Y', $timestamp );
    }

    return $det;
}

function get_sit_visita( $revog, $data_fim ) {

    $visit = array();
    $visit['suspenso']  = false;
    $visit['excluido']  = false;
    $visit['css_class'] = 'visit_ativa';
    $visit['css_dest']  = 'visit_ativa_destaque';
    $visit['sit_v']     = 'ATIVA';

    if ( $revog == 1 or is_null( $revog ) ) return $visit;

    $data_hoje = date('Y-m-d');

    if ( !empty( $data_fim ) ) {
        if ( $data_fim <= $data_hoje ) {
            return $visit;
        }
    }
    //echo msg_js( "'" . $data_fim . "'" );

    $visit['suspenso']  = true;
    $visit['css_class'] = 'visit_susp';
    $visit['css_dest']  = 'visit_susp_destaque';
    $visit['sit_v']     = 'SUSPENSA';

    if ( empty( $data_fim ) ) { //se o periodo estiver vazio, é visita excluida

        $visit['suspenso']  = false;
        $visit['excluido']  = true;
        $visit['css_class'] = 'visit_excl';
        $visit['css_dest']  = 'visit_excl_destaque';
        $visit['sit_v']     = 'EXCLUIDA';

    }

    return $visit;

}


function manipula_sit_visia( $idvist ) {

    $id = empty( $idvist ) ? '' : (int)$idvist;

    if ( empty( $id ) ) return false;

    $visit = array();
    $visit['suspenso']  = false;
    $visit['excluido']  = false;
    $visit['corfontv']  = '#000000';
    $visit['css_class'] = 'visit_ativa';
    $visit['sit_v']     = 'ATIVA';

    $query = "SELECT
                `periodo`
              FROM
                `visita_susp`
              WHERE
                `cod_visita` = $id
                AND
                ( ( CURDATE() BETWEEN `data_inicio` AND ADDDATE( `data_inicio`, `periodo` ) )
                  OR
                ( CURDATE() >= `data_inicio` AND ISNULL( ADDDATE( `data_inicio`, `periodo` ) ) ) )
                AND
                `revog` = FALSE
              ORDER BY
                ADDDATE( `data_inicio`, `periodo` ) ASC
              LIMIT 1";

    $db    = SicopModel::getInstance();
    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return $visit;

    $cont = $query->num_rows;

    if ( $cont != 1 ) return $visit;

    $dados = '';
    $dados = $query->fetch_object();

    $visit['suspenso']  = true;
    $visit['corfontv']  = '#CC9900';
    $visit['css_class'] = 'visit_susp';
    $visit['sit_v']     = 'SUSPENSA';

    $periodo = $dados->periodo;

    if ( empty( $periodo ) ) { //se o periodo estiver vazio, é visita excluida

        $visit['excluido']  = true;
        $visit['corfontv']  = '#FF0000';
        $visit['css_class'] = 'visit_excl';
        $visit['sit_v']     = 'EXCLUIDA';

    }

    return $visit;

}


function manipula_sit_visia_cq( $idvist ) {

    $id = empty( $idvist ) ? '' : (int)$idvist;

    if ( empty( $id ) ) return false;

    $visit = array();
    $visit['suspenso']  = false;
    $visit['excluido']  = false;
    $visit['corfontv']  = '#000000';
    $visit['css_class'] = 'visit_ativa';
    $visit['css_dest']  = 'visit_ativa_destaque';
    $visit['sit_v']     = 'ATIVA';
    $visit['data_ini']  = '';
    $visit['data_fim']  = '';
    $visit['motivo']    = '';

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
    $db->closeConnection();

    if ( !$query ) return $visit;

    $cont = $query->num_rows;

    if ( $cont != 1 ) return $visit;

    $dados = '';
    $dados = $query->fetch_object();

    $visit['suspenso']  = true;
    $visit['excluido']  = false;
    $visit['corfontv']  = '#CC9900';
    $visit['css_class'] = 'visit_susp';
    $visit['css_dest']  = 'visit_susp_destaque';
    $visit['sit_v']     = 'SUSPENSA';
    $visit['data_ini']  = $dados->data_inicio_f;
    $visit['motivo']    = $dados->motivo;
    $visit['data_fim']  = $dados->data_fim;

    $periodo = $dados->periodo;

    if ( empty( $periodo ) ) { //se o periodo estiver vazio, é visita excluida

        $visit['excluido']  = true;
        $visit['corfontv']  = '#FF0000';
        $visit['css_class'] = 'visit_excl';
        $visit['css_dest']  = 'visit_excl_destaque';
        $visit['sit_v']     = 'EXCLUIDA';
        $visit['data_fim']  = '';

    }

    return $visit;

}

function manipula_sit_visia_c( $d_visit ) {

    $visit = array( );

    $visit['sit_v']     = '';
    $visit['tipo']      = '';
    $visit['corfontv']  = '#000000';
    $visit['css_class'] = 'visit_ativa';
    $visit['data_ini']  = '';
    $visit['data_fim']  = '';
    $visit['motivo']    = '';

    //$sit_visit = ( empty( $d_visit['tipo_susp'] ) ? '' : $d_visit['tipo_susp'] );
    $sit_visit = '';

    if ( !empty( $d_visit['data_inicio'] ) ) {
        $sit_visit = ( empty( $d_visit['periodo'] ) ? 'D' : 'T' );
    }

    switch ( $sit_visit ) {
        case 'T': // SUSPENSÃO TEMPORÁRIA
            $visit['corfontv']  = "#CC9900";
            $visit['css_class'] = 'visit_susp';
            $visit['tipo']      = 'TEMPORÁRIA';
            $visit['sit_v']     = 'SUSPENSO';
            $visit['data_ini']  = ( empty( $d_visit['data_inicio'] ) ? '' : $d_visit['data_inicio'] );
            $visit['data_fim']  = ( empty( $d_visit['data_fim'] ) ? '' : $d_visit['data_fim'] );
            $visit['motivo']    = ( empty( $d_visit['motivo'] ) ? '' : $d_visit['motivo'] );
            break;
        case 'D': // SUSPENSÃO DEFINITIVA
            $visit['corfontv']  = "#FF0000";
            $visit['css_class'] = 'visit_excl';
            $visit['tipo']      = 'DEFINITIVA';
            $visit['sit_v']     = 'EXCLUIDO';
            $visit['data_ini']  = ( empty( $d_visit['data_inicio'] ) ? '' : $d_visit['data_inicio'] );
            $visit['data_fim']  = ( empty( $d_visit['data_fim'] ) ? '' : $d_visit['data_fim'] );
            $visit['motivo']    = ( empty( $d_visit['motivo'] ) ? '' : $d_visit['motivo'] );
            break;
        case '': // ATIVA
            $visit['corfontv']  = "#000000";
            $visit['css_class'] = 'visit_ativa';
            $visit['sit_v']     = 'ATIVO';
            break;
    }

    if ( !empty( $visit['data_ini'] ) ) {
        $timestamp = strtotime( $visit['data_ini'] );
        $visit['data_ini'] = date( 'd/m/Y', $timestamp );
    }

    if ( !empty( $visit['data_fim'] ) ) {
        $timestamp = strtotime( $visit['data_fim'] );
        $visit['data_fim'] = date( 'd/m/Y', $timestamp );
    }

    return $visit;
}

function get_sit_process_b( $preso = NULL, $extinto = NULL ) {

    global $process_class;

    if ( empty ( $preso ) and empty ( $extinto ) ) {
        $process_class = 'process_ativo';
    }

    if ( $preso == 1 ) {
        $process_class = 'process_preso';
    }

    if ( $extinto == 1 ) {
        $process_class = 'process_extinto';
    }



}

function cal_periodo( $ano = 0, $mes = 0, $dia = 0 ) {

    $p = array( );

    $p['ano'] = $ano;
    $p['mes'] = $mes;
    $p['dia'] = $dia;
    $ac_mes = 0;
    $ac_ano = 0;

    if ( $p['dia'] > 29 ) {
        $ac_mes = floor( $p['dia'] / 30 );
        $p['dia'] = $p['dia'] % 30;
    }

    $p['mes'] = $p['mes'] + $ac_mes;

    if ( $p['mes'] > 11 ) {
        $ac_ano = floor( $p['mes'] / 12 );
        $p['mes'] = $p['mes'] % 12;
    }

    $p['ano'] = $p['ano'] + $ac_ano;

    $pf = '';

    if ( !empty( $p['ano'] ) ) {
        $pf .= $p['ano'] . ' ano(s)';
    }

    if ( !empty( $p['mes'] ) ) {
        if ( !empty( $p['ano'] ) ) {
            $pf .= ', ' . $p['mes'] . ' mes(es)';
        } else {
            $pf .= $p['mes'] . ' mes(es)';
        }
    }

    if ( !empty( $p['dia'] ) ) {
        if ( !empty( $p['mes'] ) || !empty( $p['ano'] ) ) {
            $pf .= ', ' . $p['dia'] . ' dia(s)';
        } else {
            $pf .= $p['dia'] . ' dia(s)';
        }
    }

    return $pf;
}

function cal_cond( $iddet = '' ) {

    $iddet = empty( $iddet ) ? '' : (int)$iddet;

    if ( empty( $iddet ) ) return false;

    $query = "SELECT
                SUM(`gra_p_ano`) AS ano,
                SUM(`gra_p_mes`) AS mes,
                SUM(`gra_p_dia`) AS dia
              FROM
                `grade`
              WHERE
                `cod_detento` = $iddet
                AND
                `gra_campo_x` = false
                AND
                `gra_preso` = true";

    $db    = SicopModel::getInstance();
    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    $cont = $query->num_rows;

    if ( $cont != 1 ) return false;

    $dados = '';
    $dados = $query->fetch_object();

    return cal_periodo( $dados->ano, $dados->mes, $dados->dia );

}

/**
 * Validate a date
 *
 * @param string $data
 * @param string formato
 * @return bool
 */
function validaData( $data, $formato = 'DD/MM/AAAA' ) {

    $d = '';
    $m = '';
    $a = '';

    switch ( $formato ) {
        case 'DD-MM-AAAA':
        case 'DD/MM/AAAA':
            list($d, $m, $a) = preg_split( '/[-.\/ ]/', $data );
            break;

        case 'AAAA/MM/DD':
        case 'AAAA-MM-DD':
            list($a, $m, $d) = preg_split( '/[-./ ]/', $data );
            break;

        case 'AAAA/DD/MM':
        case 'AAAA-DD-MM':
            list($a, $d, $m) = preg_split( '/[-./ ]/', $data );
            break;

        case 'MM-DD-AAAA':
        case 'MM/DD/AAAA':
            list($m, $d, $a) = preg_split( '/[-./ ]/', $data );
            break;

        case 'AAAAMMDD':
            $a = substr( $data, 0, 4 );
            $m = substr( $data, 4, 2 );
            $d = substr( $data, 6, 2 );
            break;

        case 'AAAADDMM':
            $a = substr( $data, 0, 4 );
            $d = substr( $data, 4, 2 );
            $m = substr( $data, 6, 2 );
            break;

        default:
            throw new Exception( "Formato de data inválido" );
            break;
    }

    return checkdate( $m, $d, $a );

}

function porcent_ref_pop( $total, $populacao ) {

    if ( empty( $populacao ) ) return false;

    $porcentagem = $total / $populacao * 100;

    $porcentagem = round( $porcentagem, 2 );

    $porcentagem = number_format( $porcentagem, 2, ',', '' );

    return $porcentagem;

}

function alphaID( $in, $to_num = false, $pad_up = false ) {
    /**
     * Traduz números para texto e vice-e-versa
     *
     * Traduz qualquer número (até 9007199254740992)
     * para uma versão menor, usando letras:
     * 9007199254740989 --> PpQXn7COf
     *
     * Especificando o segundo parâmetro como true temos:
     * PpQXn7COf --> 9007199254740989
     *
     * @author    Kevin van Zonneveld <kevin@vanzonneveld.net>
     * @copyright 2008 Kevin van Zonneveld (http://kevin.vanzonneveld.net)
     * @license   http://www.opensource.org/licenses/bsd-license.php New BSD Licence
     * @version   SVN: Release: $Id: alphaID.inc.php 344 2009-06-10 17:43:59Z kevin $
     *
     * @param mixed   $in     String or long input to translate
     * @param boolean $to_num Reverses translation when true
     * @param mixed   $pad_up Number or boolean padds the result up to a specified length
     *
     * @return mixed string or long
     */

    // Letras que serão usadas no índice textual
    $index = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $base = strlen( $index );
    $out = '';

    if ( $to_num ) {
        // Tradução de texto para número
        $in = strrev( $in );
        $out = 0;
        $len = strlen( $in ) - 1;
        for ( $t = 0; $t <= $len; $t++ ) {
            $bcpow = bcpow( $base, $len - $t );
            $out = $out + strpos( $index, substr( $in, $t, 1 ) ) * $bcpow;
        }

        if ( is_numeric( $pad_up ) ) {
            $pad_up--;
            if ( $pad_up > 0 ) {
                $out -= pow( $base, $pad_up );
            }
        }
    } else {
        // Tradução de número para texto
        if ( is_numeric( $pad_up ) ) {
            $pad_up--;
            if ( $pad_up > 0 ) {
                $in += pow( $base, $pad_up );
            }
        }

        $out = '';

        for ( $t = floor( log10( $in ) / log10( $base ) ); $t >= 0; $t-- ) {
            $a = floor( $in / bcpow( $base, $t ) );
            $out = $out . substr( $index, $a, 1 );
            $in = $in - ($a * bcpow( $base, $t ));
        }
        $out = strrev( $out );
    }

    return $out;
}

/*
 * Cria uma msg javascript, só de retorno, só de alert, ou ambos
 * @param  $msg  string  mensagem de alert, se deixada em branco, não cria o alert
 * @param  $num_ret  integer  número de retorno(s), se deixado em branco não cria o retorno
 */
function msg_js( $msg, $num_ret = 0 ) {

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

function calcula_mdc( $a, $b ) {
    if ( $b == 0 ) return $a;
    return calcula_mdc( $b, $a % $b );
}

function dados_det( $where = '' ) {

    if ( empty( $where ) ) return false;

    $db    = SicopModel::getInstance();
    $where = $db->escape_string( $where );

    $query = "SELECT
                `iddetento`,
                `nome_det`,
                `matricula`
              FROM
                `detentos`
              WHERE
                `iddetento` = $where
              LIMIT 1";

    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    $cont = $query->num_rows;

    if ( $cont != 1 ) return false;

    global $quebra;
    $dados = '';
    $dados = $query->fetch_object();

    $idd       = $dados->iddetento;
    $nome_det  = $dados->nome_det;
    $matricula = !empty( $dados->matricula ) ? formata_num( $dados->matricula ) : '';

    $nome_det  = link_pag( $nome_det, "detento/detalhesdet.php?iddet=$idd" );

    $detento   = '[ ' . SICOP_DET_DESC_U . ' ]' . $quebra;
    $detento  .= "<b>ID:</b> $idd; <b>Nome:</b> $nome_det; <b>Matrícula:</b> $matricula";

    return $detento;

}

function dados_det_wl( $where = '' ) {

    if ( empty( $where ) ) return false;

    $db    = SicopModel::getInstance();
    $where = $db->escape_string( $where );

    $query = "SELECT
                `iddetento`,
                `nome_det`,
                `matricula`
              FROM
                `detentos`
              WHERE
                `iddetento` $where
              ORDER BY
                `nome_det`";

    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    $cont = $query->num_rows;

    if ( $cont != 1 ) return false;

    global $quebra;
    $dados = '';

    $detento = '[ ' . SICOP_DET_DESC_U . '(S) ]' . $quebra;
    $i = 0;
    while ( $dados = $query->fetch_object() ) {

        ++$i;
        $idd       = $dados->iddetento;
        $nome_det  = $dados->nome_det;
        $matricula = !empty( $dados->matricula ) ? formata_num( $dados->matricula ) : '';
        $nome_det  = link_pag( $nome_det, "detento/detalhesdet.php?iddet=$idd" );
        $detento  .= "<b>ID:</b> $idd; <b>Nome:</b> $nome_det; <b>Matrícula:</b> $matricula";
        if ( $i != $cont ) $detento .= $quebra;

    }

    return $detento;

}

function dados_visit( $where = '' ) {

    if ( empty( $where ) ) return false;

    $db    = SicopModel::getInstance();
    $where = $db->escape_string( $where );

    $query = "SELECT
                `idvisita`,
                `nome_visit`,
                `rg_visit`
              FROM
                `visitas`
              WHERE
                `idvisita` = $where
              LIMIT 1";

    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    $cont = $query->num_rows;

    if ( $cont != 1 ) return false;

    global $quebra;
    $dados = '';
    $dados = $query->fetch_object();

    $idvst      = $dados->idvisita;
    $nome_visit = $dados->nome_visit;
    $rg_visit   = $dados->rg_visit;

    $nome_visit = link_pag( $nome_visit, "visita/detalvisit.php?idvisit=$idvst" );

    $visita     = '[ VISITANTE ]' . $quebra;
    $visita    .= "<b>ID:</b> $idvst; <b>Nome:</b> $nome_visit; <b>R.G.:</b> $rg_visit.";

    return $visita;

}

function dados_visit_wl( $where = '' ) {

    if ( empty( $where ) ) return false;

    $db    = SicopModel::getInstance();
    $where = $db->escape_string( $where );

    // pegar os dados dos visitantes que estão entrando
    $query = "SELECT
                `idvisita`,
                `cod_detento`,
                `nome_visit`,
                `rg_visit`,
                DATE_FORMAT(`visitas`.`nasc_visit`, '%d/%m/%Y') AS nasc_visit_f,
                FLOOR(DATEDIFF(CURDATE(), `visitas`.`nasc_visit`) / 365.25) AS idade_visit
              FROM
                `visitas`
              WHERE
                `idvisita` $where
              ORDER BY
                `idade_visit` DESC";

    //echo nl2br($query);
    //exit;

    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    $cont = $query->num_rows;

    if ( $cont < 1 ) return false;

    global $quebra;
    $dados = '';
    $visita = '[ VISITANTE(S) ]' . $quebra;
    $i = 0;
    while ( $dados = $query->fetch_object() ) {

        ++$i;
        $idvst       = $dados->idvisita;
        $nome_visit  = link_pag( $dados->nome_visit, "visita/detalvisit.php?idvisit=$idvst" );
        $rg_visit    = $dados->rg_visit;
        $nasc_visit  = $dados->nasc_visit_f;
        $idade_visit = $dados->idade_visit;
        $visita     .= "<b>ID:</b> $idvst; <b>Nome:</b> $nome_visit; <b>R.G.:</b> $rg_visit, <b>nascimento:</b> $nasc_visit, <b>idade:</b> $idade_visit;";
        if ( $i != $cont ) $visita  .= $quebra;

    }

    return $visita;

}

function dados_pda( $where = '' ) {

    if ( empty( $where ) ) return false;

    $db    = SicopModel::getInstance();
    $where = $db->escape_string( $where );

    $query = "SELECT
                `idsind`,
                `num_pda`,
                `ano_pda`,
                `local_pda`
              FROM
                `sindicancias`
              WHERE
                `idsind` = $where
              LIMIT 1";

    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    $cont = $query->num_rows;

    if ( $cont != 1 ) return false;

    global $quebra;
    $dados = '';
    $dados = $query->fetch_object();

    $local_pda = $dados->local_pda;
    $num_pda = $dados->num_pda;
    $ano_pda = $dados->ano_pda;

    $num   = format_num_pda( $num_pda, $ano_pda, $local_pda );
    $ids   = $dados->idsind;

    $num   = link_pag( $num, "sind/detalpda.php?idsind=$ids" );

    $sind  = '[ PDA ]' . $quebra;
    $sind .= "<b>ID:</b> $ids; <b>Número:</b> $num";

    return $sind;

}

function dados_aud( $where = '' ) {

    if ( empty( $where ) ) return false;

    $db    = SicopModel::getInstance();
    $where = $db->escape_string( $where );

    $query = "SELECT
                `idaudiencia`,
                DATE_FORMAT( `data_aud`, '%d/%m/%Y' ) AS `data_aud`,
                DATE_FORMAT( `hora_aud`, '%H:%i' ) AS `hora_aud`,
                `local_aud`,
                `cidade_aud`
              FROM
                `audiencias`
              WHERE
                `idaudiencia` = $where
              LIMIT 1";

    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    $cont = $query->num_rows;

    if ( $cont != 1 ) return false;

    global $quebra;
    $dados = '';
    $dados = $query->fetch_object();

    $ida    = $dados->idaudiencia;
    $data   = $dados->data_aud;
    $hora   = $dados->hora_aud;
    $local  = $dados->local_aud;
    $cidade = $dados->cidade_aud;

    $local  = link_pag( $local, "cadastro/detalaud.php?idaud=$ida" );

    $aud    = '[ AUDIÊNCIA ]' . $quebra;
    $aud   .= "<b>ID:</b> $ida; <b>Data:</b> $data; <b>Hora:</b> $hora; <b>Local:</b> $local; <b>Cidade:</b> $cidade";

    return $aud;

}

function dados_obs( $prefix = '', $where = '' ) {

    if ( empty( $where ) ) return false;

    if ( empty( $prefix ) ) return false;

    $db     = SicopModel::getInstance();
    $where  = $db->escape_string( $where );
    $prefix = $db->escape_string( $prefix );
    $prefix = mb_strtolower( $prefix );

    $query = "SELECT
                `id_obs_$prefix` AS idobs,
                `obs_$prefix` AS obs
              FROM
                `obs_$prefix`
              WHERE
                `id_obs_$prefix` = $where
              LIMIT 1";

    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    $cont = $query->num_rows;

    if ( $cont != 1 ) return false;

    global $quebra;
    $dados = '';
    $dados = $query->fetch_object();

    $obs_id = $dados->idobs;
    $obs_tx = $dados->obs;

    $obs    = '[ OBSERVAÇÃO ]' . $quebra;
    $obs   .= "<b>ID:</b> $obs_id; <b>Observação:</b> $obs_tx";

    return $obs;

}

function dados_pec( $where = '' ) {

    if ( empty( $where ) ) return false;

    $db    = SicopModel::getInstance();
    $where = $db->escape_string( $where );

    $query = "SELECT
                `peculio`.`idpeculio`,
                `peculio`.`cod_detento`,
                `peculio`.`descr_peculio`,
                `tipopeculio`.`tipo_peculio`
              FROM
                `peculio`
                INNER JOIN `tipopeculio` ON `peculio`.`cod_tipo_peculio` = `tipopeculio`.`idtipopeculio`
              WHERE
                `peculio`.`idpeculio` = $where
              LIMIT 1";

    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    $cont = $query->num_rows;

    if ( $cont != 1 ) return false;

    global $quebra;
    $dados = '';
    $dados = $query->fetch_object();

    $idd   = $dados->cod_detento;
    $idp   = $dados->idpeculio;
    $desc  = $dados->descr_peculio;
    $tipo  = $dados->tipo_peculio;

    $tipo  = link_pag( $tipo, "peculio/detalpec.php?iddet=$idd" );

    $peculio  = '[ PERTENCE ]' . $quebra;
    $peculio .= "<b>ID:</b> $idp; <b>Tipo:</b> $tipo; <b>Descrição:</b> $desc";

    return $peculio;

}

function dados_sedex( $where = '', $limit = '' ) {

    if ( empty( $where ) ) return false;

    $db    = SicopModel::getInstance();
    $where = $db->escape_string( $where );

    $clausula_where = "`sedex`.`idsedex` IN( $where )";

    if ( !empty( $limit ) ) {
        $clausula_where = "`sedex`.`idsedex` = $where LIMIT 1";
    }

    $query = "SELECT
                `sedex`.`idsedex`,
                `sedex`.`cod_sedex`,
                `detentos`.`iddetento`,
                `detentos`.`nome_det`,
                `detentos`.`matricula`
              FROM
                `sedex`
                INNER JOIN `detentos` ON `sedex`.`cod_detento` = `detentos`.`iddetento`
              WHERE
                $clausula_where";

    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    $cont = $query->num_rows;

    if ( $cont < 1 ) return false;

    global $quebra;
    $dados = '';
    $sedex = '[ SEDEX ]' . $quebra;
    $i = 0;
    while ( $dados = $query->fetch_object() ) {

        ++$i;
        $idd        = $dados->iddetento;
        $ids        = $dados->idsedex;
        $nome_det   = link_pag( $dados->nome_det, "detento/detalhesdet.php?iddet=$idd" );
        $matricula  = !empty( $dados->matricula ) ? formata_num( $dados->matricula ) : '';
        $cod_sedex  = link_pag( $dados->cod_sedex, "sedex/detalsedex.php?ids=$ids" );
        $sedex     .= "<b>" . SICOP_DET_DESC_FU . ":</b> $nome_det, <b>Matrícula:</b> $matricula, <b>Código do sedex:</b> $cod_sedex;";
        if ( $i != $cont ) $sedex .= $quebra;

    }

    return $sedex;

}

function dados_sedex_only( $where = '', $limit = '' ) {

    if ( empty( $where ) ) return false;

    $db    = SicopModel::getInstance();
    $where = $db->escape_string( $where );

    $clausula_where = "`sedex`.`idsedex` IN( $where )";

    if ( !empty( $limit ) ) {
        $clausula_where = "`sedex`.`idsedex` = $where LIMIT 1";
    }

    $query = "SELECT
                `sedex`.`idsedex`,
                `sedex`.`cod_sedex`
              FROM
                `sedex`
              WHERE
                $clausula_where";

    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    $cont = $query->num_rows;

    if ( $cont < 1 ) return false;

    global $quebra;
    $dados = '';
    $sedex = '[ SEDEX ]' . $quebra;
    $i = 0;
    while ( $dados = $query->fetch_object() ) {

        ++$i;
        $ids        = $dados->idsedex;
        $cod_sedex  = link_pag( $dados->cod_sedex, "sedex/detalsedex.php?ids=$ids" );
        $sedex     .= "<b>ID do sedex:</b> $ids; <b>Código do sedex:</b> $cod_sedex;";
        if ( $i != $cont ) $sedex .= $quebra;

    }

    return $sedex;

}

function dados_item_sedex( $where = '', $limit = '' ) {

    if ( empty( $where ) ) return false;

    $db    = SicopModel::getInstance();
    $where = $db->escape_string( $where );

    $clausula_where = "`sedex_itens`.`id_item` IN( $where )";

    if ( !empty( $limit ) ) {
        $clausula_where = "`sedex_itens`.`id_item` = $where LIMIT 1";
    }

    $query = "SELECT
                `tipo_un_medida`.`un_medida`,
                `sedex_itens`.`id_item`,
                `sedex_itens`.`quant`,
                `sedex_itens`.`desc`
              FROM
                `sedex_itens`
                INNER JOIN `tipo_un_medida` ON `sedex_itens`.`cod_um` = `tipo_un_medida`.`idum`
              WHERE
                $clausula_where";

    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    $cont = $query->num_rows;

    if ( $cont < 1 ) return false;

    global $quebra;
    $dados = '';
    $itens_sedex = '[ ITENS DO SEDEX ]' . $quebra;
    $i = 0;
    while ( $dados = $query->fetch_object() ) {

        ++$i;
        $id           = $dados->id_item;
        $medida       = $dados->un_medida;
        $quant        = str_replace( '.', ',', $dados->quant );
        $desc         = $dados->desc;
        $itens_sedex .= "<b>ID do item:</b> $id; <b>Medida:</b> $medida; <b>Quantidade:</b> $quant; <b>Descrição:</b> $desc;";
        if ( $i != $cont ) $itens_sedex .= $quebra;

    }

    return $itens_sedex;

}

function dados_radio( $where = '', $limit = '' ) {

    if ( empty( $where ) ) return false;

    $db    = SicopModel::getInstance();
    $where = $db->escape_string( $where );

    $clausula_where = "`idradio` IN( $where )";

    if ( !empty( $limit ) ) {
        $clausula_where = "`idradio` = $where LIMIT 1";
    }

    $query = "SELECT
                `idradio`,
                `marca_radio`,
                `cor_radio`
              FROM
                `detentos_radio`
              WHERE
                $clausula_where";

    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    $cont = $query->num_rows;

    if ( $cont < 1 ) return false;

    global $quebra;
    $dados = '';
    $d_item = '[ RÁDIO ]' . $quebra;
    $i = 0;
    while ( $dados = $query->fetch_object() ) {

        ++$i;
        $id      = $dados->idradio;
        $marca   = link_pag( $dados->marca_radio, "incl/detalradio.php?idradio=$id" );
        $cor     = $dados->cor_radio;
        $d_item .= "<b>ID:</b> $id; <b>Marca:</b> $marca; <b>Cor:</b> $cor;";
        if ( $i != $cont ) $d_item .= $quebra;

    }

    return $d_item;

}

function dados_tv( $where = '', $limit = '' ) {

    if ( empty( $where ) ) return false;

    $db    = SicopModel::getInstance();
    $where = $db->escape_string( $where );

    $clausula_where = "`idtv` IN( $where )";

    if ( !empty( $limit ) ) {
        $clausula_where = "`idtv` = $where LIMIT 1";
    }

    $query = "SELECT
                `idtv`,
                `marca_tv`,
                `cor_tv`
              FROM
                `detentos_tv`
              WHERE
                $clausula_where";

    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    $cont = $query->num_rows;

    if ( $cont < 1 ) return false;

    global $quebra;
    $dados = '';
    $d_item = '[ TV ]' . $quebra;
    $i = 0;
    while ( $dados = $query->fetch_object() ) {

        ++$i;
        $id      = $dados->idtv;
        $marca   = link_pag( $dados->marca_tv, "incl/detaltv.php?idtv=$id" );
        $cor     = $dados->cor_tv;
        $d_item .= "<b>ID:</b> $id; <b>Marca:</b> $marca; <b>Cor:</b> $cor;";
        if ( $i != $cont ) $d_item .= $quebra;

    }

    return $d_item;

}

function dados_mov( $where = '', $limit = '' ) {

    if ( empty( $where ) ) return false;

    $db    = SicopModel::getInstance();
    $where = $db->escape_string( $where );

    $clausula_where = "`mov_det`.`id_mov` IN( $where )";

    if ( !empty( $limit ) ) {
        $clausula_where = "`mov_det`.`id_mov` = $where LIMIT 1";
    }

    $query = "SELECT
                `mov_det`.`id_mov`,
                `tipomov`.`tipo_mov`,
                `unidades`.`unidades` AS local_mov,
                DATE_FORMAT( `mov_det`.`data_mov`, '%d/%m/%Y' ) AS data_mov_f
              FROM
                `mov_det`
                LEFT JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                LEFT JOIN `unidades` ON `mov_det`.`cod_local_mov` = `unidades`.`idunidades`
              WHERE
                $clausula_where";

    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    $cont = $query->num_rows;

    if ( $cont < 1 ) return false;

    global $quebra;

    $d_item = '[ MOVIMENTAÇÃO ]' . $quebra;
    $i = 0;
    $dados = '';
    while ( $dados = $query->fetch_object() ) {

        ++$i;
        $id      = $dados->id_mov;
        $tipo    = $dados->tipo_mov;
        $local   = $dados->local_mov;
        $data    = $dados->data_mov_f;
        $d_item .= "<b>ID:</b> $id; <b>Tipo de movimentação:</b> $tipo; <b>Local:</b> $local; <b>Data:</b> $data";
        if ( $i != $cont ) $d_item .= $quebra;

    }

    return $d_item;

}

function dados_user( $where = '' ) {

    if ( empty( $where ) ) return false;

    $db    = SicopModel::getInstance();
    $where = $db->escape_string( $where );

    $query = "SELECT
                `sicop_users`.`iduser`,
                `sicop_users`.`nomeuser`,
                `sicop_setor`.`setor`
              FROM
                `sicop_users`
                INNER JOIN `sicop_setor` ON `sicop_users`.`cod_setor` = `sicop_setor`.`idsetor`
              WHERE
                `sicop_users`.`iduser` = $where
              LIMIT 1";

    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    $cont = $query->num_rows;

    if ( $cont != 1 ) return false;

    global $quebra;
    $dados = '';
    $dados = $query->fetch_object();

    $idu   = $dados->iduser;
    $nome  = $dados->nomeuser;
    $setor = $dados->setor;

    $nome  = link_pag( $nome, "user/user.php?iduser=$idu" );

    $user  = '[ USUÁRIO ]' . $quebra;
    $user .= "<span class='destaque_leg_log'>ID:</span> $idu; <span class='destaque_leg_log'>Nome:</span> $nome; <span class='destaque_leg_log'>Setor:</span> $setor;";

    return $user;

}

function dados_perm( $where = '' ) {

    if ( empty( $where ) ) return false;

    $db    = SicopModel::getInstance();
    $where = $db->escape_string( $where );

    $query = "SELECT
                `sicop_users_perm`.`idpermissao`,
                `sicop_n_setor`.`id_n_setor`,
                `sicop_n_setor`.`n_setor_nome`,
                `sicop_u_n`.`descnivel`,
                `sicop_u_n`.`descnivel_visit`
              FROM
                `sicop_users_perm`
                INNER JOIN `sicop_n_setor` ON `sicop_users_perm`.`cod_n_setor` = `sicop_n_setor`.`id_n_setor`
                INNER JOIN `sicop_u_n` ON `sicop_users_perm`.`cod_nivel` = `sicop_u_n`.`idnivel`
              WHERE
                `sicop_users_perm`.`idpermissao` = $where
              LIMIT 1";

    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return false;

    $cont = $query->num_rows;

    if ( $cont != 1 ) return false;

    global $quebra;
    $dados = '';
    $dados = $query->fetch_object();

    $idp   = $dados->idpermissao;
    $nivel = $dados->id_n_setor == 38 ? $dados->descnivel_visit : $dados->descnivel;
    $setor = $dados->n_setor_nome;

    $user  = '[ PERMISSÃO ]' . $quebra;
    $user .= "<span class='destaque_leg_log'>ID:</span> $idp; <span class='destaque_leg_log'>Setor:</span> $setor; <span class='destaque_leg_log'>Nível de acesso:</span> $nivel;";

    return $user;

}

function link_ord_asc( $ordpor, $campo, $q_string, $title = '' ) {

    $ord       = substr( $ordpor, -1 );
    $ord_campo = substr( $ordpor, 0, -1 );
    $img_path  = SICOP_SYS_IMG_PATH;

    $title_f = 'Ordenado crescente';
    if ( !empty( $title ) ) {
        $title_f = "Ordenado por $title crescente";
    }

    $link = "<img src='$img_path/s_asc_m.png' alt='$title_f' width='11' height='9' border='0' />";

    if ( $ord == 'd' or $ord_campo != $campo ) {

        $title_f = 'Ordenar crescente';
        if ( !empty( $title ) ) {
            $title_f = "Ordenar por $title crescente";
        }

        $q_string_f = "op=$campo";
        if ( !empty ( $q_string ) ) {
            $q_string_f = http_build_query( $q_string ) . "&op=$campo";
        }

        $link = "<a href='?" . $q_string_f . 'a' . "' title='$title_f'><img src='$img_path/s_asc.png' alt='$title_f' width='11' height='9' border='0' /></a>";
    }

    return $link;
}

function link_ord_desc( $ordpor, $campo, $q_string, $title = '' ) {

    $ord       = substr( $ordpor, -1 );
    $ord_campo = substr( $ordpor, 0, -1 );
    $img_path  = SICOP_SYS_IMG_PATH;

    $title_f = 'Ordenado decrescente';
    if ( !empty( $title ) ) {
        $title_f = "Ordenado por $title decrescente";
    }

    $link = "<img src='$img_path/s_desc_m.png' alt='$title_f' width='11' height='9' border='0' />";

    if ( $ord == 'a' or $ord_campo != $campo ) {

        $title_f = 'Ordenar decrescente';
        if ( !empty( $title ) ) {
            $title_f = "Ordenar por $title decrescente";
        }

        $q_string_f = "op=$campo";
        if ( !empty ( $q_string ) ) {
            $q_string_f = http_build_query( $q_string ) . "&op=$campo";
        }

        $link = "<a href='?" . $q_string_f . 'd' . "' title='$title_f'><img src='$img_path/s_desc.png' alt='$title_f' width='11' height='9' border='0' /></a>";
    }

    return $link;
}

/*
 * função para substituir os nomes digitados pelos nomes corretos das unidades
 * pega valores do banco de dados. Pega os valores em qualquer parte da string.
 * @param $str  string  a string que será tratada.
 * @return  string  a string tratada
 */
function replace_names_unidades ( $str ) {

    //remover pontos
    $str = mb_ereg_replace( '[\.]', '', $str );

    // armazena o valor original da strinsg
    $str_orig = $str;

    // substitui traço e ponto e virgula, por virgula
    $str = mb_ereg_replace( '[-;]', ',', $str );

    $str = explode( ',', $str );

    $db  = SicopModel::getInstance();

    $str_v = '';
    foreach ( $str as &$value ) { // monta os valores para o comparador IN()

        $value = trim( $value );

        if ( empty ( $value ) ) continue;

        $value = $db->escape_string( $value );

        $str_v .= "'" . $value . "',";

    }

    // retirar a ultima virgula
    $str_v = substr( $str_v, 0, -1 );

    $query = "SELECT
                `bad_name`,
                `correct_name`,
                `unidades`.`unidades`
              FROM
                `replace_unidades`
                LEFT JOIN `unidades` ON `replace_unidades`.`cod_correct_name` = `unidades`.`idunidades`
              WHERE
                `bad_name` IN ( $str_v )";

    $query = $db->query( $query );
    $db->closeConnection();

    if ( !$query ) return $str_orig;

    $cont = $query->num_rows;

    if ( $cont < 1 ) return $str_orig;

    $bad_name     = array();
    $correct_name = array();
    $d_query      = '';
    while ( $d_query = $query->fetch_object() ) {

        if ( empty( $d_query->unidades ) and empty( $d_query->correct_name ) ) {
            continue;
        }

        $bad_name[]     .= $d_query->bad_name;
        $correct_name[] .= !empty ( $d_query->unidades ) ? $d_query->unidades : $d_query->correct_name ;

    }

    // faz a substituição dos valores na string original
    $str_f = str_ireplace( $bad_name, $correct_name, $str_orig );

    return $str_f;

}

function get_where_det( $sit_det ) {

    $sit_det = (int)$sit_det;
    if ( empty( $sit_det ) ) {
        return false;
    }

    $where = '';

    switch ( $sit_det ) {

        case 1: // na casa/da casa - total
            $where = '( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 OR `mov_det_out`.`cod_tipo_mov` = 6 )
                      AND
                      ( `mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 2 OR `mov_det_in`.`cod_tipo_mov` = 3 )';
            break;

        default:
        case 2: // na casa
            $where = '( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 )
                      AND
                      ( `mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 2 OR `mov_det_in`.`cod_tipo_mov` = 3 )';
            break;

        case 3: // da casa
            $where = '( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 OR `mov_det_out`.`cod_tipo_mov` = 6 )
                      AND
                      ( `mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 3 )';
            break;

        case 4: // transito na casa
            $where = '( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 OR `mov_det_out`.`cod_tipo_mov` = 6 )
                      AND
                      ( `mov_det_in`.`cod_tipo_mov` = 2 )';
            break;

        case 5: // transito da casa
            $where = '`mov_det_out`.`cod_tipo_mov` = 6
                      AND
                      ( `mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 2 OR `mov_det_in`.`cod_tipo_mov` = 3 )';
            break;

        case 6: // transferido
            $where = '( `mov_det_out`.`cod_tipo_mov` = 7 OR `mov_det_out`.`cod_tipo_mov` = 8 )
                      AND
                      ( `mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 2 OR `mov_det_in`.`cod_tipo_mov` = 3 )';
            break;

        /**
         * BETWEEN 100 AND 199 = ALVARA DE SOLTURA
         * BETWEEN 200 AND 299 = EVASAO
         * BETWEEN 300 AND 399 = OBITO
         */
        case 7: // excluído
            $where = '( `mov_det_out`.`cod_tipo_mov` = 5  AND ( `unidades_out`.`idunidades` BETWEEN 100 AND 199 ) )';
            break;

        case 8: // evadido
            $where = '( `mov_det_out`.`cod_tipo_mov` = 5  AND ( `unidades_out`.`idunidades` BETWEEN 200 AND 299 ) )';
            break;

        case 9: // falecido
            $where = '( `mov_det_out`.`cod_tipo_mov` = 5  AND ( `unidades_out`.`idunidades` BETWEEN 300 AND 399 ) )';
            break;

        case 10: // a chegar
            $where = '( ISNULL( `mov_det_in`.`cod_tipo_mov` ) AND ISNULL( `mov_det_out`.`cod_tipo_mov` ) )';
            break;

        case 11: // transito na casa da casa
            $where = '( `mov_det_out`.`cod_tipo_mov` = 6 AND `mov_det_in`.`cod_tipo_mov` = 2 )';
            break;

        case 12: // na casa da casa
            $where = '( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 )
                      AND
                      ( `mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 3 )';
            break;

    }

    return $where;

}

function get_quali_visit( $idvisit ) {

    $idvisit = (int)$idvisit;

}

function depur( $variavel = '' ) {

    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);

    if ( is_array( $variavel ) ) {

        $arr_var = '';

        foreach ( $variavel as $key => $value ) {

            $arr_var .= "$key => $value" . PHP_EOL;

        }

        echo nl2br( $arr_var );

    } else {

        echo nl2br( $variavel );

    }

    //exit;

}

?>