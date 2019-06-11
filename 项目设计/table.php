DROP DATABASE IF EXISTS `twl`;
CREATE DATABASE twl DEFAULT CHARSET=UTF8; 
USE twl;
DROP TABLE IF EXISTS `label`;

CREATE TABLE `label` (
  `labelid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `teamid` mediumint(8) DEFAULT NULL,
  `labelname` varchar(50) DEFAULT NULL,
  `color` char(9) DEFAULT NULL,
  PRIMARY KEY (`labelid`)
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `membership`;

CREATE TABLE `membership` (
  `teamid` mediumint(8) unsigned NOT NULL,
  `memberid` bigint(20) unsigned NOT NULL,
  `verifykey` char(40) DEFAULT NULL,
  PRIMARY KEY (`teamid`,`memberid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `mylog`;
CREATE TABLE `mylog` (
  `userid` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `fromtime` float DEFAULT NULL,
  `totime` float DEFAULT NULL,
  `label` int(10) unsigned DEFAULT NULL,
  `content` text,
  `teamid` mediumint(8) unsigned NOT NULL,
  `archive` tinyint(1) NOT NULL DEFAULT '0',
  KEY `label` (`label`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `profile`;
CREATE TABLE `profile` (
  `userid` bigint(20) unsigned NOT NULL,
  `portrait` varchar(255) DEFAULT '',
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `profiles`;
CREATE TABLE `profiles` (
  `id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `team`;
CREATE TABLE `team` (
  `teamid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `teamname` char(50) NOT NULL DEFAULT '',
  `createby` bigint(20) unsigned NOT NULL,
  `managedby` bigint(20) unsigned NOT NULL,
  `createtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `membernum` smallint(5) unsigned NOT NULL DEFAULT '1',
  `logo` char(32) DEFAULT '',
  PRIMARY KEY (`teamid`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `userid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `pw` char(32) NOT NULL,
  `salt` char(20) NOT NULL,
  `name` varchar(32) NOT NULL,
  `active` bool NOT NULL DEFAULT false,
  `createtm` timestamp,
  `lastlogintm` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `allowed` tinyint(1) DEFAULT '1',
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`userid`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
