<?php
namespace Fisharebest\Webtrees;

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

use PDOException;

/**
 * Class Tree - Provide an interface to the wt_gedcom table
 */
class Tree {
	/** @var integer The tree's ID number */
	private $tree_id;

	/** @var string The tree's name */
	private $name;

	/** @var string The tree's title */
	private $title;

	/** @var integer[] Default access rules for facts in this tree */
	private $fact_privacy;

	/** @var integer[] Default access rules for individuals in this tree */
	private $individual_privacy;

	/** @var integer[][] Default access rules for individual facts in this tree */
	private $individual_fact_privacy;

	/** @var Tree[] All trees that we have permission to see. */
	private static $trees;

	/** @var string[] Cached copy of the wt_gedcom_setting table. */
	private $preferences;

	/** @var string[][] Cached copy of the wt_user_gedcom_setting table. */
	private $user_preferences = array();

	/**
	 * Create a tree object.  This is a private constructor - it can only
	 * be called from Tree::getAll() to ensure proper initialisation.
	 *
	 * @param integer $tree_id
	 * @param string  $tree_name
	 * @param string  $tree_title
	 */
	private function __construct($tree_id, $tree_name, $tree_title) {
		$this->tree_id                 = $tree_id;
		$this->name                    = $tree_name;
		$this->title                   = $tree_title;
		$this->fact_privacy            = array();
		$this->individual_privacy      = array();
		$this->individual_fact_privacy = array();

		// Load the privacy settings for this tree
		$rows = Database::prepare(
			"SELECT SQL_CACHE xref, tag_type, CASE resn WHEN 'none' THEN :priv_public WHEN 'privacy' THEN :priv_user WHEN 'confidential' THEN :priv_none WHEN 'hidden' THEN :priv_hide END AS resn" .
			" FROM `##default_resn` WHERE gedcom_id = :tree_id"
		)->execute(array(
			'priv_public' => WT_PRIV_PUBLIC,
			'priv_user'   => WT_PRIV_USER,
			'priv_none'   => WT_PRIV_NONE,
			'priv_hide'   => WT_PRIV_HIDE,
			'tree_id'     => $this->tree_id
		))->fetchAll();

		foreach ($rows as $row) {
			if ($row->xref !== null) {
				if ($row->tag_type !== null) {
					$this->individual_fact_privacy[$row->xref][$row->tag_type] = (int) $row->resn;
				} else {
					$this->individual_privacy[$row->xref] = (int) $row->resn;
				}
			} else {
				$this->fact_privacy[$row->tag_type] = (int) $row->resn;
			}
		}


	}

	/**
	 * The ID of this tree
	 *
	 * @return integer
	 */
	public function getTreeId() {
		return $this->tree_id;
	}

	/**
	 * The name of this tree
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * The name of this tree
	 *
	 * @return string
	 */
	public function getNameHtml() {
		return Filter::escapeHtml($this->name);
	}

	/**
	 * The name of this tree
	 *
	 * @return string
	 */
	public function getNameUrl() {
		return Filter::escapeUrl($this->name);
	}

	/**
	 * The title of this tree
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * The title of this tree, with HTML markup
	 *
	 * @return string
	 */
	public function getTitleHtml() {
		return Filter::escapeHtml($this->title);
	}

	/**
	 * The fact-level privacy for this tree.
	 *
	 * @return integer[]
	 */
	public function getFactPrivacy() {
		return $this->fact_privacy;
	}

	/**
	 * The individual-level privacy for this tree.
	 *
	 * @return integer[]
	 */
	public function getIndividualPrivacy() {
		return $this->individual_privacy;
	}

	/**
	 * The individual-fact-level privacy for this tree.
	 *
	 * @return integer[][]
	 */
	public function getIndividualFactPrivacy() {
		return $this->individual_fact_privacy;
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
			$this->preferences = Database::prepare(
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
				Database::prepare(
					"DELETE FROM `##gedcom_setting` WHERE gedcom_id = :tree_id AND setting_name = :setting_name"
				)->execute(array(
					'tree_id'      => $this->tree_id,
					'setting_name' => $setting_name,
				));
			} else {
				Database::prepare(
					"REPLACE INTO `##gedcom_setting` (gedcom_id, setting_name, setting_value)" .
					" VALUES (:tree_id, :setting_name, LEFT(:setting_value, 255))"
				)->execute(array(
					'tree_id'       => $this->tree_id,
					'setting_name'  => $setting_name,
					'setting_value' => $setting_value,
				));
			}
			// Update our cache
			$this->preferences[$setting_name] = $setting_value;
			// Audit log of changes
			Log::addConfigurationLog('Tree setting "' . $setting_name . '" set to "' . $setting_value . '"', $this);
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
			$this->user_preferences[$user->getUserId()] = Database::prepare(
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
			if ($setting_value === null) {
				Database::prepare(
					"DELETE FROM `##user_gedcom_setting` WHERE gedcom_id = :tree_id AND user_id = :user_id AND setting_name = :setting_name"
				)->execute(array(
					'tree_id'      => $this->tree_id,
					'user_id'      => $user->getUserId(),
					'setting_name' => $setting_name,
				));
			} else {
				Database::prepare(
					"REPLACE INTO `##user_gedcom_setting` (user_id, gedcom_id, setting_name, setting_value) VALUES (:user_id, :tree_id, :setting_name, LEFT(:setting_value, 255))"
				)->execute(array(
					'user_id'       => $user->getUserId(),
					'tree_id'       => $this->tree_id,
					'setting_name'  => $setting_name,
					'setting_value' => $setting_value
				));
			}
			// Update our cache
			$this->user_preferences[$user->getUserId()][$setting_name] = $setting_value;
			// Audit log of changes
			Log::addConfigurationLog('Tree setting "' . $setting_name . '" set to "' . $setting_value . '" for user "' . $user->getUserName() . '"', $this);
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
	 * @return Tree[]
	 */
	public static function getAll() {
		if (self::$trees === null) {
			self::$trees = array();
			$rows = Database::prepare(
				"SELECT SQL_CACHE g.gedcom_id AS tree_id, g.gedcom_name AS tree_name, gs1.setting_value AS tree_title" .
				" FROM `##gedcom` g" .
				" LEFT JOIN `##gedcom_setting`      gs1 ON (g.gedcom_id=gs1.gedcom_id AND gs1.setting_name='title')" .
				" LEFT JOIN `##gedcom_setting`      gs2 ON (g.gedcom_id=gs2.gedcom_id AND gs2.setting_name='imported')" .
				" LEFT JOIN `##gedcom_setting`      gs3 ON (g.gedcom_id=gs3.gedcom_id AND gs3.setting_name='REQUIRE_AUTHENTICATION')" .
				" LEFT JOIN `##user_gedcom_setting` ugs ON (g.gedcom_id=ugs.gedcom_id AND ugs.setting_name='canedit' AND ugs.user_id=?)" .
				" WHERE " .
				"  g.gedcom_id>0 AND (" . // exclude the "template" tree
				"    EXISTS (SELECT 1 FROM `##user_setting` WHERE user_id=? AND setting_name='canadmin' AND setting_value=1)" . // Admin sees all
				"   ) OR (" .
				"    gs2.setting_value = 1 AND (" . // Allow imported trees, with either:
				"     gs3.setting_value <> 1 OR" . // visitor access
				"     IFNULL(ugs.setting_value, 'none')<>'none'" . // explicit access
				"   )" .
				"  )" .
				" ORDER BY g.sort_order, 3"
			)->execute(array(Auth::id(), Auth::id()))->fetchAll();
			foreach ($rows as $row) {
				self::$trees[] = new self((int) $row->tree_id, $row->tree_name, $row->tree_title);
			}
		}

		return self::$trees;
	}

	/**
	 * Find the tree with a specific ID.
	 *
	 * @param integer $tree_id
	 *
	 * @return Tree
	 * @throws \DomainException
	 */
	public static function findById($tree_id) {
		foreach (self::getAll() as $tree) {
			if ($tree->tree_id == $tree_id) {
				return $tree;
			}
		}
		throw new \DomainException;
	}

	/**
	 * Find the tree with a specific name.
	 *
	 * @param string $tree_name
	 *
	 * @return Tree|null
	 */
	public static function findByName($tree_name) {
		foreach (self::getAll() as $tree) {
			if ($tree->name === $tree_name) {
				return $tree;
			}
		}

		return null;
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
			$list[$tree->tree_id] = $tree->title;
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
			$list[$tree->name] = $tree->title;
		}

		return $list;
	}

	/**
	 * Find the ID number for a tree name
	 *
	 * @param string $tree_name
	 *
	 * @return integer|null
	 */
	public static function getIdFromName($tree_name) {
		foreach (self::getAll() as $tree) {
			if ($tree->name === $tree_name) {
				return $tree->tree_id;
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
		return self::findById($tree_id)->name;
	}

	/**
	 * Create a new tree
	 *
	 * @param string $tree_name
	 * @param string $tree_title
	 *
	 * @return Tree
	 */
	public static function create($tree_name, $tree_title) {
		try {
			// Create a new tree
			Database::prepare(
				"INSERT INTO `##gedcom` (gedcom_name) VALUES (?)"
			)->execute(array($tree_name));
			$tree_id = Database::prepare("SELECT LAST_INSERT_ID()")->fetchOne();
		} catch (PDOException $ex) {
			// A tree with that name already exists?
			return self::findById(self::getIdFromName($tree_name));
		}

		// Update the list of trees - to include this new one
		self::$trees = null;
		$tree        = self::findById($tree_id);

		$tree->setPreference('imported', '0');
		$tree->setPreference('title', $tree_title);

		// Module privacy
		Module::setDefaultAccess($tree_id);

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
		$tree->setPreference('REPO_FACTS_ADD', 'PHON,EMAIL,FAX,WWW,RESN');
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
		$tree->setPreference('THUMBNAIL_WIDTH', '100');
		$tree->setPreference('USE_RIN', '0');
		$tree->setPreference('USE_SILHOUETTE', '1');
		$tree->setPreference('WATERMARK_THUMB', '0');
		$tree->setPreference('WEBMASTER_USER_ID', Auth::id());
		$tree->setPreference('WEBTREES_EMAIL', '');
		$tree->setPreference('WORD_WRAPPED_NOTES', '0');

		// Default restriction settings
		$statement = Database::prepare(
			"INSERT INTO `##default_resn` (gedcom_id, xref, tag_type, resn) VALUES (?, NULL, ?, ?)"
		);
		$statement->execute(array($tree_id, 'SSN', 'confidential'));
		$statement->execute(array($tree_id, 'SOUR', 'privacy'));
		$statement->execute(array($tree_id, 'REPO', 'privacy'));
		$statement->execute(array($tree_id, 'SUBM', 'confidential'));
		$statement->execute(array($tree_id, 'SUBN', 'confidential'));

		// Genealogy data
		// It is simpler to create a temporary/unimported GEDCOM than to populate all the tables...
		$john_doe = /* I18N: This should be a common/default/placeholder name of an individual.  Put slashes around the surname. */
			I18N::translate('John /DOE/');
		$note     = I18N::translate('Edit this individual and replace their details with your own');
		Database::prepare("INSERT INTO `##gedcom_chunk` (gedcom_id, chunk_data) VALUES (?, ?)")->execute(array(
			$tree_id,
			"0 HEAD\n1 CHAR UTF-8\n0 @I1@ INDI\n1 NAME {$john_doe}\n1 SEX M\n1 BIRT\n2 DATE 01 JAN 1850\n2 NOTE {$note}\n0 TRLR\n"
		));

		// Set the initial blocks
		Database::prepare(
			"INSERT INTO `##block` (gedcom_id, location, block_order, module_name)" .
			" SELECT ?, location, block_order, module_name" .
			" FROM `##block`" .
			" WHERE gedcom_id = -1"
		)->execute(array($tree_id));

		// Update our cache
		self::$trees[$tree->tree_id] = $tree;

		return $tree;
	}

	/**
	 * Are there any pending edits for this tree, than need reviewing by a moderator.
	 *
	 * @return bool
	 */
	public function hasPendingEdit() {
		return (bool) Database::prepare(
			"SELECT 1 FROM `##change` WHERE status = 'pending' AND gedcom_id = :tree_id"
		)->execute(array(
			'tree_id' => $this->tree_id
		))->fetchOne();
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
		Database::prepare("DELETE FROM `##gedcom_chunk` WHERE gedcom_id = ?")->execute(array($this->tree_id));
		Database::prepare("DELETE FROM `##individuals`  WHERE i_file    = ?")->execute(array($this->tree_id));
		Database::prepare("DELETE FROM `##families`     WHERE f_file    = ?")->execute(array($this->tree_id));
		Database::prepare("DELETE FROM `##sources`      WHERE s_file    = ?")->execute(array($this->tree_id));
		Database::prepare("DELETE FROM `##other`        WHERE o_file    = ?")->execute(array($this->tree_id));
		Database::prepare("DELETE FROM `##places`       WHERE p_file    = ?")->execute(array($this->tree_id));
		Database::prepare("DELETE FROM `##placelinks`   WHERE pl_file   = ?")->execute(array($this->tree_id));
		Database::prepare("DELETE FROM `##name`         WHERE n_file    = ?")->execute(array($this->tree_id));
		Database::prepare("DELETE FROM `##dates`        WHERE d_file    = ?")->execute(array($this->tree_id));
		Database::prepare("DELETE FROM `##change`       WHERE gedcom_id = ?")->execute(array($this->tree_id));

		if ($keep_media) {
			Database::prepare("DELETE FROM `##link` WHERE l_file =? AND l_type<>'OBJE'")->execute(array($this->tree_id));
		} else {
			Database::prepare("DELETE FROM `##link`  WHERE l_file =?")->execute(array($this->tree_id));
			Database::prepare("DELETE FROM `##media` WHERE m_file =?")->execute(array($this->tree_id));
		}
	}

	/**
	 * Delete everything relating to a tree
	 */
	public function delete() {
		// If this is the default tree, then unset it
		if (Site::getPreference('DEFAULT_GEDCOM') === self::getNameFromId($this->tree_id)) {
			Site::setPreference('DEFAULT_GEDCOM', '');
		}

		$this->deleteGenealogyData(false);

		Database::prepare("DELETE `##block_setting` FROM `##block_setting` JOIN `##block` USING (block_id) WHERE gedcom_id=?")->execute(array($this->tree_id));
		Database::prepare("DELETE FROM `##block`               WHERE gedcom_id = ?")->execute(array($this->tree_id));
		Database::prepare("DELETE FROM `##user_gedcom_setting` WHERE gedcom_id = ?")->execute(array($this->tree_id));
		Database::prepare("DELETE FROM `##gedcom_setting`      WHERE gedcom_id = ?")->execute(array($this->tree_id));
		Database::prepare("DELETE FROM `##module_privacy`      WHERE gedcom_id = ?")->execute(array($this->tree_id));
		Database::prepare("DELETE FROM `##next_id`             WHERE gedcom_id = ?")->execute(array($this->tree_id));
		Database::prepare("DELETE FROM `##hit_counter`         WHERE gedcom_id = ?")->execute(array($this->tree_id));
		Database::prepare("DELETE FROM `##default_resn`        WHERE gedcom_id = ?")->execute(array($this->tree_id));
		Database::prepare("DELETE FROM `##gedcom_chunk`        WHERE gedcom_id = ?")->execute(array($this->tree_id));
		Database::prepare("DELETE FROM `##log`                 WHERE gedcom_id = ?")->execute(array($this->tree_id));
		Database::prepare("DELETE FROM `##gedcom`              WHERE gedcom_id = ?")->execute(array($this->tree_id));

		// After updating the database, we need to fetch a new (sorted) copy
		self::$trees = null;
	}

	/**
	 * Export the tree to a GEDCOM file
	 *
	 * @param resource $stream
	 */
	public function exportGedcom($stream) {
		$stmt = Database::prepare(
			"SELECT i_gedcom AS gedcom FROM `##individuals` WHERE i_file = :tree_id_1" .
			" UNION ALL " .
			"SELECT f_gedcom AS gedcom FROM `##families`    WHERE f_file = :tree_id_2" .
			" UNION ALL " .
			"SELECT s_gedcom AS gedcom FROM `##sources`     WHERE s_file = :tree_id_3" .
			" UNION ALL " .
			"SELECT o_gedcom AS gedcom FROM `##other`       WHERE o_file = :tree_id_4 AND o_type NOT IN ('HEAD', 'TRLR')" .
			" UNION ALL " .
			"SELECT m_gedcom AS gedcom FROM `##media`       WHERE m_file = :tree_id_5"
		)->execute(array(
			'tree_id_1' => $this->tree_id,
			'tree_id_2' => $this->tree_id,
			'tree_id_3' => $this->tree_id,
			'tree_id_4' => $this->tree_id,
			'tree_id_5' => $this->tree_id
		));

		$buffer = reformat_record_export(gedcom_header($this->name));
		while ($row = $stmt->fetch()) {
			$buffer .= reformat_record_export($row->gedcom);
			if (strlen($buffer) > 65535) {
				fwrite($stream, $buffer);
				$buffer = '';
			}
		}
		fwrite($stream, $buffer . '0 TRLR' . WT_EOL);
	}

	/**
	 * Import data from a gedcom file into this tree.
	 *
	 * @param string  $path       The full path to the (possibly temporary) file.
	 * @param string  $filename   The preferred filename, for export/download.
	 *
	 * @throws \Exception
	 */
	public function importGedcomFile($path, $filename) {
		// Read the file in blocks of roughly 64K.  Ensure that each block
		// contains complete gedcom records.  This will ensure we don’t split
		// multi-byte characters, as well as simplifying the code to import
		// each block.

		$file_data = '';
		$fp        = fopen($path, 'rb');

		// Don’t allow the user to cancel the request.  We do not want to be left with an incomplete transaction.
		ignore_user_abort(true);

		Database::beginTransaction();
		$this->deleteGenealogyData($this->getPreference('keep_media'));
		$this->setPreference('gedcom_filename', $filename);
		$this->setPreference('imported', '0');

		while (!feof($fp)) {
			$file_data .= fread($fp, 65536);
			// There is no strrpos() function that searches for substrings :-(
			for ($pos = strlen($file_data) - 1; $pos > 0; --$pos) {
				if ($file_data[$pos] === '0' && ($file_data[$pos - 1] === "\n" || $file_data[$pos - 1] === "\r")) {
					// We’ve found the last record boundary in this chunk of data
					break;
				}
			}
			if ($pos) {
				Database::prepare(
					"INSERT INTO `##gedcom_chunk` (gedcom_id, chunk_data) VALUES (?, ?)"
				)->execute(array($this->tree_id, substr($file_data, 0, $pos)));
				$file_data = substr($file_data, $pos);
			}
		}
		Database::prepare(
			"INSERT INTO `##gedcom_chunk` (gedcom_id, chunk_data) VALUES (?, ?)"
		)->execute(array($this->tree_id, $file_data));

		Database::commit();
		fclose($fp);
	}

	/**
	 * Generate a new XREF, unique across all family trees
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	public function getNewXref($type = 'INDI') {
		/** @var string[] Which tree preference is used for which record type */
		static $type_to_preference = array(
			'INDI' => 'GEDCOM_ID_PREFIX',
			'FAM'  => 'FAM_ID_PREFIX',
			'OBJE' => 'MEDIA_ID_PREFIX',
			'NOTE' => 'NOTE_ID_PREFIX',
			'SOUR' => 'SOURCE_ID_PREFIX',
			'REPO' => 'REPO_ID_PREFIX',
		);

		if (array_key_exists($type, $type_to_preference)) {
			$prefix = $this->getPreference($type_to_preference[$type]);
		} else {
			// Use the first non-underscore character
			$prefix = substr(trim($type, '_'), 0, 1);
		}

		do {
			// Use LAST_INSERT_ID(expr) to provide a transaction-safe sequence.  See
			// http://dev.mysql.com/doc/refman/5.6/en/information-functions.html#function_last-insert-id
			$statement = Database::prepare(
				"UPDATE `##next_id` SET next_id = LAST_INSERT_ID(next_id + 1) WHERE record_type = :record_type AND gedcom_id = :tree_id"
			);
			$statement->execute(array(
				'record_type' => $type,
				'tree_id'     => $this->tree_id,
			));

			if ($statement->rowCount() === 0) {
				// First time we've used this record type.
				Database::prepare(
					"INSERT INTO `##next_id` (gedcom_id, record_type, next_id) VALUES(:tree_id, :record_type, 1)"
				)->execute(array(
					'record_type' => $type,
					'tree_id'     => $this->tree_id,
				));
				$num = 1;
			} else {
				$num = Database::prepare("SELECT LAST_INSERT_ID()")->fetchOne();
			}

			// Records may already exist with this sequence number.
			$already_used = Database::prepare(
				"SELECT i_id FROM `##individuals` WHERE i_id = :i_id" .
				" UNION ALL " .
				"SELECT f_id FROM `##families` WHERE f_id = :f_id" .
				" UNION ALL " .
				"SELECT s_id FROM `##sources` WHERE s_id = :s_id" .
				" UNION ALL " .
				"SELECT m_id FROM `##media` WHERE m_id = :m_id" .
				" UNION ALL " .
				"SELECT o_id FROM `##other` WHERE o_id = :o_id" .
				" UNION ALL " .
				"SELECT xref FROM `##change` WHERE xref = :xref"
			)->execute(array(
				'i_id' => $prefix . $num,
				'f_id' => $prefix . $num,
				's_id' => $prefix . $num,
				'm_id' => $prefix . $num,
				'o_id' => $prefix . $num,
				'xref' => $prefix . $num,
			))->fetchOne();
		} while ($already_used);

		return $prefix . $num;
	}

	/**
	 * Create a new record from GEDCOM data.
	 *
	 * @param string $gedcom
	 *
	 * @return GedcomRecord
	 * @throws \Exception
	 */
	public function createRecord($gedcom) {
		if (preg_match('/^0 @(' . WT_REGEX_XREF . ')@ (' . WT_REGEX_TAG . ')/', $gedcom, $match)) {
			$xref = $match[1];
			$type = $match[2];
		} else {
			throw new \Exception('Invalid argument to GedcomRecord::createRecord(' . $gedcom . ')');
		}
		if (strpos("\r", $gedcom) !== false) {
			// MSDOS line endings will break things in horrible ways
			throw new \Exception('Evil line endings found in GedcomRecord::createRecord(' . $gedcom . ')');
		}

		// webtrees creates XREFs containing digits.  Anything else (e.g. “new”) is just a placeholder.
		if (!preg_match('/\d/', $xref)) {
			$xref   = $this->getNewXref($type);
			$gedcom = preg_replace('/^0 @(' . WT_REGEX_XREF . ')@/', '0 @' . $xref . '@', $gedcom);
		}

		// Create a change record, if not already present
		if (!preg_match('/\n1 CHAN/', $gedcom)) {
			$gedcom .= "\n1 CHAN\n2 DATE " . date('d M Y') . "\n3 TIME " . date('H:i:s') . "\n2 _WT_USER " . Auth::user()->getUserName();
		}

		// Create a pending change
		Database::prepare(
			"INSERT INTO `##change` (gedcom_id, xref, old_gedcom, new_gedcom, user_id) VALUES (?, ?, '', ?, ?)"
		)->execute(array(
			$this->tree_id,
			$xref,
			$gedcom,
			Auth::id()
		));

		Log::addEditLog('Create: ' . $type . ' ' . $xref);

		// Accept this pending change
		if (Auth::user()->getPreference('auto_accept')) {
			accept_all_changes($xref, $this->tree_id);
		}
		// Return the newly created record.  Note that since GedcomRecord
		// has a cache of pending changes, we cannot use it to create a
		// record with a newly created pending change.
		return GedcomRecord::getInstance($xref, $this->tree_id, $gedcom);
	}
}
