<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
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
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página de alteração de dados da localidade da lista telefonica.\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( '', 1 );
    exit;
}

$q_local_tel = "SELECT
                  `idlistatel`,
                  `tel_local`,
                  `tel_end`,
                  `tel_cep`,
                  `tel_codmin`,
                  `tel_diretor`
                FROM
                  `listatel`
                WHERE
                  `idlistatel` = $idlt";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_local_tel = $model->query( $q_local_tel );

// fechando a conexao
$model->closeConnection();

if( !$q_local_tel ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_q_local_tel = $q_local_tel->num_rows;

if( $cont_q_local_tel < 1 ) {

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( EDIÇÃO DE LOCALIDADE DA LISTA TELEFÔNICA ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_lt = $q_local_tel->fetch_assoc();

$cep_f = '';
if ( !empty( $d_lt['tel_cep'] ) ) {

    $formata_cep = new FormataString();
    $cep_f = $formata_cep->getCEP( $d_lt['tel_cep'] );

}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Alterar dados da localidade';

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

            <p class="descript_page">ALTERAR LOCALIDADE DA LISTA TELEFÔNICA</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendlistatel.php" method="post" name="edit_lt">

                <table class="edit">
                    <tr>
                        <td class="lista_tel_leg">Localidade:</td>
                        <td class="lista_tel_field">
                            <input name="tel_local" type="text" class="CaixaTexto" id="tel_local" onblur="upperMe(this);" onkeypress="return blockChars(event, 4);" value="<?php echo $d_lt['tel_local']; ?>" size="80" maxlength="150" />
                        </td>
                    </tr>
                    <tr>
                        <td class="lista_tel_leg">Endereço:</td>
                        <td class="lista_tel_field">
                            <textarea style="width: 400px;" name="tel_end" id="tel_end" cols="80" rows="3" class="CaixaTexto" onblur="upperMe(this);" onkeypress="return blockChars(event, 4);"><?php echo $d_lt['tel_end']; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="lista_tel_leg">CEP:</td>
                        <td class="lista_tel_field">
                            <input name="tel_cep" type="text" class="CaixaTexto" id="tel_cep" onkeypress="mascara(this, mcep);return blockChars(event, 2);" value="<?php echo $cep_f; ?>" size="11" maxlength="9" />
                        </td>
                    </tr>
                    <tr>
                        <td class="lista_tel_leg">Código minemônico:</td>
                        <td class="lista_tel_field">
                            <input name="tel_codmin" type="text" class="CaixaTexto" id="tel_codmin" onblur="upperMe(this);" onkeypress="return blockChars(event, 3);" value="<?php echo $d_lt['tel_codmin']; ?>" size="12" maxlength="10" />
                        </td>
                    </tr>
                    <tr>
                        <td class="lista_tel_leg">Diretor:</td>
                        <td class="local_lt_menor">
                            <input name="tel_diretor" type="text" class="CaixaTexto" id="tel_diretor" onblur="upperMe(this);" onkeypress="return blockChars(event, 4);" value="<?php echo $d_lt['tel_diretor']; ?>" size="80" maxlength="100" />
                        </td>
                    </tr>
                </table><!-- fim da <table class="edit"> -->

                <input type="hidden" name="idlt_local" value="<?php echo $idlt; ?>" />
                <input type="hidden" name="proced" value="2" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" onclick="return valida_listatel(2);" value="Atualizar" />
                    <input class="form_bt" type="button" name="" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form><!-- fim do <form method="post" name="edit_lt"> -->

            <script type="text/javascript">id("tel_local").focus()</script>

<?php include 'footer.php'; ?>