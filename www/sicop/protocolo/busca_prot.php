<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_prot   = get_session( 'n_prot', 'int' );
$n_prot_n = 2;

if ( $n_prot < $n_prot_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'BUSCA DE PROTOCOLO';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$campo_prot_num   = get_get( 'campo_prot_num', 'int' );
$campo_prot_ano   = get_get( 'campo_prot_ano', 'int' );
$data_in          = get_get( 'data_in', 'busca' );
$hora_in          = get_get( 'hora_in', 'busca' );
$data_out         = get_get( 'data_out', 'busca' );
$hora_out         = get_get( 'hora_out', 'busca' );
$campo_tipo_doc   = get_get( 'campo_tipo_doc', 'int' );
$campo_assunto    = get_get( 'campo_assunto', 'busca' );
$campo_origem     = get_get( 'campo_origem', 'busca' );
$campo_prot_setor = get_get( 'campo_prot_setor', 'int' );

if( !empty( $_GET['busca'] ) ) {

    $where = '';

    if ( !empty( $campo_prot_num ) ){
        $where .= "WHERE `protocolo`.`prot_num` = $campo_prot_num";
    }

    if ( !empty( $campo_prot_ano ) ){

        $clausula = "`protocolo`.`prot_ano` = $campo_prot_ano";

        if ( !empty( $where ) ){
            $where .= ' AND ' . $clausula;
        } else {
            $where .= 'WHERE ' . $clausula;
        }

    }

    if ( !empty( $data_in ) or !empty( $data_out ) ) {

        if ( !empty( $data_in ) and !empty( $data_out ) ) {

            $data_in_f = $data_in . ' ' . $hora_in;
            $data_out_f = $data_out . ' ' . $hora_out;

            $clausula_data = "`protocolo`.`prot_data_in` BETWEEN STR_TO_DATE( '$data_in_f', '%d/%m/%Y %H:%i' ) AND STR_TO_DATE( '$data_out_f', '%d/%m/%Y %H:%i' )";

            if ( !empty( $where ) ){
                $where .= ' AND ' . $clausula_data;
            } else {
                $where .= 'WHERE ' . $clausula_data;
            }

        } else {

            $hora_f = !empty( $hora_in ) ? $hora_in : $hora_out;
            $data_f = !empty( $data_in ) ? $data_in : $data_out;

            //$clausula_data = "$sql_campo_data = IF( STR_TO_DATE( '$data_in_f', '%d/%m/%Y %H:%i' ), STR_TO_DATE( '$data_in_f', '%d/%m/%Y %H:%i' ), STR_TO_DATE( '$data_out_f', '%d/%m/%Y %H:%i' ) )";
            $clausula_data = "DATE( `protocolo`.`prot_data_in` ) = STR_TO_DATE( '$data_f', '%d/%m/%Y' ) ";

            $clausula_hora = '';
            if ( !empty( $hora_f ) ){
                $clausula_hora = "AND ( HOUR( `protocolo`.`prot_hora_in` ) = HOUR( '$hora_f' ) AND MINUTE( `protocolo`.`prot_hora_in` ) = MINUTE( '$hora_f' ) ) ";
            }

            if ( !empty( $where ) ){
                $where .= ' AND ' . $clausula_data . $clausula_hora;
            } else {
                $where .= 'WHERE ' . $clausula_data . $clausula_hora;
            }

        }

    }

    if ( !empty( $campo_tipo_doc ) ){

        $clausula = "`protocolo`.`prot_cod_tipo_doc` = $campo_tipo_doc";

        if ( !empty( $where ) ){
            $where .= ' AND ' . $clausula;
        } else {
            $where .= 'WHERE ' . $clausula;
        }

    }

    if ( !empty( $campo_assunto ) ){

        $clausula = "`protocolo`.`prot_assunto` LIKE '%$campo_assunto%'";

        if ( !empty( $where ) ){
            $where .= ' AND ' . $clausula;
        } else {
            $where .= 'WHERE ' . $clausula;
        }

    }

    if ( !empty( $campo_origem ) ){

        $clausula = "`protocolo`.`prot_origem` LIKE '%$campo_origem%'";

        if ( !empty( $where ) ){
            $where .= ' AND ' . $clausula;
        } else {
            $where .= 'WHERE ' . $clausula;
        }

    }

    if ( !empty( $campo_prot_setor ) ){

        $clausula = "`protocolo`.`prot_cod_setor` = $campo_prot_setor";

        if ( !empty( $where ) ){
            $where .= ' AND ' . $clausula;
        } else {
            $where .= 'WHERE ' . $clausula;
        }

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
               $where
               ORDER BY
                 `protocolo`.`prot_ano`, `protocolo`.`prot_num`";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_prot = $model->query( $q_prot );

    // fechando a conexao
    $model->closeConnection();

    $querytime = $model->getQueryTime();

    $cont = $q_prot->num_rows;

    $valor_busca = valor_user($_GET);

    $mensagem = "[ BUSCA EFETUADA ]\nBusca de protocolo efetuada\n\n $valor_busca\n\n Página: $pag";
    salvaLog( $mensagem );

}

$q_tipo_doc = 'SELECT `id_tipo_doc`, `tipo_doc` FROM `tipo_prot_doc` ORDER BY `tipo_doc` ASC';
$q_setor_dest = 'SELECT `idsetor`, `desc_prot` FROM `sicop_setor` ORDER BY `desc_prot` ASC';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_tipo_doc = $model->query( $q_tipo_doc );
$q_setor_dest = $model->query( $q_setor_dest );

// fechando a conexao
$model->closeConnection();

$desc_pag = 'Pesquisar protocolo';

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>

            <p class="descript_page">PESQUISAR PROTOCOLO</p>

            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="buscaprot" id="buscaprot" >
                <table class="busca_form"><!--remacc(campobusca);-->
                    <tr>
                        <td width="82" align="right">Número:</td>
                        <td width="191" align="left"><input name="campo_prot_num" type="text" class="CaixaTexto" id="campo_prot_num" onkeypress="return blockChars(event, 2);" value="<?php echo $campo_prot_num ?>" size="5" maxlength="4" />/<input name="campo_prot_ano" type="text" class="CaixaTexto" id="campo_prot_ano" onkeypress="return blockChars(event, 2);" value="<?php echo $campo_prot_ano ?>" size="5" maxlength="4" /></td>
                    </tr>
                    <tr>
                        <td align="right">Entre:</td>
                        <td align="left"><input name="data_in" type="text" class="CaixaTexto" id="data_in" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_in ?>" size="12" maxlength="10" /> às <input name="hora_in" type="text" class="CaixaTexto" id="hora_in" onblur="verifica_hora(this, this.value)" onkeypress="mascara_hora(this, this.value); return blockChars(event, 2);" value="<?php echo $hora_in; ?>" size="5" maxlength="5" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right">e:</td>
                        <td align="left"><input name="data_out" type="text" class="CaixaTexto" id="data_out" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_out ?>" size="12" maxlength="10" /> às <input name="hora_out" type="text" class="CaixaTexto" id="hora_out" onblur="verifica_hora(this, this.value)" onkeypress="mascara_hora(this, this.value); return blockChars(event, 2);" value="<?php echo $hora_out; ?>" size="5" maxlength="5" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Tipo:</td>
                        <td align="left">
                            <select name="campo_tipo_doc" class="CaixaTexto" id="campo_tipo_doc">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $d_tipo_doc = $q_tipo_doc->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_tipo_doc['id_tipo_doc']; ?>" <?php echo $d_tipo_doc['id_tipo_doc'] == $campo_tipo_doc ? 'selected="selected"' : ''; ?>><?php echo $d_tipo_doc['tipo_doc']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Assunto:</td>
                        <td align="left"><input name="campo_assunto" type="text" class="CaixaTexto" id="campo_assunto" onkeypress="return blockChars(event, 4);" value="<?php echo $campo_assunto ?>" size="30" /></td>
                    </tr>
                    <tr>
                        <td align="right">Origem:</td>
                        <td align="left"><input name="campo_origem" type="text" class="CaixaTexto" id="campo_origem" onkeypress="return blockChars(event, 4);" value="<?php echo $campo_origem ?>" size="30" /></td>
                    </tr>
                    <tr>
                        <td align="right">Setor:</td>
                        <td align="left">
                            <select name="campo_prot_setor" class="CaixaTexto" id="campo_prot_setor">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $d_setor_dest = $q_setor_dest->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_setor_dest['idsetor']; ?>" <?php echo $d_setor_dest['idsetor'] == $campo_prot_setor ? 'selected="selected"' : ''; ?>><?php echo $d_setor_dest['desc_prot']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <input name="busca" type="hidden" id="busca" value="busca" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="buscar" id="buscar" value="Buscar" />
                </div>

            </form>
            <script type="text/javascript">

                $(function() {
                    $( "#campo_prot_num" ).focus();
                    $( "#data_in, #data_out" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

            <?php if ( $n_prot >= 3 ) { ?>
            <p class="link_common" style="margin-top: 10px;"><a href="cad_prot.php">Cadastrar documento</a></p>
            <?php }; ?>

                <?php

                if ( empty( $_GET['busca'] ) ) {
                    include 'footer.php';
                    exit;
                }

                if ( empty( $cont ) or $cont < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                    echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';
                    include 'footer.php';
                    exit;
                }

                ?>

            <table class="lista_busca">
                <tr>
                    <th class="num_od">N</th>
                    <th class="n_prot">Nº PROTOCOLO</th>
                    <th class="desc_data_long">DATA / HORA</th>
                    <th class="mod_ent">ENTRADA</th>
                    <th class="tipo_doc">TIPO</th>
                    <th class="prot_asunt">ASSUNTO</th>
                    <th class="prot_origem">ORIGEM</th>
                    <th class="prot_dest">SETOR</th>
                </tr>
                <?php
                $i = 1;

                while ( $d_prot = $q_prot->fetch_assoc() ) {

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
                </tr>
                <?php } // fim do while ?>
            </table>

<?php include 'footer.php';?>