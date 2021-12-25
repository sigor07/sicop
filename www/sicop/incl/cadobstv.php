<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_incl   = get_session( 'n_incl', 'int' );
$n_incl_n = 3;

$targ = empty( $_GET['targ'] ) ? 0 : (int)$_GET['targ'];

if ( $targ == 1) {
    $botao_canc = 'self.window.close()';
    $botao_value = 'Fechar';
} else {
    $targ = 0;
    $botao_canc = 'history.go(-1)';
    $botao_value = 'Cancelar';
}

$motivo_pag = 'CADASTRAR OBSERVAÇÃO DE TV';

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

$idtv = get_get( 'idtv', 'int' );

if ( empty( $idtv ) ) {

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

$q_tv = "SELECT
            `detentos_tv`.`idtv`,
            `detentos_tv`.`cod_detento`,
            `detentos_tv`.`cod_cela`,
            `detentos_tv`.`marca_tv`,
            `detentos_tv`.`cor_tv`,
            `detentos_tv`.`polegadas`,
            `detentos_tv`.`lacre_1`,
            `detentos_tv`.`lacre_2`,
            `cela`.`cela`,
            `raio`.`raio`
          FROM
            `detentos_tv`
            LEFT JOIN `cela` ON `detentos_tv`.`cod_cela` = `cela`.`idcela`
            LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
          WHERE
            `detentos_tv`.`idtv` = $idtv
          LIMIT 1";


// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_tv = $model->query( $q_tv );

// fechando a conexao
$model->closeConnection();

if( !$q_tv ) {

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';
    echo msg_js( '', $ret );

    exit;

}

$cont_tv = $q_tv->num_rows;

if ( $cont_tv != 1 ) {

    $mensagem = "A consulta retornou 0 ocorrencias (TV).\n\n Página $pag";
    salvaLog($mensagem);

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';
    echo msg_js( '', $ret );

    exit;

}

$d_tv = $q_tv->fetch_assoc();

$iddet = $d_tv['cod_detento'];

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Cadastrar observação de TV';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

if ( $targ == 1 ) {
    require 'cab_simp.php';
} else {
    require 'cab.php';
}
//echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']
?>

            <p class="descript_page">NOVA OBSERVAÇÃO DE TV</p>

            <p class="table_leg">TV</p>

            <table class="lista_busca">
                <tr bgcolor="#FAFAFA">
                    <td width="179" height="20" align="center">Marca: <?php echo $d_tv['marca_tv'] ?></td>
                    <td width="180" align="center">Cor: <?php echo $d_tv['cor_tv'] ?></td>
                    <td width="179" align="center"><?php echo SICOP_RAIO ?>: <?php echo $d_tv['raio'] ?> - <?php echo SICOP_CELA ?>: <?php echo $d_tv['cela'] ?></td>
                    <td width="181" align="center">Lacres: <?php echo $d_tv['lacre_1'] ?> / <?php echo $d_tv['lacre_2'] ?></td>
                </tr>
            </table>

            <?php include 'quali/det_basic.php'; ?>

            <p class="table_leg">Observação:</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendtvobs.php" method="post" name="cadobs" id="cadobs" onSubmit="return validacadobstv()">

                <div align="center">
                    <textarea name="obs_tv" id="obs_tv" cols="75" rows="5" class="CaixaTexto" onkeypress="return blockChars(event, 4);"></textarea>
                </div>

                <script type="text/javascript">
                id('obs_tv').focus();
            /*            CKEDITOR.replace( 'obs_tv',
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

                <input name="idtv" type="hidden" id="idtv" value="<?php echo $d_tv['idtv'];?>" />
                <input name="iddet" type="hidden" id="iddet" value="<?php echo $iddet;?>" />
                <input name="targ" type="hidden" id="targ" value="<?php echo $targ;?>" />
                <input name="proced" type="hidden" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Cadastrar" />
                    <input class="form_bt" name="" type="button" onclick="<?php echo $botao_canc; ?>" value="Cancelar" />
                </div>

            </form>

<?php include 'footer.php'; ?>