<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_pront   = get_session( 'n_pront', 'int' );
$n_pront_n = 2;

$motivo_pag = 'INFOPEN';

if ( $n_pront < $n_pront_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$desc_pag = 'INFOPEN - Prontuário';


require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) {
    $pag_atual .= '?' . $qs;
}

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>

            <p class="descript_page">INFOPEN - PRONTUÁRIO</p>

            <table width="492" style="margin: 0 auto;">
                <tr>
                    <td width="240" valign="top">
                        <ul id="menu_infop">
                            <li class="sub"><div>Artigo</div></li>
                            <li><a href="infop_art.php" title="<?php echo SICOP_DET_DESC_FU; ?>s pelo artigo" ><?php echo SICOP_DET_DESC_FU; ?>s pelo artigo</a></li>
                            <li><a href="lista_infop.php?tipo_infop=part" title="Pendências de artigo" >Pendências de artigo</a></li>
                            <li class="sub"><div>Raça</div></li>
                            <li><a href="infop_raca.php" title="<?php echo SICOP_DET_DESC_FU; ?>s pela raça" ><?php echo SICOP_DET_DESC_FU; ?>s pela raça</a></li>
                            <li><a href="lista_infop.php?tipo_infop=praca" title="Pendências de raça" >Pendências de raça</a></li>
                            <li class="sub"><div>Regime de Prisão</div></li>
                            <li><a href="infop_reg.php" title="<?php echo SICOP_DET_DESC_FU; ?>s pelo regime de prisão" ><?php echo SICOP_DET_DESC_FU; ?>s pelo regime de prisão</a></li>
                            <li><a href="lista_infop.php?tipo_infop=preg" title="Pendências de regime de prisão" >Pendências de regime de prisão</a></li>
                            <li class="sub"><div>Idade</div></li>
                            <li><a href="infop_idade.php" title="<?php echo SICOP_DET_DESC_FU; ?>s pela idade" ><?php echo SICOP_DET_DESC_FU; ?>s pela idade</a></li>
                            <li><a href="lista_infop.php?tipo_infop=pidade" title="Pendências de idade" >Pendências de idade</a></li>
                            <li class="sub"><div>Nacionalidade</div></li>
                            <li><a href="infop_nac.php" title="<?php echo SICOP_DET_DESC_FU; ?>s pela nacionalidade" ><?php echo SICOP_DET_DESC_FU; ?>s pela nacionalidade</a></li>
                            <li><a href="lista_infop.php?tipo_infop=pnac" title="Pendências de nacionalidade" >Pendências de nacionalidade</a></li>
                        </ul>
                    </td>
                    <td width="240" valign="top">
                        <ul id="menu_infop">
                            <li class="sub"><div>Escolaridade</div></li>
                            <li><a href="infop_esc.php" title="<?php echo SICOP_DET_DESC_FU; ?>s pela escolaridade" ><?php echo SICOP_DET_DESC_FU; ?>s pela escolaridade</a></li>
                            <li><a href="lista_infop.php?tipo_infop=pesc" title="Pendências de escolaridade" >Pendências de escolaridade</a></li>
                            <li class="sub"><div>Condenação</div></li>
                            <li><a href="infop_cond.php" title="<?php echo SICOP_DET_DESC_FU; ?>s pela condenação" ><?php echo SICOP_DET_DESC_FU; ?>s pela condenação</a></li>
                            <li class="sub"><div>Procedência</div></li>
                            <li><a href="infop_proced.php" title="<?php echo SICOP_DET_DESC_FU; ?>s pela procedência" ><?php echo SICOP_DET_DESC_FU; ?>s pela procedência</a></li>
                            <li class="sub"><div>Movimentações</div></li>
                            <li><a href="<?php echo SICOP_ABS_PATH ?>cont_mov.php" title="Movimentações por data" >Movimentações por data</a></li>
                        </ul>
                    </td>
                </tr>
            </table>

<?php include 'footer.php'; ?>