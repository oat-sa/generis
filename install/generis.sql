-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- G�n�r� le : Lun 09 Novembre 2009 � 18:51
-- Version du serveur: 5.1.36
-- Version de PHP: 5.2.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de donn�es: `generis`
--

-- --------------------------------------------------------

--
-- Structure de la table `extensions`
--
-- Cr�ation: Lun 09 Novembre 2009 � 17:26
-- Derni�re modification: Lun 09 Novembre 2009 � 18:19
--

CREATE TABLE IF NOT EXISTS `extensions` (
  `id` varchar(25) NOT NULL,
  `name` varchar(150) NOT NULL,
  `version` varchar(4) NOT NULL,
  `loaded` tinyint(1) NOT NULL,
  `loadAtStartUp` tinyint(1) NOT NULL,
  `ghost` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `extensions`
--


-- --------------------------------------------------------

--
-- Structure de la table `grouplocaluser`
--
-- Cr�ation: Mer 04 Novembre 2009 � 15:30
-- Derni�re modification: Mer 04 Novembre 2009 � 15:30
-- Derni�re v�rification: Mer 04 Novembre 2009 � 15:30
--

CREATE TABLE IF NOT EXISTS `grouplocaluser` (
  `Name` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`Name`),
  KEY `Name` (`Name`),
  KEY `Name_2` (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `grouplocaluser`
--

INSERT INTO `grouplocaluser` (`Name`) VALUES
('admin');

-- --------------------------------------------------------

--
-- Structure de la table `log_actions`
--
-- Cr�ation: Mer 04 Novembre 2009 � 15:30
-- Derni�re modification: Mer 04 Novembre 2009 � 15:30
-- Derni�re v�rification: Mer 04 Novembre 2009 � 15:30
--

CREATE TABLE IF NOT EXISTS `log_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `model_id` int(11) NOT NULL DEFAULT '0',
  `user` varchar(255) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `descr_id` int(11) NOT NULL DEFAULT '0',
  `subject` varchar(255) DEFAULT NULL,
  `details` longblob,
  PRIMARY KEY (`id`),
  KEY `idx_logactions_modelid` (`model_id`),
  KEY `idx_logactions_parentid` (`parent_id`),
  KEY `idx_logactions_user` (`user`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Contenu de la table `log_actions`
--

INSERT INTO `log_actions` (`id`, `parent_id`, `model_id`, `user`, `date`, `descr_id`, `subject`, `details`) VALUES
(1, NULL, 2, 'Admin', '2005-07-12 10:38:01', 1, '', ''),
(2, NULL, 2, 'Admin', '2005-07-12 10:38:01', 1, '', ''),
(3, NULL, 2, 'Admin', '2005-07-12 10:38:02', 1, '', ''),
(4, NULL, 2, 'Admin', '2005-07-12 10:38:02', 1, '', ''),
(5, NULL, 2, 'Admin', '2005-07-12 10:38:02', 1, '', ''),
(6, NULL, 2, 'Admin', '2005-07-12 10:38:02', 1, '', ''),
(7, NULL, 2, 'Admin', '2005-07-12 10:38:02', 1, '', ''),
(8, NULL, 2, 'Admin', '2005-07-12 10:38:02', 1, '', ''),
(9, NULL, 2, '', '2005-07-13 09:36:48', 1, '', ''),
(10, NULL, 2, '', '2005-07-13 09:36:49', 1, '', ''),
(11, NULL, 2, '', '2005-07-13 09:52:48', 1, '', ''),
(12, NULL, 2, '', '2005-07-13 09:52:48', 1, '', ''),
(13, NULL, 2, '', '2005-07-13 15:05:44', 1, '', ''),
(14, NULL, 2, '', '2005-07-13 15:05:44', 1, '', ''),
(15, NULL, 2, '', '2005-07-13 15:05:44', 1, '', ''),
(16, NULL, 2, '', '2005-07-13 15:05:44', 1, '', '');

-- --------------------------------------------------------

--
-- Structure de la table `log_action_descr`
--
-- Cr�ation: Mer 04 Novembre 2009 � 15:30
-- Derni�re modification: Mer 04 Novembre 2009 � 15:30
-- Derni�re v�rification: Mer 04 Novembre 2009 � 15:30
--

CREATE TABLE IF NOT EXISTS `log_action_descr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_logactiondescr_description` (`description`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Contenu de la table `log_action_descr`
--

INSERT INTO `log_action_descr` (`id`, `description`) VALUES
(1, 'Statement added');

-- --------------------------------------------------------

--
-- Structure de la table `models`
--
-- Cr�ation: Mer 04 Novembre 2009 � 15:30
-- Derni�re modification: Lun 09 Novembre 2009 � 18:19
-- Derni�re v�rification: Mer 04 Novembre 2009 � 15:30
--

CREATE TABLE IF NOT EXISTS `models` (
  `modelID` int(11) NOT NULL AUTO_INCREMENT,
  `modelURI` varchar(255) NOT NULL DEFAULT '',
  `baseURI` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`modelID`),
  KEY `idx_models_modelURI` (`modelURI`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Contenu de la table `models`
--

INSERT INTO `models` (`modelID`, `modelURI`, `baseURI`) VALUES
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#'),
(5, 'http://www.w3.org/2000/01/rdf-schema#', 'http://www.w3.org/2000/01/rdf-schema#'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#', 'http://www.tao.lu/Ontologies/TAO.rdf#'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#', 'http://www.tao.lu/Ontologies/generis.rdf#');

-- --------------------------------------------------------

--
-- Structure de la table `settings`
--
-- Cr�ation: Lun 09 Novembre 2009 � 17:55
-- Derni�re modification: Lun 09 Novembre 2009 � 17:55
-- Derni�re v�rification: Lun 09 Novembre 2009 � 17:55
--

CREATE TABLE IF NOT EXISTS `settings` (
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `settings`
--


-- --------------------------------------------------------

--
-- Structure de la table `statements`
--
-- Cr�ation: Mer 04 Novembre 2009 � 15:30
-- Derni�re modification: Lun 09 Novembre 2009 � 18:19
-- Derni�re v�rification: Mer 04 Novembre 2009 � 15:30
--

CREATE TABLE IF NOT EXISTS `statements` (
  `modelID` int(11) NOT NULL DEFAULT '0',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `predicate` varchar(255) NOT NULL DEFAULT '',
  `object` longblob,
  `l_language` varchar(255) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` varchar(255) DEFAULT NULL,
  `stread` varchar(255) NOT NULL DEFAULT 'yyy[]',
  `stedit` varchar(255) NOT NULL DEFAULT 'yy-[]',
  `stdelete` varchar(255) NOT NULL DEFAULT 'y--[Administrators]',
  `epoch` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_statements_modelID` (`modelID`),
  KEY `idx_statements_subject` (`subject`),
  KEY `idx_statements_predicate` (`predicate`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Contenu de la table `statements`
--

INSERT INTO `statements` (`modelID`, `subject`, `predicate`, `object`, `l_language`, `id`, `author`, `stread`, `stedit`, `stdelete`, `epoch`) VALUES
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7384, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.w3.org/2000/01/rdf-schema#label', 0x576964676574, 'en', 7385, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x5370656369666965732074686520666f726d20696e7465726661636520776964676574, 'en', 7386, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7387, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e72646623576964676574436c617373, '', 7388, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e72646623436f6d626f426f78, '', 7389, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7390, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint', 'http://www.w3.org/2000/01/rdf-schema#label', 0x5769646765742052616e676520436f6e73747261696e74, 'en', 7391, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x546869732070726f706572747920636f6e73747261696e73207769646765747320746f206365727461696e207479706573206f662072616e676573, 'en', 7392, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e72646623576964676574436c617373, '', 7393, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662372616e6765436f6e73747261696e745479706573, '', 7394, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e72646623436f6d626f426f78, '', 7395, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraintTypes', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7396, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraintTypes', 'http://www.w3.org/2000/01/rdf-schema#label', 0x5479706573206f66205769646765742052616e676520436f6e73747261696e7473, 'en', 7397, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraintTypes', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x54686520636c617373206f662072616e676520636f6e73747261696e7473206170706c696361626c6520746f2077696467657473, 'en', 7398, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint-Resource', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662372616e6765436f6e73747261696e745479706573, '', 7399, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint-Resource', 'http://www.w3.org/2000/01/rdf-schema#label', 0x7265736f7572636573, 'en', 7400, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint-Resource', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x5265736f75726365732061726520616e79206465736372697074696f6e2c206f7220616e79206f626a656374206964656e74696669656420627920616e20555249, 'en', 7401, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint-Literal', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662372616e6765436f6e73747261696e745479706573, '', 7402, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint-Literal', 'http://www.w3.org/2000/01/rdf-schema#label', 0x6c69746572616c73, 'en', 7403, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint-Literal', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x416e7920737472696e67202852444653207479706465204c69746572616c7329, 'en', 7404, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#WidgetClass', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7405, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#WidgetClass', 'http://www.w3.org/2000/01/rdf-schema#label', 0x57696467657420436c617373, 'en', 7406, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#WidgetClass', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x54686520636c617373206f6620616c6c20706f737369626c652077696467657473, 'en', 7407, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ComboBox', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e72646623576964676574436c617373, '', 7408, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ComboBox', 'http://www.w3.org/2000/01/rdf-schema#label', 0x44726f7020646f776e206d656e75, 'en', 7409, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ComboBox', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x496e2064726f7020646f776e206d656e752c206f6e65206d61792073656c656374203120746f204e206f7074696f6e73, 'en', 7410, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ComboBox', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662372616e6765436f6e73747261696e742d5265736f75726365, '', 7411, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#RadioBox', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e72646623576964676574436c617373, '', 7412, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#RadioBox', 'http://www.w3.org/2000/01/rdf-schema#label', 0x526164696f20627574746f6e, 'en', 7413, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#RadioBox', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x496e20726164696f20626f7865732c206f6e65206d61792073656c6563742065786163746c79206f6e65206f7074696f6e, 'en', 7414, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#RadioBox', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662372616e6765436f6e73747261696e742d5265736f75726365, '', 7415, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#CheckBox', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e72646623576964676574436c617373, '', 7416, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#CheckBox', 'http://www.w3.org/2000/01/rdf-schema#label', 0x436865636b20626f78, 'en', 7417, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#CheckBox', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x496e20636865636b20626f7865732c206f6e65206d61792073656c656374203020746f204e206f7074696f6e73, 'en', 7418, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#CheckBox', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662372616e6765436f6e73747261696e742d5265736f75726365, '', 7419, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ClassTreeView', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e72646623576964676574436c617373, '', 7420, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ClassTreeView', 'http://www.w3.org/2000/01/rdf-schema#label', 0x436c61737320547265652056696577, 'en', 7421, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ClassTreeView', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x5472656520766965772077696467657420646973706c6179732074686520636c6173732074726565207374617274696e672066726f6d206120676976656e20636c617373206c6576656c2e2074686520757365722073656c65637473206120636c617373, 'en', 7422, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ClassTreeView', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662372616e6765436f6e73747261696e742d5265736f75726365, '', 7423, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TreeView', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e72646623576964676574436c617373, '', 7424, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TreeView', 'http://www.w3.org/2000/01/rdf-schema#label', 0x496e7374616e636520547265652056696577, 'en', 7425, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TreeView', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x5472656520766965772077696467657420646973706c6179732074686520636c6173732074726565207374617274696e672066726f6d206120676976656e20636c617373206c6576656c2c2061742065616368206c6576656c2c2074686520696e7374616e6365206f662074686520686967686c69676874656420636c6173732061726520646973706c6179656420666f7220757365722073656c656374696f6e, 'en', 7426, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TreeView', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662372616e6765436f6e73747261696e742d5265736f75726365, '', 7427, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ExpandFormButton', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e72646623576964676574436c617373, '', 7428, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ExpandFormButton', 'http://www.w3.org/2000/01/rdf-schema#label', 0x457870616e6420466f726d, 'en', 7429, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ExpandFormButton', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x4120627574746f6e20746f20657870616e642074686520666f726d206f662070726f70657274696573206f662074686520636c617373207468652074617267657420696e7374616e63652062656c6f6e677320746f, 'en', 7430, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ExpandFormButton', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662372616e6765436f6e73747261696e742d5265736f75726365, '', 7431, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e72646623576964676574436c617373, '', 7432, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox', 'http://www.w3.org/2000/01/rdf-schema#label', 0x41205465787420426f78, 'en', 7433, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x4120706172746963756c6172207465787420626f78, 'en', 7434, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662372616e6765436f6e73747261696e742d4c69746572616c, '', 7435, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textHeight', 0x31, '', 7436, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#HiddenBox', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e72646623576964676574436c617373, '', 7437, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#HiddenBox', 'http://www.w3.org/2000/01/rdf-schema#label', 0x48696464656e20426f78, 'en', 7438, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#HiddenBox', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x436f6e74656e742069732068696464656e, 'en', 7439, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#HiddenBox', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662372616e6765436f6e73747261696e742d4c69746572616c, '', 7440, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#HiddenBox', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textHeight', 0x31, '', 7441, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#HTMLArea', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e72646623576964676574436c617373, '', 7442, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#HTMLArea', 'http://www.w3.org/2000/01/rdf-schema#label', 0x48544d4c41726561, 'en', 7443, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#HTMLArea', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x416e2068746d6c2061726561, 'en', 7444, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#HTMLArea', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662372616e6765436f6e73747261696e742d4c69746572616c, '', 7445, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#HTMLArea', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textHeight', 0x31, '', 7446, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextArea', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e72646623576964676574436c617373, '', 7447, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextArea', 'http://www.w3.org/2000/01/rdf-schema#label', 0x4120546578742041726561, 'en', 7448, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextArea', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x4120706172746963756c617220746578742041726561, 'en', 7449, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextArea', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662372616e6765436f6e73747261696e742d4c69746572616c, '', 7450, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ListBox', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e72646623576964676574436c617373, '', 7451, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ListBox', 'http://www.w3.org/2000/01/rdf-schema#label', 0x4c697374426f78, 'en', 7452, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ListBox', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x4c697374426f78, 'en', 7453, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ListBox', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662372616e6765436f6e73747261696e742d4c69746572616c, '', 7454, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#seq', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e72646623576964676574436c617373, '', 7455, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#seq', 'http://www.w3.org/2000/01/rdf-schema#label', 0x53657175656e6365, 'en', 7456, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#seq', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x53657175656e6365, 'en', 7457, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#seq', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662372616e6765436f6e73747261696e742d4c69746572616c, '', 7458, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textHeight', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7459, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textHeight', 'http://www.w3.org/2000/01/rdf-schema#label', 0x5465787420486569676874, 'en', 7460, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textHeight', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x54686520686569677468206f6620746865207465787420626f782c2065787072657373656420696e206e756d626572206f66206c696e6573, 'en', 7461, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textHeight', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662354657874576964676574436c617373, '', 7462, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textHeight', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123496e7465676572, '', 7463, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textHeight', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f54657874426f78, '', 7464, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/TextBox', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textLength', 0x33, '', 7465, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textLength', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7466, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textLength', 'http://www.w3.org/2000/01/rdf-schema#label', 0x54657874204c656e677468, 'en', 7467, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textLength', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x546865206c656e677468206f6620746865207465787420626f782c2065787072657373656420696e206e756d626572206f662063686172616374657273, 'en', 7468, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textLength', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662354657874576964676574436c617373, '', 7469, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textLength', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123496e7465676572, '', 7470, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textLength', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662354657874426f78, '', 7471, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textLength', 0x33, '', 7472, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e726466235472656556696577, '', 7473, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e726466235472656556696577, '', 7474, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.w3.org/2000/01/rdf-schema#subPropertyOf', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e726466235472656556696577, '', 7475, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e726466235472656556696577, '', 7476, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e726466235472656556696577, '', 7477, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.w3.org/2000/01/rdf-schema#label', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662348544d4c41726561, '', 7478, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#HTMLArea', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textLength', 0x323535, '', 7479, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.w3.org/2000/01/rdf-schema#seeAlso', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662354657874426f78, '', 7480, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textLength', 0x323535, '', 7481, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662354657874426f78, '', 7482, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e726466235465787441726561, '', 7483, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextArea', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textHeight', 0x3130, '', 7484, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextArea', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textLength', 0x323535, '', 7485, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.w3.org/2000/01/rdf-schema#comment', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662348544d4c41726561, '', 7486, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#HTMLArea', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textHeight', 0x3130, '', 7487, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f323030322f30372f6f776c234f6e746f6c6f6779, '', 7488, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'http://purl.org/dc/elements/1.1/title', 0x5468652052444620566f636162756c617279202852444629, '', 7489, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'http://purl.org/dc/elements/1.1/description', 0x54686973206973207468652052444620536368656d6120666f72207468652052444620766f636162756c61727920646566696e656420696e2074686520524446206e616d6573706163652e, '', 7490, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7491, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e7323, '', 7492, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/2000/01/rdf-schema#label', 0x74797065, '', 7493, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x546865207375626a65637420697320616e20696e7374616e6365206f66206120636c6173732e, '', 7494, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7495, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7496, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7497, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e7323, '', 7498, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'http://www.w3.org/2000/01/rdf-schema#label', 0x50726f7065727479, '', 7499, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x54686520636c617373206f66205244462070726f706572746965732e, '', 7500, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7501, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7502, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e7323, '', 7503, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement', 'http://www.w3.org/2000/01/rdf-schema#label', 0x53746174656d656e74, '', 7504, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7505, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x54686520636c617373206f66205244462073746174656d656e74732e, '', 7506, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#subject', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7507, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#subject', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e7323, '', 7508, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#subject', 'http://www.w3.org/2000/01/rdf-schema#label', 0x7375626a656374, '', 7509, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#subject', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x546865207375626a656374206f6620746865207375626a656374205244462073746174656d656e742e, '', 7510, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#subject', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732353746174656d656e74, '', 7511, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#subject', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7512, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#predicate', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7513, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#predicate', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e7323, '', 7514, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#predicate', 'http://www.w3.org/2000/01/rdf-schema#label', 0x707265646963617465, '', 7515, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#predicate', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x54686520707265646963617465206f6620746865207375626a656374205244462073746174656d656e742e, '', 7516, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#predicate', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732353746174656d656e74, '', 7517, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#predicate', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7518, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#object', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7519, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#object', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e7323, '', 7520, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#object', 'http://www.w3.org/2000/01/rdf-schema#label', 0x6f626a656374, '', 7521, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#object', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x546865206f626a656374206f6620746865207375626a656374205244462073746174656d656e742e, '', 7522, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#object', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732353746174656d656e74, '', 7523, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#object', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7524, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Bag', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7525, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Bag', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e7323, '', 7526, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Bag', 'http://www.w3.org/2000/01/rdf-schema#label', 0x426167, '', 7527, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Bag', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x54686520636c617373206f6620756e6f72646572656420636f6e7461696e6572732e, '', 7528, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Bag', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436f6e7461696e6572, '', 7529, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Seq', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7530, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Seq', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e7323, '', 7531, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Seq', 'http://www.w3.org/2000/01/rdf-schema#label', 0x536571, '', 7532, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Seq', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x54686520636c617373206f66206f72646572656420636f6e7461696e6572732e, '', 7533, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Seq', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436f6e7461696e6572, '', 7534, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Alt', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7535, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Alt', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e7323, '', 7536, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Alt', 'http://www.w3.org/2000/01/rdf-schema#label', 0x416c74, '', 7537, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Alt', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x54686520636c617373206f6620636f6e7461696e657273206f6620616c7465726e6174697665732e, '', 7538, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Alt', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436f6e7461696e6572, '', 7539, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7540, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e7323, '', 7541, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', 'http://www.w3.org/2000/01/rdf-schema#label', 0x76616c7565, '', 7542, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x4964696f6d617469632070726f7065727479207573656420666f7220737472756374757265642076616c7565732e, '', 7543, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7544, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7545, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#List', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7546, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#List', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e7323, '', 7547, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#List', 'http://www.w3.org/2000/01/rdf-schema#label', 0x4c697374, '', 7548, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#List', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x54686520636c617373206f6620524446204c697374732e, '', 7549, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#List', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7550, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#nil', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e73234c697374, '', 7551, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#nil', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e7323, '', 7552, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#nil', 'http://www.w3.org/2000/01/rdf-schema#label', 0x6e696c, '', 7553, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#nil', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x54686520656d707479206c6973742c2077697468206e6f206974656d7320696e2069742e204966207468652072657374206f662061206c697374206973206e696c207468656e20746865206c69737420686173206e6f206d6f7265206974656d7320696e2069742e, '', 7554, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#first', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7555, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#first', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e7323, '', 7556, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#first', 'http://www.w3.org/2000/01/rdf-schema#label', 0x6669727374, '', 7557, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#first', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x546865206669727374206974656d20696e20746865207375626a65637420524446206c6973742e, '', 7558, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46');
INSERT INTO `statements` (`modelID`, `subject`, `predicate`, `object`, `l_language`, `id`, `author`, `stread`, `stedit`, `stdelete`, `epoch`) VALUES
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#first', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e73234c697374, '', 7559, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#first', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7560, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#rest', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7561, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#rest', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e7323, '', 7562, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#rest', 'http://www.w3.org/2000/01/rdf-schema#label', 0x72657374, '', 7563, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#rest', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x5468652072657374206f6620746865207375626a65637420524446206c69737420616674657220746865206669727374206974656d2e, '', 7564, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#rest', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e73234c697374, '', 7565, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#rest', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e73234c697374, '', 7566, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#XMLLiteral', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61234461746174797065, '', 7567, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#XMLLiteral', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61234c69746572616c, '', 7568, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#XMLLiteral', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e7323, '', 7569, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#XMLLiteral', 'http://www.w3.org/2000/01/rdf-schema#label', 0x584d4c4c69746572616c, '', 7570, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#XMLLiteral', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x54686520636c617373206f6620584d4c206c69746572616c2076616c7565732e, '', 7571, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(4, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'http://www.w3.org/2000/01/rdf-schema#seeAlso', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d612d6d6f7265, '', 7572, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f323030322f30372f6f776c234f6e746f6c6f6779, '', 7573, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#', 'http://purl.org/dc/elements/1.1/title', 0x5468652052444620536368656d6120766f636162756c61727920285244465329, '', 7574, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Resource', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7575, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Resource', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123, '', 7576, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Resource', 'http://www.w3.org/2000/01/rdf-schema#label', 0x5265736f75726365, '', 7577, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Resource', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x54686520636c617373207265736f757263652c2065766572797468696e672e, '', 7578, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Class', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7579, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Class', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123, '', 7580, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Class', 'http://www.w3.org/2000/01/rdf-schema#label', 0x436c617373, '', 7581, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Class', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x54686520636c617373206f6620636c61737365732e, '', 7582, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Class', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7583, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7584, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123, '', 7585, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.w3.org/2000/01/rdf-schema#label', 0x737562436c6173734f66, '', 7586, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x546865207375626a656374206973206120737562636c617373206f66206120636c6173732e, '', 7587, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7588, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7589, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#subPropertyOf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7590, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#subPropertyOf', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123, '', 7591, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#subPropertyOf', 'http://www.w3.org/2000/01/rdf-schema#label', 0x73756250726f70657274794f66, '', 7592, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#subPropertyOf', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x546865207375626a65637420697320612073756270726f7065727479206f6620612070726f70657274792e, '', 7593, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#subPropertyOf', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7594, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#subPropertyOf', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7595, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#comment', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7596, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#comment', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123, '', 7597, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#comment', 'http://www.w3.org/2000/01/rdf-schema#label', 0x636f6d6d656e74, '', 7598, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#comment', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x41206465736372697074696f6e206f6620746865207375626a656374207265736f757263652e, '', 7599, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#comment', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7600, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#comment', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61234c69746572616c, '', 7601, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#label', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7602, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#label', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123, '', 7603, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#label', 'http://www.w3.org/2000/01/rdf-schema#label', 0x6c6162656c, '', 7604, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#label', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x412068756d616e2d7265616461626c65206e616d6520666f7220746865207375626a6563742e, '', 7605, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#label', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7606, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#label', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61234c69746572616c, '', 7607, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7608, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123, '', 7609, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.w3.org/2000/01/rdf-schema#label', 0x646f6d61696e, '', 7610, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x4120646f6d61696e206f6620746865207375626a6563742070726f70657274792e, '', 7611, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7612, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7613, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7614, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123, '', 7615, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#label', 0x72616e6765, '', 7616, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x412072616e6765206f6620746865207375626a6563742070726f70657274792e, '', 7617, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7618, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7619, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#seeAlso', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7620, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#seeAlso', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123, '', 7621, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#seeAlso', 'http://www.w3.org/2000/01/rdf-schema#label', 0x736565416c736f, '', 7622, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#seeAlso', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x4675727468657220696e666f726d6174696f6e2061626f757420746865207375626a656374207265736f757263652e, '', 7623, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#seeAlso', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7624, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#seeAlso', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7625, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7626, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123, '', 7627, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 'http://www.w3.org/2000/01/rdf-schema#subPropertyOf', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123736565416c736f, '', 7628, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 'http://www.w3.org/2000/01/rdf-schema#label', 0x6973446566696e65644279, '', 7629, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x54686520646566696e696e6974696f6e206f6620746865207375626a656374207265736f757263652e, '', 7630, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7631, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7632, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Literal', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7633, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Literal', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123, '', 7634, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Literal', 'http://www.w3.org/2000/01/rdf-schema#label', 0x4c69746572616c, '', 7635, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Literal', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x54686520636c617373206f66206c69746572616c2076616c7565732c2065672e207465787475616c20737472696e677320616e6420696e7465676572732e, '', 7636, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Literal', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7637, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Container', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7638, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Container', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123, '', 7639, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Container', 'http://www.w3.org/2000/01/rdf-schema#label', 0x436f6e7461696e6572, '', 7640, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Container', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7641, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Container', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x54686520636c617373206f662052444620636f6e7461696e6572732e, '', 7642, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#ContainerMembershipProperty', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7643, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#ContainerMembershipProperty', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123, '', 7644, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#ContainerMembershipProperty', 'http://www.w3.org/2000/01/rdf-schema#label', 0x436f6e7461696e65724d656d6265727368697050726f7065727479, '', 7645, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#ContainerMembershipProperty', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x54686520636c617373206f6620636f6e7461696e6572206d656d626572736869702070726f706572746965732c207264663a5f312c207264663a5f322c202e2e2e2c0a2020202020202020202020202020202020202020616c6c206f6620776869636820617265207375622d70726f70657274696573206f6620276d656d626572272e, '', 7646, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#ContainerMembershipProperty', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7647, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#member', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7648, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#member', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123, '', 7649, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#member', 'http://www.w3.org/2000/01/rdf-schema#label', 0x6d656d626572, '', 7650, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#member', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x41206d656d626572206f6620746865207375626a656374207265736f757263652e, '', 7651, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#member', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7652, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#member', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7653, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Datatype', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7654, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Datatype', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123, '', 7655, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Datatype', 'http://www.w3.org/2000/01/rdf-schema#label', 0x4461746174797065, '', 7656, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Datatype', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x54686520636c617373206f6620524446206461746174797065732e, '', 7657, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#Datatype', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7658, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(5, 'http://www.w3.org/2000/01/rdf-schema#', 'http://www.w3.org/2000/01/rdf-schema#seeAlso', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d612d6d6f7265, '', 7659, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 0x687474703a2f2f7777772e74616f2e6c752f4f6e746f6c6f676965732f67656e657269732e7264662367656e657269735f526573736f75726365, '', 7660, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject', 'http://www.w3.org/2000/01/rdf-schema#label', 0x54414f204f626a656374, 'EN', 7661, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x416e7920726573736f757263652072656c6174656420746f206574657374696e67, 'EN', 7662, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#Plugin', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7663, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#Plugin', 'http://www.w3.org/2000/01/rdf-schema#label', 0x506c7567696e, 'EN', 7664, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#Plugin', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x506c7567696e, 'EN', 7665, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#Plugin', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d6123436c617373, '', 7666, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#Plugin', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61234c69746572616c, '', 7667, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#Plugin', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662354657874426f78, '', 7668, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#generis_Ressource', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7669, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#generis_Ressource', 'http://www.w3.org/2000/01/rdf-schema#label', 0x67656e657269735f526573736f75726365, 'EN', 7670, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#generis_Ressource', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x67656e657269735f526573736f75726365, 'EN', 7671, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#Model', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61235265736f75726365, '', 7672, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#Model', 'http://www.w3.org/2000/01/rdf-schema#label', 0x4d6f64656c, 'EN', 7673, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#Model', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x4d6f64656c, 'EN', 7674, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#Plugin', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7675, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#Plugin', 'http://www.w3.org/2000/01/rdf-schema#label', 0x506c7567696e, 'EN', 7676, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#Plugin', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x506c7567696e, 'EN', 7677, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#Plugin', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e74616f2e6c752f4f6e746f6c6f676965732f67656e657269732e726466234d6f64656c, '', 7678, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#Plugin', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e77332e6f72672f323030302f30312f7264662d736368656d61234c69746572616c, '', 7679, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#Plugin', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e7264662354657874426f78, '', 7680, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7681, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.w3.org/2000/01/rdf-schema#label', 0x69735f6c616e67756167655f646570656e64656e74, 'EN', 7682, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x69735f6c616e67756167655f646570656e64656e74, 'EN', 7683, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.w3.org/2000/01/rdf-schema#domain', 0x687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e732350726f7065727479, '', 7684, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.w3.org/2000/01/rdf-schema#range', 0x687474703a2f2f7777772e74616f2e6c752f4f6e746f6c6f676965732f67656e657269732e72646623426f6f6c65616e, '', 7685, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'is_language_dependent', 0x687474703a2f2f7777772e74616f2e6c752f4f6e746f6c6f676965732f67656e657269732e7264662346616c7365, '', 7686, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 0x687474703a2f2f7777772e74616f2e6c752f6461746174797065732f576964676574446566696e6974696f6e732e72646623526164696f426f78, '', 7687, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#Boolean', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 0x687474703a2f2f7777772e74616f2e6c752f4f6e746f6c6f676965732f67656e657269732e7264662367656e657269735f526573736f75726365, '', 7688, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#Boolean', 'http://www.w3.org/2000/01/rdf-schema#label', 0x426f6f6c65616e, 'EN', 7689, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#Boolean', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x426f6f6c65616e, 'EN', 7690, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#True', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e74616f2e6c752f4f6e746f6c6f676965732f67656e657269732e72646623426f6f6c65616e, '', 7691, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#True', 'http://www.w3.org/2000/01/rdf-schema#label', 0x54727565, 'EN', 7692, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#True', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x54727565, 'EN', 7693, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#False', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0x687474703a2f2f7777772e74616f2e6c752f4f6e746f6c6f676965732f67656e657269732e72646623426f6f6c65616e, '', 7694, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#False', 'http://www.w3.org/2000/01/rdf-schema#label', 0x46616c7365, 'EN', 7695, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#False', 'http://www.w3.org/2000/01/rdf-schema#comment', 0x46616c7365, 'EN', 7696, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-11-04 15:28:46');

-- --------------------------------------------------------

--
-- Structure de la table `subscribee`
--
-- Cr�ation: Mer 04 Novembre 2009 � 15:30
-- Derni�re modification: Mer 04 Novembre 2009 � 15:30
--

CREATE TABLE IF NOT EXISTS `subscribee` (
  `Login` varchar(32) NOT NULL DEFAULT '',
  `Password` varchar(32) NOT NULL DEFAULT '',
  `URL` varchar(255) NOT NULL DEFAULT '',
  `Type` varchar(255) NOT NULL,
  `IdSub` int(32) NOT NULL AUTO_INCREMENT,
  `DatabaseName` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`IdSub`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Contenu de la table `subscribee`
--


-- --------------------------------------------------------

--
-- Structure de la table `subscriber`
--
-- Cr�ation: Mer 04 Novembre 2009 � 15:30
-- Derni�re modification: Mer 04 Novembre 2009 � 15:30
--

CREATE TABLE IF NOT EXISTS `subscriber` (
  `Id` int(32) NOT NULL AUTO_INCREMENT,
  `Login` varchar(32) NOT NULL DEFAULT '',
  `Password` varchar(32) NOT NULL DEFAULT '',
  `LastVisit` varchar(32) NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `ismember` int(32) NOT NULL DEFAULT '0',
  `DatabaseName` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Contenu de la table `subscriber`
--

INSERT INTO `subscriber` (`Id`, `Login`, `Password`, `LastVisit`, `enabled`, `ismember`, `DatabaseName`) VALUES
(21, '47072', 'e3d23b257cd19c27ca38fb7a8eeb9cd1', '', 1, 1, ''),
(22, '29200', 'fec73528cd9681706631f08c0f166dae', '', 1, 1, ''),
(25, '56078', 'b4fcb370c237271d1e9453614862944f', '', 1, 1, ''),
(24, '22100', '0b47657d6bcf28d3ea29ccea75dec4bc', '', 1, 1, '');

-- --------------------------------------------------------

--
-- Structure de la table `subscribersgroup`
--
-- Cr�ation: Mer 04 Novembre 2009 � 15:30
-- Derni�re modification: Mer 04 Novembre 2009 � 15:30
--

CREATE TABLE IF NOT EXISTS `subscribersgroup` (
  `ID` int(32) NOT NULL AUTO_INCREMENT,
  `subgroupof` int(32) NOT NULL DEFAULT '0',
  `Name` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Contenu de la table `subscribersgroup`
--

INSERT INTO `subscribersgroup` (`ID`, `subgroupof`, `Name`) VALUES
(1, 0, 'ROOT'),
(2, 1, 'ROOTA'),
(3, 1, 'ROOTB'),
(4, 2, 'C'),
(5, 3, 'D');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--
-- Cr�ation: Mer 04 Novembre 2009 � 15:30
-- Derni�re modification: Mer 04 Novembre 2009 � 15:30
-- Derni�re v�rification: Mer 04 Novembre 2009 � 15:30
--

CREATE TABLE IF NOT EXISTS `user` (
  `login` varchar(32) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `usergroup` varchar(32) NOT NULL DEFAULT '',
  `LastName` varchar(64) NOT NULL DEFAULT '',
  `FirstName` varchar(64) NOT NULL DEFAULT '',
  `E_Mail` varchar(128) NOT NULL DEFAULT '',
  `Company` varchar(128) NOT NULL DEFAULT '',
  `Deflg` char(2) NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`login`),
  KEY `login` (`login`),
  KEY `usergroup` (`usergroup`),
  KEY `login_2` (`login`),
  KEY `usergroup_2` (`usergroup`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `user`
--

-- --------------------------------------------------------

--
-- Structure de la table `_mask`
--
-- Cr�ation: Mer 04 Novembre 2009 � 15:30
-- Derni�re modification: Mer 04 Novembre 2009 � 15:30
--

CREATE TABLE IF NOT EXISTS `_mask` (
  `user` varchar(255) NOT NULL DEFAULT '',
  `Scope` varchar(255) NOT NULL DEFAULT '',
  `Method` varchar(255) NOT NULL DEFAULT '',
  `onAssertPrivileges` longtext NOT NULL,
  `_comment` varchar(255) NOT NULL DEFAULT '',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Contenu de la table `_mask`
--

