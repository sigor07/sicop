<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

keepHistory();

$n_rol   = get_session( 'n_rol', 'int' );
$n_rol_n = 3;


if ( $n_rol < $n_rol_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

if ( empty( $_SESSION['l_id_vis'] ) ) {
    redir( 'home' );
    exit;
}

$l_id_vis = (int)$_SESSION['l_id_vis'];

$query_v = "SELECT
              visitas.idvisita,
              visitas.cod_detento,
              visitas.nome_visit,
              visitas.sexo_visit,
              tipoparentesco.parentesco
            FROM
              visitas
              INNER JOIN tipoparentesco ON visitas.cod_parentesco = tipoparentesco.idparentesco
            WHERE
              visitas.idvisita = $l_id_vis
            LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_v = $model->query( $query_v );

// fechando a conexao
$model->closeConnection();

if ( !$query_v ) {

    echo msg_js( '', 1 );
    exit;

}

$contv = $query_v->num_rows;

if( $contv < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrencias (visitas).\n\n Página $pag";
    salvaLog($mensagem);
    echo msg_js( '', 1 );
    exit;
}

$d_visit = $query_v->fetch_assoc();

$iddet = $d_visit['cod_detento'];

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

require 'cab.php';
?>

            <p class="descript_page">CADASTRO DE VISITANTE EFETUADO!</p>

            <table class="lista_busca">
                <tr bgcolor="#FAFAFA">
                    <td width="113" height="20">Identificador (ID):</td>
                    <td width="372"><?php echo $d_visit['idvisita']; ?></td>
                </tr>
                <tr bgcolor="#FAFAFA">
                    <td height="20">Visitante:</td>
                    <td><a href="detalvisit.php?idvisit=<?php echo $d_visit['idvisita']; ?>" title="Clique aqui para abrir os detalhes deste visitante"><?php echo $d_visit['nome_visit']; ?></a></td>
                </tr>
                <tr bgcolor="#FAFAFA">
                    <td height="20">Sexo:</td>
                    <td><?php echo $d_visit['sexo_visit'] ?></td>
                </tr>
                <tr bgcolor="#FAFAFA">
                    <td height="20">Parentesco:</td>
                    <td><?php echo $d_visit['parentesco']; ?></td>
                </tr>
            </table>

            <?php include 'quali/det_basic.php'; ?>

            <p class="bt_leg" style="margin-top: 10px;">O que você deseja fazer agora?</p>

            <ul id="menuok">
                <li><a href="rol_visit.php?iddet=<?php echo $iddet; ?>">Ir para o rol d<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?></a></li>
                <li><a href="regentrv.php?iddet=<?php echo $iddet; ?>">Ir para o registro de entrada de visitante</a></li>
                <li><a href="cadastravisit.php?iddet=<?php echo $iddet; ?>" >Cadastrar outro visitante para <b><?php echo SICOP_DET_PRON_U; ?></b> <?php echo SICOP_DET_DESC_L; ?></a></li>
                <li><a href="<?php echo SICOP_ABS_PATH ?>buscadet.php?proced=cadrol" >Cadastrar outro visitante para <b>OUTR<?php echo SICOP_DET_ART_U; ?></b> <?php echo SICOP_DET_DESC_L; ?></a></li>
            </ul>

<?php include 'footer.php'; ?>
