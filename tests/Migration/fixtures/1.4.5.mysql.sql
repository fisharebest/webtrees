-- MySQL 8.0 schema produced by upgrading 1.0.0 with tagged release migrations; no application data.
-- MySQL 8 requires dropping the obsolete IGNORE hint from ALTER TABLE on an empty media table.
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `block` (
  `block_id` int NOT NULL AUTO_INCREMENT,
  `gedcom_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `xref` varchar(20) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `location` enum('main','side') COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `block_order` int NOT NULL,
  `module_name` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`block_id`),
  KEY `fk1` (`gedcom_id`),
  KEY `fk2` (`user_id`),
  KEY `fk3` (`module_name`),
  CONSTRAINT `block_ibfk_1` FOREIGN KEY (`gedcom_id`) REFERENCES `gedcom` (`gedcom_id`),
  CONSTRAINT `block_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  CONSTRAINT `block_ibfk_3` FOREIGN KEY (`module_name`) REFERENCES `module` (`module_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `block_setting` (
  `block_id` int NOT NULL,
  `setting_name` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`block_id`,`setting_name`),
  CONSTRAINT `block_setting_ibfk_1` FOREIGN KEY (`block_id`) REFERENCES `block` (`block_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `change` (
  `change_id` int NOT NULL AUTO_INCREMENT,
  `change_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('accepted','pending','rejected') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'pending',
  `gedcom_id` int NOT NULL,
  `xref` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `old_gedcom` mediumtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `new_gedcom` mediumtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`change_id`),
  KEY `ix1` (`gedcom_id`,`status`,`xref`),
  KEY `fk1` (`user_id`),
  CONSTRAINT `change_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  CONSTRAINT `change_ibfk_2` FOREIGN KEY (`gedcom_id`) REFERENCES `gedcom` (`gedcom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dates` (
  `d_day` tinyint NOT NULL,
  `d_month` char(5) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `d_mon` tinyint NOT NULL,
  `d_year` smallint NOT NULL,
  `d_julianday1` mediumint NOT NULL,
  `d_julianday2` mediumint NOT NULL,
  `d_fact` varchar(15) COLLATE utf8mb3_unicode_ci NOT NULL,
  `d_gid` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `d_file` int NOT NULL,
  `d_type` enum('@#DGREGORIAN@','@#DJULIAN@','@#DHEBREW@','@#DFRENCH R@','@#DHIJRI@','@#DROMAN@','@#DJALALI@') COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  KEY `ix1` (`d_day`),
  KEY `ix2` (`d_month`),
  KEY `ix3` (`d_mon`),
  KEY `ix4` (`d_year`),
  KEY `ix5` (`d_julianday1`),
  KEY `ix6` (`d_julianday2`),
  KEY `ix7` (`d_gid`),
  KEY `ix8` (`d_file`),
  KEY `ix9` (`d_type`),
  KEY `ix10` (`d_fact`,`d_gid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `default_resn` (
  `default_resn_id` int NOT NULL AUTO_INCREMENT,
  `gedcom_id` int NOT NULL,
  `xref` varchar(20) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tag_type` varchar(15) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `resn` enum('none','privacy','confidential','hidden') COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`default_resn_id`),
  UNIQUE KEY `ux1` (`gedcom_id`,`xref`,`tag_type`),
  CONSTRAINT `default_resn_ibfk_1` FOREIGN KEY (`gedcom_id`) REFERENCES `gedcom` (`gedcom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `families` (
  `f_id` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `f_file` int NOT NULL,
  `f_husb` varchar(20) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `f_wife` varchar(20) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `f_gedcom` mediumtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `f_numchil` int NOT NULL,
  PRIMARY KEY (`f_id`,`f_file`),
  UNIQUE KEY `ux1` (`f_file`,`f_id`),
  KEY `ix1` (`f_husb`),
  KEY `ix2` (`f_wife`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gedcom` (
  `gedcom_id` int NOT NULL AUTO_INCREMENT,
  `gedcom_name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`gedcom_id`),
  UNIQUE KEY `ux1` (`gedcom_name`),
  KEY `ix1` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gedcom_chunk` (
  `gedcom_chunk_id` int NOT NULL AUTO_INCREMENT,
  `gedcom_id` int NOT NULL,
  `chunk_data` mediumblob NOT NULL,
  `imported` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gedcom_chunk_id`),
  KEY `ix1` (`gedcom_id`,`imported`),
  CONSTRAINT `gedcom_chunk_ibfk_1` FOREIGN KEY (`gedcom_id`) REFERENCES `gedcom` (`gedcom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gedcom_setting` (
  `gedcom_id` int NOT NULL,
  `setting_name` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL,
  `setting_value` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`gedcom_id`,`setting_name`),
  CONSTRAINT `gedcom_setting_ibfk_1` FOREIGN KEY (`gedcom_id`) REFERENCES `gedcom` (`gedcom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `hit_counter` (
  `gedcom_id` int NOT NULL,
  `page_name` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL,
  `page_parameter` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL,
  `page_count` int NOT NULL,
  PRIMARY KEY (`gedcom_id`,`page_name`,`page_parameter`),
  CONSTRAINT `hit_counter_ibfk_1` FOREIGN KEY (`gedcom_id`) REFERENCES `gedcom` (`gedcom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `individuals` (
  `i_id` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `i_file` int NOT NULL,
  `i_rin` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `i_sex` enum('U','M','F') COLLATE utf8mb3_unicode_ci NOT NULL,
  `i_gedcom` mediumtext COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`i_id`,`i_file`),
  UNIQUE KEY `ux1` (`i_file`,`i_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ip_address` (
  `ip_address` varchar(40) COLLATE utf8mb3_unicode_ci NOT NULL,
  `category` enum('banned','search-engine','allowed') COLLATE utf8mb3_unicode_ci NOT NULL,
  `comment` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `link` (
  `l_file` int NOT NULL,
  `l_from` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `l_type` varchar(15) COLLATE utf8mb3_unicode_ci NOT NULL,
  `l_to` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`l_from`,`l_file`,`l_type`,`l_to`),
  UNIQUE KEY `ux1` (`l_to`,`l_file`,`l_type`,`l_from`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `log` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `log_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `log_type` enum('auth','config','debug','edit','error','media','search') COLLATE utf8mb3_unicode_ci NOT NULL,
  `log_message` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `ip_address` varchar(40) COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `gedcom_id` int DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `ix1` (`log_time`),
  KEY `ix2` (`log_type`),
  KEY `ix3` (`ip_address`),
  KEY `fk1` (`user_id`),
  KEY `fk2` (`gedcom_id`),
  CONSTRAINT `log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  CONSTRAINT `log_ibfk_2` FOREIGN KEY (`gedcom_id`) REFERENCES `gedcom` (`gedcom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `m_id` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `m_ext` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `m_type` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `m_titl` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `m_filename` varchar(512) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `m_file` int NOT NULL,
  `m_gedcom` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`m_file`,`m_id`),
  KEY `ix1` (`m_id`,`m_file`),
  KEY `ix2` (`m_ext`,`m_type`),
  KEY `ix3` (`m_titl`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `message` (
  `message_id` int NOT NULL AUTO_INCREMENT,
  `sender` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `ip_address` varchar(40) COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `subject` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`),
  KEY `fk1` (`user_id`),
  CONSTRAINT `message_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `module` (
  `module_name` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` enum('enabled','disabled') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'enabled',
  `tab_order` int DEFAULT NULL,
  `menu_order` int DEFAULT NULL,
  `sidebar_order` int DEFAULT NULL,
  PRIMARY KEY (`module_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `module_privacy` (
  `module_name` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL,
  `gedcom_id` int NOT NULL,
  `component` enum('block','chart','menu','report','sidebar','tab','theme') COLLATE utf8mb3_unicode_ci NOT NULL,
  `access_level` tinyint NOT NULL,
  PRIMARY KEY (`module_name`,`gedcom_id`,`component`),
  KEY `fk2` (`gedcom_id`),
  CONSTRAINT `module_privacy_ibfk_1` FOREIGN KEY (`module_name`) REFERENCES `module` (`module_name`),
  CONSTRAINT `module_privacy_ibfk_2` FOREIGN KEY (`gedcom_id`) REFERENCES `gedcom` (`gedcom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `module_setting` (
  `module_name` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL,
  `setting_name` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL,
  `setting_value` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`module_name`,`setting_name`),
  CONSTRAINT `module_setting_ibfk_1` FOREIGN KEY (`module_name`) REFERENCES `module` (`module_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `name` (
  `n_file` int NOT NULL,
  `n_id` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `n_num` int NOT NULL,
  `n_type` varchar(15) COLLATE utf8mb3_unicode_ci NOT NULL,
  `n_sort` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `n_full` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `n_surname` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `n_surn` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `n_givn` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `n_soundex_givn_std` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `n_soundex_surn_std` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `n_soundex_givn_dm` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `n_soundex_surn_dm` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`n_id`,`n_file`,`n_num`),
  KEY `ix1` (`n_full`,`n_id`,`n_file`),
  KEY `ix2` (`n_surn`,`n_file`,`n_type`,`n_id`),
  KEY `ix3` (`n_givn`,`n_file`,`n_type`,`n_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `next_id` (
  `gedcom_id` int NOT NULL,
  `record_type` varchar(15) COLLATE utf8mb3_unicode_ci NOT NULL,
  `next_id` decimal(20,0) NOT NULL,
  PRIMARY KEY (`gedcom_id`,`record_type`),
  CONSTRAINT `next_id_ibfk_1` FOREIGN KEY (`gedcom_id`) REFERENCES `gedcom` (`gedcom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `other` (
  `o_id` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `o_file` int NOT NULL,
  `o_type` varchar(15) COLLATE utf8mb3_unicode_ci NOT NULL,
  `o_gedcom` mediumtext COLLATE utf8mb3_unicode_ci,
  PRIMARY KEY (`o_id`,`o_file`),
  UNIQUE KEY `ux1` (`o_file`,`o_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `placelinks` (
  `pl_p_id` int NOT NULL,
  `pl_gid` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `pl_file` int NOT NULL,
  PRIMARY KEY (`pl_p_id`,`pl_gid`,`pl_file`),
  KEY `ix1` (`pl_p_id`),
  KEY `ix2` (`pl_gid`),
  KEY `ix3` (`pl_file`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `places` (
  `p_id` int NOT NULL AUTO_INCREMENT,
  `p_place` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `p_parent_id` int DEFAULT NULL,
  `p_file` int NOT NULL,
  `p_std_soundex` text COLLATE utf8mb3_unicode_ci,
  `p_dm_soundex` text COLLATE utf8mb3_unicode_ci,
  PRIMARY KEY (`p_id`),
  UNIQUE KEY `ux1` (`p_parent_id`,`p_file`,`p_place`),
  KEY `ix1` (`p_file`,`p_place`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `session` (
  `session_id` char(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `session_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int NOT NULL,
  `ip_address` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL,
  `session_data` mediumblob NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `ix1` (`session_time`),
  KEY `ix2` (`user_id`,`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_access_rule` (
  `site_access_rule_id` int NOT NULL AUTO_INCREMENT,
  `ip_address_start` int unsigned NOT NULL DEFAULT '0',
  `ip_address_end` int unsigned NOT NULL DEFAULT '4294967295',
  `user_agent_pattern` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `rule` enum('allow','deny','robot','unknown') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'unknown',
  `comment` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`site_access_rule_id`),
  UNIQUE KEY `ix1` (`user_agent_pattern`,`ip_address_start`,`ip_address_end`),
  KEY `ix2` (`ip_address_start`),
  KEY `ix3` (`ip_address_end`),
  KEY `ix4` (`rule`),
  KEY `ix5` (`user_agent_pattern`),
  KEY `ix6` (`updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_setting` (
  `setting_name` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL,
  `setting_value` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`setting_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sources` (
  `s_id` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `s_file` int NOT NULL,
  `s_name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `s_gedcom` mediumtext COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`s_id`,`s_file`),
  UNIQUE KEY `ux1` (`s_file`,`s_id`),
  KEY `ix1` (`s_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `user_name` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL,
  `real_name` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `password` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `ux1` (`user_name`),
  UNIQUE KEY `ux2` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_gedcom_setting` (
  `user_id` int NOT NULL,
  `gedcom_id` int NOT NULL,
  `setting_name` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL,
  `setting_value` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`,`gedcom_id`,`setting_name`),
  KEY `fk2` (`gedcom_id`),
  CONSTRAINT `user_gedcom_setting_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  CONSTRAINT `user_gedcom_setting_ibfk_2` FOREIGN KEY (`gedcom_id`) REFERENCES `gedcom` (`gedcom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_setting` (
  `user_id` int NOT NULL,
  `setting_name` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL,
  `setting_value` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`,`setting_name`),
  CONSTRAINT `user_setting_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
