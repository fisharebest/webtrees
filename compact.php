<?php
// Compact pedigree tree
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team.  All rights reserved.
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

define('WT_SCRIPT_NAME', 'compact.php');
require './includes/session.php';

$controller = new WT_Controller_Compact();
$controller
	->pageHeader()
	->addExternalJavascript(WT_STATIC_URL . 'js/autocomplete.js')
	->addInlineJavascript('autocomplete();');

?>
<div id="compact-page">
	<h2><?php echo $controller->getPageTitle(); ?></h2>
	<form name="people" id="people" method="get" action="?">
		<input type="hidden" name="ged" value="<?php echo WT_Filter::escapeHtml(WT_GEDCOM); ?>">
		<table class="list_table">
			<tr>
				<td class="descriptionbox">
					<?php echo WT_I18N::translate('Individual'); ?>
				</td>
				<td class="optionbox vmiddle">
					<input class="pedigree_form" data-autocomplete-type="INDI" type="text" name="rootid" id="rootid" size="3" value="<?php echo $controller->rootid; ?>">
					<?php echo print_findindi_link('rootid'); ?>
				</td>
					<td <?php echo $SHOW_HIGHLIGHT_IMAGES ? 'rowspan="2"' : ''; ?> class="facts_label03">
						<input type="submit" value="<?php echo WT_I18N::translate('View'); ?>">
					</td>
				</tr>
				<?php if ($SHOW_HIGHLIGHT_IMAGES) { ?>
				<tr>
					<td class="descriptionbox">
						<?php echo WT_I18N::translate('Show highlight images in individual boxes'); ?>
					</td>
					<td class="optionbox">
						<input name="show_thumbs" type="checkbox" value="1" <?php echo $controller->show_thumbs ? 'checked="checked"' : ''; ?>>
					</td>
				</tr>
				<?php } ?>
			</table>
	</form>
	<div id="compact_chart">
		<table width="100%" style="text-align:center;">
			<tr>
				<?php echo $controller->sosa_person(16); ?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<?php echo $controller->sosa_person(18); ?>
				<td>&nbsp;</td>
				<?php echo $controller->sosa_person(24); ?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<?php echo $controller->sosa_person(26); ?>
			</tr>
			<tr>
				<td><?php echo $controller->sosa_arrow(16, 'up'); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosa_arrow(18, 'up'); ?></td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosa_arrow(24, 'up'); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosa_arrow(26, 'up'); ?></td>
			</tr>
			<tr>
				<?php echo $controller->sosa_person(8); ?>
				<td><?php echo $controller->sosa_arrow(8, 'left'); ?></td>
				<?php echo $controller->sosa_person(4); ?>
				<td><?php echo $controller->sosa_arrow(9, 'right'); ?></td>
				<?php echo $controller->sosa_person(9); ?>
				<td>&nbsp;</td>
				<?php echo $controller->sosa_person(12); ?>
				<td><?php echo $controller->sosa_arrow(12, 'left'); ?></td>
				<?php echo $controller->sosa_person(6); ?>
				<td><?php  echo $controller->sosa_arrow(13, 'right'); ?></td>
				<?php echo $controller->sosa_person(13); ?>
			</tr>
			<tr>
				<td><?php echo $controller->sosa_arrow(17, 'down'); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosa_arrow(19, 'down'); ?></td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosa_arrow(25, 'down'); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosa_arrow(27, 'down'); ?></td>
			</tr>
			<tr>
				<?php echo $controller->sosa_person(17); ?>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosa_arrow(4, 'up'); ?></td>
				<td>&nbsp;</td>
				<?php echo $controller->sosa_person(19); ?>
				<td>&nbsp;</td>
				<?php echo $controller->sosa_person(25); ?>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosa_arrow(6, 'up'); ?></td>
				<td>&nbsp;</td>
				<?php echo $controller->sosa_person(27); ?>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<?php echo $controller->sosa_person(2); ?>
				<td>&nbsp;</td>
				<td colspan="3">
					<table width="100%">
						<tr>
							<td width='25%'><?php echo $controller->sosa_arrow(2, 'left'); ?></td>
							<?php echo $controller->sosa_person(1); ?>
							<td width='25%'><?php echo $controller->sosa_arrow(3, 'right'); ?></td>
						</tr>
					</table>
				</td>
				<td>&nbsp;</td>
				<?php echo $controller->sosa_person(3); ?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<?php echo $controller->sosa_person(20); ?>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosa_arrow(5, 'down'); ?></td>
				<td>&nbsp;</td>
				<?php echo $controller->sosa_person(22); ?>
				<td>&nbsp;</td>
				<?php echo $controller->sosa_person(28); ?>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosa_arrow(7, 'down'); ?></td>
				<td>&nbsp;</td>
				<?php echo $controller->sosa_person(30); ?>
			</tr>
			<tr>
				<td><?php echo $controller->sosa_arrow(20, 'up'); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosa_arrow(22, 'up'); ?></td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosa_arrow(28, 'up'); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosa_arrow(30, 'up'); ?></td>
			</tr>
			<tr>
				<?php echo $controller->sosa_person(10); ?>
				<td><?php echo $controller->sosa_arrow(10, 'left'); ?></td>
				<?php echo $controller->sosa_person(5); ?>
				<td><?php echo $controller->sosa_arrow(11, 'right'); ?></td>
				<?php echo $controller->sosa_person(11); ?>
				<td>&nbsp;</td>
				<?php echo $controller->sosa_person(14); ?>
				<td><?php echo $controller->sosa_arrow(14, 'left'); ?></td>
				<?php echo $controller->sosa_person(7); ?>
				<td><?php echo $controller->sosa_arrow(15, 'right'); ?></td>
				<?php echo $controller->sosa_person(15); ?>
			</tr>
			<tr>
				<td><?php echo $controller->sosa_arrow(21, 'down'); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosa_arrow(23, 'down'); ?></td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosa_arrow(29, 'down'); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosa_arrow(31, 'down'); ?></td>
			</tr>
			<tr>
				<?php echo $controller->sosa_person(21); ?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<?php echo $controller->sosa_person(23); ?>
				<td>&nbsp;</td>
				<?php echo $controller->sosa_person(29); ?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<?php echo $controller->sosa_person(31); ?>
			</tr>
		</table>
	</div>
</div>
