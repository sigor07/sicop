<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';

$pag        = link_pag();
$tipo       = '';
$motivo_pag = 'IMPRESSÃO DE FORMULÁRIO DE ENTRADA DE VISITANTE';

$titulo     = $_SESSION['titulo'];

$num_seq = empty( $_GET['num_seq'] ) ? '' : (int)$_GET['num_seq'];

if ( empty( $num_seq ) ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página ( $motivo_pag ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 'f' );
    exit;
}

// pegar os ids dos visitantes que estão no numero de sequencia
$q_id_visit = "SELECT `cod_visita` FROM `visita_mov` WHERE `num_seq` = $num_seq AND DATE(`data_in`) = DATE(NOW())";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_id_visit = $model->query( $q_id_visit );

// fechando a conexao
$model->closeConnection();

if( !$q_id_visit ) {

    echo msg_js( 'FALHA!!!', 'f' );
    exit;

}

$cont = $q_id_visit->num_rows;

if ( $cont < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrencias ( $motivo_pag ).\n\n Página $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 'f' );
    exit;
}


$idvisit = '';
while( $idv = $q_id_visit->fetch_assoc() ) {
    $idvisit .= $idv['cod_visita'] . ',';
}

// verificar se algum dos visitantes entrou com jumbo
$jumbo = false;
$q_j_visit = "SELECT COUNT( `idmov_visit` ) FROM `visita_mov` WHERE `num_seq` = $num_seq AND DATE(`data_in`) = DATE(NOW()) AND `jumbo` = TRUE";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
(int)$j_visit = $model->fetchOne( $q_j_visit );

// fechando a conexao
$model->closeConnection();

if ( $j_visit >= 1 ) $jumbo = true;

$idvisit = substr($idvisit, 0, -1);

// pegar o visitante adulto
$q_visit_a = "SELECT
                `visitas`.`idvisita`,
                `visitas`.`nome_visit`,
                `visitas`.`rg_visit`,
                `visitas`.`sexo_visit`,
                `visitas`.`nasc_visit`,
                DATE_FORMAT(`visitas`.`nasc_visit`, '%d/%m/%Y') AS nasc_visit_f,
                FLOOR(DATEDIFF(CURDATE(), `visitas`.`nasc_visit`) / 365.25) AS idade_visit,
                `visitas`.`pai_visit`,
                `visitas`.`mae_visit`,
                `tipoparentesco`.parentesco,
                `visita_fotos`.`foto_visit_g`,
                `visita_fotos`.`foto_visit_p`
              FROM
                `visitas`
                INNER JOIN `tipoparentesco` ON `visitas`.`cod_parentesco` = `tipoparentesco`.`idparentesco`
                LEFT JOIN `visita_fotos` ON `visita_fotos`.`id_foto` = `visitas`.`cod_foto`
              WHERE
                `idvisita` IN($idvisit)
                AND
                FLOOR(DATEDIFF(CURDATE(), `visitas`.`nasc_visit`)/365.25) >= 18
              LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_visit_a = $model->query( $q_visit_a );

// fechando a conexao
$model->closeConnection();

if( !$q_visit_a ) {

    echo msg_js( 'FALHA!!!', 'f' );
    exit;

}

$cont = $q_visit_a->num_rows;

if ( $cont < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrencias ( $motivo_pag - VISITANTES ADULTOS ).\n\n Página $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 'f' );
    exit;
}

$d_visit_a = $q_visit_a->fetch_assoc();

$foto_g = $d_visit_a['foto_visit_g'];
$foto_p = $d_visit_a['foto_visit_p'];

$foto_visit = ck_pic( $foto_g, $foto_p, false, 2 );


// pegar os visitantes menores
$menores = true;
$q_visit_m = "SELECT
                `visitas`.`idvisita`,
                `visitas`.`nome_visit`,
                `visitas`.`sexo_visit`,
                `visitas`.`nasc_visit`,
                DATE_FORMAT(`visitas`.`nasc_visit`, '%d/%m/%Y') AS nasc_visit_f,
                FLOOR(DATEDIFF(CURDATE(), `visitas`.`nasc_visit`) / 365.25) AS idade_visit,
                `tipoparentesco`.parentesco
              FROM
                `visitas`
                INNER JOIN `tipoparentesco` ON `visitas`.`cod_parentesco` = `tipoparentesco`.`idparentesco`
              WHERE
                `idvisita` IN($idvisit)
                AND
                FLOOR(DATEDIFF(CURDATE(), `visitas`.`nasc_visit`)/365.25) < 18";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_visit_m = $model->query( $q_visit_m );

// fechando a conexao
$model->closeConnection();

if( !$q_visit_m ) {

    echo msg_js( 'FALHA!!!', 'f' );
    exit;

}

$quant_menores = $q_visit_m->num_rows;
if ( $quant_menores < 1 ) $menores = false;

// pegar os dados do detento
$q_det = "SELECT
            `detentos`.`iddetento`,
            `detentos`.`nome_det`,
            `detentos`.`matricula`,
            `cela`.`cela`,
            `raio`.`raio`
          FROM
            `detentos`
            LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
            LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
          WHERE
            `iddetento` = ( SELECT `cod_detento` FROM `visitas` WHERE `idvisita` IN($idvisit) LIMIT 1 )
          LIMIT 1";

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

$cont = $q_det->num_rows;

if ( $cont < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrencias ( $motivo_pag - DETENTO ).\n\n Página $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 'f' );
    exit;
}

$d_det = $q_det->fetch_assoc();


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="pt-br" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="<?php echo SICOP_SYS_IMG_PATH; ?>favicon.ico" type="image/x-icon" />
        <title><?php echo $titulo ?></title>
        <link href="<?php echo SICOP_ABS_PATH ?>css/reset.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_p_entrv.css" rel="stylesheet" type="text/css" media="all" />
        <link rel="stylesheet" type="text/css" href="<?php echo SICOP_ABS_PATH ?>css/VReport_print.css" media="print" />
        <link rel="stylesheet" type="text/css" href="<?php echo SICOP_ABS_PATH ?>css/VReport_screen.css" media="screen" />
    </head>

    <body onload="Javascript:window.print();self.window.close()">
        <!-- onload="Javascript:window.print();self.window.close()" -->

        <div class="corpo">
            <!--<span id="_VReportHeader"></span>-->

            <p align="center" class="par_forte_i">FORMULÁRIO DE REVISTA</p>
            <p align="center" class="par_forte_i">Número sequência <?php echo $num_seq ?> - Data: <?php echo date('d/m/Y')?></p>

            <!--<span id="_VReportContent"></span>-->
            <br />
            <p align="center" class="par_corpo"><b>DETENTO</b></p>

            <table width="650" class="bordasimples" border="1" align="center" cellpadding="1" cellspacing="0" >
                <tr >
                    <td width="320" height="15">Nome: <?php echo $d_det['nome_det'];?></td>
                    <td width="160">Matrícula: <?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] ) ?></td>
                    <td width="156" align="center"><b><?php echo SICOP_RAIO ?> <?php echo $d_det['raio'] ?> - <?php echo SICOP_CELA ?> <?php echo $d_det['cela'] ?></b></td>
                </tr>
            </table>
            <br />
            <p align="center" class="par_corpo"><b>VISITANTES</b></p>

            <table width="650" class="bordasimples" border="1" align="center" cellpadding="1" cellspacing="0" >
                <tr >
                    <td height="15" colspan="3" align="center"><b>ADULTO</b></td>
                </tr>
                <tr >
                    <td width="320" height="15">Nome: <?php echo $d_visit_a['nome_visit'];?></td>
                    <td width="188">R.G.: <?php echo $d_visit_a['rg_visit'] ?></td>
                    <td width="128" rowspan="5" align="center"><img src="<?php echo $foto_visit ?>" alt="" width="80" height="108" /></td>
                </tr>
                <tr >
                    <td height="15">Data de Nascimento: <?php echo empty($d_visit_a['nasc_visit_f']) ? '' : $d_visit_a['nasc_visit_f'].' - '.$d_visit_a['idade_visit'].' anos';// echo pegaIdade($d_visit['data_nasc'])  ?></td>
                    <td>Sexo: <?php echo $d_visit_a['sexo_visit'] ?></td>
                </tr>
                <tr >
                    <td height="15">Parentesco: <?php echo $d_visit_a['parentesco'] ?></td>
                    <td>ID: <?php echo $d_visit_a['idvisita'] ?></td>
                </tr>
                <tr >
                    <td height="15">Pai: <?php echo $d_visit_a['pai_visit'] ?></td>
                    <td>&nbsp;</td>
                </tr>
                <tr >
                    <td height="15">Mãe: <?php echo $d_visit_a['mae_visit'] ?></td>
                    <td>&nbsp;</td>
                </tr>
            </table>

            <?php if ( $menores ) { ?>
            <table width="650" class="bordasimples" border="1" align="center" cellpadding="1" cellspacing="0" >
                <tr >
                    <td height="15" colspan="4" align="center"><b>MENORES</b></td>
                </tr>
                <tr >
                    <td height="15" align="center">NOME</td>
                    <td align="center">NASCIMENTO</td>
                    <td align="center">PARENTESCO</td>
                    <td align="center">SEXO</td>
                </tr>
                    <?php     while( $d_visit_m = $q_visit_m->fetch_assoc() ) {?>
                <tr >
                    <td width="320" height="15"><?php echo $d_visit_m['nome_visit'];?></td>
                    <td width="155" align="center"><?php echo empty($d_visit_m['nasc_visit_f']) ? '' : $d_visit_m['nasc_visit_f'].' - '.$d_visit_m['idade_visit'].' anos';// echo pegaIdade($d_visit['data_nasc'])  ?></td>
                    <td width="112" align="center"><?php echo $d_visit_m['parentesco'] ?></td>
                    <td width="45" align="center"><?php echo $d_visit_m['sexo_visit'] ?></td>
                </tr>

                        <?php
                    }// fim do while
                }// fim do if que verifica menores
                ?>

            </table>

            <br />
            <table width="650" class="bordasimples" border="1" align="center" cellpadding="1" cellspacing="0" >
                <?php if ( $d_visit_a['sexo_visit'] == 'M' ) { ?>
                <tr >
                    <td width="640" height="30" class="jumbo">GUARDA VOLUMES: </td>
                </tr>
                    <?php } else { ?>
                <tr >
                    <td width="320" height="15" align="center">REVISTA</td>
                    <td width="320" align="center">GUARDA VOLUMES</td>
                </tr>
                <tr >
                    <td height="70">&nbsp;</td>
                    <td align="center">&nbsp;</td>
                </tr>
                    <?php } ?>
            </table>
            <br />
            <table width="650" class="bordasimples" border="1" align="center" cellpadding="1" cellspacing="0" >
                <tr >
                    <td height="15" colspan="4" align="center"><b>JUMBO</b></td>
                </tr>
                <?php if ( !$jumbo ) {?>
                <tr >
                    <td height="15" colspan="4" align="center"> <span class="par_forte_n">*** SEM JUMBO ***</span></td>
                </tr>
                    <?php } else {?>
                <tr >
                    <td width="160" height="60" class="jumbo"><span class="par_forte_n">VASILHAMES:</span></td>
                    <td width="162" class="jumbo"><span class="par_forte_n">REFRIGERANTES:</span></td>
                    <td width="155" class="jumbo"><span class="par_forte_n">SACOLA:</span></td>
                    <td width="155" class="jumbo"><span class="par_forte_n">Nº DA CAIXA:</span></td>
                </tr>
                    <?php } ?>
            </table>
            <?php if ( $d_visit_a['sexo_visit'] == 'M' ) { ?>
          <br />
            <div align="center" class="par_min">corte aqui</div>
            <div class="hRule"><hr /></div>
            <br />

            <table width="650" class="bordasimples" border="1" align="center" cellpadding="1" cellspacing="0" >
              <tr >
                    <td height="15" colspan="5" align="center"><b>REVISTA MASCULINA</b></td>
              </tr>
                <tr >
                    <td height="15" colspan="3" ><?php echo SICOP_DET_DESC_FU; ?>: <?php echo $d_det['nome_det'];?></td>
                    <td width="161" height="15" >Matrícula: <?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] ) ?></td>
                    <td width="156" align="center"><b><?php echo SICOP_RAIO ?> <?php echo $d_det['raio'] ?> - <?php echo SICOP_CELA ?> <?php echo $d_det['cela'] ?></b></td>
                </tr>
                <tr >
                    <td height="15" colspan="3" >Visitante: <?php echo $d_visit_a['nome_visit'];?></td>
                    <td height="15" >ID.: <?php echo $d_visit_a['idvisita'] ?></td>
                    <td align="center"><b>Núm. seq.: <?php echo $num_seq ?></b></td>
                </tr>
                <tr >
                    <td height="15" colspan="2" align="center">ENTRADA</td>
                    <td width="50" rowspan="12" class="jumbo">&nbsp;</td>
                    <td colspan="2" class="jumbo">R.G.: <?php echo $d_visit_a['rg_visit'] ?></td>
                </tr>
                <tr >
                    <td width="130" rowspan="5">&nbsp;</td>
                    <td width="131" rowspan="5">&nbsp;</td>
                    <td height="15" colspan="2" class="jumbo">OBSERVAÇÕES:</td>
                </tr>
                <tr >
                    <td height="15" colspan="2" class="jumbo"><p>Camisa:</p></td>
                </tr>
                <tr >
                    <td height="15" colspan="2" class="jumbo">Bulsa:</td>
                </tr>
                <tr >
                    <td height="15" colspan="2" class="jumbo">Calça:</td>
                </tr>
                <tr >
                    <td height="15" colspan="2" class="jumbo">Calçado:</td>
                </tr>
                <tr >
                    <td height="15" colspan="2" align="center">SAÍDA</td>
                    <td colspan="2" class="jumbo">Cinto:</td>
                </tr>
                <tr >
                    <td rowspan="5">&nbsp;</td>
                    <td rowspan="5">&nbsp;</td>
                    <td height="15" colspan="2" class="jumbo">Óculos:</td>
                </tr>
                <tr >
                    <td height="15" colspan="2" class="jumbo">Outros:</td>
                </tr>
                <tr >
                    <td height="15" colspan="2" class="jumbo">&nbsp;</td>
                </tr>
                <tr >
                    <td height="15" colspan="2" class="jumbo">&nbsp;</td>
                </tr>
                <tr >
                    <td height="15" colspan="2" class="jumbo">&nbsp;</td>
                </tr>
            </table>
                <?php } ?>
<br />
            <!--</span>-->
            <span class="_Footer">
                <table width="650" class="bordasimples_f" border="1" align="center" cellpadding="1" cellspacing="0" >
                    <tr >
                        <td width="320" height="15">Visitante: <?php echo $d_visit_a['nome_visit'];?></td>
                        <td width="160">R.G.: <?php echo $d_visit_a['rg_visit'] ?></td>
                        <td width="156" rowspan="2" class="jumbo">BOX Nº</td>
                    </tr>
                    <tr >
                        <td width="320" height="15"><?php echo SICOP_DET_DESC_FU; ?>: <?php echo $d_det['nome_det'];?></td>
                        <td width="160">Matrícula: <?php echo formata_num($d_det['matricula']) ?></td>
                    </tr>
                </table>
            </span>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
        </div>

    </body>
</html>