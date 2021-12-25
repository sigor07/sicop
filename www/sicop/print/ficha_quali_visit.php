<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';

$pag = link_pag();
$tipo = '';
$motivo_pag = 'IMPRESSÃO DE FICHA QUALIFICATIVA DE VISITANTE';

$imp_rol = get_session ( 'imp_rol', 'int' );
$n_rol_n = 1;

if ( $imp_rol < $n_rol_n ) {

    require 'cab_simp.php';
    $tipo = 3;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso à página SEM PERMISSÕES ( $motivo_pag ).";
    get_msg( $msg, 1 );

    exit;

}

$is_post = is_post();
if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );

    exit;

}

extract( $_POST, EXTR_OVERWRITE );

$idvisita = empty( $idvisita ) ? '' : (int)$idvisita;
//$idvisita = get_get( 'idvisita', 'int' );
if ( empty( $idvisita ) ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo']  = 'err';
    $msg['text']  = "Identificador do visitante em branco ou inválido ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 'f');

    exit;

}

$querydet = "SELECT
               `detentos`.`iddetento`,
               `detentos`.`nome_det`,
               `detentos`.`matricula`,
               `detentos`.`rg_civil`,
               `detentos`.`execucao`,
               `detentos`.`vulgo`,
               `detentos`.`nasc_det`,
               DATE_FORMAT ( `detentos`.`nasc_det`, '%d/%m/%Y' ) AS `nasc_det`,
               `FLOOR` ( DATEDIFF ( CURDATE(), `detentos`.`nasc_det` ) / 365.25 ) AS `idade_det`,
               `detentos`.`pai_det`,
               `detentos`.`mae_det`,
               `cidades`.`nome` AS `cidade`,
               `estados`.`sigla` AS `estado`,
               `cela`.`cela`,
               `raio`.`raio`,
               `det_fotos`.`foto_det_g`,
               `det_fotos`.`foto_det_p`
             FROM
               `detentos`
               LEFT JOIN `cidades` ON `detentos`.`cod_cidade` = `cidades`.`idcidade`
               LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado`
               LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
               LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
               LEFT JOIN `det_fotos` ON `det_fotos`.`id_foto` = `detentos`.`cod_foto`
             WHERE
               `detentos`.`iddetento` = ( SELECT `visitas`.`cod_detento` FROM `visitas` WHERE `visitas`.`idvisita` = $idvisita LIMIT 1 )
             LIMIT 1";

$queryvis = "SELECT
               `visitas`.`idvisita`,
               `visitas`.`cod_detento`,
               `visitas`.`num_in`,
               `visitas`.`nome_visit`,
               `visitas`.`rg_visit`,
               `visitas`.`sexo_visit`,
               `visitas`.`nasc_visit`,
               DATE_FORMAT ( `visitas`.`nasc_visit`, '%d/%m/%Y' ) AS `nasc_visit_f`,
               FLOOR ( DATEDIFF ( CURDATE(), `visitas`.`nasc_visit` ) / 365.25 ) AS `idade_visit`,
               `tipoparentesco`.`parentesco`,
               `visitas`.`resid_visit`,
               `visitas`.`telefone_visit`,
               `visitas`.`pai_visit`,
               `visitas`.`mae_visit`,
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
               DATE_FORMAT ( `visitas`.`data_add`, '%d/%m/%Y às %H:%i' ) AS `data_add`,
               `visitas`.`user_up`,
               DATE_FORMAT ( `visitas`.`data_up`, '%d/%m/%Y às %H:%i' ) AS `data_up`,
               `cidades`.`nome` AS `cidade_visit`,
               `estados`.`sigla` AS `estado_visit`,
               `visita_fotos`.`foto_visit_g`,
               `visita_fotos`.`foto_visit_p`
             FROM
               `visitas`
               LEFT JOIN `tipoparentesco` ON `visitas`.`cod_parentesco` = `tipoparentesco`.`idparentesco`
               LEFT JOIN `cidades` ON `visitas`.`cod_cidade_v` = `cidades`.`idcidade`
               LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado`
               LEFT JOIN `visita_fotos` ON `visita_fotos`.`id_foto` = `visitas`.`cod_foto`
             WHERE
               `visitas`.`idvisita` = $idvisita
             LIMIT 1";


// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$queryvis = $model->query( $queryvis );

// fechando a conexao
$model->closeConnection();

if ( !$queryvis ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( $motivo_pag - VISITANTE ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 'f' );
    exit;

}

$contv = $queryvis->num_rows;
if ( $contv < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "A consulta retornou 0 ocorrências ( $motivo_pag - VISITANTE ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 'f' );
    exit;

}

$d_visit = $queryvis->fetch_assoc();

$foto_g = $d_visit['foto_visit_g'];
$foto_p = $d_visit['foto_visit_p'];

$foto_visit = ck_pic( $foto_g, $foto_p, false, 2 );

$suspenso      = false;
$visit_class   = 'visit_ativa';
$sit_v_atual   = 'ATIVA';
$susp_data_ini = '';
$susp_data_fim = '';
$susp_motivo   = '';

$visit = manipula_sit_visia_cq( $idvisita );

if ( $visit ) {

    $suspenso      = $visit['suspenso'];
    $visit_class   = $visit['css_class'];
    $sit_v_atual   = $visit['sit_v'];
    $susp_data_ini = $visit['data_ini'];
    $susp_data_fim = $visit['data_fim'];
    $susp_motivo   = $visit['motivo'];

}

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$querydet = $model->query( $querydet );

// fechando a conexao
$model->closeConnection();

if ( !$querydet ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( $motivo_pag - DETENTO).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 'f' );
    exit;

}

$contd = $querydet->num_rows;

if( $contd < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "A consulta retornou 0 ocorrências ( $motivo_pag - DETENTO ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 'f' );
    exit;

}

$d_det = $querydet->fetch_assoc();
$iddet = $d_det['iddetento'];

$foto_g   = $d_det['foto_det_g'];
$foto_p   = $d_det['foto_det_p'];

$foto_det = ck_pic( $foto_g, $foto_p, false, 1 );

// pegar os dados do visitante
$visitante = dados_visit( $idvisita );

// pegar os dados do preso
$detento = dados_det( $iddet );

// montar a mensagem q será salva no log
$msg = array();
$msg['tipo'] = 'desc';
$msg['entre_ch'] = $motivo_pag;
$msg['text'] = "Impressão da ficha qualificativa de visitante. \n\n $visitante \n\n $detento";

get_msg( $msg, 1 );

$titulo        = get_session ( 'titulo' );
$secretaria    = get_session ( 'secretaria' );
$coordenadoria = get_session ( 'coordenadoria' );
$unidadecurto  = get_session ( 'unidadecurto' );
$endereco      = get_session ( 'endereco' );

$iduser        = get_session ( 'user_id' );
$ip            = $_SERVER['REMOTE_ADDR'];
$maquina       = substr( $ip, strrpos( $ip, '.' ) + 1 );

?>

<?php require 'cab_print.php'; ?>
<?php require 'cabecalho_v.php'; ?>

        <div class="corpo_quali_visit">
            <p align="center" class="par_extra_forte">FICHA DE IDENTIFICAÇÃO DE VISITANTE</p>

            <p align="center" class="par_min">&nbsp;</p>

            <p align="center" class="par_forte_visit">VISITANTE</p>

            <table class="quali_visita">
                <tr >
                    <td class="td_visit_med"><span class="destaque_leg">Visitante:</span> <?php echo $d_visit['nome_visit'] ?></td>
                    <td class="td_visit_min"><span class="destaque_leg">RG:</span> <?php echo $d_visit['rg_visit'] ?></td>
                    <td class="td_visit_foto" rowspan="8"><img src="<?php echo $foto_visit ?>" alt="" width="130" height="172" /></td>
                </tr>
                <tr>
                    <td class="td_visit_med"><span class="destaque_leg">Data de Nascimento:</span> <?php echo empty( $d_visit['nasc_visit_f'] ) ? '' : $d_visit['nasc_visit_f'] . ' - ' . $d_visit['idade_visit'] . ' anos'; // echo pegaIdade($d_visit['data_nasc'])    ?></td>
                    <td class="td_visit_min"><span class="destaque_leg">Sexo:</span> <?php echo $d_visit['sexo_visit'] ?></td>
                </tr>
                <tr>
                    <td class="td_visit_med"><span class="destaque_leg">Parentesco:</span> <?php echo $d_visit['parentesco'] ?></td>
                    <td class="td_visit_min"><span class="destaque_leg">ID no sistema:</span> <?php echo $d_visit['idvisita'] ?></td>
                </tr>
                <tr>
                    <td class="td_visit_med"><span class="destaque_leg">Telefone:</span> <?php echo preg_replace( '/([0-9]{2})([0-9]{4})([0-9]{4})/', '(\\1) \\2-\\3', $d_visit['telefone_visit'] ) ?></td>
                    <td class="td_visit_min">&nbsp;</td>
                </tr>
                <tr>
                    <td class="td_visit_med"><span class="destaque_leg">Endereço:</span> <?php echo $d_visit['resid_visit'] ?></td>
                    <td class="td_visit_min">&nbsp;</td>
                </tr>

                <tr>
                    <td class="td_visit_med"><span class="destaque_leg">Naturalidade:</span> <?php echo $d_visit['cidade_visit'] ?> - <?php echo $d_visit['estado_visit'] ?></td>
                    <td class="td_visit_min">&nbsp;</td>
                </tr>
                <tr>
                    <td class="td_visit_med"><span class="destaque_leg">Pai:</span> <?php echo $d_visit['pai_visit'] ?></td>
                    <td class="td_visit_min"></td>
                </tr>
                <tr>
                    <td class="td_visit_med"><span class="destaque_leg">Mãe:</span> <?php echo $d_visit['mae_visit'] ?></td>
                    <td class="td_visit_min">&nbsp;</td>
                </tr>
                <tr>
                    <td class="td_visit_med"><span class="destaque_leg">Defeito(s) físico(s):</span> <?php echo $d_visit['defeito_fisico'] ?></td>
                    <td class="td_visit_med" colspan="2"><span class="destaque_leg">Sinal(is) de nascimento:</span> <?php echo $d_visit['sinal_nasc'] ?></td>
                </tr>
                <tr>
                    <td class="td_visit_med"><span class="destaque_leg">Cicatriz(es):</span> <?php echo $d_visit['cicatrizes'] ?></td>
                    <td class="td_visit_med" colspan="2"><span class="destaque_leg">Tatuagem(ns):</span> <?php echo $d_visit['tatuagens'] ?></td>
                </tr>
                <tr>
                    <td class="td_visit_med"><span class="destaque_leg">Situação atual do visitante:</span> <font class="par_forte_visit <?php echo $visit_class; ?>" ><?php echo $sit_v_atual; ?></font></td>
                    <td class="td_visit_min" align="center" ><?php if ( $suspenso ) { ?> A partir de <?php echo $susp_data_ini ?> <?php } ?></td>
                    <td class="td_visit_foto" align="center" ><?php if ( !empty( $susp_data_fim ) ) { ?> Até <?php echo $susp_data_fim ?> <?php } ?></td>
                </tr>
                <tr>
                    <td class="td_visit_grd" colspan="3" ><?php if ( $suspenso ) { ?><span class="destaque_leg">Motivo:</span> <?php echo $susp_motivo ?><?php } ?></td>
                </tr>
            </table><!-- /table class="quali_visita" -->

            <p align="center" class="par_min">&nbsp;</p>

            <p align="center" class="par_forte_visit">DETENTO</p>

            <table class="quali_det_foto">
                <tr >
                    <td class="td_det_med"><span class="destaque_leg"><?php echo SICOP_DET_DESC_FU; ?>:</span> <?php echo $d_det['nome_det']; ?></td>
                    <td class="td_det_min"><span class="destaque_leg">Matrícula:</span> <?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] ); ?></td>
                    <td class="td_det_foto" rowspan="6"><img src="<?php echo $foto_det ?>" alt="" width="100" height="134" /></td>
                </tr>
                <tr>
                    <td class="td_det_med"><span class="destaque_leg">Vulgo(s):</span> <?php echo $d_det['vulgo'] ?></td>
                    <td class="td_det_min"><span class="destaque_leg">Execução:</span> <?php echo!empty( $d_det['execucao'] ) ? number_format( $d_det['execucao'], 0, '', '.' ) : 'N/C' ?></td>
                </tr>
                <tr >
                    <td class="td_det_med"><span class="destaque_leg">Data de Nascimento:</span> <?php echo empty( $d_det['nasc_det'] ) ? '' : $d_det['nasc_det'] . ' - ' . $d_det['idade_det'] . ' anos'; // echo pegaIdade($d_det['data_nasc'])   ?></td>
                    <td class="td_det_min"><span class="destaque_leg">RG Civil:</span> <?php if ( !empty( $d_det['rg_civil'] ) ) echo formata_num( $d_det['rg_civil'] ); ?></td>
                </tr>
                <tr >
                    <td class="td_det_med"><span class="destaque_leg">Cidade:</span> <?php echo $d_det['cidade'] . ' - ' . $d_det['estado'] ?></td>
                    <td class="td_det_min"><span class="destaque_leg">ID no sistema:</span> <?php echo $d_det['iddetento'] ?></td>
                </tr>
                <tr >
                    <td class="td_det_med"><span class="destaque_leg">Pai:</span> <?php echo $d_det['pai_det'] ?></td>
                    <td class="td_det_min">&nbsp;</td>
                </tr>
                <tr >
                    <td class="td_det_med"><span class="destaque_leg">Mãe:</span> <?php echo $d_det['mae_det'] ?></td>
                    <td class="td_det_min">&nbsp;</td>
                </tr>
            </table><!-- /table class="quali_det_foto" -->

            <p align="center" class="par_corpo">&nbsp;</p>

            <table class="plan_digital">
                <tr >
                    <td class="marq_pol">&nbsp;</td>
                    <td class="marq_pol">&nbsp;</td>
                    <td class="marq_pol">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_mao" rowspan="2"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>mao_d.png" alt="" width="20" height="108" /></td>
                </tr>
                <tr >
                    <td class="digital_pol" colspan="3">Polegar</td>
                    <td class="digital_dedos" colspan="3">Indicador</td>
                    <td class="digital_dedos" colspan="3">Médio</td>
                    <td class="digital_dedos" colspan="3">Anular</td>
                    <td class="digital_dedos" colspan="3">Mínimo</td>
                </tr>
                <tr >
                    <td class="marq_pol">&nbsp;</td>
                    <td class="marq_pol">&nbsp;</td>
                    <td class="marq_pol">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_dedos">&nbsp;</td>
                    <td class="marq_mao" rowspan="2"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>mao_e.png" alt="" width="23" height="124" /></td>
                </tr>
                <tr >
                    <td class="digital_pol" colspan="3">Polegar</td>
                    <td class="digital_dedos" colspan="3">Indicador</td>
                    <td class="digital_dedos" colspan="3">Médio</td>
                    <td class="digital_dedos" colspan="3">Anular</td>
                    <td class="digital_dedos" colspan="3">Mínimo</td>
                </tr>
            </table><!-- /table class="plan_digital" -->

            <span class="_Footer">
                <div class="rodape">
                    <p align="right" class="par_min">Usuário: <?php echo $iduser ?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' ) ?></p>
                    <hr align="center" width="645" size="0" noshade="noshade" color="#000000" />
                    <p align="center"><?php echo $endereco ?></p>
                </div>
            </span><!-- /span class="_Footer" -->

        </div>
    </body>
</html>