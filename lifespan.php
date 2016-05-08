<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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
namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Controller\LifespanController;
use Fisharebest\Webtrees\Functions\FunctionsPrint;

define('WT_SCRIPT_NAME', 'lifespan.php');
require './includes/session.php';

global $WT_TREE;

$controller = new LifespanController;
$controller
	->restrictAccess(Module::isActiveChart($WT_TREE, 'lifespans_chart'))
	->pageHeader()
	->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL);

?>
<div id="lifespan-page">
	<h2><?php echo I18N::translate('Lifespans') ?></h2>

	<form>
		<table class="list_table">
			<tbody>
				<tr>
					<th class="descriptionbox" colspan="4">
						<?php echo I18N::translate('Select individuals by place or date') ?>
					</th>
					<th class="descriptionbox" colspan="2">
						<?php echo I18N::translate('Add individuals') ?>
					</th>
				</tr>
				<tr>
					<td class="optionbox">
						<label for="place">
							<?php echo GedcomTag::getLabel('PLAC') ?>
						</label>
					</td>
					<td class="optionbox" colspan="3">
						<input id="place" data-autocomplete-type="PLAC" type="text" size="30" name="place">
					</td>
					<td class="optionbox">
						<label for="newpid">
							<?php echo I18N::translate('Individual') ?>
						</label>
					</td>
					<td class="optionbox">
						<input id="newpid" class="pedigree_form" data-autocomplete-type="INDI" type="text" size="5" name="newpid"><?php echo FunctionsPrint::printFindIndividualLink('newpid') ?>

					</td>
				</tr>
				<tr>
					<td class="optionbox">
						<label for="beginYear">
							<?php echo /* I18N: The earliest year in a range */ I18N::translate('Start year') ?>
						</label>
					</td>
					<td class="optionbox">
						<input id="beginYear" type="text" name="beginYear" size="5">
					</td>
					<td class="optionbox">
						<label for="endYear">
							<?php echo /* I18N: The latest year in a range */ I18N::translate('End year') ?>
						</label>
					</td>
					<td class="optionbox">
						<input id="endYear" type="text" name="endYear" size="5">
					</td>
					<td class="optionbox" colspan="2">
						<label for="addFamily">
							<input id="addFamily" type="checkbox" value="yes" name="addFamily">
							<?php echo /* I18N: Label for a configuration option */ I18N::translate('Include the individual’s immediate family') ?>
						</label>
					</td>
				</tr>
				<tr>
					<td class="optionbox">
						<label for="calendar">
							<?php echo I18N::translate('Calendar') ?>
						</label>
					</td>
					<td class="optionbox">
						<select id="calendar" name="calendar">
							<?php echo $controller->getCalendarOptionList() ?>
						</select>
					</td>
					<td class="optionbox" colspan="2">
						<label for="strictDate">
							<input id="strictDate" type="checkbox" value="yes" name="strictDate">
							<?php echo I18N::translate('Match calendar') ?>
						</label>
					</td>
					<th class="descriptionbox" colspan="2">
						<input id="clear" type="hidden" name="clear" value=0>
						<input type="reset" value="<?php echo /* I18N: A button label */ I18N::translate('reset') ?>">
						<input type="submit" value="<?php echo /* I18N: A button label */ I18N::translate('show') ?>">
					</th>
				</tr>
			</tbody>
		</table>
	</form>

	<div id="lifespan-chart">
		<h4><?php echo $controller->subtitle ?></h4>
		<div id="lifespan-scale">
			<?php $controller->printTimeline() ?>
		</div>
		<div id="lifespan-people">
			<?php $maxY = $controller->fillTimeline() ?>
		</div>
	</div>
</div>
<?php
$controller
	->addInlineJavascript("
		autocomplete();
		var scale = jQuery('#lifespan-scale'),
			barHeight = jQuery('#lifespan-people').children().first().outerHeight();
		jQuery('#lifespan-chart')
			.width(scale.width())
			.height(Math.ceil(jQuery('h4').outerHeight() + scale.height() + barHeight + $maxY));
		jQuery('form').on('reset', function() {
			jQuery('#clear').val(1);
			jQuery(this).submit();
		});
	");
