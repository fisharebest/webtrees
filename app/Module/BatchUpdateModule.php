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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Module\BatchUpdate\BatchUpdateBasePlugin;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;

/**
 * Class BatchUpdateModule
 */
class BatchUpdateModule extends AbstractModule implements ModuleConfigInterface {
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

	/** @var BatchUpdateBasePlugin  The current plugin */
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

	/**
	 * How should this module be labelled on tabs, menus, etc.?
	 *
	 * @return string
	 */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Batch update');
	}

	/**
	 * A sentence describing what this module does.
	 *
	 * @return string
	 */
	public function getDescription() {
		return /* I18N: Description of the “Batch update” module */ I18N::translate('Apply automatic corrections to your genealogy data.');
	}

	/**
	 * This is a general purpose hook, allowing modules to respond to routes
	 * of the form module.php?mod=FOO&mod_action=BAR
	 *
	 * @param string $mod_action
	 */
	public function modAction($mod_action) {
		switch ($mod_action) {
		case 'admin_batch_update':
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
	private function main() {
		global $WT_TREE;

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
							GedcomRecord::getInstance($this->xref, $WT_TREE)->updateRecord($newrecord, $this->PLUGIN->chan);
						} else {
							GedcomRecord::getInstance($this->xref, $WT_TREE)->deleteRecord();
						}
					}
				}
				$this->xref = $this->findNextXref($this->xref);
				break;
			case 'update_all':
				foreach ($this->all_xrefs as $xref => $type) {
					$record = self::getLatestRecord($xref, $type);
					if ($this->PLUGIN->doesRecordNeedUpdate($xref, $record)) {
						$newrecord = $this->PLUGIN->updateRecord($xref, $record);
						if ($newrecord != $record) {
							if ($newrecord) {
								GedcomRecord::getInstance($xref, $WT_TREE)->updateRecord($newrecord, $this->PLUGIN->chan);
							} else {
								GedcomRecord::getInstance($xref, $WT_TREE)->deleteRecord();
							}
						}
					}
				}
				$this->xref = '';
				break;
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
		$controller = new PageController;
			$controller
				->setPageTitle(I18N::translate('Batch update'))
				->restrictAccess(Auth::isAdmin())
				->pageHeader();

		echo $this->getJavascript();
		?>
		<ol class="breadcrumb small">
			<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
			<li><a href="admin_modules.php"><?php echo I18N::translate('Module administration'); ?></a></li>
			<li class="active"><?php echo $controller->getPageTitle(); ?></li>
		</ol>
		<h2><?php echo $controller->getPageTitle(); ?></h2>
		
		<form id="batch_update_form" class="form-horizontal" action="module.php" method="get">
			<input type="hidden" name="mod" value="batch_update">
			<input type="hidden" name="mod_action" value="admin_batch_update">
			<input type="hidden" name="xref"   value="' . $this->xref . '">
			<input type="hidden" name="action" value=""><?php // will be set by javascript for next update  ?>
			<input type="hidden" name="data"   value=""><?php // will be set by javascript for next update  ?>
			<div class="form-group">
				<label class="control-label col-sm-3"><?php echo I18N::translate('Family tree') ?></label>
				<div class="col-sm-9">
		<?php echo FunctionsEdit::selectEditControl('ged', Tree::getNameList(), '', $WT_TREE->getName(), 'class="form-control" onchange="reset_reload();"') ?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3"><?php echo I18N::translate('Batch update') ?></label>
				<div class="col-sm-9">
					<select class="form-control" name="plugin" onchange="reset_reload();">
						<?php if (!$this->plugin): ?>
							<option value="" selected></option>
						<?php endif; ?>
						<?php foreach ($this->plugins as $class => $plugin): ?>
							<option value="<?php echo $class ?>" <?php echo $this->plugin == $class ? 'selected' : ''; ?>><?php echo $plugin->getName(); ?></option>
					<?php endforeach; ?>
					</select>
					<?php if ($this->PLUGIN): ?>
						<p class="small text-muted"><?php echo $this->PLUGIN->getDescription() ?></p>
		<?php endif; ?>
				</div>
			</div>

				<?php if (!Auth::user()->getPreference('auto_accept')): ?>
				<div class="alert alert-danger">
				<?php echo I18N::translate('Your user account does not have “automatically approve changes” enabled.  You will only be able to change one record at a time.'); ?>
				</div>
			<?php endif; ?>

			<?php // If a plugin is selected, display the details ?>
			<?php if ($this->PLUGIN): ?>
				<?php echo $this->PLUGIN->getOptionsForm(); ?>
				<?php if (substr($this->action, -4) == '_all'): ?>
					<?php // Reset - otherwise we might "undo all changes", which refreshes the ?>
					<?php // page, which makes them all again!  ?>
					<script>reset_reload();</script>
			<?php else: ?>
					<hr>
					<div id="batch_update2" class="col-sm-12">
						<?php if ($this->curr_xref): ?>
							<?php // Create an object, so we can get the latest version of the name. ?>
								<?php $this->record = GedcomRecord::getInstance($this->curr_xref, $WT_TREE); ?>			
							<div class="form-group">
								<?php echo self::createSubmitButton(I18N::translate('previous'), $this->prev_xref) ?>
					<?php echo self::createSubmitButton(I18N::translate('next'), $this->next_xref) ?>
							</div>
							<div class="form-group">
								<a class="lead" href="<?php echo $this->record->getHtmlUrl(); ?>"><?php echo $this->record->getFullName(); ?></a>
					<?php echo $this->PLUGIN->getActionPreview($this->record); ?>
							</div>
							<div class="form-group">
							<?php echo implode(' ', $this->PLUGIN->getActionButtons($this->curr_xref, $this->record)); ?>
							</div>
						<?php else: ?>
							<div class="alert alert-info"><?php echo I18N::translate('Nothing found.'); ?></div>
					<?php endif; ?>
					</div>
				<?php endif; ?>
		<?php endif; ?>
		</form>
		<?php
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
		global $WT_TREE;

		$sql  = array();
		$vars = array();
		foreach ($this->PLUGIN->getRecordTypesToUpdate() as $type) {
			switch ($type) {
			case 'INDI':
				$sql[]  = "SELECT i_id, 'INDI' FROM `##individuals` WHERE i_file=?";
				$vars[] = $WT_TREE->getTreeId();
				break;
			case 'FAM':
				$sql[]  = "SELECT f_id, 'FAM' FROM `##families` WHERE f_file=?";
				$vars[] = $WT_TREE->getTreeId();
				break;
			case 'SOUR':
				$sql[]  = "SELECT s_id, 'SOUR' FROM `##sources` WHERE s_file=?";
				$vars[] = $WT_TREE->getTreeId();
				break;
			case 'OBJE':
				$sql[]  = "SELECT m_id, 'OBJE' FROM `##media` WHERE m_file=?";
				$vars[] = $WT_TREE->getTreeId();
				break;
			default:
				$sql[]  = "SELECT o_id, ? FROM `##other` WHERE o_type=? AND o_file=?";
				$vars[] = $type;
				$vars[] = $type;
				$vars[] = $WT_TREE->getTreeId();
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
		$plugins    = array();
		$dir_handle = opendir(__DIR__ . '/BatchUpdate');
		while (($file = readdir($dir_handle)) !== false) {
			if (substr($file, -10) == 'Plugin.php' && $file !== 'BatchUpdateBasePlugin.php') {
				$class           = '\Fisharebest\Webtrees\Module\BatchUpdate\\' . basename($file, '.php');
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
			'<input class="btn btn-primary" type="submit" value="' . $text . '" onclick="' .
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
		global $WT_TREE;

		switch ($type) {
		case 'INDI':
			return Individual::getInstance($xref, $WT_TREE)->getGedcom();
		case 'FAM':
			return Family::getInstance($xref, $WT_TREE)->getGedcom();
		case 'SOUR':
			return Source::getInstance($xref, $WT_TREE)->getGedcom();
		case 'REPO':
			return Repository::getInstance($xref, $WT_TREE)->getGedcom();
		case 'OBJE':
			return Media::getInstance($xref, $WT_TREE)->getGedcom();
		case 'NOTE':
			return Note::getInstance($xref, $WT_TREE)->getGedcom();
		default:
			return GedcomRecord::getInstance($xref, $WT_TREE)->getGedcom();
		}
	}

	/**
	 * The URL to a page where the user can modify the configuration of this module.
	 * These links are displayed in the admin page menu.
	 *
	 * @return string
	 */
	public function getConfigLink() {
		return 'module.php?mod=' . $this->getName() . '&amp;mod_action=admin_batch_update';
	}

}
