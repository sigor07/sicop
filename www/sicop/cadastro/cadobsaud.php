<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_cad_n    = 3;

$targ = get_get( 'targ', 'int' );

$botao_canc  = 'history.go(-1)';
$botao_value = 'Cancelar';

if ( $targ == 1 ) {
    $botao_canc  = 'self.window.close()';
    $botao_value = 'Fechar';
}

$motivo_pag = 'CADASTRO DE OBSERVAÇÃO DE AUDIÊNCIA';

if ( $n_cadastro < $n_cad_n ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    $ret = 1;
    if ( $targ == 1 ) $ret = 'f';

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', $ret );

    exit;

}

$idaud = get_get( 'idaud', 'int' );

if ( empty( $idaud ) ){

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
                `motivo_justi`
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

if( !$query_aud ) {

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';
    echo msg_js( '', $ret );

    exit;

}

$cont_aud = $query_aud->num_rows;

if( $cont_aud < 1 ) {

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( CADASTRAMENTO DE OBSERVAÇÃO - AUDIÊNCIAS ).\n\n Página: $pag";
    salvaLog( $mensagem );

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';
    echo msg_js( '', $ret );

    exit;

}

$d_aud = $query_aud->fetch_assoc();

$iddet = $d_aud['cod_detento'];

$aud = trata_sit_aud( $d_aud['sit_aud'] );

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Cadastrar observação';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

if ( $targ == 1 ){

    require 'cab_simp.php';

} else {

    require 'cab.php';
    $pag_atual = $_SERVER['PHP_SELF'];
    $qs = $_SERVER['QUERY_STRING'];

    if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

    $trail = new Breadcrumb();
    $trail->add( $desc_pag, $pag_atual, 5 );
    $trail->output();

}
?>

            <p class="descript_page">NOVA OBERVAÇÃO DE AUDIÊNCIA</p>

            <p class="table_leg">Audiência</p>

            <table class="lista_busca">
              <tr bgcolor="#FAFAFA">
                    <td width="292" height="20" >Data/Hora: <?php echo $d_aud['data_aud_f']?> às <?php echo $d_aud['hora_aud_f']?></td>
                    <td width="293" >Tipo de apresentação: <?php echo trata_tipo_aud($d_aud['tipo_aud'])?></td>
                </tr>
                <?php
                $local = 'Local:';
                $process = 'Número do processo:';

                if ($d_aud['tipo_aud'] == 4 || $d_aud['tipo_aud'] == 6){
                    $local = 'Para realizar:';
                } else if ($d_aud['tipo_aud'] == 5 ){
                    $process = 'Número do inquérito:';
                } else if ($d_aud['tipo_aud'] == 7 ){
                    $local = 'A fim de ser:';
                }

                ?>
                <tr bgcolor="#FAFAFA">
                    <td height="20" colspan="2" ><?php echo $local; ?> <?php echo $d_aud['local_aud']?></td>
                </tr>
                <tr bgcolor="#FAFAFA">
                    <td height="20" colspan="2" >Cidade: <?php echo $d_aud['cidade_aud']?></td>
                </tr>
                <?php if ($d_aud['tipo_aud'] == 1 || $d_aud['tipo_aud'] == 4 || $d_aud['tipo_aud'] == 5 || $d_aud['tipo_aud'] == 7){?>
                <tr bgcolor="#FAFAFA">
                    <td height="20" colspan="2" ><?php echo $process; ?> <?php echo $d_aud['num_processo']?></td>
                </tr>
                <?php }?>
                <tr bgcolor="#FAFAFA">
                    <td height="20" colspan="2" >Situação da audiência: <b><font color="<?php echo $aud['corfontaud']; ?>"><?php echo $aud['sitaud']; ?></font></b></td>
                </tr>
                <?php if ($d_aud['sit_aud'] != 11){?>
                <tr bgcolor="#FAFAFA">
                    <td height="20" colspan="2" >Motivo: <?php echo $d_aud['motivo_justi'] ?></td>
                </tr>
                   <?php }?>
            </table>

            <?php include 'quali/det_basic.php'; ?>


            <form action="<?php echo SICOP_ABS_PATH ?>send/sendaudobs.php" method="post" name="cadobsaud" id="cadobsaud">

                <p class="table_leg">Observações:</p>

                <p align="center">
                    <textarea name="obs_aud" id="obs_aud" cols="75" rows="5" class="CaixaTexto" onBlur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 1);"></textarea>
                </p>

                <input type="hidden" name="idaud" id="idaud" value="<?php echo $d_aud['idaudiencia'];?>" />
                <input type="hidden" name="targ" id="targ" value="<?php echo $targ;?>" />
                <input type="hidden" name="proced" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Cadastrar" />
                    <input class="form_bt" name="" type="button" onclick="<?php echo $botao_canc; ?>" value="<?php echo $botao_value;?>" />
                </div>

            </form>

            <script type="text/javascript">id( 'obs_aud' ).focus();</script>
            <script type="text/javascript">

                $(function() {
                    $("form").submit(function() {
                        if ( valida_obs( 'obs_aud' ) == true ) {
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

<?php include 'footer.php'; ?>