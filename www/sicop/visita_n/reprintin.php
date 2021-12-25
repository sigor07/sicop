<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_rol   = get_session( 'n_rol', 'int' );
$n_rol_n = 3;

if ($n_rol < $n_rol_n) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'REIMPRESSÃO DO FORMULÁRIO DE ENTRADA DE VISITANTES';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $num_seq = (int)$_POST['num_seq'];

    if ( empty($num_seq) ){
        $mensagem = "ERRO -> Número de sequência de entrada em branco. Operação cancelada (EXCLUSÃO DE REGISTRO DE ENTRADA NA PÁGINA DE BUSCA).\n\n Página: $pag";
        salvaLog($mensagem);
        require 'cab_simp.php';
        echo msg_js( 'Não encontrado!!!', 2 );
        exit;
    }

    $q_reg = "SELECT
                  `visita_mov`.`cod_visita`,
                  `visitas`.`iddetento`,
                  `visitas`.`nome_visit`,
                  `visitas`.`sexo_visit`,
                  `visitas`.`nasc_visit`,
                   DATE_FORMAT(`visitas`.`nasc_visit`, '%d/%m/%Y') AS nasc_visit_f,
                   FLOOR(DATEDIFF(CURDATE(), `visitas`.`nasc_visit`) / 365.25) AS idade_visit,
                   `tipoparentesco`.`parentesco`
                FROM
                  `visita_mov`
                  INNER JOIN `visitas` ON `visita_mov`.`cod_visita` = `visitas`.`idvisita`
                  INNER JOIN `tipoparentesco` ON `visitas`.`cod_parentesco` = `tipoparentesco`.`idparentesco`
                WHERE
                  `visita_mov`.`num_seq` = $num_seq AND DATE(`visita_mov`.`data_in`) = DATE(NOW())
                ORDER BY idade_visit DESC";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_reg = $model->query( $q_reg );

    // fechando a conexao
    $model->closeConnection();

    if( !$q_reg ) {

        echo msg_js( 'Não encontrado!!!', 1 );
        exit;

    }

    $contr = $q_reg->num_rows;

    if( $contr < 1 ) {
        $mensagem = "A consulta retornou 0 ocorrencias (IMPRESSÃO DE REGISTRO DE ENTRADA NA PÁGINA DE BUSCA). \n\n Página $pag";
        salvaLog($mensagem);
        require 'cab_simp.php';
        echo msg_js( 'Não encontrado!!!', 1 );
        exit;
    }


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
                  `detentos`.`iddetento` = ( SELECT `visitas`.`cod_detento` FROM `visita_mov` INNER JOIN `visitas` ON `visita_mov`.`cod_visita` = `visitas`.`idvisita` WHERE `visita_mov`.`num_seq` = $num_seq AND DATE(`visita_mov`.`data_in`) = DATE(NOW()) LIMIT 1 )
                LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_det = $model->query( $q_det );

    // fechando a conexao
    $model->closeConnection();

    if( !$q_det ) {

        echo msg_js( 'Não encontrado!!!', 1 );
        exit;

    }

    $contd = $q_det->num_rows;

    if ( $contd < 1 ) {
        $mensagem = "A consulta retornou 0 ocorrencias (DETENTOS).\n\n Página $pag";
        salvaLog( $mensagem );
        require 'cab_simp.php';
        echo msg_js( 'Não encontrado!!!', 1 );
        exit;
    }

    $d_det = $q_det->fetch_assoc();

    $mensagem = "Acesso à página $pag";
    salvaLog($mensagem);

}


require 'cab.php';
//echo returnHistory() . '<br>';
//echo returnHistory(2);
?>

            <p class="descript_page">IMPRIMIR FORMULÁRIO DE ENTRADA DE VISITANTE</p>

            <p style="text-align: center">
                <img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_attention.png" alt="Atenção" class="icon_alert" />&nbsp; Você só poderá reimprimir formulário de hoje.
            </p>

            <?php if ( empty( $_POST ) ) { ?>
            <form action="reprintin.php" method="post" name="reprintin" id="reprintin">


                <p class="table_leg">Digite o número de sequência do registro que você deseja imprimir:</p>

                <div class="form_one_field">
                    <input name="num_seq" type="text" class="CaixaTexto" id="num_seq" onKeyPress="return blockChars(event, 2);" size="3" maxlength="3" />
                </div>

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="buscar" id="buscar" value="Buscar" />
                    <input class="form_bt" type="button" name="" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

        <script type="text/javascript">id("num_seq").focus();</script>

        <?php } else {?>

        <p class="table_leg">Visitantes que estão registrados na sequência número <?php echo $num_seq;?>:</p>
            <table class="lista_busca">
                <tr>
                    <th class="visit_nome">NOME DO VISITANTE</th>
                    <th class="visit_data_nasc">NASCIMENTO</th>
                    <th class="visit_parent">PARENTESCO</th>
                    <th class="visit_sexo">SEXO</th>
                </tr>
                    <?php while( $d_visit = $q_reg->fetch_assoc() ) {?>
                <tr class="even">
                    <td class="visit_nome"><?php echo $d_visit['nome_visit']; ?></td>
                    <td class="visit_data_nasc"><?php echo empty( $d_visit['nasc_visit_f'] ) ? '' : $d_visit['nasc_visit_f'] . ' - ' . $d_visit['idade_visit'] . ' anos'; // echo pegaIdade($d_visit['data_nasc'])   ?></td>
                    <td class="visit_parent"><?php echo $d_visit['parentesco'] ?></td>
                    <td class="visit_sexo"><?php echo $d_visit['sexo_visit'] ?></td>
                </tr>
                <?php }// fim do while ?>
            </table>

            <p class="table_leg"><?php echo SICOP_DET_DESC_FU; ?> que recebeu a(s) visita(s):</p>

            <table class="lista_busca">
                <tr bgcolor="#ECE9D8">
                    <td width="360" height="20" >Nome: <a href="<?php echo SICOP_ABS_PATH ?>detento/detalhesdet.php?iddet=<?php echo $d_det['iddetento']; ?>" title="Clique aqui para abrir a qualificativa deste detento"><?php echo $d_det['nome_det']; ?></a></td>
                    <td width="140" height="20" > Matrícula: <?php echo formata_num( $d_det['matricula'] ) ?></td>
                    <td width="140" height="20" align="center"><?php echo (empty( $d_det['raio'] )) ? '' : SICOP_RAIO . ': ' . $d_det['raio']; ?>&nbsp;&nbsp;&nbsp;<?php echo (empty( $d_det['cela'] )) ? '' : SICOP_CELA . ': ' . $d_det['cela']; ?></td>
                </tr>
            </table>

            <p class="descript_page">Imprimir este formulário de entrada?</p>

            <div class="form_bts">
                <input class="form_bt" name="imprimir" id="imprimir" type="button" onclick="javascript: ow ('../print/ent_visit.php?num_seq=<?php echo $num_seq; ?>', '600', '600'); history.go(-2);" value="Imprimir" />
                <input class="form_bt" name="" type="button" onclick="history.go(-2)" value="Cancelar" />
            </div>

<?php }?>

<?php include 'footer.php'; ?>