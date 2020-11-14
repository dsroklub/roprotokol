-- MySQL dump 10.13  Distrib 5.7.26, for Linux (x86_64)
--
-- Host: localhost    Database: roprotokol
-- ------------------------------------------------------
-- Server version	5.7.26-1+b1-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED='c642768f-71b7-11e5-b30f-eca86bfebae2:1-12598';

--
-- Dumping data for table `MemberRightType`
--

LOCK TABLES `MemberRightType` WRITE;
/*!40000 ALTER TABLE `MemberRightType` DISABLE KEYS */;
INSERT INTO `MemberRightType` (`member_right`, `description`, `arg`, `showname`, `predicate`, `active`) VALUES ('8','otter','','otterret','have otterret',1),('8cox','otter styrmand','','otter-styrmandsret','være otter-styrmand',1),('admin','administrator p roprotkollen','roprotokol','administrator','være administrator',1),('admin','materieludvalg, bådvagter','vedligehold','administrator','være administrator',1),('competition','kaproere','','kaproer','være kaproer',1),('cox','styrmand','','styrmandsret','være styrmand',1),('coxtheory','styrmandsteori','','styrmandsteori','have styrmandsteori',1),('developer','udvikler','admin','udvikler','udvikle',0),('entringsøvelse','Entringsøvelse','','entringsøvelse','have øvet entring',1),('event','forum-oprettet','fora','opette nye fora','kunne oprette fora',1),('gym','gymnastik admin','admin','administrator for gymnastikprotokol','',1),('instructor','instruktør','','instruktørret','være instruktør',1),('instructor','instruktør, kajak','kajak','instruktørret','være instruktør',1),('instructor','instruktør, sculler','outrigger','instruktørret','være instruktør',1),('instructor','instruktør inrigger','row','instruktørret','være instruktør',1),('kajak','kajakret B','helårs','kajakret','have kajakret',1),('kajak','kajak','sommer','kajakret','have kajakret',1),('kanin','kanin','','kanin','være kanin',1),('langturøresund','Langure på Øresund','','Øresund langtursret','have øresund langtursret',0),('longdistance','langtursstyrmand','','langtursstyrmandsret','være langtursstyrmand',1),('longdistancetheory','langtursstyrmandsteori','','langtursteori','have langtursteori',1),('motorboat','motorbåd','','motorbådsret','have motorbådsret',1),('polokajak','polokajakret B','helårs','polokajakret','have polokajakret',1),('polokajak','polokajakret A','sommer','polokajakret','have polokajakret',1),('remote_access','roprotokol fjernadgang','roprotokol','roprotokol fjernadgang','have tilladelse til at bruge roprotokollen udefra',1),('rowright','roret','','roret','have roret',1),('sculler','sculler','sommer','scullerret','have scullerret',1),('skærgård','skærgården','','skærgårdsret','have skærgårdsret',0),('surfski','Surfski-helårsret','helårs','surfski-helårsret','have surfski-helårsret',1),('surfski','Surfski-ret','sommer','surfski-ret','have surfski-ret',1),('svava','svava','','svavaret','have svavaret',1),('swim400','kan svømme 400m','','svømme 400m','kunne svømme 400m',1),('wrench','har ikke deltaget i vintervedligehold 2015/2016','2016','Mangler vintervedligehold','mangle vintervedligehold',1),('wrench','har ikke deltaget i vintervedligehold 2016/2017','2017','Mangler vintervedligehold','mangle vintervedligehold',1),('wrench','har ikke deltaget i vintervedligehold 2017/2018','2018','mangler vintervedligehold','mangle vintervedligehold',1),('wrench','har ikke deltaget i vintervedligehold 2018/2019','2019','mangler vintervedligehold','mangle vintervedligehold',0),('wrench','har ikke deltaget i vintervedligehold 2019/2020','2020','mangler vintervedligehold','mangle vintervedligehold',0);
/*!40000 ALTER TABLE `MemberRightType` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-12-06 15:01:35
