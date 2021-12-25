<?php

if ( !isset( $_SESSION ) ) session_start();

require 'init/config.php';
require 'incl_complete.php';

$op     = !empty( $_POST['op'] ) ? tratabusca( $_POST['op'] ) : '';
$n_raio = !empty( $_POST['n_raio'] ) ? (int)$_POST['n_raio'] : '';

$query_raio = 'SELECT `idraio`, `raio` FROM `raio` ORDER BY `raio` ASC';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_raio = $model->query( $query_raio );

// fechando a conexao
$model->closeConnection();

$desc_pag = 'Gerar lista de detentos para visita';

require 'cab.php';
$trail = new Breadcrumb();
$trail->add('Gerar lista para visita', $_SERVER['PHP_SELF'], 3);
$trail->output();
?>


            <p class="descript_page">GERAR LISTA DE <?php echo SICOP_DET_DESC_U; ?>S PARA VISITA</p>

            <form action="gera_lista_v.php" method="post" name="buscadet" id="buscadet" onsubmit="return validalista();">

                <table class="busca_form"><!--remacc(campobusca);-->
                    <tr>
                        <td width="90" align="right"><?php echo SICOP_RAIO ?>:</td>
                        <td width="168" align="left">
                            <select name="n_raio" class="CaixaTexto" id="n_raio">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $dados_raio = $query_raio->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados_raio['idraio'];?>" <?php echo $dados_raio['idraio'] == $n_raio ? 'selected="selected"' : ''; ?>><?php echo $dados_raio['raio'];?></option>
                                <?php };?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Ordenar por:</td>
                        <td align="left">
                            <select name="op" class="CaixaTexto" id="op">
                                <option value="nome" <?php echo $op == 'nome' or empty( $op ) ? 'selected="selected"' : ''; ?> >Nome</option>
                                <option value="matr" <?php echo $op == 'matr' ? 'selected="selected"' : ''; ?> >Matrícula</option>
                                <option value="cela" <?php echo $op == 'cela' ? 'selected="selected"' : ''; ?> ><?php echo SICOP_CELA ?></option>
                            </select>
                        </td>
                    </tr>
                </table>

                <input name="gera" type="hidden" id="gera" value="gera" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="gerar" id="gerar" value="Gerar lista" />
                </div>

            </form>

            <?php if ( !empty( $_POST['gera'] ) ) { ?>
            <script type="text/javascript">javascript: ow('print/lista_visita.php?n_raio=<?php echo $n_raio; ?>&op=<?php echo $op; ?>', '600', '600'); //history.go(-1);</script>
            <?php } ?>

<?php include 'footer.php'; ?>