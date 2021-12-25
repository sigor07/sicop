<?php

setlocale( LC_ALL, 'pt_BR', 'ptb', 'pt-BR', 'pt-br', 'PT-BR', 'pt_BR.ISO-8859-1', 'pt_BR.utf-8', 'portuguese-brazil', 'bra' );
date_default_timezone_set( 'America/Sao_Paulo' );

include '/var/www/sicop/init/config.php';

require 'funcoes_init.php';
require 'funcoes.php';
include 'manipula_erro.php';

set_error_handler( 'manipuladorErros' );

set_time_limit( 0 );

$data_hora = date("d_m_Y_H_i_s");

$dir = 'bkdigital';

$return_cm1 = '';
$return_cm2 = '';
$return_cm3 = '';
$return_cm4 = '';

$command1 = "tar -zcf /$dir/digital_$data_hora.tar.gz /bd/digital/";
system( $command1, $return_cm1 );

// se a compactação foi feita... ( se $command1 retornou 0 - nenhum erro )
if ( empty( $return_cm1 ) ) {

	// comando para montar o diretório de backup
	$command2 = 'smbmount //' . SICOP_BACKUP_IP . "/$dir /mnt -o username=" . SICOP_BACKUP_USER . ',password=' . SICOP_BACKUP_PASS . "";

	system( $command2, $return_cm2 );

	// se o diretório foi montado... ( se $command2 retornou 0 - nenhum erro )
	if ( empty( $return_cm2 ) ) {

		// comando para copiar o os arquivos da pasta do servidor para a pasta de backup ( somente novos arquivos )
		$command3 = "cp -u /$dir/* /mnt/";

		// comando para desmontar o diretorio de backup
		$command4 = 'smbumount /mnt/';

		system( $command3, $return_cm3 );
		system( $command4, $return_cm4 );

	}

}

// gerar a mensagem q será salva no log
$msg = sysmsg::create_msg();
$msg->add_chaves( 'BACKUP DE DIGITAL REALIZADO', 0, 1 );
$msg->set_msg( 'Backup de digital realizada com sucesso' );

if ( !empty( $return_cm1 ) or !empty( $return_cm2 ) or !empty( $return_cm3 ) or !empty( $return_cm4 ) ) {

    $msg->set_msg_type( SM_TYPE_ERR );
    $msg->add_quebras( 1 );
    $msg->set_msg( 'Falha ao realizar backup de digital.' );

}

if ( !empty( $return_cm1 )  ) {

    $msg->add_chaves( 'FALHA DO COMNADO 1', 2, 1 );
    $msg->set_msg( 'Falha no comando de compactação.' );
    $msg->add_chaves( 'RETORNO DO COMANDO', 2, 1 );
    $msg->set_msg( $return_cm1 );

}

if ( !empty( $return_cm2 )  ) {

    $msg->add_chaves( 'FALHA DO COMNADO 2', 2, 1 );
    $msg->set_msg( 'Falha no comando de montagem do diretório de backup.' );
    $msg->add_chaves( 'RETORNO DO COMANDO', 2, 1 );
    $msg->set_msg( $return_cm2 );

}

if ( !empty( $return_cm3 )  ) {

    $msg->add_chaves( 'FALHA DO COMNADO 3', 2, 1 );
    $msg->set_msg( 'Falha no comando de cópia.' );
    $msg->add_chaves( 'RETORNO DO COMANDO', 2, 1 );
    $msg->set_msg( $return_cm3 );

}

if ( !empty( $return_cm4 )  ) {

    $msg->add_chaves( 'FALHA DO COMNADO 4', 2, 1 );
    $msg->set_msg( 'Falha no comando de desmontagem do diretório de backup.' );
    $msg->add_chaves( 'RETORNO DO COMANDO', 2, 1 );
    $msg->set_msg( $return_cm4 );

}

$msg->get_msg();


?>