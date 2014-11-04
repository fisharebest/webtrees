<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
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

use Rhumsaa\Uuid\Uuid;
use WT\Auth;

// Note that the user favorites module simply extends this module, so ensure that the
// logic works for both.
class gedcom_favorites_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Favorites');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Favorites” module */ WT_I18N::translate('Display and manage a family tree’s favorite pages.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $ctype, $show_full, $PEDIGREE_FULL_DETAILS, $controller;

		self::updateSchema(); // make sure the favorites table has been created

		$action = WT_Filter::get('action');
		switch ($action) {
		case 'deletefav':
			$favorite_id = WT_Filter::getInteger('favorite_id');
			if ($favorite_id) {
				self::deleteFavorite($favorite_id);
			}
			break;
		case 'addfav':
			$gid      = WT_Filter::get('gid', WT_REGEX_XREF);
			$favnote  = WT_Filter::get('favnote');
			$url      = WT_Filter::getUrl('url');
			$favtitle = WT_Filter::get('favtitle');

			if ($gid) {
				$record = WT_GedcomRecord::getInstance($gid);
				if ($record && $record->canShow()) {
					self::addFavorite(array(
						'user_id'   => $ctype === 'user' ? Auth::id() : null,
						'gedcom_id' => WT_GED_ID,
						'gid'       => $record->getXref(),
						'type'      => $record::RECORD_TYPE,
						'url'       => null,
						'note'      => $favnote,
						'title'     => $favtitle,
					));
				}
			} elseif ($url) {
				self::addFavorite(array(
					'user_id'   => $ctype === 'user' ? Auth::id() : null,
					'gedcom_id' => WT_GED_ID,
					'gid'       => null,
					'type'      => 'URL',
					'url'       => $url,
					'note'      => $favnote,
					'title'     => $favtitle ? $favtitle : $url,
				));
			}
			break;
		}

		$block = get_block_setting($block_id, 'block', false);
		if ($cfg) {
			foreach (array('block') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name = $cfg[$name];
				}
			}
		}

		// Override GEDCOM configuration temporarily
		if (isset($show_full)) $saveShowFull = $show_full;
		$savePedigreeFullDetails = $PEDIGREE_FULL_DETAILS;
		$show_full = 1;
		$PEDIGREE_FULL_DETAILS = 1;

		$userfavs = $this->getFavorites($ctype === 'user' ? Auth::id() : WT_GED_ID);
		if (!is_array($userfavs)) {
			$userfavs = array();
		}

		$id=$this->getName().$block_id;
		$class=$this->getName().'_block';
		$title=$this->getTitle();

		if (Auth::check()) {
			$controller
				->addExternalJavascript(WT_STATIC_URL . 'js/autocomplete.js')
				->addInlineJavascript('autocomplete();');
		}

		$content = '';
		$style = 2; // 1 means "regular box", 2 means "wide box"
		if ($userfavs) {
			foreach ($userfavs as $key=>$favorite) {
				if (isset($favorite['id'])) $key=$favorite['id'];
				$removeFavourite = '<a class="font9" href="index.php?ctype='.$ctype.'&amp;action=deletefav&amp;favorite_id='.$key.'" onclick="return confirm(\''.WT_I18N::translate('Are you sure you want to remove this item from your list of favorites?').'\');">'.WT_I18N::translate('Remove').'</a> ';
				if ($favorite['type']=='URL') {
					$content .= '<div id="boxurl'.$key.'.0" class="person_box">';
					if ($ctype=='user' || WT_USER_GEDCOM_ADMIN) $content .= $removeFavourite;
					$content .= '<a href="'.$favorite['url'].'"><b>'.$favorite['title'].'</b></a>';
					$content .= '<br>'.$favorite['note'];
					$content .= '</div>';
				} else {
					$record=WT_GedcomRecord::getInstance($favorite['gid']);
					if ($record && $record->canShow()) {
						if ($record instanceof WT_Individual) {
							$content .= '<div id="box'.$favorite["gid"].'.0" class="person_box action_header';
							switch ($record->getsex()) {
							case 'M':
								break;
							case 'F':
								$content .= 'F';
								break;
							default:
								$content .= 'NN';
								break;
							}
							$content .= '">';
							if ($ctype=="user" || WT_USER_GEDCOM_ADMIN) $content .= $removeFavourite;
							ob_start();
							print_pedigree_person($record, $style);
							$content .= ob_get_clean();
							$content .= $favorite['note'];
							$content .= '</div>';
						} else {
							$content .= '<div id="box'.$favorite['gid'].'.0" class="person_box">';
							if ($ctype=='user' || WT_USER_GEDCOM_ADMIN) {
								$content .= $removeFavourite;
							}
							$content .= $record->format_list('span');
							$content .= '<br>'.$favorite['note'];
							$content .= '</div>';
						}
					}
				}
			}
		}
		if ($ctype=='user' || WT_USER_GEDCOM_ADMIN) {
			$uniqueID = Uuid::uuid4(); // This block can theoretically appear multiple times, so use a unique ID.
			$content .= '<div class="add_fav_head">';
			$content .= '<a href="#" onclick="return expand_layer(\'add_fav'.$uniqueID.'\');">'.WT_I18N::translate('Add a new favorite').'<i id="add_fav'.$uniqueID.'_img" class="icon-plus"></i></a>';
			$content .= '</div>';
			$content .= '<div id="add_fav'.$uniqueID.'" style="display: none;">';
			$content .= '<form name="addfavform" method="get" action="index.php">';
			$content .= '<input type="hidden" name="action" value="addfav">';
			$content .= '<input type="hidden" name="ctype" value="'.$ctype.'">';
			$content .= '<input type="hidden" name="ged" value="'.WT_GEDCOM.'">';
			$content .= '<div class="add_fav_ref">';
			$content .= '<input type="radio" name="fav_category" value="record" checked="checked" onclick="jQuery(\'#gid'.$uniqueID.'\').removeAttr(\'disabled\'); jQuery(\'#url, #favtitle\').attr(\'disabled\',\'disabled\').val(\'\');">';
			$content .= '<label for="gid'.$uniqueID.'">'.WT_I18N::translate('Enter an individual, family, or source ID').'</label>';
			$content .= '<input class="pedigree_form" data-autocomplete-type="IFSRO" type="text" name="gid" id="gid'.$uniqueID.'" size="5" value="">';
			$content .= ' '.print_findindi_link('gid'.$uniqueID);
			$content .= ' '.print_findfamily_link('gid'.$uniqueID);
			$content .= ' '.print_findsource_link('gid'.$uniqueID);
			$content .= ' '.print_findrepository_link('gid'.$uniqueID);
			$content .= ' '.print_findnote_link('gid'.$uniqueID);
			$content .= ' '.print_findmedia_link('gid'.$uniqueID);
			$content .= '</div>';
			$content .= '<div class="add_fav_url">';
			$content .= '<input type="radio" name="fav_category" value="url" onclick="jQuery(\'#url, #favtitle\').removeAttr(\'disabled\'); jQuery(\'#gid'.$uniqueID.'\').attr(\'disabled\',\'disabled\').val(\'\');">';
			$content .= '<input type="text" name="url" id="url" size="20" value="" placeholder="'.WT_Gedcom_Tag::getLabel('URL').'" disabled="disabled"> ';
			$content .= '<input type="text" name="favtitle" id="favtitle" size="20" value="" placeholder="'.WT_I18N::translate('Title').'" disabled="disabled">';
			$content .= '<p>'.WT_I18N::translate('Enter an optional note about this favorite').'</p>';
			$content .= '<textarea name="favnote" rows="6" cols="50"></textarea>';
			$content .= '</div>';
			$content .= '<input type="submit" value="'.WT_I18N::translate('Add').'">';
			$content .= '</form></div>';
		}

		if ($template) {
			if ($block) {
				require WT_THEME_DIR.'templates/block_small_temp.php';
			} else {
				require WT_THEME_DIR.'templates/block_main_temp.php';
			}
		} else {
			return $content;
		}
		// Restore GEDCOM configuration
		unset($show_full);
		if (isset($saveShowFull)) $show_full = $saveShowFull;
		$PEDIGREE_FULL_DETAILS = $savePedigreeFullDetails;
	}

	// Implement class WT_Module_Block
	public function loadAjax() {
		return false;
	}

	// Implement class WT_Module_Block
	public function isUserBlock() {
		return false;
	}

	// Implement class WT_Module_Block
	public function isGedcomBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
		if (WT_Filter::postBool('save') && WT_Filter::checkCsrf()) {
			set_block_setting($block_id, 'block',  WT_Filter::postBool('block'));
			exit;
		}

		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$block=get_block_setting($block_id, 'block', false);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: label for a yes/no option */ WT_I18N::translate('Add a scrollbar when block contents grow');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('block', $block);
		echo '</td></tr>';
	}

	// Delete a favorite from the database
	public static function deleteFavorite($favorite_id) {
		return (bool)
			WT_DB::prepare("DELETE FROM `##favorite` WHERE favorite_id=?")
			->execute(array($favorite_id));
	}

	// Store a new favorite in the database
	public static function addFavorite($favorite) {
		// -- make sure a favorite is added
		if (empty($favorite['gid']) && empty($favorite['url'])) {
			return false;
		}

		//-- make sure this is not a duplicate entry
		$sql = "SELECT SQL_NO_CACHE 1 FROM `##favorite` WHERE";
		if (!empty($favorite['gid'])) {
			$sql.=" xref=?";
			$vars=array($favorite['gid']);
		} else {
			$sql.=" url=?";
			$vars=array($favorite['url']);
		}
		$sql.=" AND gedcom_id=?";
		$vars[]=$favorite['gedcom_id'];
		if ($favorite['user_id']) {
			$sql.=" AND user_id=?";
			$vars[]=$favorite['user_id'];
		} else {
			$sql.=" AND user_id IS NULL";
		}

		if (WT_DB::prepare($sql)->execute($vars)->fetchOne()) {
			return false;
		}

		//-- add the favorite to the database
		return (bool)
			WT_DB::prepare("INSERT INTO `##favorite` (user_id, gedcom_id, xref, favorite_type, url, title, note) VALUES (? ,? ,? ,? ,? ,? ,?)")
				->execute(array($favorite['user_id'], $favorite['gedcom_id'], $favorite['gid'], $favorite['type'], $favorite['url'], $favorite['title'], $favorite['note']));
	}

	// Get favorites for a user or family tree
	public static function getFavorites($gedcom_id) {
		self::updateSchema(); // make sure the favorites table has been created

		return
			WT_DB::prepare(
				"SELECT SQL_CACHE favorite_id AS id, user_id, gedcom_id, xref AS gid, favorite_type AS type, title, note, url".
				" FROM `##favorite` WHERE gedcom_id=? AND user_id IS NULL")
			->execute(array($gedcom_id))
			->fetchAll(PDO::FETCH_ASSOC);
	}

	protected static function updateSchema() {
		// Create tables, if not already present
		try {
			WT_DB::updateSchema(WT_ROOT.WT_MODULES_DIR.'gedcom_favorites/db_schema/', 'FV_SCHEMA_VERSION', 4);
		} catch (PDOException $ex) {
			// The schema update scripts should never fail.  If they do, there is no clean recovery.
			die($ex);
		}
	}
}
