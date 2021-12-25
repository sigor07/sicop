<?php

//function __autoload( $class_name ) {
//    require_once 'classes/class.' . $class_name . '.php';
//}


//class load_class {
//
//    public static function loader( $classe ) {
//        spl_autoload_extensions( '.class.php, .php' );
//        spl_autoload( 'classes/' . $classe );
//    }
//
//}
//
//spl_autoload_register( array( 'load_class', 'loader' ) );


include 'init/config.php';

set_time_limit(0);

$db = SicopModel::getInstance();



$q_select = 'SELECT `cod_detento`, `data_aud`, `hora_aud`
             FROM `audiencia`
             WHERE
             `cod_detento` = 3563
             AND
             `data_aud` = "2009-09-14"';

$q_insert = 'INSERT INTO `audiencia`
             (`cod_detento`, `data_aud`, `hora_aud`, `local_aud`, `cidade_aud`, `tipo_aud`, `num_processo`, `sit_aud`, `user_add`)
             VALUES
             (3563, "2009-09-14", "15:00:00", "2 VARA CRIMINAL", "GUARARAPES - SP", 1, "195/2009", 11, 1)';

$q_delet  = 'DELETE
             FROM `audiencia`
             WHERE
             `cod_detento` = 3563
             AND
             `data_aud` = "2009-09-14"';

$querytime_before = array_sum( explode( ' ', microtime() ) );



for ( $i = 0; $i < 10; $i++ ) {
	
	$db->query( $q_select );

    //$db->query( $q_insert );

}

// pega o tempo depois da execução da query
$querytime_after = array_sum( explode( ' ', microtime() ) );

// calcula o tempo de execução da query
$querytime = $querytime_after - $querytime_before;

//$db->query( $q_delet );

$db->closeConnection();


echo $querytime;
exit;


//include 'classes/class.sysmsg.php';
//include 'db.php';
include 'funcoes_init.php';

//$msg = new sysmsg();
//$msg->create_msg();

$msg = sysmsg::create_msg();
$msg->set_msg_type( SM_TYPE_ERR );
$msg->add_quebras( 2 );
$msg->set_msg( 'davi' );
$msg->set_msg( 'davi' );
$msg->set_msg( 'davi' );
$msg->set_msg_pre_def( SM_QUERY_NO_RESULT );
$msg->add_chaves( 'afadsf ads asdf', 1, 1 );
$msg->add_chaves( 'afadsf ads asdf', 0, 0 );
$msg->add_parenteses( 'afadsf ads asdf' );
$a = $msg->get_msg();
echo $a . '<br />';


$msg = sysmsg::create_msg();
$msg->set_msg_type( SM_TYPE_ATEN );
$msg->set_msg_pre_def( SM_NO_PERM );
$msg->add_parenteses( 'afadsf ads asdf' );
$a = $msg->get_msg();
echo $a . '<br />';

//	class MinhaClasse
//	{
//		public static $propriedade = "Minha propriedade estática <br />";
//
//		public static function MetodoEstatico()
//		{
//			echo "Método Estático";
//		}
//	}
//	//Acessando as propriedades estáticas
//	echo MinhaClasse::$propriedade;
//	//Acessando os métodos estáticos
//	MinhaClasse::MetodoEstatico();


?>
