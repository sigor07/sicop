<?php

/**
 * Parametros de configuração do sistema
 * Data 01/01/2012
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

/**
 *
 * em andamento:
 *
 * alteração nas visitas, passando a relação com os detentos de 1:N  para N:N;
 *
 * registro da lista da casa diária para posterior consulta, como por exemplo quem estava na cela em determinada data;
 *
 * armazenamento da configurações no banco/sessions, ao invés de constantes;
 *
 * alteração do modo de armazenamento dos diretores;
 *
 *
 *
 * histórico de alterações
 *
 * 27/fev/2012
 * todas as conexões com o banco foram orientadas a objeto e direcionadas a um unico
 * arquivo
 *
 * ** 09/fev/2012
 * criação das constantes para armazenamento dos códigos da situação do detento, e alterado os dados no sistema
 * SICOP_SIT_DET_NA
 * SICOP_SIT_DET_TRANA
 * SICOP_SIT_DET_TRADA
 * SICOP_SIT_DET_TRANADA
 * SICOP_SIT_DET_TRANSF
 * SICOP_SIT_DET_EXCLUIDO
 * SICOP_SIT_DET_EVADIDO
 * SICOP_SIT_DET_FALECIDO
 * SICOP_SIT_DET_ACEHGAR
 *
 *
 * ** jan/2012
 * alteração do diretório int/ para init/;
 *
 *
 */

if ( !isset( $_SESSION ) ) session_start();

setlocale( LC_ALL, 'pt_BR', 'ptb', 'pt-BR', 'pt-br', 'PT-BR', 'pt_BR.ISO-8859-1', 'pt_BR.utf-8', 'portuguese-brazil', 'bra' );
date_default_timezone_set( 'America/Sao_Paulo' );

/**
 * nome do sistema
 */
define( 'SICOP_SYS_NAME', 'SICOP - Sistema de Controle Prisional' );

/**
 * criador do sistema
 */
define( 'SICOP_SYS_AUTHOR', 'SICOP - Sistema de Controle Prisional' );

/**
 * descrição do sistema
 */
define( 'SICOP_SYS_KW', 'SICOP - Sistema de Controle Prisional - Desenvolvido por José Rafael Gonçalves - CDP de São José do Rio Preto - SP' );


/**
 * document root
 */
$doc_root = !empty( $_SERVER['DOCUMENT_ROOT'] ) ? $_SERVER['DOCUMENT_ROOT'] : '/var/www';
define( 'SICOP_DOC_ROOT', $doc_root );

/**
 * pasta do sistema
 */
define( 'SICOP_SYS_FOLDER', 'sicop' );

/**
 * caminho absoluto
 */
define( 'SICOP_ABS_PATH', '/' . SICOP_SYS_FOLDER . '/' );

/**
 * define o caminho de inclusão global
 */

//define( 'SICOP_INCL_PATH', SICOP_DOC_ROOT . SICOP_ABS_PATH . 'init/includes/' );
define( 'SICOP_INCL_PATH', SICOP_DOC_ROOT . SICOP_ABS_PATH );
$path_pieces = array(
    SICOP_INCL_PATH . '_controller/',
    SICOP_INCL_PATH . '_model/',
    SICOP_INCL_PATH . '_view/',
    SICOP_INCL_PATH . 'init/includes/'
);
$paths = implode( PATH_SEPARATOR, $path_pieces );
set_include_path( get_include_path() . PATH_SEPARATOR . $paths );

require 'manipulaErro.php';
set_error_handler( 'manipuladorErros' );

/**
 * caminho da pasta dos arquivos de configuração
 */
define( 'SICOP_INIT_PATH',  SICOP_INCL_PATH . 'init/' );
/**
 * nome da pasta das imagens
 */
define( 'SICOP_IMG_FOLDER_NAME', 'sicop_pics' );

/**
 * pasta das imagens
 */
define( 'SICOP_IMG_FOLDER', SICOP_DOC_ROOT . '/' . SICOP_IMG_FOLDER_NAME );

/**
 * nome da pasta de fotos dos detentos
 */
define( 'SICOP_DET_FOLDER_NAME', 'detentos' );


/**
 * pasta das fotos dos detentos
 */
define( 'SICOP_DET_FOLDER', SICOP_DOC_ROOT . '/' . SICOP_IMG_FOLDER_NAME . '/' . SICOP_DET_FOLDER_NAME . '/' );

/**
 * nome da pasta de fotos dos visitantes
 */
define( 'SICOP_VISIT_FOLDER_NAME', 'visitas' );


/**
 * pasta das fotos dos visitantes
 */
define( 'SICOP_VISIT_FOLDER', SICOP_DOC_ROOT . '/' . SICOP_IMG_FOLDER_NAME . '/' . SICOP_VISIT_FOLDER_NAME . '/' );

/**
 * caminho para as fotos
 */
define( 'SICOP_IMG_PATH', '/' . SICOP_IMG_FOLDER_NAME . '/' );

/**
 * caminho para as fotos do detento
 */
define( 'SICOP_DET_IMG_PATH', SICOP_IMG_PATH . SICOP_DET_FOLDER_NAME . '/' );

/**
 * caminho para as fotos do visitante
 */
define( 'SICOP_VISIT_IMG_PATH', SICOP_IMG_PATH . SICOP_VISIT_FOLDER_NAME . '/' );

/**
 * caminho para as imagens do sistema
 */
define( 'SICOP_SYS_IMG_PATH', SICOP_IMG_PATH . 'system' . '/' );

/**
 * caminho para a pasta send
 */
define( 'SICOP_SEND_PATH', SICOP_ABS_PATH . 'send/' );



/**
 * ---------------------------------------------------------
 *  SEXO E DESCRIÇÃO DO DETENTO
 * ---------------------------------------------------------
 */

/**
 * definindo o sexo dos presos da unidade
 * utilizar 'o' para masculino ou 'a' para feminino
 */
$artigo_lower        = 'o';
$artigo_upper        = mb_strtoupper( $artigo_lower );

$pronome_lower       = 'este';
$sexo                = 'M';
$sexo_full           = 'MASC';

if ( $artigo_lower == 'a' ) {
    $pronome_lower = 'esta';
    $sexo          = 'F';
    $sexo_full     = 'FEM';
}

$promone_upper       = mb_strtoupper( $pronome_lower );
$promone_first_upper = mb_convert_case ( $pronome_lower, MB_CASE_TITLE );

$desc_lower          = 'detent' . $artigo_lower;
$desc_upper          = mb_strtoupper( $desc_lower );
$desc_first_upper    = mb_convert_case ( $desc_lower, MB_CASE_TITLE );

/**
 * artigo para as descrições minúsculo
 */
define( 'SICOP_DET_ART_L', $artigo_lower );

/**
 * artigo para as descrições MAIÚSCULO
 */
define( 'SICOP_DET_ART_U', $artigo_upper );

/**
 * descrição minúsculo
 */
define( 'SICOP_DET_DESC_L', $desc_lower );

/**
 * descrição MAIÚSCULO
 */
define( 'SICOP_DET_DESC_U', $desc_upper );

/**
 * descrição com a primeira letra em Maiúsculo
 */
define( 'SICOP_DET_DESC_FU', $desc_first_upper );


/**
 * pronome minúsculo
 */
define( 'SICOP_DET_PRON_L', $pronome_lower );

/**
 * pronome MAIÚSCULO
 */
define( 'SICOP_DET_PRON_U', $promone_upper );

/**
 * pronome com a primeira letra em Maiúsculo
 */
define( 'SICOP_DET_PRON_FU', $promone_first_upper );

/**
 * sexo com uma letra ( M OU F )
 */
define( 'SICOP_DET_SEX', $sexo );

/**
 * sexo com 4 letras ( MASC OU FEM )
 */
define( 'SICOP_DET_SEX_F', $sexo_full );

/**
 * ---------------------------------------------------------
 */

/**
 * ---------------------------------------------------------
 *  CONFIGURAÇÕES DE RAIO E CELA
 * ---------------------------------------------------------
 */

/**
 * nomenclatura do raio
 */
define( 'SICOP_RAIO', 'Raio' );

/**
 * nomenclatura da cela
 */
define( 'SICOP_CELA', 'Cela' );

/**
 * nomenclatura do raio abreviado
 */
define( 'SICOP_RAIO_AB', 'R' );

/**
 * nomenclatura da cela abreviada
 */
define( 'SICOP_CELA_AB', 'C' );

/**
 * ---------------------------------------------------------
 */



/**

COMO É
11 = NA CASA
112 = TRANSITO NA CASA
113 = TRANSITO DA CASA
114 = TRANSITO NA CASA DA CASA

12 = TRANSFERIDO
13 = EXCLUIDO (ALVARA)
14 = EVADIDO
15 = FALECIDO
'' = A CHEGAR

COMO VAI FICAR
1 = NA CASA
2 = TRANSITO NA CASA
3 = TRANSITO DA CASA
4 = TRANSITO NA CASA DA CASA

5 = TRANSFERIDO
6 = EXCLUIDO (ALVARA)
7 = EVADIDO
8 = FALECIDO
'' = A CHEGAR



 */


/**
 * ---------------------------------------------------------
 *  CÓDIGOS DA SITUAÇÃO DO DETENTO
 * ---------------------------------------------------------
 */
/**
 * código da situação do detento - NA CASA
 */
define( 'SICOP_SIT_DET_NA', 11 );

/**
 * código da situação do detento - TRÂNSITO NA CASA
 */
define( 'SICOP_SIT_DET_TRANA', 112 );

/**
 * código da situação do detento - TRÂNSITO DA CASA
 */
define( 'SICOP_SIT_DET_TRADA', 113 );

/**
 * código da situação do detento - TRÂNSITO NA CASA DA CASA
 */
define( 'SICOP_SIT_DET_TRANADA', 114 );

/**
 * código da situação do detento - TRÂNSFERIDO
 */
define( 'SICOP_SIT_DET_TRANSF', 12 );

/**
 * código da situação do detento - EXCLUÍDO
 */
define( 'SICOP_SIT_DET_EXCLUIDO', 13 );

/**
 * código da situação do detento - EVADIDO
 */
define( 'SICOP_SIT_DET_EVADIDO', 14 );

/**
 * código da situação do detento - FALECIDO
 */
define( 'SICOP_SIT_DET_FALECIDO', 15 );

/**
 * código da situação do detento - À CHEGAR
 */
define( 'SICOP_SIT_DET_ACEHGAR', '' );

/**
 * ---------------------------------------------------------
 */


/**
 * ---------------------------------------------------------
 *  CONFIGUAÇÕES DE BACKUP
 * ---------------------------------------------------------
 */

/**
 * IP do servidor de backup
 */
define( 'SICOP_BACKUP_IP', '172.16.116.242' );

/**
 * usuário para a pasta do servidor de backup
 */
define( 'SICOP_BACKUP_USER', 'root' );

/**
 * senha para a pasta do servidor de backup
 */
define( 'SICOP_BACKUP_PASS', '11aza@72' );

/**
 * ---------------------------------------------------------
 */


/**
 * ---------------------------------------------------------
 *  CONFIGURAÇÕES DA CONEXÃO COM O BANCO DE DADOS
 * ---------------------------------------------------------
 */

/**
 * IP do servidor do banco de dados
 */
define( 'SICOP_DB_SERVER', 'localhost' );
/**
 * usuário para do banco de dados
 */
define( 'SICOP_DB_USER', 'root' );

/**
 * senha para o banco de dados
 */
define( 'SICOP_DB_PASS', '11aza@72' );

/**
 * banco de dados usado pelo sistema
 */
define( 'SICOP_DB', 'bd_pjunq' );

/**
 * ---------------------------------------------------------
 */

/**
 * classe para auto-include dos arquivos das classes
 * @param string $class o nome da classe
 */
class load_class {

    public static function loader( $class ) {

        $class = strtolower( $class );

        spl_autoload( 'classes/' . $class );

    }

    public static function loader_model_control( $class ) {

        $class = strtolower( $class );

        spl_autoload( $class );

    }

}

/**
 * definindo o autoloader de classes
 */
spl_autoload_register( array( 'load_class', 'loader' ) );
spl_autoload_register( array( 'load_class', 'loader_model_control' ) );
spl_autoload_extensions( '.class.php,.php' );

//echo get_include_path();
//print_r( spl_autoload_extensions() );

//exit;


//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//
//echo SICOP_INIT_PATH;
//exit;
//
//include 'funcoes.php';
//include 'funcoes_init.php';
//
//$a[] = 333;
//$a[] = 444;
//
//$_SESSION['user_list'] = $a;
//
//$lista = montalista::create_list(  );
////$lista->get_lista_atual();
//$lista->add( 1 );
//$lista->add( 2 );
//
//echo '<br/>' . $lista->get_str();
//
//$lista->add( 3 );
//$lista->add( 4 );
//
//echo '<br/>' . $lista->get_str();
//
//$lista->rem( 3 );
//$lista->rem( 444 );
//
//echo '<br/>' . $lista->get_str();
//
//print_r( $_SESSION['user_list'] );

//echo $doc_root;
//exit;
?>
