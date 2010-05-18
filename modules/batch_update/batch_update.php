<?php
/**
 * Batch Update module for phpGedView
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2008  PGV Development Team.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Module
 * @author Greg Roach
 * $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

if (!WT_USER_GEDCOM_ADMIN) {
	header('Location: module.php?mod=batch_update');
	exit;
}

require WT_ROOT.'includes/functions/functions_edit.php';

class batch_update {
	var $plugin   =null; // Form parameter: chosen plugin
	var $xref     =null; // Form parameter: record to update
	var $action   =null; // Form parameter: how to update record
	var $data     =null; // Form parameter: additional details for $action
	var $plugins  =null; // Array of available plugins
	var $PLUGIN   =null; // An instance of a plugin object
	var $all_xrefs=null; // An array of all xrefs that might need to be updated
	var $prev_xref=null; // The previous xref to process
	var $curr_xref=null; // The xref to process
	var $next_xref=null; // The next xref to process
	var $record   =null; // A GedcomRecord object corresponding to $curr_xref

	// Main entry point - called by the PGV framework in response to module.php?mod=batch_update
	function main() {
		// HTML common to all pages
		$html=
			print_header(i18n::translate('Batch Update')).
			self::getJavascript().
			'<form id="batch_update_form" action="module.php" method="get">'.
			'<input type="hidden" name="mod" value="batch_update">'.
			'<input type="hidden" name="mod_action" value="batch_update">'.
			'<input type="hidden" name="xref"   value="'.$this->xref.'">'.
			'<input type="hidden" name="action" value="">'. // will be set by javascript for next update
			'<input type="hidden" name="data"   value="">'. // will be set by javascript for next update
			'<table class="list_table width100"><tr valign="top">'.
			'<td class="list_label">'.i18n::translate('GEDCOM File:').'</td>'.
			'<td class="optionbox wrap"><select name="GEDCOM" onchange="reset_reload();">';

		$all_gedcoms=get_all_gedcoms();
		asort($all_gedcoms);
		foreach ($all_gedcoms as $ged_id=>$gedcom) {
			$html.='<option value="'.$gedcom.'"'.($ged_id==WT_GED_ID ? ' selected="selected"' : '').'>'.get_gedcom_setting($ged_id, 'title').'</option>';
		}
		$html.='</select></td></tr><tr valign="top"><td class="list_label">'.i18n::translate('Batch Update').':</td><td class="optionbox wrap"><select name="plugin" onchange="reset_reload();">';
		if (!$this->plugin) {
			$html.='<option value="" selected="selected"></option>';
		}

		foreach ($this->plugins as $class=>$plugin) {
			$html.='<option value="'.$class.'"'.($this->plugin==$class ? ' selected="selected"' : '').'>'.$plugin->getName().'</option>';
		}
		$html.='</select>';
		if ($this->PLUGIN) {
			$html.='<br/><i>'.$this->PLUGIN->getDescription().'</i>';
		}
		$html.='</td></tr>';

		// If a plugin is selected, display the details
		if ($this->PLUGIN) {
			$html.=$this->PLUGIN->getOptionsForm();
			if (substr($this->action, -4)=='_all') {
				// Reset - otherwise we might "undo all changes", which refreshes the
				// page, which makes them all again!
				$html.='<script type="text/javascript">reset_reload();</script>';
			} else {
				if ($this->curr_xref) {
					// Create an object, so we can get the latest version of the name.
					$object=GedcomRecord::getInstance($this->curr_xref);
					$object->setGedcomRecord($this->record);

					$html.=
						'</table><br/><table class="list_table width100"><tr valign="middle"><td class="list_label center width20">'.
						self::createSubmitButton(i18n::translate('&lt; Previous'), $this->prev_xref).
						self::createSubmitButton(i18n::translate('Next &gt;'), $this->next_xref).
						'</td><td class="optionbox width80"><h1><a href="'.$object->getLinkUrl().'">'.$object->getFullName().'</a>'.
						'</h1></td>'.
						'</tr><tr><td valign="top" class="list_label center width20">'.
						'<br/>'.implode('<br/>',$this->PLUGIN->getActionButtons($this->curr_xref, $this->record)).'<br/>'.
						'</td><td dir="ltr" align="left" class="optionbox width80">'.
						$this->PLUGIN->getActionPreview($this->curr_xref, $this->record);
						'</td></tr>';
				} else {
					$html.='<tr><td colspan=2>'.i18n::translate('Nothing found.').'</td></tr>';
				}
			}
		}
		$html.='</table></form>';
		ob_start();
		print_footer();
		return $html.ob_get_clean();
	}

	// Constructor - initialise variables and validate user-input
	function __construct() {
		$this->plugins=self::getPluginList();              // List of available plugins
		$this->plugin =safe_GET('plugin', array_keys($this->plugins)); // User parameters
		$this->xref   =safe_GET('xref',   WT_REGEX_XREF);
		$this->action =safe_GET('action');
		$this->data   =safe_GET('data');

		// Don't do any processing until a plugin is chosen.
		if ($this->plugin) {
			$this->PLUGIN=new $this->plugin;
			$this->PLUGIN->getOptions();
			$this->getAllXrefs();

			switch ($this->action) {
			case '':
				break;
			case 'update':
				$record=self::getLatestRecord($this->xref, $this->all_xrefs[$this->xref]);
				if ($this->PLUGIN->doesRecordNeedUpdate($this->xref, $record)) {
					$newrecord=$this->PLUGIN->updateRecord($this->xref, $record);
					if ($newrecord!=$record) {
						if ($newrecord) {
							replace_gedrec($this->xref, WT_GED_ID, $newrecord, $this->PLUGIN->chan);
						} else {
							delete_gedrec($this->xref, WT_GED_ID);
						}
					}
				}
				$this->xref=$this->findNextXref($this->xref);
				break;
			case 'update_all':
				foreach ($this->all_xrefs as $xref=>$type) {
					$record=self::getLatestRecord($xref, $type);
					if ($this->PLUGIN->doesRecordNeedUpdate($xref, $record)) {
						$newrecord=$this->PLUGIN->updateRecord($xref, $record);
						if ($newrecord!=$record) {
							if ($newrecord) {
								replace_gedrec($xref, WT_GED_ID, $newrecord, $this->PLUGIN->chan);
							} else {
								delete_gedrec($xref, WT_GED_ID);
							}
						}
					}
				}
				$this->xref='';
				return;
			case 'delete':
				$record=self::getLatestRecord($this->xref, $this->all_xrefs[$this->xref]);
				if ($this->PLUGIN->doesRecordNeedUpdate($this->xref, $record)) {
					delete_gedrec($this->xref, WT_GED_ID);
				}
				$this->xref=$this->findNextXref($this->xref);
				break;
			case 'delete_all':
				foreach ($this->all_xrefs as $xref=>$type) {
					$record=self::getLatestRecord($xref, $type);
					if ($this->PLUGIN->doesRecordNeedUpdate($xref, $record)) {
						delete_gedrec($xref, WT_GED_ID);
					}
				}
				$xref->xref='';
				return;
			default:
				// Anything else will be handled by the plugin
				$this->PLUGIN->performAction($this->xref, $this->record, $this->action, $this->data);
				break;
			}

			// Make sure that our requested record really does need updating.
			// It may have been updated in another session, or may not have
			// been specified at all.
			if (array_key_exists($this->xref, $this->all_xrefs) &&
				$this->PLUGIN->doesRecordNeedUpdate($this->xref, self::getLatestRecord($this->xref, $this->all_xrefs[$this->xref]))) {
				$this->curr_xref=$this->xref;
			}
			// The requested record doesn't need updating - find one that does
			if (!$this->curr_xref) {
				$this->curr_xref=$this->findNextXref($this->xref);
			}
			if (!$this->curr_xref) {
				$this->curr_xref=$this->findPrevXref($this->xref);
			}
			// If we've found a record to update, get details and look for the next/prev
			if ($this->curr_xref) {
				$this->record=self::getLatestRecord($this->curr_xref, $this->all_xrefs[$this->curr_xref]);
				$this->prev_xref=$this->findPrevXref($this->curr_xref);
				$this->next_xref=$this->findNextXref($this->curr_xref);
			}
		}
	}

	// Find the next record that needs to be updated
	function findNextXref($xref) {
		foreach (array_keys($this->all_xrefs) as $key) {
			if ($key>$xref) {
				$record=self::getLatestRecord($key, $this->all_xrefs[$key]);
				if ($this->PLUGIN->doesRecordNeedUpdate($key, $record)) {
					return $key;
				}
			}
		}
		return null;
	}

	// Find the previous record that needs to be updated
	function findPrevXref($xref) {
		foreach (array_reverse(array_keys($this->all_xrefs)) as $key) {
			if ($key<$xref) {
				$record=self::getLatestRecord($key, $this->all_xrefs[$key]);
				if ($this->PLUGIN->doesRecordNeedUpdate($key, $record)) {
					return $key;
				}
			}
		}
		return null;
	}

	function getAllXrefs() {
		global $TBLPREFIX;

		$sql=array();
		$vars=array();
		foreach ($this->PLUGIN->getRecordTypesToUpdate() as $type) {
			switch ($type) {
			case 'INDI':
				$sql[]="SELECT i_id, 'INDI' FROM {$TBLPREFIX}individuals WHERE i_file=?";
				$vars[]=WT_GED_ID;
				break;
			case 'FAM':
				$sql[]="SELECT f_id, 'FAM' FROM {$TBLPREFIX}families WHERE f_file=?";
				$vars[]=WT_GED_ID;
				break;
			case 'SOUR':
				$sql[]="SELECT s_id, 'SOUR' FROM {$TBLPREFIX}sources WHERE s_file=?";
				$vars[]=WT_GED_ID;
				break;
			case 'OBJE':
				$sql[]="SELECT m_media, 'OBJE' FROM {$TBLPREFIX}media WHERE m_gedfile=?";
				$vars[]=WT_GED_ID;
				break;
			default:
				$sql[]="SELECT o_id, ? FROM {$TBLPREFIX}other WHERE o_type=? AND o_file=?";
				$vars[]=$type;
				$vars[]=$type;
				$vars[]=WT_GED_ID;
				break;
			}
		}
		$this->all_xrefs=
			WT_DB::prepare(implode(' UNION ', $sql).' ORDER BY 1 ASC')
			->execute($vars)
			->fetchAssoc();
	}

	// Scan the plugin directory for a list of plugins
	static function getPluginList() {
		$array=array();
		$dir=dirname(__FILE__).'/plugins/';
		$dir_handle=opendir($dir);
		while ($file=readdir($dir_handle)) {
			if (substr($file, -4)=='.php') {
				require dirname(__FILE__).'/plugins/'.$file;
				$class=basename($file, '.php').'_bu_plugin';
				$array[$class]=new $class;
			}
		}
		closedir($dir_handle);
		return $array;
	}

	// Javascript that gets included on every page
	static function getJavascript() {
		return
			'<script type="text/javascript">'.
			'function reset_reload() {'.
			' var bu_form=document.getElementById("batch_update_form");'.
			' bu_form.xref.value="";'.
			' bu_form.action.value="";'.
			' bu_form.data.value="";'.
			' bu_form.submit();'.
			'}</script>'
		;
	}

	// Create a submit button for our form
	static function createSubmitButton($text, $xref, $action='', $data='') {
		return
			'<input type="submit" value="'.$text.'" onclick="'.
			'this.form.xref.value=\''.htmlspecialchars($xref).'\';'.
			'this.form.action.value=\''.htmlspecialchars($action).'\';'.
			'this.form.data.value=\''.htmlspecialchars($data).'\';'.
			'return true;"'.
			($xref ? '' : ' disabled').'>';
	}

	// Get the current view of a record, allowing for pending changes
	static function getLatestRecord($xref, $type) {
		return find_gedcom_record($xref, WT_GED_ID);
	}
}

// Each plugin should extend the base_plugin class, and implement these
// two functions:
//
//  bool doesRecordNeedUpdate($xref, $gedrec)
//  string updateRecord($xref, $gedrec)
//
class base_plugin {
	var $chan=false; // User option; update change record

	// Default is to operate on INDI records
	function getRecordTypesToUpdate() {
		return array('INDI');
	}

	// Default option is just the "don't update CHAN record"
	function getOptions() {
		$this->chan=safe_GET_bool('chan');
	}

	// Default option is just the "don't update CHAN record"
	function getOptionsForm() {
		return
			'<tr valign="top"><td class="list_label width20">'.i18n::translate('Update the CHAN record').':</td>'.
			'<td class="optionbox wrap"><select name="chan" onchange="this.form.submit();">'.
			'<option value="no"' .($this->chan ? '' : ' selected="selected"').'>'.i18n::translate('No') .'</option>'.
			'<option value="yes"'.($this->chan ? ' selected="selected"' : '').'>'.i18n::translate('Yes').'</option>'.
			'</select></td></tr>';
	}

	// Default buttons are update and update_all
	function getActionButtons($xref) {
		return array(
			batch_update::createSubmitButton(i18n::translate('Update'),     $xref, 'update'),
			batch_update::createSubmitButton(i18n::translate('Update all'), $xref, 'update_all')
		);
	}

	// Default previewer for plugins with no custom preview.
	function getActionPreview($xref, $gedrec) {
		$old_lines=preg_split('/[\r\n]+/', $gedrec);
		$new_lines=preg_split('/[\r\n]+/', $this->updateRecord($xref, $gedrec));
		// Find matching lines using longest-common-subsequence algorithm.
		$lcs=self::LCS($old_lines, $new_lines, 0, count($old_lines)-1, 0, count($new_lines)-1);

		$diff_lines=array();
		$last_old=-1;
		$last_new=-1;
		while ($lcs) {
			list($old, $new)=array_shift($lcs);
			while ($last_old<$old-1) {
				$diff_lines[]=self::decorateDeletedText($old_lines[++$last_old]);
			}
			while ($last_new<$new-1) {
				$diff_lines[]=self::decorateInsertedText($new_lines[++$last_new]);
			}
			$diff_lines[]=$new_lines[$new];
			$last_old=$old;
			$last_new=$new;
		}
		while ($last_old<count($old_lines)-1) {
			$diff_lines[]=self::decorateDeletedText($old_lines[++$last_old]);
		}
		while ($last_new<count($new_lines)-1) {
			$diff_lines[]=self::decorateInsertedText($new_lines[++$last_new]);
		}

		return '<pre>'.self::createEditLinks(implode("\n", $diff_lines)).'</pre>';
	}

	// Longest Common Subsequence.
	static function LCS($X, $Y, $x1, $x2, $y1, $y2) {
		if ($x2-$x1>=0 && $y2-$y1>=0) {
			if ($X[$x1]==$Y[$y1]) {
				// Match at start of sequence
				$tmp=self::LCS($X, $Y, $x1+1, $x2, $y1+1, $y2);
				array_unshift($tmp, array($x1, $y1));
				return $tmp;
			} elseif ($X[$x2]==$Y[$y2]) {
				// Match at end of sequence
				$tmp=self::LCS($X, $Y, $x1, $x2-1, $y1, $y2-1);
				array_push($tmp, array($x2, $y2));
				return $tmp;
			} else {
				// No match.  Look for subsequences
				$tmp1=self::LCS($X, $Y, $x1, $x2, $y1, $y2-1);
				$tmp2=self::LCS($X, $Y, $x1, $x2-1, $y1, $y2);
				return count($tmp1) > count($tmp2) ? $tmp1 : $tmp2;
			}
		} else {
			// One array is empty - end recursion
			return array();
		}
	}

	// Default handler for plugin with no custom actions.
	function performAction($xref, $gedrec, $action, $data) {
	}

	// Decorate inserted/deleted text
	static function decorateInsertedText($text) {
		return '<span class="search_hit">'.$text.'</span>';
	}
	static function decorateDeletedText($text) {
		return '<span style="text-decoration:line-through;">'.$text.'</span>';
	}

	// Converted gedcom links into editable links
	static function createEditLinks($gedrec) {
		return preg_replace(
			"/@([^#@\n]+)@/m",
			'<a href="javascript:;" onclick="return edit_raw(\'\\1\');">@\\1@</a>',
			$gedrec
		);
	}
}

?>
