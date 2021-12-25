<?php

if ( empty( $valorbusca ) ) $valorbusca = '';
if ( empty( $link ) ) $link = SICOP_ABS_PATH . 'detento/detalhesdet.php';
if ( empty( $add_link ) ) $add_link = '';

if ( empty( $cont ) or $cont < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
    echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';
    include 'footer.php';
    exit;
}

// adicionando o javascript
$cab_js = 'ajax/jq_lista_busca.js';
set_cab_js( $cab_js );

echo get_cab_js();

?>

            <p class="p_q_info">
                Essa consulta retornou <?php echo $cont ?> registros (<?php echo round( $querytime, 2 ) ?> seg) <!--<a href="buscadet.php<?php //echo '?proced=' . $proced; ?>">Nova consulta</a>-->
                <?php if ( $imp_chefia >= $n_imp_n or $imp_cadastro >= $n_imp_n ) { ?>
                - <a href='javascript:void(0)' title="Imprimir a lista" id="print_lista" >Imprimir</a>
                - <a href='javascript:void(0)' title="Exportar a lista para o excel" id="exp_lista">Exportar</a>
                <?php }; ?>
            </p>

            <form action="" method="post" name="lista_det" id="lista_det">

                <table class="lista_busca">

                    <tr class="cab">
                        <th class="num_od">N</th>
                        <th class="nome_det"><?php echo SICOP_DET_DESC_FU; ?>
                            <?php echo link_ord_asc( $ordpor, 'nome', $q_string, 'nome d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L ) ?>
                            <?php echo link_ord_desc( $ordpor, 'nome', $q_string, 'nome d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L ) ?>
                        </th>
                        <th class="matr_det">Matrícula
                            <?php echo link_ord_asc( $ordpor, 'matr', $q_string, 'matrícula' ) ?>
                            <?php echo link_ord_desc( $ordpor, 'matr', $q_string, 'matrícula' ) ?>
                        </th>
                        <th class="raio_det"><?php echo SICOP_RAIO ?>
                            <?php echo link_ord_asc( $ordpor, 'raio', $q_string, mb_strtolower( SICOP_RAIO ) . '/' . mb_strtolower( SICOP_CELA ) ) ?>
                            <?php echo link_ord_desc( $ordpor, 'raio', $q_string, mb_strtolower( SICOP_RAIO ) . '/' . mb_strtolower( SICOP_CELA ) ) ?>
                        </th>
                        <th class="cela_det"><?php echo SICOP_CELA ?></th>
                        <th class="local_mov">Procedência
                            <?php echo link_ord_asc( $ordpor, 'proc', $q_string, 'procedência' ) ?>
                            <?php echo link_ord_desc( $ordpor, 'proc', $q_string, 'procedência' ) ?>
                        </th>
                        <th class="data_mov"> Inclusão
                            <?php echo link_ord_asc( $ordpor, 'data', $q_string, 'data da inclusão' ) ?>
                            <?php echo link_ord_desc( $ordpor, 'data', $q_string, 'data da inclusão' ) ?>
                        </th>
                        <th class="oculta"></th>
                    </tr>

                    <?php
                    $i = 1;

                    while( $d_det = $query->fetch_object() ) {

                        $tipo_mov_in  = $d_det->tipo_mov_in;
                        $procedencia  = $d_det->procedencia;
                        $data_incl    = $d_det->data_incl;
                        $tipo_mov_out = $d_det->tipo_mov_out;
                        $iddestino    = $d_det->iddestino;

                        $det = manipula_sit_det_l( $tipo_mov_in, $procedencia, $data_incl, $tipo_mov_out, $iddestino );

                        ?>
                    <tr class="even">
                        <td class="num_od"><?php echo $i++; ?></td>
                        <td class="nome_det <?php if ( stripos( $ordpor, 'nome' ) !== false ) echo 'ord';?>" title="Pai: <?php echo $d_det->pai_det;?>&#13;Mãe: <?php echo $d_det->mae_det;?>&#13;Situação atual: <?php echo $det['sitat'];?>" ><a href="<?php echo $link; ?>?iddet=<?php echo $d_det->iddetento . $add_link /*alphaID($d_det['iddetento'])*/;?>"> <?php echo highlight( $valorbusca, $d_det->nome_det );?></a></td>
                        <td class="matr_det <?php echo $det['css_class']; if ( stripos( $ordpor, 'matr' ) !== false ) echo ' ord';?>"><?php echo !empty( $d_det->matricula ) ? formata_num( $d_det->matricula ) : '&nbsp;';?></td>
                        <td class="raio_det <?php echo $det['css_class']; if ( stripos( $ordpor, 'raio' ) !== false ) echo ' ord';?>"><?php echo !empty( $d_det->raio ) ? $d_det->raio : '&nbsp;'; ?></td>
                        <td class="cela_det <?php echo $det['css_class']; if ( stripos( $ordpor, 'raio' ) !== false ) echo ' ord';?>"><?php echo !empty( $d_det->cela ) ? $d_det->cela : '&nbsp;'; ?></td>
                        <td class="local_mov <?php echo $det['css_class']; if ( stripos( $ordpor, 'proc' ) !== false ) echo ' ord';?>"><?php echo !empty( $det['procedencia'] ) ? $det['procedencia'] : '&nbsp;'; ?></td>
                        <td class="data_mov <?php echo $det['css_class']; if ( stripos( $ordpor, 'data' ) !== false ) echo ' ord';?>"><?php echo !empty( $det['data_incl'] ) ? $det['data_incl'] : '&nbsp;'; ?></td>
                        <td class="oculta"><input type="hidden" name="iddet_p[]" value="<?php echo $d_det->iddetento;?>" /></td>
                    </tr>
                        <?php } // fim do while ?>
                </table>

                <input type="hidden" name="op" value="<?php echo $ordpor;?>" />

            </form><!-- /form id="lista_det" -->