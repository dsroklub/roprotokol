-- MariaDB dump 10.19  Distrib 10.5.10-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: roprotokol
-- ------------------------------------------------------
-- Server version	10.5.10-MariaDB-2-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `MemberRightType`
--

DROP TABLE IF EXISTS `MemberRightType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MemberRightType` (
  `member_right` varchar(50) NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  `arg` varchar(200) NOT NULL DEFAULT '',
  `showname` varchar(255) DEFAULT NULL,
  `predicate` varchar(255) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 1,
  `category` char(20) DEFAULT 'roning',
  `validity` float DEFAULT NULL,
  PRIMARY KEY (`member_right`,`arg`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MemberRightType`
--

LOCK TABLES `MemberRightType` WRITE;
/*!40000 ALTER TABLE `MemberRightType` DISABLE KEYS */;
INSERT INTO `MemberRightType` (`member_right`, `description`, `arg`, `showname`, `predicate`, `active`, `category`, `validity`) VALUES ('8cox','otter styrmand','','otter-styrmandsret','være otter-styrmand',1,'roning',NULL),('admin','bestyrelsesmedlem','bestyrelsen','bestyrelsesmedlem','medlem af bestyrelsen',1,'klub',NULL),('admin','kontingentkasserer','kontingentkasserer','kontingentkasserer','vaere kontingentkasserer',1,'klub',NULL),('admin','administrator p roprotkollen','roprotokol','administrator','være administrator',1,'admin',NULL),('admin','materieludvalg, bådvagter','vedligehold','administrator','være administrator',1,'admin',NULL),('competition','kaproere','','kaproer','være kaproer',1,'roning',NULL),('cox','styrmand','','styrmandsret','være styrmand',1,'roning',NULL),('cox','Begrænset styrmand','Begrænset','Begrænset styrmandsret','være begrænset styrmand',1,'roning',NULL),('coxtheory','styrmandsteori','','styrmandsteori','have styrmandsteori',1,'roning',NULL),('developer','udvikler','admin','udvikler','udvikle',0,'roning',NULL),('entringsøvelse','Entringsøvelse','','entringsøvelse','have øvet entring',1,'roning',3),('event','forum-oprettet','fora','opette nye fora','kunne oprette fora',1,'klub',NULL),('gym','gymnastik admin','admin','administrator for gymnastikprotokol','være gymnastikadministrator',1,'klub',NULL),('instructor','instruktør','','instruktør','være instruktør',0,'roning',NULL),('instructor','styrmandsinstruktør','cox','instruktør','være styrmandsinstruktør',1,'roning',NULL),('instructor','instruktør, kajak','kajak','instruktørret','være instruktør',1,'roning',NULL),('instructor','instruktør, sculler','outrigger','instruktørret','være instruktør',1,'roning',NULL),('instructor','instruktør inrigger','row','instruktørret','være instruktør',1,'roning',NULL),('kajak','kajakret B','helårs','kajakret','have kajakret',1,'roning',NULL),('kajak','kajak','sommer','kajakret','have kajakret',1,'roning',NULL),('kanin','kanin','','kanin','være kanin',1,'roning',NULL),('longdistance','langtursstyrmand','','langtursstyrmandsret','være langtursstyrmand',1,'roning',NULL),('longdistancetheory','langtursstyrmandsteori','','langtursteori','have langtursteori',1,'roning',NULL),('longdistance_swim','Langtur svømmeprøve','300m svøm','Langtur svømmeprøve','have taget langtur svømmeprøve indenfor 3 år',1,'roning',3),('motorboat','motorbåd','','motorbådsret','have motorbådsret',1,'roning',NULL),('polokajak','polokajakret B','helårs','polokajakret','have polokajakret',1,'roning',NULL),('polokajak','polokajakret A','sommer','polokajakret','have polokajakret',1,'roning',NULL),('remote_access','roprotokol fjernadgang','roprotokol','roprotokol fjernadgang','have tilladelse til at bruge roprotokollen udefra',1,'admin',NULL),('rowright','roret','','roret','have roret',1,'roning',NULL),('sculler','sculler','sommer','scullerret','have scullerret',1,'roning',NULL),('surfski','Surfski-helårsret','helårs','surfski-helårsret','have surfski-helårsret',1,'roning',NULL),('surfski','Surfski-ret','sommer','surfski-ret','have surfski-ret',1,'roning',NULL),('svava','svava','','svavaret','have svavaret',1,'roning',NULL),('swim400','kan svømme 400m','','svømme 400m','kunne svømme 400m',1,'roning',NULL),('wrench','har ikke deltaget i vintervedligehold 2015/2016','2016','Mangler vintervedligehold','mangle vintervedligehold',1,'klub',NULL),('wrench','har ikke deltaget i vintervedligehold 2016/2017','2017','Mangler vintervedligehold','mangle vintervedligehold',1,'klub',NULL),('wrench','har ikke deltaget i vintervedligehold 2017/2018','2018','mangler vintervedligehold','mangle vintervedligehold',1,'klub',NULL),('wrench','har ikke deltaget i vintervedligehold 2018/2019','2019','mangler vintervedligehold','mangle vintervedligehold',0,'klub',NULL),('wrench','har ikke deltaget i vintervedligehold 2019/2020','2020','mangler vintervedligehold','mangle vintervedligehold',0,'klub',NULL);
/*!40000 ALTER TABLE `MemberRightType` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-06-29  0:10:13
