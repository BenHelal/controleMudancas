-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 17, 2023 at 01:12 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `controlmud`
--
CREATE DATABASE IF NOT EXISTS `controlmud` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `controlmud`;

-- --------------------------------------------------------

--
-- Table structure for table `config_email`
--

CREATE TABLE `config_email` (
  `id` int(11) NOT NULL,
  `host` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `smtp_auth` tinyint(1) NOT NULL,
  `port` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_system` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_obj` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chart_set` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `config_email`
--

INSERT INTO `config_email` (`id`, `host`, `smtp_auth`, `port`, `username`, `password`, `email_system`, `title_obj`, `subject`, `chart_set`) VALUES
(1, 'smtp.office365.com', 1, 587, 'noreply@serdia.com.br', '9BhAsZw8a8ZrnQzX', 'noreply@serdia.com.br', 'Serdia Control Mudanças', 'Control de mudanças', 'UTF-8');

-- --------------------------------------------------------

--
-- Table structure for table `departemant`
--

CREATE TABLE `departemant` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departemant`
--

INSERT INTO `departemant` (`id`, `name`) VALUES
(1, 'TI'),
(2, 'PCM'),
(3, 'PCP'),
(4, 'ENG PRODUTO'),
(5, 'COMERCIAL'),
(6, 'EXPEDIÇÃO'),
(7, 'COMPRAS'),
(8, 'GERÊNCIA'),
(9, 'ENG PROCESSO'),
(10, 'ENG PROC. SMT'),
(11, 'ENG. TESTE'),
(12, 'T.I. PATO BRANCO'),
(13, 'MANUTENÇÃO'),
(14, 'POS COMPOR'),
(15, 'ALMOXARIFADO'),
(16, 'GERÊNCIA PB'),
(17, 'FINANCEIRO'),
(18, 'PORTARIA'),
(19, 'PRODUÇÃO PTH'),
(20, 'PRÉ-FORMA'),
(21, 'SEG. DO TRAB.'),
(22, 'ENG. DA QUALIDADE'),
(23, 'SMD'),
(24, 'DESENVOLVIMENTO'),
(25, 'RH'),
(26, 'DIRETORIA'),
(27, 'PRODUÇÃO PB'),
(28, 'ZELADORIA'),
(29, 'Porto Seco SM'),
(30, 'RECEPÇÃO'),
(31, 'FATURAMENTO'),
(32, 'ALMOX PB'),
(33, 'QUALIDADE PB'),
(34, 'P&D'),
(35, 'OPERAÇÕES (SUPORTE)');

-- --------------------------------------------------------

--
-- Table structure for table `departemant_mudancas`
--
-- Error reading structure for table controlmud.departemant_mudancas: #1932 - Table &#039;controlmud.departemant_mudancas&#039; doesn&#039;t exist in engine
-- Error reading data for table controlmud.departemant_mudancas: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `controlmud`.`departemant_mudancas`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `departemant_mudancass`
--

CREATE TABLE `departemant_mudancass` (
  `id` int(11) NOT NULL,
  `mudancas_id` int(11) DEFAULT NULL,
  `departemant_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departemant_mudancass`
--

INSERT INTO `departemant_mudancass` (`id`, `mudancas_id`, `departemant_id`) VALUES
(602, 61, 1),
(608, 60, 1),
(654, 62, 9),
(655, 62, 8),
(656, 62, 7),
(657, 62, 6),
(658, 62, 5),
(659, 62, 4),
(660, 62, 3),
(661, 62, 2),
(662, 62, 1);

-- --------------------------------------------------------

--
-- Table structure for table `departemant_process`
--

CREATE TABLE `departemant_process` (
  `id` int(11) NOT NULL,
  `process_id` int(11) DEFAULT NULL,
  `departemant_id` int(11) DEFAULT NULL,
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departemant_process`
--

INSERT INTO `departemant_process` (`id`, `process_id`, `departemant_id`, `comment`, `person_id`) VALUES
(213, 210, 1, NULL, NULL),
(214, 211, 1, NULL, NULL),
(215, 212, 9, NULL, NULL),
(216, 213, 8, NULL, NULL),
(217, 214, 7, NULL, NULL),
(218, 215, 6, NULL, NULL),
(219, 216, 5, NULL, NULL),
(220, 217, 4, NULL, NULL),
(221, 218, 3, NULL, NULL),
(222, 219, 2, NULL, NULL),
(223, 220, 1, NULL, NULL),
(224, 220, 1, 'Teste aprovado', 247);

-- --------------------------------------------------------

--
-- Table structure for table `email`
--

CREATE TABLE `email` (
  `id` int(11) NOT NULL,
  `send_by_id` int(11) DEFAULT NULL,
  `send_to_id` int(11) DEFAULT NULL,
  `mudancas_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email`
--

INSERT INTO `email` (`id`, `send_by_id`, `send_to_id`, `mudancas_id`, `title`, `body`) VALUES
(176, 191, 191, 41, 'Create new controle do mudancas', 'create'),
(177, 191, 191, 41, 'gerente do controle do mudancas', 'gerente'),
(178, 191, 191, 41, 'approved gerente controle do mudancas', 'approved_ger'),
(182, 78, 78, 42, 'Create new controle do mudancas', 'create'),
(183, 191, 191, 41, 'approved gestor controle do mudancas', 'approved_gestor'),
(186, 191, 191, 43, 'Create new controle do mudancas', 'create'),
(187, 191, 191, 43, 'gerente do controle do mudancas', 'gerente'),
(188, 191, 191, 43, 'approved gerente controle do mudancas', 'approved_ger'),
(189, 191, 191, 43, 'reject gestor controle do mudancas', 'reject_gestor'),
(192, 137, 137, 44, 'Create new controle do mudancas', 'create'),
(193, 137, 78, 44, 'Create new controle do mudancas', 'manager'),
(194, 78, 137, 44, 'approved gerente controle do mudancas', 'approved_ger'),
(195, 78, 137, 44, 'gerente do controle do mudancas', 'gerente'),
(196, 78, 137, 44, 'approved gerente controle do mudancas', 'approved_ger'),
(197, 137, 137, 44, 'approved gestor controle do mudancas', 'approved_gestor'),
(200, 191, 191, 45, 'Create new controle do mudancas', 'create'),
(201, 191, 191, 45, 'gerente do controle do mudancas', 'gerente'),
(202, 191, 191, 45, 'approved gerente controle do mudancas', 'approved_ger'),
(203, 191, 191, 45, 'approved gestor controle do mudancas', 'approved_gestor'),
(206, 191, 191, 46, 'Create new controle do mudancas', 'create'),
(207, 191, 191, 46, 'gerente do controle do mudancas', 'gerente'),
(208, 191, 191, 46, 'approved gerente controle do mudancas', 'approved_ger'),
(209, 191, 191, 46, 'approved gestor controle do mudancas', 'approved_gestor');

-- --------------------------------------------------------

--
-- Table structure for table `icons`
--

CREATE TABLE `icons` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `manager`
--

CREATE TABLE `manager` (
  `id` int(11) NOT NULL,
  `person_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mudancas`
--

CREATE TABLE `mudancas` (
  `id` int(11) NOT NULL,
  `add_by_id` int(11) DEFAULT NULL,
  `manger_mudancas_id` int(11) DEFAULT NULL,
  `area_resp_id` int(11) NOT NULL,
  `manager_user_add_id` int(11) DEFAULT NULL,
  `nome_mudanca` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc_mudanca` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc_impacto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc_impacto_area` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `justif` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approved` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `done` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nansen_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nansen_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_mudancas` date DEFAULT NULL,
  `end_mudancas` date DEFAULT NULL,
  `effictive_start_date` date DEFAULT NULL,
  `cost` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `implemented` tinyint(1) DEFAULT NULL,
  `imp_desc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_imp` date DEFAULT NULL,
  `com_man` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `com_gest` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `app_man` int(11) DEFAULT NULL,
  `app_gest` int(11) DEFAULT NULL,
  `data_creation` datetime NOT NULL,
  `manager_user_comment` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manager_user_app` int(11) DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mudancas`
--

INSERT INTO `mudancas` (`id`, `add_by_id`, `manger_mudancas_id`, `area_resp_id`, `manager_user_add_id`, `nome_mudanca`, `desc_mudanca`, `desc_impacto`, `desc_impacto_area`, `justif`, `approved`, `done`, `nansen_name`, `nansen_number`, `start_mudancas`, `end_mudancas`, `effictive_start_date`, `cost`, `implemented`, `imp_desc`, `date_of_imp`, `com_man`, `com_gest`, `app_man`, `app_gest`, `data_creation`, `manager_user_comment`, `manager_user_app`, `photo`) VALUES
(41, 191, 191, 36, 191, 'xdqzxd', 'dqxzdqz', 'xdzqdx', 'dxqzdx', 'dxzq', 'approved', 'Feito', '', '', '2023-01-12', '2023-01-12', '2023-01-12', 'xdzq', NULL, 'xdzqxd', '2023-01-12', 'dxqzxd', 'xdzqxd', 1, 1, '2023-01-12 14:59:02', 'como gerente de 002 – COMERCIAL MATRIZ', 1, NULL),
(42, 78, NULL, 55, 78, 'teste', 'teste', 'teste', 'teste', 'teste', 'approved', NULL, '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-12 15:01:58', 'como gerente de 021 – TI MATRIZ (INFRAESTRUTURA E REDE)', 1, NULL),
(43, 191, 191, 36, 191, 'xdqzd', 'qxzdqzxdq', 'qzxdqzqxd', 'xzqdzq', 'xdzqd', 'approved', 'Feito', '', '', '2023-01-12', '2023-01-12', '2023-01-12', 'dxqz', 0, 'xdqzx', NULL, 'xdzqxd', 'dxzqdq', 1, 2, '2023-01-12 15:02:56', 'como gerente de 002 – COMERCIAL MATRIZ', 1, NULL),
(44, 137, 137, 55, 78, 'hkuvuivb', 'adasda', 'adasda', 'asdada', 'asdsadsa', NULL, 'Feito', '', '', '2023-01-12', '2023-01-12', '2023-01-12', NULL, 1, NULL, '2023-01-13', 'sadsad', 'teste', 1, 1, '2023-01-12 15:06:41', 'ok', 1, NULL),
(45, 191, 191, 36, 191, 'xdqz', 'xqzxzqd', 'qxzd', 'xqdzxdqz', 'xqzd', 'approved', 'Feito', '', '', '2023-01-13', '2023-01-13', '2023-01-13', 'xdqz', NULL, 'xdqz', '2023-01-13', 'test', 'test', 1, 1, '2023-01-13 09:25:55', 'como gerente de 002 – COMERCIAL MATRIZ', 1, 'logoUser.png'),
(46, 191, 191, 36, 191, 'dxzq', 'dxzq', 'xdqzxdqz', 'xdqzqxd', 'xdqzd', 'approved', 'Feito', '', '', '2023-01-13', '2023-01-13', '2023-01-13', 'xdqz', NULL, 'xqzd', '2023-01-13', 'test', 'test', 1, 1, '2023-01-13 09:30:08', 'como gerente de 002 – COMERCIAL MATRIZ', 1, '46.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `mudancas_sector`
--

CREATE TABLE `mudancas_sector` (
  `mudancas_id` int(11) NOT NULL,
  `sector_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mudancas_sector`
--

INSERT INTO `mudancas_sector` (`mudancas_id`, `sector_id`) VALUES
(41, 36),
(42, 55),
(43, 36),
(44, 55),
(45, 36),
(46, 36);

-- --------------------------------------------------------

--
-- Table structure for table `nansen`
--
-- Error reading structure for table controlmud.nansen: #1932 - Table &#039;controlmud.nansen&#039; doesn&#039;t exist in engine
-- Error reading data for table controlmud.nansen: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `controlmud`.`nansen`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `person`
--

CREATE TABLE `person` (
  `id` int(11) NOT NULL,
  `function_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permission` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departemant` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_connection` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `person`
--

INSERT INTO `person` (`id`, `function_id`, `name`, `user_name`, `password`, `role`, `permission`, `email`, `departemant`, `last_connection`) VALUES
(1, NULL, 'Wellington  Ulisses Santos', NULL, NULL, NULL, NULL, 'wellington.santos@serdia.com.br', 'TI', NULL),
(2, NULL, 'Wellington Santos', NULL, NULL, NULL, NULL, 'nitens@serdia.com.br', 'TI', NULL),
(3, NULL, 'Tereza Silva', NULL, NULL, NULL, NULL, 'tereza.silva@serdia.com.br', 'PCM', NULL),
(4, NULL, 'Alessandro Gusse', NULL, NULL, NULL, NULL, 'alessandro.gusse@serdia.com.br', 'PCP', NULL),
(5, NULL, 'Tais Makellen Andrade', NULL, NULL, NULL, NULL, '', 'ENG PRODUTO', NULL),
(6, NULL, 'Odete Ferreira', NULL, NULL, NULL, NULL, 'wellington.santos@serdia.com.br', 'COMERCIAL', NULL),
(7, NULL, 'Michel Lima', NULL, NULL, NULL, NULL, 'expedicao@serdia.com.br', 'EXPEDIÇÃO', NULL),
(8, NULL, 'Nilson Ishikawa', NULL, NULL, NULL, NULL, ' nilson@serdia.com.br', 'TI', NULL),
(9, NULL, 'Marcela Petruy', NULL, NULL, NULL, NULL, 'marcela.petruy@serdia.com.br', 'COMPRAS', NULL),
(10, NULL, 'Flavio Rocha', NULL, NULL, NULL, NULL, ' flavio.rocha@serdia.com.br', 'GERÊNCIA', NULL),
(11, NULL, 'Silmara da Silva', NULL, NULL, NULL, NULL, 'engenharia@serdia.com.br', 'ENG PRODUTO', NULL),
(12, NULL, 'Fabiano Alves Francisco', NULL, NULL, NULL, NULL, 'fabiano.francisco@serdia.com.br', 'PCP', NULL),
(13, NULL, 'Rafael Przybysz', NULL, NULL, NULL, NULL, 'engenharia@serdia.com.br', 'ENG PRODUTO', NULL),
(14, NULL, 'Daniele Araujo', NULL, NULL, NULL, NULL, 'processos@serdia.com.br', 'ENG PROCESSO', NULL),
(15, NULL, 'Rafael Tavora', NULL, NULL, NULL, NULL, 'rafael.tavora@serdia.com.br', 'GERÊNCIA', NULL),
(16, NULL, 'João Ramiro', NULL, NULL, NULL, NULL, 'joao.ramiro@serdia.com.br', 'ENG PROC. SMT', NULL),
(17, NULL, 'Cleber Brantl de Souza', NULL, NULL, NULL, NULL, 'cleber.brantl@serdia.com.br', 'ENG. TESTE', NULL),
(18, NULL, 'Diego Biondo', NULL, NULL, NULL, NULL, 'diego.biondo@serdia.com.br', 'ENG PROCESSO', NULL),
(19, NULL, 'Mara Lucia Pitol de Barros', NULL, NULL, NULL, NULL, 'mara.barros@serdia.com.br', 'COMERCIAL', NULL),
(20, NULL, 'Leila Stoco', NULL, NULL, NULL, NULL, 'leila.stoco@serdia.com.br', 'COMPRAS', NULL),
(21, NULL, 'Ricardo Mauricio Dorigo', NULL, NULL, NULL, NULL, 'debug.pb@serdia.com.br', 'T.I. PATO BRANCO', NULL),
(22, NULL, 'Rita', NULL, NULL, NULL, NULL, 'rita.cassia@serdia.com.br', 'COMPRAS', NULL),
(23, NULL, 'Diego Janke', NULL, NULL, NULL, NULL, 'diego.janke@serdia.com.br', 'MANUTENÇÃO', NULL),
(24, NULL, 'Ana Carla Mendes', NULL, NULL, NULL, NULL, 'anacarla.mendes@serdia.com.br', 'COMERCIAL', NULL),
(25, NULL, 'Nilva Rocha Sofa', NULL, NULL, NULL, NULL, 'poscompor@serdia.com.br', 'POS COMPOR', NULL),
(26, NULL, 'Rogério Cristovão José', NULL, NULL, NULL, NULL, 'almox3@serdia.com.br', 'ALMOXARIFADO', NULL),
(27, NULL, 'Francisca Silva', NULL, NULL, NULL, NULL, '', 'ALMOXARIFADO', NULL),
(28, NULL, 'Rosilene Aparecida Silva', NULL, NULL, NULL, NULL, '', 'ALMOXARIFADO', NULL),
(29, NULL, 'Rosana Demetrio Costa', NULL, NULL, NULL, NULL, '', 'GERÊNCIA PB', NULL),
(30, NULL, 'Carina De Fatima', NULL, NULL, NULL, NULL, '', 'ALMOXARIFADO', NULL),
(31, NULL, 'Rosirene De Fátima Coelho', NULL, NULL, NULL, NULL, 'rosirene.coelho@serdia.com.br', 'EXPEDIÇÃO', NULL),
(32, NULL, 'Thais Cristina Shlarski', NULL, NULL, NULL, NULL, '', 'EXPEDIÇÃO', NULL),
(33, NULL, 'Edmilson Veloso', NULL, NULL, NULL, NULL, '', 'EXPEDIÇÃO', NULL),
(34, NULL, 'Janaina Patricia Schwingel', NULL, NULL, NULL, NULL, 'janaina@serdia.com.br', 'FINANCEIRO', NULL),
(35, NULL, 'Patricia Rumor', NULL, NULL, NULL, NULL, 'patricia.rumor@serdia.com.br', 'GERÊNCIA', NULL),
(36, NULL, 'José Roberto Dos Santos Junior', NULL, NULL, NULL, NULL, 'joseroberto.jr@serdia.com.br', 'MANUTENÇÃO', NULL),
(37, NULL, 'Portaria', NULL, NULL, NULL, NULL, 'portaria@serdia.com.br', 'PORTARIA', NULL),
(38, NULL, 'Roseni Coelho', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(39, NULL, 'Rosilene Folquenim', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(40, NULL, 'Lucileide  Da Luz', NULL, NULL, NULL, NULL, '', 'PRÉ-FORMA', NULL),
(41, NULL, 'Vera Lucia Janhz', NULL, NULL, NULL, NULL, 'segurancadotrabalho@serdia.com.br	', 'SEG. DO TRAB.', NULL),
(42, NULL, 'Patricia  Regazzo', NULL, NULL, NULL, NULL, 'patricia.regazzo@serdia.com.br', 'PRODUÇÃO PTH', NULL),
(43, NULL, 'Celina Maximo', NULL, NULL, NULL, NULL, 'programacao.smd-2t@serdia.com.br', 'ENG PROCESSO', NULL),
(44, NULL, 'Elaine Oroski', NULL, NULL, NULL, NULL, 'sac@serdia.com.br', 'ENG. DA QUALIDADE', NULL),
(45, NULL, 'Francielle Kupczki Krezko', NULL, NULL, NULL, NULL, '', 'SMD', NULL),
(46, NULL, 'Tatiane Miranda', NULL, NULL, NULL, NULL, '', 'SMD', NULL),
(47, NULL, 'Rodrigo Pavarin', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(48, NULL, 'Marcelo Daniel Camargo Dos Santos', NULL, NULL, NULL, NULL, '', 'ENG. TESTE', NULL),
(49, NULL, 'Orlandimirton Nunes De Oliveira', NULL, NULL, NULL, NULL, 'astec@serdia.com.br', 'ENG. TESTE', NULL),
(50, NULL, 'Marilene Honorato', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(51, NULL, 'Rogerio Rolim', NULL, NULL, NULL, NULL, '', 'ENG. TESTE', NULL),
(52, NULL, 'Claudio Hayasi', NULL, NULL, NULL, NULL, 'claudio.hayasi@serdia.com.br', 'DESENVOLVIMENTO', NULL),
(53, NULL, 'Josiele Zanoni', NULL, NULL, NULL, NULL, '', 'ENG. TESTE', NULL),
(54, NULL, 'Douglas Henrique Cruz', NULL, NULL, NULL, NULL, '', 'ENG. TESTE', NULL),
(55, NULL, 'Denisia Teixeira', NULL, NULL, NULL, NULL, 'denisia.teixeira@serdia.com.br', 'GERÊNCIA', NULL),
(56, NULL, 'Gleisimara Vieira', NULL, NULL, NULL, NULL, 'gleisimara.vieira@serdia.com.br', 'RH', NULL),
(57, NULL, 'Suzana Ozório', NULL, NULL, NULL, NULL, 'suzana.osorio@serdia.com.br', 'RH', NULL),
(58, NULL, 'Marco Reis', NULL, NULL, NULL, NULL, 'marco.reis@serdia.com.br', 'ENG PROC. SMT', NULL),
(59, NULL, 'Pamela da Costa dos Santos', NULL, NULL, NULL, NULL, 'pamela.santos@serdia.com.br', 'FINANCEIRO', NULL),
(60, NULL, 'Lais Gabrieli Pereira da Silva', NULL, NULL, NULL, NULL, 'lais.silva@serdia.com.br', 'COMPRAS', NULL),
(61, NULL, 'Herbert Chaves de Lima', NULL, NULL, NULL, NULL, '', 'ENG. TESTE', NULL),
(62, NULL, 'Aroilde Istailer Barbosa Souza', NULL, NULL, NULL, NULL, 'aroilde.souza@serdia.com.br', 'ENG PROC. SMT', NULL),
(63, NULL, 'Sirlene Steclan', NULL, NULL, NULL, NULL, 'qualidade.smd@serdia.com.br', 'ENG. DA QUALIDADE', NULL),
(64, NULL, 'Dayane Cindy Patrocinio Roque', NULL, NULL, NULL, NULL, 'dayane.roque@serdia.com.br', 'COMPRAS', NULL),
(65, NULL, 'Diogo Pereira', NULL, NULL, NULL, NULL, 'diogo.pereira@serdia.com.br', 'ENG PROC. SMT', NULL),
(66, NULL, 'Roberto Tamlym Mendonça', NULL, NULL, NULL, NULL, 'roberto@serdia.com.br', 'DIRETORIA', NULL),
(67, NULL, 'Ana Paula Jahnz', NULL, NULL, NULL, NULL, 'ana.jahnz@serdia.com.br', 'ENG PRODUTO', NULL),
(68, NULL, 'Jonatas de Lucas Maziero', NULL, NULL, NULL, NULL, 'ti@serdia.com.br', 'TI', NULL),
(69, NULL, 'Raysa da Silva Lorena', NULL, NULL, NULL, NULL, 'astec2@serdia.com.br', 'ENG. TESTE', NULL),
(70, NULL, 'Wagner Rezende', NULL, NULL, NULL, NULL, 'wagner.rezende@serdia.com.br', 'ENG PRODUTO', NULL),
(71, NULL, 'CARMEM MONTOIA', NULL, NULL, NULL, NULL, 'producao.queclink', 'ENG. TESTE', NULL),
(72, NULL, 'Fernando Borges', NULL, NULL, NULL, NULL, '', 'ENG. TESTE', NULL),
(73, NULL, 'Rosana Costa', NULL, NULL, NULL, NULL, 'rosana.costa@serdia.com.br', 'PRODUÇÃO PB', NULL),
(74, NULL, 'Maicon Oldoni', NULL, NULL, NULL, NULL, 'astec.pb@serdia.com.br', 'PRODUÇÃO PB', NULL),
(75, NULL, 'Majore Cristina Bueno', NULL, NULL, NULL, NULL, '', 'SMD', NULL),
(76, NULL, 'Valdirene Aparecisa de Souza Lima', NULL, NULL, NULL, NULL, '', 'SMD', NULL),
(77, NULL, 'Andressa dos Santos', NULL, NULL, NULL, NULL, '', 'SMD', NULL),
(78, 55, 'Juliano Cassio do Amaral', NULL, NULL, 'admin', 'ler criar atualização', 'juliano.cassio@serdia.com.br', 'TI', '2023-01-12 15:00:26'),
(79, NULL, 'Wellynton Sanches Farias', NULL, NULL, NULL, NULL, '', 'ENG. TESTE', NULL),
(80, NULL, 'Jessica Buzeti', NULL, NULL, NULL, NULL, 'jessica.buzeti@serdia.com.br', 'COMPRAS', NULL),
(81, NULL, 'Wagner Espricigo Maia', NULL, NULL, NULL, NULL, '', 'ENG. TESTE', NULL),
(82, NULL, 'Alessandra Gomes', NULL, NULL, NULL, NULL, 'almox2@serdia.com.br', 'ALMOXARIFADO', NULL),
(83, NULL, 'José Valter', NULL, NULL, NULL, NULL, '', 'ALMOXARIFADO', NULL),
(84, NULL, 'Cristiane Santos', NULL, NULL, NULL, NULL, 'cristiane.santos@serdia.com.br', 'PRODUÇÃO PTH', NULL),
(85, NULL, 'Nara Rejane Mosquer', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PB', NULL),
(86, NULL, 'Luis Alberto Rocha', NULL, NULL, NULL, NULL, 'luis.rocha@serdia.com.br', 'GERÊNCIA', NULL),
(87, 46, 'Julie Patricie Fernandes Vieira', NULL, NULL, 'admin', 'ler criar atualização', 'julie.vieira@serdia.com.br', 'ENG. DA QUALIDADE', NULL),
(88, NULL, 'Nelson Tomelin', NULL, NULL, NULL, NULL, '', 'ENG. TESTE', NULL),
(89, NULL, 'Gisele Pereira', NULL, NULL, NULL, NULL, 'gisele.pereira@serdia.com.br', 'PRODUÇÃO PTH', NULL),
(90, NULL, 'Lucileide Luz', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(91, NULL, 'Valdirene Pereira', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(92, NULL, 'Eliane Alvarenga ', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(93, NULL, 'Patricia Vaudan Domingues', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(94, NULL, 'Alisson Felipe Lages', NULL, NULL, NULL, NULL, 'alisson.lages@serdia.com.br', 'TI', NULL),
(95, NULL, 'Adriana F. de Lima Jeremias', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(96, NULL, 'Ageu Reis de Paiva', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(97, NULL, 'Andreia Ferreira Guedes', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(98, NULL, 'Brandoln Siqueira', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(99, NULL, 'Claudia Bora', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(100, NULL, 'Dayane Duarte Calado', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(101, NULL, 'Gisele de Oliveira Rocha', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(102, NULL, 'Gissele Ribas Lopes', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(103, NULL, 'Guilherme Lima da Silva', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(104, NULL, 'Iglea Pinheiro', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(105, NULL, 'Ivone Lopes Pego', NULL, NULL, NULL, NULL, 'inspecao@serdia.com.br', 'ENG. DA QUALIDADE', NULL),
(106, NULL, 'Ivonete Dal Pontes', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(107, NULL, 'Juliana dos Santos Pereira', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(108, NULL, 'Karla Cristina Bastos Ribeiro', NULL, NULL, NULL, NULL, 'inspecao@serdia.com.br', 'PRODUÇÃO PTH', NULL),
(109, NULL, 'Marcia Regina Santana', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(110, NULL, 'Robert Vieira', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(111, NULL, 'Viviane Proença', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(112, NULL, 'Zeni Ferreira', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(113, NULL, 'Vanessa Vieira de Salle', NULL, NULL, NULL, NULL, '', 'SMD', NULL),
(114, NULL, 'Luiz Alexandre Carneiro da Silva', NULL, NULL, NULL, NULL, '', 'ENG. TESTE', NULL),
(115, NULL, 'Rosa Eliane Moreira Caratchuk', NULL, NULL, NULL, NULL, '', 'ZELADORIA', NULL),
(116, NULL, 'Tatiane Priscila Z. Drozdek', NULL, NULL, NULL, NULL, '', 'PORTARIA', NULL),
(117, NULL, 'Carlos Roberto do Nascimento', NULL, NULL, NULL, NULL, '', 'MANUTENÇÃO', NULL),
(118, NULL, 'Josiel de Souza Ris', NULL, NULL, NULL, NULL, '', 'MANUTENÇÃO', NULL),
(119, NULL, 'Ketly Jahnz dos Santos', NULL, NULL, NULL, NULL, '', 'ALMOXARIFADO', NULL),
(120, NULL, 'Elaine Barbosa de Lima', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PB', NULL),
(121, NULL, 'CLEVERSON ANTONIO LOPES', NULL, NULL, NULL, NULL, '', 'ENG. TESTE', NULL),
(122, NULL, 'Mauricio Trajano', NULL, NULL, NULL, NULL, '', 'Porto Seco SM', NULL),
(123, NULL, 'Glenda Felix', NULL, NULL, NULL, NULL, '', 'Porto Seco SM', NULL),
(124, NULL, 'Amanda Casemiro', NULL, NULL, NULL, NULL, '', 'Porto Seco SM', NULL),
(125, NULL, 'Felipe Monteiro', NULL, NULL, NULL, NULL, '', 'Porto Seco SM', NULL),
(126, NULL, 'Luciele Freire Trindade', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(127, 50, 'Ana Cláudia Mangi', NULL, NULL, '', 'ler criar atualização', 'ana.mangi@serdia.com.br', 'ENG. DA QUALIDADE', NULL),
(128, NULL, 'Gabriel Luis Cajeux ', NULL, NULL, NULL, NULL, 'engenharia@serdia.com.br', 'ENG PRODUTO', NULL),
(129, NULL, 'Fabiano Moura', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(130, NULL, 'Jackson Barbosa', NULL, NULL, NULL, NULL, '', 'PCP', NULL),
(131, NULL, 'Rosangela Guedes de Souza', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(132, NULL, 'Maísa Sally Dorta Alves Gonçalves', NULL, NULL, NULL, NULL, '', 'COMPRAS', NULL),
(133, NULL, 'Stefany Barbosa Souza', NULL, NULL, NULL, NULL, 'recepcao@serdia.com.br', 'FINANCEIRO', NULL),
(134, NULL, 'Luara Cezar', NULL, NULL, NULL, NULL, '', 'COMERCIAL', NULL),
(135, NULL, 'Leandro Barbosa Silva', NULL, NULL, NULL, NULL, 'leandro.silva@serdia.com.br', 'ALMOXARIFADO', NULL),
(136, NULL, 'GUILHERME QUINTILIANO DOS SANTOS', NULL, NULL, NULL, NULL, '', 'PCM', NULL),
(137, 55, 'Marcos Paulo Rodrigues Santos', NULL, NULL, NULL, 'ler criar atualização', 'marcos.santos@serdia.com.br', 'TI', '2023-01-13 10:08:24'),
(138, NULL, 'CAROLINE DOS SANTOS', NULL, NULL, NULL, NULL, '', 'ALMOXARIFADO', NULL),
(139, NULL, 'Cleiton Natalino Feitosa', NULL, NULL, NULL, NULL, 'almox@serdia.com.br', 'ALMOXARIFADO', NULL),
(140, NULL, 'KETELLIN KELLEN DA SILVA', NULL, NULL, NULL, NULL, ' ketellin.silva@serdia.com.br', 'COMPRAS', NULL),
(141, NULL, 'Marcelo Carlesso', NULL, NULL, NULL, NULL, 'marcelo.carlesso@serdia.com.br', 'PRODUÇÃO PTH', NULL),
(142, NULL, 'Janaina de Oliveira de Nascimento', NULL, NULL, NULL, NULL, 'janaina.rossi@serdia.com.br', 'PRODUÇÃO PTH', NULL),
(143, NULL, 'Caroline dos santos Pimentel', NULL, NULL, NULL, NULL, '', 'ALMOXARIFADO', NULL),
(144, NULL, 'Flavio Cristiano', NULL, NULL, NULL, NULL, '', 'ALMOXARIFADO', NULL),
(145, NULL, 'Patricia Luciene Pereira', NULL, NULL, NULL, NULL, '', 'EXPEDIÇÃO', NULL),
(146, NULL, 'Amanda Soares', NULL, NULL, NULL, NULL, 'amanda.soares@serdia.com.br', 'COMERCIAL', NULL),
(147, NULL, 'Grazieli Daiana Mazutti', NULL, NULL, NULL, NULL, 'inspecao@serdia.com.br', 'ENG. DA QUALIDADE', NULL),
(148, NULL, 'Laura Vitoria Simplicio', NULL, NULL, NULL, NULL, '', 'RECEPÇÃO', NULL),
(149, NULL, 'Marlene Pereira da Silva', NULL, NULL, NULL, NULL, '', 'ZELADORIA', NULL),
(150, NULL, 'Stefany Camargo Marques', NULL, NULL, NULL, NULL, '', 'ALMOXARIFADO', NULL),
(151, NULL, 'Aline Mendes de Brito', NULL, NULL, NULL, NULL, '', 'ENG. TESTE', NULL),
(152, NULL, 'Maria de Fatima Santana', NULL, NULL, NULL, NULL, '', 'SMD', NULL),
(153, NULL, 'Deuni Rosa Forte', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(154, NULL, 'Erica Cristina Fialho de Salle', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(155, NULL, 'Tania Mara de Camargo', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(156, NULL, 'Eder Massaroto', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(157, NULL, 'Artur Leandro de Santana Junior', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(158, NULL, 'Rony Cesar de Souza', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(159, NULL, 'MARIA ESLI BLEICHWHL', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(160, NULL, 'Aline Mendes', NULL, NULL, NULL, NULL, '', 'ENG. TESTE', NULL),
(161, NULL, 'Ana Lima', NULL, NULL, NULL, NULL, '', 'FATURAMENTO', NULL),
(162, NULL, 'Bruna Aparecida Scopetz', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(163, NULL, 'Marilene Alves Batista', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PB', NULL),
(164, NULL, 'Rafaela Nolasco', NULL, NULL, NULL, NULL, '', 'RH', NULL),
(165, NULL, 'ANDRÉ HENRIQUE GONÇALVES DE PAULA', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(166, NULL, 'ADRIANA FERREIRA', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(167, NULL, 'DANIEL BORGONOVO', NULL, NULL, NULL, NULL, '', 'PCP', NULL),
(168, NULL, 'DAYANE RAQUEL DE CASTRO', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(169, NULL, 'BRENO DOS SANTOS AYRES', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(170, NULL, 'BRYAN MARTINY', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(171, NULL, 'LETICIA RODRIGUES VASCONCELOS GOMES', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(172, NULL, 'MILENA NAYARA TAVARES', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(173, NULL, 'SANDRA PARECIDA MIRANDA MARCO', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(174, NULL, 'KAREN ALLANA ROCHA DE LIMA', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(175, NULL, 'MARCIA CZELUSNIAKI DELGADO', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(176, NULL, 'MARILENE CAMARGOS DE JESUS', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(177, NULL, 'EBELY DA CONCEIÇÃO VIEIRA', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(178, NULL, 'Tais Oliveira Paludo', NULL, NULL, NULL, NULL, '', 'COMERCIAL', NULL),
(179, NULL, 'Luiz Gustavo', NULL, NULL, NULL, NULL, '', 'COMERCIAL', NULL),
(180, NULL, 'Anna Paulla da Rocha', NULL, NULL, NULL, NULL, 'annapaulla.rocha@serdia.com.br', 'ENG PROCESSO', NULL),
(181, NULL, 'Angélica Cordeiro', NULL, NULL, NULL, NULL, 'angelica.cordeiro@serdia.com.br', 'COMPRAS', NULL),
(182, NULL, 'Gizelia Alvez Leandro', NULL, NULL, NULL, NULL, 'gizelia.leandro@serdia.com.br', 'ENG PROCESSO', NULL),
(183, NULL, 'Matildes de Oliveira', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(184, NULL, 'CRISTIAN AUGUSTO DE MACEDO E LIMA', NULL, NULL, NULL, NULL, 'cristian.lima@serdia.com.br', 'ENG PROCESSO', NULL),
(185, NULL, 'DAPHILIN PEREIRA RODRIGUES', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(186, NULL, 'Beatriz Silva', NULL, NULL, NULL, NULL, ' beatriz.silva@serdia.com.br', 'COMPRAS', NULL),
(187, NULL, 'Renan Melo', NULL, NULL, NULL, NULL, 'almox.pb@serdia.com.br', 'ALMOX PB', NULL),
(188, NULL, 'Joana Camila Carneiro dos Santos Binsfeld', NULL, NULL, NULL, NULL, 'qualidade.pb@serdia.com.br', 'QUALIDADE PB', NULL),
(189, NULL, 'Claudia Gastl', NULL, NULL, NULL, NULL, 'producao.processopb@serdia.com.br', 'PRODUÇÃO PB', NULL),
(190, NULL, 'Julio Cesar Alvarenga Jardim', NULL, NULL, NULL, NULL, 'teste.pb@serdia.com.br', 'T.I. PATO BRANCO', NULL),
(191, 36, 'Wajdi Ben Helal', NULL, NULL, 'admin', 'ler criar atualização', 'ben.helal@serdia.com.br', 'TI', '2023-01-13 09:05:04'),
(192, NULL, 'MICHELLE GODAR', NULL, NULL, NULL, NULL, '', 'PORTARIA', NULL),
(193, NULL, 'LEONARDO VICENTE DOS SANTOS ALMEIDA', NULL, NULL, NULL, NULL, '', 'ALMOXARIFADO', NULL),
(194, NULL, 'ALCIONE CARDOSO DE OLIVEIRA', NULL, NULL, NULL, NULL, '', 'ENG. TESTE', NULL),
(195, NULL, 'LETICIA MAIA', NULL, NULL, NULL, NULL, '', 'ENG. TESTE', NULL),
(196, NULL, 'TAMIRIS JENNIFER FIGUEIREDO MATHIAS', NULL, NULL, NULL, NULL, '', 'FINANCEIRO', NULL),
(197, NULL, 'VALDECIR PADOVANI JUNIOR', NULL, NULL, NULL, NULL, 'valdecir.padovani@serdia.com.br', 'P&D', NULL),
(198, NULL, 'LARISSA TERESA MICHELOTI DE SOUZA', NULL, NULL, NULL, NULL, 'larissa.micheloti@serdia.com.br', 'COMPRAS', NULL),
(199, NULL, 'GUILHERME DO VAL MOREIRA', NULL, NULL, NULL, NULL, '', 'ENG PROCESSO', NULL),
(200, NULL, 'PIETRA MARQUES LIMA', NULL, NULL, NULL, NULL, 'pietra.lima@serdia.com.br', 'COMPRAS', NULL),
(201, NULL, 'ERALDO THELMO GRESS', NULL, NULL, NULL, NULL, '', 'ENG PROCESSO', NULL),
(202, NULL, 'AMANDA VIEIRA CORREIA', NULL, NULL, NULL, NULL, '', 'OPERAÇÕES (SUPORTE)', NULL),
(203, NULL, 'JOSIANE SIQUEIRA', NULL, NULL, NULL, NULL, '', 'OPERAÇÕES (SUPORTE)', NULL),
(204, NULL, 'Ana Beatriz Costa Nunes', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PB', NULL),
(205, NULL, 'MARILENE WOLEK', NULL, NULL, NULL, NULL, '', 'OPERAÇÕES (SUPORTE)', NULL),
(206, NULL, 'MARILUSI BECKER', NULL, NULL, NULL, NULL, '', 'OPERAÇÕES (SUPORTE)', NULL),
(207, NULL, 'VANESSA CARVALHO DA ROSA DE OLIVEIRA', NULL, NULL, NULL, NULL, '', 'OPERAÇÕES (SUPORTE)', NULL),
(208, NULL, 'Eucleia Kwiatkowski', NULL, NULL, NULL, NULL, 'eucleia.kwiatkowski@serdia.com.br', 'COMPRAS', NULL),
(209, NULL, 'ANDRE LUIZ IGNACIO SANTOS', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(210, NULL, 'ELAINE CRISTINA DE OLIVEIRA BROCANELLI', NULL, NULL, NULL, NULL, '', 'ENG PROCESSO', NULL),
(211, NULL, 'ETIENE CRISTINE RIBEIRO', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(212, NULL, 'EDINEIA FERREIRA TEIXEIRA', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(213, NULL, 'EVELYN CELESTINO TEIXEIRA', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(214, NULL, 'FLAVIA FERREIRA ANTONIO', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(215, NULL, 'RAQUEL RODRIGUES CRUZ', NULL, NULL, NULL, NULL, '', 'P&D', NULL),
(216, NULL, 'RODRIGO DOS SANTOS LACERDA', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(217, NULL, 'CLEONICE DO CARMO DE OLIVEIRA', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(218, NULL, 'SANTA LEONARDA RODRIGUES FERNANDES', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(219, NULL, 'BRUNA DE MATTOS VIEIRA', NULL, NULL, NULL, NULL, '', 'ENG. TESTE', NULL),
(220, NULL, 'JULIANA LIMA DA SILVA', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(221, NULL, 'MATHEUS BARBISAN GRESS', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(222, NULL, 'CARLA CARDOSO DE OLIVEIRA', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(223, NULL, 'MURILO PEIXER DOS SANTOS', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(224, NULL, 'TAINARA RODRIGUES SANTOS', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(225, NULL, 'MEIRE ROSE SOUZA DO NASCIMENTO', NULL, NULL, NULL, NULL, '', 'ZELADORIA', NULL),
(226, NULL, 'PAULO LUCIO PEGORARO', NULL, NULL, NULL, NULL, 'paulo.pegoraro@serdia.com.br', 'COMERCIAL', NULL),
(227, NULL, 'Jhonatan Marques', NULL, NULL, NULL, NULL, '', 'COMPRAS', NULL),
(228, NULL, 'Willian Costa', NULL, NULL, NULL, NULL, 'willian.costa@serdia.com.br', 'ENG PROCESSO', NULL),
(229, NULL, 'Maria Martinha', NULL, NULL, NULL, NULL, 'maria.silva@serdia.com.br', 'ENG. DA QUALIDADE', NULL),
(230, NULL, 'MAGNUM OLDONI', NULL, NULL, NULL, NULL, '', 'OPERAÇÕES (SUPORTE)', NULL),
(231, NULL, 'MARISTELA BIAZUSSI', NULL, NULL, NULL, NULL, '', 'OPERAÇÕES (SUPORTE)', NULL),
(232, NULL, 'GUILHERME PINHEIRO ALVARENGA', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(233, NULL, 'ISABEL FERNANDA DELFINO', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(234, NULL, 'Natan Oliveira', NULL, NULL, NULL, NULL, 'almox1.pb@serdia.com.br', 'ALMOXARIFADO', NULL),
(235, NULL, 'Lucas Melo da Costa', NULL, NULL, NULL, 'ler criar atualização', 'lucas.costa@serdia.com.br', 'TI', '2023-01-11 13:23:53'),
(236, NULL, 'Anouar ', NULL, NULL, NULL, NULL, '', 'ENG PROCESSO', NULL),
(237, NULL, 'ROSANGELA DE SOUZA TEIXEIRA', NULL, NULL, NULL, NULL, '', 'PRODUÇÃO PTH', NULL),
(238, NULL, 'Paulo Valentim', NULL, NULL, NULL, NULL, '', 'COMERCIAL', NULL),
(239, NULL, 'Anderson Alexandre Zocche', NULL, NULL, NULL, NULL, '', 'ENG. TESTE', NULL),
(240, NULL, 'ANDREIA LUCIA  DA SILVA', NULL, NULL, NULL, NULL, '', 'OPERAÇÕES (SUPORTE)', NULL),
(241, NULL, 'DAYANE XAVIER DA SILVA GRACIOLLI ', NULL, NULL, NULL, NULL, '', 'OPERAÇÕES (SUPORTE)', NULL),
(242, NULL, 'FELIPE AUGUSTO NOVAES', NULL, NULL, NULL, NULL, '', 'OPERAÇÕES (SUPORTE)', NULL),
(243, NULL, 'GRACIELI DE OLIVEIRA', NULL, NULL, NULL, NULL, '', 'OPERAÇÕES (SUPORTE)', NULL),
(244, NULL, 'GRAZIELI SIQUEIRA', NULL, NULL, NULL, NULL, '', 'OPERAÇÕES (SUPORTE)', NULL),
(245, NULL, 'LINDAMIR BATISTA', NULL, NULL, NULL, NULL, '', 'OPERAÇÕES (SUPORTE)', NULL),
(246, NULL, 'VIVIANE APARECIDA LOUREIRO SAMPAIO', NULL, NULL, NULL, NULL, '', 'OPERAÇÕES (SUPORTE)', NULL),
(247, NULL, 'MACLEISEN FABIOLA FOLLE', NULL, NULL, NULL, NULL, '', 'OPERAÇÕES (SUPORTE)', NULL),
(248, NULL, 'LUCIANE DE ALMEIDA DOS SANTOS', NULL, NULL, NULL, NULL, '', 'OPERAÇÕES (SUPORTE)', NULL),
(249, NULL, 'FELIPE DIAS DA SILVA', NULL, NULL, NULL, NULL, '', 'OPERAÇÕES (SUPORTE)', NULL),
(250, NULL, 'Erivelton Batista', NULL, NULL, NULL, NULL, 'erivelton.batista@serdia.com.br', 'ENG PROC. SMT', NULL),
(251, NULL, 'RICARDO VIGANÓ', NULL, NULL, NULL, NULL, '', 'OPERAÇÕES (SUPORTE)', NULL),
(252, NULL, 'Vanessa Colodel', NULL, NULL, NULL, NULL, 'vanessa.colodel@serdia.com.br', 'COMERCIAL', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `process`
--

CREATE TABLE `process` (
  `id` int(11) NOT NULL,
  `mudancas_id` int(11) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `process`
--

INSERT INTO `process` (`id`, `mudancas_id`, `status`) VALUES
(34, 41, 'created'),
(35, 42, 'created'),
(36, 43, 'created'),
(37, 44, 'created'),
(38, 45, 'created'),
(39, 46, 'created');

-- --------------------------------------------------------

--
-- Table structure for table `process_departemant`
--
-- Error reading structure for table controlmud.process_departemant: #1932 - Table &#039;controlmud.process_departemant&#039; doesn&#039;t exist in engine
-- Error reading data for table controlmud.process_departemant: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `controlmud`.`process_departemant`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `requestper`
--

CREATE TABLE `requestper` (
  `id` int(11) NOT NULL,
  `person_id` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `justification` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approves` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `requestper`
--

INSERT INTO `requestper` (`id`, `person_id`, `date`, `justification`, `approves`) VALUES
(1, 191, '2023-01-11 09:15:01', 'test', 'yes'),
(2, 235, '2023-01-11 13:23:56', 'test', 'yes'),
(3, 137, '2023-01-12 08:13:23', 'asdad', 'yes'),
(4, 78, '2023-01-12 08:15:14', 'aaa', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `sector`
--

CREATE TABLE `sector` (
  `id` int(11) NOT NULL,
  `departemant_id` int(11) DEFAULT NULL,
  `coordinator_id` int(11) DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sector`
--

INSERT INTO `sector` (`id`, `departemant_id`, `coordinator_id`, `manager_id`, `name`) VALUES
(35, 26, 66, 66, '001 – DIRETORIA MATRIZ'),
(36, 5, 226, 191, '002 – COMERCIAL MATRIZ'),
(37, 4, 70, 15, '003 – ENGº DE PRODUTO MATRIZ'),
(38, 7, 9, 250, '004 – COMPRAS MATRIZ (COMPRAS/ IMPORTAÇÃO/ORÇAMENTO)'),
(39, 10, 62, 15, '005 – ENG DE PROCESSO MATRIZ (SMT)'),
(40, 9, 62, 15, '006 – ENG DE PROCESSO MATRIZ (PTH)'),
(41, 3, 4, 250, '007 – PCPM MATRIZ'),
(42, 17, 35, 35, '008 – FINANCEIRO MATRIZ'),
(43, 15, 135, 250, '009 – RECEBIMENTO MATRIZ'),
(44, 22, 48, 10, '010 – GARANTIA DA QUALIDADE MATRIZ (INSP. RECEB. MAT.)'),
(45, 15, 135, 250, '011 – ALMOXARIFADO MATRIZ'),
(46, 22, 10, 10, '012 – GESTÃO DA QUALIDADE MATRIZ (SGQ)'),
(47, 19, 42, 15, '013 – PRODUÇÃO SMT MATRIZ'),
(48, 19, 42, 15, '014 – PRODUÇÃO THT MATRIZ'),
(49, 11, 48, 10, '015 – DEBUG MATRIZ'),
(50, 22, 10, 87, '016 – GARANTIA DA QUALIDADE MATRIZ (CONTR. QUAL. DO PROD.)'),
(51, 22, 48, 10, '017 – GARANTIA DA QUALIDADE MATRIZ (CALIBRAÇÃO)'),
(52, 6, 31, 250, '018 – EXPEDIÇÃO MATRIZ'),
(53, 24, 48, 10, '019 – ENGº DE PROJETOS MATRIZ'),
(54, 22, 10, 10, '020 – RMA MATRIZ'),
(55, 1, 86, 78, '021 – TI MATRIZ (INFRAESTRUTURA E REDE)'),
(56, 13, 23, 15, '022 – ENGº DE MANUTENÇÃO MATRIZ (INDUSTRIAL / PREDIAL / INDUSTRIAL SMT)'),
(57, 25, 55, 55, '023 – RECURSOS HUMANOS MATRIZ MATRIZ (RH / SSMA)'),
(58, 11, 48, 10, '024 – ENGº DE TESTES MATRIZ ( ITS / SUPORTE TÉCNICO JIGAS / TREINAMENTOS)'),
(59, 33, 73, 10, '025 – GARANTIA DA QUALIDADE FILIAL (CONTR. QUAL. DO PROD.)'),
(60, 33, 73, 10, '026 – RMA FILIAL'),
(61, 11, 73, 250, '027 – PCP FILIAL'),
(62, 15, 73, 250, '028 – RECEBIMENTO FILIAL (ALMOX. RECEB.)'),
(63, 9, 73, 15, '029 – ENGº DE PROCESSOS FILIAL (THT)'),
(64, 27, 73, 15, '030 – PRODUÇÃO THT FILIAL'),
(65, 11, 73, 10, '031 – DEBUG FILIAL'),
(66, 32, 73, 250, '032 – EXPEDIÇÃO FILIAL'),
(67, 13, 73, 15, '033 – ENGº DE MANUTENÇÃO FILIAL (INDUSTRIAL)'),
(68, 12, 73, 78, '034 – TI FILIAL (INFRAESTRUTURA E REDE)'),
(69, 25, 73, 55, '035 _ RH FILIAL'),
(70, 22, 48, 10, '036 _ SAC MATRIZ'),
(75, NULL, NULL, NULL, '020 – RMA / SAC MATRIZ (ASTEC)  '),
(76, NULL, 73, NULL, '026 – RMA / SAC FILIAL (ASTEC)  ');

-- --------------------------------------------------------

--
-- Table structure for table `sector_process`
--

CREATE TABLE `sector_process` (
  `id` int(11) NOT NULL,
  `process_id` int(11) DEFAULT NULL,
  `sector_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `app_sector_man` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sector_process`
--

INSERT INTO `sector_process` (`id`, `process_id`, `sector_id`, `person_id`, `comment`, `app_sector_man`) VALUES
(36, 34, 36, 191, 'dxqzxd', 1),
(37, 35, 55, 78, NULL, NULL),
(38, 36, 36, 191, 'xdzqxd', 1),
(39, 37, 55, 78, 'sadsad', 1),
(40, 38, 36, 191, 'test', 1),
(41, 39, 36, 191, 'test', 1);

-- --------------------------------------------------------

--
-- Table structure for table `theme_admin`
--

CREATE TABLE `theme_admin` (
  `id` int(11) NOT NULL,
  `color_bar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warrning_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valid_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `theme_manager`
--

CREATE TABLE `theme_manager` (
  `id` int(11) NOT NULL,
  `color_bar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warrning_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valid_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `theme_user`
--

CREATE TABLE `theme_user` (
  `id` int(11) NOT NULL,
  `color_bar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warrning_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valid_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `config_email`
--
ALTER TABLE `config_email`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departemant`
--
ALTER TABLE `departemant`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departemant_mudancass`
--
ALTER TABLE `departemant_mudancass`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_DFF9C4BEEC585363` (`mudancas_id`),
  ADD KEY `IDX_DFF9C4BE5768A208` (`departemant_id`);

--
-- Indexes for table `departemant_process`
--
ALTER TABLE `departemant_process`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_3434BFCB7EC2F574` (`process_id`),
  ADD KEY `IDX_3434BFCB5768A208` (`departemant_id`),
  ADD KEY `IDX_3434BFCB217BBB47` (`person_id`);

--
-- Indexes for table `email`
--
ALTER TABLE `email`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_E7927C74C3852542` (`send_by_id`),
  ADD KEY `IDX_E7927C7459574F23` (`send_to_id`),
  ADD KEY `IDX_E7927C74EC585363` (`mudancas_id`);

--
-- Indexes for table `icons`
--
ALTER TABLE `icons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `manager`
--
ALTER TABLE `manager`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_FA2425B9217BBB47` (`person_id`);

--
-- Indexes for table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Indexes for table `mudancas`
--
ALTER TABLE `mudancas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_8329091717542AC5` (`add_by_id`),
  ADD KEY `IDX_832909175C226A17` (`manger_mudancas_id`),
  ADD KEY `IDX_83290917F13FCFC9` (`area_resp_id`),
  ADD KEY `IDX_832909175391D32A` (`manager_user_add_id`);

--
-- Indexes for table `mudancas_sector`
--
ALTER TABLE `mudancas_sector`
  ADD PRIMARY KEY (`mudancas_id`,`sector_id`),
  ADD KEY `IDX_4DB9943EC585363` (`mudancas_id`),
  ADD KEY `IDX_4DB9943DE95C867` (`sector_id`);

--
-- Indexes for table `person`
--
ALTER TABLE `person`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_34DCD17667048801` (`function_id`);

--
-- Indexes for table `process`
--
ALTER TABLE `process`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_861D1896EC585363` (`mudancas_id`);

--
-- Indexes for table `requestper`
--
ALTER TABLE `requestper`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_290E4325217BBB47` (`person_id`);

--
-- Indexes for table `sector`
--
ALTER TABLE `sector`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_4BA3D9E85768A208` (`departemant_id`),
  ADD KEY `IDX_4BA3D9E8E7877946` (`coordinator_id`),
  ADD KEY `IDX_4BA3D9E8783E3463` (`manager_id`);

--
-- Indexes for table `sector_process`
--
ALTER TABLE `sector_process`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_2C908BB17EC2F574` (`process_id`),
  ADD KEY `IDX_2C908BB1DE95C867` (`sector_id`),
  ADD KEY `IDX_2C908BB1217BBB47` (`person_id`);

--
-- Indexes for table `theme_admin`
--
ALTER TABLE `theme_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `theme_manager`
--
ALTER TABLE `theme_manager`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `theme_user`
--
ALTER TABLE `theme_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `config_email`
--
ALTER TABLE `config_email`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `departemant`
--
ALTER TABLE `departemant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `departemant_mudancass`
--
ALTER TABLE `departemant_mudancass`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=663;

--
-- AUTO_INCREMENT for table `departemant_process`
--
ALTER TABLE `departemant_process`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=225;

--
-- AUTO_INCREMENT for table `email`
--
ALTER TABLE `email`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=210;

--
-- AUTO_INCREMENT for table `icons`
--
ALTER TABLE `icons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `manager`
--
ALTER TABLE `manager`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mudancas`
--
ALTER TABLE `mudancas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `person`
--
ALTER TABLE `person`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=253;

--
-- AUTO_INCREMENT for table `process`
--
ALTER TABLE `process`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `requestper`
--
ALTER TABLE `requestper`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sector`
--
ALTER TABLE `sector`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `sector_process`
--
ALTER TABLE `sector_process`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `theme_admin`
--
ALTER TABLE `theme_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `theme_manager`
--
ALTER TABLE `theme_manager`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `theme_user`
--
ALTER TABLE `theme_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `departemant_mudancass`
--
ALTER TABLE `departemant_mudancass`
  ADD CONSTRAINT `FK_DFF9C4BE5768A208` FOREIGN KEY (`departemant_id`) REFERENCES `departemant` (`id`),
  ADD CONSTRAINT `FK_DFF9C4BEEC585363` FOREIGN KEY (`mudancas_id`) REFERENCES `mudancas` (`id`);

--
-- Constraints for table `departemant_process`
--
ALTER TABLE `departemant_process`
  ADD CONSTRAINT `FK_3434BFCB217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `FK_3434BFCB5768A208` FOREIGN KEY (`departemant_id`) REFERENCES `departemant` (`id`),
  ADD CONSTRAINT `FK_3434BFCB7EC2F574` FOREIGN KEY (`process_id`) REFERENCES `process` (`id`);

--
-- Constraints for table `email`
--
ALTER TABLE `email`
  ADD CONSTRAINT `FK_E7927C7459574F23` FOREIGN KEY (`send_to_id`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `FK_E7927C74C3852542` FOREIGN KEY (`send_by_id`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `FK_E7927C74EC585363` FOREIGN KEY (`mudancas_id`) REFERENCES `mudancas` (`id`);

--
-- Constraints for table `manager`
--
ALTER TABLE `manager`
  ADD CONSTRAINT `FK_FA2425B9217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`);

--
-- Constraints for table `mudancas`
--
ALTER TABLE `mudancas`
  ADD CONSTRAINT `FK_8329091717542AC5` FOREIGN KEY (`add_by_id`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `FK_832909175391D32A` FOREIGN KEY (`manager_user_add_id`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `FK_832909175C226A17` FOREIGN KEY (`manger_mudancas_id`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `FK_83290917F13FCFC9` FOREIGN KEY (`area_resp_id`) REFERENCES `sector` (`id`);

--
-- Constraints for table `mudancas_sector`
--
ALTER TABLE `mudancas_sector`
  ADD CONSTRAINT `FK_4DB9943DE95C867` FOREIGN KEY (`sector_id`) REFERENCES `sector` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_4DB9943EC585363` FOREIGN KEY (`mudancas_id`) REFERENCES `mudancas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `person`
--
ALTER TABLE `person`
  ADD CONSTRAINT `FK_34DCD17667048801` FOREIGN KEY (`function_id`) REFERENCES `sector` (`id`);

--
-- Constraints for table `process`
--
ALTER TABLE `process`
  ADD CONSTRAINT `FK_861D1896EC585363` FOREIGN KEY (`mudancas_id`) REFERENCES `mudancas` (`id`);

--
-- Constraints for table `requestper`
--
ALTER TABLE `requestper`
  ADD CONSTRAINT `FK_290E4325217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`);

--
-- Constraints for table `sector`
--
ALTER TABLE `sector`
  ADD CONSTRAINT `FK_4BA3D9E85768A208` FOREIGN KEY (`departemant_id`) REFERENCES `departemant` (`id`),
  ADD CONSTRAINT `FK_4BA3D9E8783E3463` FOREIGN KEY (`manager_id`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `FK_4BA3D9E8E7877946` FOREIGN KEY (`coordinator_id`) REFERENCES `person` (`id`);

--
-- Constraints for table `sector_process`
--
ALTER TABLE `sector_process`
  ADD CONSTRAINT `FK_2C908BB1217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `FK_2C908BB17EC2F574` FOREIGN KEY (`process_id`) REFERENCES `process` (`id`),
  ADD CONSTRAINT `FK_2C908BB1DE95C867` FOREIGN KEY (`sector_id`) REFERENCES `sector` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
