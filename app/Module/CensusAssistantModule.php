<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Controller\SimpleController;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Note;

/**
 * Class CensusAssistantModule
 */
class CensusAssistantModule extends AbstractModule {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Census assistant');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Census assistant” module */ I18N::translate('An alternative way to enter census transcripts and link them to individuals.');
	}

	/**
	 * This is a general purpose hook, allowing modules to respond to routes
	 * of the form module.php?mod=FOO&mod_action=BAR
	 *
	 * @param string $mod_action
	 */
	public function modAction($mod_action) {
		switch ($mod_action) {
		case 'census_find':
			self::censusFind();
			break;
		case 'media_find':
			self::mediaFind();
			break;
		case 'media_query_3a':
			self::mediaQuery();
			break;
		default:
			http_response_code(404);
		}
	}

	/**
	 * Find an individual.
	 */
	private static function censusFind() {
		global $WT_TREE;

		$controller = new SimpleController;

		$filter   = Filter::get('filter');
		$action   = Filter::get('action');
		$callback = Filter::get('callback');
		$multiple = Filter::getBool('multiple');

		$controller
			->setPageTitle(I18N::translate('Find an individual'))
			->pageHeader();

		?>
		<script>
			function pasterow(id, nam, mnam, label, gend, cond, dom, dob, dod, occu, age, birthpl, fbirthpl, mbirthpl, chilBLD) {
				window.opener.insertRowToTable(id, nam, mnam, label, gend, cond, dom, dob, dod, occu, age, birthpl, fbirthpl, mbirthpl, chilBLD);
				<?php if (!$multiple) echo "window.close();"; ?>
			}

			function pasteid(id, name, thumb) {
				if (thumb) {
					window.opener.<?php echo $callback; ?>(id, name, thumb);
					<?php if (!$multiple) echo "window.close();"; ?>
				} else {
					// GEDFact_assistant ========================
					if (window.opener.document.getElementById('addlinkQueue')) {
						window.opener.insertRowToTable(id, name);
					}
					window.opener.<?php echo $callback; ?>(id);
					if (window.opener.pastename) {
						window.opener.pastename(name);
					}
					<?php if (!$multiple) echo "window.close();"; ?>
				}
			}
		</script>
		<?php

		echo "<div align=\"center\">";
		echo "<table class=\"list_table width90\" border=\"0\">";
		echo "<tr><td style=\"padding: 10px;\" valign=\"top\" class=\"facts_label03 width90\">";
		echo I18N::translate('Find an individual');
		echo "</td>";
		echo "</table>";
		echo "<br>";

		if ($action == "filter") {
			$filter       = trim($filter);
			$filter_array = explode(' ', preg_replace('/ {2,}/', ' ', $filter));

			// Output Individual for GEDFact Assistant ======================
			echo "<table class=\"tabs_table width90\">";
			$myindilist = FunctionsDb::searchIndividualNames($filter_array, array($WT_TREE));
			if ($myindilist) {
				echo "<tr><td class=\"list_value_wrap\"><ul>";
				usort($myindilist, '\Fisharebest\Webtrees\GedcomRecord::compare');
				foreach ($myindilist as $indi) {
					$nam       = $indi->getAllNames();
					$wholename = rtrim($nam[0]['givn'], '*') . "&nbsp;" . $nam[0]['surname'];
					$fulln     = rtrim($nam[0]['givn'], '*') . "&nbsp;" . $nam[0]['surname'];
					$fulln     = str_replace('"', '\'', $fulln); // Replace double quotes
					$fulln     = str_replace("@N.N.", "(" . I18N::translate('unknown') . ")", $fulln);
					$fulln     = str_replace("@P.N.", "(" . I18N::translate('unknown') . ")", $fulln);
					$givn      = rtrim($nam[0]['givn'], '*');
					$surn      = $nam[0]['surname'];
					if (isset($nam[1])) {
						$fulmn = rtrim($nam[1]['givn'], '*') . "&nbsp;" . $nam[1]['surname'];
						$fulmn = str_replace('"', '\'', $fulmn); // Replace double quotes
						$fulmn = str_replace("@N.N.", "(" . I18N::translate('unknown') . ")", $fulmn);
						$fulmn = str_replace("@P.N.", "(" . I18N::translate('unknown') . ")", $fulmn);
						$marn  = $nam[1]['surname'];
					} else {
						$fulmn = $fulln;
					}

					//-- Build Indi Parents Family to get FBP and MBP  -----------
					foreach ($indi->getChildFamilies() as $family) {
						$father = $family->getHusband();
						$mother = $family->getWife();
						if (!is_null($father)) {
							$FBP = $father->getBirthPlace();
						}
						if (!is_null($mother)) {
							$MBP = $mother->getBirthPlace();
						}
					}
					if (!isset($FBP)) { $FBP = "UNK, UNK, UNK, UNK"; }
					if (!isset($MBP)) { $MBP = "UNK, UNK, UNK, UNK"; }

					//-- Build Indi Spouse Family to get marriage Date ----------
					foreach ($indi->getSpouseFamilies() as $family) {
						$marrdate = $family->getMarriageDate();
						$marrdate = ($marrdate->minimumJulianDay() + $marrdate->maximumJulianDay()) / 2; // Julian
						$children = $family->getChildren();
					}
					if (!isset($marrdate)) { $marrdate = ""; }

					//-- Get Children’s Name, DOB, DOD --------------------------
					$chBLDarray = array();
					if (isset($children)) {
						foreach ($children as $key => $child) {
							$chnam                       = $child->getAllNames();
							$chfulln                     = rtrim($chnam[0]['givn'], '*') . " " . $chnam[0]['surname'];
							$chfulln                     = str_replace('"', "", $chfulln); // Must remove quotes completely here
							$chfulln                     = str_replace("@N.N.", "(" . I18N::translate('unknown') . ")", $chfulln);
							$chfulln                     = str_replace("@P.N.", "(" . I18N::translate('unknown') . ")", $chfulln); // Child’s Full Name
							$chdob                       = ($child->getBirthDate()->minimumJulianDay() + $child->getBirthDate()->maximumJulianDay()) / 2; // Child’s Date of Birth (Julian)
							if (!isset($chdob)) { $chdob = ""; }
							$chdod                       = ($child->getDeathDate()->minimumJulianDay() + $child->getDeathDate()->maximumJulianDay()) / 2; // Child’s Date of Death (Julian)
							if (!isset($chdod)) { $chdod = ""; }
							$chBLD                       = ($chfulln . ", " . $chdob . ", " . $chdod);
							array_push($chBLDarray, $chBLD);
						}
					}
					if ($chBLDarray && $indi->getSex() == "F") {
						$chBLDarray = implode("::", $chBLDarray);
					} else {
						$chBLDarray = '';
					}

					echo "<li>";
					echo "<a href=\"#\" onclick=\"window.opener.insertRowToTable(";
					echo "'" . $indi->getXref() . "', "; // id        - Indi Id
					echo "'" . addslashes(strip_tags($fulln)) . "', "; // nam       - Name
					echo "'" . addslashes(strip_tags($fulmn)) . "', "; // mnam      - Married Name
					echo "'-', "; // label     - Relation to Head of Household
					echo "'" . $indi->getSex() . "', "; // gend      - Sex
					echo "'S', "; // cond      - Marital Condition
					echo "'" . $marrdate . "', "; // dom       - Date of Marriage
					echo "'" . (($indi->getBirthDate()->minimumJulianDay() + $indi->getBirthDate()->maximumJulianDay()) / 2) . "' ,"; // dob       - Date of Birth
					echo "'" . (1901 - $indi->getbirthyear()) . "' ,"; // ~age~     - Census Date minus YOB (Preliminary)
					echo "'" . (($indi->getDeathDate()->minimumJulianDay() + $indi->getDeathDate()->maximumJulianDay()) / 2) . "' ,"; // dod       - Date of Death
					echo "'', "; // occu      - Occupation
					echo "'" . Filter::escapeHtml($indi->getbirthplace()) . "', "; // birthpl   - Birthplace
					echo "'" . $FBP . "', "; // fbirthpl  - Father’s Birthplace
					echo "'" . $MBP . "', "; // mbirthpl  - Mother’s Birthplace
					echo "'" . $chBLDarray . "'"; // chilBLD   - Array of Children (name, birthdate, deathdate)
					echo ");";
					echo "return false;\">";
					echo "<b>" . $indi->getFullName() . "</b>&nbsp;&nbsp;&nbsp;"; // Name Link
					echo "</span><br><span class=\"list_item\">", GedcomTag::getLabel('BIRT', $indi), " ", $indi->getbirthyear(), "&nbsp;&nbsp;&nbsp;", $indi->getbirthplace(), "</span>";
					echo "</a>";
					echo "</li>";
					echo "<hr>";
				}
				echo '</ul></td></tr>';
			} else {
				echo "<tr><td class=\"list_value_wrap\">";
				echo I18N::translate('No results found.');
				echo "</td></tr>";
			}
			echo "</table>";
		}
		echo '<button onclick="window.close();">', I18N::translate('close'), '</button>';
		echo "</div>"; // Close div that centers table
	}

	/**
	 * Find a media object.
	 */
	private static function mediaFind() {
		global $WT_TREE;

		$controller = new SimpleController;
		$filter     = Filter::get('filter');
		$multiple   = Filter::getBool('multiple');

		$controller
			->setPageTitle(I18N::translate('Find an individual'))
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
					alert("<?php echo I18N::translate('Please enter more than one character.'); ?>");
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
		echo '<button onclick="window.close();">', I18N::translate('close'), '</button>';
		echo "<br>";

		$filter       = trim($filter);
		$filter_array = explode(' ', preg_replace('/ {2,}/', ' ', $filter));
		echo "<table class=\"tabs_table width90\"><tr>";
		$myindilist = FunctionsDb::searchIndividualNames($filter_array, array($WT_TREE));
		if ($myindilist) {
			echo "<td class=\"list_value_wrap\"><ul>";
			usort($myindilist, '\Fisharebest\Webtrees\GedcomRecord::compare');
			foreach ($myindilist as $indi) {
				$nam = Filter::escapeHtml($indi->getFullName());
				echo "<li><a href=\"#\" onclick=\"pasterow(
					'" . $indi->getXref() . "' ,
					'" . $nam . "' ,
					'" . $indi->getSex() . "' ,
					'" . $indi->getbirthyear() . "' ,
					'" . (1901 - $indi->getbirthyear()) . "' ,
					'" . $indi->getbirthplace() . "'); return false;\">
					<b>" . $indi->getFullName() . "</b>&nbsp;&nbsp;&nbsp;";

				$born = GedcomTag::getLabel('BIRT');
				echo "</span><br><span class=\"list_item\">", $born, " ", $indi->getbirthyear(), "&nbsp;&nbsp;&nbsp;", $indi->getbirthplace(), "</span></a></li>";
			echo "<hr>";
			}
			echo '</ul></td></tr><tr><td class="list_label">', I18N::translate('Total individuals: %s', count($myindilist)), '</tr></td>';
		} else {
			echo "<td class=\"list_value_wrap\">";
			echo I18N::translate('No results found.');
			echo "</td></tr>";
		}
		echo "</table>";
		echo '</div>';
	}

	/**
	 * Search for a media object.
	 */
	private static function mediaQuery() {
		global $WT_TREE;

		$iid2 = Filter::get('iid', WT_REGEX_XREF);

		$controller = new SimpleController;
		$controller
			->setPageTitle(I18N::translate('Link to an existing media object'))
			->pageHeader();

		$record = GedcomRecord::getInstance($iid2, $WT_TREE);
		if ($record) {
			$headjs = '';
			if ($record instanceof Family) {
				if ($record->getHusband()) {
					$headjs = $record->getHusband()->getXref();
				} elseif ($record->getWife()) {
					$headjs = $record->getWife()->getXref();
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
				window.opener.alert('<?php echo $iid2; ?> - <?php echo I18N::translate('Not a valid individual, family, or source ID'); ?>');
				window.close();
			}
			</script>
			<?php
		}
		?>
		<script>window.onLoad = insertId();</script>
		<?php
	}

	/**
	 * Convert custom markup into HTML
	 *
	 * @param Note $note
	 *
	 * @return string
	 */
	public static function formatCensusNote(Note $note) {
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
			$title     = Filter::escapeHtml($match[1]);
			$preamble  = Filter::escapeHtml($match[2]);
			$header    = Filter::escapeHtml($match[3]);
			$data      = Filter::escapeHtml($match[4]);
			$postamble = Filter::escapeHtml($match[5]);

			$fmt_headers = array();
			foreach ($headers as $key => $value) {
				$fmt_headers['.b.' . $key] = '<span title="' . Filter::escapeHtml($value) . '">' . $key . '</span>';
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
				'<thead>' . $thead . '</thead>' .
				'<tbody>' . $tbody . '</tbody>' .
				'</table>' .
				'<p>' . $postamble . '</p>';
		} else {
			// Not a census-assistant shared note - apply default formatting
			return Filter::formatText($note->getNote(), $WT_TREE);
		}
	}
}
