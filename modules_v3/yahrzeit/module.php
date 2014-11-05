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

use Fisharebest\ExtCalendar\JewishCalendar;
use Rhumsaa\Uuid\Uuid;
use WT\Auth;

class yahrzeit_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module.  Yahrzeiten (the plural of Yahrzeit) are special anniversaries of deaths in the Hebrew faith/calendar. */ WT_I18N::translate('Yahrzeiten');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Yahrzeiten” module.  A “Hebrew death” is a death where the date is recorded in the Hebrew calendar. */ WT_I18N::translate('A list of the Hebrew death anniversaries that will occur in the near future.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $ctype, $controller;

		$days      = get_block_setting($block_id, 'days',       7);
		$infoStyle = get_block_setting($block_id, 'infoStyle', 'table');
		$calendar  = get_block_setting($block_id, 'calendar',  'jewish');
		$block     = get_block_setting($block_id, 'block',      true);

		if ($cfg) {
			foreach (array('days', 'infoStyle', 'block') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name=$cfg[$name];
				}
			}
		}

		$startjd = WT_CLIENT_JD;
		$endjd   = WT_CLIENT_JD + $days - 1;

		$id    = $this->getName() . $block_id;
		$class = $this->getName() . '_block';
		if ($ctype === 'gedcom' && WT_USER_GEDCOM_ADMIN || $ctype === 'user' && Auth::check()) {
			$title = '<i class="icon-admin" title="'.WT_I18N::translate('Configure').'" onclick="modalDialog(\'block_edit.php?block_id='.$block_id.'\', \''.$this->getTitle().'\');"></i>';
		} else {
			$title = '';
		}
		$title .= $this->getTitle();

		$content='';
		// The standard anniversary rules cover most of the Yahrzeit rules, we just
		// need to handle a few special cases.
		// Fetch normal anniversaries...
		$yahrzeits=array();
		for ($jd=$startjd-1; $jd<=$endjd+$days; ++$jd) {
			foreach (get_anniversary_events($jd, 'DEAT _YART') as $fact) {
				// Exact hebrew dates only
				$date = $fact->getDate();
				if ($date->MinDate() instanceof WT_Date_Jewish && $date->MinJD()==$date->MaxJD()) {
					$fact->jd = $jd;
					$yahrzeits[]=$fact;
				}
			}
		}

		// ...then adjust dates
		$jewish_calendar = new JewishCalendar;

		foreach ($yahrzeits as $yahrzeit) {
			if ($yahrzeit->getTag() == 'DEAT') { // Just DEAT, not _YART
				$today=new WT_Date_Jewish($yahrzeit->jd);
				$hd=$yahrzeit->getDate()->MinDate();
				$hd1=new WT_Date_Jewish($hd);
				$hd1->y+=1;
				$hd1->setJdFromYmd();
				// Special rules.  See http://www.hebcal.com/help/anniv.html
				// Everything else is taken care of by our standard anniversary rules.
				if ($hd->d==30 && $hd->m==2 && $hd->y!=0 && $hd1->daysInMonth()<30) { // 30 CSH
					// Last day in CSH
					$yahrzeit->jd = $jewish_calendar->ymdToJd($today->y, 3, 1)-1;
				} elseif ($hd->d==30 && $hd->m==3 && $hd->y!=0 && $hd1->daysInMonth()<30) { // 30 KSL
					// Last day in KSL
					$yahrzeit->jd = $jewish_calendar->ymdToJd($today->y, 4, 1)-1;
				} elseif ($hd->d==30 && $hd->m==6 && $hd->y!=0 && $today->daysInMonth()<30 && !$today->isLeapYear()) { // 30 ADR
					// Last day in SHV
					$yahrzeit->jd = $jewish_calendar->ymdToJd($today->y, 6, 1)-1;
				}
			}
		}

		switch ($infoStyle) {
		case 'list':
			foreach ($yahrzeits as $yahrzeit)
				if ($yahrzeit->jd >= $startjd && $yahrzeit->jd < $startjd+$days) {
					$ind=$yahrzeit->getParent();
					$content .= "<a href=\"".$ind->getHtmlUrl()."\" class=\"list_item name2\">".$ind->getFullName()."</a>".$ind->getSexImage();
					$content .= "<div class=\"indent\">";
					$content .= $yahrzeit->getDate()->Display(true);
					$content .= ', '.WT_I18N::translate('%s year anniversary', $yahrzeit->anniv);
					$content .= "</div>";
				}
			break;
		case 'table':
		default:
			$table_id = Uuid::uuid4(); // table requires a unique ID
			$controller
				->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
				->addInlineJavascript('
					jQuery("#'.$table_id.'").dataTable({
						dom: \'t\',
						'.WT_I18N::datatablesI18N().',
						autoWidth: false,
						paginate: false,
						lengthChange: false,
						filter: false,
						info: true,
						jQueryUI: true,
						sorting: [[5,"asc"]],
						columns: [
							/* 0-name */ { dataSort: 1 },
							/* 1-NAME */ { visible: false },
							/* 2-date */ { dataSort: 3 },
							/* 3-DATE */ { visible: false },
							/* 4-Aniv */ { class: "center"},
							/* 5-yart */ { dataSort: 6 },
							/* 6-YART */ { visible: false }
						]
					});
					jQuery("#'.$table_id.'").css("visibility", "visible");
					jQuery(".loading-image").css("display", "none");
				');
			$content='';
			$content .= '<div class="loading-image">&nbsp;</div>';
			$content .= '<table id="'.$table_id.'" class="width100" style="visibility:hidden;">';
			$content .= '<thead><tr>';
			$content .= '<th>'.WT_Gedcom_Tag::getLabel('NAME').'</th>';
			$content .= '<th>'.WT_Gedcom_Tag::getLabel('NAME').'</th>';
			$content .= '<th>'.WT_Gedcom_Tag::getLabel('DEAT').'</th>';
			$content .= '<th>DEAT</th>';
			$content .= '<th><i class="icon-reminder" title="'.WT_I18N::translate('Anniversary').'"></i></th>';
			$content .= '<th>'.WT_Gedcom_Tag::getLabel('_YART').'</th>';
			$content .= '<th>_YART</th>';
			$content .= '</tr></thead><tbody>';

			foreach ($yahrzeits as $yahrzeit) {
				if ($yahrzeit->jd >= $startjd && $yahrzeit->jd < $startjd+$days) {
					$content .= '<tr>';
					$ind=$yahrzeit->getParent();
					// Individual name(s)
					$name=$ind->getFullName();
					$url=$ind->getHtmlUrl();
					$content .= '<td>';
					$content .= '<a href="'.$url.'">'.$name.'</a>';
					$content .= $ind->getSexImage();
					$addname=$ind->getAddName();
					if ($addname) {
						$content .= '<br><a href="'.$url.'">'.$addname.'</a>';
					}
					$content .= '</td>';
					$content .= '<td>'.$ind->getSortName().'</td>';

					// death/yahrzeit event date
					$content .= '<td>'.$yahrzeit->getDate()->Display().'</td>';
					$content .= '<td>'.$yahrzeit->getDate()->minJD().'</td>';// sortable date

					// Anniversary
					$content .= '<td>'.$yahrzeit->anniv.'</td>';

					// upcomming yahrzeit dates
					switch ($calendar) {
					case 'gregorian':
						$today=new WT_Date_Gregorian($yahrzeit->jd);
						break;
					case 'jewish':
					default:
						$today=new WT_Date_Jewish($yahrzeit->jd);
						break;
					}
					$td=new WT_Date($today->format('%@ %A %O %E'));
					$content .= '<td>'.$td->display().'</td>';
					$content .= '<td>'.$td->minJD().'</td>';// sortable date

					$content .= '</tr>';
				}
			}
			$content .= '</tbody></table>';

			break;
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
		if (WT_Filter::postBool('save') && WT_Filter::checkCsrf()) {
			set_block_setting($block_id, 'days',      WT_Filter::postInteger('days', 1, 30, 7));
			set_block_setting($block_id, 'infoStyle', WT_Filter::post('infoStyle', 'list|table', 'table'));
			set_block_setting($block_id, 'calendar',  WT_Filter::post('calendar', 'jewish|gregorian', 'jewish'));
			set_block_setting($block_id, 'block',     WT_Filter::postBool('block'));
			exit;
		}

		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$days=get_block_setting($block_id, 'days', 7);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Number of days to show');
		echo '</td><td class="optionbox">';
		echo '<input type="text" name="days" size="2" value="'.$days.'">';
		echo ' <em>', WT_I18N::plural('maximum %d day', 'maximum %d days', 30, 30) ,'</em>';
		echo '</td></tr>';

		$infoStyle=get_block_setting($block_id, 'infoStyle', 'table');
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Presentation style');
		echo '</td><td class="optionbox">';
		echo select_edit_control('infoStyle', array('list'=>WT_I18N::translate('list'), 'table'=>WT_I18N::translate('table')), null, $infoStyle, '');
		echo '</td></tr>';

		$calendar=get_block_setting($block_id, 'calendar');
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Calendar');
		echo '</td><td class="optionbox">';
		echo select_edit_control('calendar', array(
			'jewish'   =>WT_Date_Jewish::calendarName(),
			'gregorian'=>WT_Date_Gregorian::calendarName(),
		), null, $calendar, '');
		echo '</td></tr>';

		$block=get_block_setting($block_id, 'block', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: label for a yes/no option */ WT_I18N::translate('Add a scrollbar when block contents grow');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('block', $block);
		echo '</td></tr>';
	}
}
