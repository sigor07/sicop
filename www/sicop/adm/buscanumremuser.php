<?php
if ( !isset( $_SESSION ) ) session_start();

/*ob_start("ob_gzhandler");*/

require '../init/config.php';
require 'incl_complete.php';

$pag      = link_pag();
$tipo     = '';
$tipo_pag = 'BUSCA DE NÚMEROS DE OFÍCIO';

$nivel_necessario = 3;
$n_adm            = get_session( 'n_adm', 'int' );
$iduser           = get_session( 'user_id', 'int' );

if ( $n_adm < $nivel_necessario ) {

    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $tipo_pag;
    get_msg( $msg, 1 );

    exit;

}

$q_numrms = "SELECT `idnumrms`, `numero_rms`, `ano`, `nome_cham`, `sigla_setor`, `coment`, DATE_FORMAT(`dataadd`, '%d/%m/%Y às %H:%i') AS `dataadd`
            FROM `numerorms`
            LEFT JOIN `sicop_setor` ON `numerorms`.`idsetor` = `sicop_setor`.`idsetor`
            LEFT JOIN `sicop_users` ON `numerorms`.`iduser` = `sicop_users`.`iduser`
            WHERE `numerorms`.`iduser` = $iduser
            ORDER BY `ano` DESC, `numero_rms` DESC";

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Listar números';

require 'cab.php';
$trail = new Breadcrumb();
$trail->add( $desc_pag, $_SERVER['PHP_SELF'], 3 );
$trail->output();
?>

            <p class="descript_page">NÚMERO(S) PARA REMESSA(S) SOLICITADOS</p>

            <?php
            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $q_numrms = $model->query( $q_numrms );

            // fechando a conexao
            $model->closeConnection();

            $cont_rms = $q_numrms->num_rows;
            if ( $cont_rms < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Nenhum número.</p>';
            } else {
            ?>
            <table class="lista_busca">
                <tr>
                    <th class="n_ano">Número / ano</th>
                    <th class="user_log">Usuário</th>
                    <th class="setor_num">Setor</th>
                    <th class="desc_num">Descrição</th>
                    <th class="desc_data_long">Data / hora</th>
                </tr>
                <?php while ( $d_numrms = $q_numrms->fetch_assoc() ) { ?>
                <tr class="even">
                    <td class="n_ano"><?php echo $d_numrms['numero_rms'] . '/' . $d_numrms['ano']; ?></td>
                    <td class="user_log"><?php echo $d_numrms['nome_cham']; ?></td>
                    <td class="setor_num"><?php echo $d_numrms['sigla_setor']; ?></td>
                    <td class="desc_num"><?php echo nl2br($d_numrms['coment']); ?></td>
                    <td class="desc_data_long"><?php echo $d_numrms['dataadd']; ?></td>
                </tr>
                      <?php }?>
            </table>
                <?php }?>

<?php include 'footer.php'; ?>

