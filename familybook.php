<?php
// View for the family book chart
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.
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

define('WT_SCRIPT_NAME', 'familybook.php');
require './includes/session.php';

$controller = new WT_Controller_Familybook();
$controller
	->pageHeader()
	->addExternalJavascript(WT_STATIC_URL . 'js/autocomplete.js')
	->addInlineJavascript('autocomplete();');

?>
<div id="familybook-page">
	<h2><?php echo $controller->getPageTitle(); ?></h2>
	<form method="get" name="people" action="?">
		<input type="hidden" name="ged" value="<?php echo WT_Filter::escapeHtml(WT_GEDCOM); ?>">
		<table class="list_table">
			<tr>
				<td class="descriptionbox">
					<?php echo WT_I18N::translate('Individual'); ?>
				</td>
				<td class="optionbox">
					<input class="pedigree_form" data-autocomplete-type="INDI" type="text" name="rootid" id="rootid" size="3" value="<?php echo $controller->root->getXref(); ?>">
					<?php echo print_findindi_link('rootid'); ?>
				</td>
				<td class="descriptionbox">
					<?php echo WT_I18N::translate('Show details'); ?>
				</td>
				<td class="optionbox">
					<input type="hidden" name="show_full" value="<?php echo $controller->show_full; ?>">
					<input type="checkbox" value="<?php	if ($controller->show_full) echo "1\" checked=\"checked\" onclick=\"document.people.show_full.value='0';"; else echo "0\" onclick=\"document.people.show_full.value='1';"; ?>">
				</td>
				<td rowspan="3" class="topbottombar vmiddle">
					<input type="submit" value="<?php echo /* I18N: Submit button, on a form */ WT_I18N::translate('View'); ?>">
				</td>
			</tr>
			<tr>
				<td class="descriptionbox">
					<?php echo WT_I18N::translate('Generations'); ?>
				</td>
				<td class="optionbox">
					<select name="generations">
						<?php
						for ($i=2; $i<=$MAX_DESCENDANCY_GENERATIONS; $i++) {
							echo "<option value=\"".$i."\"" ;
							if ($i == $controller->generations) echo " selected=\"selected\"";
							echo ">".WT_I18N::number($i)."</option>";
						}
						?>
					</select>
				</td>
				<td class="descriptionbox">
					<?php echo WT_I18N::translate('Show spouses'), help_link('show_spouse'); ?>
				</td>
				<td class="optionbox">
					<input type="checkbox" value="1" name="show_spouse" <?php if ($controller->show_spouse) echo " checked=\"checked\""; ?>>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox">
					<?php echo WT_I18N::translate('Descent steps'), help_link('fambook_descent'); ?>
				</td>
				<td class="optionbox">
					<input type="text" size="3" name="descent" value="<?php echo $controller->descent; ?>">
				</td>
				<td class="descriptionbox">
					<?php echo WT_I18N::translate('Box width'); ?>
				</td>
				<td class="optionbox">
					<input type="text" size="3" name="box_width" value="<?php echo $controller->box_width; ?>"> %
				</td>
			</tr>
		</table>
	</form>
<div id="familybook_chart" style="z-index:1;">
<?php
if ($controller->root) {
	$controller->printFamilyBook($controller->root, $controller->descent);
}
?>
</div>
</div> <!-- close #familybook-page -->
