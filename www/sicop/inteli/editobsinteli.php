<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_inteli   = get_session( 'n_inteli', 'int' );
$n_inteli_n = 3;

$motivo_pag = 'ALTERAÇÃO DE OBSERVAÇÃO - INTELIGÊNCIA';

if ( $n_inteli < $n_inteli_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idobs = get_get( 'idobs', 'int' );
if ( empty( $idobs ) ) {


    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( IDENTIFICADOR EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );


    echo msg_js( '', 1 );


    exit;

}

$query_obs = "SELECT
                `id_obs_inteli`,
                `cod_inteli`,
                `obs_inteli`
              FROM
                `obs_inteli`
              WHERE
                `id_obs_inteli` = $idobs
              LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_obs = $model->query( $query_obs );

// fechando a conexao
$model->closeConnection();

if( !$query_obs ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_obs = $query_obs->num_rows;

if($cont_obs < 1) {
    $mensagem = "A consulta retornou 0 ocorrencias (observações).\n\n Página $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$d_obs = $query_obs->fetch_assoc();

$idinteli = $d_obs['cod_inteli'];

$q_inteli = "SELECT `cod_detento` FROM `inteligencia` WHERE `idinteli` = $idinteli";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_inteli = $model->query( $q_inteli );

// fechando a conexao
$model->closeConnection();

if( !$q_inteli ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_inteli = $q_inteli->num_rows;

if( $cont_inteli < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrencias ( $motivo_pag ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$d_inteli = $q_inteli->fetch_assoc();
$iddet = $d_inteli['cod_detento'];

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

// adicionando o javascript
$cab_js   = array();
$cab_js[] = 'valida.js';
$cab_js[] = 'ckeditor/ckeditor.js';
set_cab_js( $cab_js );

require 'cab.php';
?>

    <p class="descript_page">ALTERAR OBSERVAÇÃO DA INTELIGÊNCIA</p>

    <?php include 'quali/det_basic.php'; ?>

    <p class="table_leg">Observações:</p>

    <form action="<?php echo SICOP_ABS_PATH ?>send/sendinteliobs.php" method="post" name="editobs" id="editobs" onSubmit="return validacadobsdet()">

        <div align="center"><textarea name="obs_inteli" cols="75" rows="5" class="CaixaTexto" id="obs_inteli" onkeypress="return blockChars(event, 4);"><?php echo $d_obs['obs_inteli'];?></textarea></div>

        <script type="text/javascript">
            CKEDITOR.replace( 'obs_inteli',
            {

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
        <input name="id_obs_inteli" type="hidden" id="id_obs_inteli" value="<?php echo $d_obs['id_obs_inteli'];?>" />
        <input name="proced" type="hidden" id="proced" value="1" />

        <div class="form_bts">
            <input class="form_bt" type="submit" name="submit" id="submit" value="Alterar" />
            <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
        </div>

    </form>

<?php include 'footer.php'; ?>