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
  `block_id` int(11) NOT NULL AUTO_INCREMENT,
  `gedcom_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `xref` varchar(20) DEFAULT NULL,
  `location` enum('main','side') DEFAULT NULL,
  `block_order` int(11) NOT NULL,
  `module_name` varchar(32) NOT NULL,
  PRIMARY KEY (`block_id`),
  KEY `wt_block_module_name_index` (`module_name`),
  KEY `wt_block_gedcom_id_index` (`gedcom_id`),
  KEY `wt_block_user_id_index` (`user_id`),
  CONSTRAINT `wt_block_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`),
  CONSTRAINT `wt_block_module_name_foreign` FOREIGN KEY (`module_name`) REFERENCES `wt_module` (`module_name`),
  CONSTRAINT `wt_block_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `wt_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_block_setting` (
  `block_id` int(11) NOT NULL,
  `setting_name` varchar(32) NOT NULL,
  `setting_value` longtext NOT NULL,
  PRIMARY KEY (`block_id`,`setting_name`),
  CONSTRAINT `wt_block_setting_block_id_foreign` FOREIGN KEY (`block_id`) REFERENCES `wt_block` (`block_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_change` (
  `change_id` int(11) NOT NULL AUTO_INCREMENT,
  `change_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('accepted','pending','rejected') NOT NULL DEFAULT 'pending',
  `gedcom_id` int(11) NOT NULL,
  `xref` varchar(20) NOT NULL,
  `old_gedcom` longtext NOT NULL,
  `new_gedcom` longtext NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`change_id`),
  KEY `wt_change_gedcom_id_status_xref_index` (`gedcom_id`,`status`,`xref`),
  KEY `wt_change_user_id_index` (`user_id`),
  CONSTRAINT `wt_change_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`),
  CONSTRAINT `wt_change_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `wt_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_dates` (
  `d_day` tinyint(4) NOT NULL,
  `d_month` char(5) DEFAULT NULL,
  `d_mon` tinyint(4) NOT NULL,
  `d_year` smallint(6) NOT NULL,
  `d_julianday1` mediumint(9) NOT NULL,
  `d_julianday2` mediumint(9) NOT NULL,
  `d_fact` varchar(15) NOT NULL,
  `d_gid` varchar(20) NOT NULL,
  `d_file` int(11) NOT NULL,
  `d_type` enum('@#DGREGORIAN@','@#DJULIAN@','@#DHEBREW@','@#DFRENCH R@','@#DHIJRI@','@#DROMAN@','@#DJALALI@') NOT NULL,
  KEY `wt_dates_d_day_index` (`d_day`),
  KEY `wt_dates_d_month_index` (`d_month`),
  KEY `wt_dates_d_mon_index` (`d_mon`),
  KEY `wt_dates_d_year_index` (`d_year`),
  KEY `wt_dates_d_julianday1_index` (`d_julianday1`),
  KEY `wt_dates_d_julianday2_index` (`d_julianday2`),
  KEY `wt_dates_d_gid_index` (`d_gid`),
  KEY `wt_dates_d_file_index` (`d_file`),
  KEY `wt_dates_d_type_index` (`d_type`),
  KEY `wt_dates_d_fact_d_gid_index` (`d_fact`,`d_gid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_default_resn` (
  `default_resn_id` int(11) NOT NULL AUTO_INCREMENT,
  `gedcom_id` int(11) NOT NULL,
  `xref` varchar(20) DEFAULT NULL,
  `tag_type` varchar(15) DEFAULT NULL,
  `resn` enum('none','privacy','confidential','hidden') NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`default_resn_id`),
  UNIQUE KEY `wt_default_resn_gedcom_id_xref_tag_type_unique` (`gedcom_id`,`xref`,`tag_type`),
  CONSTRAINT `wt_default_resn_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_families` (
  `f_id` varchar(20) NOT NULL,
  `f_file` int(11) NOT NULL,
  `f_husb` varchar(20) DEFAULT NULL,
  `f_wife` varchar(20) DEFAULT NULL,
  `f_gedcom` longtext NOT NULL,
  `f_numchil` int(11) NOT NULL,
  PRIMARY KEY (`f_id`,`f_file`),
  UNIQUE KEY `wt_families_f_file_f_id_unique` (`f_file`,`f_id`),
  KEY `wt_families_f_husb_index` (`f_husb`),
  KEY `wt_families_f_wife_index` (`f_wife`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_favorite` (
  `favorite_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `gedcom_id` int(11) NOT NULL,
  `xref` varchar(20) DEFAULT NULL,
  `favorite_type` enum('INDI','FAM','SOUR','REPO','OBJE','NOTE','URL') NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `note` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`favorite_id`),
  KEY `wt_favorite_user_id_index` (`user_id`),
  KEY `wt_favorite_gedcom_id_user_id_index` (`gedcom_id`,`user_id`),
  CONSTRAINT `wt_favorite_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`) ON DELETE CASCADE,
  CONSTRAINT `wt_favorite_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `wt_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_gedcom` (
  `gedcom_id` int(11) NOT NULL AUTO_INCREMENT,
  `gedcom_name` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `media_folder` varchar(255) NOT NULL DEFAULT 'media/',
  `title` varchar(255) NOT NULL DEFAULT 'tree',
  `gedcom_filename` varchar(255) NOT NULL DEFAULT 'tree.ged',
  `imported` int(11) NOT NULL DEFAULT 1,
  `private` int(11) NOT NULL DEFAULT 0,
  `contact_user_id` int(11) DEFAULT NULL,
  `support_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`gedcom_id`),
  UNIQUE KEY `wt_gedcom_gedcom_name_unique` (`gedcom_name`),
  KEY `wt_gedcom_sort_order_index` (`sort_order`),
  KEY `wt_gedcom_media_folder_index` (`media_folder`),
  KEY `wt_gedcom_title_index` (`title`),
  KEY `wt_gedcom_gedcom_filename_index` (`gedcom_filename`),
  KEY `wt_gedcom_imported_index` (`imported`),
  KEY `wt_gedcom_private_index` (`private`),
  KEY `wt_gedcom_contact_user_id_index` (`contact_user_id`),
  KEY `wt_gedcom_support_user_id_index` (`support_user_id`),
  CONSTRAINT `wt_gedcom_contact_user_id_foreign` FOREIGN KEY (`contact_user_id`) REFERENCES `wt_user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `wt_gedcom_support_user_id_foreign` FOREIGN KEY (`support_user_id`) REFERENCES `wt_user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_gedcom_chunk` (
  `gedcom_chunk_id` int(11) NOT NULL AUTO_INCREMENT,
  `gedcom_id` int(11) NOT NULL,
  `chunk_data` longtext NOT NULL,
  `imported` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`gedcom_chunk_id`),
  KEY `wt_gedcom_chunk_gedcom_id_imported_index` (`gedcom_id`,`imported`),
  CONSTRAINT `wt_gedcom_chunk_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_gedcom_setting` (
  `gedcom_id` int(11) NOT NULL,
  `setting_name` varchar(32) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  PRIMARY KEY (`gedcom_id`,`setting_name`),
  CONSTRAINT `wt_gedcom_setting_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_hit_counter` (
  `gedcom_id` int(11) NOT NULL,
  `page_name` varchar(32) NOT NULL,
  `page_parameter` varchar(32) NOT NULL,
  `page_count` int(11) NOT NULL,
  PRIMARY KEY (`gedcom_id`,`page_name`,`page_parameter`),
  CONSTRAINT `wt_hit_counter_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_individuals` (
  `i_id` varchar(20) NOT NULL,
  `i_file` int(11) NOT NULL,
  `i_rin` varchar(20) NOT NULL,
  `i_sex` enum('U','M','F') NOT NULL,
  `i_gedcom` longtext NOT NULL,
  PRIMARY KEY (`i_id`,`i_file`),
  UNIQUE KEY `wt_individuals_i_file_i_id_unique` (`i_file`,`i_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_link` (
  `l_file` int(11) NOT NULL,
  `l_from` varchar(20) NOT NULL,
  `l_type` varchar(15) NOT NULL,
  `l_to` varchar(20) NOT NULL,
  PRIMARY KEY (`l_from`,`l_file`,`l_type`,`l_to`),
  UNIQUE KEY `wt_link_l_to_l_file_l_type_l_from_unique` (`l_to`,`l_file`,`l_type`,`l_from`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `log_type` enum('auth','config','debug','edit','error','media','search') NOT NULL,
  `log_message` longtext NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `gedcom_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `wt_log_log_time_index` (`log_time`),
  KEY `wt_log_log_type_index` (`log_type`),
  KEY `wt_log_ip_address_index` (`ip_address`),
  KEY `wt_log_user_id_index` (`user_id`),
  KEY `wt_log_gedcom_id_index` (`gedcom_id`),
  CONSTRAINT `wt_log_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`),
  CONSTRAINT `wt_log_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `wt_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_media` (
  `m_id` varchar(20) NOT NULL,
  `m_file` int(11) NOT NULL,
  `m_gedcom` longtext DEFAULT NULL,
  PRIMARY KEY (`m_file`,`m_id`),
  UNIQUE KEY `wt_media_m_id_m_file_unique` (`m_id`,`m_file`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_media_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `m_id` varchar(20) NOT NULL,
  `m_file` int(11) NOT NULL,
  `multimedia_file_refn` varchar(248) NOT NULL,
  `multimedia_format` varchar(4) NOT NULL,
  `source_media_type` varchar(15) NOT NULL,
  `descriptive_title` varchar(248) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `wt_media_file_m_id_m_file_index` (`m_id`,`m_file`),
  KEY `wt_media_file_m_file_m_id_index` (`m_file`,`m_id`),
  KEY `wt_media_file_m_file_multimedia_file_refn_index` (`m_file`,`multimedia_file_refn`),
  KEY `wt_media_file_m_file_multimedia_format_index` (`m_file`,`multimedia_format`),
  KEY `wt_media_file_m_file_source_media_type_index` (`m_file`,`source_media_type`),
  KEY `wt_media_file_m_file_descriptive_title_index` (`m_file`,`descriptive_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_message` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `sender` varchar(64) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`message_id`),
  KEY `wt_message_user_id_index` (`user_id`),
  CONSTRAINT `wt_message_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `wt_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_module` (
  `module_name` varchar(32) NOT NULL,
  `status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `tab_order` int(11) DEFAULT NULL,
  `menu_order` int(11) DEFAULT NULL,
  `sidebar_order` int(11) DEFAULT NULL,
  `footer_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`module_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_module_privacy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module_name` varchar(32) NOT NULL,
  `gedcom_id` int(11) NOT NULL,
  `interface` varchar(255) NOT NULL,
  `access_level` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wt_module_privacy_ix1` (`gedcom_id`,`module_name`,`interface`),
  UNIQUE KEY `wt_module_privacy_ix2` (`module_name`,`gedcom_id`,`interface`),
  CONSTRAINT `wt_module_privacy_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`) ON DELETE CASCADE,
  CONSTRAINT `wt_module_privacy_module_name_foreign` FOREIGN KEY (`module_name`) REFERENCES `wt_module` (`module_name`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_module_setting` (
  `module_name` varchar(32) NOT NULL,
  `setting_name` varchar(32) NOT NULL,
  `setting_value` longtext NOT NULL,
  PRIMARY KEY (`module_name`,`setting_name`),
  CONSTRAINT `wt_module_setting_module_name_foreign` FOREIGN KEY (`module_name`) REFERENCES `wt_module` (`module_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_name` (
  `n_file` int(11) NOT NULL,
  `n_id` varchar(20) NOT NULL,
  `n_num` int(11) NOT NULL,
  `n_type` varchar(15) NOT NULL,
  `n_sort` varchar(255) NOT NULL,
  `n_full` varchar(255) NOT NULL,
  `n_surname` varchar(255) DEFAULT NULL,
  `n_surn` varchar(255) DEFAULT NULL,
  `n_givn` varchar(255) DEFAULT NULL,
  `n_soundex_givn_std` varchar(255) DEFAULT NULL,
  `n_soundex_surn_std` varchar(255) DEFAULT NULL,
  `n_soundex_givn_dm` varchar(255) DEFAULT NULL,
  `n_soundex_surn_dm` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`n_id`,`n_file`,`n_num`),
  KEY `wt_name_n_full_n_id_n_file_index` (`n_full`,`n_id`,`n_file`),
  KEY `wt_name_n_surn_n_file_n_type_n_id_index` (`n_surn`,`n_file`,`n_type`,`n_id`),
  KEY `wt_name_n_givn_n_file_n_type_n_id_index` (`n_givn`,`n_file`,`n_type`,`n_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_news` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `gedcom_id` int(11) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`news_id`),
  KEY `wt_news_user_id_updated_index` (`user_id`,`updated`),
  KEY `wt_news_gedcom_id_updated_index` (`gedcom_id`,`updated`),
  CONSTRAINT `wt_news_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`) ON DELETE CASCADE,
  CONSTRAINT `wt_news_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `wt_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_other` (
  `o_id` varchar(20) NOT NULL,
  `o_file` int(11) NOT NULL,
  `o_type` varchar(15) NOT NULL,
  `o_gedcom` longtext NOT NULL,
  PRIMARY KEY (`o_id`,`o_file`),
  UNIQUE KEY `wt_other_o_file_o_id_unique` (`o_file`,`o_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_place_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `place` varchar(120) NOT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wt_place_location_parent_id_place_unique` (`parent_id`,`place`),
  UNIQUE KEY `wt_place_location_place_parent_id_unique` (`place`,`parent_id`),
  KEY `wt_place_location_latitude_index` (`latitude`),
  KEY `wt_place_location_longitude_index` (`longitude`),
  CONSTRAINT `wt_place_location_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `wt_place_location` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_placelinks` (
  `pl_p_id` int(11) NOT NULL,
  `pl_gid` varchar(20) NOT NULL,
  `pl_file` int(11) NOT NULL,
  PRIMARY KEY (`pl_p_id`,`pl_gid`,`pl_file`),
  KEY `wt_placelinks_pl_p_id_index` (`pl_p_id`),
  KEY `wt_placelinks_pl_gid_index` (`pl_gid`),
  KEY `wt_placelinks_pl_file_index` (`pl_file`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_places` (
  `p_id` int(11) NOT NULL AUTO_INCREMENT,
  `p_place` varchar(150) NOT NULL,
  `p_parent_id` int(11) DEFAULT NULL,
  `p_file` int(11) NOT NULL,
  `p_std_soundex` longtext DEFAULT NULL,
  `p_dm_soundex` longtext DEFAULT NULL,
  PRIMARY KEY (`p_id`),
  UNIQUE KEY `wt_places_p_parent_id_p_file_p_place_unique` (`p_parent_id`,`p_file`,`p_place`),
  KEY `wt_places_p_file_p_place_index` (`p_file`,`p_place`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_session` (
  `session_id` varchar(256) NOT NULL,
  `session_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `session_data` longtext NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `wt_session_session_time_index` (`session_time`),
  KEY `wt_session_user_id_ip_address_index` (`user_id`,`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_site_setting` (
  `setting_name` varchar(32) NOT NULL,
  `setting_value` varchar(2000) NOT NULL,
  PRIMARY KEY (`setting_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_sources` (
  `s_id` varchar(20) NOT NULL,
  `s_file` int(11) NOT NULL,
  `s_name` varchar(255) NOT NULL,
  `s_gedcom` longtext NOT NULL,
  PRIMARY KEY (`s_id`,`s_file`),
  UNIQUE KEY `wt_sources_s_file_s_id_unique` (`s_file`,`s_id`),
  KEY `wt_sources_s_name_index` (`s_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(32) NOT NULL,
  `real_name` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `password` varchar(128) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `wt_user_user_name_unique` (`user_name`),
  UNIQUE KEY `wt_user_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_user_gedcom_setting` (
  `user_id` int(11) NOT NULL,
  `gedcom_id` int(11) NOT NULL,
  `setting_name` varchar(32) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`,`gedcom_id`,`setting_name`),
  KEY `wt_user_gedcom_setting_gedcom_id_index` (`gedcom_id`),
  CONSTRAINT `wt_user_gedcom_setting_gedcom_id_foreign` FOREIGN KEY (`gedcom_id`) REFERENCES `wt_gedcom` (`gedcom_id`),
  CONSTRAINT `wt_user_gedcom_setting_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `wt_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wt_user_setting` (
  `user_id` int(11) NOT NULL,
  `setting_name` varchar(32) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`,`setting_name`),
  CONSTRAINT `wt_user_setting_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `wt_user` (`user_id`)
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
