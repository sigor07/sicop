<?php
if ( !isset( $_SESSION ) ) session_start();

/* ob_start("ob_gzhandler"); */

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

//$nivel_necessario = 3;
//$n_adm = get_session( 'n_adm', 'int' );
//
//if ( $n_adm < $nivel_necessario ) {
//    require 'cab_simp.php';
//    $tipo = 0;
//    include '../init/msgnopag.php';
//    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
//    salvaLog( $mensagem );
//    exit;
//}

$is_post = is_post();
if ( $is_post ) {

    extract( $_POST, EXTR_OVERWRITE );

    $_SESSION['diretor_geral'] = $titular_dg;
    $_SESSION['diretor_seg'] = $titular_seg;
    $_SESSION['diretor_pront'] = $titular_cimic;
    $_SESSION['diretor_saude'] = $titular_saude;
    $_SESSION['diretor_rh'] = $titular_rh;
    $_SESSION['diretor_ca'] = $titular_dca;

    //header( 'Location: ../adm.php' );
    redir ( 'home' );
    exit;
}

$dg = $_SESSION['diretor_geral'];
$dseg = $_SESSION['diretor_seg'];
$dpront = $_SESSION['diretor_pront'];
$dsaude = $_SESSION['diretor_saude'];
$drh = $_SESSION['diretor_rh'];
$dca = $_SESSION['diretor_ca'];

/*
  -------------------
 * ** SETORES ***
  -------------------
  1 - DCIMIC
  2 - DCA
  3 - DNP
  4 - DCSD
  5 - DNS
  6 - DG
  -------------------
 */

$q_dcimic = 'SELECT `iddiretoresn`, `diretor`, `titulo_diretor`, `setor`, `ativo` FROM `diretores_n` WHERE `setor` = 1 AND `ativo` = TRUE ORDER BY `diretor` ASC';
$q_dca = 'SELECT `iddiretoresn`, `diretor`, `titulo_diretor`, `setor`, `ativo` FROM `diretores_n` WHERE `setor` = 2 AND `ativo` = TRUE ORDER BY `diretor` ASC';
$q_dnp = 'SELECT `iddiretoresn`, `diretor`, `titulo_diretor`, `setor`, `ativo` FROM `diretores_n` WHERE `setor` = 3 AND `ativo` = TRUE ORDER BY `diretor` ASC';
$q_dcsd = 'SELECT `iddiretoresn`, `diretor`, `titulo_diretor`, `setor`, `ativo` FROM `diretores_n` WHERE `setor` = 4 AND `ativo` = TRUE ORDER BY `diretor` ASC';
$q_dns = 'SELECT `iddiretoresn`, `diretor`, `titulo_diretor`, `setor`, `ativo` FROM `diretores_n` WHERE `setor` = 5 AND `ativo` = TRUE ORDER BY `diretor` ASC';
$q_dg = 'SELECT `iddiretoresn`, `diretor`, `titulo_diretor`, `setor`, `ativo` FROM `diretores_n` WHERE `setor` = 6 AND `ativo` = TRUE ORDER BY `diretor` ASC';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_dcimic = $model->query( $q_dcimic );
$q_dca = $model->query( $q_dca );
$q_dnp = $model->query( $q_dnp );
$q_dcsd = $model->query( $q_dcsd );
$q_dns = $model->query( $q_dns );
$q_dg = $model->query( $q_dg );

// fechando a conexao
$model->closeConnection();

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Alterar diretores';

require 'cab.php';
$trail = new Breadcrumb();
$trail->add( $desc_pag, $_SERVER['PHP_SELF'], 3 );
$trail->output();
?>

            <p class="descript_page">ALTERAR DIRETORES PARA ESTA SEÇÃO</p>

            <form action="altdir.php" method="post" name="altdir" id="altdir" >

                <table class="edit">
                    <tr>
                        <th class="alt_dir" colspan="3" scope="col">Diretor geral</th>
                    </tr>
                    <?php
                    while ( $d_dg = $q_dg->fetch_assoc() ) {
                    ?>
                    <tr>
                        <td width="270"><?php echo $d_dg['diretor']; ?></td>
                        <td width="270"><?php echo $d_dg['titulo_diretor']; ?></td>
                        <td width="30" align="center"><input name="titular_dg" type="radio" value="<?php echo $d_dg['iddiretoresn'] ?>" <?php echo $d_dg['iddiretoresn'] == $dg ? 'checked="checked"' : ''; ?> /> </td>
                    </tr>
                    <?php } ?>
                </table>

                <table class="edit">
                    <tr>
                        <th class="alt_dir" colspan="3" scope="col">Diretor do Centro Administrativo</th>
                    </tr>
                    <?php
                    while ( $d_dca = $q_dca->fetch_assoc() ) {
                    ?>
                    <tr>
                        <td width="270"><?php echo $d_dca['diretor']; ?></td>
                        <td width="270"><?php echo $d_dca['titulo_diretor']; ?></td>
                        <td width="30" align="center"><input name="titular_dca" type="radio" value="<?php echo $d_dca['iddiretoresn'] ?>" <?php echo $d_dca['iddiretoresn'] == $dca ? 'checked="checked"' : ''; ?> /> </td>
                    </tr>
                    <?php } ?>
                </table>

                <table class="edit">
                    <tr>
                        <th class="alt_dir" colspan="3" scope="col">Diretor do CIMIC</th>
                    </tr>
                    <?php
                    while ( $d_dcimic = $q_dcimic->fetch_assoc() ) {
                    ?>
                    <tr>
                        <td width="270"><?php echo $d_dcimic['diretor']; ?></td>
                        <td width="270"><?php echo $d_dcimic['titulo_diretor']; ?></td>
                        <td width="30" align="center"><input name="titular_cimic" type="radio" value="<?php echo $d_dcimic['iddiretoresn'] ?>" <?php echo $d_dcimic['iddiretoresn'] == $dpront ? 'checked="checked"' : ''; ?> /> </td>
                    </tr>
                    <?php } ?>
                </table>

                <table class="edit">
                    <tr>
                        <th class="alt_dir" colspan="3" scope="col">Diretor do RH</th>
                    </tr>
                    <?php
                    while ( $d_dnp = $q_dnp->fetch_assoc() ) {
                    ?>
                    <tr>
                        <td width="270"><?php echo $d_dnp['diretor']; ?></td>
                        <td width="270"><?php echo $d_dnp['titulo_diretor']; ?></td>
                        <td width="30" align="center"><input name="titular_rh" type="radio" value="<?php echo $d_dnp['iddiretoresn'] ?>" <?php echo $d_dnp['iddiretoresn'] == $drh ? 'checked="checked"' : ''; ?> /> </td>
                    </tr>
                    <?php } ?>
                </table>

                <table class="edit">
                    <tr>
                        <th class="alt_dir" colspan="3" scope="col">Diretor de Segurança</th>
                    </tr>
                    <?php
                    while ( $d_dcsd = $q_dcsd->fetch_assoc() ) {
                    ?>
                    <tr>
                        <td width="270"><?php echo $d_dcsd['diretor']; ?></td>
                        <td width="270"><?php echo $d_dcsd['titulo_diretor']; ?></td>
                        <td width="30" align="center"><input name="titular_seg" type="radio" value="<?php echo $d_dcsd['iddiretoresn'] ?>" <?php echo $d_dcsd['iddiretoresn'] == $dseg ? 'checked="checked"' : ''; ?> /> </td>
                    </tr>
                    <?php } ?>
                </table>

                <table class="edit">
                    <tr>
                        <th class="alt_dir" colspan="3" scope="col">Diretor da Saude</th>
                    </tr>
                    <?php
                    while ( $d_dns = $q_dns->fetch_assoc() ) {
                    ?>
                    <tr>
                        <td width="270"><?php echo $d_dns['diretor']; ?></td>
                        <td width="270"><?php echo $d_dns['titulo_diretor']; ?></td>
                        <td width="30" align="center"><input name="titular_saude" type="radio" value="<?php echo $d_dns['iddiretoresn'] ?>" <?php echo $d_dns['iddiretoresn'] == $dsaude ? 'checked="checked"' : ''; ?> /> </td>
                    </tr>
                    <?php } ?>
                </table>

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="alterar" id="alterar" value="Alterar" />
                </div>
            </form>

<?php include 'footer.php';?>
