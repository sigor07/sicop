<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$idlt = get_get( 'idlt', 'int' );

if ( empty( $idlt ) ){
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página de detalhes do protocolo.\n\nPágina: $pag";
    salvaLog($mensagem);
    echo msg_js( '', 1 );
    exit;
}

$n_admsist = get_session( 'n_admsist', 'int' );

$q_listatel = "SELECT
                 `idlistatel`,
                 `tel_local` ,
                 `tel_end`,
                 `tel_cep`,
                 `tel_codmin`,
                 `tel_diretor`,
                 `user_add`,
                 DATE_FORMAT(`data_add`, '%d/%m/%Y às %H:%i') AS data_add,
                 `user_up`,
                 DATE_FORMAT(`data_up`, '%d/%m/%Y às %H:%i') AS data_up
               FROM
                 `listatel`
               WHERE
                 `idlistatel` = $idlt
               LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_listatel = $model->query( $q_listatel );

// fechando a conexao
$model->closeConnection();

if( !$q_listatel ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_lt = $q_listatel->num_rows;

if ( $cont_lt < 1 ) {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( DETALHES DA LOCALIDADE - LISTA DE TELEFONES ).\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$d_lt = $q_listatel->fetch_assoc();

$cep_f = '';
if ( !empty( $d_lt['tel_cep'] ) ) {

    $formata_cep = new FormataString();
    $cep_f = $formata_cep->getCEP( $d_lt['tel_cep'] );

}

$user_add = '';
$user_up = '';

if ( !empty( $d_lt['user_add'] ) ){
    $user_add = 'ID usuário: ' . $d_lt['user_add'] . ', em ' . $d_lt['data_add'];
};

if ( !empty( $d_lt['user_up'] ) ){
    $user_up = 'ID usuário: ' . $d_lt['user_up'] . ', em ' . $d_lt['data_up'];
};

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Detalhes da localidade';

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) {
    $pag_atual .=  '?' . $qs;
}

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>

            <p class="descript_page"> DETALHES DA LOCALIDADE </p>

            <?php if ( $n_admsist >= 4 ) { ?>
            <p class="link_common">
                <a href='cad_local_tel.php' title="Cadastrar nova localidade">Cadastrar</a> |
                <a href='edit_local_tel.php?idlt=<?php echo $idlt; ?>' title="Alterar os dados desta localidade">Alterar</a> |
                <a href='javascript:void(0)' onclick='drop_local_tel( <?php echo $idlt; ?> )' title="Excluir esta localidade">Excluir</a>
            </p>
            <?php } ?>

            <table class="detal_lt">
                <tr>
                    <td colspan="2" class="local_lt_maior">Localidade: <?php echo $d_lt['tel_local']; ?></td>
                </tr>
                <tr>
                    <td colspan="2" class="local_lt_maior">Endereço: <?php echo $d_lt['tel_end']; ?><?php if ( !empty( $cep_f ) ) { ?> - CEP: <?php echo $cep_f; ?><?php } ?></td>
                </tr>
                <tr>
                    <td class="local_lt_menor">Código minemônico: <?php echo $d_lt['tel_codmin']; ?></td>
                    <td class="local_lt_menor">Diretor: <?php echo $d_lt['tel_diretor']; ?></td>
                </tr>
                <tr>
                    <td class="user_cab">CADASTRAMENTO</td>
                    <td class="user_cab">ÚLTIMA ATUALIZAÇÃO</td>
                </tr>
                <tr>
                    <td class="user_fild"><?php echo $user_add ?></td>
                    <td class="user_fild"><?php echo $user_up; ?></td>
                </tr>
            </table><!-- fim da <table class="detal_lt"> -->


            <div class="linha_tel">
                TELEFONES <?php if ( $n_admsist >= 3 ) {  ?> - <a href="cad_num_tel.php?idlt=<?php echo $idlt ?>">Adicionar número</a><?php }; ?>
              <hr />
            </div>

            <?php

                $q_num_tel = "SELECT
                                `idlistatel_num`,
                                `cod_listatel`,
                                `ltn_num`,
                                `ltn_ramal`,
                                `ltn_desc`
                              FROM
                                `listatel_num`
                              WHERE
                                `cod_listatel` = $idlt";

                // instanciando o model
                $model = SicopModel::getInstance();

                // executando a query
                $q_num_tel = $model->query( $q_num_tel );

                // fechando a conexao
                $model->closeConnection();

                $cont_n_lt = 0;

                if( $q_listatel ) $cont_n_lt = $q_num_tel->num_rows;

                if( $cont_n_lt < 1 ) {

                    echo '<p class="p_q_no_result">Não há números cadastrados.</p>';

                } else {

            ?>

            <table class="lista_busca">
                <tr class="cab">
                    <th class="num_od">N</th>
                    <th class="num_listatel">Número</th>
                    <th class="desc_listatel">Tipo / Descrição</th>
                    <?php if ( $n_admsist >= 3 ) { ?>
                    <th class="tb_bt">&nbsp;</th>
                    <?php if ( $n_admsist >= 4 ) { ?>
                    <th class="tb_bt">&nbsp;</th>
                    <?php } ?>
                    <?php } ?>

                </tr>

                <?php

                    $i = 1;
                    $formata_tel = new FormataString();

                    while ( $d_num_tel = $q_num_tel->fetch_assoc() ) {

                    $num_tel_f = $formata_tel->getTelefone( $d_num_tel['ltn_num'] );

                ?>

                <tr class="even">
                    <td class="num_od"><?php echo $i++; ?></td>
                    <td class="num_listatel"><?php echo $num_tel_f; ?><?php if( !empty( $d_num_tel['ltn_ramal'] ) ) echo ' R: ' . $d_num_tel['ltn_ramal']; ?></td>
                    <td class="desc_listatel"><?php echo $d_num_tel['ltn_desc']; ?></td>
                    <?php if ( $n_admsist >= 3 ) { ?>
                    <td class="tb_bt">
                        <a href="edit_num_tel.php?idnt=<?php echo $d_num_tel['idlistatel_num']; ?>" title="Alterar este número" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="Alterar este número" /></a>
                    </td>
                    <?php if ( $n_admsist >= 4 ) { ?>
                    <td class="tb_bt"><a href='javascript:void(0)' onclick='drop_num_tel( <?php echo $d_num_tel['idlistatel_num']; ?> )' title="Excluir este número"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir este número" class="icon_button" /></a></td>
                    <?php } ?>
                    <?php } ?>

                </tr>

                <?php } // fim do while ( $d_num_tel... ?>

            </table><!-- fim da <table class="lista_busca"> -->

            <?php } // fim do if( $cont_lt < 1 ) ?>

            <div class="linha">
                OBSERVAÇÕES<?php if ( $n_admsist >= 3 ) {  ?> - <a href="cad_obs_tel.php?idlt=<?php echo $idlt ?>" title="Adicionar uma observação">Adicionar observação</a> <a href="#" title="Abrir em outra janela" onClick="javascript: ow('cad_obs_tel.php?idlt=<?php echo $idlt; ?>&targ=1', '800', '400'); return false" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>user_popup.png" alt="Abrir em outra janela" class="icon_popup" /></a><?php }; ?>
                <hr />
            </div>

            <?php
                $q_obs = "SELECT
                            `id_obs_listatel`,
                            `cod_listatel`,
                            `obs_listatel`,
                            `user_add`,
                            DATE_FORMAT(`data_add`, '%d/%m/%Y') AS data_add_f,
                            DATE_FORMAT(`data_add`, '%d/%m/%Y às %H:%i') AS data_add_fc,
                            `user_up`,
                            DATE_FORMAT(`data_up`, '%d/%m/%Y às %H:%i') AS data_up_f
                          FROM
                            `obs_listatel`
                          WHERE
                             `cod_listatel` = $idlt
                          ORDER BY
                            `data_add` DESC";

                // instanciando o model
                $model = SicopModel::getInstance();

                // executando a query
                $q_obs = $model->query( $q_obs );

                // fechando a conexao
                $model->closeConnection();

                $cont_obs = 0;

                if( $q_obs ) $cont_obs = $q_obs->num_rows;

                if( $cont_obs < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                    echo '<p class="p_q_no_result">Não há observações.</p>';
                } else {
                ?>

            <table class="lista_busca">
                <tr >
                    <th class="desc_data">DATA</th>
                    <th class="desc_obs">OBSERVAÇÃO</th>
                    <?php if ( $n_admsist >= 3 ) {  ?>
                    <th class="tb_bt" >&nbsp;</th>
                    <?php if ( $n_admsist >= 4 ) {  ?>
                    <th class="tb_bt" >&nbsp;</th>
                    <?php } ?>
                    <?php } ?>
                </tr>

                <?php while( $dados_obs = $q_obs->fetch_assoc() ) { ?>
                <tr class="even">
                    <td class="desc_data"><?php echo $dados_obs['data_add_f'] ?></td>
                    <td class="desc_obs"><div align="justify"><?php echo nl2br($dados_obs['obs_listatel']) ?></div></td>
                    <?php if ( $n_admsist >= 3 ) {  ?>
                    <td class="tb_bt">
                        <a href="edit_obs_tel.php?idobs=<?php echo $dados_obs['id_obs_listatel']; ?>" title="Alterar esta observação" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="Alterar esta observação" /></a>
                    </td>
                    <?php if ( $n_admsist >= 4 ) {  ?>
                    <td class="tb_bt">
                        <a href='javascript:void(0)' onclick='drop( "id_obs_listatel", "<?php echo $dados_obs['id_obs_listatel']; ?>", "sendlistatelobs", "drop_obs_listatel", "2")' title="Excluir esta observação"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir esta observação" class="icon_button" /></a>
                    </td>
                    <?php } ?>
                    <?php } ?>
                </tr>
                <tr>
                    <td colspan="4" class="desc_user" >Cadastrado em <?php echo $dados_obs['data_add_fc'] ?>, usuário <?php echo $dados_obs['user_add'] ?><?php if ($dados_obs['user_up'] and $dados_obs['data_up_f']) {?> - Atualizado em <?php echo $dados_obs['data_up_f'] ?>, usuário <?php echo $dados_obs['user_up'] ?> <?php }?></td>
                </tr>
                <?php } // fim do while( $dados_obs... ?>
            </table>
            <?php } // fim do if( $cont_obs < 1 ) ?>

<?php include 'footer.php'; ?>