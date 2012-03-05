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

class charts_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module/block */ WT_I18N::translate('Charts');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the "Charts" module */ WT_I18N::translate('An alternative way to display charts.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $ctype, $WT_IMAGES, $PEDIGREE_FULL_DETAILS, $show_full, $bwidth, $bheight;

		$PEDIGREE_ROOT_ID=get_gedcom_setting(WT_GED_ID, 'PEDIGREE_ROOT_ID');

		$details=get_block_setting($block_id, 'details', false);
		$type   =get_block_setting($block_id, 'type', 'pedigree');
		$pid    =get_block_setting($block_id, 'pid', WT_USER_ID ? (WT_USER_GEDCOM_ID ? WT_USER_GEDCOM_ID : $PEDIGREE_ROOT_ID) : $PEDIGREE_ROOT_ID);
		$block  =get_block_setting($block_id, 'block');
		if ($cfg) {
			foreach (array('details', 'type', 'pid', 'block') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name=$cfg[$name];
				}
			}
		}

		// Override the request
		$_GET['rootid']=$pid;

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

		if ($type!='treenav') {
			$controller=new WT_Controller_Hourglass($pid,0,3);
			$controller->setupJavascript();
		}

		$person = WT_Person::getInstance($pid);
		if (!$person) {
			$pid = $PEDIGREE_ROOT_ID;
			set_block_setting($block_id, 'pid', $pid);
			$person = WT_Person::getInstance($pid);
		}

		$id=$this->getName().$block_id;
		$class=$this->getName().'_block';
		if ($ctype=='gedcom' && WT_USER_GEDCOM_ADMIN || $ctype=='user' && WT_USER_ID) {
			$title='<img class="adminicon" src="'.$WT_IMAGES['admin'].'" width="15" height="15" alt="'.WT_I18N::translate('Configure').'"  onclick="modalDialog(\'block_edit.php?block_id='.$block_id.'\', \''.$this->getTitle().'\');">';
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
			$content = "<script src=\"".WT_STATIC_URL."js/webtrees.js\" type=\"text/javascript\"></script>";
			$content .= '<table cellspacing="0" cellpadding="0" border="0"><tr>';
			if ($type=='descendants' || $type=='hourglass') {
				$content .= "<td valign=\"middle\">";
				ob_start();
				$controller->print_descendency($person, 1, false);
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
				$controller->print_person_pedigree($person, 1);
				$content .= ob_get_clean();
				$content .= "</td>";
			}
			if ($type=='treenav') {
				// TODO: we should
				// 1) check whether the block is active
				// 2) find out why it is necessary to load jquery, when it is already loaded
				require_once WT_MODULES_DIR.'tree/module.php';
				require_once WT_MODULES_DIR.'tree/class_treeview.php';
				$mod=new tree_WT_Module;
				$tv=new TreeView;
				$content .= '<td>';
				$content .= '<script type="text/javascript" src="'.WT_JQUERY_URL.'"></script><script type="text/javascript" src="'.WT_JQUERYUI_URL.'"></script>';

				$content .= $mod->css;
				$content .= $mod->headers;
				$content .= '<script type="text/javascript" src="'.$mod->js.'"></script>';
		    list($html, $js) = $tv->drawViewport($person->getXref(), 2);
				$content .= $html.WT_JS_START.$js.WT_JS_END;
				$content .= '</td>';
			}
			$content .= "</tr></table>";
		} else {
			$content=WT_I18N::translate('You must select an individual and chart type in the block configuration settings.');
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
		global $ctype, $ENABLE_AUTOCOMPLETE;

		$PEDIGREE_ROOT_ID=get_gedcom_setting(WT_GED_ID, 'PEDIGREE_ROOT_ID');

		if (safe_POST_bool('save')) {
			set_block_setting($block_id, 'details', safe_POST_bool('details'));
			set_block_setting($block_id, 'type',    safe_POST('type', array('pedigree', 'descendants', 'hourglass', 'treenav'), 'pedigree'));
			set_block_setting($block_id, 'pid',     safe_POST('pid', WT_REGEX_XREF));
			echo WT_JS_START, 'window.opener.location.href=window.opener.location.href;window.close();', WT_JS_END;
			exit;
		}

		$details=get_block_setting($block_id, 'details', false);
		$type   =get_block_setting($block_id, 'type',    'pedigree');
		$pid    =get_block_setting($block_id, 'pid', WT_USER_ID ? (WT_USER_GEDCOM_ID ? WT_USER_GEDCOM_ID : $PEDIGREE_ROOT_ID) : $PEDIGREE_ROOT_ID);

		if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';
	?>
		<tr><td class="descriptionbox wrap width33"><?php echo WT_I18N::translate('Chart type'); ?></td>
		<td class="optionbox">
			<select name="type">
				<option value="pedigree"<?php if ($type=="pedigree") echo " selected=\"selected\""; ?>><?php echo WT_I18N::translate('Pedigree'); ?></option>
				<option value="descendants"<?php if ($type=="descendants") echo " selected=\"selected\""; ?>><?php echo WT_I18N::translate('Descendants'); ?></option>
				<option value="hourglass"<?php if ($type=="hourglass") echo " selected=\"selected\""; ?>><?php echo WT_I18N::translate('Hourglass chart'); ?></option>
				<option value="treenav"<?php if ($type=="treenav") echo " selected=\"selected\""; ?>><?php echo WT_I18N::translate('Interactive tree'); ?></option>
			</select>
		</td></tr>
		<tr>
			<td class="descriptionbox wrap width33"><?php echo WT_I18N::translate('Show Details'); ?></td>
		<td class="optionbox">
			<select name="details">
					<option value="no" <?php if (!$details) echo " selected=\"selected\""; ?>><?php echo WT_I18N::translate('no'); ?></option>
					<option value="yes" <?php if ($details) echo " selected=\"selected\""; ?>><?php echo WT_I18N::translate('yes'); ?></option>
			</select>
			</td>
		</tr>
		<tr>
			<td class="descriptionbox wrap width33"><?php echo WT_I18N::translate('Individual'); ?></td>
			<td class="optionbox">
				<input type="text" name="pid" id="pid" value="<?php echo $pid; ?>" size="5">
				<?php
				print_findindi_link('pid','');
				$root=WT_Person::getInstance($pid);
				if ($root) {
					echo ' <span class="list_item">', $root->getFullName(), $root->format_first_major_fact(WT_EVENTS_BIRT, 1), '</span>';
				}
				?>
			</td>
		</tr>
		<?php

		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$block=get_block_setting($block_id, 'block', false);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: label for a yes/no option */ WT_I18N::translate('Add a scrollbar when block contents grow');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('block', $block);
		echo '</td></tr>';
	}
}
