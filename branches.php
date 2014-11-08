<?php
// List branches by surname
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

define('WT_SCRIPT_NAME', 'branches.php');
require './includes/session.php';


$controller = new WT_Controller_Branches();
$controller
	->pageHeader()
	->addExternalJavascript(WT_STATIC_URL . 'js/autocomplete.js')
	->addInlineJavascript('autocomplete();');

?>
<div id="branches-page">
	<h2><?php echo $controller->getPageTitle(); ?></h2>
	<form name="surnlist" id="surnlist" action="branches.php">
		<table class="facts_table width50">
			<tr>
				<td class="descriptionbox">
					<?php echo WT_Gedcom_Tag::getLabel('SURN'); ?>
				</td>
				<td class="optionbox">
					<input data-autocomplete-type="SURN" type="text" name="surname" id="SURN" value="<?php echo WT_Filter::escapeHtml($controller->getSurname()); ?>" dir="auto">
					<input type="hidden" name="ged" id="ged" value="<?php echo WT_Filter::escapeHtml(WT_GEDCOM); ?>">
					<input type="submit" value="<?php echo WT_I18N::translate('View'); ?>">
					<p>
						<?php echo WT_I18N::translate('Phonetic search'); ?>
					</p>
					<p>
						<input type="checkbox" name="soundex_std" id="soundex_std" value="1" <?php if ($controller->getSoundexStd()) echo ' checked="checked"'; ?>>
						<label for="soundex_std"><?php echo WT_I18N::translate('Russell'); ?></label>
						<input type="checkbox" name="soundex_dm" id="soundex_dm" value="1" <?php if ($controller->getSoundexDm()) echo ' checked="checked"'; ?>>
						<label for="soundex_dm"><?php echo WT_I18N::translate('Daitch-Mokotoff'); ?></label>
					</p>
				</td>
			</tr>
		</table>
	</form>
	<ol>
		<?php echo $controller->getPatriarchsHtml(); ?>
	</ol>
</div>
