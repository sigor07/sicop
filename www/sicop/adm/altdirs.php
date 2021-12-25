<?php
if ( !isset( $_SESSION ) ) session_start();

/* ob_start("ob_gzhandler"); */

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

keepHistory();

$nivel_necessario = 3;
$n_admsist = get_session( 'n_admsist', 'int' );

if ( $n_admsist < $nivel_necessario ) {
    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
    salvaLog( $mensagem );
    exit;
}


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

$q_dcimic = 'SELECT `iddiretoresn`, `diretor`, `titulo_diretor`, `setor`, `ativo` FROM `diretores_n` WHERE `setor` = 1 ORDER BY `diretor` ASC';
$q_dca = 'SELECT `iddiretoresn`, `diretor`, `titulo_diretor`, `setor`, `ativo` FROM `diretores_n` WHERE `setor` = 2 ORDER BY `diretor` ASC';
$q_dnp = 'SELECT `iddiretoresn`, `diretor`, `titulo_diretor`, `setor`, `ativo` FROM `diretores_n` WHERE `setor` = 3 ORDER BY `diretor` ASC';
$q_dcsd = 'SELECT `iddiretoresn`, `diretor`, `titulo_diretor`, `setor`, `ativo` FROM `diretores_n` WHERE `setor` = 4 ORDER BY `diretor` ASC';
$q_dns = 'SELECT `iddiretoresn`, `diretor`, `titulo_diretor`, `setor`, `ativo` FROM `diretores_n` WHERE `setor` = 5 ORDER BY `diretor` ASC';
$q_dg = 'SELECT `iddiretoresn`, `diretor`, `titulo_diretor`, `setor`, `ativo` FROM `diretores_n` WHERE `setor` = 6 ORDER BY `diretor` ASC';

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

require 'cab.php';
$trail = new Breadcrumb();
$trail->add( 'Alterar diretores', $_SERVER['PHP_SELF'], 3 );
$trail->output();
?>

<p class="descript_page">ALTERAR DIRETORES</p>

<?php if ( $n_admsist >= 3 ) { ?>
<p class="link_common">
    <a href="caddiretor.php" title="Cadastrar novo diretor" >Cadastrar diretor</a> - <a href="alttitular.php" title="Alterar o diretor titular" >Alterar titular</a>
</p>
<?php }; ?>


<table class="lista_busca">
    <tr bgcolor="#FAFAFA">
        <th colspan="4" scope="col">Diretor geral</th>
    </tr>
    <?php
    while ( $d_dg = $q_dg->fetch_assoc() ) {
    ?>
        <tr class="even">
            <td width="270"><?php echo $d_dg['diretor']; ?></td>
            <td width="270"><?php echo $d_dg['titulo_diretor']; ?></td>
            <td class="tb_bt"><?php if ( $n_admsist >= 3 ) { ?><a href="editdiretor.php?iddir=<?php echo $d_dg['iddiretoresn']; ?>" title="Alterar dados deste diretor" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="" /></a><?php }; ?></td>
            <td class="tb_bt"><?php if ( $n_admsist >= 4 ) { ?><a href="deldiretor.php?iddir=<?php echo $d_dg['iddiretoresn']; ?>" title="Excluir este diretor" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="" /></a><?php }; ?></td>
        </tr>
<?php } ?>
</table>

<p>&nbsp;</p>

<table class="lista_busca">
    <tr bgcolor="#FAFAFA">
        <th colspan="4" scope="col">Diretor do Centro Administrativo</th>
    </tr>
    <?php
    while ( $d_dca = $q_dca->fetch_assoc() ) {
    ?>
        <tr class="even">
            <td width="270"><?php echo $d_dca['diretor']; ?></td>
            <td width="270"><?php echo $d_dca['titulo_diretor']; ?></td>
            <td class="tb_bt"><?php if ( $n_admsist >= 3 ) { ?><a href="editdiretor.php?iddir=<?php echo $d_dca['iddiretoresn']; ?>" title="Alterar dados deste diretor" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="" /></a><?php }; ?></td>
            <td class="tb_bt"><?php if ( $n_admsist >= 4 ) { ?><a href="deldiretor.php?iddir=<?php echo $d_dca['iddiretoresn']; ?>" title="Excluir este diretor" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="" /></a><?php }; ?></td>
        </tr>
<?php } ?>
</table>

<p>&nbsp;</p>

<table class="lista_busca">
    <tr bgcolor="#FAFAFA" >
        <th colspan="4" scope="col">Diretor do CIMIC</th>
    </tr>
    <?php
    while ( $d_dcimic = $q_dcimic->fetch_assoc() ) {
    ?>
        <tr class="even">
            <td width="270"><?php echo $d_dcimic['diretor']; ?></td>
            <td width="270"><?php echo $d_dcimic['titulo_diretor']; ?></td>
            <td class="tb_bt"><?php if ( $n_admsist >= 3 ) { ?><a href="editdiretor.php?iddir=<?php echo $d_dcimic['iddiretoresn']; ?>" title="Alterar dados deste diretor" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="" /></a><?php }; ?></td>
            <td class="tb_bt"><?php if ( $n_admsist >= 4 ) { ?><a href="deldiretor.php?iddir=<?php echo $d_dcimic['iddiretoresn']; ?>" title="Excluir este diretor" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="" /></a><?php }; ?></td>
        </tr>
<?php } ?>
</table>

<p>&nbsp;</p>

<table class="lista_busca">
    <tr bgcolor="#FAFAFA" >
        <th colspan="4" scope="col">Diretor do RH</th>
    </tr>
    <?php
    while ( $d_dnp = $q_dnp->fetch_assoc() ) {
    ?>
        <tr class="even">
            <td width="270"><?php echo $d_dnp['diretor']; ?></td>
            <td width="270"><?php echo $d_dnp['titulo_diretor']; ?></td>
            <td class="tb_bt"><?php if ( $n_admsist >= 3 ) { ?><a href="editdiretor.php?iddir=<?php echo $d_dnp['iddiretoresn']; ?>" title="Alterar dados deste diretor" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="" /></a><?php }; ?></td>
            <td class="tb_bt"><?php if ( $n_admsist >= 4 ) { ?><a href="deldiretor.php?iddir=<?php echo $d_dnp['iddiretoresn']; ?>" title="Excluir este diretor" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="" /></a><?php }; ?></td>
        </tr>
<?php } ?>
</table>

<p>&nbsp;</p>

<table class="lista_busca">
    <tr bgcolor="#FAFAFA" >
        <th colspan="4" scope="col">Diretor de Segurança</th>
    </tr>
    <?php
    while ( $d_dcsd = $q_dcsd->fetch_assoc() ) {
    ?>
        <tr class="even">
            <td width="270"><?php echo $d_dcsd['diretor']; ?></td>
            <td width="270"><?php echo $d_dcsd['titulo_diretor']; ?></td>
            <td class="tb_bt"><?php if ( $n_admsist >= 3 ) { ?><a href="editdiretor.php?iddir=<?php echo $d_dcsd['iddiretoresn']; ?>" title="Alterar dados deste diretor" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="" /></a><?php }; ?></td>
            <td class="tb_bt"><?php if ( $n_admsist >= 4 ) { ?><a href="deldiretor.php?iddir=<?php echo $d_dcsd['iddiretoresn']; ?>" title="Excluir este diretor" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="" /></a><?php }; ?></td>
        </tr>
<?php } ?>
</table>

<p>&nbsp;</p>

<table class="lista_busca">
    <tr bgcolor="#FAFAFA" >
        <th colspan="4" scope="col">Diretor da Saude</th>
    </tr>
    <?php
    while ( $d_dns = $q_dns->fetch_assoc() ) {
    ?>
        <tr class="even">
            <td width="270"><?php echo $d_dns['diretor']; ?></td>
            <td width="270"><?php echo $d_dns['titulo_diretor']; ?></td>
            <td class="tb_bt"><?php if ( $n_admsist >= 3 ) { ?><a href="editdiretor.php?iddir=<?php echo $d_dns['iddiretoresn']; ?>" title="Alterar dados deste diretor" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="" /></a><?php }; ?></td>
            <td class="tb_bt"><?php if ( $n_admsist >= 4 ) { ?><a href="deldiretor.php?iddir=<?php echo $d_dns['iddiretoresn']; ?>" title="Excluir este diretor" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="" /></a><?php }; ?></td>
        </tr>
<?php } ?>
</table>
<?php include 'footer.php';?>