<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_det_alias = get_session( 'n_det_alias', 'int' );
$n_alias_n   = 1;

$targ = get_get( 'targ', 'int' );

$botao_canc = 'history.go(-1)';
$botao_value = 'Cancelar';

if ( $targ == 1 ) {
    $botao_canc = 'self.window.close()';
    $botao_value = 'Fechar';
}

$noreload = get_get( 'noreload', 'int' );

$motivo_pag = 'CADASTRAMENTO DE ALIAS DE ' . SICOP_DET_DESC_U;

if ( $n_det_alias < $n_alias_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', $ret );

    exit;

}

$iddet = get_get( 'iddet', 'int' );

if ( empty( $iddet ) ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';
    echo msg_js( '', $ret );

    exit;

}

$queryalias = "SELECT
                  `aliases`.`idalias`,
                  `aliases`.`cod_detento`,
                  `aliases`.`alias_det`,
                  DATE_FORMAT( `aliases`.`data_add`, '%d/%m/%Y' ) AS data_add_f,
                  `tipoalias`.`tipoalias`
                FROM
                  `aliases`
                  INNER JOIN tipoalias ON `aliases`.`cod_tipoalias` = `tipoalias`.`idtipoalias`
                WHERE
                  `aliases`.`cod_detento` = $iddet
                ORDER BY
                  `tipoalias`.`tipoalias` ASC";

$q_tipo_alias = 'SELECT `idtipoalias`,`tipoalias` FROM `tipoalias` ORDER BY `tipoalias` ASC';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_tipo_alias = $model->query( $q_tipo_alias );

// fechando a conexao
$model->closeConnection();

if( !$q_tipo_alias ) {

    echo msg_js( '', 1 );
    exit;

}

$cont_tipo_alias = $q_tipo_alias->num_rows;

if( $cont_tipo_alias < 1 ) {

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( $motivo_pag ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( '', 1 );
    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Cadastrar alias';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

if ( $targ == 1 ) {
    require 'cab_simp.php';
} else {
    require 'cab.php';
    $pag_atual = $_SERVER['PHP_SELF'];
    $qs = $_SERVER['QUERY_STRING'];

    if ( !empty( $qs ) ) {
        $pag_atual .= '?' . $qs;
    }
    $trail = new Breadcrumb();
    $trail->add( $desc_pag, $pag_atual, 5 );
    $trail->output();
}

?>

            <p class="descript_page">NOVO ALIAS</p>

            <?php include 'quali/det_basic.php'; ?>

            <form action="<?php echo SICOP_ABS_PATH ?>send/senddetalias.php" method="post" name="cadaliasdet" id="cadaliasdet">

                <table class="edit">
                    <tr >
                        <td width="90">Tipo de alias:</td>
                        <td width="325">
                            <select name="tipoalias" class="CaixaTexto" id="tipoalias">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while( $d_tipo_alias = $q_tipo_alias->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_tipo_alias['idtipoalias'];?>"><?php echo $d_tipo_alias['tipoalias'];?></option>
                                <?php };?>
                            </select>
                            <script type="text/javascript">id("tipoalias").focus();</script>
                        </td>
                    </tr>
                    <tr >
                        <td>Alias:</td>
                        <td><textarea name="alias_det" id="alias_det" cols="60" rows="3" class="CaixaTexto" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);" onkeydown="textCounter(this, 245);" onkeyup="textCounter(this, 245);"></textarea></td>
                    </tr>
                </table><!-- fim da <table class="edit"> -->

                <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet; ?>" />
                <input type="hidden" name="targ" id="targ" value="<?php echo $targ;?>" />
                <input type="hidden" name="noreload" id="noreload" value="<?php echo $noreload;?>" />
                <input type="hidden" name="proced" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" onclick="return validacadaliasdet();" value="Cadastrar" />
                    <input class="form_bt" type="submit" name="cadadd" id="submit" onclick="return validacadaliasdet();" value="Cadastrar e adicionar outro" />
                    <input class="form_bt" type="button" name="" onclick="<?php echo $botao_canc ?>" value="<?php echo $botao_value ?>" />
                </div>

            </form><!-- fim do <form ... > -->

            <div class="linha_alias">
                ALIASES CADASTRADOS
                <hr />
            </div>

            <?php
            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $queryalias = $model->query( $queryalias );

            // fechando a conexao
            $model->closeConnection();

            $contali = $queryalias->num_rows;
            if( $contali < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não há aliases cadastrados.</p>';
            } else {
                ?>

            <table class="lista_busca">
                <tr class="cab">
                    <th class="desc_data">DATA</th>
                    <th class="tipo_alias">TIPO DE ALIAS</th>
                    <th class="desc_alias">ALIAS</th>
                </tr>

                <?php while( $d_alias = $queryalias->fetch_assoc() ) { ?>

                <tr class="even">
                    <td class="desc_data"><?php echo $d_alias['data_add_f'] ?></td>
                    <td class="tipo_alias"><?php echo $d_alias['tipoalias'] ?></td>
                    <td class="desc_alias"><?php echo nl2br( $d_alias['alias_det'] ) ?></td>
                </tr>

                <?php } // fim do while ?>

            </table><!-- fim da <table class="lista_busca"> -->

            <?php } // fim do if que conta o número de ocorrencias ?>

<?php include 'footer.php'?>