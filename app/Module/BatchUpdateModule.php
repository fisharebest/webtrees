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

/**
 * Class BatchUpdateModule
 */
class BatchUpdateModule extends Module implements ModuleConfigInterface {
	/** @var string  Form parameter: chosen plugin*/
	private $plugin;

	/** @var string Form parameter: record to update */
	private $xref;

	/** @var string Form parameter: how to update record */
	private $action;

	/** @var string Form parameter: additional details for $action */
	private $data;

	/** @var BatchUpdateBasePlugin[] All available plugins */
	private $plugins;

	/** @var @var BatchUpdateBasePlugin  The current plugin */
	private $PLUGIN;

	/** @var string[] n array of all xrefs that might need to be updated */
	private $all_xrefs;

	/** @var string The previous xref to process */
	private $prev_xref;

	/** @var String The current xref being process */
	private $curr_xref;

	/** @var string The next xref to process */
	private $next_xref;

	/** @var GedcomRecord The record corresponding to $curr_xref */
	private $record;

	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Batch update');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Batch update” module */ I18N::translate('Apply automatic corrections to your genealogy data.');
	}

	/** {@inheritdoc} */
	public function modAction($mod_action) {
		switch ($mod_action) {
		case 'admin_batch_update':
			$controller = new PageController;
			$controller
				->setPageTitle(I18N::translate('Batch update'))
				->restrictAccess(Auth::isAdmin())
				->pageHeader();

			echo $this->main();
			break;

		default:
			http_response_code(404);
			break;
		}
	}

	/**
	 * Main entry point
	 *
	 * @return string
	 */
	function main() {
		$this->plugins = $this->getPluginList(); // List of available plugins
		$this->plugin  = Filter::get('plugin'); // User parameters
		$this->xref    = Filter::get('xref', WT_REGEX_XREF);
		$this->action  = Filter::get('action');
		$this->data    = Filter::get('data');

		// Don't do any processing until a plugin is chosen.
		if ($this->plugin && array_key_exists($this->plugin, $this->plugins)) {
			$this->PLUGIN = new $this->plugin;
			$this->PLUGIN->getOptions();
			$this->getAllXrefs();

			switch ($this->action) {
			case 'update':
				$record = self::getLatestRecord($this->xref, $this->all_xrefs[$this->xref]);
				if ($this->PLUGIN->doesRecordNeedUpdate($this->xref, $record)) {
					$newrecord = $this->PLUGIN->updateRecord($this->xref, $record);
					if ($newrecord != $record) {
						if ($newrecord) {
							GedcomRecord::getInstance($this->xref)->updateRecord($newrecord, $this->PLUGIN->chan);
						} else {
							GedcomRecord::getInstance($this->xref)->deleteRecord();
						}
					}
				}
				$this->xref = $this->findNextXref($this->xref);
				break;
			case 'update_all':
				foreach ($this->all_xrefs as $xref=>$type) {
					$record = self::getLatestRecord($xref, $type);
					if ($this->PLUGIN->doesRecordNeedUpdate($xref, $record)) {
						$newrecord = $this->PLUGIN->updateRecord($xref, $record);
						if ($newrecord != $record) {
							if ($newrecord) {
								GedcomRecord::getInstance($this->xref)->updateRecord($newrecord, $this->PLUGIN->chan);
							} else {
								GedcomRecord::getInstance($this->xref)->deleteRecord();
							}
						}
					}
				}
				$this->xref = '';

				return '';
			}

			// Make sure that our requested record really does need updating.
			// It may have been updated in another session, or may not have
			// been specified at all.
			if (array_key_exists($this->xref, $this->all_xrefs) &&
				$this->PLUGIN->doesRecordNeedUpdate($this->xref, self::getLatestRecord($this->xref, $this->all_xrefs[$this->xref]))) {
				$this->curr_xref = $this->xref;
			}
			// The requested record doesn't need updating - find one that does
			if (!$this->curr_xref) {
				$this->curr_xref = $this->findNextXref($this->xref);
			}
			if (!$this->curr_xref) {
				$this->curr_xref = $this->findPrevXref($this->xref);
			}
			// If we've found a record to update, get details and look for the next/prev
			if ($this->curr_xref) {
				$this->prev_xref = $this->findPrevXref($this->curr_xref);
				$this->next_xref = $this->findNextXref($this->curr_xref);
			}
		}

		// HTML common to all pages
		$html =
			$this->getJavascript() .
			'<form id="batch_update_form" action="module.php" method="get">' .
			'<input type="hidden" name="mod" value="batch_update">' .
			'<input type="hidden" name="mod_action" value="admin_batch_update">' .
			'<input type="hidden" name="xref"   value="' . $this->xref . '">' .
			'<input type="hidden" name="action" value="">' . // will be set by javascript for next update
			'<input type="hidden" name="data"   value="">' . // will be set by javascript for next update
			'<table id="batch_update"><tr>' .
			'<th>' . I18N::translate('Family tree') . '</th>' .
			'<td>' . select_edit_control('ged', Tree::getNameList(), '', WT_GEDCOM, 'onchange="reset_reload();"') .
			'</td></tr><tr><th>' . I18N::translate('Batch update') . '</th><td><select name="plugin" onchange="reset_reload();">';
		if (!$this->plugin) {
			$html .= '<option value="" selected></option>';
		}

		foreach ($this->plugins as $class=>$plugin) {
			$html .= '<option value="' . $class . '" ' . ($this->plugin == $class ? 'selected' : '') . '>' . $plugin->getName() . '</option>';
		}
		$html .= '</select>';
		if ($this->PLUGIN) {
			$html .= '<br><em>' . $this->PLUGIN->getDescription() . '</em>';
		}
		$html .= '</td></tr>';

		if (!Auth::user()->getPreference('auto_accept')) {
			$html .= '<tr><td colspan="2" class="warning">' . I18N::translate('Your user account does not have “automatically approve changes” enabled.  You will only be able to change one record at a time.') . '</td></tr>';
		}

		// If a plugin is selected, display the details
		if ($this->PLUGIN) {
			$html .= $this->PLUGIN->getOptionsForm();
			if (substr($this->action, -4) == '_all') {
				// Reset - otherwise we might "undo all changes", which refreshes the
				// page, which makes them all again!
				$html .= '<script>reset_reload();</script>';
			} else {
				if ($this->curr_xref) {
					// Create an object, so we can get the latest version of the name.
					$this->record = GedcomRecord::getInstance($this->curr_xref);

					$html .=
						'</table><table id="batch_update2"><tr><td>' .
						self::createSubmitButton(I18N::translate('previous'), $this->prev_xref) .
						self::createSubmitButton(I18N::translate('next'), $this->next_xref) .
						'</td><th><a href="' . $this->record->getHtmlUrl() . '">' . $this->record->getFullName() . '</a>' .
						'</th>' .
						'</tr><tr><td valign="top">' .
						'<br>' . implode('<br>', $this->PLUGIN->getActionButtons($this->curr_xref, $this->record)) . '<br>' .
						'</td><td dir="ltr" align="left">' .
						$this->PLUGIN->getActionPreview($this->record) .
						'</td></tr>';
				} else {
					$html .= '<tr><td class="accepted" colspan=2>' . I18N::translate('Nothing found.') . '</td></tr>';
				}
			}
		}
		$html .= '</table></form>';

		return $html;
	}

	/**
	 * Find the next record that needs to be updated.
	 *
	 * @param string $xref
	 *
	 * @return string|null
	 */
	private function findNextXref($xref) {
		foreach (array_keys($this->all_xrefs) as $key) {
			if ($key > $xref) {
				$record = self::getLatestRecord($key, $this->all_xrefs[$key]);
				if ($this->PLUGIN->doesRecordNeedUpdate($key, $record)) {
					return $key;
				}
			}
		}
		return null;
	}

	/**
	 * Find the previous record that needs to be updated.
	 *
	 * @param string $xref
	 *
	 * @return string|null
	 */
	private function findPrevXref($xref) {
		foreach (array_reverse(array_keys($this->all_xrefs)) as $key) {
			if ($key < $xref) {
				$record = self::getLatestRecord($key, $this->all_xrefs[$key]);
				if ($this->PLUGIN->doesRecordNeedUpdate($key, $record)) {
					return $key;
				}
			}
		}
		return null;
	}

	/**
	 * Generate a list of all XREFs.
	 */
	private function getAllXrefs() {
		$sql = array();
		$vars = array();
		foreach ($this->PLUGIN->getRecordTypesToUpdate() as $type) {
			switch ($type) {
			case 'INDI':
				$sql[] = "SELECT i_id, 'INDI' FROM `##individuals` WHERE i_file=?";
				$vars[] = WT_GED_ID;
				break;
			case 'FAM':
				$sql[] = "SELECT f_id, 'FAM' FROM `##families` WHERE f_file=?";
				$vars[] = WT_GED_ID;
				break;
			case 'SOUR':
				$sql[] = "SELECT s_id, 'SOUR' FROM `##sources` WHERE s_file=?";
				$vars[] = WT_GED_ID;
				break;
			case 'OBJE':
				$sql[] = "SELECT m_id, 'OBJE' FROM `##media` WHERE m_file=?";
				$vars[] = WT_GED_ID;
				break;
			default:
				$sql[] = "SELECT o_id, ? FROM `##other` WHERE o_type=? AND o_file=?";
				$vars[] = $type;
				$vars[] = $type;
				$vars[] = WT_GED_ID;
				break;
			}
		}
		$this->all_xrefs =
			Database::prepare(implode(' UNION ', $sql) . ' ORDER BY 1 ASC')
				->execute($vars)
				->fetchAssoc();
	}

	/**
	 * Scan the plugin folder for a list of plugins
	 *
	 * @return BatchUpdateBasePlugin[]
	 */
	private function getPluginList() {
		$plugins = array();
		$dir_handle = opendir(__DIR__);
		while ($file = readdir($dir_handle)) {
			if (substr($file, -10) == 'Plugin.php' && $file !== 'BatchUpdateBasePlugin.php') {
				$class = __NAMESPACE__ . '\\' . basename($file, '.php');
				$plugins[$class] = new $class;
			}
		}
		closedir($dir_handle);

		return $plugins;
	}

	/**
	 * Javascript that gets included on every page
	 *
	 * @return string
	 */
	private function getJavascript() {
		return
			'<script>' .
			'function reset_reload() {' .
			' var bu_form=document.getElementById("batch_update_form");' .
			' bu_form.xref.value="";' .
			' bu_form.action.value="";' .
			' bu_form.data.value="";' .
			' bu_form.submit();' .
			'}</script>';
	}

	/**
	 * Create a submit button for our form
	 *
	 * @param string $text
	 * @param string $xref
	 * @param string $action
	 * @param string $data
	 *
	 * @return string
	 */
	public static function createSubmitButton($text, $xref, $action = '', $data = '') {
		return
			'<input type="submit" value="' . $text . '" onclick="' .
			'this.form.xref.value=\'' . Filter::escapeHtml($xref) . '\';' .
			'this.form.action.value=\'' . Filter::escapeHtml($action) . '\';' .
			'this.form.data.value=\'' . Filter::escapeHtml($data) . '\';' .
			'return true;"' .
			($xref ? '' : ' disabled') . '>';
	}

	/**
	 * Get the current view of a record, allowing for pending changes
	 *
	 * @param string $xref
	 * @param string $type
	 *
	 * @return string
	 */
	public static function getLatestRecord($xref, $type) {
		switch ($type) {
		case 'INDI':
			return Individual::getInstance($xref)->getGedcom();
		case 'FAM':
			return Family::getInstance($xref)->getGedcom();
		case 'SOUR':
			return Source::getInstance($xref)->getGedcom();
		case 'REPO':
			return Repository::getInstance($xref)->getGedcom();
		case 'OBJE':
			return Media::getInstance($xref)->getGedcom();
		case 'NOTE':
			return Note::getInstance($xref)->getGedcom();
		default:
			return GedcomRecord::getInstance($xref)->getGedcom();
		}
	}

	/** {@inheritdoc} */
	public function getConfigLink() {
		return 'module.php?mod=' . $this->getName() . '&amp;mod_action=admin_batch_update';
	}


}
