<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_admsist        = get_session( 'n_admsist', 'int' );
$nivel_necessario = 2;

$motivo_pag = 'USUÁRIOS ON LINE';

if ($n_admsist < $nivel_necessario) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$ordpor = 'nomea';

if ( !empty( $_GET['op'] ) ) {
    $ordpor = get_get( 'op', 'busca' );
}

switch ( $ordpor ) {
    default:
    case 'nomea':
        $ordbusca = '`sicop_users`.`nome_cham` ASC';
        break;
    case 'nomed':
        $ordbusca = '`sicop_users`.`nome_cham` DESC';
        break;
    case 'dataa':
        $ordbusca = '`visitas_online`.`hora` ASC';
        break;
    case 'datad':
        $ordbusca = '`visitas_online`.`hora` DESC';
        break;
}

$query_user = "SELECT
                 `visitas_online`.`ip`,
                 `visitas_online`.`url`,
                 `visitas_online`.`hora`,
                 DATE_FORMAT( `visitas_online`.`hora`, '%d/%m/%Y às %H:%i:%s' ) AS data_f,
                 `sicop_users`.`iduser`,
                 `sicop_users`.`nome_cham`
               FROM
                 `visitas_online`
                 INNER JOIN `sicop_users` ON `visitas_online`.`cod_user` = `sicop_users`.`iduser`
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

if($contu < 1) {
    $mensagem = "A consulta retornou 0 ocorrencias.\n\n Página $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Usuários on line';

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

            <p class="descript_page">USUÁRIOS ON LINE</p>

            <span class="fix_table">
                <table class="lista_busca">
                    <tr >
                        <th class="num_od">&nbsp;</th>
                        <th class="uo_user">USUÁRIO
                            <?php echo link_ord_asc( $ordpor, 'nome', '', 'nome do usuário' ) ?>
                            <?php echo link_ord_desc( $ordpor, 'nome', '', 'nome do usuário' ) ?>
                        </th>
                        <th class="desc_data_long">DATA / HORA

                            <?php echo link_ord_asc( $ordpor, 'data', '', 'nome do usuário' ) ?>
                            <?php echo link_ord_desc( $ordpor, 'data', '', 'nome do usuário' ) ?>
                        </th>
                        <th class="uo_view">ÚLTIMA VISUALIZAÇÃO</th>
                        <th class="uo_ip">I.P.</th>
                    </tr>
                    <?php
                    $i = 0;
                    while ( $d_user = $query_user->fetch_assoc() ) {
                        ?>
                    <tr class="even">
                        <td class="num_od"><?php echo++$i ?></td>
                        <td class="uo_user  <?php if ( stripos( $ordpor, 'nome' ) !== false ) echo 'ord';?>"><a href="<?php echo SICOP_ABS_PATH ?>user/user.php?iduser=<?php echo $d_user['iduser'] ?>"><?php echo $d_user['nome_cham'] ?></a></td>
                        <td class="desc_data_long  <?php if ( stripos( $ordpor, 'data' ) !== false ) echo 'ord';?>"><?php echo $d_user['data_f'] ?></td>
                        <td class="uo_view"><?php echo $d_user['url'] ?></td>
                        <td class="uo_ip"><?php echo $d_user['ip'] ?></td>
                    </tr>
                    <?php } // fim do while ?>
                </table>
            </span>

<?php include 'footer.php'; ?>