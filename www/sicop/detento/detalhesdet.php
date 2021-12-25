<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag     =   link_pag();
$tipo    = '';
$caminho = '';

$n_rol_n   = 2;
$n_sind_n  = 2;
$n_cad_n   = 2;
$n_pront_n = 2;
$n_pec_n   = 2;
$n_alt_n   = 1;
$n_foto_n  = 1;
$n_mov_n   = 1;
$n_rc_n    = 1;
$n_obs_n   = 1;
$n_alias_n = 1;

$n_rol          = get_session( 'n_rol', 'int' );
$n_sind         = get_session( 'n_sind', 'int' );
$n_cadastro     = get_session( 'n_cadastro', 'int' );
$n_chefia       = get_session( 'n_chefia', 'int' );
$n_pront        = get_session( 'n_pront', 'int' );
$n_peculio      = get_session( 'n_peculio', 'int' );
$n_det_alt      = get_session( 'n_det_alt', 'int' );
$n_det_alt_foto = get_session( 'n_det_alt_foto', 'int' );
$n_det_mov      = get_session( 'n_det_mov', 'int' );
$n_det_rc       = get_session( 'n_det_rc', 'int' );
$n_det_obs      = get_session( 'n_det_obs', 'int' );
$n_det_alias    = get_session( 'n_det_alias', 'int' );

$imp_incl   = get_session( 'imp_incl', 'int' );
$imp_chefia = get_session( 'imp_chefia', 'int' );
$imp_det    = get_session( 'imp_det', 'int' );

$motivo_pag = 'DETALHES D' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U;

/*$iddet = (int)alphaID($_GET['iddet'], true);*/

$iddet = get_get( 'iddet', 'int' );

if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ATEN );
    $msg->set_msg_pre_def( SM_DIRECT_ACCESS );
    $msg->add_parenteses( "IDENTIFICADOR EM BRANCO - $motivo_pag" );
    $msg->get_msg();

    echo msg_js( '', 1 );

    exit;

}

$querydet = "SELECT
               `detentos`.`iddetento`,
               `detentos`.`nome_det`,
               `detentos`.`matricula`,
               `detentos`.`rg_civil`,
               `detentos`.`execucao`,
               `detentos`.`cpf`,
               `detentos`.`vulgo`,
               `detentos`.`profissao`,
               `detentos`.`pai_det`,
               `detentos`.`mae_det`,
               `detentos`.`primario`,
               `detentos`.`prisoes_ant`,
               `detentos`.`fuga`,
               `detentos`.`local_fuga`,
               `detentos`.`estatura`,
               `detentos`.`peso`,
               `detentos`.`defeito_fisico`,
               `detentos`.`sinal_nasc`,
               `detentos`.`cicatrizes`,
               `detentos`.`tatuagens`,
               `detentos`.`resid_det`,
               `detentos`.`possui_adv`,
               `detentos`.`caso_emergencia`,
               `detentos`.`obs_artigos`,
               `detentos`.`data_quali`,
               `detentos`.`funcionario`,
               `detentos`.`monitorado`,
               `detentos`.`aut_visita`,
               `detentos`.`aut_sedex`,
               `detentos`.`dados_prov`,
               `detentos`.`user_add`,
               `detentos`.`data_add`,
               `detentos`.`user_up`,
               `detentos`.`data_up`,
               DATE_FORMAT(`detentos`.`nasc_det`, '%d/%m/%Y') AS nasc_det,
               FLOOR(DATEDIFF(CURDATE(), `detentos`.`nasc_det`) / 365.25) AS idade,
               DATE_FORMAT(`detentos`.`data_prisao`, '%d/%m/%Y') AS data_prisao,
               DATE_FORMAT(`detentos`.`data_add`, '%d/%m/%Y às %H:%i') AS data_add,
               DATE_FORMAT(`detentos`.`data_up`, '%d/%m/%Y às %H:%i') AS data_up,
               `tipoartigo`.`artigo`,
               `tipocutis`.`cutis`,
               `tipocabelos`.`cabelos`,
               `tipoescolaridade`.`escolaridade`,
               `tipoestadocivil`.`est_civil`,
               `tipoolhos`.`olhos`,
               `tiporeligiao`.`religiao`,
               `tiposituacaoprocessual`.`sit_proc`,
               `tiponacionalidade`.`nacionalidade`,
               `cidades`.`nome` AS cidade,
               `estados`.`sigla` AS estado,
               `mov_det_in`.`data_mov` AS data_incl,
               DATE_FORMAT(`mov_det_in`.`data_mov`, '%d/%m/%Y') AS data_incl_f,
               `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
               `mov_det_out`.`data_mov` AS data_excl,
               DATE_FORMAT(`mov_det_out`.`data_mov`, '%d/%m/%Y') AS data_excl_f,
               `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
               `unidades_in`.`unidades` AS procedencia,
               `unidades_out`.`unidades` AS destino,
               `unidades_out`.`idunidades` AS iddestino,
               `unidades_prisao`.`unidades` AS local_prisao,
               `cela`.`cela`,
               `raio`.`raio`,
               `det_fotos`.`foto_det_g`,
               `det_fotos`.`foto_det_p`
             FROM
               `detentos`
               LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
               LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
               LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
               LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
               LEFT JOIN `unidades` `unidades_prisao` ON `detentos`.`cod_local_prisao` = `unidades_prisao`.`idunidades`
               LEFT JOIN `tipocutis` ON `detentos`.`cod_cutis` = `tipocutis`.`idcutis`
               LEFT JOIN `tipocabelos` ON `detentos`.`cod_cabelos` = `tipocabelos`.`idcabelos`
               LEFT JOIN `tipoescolaridade` ON `detentos`.`cod_instrucao` = `tipoescolaridade`.`idescolaridade`
               LEFT JOIN `tipoestadocivil` ON `detentos`.`cod_est_civil` = `tipoestadocivil`.`idest_civil`
               LEFT JOIN `tipoolhos` ON `detentos`.`cod_olhos` = `tipoolhos`.`idolhos`
               LEFT JOIN `tiporeligiao` ON `detentos`.`cod_religiao` = `tiporeligiao`.`idreligiao`
               LEFT JOIN `tiposituacaoprocessual` ON `detentos`.`cod_sit_proc` = `tiposituacaoprocessual`.`idsit_proc`
               LEFT JOIN `cidades` ON `detentos`.`cod_cidade` = `cidades`.`idcidade`
               LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado`
               LEFT JOIN `tipoartigo` ON `detentos`.`cod_artigo` = `tipoartigo`.`idartigo`
               LEFT JOIN `tiponacionalidade` ON `detentos`.`cod_nacionalidade` = `tiponacionalidade`.`idnacionalidade`
               LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
               LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
               LEFT JOIN `det_fotos` ON `det_fotos`.`id_foto` = `detentos`.`cod_foto`
             WHERE
               `detentos`.`iddetento` = $iddet
             LIMIT 1";

$queryalias = "SELECT
                 `aliases`.`idalias`,
                 `aliases`.`cod_detento`,
                 `aliases`.`alias_det`,
                 `aliases`.`user_add`,
                 DATE_FORMAT(`aliases`.`data_add`, '%d/%m/%Y') AS data_add_f,
                 DATE_FORMAT(`aliases`.`data_add`, '%d/%m/%Y às %H:%i') AS data_add_fc,
                 `aliases`.`user_up`,
                 DATE_FORMAT(`aliases`.`data_up`, '%d/%m/%Y às %H:%i') AS data_up_f,
                 `tipoalias`.`tipoalias`
               FROM
                 `aliases`
                 INNER JOIN tipoalias ON `aliases`.`cod_tipoalias` = `tipoalias`.`idtipoalias`
               WHERE
                 `aliases`.`cod_detento` = $iddet
               ORDER BY
                 `tipoalias`.`tipoalias` ASC";

$query_mov = "SELECT
                `mov_det`.`id_mov`,
                `mov_det`.`cod_tipo_mov`,
                `mov_det`.`cod_local_mov`,
                `mov_det`.`data_mov`,
                DATE_FORMAT(`mov_det`.`data_mov`, '%d/%m/%Y') AS data_mov_f,
                `mov_det`.`user_add`,
                DATE_FORMAT(`mov_det`.`data_add`, '%d/%m/%Y às %H:%i') AS data_add_fc,
                `mov_det`.`user_up`,
                DATE_FORMAT(`mov_det`.`data_up`, '%d/%m/%Y às %H:%i') AS data_up_f,
                `tipomov`.`sigla_mov`,
                `tipomov`.`tipo_mov`,
                `unidades`.`unidades` AS local_mov
              FROM
                `mov_det`
                LEFT JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                LEFT JOIN `unidades` ON `mov_det`.`cod_local_mov` = `unidades`.`idunidades`
              WHERE
                `mov_det`.`cod_detento` = $iddet
              ORDER BY
                `mov_det`.`data_mov` DESC,
                `mov_det`.`data_add` DESC
              LIMIT 10";

$q_count_mov = "SELECT
                  COUNT( `cod_detento` ) AS `total_mov`
                FROM
                  `mov_det`
                WHERE
                  `cod_detento` = $iddet";

$query_rc = "SELECT
               `mov_rc_det`.`id_mov_rc`,
               `old_raio`.`raio` AS old_raio,
               `old_cela`.`cela` AS old_cela,
               `new_raio`.`raio` AS n_raio,
               `new_cela`.`cela` AS n_cela,
               `mov_rc_det`.`data_rc`,
               DATE_FORMAT(`mov_rc_det`.`data_rc`, '%d/%m/%Y') AS data_rc_f,
               `mov_rc_det`.`user_add`
             FROM
               `mov_rc_det`
               LEFT JOIN `cela` `old_cela` ON `mov_rc_det`.`cod_old_cela` = `old_cela`.`idcela`
               INNER JOIN `cela` `new_cela` ON `mov_rc_det`.`cod_n_cela` = `new_cela`.`idcela`
               LEFT JOIN `raio` `old_raio` ON `old_cela`.`cod_raio` = `old_raio`.`idraio`
               INNER JOIN `raio` `new_raio` ON `new_cela`.`cod_raio` = `new_raio`.`idraio`
             WHERE
               `mov_rc_det`.`cod_detento` = $iddet
             ORDER BY
               `mov_rc_det`.`data_rc` DESC, `mov_rc_det`.`data_add` DESC
             LIMIT 10";


$query_obs = "SELECT
                `id_obs_det`,
                `cod_detento`,
                `obs_det`,
                `user_add`,
                DATE_FORMAT( `data_add`, '%d/%m/%Y' ) AS data_add_f,
                DATE_FORMAT( `data_add`, '%d/%m/%Y às %H:%i' ) AS data_add_fc,
                `data_add`,
                `user_up`,
                DATE_FORMAT( `data_up`, '%d/%m/%Y às %H:%i' ) AS data_up_f,
                `data_up`
              FROM
                `obs_det`
              WHERE
                `cod_detento` = $iddet
              ORDER BY
                `data_add` DESC
              LIMIT 10";

$q_count_obs = "SELECT
                  COUNT( `cod_detento` ) AS `total_obs`
                FROM
                  `obs_det`
                WHERE
                  `cod_detento` = $iddet";

$db = SicopModel::getInstance();

$querydet = $db->query( $querydet );
if ( !$querydet ) {

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();
    $db->closeConnection();

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( $motivo_pag ). \n\n $msg_err_mysql";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$db->closeConnection(); // fecho a conexao

$contd = $querydet->num_rows;
if ( $contd < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "A consulta retornou 0 ocorrências ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$d_det = $querydet->fetch_assoc();

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$foto_g = $d_det['foto_det_g'];
$foto_p = $d_det['foto_det_p'];

$foto_det = ck_pic( $foto_g, $foto_p, false, 1 );

$pasta  = SICOP_DET_FOLDER;
$amplia = true;
if ( empty( $foto_g ) or !is_file( $pasta . $foto_g ) ) {
    $amplia = false;
}

$fontautv = '';
$fontautvf = '';
$fontauts = '';
$fontautsf = '';
// para mudar a cor do item que expecifica se o preso pode ou não receber visitas
if ( $d_det['aut_visita'] == 0) {

    $fontautv  = '<font color="#FF0000"><b>';
    $fontautvf = '</b></font>';

}

// para mudar a cor do item que expecifica se o preso pode ou não receber sedex
if ( $d_det['aut_sedex'] == 0) {

    $fontauts  = '<font color="#FF0000"><b>';
    $fontautsf = '</b></font>';

}

$tipo_mov_in  = $d_det['tipo_mov_in'];
$procedencia  = $d_det['procedencia'];
$data_incl    = $d_det['data_incl'];
$tipo_mov_out = $d_det['tipo_mov_out'];
$iddestino    = $d_det['iddestino'];
$destino      = $d_det['destino'];
$data_excl    = $d_det['data_excl'];

$det = manipula_sit_det_c( $tipo_mov_in, $procedencia, $data_incl, $tipo_mov_out, $iddestino, $destino, $data_excl );
$sit_det = manipula_sit_det_id( $tipo_mov_in, $tipo_mov_out, $iddestino );

$user_add = '';
$user_up = '';

if ( !empty( $d_det['user_add'] ) ) {
    $user_add = 'ID usuário: ' . $d_det['user_add'] . ', em ' . $d_det['data_add'];
};

if ( !empty( $d_det['user_up'] ) ) {
    $user_up = 'ID usuário: ' . $d_det['user_up'] . ', em ' . $d_det['data_up'];
};

$pena = cal_cond( $iddet );

$desc_pag = 'Detalhes d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L;

// adicionando o javascript
$cab_js = array();
$cab_js[] = 'ajax/ajax_det.js';
$cab_js[] = 'ajax/jq_print_det.js';
set_cab_js( $cab_js );


require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>

            <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet; ?>" />

            <p class="descript_page">DETALHES D<?php echo SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U; ?></p>

            <p class="link_det" >
                <?php if ( $n_det_alt >= $n_alt_n ) {  ?>
                <a href="editdet.php?iddet=<?php echo $d_det['iddetento'] ?>" title="Alterar dados d<?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>">Alterar dados</a>
                <?php }; ?>
                <?php echo ( $n_det_alt >= $n_alt_n and ( $n_det_alt_foto >= $n_foto_n and !empty( $d_det['matricula'] ) ) ) ? ' | ' : ''  ?>
                <?php if ($n_det_alt_foto >= $n_foto_n and !empty($d_det['matricula'] ) ) {  ?>
                <a id="alter_foto_det" href='javascript:void(0)' title="Alterar a foto d<?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>">Alterar foto</a>
                <?php }; ?>
                <?php echo ( $n_det_alt >= $n_alt_n or ( $n_det_alt_foto >= $n_foto_n and !empty( $d_det['matricula'] ) ) ) ? ' | ' : ''  ?>
                <a href="fotos_det.php?iddet=<?php echo $d_det['iddetento'] ?>" title="Ver as fotos d<?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>">Relação de fotos</a>
                <?php if ( $n_chefia >= 4 ) {  ?>
                | <a href='javascript:void(0)' onclick='drop( "iddet", "<?php echo $iddet; ?>", "senddetdel", "drop_det", "2")' title="Excluir <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?> do sistema">Excluir <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?></a>
                <?php }; ?>
            </p>

            <?php if ( $imp_incl >= 1 or $imp_det >=1 or $imp_chefia >=1 ) {?>
            <p class="link_det" >
                <a href="#" title="Impressões d<?php echo SICOP_DET_PRON_L; ?> <?php echo SICOP_DET_DESC_L; ?>" id="print_doc_det">Imprimir documentos</a>
                | <a href="#" title="Imprimir a foto d<?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>" onclick="javascript: ow('<?php echo $caminho; ?>print/foto_det.php?iddet=<?php echo $d_det['iddetento'] ?>', '600', '600'); return false" >Imprimir foto</a>
                <?php if ( $imp_chefia >=1 ) {?>
                | <a href="termo_seg.php?iddet=<?php echo $iddet; ?>" title="Imprimir os termos de seguro d<?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>" >Imprimir termos do seguro</a>
                <?php }; ?>
            </p>
            <?php }; ?>
            <p class="link_det">
                <a href="#obs" title="Ir para as observações">Observações</a> |
                <a href="#mov" title="Ir para o histórico de movimentações">Movimentações</a> |
                <a href="#rc" title="Ir para o histórico de mudanças de <?php echo mb_strtolower( SICOP_RAIO ) . '/' . mb_strtolower( SICOP_CELA ) ?>">Mudanças de <?php echo mb_strtolower( SICOP_RAIO ) . '/' . mb_strtolower( SICOP_CELA ) ?></a> |
                <a href="#alias" title="Ir para aliases">Aliases</a>
                <?php if ($n_rol >= $n_rol_n) { ?> | <a href="#rol" title="Ir para o rol de visitas">Rol de Visitas</a><?php }?>
                <?php if ($n_sind >= $n_sind_n) { ?> | <a href="#sind" title="Ir para a sindicância">Sindicâncias</a><?php }?>
                <?php if ($n_cadastro >= $n_cad_n) { ?> | <a href="#aud" title="Ir para as audiências">Audiências</a><?php }?>
                <?php if ($n_pront >= $n_pront_n) { ?> | <a href="<?php echo $caminho; ?>prontuario/detalgrade.php?iddet=<?php echo $d_det['iddetento'] ?>" title="Ver a grade processual d<?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>">Grade</a><?php }?>
                <?php if ($n_peculio >= $n_pec_n) { ?> | <a href="<?php echo $caminho; ?>peculio/detalpec.php?iddet=<?php echo $d_det['iddetento'] ?>" title="Ver os pertences e pecúlio d<?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>">Pecúlio</a><?php }?>
            </p>

            <table class="detal_det">
                <tr>
                    <td class="mid"><?php echo SICOP_DET_DESC_FU; ?>: <?php echo $d_det['nome_det'] ?></td>
                    <td class="mini">Matrícula: <?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] ) ?></td>
                    <td class="mini" rowspan="8" align="center">
                        <?php if ( $amplia ){ ?>
                        <a id="link_foto_det" href="<?php echo SICOP_DET_IMG_PATH . $foto_g ?>" title="<?php echo $d_det['nome_det']; if ( !empty( $d_det['matricula'] ) ) echo ' - ' . formata_num( $d_det['matricula'] ) ?>">
                        <?php }; ?>
                        <img src="<?php echo $foto_det ?>" alt="" class="foto_det" />
                        <?php if ( $amplia ){ ?></a><?php } ?>
                    </td>
                </tr>
                <tr>
                    <td class="mid">Artigo: <?php echo $d_det['artigo'] ?></td>
                    <td class="mini">RG Civil: <?php if ( !empty( $d_det['rg_civil'] ) ) echo formata_num( $d_det['rg_civil'] ) ?></td>
                </tr>
                <tr>
                    <td class="mid">Vulgo(s): <?php echo $d_det['vulgo'] ?></td>
                    <td class="mini">Execução: <?php echo !empty($d_det['execucao']) ? number_format($d_det['execucao'], 0, '', '.') : 'N/C' ?></td>
                </tr>
                <tr>
                    <td class="mid">Nacionalidade: <?php echo $d_det['nacionalidade'] ?></td>
                    <td class="mini">CPF: <?php echo !empty( $d_det['cpf'] ) ? formata_num( $d_det['cpf'], 2 ) : 'N/C'; ?></td>
                </tr>
                <tr>
                    <td class="mid">Data de Nascimento: <?php echo empty( $d_det['nasc_det'] ) ? '' : $d_det['nasc_det']. ' - ' .$d_det['idade'] . ' anos';// echo pegaIdade($d_det['data_nasc'])  ?></td>
                    <td class="mini">ID no sistema: <?php echo $d_det['iddetento'] ?></td>
                </tr>
                <tr>
                    <td class="mid">Cidade: <?php echo $d_det['cidade'] . ' - ' . $d_det['estado'] ?></td>
                    <td class="mini"><?php echo empty( $d_det['dados_prov'] ) ? '&nbsp;' : 'Dados provisórios na PRODESP'; ?></td>
                </tr>
                <tr>
                    <td class="mid">Pai: <?php echo $d_det['pai_det'] ?></td>
                    <td class="mini">&nbsp;</td>
                </tr>
                <tr>
                    <td class="mid">Mãe: <?php echo $d_det['mae_det'] ?></td>
                    <td class="mini">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3" class="quebra_table">MOVIMENTAÇÃO E LOCALIZAÇÃO</td>
                </tr>
                <tr>
                    <td class="mid">Situação atual: <b><span style="font-size: 12px;" class="<?php echo $det['css_class'];?>"><?php echo $det['sitat'] ?></span></b></td>
                    <td class="mini_rc"><span id="raio"><?php echo empty( $d_det['raio'] ) ? '' : SICOP_RAIO . ': ' . $d_det['raio']; ?></span></td>
                    <td class="mini_rc"><span id="cela"><?php echo empty( $d_det['cela'] ) ? '' : SICOP_CELA . ': ' . $d_det['cela']; ?></span></td>
                </tr>
                <tr>
                  <td class="mid"><?php echo $det['data_incl'] ?></td>
                  <td class="mid" colspan="2"><?php echo $det['data_excl'] ?></td>
                </tr>
                <tr>
                    <td class="mid"><?php echo $det['procedencia'] ?></td>
                    <td class="mid" colspan="2"><?php echo $det['destino'] ?></td>
                </tr>
                <tr>
                    <td class="great_links" colspan="3">
                        <?php if ( $sit_det != 15 ) {  ?>
                        <?php if ( $n_det_mov >= $n_mov_n ) {  ?>
                        <a href="cadmovdet.php?iddet=<?php echo $d_det['iddetento'] ?>">Nova movimentação</a> <a href="#" title="Abrir em outra janela" onclick="javascript: ow('cadmovdet.php?iddet=<?php echo $d_det['iddetento']; ?>&targ=1', '800', '560'); return false" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>user_popup.png" alt="Abrir em outra janela" class="icon_popup" /></a>
                        <?php }; ?>
                        <?php echo ( $n_det_mov >= $n_mov_n and $n_det_rc >= $n_rc_n ) ? '|' : '';?>
                        <?php if ( $n_det_rc >= $n_rc_n ) { ?>
                        <a class="link_add_rc" title="Alterar <?php echo mb_strtolower( SICOP_RAIO ) . '/' . mb_strtolower( SICOP_CELA ) ?>" href="javascript:void(0)">Mudar <?php echo mb_strtolower( SICOP_RAIO ) . '/' . mb_strtolower( SICOP_CELA ) ?></a>
                        <?php }; ?>
                        <?php }; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="quebra_table">COMPLEIÇÃO E SINAIS PARTICULARES</td>
                </tr>
                <tr>
                    <td class="mid">Cútis: <?php echo $d_det['cutis'] ?></td>
                    <td class="mid" colspan="2">Cabelos: <?php echo $d_det['cabelos'] ?></td>
                </tr>
                <tr>
                    <td class="mid">Olhos: <?php echo $d_det['olhos'] ?></td>
                    <td class="mini">Estatura: <?php echo !empty( $d_det['estatura'] ) ? preg_replace('/([0-9]{1})([0-9]{2})/','\\1,\\2',$d_det['estatura']) : ''; ?></td>
                    <td class="mini">Peso (kg): <?php echo $d_det['peso'] ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="great">Defeito(s) físico(s): <?php echo $d_det['defeito_fisico'] ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="great">Sinal(is) de nascimento: <?php echo $d_det['sinal_nasc'] ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="great">Cicatriz(es): <?php echo $d_det['cicatrizes'] ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="great">Tatuagem(ns): <?php echo $d_det['tatuagens'] ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="quebra_table">DADOS PRISIONAIS</td>
                </tr>
                <tr>
                    <td class="mid">Data da prisão: <?php echo $d_det['data_prisao'] ?></td>
                    <td class="mid" colspan="2">Primário: <?php echo tratasn($d_det['primario']) ?></td>
                </tr>
                 <tr>
                    <td class="great" colspan="3">Local da prisão: <?php echo $d_det['local_prisao'] ?></td>
                </tr>
                <tr>
                    <td class="mid">Condenado a: <?php echo $pena; ?></td>
                    <td class="mid" colspan="2">Situação Processual: <?php echo $d_det['sit_proc'] ?></td>
                </tr>
                <tr>
                    <td class="great" colspan="3">Prisões onde esteve recolhido: <?php echo $d_det['prisoes_ant'] ?></td>
                </tr>
                <tr>
                    <td class="great" colspan="3">Fuga(s): <?php echo empty($d_det['local_fuga']) || $d_det['fuga'] == '0' ? 'NADA CONSTA' : $d_det['local_fuga'] ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="quebra_table">OUTRAS INFORMAÇÕES</td>
                </tr>
                <tr>
                    <td class="mid">Estado civil: <?php echo $d_det['est_civil'] ?></td>
                    <td class="mid" colspan="2">Instrução: <?php echo $d_det['escolaridade'] ?></td>
                </tr>
                <tr>
                    <td class="mid">Religião: <?php echo $d_det['religiao'] ?></td>
                    <td class="mid" colspan="2">Possui advogado particular: <?php echo tratasn($d_det['possui_adv']) ?></td>
                </tr>
                <tr>
                    <td class="great" colspan="3">Profissão: <?php echo $d_det['profissao'] ?></td>
                </tr>
                <tr>
                    <td class="great" colspan="3">Última residência: <?php echo $d_det['resid_det'] ?></td>
                </tr>
                <tr>
                    <td class="great" colspan="3">Em caso de emergência, avisar: <?php echo $d_det['caso_emergencia'] ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="quebra_table">&nbsp;</td>
                </tr>
                <tr>
                    <td class="mid_destaque">CADASTRAMENTO</td>
                    <td class="mid_destaque" colspan="2">ÚLTIMA ATUALIZAÇÃO</td>
                </tr>
                <tr>
                    <td class="mid" align="center"><?php echo $user_add ?></td>
                    <td class="mid" colspan="2" align="center"><?php echo $user_up; ?></td>
                </tr>
            </table>

            <div id="obs"></div>

            <div class="linha">
                OBSERVAÇÕES
                <?php
                $cont_all_obs = 0;
                $cont_all_obs = $db->fetchOne( $q_count_obs );
                $db->closeConnection();
                if ( $cont_all_obs > 10 ) { ?>
                <br />
                <span id="span_all_obs">Mostrando as 10 últimas observações - <a id="all_obs" href="javascript:void(0)">Mostrar todas</a></span>
                <?php } ?>
                <?php if ( $n_det_obs >= $n_obs_n ) {  ?> - <a id="link_add_obs" href="javascript:void(0)" title="Adicionar uma observação para <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>">Adicionar observação</a><?php }; ?>
                <hr />
            </div>

            <div id="table_obs">
            <?php

            $query_obs = $db->query( $query_obs );
            $db->closeConnection();
            $cont_obs  = $query_obs->num_rows;
            if ( $cont_obs < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
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
                    <?php while ( $dados_obs = $query_obs->fetch_assoc() ) { ?>
                    <tr class="even">
                        <td class="desc_data"><?php echo $dados_obs['data_add_f'] ?></td>
                        <td class="desc_obs"><?php echo nl2br( $dados_obs['obs_det'] ) ?></td>
                        <td class="tb_bt">
                        <?php if ( $n_det_obs >= $n_obs_n ) {  ?>
                            <input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" name="edit_obs_det[]" value="<?php echo $dados_obs['id_obs_det'] ;?>" title="Alterar observação" />
                        <?php }; ?>
                        </td>
                        <td class="tb_bt">
                        <?php if ( $n_chefia >= 4 ) {  ?>
                            <input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" name="del_obs_det[]" value="<?php echo $dados_obs['id_obs_det'] ;?>" title="Excluir observação" />
                        <?php }; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="desc_user">Cadastrado em <?php echo $dados_obs['data_add_fc'] ?>, usuário <?php echo $dados_obs['user_add'] ?><?php if ($dados_obs['user_up'] and $dados_obs['data_up_f']) {?> - Atualizado em <?php echo $dados_obs['data_up_f'] ?>, usuário <?php echo $dados_obs['user_up'] ?> <?php }?></td>
                    </tr>
                    <?php } // fim do while ?>
                </table>
            <?php } // fim do if que conta o número de ocorrencias ?>
            </div><!-- /div id="table_obs" -->

            <div id="mov"></div>

            <div class="linha">
                HISTÓRICO DE MOVIMENTAÇÃO
                <?php
                $cont_all_mov = 0;
                $cont_all_mov = $db->fetchOne( $q_count_mov );
                $db->closeConnection();
                if ( $cont_all_mov > 10 ) { ?>
                <br />
                <span id="span_all_mov">Mostrando as 10 últimas movimentações - <a id="all_mov" href="javascript:void(0)">Mostrar todas</a></span>
                <?php } ?>
                <?php if ( $sit_det != 15 ) {  ?>
                    <?php if ( $n_det_mov >= $n_mov_n ) {  ?>
                - <a href="cadmovdet.php?iddet=<?php echo $d_det['iddetento']; ?>" title="Cadastrar uma nova movimentação">Nova movimentação</a> <a href="#" title="Abrir em outra janela" onClick="javascript: ow('cadmovdet.php?iddet=<?php echo $d_det['iddetento']; ?>&targ=1', '800', '560'); return false" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>user_popup.png" alt="Abrir em outra janela" class="icon_popup" /></a>
                    <?php }; ?>
                <?php }; ?>
                <?php if ( $n_cadastro >= 3 ) {  ?>
                - <a href="mov_acervo.php?iddet=<?php echo $d_det['iddetento']; ?>" title="Cadastrar movimentação no acervo">Cadastrar acervo</a> <a href="#" title="Abrir em outra janela" onClick="javascript: ow('mov_acervo.php?iddet=<?php echo $d_det['iddetento']; ?>&targ=1', '800', '560'); return false" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>user_popup.png" alt="Abrir em outra janela" class="icon_popup" /></a>
                <?php }; ?>
                <hr />
            </div>
            <?php
            $query_mov = $db->query( $query_mov );
            $db->closeConnection();
            $cont_mov  = $query_mov->num_rows;
            if ( $cont_mov < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não há movimentações.</p>';
            } else {
                ?>
            <div id="table_mov">
                <table class="lista_busca">
                    <tr >
                        <th class="desc_data">DATA</th>
                        <th class="tipo_mov">TIPO DE MOVIEMTAÇÃO</th>
                        <th class="local_hist_mov">LOCAL</th>
                        <th class="tb_bt">&nbsp;</th>
                        <th class="tb_bt">&nbsp;</th>
                    </tr>
                        <?php
                        $i = 0;
                        while ( $dados_mov = $query_mov->fetch_assoc() ) {
                            ++$i;
                            ?>
                    <tr class="even">
                        <td class="desc_data"><?php echo $dados_mov['data_mov_f'] ?></td>
                        <td class="tipo_mov"><?php echo $dados_mov['sigla_mov']. ' - ' .$dados_mov['tipo_mov'] ?></td>
                        <td class="local_hist_mov"><?php echo $dados_mov['local_mov'] ?></td>
                        <td class="tb_bt">
                        <?php if ( $n_chefia >= 3 ) {  ?>
                          <?php if ( $i == 1 ) {  ?>
                            <a href="edit_mov_det.php?idmov=<?php echo $dados_mov['id_mov']; ?>" title="Alterar esta movimentação" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="Alterar esta movimentação" /></a>
                          <?php }; ?>
                        <?php }; ?>
                        </td>
                        <td class="tb_bt">
                        <?php if ( $n_chefia >= 4 ) {  ?>
                          <?php if ( $i == 1 ) {  ?>
                            <a href='javascript:void(0)' onclick='drop( "idmov", "<?php echo $dados_mov['id_mov']; ?>", "senddetmovdel", "drop_mov_det", "2")' title="Excluir esta movimentação"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir esta movimentação" class="icon_button" /></a>
                          <?php } else {?>
                            <a href='javascript:void(0)' onclick='drop( "idmov", "<?php echo $dados_mov['id_mov']; ?>", "senddetmovdelacervo", "drop_mov_det", "2")' title="Excluir esta movimentação"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir esta movimentação" class="icon_button" /></a>
                          <?php }; ?>
                        <?php }; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="desc_user" colspan="5">Cadastrado em <?php echo $dados_mov['data_add_fc'] ?>, usuário <?php echo $dados_mov['user_add'] ?><?php if ($dados_mov['user_up'] and $dados_mov['data_up_f']) {?> - Atualizado em <?php echo $dados_mov['data_up_f'] ?>, usuário <?php echo $dados_mov['user_up'] ?> <?php }?></td>
                    </tr>
                       <?php } // fim do while ?>
                </table>
            </div><!-- /div id="table_mov" -->
            <?php } // fim do if que conta o número de ocorrencias ?>

            <div id="rc"></div>

            <div class="linha">
                HISTÓRICO DE MUDANÇAS DE <?php echo mb_strtoupper( SICOP_RAIO ) ?> E <?php echo mb_strtoupper( SICOP_CELA ) ?><?php if ( $sit_det != 15 ) {  ?><?php if ($n_det_rc >= $n_rc_n) {  ?> - <a class="link_add_rc" title="Alterar <?php echo mb_strtolower( SICOP_RAIO ) . '/' . mb_strtolower( SICOP_CELA ) ?>" href="javascript:void(0)">Mudar <?php echo mb_strtolower( SICOP_RAIO ) . '/' . mb_strtolower( SICOP_CELA ) ?></a><?php }; ?><?php }; ?>
                <hr />
            </div>
            <?php
            $query_rc = $db->query( $query_rc );
            $db->closeConnection();
            $cont_rc  = $query_rc->num_rows;
            if ( $cont_rc < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não há mudanças de ' . mb_strtolower( SICOP_RAIO ) . '/' . mb_strtolower( SICOP_CELA ) . '.</p>';
            } else {
                ?>
            <table class="lista_busca">
                <tr >
                    <th class="desc_data">&nbsp;</th>
                    <th colspan="2" align="center" >MUDOU DO</th>
                    <th colspan="2" align="center" >PARA</th>
                    <th align="center">&nbsp;</th>
                </tr>
                <tr >
                    <th class="desc_data">DATA</th>
                    <th class="raio_old"><?php echo mb_strtoupper( SICOP_RAIO ) ?></th>
                    <th class="cela_old"><?php echo mb_strtoupper( SICOP_CELA ) ?></th>
                    <th class="raio_new"><?php echo mb_strtoupper( SICOP_RAIO ) ?></th>
                    <th class="cela_new"><?php echo mb_strtoupper( SICOP_CELA ) ?></th>
                    <th class="rc_user">USUÁRIO</th>
                </tr>
                    <?php while ( $dados_rc = $query_rc->fetch_assoc() ) { ?>
                <tr class="even">
                    <td class="desc_data"><?php echo $dados_rc['data_rc_f'] ?></td>
                    <td class="raio_old"><?php echo $dados_rc['old_raio']; ?></td>
                    <td class="cela_old"><?php echo $dados_rc['old_cela']; ?></td>
                    <td class="raio_new"><?php echo $dados_rc['n_raio']; ?></td>
                    <td class="cela_new"><?php echo $dados_rc['n_cela']; ?></td>
                    <td class="rc_user"><?php echo empty( $dados_rc['user_add'] ) ? '' : $dados_rc['user_add']; ?></td>
                </tr>
                <?php } // fim do while ?>
            </table>
            <?php } // fim do if que conta o número de ocorrencias ?>

            <div id="alias"></div>

            <div class="linha">
                ALIASES<?php if ( $n_det_alias >= $n_alias_n ) {  ?> - <a href="cadaliasdet.php?iddet=<?php echo $d_det['iddetento']; ?>" title="Cadastrar um novo alias">Adicionar alias</a> <a href="#" title="Abrir em outra janela" onClick="javascript: ow('cadaliasdet.php?iddet=<?php echo $d_det['iddetento']; ?>&targ=1', '830', '600'); return false"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>user_popup.png" alt="Abrir em outra janela" class="icon_popup" /></a><?php }; ?>
                <hr />
            </div>
            <?php
                $queryalias = $db->query( $queryalias );
                $db->closeConnection();
                $contali    = $queryalias->num_rows;
                if ( $contali < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                    echo '<p class="p_q_no_result">Não há aliases cadastrados.</p>';
                } else {
                ?>
            <table class="lista_busca">
                <tr class="cab">
                    <th class="desc_data">DATA</th>
                    <th class="tipo_alias">TIPO DE ALIAS</th>
                    <th class="desc_alias">ALIAS</th>
                    <th class="tb_bt">&nbsp;</th>
                    <th class="tb_bt">&nbsp;</th>
                </tr>
                    <?php
                    while( $dadosali = $queryalias->fetch_assoc() ) {
                        ?>
                <tr class="even">
                    <td class="desc_data"><?php echo $dadosali['data_add_f'] ?></td>
                    <td class="tipo_alias"><?php echo $dadosali['tipoalias'] ?></td>
                    <td class="desc_alias"><?php echo nl2br($dadosali['alias_det']) ?></td>
                    <td class="tb_bt"><?php if ($n_det_alias >= $n_alias_n) {  ?><a href="editaliasdet.php?idalias=<?php echo $dadosali['idalias']; ?>" title="Alterar este alias" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="Alterar este alias" class="icon_button" /></a><?php }; ?></td>
                    <td class="tb_bt"><?php if ($n_chefia >= 4) {  ?><a href='javascript:void(0)' onclick='drop( "id_alias", "<?php echo $dadosali['idalias']; ?>", "senddetalias", "drop_alias_det", "2")' title="Excluir este alias"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir este alias" class="icon_button" /></a><?php }; ?></td>
                </tr>
                <tr>
                    <td colspan="6" class="desc_user">Cadastrado em <?php echo $dadosali['data_add_fc'] ?>, usuário <?php echo $dadosali['user_add'] ?><?php if ($dadosali['user_up'] and $dadosali['data_up_f']) {?> - Atualizado em <?php echo $dadosali['data_up_f'] ?>, usuário <?php echo $dadosali['user_up'] ?> <?php }?></td>
                </tr>
                <?php } // fim do while ?>
            </table>
            <?php } // fim do if que conta o número de ocorrencias ?>

            <?php if ( $n_rol >= $n_rol_n ) { // limitar o acesso de que não tem pemições para acessar o rol de visitas ?>
            <div id="rol"></div>

            <div class="linha">
                ROL DE VISITAS (ativa - <font color="#FF0000">excluida</font> - <font color="#CC9900">suspensa</font>) <?php if ($n_rol >= $n_rol_n && !empty($d_det['matricula'])) {  ?> - <a href="<?php echo $caminho; ?>visita/rol_visit.php?iddet=<?php echo $d_det['iddetento'] ?>">Ir para o rol</a><?php }; ?>
              <hr />
            </div>
            <div class="linha_perm_rol">
                <p class="par_curto"><?php echo $fontautv ?><?php echo SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_L; ?> pode receber visitas: <?php echo tratasn( $d_det['aut_visita'] ) . $fontautvf ?></p>
                <p class="par_curto"><?php echo $fontauts ?><?php echo SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_L; ?> pode receber sedex: <?php echo tratasn( $d_det['aut_sedex'] ) . $fontautsf ?></p>
            </div>
            <?php

                $queryvis = "SELECT
                               `visitas`.`idvisita`,
                               `visitas`.`cod_detento`,
                               `visitas`.`nome_visit`,
                               `visitas`.`sexo_visit`,
                               `visitas`.`nasc_visit`,
                               DATE_FORMAT( `visitas`.`nasc_visit`, '%d/%m/%Y' ) AS nasc_visit_f,
                               FLOOR( DATEDIFF( CURDATE(), `visitas`.`nasc_visit` ) / 365.25 ) AS idade_visit,
                               `tipoparentesco`.`parentesco`
                             FROM
                               `visitas`
                               LEFT JOIN `tipoparentesco` ON `visitas`.`cod_parentesco` = `tipoparentesco`.`idparentesco`
                             WHERE
                               `visitas`.`cod_detento` = $iddet
                               AND
                               `visitas`.`num_in` = ( SELECT `n_p_trans` FROM `detentos` WHERE `iddetento` = $iddet LIMIT 1 )
                             ORDER BY
                               `visitas`.`nome_visit`";

                $queryvis = $db->query( $queryvis );
                $db->closeConnection();
                $contv = 0;
                if ( $queryvis ) $contv = $queryvis->num_rows;

                // se o número de ocorrências for menor do que 1, mostra a mensagem
                if( $contv < 1 ) {

                    echo '<p class="p_q_no_result">Não há visitas cadastradas.</p>';

                } else {

                    ?>

            <table class="lista_busca">
                <tr>
                    <th class="visit_id">ID</th>
                    <th class="visit_nome">NOME DO VISITANTE</th>
                    <th class="visit_data_nasc">NASCIMENTO</th>
                    <th class="visit_parent">PARENTESCO</th>
                    <th class="visit_sexo">SEXO</th>
                    <th class="tb_bt">&nbsp;</th>
                    <th class="tb_bt">&nbsp;</th>
                </tr>
                <?php
                while( $dadosv = $queryvis->fetch_assoc() ) {

                    $idvisita = $dadosv['idvisita'];

                    $visit = manipula_sit_visia( $idvisita );

                    $suspenso    = false;
                    $visit_class = 'visit_ativa';
                    $sit_v_atual = 'ATIVA';

                    if ( $visit ) {

                        $suspenso    = $visit['suspenso'];
                        $visit_class = $visit['css_class'];
                        $sit_v_atual = $visit['sit_v'];

                    }

                    ?>
                <tr class="even">
                    <td class="visit_id"><?php echo $dadosv['idvisita'] ?></td>
                    <td class="visit_nome" title="Situação do visitante: <?php echo $sit_v_atual; ?>"><a href="<?php echo $caminho; ?>visita/detalvisit.php?idvisit=<?php echo $dadosv['idvisita'] ?>" ><?php echo $dadosv['nome_visit'] ?></a></td>
                    <td class="visit_data_nasc <?php echo $visit_class; ?>"><?php echo $dadosv['nasc_visit_f'] ?><?php echo !is_null( $dadosv['idade_visit'] ) ? ' - ' . $dadosv['idade_visit'] . ' anos'  : ''; ?></td>
                    <td class="visit_parent <?php echo $visit_class; ?>"><?php echo $dadosv['parentesco'] ?></td>
                    <td class="visit_sexo <?php echo $visit_class; ?>"><?php echo $dadosv['sexo_visit'] ?></td>
                    <td class="tb_bt"><?php if ($n_rol >= 3 ) { ?><a href="<?php echo $caminho; ?>visita/editvisit.php?idvisit=<?php echo $dadosv['idvisita']; ?>" title="Alterar dados deste visitante" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="Alterar dados deste visitante" class="icon_button" /></a><?php }; ?></td>
                    <td class="tb_bt"><?php if ($n_rol >= 4 ) { ?><a href='javascript:void(0)' onclick='drop_visit(<?php echo $iddet; ?>, <?php echo $dadosv['idvisita']; ?>)' title="Excluir este visitante"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir este visitante" class="icon_button" /></a><?php }; ?></td>
                </tr>
                  <?php } // fim do while ?>
            </table>
                <?php
                    } // fim do if que conta o número de ocorrencias
                } // fim do if que verifica as permissões
                ?>

            <?php if ( $n_sind >= $n_sind_n ) { // limitar o acesso de que não tem pemições para acessar a sindicância ?>
            <div id="sind"></div>

            <div class="linha">
                SINDICÂNCIAS (<font color="#FF0000">em reabilitação</font>) <?php if ($n_sind >= 3 && !empty($d_det['matricula'])) {  ?> - <a href="<?php echo $caminho; ?>sind/cadpda.php?iddet=<?php echo $d_det['iddetento'] ?>">Cadastrar sindicância</a><?php }; ?>
                <hr />
            </div>
            <?php

                $querysind = "SELECT
                                `sindicancias`.`idsind`,
                                `sindicancias`.`cod_detento`,
                                `sindicancias`.`num_pda`,
                                `sindicancias`.`ano_pda`,
                                `sindicancias`.`local_pda`,
                                DATE_FORMAT(`sindicancias`.`data_ocorrencia`, '%d/%m/%Y') AS data_ocorrencia,
                                `sindicancias`.`sit_pda`,
                                `sindicancias`.`data_reabilit`,
                                DATE_FORMAT(`sindicancias`.`data_reabilit`, '%d/%m/%Y') AS data_reab_f,
                                `tipositdet`.`situacaodet`
                              FROM
                                `sindicancias`
                                LEFT JOIN `tipositdet` ON `sindicancias`.`cod_sit_detento` = `tipositdet`.`idsitdet`
                              WHERE
                                `sindicancias`.`cod_detento` = $iddet
                              ORDER BY
                                `sindicancias`.`ano_pda`, `sindicancias`.`num_pda`";

                $querysind = $db->query( $querysind );
                $db->closeConnection();
                $conts     = $querysind->num_rows;
                if ( $conts < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                    echo '<p class="p_q_no_result">Nada consta.</p>';
                } else {
                ?>
            <table class="lista_busca">
                <tr>
                    <th class="num_pda">NÚMERO DO PDA</th>
                    <th class="data_oc">DATA DA OCORRÊNCIA</th>
                    <th class="sit_pda">SITUAÇÃO DO PDA</th>
                    <th class="sit_det_pda">SITUAÇÃO D<?php echo SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U; ?></th>
                    <th class="data_reab">REABILIATAÇÃO EM</th>
                </tr>
                <?php
                while ( $dadoss = $querysind->fetch_assoc() ) {

                    $numpda = format_num_pda( $dadoss['num_pda'], $dadoss['ano_pda'], $dadoss['local_pda'] );

                    $corfonts = muda_cor_pda( $dadoss['data_reabilit'], $dadoss['sit_pda'] );

                    ?>
                <tr class="even">
                    <td class="num_pda"><a href="<?php echo $caminho; ?>sind/detalpda.php?idsind=<?php echo $dadoss['idsind'] ?>"><?php echo $numpda ?></a></td>
                    <td class="data_oc"><font color="<?php echo $corfonts;?>"><?php echo $dadoss['data_ocorrencia'] ?></font></td>
                    <td class="sit_pda"><font color="<?php echo $corfonts;?>"><?php echo trata_sit_pda($dadoss['sit_pda']) ?></font></td>
                    <td class="sit_det_pda"><font color="<?php echo $corfonts;?>"><?php echo $dadoss['situacaodet'] ?></font></td>
                    <td class="data_reab"><font color="<?php echo $corfonts;?>"><?php echo $dadoss['data_reab_f'] ?></font></td>
                </tr>
                <?php } // fim do while ?>
            </table>
            <?php
                } // fim do if que conta o número de ocorrencias
            } // fim do if que verifica as permissões
            ?>

            <?php if ( $n_cadastro >= $n_cad_n ) { // limitar o acesso de que não tem pemições para acessar o cadastro ?>
            <div id="aud"></div>
            <div class="linha">
                AUDIÊNCIAS (ativa - <font color="#FF0000">justificada</font> - <font color="#CC9900">cancelada</font>)
                <?php
                $q_count_aud_fut = "SELECT
                                      COUNT( `idaudiencia` ) AS `total_aud`
                                    FROM
                                      `audiencias`
                                    WHERE
                                      `cod_detento` = $iddet
                                      AND
                                      `data_aud` < DATE( NOW() )";

                $cont_all_aud = 0;
                $cont_all_aud = $db->fetchOne( $q_count_aud_fut );
                $db->closeConnection();
                if ( $cont_all_aud >= 1 ) { ?>
                <br />
                <span id="span_all_aud">Mostrando as audiências futuras - <a id="all_aud" href="javascript:void(0)">Mostrar todas</a></span>
                <?php } ?>

                <?php if ( $n_cadastro >= 3 and !empty( $d_det['matricula'] ) ) {  ?> - <a href="<?php echo $caminho; ?>cadastro/cadaud.php?iddet=<?php echo $d_det['iddetento'] ?>">Cadastrar audiência</a><?php }; ?>
                <hr />
            </div>
            <div id="table_aud">
            <?php

                $queryaud = "SELECT
                               `idaudiencia`,
                               `cod_detento`,
                               `data_aud`,
                               `hora_aud`,
                               `local_aud`,
                               `cidade_aud`,
                               `tipo_aud`,
                               `num_processo`,
                               `sit_aud`,
                               DATE_FORMAT(`data_aud`, '%d/%m/%Y') AS `data_aud_f`,
                               DATE_FORMAT(`hora_aud`, '%H:%i') AS `hora_aud_f`
                             FROM
                               `audiencias`
                             WHERE
                               `cod_detento` = $iddet
                               AND
                               `data_aud` >= DATE( NOW() )
                             ORDER BY
                               `data_aud` DESC, `hora_aud`";

                $queryaud = $db->query( $queryaud );
                $db->closeConnection();
                $conta    = $queryaud->num_rows;
                if ( $conta < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                    echo '<p class="p_q_no_result">Nenhuma audiência agendada.</p>';
                } else {

                    ?>

                <table class="lista_busca">
                    <tr>
                        <th class="local_aud_hist">LOCAL DE APRESENTAÇÃO</th>
                        <th class="cidade_aud_hist">CIDADE</th>
                        <th class="data_hora_aud">DATA / HORA</th>
                        <th class="n_process">Nº DO PROCESSO</th>
                    </tr>
                    <?php
                    while ( $dadosa = $queryaud->fetch_assoc() ) {

                        $aud = trata_sit_aud( $dadosa['sit_aud'] );

                        ?>
                    <tr class="even" title="Situação da audiência: <?php echo $aud['sitaud']; ?>">
                        <td class="local_aud_hist"><a href="<?php echo $caminho; ?>cadastro/detalaud.php?idaud=<?php echo $dadosa['idaudiencia'] ?>" ><?php echo $dadosa['local_aud'] ?></a></td>
                        <td class="cidade_aud_hist <?php echo $aud['css_class']; ?>"><?php echo $dadosa['cidade_aud'] ?></td>
                        <td class="data_hora_aud <?php echo $aud['css_class']; ?>"><?php echo $dadosa['data_aud_f'] . ' às ' . $dadosa['hora_aud_f']?></td>
                        <td class="n_process <?php echo $aud['css_class']; ?>"><?php echo $dadosa['num_processo'] ?></td>
                    </tr>
                    <?php } // fim do while ?>
                </table>
                <?php } // fim do if que conta o número de ocorrencias ?>
            </div>
            <?php } // fim do if que verifica as permissões ?>

<?php include 'footer.php'; ?>
