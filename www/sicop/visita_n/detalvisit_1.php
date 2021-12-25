<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag          = link_pag();
$motivo_pag   = 'DETALHES DO VISITANTE';
$img_sys_path = SICOP_SYS_IMG_PATH;

$n_rol   = get_session( 'n_rol', 'int' );
$n_rol_n = 2;

if ( $n_rol < $n_rol_n ) {

    // montar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ATEN );
    $msg->set_msg_pre_def( SM_NO_PERM );
    $msg->add_parenteses( $motivo_pag );
    $msg->get_msg();

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$imp_rol = get_session( 'imp_rol', 'int' );

$idvisit = get_get( 'idvisit', 'int' );

if ( empty( $idvisit ) ) {

    // montar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ATEN );
    $msg->set_msg_pre_def( SM_DIRECT_ACCESS );
    $msg->add_parenteses( "IDENTIFICADOR EM BRANCO - $motivo_pag" );
    $msg->get_msg();

    echo msg_js( '', 1 );

    exit;

}

// sub-query utilizada para retornar apenas 1 suspenção
$sub_query_v = "SELECT
                  visita_susp.id_visit_susp
                FROM
                  visita_susp
                WHERE
                  visita_susp.cod_visita = visitas.idvisita
                ORDER BY
                  revog, data_inicio DESC
                LIMIT 1";

$query_visit = "SELECT
                  `visitas`.`idvisita`,
                  `visitas`.`nome_visit`,
                  `visitas`.`rg_visit`,
                  `visitas`.`sexo_visit`,
                  `visitas`.`nasc_visit`,
                  DATE_FORMAT( `visitas`.`nasc_visit`, '%d/%m/%Y' ) AS nasc_visit_f,
                  FLOOR( DATEDIFF( CURDATE(), visitas.nasc_visit )/365.25 ) AS idade_visit,
                  `visitas`.`resid_visit`,
                  `visitas`.`telefone_visit`,
                  `visitas`.`pai_visit`,
                  `visitas`.`mae_visit`,
                  `visitas`.`telefone_visit`,
                  `visitas`.`defeito_fisico`,
                  `visitas`.`sinal_nasc`,
                  `visitas`.`cicatrizes`,
                  `visitas`.`tatuagens`,
                  `visitas`.`doc_rg`,
                  `visitas`.`doc_foto34`,
                  `visitas`.`doc_resid`,
                  `visitas`.`doc_ant`,
                  `visitas`.`doc_cert`,
                  `visitas`.`user_add`,
                  DATE_FORMAT( `visitas`.`data_add`, '%d/%m/%Y às %H:%i' ) AS data_add,
                  `visitas`.`user_up`,
                  DATE_FORMAT( `visitas`.`data_up`, '%d/%m/%Y às %H:%i' ) AS data_up,
                  `cidades`.`nome` AS cidade_visit,
                  `estados`.`sigla` AS estado_visit,
                  `visita_fotos`.`foto_visit_g`,
                  `visita_fotos`.`foto_visit_p`,
                  DATE_FORMAT( `visita_susp`.`data_inicio`, '%d/%m/%Y' ) AS data_inicio_f,
                  visita_susp.periodo,
                  visita_susp.motivo,
                  visita_susp.revog,
                  ADDDATE( visita_susp.data_inicio, visita_susp.periodo ) AS data_fim,
                  DATE_FORMAT( ADDDATE( visita_susp.data_inicio, visita_susp.periodo ), '%d/%m/%Y' ) AS data_fim_f
                FROM
                  `visitas`
                  LEFT JOIN `cidades` ON `visitas`.`cod_cidade_v` = `cidades`.`idcidade`
                  LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado`
                  LEFT JOIN `visita_fotos` ON `visita_fotos`.`id_foto` = `visitas`.`cod_foto`
                  LEFT JOIN visita_susp ON visitas.idvisita = visita_susp.cod_visita
                WHERE
                  `visitas`.`idvisita` = $idvisit
                  AND
                  ( visita_susp.id_visit_susp = ( $sub_query_v ) OR ISNULL( visita_susp.id_visit_susp ) )
                LIMIT 1";

$query_mov_visit = "SELECT
                      `idmov_visit`,
                      `cod_visita`,
                      `num_seq`,
                      `jumbo`,
                      `data_in`,
                      DATE_FORMAT(`data_in`, '%d/%m/%Y') AS data_in_f,
                      DATE_FORMAT(`data_in`, '%H:%i') AS hora_in,
                      `user_in`,
                      DATE_FORMAT(`data_out`, '%d/%m/%Y') AS data_out,
                      DATE_FORMAT(`data_out`, '%H:%i') AS hora_out,
                      `user_out`
                    FROM
                      `visita_mov`
                    WHERE
                      `cod_visita` = $idvisit
                    ORDER BY
                      `data_in` DESC
                    LIMIT 10";

$query_obs = "SELECT
                `id_obs_visit`,
                `cod_visita`,
                `obs_visit`,
                `destacar`,
                `user_add`,
                DATE_FORMAT(`data_add`, '%d/%m/%Y') AS data_add_f,
                DATE_FORMAT(`data_add`, '%d/%m/%Y às %H:%i') AS data_add_fc,
                `data_add`,
                `user_up`,
                DATE_FORMAT(`data_up`, '%d/%m/%Y às %H:%i') AS data_up_f,
                `data_up`
              FROM
                `obs_visit`
              WHERE
                cod_visita = $idvisit
              ORDER BY
                `data_add` DESC
              LIMIT 10";

$q_cont_obs = "SELECT
                `id_obs_visit`
              FROM
                `obs_visit`
              WHERE
                `cod_visita` = $idvisit
                AND
                `destacar` = TRUE
              ORDER BY
                `data_add` DESC
              LIMIT 10";

$query_susp = "SELECT
                 `id_visit_susp`,
                 `cod_visita`,
                 `data_inicio`,
                 DATE_FORMAT(`data_inicio`, '%d/%m/%Y') AS data_inicio_f,
                 `periodo`,
                 ADDDATE(`data_inicio`, `periodo`) AS `data_fim`,
                 DATE_FORMAT( ADDDATE( `data_inicio`, `periodo` ), '%d/%m/%Y') AS data_fim_f,
                 `motivo`,
                 `revog`,
                 `user_add`,
                 `data_add`,
                 DATE_FORMAT(`data_add`, '%d/%m/%Y às %H:%i') AS data_add_f,
                 `user_up`,
                 `data_up`,
                 DATE_FORMAT(`data_up`, '%d/%m/%Y às %H:%i') AS data_up_f
               FROM
                 `visita_susp` ";

$query_susp_lista = $query_susp . "WHERE
                                     ( `cod_visita` = $idvisit )
                                   ORDER BY `data_inicio` DESC";

$db = SicopModel::getInstance();
$query_visit = $db->query( $query_visit );
if ( !$query_visit ) {

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();
    $db->closeConnection();

    // gerar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ERR );
    $msg->add_quebras( 1 );
    $msg->set_msg_pre_def( SM_QUERY_FAIL );
    $msg->add_parenteses( $motivo_pag );
    $msg->add_quebras( 2 );
    $msg->set_msg( $msg_err_mysql );
    $msg->get_msg();

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$db->closeConnection();

$contv = $query_visit->num_rows;
if ( $contv < 1 ) {

    // gerar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ERR );
    $msg->add_quebras( 1 );
    $msg->set_msg( "A consulta retornou 0 ocorrências" );
    $msg->add_parenteses( $motivo_pag );
    $msg->add_quebras( 2 );
    $msg->get_msg();

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$query_obs = mysql_query( $query_obs );
$cont_obs = mysql_num_rows( $query_obs );

$q_cont_obs = mysql_query( $q_cont_obs );
$cont_obs_destaq = mysql_num_rows( $q_cont_obs );

$atent_obs = '';
if ( $cont_obs_destaq >= 1 ) {
    $atent_obs = 'ATENTAR OBSERVAÇÕES';
}

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Detalhes do visitante';

$d_visit = $query_visit->fetch_assoc();

$foto_g = $d_visit['foto_visit_g'];
$foto_p = $d_visit['foto_visit_p'];

$foto_visit = ck_pic( $foto_g, $foto_p, false, 2 );

$amplia = true;
if ( empty( $foto_g ) ) {
    $amplia = false;
}

$user_add = '';
$user_up = '';

if ( !empty( $d_visit['user_add'] ) ) {
    $user_add = 'Usuário: ' . $d_visit['user_add'] . ', em ' . $d_visit['data_add'];
}

if ( !empty( $d_visit['user_up'] ) ) {
    $user_up = 'Usuário: ' . $d_visit['user_up'] . ', em ' . $d_visit['data_up'];
}


$query_susp_lista = mysql_query( $query_susp_lista );
$erromysql = mysql_error();
$contsusplista = mysql_num_rows( $query_susp_lista );

$revog    = $d_visit['revog'];
$data_fim = $d_visit['data_fim'];

$susp = get_sit_visita( $revog, $data_fim );

$suspenso = $susp['suspenso'];
$excluido = $susp['excluido'];

$num_pass_visit = $d_visit['num_in'];
$n_pass_det     = $d_det['n_p_trans'];
$inativo = false;
if ( $num_pass_visit != $n_pass_det ) {
    $inativo = true;
}


















$proced      = get_get( 'proced', 'int' );
$reg_entrada = false;
$reg_saida   = false;
$pode_entrar = true;
$pode_sair   = true;

if ( !empty( $proced ) and $proced == 1 ){

    if ( $n_rol < 3 ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'perm';
        $msg['entre_ch'] = 'REGISTRO DE ENTRADA DE VISITANTE';
        get_msg( $msg, 1 );

        require 'cab_simp.php';
        echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

        exit;

    }

    $reg_entrada = true;
    $jumbo       = true;
    $motivo      = '';
    $desc_pag    = 'Registrar entrada';

    $idd     = $d_visit['cod_detento'];
    $idv     = $d_visit['idvisita'];
    $nasc_v  = $d_visit['nasc_visit'];
    $idade_v = $d_visit['idade_visit'];

    /**
     * PARTE QUE VERIFICA A SITUAÇÃO DO DETENTO
     */

    // pegar os dados do detento
    $query_det_in = "SELECT
                       `detentos`.`cod_cela`,
                       `detentos`.`aut_visita`,
                       `detentos`.`n_p_trans`,
                       `mov_det_in`.`data_mov` AS data_incl,
                       `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
                       `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
                       `unidades_out`.`idunidades` AS iddestino
                     FROM
                       `detentos`
                       LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                       LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                       LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                     WHERE
                       `iddetento` = $idd
                     LIMIT 1";
    $query_det_in = mysql_query( $query_det_in );
    $d_det_in     = mysql_fetch_assoc( $query_det_in );

    $tipo_mov_in  = $d_det_in['tipo_mov_in'];
    $tipo_mov_out = $d_det_in['tipo_mov_out'];
    $iddestino    = $d_det_in['iddestino'];
    $sit_det_in   = manipula_sit_det_id( $tipo_mov_in, $tipo_mov_out, $iddestino );

    $aut_visita_in = $d_det_in['aut_visita'];
    $data_in       = $d_det_in['data_incl'];
    $idcela        = $d_det_in['cod_cela'];

    // verifica a situação do detento
    if ( $sit_det_in == SICOP_SIT_DET_TRADA ||     // TRANSITO DA CASA
         $sit_det_in == SICOP_SIT_DET_TRANADA ||   // TRANSITO NA CASA DA CASA
         $sit_det_in == SICOP_SIT_DET_TRANSF ||    // TRANSFERIDO
         $sit_det_in == SICOP_SIT_DET_EXCLUIDO ||  // EXCLUIDO (ALVARA)
         $sit_det_in == SICOP_SIT_DET_EVADIDO ||   // EVADIDO
         $sit_det_in == SICOP_SIT_DET_FALECIDO ||  // FALECIDO
         $sit_det_in == SICOP_SIT_DET_ACEHGAR ) {  // A CHEGAR

        $pode_entrar = false;

        $motivo = SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_L;

        if ( $sit_det_in == SICOP_SIT_DET_TRADA || $sit_det_in == SICOP_SIT_DET_TRANADA ) {
            $motivo .= ' está de transito.';
        } else if ( $sit_det_in == SICOP_SIT_DET_TRANSF ) {
            $motivo .= ' foi transferido.';
        } else if ( $sit_det_in == SICOP_SIT_DET_EXCLUIDO ) {
            $motivo .= ' foi post' . SICOP_DET_ART_L . ' em liberdade.';
        } else if ( $sit_det_in == SICOP_SIT_DET_EVADIDO ) {
            $motivo .= ' está evadid' . SICOP_DET_ART_L . '.';
        } else if ( $sit_det_in == SICOP_SIT_DET_FALECIDO ) {
            $motivo .= ' faleceu.';
        } else if ( $sit_det_in == SICOP_SIT_DET_ACEHGAR ) {
            $motivo .= ' não está na unidade.';
        }

    }

    // calculos de timestamp para o R.O.
    $data_fim = strtotime( $data_in . "+7 days" );
    $hj       = time();

    // verifica se o detento esta de R.O. (somente presos DA CASA)
    if ( ( $sit_det_in == SICOP_SIT_DET_NA and $data_fim > $hj ) and $pode_entrar ) {

        $pode_entrar = false;
        $motivo = SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_L . ' encontra-se em regime de observação (R.O.).';

    }

    // verifica se o detento esta autorizado a receber visitas
    if ( $aut_visita_in == 0 and $pode_entrar ) {

        $pode_entrar = false;
        $motivo = SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_L . ' não está autorizad' . SICOP_DET_ART_L . ' a receber visitas.';

    }

    // verifica se o detento possui raio e cela cadastrados
    if ( empty( $idcela ) and $pode_entrar ) {

        $pode_entrar = false;
        $motivo = SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_L . ' não possui ' . mb_strtolower( SICOP_RAIO ) . '/' . mb_strtolower( SICOP_CELA ) . ' cadastrados. Entre em contato com a chefia!';

    }

    if ( $pode_entrar ) {

        $n_reg_v = get_session( 'n_reg_v', 'int' );

        /*
         * se o $n_reg_v for igual a 4 o usuário pode
         * registrar entrada para qualquer raio.
         * neste caso verifica se é diferente de 4
         */
        if ( $n_reg_v != 4 ) {

            $user_raio_par = true;

            // se nao tiver $n_reg_v ou ele for igual a 1 o usário não pode registrar entrada de visitantes
            if ( empty( $n_reg_v ) || $n_reg_v == 1 ) {

                $pode_entrar = false;
                $motivo = 'Você não está autorizado a registrar entrada de visitantes!';

            // se $n_reg_v for igual a 3, registra para os ímpares
            } else if ( $n_reg_v == 3 ) {

                $user_raio_par = false;

            }

            // consulta para pegar o idraio e a cela do detento.
            $q_ck_rc = "SELECT
                          `cela`,
                          `cod_raio`
                        FROM
                          `cela`
                        WHERE
                          `idcela` = $idcela
                        LIMIT 1";

            $q_ck_rc   = mysql_query( $q_ck_rc );
            $d_rc      = mysql_fetch_assoc( $q_ck_rc );
            $idraio_ck = $d_rc['cod_raio'];
            $cela_ck   = $d_rc['cela'];

            // se $idraio_ck for menor do que 9, o detento esta nos raios de convívio 1 a 8...
            if ( $idraio_ck < 9 ) {

                /*
                 * $idraio_ck%2 -> se o resto da divisão por 2 for 0 o raio é par.
                 *
                 * se o usuário puder registrar para raios pares
                 * e o raio for ímpar
                 * ou
                 * se o usuário puder registrar para raios ímpares
                 * e o raio for par...
                 */
                if ( ( $user_raio_par and $idraio_ck%2 != 0 ) or ( !$user_raio_par and $idraio_ck%2 == 0 )  ) {
                    $pode_entrar = false;
                    $motivo = 'Você não está autorizado a registrar entrada de visitantes para este raio!';
                }

            /*
             * se o $idraio_ck for maior/igual a 9 e menor do que 12
             * é por que o detento esta no PH, PD, INCL
             * visitas somente aos sabados
             */
            } else if ( $idraio_ck >= 9 and $idraio_ck < 12 ) {

                // verifica se do dia da semana é sabado
                $dia_semana = date( 'w', time() );
                if ( $dia_semana != 6 ) {
                    $pode_entrar = false;
                    $motivo = 'Visitas para este pavilhão só estão autorizadas aos sabados!';
                }

            /*
             * se o $idraio_ck for igual a 12, o detento esta no PS
             * e segue a mesma verificação dos raios, mas nas celas
             */
            } else if ( $idraio_ck == 12 ) {

                /*
                 * $cela_ck%2 -> se o resto da divisão por 2 for 0 a cela é par.
                 *
                 * se o usuário puder registrar para raios pares
                 * e a cela for ímpar
                 * ou
                 * se o usuário puder registrar para raios ímpares
                 * e a cela for par...
                 */
                if ( ( $user_raio_par and $cela_ck%2 != 0 ) or ( !$user_raio_par and $cela_ck%2 == 0 )  ) {
                    $pode_entrar = false;
                    $motivo = 'Você não está autorizado a registrar entrada de visitantes para esta cela!';
                }

            // se não tiver nenhum do valores acima barra a entrada por raio/cela inválidos
            } else {

                $pode_entrar = false;
                $motivo = SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_L . ' não possui ' . mb_strtolower( SICOP_RAIO ) . '/' . mb_strtolower( SICOP_CELA ) . ' cadastrados. Entre em contato com a chefia!';

            }

        }

    }


    /**
     * FIM PARTE QUE VERIFICA A SITUAÇÃO DO DETENTO
     */

    /**
     * PARTE QUE VERIFICA A SITUAÇÃO DO VISITANTE
     */

    // pesquisar se o visitante possui suspensões ativas
    if ( $pode_entrar ) {

        $sit_visit = manipula_sit_visia( $idv );

        if ( $sit_visit['suspenso'] ) {

            $pode_entrar = false;
            $motivo = 'Este visitante está suspenso.';

        }

        if ( $sit_visit['excluido'] ) {

            $pode_entrar = false;
            $motivo = 'Este visitante está excluído do rol d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . '.';

        }

    } // fim do if que verifica se o visitante pode entrar

    if ( $inativo and $pode_entrar ) {

            $pode_entrar = false;
            $motivo = 'O visitante é de passagens anteriores e está inativo no rol d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . '.';

    } // fim do if que verifica se o visitante pode entrar

    // se a data de nascimento não estiver em branco e o visitante for menor de 18 anos
    if ( ( !empty( $nasc_v ) and $idade_v < 18 ) and $pode_entrar ) {

        if ( $idade_v < 16 ) {
            $pode_entrar = false;
            $motivo = 'Menores só podem entrar acompanhados de adultos.';
        } else if ( ( $idade_v >= 16 and $idade_v < 18 ) and empty( $aut_jud ) ) {
            $pode_entrar = false;
            $motivo = 'Entre 16 e 18 anos, o menor deve estar acompanhado de um adulto ou possuir autorização judicial para entrar na unidade.';
        }

    }

    // pesquisar quantos visitantes com 12 anos ou mais ja entraram para o detento
    if ( $pode_entrar ) {

        $q_quant_in = "SELECT
                         `visitas`.`idvisita`
                       FROM
                         `visita_mov`
                         INNER JOIN `visitas` ON `visita_mov`.`cod_visita` = `visitas`.`idvisita`
                       WHERE
                         DATE( `visita_mov`.`data_in`) = DATE(NOW())
                         AND
                         `visitas`.`cod_detento` = $idd
                         AND
                         FLOOR( DATEDIFF( CURDATE(), `visitas`.`nasc_visit` )/365.25 ) >= 12";

        $q_quant_in = mysql_query( $q_quant_in );
        $quant_in = mysql_num_rows( $q_quant_in );

        // se houver 2 ou mais, o limite de 2 visitantes adultos já foi atingido
        if ( $quant_in >= 2 ) {

            $pode_entrar = false;
            $motivo = 'Já entraram 2 (dois) visitantes adultos para ' . SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L . '.';

        }
    } // fim do if que verifica se o visitante pode entrar

    // se a data de nascimento do visitante estiver em branco
    if ( empty( $nasc_v ) and $pode_entrar ) {

        $pode_entrar = false;
        $motivo = 'Você deve preencher a data de nascimento para o ingresso do visitante na unidade.';

    }

    // pesquisar se este visitante ja entrou no dia na unidade
    if ( $pode_entrar ) {

        $q_ja_ent  = "SELECT `cod_visita` FROM `visita_mov` WHERE DATE(`data_in`) = DATE(NOW()) AND `cod_visita` = $idv LIMIT 1";
        $q_ja_ent  = mysql_query( $q_ja_ent );
        $ja_entrou = mysql_num_rows( $q_ja_ent );

        // se ja entrou no dia
        if ( $ja_entrou >= 1 ) {

            $pode_entrar = false;
            $motivo = 'Este visitante já está na unidade.';

        }

    } // fim do if que verifica se o visitante pode entrar

    /**
     * FIM DA PARTE QUE VERIFICA A SITUAÇÃO DO VISITANTE
     */

    // pesquisar se algum visitante já entrou com jumbo
    if ( $pode_entrar ) {

        $q_jumbo = "SELECT
                      `visitas`.`idvisita`
                    FROM
                      `visita_mov`
                      INNER JOIN `visitas` ON `visita_mov`.`cod_visita` = `visitas`.`idvisita`
                    WHERE
                      DATE( `visita_mov`.`data_in` ) = DATE( NOW() )
                      AND
                      `visitas`.`cod_detento` = $idd
                      AND
                      `visita_mov`.`jumbo` = TRUE";

        $q_jumbo = mysql_query( $q_jumbo );
        $quant_jumbo = mysql_num_rows( $q_jumbo );
        if ( $quant_jumbo >= 1 ) {
            $jumbo = false;
        }

    } // fim do if que verifica se o visitante pode entrar

} else if ( !empty( $proced ) and $proced == 2 ) {

    if ( $n_rol < 3 ) {

        // montar a mensagem q será salva no log
        $msg             = array();
        $msg['tipo']     = 'perm';
        $msg['entre_ch'] = 'REGISTRO DE SAIDA DE VISITANTE';
        get_msg( $msg, 1 );

        require 'cab_simp.php';
        echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

        exit;

    }

    $reg_saida = true;
    $motivo    = '';
    $idv       = $d_visit['idvisita'];

    // pesquisar se este visitante entrou no dia na unidade
    $q_out = "SELECT
                `visita_mov`.`cod_visita`,
                `visita_mov`.`num_seq`,
                `visita_mov`.`data_out`
              FROM
                `visita_mov`
              WHERE
                DATE( `visita_mov`.`data_in` ) = DATE( NOW() )
                AND
                `visita_mov`.`cod_visita` = $idv
              LIMIT 1";

    $q_out  = mysql_query( $q_out );
    $entrou = mysql_num_rows( $q_out );

    // se não entrou no dia
    if ( $entrou < 1 ) {

        $pode_sair = false;
        $motivo    = 'Este visitante não entrou na unidade.';

    }

    $d_v_out = '';
    if ( $pode_sair ) {
        $d_v_out = mysql_fetch_assoc( $q_out );
    }

    if ( $pode_sair ) {

        $data_out = $d_v_out['data_out'];

         // se $data_out não for empty, é porque o visitante já saiu
        if ( !empty( $data_out ) ) {

            $pode_sair = false;
            $motivo    = 'Este visitante já saiu da unidade.';

        }

    } // fim do if que verifica se o visitante pode sair

    if ( $pode_sair ) {

        $num_seq_out = $d_v_out['num_seq'];

    } // fim do if que verifica se o visitante pode sair


}

// query que conta as entradas do visitante
$total_in          = 0;
$q_total_mov_visit = "SELECT COUNT(`idmov_visit`) AS total FROM `visita_mov` WHERE `cod_visita` = $idvisit";
$q_total_mov_visit = mysql_query($q_total_mov_visit );
$d_t_mov_v         = mysql_fetch_assoc($q_total_mov_visit);
$total_in          = $d_t_mov_v['total'];

// adicionando o javascript
$cab_js = 'ajax/ajax_visit.js';
set_cab_js( $cab_js );

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 6 );
$trail->output();
?>

        <input type="hidden" name="idvisit" id="idvisit" value="<?php echo $idvisit; ?>" />

        <?php if ( !$pode_entrar and $reg_entrada ) { ?>
        <script type="text/javascript">
            alert('Este visitante NÃO PODE entrar na unidade! \nMotivo: <?php echo $motivo ?>');
        </script>
        <?php }?>
        <?php if ( !$pode_sair and $reg_saida ) { ?>
        <script type="text/javascript">
            alert('Você NÃO PODE registar a saida deste visitante da unidade! \nMotivo: <?php echo $motivo ?>');
        </script>
        <?php }?>

            <p class="descript_page">DETALHES DO VISITANTE</p>

            <?php if ( $imp_rol >= 1 ) { ?>
            <p class="link_common">
                <a href="#" title="Imprimir a relação de documentos para este visitante" onclick="javascript: ow('<?php echo SICOP_ABS_PATH; ?>print/rec_visit.php?idvisit=<?php echo $d_visit['idvisita'] ?>', '600', '600'); return false" >Imprimir relação de documentos</a>
                | <a href='javascript:void(0)' onclick="submit_form_nwid( '<?php echo SICOP_ABS_PATH; ?>print/ficha_quali_visit.php', 'idvisita', <?php echo $idvisit ?> )"  title="Imprimir a ficha de identificação deste visitante" >Imprimir ficha de identificação</a>
            </p>
            <?php }; ?>
            <?php if ( $n_rol >= 3 ) { ?>
            <p class="link_common">
                <?php if ( $inativo ) {?>
                <a href='javascript:void(0)' onclick='reat_visit(<?php echo $iddet; ?>, <?php echo $d_visit['idvisita']; ?>)' title="Reativar este visitante">Reativar visitante</a>
                <?php } else { ?>
                <a href="editvisit.php?idvisit=<?php echo $d_visit['idvisita'] ?>" title="Alterar dados deste visitante">Alterar dados</a> |
                <a href="suspvisit.php?idvisit=<?php echo $d_visit['idvisita'] ?>" title="Suspender a entrada deste visitante">Suspender visitante</a> |
                <a id="alter_foto_visit" href="cadimgvisit.php?idvisit=<?php echo $d_visit['idvisita'] ?>" title="Alterar a foto deste visitante">Alterar foto</a>
                <?php }; ?>
                | <a href="fotos_visit.php?idvisit=<?php echo $d_visit['idvisita'] ?>" title="Ver as fotos deste visitante">Relação de fotos</a>
                <?php if ( $n_rol >= 4 ) { ?> | <a href='javascript:void(0)' onclick='drop_visit(<?php echo $d_visit['idvisita']; ?>)' title="Excluir este visitante">Excluir Visitante</a><?php }; ?>
            </p>
            <?php }; ?>

            <table class="detal_visit">
                <tr>
                    <td class="mid">Visitante: <?php echo $d_visit['nome_visit'] ?></td>
                    <td class="mini">RG: <?php echo $d_visit['rg_visit'] ?></td>
                    <td class="mini" rowspan="8" align="center">
                        <?php if ( $amplia ){ ?>
                        <a id="link_foto_visit" href="<?php echo SICOP_VISIT_IMG_PATH . $foto_g ?>" title="<?php echo $d_visit['nome_visit']; if ( !empty( $d_visit['rg_visit'] ) ) echo ' - ' . $d_visit['rg_visit']; ?>">
                        <?php }; ?>
                        <img src="<?php echo $foto_visit ?>" alt="" class="foto_visit" />
                        <?php if ( $amplia ){ ?></a><?php } ?>

                    </td>
                </tr>

                <tr>
                    <td class="mid">Data de Nascimento: <?php echo ( empty( $d_visit['nasc_visit_f'] ) ) ? '' : $d_visit['nasc_visit_f']  . ' - ' .$d_visit['idade_visit'] . ' anos';// echo pegaIdade($d_visit['data_nasc'])  ?></td>
                    <td class="mini">Sexo: <?php echo $d_visit['sexo_visit'] ?></td>
                </tr>
                <tr>
                    <td class="mid">Parentesco:</td>
                    <td class="mini">ID no sistema: <?php echo $d_visit['idvisita'] ?></td>
                </tr>
                <tr>
                    <td class="mid">Endereço: <?php echo $d_visit['resid_visit'] ?></td>
                    <td class="mini">Nº de visitas: <?php echo $total_in ?></td>
                </tr>
                <tr>
                    <td class="mid">Telefone: <?php echo preg_replace( '/([0-9]{2})([0-9]{4})([0-9]{4})/', '(\\1) \\2-\\3', $d_visit['telefone_visit'] ) ?></td>
                    <td class="mini"><?php if ( !empty( $atent_obs ) ) { ?><a href="#obs"><font color="#FF0000"><b><?php echo $atent_obs;?></b></font></a><?php } ?></td>
                </tr>
                <tr>
                    <td class="mid">Naturalidade: <?php echo $d_visit['cidade_visit'] ?> - <?php echo $d_visit['estado_visit'] ?></td>
                    <td class="mini">&nbsp;</td>
                </tr>
                <tr>
                    <td class="mid">Pai: <?php echo $d_visit['pai_visit'] ?></td>
                    <td class="mini">&nbsp;</td>
                </tr>
                <tr>
                    <td class="mid">Mãe: <?php echo $d_visit['mae_visit'] ?></td>
                    <td class="mini">&nbsp;</td>
                </tr>
                <tr>
                    <td class="mid">Defeito(s) físico(s): <?php echo $d_visit['defeito_fisico'] ?></td>
                    <td class="mid" colspan="2">Sinal(is) de nascimento: <?php echo $d_visit['sinal_nasc'] ?></td>
                </tr>
                <tr>
                    <td class="mid">Cicatriz(es): <?php echo $d_visit['cicatrizes'] ?></td>
                    <td class="mid" colspan="2">Tatuagem(ns): <?php echo $d_visit['tatuagens'] ?></td>
                </tr>
                <tr>
                    <td class="mid">Situação atual do visitante: <span class="<?php echo $susp['css_dest'] ?>"><?php echo $susp['sit_v']; ?></span><?php if ( $inativo ) {?> (Inativo no rol) <?php }; ?></td>
                    <td align="center" ><?php if ( $suspenso || $excluido ) { ?> A partir de <?php echo $d_visit['data_inicio_f'] ?> <?php } ?></td>
                    <td align="center" ><?php if ( $suspenso ) { ?> Até <?php echo $d_visit['data_fim_f'] ?> <?php } ?></td>
                </tr>
                <?php if ( $suspenso || $excluido ) { ?>
                <tr>
                    <td class="great_mot" colspan="3">Motivo: <?php echo $d_visit['motivo'] ?></td>
                </tr>
                <?php } ?>

                <tr>
                    <td class="great_destaque" colspan="3">DOCUMENTAÇÃO</td>
                </tr>
                <tr>
                    <td class="great" colspan="3">
                        Xerox RG: <font color="<?php echo empty( $d_visit['doc_rg'] ) ? '#FF0000' : '#000000';?>"> <?php echo tratasnv( $d_visit['doc_rg'] ) ?> </font> |
                        Foto 3x4: <font color="<?php echo empty( $d_visit['doc_foto34'] ) ? '#FF0000' : '#000000';?>"> <?php echo tratasnv( $d_visit['doc_foto34'] ) ?> </font> |
                        Comp. resid.: <font color="<?php echo empty( $d_visit['doc_resid'] ) ? '#FF0000' : '#000000';?>"> <?php echo tratasnv( $d_visit['doc_resid'] ) ?> </font> |
                        Ant. criminais: <font color="<?php echo empty( $d_visit['doc_ant'] ) ? '#FF0000' : '#000000';?>"> <?php echo tratasnv( $d_visit['doc_ant'] ) ?> </font> |
                        Certidão nascimento/casamento: <font color="<?php echo empty ( $d_visit['doc_cert'] ) ? '#FF0000' : '#000000';?>"> <?php echo tratasnv( $d_visit['doc_cert'] ) ?> </font>
                    </td>
                </tr>
                <tr>
                    <td class="mid_destaque">CADASTRAMENTO</td>
                    <td class="mid_destaque" colspan="2">ÚLTIMA ATUALIZAÇÃO</td>
                </tr>
                <tr>
                    <td class="mid_user"><?php echo $user_add ?></td>
                    <td class="mid_user" colspan="2"><?php echo $user_up ?></td>
                </tr>
            </table>

            <?php if ( $pode_entrar and $reg_entrada ) {?>
            <p>&nbsp;</p>
            <form action="<?php echo SICOP_ABS_PATH; ?>send/sendvisitin.php" method="post" name="visit_in" id="visit_in" >

                <?php if ( $jumbo ) {?>
                <p align="center"><input name="jumbo" type="checkbox" id="jumbo" value="1"/> Com jumbo</p>
                <?php } else {?>
                <p align="center"><img src="<?php echo $img_sys_path; ?>s_attention.png" alt="Atenção" class="icon_alert" /> Já entrou um visitante com jumbo para <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>!</p>
                <?php } ?>

                <input name="idvisit[]" type="hidden" id="idvisit" value="<?php echo $d_visit['idvisita'] ?>" />
                <input name="proced" type="hidden" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" value="Registrar entrada" />

                    <span id="bmenor"></span>

                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">
            //<!--
                $(function(){

                    var cont       = $("span#bmenor");
                    var bt_comum   = '<input class="form_bt" name="menor" type="button" onclick="javascript: location.href=\'entr_menor.php?idvisit=<?php echo $d_visit['idvisita'];?>\';" value="Entrada com menor" />';
                    var bt_jumbo   = '<input class="form_bt" name="menor" type="button" onclick="javascript: location.href=\'entr_menor.php?idvisit=<?php echo $d_visit['idvisita'];?>&jumbo=1\';" value="Entrada com menor" />';

                    cont.html( bt_comum );

                    $("input#jumbo").live( "click", function(){

                        if ( $(this).attr('checked') ) {
                            cont.html( bt_jumbo );
                        } else {
                            cont.html( bt_comum );
                        }

                    });

                });

            //-->
            </script>

            <?php } else if ( !$pode_entrar and $reg_entrada ) {?>

            <p class="visit_not_in">ESTE VISITANTE NÃO PODE ENTRAR NA UNIDADE</p>
            <p class="visit_not_in">MOTIVO:</p>
            <div style="width:500px; margin:auto;">
                <p align="center" class="visit_not_in"><?php echo $motivo ?></p>
            </div>

            <?php } ?>

            <?php if ( $pode_sair and $reg_saida ) {?>

            <form action="<?php echo SICOP_ABS_PATH; ?>send/sendvisitin.php" method="post" name="visit_in" id="visit_in" >

                <?php
                    $q_reg = "SELECT
                                `visita_mov`.`cod_visita`,
                                `visitas`.`cod_detento`,
                                `visitas`.`nome_visit`,
                                `visitas`.`sexo_visit`,
                                `visitas`.`nasc_visit`,
                                DATE_FORMAT(`visitas`.`nasc_visit`, '%d/%m/%Y') AS nasc_visit_f,
                                FLOOR( DATEDIFF( CURDATE(), `visitas`.`nasc_visit` ) / 365.25) AS idade_visit,
                                `tipoparentesco`.`parentesco`
                              FROM
                                `visita_mov`
                                INNER JOIN `visitas` ON `visita_mov`.`cod_visita` = `visitas`.`idvisita`
                                INNER JOIN `tipoparentesco` ON `visitas`.`cod_parentesco` = `tipoparentesco`.`idparentesco`
                              WHERE
                                `visita_mov`.`num_seq` = $num_seq_out
                                AND
                                DATE( `visita_mov`.`data_in` ) = DATE( NOW() )
                                AND
                                `visita_mov`.`cod_visita` != $idv
                              ORDER BY
                                `idade_visit` DESC";

                    //echo nl2br( $q_reg );
                    //exit;

                    $q_reg = mysql_query( $q_reg );

                    $motivo_pag = 'REGISTRO DE SAÍDA DE VISITANTES';
                    if ( !$q_reg ) {

                        // pegar a mensagem de erro mysql
                        $msg_err_mysql = get_err_mysql();

                        // montar a mensagem q será salva no log
                        $msg = array( );
                        $msg['tipo'] = 'err';
                        $msg['text'] = "Falha na consulta ( $motivo_pag ).\n\n $msg_err_mysql";
                        $msg['linha'] = __LINE__;
                        get_msg( $msg, 1 );

                        echo msg_js( 'FALHA!', 1 );
                        exit;

                    }

                    $cont = mysql_num_rows( $q_reg );
                    if ( $cont >= 1 ) {

                ?>

                <p align="center">Menores que estão com este visitante:</p>

                <table width="660" align="center" class="space">
                    <tr>
                        <td align="center">NOME</td>
                        <td align="center">NASCIMENTO</td>
                        <td align="center">PARENTESCO</td>
                        <td align="center">SEXO</td>
                        <td class="oculta"></td>
                    </tr>
                    <?php while ( $d_out = mysql_fetch_assoc( $q_reg ) ) { ?>
                    <tr>
                        <td width="325"><?php echo $d_out['nome_visit']; ?></td>
                        <td width="155" align="center"><?php echo empty( $d_out['nasc_visit_f'] ) ? '' : $d_out['nasc_visit_f'] . ' - ' . $d_out['idade_visit'] . ' anos'; // echo pegaIdade($d_visit['data_nasc'])   ?></td>
                        <td width="110" align="center"><?php echo $d_out['parentesco'] ?></td>
                        <td width="45" align="center"><?php echo $d_out['sexo_visit'] ?></td>
                        <td class="oculta"><input type="hidden" name="idvisit[]" value="<?php echo $d_out['idvisita'];?>" /></td>
                    </tr>

                    <?php }// fim do while ?>

                </table>
                <?php }// fim do if ( $cont >= 1 ) ?>

                <br/>

                <input name="idvisit[]" type="hidden" value="<?php echo $d_visit['idvisita'] ?>" />
                <input name="num_seq" type="hidden" id="num_seq" value="<?php echo $num_seq_out; ?>" />
                <input name="proced" type="hidden" id="proced" value="1" />

                <div class="form_bts">
                    <input class="form_bt" name="reg_out" type="submit" value="Registrar saída" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>


            <?php } else if ( !$pode_sair and $reg_saida ) {?>

            <p align="center" class="visit_not_in">VOCÊ NÃO PODE REGISTRAR A SAIDA DESTE VISITANTE</p>
            <p align="center" class="visit_not_in">MOTIVO:</p>
            <div style="width:500px; margin:auto;">
                <p align="center" class="visit_not_in"><?php echo $motivo ?></p>
            </div>

            <?php } ?>

            <div class="linha">
                HISTÓRICO DE VISTITAS
                <hr />
            </div>
            <?php
            $query_mov_visit = mysql_query( $query_mov_visit );
            $contmv = mysql_num_rows( $query_mov_visit );
            if( $contmv < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Nada consta.</p>';
            } else {
                ?>
            <table class="lista_busca">
                <tr >
                    <th class="hist_visit">DATA</th>
                    <th class="hist_visit">ENTRADA</th>
                    <th class="hist_visit">USUÁRIO</th>
                    <th class="hist_visit">SEQUÊNCIA</th>
                    <th class="hist_visit">JUMBO</th>
                    <th class="hist_visit">SAIDA</th>
                    <th class="hist_visit">USUÁRIO</th>
                </tr>
                <?php while( $d_mov_v = mysql_fetch_assoc( $query_mov_visit ) ) { ?>
                <tr class="even" >
                    <td class="hist_visit"><?php echo $d_mov_v['data_in_f'] ?></td>
                    <td class="hist_visit"><?php echo $d_mov_v['hora_in'] ?></td>
                    <td class="hist_visit"><?php echo $d_mov_v['user_in'] ?></td>
                    <td class="hist_visit"><?php echo $d_mov_v['num_seq'] ?></td>
                    <td class="hist_visit"><?php if ( !empty( $d_mov_v['jumbo'] ) ) { ?><img src="<?php echo $img_sys_path; ?>s_add.png" alt="Sim" class="icon_button" /><?php } ?></td>
                    <td class="hist_visit"><?php echo $d_mov_v['hora_out'] ?></td>
                    <td class="hist_visit"><?php echo $d_mov_v['user_out'] ?></td>
                </tr>
                <tr class="even">
                    <td class="hist_visit" colspan="7">adfadsf</td>
                </tr>
                <tr>
                    <td class="hist_visit" colspan="7"></td>
                </tr>
                <?php } // fim do while ?>
            </table>
            <?php } // fim do if que conta o número de ocorrencias ?>

            <div id="susp"></div>

            <div class="linha">
                HISTÓRICO DE SUSPENSÕES<?php if ( $n_rol >= 3 ) {  ?> - <a href="suspvisit.php?idvisit=<?php echo $d_visit['idvisita'] ?>" title="Cadastrar suspenção para este visitante">Cadastrar suspensão</a><?php }; ?>
                <hr />
            </div>
            <?php
            if( $contsusplista < 1 ) {
                echo '<p class="p_q_no_result">Nada consta.</p>';
            } else {
                ?>
            <table class="lista_busca">
                <tr >
                    <th class="susp_visit_tipo">TIPO</th>
                    <th class="desc_data">A PARTIR DE</th>
                    <th class="susp_visit_periodo">PERÍODO</th>
                    <th class="desc_data">ATÉ</th>
                    <th class="susp_visit_mot">MOTIVO</th>
                    <th class="tb_bt">&nbsp;</th>
                    <th class="tb_bt">&nbsp;</th>
                </tr>
                    <?php
                    while( $d_susp = mysql_fetch_assoc( $query_susp_lista ) ) {

                        $susp = manipula_sit_visia_c( $d_susp );

                        $class_css = 'susp_ativa';

                        if ( $d_susp['revog'] == 1 ){
                            $class_css = 'susp_revog';
                        }

                        ?>
                <tr class="even">
                    <td class="susp_visit_tipo <?php echo $class_css; ?>"><?php echo $susp['tipo'] ?></td>
                    <td class="desc_data <?php echo $class_css; ?>"><?php echo $d_susp['data_inicio_f'] ?></td>
                    <td class="susp_visit_periodo <?php echo $class_css; ?>"><?php echo $d_susp['periodo'] ?></td>
                    <td class="desc_data <?php echo $class_css; ?>"><?php echo $d_susp['data_fim_f'] ?></td>
                    <td class="susp_visit_mot"><?php echo nl2br( $d_susp['motivo'] ); ?></td>
                    <td class="tb_bt"><?php if ( $n_rol >= 3 ) {  ?> <a href="editsuspvisit.php?idsusp=<?php echo $d_susp['id_visit_susp']; ?>" title="Alterar esta suspenção" ><img src="<?php echo $img_sys_path; ?>b_edit.png" alt="Alterar esta suspenção" /></a> <?php }; ?> </td>
                    <td class="tb_bt"><?php if ( $n_rol >= 4 ) {  ?> <a href='javascript:void(0)' onclick='drop_susp_visit(<?php echo $idvisit; ?>, <?php echo $d_susp['id_visit_susp']; ?>)' title="Excluir esta suspenção" ><img src="<?php echo $img_sys_path; ?>b_drop.png" alt="Excluir esta suspenção" class="icon_button" /></a> <?php }; ?> </td>
                </tr>
                <tr>
                    <td colspan="7" class="desc_user">Cadastrado em <?php echo $d_susp['data_add_f'] ?>, usuário <?php echo $d_susp['user_add'] ?><?php if ($d_susp['user_up'] and $d_susp['data_up_f']) {?> - Atualizado em <?php echo $d_susp['data_up_f'] ?>, usuário <?php echo $d_susp['user_up'] ?> <?php }?></td>
                </tr>
                <?php
                    }
                }
                ?>
            </table>

            <div id="obs"></div>

            <div class="linha">
                OBSERVAÇÕES<?php if ( $n_rol >= 3 ) {  ?> - <a href="cadobsvisit.php?idvisit=<?php echo $d_visit['idvisita'] ?>" title="Adicionar uma observação para este visitante">Adicionar observa&ccedil;&atilde;o</a> <a href="#" title="Abrir em outra janela" onClick="javascript: ow('cadobsvisit.php?idvisit=<?php echo $d_visit['idvisita']; ?>&targ=1', '600', '320'); return false" ><img src="<?php echo $img_sys_path; ?>user_popup.png" alt="Abrir em outra janela" class="icon_popup" /></a><?php }; ?>
                <hr />
            </div>
            <?php
            if( $cont_obs < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não há observações.</p>';
            } else {
                ?>
            <table class="lista_busca">
                <tr >
                    <th class="desc_data">DATA</th>
                    <th class="desc_obs">OBSERVAÇÃO</th>
                    <th class="tb_bt">&nbsp;</th>
                    <th class="tb_bt">&nbsp;</th>
                </tr>
                    <?php
                    while($dados_obs = mysql_fetch_assoc($query_obs)) {
                        ?>
                <tr class="even">
                    <td class="desc_data"><?php echo $dados_obs['data_add_f'] ?></td>
                    <td class="desc_obs"><?php echo nl2br($dados_obs['obs_visit']) ?></td>
                    <td class="tb_bt"><?php if ($n_rol >= 3) {  ?><a href="editobsvisit.php?idobs=<?php echo $dados_obs['id_obs_visit']; ?>" title="Alterar esta observação" ><img src="<?php echo $img_sys_path; ?>b_edit.png" alt="" /></a><?php }; ?></td>
                    <td class="tb_bt">
                    <?php if ($n_rol >= 4) {  ?>
                        <a href='javascript:void(0)' onclick='drop( "id_obs_visit", "<?php echo $dados_obs['id_obs_visit']; ?>", "sendvisitobs", "drop_obs_visit", "2" )' title="Excluir esta observação"><img src="<?php echo $img_sys_path; ?>b_drop.png" alt="Excluir esta observação" class="icon_button" /></a>
                    <?php }; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="desc_user"><?php echo !empty( $dados_obs['destacar'] ) ? 'Observação destacada - ' : '';?>Cadastrado em <?php echo $dados_obs['data_add_fc'] ?>, usuário <?php echo $dados_obs['user_add'] ?><?php if ( $dados_obs['user_up'] and $dados_obs['data_up_f'] ) {?> - Atualizado em <?php echo $dados_obs['data_up_f'] ?>, usuário <?php echo $dados_obs['user_up'] ?> <?php }?></td>
                </tr>
                <?php
                    }
                }
                ?>
            </table>

<?php include 'footer.php'; ?>
