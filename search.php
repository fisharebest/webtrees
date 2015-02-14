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

define('WT_SCRIPT_NAME', 'search.php');
require './includes/session.php';

$controller = new SearchController;
$controller
	->pageHeader()
	->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
	->addInlineJavascript('autocomplete();');

?>
<script>
	function checknames(frm) {
		action = "<?php echo $controller->action; ?>";
		if (action == "general")
		{
			if (frm.query.value.length<2) {
				alert("<?php echo I18N::translate('Please enter more than one character'); ?>");
				frm.query.focus();
				return false;
			}
		}
		else if (action == "soundex")
		{
			year = frm.year.value;
			fname = frm.firstname.value;
			lname = frm.lastname.value;
			place = frm.place.value;

			if (year == "") {
				if (fname.length < 2 && lname.length < 2 && place.length < 2) {
					alert("<?php echo I18N::translate('Please enter more than one character'); ?>");
					return false;
				}
			}

			if (year != "") {
				if (fname === "" && lname === "" && place === "") {
					alert("<?php echo I18N::translate('Please enter a given name, surname, or place in addition to the year'); ?>");
					frm.firstname.focus();
					return false;
				}
			}
			return true;
		}
		return true;
	}

</script>
<?php
echo '<div id="search-page">
	<h2>' , $controller->getPageTitle(), '</h2>';
	//========== Search Form Outer Table //==========
	echo '<form method="post" name="searchform" onsubmit="return checknames(this);" action="search.php"><input type="hidden" name="action" value="', $controller->action, '"><input type="hidden" name="isPostBack" value="true">
	<div id="search-page-table">';
		//========== General search Form ==========
		if ($controller->action == "general") {
			echo '<div class="label">', I18N::translate('Search for'), '</div>
			<div class="value"><input tabindex="1" id="query" type="text" name="query" value="';
				if (isset($controller->myquery)) {
					echo $controller->myquery;
				}
				echo '" size="40" autofocus> ', print_specialchar_link('query'), '</div>
			<div class="label">' ,  I18N::translate('Records'), '</div>
			<div class="value"><p>
				<input type="checkbox" ';
				echo isset ($controller->srindi) || !$controller->isPostBack ? 'checked' : '';
				echo ' value="yes" id="srindi" name="srindi">
					<label for="srindi">' ,  I18N::translate('Individuals'), '</label>
				</p><p>
				<input type="checkbox" ';
				echo isset ($controller->srfams) ? 'checked' : '';
				echo ' value="yes" id="srfams" name="srfams">
					<label for="srfams">' , I18N::translate('Families'), '</label>
				</p><p>
				<input type="checkbox" ';
				echo isset ($controller->srsour) ? 'checked' : '';
				echo ' value="yes" id="srsour" name="srsour">
					<label for="srsour">' ,  I18N::translate('Sources'), '</label>
				</p><p>
				<input type="checkbox" ';
				echo isset ($controller->srnote) ? 'checked' : '';
				echo ' value="yes" id="srnote" name="srnote">
					<label for="srnote">' ,  I18N::translate('Shared notes'), '</label>
			</p></div>
			<div class="label">' , I18N::translate('Associates'), '</div>
			<div class="value"><input type="checkbox" id="showasso" name="showasso" value="on" ';
				echo $controller->showasso === 'on' ? 'checked' : '';
			echo '><label for="showasso">', I18N::translate('Show related individuals/families'), '</label></div>';
		}
		//========== Search and replace Search Form ==========
		if ($controller->action == "replace") {
			if (WT_USER_CAN_EDIT) {
				echo '<div class="label">', I18N::translate('Search for'), '</div>
					<div class="value"><input tabindex="1" name="query" value="" type="text" autofocus></div>
					<div class="label">',  I18N::translate('Replace with'), '</div>
					<div class="value"><input tabindex="2" name="replace" value="" type="text"></div>';
				?>
				<script>
					function checkAll(box) {
						if (!box.checked) {
							box.form.replaceNames.disabled = false;
							box.form.replacePlaces.disabled = false;
							box.form.replacePlacesWord.disabled = false;
						} else {
							box.form.replaceNames.disabled = true;
							box.form.replacePlaces.disabled = true;
							box.form.replacePlacesWord.disabled = true;
						}
					}
				</script>
				<?php
				echo '<div class="label">', I18N::translate('Search'), '</div>
					<div class="value"><p>
						<input id="replaceAll" checked onclick="checkAll(this);" value="yes" name="replaceAll" type="checkbox">
						<label for="replaceAll">' , I18N::translate('Entire record'), '</label>
						<hr>
					</p><p>
						<input id="replaceNames" checked disabled value="yes" name="replaceNames" type="checkbox">
						<label for="replaceNames">' , I18N::translate('Individuals'), '</label>
					</p><p>
						<input id="replacePlace" checked disabled value="yes" name="replacePlaces" type="checkbox">
						<label for="replacePlace">' , I18N::translate('Place'), '</label>
					</p><p>
						<input id="replaceWords" checked disabled value="yes" name="replacePlacesWord" type="checkbox">
						<label for="replaceWords">' , I18N::translate('Whole words only'), '</label>
					</p></div>';
			}
		}
		//========== Phonetic search Form //==========
		if ($controller->action == "soundex") {
			echo '<div class="label">', I18N::translate('Given name'), '</div>
				<div class="value"><input tabindex="3" type="text" data-autocomplete-type="GIVN" name="firstname" value="' , Filter::escapeHtml($controller->firstname), '" autofocus></div>
				<div class="label">' , I18N::translate('Surname'), '</div>
				<div class="value"><input tabindex="4" type="text" data-autocomplete-type="SURN" name="lastname" value="' , Filter::escapeHtml($controller->lastname), '"></div>
				<div class="label">' , I18N::translate('Place'), '</div>
				<div class="value"><input tabindex="5" type="text"  data-autocomplete-type="PLAC2" name="place" value="' , Filter::escapeHtml($controller->place), '"></div>
				<div class="label">' , I18N::translate('Year'), '</div>
				<div class="value"><input tabindex="6" type="text" name="year" value="' , Filter::escapeHtml($controller->year), '"></div>';

			// ---- Soundex type options (Russell, DaitchM) ---
			echo '<div class="label">', I18N::translate('Phonetic algorithm'), '</div>
				<div class="value"><p>
					<input type="radio" name="soundex" value="Russell" ';
						if ($controller->soundex === 'Russell') {
							echo 'checked';
						}
						echo '>', I18N::translate('Russell');
					echo '</p><p>
						<input type="radio" name="soundex" value="DaitchM" ';
						echo $controller->soundex === 'DaitchM' || $controller->soundex == "" ? 'checked' : '';
						echo'>', I18N::translate('Daitch-Mokotoff');
				echo '</p></div>';
			// Associates Section
			echo '<div class="label">', I18N::translate('Associates'), '</div>
				<div class="value"><input type="checkbox" name="showasso" value="on" ';
					echo $controller->showasso === 'on' ? 'checked' : '';
					echo '>', I18N::translate('Show related individuals/families'),
				'</div>';
		}
		// If the search is a general or soundex search then possibly display checkboxes for the gedcoms
		if ($controller->action == "general" || $controller->action == "soundex") {
			// If more than one GEDCOM, switching is allowed AND DB mode is set, let the user select
			if ((count(Tree::getAll()) > 1) && Site::getPreference('ALLOW_CHANGE_GEDCOM')) {
				// More Than 3 Gedcom Filess enable elect all & select none buttons
				if (count(Tree::getAll()) > 3) {
					echo '<div class="label">&nbsp;</div>
						<div class="value">
						<input type="button" value="', /* I18N: select all (of the family trees) */ I18N::translate('select all'), '" onclick="jQuery(\'#search_trees :checkbox\').each(function(){jQuery(this).attr(\'checked\', true);});return false;">
							<input type="button" value="', /* I18N: select none (of the family trees) */ I18N::translate('select none'), '" onclick="jQuery(\'#search_trees :checkbox\').each(function(){jQuery(this).attr(\'checked\', false);});return false;">';
							// More Than 10 Gedcom Files enable invert selection button
							if (count(Tree::getAll()) > 10) {
								echo '<input type="button" value="', I18N::translate('invert selection'), '" onclick="jQuery(\'#search_trees :checkbox\').each(function(){jQuery(this).attr(\'checked\', !jQuery(this).attr(\'checked\'));});return false;">';
							}
						echo '</div>';
				}
				echo '<div class="label">', I18N::translate('Family trees'), '</div>
				<div id="search_trees" class="value">';
					//-- sorting menu by gedcom filename
					foreach (Tree::getAll() as $tree) {
						$str = str_replace(array(".", "-", " "), array("_", "_", "_"), $tree->getName());
						$controller->inputFieldNames[] = "$str";
						echo '<p><input type="checkbox" ';
						echo isset ($_REQUEST[$str]) ? 'checked' : '';
						echo ' value="yes" id="checkbox_', $tree->getTreeId(), '" name="', $str, '"><label for="checkbox_', $tree->getTreeId(), '">', $tree->getTitleHtml(), '</label></p>', "\n";
					}
				echo '</div>';
			}
		}

		// Links to Other Search Options
			echo '<div class="label">', I18N::translate('Other searches'), '</div>
				<div class="value">';
				if ($controller->action == "general") {
					echo '<a href="?action=soundex">', I18N::translate('Phonetic search'), '</a> | <a href="search_advanced.php">', I18N::translate('Advanced search'), '</a>';
					if (WT_USER_CAN_EDIT) {
						echo ' | <a href="?action=replace">', I18N::translate('Search and replace'), '</a>';
					}
				} elseif ($controller->action == "replace") {
					echo '<a href="?action=general">', I18N::translate('General search'), '</a> | ',
						'<a href="?action=soundex">', I18N::translate('Phonetic search'), '</a>',
						' | <a href="search_advanced.php">', I18N::translate('Advanced search'), '</a>';
				} elseif ($controller->action == "soundex") {
					echo '<a href="?action=general">', I18N::translate('General search'), '</a>',
						' | <a href="search_advanced.php">', I18N::translate('Advanced search'), '</a>';
					if (WT_USER_CAN_EDIT) {
						echo ' | <a href="?action=replace">', I18N::translate('Search and replace'), '</a>';
					}
				}
			echo '</div>
		</div>'; // Close div id="search_page-table"

		//Search buttons
		echo '<div id="search_submit">';
			if ($controller->action == "general") {
				echo '<input tabindex="2" type="submit" value="', I18N::translate('Search'), '">';
			} elseif ($controller->action == "replace") {
				echo '<input tabindex="2" type="submit" value="', I18N::translate('Search'), '">';
			} elseif ($controller->action == "soundex") {
				echo '<input tabindex="7" type="submit" value="', I18N::translate('Search'), '">';
			}
		echo '</div>'; // close div id="search_submit"
	echo '</form>';
	$controller->printResults();
echo '</div>'; // close div id "search-page"
