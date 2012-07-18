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
require WT_ROOT.'includes/functions/functions_edit.php';

$controller=new WT_Controller_Fanchart();
$controller
	->pageHeader()
	->addExternalJavascript(WT_STATIC_URL.'js/autocomplete.js')
	->addInlineJavascript('var pastefield; function paste_id(value) { pastefield.value=value; }'); // For the 'find indi' link

?>
<table class="list_table">
	<tr>
		<td>
			<h2><?php echo $controller->getPageTitle(); ?></h2>
		</td>
		<td width="50px">&nbsp;</td>
		<td>
			<form name="people" method="get" action="#">
				<table class="list_table">
					<tr>
						<td class="descriptionbox">
							<?php echo WT_I18N::translate('Individual'); ?>
						</td>
						<td class="optionbox">
							<input class="pedigree_form" type="text" name="rootid" id="rootid" size="3" value="<?php echo $controller->rootid; ?>">
							<?php echo print_findindi_link('rootid'); ?>
						</td>
						<td class="descriptionbox">
							<?php echo WT_I18N::translate('Layout'); ?>
						</td>
						<td class="optionbox">
							<?php echo select_edit_control('fan_style', $controller->getFanStyles(), null, $controller->fan_style); ?>
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
							<?php echo edit_field_integers('generations', $controller->generations, 2, 9); ?>
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
