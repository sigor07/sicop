<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_incl   = get_session( 'n_incl', 'int' );
$n_incl_n = 3;

$motivo_pag = 'CADASTRAMENTO RÁDIO';

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

if ( empty( $matricula ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Tentativa de cadastramento de rádio para ' . SICOP_DET_DESC_L . ' que não possui matrícula.';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não pode cadastrar rádio para ' . SICOP_DET_DESC_L . 's que ainda não possuem matrícula.', 1 );

    exit;

}

$idcela    = $d_matr['cod_cela'];

if ( empty( $idcela ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Tentativa de cadastramento de rádio para ' . SICOP_DET_DESC_L . ' que não possui cela.';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não pode cadastrar rádio para ' . SICOP_DET_DESC_L . 's que ainda não possuem cela.', 1 );

    exit;

}


$tipo_mov_in  = $d_matr['tipo_mov_in'];
$tipo_mov_out = $d_matr['tipo_mov_out'];
$iddestino    = $d_matr['iddestino'];
$sit_det      = manipula_sit_det_id( $tipo_mov_in, $tipo_mov_out, $iddestino );

$na_unidade = true;

// verifica a situação do detento
if ( $sit_det == SICOP_SIT_DET_TRANSF || // TRANSFERIDO
     $sit_det == SICOP_SIT_DET_EXCLUIDO || // EXCLUIDO (ALVARA)
     $sit_det == SICOP_SIT_DET_EVADIDO || // EVADIDO
     $sit_det == SICOP_SIT_DET_FALECIDO || // FALECIDO
     $sit_det == SICOP_SIT_DET_ACEHGAR ) {   // A CHEGAR

    $na_unidade = false;

}

$cela_possui = false;
$detento_possui = false;

$q_v_det   = '';
$q_v_radio = '';
if ( $na_unidade ) {
    $q_v_radio = "SELECT
                  `detentos_radio`.`idradio`,
                  `detentos_radio`.`marca_radio`,
                  `detentos_radio`.`cor_radio`,
                  `detentos_radio`.`lacre_1`,
                  `detentos_radio`.`lacre_2`,
                  `detentos`.`iddetento`,
                  `detentos`.`nome_det`,
                  `detentos`.`matricula`,
                  `tb_cela_det`.`cela` AS cela_det,
                  `tb_raio_det`.`raio` AS raio_det,
                  `tb_cela_radio`.`cela` AS cela_radio,
                  `tb_raio_radio`.`raio` AS raio_radio
                FROM
                  `detentos_radio`
                  LEFT JOIN `detentos` ON `detentos_radio`.`cod_detento` = `detentos`.`iddetento`
                  LEFT JOIN `cela` `tb_cela_det` ON `detentos`.`cod_cela` = `tb_cela_det`.`idcela`
                  LEFT JOIN `raio` `tb_raio_det` ON `tb_cela_det`.`cod_raio` = `tb_raio_det`.`idraio`
                  LEFT JOIN `cela` `tb_cela_radio` ON `detentos_radio`.`cod_cela` = `tb_cela_radio`.`idcela`
                  LEFT JOIN `raio` `tb_raio_radio` ON `tb_cela_radio`.`cod_raio` = `tb_raio_radio`.`idraio`
                WHERE
                  `detentos_radio`.`cod_cela` = $idcela";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_v_radio = $model->query( $q_v_radio );

    // fechando a conexao
    $model->closeConnection();

    $cont_cradio = $q_v_radio->num_rows;
    if ( $cont_cradio >= 1 ) $cela_possui = true;

    $q_v_det = "SELECT
                  `detentos_radio`.`idradio`,
                  `detentos_radio`.`marca_radio`,
                  `detentos_radio`.`cor_radio`,
                  `detentos_radio`.`faixas`,
                  `detentos_radio`.`lacre_1`,
                  `detentos_radio`.`lacre_2`,
                  `cela`.`cela`,
                  `raio`.`raio`
                FROM
                 `detentos_radio`
                  LEFT JOIN `cela` ON `detentos_radio`.`cod_cela` = `cela`.`idcela`
                  LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
                WHERE
                 `iddetento` = $iddet";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_v_det = $model->query( $q_v_det );

    // fechando a conexao
    $model->closeConnection();

    $cont_dradio = $q_v_det->num_rows;
    if ( $cont_dradio >= 1 ) $detento_possui = true;

}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Cadastrar rádio';

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

            <p class="descript_page">CADASTRAR RÁDIO</p>

            <?php include 'quali/det_basic.php'; ?>

            <?php if ( $detento_possui ){ ?>

            <p class="table_leg"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_attention.png" alt="Atenção" class="icon_alert" />&nbsp;ATENÇÃO -> <?php echo SICOP_DET_PRON_FU . ' ' . SICOP_DET_DESC_L; ?> já possui rádio cadastrado.</p>

            <table class="lista_busca">
                <tr align="center">
                    <td width="80" >MARCA</td>
                    <td width="80">COR </td>
                    <td width="130"><?php echo mb_strtoupper( SICOP_RAIO ) ?> / <?php echo mb_strtoupper( SICOP_CELA ) ?> DO RÁDIO</td>
                    <td width="100">LACRES</td>
                </tr>
                <?php while( $d_v_det = $q_v_det->fetch_assoc() ) { ?>
                <tr bgcolor="#FAFAFA">
                    <td height="20"><a href="detalradio.php?idradio=<?php echo $d_v_det['idradio'];?>"><?php echo $d_v_det['marca_radio'] ?></a></td>
                    <td><?php echo $d_v_det['cor_radio'] ?></td>
                    <td align="center"><?php echo $d_v_det['raio'] ?> - <?php echo $d_v_det['cela'] ?></td>
                    <td align="center"><?php echo $d_v_det['lacre_1'] ?> / <?php echo $d_v_det['lacre_2'] ?></td>
                </tr>
                <?php } ?>
            </table>

            <?php }?>

            <?php if ( $cela_possui ){ ?>

            <p class="table_leg"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_attention.png" alt="Atenção" class="icon_alert" />&nbsp;ATENÇÃO -> A cela que <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?> está já possui rádio cadastrado.</p>

            <table class="lista_busca">

                <tr align="center">
                    <td width="80" >MARCA</td>
                    <td width="80">COR </td>
                    <td width="100"><?php echo mb_strtoupper( SICOP_RAIO ) ?> / <?php echo mb_strtoupper( SICOP_CELA ) ?> DO RÁDIO</td>
                    <td width="90">LACRES</td>
                    <td width="219"><?php echo SICOP_DET_DESC_U?></td>
                    <td width="91">MATRICULA</td>
                    <td width="100"><?php echo mb_strtoupper( SICOP_RAIO ) ?> / <?php echo mb_strtoupper( SICOP_CELA ) ?> D<?php echo SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U; ?></td>
                </tr>

                <?php while( $d_v_radio = $q_v_radio->fetch_assoc() ) { ?>

                <tr bgcolor="#FAFAFA">
                    <td height="20"><a href="detalradio.php?idradio=<?php echo $d_v_radio['idradio'];?>"><?php echo $d_v_radio['marca_radio'] ?></a></td>
                    <td><?php echo $d_v_radio['cor_radio'] ?></td>
                    <td align="center"><?php echo $d_v_radio['raio_radio'] ?> - <?php echo $d_v_radio['cela_radio'] ?></td>
                    <td align="center"><?php echo $d_v_radio['lacre_1'] ?> / <?php echo $d_v_radio['lacre_2'] ?></td>
                    <td><a href="<?php echo SICOP_ABS_PATH; ?>detento/detalhesdet.php?iddet=<?php echo $d_v_radio['iddetento'];?>" title="Clique aqui para abrir a qualificativa d<?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>"><?php echo $d_v_radio['nome_det'] ?></a></td>
                    <td align="center"><?php if ( !empty( $d_v_radio['matricula'] ) ) echo formata_num( $d_v_radio['matricula'] ) ?></td>
                    <td align="center"><?php echo $d_v_radio['raio_det'] ?> - <?php echo $d_v_radio['cela_det'] ?></td>
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

            <form action="<?php echo SICOP_ABS_PATH; ?>send/sendradio.php" method="post" name="cadradio" id="cadradio" >

                <table class="edit">
                    <tr >
                        <td width="70" height="20">Data:</td>
                        <td width="140"><input name="data_radio" type="text" class="CaixaTexto" id="data_radio" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" size="12" maxlength="10" /></td>
                    </tr>
                    <tr >
                        <td height="20">Marca:</td>
                        <td><input name="marca_radio" type="text" class="CaixaTexto" id="marca_radio" onBlur="upperMe(this); remacc(this); " onKeyPress="return blockChars(event, 4);" size="22" maxlength="20" /></td>
                    </tr>
                    <tr >
                        <td height="20">Cor:</td>
                        <td><input name="cor_radio" type="text" class="CaixaTexto" id="cor_radio" onBlur="upperMe(this); remacc(this); " onKeyPress="return blockChars(event, 4);" size="22" maxlength="20" /></td>
                    </tr>
                    <tr >
                        <td height="20">Faixas:</td>
                        <td><input name="faixas" type="text" class="CaixaTexto" id="faixas" onKeyPress="return blockChars(event, 2);" size="3" maxlength="2" /></td>
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

                    $( "#data_radio" ).focus();
                    $( "#data_radio" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });

                    $("form").submit(function() {
                        if ( validacadradio() == true ) {
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