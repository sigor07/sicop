<?php
if ( !isset( $_SESSION ) ) session_start();

require 'init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';
$cont = '';
$ordpor = '';
$q_string = '';
/*ob_start("ob_gzhandler");*/


$imp_chefia   = get_session( 'imp_chefia', 'int' );
$imp_cadastro = get_session( 'imp_cadastro', 'int' );
$n_imp_n      = 1;

$tipo_fon      = '';
$valorbusca    = NULL;
$valorbusca_sf = NULL;
$proced        = '';
$data_in_ini   = get_get( 'data_in_ini', 'busca' );
$data_in_fim   = get_get( 'data_in_fim', 'busca' );
$data_out_ini  = get_get( 'data_out_ini', 'busca' );
$data_out_fim  = get_get( 'data_out_fim', 'busca' );
$unidade       = get_get( 'unidade', 'int' );
$n_cela        = get_get( 'n_cela', 'int' );
$n_raio        = get_get( 'n_raio', 'int' );
$tipo_sit      = get_get( 'tipo_sit', 'int' );
$add_link      = '';

if( !empty( $_GET['proced'] ) ) {

    $proced = get_get( 'proced', 'busca' );

}

$inner_join = '';
$motivo     = SICOP_DET_DESC_U . 'S';
$desc_pag   = 'Pesquisar ' . SICOP_DET_DESC_L . 's';
$link       = 'detento/detalhesdet.php';

switch($proced) {
    default:
    case '':
    case 'bd':
        $link = 'detento/detalhesdet.php';
        $motivo = SICOP_DET_DESC_U . 'S';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's';
        break;
    case 'cadrol':
        $n_rol = get_session( 'n_rol', 'int' );
        $n_rol_n = 3;
        if ( $n_rol < $n_rol_n ) {


            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR ' . SICOP_DET_DESC_U . 'S PARA CADASTRAMENTO DE VISITANTES';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link     = 'visita/cadastravisit.php';
        $motivo   = SICOP_DET_DESC_U . ' PARA CADASTRAMENTO DE VISITANTES';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's para cadastramento de visitantes';
        break;
    case 'regrol':
        $n_rol   = get_session( 'n_rol', 'int' );
        $n_rol_n = 3;
        if ( $n_rol < $n_rol_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR ' . SICOP_DET_DESC_U . 'S PARA REGISTRO DE ENTRADA DE VISITANTES';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link     = 'visita/regentrv.php';
        $motivo   = SICOP_DET_DESC_U . ' PARA REGISTRO DE ENTRADA';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's para registro de entrada';
        break;
    case 'brol':
        $n_rol   = get_session( 'n_rol', 'int' );
        $n_rol_n = 2;
        if ( $n_rol < $n_rol_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR RÓIS DE VISITA PEL' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U;
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link     = 'visita/rol_visit.php';
        $motivo   = 'RÓIS DE VISITAS PEL' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U;
        $desc_pag = 'Pesquisar róis de visitas pel' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L;
        break;
    case 'cadaud':
        $n_cadastro = get_session( 'n_cadastro', 'int' );
        $n_cad_n    = 3;
        if ($n_cadastro < $n_cad_n) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR ' . SICOP_DET_DESC_U . 'S PARA CADASTRAMENTO DE AUDIÊNCIA';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link = 'cadastro/cadaud.php';
        $motivo = SICOP_DET_DESC_U . ' PARA CADASTRAMENTO DE AUDIÊNCIA';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's para cadastramento de audiência';
        $ant =  get_get( 'ant', 'int' );
        if ( empty( $ant ) and isset( $_SESSION['l_id_aud'] ) ) unset( $_SESSION['l_id_aud'] );
        if ( !empty( $_SESSION['l_id_aud'] ) ) {
            $add_link = '&ant=1';
        }
        break;
    case 'cadapcc':
        $n_cadastro = get_session( 'n_cadastro', 'int' );
        $n_cad_n    = 3;
        if ($n_cadastro < $n_cad_n) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR ' . SICOP_DET_DESC_U . 'S PARA CADASTRAMENTO DE APCC';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link     = 'cadastro/cadapcc.php';
        $motivo   = SICOP_DET_DESC_U . ' PARA CADASTRAMENTO DE APCC';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's para cadastramento de APCC';
        break;
    case 'cadsind':
        $n_sind   = get_session( 'n_sind', 'int' );
        $n_sind_n = 3;
        if ( $n_sind < $n_sind_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR ' . SICOP_DET_DESC_U . 'S PARA CADASTRAMENTO DE PDA';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link     = 'sind/cadpda.php';
        $motivo   = SICOP_DET_DESC_U . 'PARA CADASTRAMENTO DE PDA';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's para cadastramento de PDA';
        break;
    case 'bsind':
        $n_sind   = get_session( 'n_sind', 'int' );
        $n_sind_n = 2;
        if ( $n_sind < $n_sind_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR PDAs PEL' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U;
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link       = 'sind/rol_pda.php';
        $motivo     = 'PDAs PEL' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U;
        $inner_join = 'INNER JOIN `sindicancias` ON `sindicancias`.`cod_detento` = `detentos`.`iddetento`';
        $desc_pag   = 'Pesquisar PDAs pel' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L;
        break;
    case 'cadpro':
        $n_pront   = get_session( 'n_pront', 'int' );
        $n_pront_n = 3;
        if ( $n_pront < $n_pront_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR ' . SICOP_DET_DESC_U . 'S PARA CADASTRAMENTO DE PROCESSO';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link     = 'prontuario/cadprocess.php';
        $motivo   = SICOP_DET_DESC_U . ' PARA CADASTRAMENTO DE PROCESSO';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's para cadastramento de processo';
        break;
    case 'bpro':
        $n_pront   = get_session( 'n_pront', 'int' );
        $n_pront_n = 2;
        if ( $n_pront < $n_pront_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR GRADES PEL' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U;
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link = 'prontuario/detalgrade.php';
        $motivo = 'GRADES PEL' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U;
        $desc_pag = 'Pesquisar grades pel' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L;
        break;
    case 'cadsed':
        $n_portaria   = get_session( 'n_portaria', 'int' );
        $n_portaria_n = 3;
        if ( $n_portaria < $n_portaria_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR ' . SICOP_DET_DESC_U . 'S PARA CADASTRAMENTO DE SEDEX';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }

        $n_sedex      = get_session( 'n_sedex', 'int' );
        $n_sedex_n    = 3;
        if ( $n_sedex < $n_sedex_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR ' . SICOP_DET_DESC_U . 'S PARA CADASTRAMENTO DE SEDEX';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link     = 'sedex/sedex_in.php';
        $motivo   = SICOP_DET_DESC_U . ' PARA CADASTRAMENTO DE SEDEX';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's para cadastramento de sedex';
        break;
    case 'bsed': // buscar rol de sedex
        $n_sedex   = get_session( 'n_sedex', 'int' );
        $n_sedex_n = 2;
        if ( $n_sedex < $n_sedex_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR SEDEX PEL' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U;
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link       = 'sedex/rol_sedex.php';
        $motivo     = 'SEDEX PEL' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U;
        $inner_join = 'INNER JOIN `sedex` ON `sedex`.`cod_detento` = `detentos`.`iddetento`';
        $desc_pag   = 'Pesquisar sedex pel' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L;
        break;
    case 'cadpec': // cadastrar pertence
        $n_peculio    = get_session( 'n_peculio', 'int' );
        $n_peculio_n  = 3;
        if ( $n_peculio < $n_peculio_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR ' . SICOP_DET_DESC_U . 'S PARA CADASTRAMENTO DE PECÚLIO';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link     = 'peculio/cadpert.php';
        $motivo   = SICOP_DET_DESC_U . ' PARA CADASTRAMENTO DE PERTENCES';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's para cadastramento de pertences';
        break;
    case 'caddep': // cadastrar deposito
        $n_peculio   = get_session( 'n_peculio', 'int' );
        $n_peculio_n = 3;
        if ( $n_peculio < $n_peculio_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR ' . SICOP_DET_DESC_U . 'S PARA CADASTRAMENTO DE DEPÓSITO';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link     = 'peculio/caddep.php';
        $motivo   = SICOP_DET_DESC_U . ' PARA CADASTRAMENTO DE DEPÓSITO';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's para cadastramento de depósito';
        break;
    case 'cadsaq': // cadastrar saque
        $n_peculio   = get_session( 'n_peculio', 'int' );
        $n_peculio_n = 3;
        if ( $n_peculio < $n_peculio_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR ' . SICOP_DET_DESC_U . 'S PARA CADASTRAMENTO DE SAQUE';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }

        $n_pec_baixa = get_session( 'n_peculio_baixa', 'int' );
        if ( $n_pec_baixa < 1 ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR ' . SICOP_DET_DESC_U . 'S PARA CADASTRAMENTO DE SAQUE';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link     = 'peculio/cadsaq.php';
        $motivo   = SICOP_DET_DESC_U . ' PARA CADASTRAMENTO DE SAQUE';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's para cadastramento de saque';
        break;
    case 'bpec': // pesquisar pecúlio
        $n_peculio   = get_session( 'n_peculio', 'int' );
        $n_peculio_n = 2;
        if ( $n_peculio < $n_peculio_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR PECÚLIO PEL' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U;
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link     = 'peculio/detalpec.php';
        $motivo   = 'PERTENCES PEL' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U;
        $desc_pag = 'Pesquisar pertences pel' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L;
        break;
    case 'cadint': //cadastrar na inteligência
        $n_inteli   = get_session( 'n_inteli', 'int' );
        $n_inteli_n = 3;
        if ( $n_inteli < $n_inteli_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR ' . SICOP_DET_DESC_U . 'S PARA CADASTRAMENTO NA INTELIGÊNCIA';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link     = 'inteli/cadinteli.php';
        $motivo   = SICOP_DET_DESC_U . ' PARA INCLUSÃO NA LISTA DE MONITORAMENTO';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's para inclusão na lista de monitoramento';
        break;
    case 'cadtv': //cadastrar tv
        $n_incl   = get_session( 'n_incl', 'int' );
        $n_incl_n = 3;
        if ( $n_incl < $n_incl_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR ' . SICOP_DET_DESC_U . 'S PARA CADASTRAMENTO DE TV';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link     = 'incl/cadtv.php';
        $motivo   = SICOP_DET_DESC_U . ' PARA CADASTRAMENTO DE TV';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's para cadastramento de tv';
        break;
    case 'cadrd': //cadastrar rádio
        $n_incl   = get_session( 'n_incl', 'int' );
        $n_incl_n = 3;
        if ( $n_incl < $n_incl_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR ' . SICOP_DET_DESC_U . 'S PARA CADASTRAMENTO DE RÁDIO';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link     = 'incl/cadradio.php';
        $motivo   = SICOP_DET_DESC_U . ' PARA CADASTRAMENTO DE RÁDIO';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's para cadastramento de rádio';
        break;
    case 'impter': // imprimir termos do prontuário
        $imp_pront = get_session( 'imp_pront', 'int' );
        if ( $imp_pront < $n_imp_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR ' . SICOP_DET_DESC_U . 'S PARA IMPRESSÃO DE TERMOS DO PRONTUÁRIO';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link     = 'prontuario/termo_enc.php';
        $motivo   = SICOP_DET_DESC_U . ' PARA IMPRESSÃO DE TERMOS DE ENCERRAMENTO';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's para impressão de termos de encerramento';
        break;
    case 'impterseg': // imprimir termos do seguro
        if ( $imp_chefia < $n_imp_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR ' . SICOP_DET_DESC_U . 'S PARA IMPRESSÃO DE TERMOS DE SEGURO';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link     = 'detento/termo_seg.php';
        $motivo   = SICOP_DET_DESC_U . ' PARA IMPRESSÃO DE TERMOS DE SEGURO';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's para impressão de termos de seguro';
        break;
    case 'imprest': // imprimir restituição
        $imp_pront = get_session( 'imp_pront', 'int' );
        if ( $imp_pront < $n_imp_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAR ' . SICOP_DET_DESC_U . 'S PARA IMPRESSÃO DE RESTITUIÇÃO DE MANDADOS DE PRISÃO E ALVARÁS';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $link = 'detento/rest_mp.php';
        $motivo = SICOP_DET_DESC_U . ' PARA IMPRESSÃO DE RESTITUIÇÃO DE MANDADOS DE PRISÃO E ALVARÁS';
        $desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's para impressão de restituição de mandados de prisão e alvarás';
        break;
}

if ( !empty( $_GET['busca'] ) ) {

    $where = '';
    $tipo_fon = get_get( 'tipo_fon', 'int' );

    if ( !empty( $_GET['campobusca'] ) ) {

        $valorbusca = get_get( 'campobusca', 'busca' );
        $valorbusca_sf = $_GET['campobusca'];

        if ( $tipo_fon == 1 ) {

            $where = "WHERE (
                               `detentos`.`nome_det` LIKE '%$valorbusca%'
                               OR
                               `detentos`.`matricula` LIKE '$valorbusca%'
                               OR
                               ( `aliases`.`cod_tipoalias` = 4 AND `aliases`.`alias_det` LIKE '%$valorbusca%' )
                             )";

        } else {

            $valorbusca = preg_replace( '/\s?\b\w{1,2}\b/', null, $valorbusca ); // remover palavras com 2 letras ou menos

            if ( empty( $valorbusca ) ) {
                echo msg_js( '', 1 );
                exit;
            }

            $arr_busca = explode( ' ', $valorbusca );

            $where = 'WHERE (';
            foreach( $arr_busca as $indice => $valor ) {
                if ( $valor == NULL ) continue;
                $where .= " `detentos`.`nome_det` LIKE '%$valor%' AND";
            }

            $where_alias = '(';
            foreach ( $arr_busca as $indice => $valor ) {
                if ( $valor == NULL ) continue;
                $where_alias .= " `aliases`.`alias_det` LIKE '%$valor%' AND";
            }

        }

        if ( $tipo_fon == 2 ) {

            if ( !empty( $where ) ) {
                $where  = substr( $where, 0, -3 ); //remover o ultimo 'AND'
                $where .= " OR `detentos`.`matricula` LIKE '$valorbusca%' ";
            }

            $where_alias_f = '';
            if ( !empty( $where_alias ) ) {
                $where_alias_f = substr($where_alias, 0, -3); //remover o ultimo 'AND'
                $where_alias_f =  ' OR ( `aliases`.`cod_tipoalias` = 4 AND ' . $where_alias_f . ' ) )';
            }

            $where .= $where_alias_f . ' )';

        }

    }

    if ( !empty( $unidade ) ) {
        if ( !empty( $where ) ) {
            $where .= " AND ( `unidades_in`.`idunidades` = $unidade )";
        } else {
            $where .= "WHERE ( `unidades_in`.`idunidades` = $unidade )";
        }
    }

    $clausula = '';
    if ( !empty( $n_cela ) or !empty( $n_raio ) ){

        if ( empty( $n_cela ) ){

            $clausula = "`raio`.`idraio` = $n_raio";

        } else {

            $clausula = "`detentos`.`cod_cela` = $n_cela";

        }

        if ( !empty( $where ) ){
            $where .= " AND ( $clausula )";
        } else {
            $where .= "WHERE ( $clausula )";
        }

    }

    if ( !empty( $data_in_ini ) or !empty( $data_in_fim ) ){

        if ( !empty( $data_in_ini ) and  !empty( $data_in_fim ) ){

            $clausula_data_in = "`mov_det_in`.`data_mov` BETWEEN STR_TO_DATE( '$data_in_ini', '%d/%m/%Y' ) AND STR_TO_DATE( '$data_in_fim', '%d/%m/%Y' )";

            if ( !empty( $where ) ){
                $where .= ' AND ' . $clausula_data_in;
            } else {
                $where .= 'WHERE ' . $clausula_data_in;
            }

        } else {

            $data_in = !empty( $data_in_ini ) ? $data_in_ini : $data_in_fim;

            $clausula_data_in = "`mov_det_in`.`data_mov` = STR_TO_DATE( '$data_in', '%d/%m/%Y' )";

            if ( !empty( $where ) ){
                $where .= ' AND ' . $clausula_data_in;
            } else {
                $where .= 'WHERE ' . $clausula_data_in;
            }

        }

    }

    if ( !empty( $data_out_ini ) or !empty( $data_out_fim ) ){

        if ( !empty( $data_out_ini ) and  !empty( $data_out_fim ) ){

            $clausula_data_out = "`mov_det_out`.`data_mov` BETWEEN STR_TO_DATE('$data_out_ini', '%d/%m/%Y') AND STR_TO_DATE('$data_out_fim', '%d/%m/%Y')";

            if ( !empty( $where ) ){
                $where .= ' AND ' . $clausula_data_out;
            } else {
                $where .= 'WHERE ' . $clausula_data_out;
            }

        } else {

            $data_out = !empty( $data_out_ini ) ? $data_out_ini : $data_out_fim;

            $clausula_data_out = "`mov_det_out`.`data_mov` = STR_TO_DATE( '$data_out', '%d/%m/%Y' )";

            if ( !empty( $where ) ){
                $where .= ' AND ' . $clausula_data_out;
            } else {
                $where .= 'WHERE ' . $clausula_data_out;
            }

        }

    }

    $clausula = get_where_det( $tipo_sit );

    if ( !empty( $clausula ) ){

        if ( !empty( $where ) ){
            $where .= ' AND ' . $clausula;
        } else {
            $where .= 'WHERE ' . $clausula;
        }

    }

    //echo $where;
    //exit;

    $ordpor = get_get( 'op', 'busca' );

    if ( empty( $ordpor ) ) {
        $ordpor = 'nomea';
    }

    $ordbusca = '`detentos`.`nome_det` ASC';

    switch ( $ordpor ) {

        default:
        case 'nomea':
            $ordbusca = '`detentos`.`nome_det` ASC';
            break;

        case 'nomed':
            $ordbusca = '`detentos`.`nome_det` DESC';
            break;

        case 'matra':
            $ordbusca = '`detentos`.`matricula` ASC';
            break;

        case 'matrd':
            $ordbusca = '`detentos`.`matricula` DESC';
            break;

        case 'proca':
            $ordbusca = '`unidades_in`.`unidades` ASC, `detentos`.`nome_det` ASC';
            break;

        case 'procd':
            $ordbusca = '`unidades_in`.`unidades` DESC, `detentos`.`nome_det` ASC';
            break;

        case 'dataa':
            $ordbusca = '`mov_det_in`.`data_mov` ASC, `detentos`.`nome_det` ASC';
            break;

        case 'datad':
            $ordbusca = '`mov_det_in`.`data_mov` DESC, `detentos`.`nome_det` ASC';
            break;

        case 'raioa':
            $ordbusca = '`detentos`.`cod_cela` ASC, `detentos`.`nome_det` ASC';
            break;

        case 'raiod':
            $ordbusca = '`detentos`.`cod_cela` DESC, `detentos`.`nome_det` ASC';
            break;

    }

    $query = "SELECT DISTINCT
                `detentos`.`iddetento`,
                `detentos`.`nome_det`,
                `detentos`.`matricula`,
                `detentos`.`pai_det`,
                `detentos`.`mae_det`,
                `mov_det_in`.`data_mov` AS data_incl,
                DATE_FORMAT(`mov_det_in`.`data_mov`, '%d/%m/%Y') AS data_incl_f,
                `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
                `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
                `unidades_in`.`unidades` AS procedencia,
                `unidades_out`.`idunidades` AS iddestino,
                `cela`.`cela`,
                `raio`.`raio`
              FROM
                `detentos`
                $inner_join
                LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
                LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                LEFT JOIN `aliases` ON `detentos`.`iddetento` = `aliases`.`cod_detento`
                LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
              $where
              ORDER BY
                $ordbusca";

    //depur ($query);
    //exit;

    $db = SicopModel::getInstance();

    $query = $db->query( $query );

    $querytime = $db->getQueryTime();

    if ( !$query ) {

        // pegar a mensagem de erro mysql
        $msg_err_mysql = $db->getErrorMsg();
        $db->closeConnection();

        // gerar a mensagem q será salva no log
        $msg = sysmsg::create_msg();
        $msg->set_msg_type( SM_TYPE_ERR );
        $msg->add_quebras( 1 );
        $msg->set_msg_pre_def( SM_QUERY_FAIL );
        $msg->add_parenteses( "PESQUISAR $motivo" );
        $msg->add_quebras( 2 );
        $msg->set_msg( $msg_err_mysql );
        $msg->get_msg();

        echo msg_js( 'FALHA!', 1 );
        exit;

    }

    $db->closeConnection();

    $cont = $query->num_rows;

    $valor_busca = valor_user( $_GET );

    // gerar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->add_chaves( "BUSCA EFETUADA", 0, 1 );
    $msg->set_msg( 'Busca de ' . SICOP_DET_DESC_L . 's efetuada' );
    $msg->add_quebras( 2 );
    $msg->set_msg( $valor_busca );
    $msg->add_quebras( 2 );
    $msg->set_msg( "Quantidade de registros retornados: $cont" );
    $msg->get_msg();

    if ( $cont == 1 ) { //se o número de ocorrências for igual a 1 vai direto para à página do detento, e finaliza com o exit
        $d_det = $query->fetch_object();
        $iddet = $d_det->iddetento;
        header( "Location: $link?iddet=" . $iddet . $add_link );
        exit;
    }

    parse_str( $_SERVER['QUERY_STRING'], $q_string );

    if ( isset( $q_string['op'] ) ) {
        unset( $q_string['op'] );
    }

}

$db = SicopModel::getInstance();

$q_tipo_sit = 'SELECT `idtipo_sit`, `tipo_sit` FROM `tipo_sit_det_busca` ORDER BY `idtipo_sit` ASC ';
$q_tipo_sit = $db->query( $q_tipo_sit );

$q_proced = 'SELECT `idunidades`, `unidades` FROM `unidades` WHERE `in` = true ORDER BY `unidades`';
$q_proced = $db->query( $q_proced );

//$query_raio = 'SELECT `idraio`, `raio` FROM `raio` ORDER BY `raio` ASC';
//$query_raio = $db->query( $query_raio );
//
//if ( !empty( $n_raio ) ) {
//
//    $query_cela = "SELECT `idcela`, `cela` FROM `cela` WHERE `cod_raio` = $n_raio ORDER BY cela ASC";
//    $query_cela = $db->query( $query_cela );
//
//}

$db->closeConnection();

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();

?>

            <p class="descript_page">PESQUISAR <?php echo $motivo; ?></p>

            <form action="buscadet.php" method="get" name="buscadet" id="buscadet" onSubmit="upperMe(campobusca); ">

                <table class="busca_form"><!--remacc(campobusca);-->
                    <tr>
                        <td class="bf_det_legend">Nome ou matrícula:</td>
                        <td class="bf_det_field"><input name="campobusca" type="text" class="CaixaTexto" id="campobusca" onkeypress="return blockChars(event, 4);" value="<?php echo $valorbusca_sf ?>" size="50" /></td>
                    </tr>
                    <tr>
                        <td class="bf_det_legend">Pesquisa fonética:</td>
                        <td class="bf_det_field"><input name="tipo_fon" type="radio" id="tipo_fon_0" value="1" <?php echo ( ( !empty($_GET ) and $tipo_fon == '1' ) or empty( $tipo_fon ) ) ? 'checked="checked"' : ''; ?> /> a frase exata &nbsp; <input name="tipo_fon" type="radio" id="tipo_fon_1" value="2" <?php echo ( !empty( $_GET ) and $tipo_fon == '2' ) ? 'checked="checked"' : ''; ?> /> que contenha as palavras </td>
                    </tr>
                    <tr>
                        <td class="bf_det_legend"><?php echo SICOP_RAIO ?>:</td>
                        <td class="bf_det_field">
                            <select name="n_raio" class="CaixaTexto" id="n_raio" onChange="$.monta_box_cela();">
                                <option value="" selected="selected">Selecione</option>
                            </select> &nbsp;&nbsp;
                            <?php echo SICOP_CELA ?>:
                            <select name="n_cela" class="CaixaTexto" id="n_cela">
                                <option value="" selected="selected">Escolha o <?php echo mb_strtolower( SICOP_RAIO ) ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="bf_det_legend">Procedência:</td>
                        <td class="bf_det_field">
                            <select name="unidade" class="CaixaTexto" id="local_mov">
                                <option value="" >Selecione...</option>
                                <?php while ( $d_proced = $q_proced->fetch_object() ) { ?>
                                <option value="<?php echo $d_proced->idunidades; ?>" <?php echo $d_proced->idunidades == $unidade ? 'selected="selected"' : ''; ?>><?php echo $d_proced->unidades; ?></option>
                                <?php };?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="bf_det_legend">Data da inclusão:</td>
                        <td class="bf_det_field">
                            <input name="data_in_ini" type="text" class="CaixaTexto" id="data_in_ini" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_in_ini ?>" size="12" maxlength="10" /> e
                            <input name="data_in_fim" type="text" class="CaixaTexto" id="data_in_fim" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_in_fim ?>" size="12" maxlength="10" />
                        </td>
                    </tr>
                    <tr>
                        <td class="bf_det_legend">Data da exclusão:</td>
                        <td class="bf_det_field">
                            <input name="data_out_ini" type="text" class="CaixaTexto" id="data_out_ini" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_out_ini ?>" size="12" maxlength="10" /> e
                            <input name="data_out_fim" type="text" class="CaixaTexto" id="data_out_fim" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_out_fim ?>" size="12" maxlength="10" />
                        </td>
                    </tr>
                    <tr>
                        <td class="bf_det_legend">Situação do preso:</td>
                        <td class="bf_det_field">
                            <select name="tipo_sit" class="CaixaTexto" id="tipo_sit">
                                <option value="">Todos</option>
                                <?php while ( $d_tipo_sit = $q_tipo_sit->fetch_object() ) { ?>
                                <option value="<?php echo $d_tipo_sit->idtipo_sit; ?>" <?php echo $d_tipo_sit->idtipo_sit == $tipo_sit ? 'selected="selected"' : ''; ?>><?php echo $d_tipo_sit->tipo_sit; ?></option>
                                <?php };?>
                            </select>
                        </td>
                    </tr>

                </table><!-- /table class="busca_form" -->

                <div class="form_bts">
                    <?php if ( $proced == 'regrol' ) { ?>
                    <p class="p_common"><a href="visita/buscavisit.php?proced=1">Pesquisar pelo visitante</a></p>
                    <?php } ?>
                    <input class="form_bt" type="submit" name="buscar" id="buscar" value="Buscar" />
                </div>

                <input type="hidden" name="proced" id="proced" value="<?php echo $proced; ?>" />
                <?php if ( !empty( $ant ) ) { ?>
                <input type="hidden" name="ant" id="ant" value="<?php echo $ant; ?>" />
                <?php } ?>
                <input type="hidden" name="busca" id="busca" value="busca" />

                <input type="hidden" name="old_raio" id="old_raio" value="<?php echo $n_raio;?>" />
                <input type="hidden" name="old_cela" id="old_cela" value="<?php echo $n_cela;?>" />

            </form>

            <script type="text/javascript">

                $(function() {

                    $( "#campobusca" ).focus().select();

                    $.monta_box_raio();

                    $( "#data_in_ini, #data_in_fim, #data_out_ini, #data_out_fim" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });

                });

            </script>

            <?php

            if ( !empty( $_GET['busca'] ) ) {

                include 'lista_busca.php';

            }

            include 'footer.php';

            ?>