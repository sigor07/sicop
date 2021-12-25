<?php

setlocale( LC_ALL, 'pt_BR', 'ptb', 'pt-BR', 'pt-br', 'PT-BR', 'pt_BR.ISO-8859-1', 'pt_BR.utf-8', 'portuguese-brazil', 'bra' );
date_default_timezone_set( 'America/Sao_Paulo' );

include '/var/www/sicop/init/config.php';

require 'funcoes_init.php';
require 'funcoes.php';
include 'manipula_erro.php';

set_error_handler( 'manipuladorErros' );

set_time_limit( 0 );

/*

ARQUIVO PARA EXECUÇÃO DE BACKUP ROTATE NO SERVIDOR E NA PASTA DE BACKUP

MANTEM OS ARQUIVOS 3 DIAS NO SERVIDOR 3 E 7 DIAS NA PASTA DE BACKUP

*/

$dias_sever = 3;  //dias que o backup permanece no servidor
$dias_backup = 7; //dias que o backup permanece na pasta de backup

$dir = 'bkmysql';

// comando para apagar os arquivos do servidor
// apaga todos os arquivos que tenha + de 3 dias da data da última modificação
$command1 = "find /$dir -mtime +$dias_sever -exec rm -f {} \;";
system( $command1, $return_cm1 );

// comando para montar o diretório de backup
$command2 = 'smbmount //' . SICOP_BACKUP_IP . "/$dir /mnt -o username=" . SICOP_BACKUP_USER . ',password=' . SICOP_BACKUP_PASS;
system( $command2, $return_cm2 );

// se o diretório foi montado... ( se $command2 retornou 0 - nenhum erro )
if ( empty( $return_cm2 ) ) {

	// comando para apagar os arquivos da pasta de backup
	// apaga todos os arquivos que tenha + de $dias_backup dias da data da última modificação
	$command3 = "find /mnt -mtime +$dias_backup -exec rm -f {} \;";
	system( $command3, $return_cm3 );

	// comando para desmontar o diretorio de backup
	$command4 = 'smbumount /mnt/';
	system( $command4, $return_cm4 );

}

?>