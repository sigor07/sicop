<?php
if ( !isset( $_SESSION ) ) session_start();

$unidadecurto = '';
$titulo = '';

if ( !isset( $_SESSION['login_count'] ) ) {
    $_SESSION['login_count'] = 1;
}

require 'init/config.php';
require 'funcoes_init.php';
require 'layout.php';

$sys_out = get_get( 'sys_out', 'int' );

if ( empty( $sys_out ) ) {

    ck_sys();

    $pag = link_pag();

    $mensagem = "Acesso à página $pag";
    salvaLog( $mensagem );

}

/*$ch = curl_init($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
$fp = fopen("example_homepage.txt", "w");

curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_HEADER, 0);

curl_exec($ch);
curl_close($ch);
fclose($fp);*/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <?php if ( !empty( $sys_out ) ) { ?>
        <meta http-equiv="Refresh" content="60;url=index.php" />
        <?php } ?>
        <link rel="shortcut icon" href="<?php echo SICOP_SYS_IMG_PATH; ?>favicon.ico" type="image/x-icon" />
        <title><?php echo $titulo ?></title>
        <link href="css/estilo.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/valida.js"></script>
        <!--
        *****************************************************************************

                      SICOP - Sistema de Controle de Presos Provisórios
           Criado e desenvolvido por JOSÉ RAFAEL GONÇALVES - AGENTE DE SEGURANÇA II

                 CENTRO DE DETENÇÃO PROVISÓRIA DE SÃO JOSÉ DO RIO PRETO - SP

              "ficar parado e não fazer nada, são duas coisas bem diferentes..."

        *****************************************************************************
        -->
    </head>

    <body class="index" onmouseover="window.status='<?php echo $unidadecurto ?>';return true;" >
        <p align="center"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>brasao.png" alt="" width="113" height="124" /></p>
        <p align="center" class="par_forte_index">SICOP</p>
        <p align="center" class="par_nor_index">Sistema de Controle Prisional</p>
        <p align="center" class="par_nor_index"><?php echo $unidadecurto ?></p>

        <?php if ( empty( $sys_out ) ) { ?>
        <form action="login.php" method="post" name="frm_login" onSubmit="javascript: return validaIndex();">

            <div class="login">
                <p align="center">Informe seu LOGIN e SENHA:</p>

                <table class="login">
                    <tr>
                        <td class="leg">Login:</td>
                        <td class="field">
                            <input name="login" type="text" class="CaixaTexto" id="login" size="15" maxlength="25" autocomplete="off" />
                        </td>
                    </tr>
                    <tr>
                        <td class="leg">Senha:</td>
                        <td class="field"><input name="senha" type="password" class="CaixaTexto" id="senha" size="15" maxlength="15" /></td>
                    </tr>
                </table>

                <div class="form_bts">
                    <input class="form_bt" name="logar" type="submit" value="Entrar" />
                </div>

            </div>

            <script type="text/javascript">

                $(function() {
                    $( "#login" ).focus();
                });

            </script>

        </form>
        <?php } else { ?>
        <p align="center" class="par_forte_index">EM MANUTENÇÃO!</p>
        <p align="center" class="par_nor_index">
            Caro usuário,<br /><br />
            o sistema encontra-se em manutenção e deverá retornar em instantes.
        </p>
        <p align="center" class="par_nor_index">
            Permaneça nesta página para que seja feita a verificação automática, se o sistema retornou.<br /><br />
            <a href="index.php" title="Verificar agora se o sistema retornou">Verificar agora</a>
        </p>
        <?php } ?>
    </body>
</html>
