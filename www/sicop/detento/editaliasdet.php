<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_det_alias = get_session( 'n_det_alias', 'int' );
$n_alias_n   = 1;

$motivo_pag = 'ALTERAÇÃO DE ALIAS DE ' . SICOP_DET_DESC_U;

if ( $n_det_alias < $n_alias_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idalias = get_get( 'idalias', 'int' );

if ( empty( $idalias ) ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( IDENTIFICADOR EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$q_alias_det = "SELECT
                  `aliases`.`idalias`,
                  `aliases`.`cod_detento`,
                  `aliases`.`alias_det`,
                  `aliases`.`cod_tipoalias` AS idaliasdet,
                  `tipoalias`.`tipoalias`
                FROM
                  `aliases`
                  INNER JOIN `tipoalias` ON `aliases`.`cod_tipoalias` = `tipoalias`.`idtipoalias`
                WHERE
                  `aliases`.`idalias` = $idalias
                LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_alias_det = $model->query( $q_alias_det );

// fechando a conexao
$model->closeConnection();

if( !$q_alias_det ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_alias = $q_alias_det->num_rows;

if( $cont_alias < 1 ) {

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( $motivo_pag ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_alias = $q_alias_det->fetch_assoc();

$iddet = $d_alias['iddetento'];

$q_tipo_alias = 'SELECT `idtipoalias`,`tipoalias` FROM `tipoalias` ORDER BY `tipoalias` ASC';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_tipo_alias = $model->query( $q_tipo_alias );

// fechando a conexao
$model->closeConnection();

if( !$q_tipo_alias ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_tipo_alias = $q_tipo_alias->num_rows;

if( $cont_tipo_alias < 1 ) {

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( $motivo_pag ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Alterar alias';

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 5 );
$trail->output();
?>

            <p class="descript_page">ALTERAR ALIAS</p>

            <?php include 'quali/det_basic.php'; ?>

            <form action="<?php echo SICOP_ABS_PATH ?>send/senddetalias.php" method="post" name="editaliasdet" id="editaliasdet">

                <table class="edit">
                    <tr>
                        <td width="90">Tipo de alias:</td>
                        <td width="325">
                            <select name="tipoalias" class="CaixaTexto" id="tipoalias">
                                <option value="" >Selecione...</option>
                                <?php while ( $d_tipo_alias = $q_tipo_alias->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_tipo_alias['idtipoalias'];?>"  <?php echo $d_tipo_alias['idtipoalias'] == $d_alias['idaliasdet'] ? 'selected="selected"' : ''; ?>><?php echo $d_tipo_alias['tipoalias'];?></option>
                                <?php };?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Alias:</td>
                        <td><textarea name="alias_det" id="alias_det" cols="60" rows="3" class="CaixaTexto" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);" onkeydown="textCounter(this, 245);" onkeyup="textCounter(this, 245);"><?php echo $d_alias['alias_det'];?></textarea></td>
                    </tr>
                </table>

                <script type="text/javascript">id("alias_det").focus();</script>

                <input type="hidden" name="id_det" id="id_det" value="<?php echo $iddet; ?>" />
                <input type="hidden" name="id_alias" id="id_alias" value="<?php echo $d_alias['idalias']; ?>" />
                <input type="hidden" name="proced" id="proced" value="1" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" onclick="return validacadaliasdet();" value="Alterar" />
                    <input class="form_bt" type="button" name="" onClick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

<?php include 'footer.php'; ?>