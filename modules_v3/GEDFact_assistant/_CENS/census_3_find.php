<?php
namespace Fisharebest\Webtrees;

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
	$filter = trim($filter);
	$filter_array = explode(' ', preg_replace('/ {2,}/', ' ', $filter));

	// Output Individual for GEDFact Assistant ======================
	echo "<table class=\"tabs_table width90\">";
	$myindilist = search_indis_names($filter_array, array($WT_TREE));
	if ($myindilist) {
		echo "<tr><td class=\"list_value_wrap\"><ul>";
		usort($myindilist, __NAMESPACE__ . '\GedcomRecord::compare');
		foreach ($myindilist as $indi) {
			$nam = $indi->getAllNames();
			$wholename = rtrim($nam[0]['givn'], '*') . "&nbsp;" . $nam[0]['surname'];
			$fulln = rtrim($nam[0]['givn'], '*') . "&nbsp;" . $nam[0]['surname'];
			$fulln = str_replace('"', '\'', $fulln); // Replace double quotes
			$fulln = str_replace("@N.N.", "(" . I18N::translate('unknown') . ")", $fulln);
			$fulln = str_replace("@P.N.", "(" . I18N::translate('unknown') . ")", $fulln);
			$givn  = rtrim($nam[0]['givn'], '*');
			$surn  = $nam[0]['surname'];
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
			$chBLDarray = Array();
			if (isset($children)) {
				foreach ($children as $key=>$child) {
					$chnam   = $child->getAllNames();
					$chfulln = rtrim($chnam[0]['givn'], '*') . " " . $chnam[0]['surname'];
					$chfulln = str_replace('"', "", $chfulln); // Must remove quotes completely here
					$chfulln = str_replace("@N.N.", "(" . I18N::translate('unknown') . ")", $chfulln);
					$chfulln = str_replace("@P.N.", "(" . I18N::translate('unknown') . ")", $chfulln); // Child’s Full Name
					$chdob   = ($child->getBirthDate()->minimumJulianDay() + $child->getBirthDate()->maximumJulianDay()) / 2; // Child’s Date of Birth (Julian)
					if (!isset($chdob)) { $chdob = ""; }
					$chdod   = ($child->getDeathDate()->minimumJulianDay() + $child->getDeathDate()->maximumJulianDay()) / 2; // Child’s Date of Death (Julian)
					if (!isset($chdod)) { $chdod = ""; }
					$chBLD   = ($chfulln . ", " . $chdob . ", " . $chdod);
					array_push($chBLDarray, $chBLD);
				}
			}
			if ($chBLDarray && $indi->getSex() == "F") {
				$chBLDarray = implode("::", $chBLDarray);
			} else {
				$chBLDarray = '';
			}

			echo "<li>";
			// ==============================================================================================================================
			// NOTES = is equivalent to= function pasterow(id, nam, mnam, label, gend, cond, dom, dob, age, dod, occu, birthpl, fbirthpl, mbirthpl, chilBLD) {
			// ==============================================================================================================================
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
