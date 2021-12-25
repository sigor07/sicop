<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_prot     = get_session( 'n_prot', 'int' );
$n_prot_n   = 2;

$n_prot_receb   = get_session( 'n_prot_receb', 'int' );
$n_prot_receb_n = 1;

$setor_user = get_session( 'idsetor', 'int' );

$motivo_pag = 'DETALHES DO PROTOCOLO';

if ( $n_prot < $n_prot_n and $n_prot_receb < $n_prot_receb_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idprot = get_get( 'idprot', 'int' );

if ( empty( $idprot ) ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( IDENTIFICADOR DA ORDEM DE SAÍDA EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$q_prot = "SELECT
             `protocolo`.`idprot`,
             `protocolo`.`prot_num`,
             `protocolo`.`prot_ano`,
             `protocolo`.`prot_cod_tipo_doc`,
             `protocolo`.`prot_assunto`,
             `protocolo`.`prot_origem`,
             `protocolo`.`prot_cod_setor`,
             DATE_FORMAT ( `protocolo`.`prot_data_in`, '%d/%m/%Y' ) AS prot_data_in_f,
             DATE_FORMAT ( `protocolo`.`prot_hora_in`, '%H:%i' ) AS prot_hora_in_f,
             `protocolo`.`prot_despachado`,
             DATE_FORMAT( `protocolo`.`prot_data_hora_desp`, '%d/%m/%Y às %H:%i' ) AS prot_data_desp_f,
             `protocolo`.`prot_user_rec`,
             DATE_FORMAT( `protocolo`.`prot_data_hora_rec`, '%d/%m/%Y às %H:%i' ) AS prot_data_rec_f,
             `protocolo`.`prot_canc`,
             `protocolo`.`user_add`,
             DATE_FORMAT(`protocolo`.`data_add`, '%d/%m/%Y às %H:%i') AS data_add,
             `protocolo`.`user_up`,
             DATE_FORMAT(`protocolo`.`data_up`, '%d/%m/%Y às %H:%i') AS data_up,
             `tipo_prot_modo_in`.`modo_in`,
             `tipo_prot_doc`.`tipo_doc`,
             `sicop_setor`.`desc_prot`,
             `sicop_users`.`nome_cham`
           FROM
             `protocolo`
             LEFT JOIN `tipo_prot_modo_in` ON `protocolo`.`prot_cod_modo_in` = `tipo_prot_modo_in`.`id_modo_in`
             LEFT JOIN `tipo_prot_doc` ON `protocolo`.`prot_cod_tipo_doc` = `tipo_prot_doc`.`id_tipo_doc`
             LEFT JOIN `sicop_setor` ON `protocolo`.`prot_cod_setor` = `sicop_setor`.`idsetor`
             LEFT JOIN `sicop_users` ON `protocolo`.`prot_user_rec` = `sicop_users`.`iduser`
           WHERE
             `protocolo`.`idprot` = $idprot
           LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_prot = $model->query( $q_prot );

// fechando a conexao
$model->closeConnection();

if ( !$q_prot ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_prot = $q_prot->num_rows;

if($cont_prot < 1) {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( DETALHES DO PROTOCOLO ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$d_prot = $q_prot->fetch_assoc();

if ( $setor_user != $d_prot['prot_cod_setor'] ) {
    if ( $n_prot < $n_prot_n ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'perm';
        $msg['entre_ch'] = $motivo_pag;
        get_msg( $msg, 1 );

        require 'cab_simp.php';
        echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

        exit;

    }
}

$prot_canc = (int)$d_prot['prot_canc'];

$cancelado = false;
if ( $prot_canc == 1 ) $cancelado = true;

$recebido = false;
$despachado = false;

if ( !$cancelado ) {

    $prot_desp = (int)$d_prot['prot_despachado'];

    if ( $prot_desp == 1 ) $despachado = true;

    $data_desp  = $d_prot['prot_data_desp_f'];

    $result_desp = 'NÃO';

    if ( $despachado ){
        $result_desp = 'SIM';
        if ( !empty( $data_desp ) ) {
            $result_desp .= ', em ' . $data_desp;
        }
    }

    $result_rec = 'NÃO';

    if ( $despachado ){

        $id_user_rec = $d_prot['prot_user_rec'];
        $user_rec    = $d_prot['nome_cham'];
        $data_rec    = $d_prot['prot_data_rec_f'];

        // se tiver $id_user_rec é que ja foi recebido
        if ( !empty( $id_user_rec ) ) {

            $result_rec = 'SIM';
            $recebido = true;

            if ( !empty( $data_rec ) ) {
                $result_rec .= ', em ' . $data_rec;
            }
            if ( !empty( $user_rec ) ) {
                $result_rec .= ', por ' . $user_rec;
            }
        }
    }
}

// MUDAR A COR DA FONTE, NO CASO DE ALVARÁ
$cor_font = '#000000';
if ( $d_prot['prot_cod_tipo_doc'] == 1 ) $cor_font = '#FF0000';

$user_add = '';
$user_up = '&nbsp;';

if (!empty($d_prot['user_add'])){
    $user_add = 'Usuário: '.$d_prot['user_add'].', em '.$d_prot['data_add'];
};

if (!empty($d_prot['user_up'])){
    $user_up = 'Usuário: '.$d_prot['user_up'].', em '.$d_prot['data_up'];
};

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Detalhes do documento';

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4);
$trail->output();
?>

            <p class="descript_page">DETALHES DO DOCUMENTO</p>

            <?php if ($n_prot >= 3 ) {  ?>
            <p class="link_common">

                <a href="edit_prot.php?idprot=<?php echo $d_prot['idprot']; ?>" title="Alterar este documento">Alterar</a>

                <?php if ( $despachado ) { ?> | <a href='javascript:void(0)' onclick='confirm_canc_desp(<?php echo $d_prot['idprot'];?>)' title="Cancelar o despacho deste documento">Cancelar despacho</a><?php } ?>

                <?php if ( $recebido ) { ?> | <a href='javascript:void(0)' onclick='confirm_canc_receb(<?php echo $d_prot['idprot'];?>)' title="Cancelar o recebimento deste documento">Cancelar recebimento</a><?php } ?>

                <?php if ($n_prot >= 4 ) {  ?>
                 | <a href="del_prot.php?idprot=<?php echo $d_prot['idprot']; ?>" title="Excluir este documento">Excluir</a>
                <?php }; ?>

            </p>
            <?php }; ?>

            <table class="detal_prot">
                <tr>
                    <td class="prot_p" colspan="2">Modo de entrada: <?php echo $d_prot['modo_in']; ?></td>
                    <td class="prot_p" colspan="2">Tipo: <font color="<?php echo $cor_font; ?>"><?php echo $d_prot['tipo_doc']; ?></font></td>
                    <td class="prot_p" colspan="2">Protocolo Nº: <b><?php echo number_format($d_prot['prot_num'], 0, '', '.') . '/' . $d_prot['prot_ano']; ?></b> às <?php echo $d_prot['prot_hora_in_f'];?></td>
                </tr>
                <tr>
                    <td class="prot_g" colspan="6">Assunto: <?php echo $d_prot['prot_assunto']; ?></td>
                </tr>
                <tr>
                    <td class="prot_m" colspan="3">Origem: <?php echo $d_prot['prot_origem']; ?></td>
                    <td class="prot_m" colspan="3">Setor: <?php echo $d_prot['desc_prot']; ?></td>
                </tr>

                <?php if ( $cancelado ) {?>
                <tr>
                    <td class="prot_g_cancel" colspan="6">CANCELADO</td>
                </tr>
                <?php } ?>

                <?php if ( !$cancelado ) {?>
                <tr>
                    <td class="prot_g" colspan="6">
                        Despachado: <?php echo $result_desp; ?>
                        <?php
                        if ( $n_prot >= 3 ) {
                            if ( !$despachado ){
                        ?>
                        - <a href='javascript:void(0)' onclick='confirm_desp_prot(<?php echo $d_prot['idprot'];?>)' title="Despachar este documento">Despachar</a>
                        <?php
                            }
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="prot_g" colspan="6">
                        Recebido: <?php echo $result_rec; ?>
                        <?php
                        if ( $despachado ){
                            if ( !$recebido ) {
                                if ( $setor_user == $d_prot['prot_cod_setor'] ) {
                                    if ( $n_prot_receb >= $n_prot_receb_n ) {
                        ?>
                                    - <a href='javascript:void(0)' onclick='confirm_receb_prot(<?php echo $d_prot['idprot'];?>)' title="Receber este documento">Receber</a>
                        <?php
                                    }
                                }
                            }
                        }
                        ?>
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <td class="prot_m_user_l" colspan="3">CADASTRAMENTO</td>
                    <td class="prot_m_user_l" colspan="3">ÚLTIMA ATUALIZAÇÃO</td>
                </tr>
                <tr>
                    <td class="prot_m_user_f" colspan="3"><?php echo $user_add ?></td>
                    <td class="prot_m_user_f" colspan="3"><?php echo $user_up; ?></td>
                </tr>
            </table>

<?php include 'footer.php';?>