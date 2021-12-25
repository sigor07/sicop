<?php

/**
 * menu de acesso
 * Data 10/02/2012
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

$usuario      = SicopController::getSession( 'nome_cham' );

$n_portaria   = SicopController::getSession( 'n_portaria', 'int' );
$n_chefia     = SicopController::getSession( 'n_chefia', 'int' );
$n_rol        = SicopController::getSession( 'n_rol', 'int' );
$n_cadastro   = SicopController::getSession( 'n_cadastro', 'int' );
$n_pront      = SicopController::getSession( 'n_pront', 'int' );
$n_incl       = SicopController::getSession( 'n_incl', 'int' );
$n_peculio    = SicopController::getSession( 'n_peculio', 'int' );
$n_saude      = SicopController::getSession( 'n_saude', 'int' );
$n_seg        = SicopController::getSession( 'n_seg', 'int' );
$n_sedex      = SicopController::getSession( 'n_sedex', 'int' );
$n_inteli     = SicopController::getSession( 'n_inteli', 'int' );
$n_sind       = SicopController::getSession( 'n_sind', 'int' );
$n_adm        = SicopController::getSession( 'n_adm', 'int' );
$n_admsist    = SicopController::getSession( 'n_admsist', 'int' );
$n_msg        = SicopController::getSession( 'n_msg', 'int' );
$n_prot       = SicopController::getSession( 'n_prot', 'int' );
$n_prot_receb = SicopController::getSession( 'n_prot_receb', 'int' );
$n_bonde      = SicopController::getSession( 'n_bonde', 'int' );

$imp_chefia   = SicopController::getSession( 'imp_chefia', 'int' );
$imp_det      = SicopController::getSession( 'imp_det', 'int' );
$imp_cadastro = SicopController::getSession( 'imp_cadastro', 'int' );
$imp_rol      = SicopController::getSession( 'imp_rol', 'int' );
$imp_pront    = SicopController::getSession( 'imp_pront', 'int' );



?>


            <div id="menu_sup">

                <span id='menu_sys'>

                    <ul class="sf-menu">

                        <li class="current" id="sf_li_top">

                            <a class="sf_a_top link_menu_sup" href="javascript:void(0)">menu</a>
                            <ul>

                                <li>
                                    <a href="<?php echo SICOP_ABS_PATH; ?>contagem.php">Mapa populacional</a>
                                </li>
                                <?php if ( $n_admsist >= 4 ) { ?>
                                <li class="current">
                                    <a href="javascript:void(0)">Listas CNJ</a>
                                    <ul>
                                        <li><a href='javascript:void(0)' id="print_list_cnj" title="Imprimir a lista">Imprimir lista cnj</a></li>
                                        <li><a href='javascript:void(0)' id="print_list_cnj_rc" title="Imprimir a lista" >Imprimir lista cnj por raio e cela</a></li>
                                        <li><a href='javascript:void(0)' id="print_pop_cnj" title="Imprimir a lista" >Imprimir população cnj</a></li>
                                    </ul>
                                </li> <!-- /li class="current" - Listas CNJ -->
                                <?php } ?>
                                <li class="current">
                                    <a href="javascript:void(0)">Pesquisar</a>
                                    <ul>
                                        <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php">Pesquisar <?php echo SICOP_DET_DESC_L; ?>s</a></li>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>buscadetesp.php">Pesquisas especiais</a></li>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>listatel/buscalistatel.php">Lista de telefones</a></li>
                                        <?php if ( $n_admsist >= 2 ) { ?>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>fotos/view.php">Fotos por data</a></li>
                                        <?php } ?>
                                    </ul>
                                </li> <!-- /li class="current" - Pesquisar -->

                                <li class="current">
                                    <a href="javascript:void(0)">Cálculos de dígitos</a>
                                    <ul>
                                        <li class="current"><a href="javascript:void(0)" onclick="javascript: ow('<?php echo SICOP_ABS_PATH; ?>calc_d_matr.php', '600', '300'); return false" >Calcular dígito de matrícula</a></li>
                                        <li><a href="javascript:void(0)" onclick="javascript: ow('<?php echo SICOP_ABS_PATH; ?>calc_d_rg.php', '600', '300'); return false" >Calcular dígito de R.G.</a></li>
                                    </ul>
                                </li> <!-- /li class="current" - Cálculos de dígitos -->

                                <?php if ( $n_portaria >= 2 ) { ?>

                                <li class="current"><!-- portaria -->
                                    <a href="javascript:void(0)">Portaria</a>
                                    <ul>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>chefia/lista_ord_saida.php">Listar ordens de saída</a></li>
                                    </ul>
                                </li> <!-- /li class="current" - portaria -->

                                <?php } ?>

                                <?php if ( $n_incl >= 2 ) { ?>

                                <li class="current">

                                    <a href="javascript:void(0)">Inclusão</a>
                                    <ul>

                                        <li class="current">
                                            <a href="javascript:void(0)">TV e Rádio</a>
                                            <ul>

                                                <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>incl/listatv.php">Listar TVs</a></li>
                                                <?php if ( $n_incl >= 3 ) { ?>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=cadtv">Cadastrar TV</a></li>
                                                <?php } ?>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>incl/listaradio.php">Listar rádios</a></li>
                                                <?php if ( $n_incl >= 3 ) { ?>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=cadrd">Cadastrar rádio</a></li>
                                                <?php } ?>

                                            </ul>
                                        </li> <!-- /li class="current" -->

                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>incl/lista_incl.php">Listar <?php echo SICOP_DET_DESC_L; ?>s pela data de inclusão</a></li>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>peculio/lista_pert_conf.php">Pertences não confirmados</a></li>

                                    </ul>
                                </li> <!-- /li class="current" - Inclusão -->

                                <?php } // if ( $n_incl >= 2 ) ?>

                                <?php if ( $n_sedex >= 2 ) { ?>

                                <li class="current">

                                    <a href="javascript:void(0)">Sedex</a>
                                    <ul>
                                        <?php if ( $n_portaria >= 3 and $n_sedex >= 3 ) { ?>
                                        <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=cadsed">Cadastrar Sedex</a></li>
                                        <?php } ?>
                                        <?php if ( $n_portaria >= 2 ) { ?>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>sedex/lista_sedex.php?sit=1">Listar Sedex recebidos</a></li>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>sedex/lista_sedex.php?sit=3">Listar Sedex separados p/ devolução</a></li>
                                        <?php } ?>
                                        <?php if ( $n_incl >= 2 ) { ?>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>sedex/lista_sedex.php?sit=2">Listar Sedex encaminhados</a></li>
                                        <?php } ?>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=bsed">Pesquisar Sedex pel<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?></a></li>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>sedex/busca_sedex.php">Pesquisar Sedex pelo código</a></li>
                                    </ul>

                                </li> <!-- /li class="current" - Sedex -->

                                <?php } // if ( $n_sedex >= 2 ) ?>

                                <?php if ( $n_chefia >= 2 ) { ?>

                                <li class="current">

                                    <a href="javascript:void(0)">Chefia</a>
                                    <ul>

                                        <?php if ( $imp_chefia >= 1 or $imp_cadastro >= 1 ) { ?>
                                        <li class="current">
                                            <a href="javascript:void(0)">Listas</a>
                                            <ul>

                                                <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>gera_lista_v.php">Gerar lista para visita</a></li>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>exp_lista.php">Exportar lista</a></li>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>monta_lista_det.php?tipo_lista=export">Montar lista para exportar ou imprimir</a></li>

                                            </ul>
                                        </li> <!-- /li class="current" -->
                                        <?php } // if ( $imp_chefia >= 1 or $imp_cadastro >= 1 ) ?>

                                        <?php if ( $imp_det >= 1 ) { ?>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>monta_lista_det.php?tipo_lista=foto_det">Imprimir fotos de <?php echo SICOP_DET_DESC_L; ?>s</a></li>
                                        <?php } // if ( $imp_det >= 1 ) ?>

                                        <?php if ( $imp_chefia >= 1 ) {?>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=impterseg" title="Pesquisar <?php echo SICOP_DET_DESC_L; ?>s para impressão de termos do seguro" >Imprimir termos do seguro</a></li>
                                        <?php } // if ( $imp_chefia >= 1 ) ?>

                                        <?php if ( $n_chefia >= 3 or $n_cadastro >= 3 ) { ?>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>detento/cadastradet.php">Cadastrar <?php echo SICOP_DET_DESC_L; ?></a></li>
                                        <?php } // if ( $n_chefia >= 3 or $n_cadastro >= 3 ) ?>

                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>chefia/lista_ord_saida.php">Listar ordens de saída</a></li>

                                    </ul>
                                </li> <!-- /li class="current" - chefia -->

                                <?php } // if ( $n_chefia >= 2 ) ?>

                                <?php if ( $n_peculio >= 2 ) { ?>
                                <li class="current">

                                    <a href="javascript:void(0)">Pecúlio</a>
                                    <ul>

                                        <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=bpec">Pesquisar pertences</a></li>
                                        <?php if ( $n_peculio >= 3 ) { ?>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>peculio/lista_pert_conf.php">Pertences não confirmados</a></li>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=cadpec">Cadastrar pertence</a></li>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>adm/numreq.php">Solicitar números para requisição de passagens</a></li>
                                        <?php } ?>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>chefia/lista_ord_saida.php">Listar ordens de saída</a></li>
                                    </ul>
                                </li> <!-- /li class="current" - Pecúlio -->
                                <?php } // if ( $n_peculio >= 2 ) ?>

                                <?php if ( $n_inteli >= 2 ) { ?>
                                <li class="current">

                                    <a href="javascript:void(0)">Inteligência</a>
                                    <ul>

                                        <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>inteli/lista_inteli.php">Listar <?php echo SICOP_DET_DESC_L; ?>s monitorados</a></li>
                                        <?php if ( $n_inteli >= 3 ) { ?>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=cadint">Incluir <?php echo SICOP_DET_DESC_L; ?></a></li>
                                        <?php } // if ( $n_peculio >= 3 )?>

                                    </ul>

                                </li> <!-- /li class="current" - Inteligência -->
                                <?php } // if ( $n_peculio >= 2 )?>

                                <?php if ( $n_cadastro >= 2 ) { ?>
                                <li class="current">

                                    <a href="javascript:void(0)">Cadastro</a>
                                    <ul>

                                        <li class="current">
                                            <a href="javascript:void(0)">Audiências</a>
                                            <ul>
                                                <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>cadastro/buscaaud.php">Pesquisar audiência</a></li>
                                                <?php if ( $n_cadastro >= 3 ) { ?>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=cadaud">Cadastrar audiência</a></li>
                                                <?php } // if ( $n_cadastro >= 3 ) ?>
                                            </ul>
                                        </li> <!-- /li class="current" -->

                                        <li class="current">
                                            <a href="javascript:void(0)">APCC</a>
                                            <ul>
                                                <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>cadastro/buscaapcc.php">Pesquisar APCC</a></li>
                                                <?php if ( $n_cadastro >= 3 ) { ?>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=cadapcc">Cadastrar APCC</a></li>
                                                <?php } // if ( $n_cadastro >= 3 ) ?>
                                            </ul>
                                        </li> <!-- /li class="current" -->

                                        <li class="current">
                                            <a href="javascript:void(0)">Dados provisórios</a>
                                            <ul>
                                                <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>cadastro/lista_prov.php">Listar <?php echo SICOP_DET_DESC_L; ?>s</a></li>
                                                <?php if ( $imp_chefia >= 1 or $imp_cadastro >= 1 ) { ?>
                                                <li><a href="javascript:void(0)" title="Imprimir a lista de <?php echo SICOP_DET_DESC_L; ?>s com dados provisórios" onclick="javascript: ow('<?php echo SICOP_ABS_PATH; ?>print/lista_d_prov.php', '600', '600'); return false" >Imprimir lista</a></li>
                                                <?php } // if ( $imp_chefia >= 1 or $imp_cadastro >= 1 ) ?>
                                            </ul>
                                        </li> <!-- /li class="current" -->

                                        <li class="current">
                                            <a href="javascript:void(0)">Pesquisa de códigos GSA</a>
                                            <ul>

                                                <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>cadastro/buscagsacit.php" title="Pesquisar códigos das cidades do GSA">Cidades</a></li>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>cadastro/buscagsadepol.php" title="Pesquisar códigos das delegacias do GSA">Delegacias</a></li>

                                            </ul>
                                        </li> <!-- /li class="current" -->

                                        <li class="current">
                                            <a href="javascript:void(0)">Escoltas e ordens de saída</a>
                                            <ul>

                                                <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>cadastro/lista_escolta.php">Listar escoltas</a></li>

                                                <?php if ( $n_cadastro >= 3 ) { ?>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>cadastro/add_escolta.php">Cadastrar escolta</a></li>
                                                <?php } // if ( $n_cadastro >= 3 ) ?>

                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>cadastro/lista_ord_saida.php">Listar ordens de saída</a></li>

                                                <?php if ( $n_cadastro >= 3 ) { ?>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>cadastro/add_ord_saida.php">Cadastrar ordem de saida</a></li>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>cadastro/add_local_escolta.php">Cadastrar localidade</a></li>
                                                <?php } // if ( $n_cadastro >= 3 ) ?>

                                            </ul>
                                        </li> <!-- /li class="current" -->


                                        <?php if ( $imp_chefia >= 1 or $imp_cadastro >= 1 or $imp_det >= 1 ) { ?>

                                        <li class="current">
                                            <a href="javascript:void(0)">Listas</a>
                                            <ul>

                                                <?php if ( $imp_chefia >= 1 or $imp_cadastro >= 1 ) { ?>
                                                <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>exp_lista.php">Exportar lista</a></li>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>monta_lista_det.php?tipo_lista=export">Montar lista para exportar ou imprimir</a></li>
                                                <?php } // <?php if ( $imp_chefia >= 1 or $imp_cadastro >= 1 ) ?>

                                                <?php if ( $imp_det >= 1 ) { ?>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>monta_lista_det.php?tipo_lista=foto_det">Imprimir fotos de <?php echo SICOP_DET_DESC_L; ?>s</a></li>
                                                <?php } // if ( $imp_det >= 1 ) ?>

                                                <?php if ( $imp_cadastro >= 1 ) { ?>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>monta_lista_det.php?tipo_lista=recibo_cad">Imprimir recibo do linhão</a></li>
                                                <?php } // if ( $imp_cadastro >= 1 ) ?>

                                            </ul>
                                        </li> <!-- /li class="current" -->

                                        <?php } // if ( $imp_chefia >= 1 or $imp_cadastro >= 1 or $imp_det >= 1 ) ?>

                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>cont_mov.php">Movimentações por data</a></li>
                                        <?php if ( $n_chefia >= 3 ) { ?>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>detento/cadastradet.php">Cadastrar <?php echo SICOP_DET_DESC_L; ?></a></li>
                                        <?php } // if ( $n_chefia >= 3 )?>

                                    </ul>
                                </li> <!-- /li class="current" - Cadastro -->

                                <?php } // if ( $n_cadastro >= 2 ) ?>

                                <?php if ( $n_pront >= 2 ) { ?>
                                <li class="current">

                                    <a href="javascript:void(0)">Prontuário</a>
                                    <ul>

                                        <li class="current">
                                            <a href="javascript:void(0)">Grades</a>
                                            <ul>
                                                <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=bpro">Pesquisar grades</a></li>
                                                <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>prontuario/buscaprocess.php">Pesquisar processos</a></li>
                                                <?php if ( $n_pront >= 3 ) { ?>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=cadpro">Cadastrar processo</a></li>
                                                <?php } // if ( $n_cadastro >= 3 ) ?>
                                            </ul>
                                        </li> <!-- /li class="current" -->

                                        <?php if ( $imp_pront >= 1 ) { ?>

                                        <li class="current">
                                            <a href="javascript:void(0)">Termos</a>
                                            <ul>

                                                <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>monta_lista_det.php?tipo_lista=termo_ab">Imprimir termos de abertura</a></li>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=impter">Imprimir termos de encerramento</a></li>

                                            </ul>
                                        </li> <!-- /li class="current" -->

                                        <li class="current">
                                            <a href="javascript:void(0)">Restituições</a>
                                            <ul>

                                                <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=imprest">Imprimir restituição de mandados</a></li>

                                            </ul>
                                        </li> <!-- /li class="current" -->

                                        <?php } // if ( $imp_pront >= 1 ) ?>
                                        <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>infopen/infopen_pront.php">Infopen</a></li>
                                    </ul>
                                </li> <!-- /li class="current" - Cadastro -->

                                <?php } // if ( $n_pront >= 2 ) ?>


                                <?php if ( $n_bonde >= 2 ) { ?>

                                <li class="current">

                                    <a href="javascript:void(0)">Bonde</a>
                                    <ul>

                                        <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>seguranca/lista_bonde.php">Listar bondes</a></li>
                                        <?php if ( $n_bonde >= 3 ) {?>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>seguranca/add_bonde.php">Cadastrar bonde</a></li>
                                        <?php } ?>

                                    </ul>
                                </li> <!-- /li class="current" - Bonde -->

                                <?php } // if ( $n_bonde >= 2 )?>

                                <?php if ( $n_rol >= 2 ) { ?>

                                <li class="current">

                                    <a href="javascript:void(0)">Visitas</a>

                                    <ul>

                                        <li class="current">
                                            <a href="javascript:void(0)">Registro de entrada</a>
                                            <ul>

                                                <?php if ( $n_rol >= 3 ) { ?>
                                                <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=regrol">Registrar entrada</a></li>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>visita/buscavisit.php?proced=1">Registrar entrada pelo visitante</a></li>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>visita/buscavisit.php?proced=2">Registrar saída</a></li>
                                                <?php } // if ( $n_rol >= 3 ) ?>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>visita/relat_entr.php">Relatórios de entrada</a></li>
                                                <?php if ( $n_rol >= 3 ) { ?>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>visita/delregin.php">Excluir registro</a></li>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>visita/reprintin.php">Reimprimir formulário</a></li>
                                                <?php } // if ( $n_rol >= 3 ) ?>

                                            </ul>
                                        </li> <!-- /li class="current" -->

                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=brol">Pesquisar róis</a></li>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>visita/buscavisit.php">Pesquisar visitantes</a></li>

                                        <?php if ( $n_rol >= 3 ) { ?>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=cadrol">Cadastrar visitante</a></li>
                                        <?php } // if ( $n_rol >= 3 ) ?>

                                        <?php if ( $imp_rol >= 1 ) { ?>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>visita/lista_visit.php">Imprimir Carteirinhas</a></li>
                                        <?php } // if ( $imp_rol >= 1 ) ?>

                                    </ul>
                                </li> <!-- /li class="current" - Visitas -->

                                <?php } // if ( $n_rol >= 2 ) ?>

                                <?php if ( $n_sind >= 2 ) { ?>
                                <li class="current">

                                    <a href="javascript:void(0)">Sindicância</a>
                                    <ul>

                                        <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=bsind">Pesquisar PDA pel<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?></a></li>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>sind/buscapdanum.php">Pesquisar PDA pelo número</a></li>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>sind/listapdaad.php">Listar PDAs de autoria desconhecida</a></li>

                                        <?php if ( $n_sind >= 2 ) { ?>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=cadsind">Cadastrar PDA</a></li>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>sind/cadpda.php?iddet=no">Cadastrar PDA de autoria desconhecida</a></li>
                                        <?php } // if ( $n_sind >= 2 ) ?>

                                    </ul>
                                </li> <!-- /li class="current" - Sindicância -->
                                <?php } // if ( $n_sind >= 2 ) ?>

                                <?php if ( $n_prot_receb >= 1 or $n_prot >= 2 ) { ?>

                                <li class="current">

                                    <a href="javascript:void(0)">Protocolo</a>
                                    <ul>

                                        <?php if ( $n_prot >= 3 ) { ?>
                                        <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>protocolo/cad_prot.php">Cadastrar documento</a></li>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>protocolo/lista_prot.php?sit=1">Listar documentos recebidos</a></li>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>protocolo/lista_prot.php?sit=2">Listar documentos pendentes</a></li>
                                        <?php } // if ( $n_prot >= 3 ) ?>

                                        <?php if ( $n_prot >= 2 ) { ?>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>protocolo/busca_prot.php">Pesquisar documentos</a></li>
                                        <?php } // if ( $n_prot >= 2 ) ?>

                                        <?php if ( $n_prot_receb >= 1 ) { ?>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>protocolo/lista_prot.php?sit=3">Receber documentos</a></li>
                                        <?php } // if ( $n_prot_receb >= 1 ) ?>

                                    </ul>
                                </li> <!-- /li class="current" - Protocolo -->

                                <?php } // if ( $n_prot_receb >= 1 or $n_prot >= 2 ) ?>

                                <?php if ( $n_adm >= 2 or $n_prot >= 2 ) { ?>

                                <li class="current">

                                    <a href="javascript:void(0)">Números</a>
                                    <ul>

                                        <?php if ( $n_adm >= 3 ) { ?>
                                        <li class="current">
                                            <a href="javascript:void(0)">Solicitação</a>
                                            <ul>

                                                <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>adm/numof.php">Solicitar números para ofício</a></li>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>adm/numfax.php">Solicitar números para fax</a></li>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>adm/numnotes.php">Solicitar números para notes</a></li>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>adm/numrms.php">Solicitar números para remessa</a></li>

                                            </ul>
                                        </li> <!-- /li class="current" -->
                                        <?php } // if ( $n_adm >= 3 ) ?>

                                        <li class="current">
                                            <a href="javascript:void(0)">Consulta</a>
                                            <ul>

                                                <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>adm/buscanumofuser.php">Números de ofícios</a></li>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>adm/buscanumfaxuser.php">Números de fax</a></li>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>adm/buscanumnotesuser.php">Números de notes</a></li>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>adm/buscanumremuser.php">Números de remessa</a></li>

                                                <?php if ( $n_prot >= 2 ) { ?>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>adm/buscanum.php">Todos os números</a></li>
                                                <?php }; ?>

                                            </ul>
                                        </li> <!-- /li class="current" -->

                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>adm/altdir.php">Alterar diretores</a></li>

                                    </ul>
                                </li> <!-- /li class="current" - Números -->

                                <?php } // if ( $n_adm >= 2 ) ?>

                                <?php if ( $n_admsist >= 2 ) { ?>

                                <li class="current">

                                    <a href="javascript:void(0)">Sistema</a>
                                    <ul>

                                        <li class="current">
                                            <a href="javascript:void(0)">Usuários</a>
                                            <ul>

                                                <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>user/listauser.php">Lista de usuários</a></li>
                                                <?php if ( $n_admsist >= 3 ) { ?>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>user/cadastrauser.php">Cadastrar usuários</a></li>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>sistema/user_on.php">Usuários on line</a></li>
                                                <?php } // if ( $n_admsist >= 3 ) ?>

                                            </ul>
                                        </li> <!-- /li class="current" -->

                                        <li class="current">
                                            <a href="javascript:void(0)">Log</a>
                                            <ul>

                                                <li class="current"><a href="<?php echo SICOP_ABS_PATH; ?>log/logalt.php?limit=yes">Log de alterações</a></li>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>log/loggeral.php?limit=yes">Log geral</a></li>
                                                <li><a href="<?php echo SICOP_ABS_PATH; ?>log/busca_log_g.php">Pesquisar no log geral</a></li>

                                            </ul>
                                        </li> <!-- /li class="current" -->

                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>sistema/detalup.php">Dados da unidade</a></li>

                                        <?php if ( $n_admsist >= 3 ) { ?>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>msg/listamsg.php">Lista de mensagens</a></li>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>adm/altdirs.php">Alterar diretores no sistema</a></li>
                                        <li><a href="<?php echo SICOP_ABS_PATH; ?>listatel/cad_local_tel.php">Cadastrar telefone</a></li>
                                        <?php } // if ( $n_admsist >= 3 ) ?>

                                    </ul>
                                </li> <!-- /li class="current" - Sistema -->

                                <?php } // if ( $n_admsist >= 2 ) ?>

                            </ul>
                        </li><!-- /li class="current"-->
                    </ul><!-- /ul class="sf-menu"-->

                </span><!-- /span id='menu_sys' -->

                <span id='info_sys'><span id='relogio'></span></span>

                <span id='user_sys'><a class="link_menu_sup" href="<?php echo SICOP_ABS_PATH; ?>user/detalheuser.php"><?php echo $usuario ?></a> <?php echo SicopController::ckMsg(); ?> <a class="link_menu_sup" href="<?php echo SICOP_ABS_PATH; ?>logout.php">Sair</a></span>

            </div><!-- /div id="menu_sup -->
            <div style="padding: 25px 0 0 0;"></div>