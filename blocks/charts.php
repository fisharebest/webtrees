<?php
/**
 * Charts Block
 *
 * This block prints pedigree, descendency, or hourglass charts for the chosen person
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * @version $Id$
 * -- Slightly modified (rtl in table values) 2006/06/09 18:00:00 pfblair
 * @package webtrees
 * @subpackage Blocks
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_CHARTS_PHP', '');

require_once WT_ROOT.'includes/controllers/hourglass_ctrl.php';
require_once WT_ROOT.'includes/classes/class_treenav.php';

$WT_BLOCKS['print_charts_block']=array(
	'name'=>i18n::translate('Charts Block'),
	'type'=>'both',
	'descr'=>i18n::translate('The Charts block allows you to place a chart on the Home or My Page.  You can configure the block to show an ancestors, descendants, or hourglass view.  You can also choose the root person for the chart.'),
	'canconfig'=>true,
	'config'=>array(
		'cache'=>1,
		'pid'=>'',
		'type'=>'pedigree',
		'details'=>'no'
	)
);

function print_charts_block($block = true, $config="", $side, $index) {
	global $WT_BLOCKS, $ctype, $WT_IMAGE_DIR, $WT_IMAGES, $PEDIGREE_ROOT_ID, $PEDIGREE_FULL_DETAILS;
	global $show_full, $bwidth, $bheight;

	if (empty($config)) $config = $WT_BLOCKS["print_charts_block"]["config"];
	if (empty($config['details'])) $config['details'] = 'no';
	if (empty($config["pid"])) {
		if (!WT_USER_ID) {
			$config["pid"] = $PEDIGREE_ROOT_ID;
		} else {
			if (WT_USER_GEDCOM_ID) {
				$config["pid"] = WT_USER_GEDCOM_ID;
			} else {
				$config["pid"] = $PEDIGREE_ROOT_ID;
			}
		}
	}

	// Override GEDCOM configuration temporarily
	if (isset($show_full)) $saveShowFull = $show_full;
	$savePedigreeFullDetails = $PEDIGREE_FULL_DETAILS;
	if ($config["details"]=="no") {
		$show_full = 0;
		// Here we could adjust the block width & height to accommodate larger displays
	} else {
		$show_full = 1;
		// Here we could adjust the block width & height to accommodate larger displays
	}
	$PEDIGREE_FULL_DETAILS = $show_full;

	if ($config['type']!='treenav') {
		$controller = new HourglassController();
		$controller->init($config["pid"],0,3);
		$controller->setupJavascript();
	}
	else {
		$nav = new TreeNav($config['pid'],'blocknav',-1);
		$nav->generations = 2;
	}

	$person = Person::getInstance($config["pid"]);
	if ($person==null) {
		$config["pid"] = $PEDIGREE_ROOT_ID;
		$person = Person::getInstance($PEDIGREE_ROOT_ID);
	}

	$id = "charts_block";
	$title='';
	if ($WT_BLOCKS["print_charts_block"]["canconfig"]) {
		if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=="user" && WT_USER_ID) {
			if ($ctype=="gedcom") {
				$name = WT_GEDCOM;
			} else {
				$name = WT_USER_NAME;
			}
			$title .= "<a href=\"javascript: configure block\" onclick=\"window.open('".encode_url("index_edit.php?name={$name}&ctype={$ctype}&action=configure&side={$side}&index={$index}")."', '_blank', 'top=50,left=50,width=700,height=400,scrollbars=1,resizable=1'); return false;\">";
			$title .= "<img class=\"adminicon\" src=\"$WT_IMAGE_DIR/".$WT_IMAGES["admin"]["small"]."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" /></a>";
		}
	}
	if ($person) {
		$name=PrintReady($person->getFullName());
		switch($config['type']) {
			case 'pedigree':
				$title .= $name." ".i18n::translate('Pedigree Tree');
				break;
			case 'descendants':
				$title .= $name." ".i18n::translate('Descendancy Chart');
				break;
			case 'hourglass':
				$title .= $name." ".i18n::translate('Hourglass Chart');
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
		if ($config['type']=='descendants' || $config['type']=='hourglass') {
			$content .= "<td valign=\"middle\">";
			ob_start();
			$controller->print_descendency($person->getXref(), 1, false);
			$content .= ob_get_clean();
			$content .= "</td>";
		}
		if ($config['type']=='pedigree' || $config['type']=='hourglass') {
			//-- print out the root person
			if ($config['type']!='hourglass') {
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
		if ($config['type']=='treenav') {
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
		$content=i18n::translate('No such ID exists in this GEDCOM file.');
	}

	global $THEME_DIR;
	require $THEME_DIR.'templates/block_small_temp.php';
	// Restore GEDCOM configuration
	unset($show_full);
	if (isset($saveShowFull)) $show_full = $saveShowFull;
	$PEDIGREE_FULL_DETAILS = $savePedigreeFullDetails;
}

function print_charts_block_config($config) {
	global $ctype, $WT_BLOCKS, $TEXT_DIRECTION, $PEDIGREE_ROOT_ID, $ENABLE_AUTOCOMPLETE;
	if (empty($config)) $config = $WT_BLOCKS["print_charts_block"]["config"];
	if (empty($config["rootId"])) $config["rootId"] = $PEDIGREE_ROOT_ID;
	if (empty($config['details'])) $config['details'] = 'no';

	if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';
?>
	<tr><td class="descriptionbox wrap width33"><?php print i18n::translate('Chart Type'); ?></td>
	<td class="optionbox">
		<select name="type">
			<option value="pedigree"<?php if ($config["type"]=="pedigree") print " selected=\"selected\""; ?>><?php print i18n::translate('Pedigree Tree'); ?></option>
			<option value="descendants"<?php if ($config["type"]=="descendants") print " selected=\"selected\""; ?>><?php print i18n::translate('Descendancy Chart'); ?></option>
			<option value="hourglass"<?php if ($config["type"]=="hourglass") print " selected=\"selected\""; ?>><?php print i18n::translate('Hourglass Chart'); ?></option>
			<?php if (file_exists(WT_ROOT.'includes/classes/class_treenav.php')) { ?>
			<option value="treenav"<?php if ($config["type"]=="treenav") print " selected=\"selected\""; ?>><?php print i18n::translate('Interactive Tree'); ?></option>
			<?php } ?>
		</select>
	</td></tr>
	<tr>
		<td class="descriptionbox wrap width33"><?php print i18n::translate('Show Details'); ?></td>
	<td class="optionbox">
		<select name="details">
				<option value="no" <?php if ($config["details"]=="no") print " selected=\"selected\""; ?>><?php print i18n::translate('No'); ?></option>
				<option value="yes" <?php if ($config["details"]=="yes") print " selected=\"selected\""; ?>><?php print i18n::translate('Yes'); ?></option>
		</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width33"><?php print i18n::translate('Root Person ID'); ?></td>
		<td class="optionbox">
			<input type="text" name="pid" id="pid" value="<?php print $config['pid']; ?>" size="5" />
			<?php
			print_findindi_link('pid','');
			$root=Person::getInstance($config['pid']);
			if ($root) {
				echo ' <span class="list_item">', $root->getFullName(), $root->format_first_major_fact(WT_EVENTS_BIRT, 1), '</span>';
			}
			?>
		</td>
	</tr>
	<?php

	// Cache file life
	if ($ctype=="gedcom") {
		echo "<tr><td class=\"descriptionbox wrap width33\">";
		echo i18n::translate('Cache file life'), help_link('cache_life');
		echo "</td><td class=\"optionbox\">";
		echo "<input type=\"text\" name=\"cache\" size=\"2\" value=\"".$config["cache"]."\" />";
		echo "</td></tr>";
	}
}
?>
