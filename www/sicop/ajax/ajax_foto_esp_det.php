<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$tipo_pag = 'IMPRESSÕES DE DETENTO - AJAX';

// instanciando a classe
$sys = new SicopController();

// checando se o sistema esta ativo
$sys->ckSys();

// instanciando a classe
$user = new userAutController();

// validando o usuário e o nível de acesso
$user->validateUser( 'n_det_alt_foto', 1, '', 5 );

// checando se o acesso foi via post
$sys->ckPost( 4 );


$op = array(
    'method'         => 'post',       // metodo que a variável será recebida
    'name'           => 'uid',        // nome da variável
    'modo_validacao' => 'int',        // modo de validação
    'minLeng'        => 1,            // comprimento mínimo
    'required'       => true,         // se é requerida ou não
    'zero_ok'        => false,        // em caso de requerida, se pode ser o número 0 (zero)
    'return_type'    => 4             // tipo de retorno em caso de erro
);
$iddet = $sys->validate( $op );

// adicionando o javascript
$cab_js = array();
$cab_js[] = 'swfobject.js';
$cab_js[] = 'multiUpload.js';
set_cab_js( $cab_js );

header( 'Content-Type: text/html; charset=utf-8' );

//Evitando cache de arquivo
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0' );
header( 'Pragma: no-cache' );
header( 'Expires: 0' );

echo get_cab_js();

?>
<div class="form_ajax">

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

</div>