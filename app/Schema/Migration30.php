<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees\Schema;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Filter;

/**
 * Upgrade the database schema from version 30 to version 31.
 */
class Migration30 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		$WEBTREES_EMAIL = 'webtrees-noreply@' . preg_replace('/^www\./i', '', Filter::server('SERVER_NAME'));

		// Default settings for new trees.  No defaults for:
		// imported, title, CONTACT_USER_ID, WEBMASTER_USER_ID
		// The following settings have defaults, but may need overwriting:
		// LANGUAGE, SURNAME_TRADITION
		Database::prepare(
			"INSERT IGNORE INTO `##gedcom_setting` (gedcom_id, setting_name, setting_value) VALUES" .
			"(-1, 'ADVANCED_NAME_FACTS', 'NICK,_AKA')," .
			"(-1, 'ADVANCED_PLAC_FACTS', '')," .
			"(-1, 'ALLOW_THEME_DROPDOWN', '1')," .
			"(-1, 'CALENDAR_FORMAT', 'gregorian')," .
			"(-1, 'CHART_BOX_TAGS', '')," .
			"(-1, 'COMMON_NAMES_ADD', '')," .
			"(-1, 'COMMON_NAMES_REMOVE', '')," .
			"(-1, 'COMMON_NAMES_THRESHOLD', '40')," .
			"(-1, 'DEFAULT_PEDIGREE_GENERATIONS', '4')," .
			"(-1, 'EXPAND_RELATIVES_EVENTS', '0')," .
			"(-1, 'EXPAND_SOURCES', '0')," .
			"(-1, 'FAM_FACTS_ADD', 'CENS,MARR,RESI,SLGS,MARR_CIVIL,MARR_RELIGIOUS,MARR_PARTNERS,RESN')," .
			"(-1, 'FAM_FACTS_QUICK', 'MARR,DIV,_NMR')," .
			"(-1, 'FAM_FACTS_UNIQUE', 'NCHI,MARL,DIV,ANUL,DIVF,ENGA,MARB,MARC,MARS')," .
			"(-1, 'FAM_ID_PREFIX', 'F')," .
			"(-1, 'FORMAT_TEXT', 'markdown')," .
			"(-1, 'FULL_SOURCES', '0')," .
			"(-1, 'GEDCOM_ID_PREFIX', 'I')," .
			"(-1, 'GEDCOM_MEDIA_PATH', '')," .
			"(-1, 'GENERATE_UIDS', '0')," .
			"(-1, 'HIDE_GEDCOM_ERRORS', '1')," .
			"(-1, 'HIDE_LIVE_PEOPLE', '1')," .
			"(-1, 'INDI_FACTS_ADD', 'AFN,BIRT,DEAT,BURI,CREM,ADOP,BAPM,BARM,BASM,BLES,CHRA,CONF,FCOM,ORDN,NATU,EMIG,IMMI,CENS,PROB,WILL,GRAD,RETI,DSCR,EDUC,IDNO,NATI,NCHI,NMR,OCCU,PROP,RELI,RESI,SSN,TITL,BAPL,CONL,ENDL,SLGC,_MILI,ASSO,RESN')," .
			"(-1, 'INDI_FACTS_QUICK', 'BIRT,BURI,BAPM,CENS,DEAT,OCCU,RESI')," .
			"(-1, 'INDI_FACTS_UNIQUE', '')," .
			"(-1, 'KEEP_ALIVE_YEARS_BIRTH', '')," .
			"(-1, 'KEEP_ALIVE_YEARS_DEATH', '')," .
			"(-1, 'LANGUAGE', 'en-US')," .
			"(-1, 'MAX_ALIVE_AGE', '120')," .
			"(-1, 'MAX_DESCENDANCY_GENERATIONS', '15')," .
			"(-1, 'MAX_PEDIGREE_GENERATIONS', '10')," .
			"(-1, 'MEDIA_DIRECTORY', 'media/')," .
			"(-1, 'MEDIA_ID_PREFIX', 'M')," .
			"(-1, 'MEDIA_UPLOAD', :MEDIA_UPLOAD)," .
			"(-1, 'META_DESCRIPTION', '')," .
			"(-1, 'META_TITLE', :META_TITLE)," .
			"(-1, 'NOTE_FACTS_ADD', 'SOUR,RESN')," .
			"(-1, 'NOTE_FACTS_QUICK', '')," .
			"(-1, 'NOTE_FACTS_UNIQUE', '')," .
			"(-1, 'NOTE_ID_PREFIX', 'N')," .
			"(-1, 'NO_UPDATE_CHAN', '0')," .
			"(-1, 'PEDIGREE_FULL_DETAILS', '1')," .
			"(-1, 'PEDIGREE_LAYOUT', '1')," .
			"(-1, 'PEDIGREE_ROOT_ID', '')," .
			"(-1, 'PEDIGREE_SHOW_GENDER', '0')," .
			"(-1, 'PREFER_LEVEL2_SOURCES', '1')," .
			"(-1, 'QUICK_REQUIRED_FACTS', 'BIRT,DEAT')," .
			"(-1, 'QUICK_REQUIRED_FAMFACTS', 'MARR')," .
			"(-1, 'REPO_FACTS_ADD', 'PHON,EMAIL,FAX,WWW,RESN')," .
			"(-1, 'REPO_FACTS_QUICK', '')," .
			"(-1, 'REPO_FACTS_UNIQUE', 'NAME,ADDR')," .
			"(-1, 'REPO_ID_PREFIX', 'R')," .
			"(-1, 'REQUIRE_AUTHENTICATION', '0')," .
			"(-1, 'SAVE_WATERMARK_IMAGE', '0')," .
			"(-1, 'SAVE_WATERMARK_THUMB', '0')," .
			"(-1, 'SHOW_AGE_DIFF', '0')," .
			"(-1, 'SHOW_COUNTER', '1')," .
			"(-1, 'SHOW_DEAD_PEOPLE', :SHOW_DEAD_PEOPLE)," .
			"(-1, 'SHOW_EST_LIST_DATES', '0')," .
			"(-1, 'SHOW_FACT_ICONS', '1')," .
			"(-1, 'SHOW_GEDCOM_RECORD', '0')," .
			"(-1, 'SHOW_HIGHLIGHT_IMAGES', '1')," .
			"(-1, 'SHOW_LDS_AT_GLANCE', '0')," .
			"(-1, 'SHOW_LEVEL2_NOTES', '1')," .
			"(-1, 'SHOW_LIVING_NAMES', :SHOW_LIVING_NAMES)," .
			"(-1, 'SHOW_MEDIA_DOWNLOAD', '0')," .
			"(-1, 'SHOW_NO_WATERMARK', :SHOW_NO_WATERMARK)," .
			"(-1, 'SHOW_PARENTS_AGE', '1')," .
			"(-1, 'SHOW_PEDIGREE_PLACES', '9')," .
			"(-1, 'SHOW_PEDIGREE_PLACES_SUFFIX', '0')," .
			"(-1, 'SHOW_PRIVATE_RELATIONSHIPS', '1')," .
			"(-1, 'SHOW_RELATIVES_EVENTS', '_BIRT_CHIL,_BIRT_SIBL,_MARR_CHIL,_MARR_PARE,_DEAT_CHIL,_DEAT_PARE,_DEAT_GPAR,_DEAT_SIBL,_DEAT_SPOU')," .
			"(-1, 'SOURCE_ID_PREFIX', 'S')," .
			"(-1, 'SOUR_FACTS_ADD', 'NOTE,REPO,SHARED_NOTE,RESN')," .
			"(-1, 'SOUR_FACTS_QUICK', 'TEXT,NOTE,REPO')," .
			"(-1, 'SOUR_FACTS_UNIQUE', 'AUTH,ABBR,TITL,PUBL,TEXT')," .
			"(-1, 'SUBLIST_TRIGGER_I', '200')," .
			"(-1, 'SURNAME_LIST_STYLE', 'style2')," .
			"(-1, 'SURNAME_TRADITION', 'paternal')," .
			"(-1, 'THUMBNAIL_WIDTH', '100')," .
			"(-1, 'USE_RIN', '0')," .
			"(-1, 'USE_SILHOUETTE', '1')," .
			"(-1, 'WATERMARK_THUMB', '0')," .
			"(-1, 'WEBTREES_EMAIL', :WEBTREES_EMAIL)," .
			"(-1, 'WORD_WRAPPED_NOTES', '0')"
		)->execute(array(
			'MEDIA_UPLOAD'      => Auth::PRIV_USER,
			'META_TITLE'        => WT_WEBTREES,
			'SHOW_DEAD_PEOPLE'  => Auth::PRIV_PRIVATE,
			'SHOW_LIVING_NAMES' => Auth::PRIV_USER,
			'SHOW_NO_WATERMARK' => Auth::PRIV_USER,
			'WEBTREES_EMAIL'    => $WEBTREES_EMAIL,
		));

		// Previous versions of webtrees allowed this setting to be empty.
		Database::prepare(
			"DELETE FROM `##gedcom_setting` WHERE setting_name  ='WEBTREES_EMAIL' AND setting_value = ''"
		)->execute();

		Database::prepare(
			"INSERT IGNORE INTO `##gedcom_setting` (gedcom_id, setting_name, setting_value)" .
			" SELECT gedcom_id, 'WEBTREES_EMAIL', :WEBTREES_EMAIL" .
			" FROM `##gedcom` WHERE gedcom_id > 0"
		)->execute(array(
			'WEBTREES_EMAIL' => $WEBTREES_EMAIL,
		));

		// Default restrictions
		Database::prepare(
			"INSERT IGNORE INTO `##default_resn` (gedcom_id, tag_type, resn) VALUES " .
			"(-1, 'SSN', 'confidential')," .
			"(-1, 'SOUR', 'privacy')," .
			"(-1, 'REPO', 'privacy')," .
			"(-1, 'SUBM', 'confidential')," .
			"(-1, 'SUBN', 'confidential')"
		)->execute();
	}
}
