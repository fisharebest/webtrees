<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// Note that the user favorites module simply extends this module, so ensure that the
// logic works for both.
class gedcom_favorites_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Favorites');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the "Favorites" module */ WT_I18N::translate('Display and manage a family tree’s favorite pages.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $ctype, $show_full, $PEDIGREE_FULL_DETAILS, $BROWSERTYPE;

		self::updateSchema(); // make sure the favorites table has been created

		$action=safe_GET('action');
		switch ($action) {
		case 'deletefav':
			$favorite_id=safe_GET('favorite_id');
			if ($favorite_id) {
				self::deleteFavorite($favorite_id);
			}
			unset($_GET['action']);
			break;
		case 'addfav':
			$gid     =safe_GET('gid');
			$favnote =safe_GET('favnote');
			$url     =safe_GET('url', WT_REGEX_URL);
			$favtitle=safe_GET('favtitle');

			if ($gid) {
				$record=WT_GedcomRecord::getInstance($gid);
				if ($record && $record->canDisplayDetails()) {
					self::addFavorite(array(
						'user_id'  =>$ctype=='user' ? WT_USER_ID : null,
						'gedcom_id'=>WT_GED_ID,
						'gid'      =>$record->getXref(),
						'type'     =>$record->getType(),
						'url'      =>null,
						'note'     =>$favnote,
						'title'    =>$favtitle,
					));
				}
			} elseif ($url) {
				self::addFavorite(array(
					'user_id'  =>$ctype=='user' ? WT_USER_ID : null,
					'gedcom_id'=>WT_GED_ID,
					'gid'      =>null,
					'type'     =>'URL',
					'url'      =>$url,
					'note'     =>$favnote,
					'title'    =>$favtitle ? $favtitle : $url,
				));
			}
			unset($_GET['action']);
			break;
		}

		$block=get_block_setting($block_id, 'block', false);
		if ($cfg) {
			foreach (array('block') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name=$cfg[$name];
				}
			}
		}

		// Override GEDCOM configuration temporarily
		if (isset($show_full)) $saveShowFull = $show_full;
		$savePedigreeFullDetails = $PEDIGREE_FULL_DETAILS;
		$show_full = 1;
		$PEDIGREE_FULL_DETAILS = 1;

		$userfavs = $this->getFavorites($ctype=='user' ? WT_USER_ID : WT_GED_ID);
		if (!is_array($userfavs)) $userfavs = array();

		$id=$this->getName().$block_id;
		$class=$this->getName().'_block';
		$title=$this->getTitle();

		if (WT_USER_ID && $ENABLE_AUTOCOMPLETE) {
			$content = '<script type="text/javascript" src="'.WT_STATIC_URL.'js/jquery/jquery.autocomplete.js"></script>
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				$("input[name^=gid]").autocomplete("autocomplete.php", {
					extraParams: {field:"IFSRO"},
					formatItem: function(row, i) {
						return row[0] + " (" + row[1] + ")";
					},
					formatResult: function(row) {
						return row[1];
					},
					width: 400,
					minChars: 2
				});
			});
			</script>';
		} else $content = '';

		if ($block) {
			$style = 2; // 1 means "regular box", 2 means "wide box"
			$tableWidth = ($BROWSERTYPE=='msie') ? '95%' : '99%'; // IE needs to have room for vertical scroll bar inside the box
			$cellSpacing = '1px';
		} else {
			$style = 2;
			$tableWidth = '99%';
			$cellSpacing = '3px';
		}
		if ($userfavs) {
			$content .= "<table width=\"{$tableWidth}\" style=\"border:none\" cellspacing=\"{$cellSpacing}\">";
			foreach ($userfavs as $key=>$favorite) {
				if (isset($favorite['id'])) $key=$favorite['id'];
				$removeFavourite = "<a class=\"font9\" href=\"index.php?ctype={$ctype}&amp;action=deletefav&amp;favorite_id={$key}\" onclick=\"return confirm('".WT_I18N::translate('Are you sure you want to remove this item from your list of Favorites?')."');\">".WT_I18N::translate('Remove')."</a><br>";
				$content .= '<tr><td>';
				if ($favorite['type']=='URL') {
					$content .= "<div id=\"boxurl".$key.".0\" class=\"person_box\">";
					if ($ctype=='user' || WT_USER_GEDCOM_ADMIN) $content .= $removeFavourite;
					$content .= "<a href=\"".$favorite['url']."\"><b>".$favorite['title'].'</b></a>';
					$content .= '<br>'.$favorite['note'];
					$content .= '</div>';
				} else {
					$record=WT_GedcomRecord::getInstance($favorite['gid']);
					if ($record && $record->canDisplayDetails()) {
						if ($record->getType()=='INDI') {
							$content .= "<div id=\"box".$favorite["gid"].".0\" class=\"person_box action_header";
							switch($record->getsex()) {
								case 'M':
									break;
								case 'F':
									$content.='F';
									break;
								case 'U':
									$content.='NN';
									break;
							}
							$content .= "\">";
							if ($ctype=="user" || WT_USER_GEDCOM_ADMIN) $content .= $removeFavourite;
							ob_start();
							print_pedigree_person($record, $style, 1, $key);
							$content .= ob_get_clean();
							$content .= $favorite['note'];
							$content .= "</div>";
						} else {
							$record=WT_GedcomRecord::getInstance($favorite['gid']);
							$content .= "<div id=\"box".$favorite['gid'].".0\" class=\"person_box\">";
							if ($ctype=='user' || WT_USER_GEDCOM_ADMIN) $content .= $removeFavourite;
							if ($record) {
								$content.=$record->format_list('span');
							} else {
								$content.=WT_I18N::translate('No such ID exists in this GEDCOM file.');
							}
							$content .= '<br>'.$favorite['note'];
							$content .= '</div>';
						}
					}
				}
				$content .= '</td></tr>';
			}
			$content .= '</table>';
		}
		if ($ctype=='user' || WT_USER_GEDCOM_ADMIN) {
			$content .=
			WT_JS_START.'var pastefield; function paste_id(value) {pastefield.value=value;}'.WT_JS_END.
			'<br>';
			$uniqueID = floor(microtime() * 1000000); // This block can theoretically appear multiple times, so use a unique ID.
			$content .= "<b><a href=\"#\" onclick=\"return expand_layer('add_fav{$uniqueID}');\"><i id=\"add_fav{$uniqueID}_img\" class=\"icon-plus\"></i> ".WT_I18N::translate('Add a new favorite')."</a></b>";
			$content .= "<br><div id=\"add_fav{$uniqueID}\" style=\"display: none;\">";
			$content .= "<form name=\"addfavform\" method=\"get\" action=\"index.php\">";
			$content .= "<input type=\"hidden\" name=\"action\" value=\"addfav\">";
			$content .= "<input type=\"hidden\" name=\"ctype\" value=\"$ctype\">";
			$content .= "<input type=\"hidden\" name=\"ged\" value=\"".WT_GEDCOM."\">";
			$content .= "<table width=\"{$tableWidth}\" style=\"border:none\" cellspacing=\"{$cellSpacing}\" class=\"center\">";
			$content .= "<tr><td>".WT_I18N::translate('Enter a Person, Family, or Source ID')." <br>";
			$content .= "<input class=\"pedigree_form\" type=\"text\" name=\"gid\" id=\"gid{$uniqueID}\" size=\"5\" value=\"\">";

			$content .= ' '.print_findindi_link('gid'.$uniqueID);
			$content .= ' '.print_findfamily_link('gid'.$uniqueID);
			$content .= ' '.print_findsource_link('gid'.$uniqueID);
			$content .= ' '.print_findrepository_link('gid'.$uniqueID);
			$content .= ' '.print_findnote_link('gid'.$uniqueID);
			$content .= ' '.print_findmedia_link('gid'.$uniqueID);

			$content .= "<br>".WT_I18N::translate('OR<br />Enter a URL and a title');
			$content .= "<table><tr><td>".WT_Gedcom_Tag::getLabel('URL')."</td><td><input type=\"text\" name=\"url\" size=\"40\" value=\"\"></td></tr>";
			$content .= "<tr><td>".WT_I18N::translate('Title:')."</td><td><input type=\"text\" name=\"favtitle\" size=\"40\" value=\"\"></td></tr></table>";
			if ($block) $content .= "</td></tr><tr><td><br>";
			else $content .= "</td><td>";
			$content .= WT_I18N::translate('Enter an optional note about this favorite');
			$content .= "<br><textarea name=\"favnote\" rows=\"6\" cols=\"50\"></textarea>";
			$content .= "</td></tr></table>";
			$content .= "<br><input type=\"submit\" value=\"".WT_I18N::translate('Add')."\" style=\"font-size:8pt;\">";
			$content .= "</form></div>";
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
		if (safe_POST_bool('save')) {
			set_block_setting($block_id, 'block',  safe_POST_bool('block'));
			echo WT_JS_START, 'window.opener.location.href=window.opener.location.href;window.close();', WT_JS_END;
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
			WT_DB::updateSchema(WT_ROOT.WT_MODULES_DIR.'gedcom_favorites/db_schema/', 'FV_SCHEMA_VERSION', 2);
		} catch (PDOException $ex) {
			// The schema update scripts should never fail.  If they do, there is no clean recovery.
			die($ex);
		}
	}
}
