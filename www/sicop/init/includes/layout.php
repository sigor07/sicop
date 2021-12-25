<?php

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
            `dataadd`,
            DATE_FORMAT(`dataadd`, '%d/%m/%Y às %H:%i') AS dataadd_f,
            `dataup`,
            DATE_FORMAT(`dataup`, '%d/%m/%Y às %H:%i') AS dataup_f
          FROM
            `sicop_unidade`
          WHERE
            `idup` = 1
          LIMIT 1";

$db = SicopModel::getInstance();
$query = $db->query( $query );
$db->closeConnection();

$dados = $query->fetch_assoc();

$secretaria      = $dados['secretaria'];
$coordenadoria   = $dados['coord'];
$unidadecurto    = $dados['unidade_sort'];
$unidadelongo    = $dados['unidade_long'];
$endereco        = $dados['endereco'];
$endereco_sort   = $dados['endereco_sort'];
$cidade          = $dados['cidade'];
$email           = $dados['email'];
$titulo          = $dados['nome_sistema'];
$datacriacao     = $dados['dataadd_f'];
$dataatualizacao = $dados['dataup_f'];

?>
