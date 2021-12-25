<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_incl   = get_session( 'n_incl', 'int' );
$n_incl_n = 3;

$motivo_pag = 'CADASTRAMENTO DE TV';

if ( $n_incl < $n_incl_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$iddet = get_get( 'iddet', 'int' );
if ( empty( $iddet ) ) {


    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( IDENTIFICADOR EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );


    echo msg_js( '', 1 );


    exit;

}

$q_matr = "SELECT
             `matricula`,
             `cod_cela`,
             `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
             `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
             `unidades_out`.`idunidades` AS iddestino
           FROM
             `detentos`
             LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
             LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
             LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
           WHERE
             `iddetento` = $iddet";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_matr = $model->query( $q_matr );

// fechando a conexao
$model->closeConnection();

if ( !$q_matr ) {

    echo msg_js( '', 1 );
    exit;

}

$d_matr = $q_matr->fetch_assoc();

$matricula = $d_matr['matricula'];
$idcela    = $d_matr['cod_cela'];

if ( empty( $matricula ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Tentativa de cadastramento de TV para ' . SICOP_DET_DESC_L . ' que não possui matrícula.';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não pode cadastrar TV para ' . SICOP_DET_DESC_L . 's que ainda não possuem matrícula.', 1 );

    exit;

}

if ( empty( $idcela ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Tentativa de cadastramento de TV para ' . SICOP_DET_DESC_L . ' que não possui cela.';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não pode cadastrar TV para um ' . SICOP_DET_DESC_L . 's que ainda não possuem cela.', 1 );

    exit;

}

$tipo_mov_in  = $d_matr['tipo_mov_in'];
$tipo_mov_out = $d_matr['tipo_mov_out'];
$iddestino    = $d_matr['iddestino'];
$sit_det      = manipula_sit_det_id( $tipo_mov_in, $tipo_mov_out, $iddestino );

$na_unidade = true;

// verifica a situação do detento
if ( $sit_det == SICOP_SIT_DET_TRANSF ||   // TRANSFERIDO
     $sit_det == SICOP_SIT_DET_EXCLUIDO ||   // EXCLUIDO (ALVARA)
     $sit_det == SICOP_SIT_DET_EVADIDO ||   // EVADIDO
     $sit_det == SICOP_SIT_DET_FALECIDO ||   // FALECIDO
     $sit_det == SICOP_SIT_DET_ACEHGAR ) {   // A CHEGAR

     $na_unidade = false;

}

$cela_possui = false;
$detento_possui = false;

$q_v_det = '';
$q_v_tv = '';
if ( $na_unidade ) {
    $q_v_tv = "SELECT
                  `detentos_tv`.`idtv`,
                  `detentos_tv`.`marca_tv`,
                  `detentos_tv`.`cor_tv`,
                  `detentos_tv`.`lacre_1`,
                  `detentos_tv`.`lacre_2`,
                  `detentos`.`iddetento`,
                  `detentos`.`nome_det`,
                  `detentos`.`matricula`,
                  `tb_cela_det`.`cela` AS cela_det,
                  `tb_raio_det`.`raio` AS raio_det,
                  `tb_cela_tv`.`cela` AS cela_tv,
                  `tb_raio_tv`.`raio` AS raio_tv
                FROM
                  `detentos_tv`
                  LEFT JOIN `detentos` ON `detentos_tv`.`cod_detento` = `detentos`.`iddetento`
                  LEFT JOIN `cela` `tb_cela_det` ON `detentos`.`cod_cela` = `tb_cela_det`.`idcela`
                  LEFT JOIN `raio` `tb_raio_det` ON `tb_cela_det`.`cod_raio` = `tb_raio_det`.`idraio`
                  LEFT JOIN `cela` `tb_cela_tv` ON `detentos_tv`.`cod_cela` = `tb_cela_tv`.`idcela`
                  LEFT JOIN `raio` `tb_raio_tv` ON `tb_cela_tv`.`cod_raio` = `tb_raio_tv`.`idraio`
                WHERE
                  `detentos_tv`.`cod_cela` = $idcela";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_v_tv = $model->query( $q_v_tv );

    // fechando a conexao
    $model->closeConnection();

    $cont_ctv = $q_v_tv->num_rows;
    if ( $cont_ctv >= 1 ) $cela_possui = true;

    $q_v_det = "SELECT
                  `detentos_tv`.`idtv`,
                  `detentos_tv`.`marca_tv`,
                  `detentos_tv`.`cor_tv`,
                  `detentos_tv`.`polegadas`,
                  `detentos_tv`.`lacre_1`,
                  `detentos_tv`.`lacre_2`,
                  `cela`.`cela`,
                  `raio`.`raio`
                FROM
                 `detentos_tv`
                  LEFT JOIN `cela` ON `detentos_tv`.`cod_cela` = `cela`.`idcela`
                  LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
                WHERE
                 `iddetento` = $iddet";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_v_det = $model->query( $q_v_det );

    // fechando a conexao
    $model->closeConnection();

    $cont_dtv = $q_v_det->num_rows;
    if ( $cont_dtv >= 1 ) $detento_possui = true;

}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Cadastrar TV';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

require 'cab.php';
$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>

            <p class="descript_page">CADASTRAR TV</p>

            <?php include 'quali/det_basic.php'; ?>

            <?php if ( $detento_possui ){ ?>

            <p class="table_leg"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_attention.png" alt="Atenção" class="icon_alert" />&nbsp;ATENÇÃO -> <?php echo SICOP_DET_PRON_FU . ' ' . SICOP_DET_DESC_L; ?> já possui TV cadastrada.</p>

            <table class="lista_busca">
                <tr align="center">
                    <td width="80" >MARCA</td>
                    <td width="80">COR </td>
                    <td width="130"><?php echo mb_strtoupper( SICOP_RAIO ) ?> / <?php echo mb_strtoupper( SICOP_CELA ) ?> DA TV</td>
                    <td width="100">LACRES</td>
                </tr>
                <?php while ( $d_v_det = $q_v_det->fetch_assoc() ) { ?>
                <tr bgcolor="#FAFAFA">
                    <td height="20"><a href="detaltv.php?idtv=<?php echo $d_v_det['idtv']; ?>"><?php echo $d_v_det['marca_tv'] ?></a></td>
                    <td><?php echo $d_v_det['cor_tv'] ?></td>
                    <td align="center"><?php echo $d_v_det['raio'] ?> - <?php echo $d_v_det['cela'] ?></td>
                    <td align="center"><?php echo $d_v_det['lacre_1'] ?> / <?php echo $d_v_det['lacre_2'] ?></td>
                </tr>
                <?php } ?>

            </table>

            <?php }?>

            <?php if ( $cela_possui ){ ?>

            <p class="table_leg"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_attention.png" alt="Atenção" class="icon_alert" />&nbsp;ATENÇÃO -> A cela que <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?> está já possui TV cadastrada.</p>

            <table class="lista_busca">
                <tr align="center">
                    <td width="80" >MARCA</td>
                    <td width="80">COR </td>
                    <td width="100"><?php echo mb_strtoupper( SICOP_RAIO ) ?> / <?php echo mb_strtoupper( SICOP_CELA ) ?> DA TV</td>
                    <td width="90">LACRES</td>
                    <td width="219"><?php echo SICOP_DET_DESC_U?></td>
                    <td width="91">MATRICULA</td>
                    <td width="100"><?php echo mb_strtoupper( SICOP_RAIO ) ?> / <?php echo mb_strtoupper( SICOP_CELA ) ?> D<?php echo SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U; ?></td>
                </tr>
                <?php while ( $d_v_tv = $q_v_tv->fetch_assoc() ) { ?>
                <tr bgcolor="#FAFAFA">
                    <td  height="20"><a href="detaltv.php?idtv=<?php echo $d_v_tv['idtv']; ?>"><?php echo $d_v_tv['marca_tv'] ?></a></td>
                    <td><?php echo $d_v_tv['cor_tv'] ?></td>
                    <td align="center"><?php echo $d_v_tv['raio_tv'] ?> - <?php echo $d_v_tv['cela_tv'] ?></td>
                    <td align="center"><?php echo $d_v_tv['lacre_1'] ?> / <?php echo $d_v_tv['lacre_2'] ?></td>
                    <td><a href="<?php echo SICOP_ABS_PATH; ?>detento/detalhesdet.php?iddet=<?php echo $d_v_tv['iddetento']; ?>" title="Clique aqui para abrir a qualificativa d<?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>"><?php echo $d_v_tv['nome_det'] ?></a></td>
                    <td align="center"><?php if ( !empty( $d_v_tv['matricula'] ) ) echo formata_num( $d_v_tv['matricula'] ); ?></td>
                    <td align="center"><?php echo $d_v_tv['raio_det'] ?> - <?php echo $d_v_tv['cela_det'] ?></td>
                </tr>

                <?php } ?>

            </table>


            <?php }?>

            <?php
            if ( !$cela_possui and !$detento_possui ){
                if ( !$na_unidade ) {
                    echo '<p class="p_q_no_result">' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_L . ' não esta na unidade.</p>';
                } else {
            ?>

            <form action="<?php echo SICOP_ABS_PATH; ?>send/sendtv.php" method="post" name="cadtv" id="cadtv">

                <table class="edit">
                    <tr >
                        <td width="70" height="20">Data:</td>
                        <td width="140"><input name="data_tv" type="text" class="CaixaTexto" id="data_tv" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" size="12" maxlength="10" /></td>
                    </tr>
                    <tr >
                        <td height="20">Marca:</td>
                        <td><input name="marca_tv" type="text" class="CaixaTexto" id="marca_tv" onBlur="upperMe(this); remacc(this); " onKeyPress="return blockChars(event, 4);" size="22" maxlength="20" /></td>
                    </tr>
                    <tr >
                        <td height="20">Cor:</td>
                        <td><input name="cor_tv" type="text" class="CaixaTexto" id="cor_tv" onBlur="upperMe(this); remacc(this); " onKeyPress="return blockChars(event, 4);" size="22" maxlength="20" /></td>
                    </tr>
                    <tr >
                        <td height="20">Polegadas:</td>
                        <td><input name="polegadas" type="text" class="CaixaTexto" id="polegadas" onKeyPress="return blockChars(event, 2);" size="3" maxlength="2" /></td>
                    </tr>
                    <tr >
                        <td height="20">Lacres:</td>
                        <td>
                            <input name="lacre_1" type="text" class="CaixaTexto" id="lacre_1" onKeyPress="return blockChars(event, 2);" size="6" maxlength="5" /> /
                            <input name="lacre_2" type="text" class="CaixaTexto" id="lacre_2" onKeyPress="return blockChars(event, 2);" size="6" maxlength="5" />
                        </td>
                    </tr>
                </table>

                <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet;?>">
                <input type="hidden" name="proced" id="proced" value="3">

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Cadastrar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>


            </form>

            <script type="text/javascript">

                $(function() {

                    $( "#data_tv" ).focus();
                    $( "#data_tv" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });

                    $("form").submit(function() {
                        if ( validacadtv() == true ) {
                            // ReadOnly em todos os inputs
                            $("input", this).attr("readonly", true);
                            // Desabilita os submits
                            $("input[type='submit'],input[type='image']", this).attr("disabled", true);
                            return true;
                        } else {
                            return false;
                        }
                    });
                });

            </script>

            <?php
                }
            }
            ?>

<?php include 'footer.php'; ?>