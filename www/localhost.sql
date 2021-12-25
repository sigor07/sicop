-- phpMyAdmin SQL Dump
-- version 4.4.13.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Tempo de geração: 01/11/2016 às 10:17
-- Versão do servidor: 5.6.28-0ubuntu0.15.10.1
-- Versão do PHP: 5.6.11-1ubuntu3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `bd_pjunq`
--
CREATE DATABASE IF NOT EXISTS `bd_pjunq` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `bd_pjunq`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `aliases`
--

DROP TABLE IF EXISTS `aliases`;
CREATE TABLE IF NOT EXISTS `aliases` (
  `idalias` int(10) unsigned NOT NULL,
  `cod_detento` int(10) unsigned NOT NULL,
  `cod_tipoalias` smallint(3) unsigned NOT NULL,
  `alias_det` varchar(250) NOT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `apcc`
--

DROP TABLE IF EXISTS `apcc`;
CREATE TABLE IF NOT EXISTS `apcc` (
  `idapcc` int(10) unsigned NOT NULL,
  `cod_detento` int(10) unsigned NOT NULL,
  `cod_numapcc` int(10) unsigned NOT NULL,
  `cod_conduta` smallint(2) unsigned DEFAULT NULL,
  `num_pda` varchar(10) DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `apcc_mov`
--

DROP TABLE IF EXISTS `apcc_mov`;
CREATE TABLE IF NOT EXISTS `apcc_mov` (
  `id_apcc_mov` int(11) unsigned NOT NULL,
  `cod_apcc` int(10) unsigned DEFAULT NULL,
  `cod_movin` int(10) unsigned DEFAULT NULL,
  `cod_movout` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `audiencia`
--

DROP TABLE IF EXISTS `audiencia`;
CREATE TABLE IF NOT EXISTS `audiencia` (
  `idaudiencia` int(10) unsigned NOT NULL,
  `cod_detento` int(10) unsigned NOT NULL,
  `cod_num_of` int(10) unsigned DEFAULT NULL,
  `data_aud` date NOT NULL,
  `hora_aud` time NOT NULL,
  `local_aud` varchar(100) NOT NULL,
  `cidade_aud` varchar(50) NOT NULL,
  `tipo_aud` smallint(2) unsigned DEFAULT NULL,
  `num_processo` varchar(50) DEFAULT NULL,
  `sit_aud` smallint(2) unsigned DEFAULT NULL,
  `motivo_justi` varchar(250) DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `audiencias`
--

DROP TABLE IF EXISTS `audiencias`;
CREATE TABLE IF NOT EXISTS `audiencias` (
  `idaudiencia` int(10) unsigned NOT NULL,
  `cod_detento` int(10) unsigned NOT NULL,
  `cod_num_of` int(10) unsigned DEFAULT NULL,
  `data_aud` date NOT NULL,
  `hora_aud` time NOT NULL,
  `local_aud` varchar(100) NOT NULL,
  `cidade_aud` varchar(50) NOT NULL,
  `tipo_aud` smallint(2) unsigned DEFAULT NULL,
  `num_processo` varchar(50) DEFAULT NULL,
  `sit_aud` smallint(2) unsigned DEFAULT NULL,
  `motivo_justi` varchar(250) DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `bonde`
--

DROP TABLE IF EXISTS `bonde`;
CREATE TABLE IF NOT EXISTS `bonde` (
  `idbonde` int(10) unsigned NOT NULL,
  `bonde_data` date DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `bonde_det`
--

DROP TABLE IF EXISTS `bonde_det`;
CREATE TABLE IF NOT EXISTS `bonde_det` (
  `idbd` int(10) unsigned NOT NULL,
  `cod_bonde_local` int(10) unsigned DEFAULT NULL,
  `cod_detento` int(10) unsigned DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `bonde_locais`
--

DROP TABLE IF EXISTS `bonde_locais`;
CREATE TABLE IF NOT EXISTS `bonde_locais` (
  `idblocal` int(10) unsigned NOT NULL,
  `cod_bonde` int(10) unsigned DEFAULT NULL,
  `cod_unidade` smallint(5) unsigned DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cela`
--

DROP TABLE IF EXISTS `cela`;
CREATE TABLE IF NOT EXISTS `cela` (
  `idcela` smallint(3) unsigned NOT NULL,
  `cod_raio` smallint(2) unsigned DEFAULT NULL,
  `cela` varchar(3) DEFAULT NULL,
  `interditada` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cidades`
--

DROP TABLE IF EXISTS `cidades`;
CREATE TABLE IF NOT EXISTS `cidades` (
  `idcidade` smallint(5) unsigned NOT NULL,
  `cod_uf` smallint(3) unsigned NOT NULL,
  `nome` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cont_mov`
--

DROP TABLE IF EXISTS `cont_mov`;
CREATE TABLE IF NOT EXISTS `cont_mov` (
  `idcontmov` int(10) DEFAULT NULL,
  `cm_data_hora` datetime DEFAULT NULL,
  `cm_in` smallint(5) unsigned DEFAULT '0',
  `cm_it` smallint(5) unsigned DEFAULT '0',
  `cm_ir` smallint(5) unsigned DEFAULT '0',
  `cm_ie` smallint(5) unsigned DEFAULT '0',
  `cm_alv` smallint(5) unsigned DEFAULT '0',
  `cm_ob` smallint(5) unsigned DEFAULT '0',
  `cm_ev` smallint(5) unsigned DEFAULT '0',
  `cm_ex` smallint(5) unsigned DEFAULT '0',
  `cm_et` smallint(5) unsigned DEFAULT '0',
  `cm_er` smallint(5) unsigned DEFAULT '0',
  `cm_ee` smallint(5) unsigned DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cont_pop`
--

DROP TABLE IF EXISTS `cont_pop`;
CREATE TABLE IF NOT EXISTS `cont_pop` (
  `idcontpop` int(10) unsigned NOT NULL,
  `cp_data_hora` datetime DEFAULT NULL,
  `cp_trans_na` smallint(5) unsigned DEFAULT '0',
  `cp_trans_da` smallint(5) unsigned DEFAULT '0',
  `cp_trans_nada` smallint(5) unsigned DEFAULT '0',
  `cp_pop_nada` smallint(5) unsigned DEFAULT '0',
  `cp_pop_na` smallint(5) unsigned DEFAULT '0',
  `cp_pop_da` smallint(5) unsigned DEFAULT '0',
  `cp_pop_total` smallint(5) unsigned DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `det_fotos`
--

DROP TABLE IF EXISTS `det_fotos`;
CREATE TABLE IF NOT EXISTS `det_fotos` (
  `id_foto` int(10) unsigned NOT NULL,
  `cod_detento` int(10) unsigned DEFAULT NULL,
  `foto_det_g` varchar(100) DEFAULT NULL,
  `foto_det_p` varchar(100) DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_add` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `detentos`
--

DROP TABLE IF EXISTS `detentos`;
CREATE TABLE IF NOT EXISTS `detentos` (
  `iddetento` int(10) unsigned NOT NULL,
  `nome_det` varchar(80) NOT NULL,
  `cod_artigo` smallint(5) unsigned DEFAULT NULL,
  `matricula` mediumint(9) unsigned DEFAULT NULL,
  `rg_civil` varchar(11) DEFAULT NULL,
  `execucao` mediumint(8) unsigned DEFAULT NULL,
  `cpf` bigint(11) unsigned zerofill DEFAULT NULL,
  `vulgo` varchar(50) DEFAULT NULL,
  `cod_nacionalidade` smallint(3) unsigned DEFAULT NULL,
  `cod_cidade` smallint(5) unsigned DEFAULT NULL,
  `nasc_det` date DEFAULT NULL,
  `profissao` varchar(50) DEFAULT NULL,
  `cod_est_civil` smallint(2) unsigned DEFAULT NULL,
  `cod_instrucao` smallint(2) unsigned DEFAULT NULL,
  `pai_det` varchar(80) DEFAULT NULL,
  `mae_det` varchar(80) DEFAULT NULL,
  `cod_movin` int(10) unsigned DEFAULT NULL COMMENT 'IDENTIFICADOR DA MOVIMENTAÇÃO DE ENTRADA',
  `cod_movout` int(10) unsigned DEFAULT NULL COMMENT 'IDENTIFICADOR DA MOVIMENTAÇÃO DE SAÍDA',
  `data_prisao` date DEFAULT NULL,
  `cod_local_prisao` smallint(5) unsigned DEFAULT NULL,
  `primario` tinyint(1) unsigned DEFAULT NULL,
  `cod_sit_proc` smallint(2) unsigned DEFAULT NULL,
  `prisoes_ant` varchar(250) DEFAULT NULL,
  `fuga` tinyint(1) unsigned DEFAULT NULL,
  `local_fuga` varchar(60) DEFAULT NULL,
  `cod_cutis` smallint(2) unsigned DEFAULT NULL,
  `cod_cabelos` smallint(2) unsigned DEFAULT NULL,
  `cod_olhos` smallint(2) unsigned DEFAULT NULL,
  `estatura` smallint(3) unsigned DEFAULT NULL,
  `peso` smallint(3) unsigned DEFAULT NULL,
  `defeito_fisico` varchar(60) DEFAULT NULL,
  `sinal_nasc` varchar(60) DEFAULT NULL,
  `cicatrizes` varchar(60) DEFAULT NULL,
  `tatuagens` varchar(60) DEFAULT NULL,
  `resid_det` varchar(150) DEFAULT NULL,
  `cod_religiao` smallint(2) unsigned DEFAULT NULL,
  `possui_adv` tinyint(1) unsigned DEFAULT NULL,
  `caso_emergencia` varchar(150) DEFAULT NULL,
  `obs_artigos` varchar(50) DEFAULT NULL,
  `data_quali` date DEFAULT NULL,
  `funcionario` smallint(3) unsigned DEFAULT NULL,
  `cod_foto` int(10) unsigned DEFAULT NULL,
  `cod_cela` smallint(3) unsigned DEFAULT NULL,
  `monitorado` tinyint(1) unsigned DEFAULT '0',
  `dados_prov` tinyint(1) unsigned DEFAULT '0',
  `jaleco` tinyint(1) unsigned DEFAULT '0',
  `calca` tinyint(1) unsigned DEFAULT '0',
  `conduta_ant` smallint(1) unsigned DEFAULT NULL,
  `data_reab` date DEFAULT NULL,
  `n_passagem` smallint(3) unsigned DEFAULT '0',
  `n_p_trans` smallint(3) unsigned DEFAULT '0',
  `aut_visita` tinyint(1) unsigned DEFAULT '1',
  `aut_sedex` tinyint(1) unsigned DEFAULT '1',
  `digital` tinyint(1) unsigned DEFAULT '0',
  `motivo_prisao` tinytext,
  `pl` varchar(8) DEFAULT NULL,
  `guia_local` varchar(6) DEFAULT NULL,
  `guia_numero` varchar(10) DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gatilhos `detentos`
--
DROP TRIGGER IF EXISTS `detentos_ai`;
DELIMITER $$
CREATE TRIGGER `detentos_ai` AFTER INSERT ON `detentos`
 FOR EACH ROW BEGIN

    INSERT INTO
      `log_alt`
        (
          `tabela`,
          `tipo_log`,
          `alteracao`,
          `user`,
          `ip`
        )
    VALUES
      (
        'DETENTOS',
        'CADASTRAMENTO',
        CONCAT
          (
            '[ CADASTRAMENTO DE DETENTO ]',
            '<br />',
            '<b>Nome:</b> ', NEW.`nome_det`,
            '<br />',
            '<b>matrícula:</b> ', IFNULL( ( SELECT `matricula` FROM `detentos` WHERE `iddetento` =  NEW.`iddetento` ), '(NULL)' ),
            '<br />',
            '<b>ID:</b> ', NEW.`iddetento`
          ),
        NEW.`user_add`,
        NEW.`ip_add`
      );

END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `detentos_bd`;
DELIMITER $$
CREATE TRIGGER `detentos_bd` BEFORE DELETE ON `detentos`
 FOR EACH ROW BEGIN

    INSERT INTO
      `log_alt`
        (
          `tabela`,
          `tipo_log`,
          `alteracao`,
          `user`,
          `ip`
        )
    VALUES
      (
        'DETENTOS',
        'EXCLUSÃO',
        CONCAT
          (
            '[ EXCLUSÃO DE DETENTO ]',
            '<br />',
            '<b>Nome:</b> ', OLD.`nome_det`,
            '<br />',
            '<b>matrícula:</b> ',IFNULL( ( SELECT `matricula` FROM `detentos` WHERE `iddetento` =  OLD.`iddetento` ), '(NULL)' ),
            '<br />',
            '<b>ID:</b> ', OLD.`iddetento`
          ),
        OLD.`user_up`,
        OLD.`ip_up`
      );

END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `detentos_bu`;
DELIMITER $$
CREATE TRIGGER `detentos_bu` BEFORE UPDATE ON `detentos`
 FOR EACH ROW /*
Trigger utilizada para registrar as alterações na tabela detentos

só pega os campos que foram alterados
*/

BEGIN

DECLARE alteracoes TEXT;
DECLARE quebra VARCHAR(7) DEFAULT '<br />';
DECLARE separador VARCHAR(7) DEFAULT  ' --> ';
DECLARE var_old TEXT DEFAULT '(NULL)';
DECLARE var_new TEXT DEFAULT '(NULL)';

IF OLD.`nome_det` != NEW.`nome_det`
OR (ISNULL(NEW.`nome_det`) AND NOT ISNULL(OLD.`nome_det`))
OR (ISNULL(OLD.`nome_det`) AND NOT ISNULL(NEW.`nome_det`)) THEN
    IF ISNULL(NEW.`nome_det`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Nome do detento:</b> ', CONCAT_WS( separador, OLD.`nome_det`,'(NULL)')));
    ELSEIF ISNULL(OLD.`nome_det`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Nome do detento:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`nome_det`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Nome do detento:</b> ', CONCAT_WS( separador, OLD.`nome_det`,NEW.`nome_det`)));
    END IF;
END IF;

IF OLD.`cod_artigo` != NEW.`cod_artigo`
OR (ISNULL(NEW.`cod_artigo`) AND NOT ISNULL(OLD.`cod_artigo`))
OR (ISNULL(OLD.`cod_artigo`) AND NOT ISNULL(NEW.`cod_artigo`)) THEN

    IF ISNULL( NEW.`cod_artigo` ) THEN

        SET var_old = ( SELECT `artigo` FROM `tipoartigo` WHERE `idartigo` = OLD.`cod_artigo` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Artigo:</b> ', CONCAT_WS(  separador, var_old, '(NULL)' ) ) );

    ELSEIF ISNULL( OLD.`cod_artigo` ) THEN

        SET var_new = ( SELECT `artigo` FROM `bd_luc`.`tipoartigo` WHERE `idartigo` = NEW.`cod_artigo` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Artigo:</b> ', CONCAT_WS(  separador, '(NULL)', var_new ) ) );

    ELSE

        SET var_old = ( SELECT `artigo` FROM `bd_luc`.`tipoartigo` WHERE `idartigo` = OLD.`cod_artigo` LIMIT 1 );
        SET var_new = ( SELECT `artigo` FROM `bd_luc`.`tipoartigo` WHERE `idartigo` = NEW.`cod_artigo` LIMIT 1 );

        SET alteracoes =  CONCAT_WS(  quebra, alteracoes, CONCAT( '<b>Artigo:</b> ', CONCAT_WS(  separador, var_old, var_new ) ) );

    END IF;
END IF;

IF OLD.`matricula` != NEW.`matricula`
OR (ISNULL(NEW.`matricula`) AND NOT ISNULL(OLD.`matricula`))
OR (ISNULL(OLD.`matricula`) AND NOT ISNULL(NEW.`matricula`)) THEN
    IF ISNULL(NEW.`matricula`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Matrícula:</b> ', CONCAT_WS( separador, OLD.`matricula`,'(NULL)')));
    ELSEIF ISNULL(OLD.`matricula`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Matrícula:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`matricula`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Matrícula:</b> ', CONCAT_WS( separador, OLD.`matricula`,NEW.`matricula`)));
    END IF;
END IF;

IF OLD.`rg_civil` != NEW.`rg_civil`
OR (ISNULL(NEW.`rg_civil`) AND NOT ISNULL(OLD.`rg_civil`))
OR (ISNULL(OLD.`rg_civil`) AND NOT ISNULL(NEW.`rg_civil`)) THEN
    IF ISNULL(NEW.`rg_civil`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>RG:</b> ', CONCAT_WS( separador, OLD.`rg_civil`,'(NULL)')));
    ELSEIF ISNULL(OLD.`rg_civil`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>RG:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`rg_civil`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>RG:</b> ', CONCAT_WS( separador, OLD.`rg_civil`,NEW.`rg_civil`)));
    END IF;
END IF;

IF OLD.`execucao` != NEW.`execucao`
OR (ISNULL(NEW.`execucao`) AND NOT ISNULL(OLD.`execucao`))
OR (ISNULL(OLD.`execucao`) AND NOT ISNULL(NEW.`execucao`)) THEN
    IF ISNULL(NEW.`execucao`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Execução:</b> ', CONCAT_WS( separador, OLD.`execucao`,'(NULL)')));
    ELSEIF ISNULL(OLD.`execucao`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Execução:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`execucao`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Execução:</b> ', CONCAT_WS( separador, OLD.`execucao`,NEW.`execucao`)));
    END IF;
END IF;

IF OLD.`vulgo` != NEW.`vulgo`
OR (ISNULL(NEW.`vulgo`) AND NOT ISNULL(OLD.`vulgo`))
OR (ISNULL(OLD.`vulgo`) AND NOT ISNULL(NEW.`vulgo`)) THEN
    IF ISNULL(NEW.`vulgo`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Vulgo:</b> ', CONCAT_WS( separador, OLD.`vulgo`,'(NULL)')));
    ELSEIF ISNULL(OLD.`vulgo`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Vulgo:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`vulgo`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Vulgo:</b> ', CONCAT_WS( separador, OLD.`vulgo`,NEW.`vulgo`)));
    END IF;
END IF;

IF OLD.`cod_nacionalidade` != NEW.`cod_nacionalidade`
OR ( ISNULL( NEW.`cod_nacionalidade` ) AND NOT ISNULL( OLD.`cod_nacionalidade` ) )
OR ( ISNULL( OLD.`cod_nacionalidade` ) AND NOT ISNULL( NEW.`cod_nacionalidade` ) ) THEN

    IF ISNULL( NEW.`cod_nacionalidade` ) THEN

        SET var_old = ( SELECT `nacionalidade` FROM `tiponacionalidade` WHERE `idnacionalidade` = OLD.`cod_nacionalidade` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Nacionalidade:</b> ', CONCAT_WS( separador, var_old, '(NULL)' ) ) );

    ELSEIF ISNULL(OLD.`cod_nacionalidade`) THEN

        SET var_new = ( SELECT `nacionalidade` FROM `tiponacionalidade` WHERE `idnacionalidade` = NEW.`cod_nacionalidade` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Nacionalidade:</b> ', CONCAT_WS( separador, '(NULL)', var_new ) ) );

    ELSE

        SET var_old = ( SELECT `nacionalidade` FROM `tiponacionalidade` WHERE `idnacionalidade` = OLD.`cod_nacionalidade` LIMIT 1 );
        SET var_new = ( SELECT `nacionalidade` FROM `tiponacionalidade` WHERE `idnacionalidade` = NEW.`cod_nacionalidade` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Nacionalidade:</b> ', CONCAT_WS( separador, var_old, var_new ) ) );

    END IF;
END IF;

IF OLD.`cod_cidade` != NEW.`cod_cidade`
OR ( ISNULL(NEW.`cod_cidade` ) AND NOT ISNULL( OLD.`cod_cidade` ) )
OR ( ISNULL(OLD.`cod_cidade` ) AND NOT ISNULL( NEW.`cod_cidade` ) ) THEN

    IF ISNULL( NEW.`cod_cidade` ) THEN

        SET var_old = ( SELECT CONCAT( `cidades`.`nome`, ' - ', `estados`.`sigla`) FROM `cidades` INNER JOIN `estados` ON `cidades`.cod_uf = `estados`.`idestado` WHERE `cidades`.idcidade = OLD.`cod_cidade` LIMIT 1 );

        SET alteracoes = CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Cidade:</b> ', CONCAT_WS( separador, var_old, '(NULL)' ) ) );

    ELSEIF ISNULL( OLD.`cod_cidade` ) THEN

        SET var_new = ( SELECT CONCAT( `cidades`.`nome`, ' - ', `estados`.`sigla`) FROM `cidades` INNER JOIN `estados` ON `cidades`.cod_uf = `estados`.`idestado` WHERE `cidades`.idcidade = NEW.`cod_cidade` LIMIT 1 );

        SET alteracoes = CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Cidade:</b> ', CONCAT_WS( separador, '(NULL)', var_new ) ) );

    ELSE

        SET var_old = ( SELECT CONCAT( `cidades`.`nome`, ' - ', `estados`.`sigla`) FROM `cidades` INNER JOIN `estados` ON `cidades`.cod_uf = `estados`.`idestado` WHERE `cidades`.idcidade = OLD.`cod_cidade` LIMIT 1 );
        SET var_new = ( SELECT CONCAT( `cidades`.`nome`, ' - ', `estados`.`sigla`) FROM `cidades` INNER JOIN `estados` ON `cidades`.cod_uf = `estados`.`idestado` WHERE `cidades`.idcidade = NEW.`cod_cidade` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Cidade:</b> ', CONCAT_WS( separador, var_old, var_new) ) );

    END IF;
END IF;

IF OLD.`nasc_det` != NEW.`nasc_det`
OR (ISNULL(NEW.`nasc_det`) AND NOT ISNULL(OLD.`nasc_det`))
OR (ISNULL(OLD.`nasc_det`) AND NOT ISNULL(NEW.`nasc_det`)) THEN
    IF ISNULL(NEW.`nasc_det`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Data de nascimento:</b> ', CONCAT_WS( separador, DATE_FORMAT(OLD.`nasc_det`, '%d/%m/%Y'),'(NULL)')));
    ELSEIF ISNULL(OLD.`nasc_det`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Data de nascimento:</b> ', CONCAT_WS( separador, '(NULL)',DATE_FORMAT(NEW.`nasc_det`, '%d/%m/%Y'))));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Data de nascimento:</b> ', CONCAT_WS( separador, DATE_FORMAT(OLD.`nasc_det`, '%d/%m/%Y'),DATE_FORMAT(NEW.`nasc_det`, '%d/%m/%Y'))));
    END IF;
END IF;

IF OLD.`profissao` != NEW.`profissao`
OR (ISNULL(NEW.`profissao`) AND NOT ISNULL(OLD.`profissao`))
OR (ISNULL(OLD.`profissao`) AND NOT ISNULL(NEW.`profissao`)) THEN
    IF ISNULL(NEW.`profissao`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Profissão:</b> ', CONCAT_WS( separador, OLD.`profissao`,'(NULL)')));
    ELSEIF ISNULL(OLD.`profissao`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Profissão:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`profissao`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Profissão:</b> ', CONCAT_WS( separador, OLD.`profissao`,NEW.`profissao`)));
    END IF;
END IF;

IF OLD.`cod_est_civil` != NEW.`cod_est_civil`
OR ( ISNULL( NEW.`cod_est_civil` ) AND NOT ISNULL( OLD.`cod_est_civil` ) )
OR ( ISNULL( OLD.`cod_est_civil` ) AND NOT ISNULL( NEW.`cod_est_civil` ) ) THEN

    IF ISNULL( NEW.`cod_est_civil` ) THEN

        SET var_old = ( SELECT `est_civil` FROM `tipoestadocivil` WHERE `idest_civil` = OLD.`cod_est_civil` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Estado civil:</b> ', CONCAT_WS( separador, var_old,'(NULL)' ) ) );

    ELSEIF ISNULL( OLD.cod_est_civil ) THEN

        SET var_new = ( SELECT `est_civil` FROM `tipoestadocivil` WHERE `idest_civil` = NEW.`cod_est_civil` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Estado civil:</b> ', CONCAT_WS( separador, '(NULL)', var_new ) ) );

    ELSE

        SET var_old = ( SELECT `est_civil` FROM `tipoestadocivil` WHERE `idest_civil` = OLD.`cod_est_civil` LIMIT 1 );
        SET var_new = ( SELECT `est_civil` FROM `tipoestadocivil` WHERE `idest_civil` = NEW.`cod_est_civil` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Estado civil:</b> ', CONCAT_WS( separador, var_old, var_new ) ) );

    END IF;
END IF;

IF OLD.`cod_instrucao` != NEW.`cod_instrucao`
OR ( ISNULL( NEW.`cod_instrucao` ) AND NOT ISNULL( OLD.`cod_instrucao` ) )
OR ( ISNULL( OLD.`cod_instrucao` ) AND NOT ISNULL( NEW.`cod_instrucao` ) ) THEN

    IF ISNULL( NEW.`cod_instrucao` ) THEN

        SET var_old = ( SELECT `escolaridade` FROM `tipoescolaridade` WHERE `idescolaridade` = OLD.`cod_instrucao` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Instrução:</b> ', CONCAT_WS( separador, var_old,'(NULL)' ) ) );

    ELSEIF ISNULL( OLD.`cod_instrucao` ) THEN

        SET var_new = ( SELECT `escolaridade` FROM `tipoescolaridade` WHERE `idescolaridade` = NEW.`cod_instrucao` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Instrução:</b> ', CONCAT_WS( separador, '(NULL)', var_new ) ) );

    ELSE

        SET var_old = ( SELECT `escolaridade` FROM `tipoescolaridade` WHERE `idescolaridade` = OLD.`cod_instrucao` LIMIT 1 );
        SET var_new = ( SELECT `escolaridade` FROM `tipoescolaridade` WHERE `idescolaridade` = NEW.`cod_instrucao` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Instrução:</b> ', CONCAT_WS( separador, var_old, var_new ) ) );

    END IF;
END IF;

IF OLD.`pai_det` != NEW.`pai_det`
OR (ISNULL(NEW.`pai_det`) AND NOT ISNULL(OLD.`pai_det`))
OR (ISNULL(OLD.`pai_det`) AND NOT ISNULL(NEW.`pai_det`)) THEN
    IF ISNULL(NEW.`pai_det`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Pai do detento:</b> ', CONCAT_WS( separador, OLD.`pai_det`,'(NULL)')));
    ELSEIF ISNULL(OLD.`pai_det`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Pai do detento:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`pai_det`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Pai do detento:</b> ', CONCAT_WS( separador, OLD.`pai_det`,NEW.`pai_det`)));
    END IF;
END IF;

IF OLD.`mae_det` != NEW.`mae_det`
OR (ISNULL(NEW.`mae_det`) AND NOT ISNULL(OLD.`mae_det`))
OR (ISNULL(OLD.`mae_det`) AND NOT ISNULL(NEW.`mae_det`)) THEN
    IF ISNULL(NEW.`mae_det`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Mãe do detento:</b> ', CONCAT_WS( separador, OLD.`mae_det`,'(NULL)')));
    ELSEIF ISNULL(OLD.`mae_det`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Mãe do detento:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`mae_det`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Mãe do detento:</b> ', CONCAT_WS( separador, OLD.`mae_det`,NEW.`mae_det`)));
    END IF;
END IF;

IF OLD.`cod_movin` != NEW.`cod_movin`
OR ( ISNULL(NEW.`cod_movin` ) AND NOT ISNULL( OLD.`cod_movin` ) )
OR ( ISNULL(OLD.`cod_movin` ) AND NOT ISNULL( NEW.`cod_movin` ) ) THEN
    IF ISNULL( NEW.`cod_movin` ) THEN

        SET var_old = ( SELECT CONCAT( DATE_FORMAT( `mov_det`.`data_mov`, '%d/%m/%Y' ), ' - ', `tipomov`.`tipo_mov`, ' - ', `unidades`.`unidades` )
                        FROM `mov_det`
                        LEFT JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                        LEFT JOIN `unidades` ON `mov_det`.`cod_local_mov` = `unidades`.`idunidades`
                        WHERE `mov_det`.id_mov = OLD.`cod_movin`
                        LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Movimentação de inclusão:</b> ', CONCAT_WS( separador, var_old, '(NULL)' ) ) );

    ELSEIF ISNULL( OLD.`cod_movin` ) THEN

        SET var_new = ( SELECT CONCAT( DATE_FORMAT( `mov_det`.`data_mov`, '%d/%m/%Y' ), ' - ', `tipomov`.`tipo_mov`, ' - ', `unidades`.`unidades` )
                        FROM `mov_det`
                        LEFT JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                        LEFT JOIN `unidades` ON `mov_det`.`cod_local_mov` = `unidades`.`idunidades`
                        WHERE `mov_det`.id_mov = NEW.`cod_movin`
                        LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Movimentação de inclusão:</b> ', CONCAT_WS( separador, '(NULL)', var_new ) ) );

    ELSE

        SET var_old = ( SELECT CONCAT( DATE_FORMAT( `mov_det`.`data_mov`, '%d/%m/%Y' ), ' - ', `tipomov`.`tipo_mov`, ' - ', `unidades`.`unidades` )
                        FROM `mov_det`
                        LEFT JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                        LEFT JOIN `unidades` ON `mov_det`.`cod_local_mov` = `unidades`.`idunidades`
                        WHERE `mov_det`.id_mov = OLD.`cod_movin`
                        LIMIT 1 );

        SET var_new = ( SELECT CONCAT( DATE_FORMAT( `mov_det`.`data_mov`, '%d/%m/%Y' ), ' - ', `tipomov`.`tipo_mov`, ' - ', `unidades`.`unidades` )
                        FROM `mov_det`
                        LEFT JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                        LEFT JOIN `unidades` ON `mov_det`.`cod_local_mov` = `unidades`.`idunidades`
                        WHERE `mov_det`.id_mov = NEW.`cod_movin`
                        LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Movimentação de inclusão:</b> ', CONCAT_WS( separador, var_old, var_new ) ) );

    END IF;
END IF;

IF OLD.`cod_movout` != NEW.`cod_movout`
OR ( ISNULL(NEW.`cod_movout` ) AND NOT ISNULL( OLD.`cod_movout` ) )
OR ( ISNULL(OLD.`cod_movout` ) AND NOT ISNULL( NEW.`cod_movout` ) ) THEN
    IF ISNULL( NEW.`cod_movout` ) THEN

        SET var_old = ( SELECT CONCAT( DATE_FORMAT( `mov_det`.`data_mov`, '%d/%m/%Y' ), ' - ', `tipomov`.`tipo_mov`, ' - ', `unidades`.`unidades` )
                        FROM `mov_det`
                        LEFT JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                        LEFT JOIN `unidades` ON `mov_det`.`cod_local_mov` = `unidades`.`idunidades`
                        WHERE `mov_det`.id_mov = OLD.`cod_movout`
                        LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Movimentação de saída:</b> ', CONCAT_WS( separador, var_old, '(NULL)' ) ) );

    ELSEIF ISNULL( OLD.`cod_movout` ) THEN

        SET var_new = ( SELECT CONCAT( DATE_FORMAT( `mov_det`.`data_mov`, '%d/%m/%Y' ), ' - ', `tipomov`.`tipo_mov`, ' - ', `unidades`.`unidades` )
                        FROM `mov_det`
                        LEFT JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                        LEFT JOIN `unidades` ON `mov_det`.`cod_local_mov` = `unidades`.`idunidades`
                        WHERE `mov_det`.id_mov = NEW.`cod_movout`
                        LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Movimentação de saída:</b> ', CONCAT_WS( separador, '(NULL)', var_new ) ) );

    ELSE

        SET var_old = ( SELECT CONCAT( DATE_FORMAT( `mov_det`.`data_mov`, '%d/%m/%Y' ), ' - ', `tipomov`.`tipo_mov`, ' - ', `unidades`.`unidades` )
                        FROM `mov_det`
                        LEFT JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                        LEFT JOIN `unidades` ON `mov_det`.`cod_local_mov` = `unidades`.`idunidades`
                        WHERE `mov_det`.id_mov = OLD.`cod_movout`
                        LIMIT 1 );

        SET var_new = ( SELECT CONCAT( DATE_FORMAT( `mov_det`.`data_mov`, '%d/%m/%Y' ), ' - ', `tipomov`.`tipo_mov`, ' - ', `unidades`.`unidades` )
                        FROM `mov_det`
                        LEFT JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                        LEFT JOIN `unidades` ON `mov_det`.`cod_local_mov` = `unidades`.`idunidades`
                        WHERE `mov_det`.id_mov = NEW.`cod_movout`
                        LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Movimentação de saída:</b> ', CONCAT_WS( separador, var_old, var_new ) ) );

    END IF;
END IF;

IF OLD.`data_prisao` != NEW.`data_prisao`
OR (ISNULL(NEW.`data_prisao`) AND NOT ISNULL(OLD.`data_prisao`))
OR (ISNULL(OLD.`data_prisao`) AND NOT ISNULL(NEW.`data_prisao`)) THEN
    IF ISNULL(NEW.`data_prisao`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Data da prisão:</b> ', CONCAT_WS( separador, DATE_FORMAT(OLD.`data_prisao`, '%d/%m/%Y'),'(NULL)')));
    ELSEIF ISNULL(OLD.`data_prisao`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Data da prisão:</b> ', CONCAT_WS( separador, '(NULL)',DATE_FORMAT(NEW.`data_prisao`, '%d/%m/%Y'))));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Data da prisão:</b> ', CONCAT_WS( separador, DATE_FORMAT(OLD.`data_prisao`, '%d/%m/%Y'),DATE_FORMAT(NEW.`data_prisao`, '%d/%m/%Y'))));
    END IF;
END IF;

IF OLD.`primario` != NEW.`primario`
OR (ISNULL(NEW.`primario`) AND NOT ISNULL(OLD.`primario`))
OR (ISNULL(OLD.`primario`) AND NOT ISNULL(NEW.`primario`)) THEN
    IF ISNULL(NEW.`primario`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Primário:</b> ', CONCAT_WS( separador, OLD.`primario`,'(NULL)')));
    ELSEIF ISNULL(OLD.`primario`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Primário:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`primario`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Primário:</b> ', CONCAT_WS( separador, OLD.`primario`,NEW.`primario`)));
    END IF;
END IF;

IF OLD.`cod_sit_proc` != NEW.`cod_sit_proc`
OR ( ISNULL( NEW.`cod_sit_proc` ) AND NOT ISNULL( OLD.`cod_sit_proc` ) )
OR ( ISNULL( OLD.`cod_sit_proc` ) AND NOT ISNULL( NEW.`cod_sit_proc` ) ) THEN

    IF ISNULL( NEW.`cod_sit_proc` ) THEN

        SET var_old = ( SELECT `sit_proc` FROM `tiposituacaoprocessual` WHERE `idsit_proc` = OLD.`cod_sit_proc` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Situação processual:</b> ', CONCAT_WS( separador, var_old, '(NULL)' ) ) );

    ELSEIF ISNULL( OLD.`cod_sit_proc` ) THEN

        SET var_new = ( SELECT `sit_proc` FROM `tiposituacaoprocessual` WHERE `idsit_proc` = NEW.`cod_sit_proc` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Situação processual:</b> ', CONCAT_WS( separador, '(NULL)', var_new ) ) );

    ELSE

        SET var_old = ( SELECT `sit_proc` FROM `tiposituacaoprocessual` WHERE `idsit_proc` = OLD.`cod_sit_proc` LIMIT 1 );
        SET var_new = ( SELECT `sit_proc` FROM `tiposituacaoprocessual` WHERE `idsit_proc` = NEW.`cod_sit_proc` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Situação processual:</b> ', CONCAT_WS( separador, var_old, var_new ) ) );

    END IF;
END IF;

IF OLD.`prisoes_ant` != NEW.`prisoes_ant`
OR (ISNULL(NEW.`prisoes_ant`) AND NOT ISNULL(OLD.`prisoes_ant`))
OR (ISNULL(OLD.`prisoes_ant`) AND NOT ISNULL(NEW.`prisoes_ant`)) THEN
    IF ISNULL(NEW.`prisoes_ant`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Prisões anteriores:</b> ', CONCAT_WS( separador, OLD.`prisoes_ant`,'(NULL)')));
    ELSEIF ISNULL(OLD.`prisoes_ant`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Prisões anteriores:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`prisoes_ant`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Prisões anteriores:</b> ', CONCAT_WS( separador, OLD.`prisoes_ant`,NEW.`prisoes_ant`)));
    END IF;
END IF;

IF OLD.`fuga` != NEW.`fuga`
OR (ISNULL(NEW.`fuga`) AND NOT ISNULL(OLD.`fuga`))
OR (ISNULL(OLD.`fuga`) AND NOT ISNULL(NEW.`fuga`)) THEN
    IF ISNULL(NEW.`fuga`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Fuga:</b> ', CONCAT_WS( separador, OLD.`fuga`,'(NULL)')));
    ELSEIF ISNULL(OLD.`fuga`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Fuga:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`fuga`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Fuga:</b> ', CONCAT_WS( separador, OLD.`fuga`,NEW.`fuga`)));
    END IF;
END IF;

IF OLD.`local_fuga` != NEW.`local_fuga`
OR (ISNULL(NEW.`local_fuga`) AND NOT ISNULL(OLD.`local_fuga`))
OR (ISNULL(OLD.`local_fuga`) AND NOT ISNULL(NEW.`local_fuga`)) THEN
    IF ISNULL(NEW.`local_fuga`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Local de fuga:</b> ', CONCAT_WS( separador, OLD.`local_fuga`,'(NULL)')));
    ELSEIF ISNULL(OLD.`local_fuga`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Local de fuga:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`local_fuga`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Local de fuga:</b> ', CONCAT_WS( separador, OLD.`local_fuga`,NEW.`local_fuga`)));
    END IF;
END IF;

IF OLD.`cod_cutis` != NEW.`cod_cutis`
OR ( ISNULL( NEW.`cod_cutis` ) AND NOT ISNULL( OLD.`cod_cutis` ) )
OR ( ISNULL( OLD.`cod_cutis` ) AND NOT ISNULL( NEW.`cod_cutis` ) ) THEN

    IF ISNULL( NEW.`cod_cutis` ) THEN

        SET var_old = ( SELECT `cutis` FROM `tipocutis` WHERE `idcutis` = OLD.`cod_cutis` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Cutis:</b> ', CONCAT_WS( separador, var_old,'(NULL)' ) ) );

    ELSEIF ISNULL( OLD.`cod_cutis` ) THEN

        SET var_new = ( SELECT `cutis` FROM `tipocutis` WHERE `idcutis` = NEW.`cod_cutis` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Cutis:</b> ', CONCAT_WS( separador, '(NULL)', var_new ) ) );

    ELSE

        SET var_old = ( SELECT `cutis` FROM `tipocutis` WHERE `idcutis` = OLD.`cod_cutis` LIMIT 1 );
        SET var_new = ( SELECT `cutis` FROM `tipocutis` WHERE `idcutis` = NEW.`cod_cutis` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Cutis:</b> ', CONCAT_WS( separador, var_old, var_new ) ) );

    END IF;
END IF;

IF OLD.`cod_cabelos` != NEW.`cod_cabelos`
OR ( ISNULL( NEW.`cod_cabelos` ) AND NOT ISNULL( OLD.`cod_cabelos` ) )
OR ( ISNULL( OLD.`cod_cabelos` ) AND NOT ISNULL( NEW.`cod_cabelos` ) ) THEN

    IF ISNULL( NEW.`cod_cabelos` ) THEN

        SET var_old = ( SELECT `cabelos` FROM `tipocabelos` WHERE `idcabelos` = OLD.`cod_cabelos` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Cabelos:</b> ', CONCAT_WS( separador, var_old, '(NULL)' ) ) );

    ELSEIF ISNULL( OLD.`cod_cabelos` ) THEN

        SET var_new = ( SELECT `cabelos` FROM `tipocabelos` WHERE `idcabelos` = NEW.`cod_cabelos` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Cabelos:</b> ', CONCAT_WS( separador, '(NULL)', var_new ) ) );

    ELSE

        SET var_old = ( SELECT `cabelos` FROM `tipocabelos` WHERE `idcabelos` = OLD.`cod_cabelos` LIMIT 1 );
        SET var_new = ( SELECT `cabelos` FROM `tipocabelos` WHERE `idcabelos` = NEW.`cod_cabelos` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Cabelos:</b> ', CONCAT_WS( separador, var_old, var_new ) ) );

    END IF;
END IF;

IF OLD.`cod_olhos` != NEW.`cod_olhos`
OR ( ISNULL( NEW.`cod_olhos` ) AND NOT ISNULL( OLD.`cod_olhos` ) )
OR ( ISNULL( OLD.`cod_olhos` ) AND NOT ISNULL( NEW.`cod_olhos` ) ) THEN

    IF ISNULL( NEW.`cod_olhos` ) THEN

        SET var_old = ( SELECT `olhos` FROM `tipoolhos` WHERE `idolhos` = OLD.`cod_olhos` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Olhos:</b> ', CONCAT_WS( separador, var_old, '(NULL)' ) ) );

    ELSEIF ISNULL( OLD.`cod_olhos` ) THEN

        SET var_new = ( SELECT `olhos` FROM `tipoolhos` WHERE `idolhos` = NEW.`cod_olhos` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Olhos:</b> ', CONCAT_WS( separador, '(NULL)', var_new ) ) );

    ELSE

        SET var_old = ( SELECT `olhos` FROM `tipoolhos` WHERE `idolhos` = OLD.`cod_olhos` LIMIT 1 );
        SET var_new = ( SELECT `olhos` FROM `tipoolhos` WHERE `idolhos` = NEW.`cod_olhos` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Olhos:</b> ', CONCAT_WS( separador, var_old, var_new ) ) );


    END IF;
END IF;

IF OLD.`estatura` != NEW.`estatura`
OR (ISNULL(NEW.`estatura`) AND NOT ISNULL(OLD.`estatura`))
OR (ISNULL(OLD.`estatura`) AND NOT ISNULL(NEW.`estatura`)) THEN
    IF ISNULL(NEW.`estatura`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Estatura:</b> ', CONCAT_WS( separador, OLD.`estatura`,'(NULL)')));
    ELSEIF ISNULL(OLD.`estatura`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Estatura:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`estatura`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Estatura:</b> ', CONCAT_WS( separador, OLD.`estatura`,NEW.`estatura`)));
    END IF;
END IF;

IF OLD.`peso` != NEW.`peso`
OR (ISNULL(NEW.`peso`) AND NOT ISNULL(OLD.`peso`))
OR (ISNULL(OLD.`peso`) AND NOT ISNULL(NEW.`peso`)) THEN
    IF ISNULL(NEW.`peso`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Peso:</b> ', CONCAT_WS( separador, OLD.`peso`,'(NULL)')));
    ELSEIF ISNULL(OLD.`peso`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Peso:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`peso`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Peso:</b> ', CONCAT_WS( separador, OLD.`peso`,NEW.`peso`)));
    END IF;
END IF;

IF OLD.`defeito_fisico` != NEW.`defeito_fisico`
OR (ISNULL(NEW.`defeito_fisico`) AND NOT ISNULL(OLD.`defeito_fisico`))
OR (ISNULL(OLD.`defeito_fisico`) AND NOT ISNULL(NEW.`defeito_fisico`)) THEN
    IF ISNULL(NEW.`defeito_fisico`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Defeitos físicos:</b> ', CONCAT_WS( separador, OLD.`defeito_fisico`,'(NULL)')));
    ELSEIF ISNULL(OLD.`defeito_fisico`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Defeitos físicos:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`defeito_fisico`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Defeitos físicos:</b> ', CONCAT_WS( separador, OLD.`defeito_fisico`,NEW.`defeito_fisico`)));
    END IF;
END IF;

IF OLD.`sinal_nasc` != NEW.`sinal_nasc`
OR (ISNULL(NEW.`sinal_nasc`) AND NOT ISNULL(OLD.`sinal_nasc`))
OR (ISNULL(OLD.`sinal_nasc`) AND NOT ISNULL(NEW.`sinal_nasc`)) THEN
    IF ISNULL(NEW.`sinal_nasc`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Sinais de nascimento:</b> ', CONCAT_WS( separador, OLD.`sinal_nasc`,'(NULL)')));
    ELSEIF ISNULL(OLD.`sinal_nasc`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Sinais de nascimento:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`sinal_nasc`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Sinais de nascimento:</b> ', CONCAT_WS( separador, OLD.`sinal_nasc`,NEW.`sinal_nasc`)));
    END IF;
END IF;

IF OLD.`cicatrizes` != NEW.`cicatrizes`
OR (ISNULL(NEW.`cicatrizes`) AND NOT ISNULL(OLD.`cicatrizes`))
OR (ISNULL(OLD.`cicatrizes`) AND NOT ISNULL(NEW.`cicatrizes`)) THEN
    IF ISNULL(NEW.`cicatrizes`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Cicatrizes:</b> ', CONCAT_WS( separador, OLD.`cicatrizes`,'(NULL)')));
    ELSEIF ISNULL(OLD.`cicatrizes`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Cicatrizes:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`cicatrizes`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Cicatrizes:</b> ', CONCAT_WS( separador, OLD.`cicatrizes`,NEW.`cicatrizes`)));
    END IF;
END IF;

IF OLD.`tatuagens` != NEW.`tatuagens`
OR (ISNULL(NEW.`tatuagens`) AND NOT ISNULL(OLD.`tatuagens`))
OR (ISNULL(OLD.`tatuagens`) AND NOT ISNULL(NEW.`tatuagens`)) THEN
    IF ISNULL(NEW.`tatuagens`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Tatuagens:</b> ', CONCAT_WS( separador, OLD.`tatuagens`,'(NULL)')));
    ELSEIF ISNULL(OLD.`tatuagens`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Tatuagens:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`tatuagens`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Tatuagens:</b> ', CONCAT_WS( separador, OLD.`tatuagens`,NEW.`tatuagens`)));
    END IF;
END IF;

IF OLD.`resid_det` != NEW.`resid_det`
OR (ISNULL(NEW.`resid_det`) AND NOT ISNULL(OLD.`resid_det`))
OR (ISNULL(OLD.`resid_det`) AND NOT ISNULL(NEW.`resid_det`)) THEN
    IF ISNULL(NEW.`resid_det`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Última residência:</b> ', CONCAT_WS( separador, OLD.`resid_det`,'(NULL)')));
    ELSEIF ISNULL(OLD.`resid_det`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Última residência:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`resid_det`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Última residência:</b> ', CONCAT_WS( separador, OLD.`resid_det`,NEW.`resid_det`)));
    END IF;
END IF;

IF OLD.`cod_religiao` != NEW.`cod_religiao`
OR ( ISNULL( NEW.`cod_religiao` ) AND NOT ISNULL( OLD.`cod_religiao` ) )
OR ( ISNULL( OLD.`cod_religiao` ) AND NOT ISNULL( NEW.`cod_religiao` ) ) THEN

    IF ISNULL( NEW.`cod_religiao` ) THEN

        SET var_old = ( SELECT `religiao` FROM `tiporeligiao` WHERE `idreligiao` = OLD.`cod_religiao` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Religião:</b> ', CONCAT_WS( separador, var_old,'(NULL)' ) ) );

    ELSEIF ISNULL( OLD.`cod_religiao` ) THEN

        SET var_new = ( SELECT `religiao` FROM `tiporeligiao` WHERE `idreligiao` = NEW.`cod_religiao` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Religião:</b> ', CONCAT_WS( separador, '(NULL)', var_new ) ) );

    ELSE

        SET var_old = ( SELECT `religiao` FROM `tiporeligiao` WHERE `idreligiao` = OLD.`cod_religiao` LIMIT 1 );
        SET var_new = ( SELECT `religiao` FROM `tiporeligiao` WHERE `idreligiao` = NEW.`cod_religiao` LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Religião:</b> ', CONCAT_WS( separador, var_old, var_new ) ) );

    END IF;
END IF;

IF OLD.`possui_adv` != NEW.`possui_adv`
OR (ISNULL(NEW.`possui_adv`) AND NOT ISNULL(OLD.`possui_adv`))
OR (ISNULL(OLD.`possui_adv`) AND NOT ISNULL(NEW.`possui_adv`)) THEN
    IF ISNULL(NEW.`possui_adv`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Possui advogado:</b> ', CONCAT_WS( separador, OLD.`possui_adv`,'(NULL)')));
    ELSEIF ISNULL(OLD.`possui_adv`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Possui advogado:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`possui_adv`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Possui advogado:</b> ', CONCAT_WS( separador, OLD.`possui_adv`,NEW.`possui_adv`)));
    END IF;
END IF;

IF OLD.`caso_emergencia` != NEW.`caso_emergencia`
OR (ISNULL(NEW.`caso_emergencia`) AND NOT ISNULL(OLD.`caso_emergencia`))
OR (ISNULL(OLD.`caso_emergencia`) AND NOT ISNULL(NEW.`caso_emergencia`)) THEN
    IF ISNULL(NEW.`caso_emergencia`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Caso emergência, avisar:</b> ', CONCAT_WS( separador, OLD.`caso_emergencia`,'(NULL)')));
    ELSEIF ISNULL(OLD.`caso_emergencia`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Caso emergência, avisar:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`caso_emergencia`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Caso emergência, avisar:</b> ', CONCAT_WS( separador, OLD.`caso_emergencia`,NEW.`caso_emergencia`)));
    END IF;
END IF;

IF OLD.`obs_artigos` != NEW.`obs_artigos`
OR (ISNULL(NEW.`obs_artigos`) AND NOT ISNULL(OLD.`obs_artigos`))
OR (ISNULL(OLD.`obs_artigos`) AND NOT ISNULL(NEW.`obs_artigos`)) THEN
    IF ISNULL(NEW.`obs_artigos`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Outros artigos:</b> ', CONCAT_WS( separador, OLD.`obs_artigos`,'(NULL)')));
    ELSEIF ISNULL(OLD.`obs_artigos`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Outros artigos:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`obs_artigos`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Outros artigos:</b> ', CONCAT_WS( separador, OLD.`obs_artigos`,NEW.`obs_artigos`)));
    END IF;
END IF;

IF OLD.`data_quali` != NEW.`data_quali`
OR (ISNULL(NEW.`data_quali`) AND NOT ISNULL(OLD.`data_quali`))
OR (ISNULL(OLD.`data_quali`) AND NOT ISNULL(NEW.`data_quali`)) THEN
    IF ISNULL(NEW.`data_quali`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Data da qualificativa:</b> ', CONCAT_WS( separador, DATE_FORMAT(OLD.`data_quali`, '%d/%m/%Y'),'(NULL)')));
    ELSEIF ISNULL(OLD.`data_quali`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Data da qualificativa:</b> ', CONCAT_WS( separador, '(NULL)',DATE_FORMAT(NEW.`data_quali`, '%d/%m/%Y'))));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Data da qualificativa:</b> ', CONCAT_WS( separador, DATE_FORMAT(OLD.`data_quali`, '%d/%m/%Y'),DATE_FORMAT(NEW.`data_quali`, '%d/%m/%Y'))));
    END IF;
END IF;

IF OLD.`funcionario` != NEW.`funcionario`
OR (ISNULL(NEW.`funcionario`) AND NOT ISNULL(OLD.`funcionario`))
OR (ISNULL(OLD.`funcionario`) AND NOT ISNULL(NEW.`funcionario`)) THEN
    IF ISNULL(NEW.`funcionario`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Funcionário:</b> ', CONCAT_WS( separador, OLD.`funcionario`,'(NULL)')));
    ELSEIF ISNULL(OLD.`funcionario`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Funcionário:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`funcionario`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Funcionário:</b> ', CONCAT_WS( separador, OLD.`funcionario`,NEW.`funcionario`)));
    END IF;
END IF;

IF OLD.`cod_foto` != NEW.`cod_foto`
OR (ISNULL(NEW.`cod_foto`) AND NOT ISNULL(OLD.`cod_foto`))
OR (ISNULL(OLD.`cod_foto`) AND NOT ISNULL(NEW.`cod_foto`)) THEN
    IF ISNULL(NEW.`cod_foto`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Foto detento:</b> ', CONCAT_WS( separador, OLD.`cod_foto`,'(NULL)')));
    ELSEIF ISNULL(OLD.`cod_foto`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Foto detento:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`cod_foto`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Foto detento:</b> ', CONCAT_WS( separador, OLD.`cod_foto`,NEW.`cod_foto`)));
    END IF;
END IF;

IF OLD.`cod_cela` != NEW.`cod_cela`
OR ( ISNULL( NEW.`cod_cela` ) AND NOT ISNULL( OLD.`cod_cela` ) )
OR ( ISNULL( OLD.`cod_cela` ) AND NOT ISNULL( NEW.`cod_cela` ) ) THEN

    IF ISNULL( NEW.`cod_cela` ) THEN

        SET var_old = ( SELECT CONCAT( 'raio: ', `raio`.`raio`, ' cela: ', `cela`.`cela` )
                        FROM `cela`
                        INNER JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
                        WHERE `cela`.idcela = OLD.`cod_cela`
                        LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Raio/Cela:</b> ', CONCAT_WS( separador, var_old, '(NULL)' ) ) );

    ELSEIF ISNULL( OLD.`cod_cela` ) THEN

        SET var_new = ( SELECT CONCAT( 'raio: ', `raio`.`raio`, ' cela: ', `cela`.`cela` )
                        FROM `cela`
                        INNER JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
                        WHERE `cela`.idcela = NEW.`cod_cela`
                        LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Raio/Cela:</b> ', CONCAT_WS( separador, '(NULL)', var_new ) ) );

    ELSE

        SET var_old = ( SELECT CONCAT( 'raio: ', `raio`.`raio`, ' cela: ', `cela`.`cela` )
                        FROM `cela`
                        INNER JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
                        WHERE `cela`.idcela = OLD.`cod_cela`
                        LIMIT 1 );

        SET var_new = ( SELECT CONCAT( 'raio: ', `raio`.`raio`, ' cela: ', `cela`.`cela` )
                        FROM `cela`
                        INNER JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
                        WHERE `cela`.idcela = NEW.`cod_cela`
                        LIMIT 1 );

        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT( '<b>Raio/Cela:</b> ', CONCAT_WS( separador, var_old, var_new ) ) );

    END IF;
END IF;

IF OLD.`dados_prov` != NEW.`dados_prov`
OR (ISNULL(NEW.`dados_prov`) AND NOT ISNULL(OLD.`dados_prov`))
OR (ISNULL(OLD.`dados_prov`) AND NOT ISNULL(NEW.`dados_prov`)) THEN
    IF ISNULL(NEW.`dados_prov`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Dados provisórios:</b> ', CONCAT_WS( separador, OLD.`dados_prov`,'(NULL)')));
    ELSEIF ISNULL(OLD.`dados_prov`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Dados provisórios:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`dados_prov`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Dados provisórios:</b> ', CONCAT_WS( separador, OLD.`dados_prov`,NEW.`dados_prov`)));
    END IF;
END IF;

IF OLD.`motivo_prisao` != NEW.`motivo_prisao`
OR (ISNULL(NEW.`motivo_prisao`) AND NOT ISNULL(OLD.`motivo_prisao`))
OR (ISNULL(OLD.`motivo_prisao`) AND NOT ISNULL(NEW.`motivo_prisao`)) THEN
    IF ISNULL(NEW.`motivo_prisao`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Motivo da Prisão atual:</b> ', CONCAT_WS( separador, OLD.`motivo_prisao`,'(NULL)')));
    ELSEIF ISNULL(OLD.`motivo_prisao`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Motivo da Prisão atual:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`motivo_prisao`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Motivo da Prisão atual:</b> ', CONCAT_WS( separador, OLD.`motivo_prisao`,NEW.`motivo_prisao`)));
    END IF;
END IF;

IF OLD.`aut_visita` != NEW.`aut_visita`
OR (ISNULL(NEW.`aut_visita`) AND NOT ISNULL(OLD.`aut_visita`))
OR (ISNULL(OLD.`aut_visita`) AND NOT ISNULL(NEW.`aut_visita`)) THEN
    IF ISNULL(NEW.`aut_visita`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Pode receber visitas:</b> ', CONCAT_WS( separador, OLD.`aut_visita`,'(NULL)')));
    ELSEIF ISNULL(OLD.`aut_visita`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Pode receber visitas:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`aut_visita`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Pode receber visitas:</b> ', CONCAT_WS( separador, OLD.`aut_visita`,NEW.`aut_visita`)));
    END IF;
END IF;

IF OLD.`aut_sedex` != NEW.`aut_sedex`
OR (ISNULL(NEW.`aut_sedex`) AND NOT ISNULL(OLD.`aut_sedex`))
OR (ISNULL(OLD.`aut_sedex`) AND NOT ISNULL(NEW.`aut_sedex`)) THEN
    IF ISNULL(NEW.`aut_sedex`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Pode receber sedex:</b> ', CONCAT_WS( separador, OLD.`aut_sedex`,'(NULL)')));
    ELSEIF ISNULL(OLD.`aut_sedex`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Pode receber sedex:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`aut_sedex`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Pode receber sedex:</b> ', CONCAT_WS( separador, OLD.`aut_sedex`,NEW.`aut_sedex`)));
    END IF;
END IF;

IF OLD.`user_add` != NEW.`user_add`
OR (ISNULL(NEW.`user_add`) AND NOT ISNULL(OLD.`user_add`))
OR (ISNULL(OLD.`user_add`) AND NOT ISNULL(NEW.`user_add`)) THEN
    IF ISNULL(NEW.`user_add`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Usuário que criou o registro:</b> ', CONCAT_WS( separador, OLD.`user_add`,'(NULL)')));
    ELSEIF ISNULL(OLD.`user_add`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Usuário que criou o registro:</b> ', CONCAT_WS( separador, '(NULL)',NEW.`user_add`)));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Usuário que criou o registro:</b> ', CONCAT_WS( separador, OLD.`user_add`,NEW.`user_add`)));
    END IF;
END IF;

IF OLD.`data_add` != NEW.`data_add`
OR (ISNULL(NEW.`data_add`) AND NOT ISNULL(OLD.`data_add`))
OR (ISNULL(OLD.`data_add`) AND NOT ISNULL(NEW.`data_add`)) THEN
    IF ISNULL(NEW.`data_add`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Data da criação do registro:</b> ', CONCAT_WS( separador, DATE_FORMAT(OLD.`data_add`, '%d/%m/%Y'),'(NULL)')));
    ELSEIF ISNULL(OLD.`data_add`) THEN
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Data da criação do registro:</b> ', CONCAT_WS( separador, '(NULL)',DATE_FORMAT(NEW.`data_add`, '%d/%m/%Y'))));
    ELSE
        SET alteracoes =  CONCAT_WS( quebra, alteracoes, CONCAT('<b>Data da criação do registro:</b> ', CONCAT_WS( separador, DATE_FORMAT(OLD.`data_add`, '%d/%m/%Y'),DATE_FORMAT(NEW.`data_add`, '%d/%m/%Y'))));
    END IF;
END IF;

IF NOT ISNULL( alteracoes ) THEN

    INSERT INTO
      `log_alt`
        (
          `tabela`,
          `tipo_log`,
          `alteracao`,
          `user`,
          `ip`
        )
    VALUES
      (
        'DETENTOS',
        'ALTERAÇÃO',
        CONCAT
          (
            '[ DETENTO ]',
            quebra,
            '<b>Nome:</b> ', ( SELECT `nome_det` FROM `detentos` WHERE `iddetento` = NEW.`iddetento` ),
            quebra,
            '<b>matrícula:</b> ', IFNULL( ( SELECT `matricula` FROM `detentos` WHERE `iddetento` =  NEW.`iddetento` ), '(NULL)' ),
            quebra,
            '<b>ID:</b> ', NEW.`iddetento`,
            quebra,
            quebra,
            '[ ALTERAÇÕES ]',
            quebra,
            alteracoes
          ),
        NEW.`user_up`,
        NEW.`ip_up`
      );

END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `detentos_fotos_esp`
--

DROP TABLE IF EXISTS `detentos_fotos_esp`;
CREATE TABLE IF NOT EXISTS `detentos_fotos_esp` (
  `id_foto` int(10) unsigned NOT NULL,
  `cod_detento` int(10) unsigned DEFAULT NULL,
  `foto_det_g` varchar(250) DEFAULT NULL,
  `foto_det_p` varchar(250) DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_add` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `detentos_radio`
--

DROP TABLE IF EXISTS `detentos_radio`;
CREATE TABLE IF NOT EXISTS `detentos_radio` (
  `idradio` int(10) unsigned NOT NULL,
  `cod_detento` int(10) unsigned DEFAULT NULL,
  `cod_cela` smallint(3) unsigned DEFAULT NULL,
  `marca_radio` varchar(20) DEFAULT NULL,
  `cor_radio` varchar(20) DEFAULT NULL,
  `faixas` smallint(2) unsigned DEFAULT NULL,
  `lacre_1` smallint(5) unsigned DEFAULT NULL,
  `lacre_2` smallint(5) unsigned DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `detentos_tv`
--

DROP TABLE IF EXISTS `detentos_tv`;
CREATE TABLE IF NOT EXISTS `detentos_tv` (
  `idtv` int(10) unsigned NOT NULL,
  `cod_detento` int(10) unsigned DEFAULT NULL,
  `cod_cela` smallint(3) unsigned DEFAULT NULL,
  `marca_tv` varchar(20) DEFAULT NULL,
  `cor_tv` varchar(20) DEFAULT NULL,
  `polegadas` smallint(2) unsigned DEFAULT NULL,
  `lacre_1` smallint(5) unsigned DEFAULT NULL,
  `lacre_2` smallint(5) unsigned DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `digital`
--

DROP TABLE IF EXISTS `digital`;
CREATE TABLE IF NOT EXISTS `digital` (
  `iddigital` int(11) NOT NULL,
  `iddetento` int(11) NOT NULL,
  `hexpoldir` mediumblob NOT NULL,
  `hexinddir` mediumblob NOT NULL,
  `hexmeddir` mediumblob NOT NULL,
  `hexanedir` mediumblob NOT NULL,
  `hexmindir` mediumblob NOT NULL,
  `hexpolesq` mediumblob NOT NULL,
  `hexindesq` mediumblob NOT NULL,
  `hexmedesq` mediumblob NOT NULL,
  `hexaneesq` mediumblob NOT NULL,
  `hexminesq` mediumblob NOT NULL,
  `user` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `diretores`
--

DROP TABLE IF EXISTS `diretores`;
CREATE TABLE IF NOT EXISTS `diretores` (
  `iddiretores` int(11) unsigned NOT NULL,
  `diretor_geral` tinyint(2) unsigned DEFAULT NULL,
  `diretor_seg` tinyint(2) unsigned DEFAULT NULL,
  `diretor_pront` tinyint(2) unsigned DEFAULT NULL,
  `diretor_saude` tinyint(2) unsigned DEFAULT NULL,
  `diretor_rh` tinyint(2) unsigned DEFAULT NULL,
  `diretor_ca` tinyint(2) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `diretores_n`
--

DROP TABLE IF EXISTS `diretores_n`;
CREATE TABLE IF NOT EXISTS `diretores_n` (
  `iddiretoresn` tinyint(3) unsigned NOT NULL,
  `diretor` varchar(80) DEFAULT NULL,
  `titulo_diretor` varchar(60) DEFAULT NULL,
  `setor` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ativo` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estados`
--

DROP TABLE IF EXISTS `estados`;
CREATE TABLE IF NOT EXISTS `estados` (
  `idestado` smallint(3) unsigned NOT NULL,
  `sigla` char(2) NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `grade`
--

DROP TABLE IF EXISTS `grade`;
CREATE TABLE IF NOT EXISTS `grade` (
  `idprocesso` int(11) unsigned NOT NULL,
  `cod_detento` int(10) unsigned DEFAULT NULL,
  `gra_preso` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'INDICATIVO SE O DETENTO ESTA PRESO POR ESTE PROCESSO',
  `gra_num_in` smallint(3) unsigned DEFAULT NULL COMMENT 'NUMERO DA ENTRADA DO DETENTO',
  `gra_num_exec` smallint(3) unsigned DEFAULT NULL,
  `gra_num_inq` varchar(20) DEFAULT NULL,
  `gra_f_p` varchar(2) DEFAULT NULL,
  `gra_num_proc` varchar(20) DEFAULT NULL,
  `gra_campo_x` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `gra_med_seg` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `gra_hediondo` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `gra_fed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `gra_outro_est` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `gra_consumado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `gra_vara` varchar(15) DEFAULT NULL,
  `gra_comarca` varchar(30) DEFAULT NULL,
  `gra_artigos` varchar(50) DEFAULT NULL,
  `gra_data_delito` date DEFAULT NULL,
  `gra_data_sent` date DEFAULT NULL,
  `gra_p_ano` smallint(3) unsigned DEFAULT NULL,
  `gra_p_mes` smallint(3) unsigned DEFAULT NULL,
  `gra_p_dia` smallint(3) unsigned DEFAULT NULL,
  `gra_regime` varchar(10) DEFAULT NULL,
  `gra_sit_atual` varchar(30) DEFAULT NULL,
  `gra_obs` text,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Gatilhos `grade`
--
DROP TRIGGER IF EXISTS `grade_ai`;
DELIMITER $$
CREATE TRIGGER `grade_ai` AFTER INSERT ON `grade`
 FOR EACH ROW BEGIN

    INSERT INTO
      `log_alt`
        (
          `tabela`,
          `tipo_log`,
          `alteracao`,
          `user`,
          `ip`
        )
    VALUES
      (
        'GRADE',
        'CADASTRAMENTO',
        CONCAT
          (
            '[ PROCESSO ]',
            '<br />',
            '<b>Número do inquérito:</b> ', IFNULL( NEW.`gra_num_inq`, '(NULL)' ),
            ';<br /> <b>Número do processo:</b> ', IFNULL( NEW.`gra_num_proc`, '(NULL)' ),
            ';<br /> <b>ID do processo:</b> ', NEW.idprocesso,
            '<br /><br />',
            '[ DETENTO ]',
            '<br /><b>Nome:</b> ', ( SELECT `nome_det` FROM `detentos` WHERE `iddetento` = NEW.`cod_detento` ),
            ';<br /><b>Matrícula:</b> ', IFNULL( ( SELECT `matricula` FROM `detentos` WHERE `iddetento` =  NEW.`cod_detento` ), '(NULL)' ),
            ';<br /><b>ID:</b> ', NEW.`cod_detento`
          ),
        NEW.`user_add`,
        NEW.`ip_add`
      );

END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `grade_bd`;
DELIMITER $$
CREATE TRIGGER `grade_bd` BEFORE DELETE ON `grade`
 FOR EACH ROW BEGIN

    INSERT INTO
      `log_alt`
        (
          `tabela`,
          `tipo_log`,
          `alteracao`,
          `user`,
          `ip`
        )
    VALUES
      (
        'GRADE',
        'EXCLUSÃO',
        CONCAT
          (
            '[ PROCESSO ]',
            '<br />',
            '<b>Número do inquérito:</b> ', IFNULL( OLD.`gra_num_inq`, '(NULL)' ),
            ';<br /> <b>Número do processo:</b> ', IFNULL( OLD.`gra_num_proc`, '(NULL)' ),
            ';<br /> <b>ID do processo:</b> ', OLD.idprocesso,
            '<br /><br />',
            '[ DETENTO ]',
            '<br /><b>Nome:</b> ', ( SELECT `nome_det` FROM `detentos` WHERE `iddetento` = OLD.`cod_detento` ),
            ',<br /><b>matrícula:</b> ', IFNULL( ( SELECT `matricula` FROM `detentos` WHERE `iddetento` =  OLD.`cod_detento` ), '(NULL)' ),
            ',<br /><b>ID:</b> ', OLD.`cod_detento`
          ),
        OLD.`user_up`,
        OLD.`ip_up`
      );

END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `grade_bu`;
DELIMITER $$
CREATE TRIGGER `grade_bu` BEFORE UPDATE ON `grade`
 FOR EACH ROW /*
Trigger utilizada para registrar as alterações 

só pega os campos que foram alterados
*/

BEGIN

DECLARE alteracoes text;

IF OLD.`gra_preso` != NEW.`gra_preso`
OR (ISNULL(NEW.`gra_preso`) AND NOT ISNULL(OLD.`gra_preso`)) 
OR (ISNULL(OLD.`gra_preso`) AND NOT ISNULL(NEW.`gra_preso`)) THEN
	IF ISNULL(NEW.`gra_preso`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Detento preso pelo processo:</b> ', CONCAT_WS(' --> ', OLD.`gra_preso`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_preso`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Detento preso pelo processo:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_preso`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Detento preso pelo processo:</b> ', CONCAT_WS(' --> ', OLD.`gra_preso`,NEW.`gra_preso`)));
	END IF;
END IF;

IF OLD.`gra_num_in` != NEW.`gra_num_in`
OR (ISNULL(NEW.`gra_num_in`) AND NOT ISNULL(OLD.`gra_num_in`)) 
OR (ISNULL(OLD.`gra_num_in`) AND NOT ISNULL(NEW.`gra_num_in`)) THEN
	IF ISNULL(NEW.`gra_num_in`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Número de entradas:</b> ', CONCAT_WS(' --> ', OLD.`gra_num_in`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_num_in`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Número de entradas:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_num_in`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Número de entradas:</b> ', CONCAT_WS(' --> ', OLD.`gra_num_in`,NEW.`gra_num_in`)));
	END IF;
END IF;

IF OLD.`gra_num_exec` != NEW.`gra_num_exec`
OR (ISNULL(NEW.`gra_num_exec`) AND NOT ISNULL(OLD.`gra_num_exec`)) 
OR (ISNULL(OLD.`gra_num_exec`) AND NOT ISNULL(NEW.`gra_num_exec`)) THEN
	IF ISNULL(NEW.`gra_num_exec`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Número índice da execução:</b> ', CONCAT_WS(' --> ', OLD.`gra_num_exec`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_num_exec`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Número índice da execução:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_num_exec`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Número índice da execução:</b> ', CONCAT_WS(' --> ', OLD.`gra_num_exec`,NEW.`gra_num_exec`)));
	END IF;
END IF;

IF OLD.`gra_num_inq` != NEW.`gra_num_inq`
OR (ISNULL(NEW.`gra_num_inq`) AND NOT ISNULL(OLD.`gra_num_inq`)) 
OR (ISNULL(OLD.`gra_num_inq`) AND NOT ISNULL(NEW.`gra_num_inq`)) THEN
	IF ISNULL(NEW.`gra_num_inq`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Número do inquérito:</b> ', CONCAT_WS(' --> ', OLD.`gra_num_inq`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_num_inq`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Número do inquérito:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_num_inq`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Número do inquérito:</b> ', CONCAT_WS(' --> ', OLD.`gra_num_inq`,NEW.`gra_num_inq`)));
	END IF;
END IF;

IF OLD.`gra_f_p` != NEW.`gra_f_p`
OR (ISNULL(NEW.`gra_f_p`) AND NOT ISNULL(OLD.`gra_f_p`)) 
OR (ISNULL(OLD.`gra_f_p`) AND NOT ISNULL(NEW.`gra_f_p`)) THEN
	IF ISNULL(NEW.`gra_f_p`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Flagrante/Portaria:</b> ', CONCAT_WS(' --> ', OLD.`gra_f_p`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_f_p`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Flagrante/Portaria:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_f_p`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Flagrante/Portaria:</b> ', CONCAT_WS(' --> ', OLD.`gra_f_p`,NEW.`gra_f_p`)));
	END IF;
END IF;

IF OLD.`gra_num_proc` != NEW.`gra_num_proc`
OR (ISNULL(NEW.`gra_num_proc`) AND NOT ISNULL(OLD.`gra_num_proc`)) 
OR (ISNULL(OLD.`gra_num_proc`) AND NOT ISNULL(NEW.`gra_num_proc`)) THEN
	IF ISNULL(NEW.`gra_num_proc`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Número do processo:</b> ', CONCAT_WS(' --> ', OLD.`gra_num_proc`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_num_proc`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Número do processo:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_num_proc`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Número do processo:</b> ', CONCAT_WS(' --> ', OLD.`gra_num_proc`,NEW.`gra_num_proc`)));
	END IF;
END IF;

IF OLD.`gra_campo_x` != NEW.`gra_campo_x`
OR (ISNULL(NEW.`gra_campo_x`) AND NOT ISNULL(OLD.`gra_campo_x`)) 
OR (ISNULL(OLD.`gra_campo_x`) AND NOT ISNULL(NEW.`gra_campo_x`)) THEN
	IF ISNULL(NEW.`gra_campo_x`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Extinsão da punibilidade:</b> ', CONCAT_WS(' --> ', OLD.`gra_campo_x`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_campo_x`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Extinsão da punibilidade:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_campo_x`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Extinsão da punibilidade:</b> ', CONCAT_WS(' --> ', OLD.`gra_campo_x`,NEW.`gra_campo_x`)));
	END IF;
END IF;

IF OLD.`gra_med_seg` != NEW.`gra_med_seg`
OR (ISNULL(NEW.`gra_med_seg`) AND NOT ISNULL(OLD.`gra_med_seg`)) 
OR (ISNULL(OLD.`gra_med_seg`) AND NOT ISNULL(NEW.`gra_med_seg`)) THEN
	IF ISNULL(NEW.`gra_med_seg`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Medida de segurança:</b> ', CONCAT_WS(' --> ', OLD.`gra_med_seg`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_med_seg`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Medida de segurança:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_med_seg`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Medida de segurança:</b> ', CONCAT_WS(' --> ', OLD.`gra_med_seg`,NEW.`gra_med_seg`)));
	END IF;
END IF;

IF OLD.`gra_hediondo` != NEW.`gra_hediondo`
OR (ISNULL(NEW.`gra_hediondo`) AND NOT ISNULL(OLD.`gra_hediondo`)) 
OR (ISNULL(OLD.`gra_hediondo`) AND NOT ISNULL(NEW.`gra_hediondo`)) THEN
	IF ISNULL(NEW.`gra_hediondo`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Crime hediondo:</b> ', CONCAT_WS(' --> ', OLD.`gra_hediondo`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_hediondo`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Crime hediondo:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_hediondo`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Crime hediondo:</b> ', CONCAT_WS(' --> ', OLD.`gra_hediondo`,NEW.`gra_hediondo`)));
	END IF;
END IF;

IF OLD.`gra_fed` != NEW.`gra_fed`
OR (ISNULL(NEW.`gra_fed`) AND NOT ISNULL(OLD.`gra_fed`)) 
OR (ISNULL(OLD.`gra_fed`) AND NOT ISNULL(NEW.`gra_fed`)) THEN
	IF ISNULL(NEW.`gra_fed`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Federal:</b> ', CONCAT_WS(' --> ', OLD.`gra_fed`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_fed`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Federal:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_fed`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Federal:</b> ', CONCAT_WS(' --> ', OLD.`gra_fed`,NEW.`gra_fed`)));
	END IF;
END IF;

IF OLD.`gra_outro_est` != NEW.`gra_outro_est`
OR (ISNULL(NEW.`gra_outro_est`) AND NOT ISNULL(OLD.`gra_outro_est`)) 
OR (ISNULL(OLD.`gra_outro_est`) AND NOT ISNULL(NEW.`gra_outro_est`)) THEN
	IF ISNULL(NEW.`gra_outro_est`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Outro estado:</b> ', CONCAT_WS(' --> ', OLD.`gra_outro_est`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_outro_est`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Outro estado:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_outro_est`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Outro estado:</b> ', CONCAT_WS(' --> ', OLD.`gra_outro_est`,NEW.`gra_outro_est`)));
	END IF;
END IF;

IF OLD.`gra_consumado` != NEW.`gra_consumado`
OR (ISNULL(NEW.`gra_consumado`) AND NOT ISNULL(OLD.`gra_consumado`)) 
OR (ISNULL(OLD.`gra_consumado`) AND NOT ISNULL(NEW.`gra_consumado`)) THEN
	IF ISNULL(NEW.`gra_consumado`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Consumado:</b> ', CONCAT_WS(' --> ', OLD.`gra_consumado`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_consumado`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Consumado:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_consumado`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Consumado:</b> ', CONCAT_WS(' --> ', OLD.`gra_consumado`,NEW.`gra_consumado`)));
	END IF;
END IF;

IF OLD.`gra_vara` != NEW.`gra_vara`
OR (ISNULL(NEW.`gra_vara`) AND NOT ISNULL(OLD.`gra_vara`)) 
OR (ISNULL(OLD.`gra_vara`) AND NOT ISNULL(NEW.`gra_vara`)) THEN
	IF ISNULL(NEW.`gra_vara`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Vara:</b> ', CONCAT_WS(' --> ', OLD.`gra_vara`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_vara`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Vara:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_vara`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Vara:</b> ', CONCAT_WS(' --> ', OLD.`gra_vara`,NEW.`gra_vara`)));
	END IF;
END IF;

IF OLD.`gra_comarca` != NEW.`gra_comarca`
OR (ISNULL(NEW.`gra_comarca`) AND NOT ISNULL(OLD.`gra_comarca`)) 
OR (ISNULL(OLD.`gra_comarca`) AND NOT ISNULL(NEW.`gra_comarca`)) THEN
	IF ISNULL(NEW.`gra_comarca`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Comarca:</b> ', CONCAT_WS(' --> ', OLD.`gra_comarca`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_comarca`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Comarca:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_comarca`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Comarca:</b> ', CONCAT_WS(' --> ', OLD.`gra_comarca`,NEW.`gra_comarca`)));
	END IF;
END IF;

IF OLD.`gra_artigos` != NEW.`gra_artigos`
OR (ISNULL(NEW.`gra_artigos`) AND NOT ISNULL(OLD.`gra_artigos`)) 
OR (ISNULL(OLD.`gra_artigos`) AND NOT ISNULL(NEW.`gra_artigos`)) THEN
	IF ISNULL(NEW.`gra_artigos`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Artigos:</b> ', CONCAT_WS(' --> ', OLD.`gra_artigos`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_artigos`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Artigos:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_artigos`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Artigos:</b> ', CONCAT_WS(' --> ', OLD.`gra_artigos`,NEW.`gra_artigos`)));
	END IF;
END IF;

IF OLD.`gra_data_delito` != NEW.`gra_data_delito`
OR (ISNULL(NEW.`gra_data_delito`) AND NOT ISNULL(OLD.`gra_data_delito`)) 
OR (ISNULL(OLD.`gra_data_delito`) AND NOT ISNULL(NEW.`gra_data_delito`)) THEN
	IF ISNULL(NEW.`gra_data_delito`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Data do delito:</b> ', CONCAT_WS(' --> ', OLD.`gra_data_delito`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_data_delito`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Data do delito:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_data_delito`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Data do delito:</b> ', CONCAT_WS(' --> ', OLD.`gra_data_delito`,NEW.`gra_data_delito`)));
	END IF;
END IF;

IF OLD.`gra_data_sent` != NEW.`gra_data_sent`
OR (ISNULL(NEW.`gra_data_sent`) AND NOT ISNULL(OLD.`gra_data_sent`)) 
OR (ISNULL(OLD.`gra_data_sent`) AND NOT ISNULL(NEW.`gra_data_sent`)) THEN
	IF ISNULL(NEW.`gra_data_sent`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Data da sentença:</b> ', CONCAT_WS(' --> ', OLD.`gra_data_sent`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_data_sent`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Data da sentença:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_data_sent`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Data da sentença:</b> ', CONCAT_WS(' --> ', OLD.`gra_data_sent`,NEW.`gra_data_sent`)));
	END IF;
END IF;

IF OLD.`gra_p_ano` != NEW.`gra_p_ano`
OR (ISNULL(NEW.`gra_p_ano`) AND NOT ISNULL(OLD.`gra_p_ano`)) 
OR (ISNULL(OLD.`gra_p_ano`) AND NOT ISNULL(NEW.`gra_p_ano`)) THEN
	IF ISNULL(NEW.`gra_p_ano`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Cond. anos:</b> ', CONCAT_WS(' --> ', OLD.`gra_p_ano`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_p_ano`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Cond. anos:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_p_ano`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Cond. anos:</b> ', CONCAT_WS(' --> ', OLD.`gra_p_ano`,NEW.`gra_p_ano`)));
	END IF;
END IF;

IF OLD.`gra_p_mes` != NEW.`gra_p_mes`
OR (ISNULL(NEW.`gra_p_mes`) AND NOT ISNULL(OLD.`gra_p_mes`)) 
OR (ISNULL(OLD.`gra_p_mes`) AND NOT ISNULL(NEW.`gra_p_mes`)) THEN
	IF ISNULL(NEW.`gra_p_mes`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Cond. meses:</b> ', CONCAT_WS(' --> ', OLD.`gra_p_mes`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_p_mes`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Cond. meses:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_p_mes`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Cond. meses:</b> ', CONCAT_WS(' --> ', OLD.`gra_p_mes`,NEW.`gra_p_mes`)));
	END IF;
END IF;

IF OLD.`gra_p_dia` != NEW.`gra_p_dia`
OR (ISNULL(NEW.`gra_p_dia`) AND NOT ISNULL(OLD.`gra_p_dia`)) 
OR (ISNULL(OLD.`gra_p_dia`) AND NOT ISNULL(NEW.`gra_p_dia`)) THEN
	IF ISNULL(NEW.`gra_p_dia`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Cond. dias:</b> ', CONCAT_WS(' --> ', OLD.`gra_p_dia`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_p_dia`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Cond. dias:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_p_dia`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Cond. dias:</b> ', CONCAT_WS(' --> ', OLD.`gra_p_dia`,NEW.`gra_p_dia`)));
	END IF;
END IF;

IF OLD.`gra_regime` != NEW.`gra_regime`
OR (ISNULL(NEW.`gra_regime`) AND NOT ISNULL(OLD.`gra_regime`)) 
OR (ISNULL(OLD.`gra_regime`) AND NOT ISNULL(NEW.`gra_regime`)) THEN
	IF ISNULL(NEW.`gra_regime`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Regime:</b> ', CONCAT_WS(' --> ', OLD.`gra_regime`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_regime`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Regime:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_regime`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Regime:</b> ', CONCAT_WS(' --> ', OLD.`gra_regime`,NEW.`gra_regime`)));
	END IF;
END IF;

IF OLD.`gra_sit_atual` != NEW.`gra_sit_atual`
OR (ISNULL(NEW.`gra_sit_atual`) AND NOT ISNULL(OLD.`gra_sit_atual`)) 
OR (ISNULL(OLD.`gra_sit_atual`) AND NOT ISNULL(NEW.`gra_sit_atual`)) THEN
	IF ISNULL(NEW.`gra_sit_atual`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Situação atual:</b> ', CONCAT_WS(' --> ', OLD.`gra_sit_atual`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_sit_atual`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Situação atual:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_sit_atual`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Situação atual:</b> ', CONCAT_WS(' --> ', OLD.`gra_sit_atual`,NEW.`gra_sit_atual`)));
	END IF;
END IF;

IF OLD.`gra_obs` != NEW.`gra_obs`
OR (ISNULL(NEW.`gra_obs`) AND NOT ISNULL(OLD.`gra_obs`)) 
OR (ISNULL(OLD.`gra_obs`) AND NOT ISNULL(NEW.`gra_obs`)) THEN
	IF ISNULL(NEW.`gra_obs`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Observações:</b> ', CONCAT_WS(' --> ', OLD.`gra_obs`,'(NULL)')));
	ELSEIF ISNULL(OLD.`gra_obs`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Observações:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`gra_obs`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Observações:</b> ', CONCAT_WS(' --> ', OLD.`gra_obs`,NEW.`gra_obs`)));
	END IF;
END IF;

IF OLD.`user_add` != NEW.`user_add` 
OR (ISNULL(NEW.`user_add`) AND NOT ISNULL(OLD.`user_add`)) 
OR (ISNULL(OLD.`user_add`) AND NOT ISNULL(NEW.`user_add`)) THEN
	IF ISNULL(NEW.`user_add`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Usuário que criou o registro:</b> ', CONCAT_WS(' --> ', OLD.`user_add`,'(NULL)')));
	ELSEIF ISNULL(OLD.`user_add`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Usuário que criou o registro:</b> ', CONCAT_WS(' --> ', '(NULL)',NEW.`user_add`)));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Usuário que criou o registro:</b> ', CONCAT_WS(' --> ', OLD.`user_add`,NEW.`user_add`)));
	END IF;
END IF;

IF OLD.`data_add` != NEW.`data_add` 
OR (ISNULL(NEW.`data_add`) AND NOT ISNULL(OLD.`data_add`)) 
OR (ISNULL(OLD.`data_add`) AND NOT ISNULL(NEW.`data_add`)) THEN

	IF ISNULL(NEW.`data_add`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Data da criação do registro:</b> ', CONCAT_WS(' --> ', DATE_FORMAT(OLD.`data_add`, '%d/%m/%Y'),'(NULL)')));
	ELSEIF ISNULL(OLD.`data_add`) THEN
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Data da criação do registro:</b> ', CONCAT_WS(' --> ', '(NULL)',DATE_FORMAT(NEW.`data_add`, '%d/%m/%Y'))));
	ELSE
		SET alteracoes =  CONCAT_WS(';<br />', alteracoes, CONCAT('<b>Data da criação do registro:</b> ', CONCAT_WS(' --> ', DATE_FORMAT(OLD.`data_add`, '%d/%m/%Y'),DATE_FORMAT(NEW.`data_add`, '%d/%m/%Y'))));
	END IF;
END IF;

IF NOT ISNULL( alteracoes ) THEN

    INSERT INTO 
      `log_alt`
        ( 
          `tabela`,
          `tipo_log`, 
          `alteracao`, 
          `user`, 
          `ip` 
        )
    VALUES
      ( 
        'GRADE', 
        'ALTERAÇÃO',
        CONCAT
          ( 
            '[ PROCESSO ]',
            '<br />', 
            '<b>Número do inquérito:</b> ', IFNULL( NEW.`gra_num_inq`, '(NULL)' ), 
            ';<br /><b>Número do processo:</b> ', IFNULL( NEW.`gra_num_proc`, '(NULL)' ), 
            ';<br /><b>ID do processo:</b> ', NEW.idprocesso,
            '<br /><br />',
            '[ DETENTO ]',
            '<br /><b>Nome:</b> ', ( SELECT `nome_det` FROM `detentos` WHERE `iddetento` = NEW.`cod_detento` ), 
            ';<br /><b>Matrícula:</b> ', IFNULL( ( SELECT `matricula` FROM `detentos` WHERE `iddetento` =  NEW.`cod_detento` ), '(NULL)' ),
            ';<br /><b>ID:</b> ', NEW.`cod_detento`,
            '<br /><br />', 
            '[ ALTERAÇÕES ]',
            '<br />',
            alteracoes 
          ), 
        NEW.`user_up`, 
        NEW.`ip_up` 
      );

END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gsa_cidades`
--

DROP TABLE IF EXISTS `gsa_cidades`;
CREATE TABLE IF NOT EXISTS `gsa_cidades` (
  `gsa_cidade_id` smallint(6) unsigned NOT NULL,
  `gsa_cidade_cod` varchar(10) DEFAULT NULL,
  `gsa_cidade_nome` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gsa_depol`
--

DROP TABLE IF EXISTS `gsa_depol`;
CREATE TABLE IF NOT EXISTS `gsa_depol` (
  `gsa_depol_id` smallint(6) unsigned NOT NULL,
  `gsa_depol_cod` varchar(12) DEFAULT NULL,
  `gsa_depol_nome` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `inteligencia`
--

DROP TABLE IF EXISTS `inteligencia`;
CREATE TABLE IF NOT EXISTS `inteligencia` (
  `idinteli` int(10) unsigned NOT NULL,
  `cod_detento` int(10) unsigned DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `listatel`
--

DROP TABLE IF EXISTS `listatel`;
CREATE TABLE IF NOT EXISTS `listatel` (
  `idlistatel` int(10) unsigned NOT NULL,
  `tel_local` varchar(150) DEFAULT NULL,
  `tel_end` tinytext,
  `tel_cep` int(8) unsigned zerofill DEFAULT NULL,
  `tel_codmin` varchar(10) DEFAULT NULL,
  `tel_diretor` varchar(100) DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `listatel_num`
--

DROP TABLE IF EXISTS `listatel_num`;
CREATE TABLE IF NOT EXISTS `listatel_num` (
  `idlistatel_num` int(10) unsigned NOT NULL,
  `cod_listatel` int(10) unsigned NOT NULL,
  `ltn_num` bigint(10) unsigned zerofill DEFAULT NULL,
  `ltn_ramal` smallint(5) unsigned DEFAULT NULL,
  `ltn_desc` varchar(30) DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `locais_apr`
--

DROP TABLE IF EXISTS `locais_apr`;
CREATE TABLE IF NOT EXISTS `locais_apr` (
  `idlocal` int(10) unsigned NOT NULL,
  `local_apr` varchar(50) DEFAULT NULL,
  `local_end` varchar(100) DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TABELA PARA OS LOCAIS DE APRESENTAÇÃO';

-- --------------------------------------------------------

--
-- Estrutura para tabela `log_alt`
--

DROP TABLE IF EXISTS `log_alt`;
CREATE TABLE IF NOT EXISTS `log_alt` (
  `idlog_alt` int(10) unsigned NOT NULL,
  `tabela` varchar(255) DEFAULT NULL,
  `tipo_log` varchar(15) DEFAULT NULL,
  `alteracao` text,
  `user` smallint(3) DEFAULT NULL,
  `data` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` char(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='TABELA DE LOG DE ALTERAÇÕES ';

-- --------------------------------------------------------

--
-- Estrutura para tabela `log_det`
--

DROP TABLE IF EXISTS `log_det`;
CREATE TABLE IF NOT EXISTS `log_det` (
  `idlog_det` int(10) unsigned NOT NULL,
  `detento` varchar(255) DEFAULT NULL,
  `matricula` mediumint(9) DEFAULT NULL,
  `tipo_log` varchar(15) DEFAULT NULL,
  `alteracao` text,
  `user` smallint(3) DEFAULT NULL,
  `data` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TABELA DE LOG DE ALTERAÇÕES DA TABELA DETENTOS';

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

DROP TABLE IF EXISTS `logs`;
CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(10) unsigned NOT NULL,
  `hora` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` char(15) DEFAULT NULL,
  `id_user` smallint(3) unsigned DEFAULT NULL,
  `mensagem` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `model_of_apr`
--

DROP TABLE IF EXISTS `model_of_apr`;
CREATE TABLE IF NOT EXISTS `model_of_apr` (
  `idmodel` int(10) unsigned NOT NULL,
  `dest_sup` varchar(40) DEFAULT NULL,
  `corpo` varchar(150) DEFAULT NULL,
  `referente` varchar(60) DEFAULT NULL,
  `prostetos` varchar(150) DEFAULT NULL,
  `tratamento` varchar(20) DEFAULT NULL,
  `dest_inf` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mov_det`
--

DROP TABLE IF EXISTS `mov_det`;
CREATE TABLE IF NOT EXISTS `mov_det` (
  `id_mov` int(10) unsigned NOT NULL,
  `cod_detento` int(10) unsigned NOT NULL,
  `cod_tipo_mov` smallint(2) unsigned NOT NULL,
  `cod_local_mov` smallint(5) unsigned DEFAULT NULL,
  `data_mov` date NOT NULL,
  `obs_mov` varchar(255) DEFAULT NULL,
  `cancel` tinyint(3) unsigned DEFAULT '0',
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tabela para registro de movimentações de detentos';

-- --------------------------------------------------------

--
-- Estrutura para tabela `mov_rc_det`
--

DROP TABLE IF EXISTS `mov_rc_det`;
CREATE TABLE IF NOT EXISTS `mov_rc_det` (
  `id_mov_rc` int(10) unsigned NOT NULL,
  `cod_detento` int(10) unsigned NOT NULL,
  `cod_old_cela` smallint(5) unsigned DEFAULT NULL,
  `cod_n_cela` smallint(5) unsigned NOT NULL,
  `data_rc` date NOT NULL,
  `obs_rc` varchar(255) DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='TABELA PARA REGISTRO DE MOVIMENTAÇÃO DE RAIO E CELA';

-- --------------------------------------------------------

--
-- Estrutura para tabela `msg`
--

DROP TABLE IF EXISTS `msg`;
CREATE TABLE IF NOT EXISTS `msg` (
  `idmsg` int(10) unsigned NOT NULL,
  `msg_titulo` varchar(150) NOT NULL,
  `msg_corpo` text NOT NULL,
  `msg_de` smallint(3) unsigned NOT NULL,
  `msg_para` smallint(3) unsigned NOT NULL,
  `msg_de_lida` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `msg_para_lida` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `msg_adm_lida` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `msg_de_exc` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `msg_para_exc` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `msg_de_vdata` datetime DEFAULT NULL,
  `msg_para_vdata` datetime DEFAULT NULL,
  `msg_adm_vdata` datetime DEFAULT NULL,
  `msg_de_ultdata` datetime DEFAULT NULL,
  `msg_para_ultdata` datetime DEFAULT NULL,
  `msg_adm_ultdata` datetime DEFAULT NULL,
  `msg_de_exdata` datetime DEFAULT NULL,
  `msg_para_exdata` datetime DEFAULT NULL,
  `msg_add` datetime DEFAULT NULL,
  `msg_ip` char(15) DEFAULT NULL,
  `msg_block` tinyint(1) unsigned DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `numeroapcc`
--

DROP TABLE IF EXISTS `numeroapcc`;
CREATE TABLE IF NOT EXISTS `numeroapcc` (
  `idnumapcc` int(10) unsigned NOT NULL,
  `numero_apcc` smallint(3) unsigned zerofill DEFAULT NULL,
  `ano` year(4) NOT NULL DEFAULT '0000',
  `iduser` smallint(3) unsigned DEFAULT NULL,
  `idsetor` smallint(3) unsigned DEFAULT NULL,
  `coment` text,
  `dataadd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `numerofax`
--

DROP TABLE IF EXISTS `numerofax`;
CREATE TABLE IF NOT EXISTS `numerofax` (
  `idnumfax` int(10) unsigned NOT NULL,
  `numero_fax` smallint(3) unsigned zerofill DEFAULT NULL,
  `ano` year(4) NOT NULL DEFAULT '0000',
  `iduser` smallint(3) unsigned DEFAULT NULL,
  `idsetor` smallint(3) unsigned DEFAULT NULL,
  `coment` text,
  `dataadd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `numeronotes`
--

DROP TABLE IF EXISTS `numeronotes`;
CREATE TABLE IF NOT EXISTS `numeronotes` (
  `idnumnotes` int(10) unsigned NOT NULL,
  `numero_notes` smallint(3) unsigned zerofill DEFAULT NULL,
  `ano` year(4) NOT NULL DEFAULT '0000',
  `iduser` smallint(3) unsigned DEFAULT NULL,
  `idsetor` smallint(3) unsigned DEFAULT NULL,
  `coment` text,
  `dataadd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `numeroof`
--

DROP TABLE IF EXISTS `numeroof`;
CREATE TABLE IF NOT EXISTS `numeroof` (
  `idnumof` int(10) unsigned NOT NULL,
  `numero_of` smallint(3) unsigned zerofill DEFAULT NULL,
  `ano` year(4) NOT NULL DEFAULT '0000',
  `iduser` smallint(3) unsigned DEFAULT NULL,
  `idsetor` smallint(3) unsigned DEFAULT NULL,
  `coment` text,
  `dataadd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `numeroreq`
--

DROP TABLE IF EXISTS `numeroreq`;
CREATE TABLE IF NOT EXISTS `numeroreq` (
  `idnumreq` int(10) unsigned NOT NULL,
  `numero_req` smallint(3) unsigned zerofill DEFAULT NULL,
  `ano` year(4) NOT NULL DEFAULT '0000',
  `iduser` smallint(3) unsigned DEFAULT NULL,
  `idsetor` smallint(3) unsigned DEFAULT NULL,
  `coment` text,
  `dataadd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `numerorms`
--

DROP TABLE IF EXISTS `numerorms`;
CREATE TABLE IF NOT EXISTS `numerorms` (
  `idnumrms` int(10) unsigned NOT NULL,
  `numero_rms` smallint(3) unsigned zerofill DEFAULT NULL,
  `ano` year(4) NOT NULL DEFAULT '0000',
  `iduser` smallint(3) unsigned DEFAULT NULL,
  `idsetor` smallint(3) unsigned DEFAULT NULL,
  `coment` text,
  `dataadd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `obs_aud`
--

DROP TABLE IF EXISTS `obs_aud`;
CREATE TABLE IF NOT EXISTS `obs_aud` (
  `id_obs_aud` int(10) unsigned NOT NULL,
  `cod_audiencia` int(10) unsigned NOT NULL,
  `obs_aud` text NOT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TABELA DE OBSERVAÇOES DE AUDIENCIA';

-- --------------------------------------------------------

--
-- Estrutura para tabela `obs_det`
--

DROP TABLE IF EXISTS `obs_det`;
CREATE TABLE IF NOT EXISTS `obs_det` (
  `id_obs_det` int(10) unsigned NOT NULL,
  `cod_detento` int(10) unsigned NOT NULL,
  `obs_det` text NOT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TABELA DE OBSERVAÇOES DO DETENTO';

-- --------------------------------------------------------

--
-- Estrutura para tabela `obs_grade`
--

DROP TABLE IF EXISTS `obs_grade`;
CREATE TABLE IF NOT EXISTS `obs_grade` (
  `id_obs_grade` int(10) unsigned NOT NULL,
  `cod_detento` int(10) unsigned NOT NULL,
  `obs_grade` text NOT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='TABELA DE OBSERVAÇOES DE PROCESSOS/GRADE';

-- --------------------------------------------------------

--
-- Estrutura para tabela `obs_inteli`
--

DROP TABLE IF EXISTS `obs_inteli`;
CREATE TABLE IF NOT EXISTS `obs_inteli` (
  `id_obs_inteli` int(10) unsigned NOT NULL,
  `cod_inteli` int(10) unsigned DEFAULT NULL,
  `obs_inteli` text NOT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='TABELA DE OBSERVAÃ‡OES DA INTELIGÃŠNCIA';

-- --------------------------------------------------------

--
-- Estrutura para tabela `obs_listatel`
--

DROP TABLE IF EXISTS `obs_listatel`;
CREATE TABLE IF NOT EXISTS `obs_listatel` (
  `id_obs_listatel` int(10) unsigned NOT NULL,
  `cod_listatel` int(10) unsigned DEFAULT NULL,
  `obs_listatel` text NOT NULL,
  `user_add` smallint(3) unsigned NOT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='TABELA DE OBSERVAÇOES DE RÁDIOS';

-- --------------------------------------------------------

--
-- Estrutura para tabela `obs_pda`
--

DROP TABLE IF EXISTS `obs_pda`;
CREATE TABLE IF NOT EXISTS `obs_pda` (
  `id_obs_pda` int(10) unsigned NOT NULL,
  `cod_pda` int(10) unsigned NOT NULL,
  `obs_pda` text NOT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TABELA DE OBSERVAÇOES DE PDA';

-- --------------------------------------------------------

--
-- Estrutura para tabela `obs_radio`
--

DROP TABLE IF EXISTS `obs_radio`;
CREATE TABLE IF NOT EXISTS `obs_radio` (
  `id_obs_radio` int(10) unsigned NOT NULL,
  `cod_radio` int(10) unsigned NOT NULL,
  `obs_radio` text NOT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='TABELA DE OBSERVAÇOES DE RÁDIOS';

-- --------------------------------------------------------

--
-- Estrutura para tabela `obs_rol`
--

DROP TABLE IF EXISTS `obs_rol`;
CREATE TABLE IF NOT EXISTS `obs_rol` (
  `id_obs_rol` int(10) unsigned NOT NULL,
  `cod_detento` int(10) unsigned NOT NULL,
  `obs_rol` text NOT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='TABELA DE OBSERVAÇOES DE PROCESSOS/GRADE';

-- --------------------------------------------------------

--
-- Estrutura para tabela `obs_tv`
--

DROP TABLE IF EXISTS `obs_tv`;
CREATE TABLE IF NOT EXISTS `obs_tv` (
  `id_obs_tv` int(10) unsigned NOT NULL,
  `cod_tv` int(10) unsigned NOT NULL,
  `obs_tv` text NOT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='TABELA DE OBSERVAÇOES DE TVs';

-- --------------------------------------------------------

--
-- Estrutura para tabela `obs_visit`
--

DROP TABLE IF EXISTS `obs_visit`;
CREATE TABLE IF NOT EXISTS `obs_visit` (
  `id_obs_visit` int(10) unsigned NOT NULL,
  `cod_visita` int(10) unsigned NOT NULL,
  `obs_visit` text NOT NULL,
  `destacar` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TABELA DE OBSERVAÇOES DO VISITANTE';

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordens_escolta`
--

DROP TABLE IF EXISTS `ordens_escolta`;
CREATE TABLE IF NOT EXISTS `ordens_escolta` (
  `idescolta` int(10) unsigned NOT NULL,
  `cod_num_of` int(10) unsigned DEFAULT NULL,
  `escolta_data` date DEFAULT NULL,
  `escolta_hora` time DEFAULT NULL,
  `finalidade` varchar(50) DEFAULT NULL,
  `retorno` tinyint(1) unsigned DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordens_escolta_det`
--

DROP TABLE IF EXISTS `ordens_escolta_det`;
CREATE TABLE IF NOT EXISTS `ordens_escolta_det` (
  `id_escolta_det` int(10) unsigned NOT NULL,
  `cod_local_escolta` int(10) unsigned DEFAULT NULL,
  `cod_detento` int(10) unsigned DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `cod_tipo` smallint(3) unsigned DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordens_escolta_locais`
--

DROP TABLE IF EXISTS `ordens_escolta_locais`;
CREATE TABLE IF NOT EXISTS `ordens_escolta_locais` (
  `id_local_escolta` int(10) unsigned NOT NULL,
  `cod_escolta` int(10) unsigned DEFAULT NULL,
  `cod_local` int(10) unsigned DEFAULT NULL,
  `local_hora` time DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordens_escolta_tipo`
--

DROP TABLE IF EXISTS `ordens_escolta_tipo`;
CREATE TABLE IF NOT EXISTS `ordens_escolta_tipo` (
  `id_tipo` smallint(3) unsigned NOT NULL,
  `tipo` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordens_saida`
--

DROP TABLE IF EXISTS `ordens_saida`;
CREATE TABLE IF NOT EXISTS `ordens_saida` (
  `id_ord_saida` int(10) unsigned NOT NULL,
  `ord_saida_data` date DEFAULT NULL,
  `ord_saida_hora` time DEFAULT NULL,
  `finalidade` varchar(50) DEFAULT NULL,
  `responsavel_escolta` varchar(50) DEFAULT NULL,
  `retorno` tinyint(1) unsigned DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordens_saida_det`
--

DROP TABLE IF EXISTS `ordens_saida_det`;
CREATE TABLE IF NOT EXISTS `ordens_saida_det` (
  `id_ord_saida_det` int(10) unsigned NOT NULL,
  `cod_local_ord_saida` int(10) unsigned DEFAULT NULL,
  `cod_detento` int(10) unsigned DEFAULT NULL,
  `cod_tipo` smallint(3) unsigned DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordens_saida_locais`
--

DROP TABLE IF EXISTS `ordens_saida_locais`;
CREATE TABLE IF NOT EXISTS `ordens_saida_locais` (
  `id_local_ord_saida` int(10) unsigned NOT NULL,
  `cod_ord_saida` int(10) unsigned DEFAULT NULL,
  `cod_local` int(10) unsigned DEFAULT NULL,
  `local_hora` time DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordens_saida_tipo`
--

DROP TABLE IF EXISTS `ordens_saida_tipo`;
CREATE TABLE IF NOT EXISTS `ordens_saida_tipo` (
  `id_tipo` smallint(3) unsigned NOT NULL,
  `tipo` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `peculio`
--

DROP TABLE IF EXISTS `peculio`;
CREATE TABLE IF NOT EXISTS `peculio` (
  `idpeculio` int(11) unsigned NOT NULL,
  `cod_detento` int(10) unsigned NOT NULL,
  `cod_tipo_peculio` smallint(4) unsigned DEFAULT NULL,
  `descr_peculio` text,
  `retirado` tinyint(1) DEFAULT '0',
  `confirm` tinyint(1) DEFAULT '0',
  `obs_ret` text,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_conf` smallint(3) unsigned DEFAULT NULL,
  `data_conf` datetime DEFAULT NULL,
  `ip_conf` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `peculio_mov`
--

DROP TABLE IF EXISTS `peculio_mov`;
CREATE TABLE IF NOT EXISTS `peculio_mov` (
  `idpeculio` int(11) unsigned NOT NULL,
  `cod_detento` int(10) unsigned NOT NULL,
  `operacao` enum('D','S') DEFAULT NULL COMMENT 'D = DEPOSITO; S = SAQUE',
  `valor` decimal(10,2) DEFAULT NULL,
  `descricao` text,
  `confirm` tinyint(1) unsigned DEFAULT '0',
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `peculio_saldo`
--

DROP TABLE IF EXISTS `peculio_saldo`;
CREATE TABLE IF NOT EXISTS `peculio_saldo` (
  `idpeculio` int(11) unsigned NOT NULL,
  `cod_detento` int(10) unsigned NOT NULL,
  `saldo` decimal(10,2) DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `protocolo`
--

DROP TABLE IF EXISTS `protocolo`;
CREATE TABLE IF NOT EXISTS `protocolo` (
  `idprot` int(10) unsigned NOT NULL,
  `prot_num` smallint(5) unsigned DEFAULT NULL,
  `prot_ano` year(4) DEFAULT NULL,
  `prot_cod_modo_in` smallint(2) unsigned DEFAULT NULL,
  `prot_cod_tipo_doc` smallint(2) unsigned DEFAULT NULL,
  `prot_assunto` text,
  `prot_origem` varchar(150) DEFAULT NULL,
  `prot_cod_setor` smallint(3) unsigned DEFAULT NULL,
  `prot_data_in` date DEFAULT NULL,
  `prot_hora_in` time DEFAULT NULL,
  `prot_despachado` tinyint(1) unsigned DEFAULT '0',
  `prot_data_hora_desp` datetime DEFAULT NULL,
  `prot_user_rec` smallint(3) unsigned DEFAULT NULL,
  `prot_data_hora_rec` datetime DEFAULT NULL,
  `prot_canc` tinyint(1) unsigned DEFAULT '0',
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `rafael`
--

DROP TABLE IF EXISTS `rafael`;
CREATE TABLE IF NOT EXISTS `rafael` (
  `teste` int(10) NOT NULL,
  `dado` int(10) DEFAULT NULL,
  `data` date NOT NULL,
  `data1` date DEFAULT '0000-00-00',
  `dataup` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ID` decimal(10,2) DEFAULT NULL,
  `TIPO` int(10) DEFAULT NULL,
  `RG` text,
  `Column9` date DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `raio`
--

DROP TABLE IF EXISTS `raio`;
CREATE TABLE IF NOT EXISTS `raio` (
  `idraio` smallint(2) unsigned NOT NULL,
  `raio` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `replace_unidades`
--

DROP TABLE IF EXISTS `replace_unidades`;
CREATE TABLE IF NOT EXISTS `replace_unidades` (
  `idrpl` int(10) unsigned NOT NULL,
  `bad_name` varchar(50) DEFAULT NULL,
  `correct_name` varchar(50) DEFAULT NULL,
  `cod_correct_name` smallint(5) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sedex`
--

DROP TABLE IF EXISTS `sedex`;
CREATE TABLE IF NOT EXISTS `sedex` (
  `idsedex` int(10) unsigned NOT NULL,
  `cod_detento` int(10) unsigned DEFAULT NULL,
  `cod_visita` int(10) unsigned DEFAULT NULL,
  `sit_sedex` smallint(3) unsigned DEFAULT NULL COMMENT '1-RECEBIDO; 2-ENC. P/ INCLUSAO; 3-SEPARADO P/ DEVOLUÇÃO; 4-DEVOLVIDO; 5-ENTREGUE',
  `cod_motivo_dev` smallint(3) unsigned DEFAULT NULL,
  `data_sedex` date DEFAULT NULL,
  `cod_sedex` char(13) DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` char(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` char(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TABELA PARA REGISTRO DE SEDEX';

-- --------------------------------------------------------

--
-- Estrutura para tabela `sedex_itens`
--

DROP TABLE IF EXISTS `sedex_itens`;
CREATE TABLE IF NOT EXISTS `sedex_itens` (
  `id_item` int(10) unsigned NOT NULL,
  `cod_sedex` int(11) unsigned DEFAULT NULL,
  `cod_um` smallint(5) unsigned DEFAULT NULL,
  `quant` float(6,3) unsigned DEFAULT NULL,
  `desc` varchar(50) DEFAULT NULL,
  `retido` tinyint(1) unsigned DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sedex_motivo`
--

DROP TABLE IF EXISTS `sedex_motivo`;
CREATE TABLE IF NOT EXISTS `sedex_motivo` (
  `idmotivo` smallint(5) unsigned NOT NULL,
  `motivo` varchar(60) DEFAULT NULL,
  `motivo_corr` varchar(70) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sedex_mov`
--

DROP TABLE IF EXISTS `sedex_mov`;
CREATE TABLE IF NOT EXISTS `sedex_mov` (
  `idmovsedex` int(11) unsigned NOT NULL,
  `cod_sedex` int(10) unsigned DEFAULT NULL,
  `sit_sedex` smallint(3) unsigned DEFAULT NULL COMMENT '1-RECEBIDO; 2-ENC. P/ INCLUSAO; 3-SEPARADO P/ DEVOLUÇÃO; 4-DEVOLVIDO; 5-PAGO',
  `data_mov` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TABELA PARA MOVIMENTAÇÃO DE SEDEX';

-- --------------------------------------------------------

--
-- Estrutura para tabela `sicop_n_setor`
--

DROP TABLE IF EXISTS `sicop_n_setor`;
CREATE TABLE IF NOT EXISTS `sicop_n_setor` (
  `id_n_setor` smallint(3) unsigned NOT NULL,
  `n_setor` varchar(15) DEFAULT NULL,
  `n_setor_nome` varchar(45) DEFAULT NULL,
  `especifico` tinyint(1) unsigned DEFAULT NULL,
  `impressao` tinyint(1) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sicop_setor`
--

DROP TABLE IF EXISTS `sicop_setor`;
CREATE TABLE IF NOT EXISTS `sicop_setor` (
  `idsetor` smallint(3) unsigned NOT NULL,
  `sigla_setor` varchar(20) NOT NULL,
  `setor` varchar(100) NOT NULL,
  `desc_prot` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sicop_u_n`
--

DROP TABLE IF EXISTS `sicop_u_n`;
CREATE TABLE IF NOT EXISTS `sicop_u_n` (
  `idnivel` tinyint(5) unsigned NOT NULL,
  `descnivel` varchar(30) NOT NULL,
  `descnivel_visit` varchar(30) NOT NULL COMMENT 'Coluna para registrar a nomenclatura dos níveis para raios nas visitas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='nivel do usuario';

-- --------------------------------------------------------

--
-- Estrutura para tabela `sicop_unidade`
--

DROP TABLE IF EXISTS `sicop_unidade`;
CREATE TABLE IF NOT EXISTS `sicop_unidade` (
  `idup` tinyint(2) unsigned NOT NULL,
  `secretaria` varchar(50) NOT NULL,
  `coord` varchar(100) NOT NULL,
  `unidade_sort` varchar(50) NOT NULL,
  `unidade_long` varchar(100) NOT NULL,
  `endereco` varchar(150) NOT NULL,
  `endereco_sort` varchar(150) NOT NULL,
  `cidade` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `nome_sistema` varchar(100) NOT NULL,
  `ativo` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `dataadd` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dataup` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sicop_unidade_instal`
--

DROP TABLE IF EXISTS `sicop_unidade_instal`;
CREATE TABLE IF NOT EXISTS `sicop_unidade_instal` (
  `idup` tinyint(2) unsigned NOT NULL,
  `secretaria` varchar(50) NOT NULL,
  `coord` varchar(100) NOT NULL,
  `unidade_sort` varchar(50) NOT NULL,
  `unidade_long` varchar(100) NOT NULL,
  `endereco` varchar(150) NOT NULL,
  `endereco_sort` varchar(150) NOT NULL,
  `cidade` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `nome_sistema` varchar(100) NOT NULL,
  `ativo` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `dataadd` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dataup` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sicop_users`
--

DROP TABLE IF EXISTS `sicop_users`;
CREATE TABLE IF NOT EXISTS `sicop_users` (
  `iduser` smallint(3) unsigned NOT NULL,
  `nomeuser` varchar(80) NOT NULL,
  `nome_cham` varchar(30) NOT NULL,
  `usuario` varchar(15) NOT NULL,
  `senha` char(40) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `cargo` varchar(30) NOT NULL,
  `cod_setor` smallint(3) unsigned DEFAULT NULL,
  `iniciais` varchar(6) NOT NULL,
  `rsuser` int(12) DEFAULT NULL,
  `ativo` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `numlogins` int(10) unsigned NOT NULL DEFAULT '0',
  `prelastlogin` datetime DEFAULT NULL,
  `datalastlogin` datetime DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sicop_users_perm`
--

DROP TABLE IF EXISTS `sicop_users_perm`;
CREATE TABLE IF NOT EXISTS `sicop_users_perm` (
  `idpermissao` int(10) unsigned NOT NULL,
  `cod_user` smallint(3) unsigned DEFAULT NULL,
  `cod_n_setor` smallint(3) unsigned DEFAULT NULL,
  `cod_nivel` tinyint(5) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sindicancias`
--

DROP TABLE IF EXISTS `sindicancias`;
CREATE TABLE IF NOT EXISTS `sindicancias` (
  `idsind` int(10) unsigned NOT NULL,
  `cod_detento` int(10) unsigned DEFAULT NULL,
  `num_pda` int(11) NOT NULL,
  `ano_pda` year(4) DEFAULT '0000',
  `local_pda` varchar(50) DEFAULT NULL,
  `data_ocorrencia` date NOT NULL,
  `sit_pda` smallint(3) unsigned NOT NULL,
  `cod_sit_detento` smallint(3) unsigned DEFAULT NULL,
  `data_reabilit` date DEFAULT NULL,
  `descr_pda` text,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_digital`
--

DROP TABLE IF EXISTS `tb_digital`;
CREATE TABLE IF NOT EXISTS `tb_digital` (
  `id_digital` int(11) NOT NULL,
  `ds_digital` mediumtext,
  `nm_foto` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_ficha`
--

DROP TABLE IF EXISTS `tb_ficha`;
CREATE TABLE IF NOT EXISTS `tb_ficha` (
  `fi_matricula` bigint(20) NOT NULL DEFAULT '0',
  `fi_digito` int(11) DEFAULT '0',
  `fi_pl` int(11) DEFAULT '0',
  `fi_nome` varchar(50) DEFAULT NULL,
  `fi_rg` bigint(20) DEFAULT '0',
  `fi_digitorg` varchar(2) DEFAULT NULL,
  `fi_outrosnomes` varchar(100) DEFAULT NULL,
  `fi_vulgo` varchar(100) DEFAULT NULL,
  `fi_nacionalidade` varchar(15) DEFAULT NULL,
  `fi_cidade` varchar(25) DEFAULT NULL,
  `fi_estado` varchar(2) DEFAULT NULL,
  `cod_cidade` varchar(10) DEFAULT NULL,
  `fi_datanascimento` date DEFAULT NULL,
  `fi_profissao` varchar(25) DEFAULT NULL,
  `fi_estadocivil` varchar(15) DEFAULT NULL,
  `fi_instrucao` varchar(15) DEFAULT NULL,
  `fi_filiacaopai` varchar(50) DEFAULT NULL,
  `fi_filiacaomae` varchar(50) DEFAULT NULL,
  `fi_datainclusao` date DEFAULT NULL,
  `fi_dataexclusao` date DEFAULT NULL,
  `fi_datadaprisao` date DEFAULT NULL,
  `fi_primario` varchar(2) DEFAULT NULL,
  `fi_reincidente` varchar(2) DEFAULT NULL,
  `fi_procedencia` varchar(50) DEFAULT NULL,
  `fi_condenadoaanos` int(11) DEFAULT NULL,
  `fi_condenadoameses` int(11) DEFAULT NULL,
  `fi_condenadoadias` int(11) DEFAULT NULL,
  `fi_situacaoprocessual` varchar(2) DEFAULT NULL,
  `fi_prisoesondeesteverecolhido` varchar(300) DEFAULT NULL,
  `fi_fulga` varchar(3) DEFAULT NULL,
  `fi_fulgalocal` varchar(100) DEFAULT NULL,
  `fi_cutis` varchar(25) DEFAULT NULL,
  `fi_peso` decimal(5,2) DEFAULT '0.00',
  `fi_cabelos` varchar(25) DEFAULT NULL,
  `fi_olhos` varchar(15) DEFAULT NULL,
  `fi_estatura` decimal(10,2) DEFAULT '0.00',
  `fi_defeitosfisicos` varchar(100) DEFAULT NULL,
  `fi_nascimentolocalcorpo` varchar(100) DEFAULT NULL,
  `fi_cicatrizlocalcorpo` varchar(100) DEFAULT NULL,
  `fi_tatuagemlocalcorpo` varchar(100) DEFAULT NULL,
  `fi_ultimaresidencia` varchar(100) DEFAULT NULL,
  `fi_religiao` varchar(25) DEFAULT NULL,
  `fi_possuiadvogado` varchar(3) DEFAULT NULL,
  `fi_sinaisnacimento` varchar(3) DEFAULT NULL,
  `fn_sinaissicatriz` varchar(3) DEFAULT NULL,
  `fi_sinaistatuagens` varchar(3) DEFAULT NULL,
  `fi_avisar` varchar(200) DEFAULT NULL,
  `fi_obs` varchar(200) DEFAULT NULL,
  `fi_sinaiscicatriz` varchar(3) DEFAULT NULL,
  `fi_foto` blob,
  `fi_polegarma` blob,
  `fi_indicadorma` blob,
  `fi_medioma` blob,
  `fi_anularma` blob,
  `fi_minimoma` blob,
  `fi_polegarme` blob,
  `fi_inidicadorme` blob,
  `fi_mediome` blob,
  `fi_anularme` blob,
  `fi_minimome` blob,
  `fi_polegarmaint` bigint(20) DEFAULT '0',
  `fi_indicadormaint` bigint(20) DEFAULT '0',
  `fi_anularmaint` bigint(20) DEFAULT '0',
  `fi_mediomaint` bigint(20) DEFAULT '0',
  `fi_minimomaint` bigint(20) DEFAULT '0',
  `fi_polegarmeint` bigint(20) DEFAULT '0',
  `fi_indicadormeint` bigint(20) DEFAULT '0',
  `fi_mediomeint` bigint(20) DEFAULT '0',
  `fi_anularmeint` bigint(20) DEFAULT '0',
  `fi_minimomeint` bigint(20) DEFAULT '0',
  `fi_cela` varchar(15) DEFAULT NULL,
  `fi_raio` varchar(15) DEFAULT NULL,
  `fi_saldodisponivel` decimal(11,2) DEFAULT '0.00',
  `fi_saldoreserva` decimal(11,2) DEFAULT '0.00',
  `fi_motivo` varchar(100) DEFAULT NULL,
  `fi_antecedente` int(11) DEFAULT '0',
  `fi_cpf` varchar(11) DEFAULT NULL,
  `fi_regime` int(11) DEFAULT '0',
  `fi_execusao` bigint(20) DEFAULT '0',
  `fi_situacaopreso` int(11) DEFAULT '0',
  `fi_registrodenascimento` varchar(3) DEFAULT NULL,
  `fi_registrodebatismo` varchar(3) DEFAULT NULL,
  `fi_titulodeeleitor` varchar(3) DEFAULT NULL,
  `fi_registrodecasamento` varchar(3) DEFAULT NULL,
  `fi_certificadomilitar` varchar(3) DEFAULT NULL,
  `fi_cnh` varchar(3) DEFAULT '0',
  `fi_ceduladeidentidade` varchar(3) DEFAULT NULL,
  `fi_carteiradetrabalho` varchar(3) DEFAULT '3',
  `fi_outrosaidentificar` varchar(3) DEFAULT '0',
  `fi_relatorio` int(11) DEFAULT '0',
  `fi_saldopoupanca` decimal(11,2) DEFAULT '0.00',
  `fi_situacaopresolancs` int(11) DEFAULT '0',
  `fi_texto` varchar(300) DEFAULT NULL,
  `fi_msg` varchar(300) DEFAULT NULL,
  `fi_tipotrabalho` int(11) DEFAULT '0',
  `fi_setor` int(11) DEFAULT '0',
  `fi_telefone` varchar(15) DEFAULT NULL,
  `fi_artigo` varchar(200) DEFAULT NULL,
  `fi_orientacaosexual` int(11) DEFAULT '0',
  `fi_telefonecelular` varchar(15) DEFAULT NULL,
  `fi_situacaotrab` int(11) DEFAULT '0',
  `fi_conferista` int(11) DEFAULT '0',
  `fi_horainicial` date DEFAULT NULL,
  `fi_horafinal` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_setor`
--

DROP TABLE IF EXISTS `tb_setor`;
CREATE TABLE IF NOT EXISTS `tb_setor` (
  `set_cd_cod` int(10) NOT NULL,
  `set_de_setor` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_prot_doc`
--

DROP TABLE IF EXISTS `tipo_prot_doc`;
CREATE TABLE IF NOT EXISTS `tipo_prot_doc` (
  `id_tipo_doc` smallint(2) unsigned NOT NULL,
  `tipo_doc` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_prot_modo_in`
--

DROP TABLE IF EXISTS `tipo_prot_modo_in`;
CREATE TABLE IF NOT EXISTS `tipo_prot_modo_in` (
  `id_modo_in` smallint(2) unsigned NOT NULL,
  `modo_in` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_sit_det_busca`
--

DROP TABLE IF EXISTS `tipo_sit_det_busca`;
CREATE TABLE IF NOT EXISTS `tipo_sit_det_busca` (
  `idtipo_sit` tinyint(3) unsigned NOT NULL,
  `tipo_sit` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_un_medida`
--

DROP TABLE IF EXISTS `tipo_un_medida`;
CREATE TABLE IF NOT EXISTS `tipo_un_medida` (
  `idum` smallint(5) unsigned NOT NULL,
  `un_medida` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipoalias`
--

DROP TABLE IF EXISTS `tipoalias`;
CREATE TABLE IF NOT EXISTS `tipoalias` (
  `idtipoalias` smallint(3) unsigned NOT NULL,
  `tipoalias` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipoartigo`
--

DROP TABLE IF EXISTS `tipoartigo`;
CREATE TABLE IF NOT EXISTS `tipoartigo` (
  `idartigo` smallint(5) unsigned NOT NULL,
  `artigo` varchar(41) DEFAULT NULL,
  `infopen` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipocabelos`
--

DROP TABLE IF EXISTS `tipocabelos`;
CREATE TABLE IF NOT EXISTS `tipocabelos` (
  `idcabelos` smallint(2) unsigned NOT NULL,
  `cabelos` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipoconduta`
--

DROP TABLE IF EXISTS `tipoconduta`;
CREATE TABLE IF NOT EXISTS `tipoconduta` (
  `idconduta` smallint(2) unsigned NOT NULL,
  `conduta` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipocutis`
--

DROP TABLE IF EXISTS `tipocutis`;
CREATE TABLE IF NOT EXISTS `tipocutis` (
  `idcutis` smallint(2) unsigned NOT NULL,
  `cutis` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipoescolaridade`
--

DROP TABLE IF EXISTS `tipoescolaridade`;
CREATE TABLE IF NOT EXISTS `tipoescolaridade` (
  `idescolaridade` smallint(2) unsigned NOT NULL,
  `escolaridade` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipoestadocivil`
--

DROP TABLE IF EXISTS `tipoestadocivil`;
CREATE TABLE IF NOT EXISTS `tipoestadocivil` (
  `idest_civil` smallint(2) unsigned NOT NULL,
  `est_civil` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipomov`
--

DROP TABLE IF EXISTS `tipomov`;
CREATE TABLE IF NOT EXISTS `tipomov` (
  `idtipo_mov` smallint(2) unsigned NOT NULL,
  `sigla_mov` varchar(2) DEFAULT NULL,
  `tipo_mov` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tiponacionalidade`
--

DROP TABLE IF EXISTS `tiponacionalidade`;
CREATE TABLE IF NOT EXISTS `tiponacionalidade` (
  `idnacionalidade` smallint(3) unsigned NOT NULL,
  `nacionalidade` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipoolhos`
--

DROP TABLE IF EXISTS `tipoolhos`;
CREATE TABLE IF NOT EXISTS `tipoolhos` (
  `idolhos` smallint(2) unsigned NOT NULL,
  `olhos` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipoparentesco`
--

DROP TABLE IF EXISTS `tipoparentesco`;
CREATE TABLE IF NOT EXISTS `tipoparentesco` (
  `idparentesco` int(3) unsigned NOT NULL,
  `parentesco` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipopeculio`
--

DROP TABLE IF EXISTS `tipopeculio`;
CREATE TABLE IF NOT EXISTS `tipopeculio` (
  `idtipopeculio` smallint(4) unsigned NOT NULL,
  `tipo_peculio` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tiporeligiao`
--

DROP TABLE IF EXISTS `tiporeligiao`;
CREATE TABLE IF NOT EXISTS `tiporeligiao` (
  `idreligiao` smallint(2) unsigned NOT NULL,
  `religiao` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipositdet`
--

DROP TABLE IF EXISTS `tipositdet`;
CREATE TABLE IF NOT EXISTS `tipositdet` (
  `idsitdet` smallint(3) unsigned NOT NULL,
  `situacaodet` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipositpda`
--

DROP TABLE IF EXISTS `tipositpda`;
CREATE TABLE IF NOT EXISTS `tipositpda` (
  `idsitpda` int(3) unsigned NOT NULL,
  `situacaopda` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tiposituacaoprocessual`
--

DROP TABLE IF EXISTS `tiposituacaoprocessual`;
CREATE TABLE IF NOT EXISTS `tiposituacaoprocessual` (
  `idsit_proc` smallint(2) unsigned NOT NULL,
  `sit_proc` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `unidades`
--

DROP TABLE IF EXISTS `unidades`;
CREATE TABLE IF NOT EXISTS `unidades` (
  `idunidades` smallint(5) unsigned NOT NULL,
  `unidades` varchar(50) DEFAULT NULL,
  `in` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ir` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `it` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ex` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `er` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `et` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `visita_fotos`
--

DROP TABLE IF EXISTS `visita_fotos`;
CREATE TABLE IF NOT EXISTS `visita_fotos` (
  `id_foto` int(10) unsigned NOT NULL,
  `cod_visita` int(10) unsigned DEFAULT NULL,
  `foto_visit_g` varchar(100) DEFAULT NULL,
  `foto_visit_p` varchar(100) DEFAULT NULL,
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_add` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `visita_mov`
--

DROP TABLE IF EXISTS `visita_mov`;
CREATE TABLE IF NOT EXISTS `visita_mov` (
  `idmov_visit` int(11) unsigned NOT NULL,
  `cod_visita` int(10) unsigned DEFAULT NULL,
  `num_seq` smallint(3) unsigned DEFAULT NULL,
  `jumbo` tinyint(1) unsigned DEFAULT '0',
  `adulto` tinyint(1) unsigned DEFAULT NULL,
  `raio_det` smallint(2) unsigned DEFAULT NULL,
  `data_in` datetime DEFAULT NULL,
  `user_in` smallint(3) unsigned DEFAULT NULL,
  `ip_in` char(15) DEFAULT NULL,
  `data_out` datetime DEFAULT NULL,
  `user_out` smallint(3) unsigned DEFAULT NULL,
  `ip_out` char(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TABELA PARA REGISTRO DE MOVIMENTAÇÃO DE VISITANTES';

-- --------------------------------------------------------

--
-- Estrutura para tabela `visita_susp`
--

DROP TABLE IF EXISTS `visita_susp`;
CREATE TABLE IF NOT EXISTS `visita_susp` (
  `id_visit_susp` int(10) unsigned NOT NULL,
  `cod_visita` int(10) unsigned DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `periodo` smallint(4) unsigned DEFAULT NULL COMMENT 'SE O PERIODO FOR NULL, A SUSPENSÃO É DEFINITIVA, SENÃO É TEMPORÁRIA',
  `motivo` text,
  `revog` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='RELAÇAO DE SUSPENÇÃO DE VISITANTES';

-- --------------------------------------------------------

--
-- Estrutura para tabela `visitas`
--

DROP TABLE IF EXISTS `visitas`;
CREATE TABLE IF NOT EXISTS `visitas` (
  `idvisita` int(10) unsigned NOT NULL,
  `cod_detento` int(10) unsigned DEFAULT NULL,
  `num_in` smallint(3) unsigned DEFAULT '1' COMMENT 'NÚMERO DA ENTRADA DO DETENTO',
  `nome_visit` varchar(80) NOT NULL,
  `rg_visit` varchar(16) DEFAULT NULL,
  `sexo_visit` enum('M','F') NOT NULL,
  `nasc_visit` date DEFAULT NULL,
  `cod_cidade_v` smallint(5) unsigned DEFAULT NULL,
  `pai_visit` varchar(80) DEFAULT NULL,
  `mae_visit` varchar(80) DEFAULT NULL,
  `cod_parentesco` int(3) unsigned NOT NULL,
  `resid_visit` varchar(150) DEFAULT NULL,
  `telefone_visit` varchar(25) DEFAULT NULL,
  `cod_foto` int(10) unsigned DEFAULT NULL,
  `defeito_fisico` varchar(80) DEFAULT NULL,
  `sinal_nasc` varchar(80) DEFAULT NULL,
  `cicatrizes` varchar(80) DEFAULT NULL,
  `tatuagens` varchar(80) DEFAULT NULL,
  `doc_rg` tinyint(1) unsigned DEFAULT '0',
  `doc_foto34` tinyint(1) unsigned DEFAULT '0',
  `doc_resid` tinyint(1) unsigned DEFAULT '0',
  `doc_ant` tinyint(1) unsigned DEFAULT '0',
  `doc_cert` tinyint(1) unsigned DEFAULT '0',
  `digital` tinyint(1) unsigned DEFAULT '0',
  `user_add` smallint(3) unsigned DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `ip_add` varchar(15) DEFAULT NULL,
  `user_up` smallint(3) unsigned DEFAULT NULL,
  `data_up` datetime DEFAULT NULL,
  `ip_up` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `visitas_online`
--

DROP TABLE IF EXISTS `visitas_online`;
CREATE TABLE IF NOT EXISTS `visitas_online` (
  `id` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `identificador` varchar(60) NOT NULL,
  `cod_user` smallint(3) unsigned DEFAULT NULL,
  `url` text,
  `hora` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `visitas_record`
--

DROP TABLE IF EXISTS `visitas_record`;
CREATE TABLE IF NOT EXISTS `visitas_record` (
  `id` int(11) NOT NULL,
  `data` datetime NOT NULL,
  `visitantes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `visitas_site`
--

DROP TABLE IF EXISTS `visitas_site`;
CREATE TABLE IF NOT EXISTS `visitas_site` (
  `id` int(10) unsigned NOT NULL,
  `data` date NOT NULL,
  `uniques` int(10) unsigned NOT NULL DEFAULT '0',
  `pageviews` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices de tabelas apagadas
--

--
-- Índices de tabela `aliases`
--
ALTER TABLE `aliases`
  ADD PRIMARY KEY (`idalias`),
  ADD KEY `alias_det` (`alias_det`),
  ADD KEY `FK_aliases_detentos` (`cod_detento`),
  ADD KEY `FK_aliases_tipoalias` (`cod_tipoalias`);

--
-- Índices de tabela `apcc`
--
ALTER TABLE `apcc`
  ADD PRIMARY KEY (`idapcc`),
  ADD KEY `FK_apcc_detentos` (`cod_detento`),
  ADD KEY `FK_apcc_numeroapcc` (`cod_numapcc`),
  ADD KEY `FK_apcc_tipoconduta` (`cod_conduta`);

--
-- Índices de tabela `apcc_mov`
--
ALTER TABLE `apcc_mov`
  ADD PRIMARY KEY (`id_apcc_mov`),
  ADD KEY `FK_apcc_mov_apcc` (`cod_apcc`),
  ADD KEY `FK_apcc_mov_mov_det_in` (`cod_movin`),
  ADD KEY `FK_apcc_mov_mov_det_out` (`cod_movout`);

--
-- Índices de tabela `audiencia`
--
ALTER TABLE `audiencia`
  ADD PRIMARY KEY (`idaudiencia`),
  ADD KEY `local_aud` (`local_aud`),
  ADD KEY `cidade_aud` (`cidade_aud`),
  ADD KEY `hora_aud` (`hora_aud`),
  ADD KEY `tipo_aud` (`tipo_aud`),
  ADD KEY `sit_aud` (`sit_aud`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `data_aud` (`data_aud`) USING BTREE,
  ADD KEY `FK_audiencias_detentos` (`cod_detento`) USING BTREE;

--
-- Índices de tabela `audiencias`
--
ALTER TABLE `audiencias`
  ADD PRIMARY KEY (`idaudiencia`),
  ADD KEY `data_aud` (`data_aud`),
  ADD KEY `local_aud` (`local_aud`),
  ADD KEY `cidade_aud` (`cidade_aud`),
  ADD KEY `hora_aud` (`hora_aud`),
  ADD KEY `tipo_aud` (`tipo_aud`),
  ADD KEY `sit_aud` (`sit_aud`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `FK_audiencias_detentos` (`cod_detento`);

--
-- Índices de tabela `bonde`
--
ALTER TABLE `bonde`
  ADD PRIMARY KEY (`idbonde`),
  ADD KEY `data` (`bonde_data`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`);

--
-- Índices de tabela `bonde_det`
--
ALTER TABLE `bonde_det`
  ADD PRIMARY KEY (`idbd`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `FK_bonde_det_bonde_locais` (`cod_bonde_local`),
  ADD KEY `FK_bonde_det_detentos` (`cod_detento`);

--
-- Índices de tabela `bonde_locais`
--
ALTER TABLE `bonde_locais`
  ADD PRIMARY KEY (`idblocal`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `FK_bonde_locais_bonde` (`cod_bonde`),
  ADD KEY `FK_bonde_locais_unidades` (`cod_unidade`);

--
-- Índices de tabela `cela`
--
ALTER TABLE `cela`
  ADD PRIMARY KEY (`idcela`),
  ADD KEY `cela` (`cela`),
  ADD KEY `FK_cela_raio` (`cod_raio`);

--
-- Índices de tabela `cidades`
--
ALTER TABLE `cidades`
  ADD PRIMARY KEY (`idcidade`),
  ADD KEY `nome` (`nome`),
  ADD KEY `FK_cidades_estados` (`cod_uf`);

--
-- Índices de tabela `cont_pop`
--
ALTER TABLE `cont_pop`
  ADD PRIMARY KEY (`idcontpop`),
  ADD KEY `cp_data_hora` (`cp_data_hora`);

--
-- Índices de tabela `det_fotos`
--
ALTER TABLE `det_fotos`
  ADD PRIMARY KEY (`id_foto`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `FK_det_fotos_detentos` (`cod_detento`);

--
-- Índices de tabela `detentos`
--
ALTER TABLE `detentos`
  ADD PRIMARY KEY (`iddetento`),
  ADD UNIQUE KEY `matricula` (`matricula`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `funcionario` (`funcionario`),
  ADD KEY `n_passagem` (`n_passagem`),
  ADD KEY `n_p_trans` (`n_p_trans`),
  ADD KEY `nome_det` (`nome_det`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `conduta_ant` (`conduta_ant`),
  ADD KEY `rg_civil` (`rg_civil`),
  ADD KEY `execucao` (`execucao`),
  ADD KEY `nasc_det` (`nasc_det`),
  ADD KEY `dados_prov` (`dados_prov`),
  ADD KEY `cod_foto` (`cod_foto`),
  ADD KEY `FK_detentos_cela` (`cod_cela`),
  ADD KEY `FK_detentos_cidades` (`cod_cidade`),
  ADD KEY `FK_detentos_local_prisao` (`cod_local_prisao`),
  ADD KEY `FK_detentos_mov_det_in` (`cod_movin`),
  ADD KEY `FK_detentos_mov_det_out` (`cod_movout`),
  ADD KEY `FK_detentos_tipoartigo` (`cod_artigo`),
  ADD KEY `FK_detentos_tipocabelos` (`cod_cabelos`),
  ADD KEY `FK_detentos_tipocutis` (`cod_cutis`),
  ADD KEY `FK_detentos_tipoescolaridade` (`cod_instrucao`),
  ADD KEY `FK_detentos_tipoestadocivil` (`cod_est_civil`),
  ADD KEY `FK_detentos_tiponacionalidade` (`cod_nacionalidade`),
  ADD KEY `FK_detentos_tipoolhos` (`cod_olhos`),
  ADD KEY `FK_detentos_tiporeligiao` (`cod_religiao`),
  ADD KEY `FK_detentos_tiposituacaoprocessual` (`cod_sit_proc`),
  ADD KEY `pl` (`pl`),
  ADD KEY `guia_local` (`guia_local`),
  ADD KEY `guia_numero` (`guia_numero`);

--
-- Índices de tabela `detentos_fotos_esp`
--
ALTER TABLE `detentos_fotos_esp`
  ADD PRIMARY KEY (`id_foto`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `FK_detentos_fotos_esp_detentos` (`cod_detento`);

--
-- Índices de tabela `detentos_radio`
--
ALTER TABLE `detentos_radio`
  ADD PRIMARY KEY (`idradio`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `FK_detentos_radio_detentos` (`cod_detento`),
  ADD KEY `FK_detentos_radio_cela` (`cod_cela`);

--
-- Índices de tabela `detentos_tv`
--
ALTER TABLE `detentos_tv`
  ADD PRIMARY KEY (`idtv`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `FK_detentos_tv_detentos` (`cod_detento`),
  ADD KEY `FK_detentos_tv_cela` (`cod_cela`);

--
-- Índices de tabela `digital`
--
ALTER TABLE `digital`
  ADD PRIMARY KEY (`iddigital`);

--
-- Índices de tabela `diretores`
--
ALTER TABLE `diretores`
  ADD PRIMARY KEY (`iddiretores`),
  ADD KEY `diretor_geral` (`diretor_geral`),
  ADD KEY `diretor_seg` (`diretor_seg`),
  ADD KEY `diretor_pront` (`diretor_pront`),
  ADD KEY `diretor_saude` (`diretor_saude`),
  ADD KEY `diretor_rh` (`diretor_rh`),
  ADD KEY `diretor_ca` (`diretor_ca`);

--
-- Índices de tabela `diretores_n`
--
ALTER TABLE `diretores_n`
  ADD PRIMARY KEY (`iddiretoresn`),
  ADD KEY `ativo` (`ativo`),
  ADD KEY `setor` (`setor`);

--
-- Índices de tabela `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`idestado`);

--
-- Índices de tabela `grade`
--
ALTER TABLE `grade`
  ADD PRIMARY KEY (`idprocesso`),
  ADD KEY `gra_preso` (`gra_preso`),
  ADD KEY `gra_data_delito` (`gra_data_delito`),
  ADD KEY `gra_fed` (`gra_fed`),
  ADD KEY `gra_outro_est` (`gra_outro_est`),
  ADD KEY `gra_campo_x` (`gra_campo_x`),
  ADD KEY `gra_num_in` (`gra_num_in`),
  ADD KEY `gra_num_inq` (`gra_num_inq`),
  ADD KEY `gra_num_proc` (`gra_num_proc`),
  ADD KEY `gra_comarca` (`gra_comarca`),
  ADD KEY `FK_grade_detentos` (`cod_detento`);

--
-- Índices de tabela `gsa_cidades`
--
ALTER TABLE `gsa_cidades`
  ADD PRIMARY KEY (`gsa_cidade_id`),
  ADD KEY `gsa_cidade_cod` (`gsa_cidade_cod`),
  ADD KEY `gsa_cidade_nome` (`gsa_cidade_nome`);

--
-- Índices de tabela `gsa_depol`
--
ALTER TABLE `gsa_depol`
  ADD PRIMARY KEY (`gsa_depol_id`),
  ADD KEY `gsa_depol_cod` (`gsa_depol_cod`),
  ADD KEY `gsa_depol_nome` (`gsa_depol_nome`);

--
-- Índices de tabela `inteligencia`
--
ALTER TABLE `inteligencia`
  ADD PRIMARY KEY (`idinteli`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `FK_inteligencia_detentos` (`cod_detento`);

--
-- Índices de tabela `listatel`
--
ALTER TABLE `listatel`
  ADD PRIMARY KEY (`idlistatel`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `tel_local` (`tel_local`);

--
-- Índices de tabela `listatel_num`
--
ALTER TABLE `listatel_num`
  ADD PRIMARY KEY (`idlistatel_num`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `FK_listatel_num_listatel` (`cod_listatel`);

--
-- Índices de tabela `locais_apr`
--
ALTER TABLE `locais_apr`
  ADD PRIMARY KEY (`idlocal`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`);

--
-- Índices de tabela `log_alt`
--
ALTER TABLE `log_alt`
  ADD PRIMARY KEY (`idlog_alt`),
  ADD KEY `data` (`data`),
  ADD KEY `user` (`user`);

--
-- Índices de tabela `log_det`
--
ALTER TABLE `log_det`
  ADD PRIMARY KEY (`idlog_det`);

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hora` (`hora`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `ip` (`ip`);

--
-- Índices de tabela `model_of_apr`
--
ALTER TABLE `model_of_apr`
  ADD PRIMARY KEY (`idmodel`);

--
-- Índices de tabela `mov_det`
--
ALTER TABLE `mov_det`
  ADD PRIMARY KEY (`id_mov`),
  ADD KEY `data_mov` (`data_mov`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `FK_mov_det_detentos` (`cod_detento`),
  ADD KEY `FK_mov_det_tipomov` (`cod_tipo_mov`),
  ADD KEY `FK_mov_det_unidades` (`cod_local_mov`);

--
-- Índices de tabela `mov_rc_det`
--
ALTER TABLE `mov_rc_det`
  ADD PRIMARY KEY (`id_mov_rc`),
  ADD KEY `data_rc` (`data_rc`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `FK_mov_rc_det_detentos` (`cod_detento`),
  ADD KEY `FK_mov_rc_det_cela_new` (`cod_n_cela`),
  ADD KEY `FK_mov_rc_det_cela_old` (`cod_old_cela`);

--
-- Índices de tabela `msg`
--
ALTER TABLE `msg`
  ADD PRIMARY KEY (`idmsg`),
  ADD KEY `FK_msg_cdriouser_de` (`msg_de`),
  ADD KEY `FK_msg_cdriouser_para` (`msg_para`),
  ADD KEY `msg_titulo` (`msg_titulo`),
  ADD KEY `msg_corpo` (`msg_corpo`(255));

--
-- Índices de tabela `numeroapcc`
--
ALTER TABLE `numeroapcc`
  ADD PRIMARY KEY (`idnumapcc`),
  ADD KEY `ano` (`ano`),
  ADD KEY `iduser` (`iduser`),
  ADD KEY `idsetor` (`idsetor`),
  ADD KEY `numero_apcc` (`numero_apcc`);

--
-- Índices de tabela `numerofax`
--
ALTER TABLE `numerofax`
  ADD PRIMARY KEY (`idnumfax`),
  ADD KEY `numero_fax` (`numero_fax`),
  ADD KEY `ano` (`ano`),
  ADD KEY `iduser` (`iduser`),
  ADD KEY `idsetor` (`idsetor`);

--
-- Índices de tabela `numeronotes`
--
ALTER TABLE `numeronotes`
  ADD PRIMARY KEY (`idnumnotes`),
  ADD KEY `ano` (`ano`),
  ADD KEY `iduser` (`iduser`),
  ADD KEY `idsetor` (`idsetor`),
  ADD KEY `numero_notes` (`numero_notes`);

--
-- Índices de tabela `numeroof`
--
ALTER TABLE `numeroof`
  ADD PRIMARY KEY (`idnumof`),
  ADD KEY `numero_of` (`numero_of`),
  ADD KEY `ano` (`ano`),
  ADD KEY `iduser` (`iduser`),
  ADD KEY `idsetor` (`idsetor`);

--
-- Índices de tabela `numeroreq`
--
ALTER TABLE `numeroreq`
  ADD PRIMARY KEY (`idnumreq`),
  ADD KEY `ano` (`ano`),
  ADD KEY `iduser` (`iduser`),
  ADD KEY `idsetor` (`idsetor`),
  ADD KEY `numero_req` (`numero_req`);

--
-- Índices de tabela `numerorms`
--
ALTER TABLE `numerorms`
  ADD PRIMARY KEY (`idnumrms`),
  ADD KEY `ano` (`ano`),
  ADD KEY `iduser` (`iduser`),
  ADD KEY `idsetor` (`idsetor`),
  ADD KEY `numero_rms` (`numero_rms`);

--
-- Índices de tabela `obs_aud`
--
ALTER TABLE `obs_aud`
  ADD PRIMARY KEY (`id_obs_aud`),
  ADD KEY `FK_obs_aud_audiencias` (`cod_audiencia`);

--
-- Índices de tabela `obs_det`
--
ALTER TABLE `obs_det`
  ADD PRIMARY KEY (`id_obs_det`),
  ADD KEY `FK_obs_det_detentos` (`cod_detento`);

--
-- Índices de tabela `obs_grade`
--
ALTER TABLE `obs_grade`
  ADD PRIMARY KEY (`id_obs_grade`),
  ADD KEY `FK_obs_grade_detentos` (`cod_detento`);

--
-- Índices de tabela `obs_inteli`
--
ALTER TABLE `obs_inteli`
  ADD PRIMARY KEY (`id_obs_inteli`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `FK_obs_inteli_inteligencia` (`cod_inteli`);

--
-- Índices de tabela `obs_listatel`
--
ALTER TABLE `obs_listatel`
  ADD PRIMARY KEY (`id_obs_listatel`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `FK_obs_listatel_listatel` (`cod_listatel`);

--
-- Índices de tabela `obs_pda`
--
ALTER TABLE `obs_pda`
  ADD PRIMARY KEY (`id_obs_pda`),
  ADD KEY `FK_obs_pda_sindicancias` (`cod_pda`);

--
-- Índices de tabela `obs_radio`
--
ALTER TABLE `obs_radio`
  ADD PRIMARY KEY (`id_obs_radio`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `FK_obs_radio_detentos_radio` (`cod_radio`);

--
-- Índices de tabela `obs_rol`
--
ALTER TABLE `obs_rol`
  ADD PRIMARY KEY (`id_obs_rol`),
  ADD KEY `FK_obs_rol_detentos` (`cod_detento`);

--
-- Índices de tabela `obs_tv`
--
ALTER TABLE `obs_tv`
  ADD PRIMARY KEY (`id_obs_tv`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `FK_obs_tv_detentos_tv` (`cod_tv`);

--
-- Índices de tabela `obs_visit`
--
ALTER TABLE `obs_visit`
  ADD PRIMARY KEY (`id_obs_visit`),
  ADD KEY `FK_obs_visit_visitas` (`cod_visita`);

--
-- Índices de tabela `ordens_escolta`
--
ALTER TABLE `ordens_escolta`
  ADD PRIMARY KEY (`idescolta`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `escolta_data` (`escolta_data`),
  ADD KEY `escolta_hora` (`escolta_hora`);

--
-- Índices de tabela `ordens_escolta_det`
--
ALTER TABLE `ordens_escolta_det`
  ADD PRIMARY KEY (`id_escolta_det`),
  ADD KEY `FK_ordens_escolta_det_ordens_escolta_locais` (`cod_local_escolta`),
  ADD KEY `FK_ordens_escolta_det_detentos` (`cod_detento`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `FK_ordens_escolta_det_ordens_escolta_tipo` (`cod_tipo`);

--
-- Índices de tabela `ordens_escolta_locais`
--
ALTER TABLE `ordens_escolta_locais`
  ADD PRIMARY KEY (`id_local_escolta`),
  ADD KEY `FK_ordens_escolta_locais_ordens_escolta` (`cod_escolta`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `FK_ordens_escolta_locais_locais_apr` (`cod_local`);

--
-- Índices de tabela `ordens_escolta_tipo`
--
ALTER TABLE `ordens_escolta_tipo`
  ADD PRIMARY KEY (`id_tipo`);

--
-- Índices de tabela `ordens_saida`
--
ALTER TABLE `ordens_saida`
  ADD PRIMARY KEY (`id_ord_saida`),
  ADD KEY `ord_saida_data` (`ord_saida_data`),
  ADD KEY `ord_saida_hora` (`ord_saida_hora`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`);

--
-- Índices de tabela `ordens_saida_det`
--
ALTER TABLE `ordens_saida_det`
  ADD PRIMARY KEY (`id_ord_saida_det`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `FK_ordens_saida_det_ordens_saida_locais` (`cod_local_ord_saida`),
  ADD KEY `FK_ordens_saida_det_detentos` (`cod_detento`),
  ADD KEY `FK_ordens_saida_det_ordens_saida_tipo` (`cod_tipo`);

--
-- Índices de tabela `ordens_saida_locais`
--
ALTER TABLE `ordens_saida_locais`
  ADD PRIMARY KEY (`id_local_ord_saida`),
  ADD KEY `local_hora` (`local_hora`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `FK_ordens_saida_locais_ordens_saida` (`cod_ord_saida`),
  ADD KEY `FK_ordens_saida_locais_locais_apr` (`cod_local`);

--
-- Índices de tabela `ordens_saida_tipo`
--
ALTER TABLE `ordens_saida_tipo`
  ADD PRIMARY KEY (`id_tipo`);

--
-- Índices de tabela `peculio`
--
ALTER TABLE `peculio`
  ADD PRIMARY KEY (`idpeculio`),
  ADD KEY `retirado` (`retirado`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `confirm` (`confirm`),
  ADD KEY `FK_peculio_detentos` (`cod_detento`),
  ADD KEY `FK_peculio_tipopeculio` (`cod_tipo_peculio`);

--
-- Índices de tabela `peculio_mov`
--
ALTER TABLE `peculio_mov`
  ADD PRIMARY KEY (`idpeculio`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `FK_peculio_mov_detentos` (`cod_detento`);

--
-- Índices de tabela `peculio_saldo`
--
ALTER TABLE `peculio_saldo`
  ADD PRIMARY KEY (`idpeculio`),
  ADD UNIQUE KEY `FK_peculio_saldo_detentos` (`cod_detento`);

--
-- Índices de tabela `protocolo`
--
ALTER TABLE `protocolo`
  ADD PRIMARY KEY (`idprot`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `prot_num` (`prot_num`),
  ADD KEY `prot_ano` (`prot_ano`),
  ADD KEY `prot_data_in` (`prot_data_in`),
  ADD KEY `prot_hora_in` (`prot_hora_in`),
  ADD KEY `FK_protocolo_tipo_prot_modo_in` (`prot_cod_modo_in`),
  ADD KEY `FK_protocolo_tipo_prot_doc` (`prot_cod_tipo_doc`),
  ADD KEY `FK_protocolo_sicop_setor` (`prot_cod_setor`);

--
-- Índices de tabela `rafael`
--
ALTER TABLE `rafael`
  ADD PRIMARY KEY (`teste`),
  ADD UNIQUE KEY `dado` (`dado`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `data` (`data`);

--
-- Índices de tabela `raio`
--
ALTER TABLE `raio`
  ADD PRIMARY KEY (`idraio`),
  ADD KEY `raio` (`raio`);

--
-- Índices de tabela `replace_unidades`
--
ALTER TABLE `replace_unidades`
  ADD PRIMARY KEY (`idrpl`),
  ADD UNIQUE KEY `bad_name` (`bad_name`),
  ADD KEY `FK_replace_unidades_unidades` (`cod_correct_name`);

--
-- Índices de tabela `sedex`
--
ALTER TABLE `sedex`
  ADD PRIMARY KEY (`idsedex`),
  ADD UNIQUE KEY `cod_sedex` (`cod_sedex`),
  ADD KEY `sit_sedex` (`sit_sedex`),
  ADD KEY `data_sedex` (`data_sedex`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `FK_sedex_detentos` (`cod_detento`),
  ADD KEY `FK_sedex_visitas` (`cod_visita`),
  ADD KEY `FK_sedex_sedex_motivo` (`cod_motivo_dev`);

--
-- Índices de tabela `sedex_itens`
--
ALTER TABLE `sedex_itens`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `user_up` (`user_up`),
  ADD KEY `data_up` (`data_up`),
  ADD KEY `FK_sedex_itens_sedex` (`cod_sedex`),
  ADD KEY `FK_sedex_itens_tipo_un_medida` (`cod_um`);

--
-- Índices de tabela `sedex_motivo`
--
ALTER TABLE `sedex_motivo`
  ADD PRIMARY KEY (`idmotivo`);

--
-- Índices de tabela `sedex_mov`
--
ALTER TABLE `sedex_mov`
  ADD PRIMARY KEY (`idmovsedex`),
  ADD KEY `FK_sedex_mov_sedex` (`cod_sedex`);

--
-- Índices de tabela `sicop_n_setor`
--
ALTER TABLE `sicop_n_setor`
  ADD PRIMARY KEY (`id_n_setor`),
  ADD KEY `especifico` (`especifico`),
  ADD KEY `impressao` (`impressao`),
  ADD KEY `n_setor` (`n_setor`);

--
-- Índices de tabela `sicop_setor`
--
ALTER TABLE `sicop_setor`
  ADD PRIMARY KEY (`idsetor`),
  ADD KEY `sigla_setor` (`sigla_setor`),
  ADD KEY `desc_prot` (`desc_prot`);

--
-- Índices de tabela `sicop_u_n`
--
ALTER TABLE `sicop_u_n`
  ADD PRIMARY KEY (`idnivel`);

--
-- Índices de tabela `sicop_unidade`
--
ALTER TABLE `sicop_unidade`
  ADD PRIMARY KEY (`idup`);

--
-- Índices de tabela `sicop_unidade_instal`
--
ALTER TABLE `sicop_unidade_instal`
  ADD PRIMARY KEY (`idup`);

--
-- Índices de tabela `sicop_users`
--
ALTER TABLE `sicop_users`
  ADD PRIMARY KEY (`iduser`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `nomeuser` (`nomeuser`),
  ADD KEY `FK_sicop_users_sicop_setor` (`cod_setor`);

--
-- Índices de tabela `sicop_users_perm`
--
ALTER TABLE `sicop_users_perm`
  ADD PRIMARY KEY (`idpermissao`),
  ADD KEY `fk_sicop_users_sicop_users_perm` (`cod_user`),
  ADD KEY `fk_sicop_u_n_sicop_users_perm` (`cod_nivel`),
  ADD KEY `fk_sicop_n_setor_sicop_users_perm` (`cod_n_setor`);

--
-- Índices de tabela `sindicancias`
--
ALTER TABLE `sindicancias`
  ADD PRIMARY KEY (`idsind`),
  ADD KEY `FK_sindicancias_detentos` (`cod_detento`),
  ADD KEY `FK_sindicancias_tipositdet` (`cod_sit_detento`);

--
-- Índices de tabela `tb_digital`
--
ALTER TABLE `tb_digital`
  ADD PRIMARY KEY (`id_digital`);

--
-- Índices de tabela `tb_setor`
--
ALTER TABLE `tb_setor`
  ADD PRIMARY KEY (`set_cd_cod`);

--
-- Índices de tabela `tipo_prot_doc`
--
ALTER TABLE `tipo_prot_doc`
  ADD PRIMARY KEY (`id_tipo_doc`),
  ADD KEY `tipo_doc` (`tipo_doc`);

--
-- Índices de tabela `tipo_prot_modo_in`
--
ALTER TABLE `tipo_prot_modo_in`
  ADD PRIMARY KEY (`id_modo_in`),
  ADD KEY `modo_in` (`modo_in`);

--
-- Índices de tabela `tipo_sit_det_busca`
--
ALTER TABLE `tipo_sit_det_busca`
  ADD PRIMARY KEY (`idtipo_sit`),
  ADD KEY `tipo_sit` (`tipo_sit`);

--
-- Índices de tabela `tipo_un_medida`
--
ALTER TABLE `tipo_un_medida`
  ADD PRIMARY KEY (`idum`);

--
-- Índices de tabela `tipoalias`
--
ALTER TABLE `tipoalias`
  ADD PRIMARY KEY (`idtipoalias`),
  ADD KEY `tipoalias` (`tipoalias`);

--
-- Índices de tabela `tipoartigo`
--
ALTER TABLE `tipoartigo`
  ADD PRIMARY KEY (`idartigo`),
  ADD KEY `artigo` (`artigo`),
  ADD KEY `infopen` (`infopen`);

--
-- Índices de tabela `tipocabelos`
--
ALTER TABLE `tipocabelos`
  ADD PRIMARY KEY (`idcabelos`),
  ADD KEY `cabelos` (`cabelos`);

--
-- Índices de tabela `tipoconduta`
--
ALTER TABLE `tipoconduta`
  ADD PRIMARY KEY (`idconduta`),
  ADD KEY `conduta` (`conduta`);

--
-- Índices de tabela `tipocutis`
--
ALTER TABLE `tipocutis`
  ADD PRIMARY KEY (`idcutis`),
  ADD KEY `cutis` (`cutis`);

--
-- Índices de tabela `tipoescolaridade`
--
ALTER TABLE `tipoescolaridade`
  ADD PRIMARY KEY (`idescolaridade`),
  ADD KEY `escolaridade` (`escolaridade`);

--
-- Índices de tabela `tipoestadocivil`
--
ALTER TABLE `tipoestadocivil`
  ADD PRIMARY KEY (`idest_civil`),
  ADD KEY `est_civil` (`est_civil`);

--
-- Índices de tabela `tipomov`
--
ALTER TABLE `tipomov`
  ADD PRIMARY KEY (`idtipo_mov`),
  ADD KEY `tipo_mov` (`tipo_mov`),
  ADD KEY `sigla_mov` (`sigla_mov`);

--
-- Índices de tabela `tiponacionalidade`
--
ALTER TABLE `tiponacionalidade`
  ADD PRIMARY KEY (`idnacionalidade`),
  ADD KEY `nacionalidade` (`nacionalidade`);

--
-- Índices de tabela `tipoolhos`
--
ALTER TABLE `tipoolhos`
  ADD PRIMARY KEY (`idolhos`),
  ADD KEY `olhos` (`olhos`);

--
-- Índices de tabela `tipoparentesco`
--
ALTER TABLE `tipoparentesco`
  ADD PRIMARY KEY (`idparentesco`),
  ADD KEY `parentesco` (`parentesco`);

--
-- Índices de tabela `tipopeculio`
--
ALTER TABLE `tipopeculio`
  ADD PRIMARY KEY (`idtipopeculio`),
  ADD KEY `tipo_peculio` (`tipo_peculio`);

--
-- Índices de tabela `tiporeligiao`
--
ALTER TABLE `tiporeligiao`
  ADD PRIMARY KEY (`idreligiao`),
  ADD KEY `religiao` (`religiao`);

--
-- Índices de tabela `tipositdet`
--
ALTER TABLE `tipositdet`
  ADD PRIMARY KEY (`idsitdet`),
  ADD KEY `situacaodet` (`situacaodet`);

--
-- Índices de tabela `tipositpda`
--
ALTER TABLE `tipositpda`
  ADD PRIMARY KEY (`idsitpda`),
  ADD KEY `situacaopda` (`situacaopda`);

--
-- Índices de tabela `tiposituacaoprocessual`
--
ALTER TABLE `tiposituacaoprocessual`
  ADD PRIMARY KEY (`idsit_proc`),
  ADD KEY `sit_proc` (`sit_proc`);

--
-- Índices de tabela `unidades`
--
ALTER TABLE `unidades`
  ADD PRIMARY KEY (`idunidades`),
  ADD KEY `unidades` (`unidades`),
  ADD KEY `in` (`in`),
  ADD KEY `ir` (`ir`),
  ADD KEY `it` (`it`),
  ADD KEY `ex` (`ex`),
  ADD KEY `er` (`er`),
  ADD KEY `et` (`et`);

--
-- Índices de tabela `visita_fotos`
--
ALTER TABLE `visita_fotos`
  ADD PRIMARY KEY (`id_foto`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `FK_visita_fotos_visitas` (`cod_visita`);

--
-- Índices de tabela `visita_mov`
--
ALTER TABLE `visita_mov`
  ADD PRIMARY KEY (`idmov_visit`),
  ADD KEY `num_seq` (`num_seq`),
  ADD KEY `jumbo` (`jumbo`),
  ADD KEY `data_in` (`data_in`),
  ADD KEY `adulto` (`adulto`),
  ADD KEY `raio_det` (`raio_det`),
  ADD KEY `FK_visita_mov_visitas` (`cod_visita`);

--
-- Índices de tabela `visita_susp`
--
ALTER TABLE `visita_susp`
  ADD PRIMARY KEY (`id_visit_susp`),
  ADD KEY `data_inicio` (`data_inicio`),
  ADD KEY `periodo` (`periodo`),
  ADD KEY `revog` (`revog`),
  ADD KEY `user_add` (`user_add`),
  ADD KEY `data_add` (`data_add`),
  ADD KEY `FK_visita_susp_visitas` (`cod_visita`);

--
-- Índices de tabela `visitas`
--
ALTER TABLE `visitas`
  ADD PRIMARY KEY (`idvisita`),
  ADD KEY `nome_visit` (`nome_visit`),
  ADD KEY `rg_visit` (`rg_visit`),
  ADD KEY `nasc_visit` (`nasc_visit`),
  ADD KEY `cod_foto` (`cod_foto`),
  ADD KEY `FK_visitas_detentos` (`cod_detento`),
  ADD KEY `FK_visitas_cidades` (`cod_cidade_v`),
  ADD KEY `FK_visitas_tipoparentesco` (`cod_parentesco`);

--
-- Índices de tabela `visitas_online`
--
ALTER TABLE `visitas_online`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `identificador` (`identificador`),
  ADD KEY `FK_visitas_online_sicop_users` (`cod_user`);

--
-- Índices de tabela `visitas_record`
--
ALTER TABLE `visitas_record`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `visitas_site`
--
ALTER TABLE `visitas_site`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `data` (`data`);

--
-- AUTO_INCREMENT de tabelas apagadas
--

--
-- AUTO_INCREMENT de tabela `aliases`
--
ALTER TABLE `aliases`
  MODIFY `idalias` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `apcc`
--
ALTER TABLE `apcc`
  MODIFY `idapcc` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `apcc_mov`
--
ALTER TABLE `apcc_mov`
  MODIFY `id_apcc_mov` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `audiencia`
--
ALTER TABLE `audiencia`
  MODIFY `idaudiencia` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `audiencias`
--
ALTER TABLE `audiencias`
  MODIFY `idaudiencia` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `bonde`
--
ALTER TABLE `bonde`
  MODIFY `idbonde` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `bonde_det`
--
ALTER TABLE `bonde_det`
  MODIFY `idbd` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `bonde_locais`
--
ALTER TABLE `bonde_locais`
  MODIFY `idblocal` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `cela`
--
ALTER TABLE `cela`
  MODIFY `idcela` smallint(3) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `cidades`
--
ALTER TABLE `cidades`
  MODIFY `idcidade` smallint(5) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `cont_pop`
--
ALTER TABLE `cont_pop`
  MODIFY `idcontpop` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `det_fotos`
--
ALTER TABLE `det_fotos`
  MODIFY `id_foto` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `detentos`
--
ALTER TABLE `detentos`
  MODIFY `iddetento` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `detentos_fotos_esp`
--
ALTER TABLE `detentos_fotos_esp`
  MODIFY `id_foto` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `detentos_radio`
--
ALTER TABLE `detentos_radio`
  MODIFY `idradio` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `detentos_tv`
--
ALTER TABLE `detentos_tv`
  MODIFY `idtv` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `digital`
--
ALTER TABLE `digital`
  MODIFY `iddigital` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `diretores`
--
ALTER TABLE `diretores`
  MODIFY `iddiretores` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `diretores_n`
--
ALTER TABLE `diretores_n`
  MODIFY `iddiretoresn` tinyint(3) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `estados`
--
ALTER TABLE `estados`
  MODIFY `idestado` smallint(3) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `grade`
--
ALTER TABLE `grade`
  MODIFY `idprocesso` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `gsa_cidades`
--
ALTER TABLE `gsa_cidades`
  MODIFY `gsa_cidade_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `gsa_depol`
--
ALTER TABLE `gsa_depol`
  MODIFY `gsa_depol_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `inteligencia`
--
ALTER TABLE `inteligencia`
  MODIFY `idinteli` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `listatel`
--
ALTER TABLE `listatel`
  MODIFY `idlistatel` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `listatel_num`
--
ALTER TABLE `listatel_num`
  MODIFY `idlistatel_num` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `locais_apr`
--
ALTER TABLE `locais_apr`
  MODIFY `idlocal` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `log_alt`
--
ALTER TABLE `log_alt`
  MODIFY `idlog_alt` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `log_det`
--
ALTER TABLE `log_det`
  MODIFY `idlog_det` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `model_of_apr`
--
ALTER TABLE `model_of_apr`
  MODIFY `idmodel` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `mov_det`
--
ALTER TABLE `mov_det`
  MODIFY `id_mov` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `mov_rc_det`
--
ALTER TABLE `mov_rc_det`
  MODIFY `id_mov_rc` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `msg`
--
ALTER TABLE `msg`
  MODIFY `idmsg` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `numeroapcc`
--
ALTER TABLE `numeroapcc`
  MODIFY `idnumapcc` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `numerofax`
--
ALTER TABLE `numerofax`
  MODIFY `idnumfax` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `numeronotes`
--
ALTER TABLE `numeronotes`
  MODIFY `idnumnotes` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `numeroof`
--
ALTER TABLE `numeroof`
  MODIFY `idnumof` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `numeroreq`
--
ALTER TABLE `numeroreq`
  MODIFY `idnumreq` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `numerorms`
--
ALTER TABLE `numerorms`
  MODIFY `idnumrms` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `obs_aud`
--
ALTER TABLE `obs_aud`
  MODIFY `id_obs_aud` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `obs_det`
--
ALTER TABLE `obs_det`
  MODIFY `id_obs_det` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `obs_grade`
--
ALTER TABLE `obs_grade`
  MODIFY `id_obs_grade` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `obs_inteli`
--
ALTER TABLE `obs_inteli`
  MODIFY `id_obs_inteli` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `obs_listatel`
--
ALTER TABLE `obs_listatel`
  MODIFY `id_obs_listatel` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `obs_pda`
--
ALTER TABLE `obs_pda`
  MODIFY `id_obs_pda` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `obs_radio`
--
ALTER TABLE `obs_radio`
  MODIFY `id_obs_radio` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `obs_rol`
--
ALTER TABLE `obs_rol`
  MODIFY `id_obs_rol` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `obs_tv`
--
ALTER TABLE `obs_tv`
  MODIFY `id_obs_tv` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `obs_visit`
--
ALTER TABLE `obs_visit`
  MODIFY `id_obs_visit` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `ordens_escolta`
--
ALTER TABLE `ordens_escolta`
  MODIFY `idescolta` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `ordens_escolta_det`
--
ALTER TABLE `ordens_escolta_det`
  MODIFY `id_escolta_det` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `ordens_escolta_locais`
--
ALTER TABLE `ordens_escolta_locais`
  MODIFY `id_local_escolta` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `ordens_escolta_tipo`
--
ALTER TABLE `ordens_escolta_tipo`
  MODIFY `id_tipo` smallint(3) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `ordens_saida`
--
ALTER TABLE `ordens_saida`
  MODIFY `id_ord_saida` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `ordens_saida_det`
--
ALTER TABLE `ordens_saida_det`
  MODIFY `id_ord_saida_det` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `ordens_saida_locais`
--
ALTER TABLE `ordens_saida_locais`
  MODIFY `id_local_ord_saida` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `ordens_saida_tipo`
--
ALTER TABLE `ordens_saida_tipo`
  MODIFY `id_tipo` smallint(3) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `peculio`
--
ALTER TABLE `peculio`
  MODIFY `idpeculio` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `peculio_mov`
--
ALTER TABLE `peculio_mov`
  MODIFY `idpeculio` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `peculio_saldo`
--
ALTER TABLE `peculio_saldo`
  MODIFY `idpeculio` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `protocolo`
--
ALTER TABLE `protocolo`
  MODIFY `idprot` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `rafael`
--
ALTER TABLE `rafael`
  MODIFY `teste` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `raio`
--
ALTER TABLE `raio`
  MODIFY `idraio` smallint(2) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `replace_unidades`
--
ALTER TABLE `replace_unidades`
  MODIFY `idrpl` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `sedex`
--
ALTER TABLE `sedex`
  MODIFY `idsedex` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `sedex_itens`
--
ALTER TABLE `sedex_itens`
  MODIFY `id_item` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `sedex_motivo`
--
ALTER TABLE `sedex_motivo`
  MODIFY `idmotivo` smallint(5) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `sedex_mov`
--
ALTER TABLE `sedex_mov`
  MODIFY `idmovsedex` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `sicop_n_setor`
--
ALTER TABLE `sicop_n_setor`
  MODIFY `id_n_setor` smallint(3) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `sicop_setor`
--
ALTER TABLE `sicop_setor`
  MODIFY `idsetor` smallint(3) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `sicop_u_n`
--
ALTER TABLE `sicop_u_n`
  MODIFY `idnivel` tinyint(5) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `sicop_unidade`
--
ALTER TABLE `sicop_unidade`
  MODIFY `idup` tinyint(2) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `sicop_unidade_instal`
--
ALTER TABLE `sicop_unidade_instal`
  MODIFY `idup` tinyint(2) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `sicop_users`
--
ALTER TABLE `sicop_users`
  MODIFY `iduser` smallint(3) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `sicop_users_perm`
--
ALTER TABLE `sicop_users_perm`
  MODIFY `idpermissao` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `sindicancias`
--
ALTER TABLE `sindicancias`
  MODIFY `idsind` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tb_digital`
--
ALTER TABLE `tb_digital`
  MODIFY `id_digital` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tb_setor`
--
ALTER TABLE `tb_setor`
  MODIFY `set_cd_cod` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tipo_prot_doc`
--
ALTER TABLE `tipo_prot_doc`
  MODIFY `id_tipo_doc` smallint(2) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tipo_prot_modo_in`
--
ALTER TABLE `tipo_prot_modo_in`
  MODIFY `id_modo_in` smallint(2) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tipo_sit_det_busca`
--
ALTER TABLE `tipo_sit_det_busca`
  MODIFY `idtipo_sit` tinyint(3) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tipo_un_medida`
--
ALTER TABLE `tipo_un_medida`
  MODIFY `idum` smallint(5) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tipoalias`
--
ALTER TABLE `tipoalias`
  MODIFY `idtipoalias` smallint(3) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tipoartigo`
--
ALTER TABLE `tipoartigo`
  MODIFY `idartigo` smallint(5) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tipocabelos`
--
ALTER TABLE `tipocabelos`
  MODIFY `idcabelos` smallint(2) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tipoconduta`
--
ALTER TABLE `tipoconduta`
  MODIFY `idconduta` smallint(2) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tipocutis`
--
ALTER TABLE `tipocutis`
  MODIFY `idcutis` smallint(2) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tipoescolaridade`
--
ALTER TABLE `tipoescolaridade`
  MODIFY `idescolaridade` smallint(2) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tipoestadocivil`
--
ALTER TABLE `tipoestadocivil`
  MODIFY `idest_civil` smallint(2) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tipomov`
--
ALTER TABLE `tipomov`
  MODIFY `idtipo_mov` smallint(2) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tiponacionalidade`
--
ALTER TABLE `tiponacionalidade`
  MODIFY `idnacionalidade` smallint(3) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tipoolhos`
--
ALTER TABLE `tipoolhos`
  MODIFY `idolhos` smallint(2) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tipoparentesco`
--
ALTER TABLE `tipoparentesco`
  MODIFY `idparentesco` int(3) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tipopeculio`
--
ALTER TABLE `tipopeculio`
  MODIFY `idtipopeculio` smallint(4) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tiporeligiao`
--
ALTER TABLE `tiporeligiao`
  MODIFY `idreligiao` smallint(2) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tipositdet`
--
ALTER TABLE `tipositdet`
  MODIFY `idsitdet` smallint(3) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tipositpda`
--
ALTER TABLE `tipositpda`
  MODIFY `idsitpda` int(3) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `tiposituacaoprocessual`
--
ALTER TABLE `tiposituacaoprocessual`
  MODIFY `idsit_proc` smallint(2) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `unidades`
--
ALTER TABLE `unidades`
  MODIFY `idunidades` smallint(5) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `visita_fotos`
--
ALTER TABLE `visita_fotos`
  MODIFY `id_foto` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `visita_mov`
--
ALTER TABLE `visita_mov`
  MODIFY `idmov_visit` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `visita_susp`
--
ALTER TABLE `visita_susp`
  MODIFY `id_visit_susp` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `visitas`
--
ALTER TABLE `visitas`
  MODIFY `idvisita` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `visitas_online`
--
ALTER TABLE `visitas_online`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `visitas_record`
--
ALTER TABLE `visitas_record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `visitas_site`
--
ALTER TABLE `visitas_site`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- Restrições para dumps de tabelas
--

--
-- Restrições para tabelas `aliases`
--
ALTER TABLE `aliases`
  ADD CONSTRAINT `FK_aliases_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_aliases_tipoalias` FOREIGN KEY (`cod_tipoalias`) REFERENCES `tipoalias` (`idtipoalias`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `apcc`
--
ALTER TABLE `apcc`
  ADD CONSTRAINT `FK_apcc_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_apcc_numeroapcc` FOREIGN KEY (`cod_numapcc`) REFERENCES `numeroapcc` (`idnumapcc`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_apcc_tipoconduta` FOREIGN KEY (`cod_conduta`) REFERENCES `tipoconduta` (`idconduta`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `apcc_mov`
--
ALTER TABLE `apcc_mov`
  ADD CONSTRAINT `FK_apcc_mov_apcc` FOREIGN KEY (`cod_apcc`) REFERENCES `apcc` (`idapcc`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_apcc_mov_mov_det_in` FOREIGN KEY (`cod_movin`) REFERENCES `mov_det` (`id_mov`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_apcc_mov_mov_det_out` FOREIGN KEY (`cod_movout`) REFERENCES `mov_det` (`id_mov`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `audiencias`
--
ALTER TABLE `audiencias`
  ADD CONSTRAINT `FK_audiencias_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `bonde_det`
--
ALTER TABLE `bonde_det`
  ADD CONSTRAINT `FK_bonde_det_bonde_locais` FOREIGN KEY (`cod_bonde_local`) REFERENCES `bonde_locais` (`idblocal`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_bonde_det_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `bonde_locais`
--
ALTER TABLE `bonde_locais`
  ADD CONSTRAINT `FK_bonde_locais_bonde` FOREIGN KEY (`cod_bonde`) REFERENCES `bonde` (`idbonde`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_bonde_locais_unidades` FOREIGN KEY (`cod_unidade`) REFERENCES `unidades` (`idunidades`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `cela`
--
ALTER TABLE `cela`
  ADD CONSTRAINT `FK_cela_raio` FOREIGN KEY (`cod_raio`) REFERENCES `raio` (`idraio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `cidades`
--
ALTER TABLE `cidades`
  ADD CONSTRAINT `FK_cidades_estados` FOREIGN KEY (`cod_uf`) REFERENCES `estados` (`idestado`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `det_fotos`
--
ALTER TABLE `det_fotos`
  ADD CONSTRAINT `FK_det_fotos_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `detentos`
--
ALTER TABLE `detentos`
  ADD CONSTRAINT `FK_detentos_cela` FOREIGN KEY (`cod_cela`) REFERENCES `cela` (`idcela`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_detentos_cidades` FOREIGN KEY (`cod_cidade`) REFERENCES `cidades` (`idcidade`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_detentos_local_prisao` FOREIGN KEY (`cod_local_prisao`) REFERENCES `unidades` (`idunidades`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_detentos_mov_det_in` FOREIGN KEY (`cod_movin`) REFERENCES `mov_det` (`id_mov`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_detentos_mov_det_out` FOREIGN KEY (`cod_movout`) REFERENCES `mov_det` (`id_mov`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_detentos_tipoartigo` FOREIGN KEY (`cod_artigo`) REFERENCES `tipoartigo` (`idartigo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_detentos_tipocabelos` FOREIGN KEY (`cod_cabelos`) REFERENCES `tipocabelos` (`idcabelos`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_detentos_tipocutis` FOREIGN KEY (`cod_cutis`) REFERENCES `tipocutis` (`idcutis`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_detentos_tipoescolaridade` FOREIGN KEY (`cod_instrucao`) REFERENCES `tipoescolaridade` (`idescolaridade`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_detentos_tipoestadocivil` FOREIGN KEY (`cod_est_civil`) REFERENCES `tipoestadocivil` (`idest_civil`),
  ADD CONSTRAINT `FK_detentos_tiponacionalidade` FOREIGN KEY (`cod_nacionalidade`) REFERENCES `tiponacionalidade` (`idnacionalidade`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_detentos_tipoolhos` FOREIGN KEY (`cod_olhos`) REFERENCES `tipoolhos` (`idolhos`),
  ADD CONSTRAINT `FK_detentos_tiporeligiao` FOREIGN KEY (`cod_religiao`) REFERENCES `tiporeligiao` (`idreligiao`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_detentos_tiposituacaoprocessual` FOREIGN KEY (`cod_sit_proc`) REFERENCES `tiposituacaoprocessual` (`idsit_proc`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `detentos_fotos_esp`
--
ALTER TABLE `detentos_fotos_esp`
  ADD CONSTRAINT `FK_detentos_fotos_esp_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `detentos_radio`
--
ALTER TABLE `detentos_radio`
  ADD CONSTRAINT `FK_detentos_radio_cela` FOREIGN KEY (`cod_cela`) REFERENCES `cela` (`idcela`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_detentos_radio_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `detentos_tv`
--
ALTER TABLE `detentos_tv`
  ADD CONSTRAINT `FK_detentos_tv_cela` FOREIGN KEY (`cod_cela`) REFERENCES `cela` (`idcela`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_detentos_tv_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `grade`
--
ALTER TABLE `grade`
  ADD CONSTRAINT `FK_grade_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `inteligencia`
--
ALTER TABLE `inteligencia`
  ADD CONSTRAINT `FK_inteligencia_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `listatel_num`
--
ALTER TABLE `listatel_num`
  ADD CONSTRAINT `FK_listatel_num_listatel` FOREIGN KEY (`cod_listatel`) REFERENCES `listatel` (`idlistatel`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `mov_det`
--
ALTER TABLE `mov_det`
  ADD CONSTRAINT `FK_mov_det_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_mov_det_tipomov` FOREIGN KEY (`cod_tipo_mov`) REFERENCES `tipomov` (`idtipo_mov`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_mov_det_unidades` FOREIGN KEY (`cod_local_mov`) REFERENCES `unidades` (`idunidades`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `mov_rc_det`
--
ALTER TABLE `mov_rc_det`
  ADD CONSTRAINT `FK_mov_rc_det_cela_new` FOREIGN KEY (`cod_n_cela`) REFERENCES `cela` (`idcela`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_mov_rc_det_cela_old` FOREIGN KEY (`cod_old_cela`) REFERENCES `cela` (`idcela`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_mov_rc_det_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `msg`
--
ALTER TABLE `msg`
  ADD CONSTRAINT `FK_msg_cdriouser_de` FOREIGN KEY (`msg_de`) REFERENCES `sicop_users` (`iduser`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_msg_cdriouser_para` FOREIGN KEY (`msg_para`) REFERENCES `sicop_users` (`iduser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `obs_aud`
--
ALTER TABLE `obs_aud`
  ADD CONSTRAINT `FK_obs_aud_audiencias` FOREIGN KEY (`cod_audiencia`) REFERENCES `audiencias` (`idaudiencia`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `obs_det`
--
ALTER TABLE `obs_det`
  ADD CONSTRAINT `FK_obs_det_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `obs_grade`
--
ALTER TABLE `obs_grade`
  ADD CONSTRAINT `FK_obs_grade_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `obs_inteli`
--
ALTER TABLE `obs_inteli`
  ADD CONSTRAINT `FK_obs_inteli_inteligencia` FOREIGN KEY (`cod_inteli`) REFERENCES `inteligencia` (`idinteli`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `obs_listatel`
--
ALTER TABLE `obs_listatel`
  ADD CONSTRAINT `FK_obs_listatel_listatel` FOREIGN KEY (`cod_listatel`) REFERENCES `listatel` (`idlistatel`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `obs_pda`
--
ALTER TABLE `obs_pda`
  ADD CONSTRAINT `FK_obs_pda_sindicancias` FOREIGN KEY (`cod_pda`) REFERENCES `sindicancias` (`idsind`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `obs_radio`
--
ALTER TABLE `obs_radio`
  ADD CONSTRAINT `FK_obs_radio_detentos_radio` FOREIGN KEY (`cod_radio`) REFERENCES `detentos_radio` (`idradio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `obs_rol`
--
ALTER TABLE `obs_rol`
  ADD CONSTRAINT `FK_obs_rol_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `obs_tv`
--
ALTER TABLE `obs_tv`
  ADD CONSTRAINT `FK_obs_tv_detentos_tv` FOREIGN KEY (`cod_tv`) REFERENCES `detentos_tv` (`idtv`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `obs_visit`
--
ALTER TABLE `obs_visit`
  ADD CONSTRAINT `FK_obs_visit_visitas` FOREIGN KEY (`cod_visita`) REFERENCES `visitas` (`idvisita`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `ordens_escolta_det`
--
ALTER TABLE `ordens_escolta_det`
  ADD CONSTRAINT `FK_ordens_escolta_det_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ordens_escolta_det_ordens_escolta_locais` FOREIGN KEY (`cod_local_escolta`) REFERENCES `ordens_escolta_locais` (`id_local_escolta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ordens_escolta_det_ordens_escolta_tipo` FOREIGN KEY (`cod_tipo`) REFERENCES `ordens_escolta_tipo` (`id_tipo`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `ordens_escolta_locais`
--
ALTER TABLE `ordens_escolta_locais`
  ADD CONSTRAINT `FK_ordens_escolta_locais_locais_apr` FOREIGN KEY (`cod_local`) REFERENCES `locais_apr` (`idlocal`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ordens_escolta_locais_ordens_escolta` FOREIGN KEY (`cod_escolta`) REFERENCES `ordens_escolta` (`idescolta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `ordens_saida_det`
--
ALTER TABLE `ordens_saida_det`
  ADD CONSTRAINT `FK_ordens_saida_det_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ordens_saida_det_ordens_saida_locais` FOREIGN KEY (`cod_local_ord_saida`) REFERENCES `ordens_saida_locais` (`id_local_ord_saida`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ordens_saida_det_ordens_saida_tipo` FOREIGN KEY (`cod_tipo`) REFERENCES `ordens_saida_tipo` (`id_tipo`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `ordens_saida_locais`
--
ALTER TABLE `ordens_saida_locais`
  ADD CONSTRAINT `FK_ordens_saida_locais_locais_apr` FOREIGN KEY (`cod_local`) REFERENCES `locais_apr` (`idlocal`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ordens_saida_locais_ordens_saida` FOREIGN KEY (`cod_ord_saida`) REFERENCES `ordens_saida` (`id_ord_saida`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `peculio`
--
ALTER TABLE `peculio`
  ADD CONSTRAINT `FK_peculio_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_peculio_tipopeculio` FOREIGN KEY (`cod_tipo_peculio`) REFERENCES `tipopeculio` (`idtipopeculio`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `peculio_mov`
--
ALTER TABLE `peculio_mov`
  ADD CONSTRAINT `FK_peculio_mov_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `peculio_saldo`
--
ALTER TABLE `peculio_saldo`
  ADD CONSTRAINT `FK_peculio_saldo_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `protocolo`
--
ALTER TABLE `protocolo`
  ADD CONSTRAINT `FK_protocolo_sicop_setor` FOREIGN KEY (`prot_cod_setor`) REFERENCES `sicop_setor` (`idsetor`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_protocolo_tipo_prot_doc` FOREIGN KEY (`prot_cod_tipo_doc`) REFERENCES `tipo_prot_doc` (`id_tipo_doc`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_protocolo_tipo_prot_modo_in` FOREIGN KEY (`prot_cod_modo_in`) REFERENCES `tipo_prot_modo_in` (`id_modo_in`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `replace_unidades`
--
ALTER TABLE `replace_unidades`
  ADD CONSTRAINT `FK_replace_unidades_unidades` FOREIGN KEY (`cod_correct_name`) REFERENCES `unidades` (`idunidades`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `sedex`
--
ALTER TABLE `sedex`
  ADD CONSTRAINT `FK_sedex_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_sedex_sedex_motivo` FOREIGN KEY (`cod_motivo_dev`) REFERENCES `sedex_motivo` (`idmotivo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_sedex_visitas` FOREIGN KEY (`cod_visita`) REFERENCES `visitas` (`idvisita`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `sedex_itens`
--
ALTER TABLE `sedex_itens`
  ADD CONSTRAINT `FK_sedex_itens_sedex` FOREIGN KEY (`cod_sedex`) REFERENCES `sedex` (`idsedex`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_sedex_itens_tipo_un_medida` FOREIGN KEY (`cod_um`) REFERENCES `tipo_un_medida` (`idum`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `sedex_mov`
--
ALTER TABLE `sedex_mov`
  ADD CONSTRAINT `FK_sedex_mov_sedex` FOREIGN KEY (`cod_sedex`) REFERENCES `sedex` (`idsedex`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `sicop_users`
--
ALTER TABLE `sicop_users`
  ADD CONSTRAINT `FK_sicop_users_sicop_setor` FOREIGN KEY (`cod_setor`) REFERENCES `sicop_setor` (`idsetor`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `sicop_users_perm`
--
ALTER TABLE `sicop_users_perm`
  ADD CONSTRAINT `fk_sicop_n_setor_sicop_users_perm` FOREIGN KEY (`cod_n_setor`) REFERENCES `sicop_n_setor` (`id_n_setor`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sicop_u_n_sicop_users_perm` FOREIGN KEY (`cod_nivel`) REFERENCES `sicop_u_n` (`idnivel`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sicop_users_sicop_users_perm` FOREIGN KEY (`cod_user`) REFERENCES `sicop_users` (`iduser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `sindicancias`
--
ALTER TABLE `sindicancias`
  ADD CONSTRAINT `FK_sindicancias_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_sindicancias_tipositdet` FOREIGN KEY (`cod_sit_detento`) REFERENCES `tipositdet` (`idsitdet`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `visita_fotos`
--
ALTER TABLE `visita_fotos`
  ADD CONSTRAINT `FK_visita_fotos_visitas` FOREIGN KEY (`cod_visita`) REFERENCES `visitas` (`idvisita`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `visita_mov`
--
ALTER TABLE `visita_mov`
  ADD CONSTRAINT `FK_visita_mov_visitas` FOREIGN KEY (`cod_visita`) REFERENCES `visitas` (`idvisita`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `visita_susp`
--
ALTER TABLE `visita_susp`
  ADD CONSTRAINT `FK_visita_susp_visitas` FOREIGN KEY (`cod_visita`) REFERENCES `visitas` (`idvisita`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `visitas`
--
ALTER TABLE `visitas`
  ADD CONSTRAINT `FK_visitas_cidades` FOREIGN KEY (`cod_cidade_v`) REFERENCES `cidades` (`idcidade`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_visitas_detentos` FOREIGN KEY (`cod_detento`) REFERENCES `detentos` (`iddetento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_visitas_tipoparentesco` FOREIGN KEY (`cod_parentesco`) REFERENCES `tipoparentesco` (`idparentesco`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `visitas_online`
--
ALTER TABLE `visitas_online`
  ADD CONSTRAINT `FK_visitas_online_sicop_users` FOREIGN KEY (`cod_user`) REFERENCES `sicop_users` (`iduser`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
