<?php
if ( !isset( $_SESSION ) ) session_start();

/*ob_start("ob_gzhandler");*/

require '../init/config.php';
require 'incl_complete.php';

$pag      = link_pag();
$tipo     = '';
$tipo_pag = 'BUSCA DE NÚMEROS DE OFÍCIO';

$nivel_necessario = 3;
$n_pec            = get_session( 'n_pec', 'int' );
$iduser           = get_session( 'user_id', 'int' );

if ( $n_pec < $nivel_necessario ) {

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

$q_numreq = "SELECT `idnumreq`, `numero_req`, `ano`, `nome_cham`, `sigla_setor`, `coment`, DATE_FORMAT(`dataadd`, '%d/%m/%Y às %H:%i') AS `dataadd`
            FROM `numeroreq`
            LEFT JOIN `sicop_setor` ON `numeroreq`.`idsetor` = `sicop_setor`.`idsetor`
            LEFT JOIN `sicop_users` ON `numeroreq`.`iduser` = `sicop_users`.`iduser`
            WHERE `numeroreq`.`iduser` = $iduser
            ORDER BY `ano` DESC, `numero_req` DESC";

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Listar números';

require 'cab.php';
$trail = new Breadcrumb();
$trail->add( $desc_pag, $_SERVER['PHP_SELF'], 3 );
$trail->output();
?>

            <p class="descript_page">NÚMERO(S) PARA REQUISIÇÃO DE PASSAGENS SOLICITADOS</p>

            <?php

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $q_numreq = $model->query( $q_numreq );

            // fechando a conexao
            $model->closeConnection();

            $cont_req = $q_numreq->num_rows;
            if ( $cont_req < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
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
                <?php while ( $d_numreq = $q_numreq->fetch_assoc() ) { ?>
                <tr class="even">
                    <td class="n_ano"><?php echo $d_numreq['numero_req'] . '/' . $d_numreq['ano']; ?></td>
                    <td class="user_log"><?php echo $d_numreq['nome_cham']; ?></td>
                    <td class="setor_num"><?php echo $d_numreq['sigla_setor']; ?></td>
                    <td class="desc_num"><?php echo nl2br($d_numreq['coment']); ?></td>
                    <td class="desc_data_long"><?php echo $d_numreq['dataadd']; ?></td>
                </tr>
                      <?php }?>
            </table>
                <?php }?>

<?php include 'footer.php'; ?>

