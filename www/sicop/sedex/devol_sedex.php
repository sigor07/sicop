<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_sedex   = get_session( 'n_sedex', 'int' );
$n_rol     = get_session( 'n_rol', 'int' );
$n_sedex_n = 3;

if ( $n_sedex < $n_sedex_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'DEVOLUÇÃO DE SEDEX';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$ids = get_get( 'ids', 'int' );

if ( empty( $ids ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página de detalhes de sedex.';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}


$q_sedex = "SELECT
              `sedex`.`idsedex`,
              `sedex`.`cod_detento`,
              `sedex`.`cod_sedex`,
              `sedex`.`cod_motivo_dev`,
              `sedex`.`sit_sedex`,
              DATE_FORMAT( `data_add`, '%d/%m/%Y' ) AS data_sedex
            FROM
              `sedex`
            WHERE
              `sedex`.`idsedex` = $ids
            LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_sedex = $model->query( $q_sedex );

// fechando a conexao
$model->closeConnection();

if( !$q_sedex ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Falha na consulta ( DEVOLUÇÃO DE SEDEX ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$cont_sedex = $q_sedex->num_rows;

if( $cont_sedex < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'A consulta retornou 0 ocorrências ( DEVOLUÇÃO DE SEDEX ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$d_sedex = $q_sedex->fetch_assoc();

$q_mot_sedex = 'SELECT `idmotivo`, `motivo` FROM `sedex_motivo` WHERE `idmotivo` IN( 1, 2, 3, 4, 5, 6, 12, 13 )';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_mot_sedex = $model->query( $q_mot_sedex );

// fechando a conexao
$model->closeConnection();

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Encaminhar sedex para devolução';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>

            <p class="descript_page">ENCAMINHAR SEDEX PARA DEVOLUÇÃO</p>

            <p class="table_leg">Sedex</p>

            <table class="detal_sedex">

                <tr>
                    <td class="sedex_p" colspan="2">Código: <?php echo $d_sedex['cod_sedex'] ?></td>
                    <td class="sedex_p" colspan="2">Data: <?php echo $d_sedex['data_mov_f'] ?></td>
                    <td class="sedex_p" colspan="2">Situação: <?php echo trata_sit_sedex( $d_sedex['sit_sedex'] ) ?></td>
                </tr>

            </table>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendsedex.php" method="post" name="sendsedex" id="sendsedex">

                <table class="edit" >
                    <tr>
                        <td class="tbe_legend_grd">Motivo da devolução:</td>
                        <td class="tbe_field">
                            <select name="motivo_dev" class="CaixaTexto" id="motivo_dev">
                                <option value="" >Selecione...</option>
                                <?php while ( $d_mot_sedex = $q_mot_sedex->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_mot_sedex['idmotivo']; ?>" ><?php echo $d_mot_sedex['motivo']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                </table>

                <input type="hidden" name="idsedex[]" id="idsedex" value="<?php echo $ids; ?>" />
                <input type="hidden" name="proced" id="proced" value="1" />
                <input type="hidden" name="sub_proced" id="sub_proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Atualizar" />
                    <input class="form_bt" type="button" name="" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">

                $(function() {
                    $("form").submit(function() {
                        if ( valida_dev_sedex() == true ) {
                            // ReadOnly em todos os inputs
                            $("input", this).attr("readonly", true);
                            // Desabilita os submits
                            $("input[type='submit'],input[type='image']", this).attr("disabled", true);
                            return true;
                        } else {
                            return false;
                        }
                    });
                });

            </script>

<?php include 'footer.php';?>