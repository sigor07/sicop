<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_admsist = get_session( 'n_admsist', 'int' );

if ( empty( $n_admsist ) or $n_admsist < 3 ) {
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página SEM PERMISSÕES. \n\n Página: $pag";
    salvaLog($mensagem);
    exit;
}

$idlt = get_get( 'idlt', 'int' );

if ( empty( $idlt ) ){
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página de alteração de dados do número da lista telefonica.\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( '', 1 );
    exit;
}

$q_local_tel = "SELECT
                  `listatel`.`tel_local`
                FROM
                  `listatel`
                WHERE
                  `listatel`.`idlistatel` = $idlt";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_local_tel = $model->query( $q_local_tel );

// fechando a conexao
$model->closeConnection();

if( !$q_local_tel ) {

    echo msg_js( '', 1 );
    exit;

}

$cont_q_local_tel = $q_local_tel->num_rows;

if( $cont_q_local_tel < 1 ) {

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( CADASTRAMENTO DE NÚMERO NA LISTA TELEFÔNICA ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( '', 1 );
    exit;

}

$d_lt = $q_local_tel->fetch_assoc();

$redir = get_get( 'redir', 'int' );

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Cadastrar número';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) {
    $pag_atual .=  '?' . $qs;
}

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 5 );
$trail->output();
?>

            <p class="descript_page">CADASTRAR NÚMERO</p>

            <table class="detal_lt">
                <tr>
                    <td class="local_lt_med">Localidade: <?php echo $d_lt['tel_local']; ?></td>
                </tr>
            </table><!-- fim da <table class="detal_lt"> -->

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendlistatel.php" method="post" name="edit_lt">

                <table class="edit">
                    <tr>
                        <td class="lista_tel_leg">
                            Número:
                        </td>
                        <td class="lista_tel_num_field">
                            <input name="ltn_num" type="text" class="CaixaTexto" id="ltn_num" onblur="upperMe(this);" onkeypress="mascara(this, mtel);return blockChars(event, 2);" value="" size="16" maxlength="14" />
                        </td>
                    </tr>
                    <tr>
                        <td class="lista_tel_leg">
                            Ramal:
                        </td>
                        <td class="lista_tel_num_field">
                            <input name="ltn_ramal" type="text" class="CaixaTexto" id="ltn_ramal" onkeypress="mascara(this, mcep);return blockChars(event, 2);" value="" size="6" maxlength="5" />
                        </td>
                    </tr>
                    <tr>
                        <td class="lista_tel_leg">
                            Descrição:
                        </td>
                        <td class="lista_tel_num_field">
                            <input name="ltn_desc" type="text" class="CaixaTexto" id="ltn_desc" onblur="upperMe(this);" onkeypress="return blockChars(event, 4);" value="" size="33" maxlength="30" />
                        </td>
                    </tr>
                </table><!-- fim da <table class="edit"> -->

                <input type="hidden" name="idlt" value="<?php echo $idlt; ?>" />
                <input type="hidden" name="redir" value="<?php echo $redir; ?>" />
                <input type="hidden" name="proced" value="5" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" onclick="return valida_listatel(1);" value="Cadastrar" />
                    <input class="form_bt" type="submit" name="cadadd" onclick="return valida_listatel(1);" value="Cadastrar e adicionar outro" />
                    <input class="form_bt" type="button" name="" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form><!-- fim do form name="edit_lt" -->

            <script type="text/javascript">id("ltn_num").focus()</script>

<?php include 'footer.php'; ?>