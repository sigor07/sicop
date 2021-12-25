<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$tipo_pag     = 'RELAÇÃO DE FOTOS D' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U . ' - AJAX';
$img_sys_path = SICOP_SYS_IMG_PATH;
$msg_falha    = '<p class="q_error">FALHA!</p>';

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

$data_ini = get_post( 'data_ini', 'busca' );
$data_fim = get_post( 'data_fim', 'busca' );
$tipo     = get_post( 'tipo', 'int' );

$n_chefia       = get_session( 'n_chefia', 'int' );
$n_incl         = get_session( 'n_incl', 'int' );
$n_det_alt_foto = get_session( 'n_det_alt_foto', 'int' );

$table_foto    = 'det_fotos';
$table_base    = 'detentos';
$id_base       = 'iddetento';
$nome_base     = 'nome_det';
$uid           = 'detentos.iddetento, detentos.matricula AS uid';
$table_field_g = 'foto_det_g';
$table_field_p = 'foto_det_p';
$cod           = 'cod_detento';
$file_flag     = 1;
$pasta         = SICOP_DET_FOLDER;
$img_path      = SICOP_DET_IMG_PATH;
$link          = SICOP_ABS_PATH . 'detento/detalhesdet.php?iddet=';

switch ( $tipo ) {
    default:
    case 1:
        $table_foto    = 'det_fotos';
        $table_base    = 'detentos';
        $id_base       = 'iddetento';
        $nome_base     = 'nome_det';
        $uid           = 'detentos.iddetento, detentos.matricula AS uid';
        $table_field_g = 'foto_det_g';
        $table_field_p = 'foto_det_p';
        $cod           = 'cod_detento';
        $file_flag     = 1;
        $pasta         = SICOP_DET_FOLDER;
        $img_path      = SICOP_DET_IMG_PATH;
        $link          = SICOP_ABS_PATH . 'detento/detalhesdet.php?iddet=';
        break;
    case 2:
        $table_foto    = 'visita_fotos';
        $table_base    = 'visitas';
        $id_base       = 'idvisita';
        $nome_base     = 'nome_visit';
        $uid           = 'visitas.idvisita AS uid';
        $table_field_g = 'foto_visit_g';
        $table_field_p = 'foto_visit_p';
        $cod           = 'cod_visita';
        $file_flag     = 2;
        $pasta         = SICOP_VISIT_FOLDER;
        $img_path      = SICOP_VISIT_IMG_PATH;
        $link          = SICOP_ABS_PATH . 'visita/detalvisit.php?idvisit=';
        break;

}

$where = '';

if ( !empty( $data_ini ) or !empty( $data_fim ) ) {

    if ( !empty( $data_ini ) and !empty( $data_fim ) ) {

        $where = "WHERE DATE( $table_foto.data_add ) BETWEEN STR_TO_DATE( '$data_ini', '%d/%m/%Y' ) AND STR_TO_DATE( '$data_fim', '%d/%m/%Y' )";

    } else {

        $data = !empty( $data_ini ) ? $data_ini : $data_fim;

        $where = "WHERE DATE( $table_foto.data_add ) = STR_TO_DATE( '$data', '%d/%m/%Y' )";

    }

}

$q_foto = "SELECT
              $table_foto.id_foto,
              $table_foto.$table_field_g AS foto_g,
              $table_foto.$table_field_p AS foto_p,
              $table_foto.user_add,
              $table_foto.data_add,
              $table_foto.ip_add,
              $table_base.$nome_base AS nome,
              $uid
            FROM
              $table_foto
              LEFT JOIN $table_base ON $table_foto.$cod = $table_base.$id_base
              $where
            ORDER BY
              $table_foto.data_add, $table_base.$nome_base
            -- limit 100";

//depur($q_foto);
//exit;

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



        if ( $cont_foto < 1 ) {

            echo '<p class="p_q_no_result">Não há fotos.</p>';

        } else {
            ?>

            <p class="p_q_info">Essa consulta retornou <?php echo $cont_foto; ?> fotos.</p>

            <?php

            $d_fotos    = '';
            $rel        = 'group_foto';
            $input_name = 'del_foto';

            while ( $d_fotos = $q_foto->fetch_object() ) {

                $id_foto = $d_fotos->id_foto;
                $foto_g = $d_fotos->foto_g;
                $foto_p = $d_fotos->foto_p;
                $id     = ( $tipo == 1 ? $d_fotos->iddetento : $d_fotos->uid );

                $foto_f = ck_pic( $foto_g, $foto_p, false, $file_flag );

                $amplia = true;
                if ( empty( $foto_g ) or !is_file( $pasta . $foto_g ) ) {
                    $amplia = false;
                }

                ?>

                <div class="icon_foto">

                    <?php if ( $amplia ){ ?>
                    <a class="link_group_foto" rel="<?php echo $rel; ?>" href="<?php echo $img_path . $foto_g ?>" title="<?php echo $d_fotos->nome; if ( !empty( $d_fotos->uid ) ) echo ' - ' . ( $tipo == 1 ? formata_num( $d_fotos->uid ) : $d_fotos->uid ) ?>">
                    <?php }; ?>
                    <img class="tumb_foto_det" src="<?php echo $foto_f ?>" alt="<?php echo $d_fotos->nome; if ( !empty( $d_fotos->uid ) ) echo ' - ' . formata_num( $d_fotos->uid ) ?>" />
                    <?php if ( $amplia ){ ?></a><?php } ?>

                    <div class="foto_bts">
                        <?php if ( $n_det_alt_foto >= 1 and ( $n_chefia >= 4 || $n_incl >= 4 ) ) { ?>
                        <input class="foto_bt" type="image" src="<?php echo $img_sys_path; ?>b_drop.png" name="<?php echo $input_name; ?>[]" value="<?php echo $id_foto;?>" title="Excluir esta foto" />
                        <?php }  ?>
                        <a href="<?php echo $link . $id; ?>" title="visualizar" target="_blank" >
                            <img src="<?php echo $img_sys_path; ?>b_view.png" alt="visualizar" class="icon_button" style="margin-top: -10px;" />
                        </a>
                    </div>

                </div><!-- /div.icon_foto -->

                <?php

                    } // /while ( $d_fotos = $q_foto->fetch_object() ) {

                } // /if ( $cont_foto < 1 ) {
            ?>