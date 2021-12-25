<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$motivo_pag = 'CADASTRAMENTO DE FOTOS ESPECIAL DE ' . SICOP_DET_DESC_U;

$n_det_alt_foto   = get_session( 'n_det_alt_foto', 'int' );
$n_det_alt_foto_n = 1;
if ( $n_det_alt_foto < $n_det_alt_foto_n ) {

    // montar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ATEN );
    $msg->set_msg_pre_def( SM_NO_PERM );
    $msg->get_msg();

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$iddet = get_get( 'iddet', 'int' );
if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ATEN );
    $msg->set_msg_pre_def( SM_DIRECT_ACCESS );
    $msg->add_parenteses( "IDENTIFICADOR EM BRANCO - $motivo_pag" );
    $msg->get_msg();

    echo msg_js( '', 1 );

    exit;

}


// gerar a mensagem q será salva no log
$msg = sysmsg::create_msg();
$msg->set_msg_pre_def( SM_ACCESS_PAGE );
$msg->get_msg();

$desc_pag = 'Cadastrar fotos especiais';

// adicionando o javascript
$cab_js = array();
$cab_js[] = 'swfobject.js';
$cab_js[] = 'multiUpload.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>


            <script type="text/javascript">

                var uploader = new multiUpload('uploader', 'uploader_files', {
                    swf:             '<?php echo SICOP_ABS_PATH; ?>swf/multiUpload.swf',
                    script:          '<?php echo SICOP_ABS_PATH; ?>send/senddetimg.php',
                    expressInstall:  '<?php echo SICOP_ABS_PATH; ?>swf/expressInstall.swf',
                    multi:           true,
                    //data:            {proced: 4},
                    fileDescription: 'JPEG Images|JPEG, GIF and PNG Images',
                    fileExtensions:  '*.jpg;*.jpeg|*.jpg;*.jpeg;*.gif;*.png',
                    onAllComplete:   function()
                    {
                        uploader.clearUploadQueue();
                    }//,
//                    onComplete:   function(e) // retorna um objeto com informações
//                    {
//                            alert(e.data); // ou: console.log(e.data); caso use firebug
//                    }

                });

                function sendIt()
                {
                    var iddet = $('#iddet').val();

                    if (iddet.length) {

                        //alert ( iddet );
                        uploader.setData({
                            iddet: iddet,
                            proced: 4
                        });
                        uploader.startUpload();
                    }

                }

            </script>

            <p class="descript_page">CADASTRAR FOTOS ESPECIAIS DE <?php echo SICOP_DET_DESC_U; ?></p>

            <?php include 'quali/det_basic.php'; ?>

            <div style="margin: 10px auto; text-align: left; width: 900px;">

                <div id="uploader"></div>

                <br style="clear:both" />

                <p class="link_common" style="text-align: left;"><a href="javascript:sendIt();">Enviar</a> | <a href="javascript:uploader.clearUploadQueue();">Limpar</a></p>

                <br style="clear:both" />

                <div id="uploader_files"></div>

                <br style="clear:both" />

                <p class="link_common" style="text-align: left;"><a href="javascript:sendIt();">Enviar</a> | <a href="javascript:uploader.clearUploadQueue();">Limpar</a></p>

            </div>

            <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet; ?>" />




<?php include 'footer.php'; ?>