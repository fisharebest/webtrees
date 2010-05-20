<?php
/**
 * Classes and libraries for module system
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2010 John Finlay
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
 * @version $Id: class_media.php 5451 2009-05-05 22:15:34Z fisharebest $
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/classes/class_module.php';
require_once WT_ROOT.'includes/controllers/hourglass_ctrl.php';
require_once WT_ROOT.'includes/classes/class_treenav.php';

class charts_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('Charts block');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('The Charts block allows you to place a chart on the Home or My Page.  You can configure the block to show an ancestors, descendants, or hourglass view.  You can also choose the root person for the chart.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id) {
		global $ctype, $WT_IMAGE_DIR, $WT_IMAGES, $PEDIGREE_ROOT_ID, $PEDIGREE_FULL_DETAILS, $show_full, $bwidth, $bheight, $THEME_DIR;

		$details=get_block_setting($block_id, 'details', false);
		$type   =get_block_setting($block_id, 'type', 'treenav');
		$pid    =get_block_setting($block_id, 'pid', WT_USER_ID ? (WT_USER_GEDCOM_ID ? WT_USER_GEDCOM_ID : $PEDIGREE_ROOT_ID) : $PEDIGREE_ROOT_ID);

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
			$controller = new HourglassController();
			$controller->init($pid,0,3);
			$controller->setupJavascript();
		} else {
			$nav = new TreeNav($pid, 'blocknav',-1);
			$nav->generations = 2;
		}

		$person = Person::getInstance($pid);
		if ($person==null) {
			$pid = $PEDIGREE_ROOT_ID;
			set_block_setting($block_id, 'pid', $pid);
			$person = Person::getInstance($pid);
		}

		$id=$this->getName().$block_id;
		$title='';
		if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=="user" && WT_USER_ID) {
			$title .= "<a href=\"javascript: configure block\" onclick=\"window.open('index_edit.php?action=configure&amp;ctype={$ctype}&amp;block_id={$block_id}', '_blank', 'top=50,left=50,width=700,height=400,scrollbars=1,resizable=1'); return false;\">";
			$title .= "<img class=\"adminicon\" src=\"$WT_IMAGE_DIR/".$WT_IMAGES["admin"]["small"]."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" /></a>";
		}
		if ($person) {
			$name=PrintReady($person->getFullName());
			switch($type) {
				case 'pedigree':
					$title .= $name." ".i18n::translate('Pedigree Tree');
					break;
				case 'descendants':
					$title .= $name." ".i18n::translate('Descendancy chart');
					break;
				case 'hourglass':
					$title .= $name." ".i18n::translate('Hourglass chart');
					break;
				case 'treenav':
					$title .= $name." ".i18n::translate('Tree');
					break;
			}
			$title .= help_link('index_charts');
			$content = "";
			$content .= "<script src=\"js/webtrees.js\" language=\"JavaScript\" type=\"text/javascript\"></script>";
			if ($show_full==0) {
				$content .= '<center><span class="details2">'.i18n::translate('Click on any of the boxes to get more information about that person.').'</span></center><br />';
			}
			$content .= '<table cellspacing="0" cellpadding="0" border="0"><tr>';
			if ($type=='descendants' || $type=='hourglass') {
				$content .= "<td valign=\"middle\">";
				ob_start();
				$controller->print_descendency($person->getXref(), 1, false);
				$content .= ob_get_clean();
				$content .= "</td>";
			}
			if ($type=='pedigree' || $type=='hourglass') {
				//-- print out the root person
				if ($type!='hourglass') {
					$content .= "<td valign=\"middle\">";
					ob_start();
					print_pedigree_person($person->getXref());
					$content .= ob_get_clean();
					$content .= "</td>";
				}
				$content .= "<td valign=\"middle\">";
				ob_start();
				$controller->print_person_pedigree($person->getXref(), 1);
				$content .= ob_get_clean();
				$content .= "</td>";
			}
			if ($type=='treenav') {
				$content .= "<td>";
				ob_start();
				$nav->drawViewport('blocknav', "", "240px");
				$content .= ob_get_clean();
				$content .= "</td>";
			}
			$content .= "</tr></table>";
			$content .= '<script language="JavaScript" type="text/javascript">
				<!--
				if (sizeLines) sizeLines();
				-->
				</script>';
		} else {
			$content=i18n::translate('You must select an individual and chart type in the block configuration settings.');
		}

		require $THEME_DIR.'templates/block_small_temp.php';

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
		return true;
	}

	// Implement class WT_Module_Block
	public function isGedcomBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
		global $ctype, $WT_BLOCKS, $TEXT_DIRECTION, $PEDIGREE_ROOT_ID, $ENABLE_AUTOCOMPLETE;

		if (safe_POST_bool('save')) {
			set_block_setting($block_id, 'details', safe_POST_bool('details'));
			set_block_setting($block_id, 'type',    safe_POST('type', array('pedigree', 'descendants', 'hourglass', 'treenav'), 'treenav'));
			set_block_setting($block_id, 'pid',     safe_POST('pid', WT_REGEX_XREF));
			echo WT_JS_START, 'window.opener.location.href=window.opener.location.href;window.close();', WT_JS_END;
			exit;
		}

		$details=get_block_setting($block_id, 'details', false);
		$type   =get_block_setting($block_id, 'type',    'treenav');
		$pid    =get_block_setting($block_id, 'pid', WT_USER_ID ? (WT_USER_GEDCOM_ID ? WT_USER_GEDCOM_ID : $PEDIGREE_ROOT_ID) : $PEDIGREE_ROOT_ID);

		if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';
	?>
		<tr><td class="descriptionbox wrap width33"><?php print i18n::translate('Chart type'); ?></td>
		<td class="optionbox">
			<select name="type">
				<option value="pedigree"<?php if ($type=="pedigree") print " selected=\"selected\""; ?>><?php print i18n::translate('Pedigree Tree'); ?></option>
				<option value="descendants"<?php if ($type=="descendants") print " selected=\"selected\""; ?>><?php print i18n::translate('Descendancy chart'); ?></option>
				<option value="hourglass"<?php if ($type=="hourglass") print " selected=\"selected\""; ?>><?php print i18n::translate('Hourglass chart'); ?></option>
				<?php if (file_exists(WT_ROOT.'includes/classes/class_treenav.php')) { ?>
				<option value="treenav"<?php if ($type=="treenav") print " selected=\"selected\""; ?>><?php print i18n::translate('Interactive tree'); ?></option>
				<?php } ?>
			</select>
		</td></tr>
		<tr>
			<td class="descriptionbox wrap width33"><?php print i18n::translate('Show Details'); ?></td>
		<td class="optionbox">
			<select name="details">
					<option value="no" <?php if (!$details) print " selected=\"selected\""; ?>><?php print i18n::translate('No'); ?></option>
					<option value="yes" <?php if ($details) print " selected=\"selected\""; ?>><?php print i18n::translate('Yes'); ?></option>
			</select>
			</td>
		</tr>
		<tr>
			<td class="descriptionbox wrap width33"><?php print i18n::translate('Root Person ID'); ?></td>
			<td class="optionbox">
				<input type="text" name="pid" id="pid" value="<?php print $pid; ?>" size="5" />
				<?php
				print_findindi_link('pid','');
				$root=Person::getInstance($pid);
				if ($root) {
					echo ' <span class="list_item">', $root->getFullName(), $root->format_first_major_fact(WT_EVENTS_BIRT, 1), '</span>';
				}
				?>
			</td>
		</tr>
		<?php
	}
}
