SET QUOTED_IDENTIFIER ON;
GO
create table "wt_gedcom" ("gedcom_id" int not null identity primary key, "gedcom_name" nvarchar(255) not null, "sort_order" int not null default '0');
GO
create unique index "gedcom_gedcom_name_unique" on "wt_gedcom" ("gedcom_name");
GO
create index "gedcom_sort_order_index" on "wt_gedcom" ("sort_order");
GO
create table "wt_site_setting" ("setting_name" nvarchar(32) not null, "setting_value" nvarchar(2000) not null);
GO
alter table "wt_site_setting" add constraint "site_setting_setting_name_primary" primary key ("setting_name");
GO
create table "wt_gedcom_setting" ("gedcom_id" int not null, "setting_name" nvarchar(32) not null, "setting_value" nvarchar(255) not null);
GO
alter table "wt_gedcom_setting" add constraint "gedcom_setting_gedcom_id_setting_name_primary" primary key ("gedcom_id", "setting_name");
GO
alter table "wt_gedcom_setting" add constraint "gedcom_setting_gedcom_id_foreign" foreign key ("gedcom_id") references "wt_gedcom" ("gedcom_id");
GO
create table "wt_user" ("user_id" int not null identity primary key, "user_name" nvarchar(32) not null, "real_name" nvarchar(64) not null, "email" nvarchar(64) not null, "password" nvarchar(128) not null);
GO
create unique index "user_user_name_unique" on "wt_user" ("user_name");
GO
create unique index "user_email_unique" on "wt_user" ("email");
GO
create table "wt_user_setting" ("user_id" int not null, "setting_name" nvarchar(32) not null, "setting_value" nvarchar(255) not null);
GO
alter table "wt_user_setting" add constraint "user_setting_user_id_setting_name_primary" primary key ("user_id", "setting_name");
GO
alter table "wt_user_setting" add constraint "user_setting_user_id_foreign" foreign key ("user_id") references "wt_user" ("user_id");
GO
create table "wt_user_gedcom_setting" ("user_id" int not null, "gedcom_id" int not null, "setting_name" nvarchar(32) not null, "setting_value" nvarchar(255) not null);
GO
alter table "wt_user_gedcom_setting" add constraint "wt_user_gedcom_setting_primary" primary key ("user_id", "gedcom_id", "setting_name");
GO
create index "user_gedcom_setting_gedcom_id_index" on "wt_user_gedcom_setting" ("gedcom_id");
GO
alter table "wt_user_gedcom_setting" add constraint "user_gedcom_setting_user_id_foreign" foreign key ("user_id") references "wt_user" ("user_id");
GO
alter table "wt_user_gedcom_setting" add constraint "user_gedcom_setting_gedcom_id_foreign" foreign key ("gedcom_id") references "wt_gedcom" ("gedcom_id");
GO
create table "wt_log" ("log_id" int not null identity primary key, "log_time" datetime not null default CURRENT_TIMESTAMP, "log_type" nvarchar(255) check ("log_type" in (N'auth', N'config', N'debug', N'edit', N'error', N'media', N'search')) not null, "log_message" nvarchar(max) not null, "ip_address" nvarchar(45) not null, "user_id" int null, "gedcom_id" int null);
GO
create index "log_log_time_index" on "wt_log" ("log_time");
GO
create index "log_log_type_index" on "wt_log" ("log_type");
GO
create index "log_ip_address_index" on "wt_log" ("ip_address");
GO
create index "log_user_id_index" on "wt_log" ("user_id");
GO
create index "log_gedcom_id_index" on "wt_log" ("gedcom_id");
GO
alter table "wt_log" add constraint "log_user_id_foreign" foreign key ("user_id") references "wt_user" ("user_id");
GO
alter table "wt_log" add constraint "log_gedcom_id_foreign" foreign key ("gedcom_id") references "wt_gedcom" ("gedcom_id");
GO
create table "wt_change" ("change_id" int not null identity primary key, "change_time" datetime not null default CURRENT_TIMESTAMP, "status" nvarchar(255) check ("status" in (N'accepted', N'pending', N'rejected')) not null default 'pending', "gedcom_id" int not null, "xref" nvarchar(20) not null, "old_gedcom" nvarchar(max) not null, "new_gedcom" nvarchar(max) not null, "user_id" int not null);
GO
create index "change_gedcom_id_status_xref_index" on "wt_change" ("gedcom_id", "status", "xref");
GO
create index "change_user_id_index" on "wt_change" ("user_id");
GO
alter table "wt_change" add constraint "change_user_id_foreign" foreign key ("user_id") references "wt_user" ("user_id");
GO
alter table "wt_change" add constraint "change_gedcom_id_foreign" foreign key ("gedcom_id") references "wt_gedcom" ("gedcom_id");
GO
create table "wt_message" ("message_id" int not null identity primary key, "sender" nvarchar(64) not null, "ip_address" nvarchar(45) not null, "user_id" int not null, "subject" nvarchar(255) not null, "body" nvarchar(max) not null, "created" datetime not null default CURRENT_TIMESTAMP);
GO
create index "message_user_id_index" on "wt_message" ("user_id");
GO
alter table "wt_message" add constraint "message_user_id_foreign" foreign key ("user_id") references "wt_user" ("user_id");
GO
create table "wt_default_resn" ("default_resn_id" int not null identity primary key, "gedcom_id" int not null, "xref" nvarchar(20) null, "tag_type" nvarchar(15) null, "resn" nvarchar(255) check ("resn" in (N'none', N'privacy', N'confidential', N'hidden')) not null, "comment" nvarchar(255) null, "updated" datetime not null default CURRENT_TIMESTAMP);
GO
create unique index "default_resn_gedcom_id_xref_tag_type_unique" on "wt_default_resn" ("gedcom_id", "xref", "tag_type");
GO
alter table "wt_default_resn" add constraint "default_resn_gedcom_id_foreign" foreign key ("gedcom_id") references "wt_gedcom" ("gedcom_id");
GO
create table "wt_individuals" ("i_id" nvarchar(20) not null, "i_file" int not null, "i_rin" nvarchar(20) not null, "i_sex" nvarchar(255) check ("i_sex" in (N'U', N'M', N'F')) not null, "i_gedcom" nvarchar(max) not null);
GO
alter table "wt_individuals" add constraint "individuals_i_id_i_file_primary" primary key ("i_id", "i_file");
GO
create unique index "individuals_i_file_i_id_unique" on "wt_individuals" ("i_file", "i_id");
GO
create table "wt_families" ("f_id" nvarchar(20) not null, "f_file" int not null, "f_husb" nvarchar(20) null, "f_wife" nvarchar(20) null, "f_gedcom" nvarchar(max) not null, "f_numchil" int not null);
GO
alter table "wt_families" add constraint "families_f_id_f_file_primary" primary key ("f_id", "f_file");
GO
create unique index "families_f_file_f_id_unique" on "wt_families" ("f_file", "f_id");
GO
create index "families_f_husb_index" on "wt_families" ("f_husb");
GO
create index "families_f_wife_index" on "wt_families" ("f_wife");
GO
create table "wt_places" ("p_id" int not null identity primary key, "p_place" nvarchar(150) not null, "p_parent_id" int null, "p_file" int not null, "p_std_soundex" nvarchar(max) null, "p_dm_soundex" nvarchar(max) null);
GO
create index "places_p_file_p_place_index" on "wt_places" ("p_file", "p_place");
GO
create unique index "places_p_parent_id_p_file_p_place_unique" on "wt_places" ("p_parent_id", "p_file", "p_place");
GO
create table "wt_placelinks" ("pl_p_id" int not null, "pl_gid" nvarchar(20) not null, "pl_file" int not null);
GO
alter table "wt_placelinks" add constraint "placelinks_pl_p_id_pl_gid_pl_file_primary" primary key ("pl_p_id", "pl_gid", "pl_file");
GO
create index "placelinks_pl_p_id_index" on "wt_placelinks" ("pl_p_id");
GO
create index "placelinks_pl_gid_index" on "wt_placelinks" ("pl_gid");
GO
create index "placelinks_pl_file_index" on "wt_placelinks" ("pl_file");
GO
create table "wt_dates" ("d_day" tinyint not null, "d_month" nchar(5) null, "d_mon" tinyint not null, "d_year" smallint not null, "d_julianday1" int not null, "d_julianday2" int not null, "d_fact" nvarchar(15) not null, "d_gid" nvarchar(20) not null, "d_file" int not null, "d_type" nvarchar(255) check ("d_type" in (N'@#DGREGORIAN@', N'@#DJULIAN@', N'@#DHEBREW@', N'@#DFRENCH R@', N'@#DHIJRI@', N'@#DROMAN@', N'@#DJALALI@')) not null);
GO
create index "dates_d_day_index" on "wt_dates" ("d_day");
GO
create index "dates_d_month_index" on "wt_dates" ("d_month");
GO
create index "dates_d_mon_index" on "wt_dates" ("d_mon");
GO
create index "dates_d_year_index" on "wt_dates" ("d_year");
GO
create index "dates_d_julianday1_index" on "wt_dates" ("d_julianday1");
GO
create index "dates_d_julianday2_index" on "wt_dates" ("d_julianday2");
GO
create index "dates_d_gid_index" on "wt_dates" ("d_gid");
GO
create index "dates_d_file_index" on "wt_dates" ("d_file");
GO
create index "dates_d_type_index" on "wt_dates" ("d_type");
GO
create index "dates_d_fact_d_gid_index" on "wt_dates" ("d_fact", "d_gid");
GO
create table "wt_media" ("m_id" nvarchar(20) not null, "m_ext" nvarchar(6) null, "m_type" nvarchar(20) null, "m_titl" nvarchar(248) null, "m_filename" nvarchar(248) null, "m_file" int not null, "m_gedcom" nvarchar(max) null);
GO
alter table "wt_media" add constraint "media_m_file_m_id_primary" primary key ("m_file", "m_id");
GO
create unique index "media_m_id_m_file_unique" on "wt_media" ("m_id", "m_file");
GO
create table "wt_next_id" ("gedcom_id" int not null, "record_type" nvarchar(15) not null, "next_id" decimal(20, 0) not null);
GO
alter table "wt_next_id" add constraint "next_id_gedcom_id_record_type_primary" primary key ("gedcom_id", "record_type");
GO
alter table "wt_next_id" add constraint "next_id_gedcom_id_foreign" foreign key ("gedcom_id") references "wt_gedcom" ("gedcom_id");
GO
create table "wt_other" ("o_id" nvarchar(20) not null, "o_file" int not null, "o_type" nvarchar(15) not null, "o_gedcom" nvarchar(max) not null);
GO
alter table "wt_other" add constraint "other_o_id_o_file_primary" primary key ("o_id", "o_file");
GO
create unique index "other_o_file_o_id_unique" on "wt_other" ("o_file", "o_id");
GO
create table "wt_sources" ("s_id" nvarchar(20) not null, "s_file" int not null, "s_name" nvarchar(255) not null, "s_gedcom" nvarchar(max) not null);
GO
alter table "wt_sources" add constraint "sources_s_id_s_file_primary" primary key ("s_id", "s_file");
GO
create unique index "sources_s_file_s_id_unique" on "wt_sources" ("s_file", "s_id");
GO
create index "sources_s_name_index" on "wt_sources" ("s_name");
GO
create table "wt_link" ("l_file" int not null, "l_from" nvarchar(20) not null, "l_type" nvarchar(15) not null, "l_to" nvarchar(20) not null);
GO
alter table "wt_link" add constraint "link_l_from_l_file_l_type_l_to_primary" primary key ("l_from", "l_file", "l_type", "l_to");
GO
create unique index "link_l_to_l_file_l_type_l_from_unique" on "wt_link" ("l_to", "l_file", "l_type", "l_from");
GO
create table "wt_name" ("n_file" int not null, "n_id" nvarchar(20) not null, "n_num" int not null, "n_type" nvarchar(15) not null, "n_sort" nvarchar(255) not null, "n_full" nvarchar(255) not null, "n_surname" nvarchar(255) null, "n_surn" nvarchar(255) null, "n_givn" nvarchar(255) null, "n_soundex_givn_std" nvarchar(255) null, "n_soundex_surn_std" nvarchar(255) null, "n_soundex_givn_dm" nvarchar(255) null, "n_soundex_surn_dm" nvarchar(255) null);
GO
alter table "wt_name" add constraint "name_n_id_n_file_n_num_primary" primary key ("n_id", "n_file", "n_num");
GO
create index "name_n_full_n_id_n_file_index" on "wt_name" ("n_full", "n_id", "n_file");
GO
create index "name_n_surn_n_file_n_type_n_id_index" on "wt_name" ("n_surn", "n_file", "n_type", "n_id");
GO
create index "name_n_givn_n_file_n_type_n_id_index" on "wt_name" ("n_givn", "n_file", "n_type", "n_id");
GO
create table "wt_module" ("module_name" nvarchar(32) not null, "status" nvarchar(255) check ("status" in (N'enabled', N'disabled')) not null default 'enabled', "tab_order" int null, "menu_order" int null, "sidebar_order" int null);
GO
alter table "wt_module" add constraint "module_module_name_primary" primary key ("module_name");
GO
create table "wt_module_setting" ("module_name" nvarchar(32) not null, "setting_name" nvarchar(32) not null, "setting_value" nvarchar(max) not null);
GO
alter table "wt_module_setting" add constraint "module_setting_module_name_setting_name_primary" primary key ("module_name", "setting_name");
GO
alter table "wt_module_setting" add constraint "module_setting_module_name_foreign" foreign key ("module_name") references "wt_module" ("module_name");
GO
create table "wt_module_privacy" ("module_name" nvarchar(32) not null, "gedcom_id" int not null, "component" nvarchar(255) check ("component" in (N'block', N'chart', N'menu', N'report', N'sidebar', N'tab', N'theme')) not null, "access_level" tinyint not null);
GO
alter table "wt_module_privacy" add constraint "wt_module_privacy_primary" primary key ("module_name", "gedcom_id", "component");
GO
create unique index "wt_module_privacy_ix1" on "wt_module_privacy" ("gedcom_id", "module_name", "component");
GO
alter table "wt_module_privacy" add constraint "module_privacy_module_name_foreign" foreign key ("module_name") references "wt_module" ("module_name");
GO
alter table "wt_module_privacy" add constraint "module_privacy_gedcom_id_foreign" foreign key ("gedcom_id") references "wt_gedcom" ("gedcom_id");
GO
create table "wt_block" ("block_id" int not null identity primary key, "gedcom_id" int null, "user_id" int null, "xref" nvarchar(20) null, "location" nvarchar(255) check ("location" in (N'main', N'side')) null, "block_order" int not null, "module_name" nvarchar(32) not null);
GO
create index "block_module_name_index" on "wt_block" ("module_name");
GO
create index "block_gedcom_id_index" on "wt_block" ("gedcom_id");
GO
create index "block_user_id_index" on "wt_block" ("user_id");
GO
alter table "wt_block" add constraint "block_module_name_foreign" foreign key ("module_name") references "wt_module" ("module_name");
GO
alter table "wt_block" add constraint "block_gedcom_id_foreign" foreign key ("gedcom_id") references "wt_gedcom" ("gedcom_id");
GO
alter table "wt_block" add constraint "block_user_id_foreign" foreign key ("user_id") references "wt_user" ("user_id");
GO
create table "wt_block_setting" ("block_id" int not null, "setting_name" nvarchar(32) not null, "setting_value" nvarchar(max) not null);
GO
alter table "wt_block_setting" add constraint "block_setting_block_id_setting_name_primary" primary key ("block_id", "setting_name");
GO
alter table "wt_block_setting" add constraint "block_setting_block_id_foreign" foreign key ("block_id") references "wt_block" ("block_id");
GO
create table "wt_hit_counter" ("gedcom_id" int not null, "page_name" nvarchar(32) not null, "page_parameter" nvarchar(32) not null, "page_count" int not null);
GO
alter table "wt_hit_counter" add constraint "wt_hit_counter_primary" primary key ("gedcom_id", "page_name", "page_parameter");
GO
alter table "wt_hit_counter" add constraint "hit_counter_gedcom_id_foreign" foreign key ("gedcom_id") references "wt_gedcom" ("gedcom_id");
GO
create table "wt_session" ("session_id" nvarchar(256) not null, "session_time" datetime not null default CURRENT_TIMESTAMP, "user_id" int not null, "ip_address" nvarchar(45) not null, "session_data" nvarchar(max) not null);
GO
alter table "wt_session" add constraint "session_session_id_primary" primary key ("session_id");
GO
create index "session_session_time_index" on "wt_session" ("session_time");
GO
create index "session_user_id_ip_address_index" on "wt_session" ("user_id", "ip_address");
GO
create table "wt_gedcom_chunk" ("gedcom_chunk_id" int not null identity primary key, "gedcom_id" int not null, "chunk_data" nvarchar(max) not null, "imported" bit not null default '0');
GO
create index "gedcom_chunk_gedcom_id_imported_index" on "wt_gedcom_chunk" ("gedcom_id", "imported");
GO
alter table "wt_gedcom_chunk" add constraint "gedcom_chunk_gedcom_id_foreign" foreign key ("gedcom_id") references "wt_gedcom" ("gedcom_id");
GO
if object_id(N'"wt_site_access_rule"', 'U') is not null drop table "wt_site_access_rule";
GO
if object_id(N'"wt_next_id"', 'U') is not null drop table "wt_next_id";
GO
create table "wt_media_file" ("id" int not null identity primary key, "m_id" nvarchar(20) not null, "m_file" int not null, "multimedia_file_refn" nvarchar(248) not null, "multimedia_format" nvarchar(4) not null, "source_media_type" nvarchar(15) not null, "descriptive_title" nvarchar(248) not null);
GO
create index "media_file_m_id_m_file_index" on "wt_media_file" ("m_id", "m_file");
GO
create index "media_file_m_file_m_id_index" on "wt_media_file" ("m_file", "m_id");
GO
create index "media_file_m_file_multimedia_file_refn_index" on "wt_media_file" ("m_file", "multimedia_file_refn");
GO
create index "media_file_m_file_multimedia_format_index" on "wt_media_file" ("m_file", "multimedia_format");
GO
create index "media_file_m_file_source_media_type_index" on "wt_media_file" ("m_file", "source_media_type");
GO
create index "media_file_m_file_descriptive_title_index" on "wt_media_file" ("m_file", "descriptive_title");
GO
create table "wt_favorite" ("favorite_id" int not null identity primary key, "user_id" int null, "gedcom_id" int not null, "xref" nvarchar(20) null, "favorite_type" nvarchar(255) check ("favorite_type" in (N'INDI', N'FAM', N'SOUR', N'REPO', N'OBJE', N'NOTE', N'URL')) not null, "url" nvarchar(255) null, "title" nvarchar(255) null, "note" nvarchar(1000) null);
GO
create index "favorite_user_id_index" on "wt_favorite" ("user_id");
GO
create index "favorite_gedcom_id_user_id_index" on "wt_favorite" ("gedcom_id", "user_id");
GO
alter table "wt_favorite" add constraint "favorite_user_id_foreign" foreign key ("user_id") references "wt_user" ("user_id") on delete cascade;
GO
alter table "wt_favorite" add constraint "favorite_gedcom_id_foreign" foreign key ("gedcom_id") references "wt_gedcom" ("gedcom_id") on delete cascade;
GO
create table "wt_news" ("news_id" int not null identity primary key, "user_id" int null, "gedcom_id" int null, "subject" nvarchar(255) not null, "body" nvarchar(max) not null, "updated" datetime not null default CURRENT_TIMESTAMP);
GO
create index "news_user_id_updated_index" on "wt_news" ("user_id", "updated");
GO
create index "news_gedcom_id_updated_index" on "wt_news" ("gedcom_id", "updated");
GO
alter table "wt_news" add constraint "news_user_id_foreign" foreign key ("user_id") references "wt_user" ("user_id") on delete cascade;
GO
alter table "wt_news" add constraint "news_gedcom_id_foreign" foreign key ("gedcom_id") references "wt_gedcom" ("gedcom_id") on delete cascade;
GO
alter table "wt_module" add "footer_order" int null;
GO
drop table "wt_module_privacy";
GO
create table "wt_module_privacy" ("id" int not null identity primary key, "module_name" nvarchar(32) not null, "gedcom_id" int not null, "interface" nvarchar(255) not null, "access_level" tinyint not null);
GO
create unique index "wt_module_privacy_ix1" on "wt_module_privacy" ("gedcom_id", "module_name", "interface");
GO
create unique index "wt_module_privacy_ix2" on "wt_module_privacy" ("module_name", "gedcom_id", "interface");
GO
alter table "wt_module_privacy" add constraint "module_privacy_module_name_foreign" foreign key ("module_name") references "wt_module" ("module_name") on delete cascade;
GO
alter table "wt_module_privacy" add constraint "module_privacy_gedcom_id_foreign" foreign key ("gedcom_id") references "wt_gedcom" ("gedcom_id") on delete cascade;
GO
create table "wt_place_location" ("id" int not null identity primary key, "parent_id" int null, "place" nvarchar(120) not null, "latitude" double precision null, "longitude" double precision null);
GO
create unique index "place_location_parent_id_place_unique" on "wt_place_location" ("parent_id", "place");
GO
create unique index "place_location_place_parent_id_unique" on "wt_place_location" ("place", "parent_id");
GO
create index "place_location_latitude_index" on "wt_place_location" ("latitude");
GO
create index "place_location_longitude_index" on "wt_place_location" ("longitude");
GO
alter table "wt_place_location" add constraint "place_location_parent_id_foreign" foreign key ("parent_id") references "wt_place_location" ("id") on delete NO ACTION on update NO ACTION;
GO
