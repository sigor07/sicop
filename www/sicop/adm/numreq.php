<?php
if ( !isset( $_SESSION ) ) session_start();

/*ob_start("ob_gzhandler");*/

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$nivel_necessario = 3;
$n_pec            = get_session( 'n_peculio', 'int' );

$motivo_pag = 'NÚMEROS DE REQUISIÇÃO DE PASSAGEM';

if ($n_pec < $nivel_necessario) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

//if (isset($_SESSION['num_of'])) unset($_SESSION['num_of']);
$iduser  = get_session( 'user_id', 'int' );
$idsetor = get_session( 'idsetor', 'int' );
$quant   = '';
$coment  = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    extract($_POST, EXTR_OVERWRITE);

    $quant  = (int)$quant;

    if (empty($quant)){
        header("Location: numreq.php");
        exit;
    } else if ($quant > 20){
        $quant = 20;
    }

    $coment = empty($coment) ? 'NULL' : "'" . tratastring($coment, 'U', false) . "'";

    $query_in = "INSERT INTO `numeroreq`
                            (`numero_req`, `ano`, `iduser`, `idsetor`, `coment`)
                            VALUES";

    $valores = "";

    for ($i=0; $i < $quant; ++$i) {
        $valores .= " ( (SELECT IFNULL(MAX(numreq.numero_req), 0) FROM numeroreq numreq WHERE ano = YEAR(NOW())) + 1, YEAR(NOW()), $iduser, $idsetor, $coment),";
    }

    $valores = substr($valores, 0, -1);

    $query_in .= $valores;

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_in = $model->query( $query_in );

    if( !$query_in ) {

        echo msg_js( 'FALHA!!!', 1 );
        exit;

    }

    $id = array();
    $id[] = $model->lastInsertId();

    // fechando a conexao
    $model->closeConnection();

    for ($i=0; $i < $quant; ++$i) {
        $id[] .= end($id) + 1;
    }

    $_SESSION['num_req'] = implode(', ', $id);

    header('Location: numreqok.php');
    exit;

}

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';
$trail = new Breadcrumb();
$trail->add('Solicitar números de requisição de passagens', $_SERVER['PHP_SELF'], 3);
$trail->output();
?>

            <p class="descript_page">SOLICITAR NÚMERO(S) PARA REQUISIÇÃO DE PASSAGENS</p>

            <form action="numreq.php" method="post" name="numreq" id="numreq" onSubmit="return validanum();">

                <table class="busca_form">
                    <tr>
                        <th width="179" align="right" scope="row" >Quantidade (max. 20):</th>
                        <td width="284"><input name="quant" type="text" class="CaixaTexto" id="quant" onkeypress="return blockChars(event, 2);" value="<?php echo $quant ?>" size="2" maxlength="2" /></td>
                    <script type="text/javascript">document.getElementById("quant").focus();</script>
                    </tr>
                    <tr>
                        <th scope="row" align="right">Descrição:</th>
                        <td><textarea name="coment" cols="50" rows="3" class="CaixaTexto" id="coment" onKeyPress="return blockChars(event, 4);"><?php echo $coment ?></textarea></td>
                    </tr>
                </table>

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="solicitar" id="solicitar" value="Solicitar" />
                    <input class="form_bt" type="button" name="" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

<?php include 'footer.php'; ?>

