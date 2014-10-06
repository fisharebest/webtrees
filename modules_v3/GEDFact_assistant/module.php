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
		$controller = new WT_Controller_Simple();
		$filter     = WT_Filter::get('filter');
		$multiple   = WT_Filter::getBool('multiple');

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
					window.opener.paste_id(id, name, thumb);
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
					window.opener.paste_id(id);
					if (window.opener.pastename) {
						window.opener.pastename(name);
					}
					<?php if (!$multiple) echo "window.close();"; ?>
				}
			}
			function checknames(frm) {
				if (document.forms[0].subclick) {
					button = document.forms[0].subclick.value;
				} else {
					button = "";
				}
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
			usort($myindilist, array('WT_GedcomRecord', 'compare'));
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

		$controller = new WT_Controller_Simple();
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
				window.opener.alert('<?php echo strtoupper($iid2); ?> - <?php echo WT_I18N::translate('Not a valid individual, family, or source ID'); ?>');
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
		global $WT_TREE;

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
			'YOI'        => 'If Foreign Born - Year of immigration',
			'YON'        => 'If Foreign Born - Year of naturalization',
			'YUS'        => 'If Foreign Born - Years in the USA',
			'YrsM'       => 'Years Married, or Y if married in Census Year',
		);

		if (preg_match('/(.*)((?:\n.*)*)\n\.start_formatted_area\.\n(.*)((?:\n.*)*)\n.end_formatted_area\.((?:\n.*)*)/', $note->getNote(), $match)) {
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
				$title . "\n" . // The newline allows the framework to expand the details and turn the first line into a link
				'<p>' . $preamble . '</p>' .
				'<table class="table-census-assistant">' .
				'<thead>' .  $thead .  '</thead>' .
				'<tbody>' .  $tbody .  '</tbody>' .
				'</table>' .
				'<p>' . $postamble . '</p>';
		} else {
			// Not a census-assistant shared note - apply default formatting
			return WT_Filter::formatText($note->getNote(), $WT_TREE);
		}
	}

	// Modify the “add shared note” field, to create a note using the assistant
	static function print_addnewnote_assisted_link($element_id, $xref, $action) {
		global $controller;

		// We do not yet support family records
		if (!WT_GedcomRecord::getInstance($xref) instanceof WT_Individual) {
			return '';
		}

		// Only modify “add shared note” links on the add/edit actions.
		// TODO: does the “edit” action work?
		if ($action != 'add' && $action != 'edit') {
			return '';
		}

		// There are lots of “add shared note” links.  We only need to modify the 2nd one
		static $n = 0;
		if (++$n != 2) {
			return '';
		}

		$controller->addInlineJavascript('
			var pid_array=jQuery("#pid_array");
			function set_pid_array(pa) {
				pid_array.val(pa);
			}
		');

		return
			'<br>' .
			'<input type="hidden" name="pid_array" id="pid_array" value="">' .
			'<a href="#" onclick="return addnewnote_assisted(document.getElementById(\'' . $element_id . '\'), \'' . $xref . '\');">' .
			WT_I18N::translate('Create a new shared note using assistant') .
			'</a>';
	}

	// Add a selector containing UK/US/FR census dates
	public static function censusDateSelector($action, $tag, $element_id) {
		global $controller;

		if ($action == 'add' && $tag == 'CENS') {
			$controller->addInlineJavascript('
				function addDate(theCensDate) {
					var ddate = theCensDate.split(", ");
					document.getElementById("setctry").value = ddate[3];
					document.getElementById("setyear").value = ddate[0];
					cal_setDateField("' . $element_id . '", parseInt(ddate[0]), parseInt(ddate[1]), parseInt(ddate[2]));
					return false;
				}
				function pasteAsstDate(setcy, setyr) {
					document.getElementById(setcy+setyr).selected = true;
					addDate(document.getElementById("selcensdate").options[document.getElementById(\'selcensdate\').selectedIndex].value);
					return false;
				}
			');

			return '
				<select id="selcensdate" name="selcensdate" onchange = "if (this.options[this.selectedIndex].value!=\'\') {
										addDate(this.options[this.selectedIndex].value);
									}">
					<option id="defdate" value="" selected>' . WT_I18N::translate('Census date') . '</option>
					<option value=""></option>
					<option id="UK1911" class="UK"  value="1911, 3, 02, UK">UK 1911</option>
					<option id="UK1901" class="UK"  value="1901, 2, 31, UK">UK 1901</option>
					<option id="UK1891" class="UK"  value="1891, 3, 05, UK">UK 1891</option>
					<option id="UK1881" class="UK"  value="1881, 3, 03, UK">UK 1881</option>
					<option id="UK1871" class="UK"  value="1871, 3, 02, UK">UK 1871</option>
					<option id="UK1861" class="UK"  value="1861, 3, 07, UK">UK 1861</option>
					<option id="UK1851" class="UK"  value="1851, 2, 30, UK">UK 1851</option>
					<option id="UK1841" class="UK"  value="1841, 5, 06, UK">UK 1841</option>
					<option value=""></option>
					<option id="USA1940" class="USA" value="1940, 3, 01, USA">US 1940</option>
					<option id="USA1930" class="USA" value="1930, 3, 01, USA">US 1930</option>
					<option id="USA1920" class="USA" value="1920, 0, 01, USA">US 1920</option>
					<option id="USA1910" class="USA" value="1910, 3, 15, USA">US 1910</option>
					<option id="USA1900" class="USA" value="1900, 5, 01, USA">US 1900</option>
					<option id="USA1890" class="USA" value="1890, 5, 01, USA">US 1890</option>
					<option id="USA1880" class="USA" value="1880, 5, 01, USA">US 1880</option>
					<option id="USA1870" class="USA" value="1870, 5, 01, USA">US 1870</option>
					<option id="USA1860" class="USA" value="1860, 5, 01, USA">US 1860</option>
					<option id="USA1850" class="USA" value="1850, 5, 01, USA">US 1850</option>
					<option id="USA1840" class="USA" value="1840, 5, 01, USA">US 1840</option>
					<option id="USA1830" class="USA" value="1830, 5, 01, USA">US 1830</option>
					<option id="USA1820" class="USA" value="1820, 7, 07, USA">US 1820</option>
					<option id="USA1810" class="USA" value="1810, 7, 06, USA">US 1810</option>
					<option id="USA1800" class="USA" value="1800, 7, 04, USA">US 1800</option>
					<option id="USA1790" class="USA" value="1790, 7, 02, USA">US 1790</option>
					<option value=""></option>
					<option id="FR1951" class="FR" value="1951, 0, 01, FR">FR 1951</option>
					<option id="FR1946" class="FR" value="1946, 0, 01, FR">FR 1946</option>
					<option id="FR1941" class="FR" value="1941, 0, 01, FR">FR 1941</option>
					<option id="FR1936" class="FR" value="1936, 0, 01, FR">FR 1936</option>
					<option id="FR1931" class="FR" value="1931, 0, 01, FR">FR 1931</option>
					<option id="FR1926" class="FR" value="1926, 0, 01, FR">FR 1926</option>
					<option id="FR1921" class="FR" value="1921, 0, 01, FR">FR 1921</option>
					<option id="FR1916" class="FR" value="1916, 0, 01, FR">FR 1916</option>
					<option id="FR1911" class="FR" value="1911, 0, 01, FR">FR 1911</option>
					<option id="FR1906" class="FR" value="1906, 0, 01, FR">FR 1906</option>
					<option id="FR1901" class="FR" value="1901, 0, 01, FR">FR 1901</option>
					<option id="FR1896" class="FR" value="1896, 0, 01, FR">FR 1896</option>
					<option id="FR1891" class="FR" value="1891, 0, 01, FR">FR 1891</option>
					<option id="FR1886" class="FR" value="1886, 0, 01, FR">FR 1886</option>
					<option id="FR1881" class="FR" value="1881, 0, 01, FR">FR 1881</option>
					<option id="FR1876" class="FR" value="1876, 0, 01, FR">FR 1876</option>
					<option value=""></option>
				</select>

				<input type="hidden" id="setctry" name="setctry" value="">
				<input type="hidden" id="setyear" name="setyear" value="">
			';
		} else {
			return '';
		}
	}
}
