<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_cad_n    = 3;

if ( $n_cadastro < $n_cad_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'ALTERAÇÃO DE OBSERVAÇÃO - AUDIÊNCIA';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idobs = get_get( 'idobs', 'int' );

if ( empty( $idobs ) ){
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página de alteração de observação de audiência.\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( '', 1 );
    exit;
}

$query_obs = "SELECT
                `id_obs_aud`,
                `cod_audiencia`,
                `obs_aud`
              FROM
                `obs_aud`
              WHERE
                `id_obs_aud` = $idobs
              LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_obs = $model->query( $query_obs );

// fechando a conexao
$model->closeConnection();

if( !$query_obs ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_obs = $query_obs->num_rows;

if( $cont_obs < 1 ) {

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta da observação retornou 0 ocorrências ( ALTERAÇÃO DE OBSERVAÇÃO - AUDIÊNCIAS ).\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 1 );
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
                `idaudiencia` = (SELECT `cod_audiencia` FROM `obs_aud` WHERE `id_obs_aud` = $idobs LIMIT 1)
              LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_aud = $model->query( $query_aud );

// fechando a conexao
$model->closeConnection();

if( !$query_aud ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_aud = $query_aud->num_rows;

if( $cont_aud < 1 ) {

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta da audiência retornou 0 ocorrências ( ALTERAÇÃO DE OBSERVAÇÃO - AUDIÊNCIAS ).\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_aud = $query_aud->fetch_assoc();

$iddet = $d_aud['cod_detento'];

$aud = trata_sit_aud( $d_aud['sit_aud'] );

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Alterar observação';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 5 );
$trail->output();
?>

            <script type="text/javascript">
                var KEY_TAB = 9;

                function processTab () {
                    if ( window.event.keyCode == KEY_TAB )
                    {
                        var s = document.selection;
                        var tr = s.createRange();

                        if ( tr != null )
                        // escolha o comportamento da tecla "tab"
                        // entre a definição do tab
                        // ou um conjunto de caracteres em branco , para um tab maior .
                            tr.text = "\t";
                        //tr.text = "   ";

                        window.event.returnValue=false;
                    }
                }
            </script>


            <p class="descript_page">ALTERAR OBSERVAÇÃO DE AUDIÊNCIAS</p>

            <p class="table_leg">Audiência</p>

            <table class="lista_busca">
                <tr bgcolor="#FAFAFA">
                    <td width="292" height="20" >Data/Hora: <?php echo $d_aud['data_aud_f'] ?> às <?php echo $d_aud['hora_aud_f'] ?></td>
                    <td width="293" >Tipo de apresentação: <?php echo trata_tipo_aud($d_aud['tipo_aud']) ?></td>
                </tr>
                <?php
                $local = 'Local:';
                $process = 'Número do processo:';

                if ($d_aud['tipo_aud'] == 4 || $d_aud['tipo_aud'] == 6) {
                    $local = 'Para realizar:';
                } else if ($d_aud['tipo_aud'] == 5) {
                    $process = 'Número do inquérito:';
                } else if ($d_aud['tipo_aud'] == 7) {
                    $local = 'A fim de ser:';
                }
                ?>
                <tr bgcolor="#FAFAFA">
                    <td height="20" colspan="2" ><?php echo $local; ?> <?php echo $d_aud['local_aud'] ?></td>
                </tr>
                <tr bgcolor="#FAFAFA">
                    <td height="20" colspan="2" >Cidade: <?php echo $d_aud['cidade_aud'] ?></td>
                </tr>
                <?php if ($d_aud['tipo_aud'] == 1 || $d_aud['tipo_aud'] == 4 || $d_aud['tipo_aud'] == 5 || $d_aud['tipo_aud'] == 7) { ?>
                <tr bgcolor="#FAFAFA">
                    <td height="20" colspan="2" ><?php echo $process; ?> <?php echo $d_aud['num_processo'] ?></td>
                </tr>
                <?php } ?>
                <tr bgcolor="#FAFAFA">
                    <td height="20" colspan="2" >Situação da audiência: <b><font color="<?php echo $aud['corfontaud']; ?>"><?php echo $aud['sitaud']; ?></font></b></td>
                </tr>
                <?php if ($d_aud['sit_aud'] != 11) { ?>
                <tr bgcolor="#FAFAFA">
                    <td height="20" colspan="2" >Motivo: <?php echo $d_aud['motivo_justi'] ?></td>
                </tr>
                <?php } ?>
            </table>

            <?php include 'quali/det_basic.php'; ?>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendaudobs.php" method="post" name="cadobsaud" id="cadobsaud">

                <p class="table_leg">Observações:</p>

                <?php $dados_obs = $query_obs->fetch_assoc(); ?>
                <p align="center">
                    <textarea name="obs_aud" id="obs_aud" cols="75" rows="5" class="CaixaTexto" onBlur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 1);" onkeydown="processTab()"><?php echo $dados_obs['obs_aud']; ?></textarea>
                </p>

                <input type="hidden" name="id_obs_aud" id="id_obs_aud" value="<?php echo $dados_obs['id_obs_aud']; ?>" />
                <input type="hidden" name="proced" id="proced" value="1" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Alterar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
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


