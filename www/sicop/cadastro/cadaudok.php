<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_cad_n    = 3;

if ( $n_cadastro < $n_cad_n ) {

    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = 'CADASTRAMENTO OK DE AUDIÊNCIA';
    get_msg( $msg, 1 );

    exit;

}

$idaud = get_session( 'l_id_aud', 'int' );

if ( empty( $idaud ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página ( IDENTIFICADOR DA AUDIÊNCIA EM BRANCO - CADASTRAMENTO OK DE AUDIÊNCIA ).';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$query_aud = "SELECT
                `idaudiencia`,
                `cod_detento`,
                `data_aud`,
                DATE_FORMAT(`data_aud`, '%d/%m/%Y') AS `data_aud_f`,
                `hora_aud`,
                DATE_FORMAT(`hora_aud`, '%H:%i') AS `hora_aud_f`,
                `local_aud`,
                `cidade_aud`,
                `tipo_aud`,
                `num_processo`,
                `sit_aud`,
                `motivo_justi`,
                `user_add`,
                DATE_FORMAT(`data_add`, '%d/%m/%Y às %H:%i') AS data_add,
                `user_up`,
                DATE_FORMAT(`data_up`, '%d/%m/%Y às %H:%i') AS data_up
              FROM
                `audiencias`
              WHERE
                `idaudiencia` = $idaud
              LIMIT 1";


// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_aud = $model->query( $query_aud );

// fechando a conexao
$model->closeConnection();

if ( !$query_aud ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( CADASTRAMENTO OK DE AUDIÊNCIA ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$cont_aud = $query_aud->num_rows;

if( $cont_aud < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'A consulta retornou 0 ocorrências ( CADASTRAMENTO OK DE AUDIÊNCIA ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$d_aud = $query_aud->fetch_assoc();

$iddet = $d_aud['cod_detento'];

$aud = trata_sit_aud( $d_aud['sit_aud'] );

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

// adicionando o javascript
$cab_js   = 'ajax/jq_aud.js';
set_cab_js( $cab_js );

$desc_pag = 'Detalhes da audiência';


require 'cab.php';
?>

            <input type="hidden" id="idaud" value="<?php echo $d_aud['idaudiencia']; ?>" />

            <p class="descript_page">DETALHES DA AUDIÊNCIA</p>

            <?php include 'quali/det_basic.php'; ?>

            <p class="table_leg">Audiência</p>

            <table class="lista_busca">
                <tr class="even">
                    <td width="292" height="20" >Data/Hora: <?php echo $d_aud['data_aud_f'] ?> às <?php echo $d_aud['hora_aud_f'] ?></td>
                    <td width="293" >Tipo de apresentação: <?php echo trata_tipo_aud( $d_aud['tipo_aud'] ) ?></td>
                </tr>
                <?php
                $local = 'Local:';
                $process = 'Número do processo:';

                if ( $d_aud['tipo_aud'] == 4 || $d_aud['tipo_aud'] == 6 ) {
                    $local = 'Para realizar:';
                } else if ( $d_aud['tipo_aud'] == 5 ) {
                    $process = 'Número do inquérito:';
                } else if ( $d_aud['tipo_aud'] == 7 ) {
                    $local = 'A fim de ser:';
                } else if ( $d_aud['tipo_aud'] == 8 ) {
                    $process = 'Tipo de atendimento:';
                }
                ?>
                <tr class="even">
                    <td height="20" colspan="2" ><?php echo $local; ?> <?php echo $d_aud['local_aud'] ?></td>
                </tr>
                <tr class="even">
                    <td height="20" colspan="2" >Cidade: <?php echo $d_aud['cidade_aud'] ?></td>
                </tr>
                <tr class="even">
                    <td height="20" colspan="2" ><?php echo $process; ?> <?php echo $d_aud['num_processo'] ?></td>
                </tr>
                <tr class="even">
                    <td height="20" colspan="2" >Situação da audiência: <b><font color="<?php echo $aud['corfontaud']; ?>"><?php echo $aud['sitaud']; ?></font></b></td>
                </tr>
                <?php if ( $d_aud['sit_aud'] != 11 ) { ?>
                <tr class="even">
                    <td height="20" colspan="2" >Motivo: <?php echo $d_aud['motivo_justi'] ?></td>
                </tr>
                <?php } ?>
            </table>

            <p class="sub_title_page">O que você deseja fazer agora?</p>

            <ul id="menuok">

                <li><a href="<?php echo SICOP_ABS_PATH ?>buscadet.php?proced=cadaud" id="cadout" title="Cadastrar outra audiência para outr<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?>">Cadastrar <b>OUTRA</b> audiência para <b>OUTR<?php echo SICOP_DET_ART_U; ?></b> <?php echo SICOP_DET_DESC_L; ?></a></li>
                <li><a href="cadaud.php?iddet=<?php echo $iddet ?>" title="Cadastrar uma nova audiência para <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>">Cadastrar <b>OUTRA</b> audiência para <b><?php echo SICOP_DET_PRON_U; ?></b> <?php echo SICOP_DET_DESC_L; ?></a></li>
                <li><a href="<?php echo SICOP_ABS_PATH ?>buscadet.php?proced=cadaud&ant=1" title="Cadastrar esta audiência para outr<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?>">Cadastrar <b>ESTA</b> audiência para <b>OUTR<?php echo SICOP_DET_ART_U; ?></b> <?php echo SICOP_DET_DESC_L; ?></a></li>
                <li><a href="javascript:void(0)" id="print_aud" title="Imprimir ofício para esta audiência"><b>Imprimir</b> o ofício</a></li>

            </ul>

            <script type="text/javascript">id("cadout").focus();</script>

<?php include 'footer.php'; ?>
