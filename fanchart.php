<?php
// View for the fan chart.
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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

define('WT_SCRIPT_NAME', 'fanchart.php');
require './includes/session.php';

$controller=new WT_Controller_Fanchart();
$controller
	->pageHeader()
	->addInlineJavaScript('var pastefield; function paste_id(value) { pastefield.value=value; }'); // For the "find indi" link

if ($ENABLE_AUTOCOMPLETE) {
	require WT_ROOT.'js/autocomplete.js.htm';
}

?>
<table class="list_table">
	<tr>
		<td>
			<h2><?php echo $controller->getPageTitle(); ?></h2>
		</td>
		<td>
			<form name="people" method="get" action="#">
				<table class="list_table">
					<tr>
						<td class="descriptionbox">
							<?php echo WT_I18N::translate('Individual'); ?>
						</td>
						<td class="optionbox">
							<input class="pedigree_form" type="text" name="rootid" id="rootid" size="3" value="<?php echo $controller->rootid; ?>">
							<?php print_findindi_link('rootid', ''); ?>
						</td>
						<td class="descriptionbox">
							<?php echo WT_I18N::translate('Layout'); ?>
						</td>
						<td class="optionbox">
							<select name="fan_style">
								<option value="2" <?php echo $controller->fan_style==2 ? 'selected="selected"' : ''; ?>>
									<?php echo /* I18N: layout option for the fan chart */ WT_I18N::translate('half circle'); ?>
								</option>
								<option value="3" <?php echo $controller->fan_style==3 ? 'selected="selected"' : ''; ?>>
									<?php echo /* I18N: layout option for the fan chart */ WT_I18N::translate('three-quarter circle'); ?>
								</option>
								<option value="4" <?php echo $controller->fan_style==4 ? 'selected="selected"' : ''; ?>>
									<?php echo /* I18N: layout option for the fan chart */ WT_I18N::translate('full circle'); ?>
								</option>
							</select>
						</td>
						<td rowspan="2" class="topbottombar vmiddle">
							<input type="submit" value="<?php echo WT_I18N::translate('View'); ?>">
						</td>
					</tr>
					<tr>
						<td class="descriptionbox">
							<?php echo WT_I18N::translate('Generations'); ?>
						</td>
						<td class="optionbox">
							<select name="generations">
								<option value="2" <?php echo $controller->generations==2 ? 'selected="selected"' : ''; ?>>
									<?php echo WT_I18N::number(2); ?>
								</option>
								<option value="3" <?php echo $controller->generations==3 ? 'selected="selected"' : ''; ?>>
									<?php echo WT_I18N::number(3); ?>
								</option>
								<option value="4" <?php echo $controller->generations==4 ? 'selected="selected"' : ''; ?>>
									<?php echo WT_I18N::number(4); ?>
								</option>
								<option value="5" <?php echo $controller->generations==5 ? 'selected="selected"' : ''; ?>>
									<?php echo WT_I18N::number(5); ?>
								</option>
								<option value="6" <?php echo $controller->generations==6 ? 'selected="selected"' : ''; ?>>
									<?php echo WT_I18N::number(6); ?>
								</option>
								<option value="7" <?php echo $controller->generations==7 ? 'selected="selected"' : ''; ?>>
									<?php echo WT_I18N::number(7); ?>
								</option>
								<option value="8" <?php echo $controller->generations==8 ? 'selected="selected"' : ''; ?>>
									<?php echo WT_I18N::number(8); ?>
								</option>
								<option value="9" <?php echo $controller->generations==9 ? 'selected="selected"' : ''; ?>>
									<?php echo WT_I18N::number(9); ?>
								</option>
							</select>
						</td>
						<td class="descriptionbox">
							<?php echo WT_I18N::translate('Width'), help_link('fan_width'); ?>
						</td>
						<td class="optionbox">
							<input type="text" size="3" name="fan_width" value="<?php echo $controller->fan_width; ?>"> %
						</td>
					</tr>
				</table>
			</form>
		</tr>
</table>

<?php

if ($controller->error_message) {
	echo '<p class="ui-state-error">', $controller->error_message, '</p>';
	exit;
}

echo $controller->chart_html;
