<?php
/**
 * Yahrzeit Block
 *
 * This block will print a list of upcoming yahrzeit (hebrew death anniversaries)
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2008  PGV Development Team.  All rights reserved.
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
 * @author Greg Roach, fisharebest@users.sourceforge.net
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_YAHRZEIT_PHP', '');

$WT_BLOCKS['print_yahrzeit']=array(
	'name'=>i18n::translate('Upcoming Yahrzeiten'),
	'type'=>'both',
	'descr'=>i18n::translate('The Upcoming Yahrzeiten block shows anniversaries of death dates that will occur in the near future.  You can configure the period shown, and the Administrator can configure how far into the future this block will look.'),
	'canconfig'=>true,
	'config'=>array(
		'cache'        =>1,
		'days'         =>30,
		'infoStyle'    =>'style2',
		'allowDownload'=>'yes'
	)
);

// this block prints a list of upcoming yahrzeit events of people in your gedcom
function print_yahrzeit($block=true, $config='', $side, $index) {
	global $SHOW_ID_NUMBERS, $ctype, $TEXT_DIRECTION;
	global $WT_IMAGE_DIR, $WT_IMAGES, $WT_BLOCKS;
	global $DAYS_TO_SHOW_LIMIT, $SHOW_MARRIED_NAMES, $SERVER_URL;

	$block=true; // Always restrict this block's height

	if (empty($config))
		$config=$WT_BLOCKS['print_yahrzeit']['config'];

	if (empty($config['infoStyle'    ])) $config['infoStyle'    ]='style2';
	if (empty($config['allowDownload'])) $config['allowDownload']='yes';
	if (empty($config['days'         ])) $config['days'         ]=$DAYS_TO_SHOW_LIMIT;

	if ($config['days']<1                  ) $config['days']=1;
	if ($config['days']>$DAYS_TO_SHOW_LIMIT) $config['days']=$DAYS_TO_SHOW_LIMIT;

	$startjd=server_jd();
	$endjd  =$startjd+max(min($config['days'], 1), $DAYS_TO_SHOW_LIMIT)-1;

	if (!WT_USER_ID) {
		$allowDownload = "no";
	}

	$id="yahrzeit";
	$title='';
	if ($WT_BLOCKS['print_yahrzeit']['canconfig']) {
		if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=="user" && WT_USER_ID) {
			if ($ctype=="gedcom") {
				$name = WT_GEDCOM;
			} else {
				$name = WT_USER_NAME;
			}
			$title .= "<a href=\"javascript: configure block\" onclick=\"window.open('".encode_url("index_edit.php?name={$name}&ctype={$ctype}&action=configure&side={$side}&index={$index}")."', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">";
			$title .= "<img class=\"adminicon\" src=\"{$WT_IMAGE_DIR}/{$WT_IMAGES['admin']['small']}\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" /></a>";
		}
	}
	$title .= i18n::translate('Upcoming Yahrzeiten');
	$title .= help_link('yahrzeit');
	$content = "";

	// The standard anniversary rules cover most of the Yahrzeit rules, we just
	// need to handle a few special cases.
	// Fetch normal anniversaries...
	$yahrzeits=array();
	$hidden=0;
	for ($jd=$startjd-1; $jd<=$endjd+30;++$jd) {
		foreach (get_anniversary_events($jd, 'DEAT _YART') as $fact) {
			// Extract hebrew dates only
			if ($fact['date']->date1->CALENDAR_ESCAPE()=='@#DHEBREW@' && $fact['date']->MinJD()==$fact['date']->MaxJD()) {
				// Apply privacy
				if (displayDetailsById($fact['id']) && showFactDetails($fact['fact'], $fact['id']) && !FactViewRestricted($fact['id'], $fact['factrec'])) {
					$yahrzeits[]=$fact;
				} else {
					++$hidden;
				}
			}
		}
	}

	// ...then adjust dates
	foreach ($yahrzeits as $key=>$yahrzeit) {
		if (strpos('1 DEAT', $yahrzeit['factrec'])!==false) { // Just DEAT, not _YART
			$today=new JewishDate($yahrzeit['jd']);
			$hd=$yahrzeit['date']->MinDate();
			$hd1=new JewishDate($hd);
			$hd1->y+=1;
			$hd1->SetJDFromYMD();
			// Special rules.  See http://www.hebcal.com/help/anniv.html
			// Everything else is taken care of by our standard anniversary rules.
			if ($hd->d==30 && $hd->m==2 && $hd->y!=0 && $hd1->DaysInMonth()<30) { // 30 CSH
				// Last day in CSH
				$yahrzeit[$key]['jd']=JewishDate::YMDtoJD($today->y, 3, 1)-1;
			}
			if ($hd->d==30 && $hd->m==3 && $hd->y!=0 && $hd1->DaysInMonth()<30) { // 30 KSL
				// Last day in KSL
				$yahrzeit[$key]['jd']=JewishDate::YMDtoJD($today->y, 4, 1)-1;
			}
			if ($hd->d==30 && $hd->m==6 && $hd->y!=0 && $today->DaysInMonth()<30 && !$today->IsLeapYear()) { // 30 ADR
				// Last day in SHV
				$yahrzeit[$key]['jd']=JewishDate::YMDtoJD($today->y, 6, 1)-1;
			}
		}
	}

	switch ($config['infoStyle']) {
	case "style1": // List style
		foreach ($yahrzeits as $yahrzeit)
			if ($yahrzeit['jd']>=$startjd && $yahrzeit['jd']<$startjd+$config['days']) {
				$ind=person::GetInstance($yahrzeit['id']);
//@@			$content .= "<a href=\"".encode_url($ind->getLinkUrl())."\" class=\"list_item name2\">".$ind->getFullName()."</a>".$ind->getSexImage();
				$content .= "<a href=\"".encode_url($ind->getLinkUrl())."\" class=\"list_item name2\">".PrintReady($ind->getFullName())."</a>".$ind->getSexImage();
				$content .= "<div class=\"indent\">";
				$content .= $yahrzeit['date']->Display(true);
				$content .= ', '.i18n::translate('%s year anniversary', $yahrzeit['anniv']);
				$content .= "</div>";
			}
		break;
	case "style2": // Table style
		require_once WT_ROOT.'js/sorttable.js.htm';
		require_once WT_ROOT.'includes/classes/class_gedcomrecord.php';
		$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
		$content .= "<table id=\"{$table_id}\" class=\"sortable list_table center\">";
		$content .= "<tr>";
		$content .= "<th class=\"list_label\">".i18n::translate('NAME')."</th>";
		$content .= "<th style=\"display:none\">GIVN</th>";
		$content .= "<th class=\"list_label\">".i18n::translate('DATE')."</th>";
		$content .= "<th class=\"list_label\"><img src=\"./images/reminder.gif\" alt=\"".i18n::translate('Anniversary')."\" title=\"".i18n::translate('Anniversary')."\" border=\"0\" /></th>";
		$content .= "<th class=\"list_label\">".i18n::translate('_YART')."</th>";
		$content .= "</tr>";

		$count=0;
		foreach ($yahrzeits as $yahrzeit) {
			if ($yahrzeit['jd']>=$startjd && $yahrzeit['jd']<$startjd+$config['days']) {
				++$count;
				$ind=person::GetInstance($yahrzeit['id']);
				$content .= "<tr class=\"vevent\">"; // hCalendar:vevent
				// Record name(s)
				$name=$ind->getFullName();
				$url=$ind->getLinkUrl();
				$content .= "<td class=\"list_value_wrap\" align=\"".get_align($name)."\">";
				$content .= "<a href=\"".encode_url($ind->getLinkUrl())."\" class=\"list_item name2\" dir=\"".$TEXT_DIRECTION."\">".PrintReady($name)."</a>";
				$content .= $ind->getSexImage();
				$addname=$ind->getAddName();
				if ($addname) {
					$content .= "<br /><a href=\"".encode_url($url)."\" class=\"list_item\">".PrintReady($addname)."</a>";
				}
				$content .= "</td>";

				// GIVN for sorting
				$content .= "<td style=\"display:none\">";
				$exp = explode(",", str_replace('<', ',', $name).",");
				$content .= $exp[1];
				$content .= "</td>";

				$today=new JewishDate($yahrzeit['jd']);
				$td=new GedcomDate($today->Format('%@ %A %O %E'));

				// death/yahrzeit event date
				$content .= "<td class=\"list_value_wrap\">";
				$content .= "<a name='{$yahrzeit['jd']}'>".$yahrzeit['date']->Display(true, NULL, array())."</a>";
				$content .= "</td>";

				// Anniversary
				$content .= "<td class=\"list_value_wrap rela\">";
				$anniv = $yahrzeit['anniv'];
				if ($anniv==0) {
					$content .= '<a name="0">&nbsp;</a>';
				} else {
					$content .= "<a name=\"{$anniv}\">{$anniv}</a>";
				}
				if ($config['allowDownload']=='yes') {
					// hCalendar:dtstart and hCalendar:summary
					//TODO does this work??
					$content .= "<abbr class=\"dtstart\" title=\"".strip_tags($yahrzeit['date']->Display(false,'Ymd',array()))."\"></abbr>";
					$content .= "<abbr class=\"summary\" title=\"".i18n::translate('Anniversary')." #$anniv ".i18n::translate($yahrzeit['fact'])." : ".PrintReady(strip_tags($ind->getFullName()))."\"></abbr>";
				}

				// upcomming yahrzeit dates
				$content .= "<td class=\"list_value_wrap\">";
				$content .= "<a href=\"".$url."\" class=\"list_item url\">".$td->Display(true, NULL, array('gregorian'))."</a>"; // hCalendar:url
				$content .= "&nbsp;</td>";

				$content .= "</tr>";
			}
		}

		// table footer
		$content .= "<tr class=\"sortbottom\">";
		$content .= "<td class=\"list_label\">";
		$content .= '<a href="javascript:;" onclick="sortByOtherCol(this,1)"><img src="images/topdown.gif" alt="" border="0" /> '.i18n::translate('GIVN').'</a><br />';
		$content .= i18n::translate('Total Names').": ".$count;
		if ($hidden) {
			$content .= "<br /><span class=\"warning\">".i18n::translate('Hidden')." : {$hidden}</span>";
		}
		$content .= "</td>";
		$content .= "<td style=\"display:none\">GIVN</td>";
		$content .= "<td>";
		if ($config['allowDownload']=='yes') {
			$uri = $SERVER_URL.basename($_SERVER['REQUEST_URI']);
			$alt = i18n::translate('Download file %s', 'hCal-events.ics');
			if (count($yahrzeits)) {
				$content .= "<a href=\"http://feeds.technorati.com/events/{$uri}\"><img src=\"images/hcal.png\" border=\"0\" alt=\"{$alt}\" title=\"{$alt}\" /></a>";
			}
		}
		$content .= '</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
		$content .= '</table>';
		break;
	}

	global $THEME_DIR;
	if ($block) {
		require $THEME_DIR.'templates/block_small_temp.php';
	} else {
		require $THEME_DIR.'templates/block_main_temp.php';
	}
}

function print_yahrzeit_config($config) {
	global $WT_BLOCKS, $DAYS_TO_SHOW_LIMIT;

	if (empty($config)) $config=$WT_BLOCKS["print_yahrzeit"]["config"];

	if (empty($config['infoStyle'    ])) $config['infoStyle'    ]='style2';
	if (empty($config['allowDownload'])) $config['allowDownload']='yes';
	if (empty($config['days'         ])) $config['days'         ]=$DAYS_TO_SHOW_LIMIT;

	if ($config['days']<1                  ) $config['days']=1;
	if ($config['days']>$DAYS_TO_SHOW_LIMIT) $config['days']=$DAYS_TO_SHOW_LIMIT;

	print '<tr><td class="descriptionbox wrap width33">';
	print i18n::translate('Number of days to show');
	print help_link('days_to_show');
	print '</td><td class="optionbox">';
	print '<input type="text" name="days" size="2" value="'.$config['days'].'" />';
	print '</td></tr>';

	print '<tr><td class="descriptionbox wrap width33">';
	print i18n::translate('Presentation Style');
	print help_link('style');
	print '</td><td class="optionbox">';
	print '<select name="infoStyle">';
	foreach (array('style1'=>i18n::translate('List'), 'style2'=>i18n::translate('Table')) as $style=>$desc) {
		print "<option value=\"{$style}\"";
		if ($config['infoStyle']==$style)
			print " selected=\"selected\"";
		print ">{$desc}</option>";
	}
	print '</select></td></tr>';

	print '<tr><td class="descriptionbox wrap width33">';
	print i18n::translate('Allow calendar events download?');
	print help_link('cal_dowload');
	print '</td><td class="optionbox">';
	print '<select name="allowDownload">';
	foreach (array('yes'=>i18n::translate('Yes'), 'no'=>i18n::translate('No')) as $value=>$desc) {
		print "<option value=\"{$value}\"";
		if ($config['allowDownload']==$value)
			print " selected=\"selected\"";
		print ">{$desc}</option>";
	}
	print '</select>';

	// Cache file life is not configurable by user:  anything other than 1 day doesn't make sense
	print '<input type="hidden" name="cache" value="1" />';

	print '</td></tr>';
}
?>
