<?php

if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';


$n_admsist   = get_session( 'n_admsist', 'int' );
$n_admsist_n = 3;

$motivo_pag = 'DETALHES DA ÚNIDADE PRISIONAL';

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

$query = "SELECT
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
              DATE_FORMAT( `dataadd`, '%d/%m/%Y às %H:%i') AS `dataadd_f`,
              DATE_FORMAT( `dataup`, '%d/%m/%Y às %H:%i') AS `dataup_f`
            FROM
              `sicop_unidade`
            WHERE
              `idup` = 1
            LIMIT 1";

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
    $mensagem = "A consulta retornou 0 ocorrencias ( DETALHES DA UNIDADE ).\n\n Página $pag";
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


require 'cab.php';
$trail = new Breadcrumb();
$trail->add('Detalhes da Unidade', $_SERVER['PHP_SELF'], 2);
$trail->output();
?>

            <p class="descript_page">DADOS DA UNIDADE PRISIONAL</p>

            <p class="link_common"><a href="editup.php">Alterar</a></p>

            <table class="detal_up">
                <tr class="even">
                    <td class="dup_legend">Secretaria</td>
                    <td class="dup_field"><?php echo $secretaria?></td>
                </tr>
                <tr class="even">
                    <td class="dup_legend">Coordenadoria</td>
                    <td class="dup_field"><?php echo $coord?></td>
                </tr>
                <tr class="even">
                    <td class="dup_legend">Nome da unidade completo</td>
                    <td class="dup_field"><?php echo $unidade_long?></td>
                </tr>
                <tr class="even">
                    <td class="dup_legend">Nome da unidade abreviado</td>
                    <td class="dup_field"><?php echo $unidade_sort?></td>
                </tr>
                <tr class="even">
                    <td class="dup_legend">Endereço da unidade completo</td>
                    <td class="dup_field"><?php echo $endereco?></td>
                </tr>
                <tr class="even">
                    <td class="dup_legend">Endereço da unidade abreviado</td>
                    <td class="dup_field"><?php echo $endereco_sort?></td>
                </tr>
                <tr class="even">
                    <td class="dup_legend">Cidade</td>
                    <td class="dup_field"><?php echo $cidade?></td>
                </tr>
                <tr class="even">
                    <td class="dup_legend">Email</td>
                    <td class="dup_field"><?php echo $email?></td>
                </tr>
                <tr class="even">
                    <td class="dup_legend">Sistema</td>
                    <td class="dup_field"><?php echo $nome_sistema?></td>
                </tr>
                <tr class="even">
                    <td class="dup_legend">Data da criação</td>
                    <td class="dup_field"><?php echo $datacriacao?></td>
                </tr>
                <tr class="even">
                    <td class="dup_legend">Data da última atualização</td>
                    <td class="dup_field"><?php echo $dataatualizacao?></td>
                </tr>
            </table>

<?php include 'footer.php'; ?>