<?php
// GEDFact page
//
// GEDFact Census information about an individual
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

$xref = WT_Filter::get('xref', WT_REGEX_XREF);

$person = WT_Individual::getInstance($xref);
check_record_access($person);

$controller
	->setPageTitle(WT_I18N::translate('Create a new shared note using assistant'))
	->addInlineJavascript(
		'jQuery("head").append(\'<link rel="stylesheet" href="' . WT_STATIC_URL . WT_MODULES_DIR . 'GEDFact_assistant/css/cens_style.css" type="text/css">\');'
	)
	->pageHeader();

echo '<div id="edit_interface-page">';
echo '<h3>', $controller->getPageTitle(), '&nbsp;&nbsp;';
	// When more languages are added to the wiki, we can expand or redesign this
	switch (WT_LOCALE) {
	case 'fr':
		echo wiki_help_link('/fr/Module_Assistant_Recensement');
		break;
	case 'en':
	default:
		echo wiki_help_link('/en/Census_Assistant_module');
		break;
	}
echo '</h3>';

?>
<div class="center" style="width:100%;">
	<?php
	?>
	<form method="post" action="edit_interface.php" onsubmit="return check_form(this);">
		<input type="hidden" name="action" value="addnoteaction_assisted">
		<input type="hidden" name="noteid" value="newnote">
		<input id="pid_array" type="hidden" name="pid_array" value="none">
		<input id="xref" type="hidden" name="xref" value=<?php echo $xref; ?>>
		<?php
		echo WT_Filter::getCsrf();

global $tabno, $linkToID, $SEARCH_SPIDER;
global $SHOW_AGE_DIFF, $GEDCOM;
global $show_full, $famid;

$summary=$person->format_first_major_fact(WT_EVENTS_BIRT, 2);
if (!($person->isDead())) {
	// If alive display age
	$bdate=$person->getBirthDate();
	$age = WT_Date::GetAgeGedcom($bdate);
	if ($age!="") {
		$summary.= "<span class=\"label\">".WT_I18N::translate('Age').":</span><span class=\"field\"> ".get_age_at_event($age, true)."</span>";
	}
}
$summary.=$person->format_first_major_fact(WT_EVENTS_DEAT, 2);

require WT_ROOT.WT_MODULES_DIR.'GEDFact_assistant/_CENS/census_1_ctrl.php';

?>
		</form>
	</div>
	<div style="clear:both;"></div>
</div><!-- id="edit_interface-page" -->
