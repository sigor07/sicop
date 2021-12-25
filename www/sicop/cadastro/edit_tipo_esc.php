<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag         = link_pag();
$tipo        = '';
$motivo_pag  = 'ALTERAÇÃO DE TIPO DE ESCOLTA';

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
    $tipo_pag = 'ALTERAÇÃO DE TIPO DE ESCOLTA';

    $id_escolta_det = empty( $id_escolta_det ) ? '' : (int)$id_escolta_det;
    if ( empty( $id_escolta_det ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'Identificador d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ' da escolta em branco. Operação cancelada ( $tipo_pag ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 'f' );

        exit;

    }

    // instanciando o model
    $model = SicopModel::getInstance();

    $tipo_esc = empty( $tipo_esc ) ? 'NULL' : (int)$tipo_esc;
    $hora = empty( $hora ) ? 'NULL' : "'" . $model->escape_string( $hora ) . "'";

    // pegar os dados do preso
    $where_det = "( SELECT `cod_detento` FROM `ordens_escolta_det` WHERE `id_escolta_det` = $id_escolta_det LIMIT 1 )";
    $detento = dados_det( $where_det );

    $query = "UPDATE
                `ordens_escolta_det`
              SET
                `hora` = STR_TO_DATE( $hora, '%H:%i' ),
                `cod_tipo` = $tipo_esc,
                `user_up` = $user,
                `data_up` = NOW(),
                `ip_up` = $ip
              WHERE
                `id_escolta_det` = $id_escolta_det
              LIMIT 1";

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

//    // montar a mensagem q será salva no log
//    $msg = array();
//    $msg['tipo']     = 'desc';
//    $msg['entre_ch'] = 'ATUALIZAÇÃO DE ESCOLTA';
//    $msg['text']     = "Atualização de tipo de escolta. \n\n[ ESCOLTA ]\n<b>ID:</b> $idescolta \n <b>Data:</b> $escolta_data";
//    get_msg( $msg, 1 );

    echo msg_js( '', 'rf' );

    exit;

}

$idescdet = get_get( 'idescdet', 'int' );
if ( empty( $idescdet ) ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( $motivo_pag - IDENTIFICADOR D" . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U . ' DO PEDIDO DE ESCOLTA EM BRANCO ).';
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );

    exit;

}

$q_det_esc = "SELECT
                `cod_detento`,
                DATE_FORMAT( `hora`, '%H:%i' ) AS `hora_f`,
                `cod_tipo`
              FROM
                `ordens_escolta_det`
              WHERE
                `id_escolta_det` = $idescdet
              LIMIT 1;";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_det_esc = $model->query( $q_det_esc );

// fechando a conexao
$model->closeConnection();

if ( !$q_det_esc ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Falha na consulta ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 'f' );
    exit;

}

$cont = $q_det_esc->num_rows;
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

$d_det_esc = $q_det_esc->fetch_assoc();

$iddet = $d_det_esc['cod_detento'];


$q_tipo_esc = 'SELECT
                 `id_tipo`,
                 `tipo`
               FROM
                 `ordens_escolta_tipo`
               ORDER BY
                 `tipo`';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_tipo_esc = $model->query( $q_tipo_esc );

// fechando a conexao
$model->closeConnection();

if ( !$q_tipo_esc ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Falha na consulta ( $motivo_pag - TIPO DE ESCOLTA ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 'f' );
    exit;

}

$cont = $q_tipo_esc->num_rows;
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

$desc_pag = 'Alterar tipo de escolta';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab_simp.php';
?>

            <p class="descript_page">ALTERAR TIPO DE ESCOLTA</p>

            <?php include 'quali/det_basic.php'; ?>

            <form action='<?php echo $_SERVER['PHP_SELF'];?>' method='post' name='edit_det_esc'>
                <table class="edit">
                    <tr>
                        <td>Hora:</td>
                        <td>
                            <input name="hora" type="text" class="CaixaTexto" id="hora" onblur="verifica_hora(this, this.value)" onkeypress="mascara_hora(this, this.value); return blockChars(event, 2);" value="<?php echo $d_det_esc['hora_f']; ?>" size="5" maxlength="5" />
                        </td>
                    </tr>
                    <tr>
                        <td>Tipo:</td>
                        <td>
                            <select name="tipo_esc" class="CaixaTexto" id="tipo_esc">
                                <option value="" >Selecione...</option>
                                <?php while ( $d_tipo_esc = $q_tipo_esc->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_tipo_esc['id_tipo']; ?>" <?php echo $d_tipo_esc['id_tipo'] == $d_det_esc['cod_tipo'] ? 'selected="selected"' : ''; ?>><?php echo $d_tipo_esc['tipo']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                </table>

                <input type="hidden" name="id_escolta_det" id="id_escolta_det" value="<?php echo $idescdet ?>" />

                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" value="Atualizar" />
                    <input class="form_bt" name="" type="button" onClick="self.window.close()" value="Cancelar" />
                </div>

            </form><!-- /form name='edit_det_esc' -->

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