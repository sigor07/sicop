<?php

if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';
$tipo_pag = 'DETALHES DO PROTOCOLO';

$n_prot     = SicopController::getSession( 'n_prot', 'int' );
$n_prot_n   = 4;

$setor_user = get_get( 'idsetor', 'int' );

if ( $n_prot < $n_prot_n ) {

    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
    salvaLog( $mensagem );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idprot = get_get( 'idprot', 'int' );

if ( empty( $idprot ) ){

    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página de detalhes do protocolo.\n\nPágina: $pag";
    salvaLog($mensagem);

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

$db = SicopModel::getInstance();

$q_prot = $db->query( $q_prot );
if ( !$q_prot ) {

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();
    $db->closeConnection();

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( $tipo_pag ). \n\n $msg_err_mysql";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$db->closeConnection(); // fecho a conexao

$contd = $q_prot->num_rows;
if ( $contd < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "A consulta retornou 0 ocorrências ( $tipo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$d_prot = $q_prot->fetch_assoc();

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
if ( $d_prot['prot_tipo_doc'] == 1 ) $cor_font = '#FF0000';

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

$desc_pag = 'Excluir documento';


require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) {
    $pag_atual .= '?' . $qs;
}

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>

            <p class="descript_page">EXCLUIR DOCUMENTO</p>

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
                    </td>
                </tr>

                <tr>
                    <td class="prot_g" colspan="6">
                        Recebido: <?php echo $result_rec; ?>
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


            <p class="confirm_ask">Tem certeza de que deseja excluir este documento?</p>

            <p style="text-align: center">
                <img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_attention.png" alt="Atenção" class="icon_alert" />&nbsp;ATENÇÃO -&gt; Você <b>não poderá</b> desfazer essa operação.
            </p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendprot.php" method="post" name="delprot" id="delprot" >

                <input name="idprot" type="hidden" id="idprot" value="<?php echo $d_prot['idprot']; ?>"/>
                <input name="proced" type="hidden" id="proced" value="2"/>

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Excluir" />
                    <input class="form_bt" type="button" name="" onClick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

<?php include 'footer.php';?>