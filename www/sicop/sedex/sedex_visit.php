<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_portaria   = get_session( 'n_portaria', 'int' );
$n_portaria_n = 3;

$n_sedex   = get_session( 'n_sedex', 'int' );
$n_sedex_n = 3;

if ( $n_portaria < $n_portaria_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'CADASTRAMENTO DE SEDEX - VISITAS';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

if ( $n_sedex < $n_sedex_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'CADASTRAMENTO DE SEDEX - VISITAS';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idvisit = get_get( 'idvisit', 'int' );

if ( empty( $idvisit ) ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página. Identificador do VISITANTE em branco. ( CADASTRAMENTO DE SEDEX - VISITAS )';
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$query_visit = "SELECT
                  `idvisita`,
                  `cod_detento`,
                  `nome_visit`,
                  `rg_visit`,
                  `sexo_visit`
                FROM
                  `visitas`
                WHERE
                  `idvisita` = $idvisit
                LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_visit = $model->query( $query_visit );

// fechando a conexao
$model->closeConnection();

if( !$query_visit ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( CADASTRAMENTO DE SEDEX - VISITAS ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$contv = $query_visit->num_rows;

if( $contv < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'A consulta retornou 0 ocorrências ( CADASTRAMENTO DE SEDEX - VISITAS ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_visit = $query_visit->fetch_assoc();

$iddet = $d_visit['cod_detento'];

$query_det = "SELECT
                `aut_sedex`,
                `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
                `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
                `unidades_out`.`idunidades` AS iddestino
              FROM
                `detentos`
                LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
              WHERE
                `iddetento` = $iddet
              LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_det = $model->query( $query_det );

// fechando a conexao
$model->closeConnection();

if( !$query_det ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Falha na consulta de verificação d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ' ( CADASTRAMENTO DE SEDEX - VISITAS ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$contd = $query_det->num_rows;

if ( $contd < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'A consulta de verificação d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ' retornou 0 ocorrências ( CADASTRAMENTO DE SEDEX - VISITAS ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_det = $query_det->fetch_assoc();

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$tipo_mov_in  = $d_det['tipo_mov_in'];
$tipo_mov_out = $d_det['tipo_mov_out'];
$iddestino    = $d_det['iddestino'];
$sit_det      = manipula_sit_det_id( $tipo_mov_in, $tipo_mov_out, $iddestino );

$aut_sedex  = $d_det['aut_sedex'];
$motivo = '';

/**
 * PARTE QUE VERIFICA A SITUAÇÃO DO DETENTO
 */

 $pode_receber = true;

// verifica a situação do detento
if ( $sit_det == SICOP_SIT_DET_TRANSF ||   // TRANSFERIDO
     $sit_det == SICOP_SIT_DET_EXCLUIDO || // EXCLUIDO (ALVARA)
     $sit_det == SICOP_SIT_DET_EVADIDO ||  // EVADIDO
     $sit_det == SICOP_SIT_DET_FALECIDO || // FALECIDO
     $sit_det == SICOP_SIT_DET_ACEHGAR ) { // A CHEGAR

    $pode_receber = false;

    if ( $sit_det == SICOP_SIT_DET_TRANSF ) {
        $motivo = 1;
    } else if ( $sit_det == SICOP_SIT_DET_EXCLUIDO ) {
        $motivo = 2;
    } else if ( $sit_det == SICOP_SIT_DET_EVADIDO ) {
        $motivo = 3;
    } else if ( $sit_det == SICOP_SIT_DET_FALECIDO ) {
        $motivo = 4;
    } else if ( $sit_det == SICOP_SIT_DET_ACEHGAR ) {
        $motivo = 5;
    }

}

// pesquisar se o visitante possui suspensões ativas
if ( $pode_receber ) {

    $q_v_susp = "SELECT
                   `periodo`
                 FROM
                   `visita_susp`
                 WHERE
                   ( `cod_visita` = $idvisit
                    AND
                    ( ( CURDATE() BETWEEN `data_inicio` AND ADDDATE( `data_inicio`, `periodo` ) )
                    OR
                    ( CURDATE() >= `data_inicio` AND ISNULL( ADDDATE( `data_inicio`, `periodo` ) ) ) )
                    AND
                    `revog` = FALSE )
                 ORDER BY
                   ADDDATE( `data_inicio`, `periodo` ) ASC
                 LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_v_susp = $model->query( $q_v_susp );

    // fechando a conexao
    $model->closeConnection();

    $cont_v_susp = 0;

    if( $q_v_susp ) $cont_v_susp = $q_v_susp->num_rows;

    // se possuir um ou mais resultados é por que o visitante esta suspenso
    if ( $cont_v_susp >= 1 ) {

        $pode_receber = false;
        $d_susp       = $q_v_susp->fetch_assoc();
        $periodo      = $d_susp['periodo'];

        $motivo = 9;

        if ( empty( $periodo ) ) { //se o periodo estiver vazio, é visita excluida
            $motivo = 8;
        }

    }

}

// verifica se o detento esta autorizado a receber sedex
if ( $aut_sedex == 0  and $pode_receber ) {
    $pode_receber = false;
    $motivo = 6;
}

// verifica se o detento já recebeu sedex essa semana
if ( $pode_receber ) {

    $q_sedex_week = "SELECT `idsedex` FROM `sedex` WHERE `cod_detento` = $iddet AND WEEK(`data_sedex`,2) = WEEK(NOW(),2) AND YEAR(`data_sedex`) = YEAR(NOW()) AND `sit_sedex` IN(1,2,5)";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_sedex_week = $model->query( $q_sedex_week );

    // fechando a conexao
    $model->closeConnection();

    $cont_sedex_w = 0;

    if( $q_sedex_week ) $cont_sedex_w = $q_sedex_week->num_rows;

    if ( $cont_sedex_w >= 1 ) {
        $pode_receber = false;
        $motivo = 7;
    }

}

// caso não possa receber, faz a busca do motivo
if ( !$pode_receber ) {
    $q_motivo = "SELECT `motivo` FROM `sedex_motivo` WHERE `idmotivo` = $motivo LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $desc_motivo = $model->fetchOne( $q_motivo );

    // fechando a conexao
    $model->closeConnection();

    if( $desc_motivo ===  false ) {

        echo msg_js( 'FALHA!!!', 1 );
        exit;

    }

}

$sit_sedex = 1;
if ( !$pode_receber ) $sit_sedex = 3;

$desc_pag = 'Registrar Sedex';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 5 );
$trail->output();
?>

            <?php if ( !$pode_receber ) {?>
            <script language="JavaScript" type="text/javascript">
                alert('ATENÇÃO! Este sedex será devolvido! \nMotivo: <?php echo $desc_motivo?>');
            </script>
            <?php }?>


            <p class="descript_page">REGISTRAR SEDEX</p>

            <?php include 'quali/det_basic.php'; ?>

            <p class="table_leg">Visitante</p>

            <table style="margin: 0 auto">
                <tr style="background-color: #ECE9D8">
                    <td style="height: 20px; padding: 1px 3px; vertical-align: middle; width: 350px;"><a href="<?php echo SICOP_ABS_PATH ?>visita/detalvisit.php?idvisit=<?php echo $idvisit; ?>" title="Clique aqui para abrir o cadastro deste visitante"><?php echo $d_visit['nome_visit']; ?></a></td>
                    <td style="height: 20px; padding: 1px 3px; vertical-align: middle; width: 140px;">R.G. <?php echo $d_visit['rg_visit'] ?></td>
                    <td style="height: 20px; padding: 1px 3px; text-align: center; vertical-align: middle; width: 160px;">Sexo: <?php echo $d_visit['sexo_visit'] ?></td>
                </tr>
            </table>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendsedex.php" method="post" name="sedex_in" id="sedex_in" onSubmit="return validasedex();">

                <p class="common">Situação do Sedex: <?php if ( !$pode_receber ) { ?>SEPARADO PARA DEVOLUÇÃO<?php } else { ?>RECEBIDO<?php } ?></p>

                <?php if ( !$pode_receber ) { ?>
                    <p class="common">Motivo: <?php echo $desc_motivo ?></p>
                <?php } ?>

                <table class="edit">
                    <tr>
                        <td width="105">Data:</td>
                        <td width="105" colspan="3"><input name="data_sedex" type="text" class="CaixaTexto" id="data_sedex" onBlur="verifica_data(this, this.value)" onKeyPress="mascara_data(this, this.value);return blockChars(event, 2);" value="<?php echo date( 'd/m/Y' ) ?>" size="12" maxlength="10" /></td>
                    </tr>
                    <tr>
                        <td>Código do sedex:</td>
                        <td colspan="3"><input name="cod_sedex" type="text" class="CaixaTexto" id="cod_sedex" onBlur="upperMe(this);" onKeyPress="return blockChars(event, 3);" size="17" maxlength="13" /></td>
                    </tr>
                </table>

                <input type="hidden" name="idvisit" id="idvisit" value="<?php echo $d_visit['idvisita'] ?>" />
                <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet ?>" />
                <input type="hidden" name="sit_sedex" id="sit_sedex" value="<?php echo $sit_sedex ?>" />
                <input type="hidden" name="motivo_dev" id="motivo_dev" value="<?php echo $motivo ?>" />
                <input type="hidden" name="proced" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" value="Registrar Sedex" />
                    <input class="form_bt" name="" type="button" onClick="history.go(-1)" value="Cancelar" />
                </div>
            </form>

            <script type="text/javascript">

                $(function() {
                    $( "#cod_sedex" ).focus();
                    $( "#data_sedex" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

<?php include 'footer.php';?>