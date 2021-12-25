<?php
if ( !isset( $_SESSION ) ) session_start();

/* ob_start("ob_gzhandler"); */

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
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


$is_post = is_post();
if ( $is_post ) {

    extract( $_POST, EXTR_OVERWRITE );

    $dg = (int)$dg;
    $seg = (int)$seg;
    $cimic = (int)$cimic;
    $saude = (int)$saude;
    $rh = (int)$rh;
    $dca = (int)$dca;

    $query = "UPDATE
                `diretores`
              SET
                `diretor_geral` = $dg,
                `diretor_seg` = $seg,
                `diretor_pront` = $cimic,
                `diretor_saude` = $saude,
                `diretor_rh` = $rh,
                `diretor_ca` = $dca
              WHERE
                `iddiretores` = 1
              LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query = $model->query( $query );

    // fechando a conexao
    $model->closeConnection();

    redir( 'adm/altdirs' );
    exit;
}


/*
  -------------------
  *** SETORES ***
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
$q_dir_titular = 'SELECT `iddiretores`, `diretor_geral`, `diretor_seg`, `diretor_pront`, `diretor_saude`, `diretor_rh`, `diretor_ca` FROM `diretores` LIMIT 1';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_dcimic = $model->query( $q_dcimic );
$q_dca = $model->query( $q_dca );
$q_dnp = $model->query( $q_dnp );
$q_dcsd = $model->query( $q_dcsd );
$q_dns = $model->query( $q_dns );
$q_dg = $model->query( $q_dg );
$q_dir_titular = $model->query( $q_dir_titular );

// fechando a conexao
$model->closeConnection();

$d_titular = $q_dir_titular->fetch_assoc();

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

require 'cab.php';
$trail = new Breadcrumb();
$trail->add( 'Alterar diretor titular', $_SERVER['PHP_SELF'], 4 );
$trail->output();
?>

            <p class="descript_page">ALTERAR DIRETORES TITULARES</p>

            <form action="alttitular.php" method="post" name="alttitular" id="alttitular" >

                <table width="392" class="edit">
                    <tr>
                        <td width="120">Diretor Geral:</td>
                        <td width="262"><select name="dg" class="CaixaTexto" id="dg">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $dados = $q_dg->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados['iddiretoresn']; ?>" <?php echo $dados['iddiretoresn'] == $d_titular['diretor_geral'] ? 'selected="selected"' : ''; ?>><?php echo $dados['diretor']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Diretor Adminitrativo:</td>
                        <td>
                            <select name="dca" class="CaixaTexto" id="dca">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $dados = $q_dca->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados['iddiretoresn']; ?>" <?php echo $dados['iddiretoresn'] == $d_titular['diretor_ca'] ? 'selected="selected"' : ''; ?>><?php echo $dados['diretor']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Diretor do CIMIC:</td>
                        <td>
                            <select name="cimic" class="CaixaTexto" id="cimic">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $dados = $q_dcimic->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados['iddiretoresn']; ?>" <?php echo $dados['iddiretoresn'] == $d_titular['diretor_pront'] ? 'selected="selected"' : ''; ?>><?php echo $dados['diretor']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Diretor do RH:</td>
                        <td>
                            <select name="rh" class="CaixaTexto" id="rh">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $dados = $q_dnp->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados['iddiretoresn']; ?>" <?php echo $dados['iddiretoresn'] == $d_titular['diretor_rh'] ? 'selected="selected"' : ''; ?>><?php echo $dados['diretor']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Diretor de Segurança:</td>
                        <td>
                            <select name="seg" class="CaixaTexto" id="seg">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $dados = $q_dcsd->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados['iddiretoresn']; ?>" <?php echo $dados['iddiretoresn'] == $d_titular['diretor_seg'] ? 'selected="selected"' : ''; ?>><?php echo $dados['diretor']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Diretor de Saúde:</td>
                        <td>
                            <select name="saude" class="CaixaTexto" id="saude">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $dados = $q_dns->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados['iddiretoresn']; ?>" <?php echo $dados['iddiretoresn'] == $d_titular['diretor_saude'] ? 'selected="selected"' : ''; ?>><?php echo $dados['diretor']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                </table>

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="alterar" id="alterar" value="Alterar" />&nbsp;&nbsp;&nbsp;
                    <input class="form_bt" type="button" name="" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

<?php include 'footer.php'; ?>