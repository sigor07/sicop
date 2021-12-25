<?php
if ( !isset( $_SESSION ) ) session_start();

    require '../init/config.php';
require 'incl_print.php';

$pag  = link_pag();
$tipo = '';

$imp_pront     = get_session( 'imp_pront', 'int' );
$diretor_g     = get_session( 'diretor_geral' );
$diretor_p     = get_session( 'diretor_pront' );
$titulo        = get_session( 'titulo' );
$secretaria    = get_session( 'secretaria' );
$coordenadoria = get_session( 'coordenadoria' );
$unidadecurto  = get_session( 'unidadecurto' );
$endereco      = get_session( 'endereco' );
$cidade        = get_session( 'cidade' );
$ip            = $_SERVER['REMOTE_ADDR'];
$maquina       = substr( $ip, strrpos( $ip, '.' ) + 1 );
$iduser        = get_session( 'user_id', 'int' );


$motivo_pag = 'IMPRESSÃO DE TERMOS DE ENCERRAMENTO DO PROTUÁRIO';

if ($imp_pront < 1) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 'f' );

    exit;

}

$iddet      = get_get ( 'iddet', 'int' );
$mot_termo  = get_get ( 'mot_termo', 'int' );
$num_folhas = get_get ( 'num_folhas', 'int' );
$num_folhas = empty( $num_folhas ) ? '_____' : (int)$num_folhas;
$destino    = get_get ( 'destino', 'string' );
$data_termo = get_get ( 'data_termo', 'busca' );

if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );

    exit;

}

$q_det = "SELECT
              `detentos`.`iddetento`,
              `detentos`.`nome_det`,
              `detentos`.`matricula`,
              `detentos`.`rg_civil`,
              DATE_FORMAT(`detentos`.`nasc_det`, '%d/%m/%Y') AS `nasc_det_f`,
              `detentos`.`pai_det`,
              `detentos`.`mae_det`,
              `cidades`.`nome` AS `cidade`,
              `estados`.`sigla` AS `estado`
            FROM
              `detentos`
              LEFT JOIN `cidades` ON `detentos`.`cod_cidade` = `cidades`.`idcidade`
              LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado`
            WHERE
              `detentos`.`iddetento` IN ( $iddet )";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_det = $model->query( $q_det );

    // fechando a conexao
    $model->closeConnection();

    if( !$q_det ) {

        echo msg_js( 'FALHA!!!', 'f' );
        exit;

    }

    $cont_prot = $q_det->num_rows;

    if($cont_prot < 1) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( TERMO DE ABERTURA DO PROTUÁRIO ).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!!!', 'f' );
        exit;
    }

    $d_det = $q_det->fetch_assoc();

    // pega a data da inclusão do detento, e salva em uma variavel para ser utilizada pela função data_f()
    $timestamp = gera_timestamp($data_termo);


    $virtude = '';
    if ( $mot_termo == 1 ) {

        $virtude = 'ter sido post' . SICOP_DET_ART_L . ' em liberdade';

    } else if ( $mot_termo == 2 ) {

        $virtude = 'seu falecimento';

    } else if ( $mot_termo == 3 ) {

        $virtude = 'sua transferência para o destino especificado';

    } else if ( $mot_termo == 4 ) {

        $virtude = 'sua transferência';

    }

    $query_dp = "SELECT
                     `diretor`,
                     `titulo_diretor`
                    FROM
                      diretores_n
                    WHERE iddiretoresn = $diretor_p
                    LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_dp = $model->query( $query_dp );

    // fechando a conexao
    $model->closeConnection();

    if( !$query_dp ) {

        echo msg_js( 'FALHA!!!', 'f' );
        exit;

    }

    $contdp = $query_dp->num_rows;

    if( $contdp < 1 ) {
        $mensagem = "A consulta retornou 0 ocorrencias (DIRETOR DE PROTUÁRIO).\n\n Página $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!!!', 'f' );
        exit;
    }

    $d_dp = $query_dp->fetch_assoc();

    $d_dg = '';
    if ( $mot_termo == 4 ) {

        $query_dg = "SELECT
                         `diretor`,
                         `titulo_diretor`
                        FROM
                          diretores_n
                        WHERE iddiretoresn = $diretor_g
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
            $mensagem = "A consulta retornou 0 ocorrencias (DIRETOR GERAL).\n\n Página $pag";
            salvaLog( $mensagem );
            echo msg_js( 'FALHA!!!', 'f' );
            exit;
        }

        $d_dg = $query_dg->fetch_assoc();

    }

    // pegar os dados do preso
    $detento = dados_det( $iddet );

    $mensagem = "[ IMPRESSÃO DE TERMO DE ENCERRAMENTO ]\n Impressão de termo de encerramento do prontuário. \n\n $detento \n";
    salvaLog($mensagem);

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
        <link rel="stylesheet" type="text/css" href="<?php echo SICOP_ABS_PATH ?>css/VReport_print.css" media="print" />
        <link rel="stylesheet" type="text/css" href="<?php echo SICOP_ABS_PATH ?>css/VReport_screen.css" media="screen" />
    </head>

    <body onload="Javascript:window.print();self.window.close()">
    <!-- onload="Javascript:window.print();self.window.close()" -->

    <?php require 'cabecalho_v.php';?>

    <div class="corpo_termo">
        <p class="par_corpo">&nbsp;</p>
        <p class="par_forte_n" align="center">TERMO DE ENCERRAMENTO<?php if( $mot_termo == 4 ) {?> E REMESSA <?php } ?></p>
        <p class="par_corpo">&nbsp;</p>
        <p class="par_corpo">&nbsp;</p>
            <table width="620" align="center" cellpadding="1" cellspacing="0" class="detento">
                <tr >
                    <td colspan="3"><strong><?php echo SICOP_DET_DESC_FU; ?>:</strong> <?php echo $d_det['nome_det'];?></td>
                </tr>
                <tr>
                    <td width="180"><strong>RG:</strong> <?php echo formata_num($d_det['rg_civil']) ?></td>
                    <td width="180"><strong>Matrícula:</strong> <?php echo formata_num($d_det['matricula']) ?></td>
                    <td width="252"><strong>Nascimento:</strong> <?php echo empty($d_det['nasc_det_f']) ? "" : $d_det['nasc_det_f']; ?></td>
                </tr>
                <tr >
                    <td colspan="3"><strong>Pai:</strong> <?php echo $d_det['pai_det'] ?></td>
                </tr>
                <tr >
                    <td colspan="3"><strong>Mãe:</strong> <?php echo $d_det['mae_det'] ?></td>
                </tr>
                <tr >
                    <td colspan="3"><strong>Cidade:</strong> <?php echo $d_det['cidade'].' - '.$d_det['estado'] ?></td>
                </tr>
                <?php if( $mot_termo == 3 ) {?>
                <tr >
                    <td colspan="3"><strong>Destino:</strong> <?php echo $destino ?></td>
                </tr>
                <?php } ?>
            </table>

            <p class="par_corpo_menor">&nbsp;</p>
            <p class="par_corpo_menor">&nbsp;</p>
            <p class="par_corpo">Na presente data, nesta unidade prisional, declaro ENCERRADO o Prontuário Penitenciário d<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?> acima qualificad<?php echo SICOP_DET_ART_L; ?>, constando <?php echo $num_folhas; ?> folhas numeradas e rubricadas, em virtude de <?php echo $virtude; ?>.</p>


            <?php if( $mot_termo == 4 ) {?>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">Em seguida, faço-o concluso ao Sr. Diretor desta unidade, para ser remetido para o(a) <?php echo $destino; ?>.</p>
            <?php } ?>
            <?php if( $mot_termo != 4 ) {?>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>
            <?php } ?>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>
            <p align="right"><?php echo $cidade;?>, <?php echo data_f($timestamp) ?> </p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>
             <?php if( $mot_termo != 4 ) {?>
            <p class="par_corpo">&nbsp;</p>
            <?php } ?>
            <div class="ass_apcc">
                <p class="par_ass"><em><?php echo $d_dp['diretor'];?></em></p>
                <p class="par_ass"><?php echo $d_dp['titulo_diretor'];?></p>
            </div>
            <?php if( $mot_termo == 4 ) {?>

            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">Remeta-se este Prontuário ao Sr. Diretor da unidade acima descrita, de acordo com o que determina a portaria nº 190/61.</p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>

             <div class="ass_apcc">
                <p class="par_ass"><em><?php echo $d_dg['diretor'];?></em></p>
                <p class="par_ass"><?php echo $d_dg['titulo_diretor'];?></p>
            </div>

            <?php } ?>

            <span class="_Footer">
                  <div class="rodape_termo">
                      <p align="right" class="par_min">Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>
                      <hr align="center" width="615" size="0" noshade="noshade" color="#000000" />
                      <p align="center"><?php echo $endereco ?></p>
                  </div>
            </span>

        </div>
    </body>
</html>