<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';

$pag = link_pag();
$tipo = '';

$iniciais      = get_session( 'iniciais' );
$sigla_setor   = get_session( 'sigla_setor' );
$diretor_g     = get_session( 'diretor_geral' );
$diretor_s     = get_session( 'diretor_seg' );
$titulo        = get_session( 'titulo' );
$secretaria    = get_session( 'secretaria' );
$coordenadoria = get_session( 'coordenadoria' );
$unidadecurto  = get_session( 'unidadecurto' );
$endereco      = get_session( 'endereco_sort' );
$cidade_layout = get_session( 'cidade' );
$iduser        = get_session( 'user_id', 'int' );
$ip            = $_SERVER['REMOTE_ADDR'];
$maquina       = substr( $ip, strrpos( $ip, '.' ) + 1 );

$motivo_pag = 'IMPRESSÃO DE RESTITUIÇÃO DE MANDADO DE PRISÃO';

$imp_pront = get_session( 'imp_pront', 'int' );
if ( empty( $imp_pront ) or $imp_pront < 1 ) {

    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg         = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso à página SEM PERMISSÕES ( $motivo_pag ).";
    get_msg( $msg, 1 );

    exit;

}

$is_post = is_post();
if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página de $motivo_pag.";
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );
    exit;

}

extract( $_POST, EXTR_OVERWRITE );

$iddet = empty( $iddet ) ? '' : (int)$iddet;
if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. Operação cancelada ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 'f' );
    exit;

}
$tipo_rest = empty( $tipo_rest ) ? 1 : (int)$tipo_rest;
$sit_alv   = empty( $sit_alv ) ? 1 : (int)$sit_alv;



$referente = empty( $referente ) ? '' : tratastring( $referente, 'U', FALSE );
if ( empty( $referente ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Referente em branco. Operação cancelada ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 'f' );
    exit;

}

$dest = empty( $dest ) ? '' : tratastring( $dest, 'U', FALSE );
if ( empty( $dest ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Destino em branco. Operação cancelada ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 'f' );
    exit;

}

$cidade = empty( $cidade ) ? '' : tratastring( $cidade, 'U', FALSE );
if ( empty( $cidade ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Cidade em branco. Operação cancelada ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 'f' );
    exit;

}

$query_dg = "SELECT
               `diretor`,
               `titulo_diretor`
              FROM
                `diretores_n`
              WHERE
                `iddiretoresn` = $diretor_g
              LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_dg = $model->query( $query_dg );

// fechando a conexao
$model->closeConnection();

if( !$query_dg ) {

    echo msg_js( 'FALHA!!!', 'f' );
    exit;

}

$contdg = $query_dg->num_rows;

if( $contdg < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrencias (DIRETORES).\n\n Página $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 'f' );
    exit;
}

$d_dg = $query_dg->fetch_assoc();


// pegar os dados do detento
$detento = dados_det( $iddet );

$msg = array();
$msg['tipo'] = 'desc';
$msg['entre_ch'] = $motivo_pag;
$msg['text'] = "Impressão de restituição de Mandado de prisão \n\n $detento";

get_msg( $msg, 1 );

// comentário da função numera_of()
$coment = "[ OFÍCIOS PARA RESTITUIÇÃO DE MANDADO DE PRISÃO ]\n\n $detento";

$num_of = numera_of( $coment );


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="pt-br" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="<?php echo SICOP_SYS_IMG_PATH; ?>favicon.ico" type="image/x-icon" />
        <title><?php echo $titulo ?></title>
        <link href="<?php echo SICOP_ABS_PATH ?>css/reset.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_cabv.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_po.css" rel="stylesheet" type="text/css" />
    </head>

    <body onload="Javascript:window.print();self.window.close()">
    <!-- onload="Javascript:window.print();self.window.close()" -->

        <?php require 'cabecalho_v.php'; ?>

        <div class="corpo">

            <p class="par_corpo">&nbsp;</p>

            <p>Ofício nº <?php echo $num_of['num'];?> - <?php echo $sigla_setor; ?> - <?php echo $iniciais; ?></p>

            <p class="par_corpo">&nbsp;</p>

            <p>Referente a(o) <?php echo $referente; ?></p>

            <p class="par_corpo">&nbsp;</p>

            <p align="right"><?php echo $cidade_layout; ?>, <?php echo data_f()?> </p>

            <p class="par_corpo_menor">&nbsp;</p>
            <p class="par_corpo_menor">&nbsp;</p>

            <?php require 'quali/det_basic_print.php'; ?>

            <p class="par_corpo_menor">&nbsp;</p>
            <p class="par_corpo_menor">&nbsp;</p>

            <p class="par_corpo">Senhor Juíz(a),</p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>

            <p class="par_corpo">
            <?php if ( $tipo_rest == 1 ) { ?>

                Encaminho cópia em anexo, do Mandado de Prisão expedido por esse Douto Juízo,
                referente ao processo acima descrito, devidamente
                cumprido em desfavor d<?php echo SICOP_DET_ART_L; ?> referid<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?>.

            <?php } else { ?>

                <?php
                    $cump = 'SEM IMPEDIMENTO';
                    if ( $sit_alv == 2 ) {
                        $cump = 'COM IMPEDIMENTO';
                    }
                ?>

                Encaminho copia do Alvará de Soltura, expedido por esse
                Douto Juizo, referente ao processo acima descrito,
                em favor d<?php echo SICOP_DET_ART_L; ?> referid<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?>, o qual foi cumprido <?php echo $cump; ?>

            <?php } ?>
            </p>

            <p class="par_corpo">Aproveitando o ensejo, renovo protestos de elevada estima e distinta consideração.</p>
            <p class="par_corpo_medio">&nbsp;</p>
            <p class="par_corpo_medio">&nbsp;</p>
            <p class="par_corpo">Respeitosamente,</p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>
            <div class="ass">
                <p class="par_ass"><em><?php echo $d_dg['diretor'];?></em></p>
                <p class="par_ass"><?php echo $d_dg['titulo_diretor'];?></p>
            </div>

            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>

            <div class="desinf">


                <p class="par_desinf">A Sua Excelência o(a) Senhor(a) <br /> Juiz(a) de Direito da <?php echo $dest; ?> de <br /></p>

                <p class="par_desinf"><?php echo $cidade; ?></p>

            </div>

            <div class="comp">
                <p align="center" class="par_dh">Favor devolver uma das vias assinada.</p>
            </div>

            <p class="par_corpo">&nbsp;</p>

            <span class="_Footer">
                  <div class="rodape">
                      <p align="right" class="par_min">Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>
                      <hr align="center" width="645" size="0" noshade="noshade" color="#000000" />
                      <p align="center"><?php echo $endereco ?></p>
                  </div>
            </span>

        </div>
    </body>
</html>