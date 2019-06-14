-- MySQL dump 10.13  Distrib 5.7.26, for Win64 (x86_64)
--
-- Host: localhost    Database: twr
-- ------------------------------------------------------
-- Server version	5.7.26

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

--
-- Current Database: `twr`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `twr` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `twr`;

--
-- Table structure for table `label`
--

DROP TABLE IF EXISTS `label`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `label` (
  `labelid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `teamid` int(10) unsigned NOT NULL,
  `labelname` varchar(255) NOT NULL,
  `color` char(10) DEFAULT NULL,
  PRIMARY KEY (`labelid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `label`
--

LOCK TABLES `label` WRITE;
/*!40000 ALTER TABLE `label` DISABLE KEYS */;
INSERT INTO `label` VALUES (1,1,'Label 1','051153102'),(2,1,'会议','204153102'),(3,1,'工程','204204102'),(4,1,'聚会','051255102');
/*!40000 ALTER TABLE `label` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `membership`
--

DROP TABLE IF EXISTS `membership`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `membership` (
  `teamid` int(10) unsigned NOT NULL,
  `memberid` int(10) unsigned NOT NULL,
  `verifykey` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `membership`
--

LOCK TABLES `membership` WRITE;
/*!40000 ALTER TABLE `membership` DISABLE KEYS */;
INSERT INTO `membership` VALUES (1,1,'bccb54ab9ef021e3739b2fb215f972e15738e6e2'),(1,2,'806ed4fc0b0717000ecf3f5455691ce0f4b3ec0e');
/*!40000 ALTER TABLE `membership` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mylog`
--

DROP TABLE IF EXISTS `mylog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mylog` (
  `userid` int(10) unsigned NOT NULL,
  `teamid` int(10) unsigned NOT NULL,
  `date` date DEFAULT NULL,
  `fromtime` float unsigned DEFAULT NULL,
  `totime` float unsigned DEFAULT NULL,
  `label` char(10) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mylog`
--

LOCK TABLES `mylog` WRITE;
/*!40000 ALTER TABLE `mylog` DISABLE KEYS */;
INSERT INTO `mylog` VALUES (1,1,'2019-06-10',1,3,'1','明天去博览会\n研讨会'),(1,1,'2019-06-11',0,3,'2','凌晨会议：\n1）经营状况\n2）问题说明\n3）解决议案'),(1,1,'2019-06-12',7,10.5,'3','工程实施：\n1）停止服务器工作\n2）数据备份\n3）软件安装\n4）数据导入\n5）启动服务\n6）测试'),(1,1,'2019-06-10',7.5,9.5,'2','早会议题');
/*!40000 ALTER TABLE `mylog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profile` (
  `userid` int(10) unsigned NOT NULL,
  `portrait` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `nickname` varchar(30) DEFAULT NULL,
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile`
--

LOCK TABLES `profile` WRITE;
/*!40000 ALTER TABLE `profile` DISABLE KEYS */;
/*!40000 ALTER TABLE `profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team`
--

DROP TABLE IF EXISTS `team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team` (
  `teamid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `teamname` varchar(50) NOT NULL,
  `createby` int(11) DEFAULT NULL,
  `managedby` int(11) DEFAULT NULL,
  `createtm` int(10) unsigned DEFAULT NULL,
  `membernum` smallint(5) unsigned NOT NULL DEFAULT '0',
  `logo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`teamid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team`
--

LOCK TABLES `team` WRITE;
/*!40000 ALTER TABLE `team` DISABLE KEYS */;
INSERT INTO `team` VALUES (1,'攀岩',1,1,1560497352,1,NULL);
/*!40000 ALTER TABLE `team` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `userid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `pw` varchar(50) NOT NULL,
  `salt` varchar(50) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `createtm` int(11) DEFAULT NULL,
  `lastlogintm` int(10) unsigned DEFAULT NULL,
  `allowed` tinyint(1) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'2450329248@qq.com','2606abd8a204f014fb9b402eb435ff09','256005d034cc824ef0','TIGER',1,NULL,NULL,1,0),(2,'xiaoming@qq.com','5d035f1fac414','5','',0,NULL,0,1,0);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-06-14 17:37:20
