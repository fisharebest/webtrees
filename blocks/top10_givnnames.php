<?php
/**
 * Top 10 Given Names Block
 *
 * This block will show the top 10 given names that occur most frequently in the active gedcom
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2008  PGV Development Team.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License or,
 * at your discretion, any later version.
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
 * @author kiwi_pgv
 * @package webtrees
 * @subpackage Blocks
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_TOP10_GIVNNAMES_PHP', '');

$WT_BLOCKS['print_block_givn_top10']=array(
	'name'=>i18n::translate('Top 10 Given Names'),
	'type'=>'both',
	'descr'=>i18n::translate('This block shows a table of the 10 most frequently occurring given names in the database.  The actual number of given names shown in this block is configurable.'),
	'canconfig'=>true,
	'config'=>array(
		'cache'=>7,
		'num'=>10,
		'infoStyle'=>'style2',
		'showUnknown'=>true
	)
);

/**
 * Print First Names Block
 */
function print_block_givn_top10($block=true, $config="", $side, $index) {
	global $TEXT_DIRECTION, $WT_BLOCKS, $ctype, $WT_IMAGES, $WT_IMAGE_DIR;

	if (empty($config)) $config = $WT_BLOCKS["print_block_givn_top10"]["config"];
	if (isset($config["infoStyle"])) $infoStyle = $config["infoStyle"];  // "style1" or "style2"
	else $infoStyle = "style2";
	if (isset($config["showUnknown"])) $showUnknown = $config["showUnknown"];
	else $showUnknown = true;

	$stats=new Stats(WT_GEDCOM);

	//Print block header

	$id="top10givennames";
	$title='';
	if ($WT_BLOCKS["print_block_givn_top10"]["canconfig"]) {
		if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=="user" && WT_USER_ID) {
			if ($ctype=="gedcom") {
				$name = WT_GEDCOM;
			} else {
				$name = WT_USER_NAME;
			}
			$title .= "<a href=\"javascript: configure block\" onclick=\"window.open('".encode_url("index_edit.php?name={$name}&ctype={$ctype}&action=configure&side={$side}&index={$index}")."', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">";
			$title .= "<img class=\"adminicon\" src=\"$WT_IMAGE_DIR/".$WT_IMAGES["admin"]["small"]."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" /></a>";
		}
	}
	// I18N: There are separate lists of male/female names, containing %d names each
	$title .= i18n::plural('Top Given Name', 'Top %d Given Names', $config['num'], $config['num']);
	$title .= help_link('index_common_given_names');

	$content = '<div class="normal_inner_block">';
	//Select List or Table
	switch ($infoStyle) {
	case "style1":	// Output style 1:  Simple list style.  Better suited to left side of page.
		if ($TEXT_DIRECTION=='ltr') $padding = 'padding-left: 15px';
		else $padding = 'padding-right: 15px';
		$params=array(1,$config['num'],'rcount');
		//List Female names
		$totals=$stats->commonGivenFemaleTotals($params);
		if ($totals) {
			$content.='<b>'.i18n::translate('Female').'</b><div class="wrap" style="'.$padding.'">'.$totals.'</div><br />';
		}
		//List Male names
		$totals=$stats->commonGivenMaleTotals($params);
		if ($totals) {
			$content.='<b>'.i18n::translate('Male').'</b><div class="wrap" style="'.$padding.'">'.$totals.'</div><br />';
		}
		//List Unknown names
		$totals=$stats->commonGivenUnknownTotals($params);
		if ($totals && $showUnknown) {
			$content.='<b>'.i18n::translate('unknown').'</b><div class="wrap" style="'.$padding.'">'.$totals.'</div><br />';
		}
		break;
	case "style2":	// Style 2: Tabular format.  Narrow, 2 or 3 column table, good on right side of page
		$params=array(1,$config['num'],'rcount');
		$content.='<table class="center"><tr valign="top"><td>'.$stats->commonGivenFemaleTable($params).'</td>';
		$content.='<td>'.$stats->commonGivenMaleTable($params).'</td>';
		if ($showUnknown) {
			$content.='<td>'.$stats->commonGivenUnknownTable($params).'</td>';
		}
		$content.='</tr></table>';
		break;
	}
	$content .=  "</div>";

	global $THEME_DIR;
	if ($block) {
		require $THEME_DIR.'templates/block_small_temp.php';
	} else {
		require $THEME_DIR.'templates/block_main_temp.php';
	}
}

function print_block_givn_top10_config($config) {
	global $ctype, $WT_BLOCKS, $TEXT_DIRECTION;
	if (empty($config)) $config = $WT_BLOCKS["print_block_givn_top10"]["config"];
	if (!isset($config["cache"])) $config["cache"] = $WT_BLOCKS["print_block_givn_top10"]["config"]["cache"];
	if (!isset($config["infoStyle"])) $config["infoStyle"] = "style2";
	if (!isset($config["showUnknown"])) $config["showUnknown"] = true;

	print "<tr><td class=\"descriptionbox wrap width33\">".i18n::translate('Number of items to show')."</td>";?>
	<td class="optionbox">
		<input type="text" name="num" size="2" value="<?php print $config["num"]; ?>" />
	</td></tr>

	<tr><td class="descriptionbox wrap width33">
	<?php
	print i18n::translate('Presentation Style');
	print help_link('style');
	?>
	</td><td class="optionbox">
		<select name="infoStyle">
			<option value="style1"<?php if ($config["infoStyle"]=="style1") print " selected=\"selected\"";?>><?php print i18n::translate('List'); ?></option>
			<option value="style2"<?php if ($config["infoStyle"]=="style2") print " selected=\"selected\"";?>><?php print i18n::translate('Table'); ?></option>
		</select>
	</td></tr>

	<tr><td class="descriptionbox wrap width33">
	<?php
	echo i18n::translate('Show unknown gender'), help_link('showUnknown');
	echo '</td><td class="optionbox">';
	echo edit_field_yes_no('showUnknown', $config['showUnknown']);
	echo '</td></tr>';
	// Cache file life
	if ($ctype=="gedcom") {
  	echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Cache file life'), help_link('cache_life');
		echo '</td><td class="optionbox">';
		echo '<input type="text" name="cache" size="2" value="', $config['cache'], '" />';
		echo '</td></tr>';
	}
}
?>
