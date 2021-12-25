<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    extract($_POST, EXTR_OVERWRITE);

    $targ      = empty($targ) ? 0 : 1;
    $user      = get_session( 'user_id', 'int' );
    $ip        = "'" . $_SERVER['REMOTE_ADDR'] . "'";


    if ( empty( $idpec ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n O usuário não marcou nenhum pertence (IMPRESSÃO DE PECÚLIO).\n\n Página: $pag";
        salvaLog( $mensagem );
        ?>
        <script type="text/javascript"> alert("Você deve marcar pelo menos um sedex!"); history.go(-1);</script>
        <?php
        exit;
    }

    // monta a variavel para o comparador IN()
    $v_pec = '';
    foreach ( $idpec as $indice => $valor ) {
        if ( (int)$valor == NULL ) continue;
        $v_pec .= (int)$valor . ',';
    }

    if ( empty( $v_pec ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Após validação, o array ficou vazio. (IMPRESSÃO DE AUDIÊNCIA).\n\n Página: $pag";
        salvaLog( $mensagem );
        ?>
        <script type="text/javascript"> alert("FALHA!"); history.go(-1);</script>
        <?php
        exit;
    }

    $v_pec = substr($v_pec, 0, -1);

    if ( isset( $_SESSION['imp_pec'] ) ) unset( $_SESSION['imp_pec'] );

    $_SESSION['imp_pec'] = $v_pec;

    $saida = 'history.go(-2)';
    if ( !empty( $targ ) ) {
        $saida = 'self.window.close()';
    }

    ?>
    <script type="text/javascript" src="<?php echo SICOP_ABS_PATH ?>js/funcoes.js"></script>
    <script type="text/javascript">javascript: ow ('../print/pec_incl.php', '600', '600'); <?php echo $saida; ?>;</script>
    <?php
    exit;

} else {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de manipulação de impressão de pecúlio.\n\n Página: $pag";
    salvaLog($mensagem);
    header('Location: ../home.php');
    exit;
}
?>
</body>
</html>
