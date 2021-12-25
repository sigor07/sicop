<?php

require "init/db.php";
require "init/funcoes_init.php";

$q = "SELECT
			`visitas`.`rg_visit`
		FROM
			`visitas`
		where
			not isnull (`rg_visit`)
		GROUP BY
			`rg_visit`
		HAVING
			COUNT(*) > 1";

$db = SicopModel::getInstance();
$q = $db->query( $q );

$d = '';
$r = array( );
while ( $d = $q->fetch_assoc() ) {
    $r[] = $d['rg_visit'];
}

echo implode( ',', $r);
exit;

$q_foto_g = 'SELECT `foto_det_g` FROM `det_fotos`';
$q_foto_p = 'SELECT `foto_det_p` FROM `det_fotos`';
$q_det = 'SELECT `iddetento`, `matricula` FROM `detentos`';


$db = SicopModel::getInstance();

$q_foto_g = $db->query( $q_foto_g );
$q_foto_p = $db->query( $q_foto_p );
$q_det = $db->query( $q_det );

$fotos_g = array( );
$fotos_p = array( );
$dets = array( );

while ( $d_det = $q_foto_g->fetch_assoc() ) {
    $fotos_g[] = $d_det['foto_det_g'];
}

while ( $d_det = $q_foto_p->fetch_assoc() ) {
    $fotos_p[] = $d_det['foto_det_p'];
}

while ( $d_det = $q_det->fetch_assoc() ) {
    $iddet = $d_det['iddetento'];
    $dets["$iddet"] = $d_det['matricula'];
}

// pega o endereço do diretório
$diretorio = '../sicop_pics/detentos/'; //getcwd();
// abre o diretório
$ponteiro = opendir( $diretorio );
// monta os vetores com os itens encontrados na pasta
while ( $nome_itens = readdir( $ponteiro ) ) {
    $itens[] = $nome_itens;
}



// ordena o vetor de itens
sort( $itens );
$arquivos = array( );
$i = 0;
$where = '';

$a = 0;
if ( !empty( $_GET['a'] ) ) {
    $a = $_GET['a'];
}


$dir = '/var/www/sicop_pics/detentos/';

// percorre o vetor para fazer a separacao entre arquivos e pastas
foreach ( $itens as $listar ) {
    // retira "./" e "../" para que retorne apenas pastas e arquivos
    if ( $listar != '.' && $listar != '..' ) {

        // checa se o tipo de arquivo encontrado é uma pasta
        if ( !is_dir( $listar ) ) {

            if ( $a < 5 ) {

                if ( !in_array( $listar, $fotos_g ) and !in_array( $listar, $fotos_p ) ) {

                    switch ( $a ) {

                        case 1:

                            /*
                             * espaço no nome
                             */
                            $matr = strstr( $listar, ' ', true );

                            $id = '';
                            if ( !empty( $matr ) ) {
                                $id = array_search( $matr, $dets );
                            }

                            if ( !empty( $id ) ) {
                                $arquivos[] = $listar . ' ' . $matr . ' ' . $id;
                                $where .= "($id, '$listar', NULL, 1, '2009-01-01 08:00:00', '10.14.217.71'),<br />";
                                $i++;
                            }
                            break;

                        case 2:

                            /*
                             * _p
                             */
                            $p = '';
                            $matr = '';
                            $listar_g = strstr( $listar, '_p', true );


                            if ( !empty( $listar_g ) ) {
                                $matr = strstr( $listar, '_', true );
                            }

                            $id = '';
                            if ( !empty( $matr ) ) {
                                $id = array_search( $matr, $dets );
                            }

                            if ( !empty( $id ) ) {
                                $arquivos[] = $listar . ' ' . $matr . ' ' . $id;
                                $where .= "($id, '$listar_g.jpg', '$listar', 1, '2009-01-01 08:00:00', '10.14.217.71'),<br />";
                                $i++;
                            }
                            break;
                        case 3:

                            /*
                             * so a matricula
                             */
                            $espaço = strstr( $listar, ' ' );
                            $under  = strstr( $listar, '_' );

                            if ( empty( $espaço ) and empty( $under ) ) {

                                $matr = strstr( $listar, '.', true );
                                $id = '';
                                if ( !empty( $matr ) ) {
                                    $id = array_search( $matr, $dets );
                                }

                                if ( !empty( $id ) ) {
                                    $arquivos[] = $listar . ' ' . $matr . ' ' . $id;
                                    $where .= "($id, '$listar', NULL, 1, '2009-01-01 08:00:00', '10.14.217.71'),<br />";
                                    $i++;
                                }

                            }
                            break;
                        case 4:

                            /*
                             * deletar o que sobrou
                             */
                            unlink( $dir . $listar );
                            break;

                        default:
                            $arquivos[] = $listar;
                            $i++;
                            break;

                    }// / switch ( $a ) {

                }

            } else {

                switch ( $a ) {
                    case 5:

                        /*
                         * renomear
                         */
                        $new_listar = preg_replace( '/[ -]/', '_', $listar );

                        if ( $new_listar != $listar ) {
                            rename($dir . $listar, $dir . $new_listar);
                            $arquivos[] = $listar . ' --> ' . $new_listar;
                            $i++;
                        }
                        break;

                    default:
                        $arquivos[] = $listar;
                        $i++;
                        break;

                }


            }


        }
    }
}


// lista os arquivos se houverem
if ( $arquivos != '' ) {
    foreach ( $arquivos as $listar ) {
        //print " Arquivo: <a href='$listar'>$listar</a><br>";
        echo $listar . '<br />';
    }
}

echo $i . '<br />';
echo $where;
?>
