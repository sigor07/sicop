<?php

//$dados = $variable->fetch_assoc();


if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_rol   = get_session( 'n_rol', 'int' );
$imp_rol = get_session( 'imp_rol', 'int' );
$n_rol_n = 3;

if ($n_rol < $n_rol_n) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'ALTERAÇÃO DE PERMISSÕES - ROL DE VISITAS';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}


$is_post = is_post();
if ( $is_post ) {

    require 'cab_simp.php';

    extract($_POST, EXTR_OVERWRITE);

    $aut_visita = (int)$aut_visita;
    $aut_sedex  = (int)$aut_sedex;
    $iddet      = (int)$iddet;
    $iduser     = get_session( 'user_id', 'int' );

    if (empty($iddet)){
        $mensagem = "ERRO -> Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. Operação cancelada (ALTERAÇÃO DE PERMISSÕES DO ROL).\n\n Página: $pag";
        salvaLog($mensagem);

        echo msg_js( 'FALHA!', 2 );

        exit;

    }

    $aut_visita = empty( $aut_visita ) ? 0 : 1;
    $aut_sedex  = empty( $aut_sedex ) ? 0 : 1;

    // pegar os dados do preso
    $detento = dados_det( $iddet );

    $q_up_det = "UPDATE `detentos` SET
                     `aut_visita` = $aut_visita,
                     `aut_sedex` = $aut_sedex,
                     `user_up` = $iduser,
                     `data_up` = NOW()
                     WHERE `iddetento` = $iddet LIMIT 1";


    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_up_det = $model->query( $q_up_det );

    // fechando a conexao
    $model->closeConnection();

    if ( $q_up_det ) {

        $mensagem = "[ ALTERAÇÃO DE PERMISSÕES DO ROL DE VISITAS ]\n Alteração de permissões do rol de visitas de detento.\n\n $detento";
        salvaLog( $mensagem );
        header( 'Location: rol_visit.php?iddet=' . $iddet );
        exit;

    } else {

        echo msg_js( 'FALHA!', 2 );
        $mensagem = "[ *** ERRO *** ]\n Erro de atualização de permissões do rol de visitas de detento.\n\n $detento";
        salvaLog($mensagem);
        exit;

    }

}

$iddet = empty( $_GET['iddet'] ) ? '' : (int)$_GET['iddet'];

if ( empty( $iddet ) ){
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página de alteração de permissões do rol de visitas.\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( '', 1 );
    exit;
}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if (!empty($qs)){
    $pag_atual .=  '?' . $qs;
}
$trail = new Breadcrumb();
$trail->add('Alteração de permissões', $pag_atual, 6);
$trail->output();

?>

            <p class="descript_page">ALTERAR PERMISSÕES DE ACESSO DE VISITAS E DE SEDEX</p>

            <?php include 'quali/det_full.php'; ?>

            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" name="visit_up" id="visit_up" onSubmit="return validaeditvisit();">

                <table width="270" class="edit">
                    <tr>
                        <td width="130">Pode receber visitas:</td>
                        <td width="130" colspan="3">
                            <input type="radio" name="aut_visita" value="1" id="aut_visita_0" <?php echo ( $d_det['aut_visita'] == "1" or !empty( $d_det['aut_visita'] ) ) ? 'checked="checked"' : ''; ?> /> SIM &nbsp;&nbsp;
                            <input type="radio" name="aut_visita" value="0" id="aut_visita_1" <?php echo ( $d_det['aut_visita'] == "0" or empty( $d_det['aut_visita'] ) ) ? 'checked="checked"' : ''; ?> /> NÃO
                        </td>

                    </tr>
                    <tr>
                        <td>Pode receber sedex:</td>
                        <td colspan="3">
                            <input type="radio" name="aut_sedex" value="1" id="aut_sedex_0" <?php echo ( $d_det['aut_sedex'] == "1" or !empty( $d_det['aut_sedex'] ) ) ? 'checked="checked"' : ''; ?> /> SIM &nbsp;&nbsp;
                            <input type="radio" name="aut_sedex" value="0" id="aut_sedex_1" <?php echo ( $d_det['aut_sedex'] == "0" or empty( $d_det['aut_sedex'] ) ) ? 'checked="checked"' : ''; ?> /> NÃO
                        </td>
                    </tr>
                </table>

                <script type="text/javascript">id("aut_visita_0").focus();</script>

                <input name="iddet" type="hidden" id="iddet" value="<?php echo $d_det['iddetento'] ?>" />

                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" value="Atualizar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

<?php include 'footer.php'; ?>