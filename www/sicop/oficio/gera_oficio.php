<?php

/**
 * descrição do arquivo
 *
 * @author Rafael
 * @since 30/03/2012
 *
 * ****************************************************************************
 *
 * SICOP - Sistema de Controle de Prisional
 *
 * Sistema para controle e gerenciamento de unidades prisionais
 *
 * @author  JOSÉ RAFAEL GONÇALVES - AGENTE DE SEGURANÇA III
 * @local   CENTRO DE DETENÇÃO PROVISÓRIA DE SÃO JOSÉ DO RIO PRETO - SP
 * @since   03/01/2011
 *
 * ****************************************************************************
 */
require '../init/config.php';

// instanciando a classe
$user = new userAutController();

// checando se o sistema esta ativo
$user->ckSys();

// validando o usuário e o nível de acesso
$user->validateUser( 'n_adm', 2 );

$modeloOf = new oficio();




// gravando o acesso no log
$pag = $user->linkPag();
$mensagem = "Acesso à página $pag";
$user->salvaLog( $mensagem );

// instanciado o view
$view = new SicopView();

// adicionando o javascript
$view->setJS( 'ajax/js_handle_model_doc' );


// título da página e escrevendo o header
$desc_pag = 'Gerar ofício';
echo $view->getHeader( $desc_pag, 'C' );

require 'menu.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>







<?php include 'footer.php'; ?>