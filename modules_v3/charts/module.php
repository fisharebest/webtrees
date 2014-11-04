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

use WT\Auth;

class charts_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module/block */ WT_I18N::translate('Charts');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Charts” module */ WT_I18N::translate('An alternative way to display charts.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $WT_TREE, $ctype, $PEDIGREE_FULL_DETAILS, $show_full, $bwidth, $bheight, $controller;

		$PEDIGREE_ROOT_ID = $WT_TREE->getPreference('PEDIGREE_ROOT_ID');

		$details = get_block_setting($block_id, 'details', false);
		$type    = get_block_setting($block_id, 'type', 'pedigree');
		$pid     = get_block_setting($block_id, 'pid', Auth::check() ? (WT_USER_GEDCOM_ID ? WT_USER_GEDCOM_ID : $PEDIGREE_ROOT_ID) : $PEDIGREE_ROOT_ID);
		if ($cfg) {
			foreach (array('details', 'type', 'pid', 'block') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name = $cfg[$name];
				}
			}
		}

		// Override GEDCOM configuration temporarily
		if (isset($show_full)) $saveShowFull = $show_full;
		$savePedigreeFullDetails = $PEDIGREE_FULL_DETAILS;
		if (!$details) {
			$show_full = 0;
			// Here we could adjust the block width & height to accommodate larger displays
		} else {
			$show_full = 1;
			// Here we could adjust the block width & height to accommodate larger displays
		}
		$PEDIGREE_FULL_DETAILS = $show_full;

		$person = WT_Individual::getInstance($pid);
		if (!$person) {
			$pid = $PEDIGREE_ROOT_ID;
			set_block_setting($block_id, 'pid', $pid);
			$person = WT_Individual::getInstance($pid);
		}

		if ($type!='treenav' && $person) {
			$chartController = new WT_Controller_Hourglass($person->getXref(), 0, false);
			$controller->addInlineJavascript($chartController->setupJavascript());
		}

		$id=$this->getName().$block_id;
		$class=$this->getName().'_block';
		if ($ctype=='gedcom' && WT_USER_GEDCOM_ADMIN || $ctype=='user' && Auth::check()) {
			$title='<i class="icon-admin" title="'.WT_I18N::translate('Configure').'" onclick="modalDialog(\'block_edit.php?block_id='.$block_id.'\', \''.$this->getTitle().'\');"></i>';
		} else {
			$title='';
		}

		if ($person) {
			switch($type) {
				case 'pedigree':
					$title .= WT_I18N::translate('Pedigree of %s', $person->getFullName());
					break;
				case 'descendants':
					$title .= WT_I18N::translate('Descendants of %s', $person->getFullName());
					break;
				case 'hourglass':
					$title .= WT_I18N::translate('Hourglass chart of %s', $person->getFullName());
					break;
				case 'treenav':
					$title .= WT_I18N::translate('Interactive tree of %s', $person->getFullName());
					break;
			}
			$title .= help_link('index_charts', $this->getName());
			$content = '<table cellspacing="0" cellpadding="0" border="0"><tr>';
			if ($type=='descendants' || $type=='hourglass') {
				$content .= "<td valign=\"middle\">";
				ob_start();
				$chartController->printDescendency($person, 1, false);
				$content .= ob_get_clean();
				$content .= "</td>";
			}
			if ($type=='pedigree' || $type=='hourglass') {
				//-- print out the root person
				if ($type!='hourglass') {
					$content .= "<td valign=\"middle\">";
					ob_start();
					print_pedigree_person($person);
					$content .= ob_get_clean();
					$content .= "</td>";
				}
				$content .= "<td valign=\"middle\">";
				ob_start();
				$chartController->printPersonPedigree($person, 1);
				$content .= ob_get_clean();
				$content .= "</td>";
			}
			if ($type=='treenav') {
				require_once WT_MODULES_DIR.'tree/module.php';
				require_once WT_MODULES_DIR.'tree/class_treeview.php';
				$mod=new tree_WT_Module;
				$tv=new TreeView;
				$content .= '<td>';

				$content .= '<script>jQuery("head").append(\'<link rel="stylesheet" href="'.$mod->css().'" type="text/css" />\');</script>';
				$content .= '<script src="'.$mod->js().'"></script>';
				list($html, $js) = $tv->drawViewport($person, 2);
				$content .= $html.'<script>'.$js.'</script>';
				$content .= '</td>';
			}
			$content .= "</tr></table>";
		} else {
			$content=WT_I18N::translate('You must select an individual and chart type in the block configuration settings.');
		}

		if ($template) {
			require WT_THEME_DIR.'templates/block_main_temp.php';
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
		return true;
	}

	// Implement class WT_Module_Block
	public function isUserBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function isGedcomBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
		global $WT_TREE, $ctype, $controller;
		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$PEDIGREE_ROOT_ID = $WT_TREE->getPreference('PEDIGREE_ROOT_ID');

		if (WT_Filter::postBool('save') && WT_Filter::checkCsrf()) {
			set_block_setting($block_id, 'details', WT_Filter::postBool('details'));
			set_block_setting($block_id, 'type',    WT_Filter::post('type', 'pedigree|descendants|hourglass|treenav', 'pedigree'));
			set_block_setting($block_id, 'pid',     WT_Filter::post('pid', WT_REGEX_XREF));
			exit;
		}

		$details = get_block_setting($block_id, 'details', false);
		$type    = get_block_setting($block_id, 'type', 'pedigree');
		$pid     = get_block_setting($block_id, 'pid', Auth::check() ? (WT_USER_GEDCOM_ID ? WT_USER_GEDCOM_ID : $PEDIGREE_ROOT_ID) : $PEDIGREE_ROOT_ID);

		$controller
			->addExternalJavascript(WT_STATIC_URL . 'js/autocomplete.js')
			->addInlineJavascript('autocomplete();');
	?>
		<tr><td class="descriptionbox wrap width33"><?php echo WT_I18N::translate('Chart type'); ?></td>
		<td class="optionbox">
			<?php echo select_edit_control('type',
			array(
				'pedigree'    => WT_I18N::translate('Pedigree'),
				'descendants' => WT_I18N::translate('Descendants'),
				'hourglass'   => WT_I18N::translate('Hourglass chart'),
				'treenav'     => WT_I18N::translate('Interactive tree')),
			null, $type); ?>
		</td></tr>
		<tr>
			<td class="descriptionbox wrap width33"><?php echo WT_I18N::translate('Show details'); ?></td>
		<td class="optionbox">
			<?php echo edit_field_yes_no('details', $details); ?>
			</td>
		</tr>
		<tr>
			<td class="descriptionbox wrap width33"><?php echo WT_I18N::translate('Individual'); ?></td>
			<td class="optionbox">
				<input data-autocomplete-type="INDI" type="text" name="pid" id="pid" value="<?php echo $pid; ?>" size="5">
				<?php
				echo print_findindi_link('pid');
				$root=WT_Individual::getInstance($pid);
				if ($root) {
					echo ' <span class="list_item">', $root->getFullName(), $root->format_first_major_fact(WT_EVENTS_BIRT, 1), '</span>';
				}
				?>
			</td>
		</tr>
		<?php
	}
}
