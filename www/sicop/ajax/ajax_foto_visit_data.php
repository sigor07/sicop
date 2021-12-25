<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$tipo_pag     = 'RELAÇÃO DE FOTOS DO VISITANTE - AJAX';
$img_visit_path = SICOP_VISIT_IMG_PATH;
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

$idvisit = get_post( 'idvisit', 'int' );
if ( empty( $idvisit ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página. Identificador do visitante em branco. ( $tipo_pag )";
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$n_rol = get_session( 'n_rol', 'int' );

$q_foto = "SELECT
             `id_foto`,
             `cod_visita`,
             `foto_visit_g`,
             `foto_visit_p`,
             `user_add`,
             `data_add`,
             `ip_add`
           FROM
             `visita_fotos`
           WHERE
             `cod_visita` = $idvisit
           ORDER BY
             `data_add`";

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

$q_visit = "SELECT
              `visitas`.`nome_visit`,
              `visitas`.`cod_foto`
            FROM
              `visitas`
            WHERE
              `visitas`.`idvisita` = $idvisit
            LIMIT 1";

$q_visit = $db->query( $q_visit );
if ( !$q_visit ) {

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( DADOS DO VISITANTE - $tipo_pag ). \n\n $msg_err_mysql";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$db->closeConnection();

$cont_visit = $q_visit->num_rows;
if ( $cont_visit < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "A consulta retornou 0 ocorrências ( DADOS DO VISITANTE - $tipo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$d_visit    = $q_visit->fetch_object();
$foto_princ = $d_visit->cod_foto;

?>

                <?php

                if ( $cont_foto < 1 ) {

                    echo '<p class="p_q_no_result">Não há fotos.</p>';

                } else {

                $d_fotos = '';
                while ( $d_fotos = $q_foto->fetch_object() ) {

                    $id_foto = $d_fotos->id_foto;
                    $foto_g  = $d_fotos->foto_visit_g;
                    $foto_p  = $d_fotos->foto_visit_p;

                    $foto_visit = ck_pic( $foto_g, $foto_p, false, 2 );

                    $pasta = SICOP_VISIT_FOLDER;

                    $amplia = true;
                    if ( empty( $foto_g ) or !is_file( $pasta . $foto_g ) ) {
                        $amplia = false;
                    }

                ?>

                <div class="icon_foto">

                    <?php if ( $amplia ){ ?>
                    <a class="link_group_foto_visit" rel="group_foto" href="<?php echo $img_visit_path . $foto_g ?>" title="<?php echo $d_visit->nome_visit; ?>">
                    <?php }; ?>
                    <img class="tumb_foto_visit" src="<?php echo $foto_visit ?>" alt="<?php echo $d_visit->nome_visit; ?>" />
                    <?php if ( $amplia ){ ?></a><?php } ?>

                    <?php

                    if ( $n_rol >= 4 ) {

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

                        ?>

                    <div class="foto_bts">

                        <input class="foto_bt" type="image" src="<?php echo $img_sys_path . $img_bt; ?>" name="def_foto_visit[]" value="<?php echo $id_foto;?>" title="<?php echo $title; ?>" <?php echo $disabled; ?>/>
                        <input class="foto_bt" type="image" src="<?php echo $img_sys_path; ?>b_drop.png" name="del_foto_visit[]" value="<?php echo $id_foto;?>" title="Excluir esta foto" />

                    </div>

                    <?php }  ?>

                </div><!-- /div.icon_foto -->

                <?php

                    } // /while ( $d_fotos = $q_foto->fetch_object() ) {

                } // /if ( $cont_foto < 1 ) {

                ?>

