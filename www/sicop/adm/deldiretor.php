<?php

if ( !isset( $_SESSION ) ) session_start();

/*ob_start("ob_gzhandler");*/

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$nivel_necessario = 4;
$n_admsist        = get_session( 'n_admsist', 'int' );

$motivo_pag = 'EXCLUSÃO DE DIRETOR';

if ($n_admsist < $nivel_necessario) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$iddir = get_get( 'iddir', 'int' );

if ( empty( $iddir ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$q_diretor = "SELECT
                `iddiretoresn`,
                `diretor`,
                `titulo_diretor`,
                `sicop_setor`.`sigla_setor`,
                `sicop_setor`.`setor`,
                `ativo`
              FROM
                `diretores_n`
                INNER JOIN `sicop_setor` ON `diretores_n`.`setor` = `sicop_setor`.`idsetor`
              WHERE
                `iddiretoresn` = $iddir
              LIMIT 1";


// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_diretor = $model->query( $q_diretor );

// fechando a conexao
$model->closeConnection();

$d_dir = $q_diretor->fetch_assoc();

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

require 'cab.php';
$trail = new Breadcrumb();
$trail->add( 'Alterar dados do diretor', $_SERVER['PHP_SELF'], 4 );
$trail->output();
?>

            <p class="descript_page">EXCLUIR DIRETOR</p>

            <table class="edit">
                <tr>
                    <td width="50">Nome:</td>
                    <td width="350"><?php echo $d_dir['diretor'] ?></td>
                </tr>
                <tr>
                    <td>Título:</td>
                    <td><?php echo $d_dir['titulo_diretor'] ?></td>
                </tr>
                <tr>
                    <td>Setor:</td>
                    <td> <?php echo $d_dir['sigla_setor'] . ' - ' . $d_dir['setor'] ?></td>
                </tr>
                <tr>
                    <td>Ativo:</td>
                    <td> <?php echo tratasn( $d_dir['ativo'] ) ?></td>
                </tr>
            </table>

            <p class="confirm_ask">Tem certeza de que deseja excluir este diretor?</p>

            <p style="text-align: center">
                <img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_attention.png" alt="Atenção" class="icon_alert" />&nbsp;ATENÇÃO -> Você <b>não poderá</b> desfazer essa operação.
            </p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/senddiretor.php" method="post" name="editdir" id="editdir" >

                <input name="proced" type="hidden" id="proced" value="2">
                <input name="iddir" type="hidden" id="iddir" value="<?php echo $iddir; ?>" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="excluir" id="submit" value="Excluir" />
                    <input class="form_bt" type="button" name="" onClick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

<?php include 'footer.php'; ?>