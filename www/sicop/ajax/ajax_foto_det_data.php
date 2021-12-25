<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

error_reporting(E_ALL);
ini_set('display_errors', TRUE);

$tipo_pag     = 'RELAÇÃO DE FOTOS D' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U . ' - AJAX';
$img_det_path = SICOP_DET_IMG_PATH;
$img_sys_path = SICOP_SYS_IMG_PATH;
$msg_falha = '<p class="q_error">FALHA!</p>';

$is_post = is_post();
if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $tipo_pag ).";
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$iddet = get_post( 'iddet', 'int' );
if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página. Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. ( $tipo_pag )";
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$n_chefia       = get_session( 'n_chefia', 'int' );
$n_incl         = get_session( 'n_incl', 'int' );
$n_det_alt_foto = get_session( 'n_det_alt_foto', 'int' );
$proced         = get_post( 'proced', 'int' );


$q_det = "SELECT
            `iddetento`,
            `nome_det`,
            `matricula`,
            `cod_foto`
          FROM
            `detentos`
          WHERE
            `iddetento` = $iddet
          LIMIT 1";

$db = SicopModel::getInstance();
$q_det = $db->query( $q_det );
if ( !$q_det ) {

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();
    $db->closeConnection();

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( DADOS D" . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U . " - $tipo_pag ). \n\n $msg_err_mysql";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$db->closeConnection();

$cont_det = $q_det->num_rows;
if ( $cont_det < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "A consulta retornou 0 ocorrências ( DADOS D" . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U . " - $tipo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$d_det      = $q_det->fetch_object();
$foto_princ = $d_det->cod_foto;

$q_foto = "SELECT
             `id_foto`,
             `cod_detento`,
             `foto_det_g`,
             `foto_det_p`,
             `user_add`,
             `data_add`,
             `ip_add`
           FROM
             `detentos_fotos_esp`
           WHERE
             `cod_detento` = $iddet
           ORDER BY
             `data_add`";

if ( $proced == 1 ) {


    $q_foto = "SELECT
                 `id_foto`,
                 `cod_detento`,
                 `foto_det_g`,
                 `foto_det_p`,
                 `user_add`,
                 `data_add`,
                 `ip_add`
               FROM
                 `det_fotos`
               WHERE
                 `cod_detento` = $iddet
               ORDER BY
                 `data_add`";

}

$db = SicopModel::getInstance();
$q_foto = $db->query( $q_foto );

if ( !$q_foto ) {

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( $tipo_pag ). \n\n $msg_err_mysql";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$cont_foto = $q_foto->num_rows;



?>



        <?php

        if ( $cont_foto < 1 ) {

            echo '<p class="p_q_no_result">Não há fotos.</p>';

        } else {

            $d_fotos = '';
            $pasta = SICOP_DET_FOLDER;
            $rel   = 'group_foto_esp';
            $input_name = 'del_foto_det_esp';

            if ( $proced == 1 ) {
                $rel        = 'group_foto';
                $input_name = 'del_foto_det';
            }

            while ( $d_fotos = $q_foto->fetch_object() ) {

                $id_foto = $d_fotos->id_foto;
                $foto_g = $d_fotos->foto_det_g;
                $foto_p = $d_fotos->foto_det_p;

                $foto_det = ck_pic( $foto_g, $foto_p, false, 1 );

                $amplia = true;
                if ( empty( $foto_g ) or !is_file( $pasta . $foto_g ) ) {
                    $amplia = false;
                }

                ?>

                <div class="icon_foto">

                    <?php if ( $amplia ){ ?>
                    <a class="link_group_foto_det" rel="<?php echo $rel; ?>" href="<?php echo $img_det_path . $foto_g ?>" title="<?php echo $d_det->nome_det; if ( !empty( $d_det->matricula ) ) echo ' - ' . formata_num( $d_det->matricula ) ?>">
                    <?php }; ?>
                    <img class="tumb_foto_det" src="<?php echo $foto_det ?>" alt="<?php echo $d_det->nome_det; if ( !empty( $d_det->matricula ) ) echo ' - ' . formata_num( $d_det->matricula ) ?>" />
                    <?php if ( $amplia ){ ?></a><?php } ?>

                    <?php

                    if ( $n_det_alt_foto >= 1 and ( $n_chefia >= 4 || $n_incl >= 4 ) ) {

                        if ( $proced == 1 ) {

                            $disabled = '';
                            $img_bt   = 's_add_g.png';
                            $title    = 'Definir como foto principal';

                            if ( $id_foto == $foto_princ ) {
                                $disabled = 'disabled="disabled"';
                                $img_bt   = 's_add.png';
                                $title    = 'Esta é a foto principal';
                            }

                            if ( !is_file( $pasta . $foto_g ) ) {
                                $disabled = 'disabled="disabled"';
                                $img_bt   = 's_add_g.png';
                                $title    = 'Esta foto é inválida ou está corrompida';
                            }

                        }

                        ?>

                    <div class="foto_bts">

                        <?php if ( $proced == 1 ) { ?>
                        <input class="foto_bt" type="image" src="<?php echo $img_sys_path . $img_bt; ?>" name="def_foto_det[]" value="<?php echo $id_foto;?>" title="<?php echo $title; ?>" <?php echo $disabled; ?>/>
                        <?php } ?>
                        <input class="foto_bt" type="image" src="<?php echo $img_sys_path; ?>b_drop.png" name="<?php echo $input_name; ?>[]" value="<?php echo $id_foto;?>" title="Excluir esta foto" />

                    </div>

                    <?php }  ?>

                </div><!-- /div.icon_foto -->

                <?php

                    } // /while ( $d_fotos = $q_foto->fetch_object() ) {

                } // /if ( $cont_foto < 1 ) {
            ?>