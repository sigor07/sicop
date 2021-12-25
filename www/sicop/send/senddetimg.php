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
$tipo_pag  = 'FOTO D' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U;

$n_det_alt_foto = get_session( 'n_det_alt_foto', 'int' );

if ( empty( $n_det_alt_foto ) or $n_det_alt_foto < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'perm';
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
    $msg['text']  = "Tentativa de acesso direto à página. ( $tipo_pag )";
    get_msg( $msg, 1 );

    echo $msg_falha;

    exit;

}

extract($_POST, EXTR_OVERWRITE);

$proced = empty( $proced ) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

if ( empty( $proced ) or $proced > 5 ) {

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
    $det_where = "( SELECT `cod_detento` FROM `det_fotos` WHERE `id_foto` = $id_foto LIMIT 1 )";
    $detento = dados_det( $det_where );

    $query = "UPDATE
                `detentos`
              SET
                `cod_foto` = $id_foto,
                `user_up` = $user,
                `data_up` = NOW(),
                `ip_up` = $ip
              WHERE
                `iddetento` = ( SELECT `cod_detento` FROM `det_fotos` WHERE `id_foto` = $id_foto LIMIT 1 )
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
        $msg['text']  = "Erro de atualização ( $tipo_pag ). \n\n $detento \n\n $valor_user \n\n $msg_err_mysql.";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo 0;
        exit;

    }

    $db->closeConnection();

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'ATUALIZAÇÃO DE FOTO DE ' . SICOP_DET_DESC_U;
    $msg['text']     = "Atualização de foto de " . SICOP_DET_DESC_L . ". \n\n $detento";
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

    $n_chefia = get_session( 'n_chefia', 'int' );

    if ( empty( $n_chefia ) or $n_chefia < 4 ) {

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

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `det_fotos` WHERE `id_foto` = $id_foto LIMIT 1 )";
    $detento = dados_det( $det_where );

    // para pegar as fotos, para exclui-las do hd depois
    $q_foto_det      = "SELECT `foto_det_g`, `foto_det_p` FROM `det_fotos` WHERE `id_foto` = $id_foto LIMIT 1";

    // para pegar o id da foto atual, para ser comparado com o id da foto q esta sendo excluida
    $q_id_foto_atual = "SELECT `iddetento`, `cod_foto` FROM `detentos` WHERE `iddetento` = ( SELECT `cod_detento` FROM `det_fotos` WHERE `id_foto` = $id_foto LIMIT 1 ) LIMIT 1";

    // para excluir o caminho da foto do banco
    $query_del       = "DELETE FROM `det_fotos` WHERE `id_foto` = $id_foto LIMIT 1";

    $db = SicopModel::getInstance();

    // executa a query para verificar se a foto q esta sendo excluida
    // é a que esta sendo utilizada autalmente na qualificativa
    $q_id_foto_atual = $db->query( $q_id_foto_atual );
    $cont_foto_atual = $q_id_foto_atual->num_rows;
    $foto_atual      = '';
    $iddet           = '';
    if ( $cont_foto_atual == 1 ) {

        $d_foto_atual = $q_id_foto_atual->fetch_object();
        $foto_atual   = $d_foto_atual->cod_foto;
        $iddet        = $d_foto_atual->iddetento;

    }

    // se for a mesma vai atualizar depois de excluir...
    $up_foto = false;
    if ( $id_foto == $foto_atual ) {
        $up_foto = true;
    }

    $q_foto_det = $db->query( $q_foto_det );
    $query_del  = $db->query( $query_del );

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
        $msg['text']  = "Erro de exclusão ( $tipo_pag ). \n\n $detento \n\n $valor_user \n\n $msg_err_mysql.";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo 0;
        exit;

    }

    $db->closeConnection();

    $pasta      = SICOP_DET_FOLDER;
    $d_foto_det = $q_foto_det->fetch_object();
    $foto_det_g = $d_foto_det->foto_det_g;
    $foto_det_p = $d_foto_det->foto_det_p;

    if ( !empty( $foto_det_g ) ) {
        if ( file_exists( $pasta . $foto_det_g ) ) {
            unlink( $pasta . $foto_det_g );
        }
    }

    if ( !empty( $foto_det_p ) ) {
        if ( file_exists( $pasta . $foto_det_p ) ) {
            unlink( $pasta . $foto_det_p );
        }
    }

    if ( $up_foto ) {
        set_last_pic( $iddet, 1 );
    }

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'EXCLUSÃO DE FOTO DE ' . SICOP_DET_DESC_U;
    $msg['text']     = "Exclusão de foto de " . SICOP_DET_DESC_L . ". \n\n $detento";
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

    $iddet = get_post( 'iddet', 'int' );
    if ( empty( $iddet ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;
        exit;

    }

    // pegar os dados do preso
    $detento = dados_det( $iddet );

    // pegar a matrícula e o nome do preso
    $query_det    = "SELECT `nome_det`, `matricula` FROM `detentos` WHERE `iddetento` = $iddet LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_det    = $model->query( $query_det );

    // fechando a conexao
    $model->closeConnection();

    $d_det        = $query_det->fetch_assoc();
    $nome_det     = $d_det['nome_det'];
    $matricula_sp = $d_det['matricula']; //matrícula sem ponto e traço
    $matricula    = !empty( $d_det['matricula'] ) ? formata_num( $d_det['matricula'] ) : '';

    $data         = date('d/m/Y');

    $user         = get_session( 'user_id', 'int' );
    $ip           = "'" . $_SERVER['REMOTE_ADDR'] . "'";

    $pq           = ' p';
    $img_g        = '';
    $img_p        = '';

    // Instanciamos o objeto Upload
    $handle = new Upload( $_FILES['foto_det'] );

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
        $handle->file_new_name_body            = $matricula_sp . ' ' . $data  . ' ' . $variant; // novo nome da foto
        $handle->image_text                    = /* $unidade."\n". */$nome_det . "\n" . $matricula . "\n" . $data; //texto q será escrito na imagem

        // Definimos a pasta para onde a imagem maior será armazenada
        //$pasta = SICOP_DET_IMG_PATH;
        $pasta = SICOP_DET_FOLDER;
        $handle->Process( "$pasta" );

        // Em caso de sucesso no upload podemos fazer outras ações como insert em um banco de cados
        if ( $handle->processed ) {

            $img_g = $handle->file_dst_name;

        } else {
            // Em caso de erro listamos o erro abaixo
            echo '<fieldset>';
            echo '  <legend>Erro encontrado!</legend>';
            echo '  Erro: ' . $handle->error;
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
        $handle->file_new_name_body            = $matricula_sp . ' ' . $data . $pq . ' ' . $variant; // novo nome da foto
        $handle->image_text                    = /* $unidade."\n".$nome_det."\n". */$matricula . "\n" . $data; //texto q será escrito na imagem

        // Definimos a pasta para onde a imagem thumbs será armazenada
        $handle->Process( "$pasta" );

        if ( $handle->processed ) {

            $img_p = $handle->file_dst_name;

        } else {
            // Em caso de erro listamos o erro abaixo
            echo '<fieldset>';
            echo '  <legend>Erro encontrado!</legend>';
            echo '  Erro: ' . $handle->error;
            echo '</fieldset>';
        }

        if ( !empty( $img_g ) and !empty( $img_p ) ) {

            $q_foto_det = "INSERT INTO
                             `det_fotos`
                             (
                                `cod_detento`,
                                `foto_det_g`,
                                `foto_det_p`,
                                `user_add`,
                                `ip_add`
                             )
                           VALUES
                             (
                                $iddet,
                                '$img_g',
                                '$img_p',
                                $user,
                                $ip
                             )";


            $lastid    = 0;
            $erromysql = '';
            $success   = TRUE;

            $db = SicopModel::getInstance();
            $db->query( 'START TRANSACTION' );

            $q_foto_det = $db->query( $q_foto_det );

            if ( !$q_foto_det ) {

                // pegar a mensagem de erro mysql
                $erromysql .= $db->getErrorMsg();
                $erromysql .= PHP_EOL;
                $success = FALSE;

            }

            if ( $success ) $lastid = $db->lastInsertId();

            $q_up_det = "UPDATE
                           `detentos`
                         SET
                           `cod_foto` = $lastid,
                           `user_up` = $user,
                           `data_up` = NOW(),
                           `ip_up` = $ip
                         WHERE
                           `iddetento` = $iddet
                         LIMIT 1";

            $q_up_det = $db->query( $q_up_det );

            if ( !$q_up_det ) {

                // pegar a mensagem de erro mysql
                $erromysql .= $db->getErrorMsg();
                $erromysql .= PHP_EOL;
                $success = FALSE;

            }

            if ( !$success ) {

                $db->query( 'ROLLBACK' );
                $db->closeConnection();

                // montar a mensagem q será salva no log
                $msg = array();
                $msg['tipo']  = 'err';
                $msg['text']  = "Erro de cadastramento ( $tipo_pag ).\n\n $detento \n\n $erromysql";
                $msg['linha'] = __LINE__;
                get_msg( $msg, 1 );

                // Excluimos os arquivos temporarios
                $handle-> Clean();

                echo 0;

                exit;

            }

            $db->query( 'COMMIT' );
            $db->closeConnection();

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'desc';
            $msg['entre_ch'] = 'ALTERAÇÃO DE FOTO DE ' . SICOP_DET_DESC_U;
            $msg['text']     = "Alteração de foto de " . SICOP_DET_DESC_L . ". \n\n $detento ";
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
        echo '<p class="link_common"><a href="javascript: history.go(-1)">Voltar</a></p>';
        $mensagem = "ERRO -> Erro de cadastramento de foto de " . SICOP_DET_DESC_L . ". Arquivo: $foto_orig. Erro da classe upload: $handle->error";
        salvaLog($mensagem);
    }

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */
} else if ( $proced == 4 ){ //CADASTRAMENTO MULTIPLO - FOTOS ESPECIAIS
/*
 * -------------------------------------------------------------------
 * PARTE RESPONSAVEL PELO CADASTRAMENTO DE MULTIPLAS FOTOS
 * -------------------------------------------------------------------
 */
    $proced_tipo_pag = 'CADASTRAMENTO DE MULTIPLAS FOTOS - ' . $tipo_pag;

    $iddet = get_post( 'iddet', 'int' );
    if ( empty( $iddet ) ) {

        // montar a mensagem q será salva no log
        $msg = sysmsg::create_msg();
        $msg->set_msg_type( SM_TYPE_ERR );
        $msg->set_msg( "Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. Operação cancelada" );
        $msg->add_parenteses( $proced_tipo_pag );
        $msg->add_quebras( 2 );
        $msg->set_msg( 'Linha: ' .  __LINE__ );
        $msg->get_msg();

        echo 0;

        exit;

    }

    // pegar os dados do preso
    $detento = dados_det( $iddet );

    // pegar a matrícula e o nome do preso
    $query_det    = "SELECT `nome_det`, `matricula` FROM `detentos` WHERE `iddetento` = $iddet LIMIT 1";

    $db = SicopModel::getInstance();
    $query_det    = $db->query( $query_det );
    $db->closeConnection();

    $d_det        = $query_det->fetch_assoc();
    $nome_det     = $d_det['nome_det'];
    $matricula_sp = $d_det['matricula']; //matrícula sem ponto e traço
    $matricula    = !empty( $d_det['matricula'] ) ? formata_num( $d_det['matricula'] ) : '';

    $data         = date('d/m/Y');
    $user         = get_session( 'user_id', 'int' );
    $ip           = "'" . $_SERVER['REMOTE_ADDR'] . "'";

    $pq           = ' p';
    $img_g        = '';
    $img_p        = '';

    // Instanciamos o objeto Upload
    $handle = new Upload( $_FILES['Filedata'] );

    // Então verificamos se o arquivo foi carregado corretamente
    if ( $handle->uploaded ) {

        // cria-se uma variante para colocar no nome da foto para evitar colisão
        $variant = sha1( microtime() . rand() );

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
        $handle->file_new_name_body            = $matricula_sp . ' ' . $data  . ' ' . $variant; // novo nome da foto
        $handle->image_text                    = /* $unidade."\n". */$nome_det . "\n" . $matricula . "\n" . $data; //texto q será escrito na imagem

        // Definimos a pasta para onde a imagem maior será armazenada
        //$pasta = SICOP_DET_IMG_PATH;
        $pasta = SICOP_DET_FOLDER;
        $handle->Process( "$pasta" );

        // Em caso de sucesso no upload podemos fazer outras ações como insert em um banco de cados
        if ( $handle->processed ) {

            $img_g = $handle->file_dst_name;

        } else {
            // Em caso de erro listamos o erro abaixo
            echo '<fieldset>';
            echo '  <legend>Erro encontrado!</legend>';
            echo '  Erro: ' . $handle->error;
            echo '</fieldset>';
        }

        // Aqui nos devifimos nossas configurações de imagem do thumbs

        // cria-se uma variante para colocar no nome da foto para evitar colisão
        $variant = sha1( microtime() . rand() );

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
        $handle->file_new_name_body            = $matricula_sp . ' ' . $data . $pq . ' ' . $variant; // novo nome da foto
        $handle->image_text                    = /* $unidade."\n".$nome_det."\n". */$matricula . "\n" . $data; //texto q será escrito na imagem

        // Definimos a pasta para onde a imagem thumbs será armazenada
        $handle->Process( "$pasta" );

        if ( $handle->processed ) {

            $img_p = $handle->file_dst_name;

        } else {
            // Em caso de erro listamos o erro abaixo
            echo '<fieldset>';
            echo '  <legend>Erro encontrado!</legend>';
            echo '  Erro: ' . $handle->error;
            echo '</fieldset>';
        }

        if ( !empty( $img_g ) and !empty( $img_p ) ) {

            $q_foto_det = "INSERT INTO
                             `detentos_fotos_esp`
                             (
                                `cod_detento`,
                                `foto_det_g`,
                                `foto_det_p`,
                                `user_add`,
                                `ip_add`
                             )
                           VALUES
                             (
                                $iddet,
                                '$img_g',
                                '$img_p',
                                $user,
                                $ip
                             )";


            $lastid    = 0;
            $erromysql = '';
            $success   = TRUE;

            $db = SicopModel::getInstance();
            $q_foto_det = $db->query( $q_foto_det );

            if ( !$q_foto_det ) {

                // pegar a mensagem de erro mysql
                $erromysql = $db->getErrorMsg();
                $db->closeConnection();
                $success = FALSE;

                // montar a mensagem q será salva no log
                $msg = array();
                $msg['tipo']  = 'err';
                $msg['text']  = "Erro de cadastramento ( $tipo_pag ).\n\n $detento \n\n $erromysql";
                $msg['linha'] = __LINE__;
                get_msg( $msg, 1 );

                // Excluimos os arquivos temporarios
                $handle-> Clean();

                echo 0;

                exit;

            }

            $db->closeConnection();

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'desc';
            $msg['entre_ch'] = 'CADASTRAMENTO DE FOTO ESPECIAL DE ' . SICOP_DET_DESC_U;
            $msg['text']     = "Cadastramento de foto especial de " . SICOP_DET_DESC_L . ". \n\n $detento ";
            get_msg( $msg, 1 );

            // Excluimos os arquivos temporarios
            $handle-> Clean();

            echo 1;

            exit;

        }

        // Excluimos os arquivos temporarios
        $handle-> Clean();

    } else {
        echo 0;
    }

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO CADASTRAMENTO DE MULTIPLAS FOTOS
 * -------------------------------------------------------------------
 */
} else if ( $proced == 5 ){ //EXCLUSÃO DE FOTOS ESPECIAIS
/*
 * -------------------------------------------------------------------
 * PARTE RESPONSAVEL PELA EXCLUSÃO DE FOTOS ESPECIAIS
 * -------------------------------------------------------------------
 */

    /*
     * aqui coloca a função da página
     */
    $proced_tipo_pag = 'EXCLUSÃO DE FOTO ESPECIAL - ' . $tipo_pag;

    $n_chefia = get_session( 'n_chefia', 'int' );

    if ( empty( $n_chefia ) or $n_chefia < 4 ) {

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
        $msg = sysmsg::create_msg();
        $msg->set_msg_type( SM_TYPE_ERR );
        $msg->set_msg( 'Identificador da foto em branco em branco. Operação cancelada' );
        $msg->add_parenteses( $proced_tipo_pag );
        $msg->add_quebras( 2 );
        $msg->set_msg( 'Linha: ' .  __LINE__ );
        $msg->get_msg();

        echo $msg_falha;

        exit;

    }

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `detentos_fotos_esp` WHERE `id_foto` = $id_foto LIMIT 1 )";
    $detento = dados_det( $det_where );

    // para pegar as fotos, para exclui-las do hd depois
    $q_foto_det      = "SELECT `foto_det_g`, `foto_det_p` FROM `detentos_fotos_esp` WHERE `id_foto` = $id_foto LIMIT 1";

    // para excluir o caminho da foto do banco
    $query_del       = "DELETE FROM `detentos_fotos_esp` WHERE `id_foto` = $id_foto LIMIT 1";

    $db = SicopModel::getInstance();

    $q_foto_det = $db->query( $q_foto_det );
    $query_del  = $db->query( $query_del );

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
        $msg['text']  = "Erro de exclusão ( $tipo_pag ). \n\n $detento \n\n $valor_user \n\n $msg_err_mysql.";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo 0;
        exit;

    }

    $db->closeConnection();

    $pasta      = SICOP_DET_FOLDER;
    $d_foto_det = $q_foto_det->fetch_object();
    $foto_det_g = $d_foto_det->foto_det_g;
    $foto_det_p = $d_foto_det->foto_det_p;

    if ( !empty( $foto_det_g ) ) {
        if ( file_exists( $pasta . $foto_det_g ) ) {
            unlink( $pasta . $foto_det_g );
        }
    }

    if ( !empty( $foto_det_p ) ) {
        if ( file_exists( $pasta . $foto_det_p ) ) {
            unlink( $pasta . $foto_det_p );
        }
    }

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'EXCLUSÃO DE FOTO ESPECIAL DE ' . SICOP_DET_DESC_U;
    $msg['text']     = "Exclusão de foto de " . SICOP_DET_DESC_L . ". \n\n $detento";
    get_msg( $msg, 1 );

    echo 1;

    exit;


/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELA EXCLUSÃO DE FOTOS ESPECIAIS
 * -------------------------------------------------------------------
 */
}

?>