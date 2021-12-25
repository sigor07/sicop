<?php
if ( !isset( $_SESSION ) ) session_start();

/*
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="../css/estilo_wb.css" rel="stylesheet" type="text/css" />
    </head>
<body onmouseover="window.status='<?php echo $unidadecurto ?>';return true;" scroll=yes>

*/
$titulo       = SicopController::getSession( 'titulo' );
$unidadecurto = SicopController::getSession( 'unidadecurto' );

$dir = '';

if ( file_exists( '../css/estilo.css' ) ) {
    $dir = '../';
}

//$caminho = $_SERVER['SERVER_NAME'] . '/sicop_test/';
//$http_pos  = mb_strpos( $caminho, 'http://' );
//if ( $http_pos === false ) $caminho = 'http://' . $caminho;


$useragent = $_SERVER['HTTP_USER_AGENT'];
$browser_version = 0;
$browser = 'other';
$matched = '';

if ( preg_match( '|MSIE ([0-9]{1,2})|', $useragent, $matched ) ) {

    $browser_version = $matched[1];
    $browser = 'IE';

} elseif ( preg_match( '|Opera/([0-9].[0-9]{1,2})|', $useragent, $matched ) ) {

    $browser_version = $matched[1];
    $browser = 'Opera';

} elseif ( preg_match( '|Firefox/([0-9\.]+)|', $useragent, $matched ) ) {

    $browser_version = $matched[1];
    $browser = 'Firefox';

} elseif ( preg_match( '|Chrome/([0-9\.]+)|', $useragent, $matched ) ) {

    $browser_version = $matched[1];
    $browser = 'Chrome';

} elseif ( preg_match( '|Safari/([0-9\.]+)|', $useragent, $matched ) ) {

    $browser_version = $matched[1];
    $browser = 'Safari';

}

$caminho = SICOP_ABS_PATH;
$caminho_img = SICOP_IMG_PATH;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="<?php echo SICOP_SYS_IMG_PATH; ?>favicon.ico" type="image/x-icon" />
        <title><?php if ( !empty( $desc_pag ) ) echo $desc_pag . ' | '; ?><?php echo $titulo ?></title>
        <?php include 'css.php'; ?>

        <?php if ( ( $browser == 'IE' and $browser_version > 7 ) or $browser != 'IE' ) { ?><link href="<?php echo $caminho; ?>css/estilo_bts.css" rel="stylesheet" type="text/css"  media="screen" />
<?php } ?>

        <?php include 'js.php'; ?>

        <noscript>
            <meta http-equiv="Refresh" content="0; url=<?php echo $caminho; ?>atualiza_java.php" />
        </noscript>

        <!--[if IE 6]>
        <script type="text/javascript">window.location.href = "<?php echo $caminho; ?>atualiza_nav.php";</script>
        <![endif]-->

        <!--
        *****************************************************************************

                      SICOP - Sistema de Controle de Prisional
          Criado e desenvolvido por JOSÉ RAFAEL GONÇALVES - AGENTE DE SEGURANÇA III

                 CENTRO DE DETENÇÃO PROVISÓRIA DE SÃO JOSÉ DO RIO PRETO - SP

              "ficar parado e não fazer nada, são duas coisas bem diferentes..."

        *****************************************************************************
        -->
    </head>

    <body onmouseover="window.status='<?php echo $unidadecurto ?>';return true;">

        <input type="hidden" id="js_caminho" value="<?php echo SICOP_ABS_PATH; ?>" />
        <input type="hidden" id="js_caminho_img" value="<?php echo SICOP_IMG_PATH; ?>" />

        <div class="no_print">

        <?php include 'menu.php'; ?>
