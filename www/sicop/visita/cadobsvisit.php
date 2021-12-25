<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_rol   = get_session( 'n_rol', 'int' );
$n_rol_n = 3;

$targ = get_get( 'targ', 'int' );

$botao_canc  = 'history.go(-1)';
$botao_value = 'Cancelar';

if ( $targ == 1 ) {
    $botao_canc  = 'self.window.close()';
    $botao_value = 'Fechar';
}

$motivo_pag = 'CADASTRO DE OBSERVAÇÃO DE VISITANTE';

if ( $n_rol < $n_rol_n ) {

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

$idvisit = get_get( 'idvisit', 'int' );

if ( empty( $idvisit ) ) {

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

$queryvisit = "SELECT
                 `idvisita`,
                 `nome_visit`,
                 `rg_visit`
               FROM
                 `visitas`
               WHERE
                 `idvisita` = $idvisit
               LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$queryvisit = $model->query( $queryvisit );

// fechando a conexao
$model->closeConnection();

if( !$queryvisit ) {

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';
    echo msg_js( '', $ret );

    exit;

}

$contv = $queryvisit->num_rows;

if( $contv < 1 ) {

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( CADASTRAMENTO DE OBSERVAÇÃO - VISITANTES ).\n\n Página: $pag";
    salvaLog( $mensagem );

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';
    echo msg_js( '', $ret );

    exit;

}

$d_visit = $queryvisit->fetch_assoc();

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

            <p class="descript_page">NOVA OBSERVAÇÃO DE VISITANTE</p>

            <table width="500" class="lista_busca">
                <tr bgcolor="#FAFAFA">
                    <td width="113" height="20">Identificador (ID):</td>
                    <td width="372"><?php echo $d_visit['idvisita']; ?></td>
                </tr>
                <tr bgcolor="#FAFAFA">
                    <td height="20">Nome:</td>
                    <td><?php if ($targ == 0) { ?><a href="detalvisit.php?idvisit=<?php echo $d_visit['idvisita']; ?>" title="Clique aqui para abrir os detalhes deste visitante"><?php }; ?><?php echo $d_visit['nome_visit']; ?></a></td>
                </tr>
                <tr bgcolor="#FAFAFA">
                    <td height="20">R.G.:</td>
                    <td><?php echo $d_visit['rg_visit']; ?></td>
                </tr>
            </table>

            <p class="table_leg">Observação:</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendvisitobs.php" method="post" name="cadobsvisit" id="cadobsvisit">

                <div align="center">
                    <textarea name="obs_visit" id="obs_visit" cols="75" rows="5" class="CaixaTexto" onBlur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);"></textarea>
                </div>

                <p style="text-align: center; margin-top: 3px;">Destacar: <input name="destacar" type="checkbox" id="destacar" value="1" /></p>

                <input type="hidden" name="idvisit" id="idvisit" value="<?php echo $d_visit['idvisita']; ?>" />
                <input type="hidden" name="targ" id="targ" value="<?php echo $targ; ?>" />
                <input type="hidden" name="proced" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Cadastrar" />
                    <input class="form_bt" name="" type="button" onclick="<?php echo $botao_canc; ?>" value="<?php echo $botao_value; ?>" />
                </div>

            </form>

            <script type="text/javascript">

                id( 'obs_visit' ).focus();

                $(function() {
                    $("form").submit(function() {
                        if ( valida_obs( 'obs_visit' ) == true ) {
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