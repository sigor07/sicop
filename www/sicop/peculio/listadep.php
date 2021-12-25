<?php

if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

keepHistory();

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

if (empty($_GET['op'])) {
    $ordpor = 'nomea';
} else {
    $ordpor    = $_GET['op'];
}

$ordpor = tratabusca($ordpor);

switch($ordpor) {
    default:
    case 'nomea':
        $ordbusca = "detentos.nome_det ASC";
        break;
    case 'nomed':
        $ordbusca = "detentos.nome_det DESC";
        break;
    case 'matra':
        $ordbusca = "detentos.matricula ASC";
        break;
    case 'matrd':
        $ordbusca = "detentos.matricula DESC";
        break;
    case 'valora':
        $ordbusca = "peculio_mov.valor ASC, detentos.nome_det ASC";
        break;
    case 'valord':
        $ordbusca = "peculio_mov.valor DESC, detentos.nome_det ASC";
        break;
    case 'dataa':
        $ordbusca = "peculio_mov.data_add ASC, detentos.nome_det ASC";
        break;
    case 'datad':
        $ordbusca = "peculio_mov.data_add DESC, detentos.nome_det ASC";
        break;
}

$where = 'peculio_mov.confirm = FALSE AND peculio_mov.operacao = 1';


$query = "SELECT
              peculio_mov.idpeculio,
              peculio_mov.operacao,
              peculio_mov.valor,
              peculio_mov.confirm,
              peculio_mov.data_add,
              DATE_FORMAT(peculio_mov.data_add,'%d/%m/%Y') AS data_add_f,
              detentos.iddetento,
              detentos.nome_det,
              detentos.matricula,
              detentos.pai_det,
              detentos.mae_det,
              `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
              `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
              `unidades_out`.`idunidades` AS iddestino,
              cela.cela,
              raio.raio,
              sicop_users.nome_cham
            FROM
              peculio_mov
              INNER JOIN detentos ON peculio_mov.cod_detento = detentos.iddetento
              LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
              LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
              LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
              LEFT JOIN cela ON `detentos`.`cod_cela` = cela.idcela
              LEFT JOIN raio ON `cela`.`cod_raio` = raio.idraio
              LEFT JOIN sicop_users ON peculio_mov.user_add = sicop_users.iduser
            WHERE
        $where
            ORDER BY
        $ordbusca";


// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query = $model->query( $query );

// fechando a conexao
$model->closeConnection();

if( !$query ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont = $query->num_rows;

$querytime = $model->getQueryTime();


require 'cab.php';
$trail = new Breadcrumb();
$trail->add('Lista da casa', $_SERVER['PHP_SELF'], 3);
$trail->output();
?>

<script type="text/javascript" src="<?php echo SICOP_ABS_PATH ?>js/valida.js"></script>

<br />
<p align="center" class="paragrafo14Italico">DEPÓSITOS PENDENDO CONFIRMAÇÃO</p>

<?php
if(empty($cont) or $cont < 1) { // se o número de ocorrências for menor do que 1, mostra a mensagem
    echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>
          </body>
          </html>';
    exit;
}

?>

<p align="center">Essa consulta retornou <?php echo $cont ?> registros (<?php echo round($querytime, 2) ?> seg).</p>
<form action="<?php echo SICOP_ABS_PATH ?>send/sendpeculioconf.php" method="post" name="sendpeculioconf" id="sendpeculioconf" onSubmit="return validaapcc();">
    <table width="762" align="center" cellpadding="1" cellspacing="3" class="space">
        <tr>
            <th width="32" align="center">N</th>
            <th width="300" align="center"><?php echo SICOP_DET_DESC_FU; ?>
                    <?php if ($ordpor == 'nomea') {?>
                <img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_asc_m.png" alt="" width="11" height="9" />
                    <?php } else { ?>
                <a href="?op=nomea" title="Ordenar por nome do detento crescente" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_asc.png" alt="" width="11" height="9" /></a>
                    <?php }; ?>
                    <?php if ($ordpor == 'nomed') {?>
                <img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_desc_m.png" alt="" width="11" height="9" />
                    <?php } else { ?>
                <a href="?op=nomed" title="Ordenar por nome do detento decrescente" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_desc.png" alt="" width="11" height="9" /></a>
                    <?php }; ?></th>
            <th width="94" align="center">Matrícula
                    <?php if ($ordpor == 'matra') {?>
                <img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_asc_m.png" alt="" width="11" height="9" />
                    <?php } else { ?>
                <a href="?op=matra" title="Ordenar por matrícula crescente" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_asc.png" alt="" width="11" height="9" /></a>
                    <?php }; ?>
                    <?php if ($ordpor == 'matrd') {?>
                <img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_desc_m.png" alt="" width="11" height="9" />
                    <?php } else { ?>
                <a href="?op=matrd" title="Ordenar por matrícula decrescente" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_desc.png" alt="" width="11" height="9" /></a>
                    <?php }; ?>
            </th>
            <th width="100" align="center">Valor
                    <?php if ($ordpor == 'valora') {?>
                <img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_asc_m.png" alt="" width="11" height="9" />
                    <?php } else { ?>
                <a href="?op=valora" title="Ordenar por valor crescente" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_asc.png" alt="" width="11" height="9" /></a>
                    <?php }; ?>
                    <?php if ($ordpor == 'valord') {?>
                <img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_desc_m.png" alt="" width="11" height="9" />
                    <?php } else { ?>
                <a href="?op=valord" title="Ordenar por valor decrescente" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_desc.png" alt="" width="11" height="9" /></a>
                    <?php }; ?>
            </th>
            <th width="84" align="center">Data
                    <?php if ($ordpor == 'dataa') {?>
                <img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_asc_m.png" alt="" width="11" height="9" />
                    <?php } else { ?>
                <a href="?op=dataa" title="Ordenar pela data crescente" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_asc.png" alt="" width="11" height="9" /></a>
                    <?php }; ?>
                    <?php if ($ordpor == 'datad') {?>
                <img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_desc_m.png" alt="" width="11" height="9" />
                    <?php } else { ?>
                <a href="?op=datad" title="Ordenar pela data decrescente" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_desc.png" alt="" width="11" height="9" /></a>
                    <?php }; ?>
            </th>
            <th width="100" align="center">Usuário</th>
            <th width="20" align="center">&nbsp;</th>
            <?php

            $i = 0;

            $corlinha = "#F0F0F0";

            while($d_det = $query->fetch_assoc()) {

                $tipo_mov_in  = $d_det['tipo_mov_in'];
                $tipo_mov_out = $d_det['tipo_mov_out'];
                $iddestino    = $d_det['iddestino'];

                $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );

                ?>
        </tr>
        <tr bgcolor="#FAFAFA" class="even" height="20">
            <td align="center" ><?php echo ++$i ?></td>
            <td align="left"><a href="<?php echo SICOP_ABS_PATH ?>detento/detalhesdet.php?iddet=<?php echo $d_det['iddetento'];?>" class="dcontexto"> <?php echo $d_det['nome_det'];?><span class="dc"><b>Pai:</b> <?php echo $d_det['pai_det'];?> <br /><b>Mãe:</b> <?php echo $d_det['mae_det'];?> <br /><b><?php echo SICOP_RAIO ?>:</b> <?php echo $d_det['raio'];?> <br /><b><?php echo SICOP_CELA ?>:</b> <?php echo $d_det['cela'];?> <br /><b>Situação atual:</b> <?php echo $det['sitat'];?> </span> </a></td>
            <td align="center"><font color="<?php echo $det['corfontd'];?>"><?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] );?></font></td>
            <td align="center"><?php echo number_format($d_det['valor'], 2, ',', '.')  ?></td>
            <td align="center"><?php echo $d_det['data_add_f'] ?></td>
            <td align="center"><?php echo $d_det['nome_cham'] ?></td>
            <td align="center"><input name="idpeculio[]" type="checkbox" id="idpeculio" value="<?php echo $d_det['idpeculio'] ?>" /></td>
        <?php } ?>
        </tr>
        <tr >
            <td colspan="7" align="right"><a onclick="if (markAllRows('sendpeculioconf')) return false;" href="#" >Marcar todos</a> / <a onclick="if (unMarkAllRows('sendpeculioconf')) return false;" href="#" >Desmarcar todos</a> <img src="<?php echo SICOP_SYS_IMG_PATH; ?>arrow_rtl.png" alt="Ir para o início" width="38" height="22" align="absmiddle" /></td>
        </tr>
    </table>
    <input name="proced" type="hidden" id="proced" value="1">
    <p align="center"><input name="atualizar" type="submit" value="Confirmar marcados" />&nbsp;&nbsp;&nbsp;<input name="" type="button" onclick="history.go(-1)" value="Cancelar" /></p>
</form>

<?php include 'footer.php'; ?>