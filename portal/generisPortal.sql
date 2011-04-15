--
-- Table structure for table `generismodules`
--

DROP TABLE IF EXISTS `generismodules`;
CREATE TABLE `generismodules` (
  `generisModuleName` varchar(255) NOT NULL default '',
  `generisLogin` varchar(255) NOT NULL default '',
  `ModuleLogin` varchar(255) NOT NULL default '',
  `ModulePass` varchar(255) NOT NULL default '',
  `URL` varchar(255) NOT NULL default '',
  `enabled` varchar(32) NOT NULL default '0',
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`),
  KEY `generisLogin` (`generisLogin`)
) ENGINE=MyISAM AUTO_INCREMENT=1413 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `generismodules`
--

LOCK TABLES `generismodules` WRITE;
/*!40000 ALTER TABLE `generismodules` DISABLE KEYS */;
/*!40000 ALTER TABLE `generismodules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `generisuser`
--

DROP TABLE IF EXISTS `generisuser`;
CREATE TABLE `generisuser` (
  `generisLogin` varchar(255) NOT NULL default '',
  `generisPass` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`generisLogin`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `generisuser`
--

LOCK TABLES `generisuser` WRITE;
/*!40000 ALTER TABLE `generisuser` DISABLE KEYS */;
/*!40000 ALTER TABLE `generisuser` ENABLE KEYS */;
UNLOCK TABLES;



CREATE DATABASE IF NOT EXISTS `forum` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `forum`;


CREATE TABLE `forum_answer` (
  `question_id` varchar(255) NOT NULL default '',
  `a_id` int(4) NOT NULL default '0',
  `a_name` varchar(65) NOT NULL default '',
  `a_email` varchar(65) NOT NULL default '',
  `a_answer` longtext NOT NULL,
  `a_datetime` varchar(25) NOT NULL default '',
  KEY `a_id` (`a_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `forum_answer`
-- 

INSERT INTO `forum_answer` VALUES ('http://www.tao.lu/Ontologies/generis.rdf#Boolean', 1, 'demo', '', 'Some note about boolean', '28/03/07 11:33:07');
INSERT INTO `forum_answer` VALUES ('http://www.tao.lu/Ontologies/generis.rdf#Boolean', 2, 'demo', '', 'Another note', '28/03/07 11:33:11');

-- --------------------------------------------------------

-- 
-- Table structure for table `forum_question`
-- 

CREATE TABLE `forum_question` (
  `id` varchar(255) NOT NULL default '',
  `topic` varchar(255) NOT NULL default '',
  `detail` longtext NOT NULL,
  `name` varchar(65) NOT NULL default '',
  `email` varchar(65) NOT NULL default '',
  `datetime` varchar(25) NOT NULL default '',
  `view` int(4) NOT NULL default '0',
  `reply` int(4) NOT NULL default '0',
  `locked` int(4) NOT NULL default '0',
  `sticky` int(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `forum_question`
-- 

INSERT INTO `forum_question` VALUES ('http://www.tao.lu/Ontologies/generis.rdf#Boolean', 'http://www.tao.lu/Ontologies/generis.rdf#Boolean', 'http://www.tao.lu/Ontologies/generis.rdf#Boolean', 'demo', 'email', '28/03/07 11:32:56', 63, 5, 0, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `forum_user`
-- 

CREATE TABLE `forum_user` (
  `id` int(4) NOT NULL auto_increment,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `email` text NOT NULL,
  `realname` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

-- 
-- Dumping data for table `forum_user`
-- 

INSERT INTO `forum_user` VALUES (12, 'root', '63a9f0ea7bb98050796b649e85481845', 'patrick.plichart@tudor.lu', 'Plichart');