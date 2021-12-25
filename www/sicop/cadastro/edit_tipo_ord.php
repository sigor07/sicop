<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag         = link_pag();
$tipo        = '';
$motivo_pag  = 'ALTERAÇÃO DE TIPO DE ORDEM DE SAÍDA';

$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_cad_n    = 3;

if ( $n_cadastro < $n_cad_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$is_post = is_post();
if ( $is_post ) {

    extract( $_POST, EXTR_OVERWRITE );

    $user     = get_session( 'user_id', 'int' );
    $ip       = "'" . $_SERVER['REMOTE_ADDR'] . "'";
    $tipo_pag = 'ALTERAÇÃO DE TIPO DE ORDEM DE SAÍDA';

    $id_ord_saida_det = empty( $id_ord_saida_det ) ? '' : (int)$id_ord_saida_det;
    if ( empty( $id_ord_saida_det ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'Identificador d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ' da ordem de sáida em branco. Operação cancelada ( $tipo_pag ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 'f' );

        exit;

    }

    $tipo_ord = empty( $tipo_ord ) ? 'NULL' : (int)$tipo_ord;

    // pegar os dados do preso
    $where_det = "( SELECT `cod_detento` FROM `ordens_saida_det` WHERE `id_ord_saida_det` = $id_ord_saida_det LIMIT 1 )";
    $detento = dados_det( $where_det );

    $query = "UPDATE
                `ordens_saida_det`
              SET
                `cod_tipo` = $tipo_ord,
                `user_up` = $user,
                `data_up` = NOW(),
                `ip_up` = $ip
              WHERE
                `id_ord_saida_det` = $id_ord_saida_det
              LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query = $model->query( $query );

    // fechando a conexao
    $model->closeConnection();

    if ( !$query ) {

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de atualização ( $tipo_pag ). \n\n $detento \n\n $valor_user.";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 'f' );

        exit;

    }

    echo msg_js( '', 'rf' );

    exit;

}

$idorddet = get_get( 'idorddet', 'int' );
if ( empty( $idorddet ) ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( $motivo_pag - IDENTIFICADOR D" . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U . ' DA ORDEM DE SÁIDA EM BRANCO ).';
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );

    exit;

}

$q_det_ord = "SELECT
                `cod_detento`,
                `cod_tipo`
              FROM
                `ordens_saida_det`
              WHERE
                `id_ord_saida_det` = $idorddet
              LIMIT 1;";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_det_ord = $model->query( $q_det_ord );

// fechando a conexao
$model->closeConnection();

if ( !$q_det_ord ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Falha na consulta ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 'f' );
    exit;

}

$cont = $q_det_ord->num_rows;
if ( $cont < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "A consulta retornou 0 ocorrências ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 'f' );
    exit;

}

$d_det_ord = $q_det_ord->fetch_assoc();

$iddet = $d_det_ord['cod_detento'];


$q_tipo_ord = 'SELECT
                 `id_tipo`,
                 `tipo`
               FROM
                 `ordens_saida_tipo`
               ORDER BY
                 `tipo`';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_tipo_ord = $model->query( $q_tipo_ord );

// fechando a conexao
$model->closeConnection();

if ( !$q_tipo_ord ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Falha na consulta ( $motivo_pag - TIPO DE ORDEM DE SAÍDA ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 'f' );
    exit;

}

$cont = $q_tipo_ord->num_rows;
if ( $cont < 1 ) {


    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "A consulta retornou 0 ocorrências ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );


    echo msg_js( 'FALHA!', 'f' );
    exit;

}


$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Alterar tipo de ordem de saída';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab_simp.php';
?>

            <p class="descript_page">ALTERAR TIPO DE ORDEM DE SAÍDA</p>

            <?php include 'quali/det_basic.php'; ?>

            <form action='<?php echo $_SERVER['PHP_SELF'];?>' method='post' name='edit_det_ord'>
                <table class="edit">
                    <tr>
                        <td>Tipo:</td>
                        <td>
                            <select name="tipo_ord" class="CaixaTexto" id="tipo_ord">
                                <option value="" >Selecione...</option>
                                <?php while ( $d_tipo_ord = $q_tipo_ord->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_tipo_ord['id_tipo']; ?>" <?php echo $d_tipo_ord['id_tipo'] == $d_det_ord['cod_tipo'] ? 'selected="selected"' : ''; ?>><?php echo $d_tipo_ord['tipo']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                </table>

                <input type="hidden" name="id_ord_saida_det" id="id_ord_saida_det" value="<?php echo $idorddet ?>" />

                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" value="Atualizar" />
                    <input class="form_bt" name="" type="button" onClick="self.window.close()" value="Cancelar" />
                </div>

            </form><!-- /form name='edit_det_ord' -->

            <script type="text/javascript">

                $(function() {
                    $("form").submit(function() {
                        // ReadOnly em todos os inputs
                        $("input", this).attr("readonly", true);
                        // Desabilita os submits
                        $("input[type='submit'],input[type='image']", this).attr("disabled", true);
                        return true;
                    });
                });

            </script>

<?php include 'footer_simp.php'; ?>