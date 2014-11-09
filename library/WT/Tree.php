<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;
use WT\Log;
use WT\User;

/**
 * Class WT_Tree - Provide an interface to the wt_gedcom table
 */
class WT_Tree {
	// Tree attributes
	public $tree_id; // The "gedcom ID" number
	public $tree_name; // The "gedcom name" text
	public $tree_name_url;
	public $tree_name_html;
	public $tree_title; // The "gedcom title" text
	public $tree_title_html;
	public $imported;

	/** @var WT_Tree[] All trees that we have permission to see. */
	private static $trees;

	/** @var string[] Cached copy of the wt_gedcom_setting table. */
	private $preferences;

	/** @var string[][] Cached copy of the wt_user_gedcom_setting table. */
	private $user_preferences = array();

	/**
	 * Create a tree object.  This is a private constructor - it can only
	 * be called from WT_Tree::getAll() to ensure proper initialisation.
	 *
	 * @param $tree_id
	 * @param $tree_name
	 * @param $tree_title
	 * @param $imported
	 */
	private function __construct($tree_id, $tree_name, $tree_title, $imported) {
		$this->tree_id         = $tree_id;
		$this->tree_name       = $tree_name;
		$this->tree_name_url   = rawurlencode($tree_name);
		$this->tree_name_html  = WT_Filter::escapeHtml($tree_name);
		$this->tree_title      = $tree_title;
		$this->tree_title_html = '<span dir="auto">' . WT_Filter::escapeHtml($tree_title) . '</span>';
		$this->imported        = $imported;
	}

	/**
	 * Get the tree’s configuration settings.
	 *
	 * @param string      $setting_name
	 * @param string|null $default
	 *
	 * @return string|null
	 */
	public function getPreference($setting_name, $default = null) {
		if ($this->preferences === null) {
			$this->preferences = WT_DB::prepare(
				"SELECT SQL_CACHE setting_name, setting_value FROM `##gedcom_setting` WHERE gedcom_id = ?"
			)->execute(array($this->tree_id))->fetchAssoc();
		}

		if (array_key_exists($setting_name, $this->preferences)) {
			return $this->preferences[$setting_name];
		} else {
			return $default;
		}
	}

	/**
	 * Set the tree’s configuration settings.
	 *
	 * @param string $setting_name
	 * @param string $setting_value
	 *
	 * @return $this
	 */
	public function setPreference($setting_name, $setting_value) {
		if ($setting_value !== $this->getPreference($setting_name)) {
			// Update the database
			if ($setting_value === null) {
				WT_DB::prepare(
					"DELETE FROM `##gedcom_setting` WHERE gedcom_id = ? AND setting_name = ?"
				)->execute(array($this->tree_id, $setting_name));
			} else {
				WT_DB::prepare(
					"REPLACE INTO `##gedcom_setting` (gedcom_id, setting_name, setting_value) VALUES (?, ?, LEFT(?, 255))"
				)->execute(array($this->tree_id, $setting_name, $setting_value));
			}
			// Update our cache
			$this->preferences[$setting_name] = $setting_value;
			// Audit log of changes
			Log::addConfigurationLog('Tree setting "' . $setting_name . '" set to "' . $setting_value . '"');
		}

		return $this;
	}

	/**
	 * Get the tree’s user-configuration settings.
	 *
	 * @param User        $user
	 * @param string      $setting_name
	 * @param string|null $default
	 *
	 * @return string
	 */
	public function getUserPreference(User $user, $setting_name, $default = null) {
		// There are lots of settings, and we need to fetch lots of them on every page
		// so it is quicker to fetch them all in one go.
		if (!array_key_exists($user->getUserId(), $this->user_preferences)) {
			$this->user_preferences[$user->getUserId()] = WT_DB::prepare(
				"SELECT SQL_CACHE setting_name, setting_value FROM `##user_gedcom_setting` WHERE user_id = ? AND gedcom_id = ?"
			)->execute(array($user->getUserId(), $this->tree_id))->fetchAssoc();
		}

		if (array_key_exists($setting_name, $this->user_preferences[$user->getUserId()])) {
			return $this->user_preferences[$user->getUserId()][$setting_name];
		} else {
			return $default;
		}
	}

	/**
	 * Set the tree’s user-configuration settings.
	 *
	 * @param User    $user
	 * @param string  $setting_name
	 * @param string  $setting_value
	 *
	 * @return $this
	 */
	public function setUserPreference(User $user, $setting_name, $setting_value) {
		if ($this->getUserPreference($user, $setting_name) !== $setting_value) {
			// Update the database
			WT_DB::prepare(
				"REPLACE INTO `##user_gedcom_setting` (user_id, gedcom_id, setting_name, setting_value) VALUES (?, ?, ?, LEFT(?, 255))"
			)->execute(array($user->getUserId(), $this->tree_id, $setting_name, $setting_value));
			// Update our cache
			$this->user_preferences[$user->getUserId()][$setting_name] = $setting_value;
			// Audit log of changes
			Log::addConfigurationLog('Tree setting "' . $setting_name . '" set to "' . $setting_value . '" for user "' . $user->getUserName() . '"');
		}

		return $this;
	}

	/**
	 * Can a user accept changes for this tree?
	 *
	 * @param User $user
	 *
	 * @return boolean
	 */
	public function canAcceptChanges(User $user) {
		return Auth::isModerator($this, $user);
	}

	/**
	 * Fetch all the trees that we have permission to access.
	 *
	 * @return WT_Tree[]
	 */
	public static function getAll() {
		if (self::$trees === null) {
			self::$trees = array();
			$rows = WT_DB::prepare(
				"SELECT SQL_CACHE g.gedcom_id AS tree_id, g.gedcom_name AS tree_name, gs1.setting_value AS tree_title, gs2.setting_value AS imported" .
				" FROM `##gedcom` g" .
				" LEFT JOIN `##gedcom_setting`      gs1 ON (g.gedcom_id=gs1.gedcom_id AND gs1.setting_name='title')" .
				" LEFT JOIN `##gedcom_setting`      gs2 ON (g.gedcom_id=gs2.gedcom_id AND gs2.setting_name='imported')" .
				" LEFT JOIN `##gedcom_setting`      gs3 ON (g.gedcom_id=gs3.gedcom_id AND gs3.setting_name='REQUIRE_AUTHENTICATION')" .
				" LEFT JOIN `##user_gedcom_setting` ugs ON (g.gedcom_id=ugs.gedcom_id AND ugs.setting_name='canedit' AND ugs.user_id=?)" .
				" WHERE " .
				"  g.gedcom_id>0 AND (".          // exclude the "template" tree
				"    EXISTS (SELECT 1 FROM `##user_setting` WHERE user_id=? AND setting_name='canadmin' AND setting_value=1)" . // Admin sees all
				"   ) OR (" .
				"    gs2.setting_value = 1 AND (".                 // Allow imported trees, with either:
				"     gs3.setting_value <> 1 OR" .                 // visitor access
				"     IFNULL(ugs.setting_value, 'none')<>'none'" . // explicit access
				"   )" .
				"  )" .
				" ORDER BY g.sort_order, 3"
			)->execute(array(Auth::id(), Auth::id()))->fetchAll();
			foreach ($rows as $row) {
				self::$trees[$row->tree_id] = new WT_Tree($row->tree_id, $row->tree_name, $row->tree_title, $row->imported);
			}
		}

		return self::$trees;
	}

	/**
	 * Get the tree with a specific ID.  TODO - is this function needed long-term, or just while
	 * we integrate this class into the rest of the code?
	 *
	 * @param integer $tree_id
	 *
	 * @return WT_Tree
	 */
	public static function get($tree_id) {
		$trees = self::getAll();

		return $trees[$tree_id];
	}

	/**
	 * Create arguments to select_edit_control()
	 * Note - these will be escaped later
	 *
	 * @return string[]
	 */
	public static function getIdList() {
		$list = array();
		foreach (self::getAll() as $tree) {
			$list[$tree->tree_id] = $tree->tree_title;
		}

		return $list;
	}

	/**
	 * Create arguments to select_edit_control()
	 * Note - these will be escaped later
	 *
	 * @return string[]
	 */
	public static function getNameList() {
		$list = array();
		foreach (self::getAll() as $tree) {
			$list[$tree->tree_name] = $tree->tree_title;
		}

		return $list;
	}

	/**
	 * Find the ID number for a tree name
	 *
	 * @param integer $tree_name
	 *
	 * @return integer|null
	 */
	public static function getIdFromName($tree_name) {
		foreach (self::getAll() as $tree_id => $tree) {
			if ($tree->tree_name == $tree_name) {
				return $tree_id;
			}
		}

		return null;
	}

	/**
	 * Find the tree name from a numeric ID.
	 * @param integer $tree_id
	 *
	 * @return string
	 */
	public static function getNameFromId($tree_id) {
		return self::get($tree_id)->tree_name;
	}

	/**
	 * Create a new tree
	 *
	 * @param string $tree_name
	 *
	 * @return void
	 */
	public static function create($tree_name) {
		try {
			// Create a new tree
			WT_DB::prepare(
				"INSERT INTO `##gedcom` (gedcom_name) VALUES (?)"
			)->execute(array($tree_name));
			$tree_id = WT_DB::prepare("SELECT LAST_INSERT_ID()")->fetchOne();
		} catch (PDOException $ex) {
			// A tree with that name already exists?
			return;
		}

		// Update the list of trees - to include this new one
		self::$trees = null;
		$tree        = self::get($tree_id);

		// Module privacy
		WT_Module::setDefaultAccess($tree_id);

		// Gedcom and privacy settings
		$tree->setPreference('ADVANCED_NAME_FACTS', 'NICK,_AKA');
		$tree->setPreference('ADVANCED_PLAC_FACTS', '');
		$tree->setPreference('ALLOW_THEME_DROPDOWN', '1');
		$tree->setPreference('CALENDAR_FORMAT', 'gregorian');
		$tree->setPreference('CHART_BOX_TAGS', '');
		$tree->setPreference('COMMON_NAMES_ADD', '');
		$tree->setPreference('COMMON_NAMES_REMOVE', '');
		$tree->setPreference('COMMON_NAMES_THRESHOLD', '40');
		$tree->setPreference('CONTACT_USER_ID', Auth::id());
		$tree->setPreference('DEFAULT_PEDIGREE_GENERATIONS', '4');
		$tree->setPreference('EXPAND_RELATIVES_EVENTS', '0');
		$tree->setPreference('EXPAND_SOURCES', '0');
		$tree->setPreference('FAM_FACTS_ADD', 'CENS,MARR,RESI,SLGS,MARR_CIVIL,MARR_RELIGIOUS,MARR_PARTNERS,RESN');
		$tree->setPreference('FAM_FACTS_QUICK', 'MARR,DIV,_NMR');
		$tree->setPreference('FAM_FACTS_UNIQUE', 'NCHI,MARL,DIV,ANUL,DIVF,ENGA,MARB,MARC,MARS');
		$tree->setPreference('FAM_ID_PREFIX', 'F');
		$tree->setPreference('FORMAT_TEXT', 'markdown');
		$tree->setPreference('FULL_SOURCES', '0');
		$tree->setPreference('GEDCOM_ID_PREFIX', 'I');
		$tree->setPreference('GEDCOM_MEDIA_PATH', '');
		$tree->setPreference('GENERATE_UIDS', '0');
		$tree->setPreference('HIDE_GEDCOM_ERRORS', '1');
		$tree->setPreference('HIDE_LIVE_PEOPLE', '1');
		$tree->setPreference('INDI_FACTS_ADD', 'AFN,BIRT,DEAT,BURI,CREM,ADOP,BAPM,BARM,BASM,BLES,CHRA,CONF,FCOM,ORDN,NATU,EMIG,IMMI,CENS,PROB,WILL,GRAD,RETI,DSCR,EDUC,IDNO,NATI,NCHI,NMR,OCCU,PROP,RELI,RESI,SSN,TITL,BAPL,CONL,ENDL,SLGC,_MILI,ASSO,RESN');
		$tree->setPreference('INDI_FACTS_QUICK', 'BIRT,BURI,BAPM,CENS,DEAT,OCCU,RESI');
		$tree->setPreference('INDI_FACTS_UNIQUE', '');
		$tree->setPreference('KEEP_ALIVE_YEARS_BIRTH', '');
		$tree->setPreference('KEEP_ALIVE_YEARS_DEATH', '');
		$tree->setPreference('LANGUAGE', WT_LOCALE); // Default to the current admin’s language
		$tree->setPreference('MAX_ALIVE_AGE', 120);
		$tree->setPreference('MAX_DESCENDANCY_GENERATIONS', '15');
		$tree->setPreference('MAX_PEDIGREE_GENERATIONS', '10');
		$tree->setPreference('MEDIA_DIRECTORY', 'media/');
		$tree->setPreference('MEDIA_ID_PREFIX', 'M');
		$tree->setPreference('MEDIA_UPLOAD', WT_PRIV_USER);
		$tree->setPreference('META_DESCRIPTION', '');
		$tree->setPreference('META_TITLE', WT_WEBTREES);
		$tree->setPreference('NOTE_FACTS_ADD', 'SOUR,RESN');
		$tree->setPreference('NOTE_FACTS_QUICK', '');
		$tree->setPreference('NOTE_FACTS_UNIQUE', '');
		$tree->setPreference('NOTE_ID_PREFIX', 'N');
		$tree->setPreference('NO_UPDATE_CHAN', '0');
		$tree->setPreference('PEDIGREE_FULL_DETAILS', '1');
		$tree->setPreference('PEDIGREE_LAYOUT', '1');
		$tree->setPreference('PEDIGREE_ROOT_ID', '');
		$tree->setPreference('PEDIGREE_SHOW_GENDER', '0');
		$tree->setPreference('PREFER_LEVEL2_SOURCES', '1');
		$tree->setPreference('QUICK_REQUIRED_FACTS', 'BIRT,DEAT');
		$tree->setPreference('QUICK_REQUIRED_FAMFACTS', 'MARR');
		$tree->setPreference('REPO_FACTS_ADD', 'PHON,EMAIL,FAX,WWW,NOTE,SHARED_NOTE,RESN');
		$tree->setPreference('REPO_FACTS_QUICK', '');
		$tree->setPreference('REPO_FACTS_UNIQUE', 'NAME,ADDR');
		$tree->setPreference('REPO_ID_PREFIX', 'R');
		$tree->setPreference('REQUIRE_AUTHENTICATION', '0');
		$tree->setPreference('SAVE_WATERMARK_IMAGE', '0');
		$tree->setPreference('SAVE_WATERMARK_THUMB', '0');
		$tree->setPreference('SHOW_AGE_DIFF', '0');
		$tree->setPreference('SHOW_COUNTER', '1');
		$tree->setPreference('SHOW_DEAD_PEOPLE', WT_PRIV_PUBLIC);
		$tree->setPreference('SHOW_EST_LIST_DATES', '0');
		$tree->setPreference('SHOW_FACT_ICONS', '1');
		$tree->setPreference('SHOW_GEDCOM_RECORD', '0');
		$tree->setPreference('SHOW_HIGHLIGHT_IMAGES', '1');
		$tree->setPreference('SHOW_LDS_AT_GLANCE', '0');
		$tree->setPreference('SHOW_LEVEL2_NOTES', '1');
		$tree->setPreference('SHOW_LIVING_NAMES', WT_PRIV_USER);
		$tree->setPreference('SHOW_MEDIA_DOWNLOAD', '0');
		$tree->setPreference('SHOW_NO_WATERMARK', WT_PRIV_USER);
		$tree->setPreference('SHOW_PARENTS_AGE', '1');
		$tree->setPreference('SHOW_PEDIGREE_PLACES', '9');
		$tree->setPreference('SHOW_PEDIGREE_PLACES_SUFFIX', '0');
		$tree->setPreference('SHOW_PRIVATE_RELATIONSHIPS', '1');
		$tree->setPreference('SHOW_RELATIVES_EVENTS', '_BIRT_CHIL,_BIRT_SIBL,_MARR_CHIL,_MARR_PARE,_DEAT_CHIL,_DEAT_PARE,_DEAT_GPAR,_DEAT_SIBL,_DEAT_SPOU');
		$tree->setPreference('SHOW_STATS', '0');
		$tree->setPreference('SOURCE_ID_PREFIX', 'S');
		$tree->setPreference('SOUR_FACTS_ADD', 'NOTE,REPO,SHARED_NOTE,RESN');
		$tree->setPreference('SOUR_FACTS_QUICK', 'TEXT,NOTE,REPO');
		$tree->setPreference('SOUR_FACTS_UNIQUE', 'AUTH,ABBR,TITL,PUBL,TEXT');
		$tree->setPreference('SUBLIST_TRIGGER_I', '200');
		$tree->setPreference('SURNAME_LIST_STYLE', 'style2');
		switch (WT_LOCALE) {
		case 'es':
			$tree->setPreference('SURNAME_TRADITION', 'spanish');
			break;
		case 'is':
			$tree->setPreference('SURNAME_TRADITION', 'icelandic');
			break;
		case 'lt':
			$tree->setPreference('SURNAME_TRADITION', 'lithuanian');
			break;
		case 'pl':
			$tree->setPreference('SURNAME_TRADITION', 'polish');
			break;
		case 'pt':
		case 'pt-BR':
			$tree->setPreference('SURNAME_TRADITION', 'portuguese');
			break;
		default:
			$tree->setPreference('SURNAME_TRADITION', 'paternal');
			break;
		}
		$tree->setPreference('THEME_DIR', 'webtrees');
		$tree->setPreference('THUMBNAIL_WIDTH', '100');
		$tree->setPreference('USE_RIN', '0');
		$tree->setPreference('USE_SILHOUETTE', '1');
		$tree->setPreference('WATERMARK_THUMB', '0');
		$tree->setPreference('WEBMASTER_USER_ID', Auth::id());
		$tree->setPreference('WEBTREES_EMAIL', '');
		$tree->setPreference('WORD_WRAPPED_NOTES', '0');
		$tree->setPreference('imported', '0');
		$tree->setPreference('title',                        /* I18N: Default title for new family trees */ WT_I18N::translate('My family tree'));

		// Default restriction settings
		$statement = WT_DB::prepare(
			"INSERT INTO `##default_resn` (gedcom_id, xref, tag_type, resn) VALUES (?, NULL, ?, ?)"
		);
		$statement->execute(array($tree_id, 'SSN', 'confidential'));
		$statement->execute(array($tree_id, 'SOUR', 'privacy'));
		$statement->execute(array($tree_id, 'REPO', 'privacy'));
		$statement->execute(array($tree_id, 'SUBM', 'confidential'));
		$statement->execute(array($tree_id, 'SUBN', 'confidential'));

		// Genealogy data
		// It is simpler to create a temporary/unimported GEDCOM than to populate all the tables...
		$john_doe =/* I18N: This should be a common/default/placeholder name of an individual.  Put slashes around the surname. */
			WT_I18N::translate('John /DOE/');
		$note     = WT_I18N::translate('Edit this individual and replace their details with your own');
		WT_DB::prepare("INSERT INTO `##gedcom_chunk` (gedcom_id, chunk_data) VALUES (?, ?)")->execute(array(
			$tree_id,
			"0 HEAD\n1 CHAR UTF-8\n0 @I1@ INDI\n1 NAME {$john_doe}\n1 SEX M\n1 BIRT\n2 DATE 01 JAN 1850\n2 NOTE {$note}\n0 TRLR\n"
		));

		// Set the initial blocks
		WT_DB::prepare(
			"INSERT INTO `##block` (gedcom_id, location, block_order, module_name)" .
			" SELECT ?, location, block_order, module_name" .
			" FROM `##block`" .
			" WHERE gedcom_id = -1"
		)->execute(array($tree_id));

		// Update the list of trees - to include the new configuration settings
		self::$trees = null;
	}

	/**
	 * Delete all the genealogy data from a tree - in preparation for importing
	 * new data.  Optionally retain the media data, for when the user has been
	 * editing their data offline using an application which deletes (or does not
	 * support) media data.
	 *
	 * @param bool $keep_media
	 */
	public function deleteGenealogyData($keep_media) {
		WT_DB::prepare("DELETE FROM `##individuals` WHERE i_file   =?")->execute(array($this->tree_id));
		WT_DB::prepare("DELETE FROM `##families`    WHERE f_file   =?")->execute(array($this->tree_id));
		WT_DB::prepare("DELETE FROM `##sources`     WHERE s_file   =?")->execute(array($this->tree_id));
		WT_DB::prepare("DELETE FROM `##other`       WHERE o_file   =?")->execute(array($this->tree_id));
		WT_DB::prepare("DELETE FROM `##places`      WHERE p_file   =?")->execute(array($this->tree_id));
		WT_DB::prepare("DELETE FROM `##placelinks`  WHERE pl_file  =?")->execute(array($this->tree_id));
		WT_DB::prepare("DELETE FROM `##name`        WHERE n_file   =?")->execute(array($this->tree_id));
		WT_DB::prepare("DELETE FROM `##dates`       WHERE d_file   =?")->execute(array($this->tree_id));
		WT_DB::prepare("DELETE FROM `##change`      WHERE gedcom_id=?")->execute(array($this->tree_id));

		if ($keep_media) {
			WT_DB::prepare("DELETE FROM `##link` WHERE l_file =? AND l_type<>'OBJE'")->execute(array($this->tree_id));
		} else {
			WT_DB::prepare("DELETE FROM `##link`  WHERE l_file =?")->execute(array($this->tree_id));
			WT_DB::prepare("DELETE FROM `##media` WHERE m_file =?")->execute(array($this->tree_id));
		}
	}

	/**
	 * Delete everything relating to a tree
	 */
	public function delete() {
		// If this is the default tree, then unset it
		if (WT_Site::getPreference('DEFAULT_GEDCOM') === self::getNameFromId($this->tree_id)) {
			WT_Site::setPreference('DEFAULT_GEDCOM', '');
		}

		$this->deleteGenealogyData(false);

		WT_DB::prepare("DELETE `##block_setting` FROM `##block_setting` JOIN `##block` USING (block_id) WHERE gedcom_id=?")->execute(array($this->tree_id));
		WT_DB::prepare("DELETE FROM `##block`               WHERE gedcom_id = ?")->execute(array($this->tree_id));
		WT_DB::prepare("DELETE FROM `##user_gedcom_setting` WHERE gedcom_id = ?")->execute(array($this->tree_id));
		WT_DB::prepare("DELETE FROM `##gedcom_setting`      WHERE gedcom_id = ?")->execute(array($this->tree_id));
		WT_DB::prepare("DELETE FROM `##module_privacy`      WHERE gedcom_id = ?")->execute(array($this->tree_id));
		WT_DB::prepare("DELETE FROM `##next_id`             WHERE gedcom_id = ?")->execute(array($this->tree_id));
		WT_DB::prepare("DELETE FROM `##hit_counter`         WHERE gedcom_id = ?")->execute(array($this->tree_id));
		WT_DB::prepare("DELETE FROM `##default_resn`        WHERE gedcom_id = ?")->execute(array($this->tree_id));
		WT_DB::prepare("DELETE FROM `##gedcom_chunk`        WHERE gedcom_id = ?")->execute(array($this->tree_id));
		WT_DB::prepare("DELETE FROM `##log`                 WHERE gedcom_id = ?")->execute(array($this->tree_id));
		WT_DB::prepare("DELETE FROM `##gedcom`              WHERE gedcom_id = ?")->execute(array($this->tree_id));

		// After updating the database, we need to fetch a new (sorted) copy
		self::$trees = null;
	}

	/**
	 * Export the tree to a GEDCOM file
	 *
	 * @param $gedcom_file
	 *
	 * @return bool
	 */
	public function exportGedcom($gedcom_file) {
		require_once WT_ROOT . 'includes/functions/functions_export.php';

		// To avoid partial trees on timeout/diskspace/etc, write to a temporary file first
		$tmp_file = $gedcom_file . '.tmp';

		$file_pointer = @fopen($tmp_file, 'w');
		if ($file_pointer === false) {
			return false;
		}

		$buffer = reformat_record_export(gedcom_header($this->tree_name));

		$stmt = WT_DB::prepare(
			"SELECT i_gedcom AS gedcom FROM `##individuals` WHERE i_file = ?" .
			" UNION ALL " .
			"SELECT f_gedcom AS gedcom FROM `##families`    WHERE f_file = ?" .
			" UNION ALL " .
			"SELECT s_gedcom AS gedcom FROM `##sources`     WHERE s_file = ?" .
			" UNION ALL " .
			"SELECT o_gedcom AS gedcom FROM `##other`       WHERE o_file = ? AND o_type NOT IN ('HEAD', 'TRLR')" .
			" UNION ALL " .
			"SELECT m_gedcom AS gedcom FROM `##media`       WHERE m_file = ?"
		)->execute(array($this->tree_id, $this->tree_id, $this->tree_id, $this->tree_id, $this->tree_id));

		while ($row = $stmt->fetch()) {
			$buffer .= reformat_record_export($row->gedcom);
			if (strlen($buffer) > 65535) {
				fwrite($file_pointer, $buffer);
				$buffer = '';
			}
		}

		fwrite($file_pointer, $buffer . '0 TRLR' . WT_EOL);
		fclose($file_pointer);

		return @rename($tmp_file, $gedcom_file);
	}
}
