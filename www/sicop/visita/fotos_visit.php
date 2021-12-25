<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag            = link_pag();
$tipo_pag       = 'RELAÇÃO DE FOTOS DO VISITANTE';
$img_visit_path = SICOP_VISIT_IMG_PATH;
$img_sys_path   = SICOP_SYS_IMG_PATH;

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$idvisit = get_get( 'idvisit', 'int' );

if ( empty( $idvisit ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página. Identificador do visitante em branco. ( $tipo_pag )";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
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

    echo msg_js( '', 1 );
    exit;

}

$cont_foto = $q_foto->num_rows;

$q_id_foto_atual = "SELECT `cod_foto` FROM `visitas` WHERE `idvisita` = $idvisit LIMIT 1";
$q_id_foto_atual = $db->query( $q_id_foto_atual );
if ( !$q_id_foto_atual ) {

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( IDENTIFICADOR DA FOTO DO VISITANTE - $tipo_pag ). \n\n $msg_err_mysql";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$db->closeConnection();

$cont_foto_atual = $q_id_foto_atual->num_rows;
if ( $cont_foto_atual < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "A consulta retornou 0 ocorrências ( IDENTIFICADOR DA FOTO DO VISITANTE - $tipo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$d_foto_atual = $q_id_foto_atual->fetch_object();
$foto_atual   = $d_foto_atual->cod_foto;

$desc_pag = 'Relação de fotos do visitante';

// adicionando o javascript
$cab_js = 'ajax/ajax_foto_visit.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 7 );
$trail->output();
?>

            <input type="hidden" name="idvisit" id="idvisit" value="<?php echo $idvisit; ?>" />

            <p class="descript_page">RELAÇÃO DE FOTOS DE VISITANTE</p>

            <?php include 'quali/visit_basic.php'; ?>

            <p class="table_leg">Fotos</p>

            <div id="album_principal" class="album_foto">

                <?php

                if ( $cont_foto < 1 ) {

                    echo '<p class="p_q_no_result">Não há fotos.</p>';

                } else {

                    $d_fotos = '';

                    while ( $d_fotos = $q_foto->fetch_object() ) {

                        $id_foto = $d_fotos->id_foto;
                        $foto_g = $d_fotos->foto_visit_g;
                        $foto_p = $d_fotos->foto_visit_p;

                        $foto_visit = ck_pic( $foto_g, $foto_p, false, 2 );

                        $pasta = SICOP_VISIT_FOLDER;

                        $amplia = true;
                        if ( empty( $foto_g ) or !is_file( $pasta . $foto_g ) ) {
                            $amplia = false;
                        }

                    ?>

                    <div class="icon_foto">

                        <?php if ( $amplia ){ ?>
                        <a class="link_group_foto_visit" rel="group_foto" href="<?php echo $img_visit_path . $foto_g ?>" title="<?php echo $d_visit['nome_visit']; ?>">
                        <?php }; ?>
                        <img class="tumb_foto_visit" src="<?php echo $foto_visit ?>" alt="<?php echo $d_visit['nome_visit']; ?>" />
                        <?php if ( $amplia ){ ?></a><?php } ?>

                        <?php

                        if ( $n_rol >= 4 ) {

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

                            <input class="foto_bt" type="image" src="<?php echo $img_sys_path . $img_bt; ?>" name="def_foto_visit[]" value="<?php echo $id_foto;?>" title="<?php echo $title; ?>" <?php echo $disabled; ?>/>
                            <input class="foto_bt" type="image" src="<?php echo $img_sys_path; ?>b_drop.png" name="del_foto_visit[]" value="<?php echo $id_foto;?>" title="Excluir esta foto" />

                        </div>

                        <?php } // /if ( $n_visit_alt_foto >= 1 and ( $n_chefia >= 4 || $n_incl >= 4 ) ) {  ?>

                    </div><!-- /div.icon_foto -->


                <?php

                    } // /while ( $d_fotos = $q_foto->fetch_object() ) {

                } // /if ( $cont_foto < 1 ) {

                ?>

            </div><!-- /div.album_foto -->

<?php include 'footer.php'; ?>