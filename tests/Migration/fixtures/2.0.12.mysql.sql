/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_block` (
  `block_id` int NOT NULL AUTO_INCREMENT,
  `gedcom_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `xref` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` enum('main','side') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `block_order` int NOT NULL,
  `module_name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`block_id`),
  KEY `block_module_name_index` (`module_name`),
  KEY `block_gedcom_id_index` (`gedcom_id`),
  KEY `block_user_id_index` (`user_id`),
  CONSTRAINT `block_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`),
  CONSTRAINT `block_module_name_foreign` FOREIGN KEY (`module_name`) REFERENCES `wt_module` (`module_name`),
  CONSTRAINT `block_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `wt_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_block_setting` (
  `block_id` int NOT NULL,
  `setting_name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`block_id`,`setting_name`),
  CONSTRAINT `block_setting_block_id_foreign` FOREIGN KEY (`block_id`) REFERENCES `wt_block` (`block_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_change` (
  `change_id` int NOT NULL AUTO_INCREMENT,
  `change_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('accepted','pending','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `gedcom_id` int NOT NULL,
  `xref` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_gedcom` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_gedcom` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`change_id`),
  KEY `change_gedcom_id_status_xref_index` (`gedcom_id`,`status`,`xref`),
  KEY `change_user_id_index` (`user_id`),
  CONSTRAINT `change_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`),
  CONSTRAINT `change_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `wt_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_dates` (
  `d_day` tinyint NOT NULL,
  `d_month` char(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_mon` tinyint NOT NULL,
  `d_year` smallint NOT NULL,
  `d_julianday1` mediumint NOT NULL,
  `d_julianday2` mediumint NOT NULL,
  `d_fact` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `d_gid` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `d_file` int NOT NULL,
  `d_type` enum('@#DGREGORIAN@','@#DJULIAN@','@#DHEBREW@','@#DFRENCH R@','@#DHIJRI@','@#DROMAN@','@#DJALALI@') COLLATE utf8mb4_unicode_ci NOT NULL,
  KEY `dates_d_day_index` (`d_day`),
  KEY `dates_d_month_index` (`d_month`),
  KEY `dates_d_mon_index` (`d_mon`),
  KEY `dates_d_year_index` (`d_year`),
  KEY `dates_d_julianday1_index` (`d_julianday1`),
  KEY `dates_d_julianday2_index` (`d_julianday2`),
  KEY `dates_d_gid_index` (`d_gid`),
  KEY `dates_d_file_index` (`d_file`),
  KEY `dates_d_type_index` (`d_type`),
  KEY `dates_d_fact_d_gid_index` (`d_fact`,`d_gid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_default_resn` (
  `default_resn_id` int NOT NULL AUTO_INCREMENT,
  `gedcom_id` int NOT NULL,
  `xref` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tag_type` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resn` enum('none','privacy','confidential','hidden') COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`default_resn_id`),
  UNIQUE KEY `default_resn_gedcom_id_xref_tag_type_unique` (`gedcom_id`,`xref`,`tag_type`),
  CONSTRAINT `default_resn_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_families` (
  `f_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `f_file` int NOT NULL,
  `f_husb` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_wife` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_gedcom` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `f_numchil` int NOT NULL,
  PRIMARY KEY (`f_id`,`f_file`),
  UNIQUE KEY `families_f_file_f_id_unique` (`f_file`,`f_id`),
  KEY `families_f_husb_index` (`f_husb`),
  KEY `families_f_wife_index` (`f_wife`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_favorite` (
  `favorite_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `gedcom_id` int NOT NULL,
  `xref` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `favorite_type` enum('INDI','FAM','SOUR','REPO','OBJE','NOTE','URL') COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`favorite_id`),
  KEY `favorite_user_id_index` (`user_id`),
  KEY `favorite_gedcom_id_user_id_index` (`gedcom_id`,`user_id`),
  CONSTRAINT `favorite_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`) ON DELETE CASCADE,
  CONSTRAINT `favorite_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `wt_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_gedcom` (
  `gedcom_id` int NOT NULL AUTO_INCREMENT,
  `gedcom_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`gedcom_id`),
  UNIQUE KEY `gedcom_gedcom_name_unique` (`gedcom_name`),
  KEY `gedcom_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_gedcom_chunk` (
  `gedcom_chunk_id` int NOT NULL AUTO_INCREMENT,
  `gedcom_id` int NOT NULL,
  `chunk_data` longblob,
  `imported` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gedcom_chunk_id`),
  KEY `gedcom_chunk_gedcom_id_imported_index` (`gedcom_id`,`imported`),
  CONSTRAINT `gedcom_chunk_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_gedcom_setting` (
  `gedcom_id` int NOT NULL,
  `setting_name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`gedcom_id`,`setting_name`),
  CONSTRAINT `gedcom_setting_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_hit_counter` (
  `gedcom_id` int NOT NULL,
  `page_name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_parameter` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_count` int NOT NULL,
  PRIMARY KEY (`gedcom_id`,`page_name`,`page_parameter`),
  CONSTRAINT `hit_counter_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_individuals` (
  `i_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `i_file` int NOT NULL,
  `i_rin` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `i_sex` enum('U','M','F') COLLATE utf8mb4_unicode_ci NOT NULL,
  `i_gedcom` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`i_id`,`i_file`),
  UNIQUE KEY `individuals_i_file_i_id_unique` (`i_file`,`i_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_link` (
  `l_file` int NOT NULL,
  `l_from` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `l_type` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `l_to` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`l_from`,`l_file`,`l_type`,`l_to`),
  UNIQUE KEY `link_l_to_l_file_l_type_l_from_unique` (`l_to`,`l_file`,`l_type`,`l_from`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_log` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `log_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `log_type` enum('auth','config','debug','edit','error','media','search') COLLATE utf8mb4_unicode_ci NOT NULL,
  `log_message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `gedcom_id` int DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `log_log_time_index` (`log_time`),
  KEY `log_log_type_index` (`log_type`),
  KEY `log_ip_address_index` (`ip_address`),
  KEY `log_user_id_index` (`user_id`),
  KEY `log_gedcom_id_index` (`gedcom_id`),
  CONSTRAINT `log_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`),
  CONSTRAINT `log_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `wt_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_media` (
  `m_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `m_file` int NOT NULL,
  `m_gedcom` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`m_file`,`m_id`),
  UNIQUE KEY `media_m_id_m_file_unique` (`m_id`,`m_file`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_media_file` (
  `id` int NOT NULL AUTO_INCREMENT,
  `m_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `m_file` int NOT NULL,
  `multimedia_file_refn` varchar(248) COLLATE utf8mb4_unicode_ci NOT NULL,
  `multimedia_format` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_media_type` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descriptive_title` varchar(248) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `media_file_m_id_m_file_index` (`m_id`,`m_file`),
  KEY `media_file_m_file_m_id_index` (`m_file`,`m_id`),
  KEY `media_file_m_file_multimedia_file_refn_index` (`m_file`,`multimedia_file_refn`),
  KEY `media_file_m_file_multimedia_format_index` (`m_file`,`multimedia_format`),
  KEY `media_file_m_file_source_media_type_index` (`m_file`,`source_media_type`),
  KEY `media_file_m_file_descriptive_title_index` (`m_file`,`descriptive_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_message` (
  `message_id` int NOT NULL AUTO_INCREMENT,
  `sender` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`),
  KEY `message_user_id_index` (`user_id`),
  CONSTRAINT `message_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `wt_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_module` (
  `module_name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('enabled','disabled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'enabled',
  `tab_order` int DEFAULT NULL,
  `menu_order` int DEFAULT NULL,
  `sidebar_order` int DEFAULT NULL,
  `footer_order` int DEFAULT NULL,
  PRIMARY KEY (`module_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_module_privacy` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `module_name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gedcom_id` int NOT NULL,
  `interface` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_level` tinyint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wt_module_privacy_ix1` (`gedcom_id`,`module_name`,`interface`),
  UNIQUE KEY `wt_module_privacy_ix2` (`module_name`,`gedcom_id`,`interface`),
  CONSTRAINT `module_privacy_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`) ON DELETE CASCADE,
  CONSTRAINT `module_privacy_module_name_foreign` FOREIGN KEY (`module_name`) REFERENCES `wt_module` (`module_name`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_module_setting` (
  `module_name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`module_name`,`setting_name`),
  CONSTRAINT `module_setting_module_name_foreign` FOREIGN KEY (`module_name`) REFERENCES `wt_module` (`module_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_name` (
  `n_file` int NOT NULL,
  `n_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `n_num` int NOT NULL,
  `n_type` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `n_sort` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `n_full` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `n_surname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_surn` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_givn` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_soundex_givn_std` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_soundex_surn_std` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_soundex_givn_dm` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_soundex_surn_dm` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`n_id`,`n_file`,`n_num`),
  KEY `name_n_full_n_id_n_file_index` (`n_full`,`n_id`,`n_file`),
  KEY `name_n_surn_n_file_n_type_n_id_index` (`n_surn`,`n_file`,`n_type`,`n_id`),
  KEY `name_n_givn_n_file_n_type_n_id_index` (`n_givn`,`n_file`,`n_type`,`n_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_news` (
  `news_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `gedcom_id` int DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`news_id`),
  KEY `news_user_id_updated_index` (`user_id`,`updated`),
  KEY `news_gedcom_id_updated_index` (`gedcom_id`,`updated`),
  CONSTRAINT `news_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`) ON DELETE CASCADE,
  CONSTRAINT `news_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `wt_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_other` (
  `o_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `o_file` int NOT NULL,
  `o_type` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `o_gedcom` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`o_id`,`o_file`),
  UNIQUE KEY `other_o_file_o_id_unique` (`o_file`,`o_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_place_location` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int DEFAULT NULL,
  `place` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `place_location_parent_id_place_unique` (`parent_id`,`place`),
  UNIQUE KEY `place_location_place_parent_id_unique` (`place`,`parent_id`),
  KEY `place_location_latitude_index` (`latitude`),
  KEY `place_location_longitude_index` (`longitude`),
  CONSTRAINT `place_location_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `wt_place_location` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_placelinks` (
  `pl_p_id` int NOT NULL,
  `pl_gid` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pl_file` int NOT NULL,
  PRIMARY KEY (`pl_p_id`,`pl_gid`,`pl_file`),
  KEY `placelinks_pl_p_id_index` (`pl_p_id`),
  KEY `placelinks_pl_gid_index` (`pl_gid`),
  KEY `placelinks_pl_file_index` (`pl_file`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_places` (
  `p_id` int NOT NULL AUTO_INCREMENT,
  `p_place` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `p_parent_id` int DEFAULT NULL,
  `p_file` int NOT NULL,
  `p_std_soundex` longtext COLLATE utf8mb4_unicode_ci,
  `p_dm_soundex` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`p_id`),
  UNIQUE KEY `places_p_parent_id_p_file_p_place_unique` (`p_parent_id`,`p_file`,`p_place`),
  KEY `places_p_file_p_place_index` (`p_file`,`p_place`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_session` (
  `session_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_data` longblob,
  PRIMARY KEY (`session_id`),
  KEY `session_session_time_index` (`session_time`),
  KEY `session_user_id_ip_address_index` (`user_id`,`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_site_setting` (
  `setting_name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` varchar(2000) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`setting_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_sources` (
  `s_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `s_file` int NOT NULL,
  `s_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `s_gedcom` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`s_id`,`s_file`),
  UNIQUE KEY `sources_s_file_s_id_unique` (`s_file`,`s_id`),
  KEY `sources_s_name_index` (`s_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_user` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `user_name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `real_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_user_name_unique` (`user_name`),
  UNIQUE KEY `user_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_user_gedcom_setting` (
  `user_id` int NOT NULL,
  `gedcom_id` int NOT NULL,
  `setting_name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`,`gedcom_id`,`setting_name`),
  KEY `user_gedcom_setting_gedcom_id_index` (`gedcom_id`),
  CONSTRAINT `user_gedcom_setting_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`),
  CONSTRAINT `user_gedcom_setting_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `wt_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_user_setting` (
  `user_id` int NOT NULL,
  `setting_name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`,`setting_name`),
  CONSTRAINT `user_setting_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `wt_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;
