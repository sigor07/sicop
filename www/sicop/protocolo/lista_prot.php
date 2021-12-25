<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_prot   = get_session( 'n_prot', 'int' );
$n_prot_n = 3;

$n_prot_receb   = get_session( 'n_prot_receb', 'int' );
$n_prot_receb_n = 1;

$setor_user     = get_session( 'idsetor', 'int' );


$sit = get_get( 'sit', 'int' );

/*
    $sit
    1 = listar docs que ainda não foram despachados;
    2 = listar docs que foram despachados mas não foram recebidos - visualização do protocolo - todos os docs
    3 = listar docs que estão para ser recebidos - visualização do setor - somente docs do setor
*/

if ( empty( $sit ) or $sit > 3 ){
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página de listagem de documentos do protocolo.\n\n Página: $pag";
    salvaLog($mensagem);
    echo '<script type="text/javascript">history.go(-1);</script>';
    exit;
}

if ( $sit == 1 or $sit == 2 ) {

    if ( $n_prot < $n_prot_n ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'perm';
        $msg['entre_ch'] = 'LISTA DO PROTOCOLO';
        get_msg( $msg, 1 );

        require 'cab_simp.php';
        echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

        exit;

    }

} else if ( $sit == 3 ) {

    if ($n_prot_receb < $n_prot_receb_n) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'perm';
        $msg['entre_ch'] = 'LISTA DO PROTOCOLO';
        get_msg( $msg, 1 );

        require 'cab_simp.php';
        echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

        exit;

    }

}

$sit_lista = '';
$desc_pag  = '';
$where     = '';


if ( $sit == 1 ) {

    $sit_lista = 'LISTAR DOCUMENTOS RECEBIDOS NO PROTOCOLO';
    $desc_pag  = 'Listar documentos recebidos';
    $where     = " `protocolo`.`prot_despachado` = FALSE AND `protocolo`.`prot_canc` = FALSE";

} else if ( $sit == 2 ) {

    $sit_lista = 'LISTAR DOCUMENTOS DESPACHADOS';
    $desc_pag  = 'Listar documentos despachados';
    $where     = " `protocolo`.`prot_despachado` = TRUE AND ISNULL( `prot_user_rec` ) AND `protocolo`.`prot_canc` = FALSE";

} else if ( $sit == 3 ) {

    $sit_lista = 'RECEBER DOCUMENTOS';
    $desc_pag  = 'Receber documentos';
    $where     = " `protocolo`.`prot_despachado` = TRUE AND `protocolo`.`prot_cod_setor` = $setor_user AND ISNULL( `prot_user_rec` ) AND `protocolo`.`prot_canc` = FALSE";

}

$q_prot = "SELECT
             `protocolo`.`idprot`,
             `protocolo`.`prot_num`,
             `protocolo`.`prot_ano`,
             `protocolo`.`prot_cod_tipo_doc`,
             `protocolo`.`prot_assunto`,
             `protocolo`.`prot_origem`,
             DATE_FORMAT ( `protocolo`.`prot_data_in`, '%d/%m/%Y' ) AS prot_data_in_f,
             DATE_FORMAT ( `protocolo`.`prot_hora_in`, '%H:%i' ) AS prot_hora_in_f,
             `tipo_prot_modo_in`.`modo_in`,
             `tipo_prot_doc`.`tipo_doc`,
             `sicop_setor`.`desc_prot`
           FROM
             `protocolo`
             LEFT JOIN `tipo_prot_modo_in` ON `protocolo`.`prot_cod_modo_in` = `tipo_prot_modo_in`.`id_modo_in`
             LEFT JOIN `tipo_prot_doc` ON `protocolo`.`prot_cod_tipo_doc` = `tipo_prot_doc`.`id_tipo_doc`
             LEFT JOIN `sicop_setor` ON `protocolo`.`prot_cod_setor` = `sicop_setor`.`idsetor`
           WHERE
             $where
           ORDER BY
             `protocolo`.`prot_ano`, `protocolo`.`prot_num`";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_prot = $model->query( $q_prot );

// fechando a conexao
$model->closeConnection();

if( !$q_prot ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont = $q_prot->num_rows;

$querytime = $model->getQueryTime();

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$q_string = '';

parse_str( $_SERVER[ 'QUERY_STRING' ], $q_string );

if ( isset( $q_string['op'] ) ) {
    unset( $q_string['op'] );
}

$desc_pag = 'Protocolo';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>

            <p class="descript_page"><?php echo $sit_lista; ?></p>

            <?php if ( $n_prot >= 3 ) {?>
            <p class="link_common" ><a href="cad_prot.php">Cadastrar documento</a></p>
            <?php }; ?>

            <?php

                if ( empty( $cont ) or $cont < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                    echo '<p class="p_q_no_result">Não há documentos.</p>';
                    include 'footer.php';
                    exit;
                }

            ?>
            <?php if ( ( $n_prot >= 3 and $sit == 1 ) or ( $sit == 3 and $n_prot_receb >= 1 ) ){?>
            <form action="<?php echo SICOP_ABS_PATH ?>send/sendprotdesp.php" method="post" name="sendprotdesp" id="sendprotdesp" onSubmit="return valida_lista_prot();">
            <?php };?>

                <table class="lista_busca grid">

                    <thead>
                        <tr>
                            <th class="num_od">N</th>
                            <th class="n_prot">Nº PROTOCOLO</th>
                            <th class="desc_data_long">DATA / HORA</th>
                            <th class="mod_ent">ENTRADA</th>
                            <th class="tipo_doc">TIPO</th>
                            <th class="prot_asunt">ASSUNTO</th>
                            <th class="prot_origem">ORIGEM</th>
                            <th class="prot_dest">SETOR</th>
                            <th class="tb_ck">&nbsp;</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php
                        $i = 1;

                        while( $d_prot = $q_prot->fetch_assoc() ) {

                            $cor_font = '#000000';

                            if ( $d_prot['prot_cod_tipo_doc'] == 1 ) $cor_font = '#FF0000';

                            ?>

                    <tr class="even">
                        <td class="num_od"><?php echo $i++; ?></td>
                        <td class="n_prot"><a href="detal_prot.php?idprot=<?php echo $d_prot['idprot'] ;?>" ><?php echo number_format( $d_prot['prot_num'], 0, '', '.' ) . '/' . $d_prot['prot_ano'];?></a></td>
                        <td class="desc_data_long"><?php echo $d_prot['prot_data_in_f'] . ' às ' . $d_prot['prot_hora_in_f'];?></td>
                        <td class="mod_ent"><?php echo $d_prot['modo_in'];?></td>
                        <td class="tipo_doc"><font color="<?php echo $cor_font;?>"><?php echo $d_prot['tipo_doc'];?></font></td>
                        <td class="prot_asunt"><font color="<?php echo $cor_font;?>"><?php echo $d_prot['prot_assunto'];?></font></td>
                        <td class="prot_origem"><?php echo $d_prot['prot_origem'];?></td>
                        <td class="prot_dest"><?php echo $d_prot['desc_prot'];?></td>
                        <td class="tb_ck">
                        <?php if ( ( $n_prot >= 3 and $sit == 1 ) or ( $sit == 3 and $n_prot_receb >= 1 ) ){?>
                        <input name="prot[]" type="checkbox" class="mark_row" id="prot" value="<?php echo $d_prot['idprot'];?>" />
                        <?php }?>
                        </td>
                    </tr>
                    <?php } // fim do while ?>

                    </tbody>

                    <?php if ( ( $n_prot >= 3 and $sit == 1 ) or ( $sit == 3 and $n_prot_receb >= 1 ) ){ ?>
                    <tfoot>
                        <tr>
                            <td  class="tb_ck_leg" colspan="8">todos:</td>
                            <td class="tb_ck"><input type="checkbox" name="todos" value="todos" /></td>
                        </tr>
                    </tfoot>
                    <?php } ?>

                </table>

            <?php if ( ( $n_prot >= 3 and $sit == 1 ) or ( $sit == 3 and $n_prot_receb >= 1 ) ){ ?>
                <p class="bt_leg">COM MARCADOS</p>
                <div class="form_bts">
                    <?php if ( $n_prot >= 3 and $sit == 1 ) {?>
                    <input class="form_bt" name="dps" type="submit" id="dps" value="Despachar para os setores" />
                    <?php } else if ( $sit == 3 and $n_prot_receb >= 1 ) {?>
                    <input class="form_bt" name="rec" type="submit" id="rec" value="Receber" />
                    <?php } ?>
                </div>
            </form>
            <?php } ?>

<?php include 'footer.php';?>