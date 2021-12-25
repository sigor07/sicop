<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$pag       = link_pag();
$tipo      = '';
$msg_falha = '<p class="q_error">FALHA!</p>';

/*
 * colocar o tipo de página, o setor que ela acessa
 * ex: PECÚLIO, CADASTRO, INCLUSÃO...
 */
$tipo_pag  = 'FOTO DE VISITANTE';

$n_rol = get_session( 'n_rol', 'int' );
$n_n   = 3;
if ( $n_rol < $n_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $tipo_pag;
    get_msg( $msg, 1 );

    echo $msg_falha;

    exit;

}

$is_post = is_post();
if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página de upload de fotos de visitantes.';
    get_msg( $msg, 1 );

    echo $msg_falha;

    exit;

}

extract($_POST, EXTR_OVERWRITE);

$proced = empty( $proced ) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

if ( empty( $proced ) or $proced > 3 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Número de procedimento em branco ou inválido. Operação cancelada ( $tipo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo $msg_falha;

    exit;

}


$user = get_session( 'user_id', 'int' );
$ip   = "'" . $_SERVER['REMOTE_ADDR'] . "'";

if ( $proced == 1 ){ // ATUALIZAÇÃO
/*
 * -----------------------------------------------------------
 * PARTE RESPONSAVEL PELA ATUALIZAÇÃO
 * -----------------------------------------------------------
 */

    /*
     * aqui coloca a função da página
     */
    $proced_tipo_pag = 'ATUALIZAÇÃO - ' . $tipo_pag;

    $id_foto = empty( $id_foto ) ? '' : (int)$id_foto;
    if ( empty( $id_foto ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador da foto em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;
        exit;

    }

    // pegar os dados do preso
    $visit_where = "( SELECT `cod_visita` FROM `visita_fotos` WHERE `id_foto` = $id_foto LIMIT 1 )";
    $visita = dados_visit( $visit_where );

    $query = "UPDATE
                `visitas`
              SET
                `cod_foto` = $id_foto,
                `user_up` = $user,
                `data_up` = NOW(),
                `ip_up` = $ip
              WHERE
                `idvisita` = ( SELECT `cod_visita` FROM `visita_fotos` WHERE `id_foto` = $id_foto LIMIT 1 )
              LIMIT 1";

    $db = SicopModel::getInstance();

    $query = $db->query( $query );

    $success = TRUE;
    if( !$query ) {

        $success = FALSE;

        // pegar a mensagem de erro mysql
        $msg_err_mysql = $db->getErrorMsg();
        $db->closeConnection();

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de atualização ( $tipo_pag ). \n\n $visita \n\n $valor_user \n\n $msg_err_mysql.";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo 0;
        exit;

    }

    $db->closeConnection();

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'ATUALIZAÇÃO DE FOTO DE VISITANTE';
    $msg['text']     = "Atualização de foto de visitante. \n\n $visita";
    get_msg( $msg, 1 );

    echo 1;

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELA ATUALIZAÇÃO
 * -------------------------------------------------------------------
 */
} else if ( $proced == 2 ){ //EXCLUSÃO
/*
 * -------------------------------------------------------------------
 * PARTE RESPONSAVEL PELA EXCLUSÃO
 * -------------------------------------------------------------------
 */

    /*
     * aqui coloca a função da página
     */
    $proced_tipo_pag = 'EXCLUSÃO - ' . $tipo_pag;

    if ( empty( $n_rol ) or $n_rol < 4 ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'perm';
        $msg['entre_ch'] = $proced_tipo_pag;
        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    $id_foto = empty( $id_foto ) ? '' : (int)$id_foto;
    if ( empty( $id_foto ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador da foto em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;
        exit;

    }

    // pegar os dados do visitante
    $visit_where = "( SELECT `cod_visita` FROM `visita_fotos` WHERE `id_foto` = $id_foto LIMIT 1 )";
    $visita = dados_visit( $visit_where );

    // para pegar as fotos, para exclui-las do hd depois
    $q_foto_visit    = "SELECT `foto_visit_g`, `foto_visit_p` FROM `visita_fotos` WHERE `id_foto` = $id_foto LIMIT 1";

    // para pegar o id da foto atual, para ser comparado com o id da foto q esta sendo excluida
    $q_id_foto_atual = "SELECT `idvisita`, `cod_foto` FROM `visitas` WHERE `idvisita` = ( SELECT `cod_visita` FROM `visita_fotos` WHERE `id_foto` = $id_foto LIMIT 1 ) LIMIT 1";

    // para excluir o caminho da foto do banco
    $query_del       = "DELETE FROM `visita_fotos` WHERE `id_foto` = $id_foto LIMIT 1";

    $db = SicopModel::getInstance();

    // executa a query para verificar se a foto q esta sendo excluida
    // é a que esta sendo utilizada autalmente na qualificativa
    $q_id_foto_atual = $db->query( $q_id_foto_atual );
    $cont_foto_atual = $q_id_foto_atual->num_rows;
    $foto_atual      = '';
    $idvisit         = '';
    if ( $cont_foto_atual == 1 ) {

        $d_foto_atual = $q_id_foto_atual->fetch_object();
        $foto_atual   = $d_foto_atual->cod_foto;
        $idvisit      = $d_foto_atual->idvisita;

    }

    // se for a mesma vai atualizar depois de excluir...
    $up_foto = false;
    if ( $id_foto == $foto_atual ) {
        $up_foto = true;
    }

    $q_foto_visit = $db->query( $q_foto_visit );

    if ( !$q_foto_visit ) {

        // pegar a mensagem de erro mysql
        $msg_err_mysql = $db->getErrorMsg();

        // gerar a mensagem q será salva no log
        $msg = sysmsg::create_msg();
        $msg->set_msg_type( SM_TYPE_ERR );
        $msg->add_quebras( 1 );
        $msg->set_msg_pre_def( SM_QUERY_FAIL );
        $msg->add_parenteses( '$motivo_pag' );
        $msg->add_quebras( 2 );
        $msg->set_msg( $msg_err_mysql );
        $msg->get_msg();

    }

    $query_del    = $db->query( $query_del );

    $success = TRUE;
    if( !$query_del ) {

        $success = FALSE;

        // pegar a mensagem de erro mysql
        $msg_err_mysql = $db->getErrorMsg();
        $db->closeConnection();

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de exclusão ( $tipo_pag ). \n\n $visita \n\n $valor_user \n\n $msg_err_mysql.";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo 0;
        exit;

    }

    $db->closeConnection();

    $pasta        = SICOP_VISIT_FOLDER;
    $d_foto_visit = $q_foto_visit->fetch_object();
    $foto_visit_g = $d_foto_visit->foto_visit_g;
    $foto_visit_p = $d_foto_visit->foto_visit_p;

    if ( !empty( $foto_visit_g ) ) {
        if ( file_exists( $pasta . $foto_visit_g ) ) {
            unlink( $pasta . $foto_visit_g );
        }
    }

    if ( !empty( $foto_visit_p ) ) {
        if ( file_exists( $pasta . $foto_visit_p ) ) {
            unlink( $pasta . $foto_visit_p );
        }
    }

    if ( $up_foto ) {
        set_last_pic( $idvisit, 2 );
    }

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'EXCLUSÃO DE FOTO DE VISITANTE';
    $msg['text']     = "Exclusão de foto de visitante. \n\n $visita";
    get_msg( $msg, 1 );

    echo 1;

    exit;


/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELA EXCLUSÃO
 * -------------------------------------------------------------------
 */
} else if ( $proced == 3 ){ //CADASTRAMENTO
/*
 * -------------------------------------------------------------------
 * PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */

    $proced_tipo_pag = 'CADASTRAMENTO - ' . $tipo_pag;

    $idvisit = get_post( 'idvisit', 'int' );
    if ( empty( $idvisit ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador do visitante em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;
        exit;

    }

    // pegar os dados do preso
    $visita = dados_visit( $idvisit );

    // pegar os dados do visitante
    $query_visit = "SELECT `idvisita`, `nome_visit` FROM `visitas` WHERE `idvisita` = $idvisit LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_visit = $model->query( $query_visit );

    // fechando a conexao
    $model->closeConnection();

    $d_vist     = $query_visit->fetch_assoc();
    $idv        = $d_vist['idvisita'];
    $nome_visit = $d_vist['nome_visit'];

    $data         = date('d/m/Y');

    $user         = get_session( 'user_id', 'int' );
    $ip           = "'" . $_SERVER['REMOTE_ADDR'] . "'";

    $pq           = ' p';
    $img_g        = '';
    $img_p        = '';

    // Instanciamos o objeto Upload
    $handle = new Upload( $_FILES['foto_visit'] );

    $foto_orig = $handle->file_src_name;

    // Então verificamos se o arquivo foi carregado corretamente
    if ( $handle->uploaded ) {

        // cria-se uma variante para colocar no nome da foto para evitar colisão
        $variant = sha1( microtime() );

        // Definimos as configurações desejadas da imagem maior
        $handle->image_resize                  = true;      // redimensionar a imagem
        $handle->image_ratio                   = true;      // manter o aspect ratio
        $handle->image_rotate                  = 90;        // rotaciona a imagem
        $handle->image_x                       = 640;       // tamanho da imagem redimensionada
        $handle->image_y                       = 480;
        $handle->file_auto_rename              = false;     // nao renomeia automaticamente
        $handle->file_overwrite                = true;      // sobrescreve se houver outro arquivo com o mesmo nome
        $handle->image_ratio_no_zoom_in        = true;      // nao redimensiona se a imagem for menor
        $handle->image_text_background_percent = 100;       // transparencia no fundo do texto
        $handle->image_text_padding            = 5;         // largura da borda do fundo
        $handle->image_text_y                  = -5;        // posicionamento da imagem -> em baixo no centro
        $handle->image_text_color              = '#000000'; // cor do texto
        $handle->image_text_background         = '#FFFFFF'; // fundo no texto
        $handle->file_new_name_body            = $idvisit . ' ' . $data . ' ' . $variant; // novo nome da foto
        $handle->image_text                    = $nome_visit . ' (' . $idvisit . ')' . "\n" . $data; //texto q será escrito na imagem

        // Definimos a pasta para onde a imagem maior será armazenada
        $pasta = SICOP_VISIT_FOLDER;
        $handle->Process( "$pasta" );

        // Em caso de sucesso no upload podemos fazer outras ações como insert em um banco de cados
        if ( $handle->processed ) {

            $img_g = $handle->file_dst_name;

        } else {
            // Em caso de erro listamos o erro abaixo
            echo '<fieldset>';
            echo '  <legend>Erro encontrado!</legend>';
            echo '  Erro: ' . $handle->error . '';
            echo '</fieldset>';
        }

        // Aqui nos devifimos nossas configurações de imagem do thumbs

        // cria-se uma variante para colocar no nome da foto para evitar colisão
        $variant = sha1( microtime() );

        $handle->image_resize                  = true;
        $handle->image_ratio                   = true;
        $handle->image_rotate                  = 90;
        $handle->image_x                       = 190;
        $handle->image_y                       = 150;
        $handle->file_auto_rename              = false;
        $handle->file_overwrite                = true;
        $handle->image_ratio_no_zoom_in        = true;
        $handle->image_text_font               = 5;
        $handle->image_text_background_percent = 100;
        $handle->image_text_padding            = 5;
        $handle->image_text_y                  = -5;
        $handle->image_text_color              = '#000000'; // cor do texto
        $handle->image_text_background         = '#FFFFFF'; // fundo no texto
        $handle->file_new_name_body            = $idvisit . ' ' . $data . $pq . ' ' . $variant; // novo nome da foto
        $handle->image_text                    = $idvisit . "\n" . $data; //texto q será escrito na imagem

        // Definimos a pasta para onde a imagem thumbs será armazenada
        $handle->Process( "$pasta" );

        if ( $handle->processed ) {

            $img_p = $handle->file_dst_name;

        } else {
            // Em caso de erro listamos o erro abaixo
            echo '<fieldset>';
            echo '  <legend>Erro encontrado!</legend>';
            echo '  Erro: ' . $handle->error . '';
            echo '</fieldset>';
        }

        if ( !empty( $img_g ) and !empty( $img_p ) ) {

            $q_foto_visit = "INSERT INTO
                               `visita_fotos`
                               (
                                  `cod_visita`,
                                  `foto_visit_g`,
                                  `foto_visit_p`,
                                  `user_add`,
                                  `ip_add`
                               )
                             VALUES
                               (
                                  $idvisit,
                                  '$img_g',
                                  '$img_p',
                                  $user,
                                  $ip
                               )";


            $lastid    = 0;
            $erromysql = '';
            $success   = TRUE;

            $db = SicopModel::getInstance();
            $db->transaction();

            $q_foto_visit = $db->query( $q_foto_visit );

            if ( !$q_foto_visit ) {

                // pegar a mensagem de erro mysql
                $erromysql .= $db->getErrorMsg();
                $erromysql .= PHP_EOL;
                $success = FALSE;

            }

            if ( $success ) $lastid = $db->lastInsertId();

            $q_up_det = "UPDATE
                           `visitas`
                         SET
                           `cod_foto` = $lastid,
                           `user_up` = $user,
                           `data_up` = NOW(),
                           `ip_up` = $ip
                         WHERE
                           `idvisita` = $idvisit
                         LIMIT 1";

            $q_up_det = $db->query( $q_up_det );

            if ( !$q_up_det ) {

                // pegar a mensagem de erro mysql
                $erromysql .= $db->getErrorMsg();
                $erromysql .= PHP_EOL;
                $success = FALSE;

            }

            if ( !$success ) {

                $db->rollback();

                // montar a mensagem q será salva no log
                $msg = array();
                $msg['tipo']  = 'err';
                $msg['text']  = "Erro de alteração de imagem de visitante.\n\n $visita \n\n $erromysql";
                $msg['linha'] = __LINE__;
                get_msg( $msg, 1 );

                // Excluimos os arquivos temporarios
                $handle-> Clean();

                echo 0;

                exit;

            }

            $db->commit();

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'desc';
            $msg['entre_ch'] = 'ALTERAÇÃO DE IMAGEM DE VISITANTE';
            $msg['text']     = "Alteração de imagem de visitante. \n\n $visita ";
            get_msg( $msg, 1 );

            // Excluimos os arquivos temporarios
            $handle-> Clean();

            echo msg_js( '', 1 );

            exit;

        }

        // Excluimos os arquivos temporarios
        $handle-> Clean();

    } else {
        // Em caso de erro listamos o erro abaixo
        echo '<div id="grupo"><fieldset>';
        echo '  <legend>Erro encontrado!</legend>';
        echo '  Erro: ' . $handle->error;
        echo '</fieldset></div>';
        echo '<p align="center"><a href="javascript: history.go(-1)">Voltar</a></p>';
        $mensagem = "ERRO -> Erro de cadastramento de foto de visitante. Arquivo: $foto_orig. Erro da classe upload: $handle->error";
        salvaLog($mensagem);
    }

}
?>