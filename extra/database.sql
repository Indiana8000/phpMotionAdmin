---
--- Database Schema for phpMotionAdmin
---

CREATE TABLE IF NOT EXISTS `config` (
  `cName`         varchar(32)  NOT NULL,
  `cValue`        varchar(128) NOT NULL,
  PRIMARY KEY (`cName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `threats` (
  `tID`           int(3)  NOT NULL,
  `tName`         varchar(128) NOT NULL,
  `tStatus`       int(1)  NOT NULL DEFAULT '0',
  `tDetection`    int(1)  NOT NULL DEFAULT '0',
  `tNotification` int(1)  NOT NULL DEFAULT '0',
  `tEvent`        int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `notify` (
  `nID`           int(5) NOT NULL AUTO_INCREMENT,
  `nCam`          int(3) NOT NULL,
  `nType`         varchar(64) NOT NULL,
  `nX`            int(11) NOT NULL,
  `nP`            varchar(256) NOT NULL,
  PRIMARY KEY (`nID`),
  KEY `nCam` (`nCam`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `events` (
  `eID`           int(11)      NOT NULL AUTO_INCREMENT,
  `eDate`         timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `eCam`          int(3)       NOT NULL,
  `eType`         varchar(16)  NOT NULL,
  `eNum`          int(11)      NOT NULL,
  `eFile`         varchar(256) NOT NULL,
  `eData`         varchar(32)  NOT NULL,
  PRIMARY KEY (`eID`),
  KEY `CAM-T` (`eCam`,`eType`),
  KEY `CAM-E` (`eCam`,`eNum`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

---
--- EOF
---