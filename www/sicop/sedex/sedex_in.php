<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_portaria   = get_session( 'n_portaria', 'int');
$n_portaria_n = 3;

$n_sedex   = get_session( 'n_sedex', 'int');
$n_sedex_n = 3;

if ( $n_portaria < $n_portaria_n and $n_sedex < $n_sedex_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'CADASTRAMENTO DE SEDEX';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$iddet = get_get( 'iddet', 'int' );

if ( empty( $iddet ) ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página. Identificador d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ' em branco. ( CADASTRAMENTO DE SEDEX )';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$visita   = true;
$contv    = 0;
$queryvis = '';
if ( isset( $_GET['visit'] ) and $_GET['visit'] == 'no' ) {

    $visita = false;

}

if ( $visita ) {

    $queryvis = "SELECT
                   `visitas`.`idvisita`,
                   `visitas`.`cod_detento`,
                   `visitas`.`nome_visit`,
                   `visitas`.`sexo_visit`,
                   `visitas`.`nasc_visit`,
                   DATE_FORMAT( `visitas`.`nasc_visit`, '%d/%m/%Y' ) AS `nasc_visit_f`,
                   FLOOR( DateDiff( CurDate(), `visitas`.`nasc_visit` ) / 365.25 ) AS idade_visit,
                   `tipoparentesco`.`parentesco`
                 FROM
                   `visitas`
                   LEFT JOIN `tipoparentesco` ON `visitas`.`cod_parentesco` = `tipoparentesco`.`idparentesco`
                 WHERE
                   `visitas`.`cod_detento` = $iddet
                   AND
                   `visitas`.`num_in` = ( SELECT `n_p_trans` FROM `detentos` WHERE `iddetento` = $iddet LIMIT 1 )
                 ORDER BY
                   `visitas`.`nome_visit` ASC";

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
        $msg['text']  = "Falha na consulta ( VISITAS - CADASTRAMENTO DE SEDEX ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( '', 1 );
        exit;

    }

    $contv = $queryvis->num_rows;

}


$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

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

            <p class="descript_page">REGISTRAR SEDEX</p>

            <?php include 'quali/det_cad.php'; ?>

            <div class="linha">
                VISITAS CADASTRADAS (ativa - <font color="#FF0000">excluida</font> - <font color="#CC9900">suspensa</font>)
                <hr />
            </div>

            <?php

            if( $contv < 1 or !$visita ) { // se o número de ocorrências for menor do que 1 ou o remetente não constar no rol

                $sit = '<p class="p_q_no_result">Não há visitas cadastradas.</p>';
                $motivo = 11;

                if ( !$visita ) { // se o remetente não constar no rol
                    $sit = '<p class="p_q_no_result">O remetente não consta no rol.</p>';
                    $motivo = 10;
                }

                $q_motivo    = "SELECT `motivo` FROM `sedex_motivo` WHERE `idmotivo` = $motivo LIMIT 1";

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

                echo $sit;

                ?>
            <form action="<?php echo SICOP_ABS_PATH ?>send/sendsedex.php" method="post" name="cadobsrol" id="cadobsrol" onSubmit="return validasedex();">

                <p class="common">Situação do Sedex: SEPARADO PARA DEVOLUÇÃO</p>
                <p class="common">Motivo: <?php echo $desc_motivo; ?></p>

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

                <input type="hidden" name="sit_sedex" id="sit_sedex" value="3" />
                <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet ?>" />
                <input type="hidden" name="motivo_dev" id="motivo_dev" value="<?php echo $motivo; ?>" />
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

            <?php } else { ?>

            <table class="lista_busca">
                <tr >
                    <th class="visit_id">ID</th>
                    <th class="visit_nome">NOME DO VISITANTE</th>
                    <th class="visit_data_nasc">NASCIMENTO</th>
                    <th class="visit_parent">PARENTESCO</th>
                    <th class="visit_sexo">SEXO</th>
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
                <tr class="even" title="Situação do visitante: <?php echo $sit_v_atual; ?>">
                    <td class="visit_id"><?php echo $dadosv['idvisita'] ?></td>
                    <td class="visit_nome"><a href="sedex_visit.php?idvisit=<?php echo $idvisita ?>" ><?php echo $dadosv['nome_visit'] ?></a></td>
                    <td class="visit_data_nasc <?php echo $visit_class; ?>"><?php echo $dadosv['nasc_visit_f'] ?><?php echo !is_null( $dadosv['idade_visit'] ) ? ' - ' . $dadosv['idade_visit'] . ' anos'  : ''; ?></td>
                    <td class="visit_parent <?php echo $visit_class; ?>"><?php echo $dadosv['parentesco'] ?></td>
                    <td class="visit_sexo <?php echo $visit_class; ?>"><?php echo $dadosv['sexo_visit'] ?></td>
                </tr>

                <?php } // fim do while ?>
            </table>

            <div class="form_bts">
                <input name="menor" id="bmenorm" type="button" onclick="javascript: location.href='sedex_in.php?iddet=<?php echo $iddet;?>&visit=no';" value="Remetente não consta no rol" />
            </div>

            <?php } // fim do if que conta o número de ocorrencias ?>

<?php include 'footer.php';?>