<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_inteli   = get_session( 'n_inteli', 'int' );
$n_inteli_n = 3;

$targ = empty( $_GET['targ'] ) ? '0' : (int)$_GET['targ'];

if ( $targ == 1) {
    $botao_canc = 'self.window.close()';
    $botao_value = 'Fechar';
} else {
    $targ = '0';
    $botao_canc = 'history.go(-1)';
    $botao_value = 'Cancelar';
}

$motivo_pag = 'CADASTRAR OBSERVAÇÃO DA INTELIGÊNCIA';

if ( $n_inteli < $n_inteli_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', $ret );

    exit;

}

$idinteli = empty( $_GET['idinteli'] ) ? '' : (int)$_GET['idinteli'];

if ( empty( $idinteli ) ) {

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

$q_inteli = "SELECT `cod_detento` FROM `inteligencia` WHERE `idinteli` = $idinteli";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_inteli = $model->query( $q_inteli );

// fechando a conexao
$model->closeConnection();

if( !$q_inteli ) {

    echo msg_js( '', 1 );
    exit;

}

$cont_inteli = $q_inteli->num_rows;

if( $cont_inteli < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrencias (CADASTRO DE OBSERVAÇÃO DE INTELIGÊNCIA).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( '', 1 );
    exit;
}

$d_inteli = $q_inteli->fetch_assoc();
$iddet = $d_inteli['cod_detento'];

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Cadastrar observação da inteligência';

// adicionando o javascript
$cab_js   = array();
$cab_js[] = 'valida.js';
$cab_js[] = 'ckeditor/ckeditor.js';
set_cab_js( $cab_js );

if ( $targ == 1 ) {
    require 'cab_simp.php';
} else {
    require 'cab.php';
}
//echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']
?>

    <p class="descript_page">NOVA OBSERVAÇÃO DA INTELIGÊNCIA</p>

    <?php include 'quali/det_basic.php'; ?>

    <p class="table_leg">Observações:</p>

    <form action="<?php echo SICOP_ABS_PATH ?>send/sendinteliobs.php" method="post" name="cadobs" id="cadobs" onSubmit="return validacadobsdet()">

        <div align="center">
            <textarea name="obs_inteli" id="obs_inteli" cols="75" rows="5" class="CaixaTexto" onkeypress="return blockChars(event, 4);"></textarea>
        </div>

        <script type="text/javascript">
            CKEDITOR.replace( 'obs_inteli',
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
        </script>

        <input name="idinteli" type="hidden" id="idinteli" value="<?php echo $idinteli;?>" />
        <input name="targ" type="hidden" id="targ" value="<?php echo $targ;?>" />
        <input name="proced" type="hidden" id="proced" value="3" />

        <div class="form_bts">
            <input class="form_bt" type="submit" name="submit" id="submit" value="Cadastrar" />
            <input class="form_bt" name="" type="button" onclick="<?php echo $botao_canc; ?>" value="Cancelar" />
        </div>

    </form>

<?php include 'footer.php'; ?>