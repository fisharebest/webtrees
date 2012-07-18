<?php
// View for the family book chart
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
//
// $Id$

define('WT_SCRIPT_NAME', 'familybook.php');
require './includes/session.php';

$controller=new WT_Controller_Familybook();
$controller
	->pageHeader()
	->addExternalJavascript(WT_STATIC_URL.'js/autocomplete.js')
	->setupJavascript()
	->addInlineJavascript('sizeLines();');

if (WT_USE_LIGHTBOX) {
	$album = new lightbox_WT_Module();
	$album->getPreLoadContent();
}

?>

<table>
	<tr>
		<td class="tdtop">
			<h2><?php echo $controller->getPageTitle(); ?></h2>
		</td>
		<td width="50px">&nbsp;</td>
		<td>
			<form method="get" name="people" action="?">
				<table>
					<tr>
						<td class="descriptionbox">
							<?php echo WT_I18N::translate('Individual'); ?>
						</td>
						<td class="optionbox">
							<input class="pedigree_form" type="text" name="rootid" id="rootid" size="3" value="<?php echo $controller->rootid; ?>">
							<?php echo print_findindi_link('rootid'); ?>
						</td>
						<td class="descriptionbox">
							<?php echo WT_I18N::translate('Show Details'); ?>
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
							<?php echo WT_I18N::translate('Descent Steps'), help_link('fambook_descent'); ?>
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
		</td>
	</tr>
</table>

<?php

if ($controller->error_message) {
	echo '<p class="ui-state-error">', $controller->error_message, '</p>';
	exit;
}

?>

<div id="familybook_chart" style="width:98%; z-index:1;">
<?php $controller->print_family_book($controller->root, $controller->descent); ?>
</div>
