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

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

define('WT_SCRIPT_NAME', 'lifespan.php');
require './includes/session.php';

$controller = new LifespanController;
$controller
	->pageHeader()
	->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL);

$people = count($controller->people);

?>
<div id='lifespan-page'>
<h2><?php echo I18N::translate('Lifespans');?></h2>

	<div id='lifespan-forms' class='noprint'>
		<form class="descriptionbox" name='people' action='?'>
			<div>
				<label class='descriptionbox' for="newpid">
					<?php echo I18N::translate('Add another individual to the chart'); ?>
				</label>
				<span class='optionbox'>
					<input id='newpid' class='pedigree_form' data-autocomplete-type='INDI' type='text' size='5'
				       name='newpid'><?php print_findindi_link('newpid'); ?>
				</span>
				<span class='descriptionbox'>
					<input type='submit' value='<?php echo I18N::translate('Add'); ?>'>
				</span>
			</div>
			<div>
				<label class='descriptionbox' for="addFamily">
					<?php echo I18N::translate('Include the individual’s immediate family?'); ?>
				</label>
				<span class='optionbox'>
					<input id="addFamily" type='checkbox' checked value='yes' name='addFamily'>
				</span>
			</div>
			<div>
				<span class='descriptionbox'>
					<?php echo I18N::translate('Individuals'); ?>
				</span>
				<span class='optionbox total'>
					<?php echo $people; ?>
				</span>
			</div>
		</form>
		<form class="descriptionbox" name='buttons' action='lifespan.php' method='get'>
			<div>
				<label class='descriptionbox' for="beginYear">
					<?php echo I18N::translate('Begin year'); ?>
				</label>
				<span class='optionbox'>
					<input id="beginYear" type='text' name='beginYear' size='5'
				        value='<?php echo $controller->beginYear == 0 ? '' : $controller->beginYear; ?>'>
				</span>
				<span class='descriptionbox'>
					<input type='submit' value='<?php echo I18N::translate('Search'); ?>'>
				</span>
			</div>
			<div>
				<label class='descriptionbox' for="endYear">
					<?php echo I18N::translate('End year'); ?>
				</label>
				<span class='optionbox'>
					<input id="endYear" type='text' name='endYear' size='5'
					       value='<?php echo $controller->endYear == 0 ? '' : $controller->endYear; ?>'>
				</span>
				<span class='descriptionbox'>
					<input type='button' value='<?php echo I18N::translate('Clear chart'); ?>'
					       onclick="window.location='lifespan.php?clear=1';">
				</span>
			</div>
			<div>
				<label class='descriptionbox' for="place">
					<?php echo GedcomTag::getLabel('PLAC'); ?>
				</label>
				<span class='optionbox'>
					<input id="place" data-autocomplete-type='PLAC' type='text' name='place' size='15'
					       value='<?php echo Filter::escapeHtml($controller->place); ?>'>
				</span>
			</div>
		</form>
	</div>
	<div id='lifespan-chart'>
		<div id='lifespan-scale'>
			<?php $controller->printTimeline(); ?>
		</div>
		<div id='lifespan-people'>
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
			.css('width', scale.width())
			.css('height',  Math.ceil(scale.height() + barHeight + $maxY));
		jQuery('form div').height(jQuery('form[name=\'people\'] div').first().height());
	");
