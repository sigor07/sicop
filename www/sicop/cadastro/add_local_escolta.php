<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag         = link_pag();
$tipo        = '';

$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_cad_n    = 3;

if ( $n_cadastro < $n_cad_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'CADASTRAMENTO DE LOCALIDADE DE ESCOLTA E ORDEM DE SAÍDA';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$is_post = is_post();
if ( $is_post ) {

    extract( $_POST, EXTR_OVERWRITE );

    $user = get_session( 'user_id', 'int' );
    $ip   = "'" . $_SERVER['REMOTE_ADDR'] . "'";

    $local_apr = empty( $local_apr ) ? '' : tratastring( $local_apr, 'U', FALSE );

    if ( empty( $local_apr ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Local de apresentação em branco. Operação cancelada ( CADASTAMENTO DE LOCAL DE APRESENTAÇÃO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'Local de apresentação não pode ficar em branco!', 1 );

        exit;

    }

    $local_apr = "'" . $local_apr . "'";

    $local_end = empty( $local_end ) ? 'NULL' : "'" . tratastring( $local_end, 'U', FALSE ) . "'";

    $query_add = "INSERT INTO
                    `locais_apr`
                    (
                      `local_apr`,
                      `local_end`,
                      `user_add`,
                      `data_add`,
                      `ip_add`
                    )
                  VALUES
                    (
                      $local_apr,
                      $local_end,
                      $user,
                      NOW(),
                      $ip
                    )";

    $success = TRUE;
    $mensagem = '';

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_add = $model->query( $query_add );

    // fechando a conexao
    $model->closeConnection();

    if( $query_add ) {

        $msg = array();
        $msg['tipo']     = 'desc';
        $msg['entre_ch'] = 'CADASTRO DE LOCALIDADE DE APRESENTAÇÃO';
        $msg['text']     = "Cadastro de localidade de apresentação para escolta e ordem de saída. \n\n [ LOCALIDADE ] \n $local_apr ";

        $mensagem = get_msg( $msg );

    } else {

        $success = FALSE;

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de cadastramento de localidade de apresentação. \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        $mensagem = get_msg( $msg );

    }

    salvaLog( $mensagem );

    $num_ret = 2;
    if ( isset( $cadadd ) ) $num_ret = 1;

    $msg = '';
    if ( !$success ) $msg = 'FALHA!!!';

    echo msg_js( $msg, $num_ret );

    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Cadastrar local de apresentação';

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) )
    $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>

            <p class="descript_page">CADASTRAR LOCALIDADES PARA ESCOLTA E ORDEM DE SAÍDA</p>

            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="cadloc" id="cadloc">
                <table class="edit">
                    <tr>
                        <td class="add_local_esc_leg">Localidade:</td>
                        <td class="add_local_esc_field"><input name="local_apr" type="text" class="CaixaTexto" id="local_apr" onkeypress="return blockChars(event, 4);" size="60" maxlength="50" /></td>
                    </tr>
                    <tr>
                        <td class="add_local_esc_leg">Endereço:</td>
                        <td class="add_local_esc_field"><input name="local_end" type="text" class="CaixaTexto" id="local_end" onkeypress="return blockChars(event, 4);" size="60" maxlength="50" /></td>
                    </tr>
                </table>

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="cadloc" value="Cadastrar" onclick="return valida_escolta(3);" />
                    <input class="form_bt" type="submit" name="cadadd" value="Cadastrar e adicionar outra" onclick="return valida_escolta(3);" />
                    <input class="form_bt" type="button" name="" onClick="history.go(-1)" value="Cancelar" />
                </div>

            </form><!-- fim do form id="cadloc" -->

            <script type="text/javascript">id("local_apr").focus();</script>

<?php include 'footer.php'; ?>