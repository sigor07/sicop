<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_rol = get_session( 'n_rol', 'int' );
$n_rol_n = 3;

if ( $n_rol < $n_rol_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'ALTERAÇÃO DE SUSPENÇÃO DE VISITANTE';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idsusp = get_get( 'idsusp', 'int' );

if ( empty( $idsusp ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página. Identificador da suspensão em branco. ( ALTERAÇÃO DE SUSPENÇÃO DE VISITANTE )";
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$query_susp_edit = "SELECT
                      `id_visit_susp`,
                      `cod_visita`,
                      `data_inicio`,
                      DATE_FORMAT( `data_inicio`, '%d/%m/%Y' ) AS data_inicio_f,
                      `periodo`,
                      `motivo`,
                      `revog`,
                      `user_add`,
                      `data_add`,
                      `user_up`,
                      `data_up`
                    FROM
                      `visita_susp`
                    WHERE
                      `id_visit_susp` = $idsusp
                    LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_susp_edit = $model->query( $query_susp_edit );

// fechando a conexao
$model->closeConnection();

if ( !$query_susp_edit ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta da suspenção ( ALTERAÇÃO DE SUSPENÇÃO DE VISITANTE ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$conts = $query_susp_edit->num_rows;

if ( $conts < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'A consulta da suspenção retornou 0 ocorrências ( ALTERAÇÃO DE SUSPENÇÃO DE VISITANTE ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_susp = $query_susp_edit->fetch_assoc();

$idvisit = $d_susp['cod_visita'];

$tipo_susp = empty( $d_susp['periodo'] ) ? 'D' : 'T' ;

$desc_pag = 'Alterar suspensão de visitante';

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

// adicionando o javascript
$cab_js   = array();
$cab_js[] = 'valida.js';
$cab_js[] = 'ajax/ajax_visit.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 5 );
$trail->output();
?>


            <p class="descript_page">SUSPENDER VISITANTE</p>

            <?php include 'quali/visit_full.php'; ?>

            <form class="form_bts" action="../send/sendsuspvisit.php" method="post" name="visitsusp" id="visitsusp">

                <table class="edit">
                    <tr>
                        <td width="95">Tipo de suspensão:</td>
                        <td width="289">
                            <input type="radio" name="tipo_susp" value="T" id="tipo_susp_0" onClick="mostra_susp_visit();"  <?php echo $tipo_susp == 'T' ? 'checked="checked"' : ''; ?> />Temporária &nbsp;&nbsp;
                            <input type="radio" name="tipo_susp" value="D" id="tipo_susp_1" onClick="mostra_susp_visit();"  <?php echo $tipo_susp == 'D' ? 'checked="checked"' : ''; ?>/>Definitiva
                        </td>
                    </tr>
                    <tr>
                        <td width="95">A partir de: </td>
                        <td>
                            <input name="data_inicio" type="text" class="CaixaTexto" id="data_inicio" onBlur="verifica_data(this, this.value)" onKeyPress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $d_susp['data_inicio_f']; ?>" size="12" maxlength="10" />
                            &nbsp;&nbsp;<a href="#" onClick="javascript: datahoje('data_inicio'); return false;" >hoje</a>
                        </td>
                    </tr>
                    <tr id="tr_dias_susp">
                        <td width="95">Período <br />(em dias):</td>
                        <td><input name="periodo" type="text" class="CaixaTexto" id="periodo" onKeyPress="return blockChars(event, 2);" value="<?php echo $d_susp['periodo']; ?>" size="3" maxlength="3" /></td>
                    </tr>
                    <tr>
                        <td width="95">Motivo da suspensão:</td>
                        <td><textarea name="motivo" cols="50" rows="3" class="CaixaTexto" id="motivo" onBlur="upperMe(this);" onKeyPress="return blockChars(event, 4);" ><?php echo $d_susp['motivo']; ?></textarea></td>
                    </tr>
                    <?php if ( $n_rol >= 3 ) { ?>
                    <tr>
                        <td width="95">Revogar:</td>
                        <td><input name="revog" type="checkbox" id="revog" value="1" <?php echo $d_susp['revog'] == 1 ? 'checked="checked"' : '' ?> /></td>
                    </tr>
                    <?php } else { ?>
                    <input name="revog" type="hidden" id="revog" value="<?php echo $d_susp['revog'] == 1 ? 1 : 0 ?>" />
                    <?php } ?>
                </table>

                <div class="form_bts">
                    <input class="form_bt" name="cadastrar" type="submit" value="Atualizar" />
                    <input class="form_bt" name="" type="button" onClick="history.go(-1)" value="Cancelar" />
                </div>

                <input type="hidden" name="datahj" id="datahj" value="<?php echo date('d/m/Y') ?>" />
                <input type="hidden" name="proced" id="proced" value="1">
                <input type="hidden" name="idsusp" id="idsusp" value="<?php echo $d_susp['id_visit_susp']; ?>" />
                <input type="hidden" name="idvisit" id="idvisit" value="<?php echo $idvisit; ?>" />

            </form>

            <script type="text/javascript">

                mostra_susp_visit();

                $(function() {

                    $( "#tipo_susp_0" ).focus();
                    $( "#data_inicio" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });

                    $("form").submit(function() {
                        if ( validasuspvisit() == true ) {
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

<?php include 'footer.php'; ?>