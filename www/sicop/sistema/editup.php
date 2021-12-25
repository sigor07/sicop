<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_admsist   = get_session( 'n_admsist', 'int' );
$n_admsist_n = 3;

$motivo_pag = 'ALTERAR DADOS DA UNIDADE';

if ($n_admsist < $n_admsist_n) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$query = 'SELECT
              `idup`,
              `secretaria`,
              `coord`,
              `unidade_sort`,
              `unidade_long`,
              `endereco`,
              `endereco_sort`,
              `cidade`,
              `email`,
              `nome_sistema`,
              DATE_FORMAT( `dataadd`, "%d/%m/%Y às %H:%i") AS `dataadd_f`,
              DATE_FORMAT( `dataup`, "%d/%m/%Y às %H:%i") AS `dataup_f`
            FROM
              `sicop_unidade`
            WHERE
              `idup` = 1
            LIMIT 1';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query = $model->query( $query );

// fechando a conexao
$model->closeConnection();

if( !$query ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont = $query->num_rows;

if ( $cont < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrencias ( $motivo_pag ).\n\n Página $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$dados = $query->fetch_assoc();

$secretaria      = $dados['secretaria'];
$coord           = $dados['coord'];
$unidade_sort    = $dados['unidade_sort'];
$unidade_long    = $dados['unidade_long'];
$endereco        = $dados['endereco'];
$endereco_sort   = $dados['endereco_sort'];
$cidade          = $dados['cidade'];
$email           = $dados['email'];
$nome_sistema    = $dados['nome_sistema'];
$datacriacao     = $dados['dataadd_f'];
$dataatualizacao = $dados['dataup_f'];

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';
$trail = new Breadcrumb();
$trail->add('Alterar Detalhes da Unidade', $_SERVER['PHP_SELF'], 3);
$trail->output();
?>

            <p class="descript_page">ALTERAR DADOS DA UNIDADE PRISIONAL</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendup.php" method="post" name="editup" id="editup" onSubmit="return validaeditup();">
                <table class="edit">
                    <tr>
                        <td class="dup_legend">Secretaria</td>
                        <td class="dup_field"><input name="secretaria" type="text" class="CaixaTexto" id="secretaria" value="<?php echo $secretaria ?>" size="90" maxlength="50" /></td>
                    </tr>
                    <tr>
                        <td class="dup_legend">Coordenadoria</td>
                        <td class="dup_field"><input name="coordenadoria" type="text" class="CaixaTexto" id="coordenadoria" value="<?php echo $coord ?>" size="90" maxlength="100" /></td>
                    </tr>
                    <tr>
                        <td class="dup_legend">Nome da unidade completo</td>
                        <td class="dup_field"><input name="unidadelongo" type="text" class="CaixaTexto" id="unidadelongo" value="<?php echo $unidade_long ?>" size="90" maxlength="100" /></td>
                    </tr>
                    <tr>
                        <td class="dup_legend">Nome da unidade abreviado</td>
                        <td class="dup_field"><input name="unidadecurto" type="text" class="CaixaTexto" id="unidadecurto" value="<?php echo $unidade_sort ?>" size="90" maxlength="100" /></td>
                    </tr>
                    <tr>
                        <td class="dup_legend">Endereço da unidade completo</td>
                        <td class="dup_field"><input name="endereco" type="text" class="CaixaTexto" id="endereco" value="<?php echo $endereco ?>" size="90" maxlength="150" /></td>
                    </tr>
                    <tr>
                        <td class="dup_legend">Endereço da unidade abreviado</td>
                        <td class="dup_field"><input name="enderecocurto" type="text" class="CaixaTexto" id="enderecocurto" value="<?php echo $endereco_sort ?>" size="90" maxlength="150" /></td>
                    </tr>
                    <tr>
                        <td class="dup_legend">Cidade</td>
                        <td class="dup_field"><input name="cidade" type="text" class="CaixaTexto" id="cidade" value="<?php echo $cidade ?>" size="90" maxlength="150" /></td>
                    </tr>
                    <tr>
                        <td class="dup_legend">Email</td>
                        <td class="dup_field"><input name="email" type="text" class="CaixaTexto" id="email" value="<?php echo $email ?>" size="90" maxlength="150" /></td>
                    </tr>
                    <tr>
                        <td class="dup_legend">Sistema</td>
                        <td class="dup_field"><?php echo $nome_sistema ?></td>
                    </tr>
                    <tr>
                        <td class="dup_legend">Data da criação</td>
                        <td class="dup_field"><?php echo $datacriacao ?></td>
                    </tr>
                    <tr>
                        <td class="dup_legend">Data da última atualização</td>
                        <td class="dup_field"><?php echo $dataatualizacao ?></td>
                    </tr>
                </table>


                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" value="Atualizar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

<?php include 'footer.php'; ?>