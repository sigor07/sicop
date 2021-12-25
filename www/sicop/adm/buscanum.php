<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag          = link_pag();
$tipo         = 0;
$img_sys_path = SICOP_SYS_IMG_PATH;
$motivo_pag   = 'CONSULTA DE NÚMEROS';

$n_prot   = get_session( 'n_prot', 'int' );
$n_prot_n = 2;

if ( $n_prot < $n_prot_n ) {

    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    exit;

}

$user = get_session( 'buscanum_user', 'int' );
$num  = get_session( 'buscanum_num', 'int' );
$ano  = get_session( 'buscanum_ano', 'int' );
$tipo = get_session( 'buscanum_tipo', 'int' );

$q_user = "SELECT `iduser`, `nome_cham` FROM `sicop_users` ORDER BY `nome_cham`";

$db = SicopModel::getInstance();
$q_user = $db->query( $q_user );

if ( !$q_user ) {

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();
    $db->closeConnection();

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Falha na consulta ( $motivo_pag ).\n\n $msg_err_mysql";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$cont = $q_user->num_rows;
if ( $cont < 1 ) {


    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "A consulta retornou 0 ocorrências ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );


    echo msg_js( 'FALHA!', 1 );
    exit;

}


$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Pesquisar números solicitados';

// adicionando o javascript
$cab_js   = array();
$cab_js[] = 'ajax/ajax_buscanum.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>

            <p class="descript_page">PESQUISAR NÚMERO(S)</p>

            <form name="form_buscanum" id="form_buscanum">

                <input type="hidden" id="h_user" value="<?php echo $user; ?>" />
                <input type="hidden" id="h_num" value="<?php echo $num; ?>" />
                <input type="hidden" id="h_ano" value="<?php echo $ano; ?>" />
                <input type="hidden" id="h_tipo" value="<?php echo $tipo; ?>" />

                <table class="busca_form">
                    <tr>
                        <td class="bf_legend">Usuário:</td>
                        <td class="bf_field">
                            <select name="user" class="CaixaTexto" id="user">
                                <option value="" >Selecione...</option>
                                <?php while ( $d_user = $q_user->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_user['iduser']; ?>"><?php echo $d_user['nome_cham']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="bf_legend">Número / ano:</td>
                        <td class="bf_field">
                            <input name="num" type="text" class="CaixaTexto" id="num" onkeypress="return blockChars(event, 2);" value="<?php echo $num ?>" size="6" maxlength="5" />/<input name="ano" type="text" class="CaixaTexto" id="ano" onkeypress="return blockChars(event, 2);" value="<?php echo $ano ?>" size="5" maxlength="4" />
                        </td>
                    </tr>
                    <tr>
                        <td class="bf_legend">Tipo:</td>
                        <td class="bf_field">
                            <select name="tipo" class="CaixaTexto" id="tipo">
                                <option value="" >Selecione...</option>
                                <option value="1" >Ofício</option>
                                <option value="2" >Fax</option>
                                <option value="3" >Remessa</option>
                                <option value="4" >Notes</option>
                                <option value="5" >Requisição de passagem</option>
                            </select>
                        </td>
                    </tr>
                </table><!-- /table class="busca_form" -->

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="buscar" id="buscar" value="Buscar" />
                </div>

                <input type="hidden" name="busca" id="busca" value="busca" />

            </form>

            <div id="cont" style="margin-top: 10px"></div>

<?php include 'footer.php';?>