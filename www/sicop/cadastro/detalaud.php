<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$imp_cadastro = get_session( 'imp_cadastro', 'int');
$n_cadastro   = get_session( 'n_cadastro', 'int');
$n_cad_n      = 2;

if ( $n_cadastro < $n_cad_n ) {
    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
    salvaLog( $mensagem );
    exit;
}

$idaud = get_get( 'idaud', 'int' );

if ( empty( $idaud ) ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página de detalhes da audiência.\n\n Página: $pag";
    salvaLog( $mensagem );
    echo '<script type="text/javascript">history.go(-1);</script>';
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

$query_obs = "SELECT
                `id_obs_aud`,
                `cod_audiencia`,
                `obs_aud`,
                `user_add`,
                DATE_FORMAT(`data_add`, '%d/%m/%Y') AS data_add_f,
                DATE_FORMAT(`data_add`, '%d/%m/%Y às %H:%i') AS data_add_fc,
                `data_add`,
                `user_up`,
                DATE_FORMAT(`data_up`, '%d/%m/%Y às %H:%i') AS data_up_f,
                `data_up`
              FROM
                `obs_aud`
              WHERE
                `cod_audiencia` = $idaud
              ORDER BY
                `data_add` DESC
              LIMIT 10";

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
    $msg['text']  = 'Falha na consulta ( DETALHES DA AUDIÊNCIA ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$cont_aud = $query_aud->num_rows;

if ( $cont_aud < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'A consulta retornou 0 ocorrências ( DETALHES DA AUDIÊNCIA ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$d_aud = $query_aud->fetch_assoc();

$iddet = $d_aud['cod_detento'];

$user_add = '';
$user_up = '';

if ( !empty( $d_aud['user_add'] ) ) {
    $user_add = 'ID usuário: ' . $d_aud['user_add'] . ', em ' . $d_aud['data_add'];
};

if ( !empty( $d_aud['user_up'] ) ) {
    $user_up = 'ID usuário: ' . $d_aud['user_up'] . ', em ' . $d_aud['data_up'];
};

$aud = trata_sit_aud( $d_aud['sit_aud'] );

// adicionando o javascript
$cab_js   = 'ajax/jq_aud.js';
set_cab_js( $cab_js );

$desc_pag = 'Detalhes da audiência';

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual,4 );
$trail->output();
?>

            <input type="hidden" id="idaud" value="<?php echo $d_aud['idaudiencia']; ?>" />

            <p class="descript_page">DETALHES DA AUDIÊNCIA</p>

            <?php include 'quali/det_cad.php'; ?>

            <p class="table_leg">Audiência</p>

            <?php if ( $n_cadastro >= 3 ) { ?>
            <p class="link_common">
                    <a href="cadaud.php?iddet=<?php echo $iddet; ?>" title="Cadastrar outra audiência para <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>">Cadastrar</a> |
                    <a href="editaud.php?idaud=<?php echo $d_aud['idaudiencia'] ?>" title="Alterar os dados desta audiência">Alterar</a>
                    <?php if ( $imp_cadastro >= 1 ) { ?>
                    | <a href="javascript:void(0)" id="print_aud" title="Imprimir ofício para esta audiência">Imprimir ofício</a>
                    <?php } ?>
                    <?php if ( $n_cadastro >= 4 ) {  ?>
                    | <a href='javascript:void(0)' onclick='drop( "idaud", "<?php echo $idaud ?>", "sendaud", "drop_aud", "2")' title="Excluir esta audiência">Excluir</a>
                    <?php }; ?>
            </p>
            <?php } ?>

            <table class="lista_busca">
                <tr bgcolor="#FAFAFA">
                    <td width="294" height="20" >Data/Hora: <?php echo $d_aud['data_aud_f'] ?> às <?php echo $d_aud['hora_aud_f'] ?></td>
                    <td width="294" >Tipo de apresentação: <?php echo trata_tipo_aud($d_aud['tipo_aud']) ?></td>
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
                <tr bgcolor="#FAFAFA">
                    <td height="20" colspan="2" ><?php echo $local; ?> <?php echo $d_aud['local_aud'] ?></td>
                </tr>
                <tr bgcolor="#FAFAFA">
                    <td height="20" colspan="2" >Cidade: <?php echo $d_aud['cidade_aud'] ?></td>
                </tr>
                <?php if ( $d_aud['tipo_aud'] == 1 || $d_aud['tipo_aud'] == 4 || $d_aud['tipo_aud'] == 5 || $d_aud['tipo_aud'] == 7 || $d_aud['tipo_aud'] == 8 ) { ?>
                <tr bgcolor="#FAFAFA">
                    <td height="20" colspan="2" ><?php echo $process; ?> <?php echo $d_aud['num_processo'] ?></td>
                </tr>
                <?php } ?>
                <tr bgcolor="#FAFAFA">
                    <td height="20" colspan="2" >Situação da audiência: <b><font color="<?php echo $aud['corfontaud']; ?>"><?php echo $aud['sitaud']; ?></font></b></td>
                </tr>
                <?php if ( $d_aud['sit_aud'] != 11 ) { ?>
                <tr bgcolor="#FAFAFA">
                    <td height="20" colspan="2" >Motivo: <?php echo $d_aud['motivo_justi'] ?></td>
                </tr>
                <?php } ?>
                <tr bgcolor="#FAFAFA">
                    <td height="20" align="center" ><span class="paragrafo10negrito">CADASTRAMENTO</span></td>
                    <td height="20" align="center" ><span class="paragrafo10negrito">ÚLTIMA ATUALIZAÇÃO</span></td>
                </tr>
                <tr bgcolor="#FAFAFA">
                    <td height="20" align="center" ><?php echo $user_add ?></td>
                    <td height="20" align="center" ><?php echo $user_up ?></td>
                </tr>

            </table>

            <div id="obs"></div>
            <div class="linha">
                OBSERVAÇÕES<?php if ( $n_cadastro >= 3 ) { ?> - <a href="cadobsaud.php?idaud=<?php echo $d_aud['idaudiencia'] ?>" title="Adicionar uma observação para esta audiência">Adicionar observação</a> <a href="#" title="Abrir em outra janela" onClick="javascript: ow('cadobsaud.php?idaud=<?php echo $d_aud['idaudiencia']; ?>&targ=1', '800', '550'); return false" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>user_popup.png" alt="Abrir em outra janela" class="icon_popup" /></a><?php }; ?>
                <hr />
            </div>
            <?php
                // instanciando o model
                $model = SicopModel::getInstance();

                // executando a query
                $query_obs = $model->query( $query_obs );

                // fechando a conexao
                $model->closeConnection();

                $cont_obs = $query_obs->num_rows;
                if ( !$query_obs or $cont_obs < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                    echo '<p class="p_q_no_result">Não há observações.</p>';
                } else {
            ?>
            <table class="lista_busca">
                <tr >
                    <th class="desc_data">DATA</th>
                    <th class="desc_obs">OBSERVAÇÃO</th>
                    <th class="tb_bt">&nbsp;</th>
                    <th class="tb_bt">&nbsp;</th>
                </tr>
                <?php
                while ( $dados_obs = $query_obs->fetch_assoc() ) {
                ?>
                <tr class="even">
                    <td class="desc_data"><?php echo $dados_obs['data_add_f'] ?></td>
                    <td class="desc_obs"><?php echo nl2br($dados_obs['obs_aud']) ?></td>
                    <td class="tb_bt">
                    <?php if ( $n_cadastro >= 3 ) {?>
                        <a href="editobsaud.php?idobs=<?php echo $dados_obs['id_obs_aud']; ?>" title="Alterar esta observação" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="" /></a>
                    <?php }; ?>
                    </td>
                    <td class="tb_bt">
                    <?php if ( $n_cadastro >= 4 ) { ?>
                        <a href='javascript:void(0)' onclick='drop( "id_obs_aud", "<?php echo $dados_obs['id_obs_aud']; ?>", "sendaudobs", "drop_obs_aud", "2" )' title="Excluir esta observação"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir esta observação" class="icon_button" /></a>
                    <?php }; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="desc_user">Cadastrado em <?php echo $dados_obs['data_add_fc'] ?>, usuário <?php echo $dados_obs['user_add'] ?><?php if ($dados_obs['user_up'] and $dados_obs['data_up_f']) { ?> - Atualizado em <?php echo $dados_obs['data_up_f'] ?>, usuário <?php echo $dados_obs['user_up'] ?> <?php } ?></td>
                </tr>
                <?php
                    } // /while ( $dados_obs...
                } // /if ( !$query_obs or $cont_obs < 1 ) {
                ?>
            </table>

<?php include 'footer.php'; ?>