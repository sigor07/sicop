<?php

setlocale( LC_ALL, 'pt_BR', 'ptb', 'pt-BR', 'pt-br', 'PT-BR', 'pt_BR.ISO-8859-1', 'pt_BR.utf-8', 'portuguese-brazil', 'bra' );
date_default_timezone_set( 'America/Sao_Paulo' );

include '/var/www/sicop/init/config.php';

require 'funcoes_init.php';
require 'funcoes.php';
include 'manipula_erro.php';

set_error_handler( 'manipuladorErros' );

set_time_limit( 0 );


$dbname = SICOP_DB;
$dbuser = SICOP_DB_USER;
$dbpass = SICOP_DB_PASS;

$data_hora = date( 'd_m_Y_H_i_s' );
$backupFile = $dbname . '_' . "$data_hora.sql";
$backupPath = "/bkmysql/$backupFile";

$return_cm1 = '';
$return_cm2 = '';
$return_cm3 = '';
$return_cm4 = '';
$return_cm5 = '';

// comando para fazer o dump da base de dados
$command1 = "mysqldump --opt --databases $dbname -u $dbuser -p$dbpass > $backupPath";

// comando para compactar o dump feito
$command2 = "gzip $backupPath";

system( $command1, $return_cm1 );
system( $command2, $return_cm2 );

// se o dump e a compactação foram feitos... ( se $command1 e $command2 retornaram 0 - nenhum erro )
if ( empty( $return_cm1 ) and empty( $return_cm2 ) ) {

    // forçar a desmontagem do diretório de backup
    system( 'smbumount /mnt/' );

    // comando para montar o diretório de backup
    $command3 = 'smbmount //' . SICOP_BACKUP_IP . '/bkmysql /mnt -o username=' . SICOP_BACKUP_USER . ',password=' . SICOP_BACKUP_PASS;

    system( $command3, $return_cm3 );

    // se o diretório foi montado... ( se $command3 retornou 0 - nenhum erro )
    if ( empty( $return_cm3 ) ) {

        // comando para copiar o os arquivos da pasta do servidor para a pasta de backup ( somente novos arquivos )
        $command4 = 'cp -u /bkmysql/* /mnt/';

        // comando para desmontar o diretorio de backup
        $command5 = 'smbumount /mnt/';

        system( $command4, $return_cm4 );
        system( $command5, $return_cm5 );
    }
}

// gerar a mensagem q será salva no log
$msg = sysmsg::create_msg();
$msg->add_chaves( 'BACKUP DO BANCO DE DADOS REALIZADO', 0, 1 );
$msg->set_msg( 'Backup do banco de dados realizado com sucesso' );

if ( !empty( $return_cm1 ) or !empty( $return_cm2 ) or !empty( $return_cm3 ) or !empty( $return_cm4 ) or !empty( $return_cm5 ) ) {

    $msg->set_msg_type( SM_TYPE_ERR );
    $msg->add_quebras( 1 );
    $msg->set_msg( 'Falha ao realizar backup do banco de dados.' );

}


if ( !empty( $return_cm1 )  ) {

    $msg->add_chaves( 'FALHA DO COMNADO 1', 2, 1 );
    $msg->set_msg( 'Falha no comando de dump do banco.' );
    $msg->add_chaves( 'RETORNO DO COMANDO', 2, 1 );
    $msg->set_msg( $return_cm1 );

}

if ( !empty( $return_cm2 )  ) {

    $msg->add_chaves( 'FALHA DO COMNADO 2', 2, 1 );
    $msg->set_msg( 'Falha no comando de compactação.' );
    $msg->add_chaves( 'RETORNO DO COMANDO', 2, 1 );
    $msg->set_msg( $return_cm2 );

}

if ( !empty( $return_cm3 )  ) {

    $msg->add_chaves( 'FALHA DO COMNADO 3', 2, 1 );
    $msg->set_msg( 'Falha no comando de montagem do diretório de backup.' );
    $msg->add_chaves( 'RETORNO DO COMANDO', 2, 1 );
    $msg->set_msg( $return_cm3 );

}

if ( !empty( $return_cm4 )  ) {

    $msg->add_chaves( 'FALHA DO COMNADO 4', 2, 1 );
    $msg->set_msg( 'Falha no comando de cópia.' );
    $msg->add_chaves( 'RETORNO DO COMANDO', 2, 1 );
    $msg->set_msg( $return_cm4 );

}

if ( !empty( $return_cm5 )  ) {

    $msg->add_chaves( 'FALHA DO COMNADO 5', 2, 1 );
    $msg->set_msg( 'Falha no comando de desmontagem do diretório de backup.' );
    $msg->add_chaves( 'RETORNO DO COMANDO', 2, 1 );
    $msg->set_msg( $return_cm5 );

}

$msg->get_msg();

?>