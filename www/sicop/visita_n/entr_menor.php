<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_rol   = get_session( 'n_rol', 'int' );
$imp_rol = get_session( 'imp_rol', 'int' );
$n_rol_n = 3;

if ( $n_rol < $n_rol_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'DETALHES DO VISITANTE';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idvisit = get_get( 'idvisit', 'int' );
if ( empty( $idvisit ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página. Identificador do visitante em branco. ( REGISTRO DE ENTRADA DE MENORES )';
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$jumbo   = get_get( 'jumbo', 'int' );

$query_visit_adult = "SELECT
                        `idvisita`,
                        `cod_detento`,
                        `nome_visit`,
                        `rg_visit`,
                        `sexo_visit`
                    FROM
                        `visitas`
                    WHERE
                        `idvisita` = $idvisit
                    LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_visit_adult = $model->query( $query_visit_adult );

// fechando a conexao
$model->closeConnection();

if ( !$query_visit_adult ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Falha na consulta ( REGISTRO DE ENTRADA DE MENORES - VISITANTES ADULTOS ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$contva = $query_visit_adult->num_rows;

if ( $contva < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'A consulta retornou 0 ocorrências ( REGISTRO DE ENTRADA DE MENORES - VISITANTES ADULTOS ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_visit_a = $query_visit_adult->fetch_assoc();

$query_det = "SELECT
                `detentos`.`iddetento`,
                `detentos`.`nome_det`,
                `detentos`.`matricula`,
                `detentos`.`aut_visita`,
                `cela`.`cela`,
                `raio`.`raio`
              FROM
                `detentos`
                LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
              WHERE
                `detentos`.`iddetento` = (SELECT `cod_detento` FROM `visitas` WHERE `idvisita` = $idvisit LIMIT 1)
              LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_det = $model->query( $query_det );

// fechando a conexao
$model->closeConnection();

if ( !$query_det ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Falha na consulta ( REGISTRO DE ENTRADA DE MENORES - DETENTO ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$contd = $query_det->num_rows;

if ( $contd < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'A consulta retornou 0 ocorrências ( REGISTRO DE ENTRADA DE MENORES - DETENTO ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_det = $query_det->fetch_assoc();

$iddet = $d_det['iddetento'];

$query_visit_menor = "SELECT
                        `visitas`.`idvisita`,
                        `visitas`.`cod_detento`,
                        `visitas`.`nome_visit`,
                        `visitas`.`sexo_visit`,
                        `visitas`.`nasc_visit`,
                        DATE_FORMAT( `visitas`.`nasc_visit`, '%d/%m/%Y' ) AS nasc_visit_f,
                        FLOOR( DATEDIFF( CURDATE(), `visitas`.`nasc_visit` ) / 365.25 ) AS idade_visit,
                        `tipoparentesco`.`parentesco`
                     FROM
                        `visitas`
                        LEFT JOIN `tipoparentesco` ON `visitas`.`cod_parentesco` = `tipoparentesco`.`idparentesco`
                     WHERE
                        `visitas`.`cod_detento` = $iddet
                        AND
                        `visitas`.`num_in` = ( SELECT `n_p_trans` FROM `detentos` WHERE `iddetento` = $iddet LIMIT 1 )
                        AND
                        ( FLOOR( DATEDIFF( CURDATE(), visitas.`nasc_visit`)/365.25 ) < 18 OR ISNULL( `visitas`.`nasc_visit` ) )
                     ORDER BY `visitas`.`nome_visit` ASC";



$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Registrar entrada de visitantes';

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 5 );
$trail->output();
?>

            <p class="descript_page">REGISTRAR ENTRADA DE VISITANTES COM MENORES</p>

            <p class="table_leg"><?php echo SICOP_DET_DESC_FU; ?></p>

            <table class="lista_busca">
                <tr bgcolor="#ECE9D8">
                    <td width="400" height="20" ><a href="<?php echo SICOP_ABS_PATH ?>detento/detalhesdet.php?iddet=<?php echo $iddet; ?>" title="Clique aqui para abrir a qualificativa deste detento"><?php echo $d_det['nome_det']; ?></a></td>
                    <td width="140" height="20" > Matrícula: <?php echo formata_num( $d_det['matricula'] ) ?></td>
                    <td width="140" height="20" align="center"><span style="font-size: 12px; font-weight: bold;"><?php echo (empty( $d_det['raio'] )) ? '' : SICOP_RAIO . ': ' . $d_det['raio']; ?>&nbsp;&nbsp;&nbsp;<?php echo (empty( $d_det['cela'] )) ? '' : SICOP_CELA . ': ' . $d_det['cela']; ?></span></td>
                </tr>
            </table>

            <p class="table_leg">Visitante</p>

            <table class="lista_busca">
                <tr bgcolor="#ECE9D8">
                    <td width="400" height="20" ><a href="detalvisit.php?idvisit=<?php echo $idvisit; ?>" title="Clique aqui para abrir o cadastro deste visitante"><?php echo $d_visit_a['nome_visit']; ?></a></td>
                    <td width="140" height="20" >R.G. <?php echo $d_visit_a['rg_visit'] ?></td>
                    <td width="140" height="20" align="center">Sexo: <?php echo $d_visit_a['sexo_visit'] ?></td>
                </tr>
            </table>

            <div class="linha">
                MENORES CADASTRADOS (ativa - <font color="#FF0000">excluida</font> - <font color="#CC9900">suspensa</font>) <?php if ( $n_rol >= 3 and !empty( $d_det['matricula'] ) ) { ?> - <a href="cadastravisit.php?iddet=<?php echo $iddet ?>">Cadastrar visitante</a><?php }; ?>
                <hr />
            </div>
            <?php

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $query_visit_menor = $model->query( $query_visit_menor );

            // fechando a conexao
            $model->closeConnection();

            $contvm = 0;

            if( $query_visit_menor ) $contvm = $query_visit_menor->num_rows;

            if ( $contvm < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não há visitantes menores cadastrados.</p>';
            } else {
            ?>
            <form action="<?php echo SICOP_ABS_PATH ?>send/sendvisitin.php" method="post" name="visit_in" id="visit_in" >
                <table class="lista_busca">
                    <tr>
                        <th class="visit_id">ID</th>
                        <th class="visit_nome">NOME DO VISITANTE</th>
                        <th class="visit_data_nasc">NASCIMENTO</th>
                        <th class="visit_parent">PARENTESCO</th>
                        <th class="visit_sexo">SEXO</th>
                        <th class="tb_bt">&nbsp;</th>
                        <th class="tb_bt">&nbsp;</th>
                    </tr>
                <?php

                // instanciando o model
                $model = SicopModel::getInstance();

                while ( $d_visit_m = $query_visit_menor->fetch_assoc() ) {

                    $idvisita = $d_visit_m['idvisita'];

                    $visit = manipula_sit_visia( $idvisita );

                    $entra    = true;
                    $entrou   = false;
                    $dn       = true;
                    $suspenso = false;
                    $corfontv = '#000000';

                    if ( $visit ) {

                        $suspenso = $visit['suspenso'];
                        $corfontv = $visit['corfontv'];

                    }

                    if ( $suspenso ) {

                        $entra = false;

                    }

                    //se o visitante não estiver suspenso, verifica se já entrou
                    if ( !$suspenso ) {
                        $corfontv = '#000000';
                        $query_ent = "SELECT `idmov_visit`, `jumbo` FROM `visita_mov` WHERE `idvisita` = $idvisita AND DATE(`data_in`) = DATE(NOW()) LIMIT 1";

                        // executando a query
                        $query_ent = $model->query( $query_ent );

                        $cont_ent = 0;

                        if( $query_ent ) $cont_ent = $query_ent->num_rows;

                        if ( $cont_ent >= 1 ) {
                            $entrou = true;
                            $entra = false;
                        }

                    }

                    if ( empty( $d_visit_m['nasc_visit_f'] ) ) {
                        $entra = false;
                        $dn = false;
                    }

                ?>
                    <tr class="even">
                        <td class="visit_id"><?php echo $d_visit_m['idvisita'] ?></td>
                        <td class="visit_nome"><a href="detalvisit.php?idvisit=<?php echo $d_visit_m['idvisita'] ?>&proced=1"><?php echo $d_visit_m['nome_visit'] ?></a></td>
                        <td class="visit_data_nasc <?php echo $visit['css_class']; ?>"><?php echo $d_visit_m['nasc_visit_f'] ?><?php echo!is_null( $d_visit_m['idade_visit'] ) /* $d_visit_m['idade_visit'] != '' */ ? ' - ' . $d_visit_m['idade_visit'] . ' anos' : ''; ?></td>
                        <td class="visit_parent <?php echo $visit['css_class']; ?>"><?php echo $d_visit_m['parentesco'] ?></td>
                        <td class="visit_sexo <?php echo $visit['css_class']; ?>"><?php echo $d_visit_m['sexo_visit'] ?></td>
                        <td class="tb_bt"><?php if ( $n_rol >= 3 ) { ?><a href="editvisit.php?idvisit=<?php echo $d_visit_m['idvisita']; ?>" title="Alterar dados deste visitante" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="Alterar dados deste visitante" class="icon_button" /></a><?php }; ?></td>
                        <td class="tb_bt">
                        <?php if ( $entra ) { ?>
                            <input name="idvisit[]" type="checkbox" value="<?php echo $d_visit_m['idvisita'] ?>" />
                        <?php } else { ?>
                            <?php if ( $entrou ) { ?>
                            <div class="dcontexto"><span>Este visitante já está na unidade</span> <img src="<?php echo SICOP_SYS_IMG_PATH; ?>visit_in.png" alt="" class="icon_button" /></div>
                            <?php } else if ( !$dn and !$suspenso ) {?>
                            <div class="dcontexto"><span>Este visitante não está com a data de nascimento preenchida, portanto não está autorizado à entrar na unidade</span> <img src="<?php echo SICOP_SYS_IMG_PATH; ?>block.png" alt="" class="icon_button" /></div>
                            <?php } else { ?>
                            <div class="dcontexto"><span>Este visitante não está autorizado à entrar na unidade</span> <img src="<?php echo SICOP_SYS_IMG_PATH; ?>block.png" alt="" class="icon_button" /></div>
                            <?php } ?>
                        <?php } ?>
                        </td>
                    </tr>

                <?php

                } // fim do while

                // fechando a conexao
                $model->closeConnection();

                ?>
                </table>
                <?php } // fim do if que conta o número de ocorrencias ?>



                <input type="hidden" name="jumbo" id="jumbo" value="<?php echo $jumbo ?>" />
                <input type="hidden" name="idvisit[]" id="idvisit" value="<?php echo $d_visit_a['idvisita'] ?>" />
                <input type="hidden" name="proced" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" value="Registrar entrada" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

<?php include 'footer.php';?>