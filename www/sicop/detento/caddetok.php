<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_chefia = get_session( 'n_chefia', 'int' );
$nivel_necessario = 3;

if ( ($n_cadastro < $nivel_necessario) and ($n_chefia < $nivel_necessario) ) {
    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
    salvaLog( $mensagem );
    exit;
}

if ( empty( $_SESSION['l_id_det'] ) ) {

    redir( 'home' );
    exit;

}

$l_id_det = (int)$_SESSION['l_id_det'];

$query = "Select
              detentos.iddetento,
              detentos.nome_det,
              detentos.matricula,
              detentos.pai_det,
              detentos.mae_det
            From
              detentos
            WHERE
              detentos.iddetento = $l_id_det
            LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query = $model->query( $query );

// fechando a conexao
$model->closeConnection();

$contd = $query->num_rows;


if ( !$query or $contd < 1 ) {
    redir( 'home' );
    exit;
}

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$d_det = $query->fetch_assoc();

require 'cab.php';
?>


    <p class="descript_page">CADASTRO DE <?php echo SICOP_DET_DESC_U ?> EFETUADO!</p>

    <table class="lista_busca">
        <tr bgcolor="#FAFAFA">
            <td width="113" height="20">Identificador (ID):</td>
            <td width="372"><?php echo $d_det['iddetento']; ?></td>
        </tr>
        <tr bgcolor="#FAFAFA">
            <td height="20"><?php echo SICOP_DET_DESC_FU; ?>:</td>
            <td><a href="detalhesdet.php?iddet=<?php echo $d_det['iddetento']; ?>" title="Clique aqui para abrir a qualificativa deste detento"><?php echo $d_det['nome_det']; ?></a></td>
        </tr>
        <tr bgcolor="#FAFAFA">
            <td height="20">Matrícula:</td>
            <td><?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] ); ?></td>
        </tr>
        <tr bgcolor="#FAFAFA">
            <td height="20">Nome do pai:</td>
            <td><?php echo $d_det['pai_det']; ?></td>
        </tr>
        <tr bgcolor="#FAFAFA">
            <td height="20">Nome da mãe:</td>
            <td><?php echo $d_det['mae_det']; ?></td>
        </tr>
    </table>

    <p class="sub_title_page">O que você deseja fazer agora?</p>

    <ul id="menuok">

        <li><a href="cadmovdet.php?iddet=<?php echo $d_det['iddetento']; ?>" id="cadmov" title="Cadastrar uma movimentação para <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>">Cadastrar movimentação</a></li>
        <li><a href="cadastradet.php" title="Cadastrar outr<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?>">Cadastrar outr<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?></a></li>
        <?php if ( $n_cadastro > 2 && !empty( $d_det['matricula'] ) ) { ?>
            <li><a href="<?php echo SICOP_ABS_PATH ?>cadastro/cadaud.php?iddet=<?php echo $d_det['iddetento']; ?>" title="Cadastrar audiência para <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>">Cadastrar audiência para <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?></a></li>
        <?php } ?>

    </ul>

    <script type="text/javascript">id("cadmov").focus();</script>

<?php include 'footer.php'; ?>
