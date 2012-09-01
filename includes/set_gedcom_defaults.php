<?php
// Set/create default settings for a new gedcom.
//
// The calling module must set $ged_id and $ged_name
//
// Copyright (C) 2012 webtrees development team.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

if (!defined('WT_WEBTREES') || empty($ged_id) || empty($ged_name)) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

////////////////////////////////////////////////////////////////////////////////
// Module privacy
////////////////////////////////////////////////////////////////////////////////
WT_Module::setDefaultAccess($ged_id);

////////////////////////////////////////////////////////////////////////////////
// Gedcom and privacy settings
////////////////////////////////////////////////////////////////////////////////
set_gedcom_setting($ged_id, 'ABBREVIATE_CHART_LABELS',      false);
set_gedcom_setting($ged_id, 'ADVANCED_NAME_FACTS',          'NICK,_AKA');
set_gedcom_setting($ged_id, 'ADVANCED_PLAC_FACTS',          '');
set_gedcom_setting($ged_id, 'ALLOW_EDIT_GEDCOM',            true);
set_gedcom_setting($ged_id, 'ALLOW_THEME_DROPDOWN',         true);
set_gedcom_setting($ged_id, 'AUTO_GENERATE_THUMBS',         true);
set_gedcom_setting($ged_id, 'CALENDAR_FORMAT',              'gregorian');
set_gedcom_setting($ged_id, 'CHART_BOX_TAGS',               '');
set_gedcom_setting($ged_id, 'COMMON_NAMES_ADD',             '');
set_gedcom_setting($ged_id, 'COMMON_NAMES_REMOVE',          '');
set_gedcom_setting($ged_id, 'COMMON_NAMES_THRESHOLD',       '40');
set_gedcom_setting($ged_id, 'CONTACT_USER_ID',              WT_USER_ID);
set_gedcom_setting($ged_id, 'DEFAULT_PEDIGREE_GENERATIONS', '4');
set_gedcom_setting($ged_id, 'EXPAND_NOTES',                 false);
set_gedcom_setting($ged_id, 'EXPAND_RELATIVES_EVENTS',      false);
set_gedcom_setting($ged_id, 'EXPAND_SOURCES',               false);
set_gedcom_setting($ged_id, 'FAM_FACTS_ADD',                'CENS,MARR,RESI,SLGS,MARR_CIVIL,MARR_RELIGIOUS,MARR_PARTNERS,RESN');
set_gedcom_setting($ged_id, 'FAM_FACTS_QUICK',              'MARR,DIV,_NMR');
set_gedcom_setting($ged_id, 'FAM_FACTS_UNIQUE',             'NCHI,MARL,DIV,ANUL,DIVF,ENGA,MARB,MARC,MARS');
set_gedcom_setting($ged_id, 'FAM_ID_PREFIX',                'F');
set_gedcom_setting($ged_id, 'FULL_SOURCES',                 false);
set_gedcom_setting($ged_id, 'GEDCOM_ID_PREFIX',             'I');
set_gedcom_setting($ged_id, 'GENERATE_UIDS',                false);
set_gedcom_setting($ged_id, 'HIDE_GEDCOM_ERRORS',           true);
set_gedcom_setting($ged_id, 'HIDE_LIVE_PEOPLE',             true);
set_gedcom_setting($ged_id, 'INDI_FACTS_ADD',               'ADDR,AFN,BIRT,CHR,DEAT,BURI,CREM,ADOP,BAPM,BARM,BASM,BLES,CHRA,CONF,EMAIL,FAX,FCOM,ORDN,NATU,EMIG,IMMI,CENS,PROB,WILL,GRAD,RETI,CAST,DSCR,EDUC,IDNO,NATI,NCHI,NMR,OCCU,PROP,RELI,RESI,SSN,TITL,BAPL,CONL,ENDL,SLGC,_MILI,ASSO,RESN');
set_gedcom_setting($ged_id, 'INDI_FACTS_QUICK',             'BIRT,BURI,CHR,CENS,DEAT,OCCU,RESI');
set_gedcom_setting($ged_id, 'INDI_FACTS_UNIQUE',            '');
set_gedcom_setting($ged_id, 'KEEP_ALIVE_YEARS_BIRTH',       '');
set_gedcom_setting($ged_id, 'KEEP_ALIVE_YEARS_DEATH',       '');
set_gedcom_setting($ged_id, 'LANGUAGE',                     WT_LOCALE); // Defualt to the current admin's language`
set_gedcom_setting($ged_id, 'MAX_ALIVE_AGE',                120);
set_gedcom_setting($ged_id, 'MAX_DESCENDANCY_GENERATIONS',  '15');
set_gedcom_setting($ged_id, 'MAX_PEDIGREE_GENERATIONS',     '10');
set_gedcom_setting($ged_id, 'MEDIA_DIRECTORY',              'media/');
set_gedcom_setting($ged_id, 'MEDIA_DIRECTORY_LEVELS',       '0');
set_gedcom_setting($ged_id, 'MEDIA_EXTERNAL',               true);
set_gedcom_setting($ged_id, 'MEDIA_FIREWALL_ROOTDIR',       get_site_setting('INDEX_DIRECTORY'));
set_gedcom_setting($ged_id, 'MEDIA_FIREWALL_THUMBS',        false);
set_gedcom_setting($ged_id, 'MEDIA_ID_PREFIX',              'M');
set_gedcom_setting($ged_id, 'MEDIA_UPLOAD',                 WT_PRIV_USER); 
set_gedcom_setting($ged_id, 'META_DESCRIPTION',             '');
set_gedcom_setting($ged_id, 'META_TITLE',                   WT_WEBTREES);
set_gedcom_setting($ged_id, 'NOTE_FACTS_ADD',               'SOUR,RESN');
set_gedcom_setting($ged_id, 'NOTE_FACTS_QUICK',             '');
set_gedcom_setting($ged_id, 'NOTE_FACTS_UNIQUE',            '');
set_gedcom_setting($ged_id, 'NOTE_ID_PREFIX',               'N');
set_gedcom_setting($ged_id, 'NO_UPDATE_CHAN',               false);
set_gedcom_setting($ged_id, 'PEDIGREE_FULL_DETAILS',        true);
set_gedcom_setting($ged_id, 'PEDIGREE_LAYOUT',              true);
set_gedcom_setting($ged_id, 'PEDIGREE_ROOT_ID',             '');
set_gedcom_setting($ged_id, 'PEDIGREE_SHOW_GENDER',         false);
set_gedcom_setting($ged_id, 'POSTAL_CODE',                  true);
set_gedcom_setting($ged_id, 'PREFER_LEVEL2_SOURCES',        '1');
set_gedcom_setting($ged_id, 'QUICK_REQUIRED_FACTS',         'BIRT,DEAT');
set_gedcom_setting($ged_id, 'QUICK_REQUIRED_FAMFACTS',      'MARR');
set_gedcom_setting($ged_id, 'REPO_FACTS_ADD',               'PHON,EMAIL,FAX,WWW,NOTE,SHARED_NOTE,RESN');
set_gedcom_setting($ged_id, 'REPO_FACTS_QUICK',             '');
set_gedcom_setting($ged_id, 'REPO_FACTS_UNIQUE',            'NAME,ADDR');
set_gedcom_setting($ged_id, 'REPO_ID_PREFIX',               'R');
set_gedcom_setting($ged_id, 'REQUIRE_AUTHENTICATION',       false);
set_gedcom_setting($ged_id, 'SAVE_WATERMARK_IMAGE',         false);
set_gedcom_setting($ged_id, 'SAVE_WATERMARK_THUMB',         false);
set_gedcom_setting($ged_id, 'SHOW_AGE_DIFF',                false);
set_gedcom_setting($ged_id, 'SHOW_COUNTER',                 true);
set_gedcom_setting($ged_id, 'SHOW_DEAD_PEOPLE',             WT_PRIV_PUBLIC);
set_gedcom_setting($ged_id, 'SHOW_EST_LIST_DATES',          false);
set_gedcom_setting($ged_id, 'SHOW_FACT_ICONS',              true);
set_gedcom_setting($ged_id, 'SHOW_GEDCOM_RECORD',           false);
set_gedcom_setting($ged_id, 'SHOW_HIGHLIGHT_IMAGES',        true);
set_gedcom_setting($ged_id, 'SHOW_LDS_AT_GLANCE',           false);
set_gedcom_setting($ged_id, 'SHOW_LEVEL2_NOTES',            true);
set_gedcom_setting($ged_id, 'SHOW_LIVING_NAMES',            WT_PRIV_USER);
set_gedcom_setting($ged_id, 'SHOW_MEDIA_DOWNLOAD',          false);
set_gedcom_setting($ged_id, 'SHOW_NO_WATERMARK',            WT_PRIV_USER);
set_gedcom_setting($ged_id, 'SHOW_PARENTS_AGE',             true);
set_gedcom_setting($ged_id, 'SHOW_PEDIGREE_PLACES',         '9');
set_gedcom_setting($ged_id, 'SHOW_PEDIGREE_PLACES_SUFFIX',  false);
set_gedcom_setting($ged_id, 'SHOW_PRIVATE_RELATIONSHIPS',   true);
set_gedcom_setting($ged_id, 'SHOW_REGISTER_CAUTION',        true);
set_gedcom_setting($ged_id, 'SHOW_RELATIVES_EVENTS',        '_BIRT_CHIL,_BIRT_SIBL,_MARR_CHIL,_MARR_PARE,_DEAT_CHIL,_DEAT_PARE,_DEAT_GPAR,_DEAT_SIBL,_DEAT_SPOU');
set_gedcom_setting($ged_id, 'SHOW_STATS',                   false);
set_gedcom_setting($ged_id, 'SOURCE_ID_PREFIX',             'S');
set_gedcom_setting($ged_id, 'SOUR_FACTS_ADD',               'NOTE,REPO,SHARED_NOTE,RESN');
set_gedcom_setting($ged_id, 'SOUR_FACTS_QUICK',             'TEXT,NOTE,REPO');
set_gedcom_setting($ged_id, 'SOUR_FACTS_UNIQUE',            'AUTH,ABBR,TITL,PUBL,TEXT');
set_gedcom_setting($ged_id, 'SUBLIST_TRIGGER_I',            '200');
set_gedcom_setting($ged_id, 'SURNAME_LIST_STYLE',           'style2');
switch (WT_LOCALE) {
case 'es': set_gedcom_setting($ged_id, 'SURNAME_TRADITION', 'spanish');    break;
case 'is': set_gedcom_setting($ged_id, 'SURNAME_TRADITION', 'icelandic');  break;
case 'lt': set_gedcom_setting($ged_id, 'SURNAME_TRADITION', 'lithuanian'); break;
case 'pl': set_gedcom_setting($ged_id, 'SURNAME_TRADITION', 'polish');     break;
case 'pt': set_gedcom_setting($ged_id, 'SURNAME_TRADITION', 'portuguese'); break;
default:   set_gedcom_setting($ged_id, 'SURNAME_TRADITION', 'paternal');   break;
}
set_gedcom_setting($ged_id, 'THEME_DIR',                    'webtrees');
set_gedcom_setting($ged_id, 'THUMBNAIL_WIDTH',              '100');
set_gedcom_setting($ged_id, 'USE_GEONAMES',                 false);
set_gedcom_setting($ged_id, 'USE_MEDIA_FIREWALL',           false);
set_gedcom_setting($ged_id, 'USE_MEDIA_VIEWER',             true);
set_gedcom_setting($ged_id, 'USE_RIN',                      false);
set_gedcom_setting($ged_id, 'USE_SILHOUETTE',               true);
set_gedcom_setting($ged_id, 'WATERMARK_THUMB',              false);
set_gedcom_setting($ged_id, 'WEBMASTER_USER_ID',            WT_USER_ID);
set_gedcom_setting($ged_id, 'WEBTREES_EMAIL',               '');
set_gedcom_setting($ged_id, 'WELCOME_TEXT_AUTH_MODE',       '1');
set_gedcom_setting($ged_id, 'WELCOME_TEXT_CUST_HEAD',       false);
set_gedcom_setting($ged_id, 'WORD_WRAPPED_NOTES',           false);
set_gedcom_setting($ged_id, 'imported',                     0);
set_gedcom_setting($ged_id, 'title',                        WT_I18N::translate('Genealogy from [%s]', $ged_name));

////////////////////////////////////////////////////////////////////////////////
// Default restriction settings
////////////////////////////////////////////////////////////////////////////////
$statement=WT_DB::prepare(
	"INSERT IGNORE INTO `##default_resn` (gedcom_id, xref, tag_type, resn) VALUES (?, NULL, ?, ?)"
);
$statement->execute(array($ged_id, 'SSN',  'confidential'));
$statement->execute(array($ged_id, 'SOUR', 'privacy'));
$statement->execute(array($ged_id, 'REPO', 'privacy'));
$statement->execute(array($ged_id, 'SUBM', 'confidential'));
$statement->execute(array($ged_id, 'SUBN', 'confidential'));
