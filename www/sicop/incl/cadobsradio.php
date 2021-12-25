<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_incl   = get_session( 'n_incl', 'int' );
$n_incl_n = 3;

$targ = empty( $_GET['targ'] ) ? '0' : (int)$_GET['targ'];

if ( $targ == 1) {
    $botao_canc = 'self.window.close()';
    $botao_value = 'Fechar';
} else {
    $targ = '0';
    $botao_canc = 'history.go(-1)';
    $botao_value = 'Cancelar';
}

$motivo_pag = 'CADASTRAR OBSERVAÇÃO DE RÁDIO';

if ( $n_incl < $n_incl_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', $ret );

    exit;

}

$idradio = get_get( 'idradio', 'int' );

if ( empty( $idradio ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';
    echo msg_js( '', $ret );

    exit;

}

$q_radio = "SELECT
            `detentos_radio`.`idradio`,
            `detentos_radio`.`cod_detento`,
            `detentos_radio`.`cod_cela`,
            `detentos_radio`.`marca_radio`,
            `detentos_radio`.`cor_radio`,
            `detentos_radio`.`lacre_1`,
            `detentos_radio`.`lacre_2`,
            `cela`.`cela`,
            `raio`.`raio`
          FROM
            `detentos_radio`
            LEFT JOIN `cela` ON `detentos_radio`.`cod_cela` = `cela`.`idcela`
            LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
          WHERE
            `detentos_radio`.`idradio` = $idradio
          LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_radio = $model->query( $q_radio );

// fechando a conexao
$model->closeConnection();

if( !$q_radio ) {

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';
    echo msg_js( '', $ret );

    exit;

}

$cont_radio = $q_radio->num_rows;

if ( $cont_radio != 1 ) {

    $mensagem = "A consulta retornou 0 ocorrencias (RÁDIO).\n\n Página $pag";
    salvaLog($mensagem);

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';
    echo msg_js( '', $ret );

    exit;

}

$d_radio = $q_radio->fetch_assoc();

$iddet = $d_radio['cod_detento'];

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Cadastrar observação de rádio';


// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

if ( $targ == 1 ) {
    require 'cab_simp.php';
} else {
    require 'cab.php';
}
?>

            <p class="descript_page">NOVA OBSERVAÇÃO DE RÁDIO</p>

            <p class="table_leg">RÁDIO</p>

            <table class="lista_busca">
                <tr bgcolor="#FAFAFA">
                    <td width="179" height="20" align="center">Marca: <?php echo $d_radio['marca_radio'] ?></td>
                    <td width="180" align="center">Cor: <?php echo $d_radio['cor_radio'] ?></td>
                    <td width="179" align="center"><?php echo SICOP_RAIO ?>: <?php echo $d_radio['raio'] ?> - <?php echo SICOP_CELA ?>: <?php echo $d_radio['cela'] ?></td>
                    <td width="181" align="center">Lacres: <?php echo $d_radio['lacre_1'] ?> / <?php echo $d_radio['lacre_2'] ?></td>
                </tr>
            </table>

            <?php include 'quali/det_basic.php'; ?>

            <p class="table_leg">Observação:</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendradioobs.php" method="post" name="cadobs" id="cadobs" onSubmit="return validacadobsradio()">

                <div align="center">
                    <textarea name="obs_radio" id="obs_radio" cols="75" rows="5" class="CaixaTexto" onkeypress="return blockChars(event, 4);"></textarea>
                </div>

                <script type="text/javascript">
                id('obs_radio').focus();
            /*            CKEDITOR.replace( 'obs_radio',
                    {

                        toolbar : 'MyToolbar_ws',

                        on :
                            {
                            instanceReady : function( ev )
                            {
                                // Output paragraphs as <p>Text</p>.
                                this.dataProcessor.writer.setRules( 'p',
                                {
                                    indent : false,
                                    breakBeforeOpen : false,
                                    breakAfterOpen : false,
                                    breakBeforeClose : false,
                                    breakAfterClose : false
                                });
                            }
                        }


                        //toolbar : 'Basic',
                        //uiColor : '#9AB8F3'
                    });
            */        </script>

                <input name="idradio" type="hidden" id="idradio" value="<?php echo $d_radio['idradio'];?>" />
                <input name="iddet" type="hidden" id="iddet" value="<?php echo $iddet;?>" />
                <input name="targ" type="hidden" id="targ" value="<?php echo $targ;?>" />
                <input name="proced" type="hidden" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Cadastrar" />
                    <input class="form_bt" name="" type="button" onclick="<?php echo $botao_canc; ?>" value="Cancelar" />
                </div>

            </form>

<?php include 'footer.php'; ?>