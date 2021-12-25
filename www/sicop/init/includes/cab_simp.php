<?php
if ( !isset( $_SESSION ) ) session_start();

$titulo_cab    = get_session( 'titulo' );
$unidade_small = get_session( 'unidadecurto' );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="<?php echo SICOP_SYS_IMG_PATH; ?>favicon.ico" type="image/x-icon" />
        <title><?php if ( !empty( $desc_pag ) ) echo $desc_pag . ' | '; ?><?php echo $titulo_cab ?></title>
        <link href="<?php echo SICOP_ABS_PATH; ?>css/reset.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH; ?>css/estilo_wb.css" rel="stylesheet" type="text/css" />
        <?php //echo get_cab_css(); ?>

        <script type="text/javascript" src="<?php echo SICOP_ABS_PATH; ?>js/jquery.js"></script>
        <script type="text/javascript" src="<?php echo SICOP_ABS_PATH; ?>js/funcoes.js"></script>
        <?php echo get_cab_js(); ?>
    </head>
    <body onmouseover="window.status='<?php echo $unidade_small ?>';return true;">

        <input type="hidden" id="js_caminho" value="<?php echo SICOP_ABS_PATH; ?>" />
        <input type="hidden" id="js_caminho_img" value="<?php echo SICOP_IMG_PATH; ?>" />

        <div class="no_print">