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

class yahrzeit_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('Upcoming Yahrzeiten');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('The Upcoming Yahrzeiten block shows anniversaries of death dates that will occur in the near future.  You can configure the period shown, and the Administrator can configure how far into the future this block will look.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id) {
		global $ctype, $SHOW_ID_NUMBERS, $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES, $WT_BLOCKS, $DAYS_TO_SHOW_LIMIT, $SHOW_MARRIED_NAMES, $SERVER_URL, $THEME_DIR;

		$days=get_block_setting($block_id, 'days', $DAYS_TO_SHOW_LIMIT);
		$infoStyle=get_block_setting($block_id, 'infoStyle', 'table');
		$allowDownload=WT_USER_ID && get_block_setting($block_id, 'allowDownload', true);
		$block=get_block_setting($block_id, 'block', true);

		$startjd=server_jd();
		$endjd=$startjd+$days-1;

		$id=$this->getName().$block_id;
		if ($ctype=='gedcom' && WT_USER_GEDCOM_ADMIN || $ctype=='user' && WT_USER_ID) {
			$title="<a href=\"javascript: configure block\" onclick=\"window.open('index_edit.php?action=configure&amp;ctype={$ctype}&amp;block_id={$block_id}', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\"><img class=\"adminicon\" src=\"$WT_IMAGE_DIR/".$WT_IMAGES["admin"]["small"]."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" /></a>";
		} else {
			$title='';
		}
		$title.= i18n::translate('Upcoming Yahrzeiten').help_link('yahrzeit');
		$content='';

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

		switch ($infoStyle) {
		case 'list':
			foreach ($yahrzeits as $yahrzeit)
				if ($yahrzeit['jd']>=$startjd && $yahrzeit['jd']<$startjd+$days) {
					$ind=person::GetInstance($yahrzeit['id']);
					$content .= "<a href=\"".encode_url($ind->getLinkUrl())."\" class=\"list_item name2\">".$ind->getFullName()."</a>".$ind->getSexImage();
					$content .= "<div class=\"indent\">";
					$content .= $yahrzeit['date']->Display(true);
					$content .= ', '.i18n::translate('%s year anniversary', $yahrzeit['anniv']);
					$content .= "</div>";
				}
			break;
		case 'table':
		default:
			require_once WT_ROOT.'js/sorttable.js.htm';
			require_once WT_ROOT.'includes/classes/class_gedcomrecord.php';
			$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
			$content .= "<table id=\"{$table_id}\" class=\"sortable list_table center\">";
			$content .= "<tr>";
			$content .= "<th class=\"list_label\">".translate_fact('NAME')."</th>";
			$content .= "<th style=\"display:none\">GIVN</th>";
			$content .= "<th class=\"list_label\">".translate_fact('DATE')."</th>";
			$content .= "<th class=\"list_label\"><img src=\"./images/reminder.gif\" alt=\"".i18n::translate('Anniversary')."\" title=\"".i18n::translate('Anniversary')."\" border=\"0\" /></th>";
			$content .= "<th class=\"list_label\">".translate_fact('_YART')."</th>";
			$content .= "</tr>";

			$count=0;
			foreach ($yahrzeits as $yahrzeit) {
				if ($yahrzeit['jd']>=$startjd && $yahrzeit['jd']<$startjd+$days) {
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
					if ($allowDownload) {
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
			$content .= '<a href="javascript:;" onclick="sortByOtherCol(this,1)"><img src="images/topdown.gif" alt="" border="0" /> '.translate_fact('GIVN').'</a><br />';
			$content .= i18n::translate('Total Names').": ".$count;
			if ($hidden) {
				$content .= "<br /><span class=\"warning\">".i18n::translate('Hidden')." : {$hidden}</span>";
			}
			$content .= "</td>";
			$content .= "<td style=\"display:none\">GIVN</td>";
			$content .= "<td>";
			if ($allowDownload) {
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

		$block=get_block_setting($block_id, 'block', true);
		if ($block) {
			require $THEME_DIR.'templates/block_small_temp.php';
		} else {
			require $THEME_DIR.'templates/block_main_temp.php';
		}
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
		global $DAYS_TO_SHOW_LIMIT;

		if (safe_POST_bool('save')) {
			set_block_setting($block_id, 'days', safe_POST_integer('days', 1, $DAYS_TO_SHOW_LIMIT, $DAYS_TO_SHOW_LIMIT));
			set_block_setting($block_id, 'infoStyle', safe_POST('infoStyle', array('list', 'table'), 'table'));
			set_block_setting($block_id, 'allowDownload', safe_POST_bool('allowDownload'));
			set_block_setting($block_id, 'block',  safe_POST_bool('block'));
			echo WT_JS_START, 'window.opener.location.href=window.opener.location.href;window.close();', WT_JS_END;
			exit;
		}

		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$days=get_block_setting($block_id, 'days', $DAYS_TO_SHOW_LIMIT);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Number of days to show'), help_link('days_to_show');
		echo '</td><td class="optionbox">';
		echo '<input type="text" name="days" size="2" value="'.$days.'" />';
		echo '</td></tr>';

		$infoStyle=get_block_setting($block_id, 'infoStyle', 'style2');
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Presentation style'), help_link('style');
		echo '</td><td class="optionbox">';
		echo select_edit_control('infoStyle', array('list'=>i18n::translate('List'), 'table'=>i18n::translate('Table')), null, $infoStyle, '');
		echo '</td></tr>';

		$allowDownload=get_block_setting($block_id, 'allowDownload', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Allow calendar events download?'), help_link('cal_dowload');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('allowDownload', $allowDownload);
		echo '</td></tr>';

		$block=get_block_setting($block_id, 'block', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Add a scrollbar when block contents grow');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('block', $block);
		echo '</td></tr>';
	}
}
