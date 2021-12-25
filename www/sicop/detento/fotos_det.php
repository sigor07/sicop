<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag          = link_pag();
$tipo_pag     = 'RELAÇÃO DE FOTOS D' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U;
$img_det_path = SICOP_DET_IMG_PATH;
$img_sys_path = SICOP_SYS_IMG_PATH;

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$iddet = get_get( 'iddet', 'int' );
if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ATEN );
    $msg->set_msg_pre_def( SM_DIRECT_ACCESS );
    $msg->add_parenteses( "IDENTIFICADOR EM BRANCO - $tipo_pag" );
    $msg->get_msg();

    echo msg_js( '', 1 );

    exit;

}

$n_chefia       = get_session( 'n_chefia', 'int' );
$n_incl         = get_session( 'n_incl', 'int' );
$n_det_alt_foto = get_session( 'n_det_alt_foto', 'int' );

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

$db = SicopModel::getInstance();
$q_foto = $db->query( $q_foto );

if ( !$q_foto ) {

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();
    $db->closeConnection();

    // gerar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ERR );
    $msg->add_quebras( 1 );
    $msg->set_msg_pre_def( SM_QUERY_FAIL );
    $msg->add_parenteses( $tipo_pag );
    $msg->add_quebras( 2 );
    $msg->set_msg( $msg_err_mysql );
    $msg->get_msg();

    echo msg_js( '', 1 );
    exit;

}

$cont_foto = $q_foto->num_rows;

$q_id_foto_atual = "SELECT `cod_foto` FROM `detentos` WHERE `iddetento` = $iddet LIMIT 1";
$q_id_foto_atual = $db->query( $q_id_foto_atual );
if ( !$q_id_foto_atual ) {

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();
    $db->closeConnection();

    // gerar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ERR );
    $msg->add_quebras( 1 );
    $msg->set_msg_pre_def( SM_QUERY_FAIL );
    $msg->add_parenteses( "IDENTIFICADOR DA FOTO D" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " - $tipo_pag" );
    $msg->add_quebras( 2 );
    $msg->set_msg( $msg_err_mysql );
    $msg->get_msg();

    echo msg_js( '', 1 );
    exit;

}

$cont_foto_atual = $q_id_foto_atual->num_rows;
if ( $cont_foto_atual < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "A consulta retornou 0 ocorrências ( IDENTIFICADOR DA FOTO D" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " - $tipo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$d_foto_atual = $q_id_foto_atual->fetch_object();
$foto_atual   = $d_foto_atual->cod_foto;


$q_foto_esp = "SELECT
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

$q_foto_esp = $db->query( $q_foto_esp );

if ( !$q_foto_esp ) {

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();
    $db->closeConnection();

    // gerar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ERR );
    $msg->add_quebras( 1 );
    $msg->set_msg_pre_def( SM_QUERY_FAIL );
    $msg->add_parenteses( "FOTOS ESPECIAIS - $tipo_pag" );
    $msg->add_quebras( 2 );
    $msg->set_msg( $msg_err_mysql );
    $msg->get_msg();

    echo msg_js( '', 1 );
    exit;

}

$cont_foto_esp = $q_foto_esp->num_rows;

$db->closeConnection();

$desc_pag = 'Relação de fotos d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L;

// adicionando o javascript
$cab_js = 'ajax/ajax_foto_det.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) )
    $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>



            <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet; ?>" />

            <p class="descript_page">RELAÇÃO DE FOTOS D<?php echo SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U; ?></p>

            <?php if ( $n_det_alt_foto >= 1 ) { ?>
            <p class="link_common"><a href="<?php echo SICOP_ABS_PATH; ?>incl/foto_esp.php?iddet=<?php echo $iddet; ?>" title="Cadastrar fotos">Cadastrar fotos especiais</a></p>
            <?php } ?>

            <?php include 'quali/det_basic.php'; ?>

            <p class="table_leg">Fotos</p>

            <div id="album_principal" class="album_foto">

                <?php

                if ( $cont_foto < 1 ) {

                    echo '<p class="p_q_no_result">Não há fotos.</p>';

                } else {

                    $d_fotos = '';

                    $pasta = SICOP_DET_FOLDER;

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
                        <a class="link_group_foto_det" rel="group_foto" href="<?php echo $img_det_path . $foto_g ?>" title="<?php echo $d_det['nome_det']; if ( !empty( $d_det['matricula'] ) ) echo ' - ' . formata_num( $d_det['matricula'] ) ?>">
                        <?php }; ?>
                        <img class="tumb_foto_det" src="<?php echo $foto_det ?>" alt="<?php echo $d_det['nome_det']; if ( !empty( $d_det['matricula'] ) ) echo ' - ' . formata_num( $d_det['matricula'] ) ?>" />
                        <?php if ( $amplia ){ ?></a><?php } ?>

                        <?php

                        if ( $n_det_alt_foto >= 1 and ( $n_chefia >= 4 || $n_incl >= 4 ) ) {

                            $disabled = '';
                            $img_bt   = 's_add_g.png';
                            $title    = 'Definir como foto principal';

                            if ( $id_foto == $foto_atual ) {
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

                            <input class="foto_bt" type="image" src="<?php echo $img_sys_path . $img_bt; ?>" name="def_foto_det[]" value="<?php echo $id_foto;?>" title="<?php echo $title; ?>" <?php echo $disabled; ?>/>
                            <input class="foto_bt" type="image" src="<?php echo $img_sys_path; ?>b_drop.png" name="del_foto_det[]" value="<?php echo $id_foto;?>" title="Excluir esta foto" />

                        </div>

                        <?php } // /if ( $n_det_alt_foto >= 1 and ( $n_chefia >= 4 || $n_incl >= 4 ) ) {  ?>

                    </div><!-- /div.icon_foto -->


                <?php

                    } // /while ( $d_fotos = $q_foto->fetch_object() ) {

                } // /if ( $cont_foto < 1 ) {

                ?>

            </div><!-- /div.album_foto -->

            <br style="clear: both"/>

            <p class="table_leg">Fotos Especiais e Tatuagens</p>

            <div id="album_especial" class="album_foto">

                <?php

                if ( $cont_foto_esp < 1 ) {

                    echo '<p class="p_q_no_result">Não há fotos.</p>';

                } else {

                    $d_fotos_esp = '';

                    $pasta = SICOP_DET_FOLDER;

                    while ( $d_fotos_esp = $q_foto_esp->fetch_object() ) {

                        $id_foto = $d_fotos_esp->id_foto;
                        $foto_g = $d_fotos_esp->foto_det_g;
                        $foto_p = $d_fotos_esp->foto_det_p;

                        $foto_det = ck_pic( $foto_g, $foto_p, false, 1 );

                        $amplia = true;
                        if ( empty( $foto_g ) or !is_file( $pasta . $foto_g ) ) {
                            $amplia = false;
                        }

                    ?>

                    <div class="icon_foto">

                        <?php if ( $amplia ){ ?>
                        <a class="link_group_foto_det" rel="group_foto_esp" href="<?php echo $img_det_path . $foto_g ?>" title="<?php echo $d_det['nome_det']; if ( !empty( $d_det['matricula'] ) ) echo ' - ' . formata_num( $d_det['matricula'] ) ?>">
                        <?php }; ?>
                        <img class="tumb_foto_det" src="<?php echo $foto_det ?>" alt="<?php echo $d_det['nome_det']; if ( !empty( $d_det['matricula'] ) ) echo ' - ' . formata_num( $d_det['matricula'] ) ?>" />
                        <?php if ( $amplia ){ ?></a><?php } ?>

                        <?php

                        if ( $n_det_alt_foto >= 1 and ( $n_chefia >= 4 || $n_incl >= 4 ) ) {

                            $disabled = '';
                            $img_bt   = 's_add_g.png';
                            $title    = 'Definir como foto principal';

                            if ( $id_foto == $foto_atual ) {
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


                            <input class="foto_bt" type="image" src="<?php echo $img_sys_path; ?>b_drop.png" name="del_foto_det_esp[]" value="<?php echo $id_foto;?>" title="Excluir esta foto" />

                        </div>

                        <?php } // /if ( $n_det_alt_foto >= 1 and ( $n_chefia >= 4 || $n_incl >= 4 ) ) {  ?>

                    </div><!-- /div.icon_foto -->


                <?php

                    } // /while ( $d_fotos_esp = $q_foto->fetch_object() ) {

                } // /if ( $cont_foto < 1 ) {

                ?>

            </div><!-- /div.album_foto -->

<?php include 'footer.php'; ?>