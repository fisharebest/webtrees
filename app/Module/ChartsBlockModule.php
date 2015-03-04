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
 * Class ChartsBlockModule
 */
class ChartsBlockModule extends Module implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module/block */ I18N::translate('Charts');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Charts” module */ I18N::translate('An alternative way to display charts.');
	}

	/** {@inheritdoc} */
	public function getBlock($block_id, $template = true, $cfg = null) {
		global $WT_TREE, $ctype, $controller;

		$PEDIGREE_ROOT_ID = $WT_TREE->getPreference('PEDIGREE_ROOT_ID');

		$details = get_block_setting($block_id, 'details', '0');
		$type    = get_block_setting($block_id, 'type', 'pedigree');
		$pid     = get_block_setting($block_id, 'pid', Auth::check() ? (WT_USER_GEDCOM_ID ? WT_USER_GEDCOM_ID : $PEDIGREE_ROOT_ID) : $PEDIGREE_ROOT_ID);

		if ($cfg) {
			foreach (array('details', 'type', 'pid', 'block') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name = $cfg[$name];
				}
			}
		}

		$person = Individual::getInstance($pid);
		if (!$person) {
			$pid = $PEDIGREE_ROOT_ID;
			set_block_setting($block_id, 'pid', $pid);
			$person = Individual::getInstance($pid);
		}

		$id = $this->getName() . $block_id;
		$class = $this->getName() . '_block';
		if ($ctype == 'gedcom' && WT_USER_GEDCOM_ADMIN || $ctype == 'user' && Auth::check()) {
			$title = '<i class="icon-admin" title="' . I18N::translate('Configure') . '" onclick="modalDialog(\'block_edit.php?block_id=' . $block_id . '\', \'' . $this->getTitle() . '\');"></i>';
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
				print_pedigree_person($person, $details);
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
				$tv = new TreeView;
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

	/** {@inheritdoc} */
	public function configureBlock($block_id) {
		global $WT_TREE, $controller;

		$PEDIGREE_ROOT_ID = $WT_TREE->getPreference('PEDIGREE_ROOT_ID');

		if (Filter::postBool('save') && Filter::checkCsrf()) {
			set_block_setting($block_id, 'details', Filter::postBool('details'));
			set_block_setting($block_id, 'type', Filter::post('type', 'pedigree|descendants|hourglass|treenav', 'pedigree'));
			set_block_setting($block_id, 'pid', Filter::post('pid', WT_REGEX_XREF));
		}

		$details = get_block_setting($block_id, 'details', '0');
		$type    = get_block_setting($block_id, 'type', 'pedigree');
		$pid     = get_block_setting($block_id, 'pid', Auth::check() ? (WT_USER_GEDCOM_ID ? WT_USER_GEDCOM_ID : $PEDIGREE_ROOT_ID) : $PEDIGREE_ROOT_ID);

		$controller
			->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
			->addInlineJavascript('autocomplete();');
	?>
		<tr>
			<td colspan="2">
				<?php echo I18N::translate('This block allows a pedigree, descendancy, or hourglass chart to appear on your “My page” or the “Home page”.  Because of space limitations, the charts should be placed only on the left side of the page.<br><br>When this block appears on the “Home page”, the root individual and the type of chart to be displayed are determined by the administrator.  When this block appears on the user’s “My page”, these options are determined by the user.<br><br>The behavior of these charts is identical to their behavior when they are called up from the menus.  Click on the box of an individual to see more details about them.'); ?>
			</td>
		</tr>
		<tr>
			<td class="descriptionbox wrap width33"><?php echo I18N::translate('Chart type'); ?></td>
			<td class="optionbox">
				<?php echo select_edit_control('type',
				array(
					'pedigree'    => I18N::translate('Pedigree'),
					'descendants' => I18N::translate('Descendants'),
					'hourglass'   => I18N::translate('Hourglass chart'),
					'treenav'     => I18N::translate('Interactive tree')),
				null, $type); ?>
			</td>
		</tr>
		<tr>
			<td class="descriptionbox wrap width33"><?php echo I18N::translate('Show details'); ?></td>
		<td class="optionbox">
			<?php echo edit_field_yes_no('details', $details); ?>
			</td>
		</tr>
		<tr>
			<td class="descriptionbox wrap width33"><?php echo I18N::translate('Individual'); ?></td>
			<td class="optionbox">
				<input data-autocomplete-type="INDI" type="text" name="pid" id="pid" value="<?php echo $pid; ?>" size="5">
				<?php
				echo print_findindi_link('pid');
				$root = Individual::getInstance($pid);
				if ($root) {
					echo ' <span class="list_item">', $root->getFullName(), $root->format_first_major_fact(WT_EVENTS_BIRT, 1), '</span>';
				}
				?>
			</td>
		</tr>
		<?php
	}
}
