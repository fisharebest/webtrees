<?php
// Census Assistant Control module for webtrees
//
// Census information about an individual
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team.
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

 if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

global $summary, $censyear, $censdate;

$censdate = new WT_Date('31 MAR 1901');
$censyear = $censdate->date1->y;
$ctry     = 'UK';

// === Set $married to "Not married as we only want the Birth name here" ===
$married=-1;

$nam = $person->getAllNames();
if ($person->getDeathYear() == 0) {
	$DeathYr = '';
} else {
	$DeathYr = $person->getDeathYear();
}
if ($person->getBirthYear() == 0) {
	$BirthYr = '';
} else {
	$BirthYr = $person->getBirthYear();
}
$fulln = rtrim($nam[0]['givn'],'*')." ".$nam[0]['surname'];
$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
$wholename = $fulln;

echo '<script src="', WT_STATIC_URL, WT_MODULES_DIR, 'GEDFact_assistant/_CENS/js/dynamicoptionlist.js"></script>';
echo '<script src="', WT_STATIC_URL, WT_MODULES_DIR, 'GEDFact_assistant/_CENS/js/date.js"></script>';

echo '<script>';
echo 'var TheCenYear = opener.document.getElementById("setyear").value;';
echo 'var TheCenCtry = opener.document.getElementById("setctry").value;';
echo '</script>';

// Header of assistant window =====================================================
echo '<div class="cens_header">';
echo '<div class="cens_header_left">';
echo WT_I18N::translate('Head of household:');
echo ' ', $wholename;
echo '</div>';
if ($summary) {
	echo '<div class="cens_header_right">', $summary, '</div>';
}
echo '</div>';

//-- Census & Source Information Area =============================================
echo '<div class="cens_container">';
echo '<span >';
require WT_ROOT.WT_MODULES_DIR.'GEDFact_assistant/_CENS/census_2_source_input.php';
echo '</span>';
//-- Proposed Census Text Area ================================================
echo '<span>';
require WT_ROOT.WT_MODULES_DIR.'GEDFact_assistant/_CENS/census_4_text.php';
echo '</span>';
echo '</div>';

//-- Search  and Add Family Members Area ==========================================
echo '<div class="optionbox cens_search" style="overflow:-moz-scrollbars-horizontal;overflow-x:hidden;overflow-y:scroll;">';
?><!--[if lte IE 7]><style>.cens_search{margin-top:-0.7em;}</style><![EndIf]--><?php
require WT_ROOT.WT_MODULES_DIR.'GEDFact_assistant/_CENS/census_3_search_add.php';
echo '</div>';

//-- Census Text Input Area =======================================================
?>
<div class="optionbox cens_textinput">
	<div class="cens_textinput_left">
		<input type="button" value="<?php echo WT_I18N::translate('Add/insert a blank row'); ?>" onclick="insertRowToTable('', '', '', '', '', '', '', '', 'Age', '', '', '', '', '', '');">
	</div>
	<div class="cens_textinput_right">
		<?php echo WT_I18N::translate('Add'); ?>
		<input  type="radio" name="totallyrad" value="0" checked="checked">
	</div>
	<?php
	//-- Census Add Rows Area =========================================================
	echo '<div class="cens_addrows">';
	require WT_ROOT.WT_MODULES_DIR.'GEDFact_assistant/_CENS/census_5_input.php';
	echo '</div>';
	?>
</div>
<script>window.onLoad = initDynamicOptionLists();</script>
