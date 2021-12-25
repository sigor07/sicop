<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$tipo_pag = 'COMBOX - AJAX';
$msg_falha = '<p class="q_error">FALHA!</p>';

//$is_post = is_post();
//if ( !$is_post ) {
//
//    // montar a mensagem q será salva no log
//    $msg = array( );
//    $msg['tipo'] = 'atn';
//    $msg['text'] = "Tentativa de acesso direto à página ( $tipo_pag ).";
//    get_msg( $msg, 1 );
//
//    echo $msg_falha;
//    exit;
//
//}

$tipo = get_post( 'tipo', 'busca' );
//$tipo = get_get( 'tipo', 'busca' );

header( 'Content-Type: text/html; charset=utf-8' );
header( 'Pragma: no-cache' );
header( 'Expires: 0' );

switch ( $tipo ) {
    case 'estado':
        busca_estados();
        break;
    case 'cidade':
        busca_cidades();
        break;
    case 'tipo_mov':
        busca_tipo_mov();
        break;
    case 'tipo_mov_ant':
        busca_tipo_mov_ant();
        break;
    case 'local':
        busca_local();
        break;
    case 'cela':
        busca_cela();
        break;
    case 'raio':
        busca_raio();
        break;
    case 'perm':
        busca_perm();
        break;
    case 'n_setor':
        busca_n_setor();
        break;
    case 'matr':
        busca_matr();
        break;
    case 'cpf':
        busca_cpf();
        break;
    case 'rgv':
        busca_rgv();
        break;
    default:
        break;
        exit;
}

function busca_estados() {

    $query = 'SELECT `idestado` AS `id`, `sigla` AS `opt` FROM `estados` ORDER BY `nome` ASC';

    $db = SicopModel::getInstance();
    $query = $db->query( $query );
    $db->closeConnection();


    $cont = $query->num_rows;

    if ( $cont < 1 ) return false;

    $combox = '<option value="">Selecione</option>' . PHP_EOL;
    $old_uf = get_post( 'old_uf', 'int' );
    $f      = '';
    while ( $f = $query->fetch_object() ) {

        $combox .= '<option value="' . $f->id . '"';

        if ( $f->id == $old_uf ) {
            $combox .= 'selected="selected"';
        }

        $combox .= '>' . $f->opt . '</option>' . PHP_EOL;

    }

    echo $combox;

}

function busca_cidades() {

    $uf = get_post( 'uf', 'int' );

    if ( empty ( $uf ) ) {
        echo '<option value="">Selecione o estado</option>' . PHP_EOL;
        return;
    }

    $query = "SELECT `idcidade` AS `id`, `nome` AS `opt` FROM `cidades` WHERE `cod_uf` = $uf ORDER BY `nome` ASC";

    $db = SicopModel::getInstance();
    $query = $db->query( $query );
    $db->closeConnection();

    $cont = $query->num_rows;

    if ( $cont < 1 ) return false;

    $combox     = '<option value="">Selecione</option>' . PHP_EOL;
    $old_cidade = get_post( 'old_cidade', 'int' );
    $f          = '';
    while ( $f = $query->fetch_object() ) {

        $combox .= '<option value="' . $f->id . '"';

        if ( $f->id == $old_cidade ) {
            $combox .= ' selected="selected"';
        }

        $combox .= '>' . $f->opt . '</option>' . PHP_EOL;

    }

    echo $combox;

}

function busca_tipo_mov() {

    $sit_det = get_post( 'sit_det', 'int' );

    $where = '';

    if ( $sit_det == SICOP_SIT_DET_FALECIDO ) {
        return false;
    } else if (  $sit_det == SICOP_SIT_DET_ACEHGAR || $sit_det == SICOP_SIT_DET_TRANSF || $sit_det == SICOP_SIT_DET_EXCLUIDO || $sit_det == SICOP_SIT_DET_EVADIDO ) { // a chegar, transferido, excluido, evadido
        $where = "WHERE `sigla_mov` IN('IN', 'IR', 'IT')";
    } else if ( $sit_det == SICOP_SIT_DET_TRANA ) { // transito na casa
        $where = "WHERE `sigla_mov` IN('IN', 'IR', 'EX', 'ET', 'ER', 'EE')";
    } else if ( $sit_det == SICOP_SIT_DET_TRADA ) { // transito da casa
        $where = "WHERE `sigla_mov` IN('IE', 'EX', 'ET', 'ER')";
    } else if ( $sit_det == SICOP_SIT_DET_TRANADA ) { // transito na casa da casa
        $where = "WHERE `sigla_mov` IN('IE')";
    } else if ( $sit_det == SICOP_SIT_DET_NA ) { // na casa
        $where = "WHERE `sigla_mov` IN('EX', 'ET', 'ER')";
    } else if ( $sit_det == 999 ) { // situação apenas de parametro para a busca de certas movimentações
        $where = "WHERE `sigla_mov` IN('IN', 'IT', 'IR', 'EX', 'ET', 'ER')";
    } else {
        $where = '';
    }

    $query = "SELECT `idtipo_mov` AS `id`, `sigla_mov`, `tipo_mov` FROM `tipomov` $where ORDER BY `idtipo_mov` ASC";

    $db = SicopModel::getInstance();
    $query = $db->query( $query );
    $db->closeConnection();

    $cont = $query->num_rows;

    if ( $cont < 1 ) return false;

    $combox       = '<option value="">Selecione</option>' . PHP_EOL;
    $old_tipo_mov = get_post( 'old_tipo_mov', 'int' );
    $f            = '';
    while ( $f = $query->fetch_object() ) {

        $combox .= '<option value="' . $f->id . '"';

        if ( $f->id == $old_tipo_mov ) {
            $combox .= 'selected="selected"';
        }

        $combox .= '>' . $f->sigla_mov . ' - ' . $f->tipo_mov . '</option>' . PHP_EOL;

    }

    echo $combox;

}

function busca_tipo_mov_ant() {

    $tipo_mov_ant = get_post( 'tipo_mov_ant', 'int' );

    $where = '';
    if ( empty( $tipo_mov_ant ) || $tipo_mov_ant == 5 || $tipo_mov_ant == 7 || $tipo_mov_ant == 8 ){ // se a mov anterior foi EX, ER, EE

        $where = "`sigla_mov` IN('IN', 'IR', 'IT')";

    } else if ( $tipo_mov_ant == 1 || $tipo_mov_ant == 3 ){ // se a mov anterior foi IN ou IR

        $where = "`sigla_mov` IN('EX', 'ET', 'ER')";

    } else if ( $tipo_mov_ant == 2 ){ // se a mov anterior foi IT

        $where = "`sigla_mov` IN('IN', 'IR', 'EX', 'ET', 'ER', 'EE')";

    } else if ( $tipo_mov_ant == 4 ){ // se a mov anterior foi IE

        $iddet = get_post( 'iddet', 'int' );
        if ( empty( $iddet ) ) return false;

        $q_mov_in = "SELECT
                       `mov_det`.`cod_tipo_mov`
                     FROM
                       `mov_det`
                     WHERE
                       `mov_det`.`cod_detento` = $iddet
                       AND
                       `mov_det`.`cod_tipo_mov` IN( 1, 2, 3 )
                     ORDER BY
                       `mov_det`.`data_mov` DESC,
                       `mov_det`.`data_add` DESC
                     LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_mov_in = $model->query( $q_mov_in );

        // fechando a conexao
        $model->closeConnection();

        $d_mov_in    = $q_mov_in->fetch_assoc();
        $tipo_mov_in = $d_mov_in['cod_tipo_mov'];

        $where = "`sigla_mov` IN('EX', 'ET', 'ER')";

        if ( $tipo_mov_in == 2 ) {

            $where = "`sigla_mov` IN('IN', 'IR', 'EX', 'ET', 'ER', 'EE')";

        }

    } else if ( $tipo_mov_ant == 6 ){ // se a mov anterior foi ET

        $where = "`sigla_mov` IN('IE', 'EX', 'ET', 'ER')";

        $sit_det = get_post( 'sit_det', 'int' );
        if ( empty( $sit_det ) ) return false;

        if ( $sit_det == SICOP_SIT_DET_TRANA ){ // se for transito na casa, quer dizer que anteriormente era transito na casa da casa

            $where = "`sigla_mov` IN('IE')";

        }
    }

    $query = "SELECT `idtipo_mov` AS `id`, `sigla_mov`, `tipo_mov` FROM `tipomov` WHERE $where ORDER BY `idtipo_mov` ASC";

    $db = SicopModel::getInstance();
    $query = $db->query( $query );
    $db->closeConnection();

    $cont = $query->num_rows;

    if ( $cont < 1 ) return false;

    $combox         = '<option value="">Selecione</option>' . PHP_EOL;
    $tipo_mov_atual = get_post( 'tipo_mov_atual', 'int' );
    $f              = '';
    while ( $f = $query->fetch_object() ) {

        $combox .= '<option value="' . $f->id . '"';

        if ( $f->id == $tipo_mov_atual ) {
            $combox .= 'selected="selected"';
        }

        $combox .= '>' . $f->sigla_mov . ' - ' . $f->tipo_mov . '</option>' . PHP_EOL;

    }

    echo $combox;

}

function busca_local() {

    $tipo_mov = get_post( 'tipo_mov', 'int' );

    if ( empty ( $tipo_mov ) ) {
        echo '<option value="">Selecione o tipo de movimentação</option>' . PHP_EOL;
        return;
    }

    $where = '`in` = TRUE';

    switch ( $tipo_mov ) {
        case '1': // inclusão
            $where = '`in` = TRUE';
            break;
        case '2': // inclusão por transito
            $where = '`it` = TRUE';
            break;
        case '3': // inclusão por remoção
            $where = '`ir` = TRUE';
            break;
        case '5': // exclusão
            $where = '`ex` = TRUE';
            break;
        case '6': // exclusão por transito
            $where = '`et` = TRUE';
            break;
        case '7': // exclusão por remoção
            $where = '`er` = TRUE';
            break;
        case '4': // inclusão por retorno
        case '8': // exclusão por retorno
            return false;
            break;
    }

    $query = "SELECT `unidades`.`idunidades` AS `id`, `unidades`.`unidades` AS `opt` FROM `unidades` WHERE $where ORDER BY `unidades`.`unidades`";

    $db = SicopModel::getInstance();
    $query = $db->query( $query );
    $db->closeConnection();

    $cont = $query->num_rows;

    if ( $cont < 1 ) return false;

    $combox        = '<option value="">Selecione</option>' . PHP_EOL;
    $old_local_mov = get_post( 'old_local_mov', 'int' );
    $f             = '';
    while ( $f = $query->fetch_object() ) {

        $combox .= '<option value="' . $f->id . '"';

        if ( $f->id == $old_local_mov ) {
            $combox .= ' selected="selected"';
        }

        $combox .= '>' . $f->opt . '</option>' . PHP_EOL;

    }

    echo $combox;

}

function busca_cela() {

    $raio = get_post( 'raio', 'int' );
    //$raio = get_get( 'raio', 'int' );

    if ( empty ( $raio ) ) {
        echo '<option value="">Selecione o raio</option>' . PHP_EOL;
        return;
    }

    $query = "SELECT `idcela` AS `id`, `cela` AS `opt` FROM `cela` WHERE `cod_raio` = $raio ORDER BY `cela` ASC";

    $db = SicopModel::getInstance();
    $query = $db->query( $query );
    $db->closeConnection();

    $cont = $query->num_rows;

    if ( $cont < 1 ) return false;

    $combox   = '<option value="">Selecione</option>' . PHP_EOL;
    
    if  ( $cont == 1 ) {
    
        $f = $query->fetch_object();
        
        //if ( empty( $f->opt ) ) {
            
            $combox = '<option value="' . $f->id . '"';
            
            $combox .= '>' . $f->opt . '</option>' . PHP_EOL;            
            
        //}        
    
    } else {       
        
        $old_cela = get_post( 'old_cela', 'int' );
        $f        = '';
        while ( $f = $query->fetch_object() ) {

            $combox .= '<option value="' . $f->id . '"';

            if ( $f->id == $old_cela ) {
                $combox .= ' selected="selected"';
            }

            $combox .= '>' . $f->opt . '</option>' . PHP_EOL;

        }
    
    }

    echo $combox;

}

function busca_raio() {

    $query = 'SELECT `idraio` AS `id`, `raio` AS `opt` FROM `raio` ORDER BY `raio` ASC';

    $db = SicopModel::getInstance();
    $query = $db->query( $query );
    $db->closeConnection();


    $cont = $query->num_rows;

    if ( $cont < 1 ) return false;
    
    $combox = '<option value="">Selecione</option>' . PHP_EOL;

    $old_raio = get_post( 'old_raio', 'int' );
    $f        = '';
    while ( $f = $query->fetch_object() ) {

        $combox .= '<option value="' . $f->id . '"';

        if ( $f->id == $old_raio ) {
            $combox .= 'selected="selected"';
        }

        $combox .= '>' . $f->opt . '</option>' . PHP_EOL;

    } 
 
    echo $combox;

}

function busca_perm() {

    $visit = get_post( 'visit', 'int' );

    $campo = 'descnivel';
    if ( !empty ( $visit ) ) {
        $campo = 'descnivel_visit';
    }

    $query = "SELECT `idnivel` AS `id`, `$campo` AS `opt` FROM `sicop_u_n` ORDER BY `idnivel` ASC";

    $db = SicopModel::getInstance();
    $query = $db->query( $query );
    $db->closeConnection();

    $cont = $query->num_rows;

    if ( $cont < 1 ) return false;

    $combox    = '<option value="">Selecione</option>' . PHP_EOL;
    $old_nivel = get_post( 'old_nivel', 'int' );
    $f         = '';
    while ( $f = $query->fetch_object() ) {

        $combox .= '<option value="' . $f->id . '"';

        if ( $f->id == $old_nivel ) {
            $combox .= 'selected="selected"';
        }

        $combox .= '>' . $f->opt . '</option>' . PHP_EOL;

    }

    echo $combox;

}

function busca_n_setor() {

    $iduser = get_post( 'iduser', 'int' );

    if ( empty ( $iduser ) ) {
        return false;
    }

    $perm_type = get_post( 'perm_type', 'int' );

    $where_imp = 'FALSE';
    $where_esp = 'FALSE';

    if ( $perm_type == 1 ) {

        $where_imp = 'FALSE';
        $where_esp = 'FALSE';

    } else if ( $perm_type == 2 ) {

        $where_imp = 'TRUE';
        $where_esp = 'FALSE';

    } else if ( $perm_type == 3 ) {

        $where_imp = 'FALSE';
        $where_esp = 'TRUE';

    } else {

        return false;

    }

    $query = "SELECT
                `sicop_n_setor`.`id_n_setor` AS `id`,
                `sicop_n_setor`.`n_setor_nome` AS `opt`
              FROM
                `sicop_n_setor`
              WHERE
                `sicop_n_setor`.`especifico` = $where_esp
                AND
                `sicop_n_setor`.`impressao` = $where_imp
                AND
                `sicop_n_setor`.`id_n_setor` NOT IN ( SELECT `cod_n_setor` FROM `sicop_users_perm` WHERE `cod_user` = $iduser )
              ORDER BY
                `sicop_n_setor`.`n_setor_nome`";

    $db = SicopModel::getInstance();
    $query = $db->query( $query );
    $db->closeConnection();

    $cont = $query->num_rows;

    if ( $cont < 1 ) return false;

    $combox = '<option value="">Selecione</option>' . PHP_EOL;
    $f      = '';
    while ( $f = $query->fetch_object() ) {

        $combox .= '<option value="' . $f->id . '"';

        $combox .= '>' . $f->opt . '</option>' . PHP_EOL;

    }

    echo $combox;

}

function busca_matr() {

    $matr = get_post( 'matr' );

    $ck_matr = ck_matr_exist( $matr );

    $saida = 0;
    if ( $ck_matr ) {
        $saida = 1;
    }

    // 1 - Matrícula Indisponível
    // 0 - Matrícula Disponível
    echo $saida;

}

function busca_cpf() {

    $cpf = get_post( 'cpf' );

    $ck = ck_cpf_exist( $cpf );

    $saida = 0;
    if ( $ck ) {
        $saida = 1;
    }

    // 1 - CPF Indisponível
    // 0 - CPF Disponível
    echo $saida;

}

function busca_rgv() {

    $rgv = get_post( 'rgv' );

    $ck = ck_rgv_exist( $rgv );

    $saida = 0;
    if ( $ck ) {
        $saida = 1;
    }

    // 1 - RG da visita Indisponível
    // 0 - RG da visita Disponível
    echo $saida;
    exit;

}

?>