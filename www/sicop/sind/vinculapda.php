<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_sind   = get_session( 'n_sind', 'int' );
$n_sind_n = 3;

if ( $n_sind < $n_sind_n ) {
    require ('/cab_simp.php');
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
    salvaLog( $mensagem );
    exit;
}

$idsind = '';
if ( isset( $_SESSION['pda_id'] ) ) {
    $idsind = (int)$_SESSION['pda_id'];
}

if ( empty( $idsind ) ) {
    header('Location: ../sindicancia.php');
    exit;
}

$query_sind = "SELECT
                 `idsind`,
                 `cod_detento`,
                 `num_pda`,
                 `ano_pda`,
                 `local_pda`,
                 `sit_pda`,
                 `data_reabilit`
               FROM
                 `sindicancias`
               WHERE
                 `idsind` = $idsind
               LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_sind = $model->query( $query_sind );

// fechando a conexao
$model->closeConnection();

$d_pda = $query_sind->fetch_assoc();

if ( !empty( $d_pda['cod_detento'] ) ) {

    // pegar os dados do preso
    $detento = dados_det( $d_pda['cod_detento'] );

    // pegar os dados do PDA
    $pda = dados_pda( $idsind );

    require 'cab_simp.php';
    ?>
    <p class="p_q_no_result">Este PDA já esta atribuido a <?php echo SICOP_DET_DESC_L; ?>.</p>
    <script type="text/javascript">setTimeout("history.go(-1)", 3000)</script>
    <?php

    include 'footer.php';

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Tentativa de vinculação de PDA já atribuido a " . SICOP_DET_DESC_L . ". \n\n $pda \n\n $detento \n\n Página: $pag";
    salvaLog( $mensagem );

    exit;

}

$numpda = format_num_pda( $d_pda['num_pda'], $d_pda['ano_pda'], $d_pda['local_pda'] );

$corfonts = muda_cor_pda( $d_pda['data_reabilit'], $d_pda['sit_pda'] );

$matr = '';
if ( isset( $_POST['matricula'] ) ) {
    $matr = tratabusca( $_POST['matricula'] );
    $matr = (int)$matr;
}

$cont = '';
$iddet = '';
if ( !empty( $matr ) ) {

    $query_det = "SELECT `iddetento` FROM `detentos` WHERE `matricula` = $matr LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    (int)$iddet = $model->fetchOne( $query_det );

    // fechando a conexao
    $model->closeConnection();

}

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Vincular PDA';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 6 );
$trail->output();
?>

            <p class="descript_page">VINCULAR PDA DE AUTORIA DESCONHECIDA</p>

            <p class="table_leg">PDA</p>

            <div class="detal_var" style="width: 300px;">
                <font color="<?php echo $corfonts; ?>"><?php echo $numpda ?></font>
            </div>

            <?php if ( empty( $matr ) ) { ?>
                <p class="sub_title_page">PESQUISAR <?php echo SICOP_DET_DESC_U; ?></p>

                <form action="vinculapda.php" method="post" name="vinculapda" id="vinculapda">

                    <p class="table_leg">Digite a MATRÍCULA d<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?> com o DÍGITO:</p>

                    <div class="form_one_field">
                        <input name="matricula" type="text" class="CaixaTexto" id="matricula" onkeypress="return blockChars(event, 5);" size="11" maxlength="9" />
                    </div>

                    <div class="form_bts">
                        <input class="form_bt" type="submit" name="submit" id="busca" onClick="return validavinculapda();" value="Buscar" />
                    </div>

                    <p class="link_common" style="margin-top: 5px;"><a href="#" onClick="javascript: ow('../buscadetm.php', '800', '600'); return false" >Não lembro a matrícula/digito</a></p>

                </form>

                <script type="text/javascript">id( 'matricula' ).focus();</script>
            <?php
            } else {

                if ( empty( $cont ) or $cont < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
            ?>
                    <p class="p_q_no_result">Não foi encontrado.</p>
                    <p class="link_common"><a href="vinculapda.php">Nova consulta</a></p>
            <?php

                } else {
            ?>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendpda.php" method="post" name="vinculapda" id="vinculapda">

                <?php include 'quali/det_basic.php'; ?>

                <p class="confirm_ask">Tem certeza de que deseja vincular o PDA a <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>?</p>

                <p style="text-align: center">
                    <img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_attention.png" alt="Atenção" class="icon_alert" />&nbsp;ATENÇÃO -> Você <b>não poderá</b> desfazer essa operação.
                </p>

                <input type="hidden" name="id_pda" id="id_pda" value="<?php echo $d_pda['idsind']; ?>" />
                <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet ?>" />
                <input type="hidden" name="proced" id="proced" value="4" />

                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" value="Vincular" />
                    <input class="form_bt" name="" type="button" onclick="javascript: location.href='detalpda.php?idsind=<?php echo $d_pda['idsind'] ?>'" value="Cancelar" />
                </div>

            </form>

            <?php
                }
            }
            ?>
<?php include 'footer.php'; ?>