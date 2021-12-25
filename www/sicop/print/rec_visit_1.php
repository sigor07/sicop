<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';

$pag  = link_pag();
$tipo = '';

$imp_rol = get_session( 'imp_rol', 'int' );
$n_rol_n = 1;

$ip            = $_SERVER['REMOTE_ADDR'];
$maquina       = substr( $ip, strrpos( $ip, '.' ) + 1 );
$iduser        = get_session( 'user_id', 'int' );

$titulo        = get_session( 'titulo' );
$secretaria    = get_session( 'secretaria' );
$coordenadoria = get_session( 'coordenadoria' );
$unidadelongo  = get_session( 'unidadelongo' );
$unidadecurto  = get_session( 'unidadecurto' );
$endereco_sort = get_session( 'endereco_sort' );
$cidade        = get_session( 'cidade' );

$motivo_pag = 'RELAÇÃO DE DOCUMENTOS PARA VISITA';

if ($imp_rol < $n_rol_n) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idvisit = get_get( 'idvisit', 'int' );

if ( empty( $idvisit ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( IDENTIFICADOR EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$queryvis = "SELECT
               `visitas`.`idvisita`,
               `visitas`.`nome_visit`,
               `visitas`.`rg_visit`,
               `tipoparentesco`.`parentesco`,
               `detentos`.`nome_det`,
               `detentos`.`matricula`
             FROM
               `visitas`
               INNER JOIN `detentos` ON `visitas`.`cod_detento` = `detentos`.`iddetento`
               LEFT JOIN `tipoparentesco` ON `visitas`.`cod_parentesco` = `tipoparentesco`.`idparentesco`
             WHERE
               `visitas`.`idvisita` = $idvisit
             LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$queryvis = $model->query( $queryvis );

// fechando a conexao
$model->closeConnection();

if( !$queryvis ) {

    echo msg_js( 'FALHA!!!', 'f' );
    exit;

}

$cont = $queryvis->num_rows;

if( $cont < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrencias (VISITAS).\n\n Página $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 'f' );
    exit;
}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$d_visit = $queryvis->fetch_assoc();




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="<?php echo SICOP_SYS_IMG_PATH; ?>favicon.ico" type="image/x-icon" />
        <title><?php echo $titulo ?></title>
        <link href="<?php echo SICOP_ABS_PATH ?>css/reset.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_p.css" rel="stylesheet" type="text/css" />
    </head>

    <body onload="Javascript:window.print();self.window.close()">
    <!--onload="Javascript:window.print();self.window.close()"-->
        <div class="corpo">

            <p class="par_forte_n" align="center"><?php echo $unidadelongo ?></p>
            <p align="center"><?php echo $endereco_sort ?></p>
            <p align="par_quebra_linha">&nbsp;</p>
            <p class="par_medio_n" align="center">DOCUMENTOS NECESSÁRIOS PARA REGULARIZAÇÃO DA VISITA</p>
            <p align="par_quebra_linha">&nbsp;</p>
            <p class="par_corpo"><b>Xerox do R.G. autenticado.</b></p>
            <p class="par_quebra_linha">&nbsp;</p>
            <p class="par_corpo"><b>Xerox do C.P.F. autenticado.</b></p>
            <p class="par_quebra_linha">&nbsp;</p>
            <p class="par_corpo"><b>Uma foto 3x4.</b></p>
            <p class="par_quebra_linha">&nbsp;</p>
            <p class="par_corpo">
                <b>Xerox do comprovante de residência recente autenticado (agua, energia ou telefone) dos últimos 6(seis) meses.</b> Se a conta estiver em nome de terceiros,
                trazer também uma declaração de residente no imóvel autenticada pelo titular da conta.
            </p>
            <p class="par_quebra_linha">&nbsp;</p>
            <p class="par_corpo"><b>Certidão de Antecedentes Criminais.</b></p>
            <p class="par_quebra_linha">&nbsp;</p>
            <p class="par_corpo"><b>*** ESPOSA E AMÁSIA *** Xerox da Certidão de Casamento</b> ou, no caso de União Estável, Declaração de União Estável com <?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L  ?>, assinada e autenticada por 2 (duas)
            testemunhas e a declarante.</p>
            <p class="par_quebra_linha">&nbsp;</p>
            <p class="par_corpo"><b>Menores (SOMENTE FILHOS DO DETENTO):</b> Xerox simples do R.G. ou Certidão de Nascimento e uma foto 3x4.</p>
            <p class="par_quebra_linha">&nbsp;</p>
            <p class="par_medio_n" align="center">OBSERVAÇÕES</p>
            <p class="par_quebra_linha">&nbsp;</p>
            <p class="par_corpo">* A não apresentação dos documentos acima no ato da primeira visita, acarretará na SUSPENSÃO do visitante, até que a situação esteja regularizada.</p>
            <p class="par_quebra_linha">&nbsp;</p>
            <p class="par_corpo">* Para o envio de correspondência, colocar o NOME COMPLETO d<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L  ?>, matrícula, raio e cela.</p>
            <p class="par_quebra_linha">&nbsp;</p>
            <p class="par_corpo">* Somente serão aceitos SEDEX cujo o nome do remetente conste no ROL DE VISITAS d<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L  ?> e que esteja com a situação regularizada, sendo limitado a <b>1 (UM) SEDEX POR SEMANA</b>.</p>
            <p class="par_quebra_linha">&nbsp;</p>
            <p class="par_corpo">* Para solicitação de ATESTADOS DE PERMANÊNCIA CARCERÁRIA, <b>PRIMEIRAMENTE</b> entre em contato com o setor responsável da unidade.</p>
            <p class="par_quebra_linha">&nbsp;</p>
            <br />
            <div align="center" class="par_min">corte aqui</div>
            <div class="hRule"><hr /></div>
            <br />
            <p class="par_quebra_linha">&nbsp;</p>
            <p class="par_forte_n" align="center"><?php echo $unidadelongo ?></p>
            <p align="center"><?php echo $endereco_sort ?></p>
            <p class="par_quebra_linha">&nbsp;</p>
            <p class="par_medio_n" align="center">DECLARAÇÃO DE CIENTE DOS DOCUMENTOS NECESSÁRIOS PARA REGULARIZAÇÃO DA VISITA</p>
            <p class="par_quebra_linha">&nbsp;</p>
            <p class="par_corpo" style="text-indent:50px;">Declaro estar ciente de que tenho que providenciar toda a documentação solicitada pelo setor de
            ROL DE VISITAS do <?php echo $unidadelongo ?>, e que o não cumprimento das exigências acarretará na SUSPENSÃO das visitas, até que a situação esteja regularizada.</p>
            <p class="par_data"><?php echo $cidade;?>, <?php echo data_f();?></p>
            <p class="par_quebra_linha">&nbsp;</p>
            <p class="par_quebra_linha">&nbsp;</p>
            <p align="center" class="par_ass">____________________________________________________________________________</p>
            <p align="center" class="par_ass"><?php echo $d_visit['idvisita'] ?> - <?php echo $d_visit['nome_visit'] ?> <?php if ( !empty( $d_visit['rg_visit'] ) ) echo ' - R.G.: ' . $d_visit['rg_visit'] ?> - <?php echo $d_visit['parentesco'] ?></p>
            <p align="center" class="par_ass">Visitante d<?php echo SICOP_DET_ART_L; ?> <?php echo SICOP_DET_DESC_FU; ?></p>
            <p align="center" class="par_ass"><?php echo $d_visit['nome_det'] ?> - <?php if ( !empty( $d_visit['matricula'] ) ) echo formata_num( $d_visit['matricula'] ) ?></p>
            <p class="par_quebra_linha">&nbsp;</p>
            <p align="right" class="par_min">Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>
        </div>

    </body>

</html>