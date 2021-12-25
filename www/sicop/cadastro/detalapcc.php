<?php
if ( !isset( $_SESSION ) ) session_start();

    require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$imp_cadastro = get_session( 'imp_cadastro', 'int' );

$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_cad_n    = 2;

$n_sind     = get_session( 'n_sind', 'int' );
$n_sind_n   = 2;

$motivo_pag = 'DETALHES DO APCC';

if ( $n_cadastro < $n_cad_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idapcc = get_get( 'idapcc', 'int' );

if ( empty( $idapcc ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( IDENTIFICADOR DO APCC EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$q_apcc = "SELECT
              apcc.idapcc,
              apcc.num_pda,
              `apcc`.`cod_detento`,
              apcc.user_add,
              DATE_FORMAT(apcc.data_add, '%d/%m/%Y às %H:%i') AS data_add,
              apcc.ip_add,
              apcc.user_up,
              DATE_FORMAT(apcc.data_up, '%d/%m/%Y às %H:%i') AS data_up,
              apcc.ip_up,
              DATE_FORMAT(apcc.data_add, '%d/%m/%Y') AS data_apcc,
              numeroapcc.numero_apcc,
              numeroapcc.ano,
              tipoconduta.conduta
            FROM
              apcc
              INNER JOIN numeroapcc ON `apcc`.`cod_numapcc` = numeroapcc.idnumapcc
              LEFT JOIN tipoconduta ON `apcc`.`cod_conduta` = tipoconduta.idconduta
            WHERE
              apcc.idapcc = $idapcc
            LIMIT 1";

$q_mov_apcc = "SELECT
                  DATE_FORMAT(movin.data_mov, '%d/%m/%Y') AS data_in_f,
                  procedencia.unidades AS procedencia,
                  DATE_FORMAT(movout.data_mov, '%d/%m/%Y') AS data_out_f,
                  destino.unidades AS destino
                FROM
                  apcc_mov
                  INNER JOIN mov_det movin ON `apcc_mov`.`cod_movin` = movin.id_mov
                  INNER JOIN unidades procedencia ON movin.cod_local_mov = procedencia.idunidades
                  LEFT JOIN mov_det movout ON `apcc_mov`.`cod_movout` = movout.id_mov
                  LEFT JOIN unidades destino ON movout.cod_local_mov = destino.idunidades
                WHERE
                  `apcc_mov`.`cod_apcc` = $idapcc
                ORDER BY
                  movin.data_mov, movout.data_mov";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_apcc = $model->query( $q_apcc );

// fechando a conexao
$model->closeConnection();

if( !$q_apcc ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_apcc = $q_apcc->num_rows;

if ( $cont_apcc < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrencias (APCC). \n\n Página $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$d_apcc = $q_apcc->fetch_assoc();

$iddet = $d_apcc['cod_detento'];

$user_add = '';
$user_up = '';

if ( !empty( $d_apcc['user_add'] ) ) {
    $user_add = 'ID usuário: ' . $d_apcc['user_add'] . ', em ' . $d_apcc['data_add'];
};

if ( !empty( $d_apcc['user_up'] ) ) {
    $user_up = 'ID usuário: ' . $d_apcc['user_up'] . ', em ' . $d_apcc['data_up'];
};

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Detalhes do APCC';

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>


            <p class="descript_page">DETALHES DO APCC</p>

            <?php include 'quali/det_cad.php'; ?>

            <p class="table_leg">Atestado</p>

            <?php if ( $n_cadastro >= 3 or $imp_cadastro >= 1 or $n_cadastro >= 4  ) { ?>
            <p class="link_common">
                <?php if ( $n_cadastro >= 3 ) { ?>
                <a href="cadapcc.php?iddet=<?php echo $iddet ?>" title="Cadastrar outro atestado para <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L ;?>">Cadastrar</a> |
                <a href="editapcc.php?idapcc=<?php echo $d_apcc['idapcc'] ?>" title="Alterar dados deste atestado">Alterar</a>
                <?php }; ?>
                <?php if ( $imp_cadastro >= 1 ) { ?>
                | <a href="#" title="Imprimir este atestado" onclick="javascript: ow('../print/apcc.php?idapcc=<?php echo $d_apcc['idapcc'] ?>', '600', '600'); return false" >Imprimir</a>
                <?php } ?>
                <?php if ( $n_cadastro >= 4 ) { ?>
                | <a href="delapcc.php?idapcc=<?php echo $d_apcc['idapcc'] ?>" title="Excluir este atestado">Excluir</a>
                <?php } ?>
            </p>
            <?php } ?>

            <p class="table_leg"><b>Número <?php echo $d_apcc['numero_apcc']?>/<?php echo $d_apcc['ano']?></b> - <b>Data <?php echo $d_apcc['data_apcc']  ?></b></p>

            <table class="lista_busca">
                <tr>
                    <th width="30" scope="col">N</th>
                    <th width="125" scope="col">DATA DA INCLUSÃO</th>
                    <th width="204" scope="col">PROCEDÊNCIA</th>
                    <th width="125" scope="col">DATA DA EXCLUSÃO</th>
                    <th width="205" scope="col">DESTINO</th>
                </tr>
                <?php
                // instanciando o model
                $model = SicopModel::getInstance();

                // executando a query
                $q_mov_apcc = $model->query( $q_mov_apcc );

                // fechando a conexao
                $model->closeConnection();

                $i = 0;
                while( $d_mov_apcc = $q_mov_apcc->fetch_assoc() ) {
                    ++$i;
                    ?>
                <tr bgcolor="#FAFAFA">
                    <td align="center"><?php echo $i; ?></td>
                    <td height="20" align="center"><?php echo $d_mov_apcc['data_in_f'] ?></td>
                    <td><?php echo $d_mov_apcc['procedencia'] ?></td>
                        <?php
                        if ( empty( $d_mov_apcc['data_out_f'] ) ) {
                            echo '<td colspan="2" align="center" ><b>PRESO ATÉ À PRESENTE DATA</b></td>';
                        } else {
                            ?>
                    <td align="center"><?php echo $d_mov_apcc['data_out_f'] ?></td>
                    <td><?php echo $d_mov_apcc['destino'] ?></td>
                </tr>
                        <?php
                    }
                }
                ?>
                <tr bgcolor="#FAFAFA">
                    <td height="20" colspan="5" align="center">Conduta: <?php echo empty( $d_apcc['conduta'] ) ? 'Sem conduta' : $d_apcc['conduta']; ?><?php if ( !empty( $d_apcc['num_pda'] ) ) { ?>&nbsp;&nbsp;&nbsp; Número do PDA: <?php echo $d_apcc['num_pda']; ?><?php } ?></td>
                </tr>
                <tr bgcolor="#FAFAFA">
                    <td width="364" height="20" colspan="3" align="center" class="paragrafo10negrito">CADASTRAMENTO</td>
                    <td width="365" height="20" colspan="2" align="center" class="paragrafo10negrito">ÚLTIMA ATUALIZAÇÃO</td>
                </tr>
                <tr bgcolor="#FAFAFA">
                    <td height="20" colspan="3" align="center"><?php echo $user_add ?></td>
                    <td height="20" colspan="2" align="center"><?php echo $user_up; ?></td>
                </tr>


            </table>

<?php include 'footer.php'; ?>