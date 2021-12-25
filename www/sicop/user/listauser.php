<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_admsist = get_session( 'n_admsist', 'int' );
$n_admsist_n = 2;

if ( $n_admsist < $n_admsist_n ) {
    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
    salvaLog( $mensagem );
    exit;
}

$ordpor = 'nomea';

if ( !empty( $_GET['op'] ) ) {
    $ordpor = $_GET['op'];
    $ordpor = tratabusca( $ordpor );
}

$ordbusca = '`nomeuser` ASC';

switch ( $ordpor ) {
    default:
    case 'nomea':
        $ordbusca = '`nomeuser` ASC';
        break;
    case 'nomed':
        $ordbusca = '`nomeuser` DESC';
        break;
    case 'ida':
        $ordbusca = '`iduser` ASC';
        break;
    case 'idd':
        $ordbusca = '`iduser` DESC';
        break;
}

$query_user = "SELECT
                 `iduser`,
                 `nomeuser`,
                 `ativo`
               FROM
                 `sicop_users`
               ORDER BY
                 $ordbusca";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_user = $model->query( $query_user );

// fechando a conexao
$model->closeConnection();

if( !$query_user ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$contu = $query_user->num_rows;

if ( $contu < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrencias ( LISTA DE USUÁRIOS ).\n\n Página $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Lista de usuários';

// adicionando o javascript
$cab_js = 'ajax/ajax_user.js';
set_cab_js( $cab_js );

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 2 );
$trail->output();
?>

            <p class="descript_page">LISTA DE USUÁRIOS</p>
            <p class="table_leg"> Total de <?php echo $contu ?> usuários cadastrados</p>
            <?php if ( $n_admsist >= 3 ) { ?>
            <p class="link_common"><a href="cadastrauser.php">Cadastrar usuário</a></p>
            <?php } ?>

            <table class="lista_busca">
                <tr >
                    <td class="num_od">&nbsp;</td>
                    <td height="20" align="center">NOME DO USUÁRIO
                        <?php if ( $ordpor == 'nomea' ) { ?>
                            <img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_asc_m.png" alt="Ordenado por nome crescente" width="11" height="9" />
                        <?php } else { ?>
                            <a href="?op=nomea" title="Ordenar por nome crescente" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_asc.png" alt="Ordenar por nome crescente" width="11" height="9" /></a>
                        <?php }; ?>
                        <?php if ( $ordpor == 'nomed' ) { ?>
                            <img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_desc_m.png" alt="Ordenado por nome decrescente" width="11" height="9" />
                        <?php } else { ?>
                            <a href="?op=nomed" title="Ordenar por nome decrescente" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_desc.png" alt="Ordenar por nome decrescente" width="11" height="9" /></a>
                        <?php }; ?>
                    </td>
                    <td align="center" >ID
                        <?php if ( $ordpor == 'ida' ) { ?>
                            <img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_asc_m.png" alt="Ordenado por ID crescente" width="11" height="9" />
                        <?php } else { ?>
                            <a href="?op=ida" title="Ordenar por ID crescente" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_asc.png" alt="Ordenar por ID crescente" width="11" height="9" /></a>
                        <?php }; ?>
                        <?php if ( $ordpor == 'idd' ) { ?>
                            <img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_desc_m.png" alt="Ordenado por ID decrescente" width="11" height="9" />
                        <?php } else { ?>
                            <a href="?op=idd" title="Ordenar por ID decrescente" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_desc.png" alt="Ordenar por ID decrescente" width="11" height="9" /></a>
                        <?php }; ?>
                    </td>
                    <td class="tb_bt">&nbsp;</td>
                    <td class="tb_bt">&nbsp;</td>
                </tr>
                <?php
                $i = 0;
                while ( $d_user = $query_user->fetch_assoc() ) {

                    $corfontv = '#000000';

                    if ( $d_user['ativo'] != 1 ) { //usuário inativo
                        $corfontv = '#FF0000';
                    }
                    ?>
                    <tr class="even">
                        <td class="num_od"><?php echo++$i ?></td>
                        <td width="312" height="20"><a href="user.php?iduser=<?php echo $d_user['iduser'] ?>"><?php echo $d_user['nomeuser'] ?></a></td>
                        <td width="60" align="center" ><font color="<?php echo $corfontv; ?>"><?php echo $d_user['iduser'] ?></font></td>
                        <td class="tb_bt"><?php if ( $n_admsist >= 3 ) { ?><a href="edituser.php?iduser=<?php echo $d_user['iduser']; ?>" title="Alterar dados deste usuário" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="" /></a><?php }; ?></td>
                        <td class="tb_bt">
                        <?php if ( $n_admsist >= 4 and $d_user['iduser'] != 1 ) { ?>
                            <input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" name="link_drop_user_arr[]" value="<?php echo $d_user['iduser']; ?>" title="Excluir este usuário" />
                        <?php }; ?>
                        </td>
                    </tr>
                    <?php } // fim do while ?>
            </table>

<?php include 'footer.php';?>