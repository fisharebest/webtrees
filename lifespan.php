<?php
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
namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Controller\LifespanController;
use Fisharebest\Webtrees\Functions\FunctionsPrint;

define('WT_SCRIPT_NAME', 'lifespan.php');
require './includes/session.php';

$controller = new LifespanController;
$controller
	->pageHeader()
	->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL);

?>
<div id="lifespan-page">
	<h2><?php echo I18N::translate('Lifespan chart');?></h2>

	<form class="descriptionbox lifespan-form noprint" method="post">
		<fieldset>
			<legend><?php echo I18N::translate('Select place or add individual(s)');?></legend>
			<label class="descriptionbox" for="place">
				<?php echo GedcomTag::getLabel('PLAC'); ?>
			</label>
			<span class="optionbox">
				<input id="place" data-autocomplete-type="PLAC" type="text" name="place" size="15"
					value="<?php echo Filter::escapeHtml($controller->place); ?>">
			</span>
			<div>
				<label class="descriptionbox" for="newpid">
					<?php echo I18N::translate('Add another individual to the chart'); ?>
				</label>
				<span class="optionbox">
					<input id="newpid" class="pedigree_form" data-autocomplete-type="INDI" type="text" size="5"
					       name="newpid"><?php FunctionsPrint::printFindIndividualLink('newpid'); ?>
				</span>
			</div>
			<div>
				<label class="descriptionbox" for="addFamily">
					<?php echo I18N::translate('Include the individual’s immediate family?'); ?>
				</label>
				<span class="optionbox">
					<input id="addFamily" type="checkbox" value="yes" name="addFamily" <?php echo $controller->showDetails;?>>
				</span>
			</div>
		</fieldset>
		<fieldset>
			<legend><?php echo I18N::translate('Filter selection by date');?></legend>
			<div>
				<label class="descriptionbox" for="beginYear">
					<?php echo I18N::translate('Begin year'); ?>
				</label>
				<span class="optionbox">
					<input id="beginYear" type="text" name="beginYear" size="5" value="<?php echo $controller->beginYear; ?>">
				</span>
			</div>
			<div>
				<label class="descriptionbox" for="endYear">
					<?php echo I18N::translate('End year'); ?>
				</label>
				<span class="optionbox">
					<input id="endYear" type="text" name="endYear" size="5"
						value="<?php echo $controller->endYear; ?>">
				</span>
			</div>
			<div>
				<label class="descriptionbox" for="strictDate">
					<?php echo I18N::translate('Match calendar'); ?>
				</label>
				<span class="optionbox">
					<input id="strictDate" type="checkbox" value="yes" name="strictDate">
				</span>
			</div>
		</fieldset>
		<fieldset>
			<legend><?php echo I18N::translate('Controls');?></legend>
			<div class="controls">
				<div>
					<input type="submit" value="<?php echo I18N::translate('Show'); ?>">
				</div>
				<div>
					<input id="clear" type="hidden" name="clear" value=0>
					<input type="reset" value="<?php echo I18N::translate('Clear chart'); ?>">
				</div>
				<div>
					<label class="descriptionbox" for="calendar">
						<?php echo I18N::translate('Calendar'); ?>
					</label>
					<span>
						<select id="calendar" name="calendar">
							<?php echo $controller->getCalendarOptionList();?>
						</select>
					</span>
				</div>
			</div>
		</fieldset>
	</form>

	<div id="lifespan-chart">
		<h4><?php echo $controller->subtitle;?></h4>
		<div id="lifespan-scale">
			<?php $controller->printTimeline(); ?>
		</div>
		<div id="lifespan-people">
			<?php $maxY = $controller->fillTimeline(); ?>
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
