<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$tipo_pag = 'PERMISSÕES DE USUÁRIO - AJAX';
$msg_falha = '<p class="q_error">FALHA!</p>';

$is_post = is_post();
if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $tipo_pag ).";
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$iduser = get_post( 'iduser', 'int' );
if ( empty( $iduser ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página. Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. ( $tipo_pag )";
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$perm_type = get_post( 'perm_type', 'int' );

$where_imp = 'FALSE';
$where_esp = 'FALSE';

if ( $perm_type == 1 ) {

    $where_imp = 'FALSE';
    $where_esp = 'FALSE';

} else if ( $perm_type == 2 ) {

    $where_imp = 'TRUE';
    $where_esp = 'FALSE';

} else if ( $perm_type == 3 ) {

    $where_imp = 'FALSE';
    $where_esp = 'TRUE';

}

$q_perm = "SELECT
             `sicop_users_perm`.`idpermissao`,
             `sicop_n_setor`.`id_n_setor`,
             `sicop_n_setor`.`n_setor_nome`,
             `sicop_u_n`.`idnivel`,
             `sicop_u_n`.`descnivel`,
             `sicop_u_n`.`descnivel_visit`
           FROM
             `sicop_users_perm`
             INNER JOIN `sicop_n_setor` ON `sicop_users_perm`.`cod_n_setor` = `sicop_n_setor`.`id_n_setor`
             INNER JOIN `sicop_u_n` ON `sicop_users_perm`.`cod_nivel` = `sicop_u_n`.`idnivel`
           WHERE
             `sicop_users_perm`.`cod_user` = $iduser
             AND
             `sicop_n_setor`.`especifico` = $where_esp
             AND
             `sicop_n_setor`.`impressao` = $where_imp
           ORDER BY
             `sicop_n_setor`.`n_setor_nome`";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_perm = $model->query( $q_perm );

// fechando a conexao
$model->closeConnection();

if ( !$q_perm ) {

    echo $msg_falha;
    exit;

}

$cont = $q_perm->num_rows;

$n_admsist = get_session( 'n_admsist', 'int' );

header( "Content-Type: text/html; charset=utf-8" );

//Evitando cache de arquivo
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0' );
header( 'Pragma: no-cache' );
header( 'Expires: 0' );

if ( $cont < 1 ) {
    echo '<p class="p_q_no_result">Não há permissões cadastradas.</p>';
    exit;
}

?>

    <table class="detal_user_perm" >
        <tr>
            <th class="detal_user_setor">Setor</th>
            <?php if ( $perm_type == 1 ) { ?>
            <th class="detal_user_nivel">Permissão</th>
            <?php } //if ( $perm_type == 1 ) { ?>
            <?php if ( $n_admsist >= 4 ) { ?>
            <?php if ( $perm_type == 1 ) { ?>
            <th class="tb_bt">&nbsp;</th>
            <?php } //if ( $perm_type == 1 ) { ?>
            <th class="tb_bt">&nbsp;</th>
            <?php } //if ( $n_admsist >= 4 ) { ?>
        </tr>

        <?php
            while ( $d_perm = $q_perm->fetch_assoc() ) {

                $nivel_access = $d_perm['descnivel'];
                $visit        = '';

                if ( $d_perm['id_n_setor'] == 38 ) {
                    $nivel_access = $d_perm['descnivel_visit'];
                    $visit        = 1;
                    //$nivel_access = AlteraVariavel_v( $d_perm['idnivel'] );
                }


        ?>

        <tr class="even">
            <td class="detal_user_setor"><?php echo $d_perm['n_setor_nome']?></td>
            <?php if ( $perm_type == 1 ) { ?>
            <td class="detal_user_nivel"><?php echo $nivel_access?></td>
            <?php } //if ( $perm_type == 1 ) { ?>
            <?php if ( $n_admsist >= 4 ) { ?>
            <?php if ( $perm_type == 1 ) { ?>
            <td class="tb_bt">
                <input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" name="edit_user_perm[]" value="<?php echo $d_perm['idpermissao']; ?>" title="Alterar permissão" />
                <input type="hidden" name="visit" value="<?php echo $visit; ?>" />
            </td>
            <?php } //if ( $perm_type == 1 ) { ?>
            <td class="tb_bt">
                <input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" name="del_user_perm[]" value="<?php echo $d_perm['idpermissao']; ?>" title="Excluir permissão" />
                <input type="hidden" name="perm_type" value="<?php echo $perm_type; ?>" />
            </td>
            <?php } //if ( $n_admsist >= 4 ) { ?>
        </tr>

        <?php } // fim do while ( $d_perm... ?>

    </table><!--/table.detal_user_perm-->


