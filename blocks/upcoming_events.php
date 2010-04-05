<?php
/**
 * On Upcoming Events Block
 *
 * This block will print a list of upcoming events
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
 * @package webtrees
 * @subpackage Blocks
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_UPCOMING_EVENTS_PHP', '');

require_once WT_ROOT.'includes/functions/functions_print_lists.php';

$WT_BLOCKS["print_upcoming_events"]["name"]		= i18n::translate('Upcoming Events');
$WT_BLOCKS["print_upcoming_events"]["descr"]		= i18n::translate('The Upcoming Events block shows anniversaries of events that will occur in the near future.  You can configure the amount of detail shown, and the administrator can configure how far into the future this block will look.');
$WT_BLOCKS["print_upcoming_events"]["infoStyle"]	= "style2";
$WT_BLOCKS["print_upcoming_events"]["sortStyle"]	= "alpha";
$WT_BLOCKS["print_upcoming_events"]["canconfig"]	= true;
$WT_BLOCKS["print_upcoming_events"]["config"]		= array(
	"cache"=>1,
	"days"=>30,
	"filter"=>"all",
	"onlyBDM"=>"no",
	"infoStyle"=>"style2",
	"sortStyle"=>"alpha",
	"allowDownload"=>"yes"
	);

//-- upcoming events block
//-- this block prints a list of upcoming events of people in your gedcom
function print_upcoming_events($block=true, $config="", $side, $index) {
	global $SHOW_ID_NUMBERS, $ctype, $TEXT_DIRECTION;
	global $WT_IMAGE_DIR, $WT_IMAGES, $WT_BLOCKS;
	global $DAYS_TO_SHOW_LIMIT;

	$block = true;      // Always restrict this block's height

	if (empty($config)) $config = $WT_BLOCKS["print_upcoming_events"]["config"];
	if (!isset($DAYS_TO_SHOW_LIMIT)) $DAYS_TO_SHOW_LIMIT = 30;
	if (isset($config["days"])) $daysprint = $config["days"];
	else $daysprint = 30;
	if (isset($config["filter"])) $filter = $config["filter"];  // "living" or "all"
	else $filter = "all";
	if (isset($config["onlyBDM"])) $onlyBDM = $config["onlyBDM"];  // "yes" or "no"
	else $onlyBDM = "no";
	if (isset($config["infoStyle"])) $infoStyle = $config["infoStyle"];  // "style1" or "style2"
	else $infoStyle = "style2";
	if (isset($config["sortStyle"])) $sortStyle = $config["sortStyle"];  // "alpha" or "anniv"
	else $sortStyle = "alpha";
	if (isset($config["allowDownload"])) $allowDownload = $config["allowDownload"];	// "yes" or "no"
	else $allowDownload = "yes";

	// Don't permit calendar download if not logged in
	if (!WT_USER_ID) {
		$allowDownload = "no";
	}


	if ($daysprint < 1) $daysprint = 1;
	if ($daysprint > $DAYS_TO_SHOW_LIMIT) $daysprint = $DAYS_TO_SHOW_LIMIT;  // valid: 1 to limit
	$startjd=client_jd()+1;
	$endjd=client_jd()+$daysprint;

	// Output starts here
	$id="upcoming_events";
	$title='';
	if ($WT_BLOCKS["print_upcoming_events"]["canconfig"]) {
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
	$title .= i18n::translate('Upcoming Events');
	$title .= help_link('index_events');

	$content = "";
	switch ($infoStyle) {
	case "style1":
		// Output style 1:  Old format, no visible tables, much smaller text.  Better suited to right side of page.
		$content .= print_events_list($startjd, $endjd, $onlyBDM=='yes'?'BIRT MARR DEAT':'', $filter=='living', $sortStyle);
		break;
	case "style2":
		// Style 2: New format, tables, big text, etc.  Not too good on right side of page
		ob_start();
		$content .= print_events_table($startjd, $endjd, $onlyBDM=='yes'?'BIRT MARR DEAT':'', $filter=='living', $allowDownload=='yes', $sortStyle);
		$content .= ob_get_clean();
		break;
	}

	global $THEME_DIR;
	if ($block) {
		require $THEME_DIR.'templates/block_small_temp.php';
	} else {
		require $THEME_DIR.'templates/block_main_temp.php';
	}
}

function print_upcoming_events_config($config) {
	global $WT_BLOCKS, $DAYS_TO_SHOW_LIMIT;
	if (empty($config)) $config = $WT_BLOCKS["print_upcoming_events"]["config"];
	if (!isset($DAYS_TO_SHOW_LIMIT)) $DAYS_TO_SHOW_LIMIT = 30;
	if (!isset($config["days"])) $config["days"] = 30;
	if (!isset($config["filter"])) $config["filter"] = "all";
	if (!isset($config["onlyBDM"])) $config["onlyBDM"] = "no";
	if (!isset($config["infoStyle"])) $config["infoStyle"] = "style2";
	if (!isset($config["sortStyle"])) $config["sortStyle"] = "alpha";
	if (!isset($config["allowDownload"])) $config["allowDownload"] = "yes";

	if ($config["days"] < 1) $config["days"] = 1;
	if ($config["days"] > $DAYS_TO_SHOW_LIMIT) $config["days"] = $DAYS_TO_SHOW_LIMIT;  // valid: 1 to limit

	?>
	<tr><td class="descriptionbox wrap width33">
	<?php
	print i18n::translate('Number of days to show');
	print help_link('days_to_show');
	?>
	</td><td class="optionbox">
		<input type="text" name="days" size="2" value="<?php print $config["days"]; ?>" />
	</td></tr>

	<tr><td class="descriptionbox wrap width33">
	<?php
	print i18n::translate('Show only events of living people?');
	?>
	</td><td class="optionbox">
		<select name="filter">
			<option value="all"<?php if ($config["filter"]=="all") print " selected=\"selected\"";?>><?php print i18n::translate('No'); ?></option>
			<option value="living"<?php if ($config["filter"]=="living") print " selected=\"selected\"";?>><?php print i18n::translate('Yes'); ?></option>
		</select>
	</td></tr>

	<tr><td class="descriptionbox wrap width33">
	<?php
	print i18n::translate('Show only Births, Deaths, and Marriages?');
	print help_link('basic_or_all');
	?>
	</td><td class="optionbox">
		<select name="onlyBDM">
			<option value="no"<?php if ($config["onlyBDM"]=="no") print " selected=\"selected\"";?>><?php print i18n::translate('No'); ?></option>
			<option value="yes"<?php if ($config["onlyBDM"]=="yes") print " selected=\"selected\"";?>><?php print i18n::translate('Yes'); ?></option>
		</select>
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
	print i18n::translate('Sort Style');
	print help_link('sort_style');
	?>
	</td><td class="optionbox">
		<select name="sortStyle">
			<option value="alpha"<?php if ($config["sortStyle"]=="alpha") print " selected=\"selected\"";?>><?php print i18n::translate('Alphabetically'); ?></option>
			<option value="anniv"<?php if ($config["sortStyle"]=="anniv") print " selected=\"selected\"";?>><?php print i18n::translate('By Anniversary'); ?></option>
		</select>
	</td></tr>

	<tr><td class="descriptionbox wrap width33">
	<?php
	print i18n::translate('Allow calendar events download?');
 	print help_link('cal_download');
	?>
	</td><td class="optionbox">
		<select name="allowDownload">
			<option value="yes"<?php if ($config["allowDownload"]=="yes") print " selected=\"selected\"";?>><?php print i18n::translate('Yes'); ?></option>
			<option value="no"<?php if ($config["allowDownload"]=="no") print " selected=\"selected\"";?>><?php print i18n::translate('No'); ?></option>
		</select>
		<input type="hidden" name="cache" value="1" />
	</td></tr>
	<?php
}
?>
