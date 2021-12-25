<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_peculio       = get_session( 'n_peculio', 'int' );
$n_peculio_baixa = get_session( 'n_peculio_baixa', 'int' );
$n_peculio_n = 3;

if ($n_peculio < $n_peculio_n) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'BAIXA DE PERTENCES - PECÚLIO';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

if ($n_peculio_baixa < 1) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'BAIXA DE PERTENCES - PECÚLIO';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$iddet = get_get( 'iddet', 'int' );

if ( empty( $iddet ) ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página da grade.\n\n Página: $pag";
    salvaLog( $mensagem );
    echo '<script type="text/javascript">history.go(-1);</script>';
    exit;
}

$q_pec = "SELECT
            `peculio`.`idpeculio`,
            `peculio`.`cod_detento`,
            `peculio`.`descr_peculio`,
            `peculio`.`retirado`,
            `peculio`.`user_add`,
            `peculio`.`data_add`,
            DATE_FORMAT( `peculio`.`data_add`, '%d/%m/%Y' ) AS data_add_f,
            DATE_FORMAT( `peculio`.`data_add`, '%d/%m/%Y às %H:%i' ) AS data_add_fc,
            `peculio`.`ip_add`,
            `peculio`.`user_up`,
            `peculio`.`data_up`,
            DATE_FORMAT( `peculio`.`data_up`, '%d/%m/%Y às %H:%i' ) AS data_up_f,
            `peculio`.`ip_up`,
            `tipopeculio`.`tipo_peculio`
          FROM
            `peculio`
            INNER JOIN `tipopeculio` ON `peculio`.`cod_tipo_peculio` = `tipopeculio`.`idtipopeculio`
          WHERE
            `peculio`.`cod_detento` = $iddet
            AND
            `peculio`.`retirado` = false
          ORDER BY
            `peculio`.`data_add`";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_pec = $model->query( $q_pec );

// fechando a conexao
$model->closeConnection();

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Baxiar pertences';

// adicionando o javascript
$cab_js   = array();
$cab_js[] = 'valida.js';
$cab_js[] = 'jquery.markrows.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) )
    $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( 'Baixar pertences', $pag_atual, 5 );
$trail->output();
?>


            <p class="descript_page">BAIXAR PERTENCES</p>

            <?php include 'quali/det_cad.php'; ?>

            <div class="linha_pec">
                PERTENCES
                <hr />
            </div>

            <?php
            $cont_pec = $q_pec->num_rows;
            if( $cont_pec < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não há pertences cadastrados.</p>';
            } else {
                  ?>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendpecbaixa.php" method="post" name="sendpecbaixa" id="sendpecbaixa" onSubmit="return validapec();">

                <table class="lista_busca grid">

                    <thead>
                        <tr>
                            <th class="desc_data">DATA</th>
                            <th class="tipo_pec">TIPO</th>
                            <th class="desc_pec">DESCRIÇÃO</th>
                            <th class="tb_ck">&nbsp;</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php while ( $d_pec = $q_pec->fetch_assoc() ) { ?>
                        <tr class="even">
                            <td class="desc_data"><?php echo $d_pec['data_add_f'] ?></td>
                            <td class="tipo_pec"><?php echo $d_pec['tipo_peculio'] ?></td>
                            <td class="desc_pec"><?php echo nl2br( $d_pec['descr_peculio'] ) ?></td>
                            <td class="tb_ck"><input name="idpec[]" type="checkbox" class="mark_row" value="<?php echo $d_pec['idpeculio']; ?>" /></td>
                        </tr>
                        <?php } // fim do while ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td  class="tb_ck_leg" colspan="3">todos:</td>
                            <td class="tb_ck"><input type="checkbox" name="todos" value="todos" /></td>
                        </tr>
                    </tfoot>

                </table>

                <p class="table_leg">Observações de retirada (será colocada nos itens marcados)</p>

                <p align="center">
                    <textarea name="obs_ret" id="obs_ret" cols="60" rows="3" class="CaixaTexto" onBlur="upperMe(this); remacc(this);" onKeyPress="return blockChars(event, 4);"></textarea>
                </p>

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Baixar" />
                    <input class="form_bt" type="button" name="" onclick="history.go(-1)" value="Cancelar" />
                </div>

                <input name="iddet" type="hidden" id="iddet" value="<?php echo $iddet;?>">
            </form>

            <?php } // fim do if que conta o número de ocorrencias ?>

<?php include 'footer.php'; ?>