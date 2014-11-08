<?php
// Compact pedigree tree
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
					<input class="pedigree_form" data-autocomplete-type="INDI" type="text" name="rootid" id="rootid" size="3" value="<?php echo $controller->root->getXref(); ?>">
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
				<?php echo $controller->sosaIndividual(16); ?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<?php echo $controller->sosaIndividual(18); ?>
				<td>&nbsp;</td>
				<?php echo $controller->sosaIndividual(24); ?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<?php echo $controller->sosaIndividual(26); ?>
			</tr>
			<tr>
				<td><?php echo $controller->sosaArrow(16, 'up'); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosaArrow(18, 'up'); ?></td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosaArrow(24, 'up'); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosaArrow(26, 'up'); ?></td>
			</tr>
			<tr>
				<?php echo $controller->sosaIndividual(8); ?>
				<td><?php echo $controller->sosaArrow(8, 'left'); ?></td>
				<?php echo $controller->sosaIndividual(4); ?>
				<td><?php echo $controller->sosaArrow(9, 'right'); ?></td>
				<?php echo $controller->sosaIndividual(9); ?>
				<td>&nbsp;</td>
				<?php echo $controller->sosaIndividual(12); ?>
				<td><?php echo $controller->sosaArrow(12, 'left'); ?></td>
				<?php echo $controller->sosaIndividual(6); ?>
				<td><?php  echo $controller->sosaArrow(13, 'right'); ?></td>
				<?php echo $controller->sosaIndividual(13); ?>
			</tr>
			<tr>
				<td><?php echo $controller->sosaArrow(17, 'down'); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosaArrow(19, 'down'); ?></td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosaArrow(25, 'down'); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosaArrow(27, 'down'); ?></td>
			</tr>
			<tr>
				<?php echo $controller->sosaIndividual(17); ?>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosaArrow(4, 'up'); ?></td>
				<td>&nbsp;</td>
				<?php echo $controller->sosaIndividual(19); ?>
				<td>&nbsp;</td>
				<?php echo $controller->sosaIndividual(25); ?>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosaArrow(6, 'up'); ?></td>
				<td>&nbsp;</td>
				<?php echo $controller->sosaIndividual(27); ?>
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
				<?php echo $controller->sosaIndividual(2); ?>
				<td>&nbsp;</td>
				<td colspan="3">
					<table width="100%">
						<tr>
							<td width='25%'><?php echo $controller->sosaArrow(2, 'left'); ?></td>
							<?php echo $controller->sosaIndividual(1); ?>
							<td width='25%'><?php echo $controller->sosaArrow(3, 'right'); ?></td>
						</tr>
					</table>
				</td>
				<td>&nbsp;</td>
				<?php echo $controller->sosaIndividual(3); ?>
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
				<?php echo $controller->sosaIndividual(20); ?>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosaArrow(5, 'down'); ?></td>
				<td>&nbsp;</td>
				<?php echo $controller->sosaIndividual(22); ?>
				<td>&nbsp;</td>
				<?php echo $controller->sosaIndividual(28); ?>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosaArrow(7, 'down'); ?></td>
				<td>&nbsp;</td>
				<?php echo $controller->sosaIndividual(30); ?>
			</tr>
			<tr>
				<td><?php echo $controller->sosaArrow(20, 'up'); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosaArrow(22, 'up'); ?></td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosaArrow(28, 'up'); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosaArrow(30, 'up'); ?></td>
			</tr>
			<tr>
				<?php echo $controller->sosaIndividual(10); ?>
				<td><?php echo $controller->sosaArrow(10, 'left'); ?></td>
				<?php echo $controller->sosaIndividual(5); ?>
				<td><?php echo $controller->sosaArrow(11, 'right'); ?></td>
				<?php echo $controller->sosaIndividual(11); ?>
				<td>&nbsp;</td>
				<?php echo $controller->sosaIndividual(14); ?>
				<td><?php echo $controller->sosaArrow(14, 'left'); ?></td>
				<?php echo $controller->sosaIndividual(7); ?>
				<td><?php echo $controller->sosaArrow(15, 'right'); ?></td>
				<?php echo $controller->sosaIndividual(15); ?>
			</tr>
			<tr>
				<td><?php echo $controller->sosaArrow(21, 'down'); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosaArrow(23, 'down'); ?></td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosaArrow(29, 'down'); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><?php echo $controller->sosaArrow(31, 'down'); ?></td>
			</tr>
			<tr>
				<?php echo $controller->sosaIndividual(21); ?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<?php echo $controller->sosaIndividual(23); ?>
				<td>&nbsp;</td>
				<?php echo $controller->sosaIndividual(29); ?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<?php echo $controller->sosaIndividual(31); ?>
			</tr>
		</table>
	</div>
</div>
