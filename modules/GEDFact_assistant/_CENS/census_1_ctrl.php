<?php
/**
 * Census Assistant Control module for phpGedView
 *
 * Census information about an individual
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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
 * @subpackage Census Assistant
 * @version $Id$
 */
 
 if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

global $summary, $theme_name, $censyear, $censdate;
 
$pid = safe_get('pid');

$censdate  = new GedcomDate("31 MAR 1901");
$censyear   = $censdate->date1->y;

$ctry       = "UK";
// $married    = GedcomDate::Compare($censdate, $marrdate);
$married=-1;

$person=Person::getInstance($pid);
// var_dump($person->getAllNames());
$nam = $person->getAllNames();
if (PrintReady($person->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($person->getDeathYear()); }
if (PrintReady($person->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($person->getBirthYear()); }
if ($married>=0 && isset($nam[1])){
	$wholename = rtrim($nam[1]['fullNN']);
} else {
	$wholename = rtrim($nam[0]['fullNN']);
}

$currpid=$pid;
?>
<script src="modules/GEDFact_assistant/_CENS/js/dynamicoptionlist.js" type="text/javascript"></script>
<script src="modules/GEDFact_assistant/_CENS/js/date.js" type="text/javascript"></script>


<?php
	echo WT_JS_START;
		 echo "var TheCenYear = opener.document.getElementById('setyear').value;";
		 echo "var TheCenCtry = opener.document.getElementById('setctry').value;"; 
	echo WT_JS_END;


	// Header of assistant window =====================================================
	echo "<div class=\"cens_header\">";
		echo "<div class=\"cens_header_left\">";
			echo i18n::translate('Head of Household:');
			echo " &nbsp;" . $wholename . "&nbsp; (" . $pid . ")";
		echo "</div>";
			if ($summary) {
				echo "<div class=\"cens_header_right\"/>". $summary. "</div>";
			}
	echo "</div>";
	

	//-- Census & Source Information Area ============================================= 
	echo "<div class=\" cens_container\">";
		echo "<span >";
			include('modules/GEDFact_assistant/_CENS/census_2_source_input.php');
		echo "</span>";
		//-- Proposed Census Text Area ================================================
		echo "<span>";
			include('modules/GEDFact_assistant/_CENS/census_4_text.php');
		echo "</span>";
	echo "</div>";
	
	//-- Search  and Add Family Members Area ========================================== 
	echo "<div class=\"optionbox cens_search\" style=\"overflow:-moz-scrollbars-horizontal;overflow-x:hidden;overflow-y:scroll;\">";
		?><!--[if lte IE 7]><style>.cens_search{margin-top:-0.7em;}</style><![EndIf]--><?php
		include('modules/GEDFact_assistant/_CENS/census_3_search_add.php');
	echo "</div>";
	
	//-- Census Text Input Area =======================================================
	?>
	<div class="optionbox cens_textinput">
		<div class="cens_textinput_left">
			<input type="button" value="<?php echo i18n::translate('Add/Insert Blank Row'); ?>" onclick="insertRowToTable('', '', '', '', '', '', '', '', 'Age', '', '', '', '', '', '');" />
		</div>
		<div class="cens_textinput_right">
			<?php echo i18n::translate('Add'); ?><br>
			<input  type="radio" name="totallyrad" value="0" checked="checked" />
		</div>	
	<?php
	
	//-- Census Add Rows Area =========================================================
		echo "<div class=\"cens_addrows\">";
			include('modules/GEDFact_assistant/_CENS/census_5_input.php');
		echo "</div>";
		?> 
	</div>
	

<script>

</script>

<script language="JavaScript" type="text/javascript">
 window.onLoad = initDynamicOptionLists();
</script>

