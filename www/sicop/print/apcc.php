<?php

/**
 * Impressão de atestado de permanencia carcerária em PDF
 *
 * @author Rafael
 * @since 20/04/2012
 *
 * ****************************************************************************
 *
 * SICOP - Sistema de Controle de Prisional
 *
 * Sistema para controle e gerenciamento de unidades prisionais
 *
 * @author  JOSÉ RAFAEL GONÇALVES - AGENTE DE SEGURANÇA III
 * @local   CENTRO DE DETENÇÃO PROVISÓRIA DE SÃO JOSÉ DO RIO PRETO - SP
 * @since   03/01/2011
 *
 * ****************************************************************************
 */

require '../init/config.php';

// instanciando a classe
$sys = new SicopController();

// checando se o sistema esta ativo
$sys->ckSys();


// instanciando a classe
$user = new userAutController();

// validando o usuário e o nível de acesso
$user->validateUser( 'imp_cadastro', 1, '', 7 );

// checando se o acesso foi via post
//$sys->ckPost( 3 );

$op = array(
    'method'         => 'get',        // metodo que a variável será recebida
    'name'           => 'idapcc',     // nome da variável
    'modo_validacao' => 'int',        // modo de validação
    'minLeng'        => 1,            // comprimento mínimo
    'required'       => true,         // se é requerida ou não
    'zero_ok'        => false,        // em caso de requerida, se pode ser o número 0 (zero)
    'return_type'    => 3             // tipo de retorno em caso de erro
);
$idapcc = $sys->validate( $op );

$q_apcc = "SELECT
              `apcc`.`idapcc`,
              `apcc`.`cod_detento`,
              `apcc`.`num_pda`,
              DATE(`apcc`.`data_add`) AS `data_apcc`,
              `numeroapcc`.`numero_apcc`,
              `numeroapcc`.`ano`,
              `tipoconduta`.`conduta`,
              `cidades`.`nome` AS `cidade`,
              `estados`.`sigla` AS `estado`,
              `cela`.`cela`,
              `raio`.`raio`
            FROM
              `apcc`
              INNER JOIN `numeroapcc` ON `apcc`.`cod_numapcc` = `numeroapcc`.`idnumapcc`
              INNER JOIN `detentos` ON `apcc`.`cod_detento` = `detentos`.`iddetento`
              LEFT JOIN `tipoconduta` ON `apcc`.`cod_conduta` = `tipoconduta`.`idconduta`
              LEFT JOIN `cidades` ON `detentos`.`cod_cidade` = `cidades`.`idcidade`
              LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado`
              LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
              LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
            WHERE
              `apcc`.`idapcc` = $idapcc
            LIMIT 1";

$q_mov_apcc = "SELECT
                 DATE_FORMAT( `movin`.`data_mov`, '%d/%m/%Y' ) AS data_in_f,
                 `procedencia`.`unidades` AS `procedencia`,
                 DATE_FORMAT( `movout`.`data_mov`, '%d/%m/%Y' ) AS data_out_f,
                 `destino`.`unidades` AS `destino`
               FROM
                 `apcc_mov`
                 INNER JOIN `mov_det` `movin` ON `apcc_mov`.`cod_movin` = `movin`.`id_mov`
                 INNER JOIN `unidades` `procedencia` ON `movin`.`cod_local_mov` = `procedencia`.`idunidades`
                 LEFT JOIN `mov_det` `movout` ON `apcc_mov`.`cod_movout` = `movout`.`id_mov`
                 LEFT JOIN `unidades` `destino` ON `movout`.`cod_local_mov` = `destino`.`idunidades`
               WHERE
                 `apcc_mov`.`cod_apcc` = $idapcc
               ORDER BY
                 `id_apcc_mov`";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_apcc = $model->query( $q_apcc );

// fechando a conexao
$model->closeConnection();

if ( !$q_apcc ) {

    echo msg_js( 'Falha!!!', 'f' );
    exit;

}

$cont_apcc = $q_apcc->num_rows;

if ( $cont_apcc < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "A consulta retornou 0 ocorrências.";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'Falha!!!', 'f' );
    exit;

}

$d_apcc = $q_apcc->fetch_assoc();

$iddet   = $d_apcc['cod_detento'];
$conduta = $d_apcc['conduta'];
$num_pda = $d_apcc['num_pda'];

$desc_apcc = 'ATESTADO DE PERMANÊNCIA E CONDUTA CARCERÁRIA ';

if ( empty( $conduta ) ) $desc_apcc = 'CERTIDÃO DE RECOLHIMENTO PRISIONAL';

// pega a data em que o atestado foi criado, e salva em uma variavel para ser utilizada pela função data_f()
$data_apcc = $d_apcc['data_apcc'];
$timestamp = strtotime( $data_apcc );

$id_diretor_g = $sys->getSession( 'diretor_geral' );
$diretor_g = new Diretor( $id_diretor_g );
$diretor_g->findDiretor();


// se não tiver conduta, pega o nome do diretor de prontuário
$id_sub_diretor = $sys->getSession( 'diretor_pront' );
if ( !empty( $conduta ) ) {

    // se a conduta não for null, entao é atestado de conduta, e precisa da assinatura do diretor de segurança
    $id_sub_diretor = $sys->getSession( 'diretor_seg' );

}

$sub_diretor = new Diretor( $id_sub_diretor );
$sub_diretor->findDiretor();

$d_dtp = '';
$local_prisao = '';
if ( empty( $conduta ) ) {

    $q_dt_prisao = "SELECT
                      DATE_FORMAT( `detentos`.`data_prisao`, '%d/%m/%Y' ) AS data_prisao,
                      `unidades`.`unidades` AS local_prisao
                    FROM
                      `detentos`
                      LEFT JOIN `unidades` ON `detentos`.`cod_local_prisao` = `unidades`.`idunidades`
                    WHERE
                      `detentos`.`iddetento` = $iddet
                    LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_dt_prisao = $model->query( $q_dt_prisao );

    // fechando a conexao
    $model->closeConnection();

    if ( !$q_dt_prisao ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Falha na consulta ( DATA E LOCAL DA PRISÃO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( '', 1 );
        exit;

    }

    $cont_dtp = $q_dt_prisao->num_rows;

    if ( $cont_dtp < 1 ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'A consulta retornou 0 ocorrências ( DATA E LOCAL DA PRISÃO ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( '', 1 );
        exit;

    }

    $d_dtp = $q_dt_prisao->fetch_assoc();
    $local_prisao = $d_dtp['local_prisao'];

    if ( empty( $local_prisao ) ) {

        $q_prisao = "SELECT
                             `unidades_in`.`unidades` AS procedencia
                           FROM
                             `detentos`
                             LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                             LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
                           WHERE
                             `detentos`.`iddetento` = $iddet
                           LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_prisao = $model->query( $q_prisao );

        // fechando a conexao
        $model->closeConnection();

        if ( !$q_prisao ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']  = 'err';
            $msg['text']  = "Falha na consulta ( LOCAL DA PRISÃO ).";
            $msg['linha'] = __LINE__;
            get_msg( $msg, 1 );

            echo msg_js( '', 1 );
            exit;

        }

        $cont_prisao = $q_prisao->num_rows;

        if ( $cont_prisao < 1 ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']  = 'err';
            $msg['text']  = 'A consulta retornou 0 ocorrências ( LOCAL DA PRISÃO ).';
            $msg['linha'] = __LINE__;
            get_msg( $msg, 1 );

            echo msg_js( '', 1 );
            exit;

        }

        $d_prisao = $q_prisao->fetch_assoc();
        $local_prisao = $d_prisao['procedencia'];

    }


}

$det = new Detento();

// comentário do número do ofício
$detento = $det->dadosDetF( $iddet );

$num_apcc = $d_apcc['numero_apcc'] . '/' . $d_apcc['ano'];

// montar a mensagem q será salva no log
$msg = sysmsg::create_msg();
$msg->add_chaves( 'IMPRESSÃO DE APCC - PDF', 0, 2 );
$msg->set_msg( "Impressão de atestado de permanência.\n\n Número: $num_apcc \n\n $detento" );
$msg->get_msg();


$iduser = $sys->getSession( 'user_id', 'int' );
$cidade = $sys->getSession( 'cidade' );

require_once('classes/tcpdf/pdf.php');

// definir a fonte
$font = 'helvetica';

// altura padrão das células
$cell_h = 6;

// create new PDF document
$pdf = new PDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );

$pdf->setFontSubsetting(false);

$desc_doc = 'APCC';

// set document information PDF_CREATOR
$pdf->SetCreator( SICOP_SYS_NAME );
$pdf->SetAuthor( SICOP_SYS_NAME );
$pdf->SetTitle( $desc_doc );
$pdf->SetSubject( $desc_doc );
$pdf->SetKeywords( SICOP_SYS_KW );

// set default monospaced font
$pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );

// margens do cabeçalho e rodapé
$pdf->SetHeaderMargin( PDF_MARGIN_HEADER );
$pdf->SetFooterMargin( PDF_MARGIN_FOOTER );

//set auto page breaks
$pdf->SetAutoPageBreak( TRUE, 15 );

//set image scale factor
$pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );

// ---------------------------------------------------------

$pdf->SetDisplayMode( 'fullwidth', 'continuous', 'UseNone' );

//set margins - left, top and right
$pdf->SetMargins( 20, PDF_MARGIN_TOP, 20 );

// adicionar uma página
$pdf->AddPage();

// configurar a fonte
$pdf->SetFont( $font, 'N', 6 );

// escrevendo o raio/cela
$txt = $d_apcc['raio'] . ' ' . $d_apcc['cela'];
$pdf->Cell( 0, '', $txt, 0, 1, 'R', 0, '', 0, false, 'T', 'M' );


// configurar a fonte
$pdf->SetFont( $font, 'B', 12 );

// escrevendo a descrição
$txt = $desc_apcc;
$pdf->Cell( 0, '', $txt, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

// escrevendo o número/ano
$txt = $d_apcc['numero_apcc'] . '/' . $d_apcc['ano'];
$pdf->Cell( 0, '', $txt, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );


// configurar a fonte
$pdf->SetFont( $font, 'N', 9.5 );

// adicionando uma quebra de linha
$pdf->Ln();
$pdf->Ln();

$pdf->getQualiDetBasic( $iddet );

// adicionando uma quebra de linha
$pdf->Ln();

// corpo
$txt  = PAR_INDET_INI;
$txt .= 'ATESTO, para os DEVIDOS FINS, que ' . SICOP_DET_ART_L . ' referid' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L;
$txt .=  ' permaneceu nesta unidade, em regime fechado, à disposição da Justiça, no(s) seguinte(s) período(s):';
$txt .= PAR_INDET_FIM;
$pdf->MultiCell( 170, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

if ( empty( $conduta ) ) {

    // adicionando uma quebra de linha
    $pdf->Ln();

    // prisão inicial
    $txt = '<b>Prisão inicial</b>: <b>Data</b>: ' . $d_dtp['data_prisao'] . ' - <b>Local</b>: ' . $local_prisao;
    $pdf->MultiCell( 170, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

}

// adicionando uma quebra de linha
$pdf->Ln();
$pdf->Ln( $cell_h );

// movimentações
$txt = 'MOVIMENTAÇÕES';
$pdf->Cell( 0, '', $txt, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );


// configurar a fonte
$pdf->SetFont( $font, 'N', 8 );


// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_mov_apcc = $model->query( $q_mov_apcc );

// fechando a conexao
$model->closeConnection();

$altura = '';

// DESENHAR OS QUADRADOS PRINCIPAIS
$border = array( 'TBRL' => array( 'width' => 0.3, 'dash' => 0 ) );
$pdf->Cell( 10, $altura, 'N', $border, 0, 'C', 0, '', 0, false, 'T', 'M' );
$pdf->Cell( 30, $altura, 'DATA DA INCLUSÃO', $border, 0, 'C', 0, '', 0, false, 'T', 'M' );
$pdf->Cell( 50, $altura, 'PROCEDÊNCIA', $border, 0, 'C', 0, '', 0, false, 'T', 'M' );
$pdf->Cell( 30, $altura, 'DATA DA EXCLUSÃO', $border, 0, 'C', 0, '', 0, false, 'T', 'M' );
$pdf->Cell( 50, $altura, 'DESTINO', $border, 0, 'C', 0, '', 0, false, 'T', 'M' );

// adicionando uma quebra de linha
$pdf->Ln();

$i = 0;
while( $d_mov_apcc = $q_mov_apcc->fetch_assoc() ) {

    $pdf->Cell( 10, $altura, ++$i, $border, 0, 'C', 0, '', 0, false, 'T', 'M' );
    $pdf->Cell( 30, $altura, $d_mov_apcc['data_in_f'], $border, 0, 'C', 0, '', 0, false, 'T', 'M' );
    $pdf->Cell( 50, $altura, $d_mov_apcc['procedencia'], $border, 0, 'C', 0, '', 0, false, 'T', 'M' );

    if ( empty( $d_mov_apcc['data_out_f'] ) ) {

        // configurar a fonte
        $pdf->SetFont( $font, 'B', 8 );

        $txt = 'PRESO ATÉ À PRESENTE DATA';
        $pdf->Cell( 80, $altura, $txt, $border, 0, 'C', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $pdf->SetFont( $font, 'N', 8 );

    } else {

        $pdf->Cell( 30, $altura, $d_mov_apcc['data_out_f'], $border, 0, 'C', 0, '', 0, false, 'T', 'M' );
        $pdf->Cell( 50, $altura, $d_mov_apcc['destino'], $border, 0, 'C', 0, '', 0, false, 'T', 'M' );

    }

    // adicionando uma quebra de linha
    $pdf->Ln();

}


// configurar a fonte
$pdf->SetFont( $font, 'N', 9.5 );

if ( !empty( $conduta ) ) {

    // adicionando uma quebra de linha
    $pdf->Ln( $cell_h );

    // conduta
    $txt = "Conduta carcerária: <b>$conduta</b>";
    $pdf->MultiCell( 170, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

    if ( !empty( $num_pda ) ) {

        // adicionando uma quebra de linha
        $pdf->Ln();

        // pda
        $txt = "Obs: Foi indiciado em PDA nº <b>$num_pda</b>, aguardando conclusão para avaliação.";
        $pdf->MultiCell( 170, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

    }


}

// adicionando uma quebra de linha
$pdf->Ln();
$pdf->Ln( $cell_h );

// configurar a fonte
$pdf->SetFont( $font, 'N', 9.5 );

// data
$data = $cidade . ', ' . $sys->dataF();
$pdf->Cell( 0, '', $data, 0, 1, 'R', 0, '', 0, false, 'T', 'M' );

// adicionando uma quebra de linha
$pdf->Ln();
$pdf->Ln( $cell_h );

// configurar a fonte
$pdf->SetFont( $font, 'I', 9.5 );

// nome do diretor
$txt = $sub_diretor->_nome;
$pdf->Cell( 0, '', $txt, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

// configurar a fonte
$pdf->SetFont( $font, 'N', 9.5 );

// titulo do diretor
$txt = $sub_diretor->_titulo;
$pdf->Cell( 0, '', $txt, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

// adicionando uma quebra de linha
$pdf->Ln();
$pdf->Ln( $cell_h );

// visto
$txt = 'Visto:';
$pdf->Cell( 0, '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

// adicionando uma quebra de linha
$pdf->Ln();

// configurar a fonte
$pdf->SetFont( $font, 'I', 9.5 );

// nome do diretor
$txt = $diretor_g->_nome;
$pdf->Cell( 0, '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );

// configurar a fonte
$pdf->SetFont( $font, 'N', 9.5 );

// titulo do diretor
$txt = $diretor_g->_titulo;
$pdf->Cell( 0, '', $txt, 0, 1, 'L', 0, '', 0, false, 'T', 'M' );









// reset pointer to the last page
$pdf->lastPage();
// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output( 'rest_mp.pdf', 'I' );

//============================================================+
// END OF FILE
//============================================================+

?>