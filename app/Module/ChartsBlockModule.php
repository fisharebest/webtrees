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
use Fisharebest\Webtrees\Controller\HourglassController;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\InteractiveTree\TreeView;
use Fisharebest\Webtrees\Theme;

/**
 * Class ChartsBlockModule
 */
class ChartsBlockModule extends AbstractModule implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module/block */ I18N::translate('Charts');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Charts” module */ I18N::translate('An alternative way to display charts.');
	}

	/**
	 * Generate the HTML content of this block.
	 *
	 * @param int      $block_id
	 * @param bool     $template
	 * @param string[] $cfg
	 *
	 * @return string
	 */
	public function getBlock($block_id, $template = true, $cfg = array()) {
		global $WT_TREE, $ctype, $controller;

		$PEDIGREE_ROOT_ID = $WT_TREE->getPreference('PEDIGREE_ROOT_ID');
		$gedcomid         = $WT_TREE->getUserPreference(Auth::user(), 'gedcomid');

		$details = $this->getBlockSetting($block_id, 'details', '0');
		$type    = $this->getBlockSetting($block_id, 'type', 'pedigree');
		$pid     = $this->getBlockSetting($block_id, 'pid', Auth::check() ? ($gedcomid ? $gedcomid : $PEDIGREE_ROOT_ID) : $PEDIGREE_ROOT_ID);

		foreach (array('details', 'type', 'pid', 'block') as $name) {
			if (array_key_exists($name, $cfg)) {
				$$name = $cfg[$name];
			}
		}

		$person = Individual::getInstance($pid, $WT_TREE);
		if (!$person) {
			$pid = $PEDIGREE_ROOT_ID;
			$this->setBlockSetting($block_id, 'pid', $pid);
			$person = Individual::getInstance($pid, $WT_TREE);
		}

		$id    = $this->getName() . $block_id;
		$class = $this->getName() . '_block';
		if ($ctype == 'gedcom' && Auth::isManager($WT_TREE) || $ctype == 'user' && Auth::check()) {
			$title = '<a class="icon-admin" title="' . I18N::translate('Configure') . '" href="block_edit.php?block_id=' . $block_id . '&amp;ged=' . $WT_TREE->getNameHtml() . '&amp;ctype=' . $ctype . '"></a>';
		} else {
			$title = '';
		}

		if ($person) {
			$content = '<table cellspacing="0" cellpadding="0" border="0"><tr>';
			switch ($type) {
			case 'pedigree':
				$title .= I18N::translate('Pedigree of %s', $person->getFullName());
				$chartController = new HourglassController($person->getXref(), $details, false);
				$controller->addInlineJavascript($chartController->setupJavascript());
				$content .= '<td valign="middle">';
				ob_start();
				FunctionsPrint::printPedigreePerson($person, $details);
				$content .= ob_get_clean();
				$content .= '</td>';
				$content .= '<td valign="middle">';
				ob_start();
				$chartController->printPersonPedigree($person, 1);
				$content .= ob_get_clean();
				$content .= '</td>';
				break;
			case 'descendants':
				$title .= I18N::translate('Descendants of %s', $person->getFullName());
				$chartController = new HourglassController($person->getXref(), $details, false);
				$controller->addInlineJavascript($chartController->setupJavascript());
				$content .= '<td valign="middle">';
				ob_start();
				$chartController->printDescendency($person, 1, false);
				$content .= ob_get_clean();
				$content .= '</td>';
				break;
			case 'hourglass':
				$title .= I18N::translate('Hourglass chart of %s', $person->getFullName());
				$chartController = new HourglassController($person->getXref(), $details, false);
				$controller->addInlineJavascript($chartController->setupJavascript());
				$content .= '<td valign="middle">';
				ob_start();
				$chartController->printDescendency($person, 1, false);
				$content .= ob_get_clean();
				$content .= '</td>';
				$content .= '<td valign="middle">';
				ob_start();
				$chartController->printPersonPedigree($person, 1);
				$content .= ob_get_clean();
				$content .= '</td>';
				break;
			case 'treenav':
				$title .= I18N::translate('Interactive tree of %s', $person->getFullName());
				$mod = new InteractiveTreeModule(WT_MODULES_DIR . 'tree');
				$tv  = new TreeView;
				$content .= '<td>';
				$content .= '<script>jQuery("head").append(\'<link rel="stylesheet" href="' . $mod->css() . '" type="text/css" />\');</script>';
				$content .= '<script src="' . $mod->js() . '"></script>';
				list($html, $js) = $tv->drawViewport($person, 2);
				$content .= $html . '<script>' . $js . '</script>';
				$content .= '</td>';
				break;
			}
			$content .= '</tr></table>';
		} else {
			$content = I18N::translate('You must select an individual and chart type in the block configuration settings.');
		}

		if ($template) {
			return Theme::theme()->formatBlock($id, $title, $class, $content);
		} else {
			return $content;
		}
	}

	/** {@inheritdoc} */
	public function loadAjax() {
		return true;
	}

	/** {@inheritdoc} */
	public function isUserBlock() {
		return true;
	}

	/** {@inheritdoc} */
	public function isGedcomBlock() {
		return true;
	}

	/**
	 * An HTML form to edit block settings
	 *
	 * @param int $block_id
	 */
	public function configureBlock($block_id) {
		global $WT_TREE, $controller;

		$PEDIGREE_ROOT_ID = $WT_TREE->getPreference('PEDIGREE_ROOT_ID');
		$gedcomid         = $WT_TREE->getUserPreference(Auth::user(), 'gedcomid');

		if (Filter::postBool('save') && Filter::checkCsrf()) {
			$this->setBlockSetting($block_id, 'details', Filter::postBool('details'));
			$this->setBlockSetting($block_id, 'type', Filter::post('type', 'pedigree|descendants|hourglass|treenav', 'pedigree'));
			$this->setBlockSetting($block_id, 'pid', Filter::post('pid', WT_REGEX_XREF));
		}

		$details = $this->getBlockSetting($block_id, 'details', '0');
		$type    = $this->getBlockSetting($block_id, 'type', 'pedigree');
		$pid     = $this->getBlockSetting($block_id, 'pid', Auth::check() ? ($gedcomid ? $gedcomid : $PEDIGREE_ROOT_ID) : $PEDIGREE_ROOT_ID);

		$controller
			->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
			->addInlineJavascript('autocomplete();');
	?>
		<tr>
			<td class="descriptionbox wrap width33"><?php echo I18N::translate('Chart type'); ?></td>
			<td class="optionbox">
				<?php echo FunctionsEdit::selectEditControl('type',
				array(
					'pedigree'    => I18N::translate('Pedigree'),
					'descendants' => I18N::translate('Descendants'),
					'hourglass'   => I18N::translate('Hourglass chart'),
					'treenav'     => I18N::translate('Interactive tree'),
				),
				null, $type); ?>
			</td>
		</tr>
		<tr>
			<td class="descriptionbox wrap width33"><?php echo I18N::translate('Show details'); ?></td>
		<td class="optionbox">
			<?php echo FunctionsEdit::editFieldYesNo('details', $details); ?>
			</td>
		</tr>
		<tr>
			<td class="descriptionbox wrap width33"><?php echo I18N::translate('Individual'); ?></td>
			<td class="optionbox">
				<input data-autocomplete-type="INDI" type="text" name="pid" id="pid" value="<?php echo $pid; ?>" size="5">
				<?php
				echo FunctionsPrint::printFindIndividualLink('pid');
				$root = Individual::getInstance($pid, $WT_TREE);
				if ($root) {
					echo ' <span class="list_item">', $root->getFullName(), $root->formatFirstMajorFact(WT_EVENTS_BIRT, 1), '</span>';
				}
				?>
			</td>
		</tr>
		<?php
	}
}
