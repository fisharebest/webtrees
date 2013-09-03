<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class GEDFact_assistant_WT_Module extends WT_Module {
	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Census assistant');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Census assistant” module */ WT_I18N::translate('An alternative way to enter census transcripts and link them to individuals.');
	}

	// Extend WT_Module
	public function modAction($mod_action) {
		switch($mod_action) {
		case '_CENS/census_3_find':
			// TODO: this file should be a method in this class
			require WT_ROOT.WT_MODULES_DIR.$this->getName().'/_CENS/census_3_find.php';
			break;
		case 'media_3_find':
			self::media_3_find();
			break;
		case 'media_query_3a':
			self::media_query_3a();
			break;
		default:
			echo $mod_action;
			header('HTTP/1.0 404 Not Found');
		}
	}

	private static function media_3_find() {
		global $MEDIA_DIRECTORY;

		$controller=new WT_Controller_Simple();
		
		$type           ='indi';
		$filter         =WT_Filter::get('filter');
		$action         =WT_Filter::get('action');
		$callback       ='paste_id';
		$media          =WT_Filter::get('media');
		$external_links =WT_Filter::get('external_links');
		$directory      =WT_Filter::get('directory');
		$multiple       =WT_Filter::getBool('multiple');
		$showthumb      =WT_Filter::getBool('showthumb');
		$all            =WT_Filter::getBool('all');
		$subclick       =WT_Filter::get('subclick');
		$choose         =WT_Filter::get('choose');
		
		$controller
			->setPageTitle(WT_I18N::translate('Find an individual'))
			->pageHeader();
		
		echo '<script>';
		?>
		
			function pasterow(id, name, gend, yob, age, bpl) {
				window.opener.opener.insertRowToTable(id, name, '', gend, '', yob, age, 'Y', '', bpl);
			}
		
			function pasteid(id, name, thumb) {
				if (thumb) {
					window.opener.<?php echo $callback; ?>(id, name, thumb);
					<?php if (!$multiple) echo "window.close();"; ?>
				} else {
					// GEDFact_assistant ========================
					if (window.opener.document.getElementById('addlinkQueue')) {
						window.opener.insertRowToTable(id, name);
						// Check if Indi, Fam or source ===================
						/*
						if (id.match("I")=="I") {
							var win01 = window.opener.window.open('edit_interface.php?action=addmedia_links&noteid=newnote&pid='+id, 'win01', edit_window_specs);
							if (window.focus) {win01.focus();}
						} else if (id.match("F")=="F") {
							// TODO --- alert('Opening Navigator with family id entered will come later');
						}
						*/
					}
					window.opener.<?php echo $callback; ?>(id);
					if (window.opener.pastename) window.opener.pastename(name);
					<?php if (!$multiple) echo "window.close();"; ?>
				}
			}
			function checknames(frm) {
				if (document.forms[0].subclick) button = document.forms[0].subclick.value;
				else button = "";
				if (frm.filter.value.length<2&button!="all") {
					alert("<?php echo WT_I18N::translate('Please enter more than one character'); ?>");
					frm.filter.focus();
					return false;
				}
				if (button=="all") {
					frm.filter.value = "";
				}
				return true;
			}
		<?php
		echo '</script>';
		
		echo "<div align=\"center\">";
		echo "<table class=\"list_table width90\" border=\"0\">";
		echo "<tr><td style=\"padding: 10px;\" valign=\"top\" class=\"facts_label03 width90\">"; // start column for find text header
		echo $controller->getPageTitle();
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		echo "<br>";
		echo '<button onclick="window.close();">', WT_I18N::translate('close'), '</button>';
		echo "<br>";
		
		$filter = trim($filter);
		$filter_array=explode(' ', preg_replace('/ {2,}/', ' ', $filter));
		echo "<table class=\"tabs_table width90\"><tr>";
		$myindilist=search_indis_names($filter_array, array(WT_GED_ID), 'AND');
		if ($myindilist) {
			echo "<td class=\"list_value_wrap\"><ul>";
			usort($myindilist, array('WT_GedcomRecord', 'Compare'));
			foreach ($myindilist as $indi) {
				$nam = WT_Filter::escapeHtml($indi->getFullName());
				echo "<li><a href=\"#\" onclick=\"pasterow(
					'".$indi->getXref()."' ,
					'".$nam."' ,
					'".$indi->getSex()."' ,
					'".$indi->getbirthyear()."' ,
					'".(1901-$indi->getbirthyear())."' ,
					'".$indi->getbirthplace()."'); return false;\">
					<b>".$indi->getFullName()."</b>&nbsp;&nbsp;&nbsp;";
	
				$born=WT_Gedcom_Tag::getLabel('BIRT');
				echo "</span><br><span class=\"list_item\">", $born, " ", $indi->getbirthyear(), "&nbsp;&nbsp;&nbsp;", $indi->getbirthplace(), "</span></a></li>";
			echo "<hr>";
			}
			echo '</ul></td></tr><tr><td class="list_label">', WT_I18N::translate('Total individuals: %s', count($myindilist)), '</tr></td>';
		} else {
			echo "<td class=\"list_value_wrap\">";
			echo WT_I18N::translate('No results found.');
			echo "</td></tr>";
		}
		echo "</table>";
		echo '</div>';
	}

	private static function media_query_3a() {
		$iid2 = WT_Filter::get('iid', WT_REGEX_XREF);

		$controller=new WT_Controller_Simple();
		$controller
			->setPageTitle(WT_I18N::translate('Link to an existing media object'))
			->pageHeader();
		
		$record=WT_GedcomRecord::getInstance($iid2);
		if ($record) {
			$headjs='';
			if ($record instanceof WT_Family) {
				if ($record->getHusband()) {
					$headjs=$record->getHusband()->getXref();
				} elseif ($record->getWife()) {
					$headjs=$record->getWife()->getXref();
				}
			}
			?>
			<script>
			function insertId() {
				if (window.opener.document.getElementById('addlinkQueue')) {
					// alert('Please move this alert window and examine the contents of the pop-up window, then click OK')
					window.opener.insertRowToTable('<?php echo $record->getXref(); ?>', '<?php echo htmlSpecialChars($record->getFullName()); ?>', '<?php echo $headjs; ?>');
					window.close();
				}
			}
			</script>
			<?php
		
		} else {
			?>
			<script>
			function insertId() {
				window.opener.alert('<?php echo strtoupper($iid2); ?> - <?php echo WT_I18N::translate('Not a valid Individual, Family or Source ID'); ?>');
				window.close();
			}
			</script>
			<?php
		}
		?>		
		<script>window.onLoad = insertId();</script>
		<?php
	}

	// Convert custom markup into HTML
	public static function formatCensusNote(WT_Note $note) {
		$headers = array(
			'AgM'        => 'Age at first marriage',
			'Age'        => 'Age at last birthday',
			'Assets'     => 'Assets = Owned,Rented - Value,Rent - Radio - Farm',
			'BIC'        => 'Born in County',
			'BOE'        => 'Born outside England',
			'BP'         => 'Birthplace - (Chapman format)',
			'Birthplace' => 'Birthplace (Full format)',
			'Bmth'       => 'Month of birth - If born within Census year',
			'ChB'        => 'Children born alive',
			'ChD'        => 'Children who have died',
			'ChL'        => 'Children still living',
			'DOB'        => 'Date of birth',
			'Edu'        => 'Education - At School, Can Read, Can Write', // or "Cannot Read, Cannot Write" ?? 
			'EmD'        => 'Employed?',
			'EmN'        => 'Unemployed?',
			'EmR'        => 'Employer?',
			'Employ'     => 'Employment',
			'Eng?'       => 'English spoken?',
			'EngL'       => 'English spoken?, if not, Native Language',
			'FBP'        => 'Father’s Birthplace - (Chapman format)',
			'Health'     => 'Health - 1.Blind, 2.Deaf & Dumb, 3.Idiotic, 4.Insane, 5.Disabled etc',
			'Home'       => 'Home Ownership - Owned/Rented-Free/Mortgaged-Farm/House-Farm Schedule number',
			'Industry'   => 'Industry',
			'Infirm'     => 'Infirmities - 1. Deaf & Dumb, 2. Blind, 3. Lunatic, 4. Imbecile/feeble-minded',
			'Lang'       => 'If Foreign Born - Native Language',
			'MBP'        => 'Mother’s Birthplace - (Chapman format)',
			'MC'         => 'Marital Condition - Married, Single, Unmarried, Widowed or Divorced',
			'Mmth'       => 'Month of marriage - If married during Census Year',
			'MnsE'       => 'Months employed during Census Year',
			'MnsU'       => 'Months unemployed during Census Year',
			'N/A'        => 'If Foreign Born - Naturalized, Alien',
			'NL'         => 'If Foreign Born - Native Language',
			'Name'       => 'Full Name or Married name if married',
			'Occupation' => 'Occupation',
			'Par'        => 'Parentage - Father if foreign born, Mother if foreign born',
			'Race'       => 'Race or Color - Black, White, Mulatto, Asian, Indian, Chinese etc',
			'Relation'   => 'Relationship to Head of Household',
			'Sex'        => 'Male or Female',
			'Situ'       => 'Situation - Disease, Infirmity, Convict, Pauper etc',
			'Ten'        => 'Tenure - Owned/Rented, (if owned)Free/Morgaged',
			'Vet'        => 'War Veteran?',
			'WH'         => 'Working at Home?',
			'War'        => 'War or Expedition',
			'WksU'       => 'Weeks unemployed during Census Year',
			'YOI'        => 'If Foreign Born - Year of Immigration',
			'YON'        => 'If Foreign Born - Year of Naturalization',
			'YUS'        => 'If Foreign Born - Years in the USA',
			'YrsM'       => 'Years Married, or Y if married in Census Year',
		);

		if (preg_match('/(.*)((?:\n.*)*)\n\.start_formatted_area\.\n(.*)((?:\n.*)*)\n.end_formatted_area\.\n(.*(?:\n.*)*)/', $note->getNote(), $match)) {
			// This looks like a census-assistant shared note
			$title     = WT_Filter::escapeHtml($match[1]);
			$preamble  = WT_Filter::escapeHtml($match[2]);
			$header    = WT_Filter::escapeHtml($match[3]);
			$data      = WT_Filter::escapeHtml($match[4]);
			$postamble = WT_Filter::escapeHtml($match[5]);

			$fmt_headers = array();
			foreach ($headers as $key=>$value) {
				$fmt_headers['.b.' . $key] = '<span title="' . WT_Filter::escapeHtml($value) . '">' . $key . '</span>';
			}

			// Substitue header labels and format as HTML
			$thead = '<tr><th>' . strtr(str_replace('|', '</th><th>', $header), $fmt_headers) . '</th></tr>';

			// Format data as HTML
			$tbody = '';
			foreach (explode("\n", $data) as $row) {
				$tbody .= '<tr>';
				foreach (explode('|', $row) as $column) {
					$tbody .= '<td>' . $column . '</td>';
				}
				$tbody .= '</tr>';
			}

			return
				'<span class="note1">' . $title . '</span>' .
				'<br>' . // Needed to allow the first line to be converted to a link
				'<span class="note1">' . $preamble . '</span>' .
				'<table class="note2">' .
				'<thead>' .  $thead .  '</thead>' .
				'<tbody>' .  $tbody .  '</tbody>' .
				'</table>' .
				'<span class="note1">' . $postamble . '</span>';
		} else {
			// Not a census-assistant shared note - apply default formatting
			return expand_urls($note->getNote());
		}
	}
}
