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

$idnt = get_get( 'idnt', 'int' );

if ( empty( $idnt ) ){
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página de alteração de dados do número da lista telefonica.\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$q_num_tel = "SELECT
                `listatel_num`.`ltn_num`,
                `listatel_num`.`ltn_ramal`,
                `listatel_num`.`ltn_desc`,
                `listatel`.`tel_local`
              FROM
                `listatel_num`
                INNER JOIN `listatel` ON `listatel_num`.`cod_listatel` = `listatel`.`idlistatel`
              WHERE
                `listatel_num`.`idlistatel_num` = $idnt";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_num_tel = $model->query( $q_num_tel );

// fechando a conexao
$model->closeConnection();

if( !$q_num_tel ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_q_num_tel = $q_num_tel->num_rows;

if( $cont_q_num_tel < 1 ) {

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( EDIÇÃO DE NÚMERO DA LISTA TELEFÔNICA ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_nt = $q_num_tel->fetch_assoc();

$tel_f = '';
if ( !empty( $d_nt['ltn_num'] ) ) {

    $formata_tel = new FormataString();
    $tel_f = $formata_tel->getTelefone( $d_nt['ltn_num'] );

}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Alterar número';

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

            <p class="descript_page">ALTERAR NÚMERO</p>

            <table class="detal_lt">
                <tr>
                    <td class="local_lt_med">Localidade: <?php echo $d_nt['tel_local']; ?></td>
                </tr>
            </table><!-- fim da <table class="detal_lt"> -->

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendlistatel.php" method="post" name="edit_lt">

                <table class="edit">
                    <tr>
                        <td class="lista_tel_leg">Número:</td>
                        <td class="lista_tel_num_field">
                            <input name="ltn_num" type="text" class="CaixaTexto" id="ltn_num" onblur="upperMe(this);" onkeypress="mascara(this, mtel);return blockChars(event, 2);" value="<?php echo $tel_f; ?>" size="16" maxlength="14" />
                        </td>
                    </tr>
                    <tr>
                        <td class="lista_tel_leg">Ramal:</td>
                        <td class="lista_tel_num_field">
                            <input name="ltn_ramal" type="text" class="CaixaTexto" id="ltn_ramal" onkeypress="mascara(this, mcep);return blockChars(event, 2);" value="<?php echo $d_nt['ltn_ramal']; ?>" size="6" maxlength="5" />
                        </td>
                    </tr>
                    <tr>
                        <td class="lista_tel_leg">Descrição:</td>
                        <td class="lista_tel_num_field">
                            <input name="ltn_desc" type="text" class="CaixaTexto" id="ltn_desc" onblur="upperMe(this);" onkeypress="return blockChars(event, 4);" value="<?php echo $d_nt['ltn_desc']; ?>" size="33" maxlength="30" />
                        </td>
                    </tr>
                </table><!-- fim da <table class="edit"> -->

                <input type="hidden" name="idnt" value="<?php echo $idnt; ?>" />
                <input type="hidden" name="proced" value="1" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" onclick="return valida_listatel(1);" value="Atualizar" />
                    <input class="form_bt" type="button" name="" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form><!-- fim do form method="post" name="edit_lt" -->

            <script type="text/javascript">id("ltn_num").focus()</script>

<?php include 'footer.php'; ?>