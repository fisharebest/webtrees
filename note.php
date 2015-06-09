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

use Fisharebest\Webtrees\Controller\NoteController;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Functions\FunctionsPrintFacts;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;
use Fisharebest\Webtrees\Module\CensusAssistantModule;

define('WT_SCRIPT_NAME', 'note.php');
require './includes/session.php';

$controller = new NoteController;

if ($controller->record && $controller->record->canShow()) {
	$controller->pageHeader();
	if ($controller->record->isPendingDeletion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is “accept”, %2$s is “reject”.  These are links. */ I18N::translate(
					'This note has been deleted.  You should review the deletion and then %1$s or %2$s it.',
					'<a href="#" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'accept') . '</a>',
					'<a href="#" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'reject') . '</a>'
				),
				' ', FunctionsPrint::helpLink('pending_changes'),
				'</p>';
		} elseif (Auth::isEditor($controller->record->getTree())) {
			echo
				'<p class="ui-state-highlight">',
				I18N::translate('This note has been deleted.  The deletion will need to be reviewed by a moderator.'),
				' ', FunctionsPrint::helpLink('pending_changes'),
				'</p>';
		}
	} elseif ($controller->record->isPendingAddtion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is “accept”, %2$s is “reject”.  These are links. */ I18N::translate(
					'This note has been edited.  You should review the changes and then %1$s or %2$s them.',
					'<a href="#" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'accept') . '</a>',
					'<a href="#" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'reject') . '</a>'
				),
				' ', FunctionsPrint::helpLink('pending_changes'),
				'</p>';
		} elseif (Auth::isEditor($controller->record->getTree())) {
			echo
				'<p class="ui-state-highlight">',
				I18N::translate('This note has been edited.  The changes need to be reviewed by a moderator.'),
				' ', FunctionsPrint::helpLink('pending_changes'),
				'</p>';
		}
	}
} else {
	http_response_code(404);
	$controller->pageHeader();
	echo '<p class="ui-state-error">', I18N::translate('This note does not exist or you do not have permission to view it.'), '</p>';

	return;
}

$controller->addInlineJavascript('
	jQuery("#note-tabs")
		.tabs({
			create: function(e, ui){
				jQuery(e.target).css("visibility", "visible");  // prevent FOUC
			}
		});
');

$linked_indi = $controller->record->linkedIndividuals('NOTE');
$linked_fam  = $controller->record->linkedFamilies('NOTE');
$linked_obje = $controller->record->linkedMedia('NOTE');
$linked_sour = $controller->record->linkedSources('NOTE');

$facts = array();
foreach ($controller->record->getFacts() as $fact) {
	if ($fact->getTag() != 'CONT') {
		$facts[] = $fact;
	}
}

// Legacy formatting, created by the census assistant
if (Module::getModuleByName('GEDFact_assistant')) {
	$text = CensusAssistantModule::formatCensusNote($controller->record);
} else {
	$text = Filter::formatText($controller->record->getNote(), $controller->record->getTree());
}

?>
<div id="note-details">
	<h2><?php echo $controller->record->getFullName(); ?></h2>
	<div id="note-tabs">
		<ul>
			<li>
				<a href="#note-edit">
					<span><?php echo I18N::translate('Details'); ?></span>
				</a>
			</li>
			<?php if ($linked_indi) { ?>
			<li>
				<a href="#indi-note">
					<span id="indisource"><?php echo I18N::translate('Individuals'); ?></span>
				</a>
			</li>
			<?php } ?>
			<?php if ($linked_fam) { ?>
			<li>
				<a href="#fam-note">
					<span id="famsource"><?php echo I18N::translate('Families'); ?></span>
				</a>
			</li>
			<?php } ?>
			<?php if ($linked_obje) { ?>
			<li>
				<a href="#media-note">
					<span id="mediasource"><?php echo I18N::translate('Media objects'); ?></span>
				</a>
			</li>
			<?php } ?>
			<?php if ($linked_sour) { ?>
			<li>
				<a href="#source-note">
					<span id="notesource"><?php echo I18N::translate('Sources'); ?></span>
				</a>
			</li>
			<?php } ?>
		</ul>
		<div id="note-edit">
			<table class="facts_table">
				<tr>
					<td class="descriptionbox">
						<?php if (Auth::isEditor($controller->record->getTree())) { ?>
							<a href="#" onclick="return edit_note('<?php echo $controller->record->getXref(); ?>')" title="<?php echo I18N::translate('Edit'); ?>">
							<i class="icon-note"></i> <?php echo I18N::translate('Shared note'); ?>
							</a>
							<div class="editfacts">
								<div class="editlink">
								<a class="editicon" href="#" onclick="return edit_note('<?php echo $controller->record->getXref(); ?>')" title="<?php echo I18N::translate('Edit'); ?>">
									<span class="link_text"><?php echo I18N::translate('Edit'); ?></span>
								</a>
							</div>
						<?php } else { ?>
						<i class="icon-note"></i>
							<?php echo I18N::translate('Shared note'); ?>
						<?php } ?>
					</td>
					<td class="optionbox wrap width80"><?php echo $text; ?></td>
				</tr>
				<?php
					foreach ($facts as $fact) {
						FunctionsPrintFacts::printFact($fact, $controller->record);
					}
					if ($controller->record->canEdit()) {
						FunctionsPrint::printAddNewFact($controller->record->getXref(), $facts, 'NOTE');
					}
				?>
			</table>
		</div>
		<?php
		if ($linked_indi) {
			echo '<div id="indi-note">', FunctionsPrintLists::individualTable($linked_indi), '</div>';
		}
		if ($linked_fam) {
			echo '<div id="fam-note">', FunctionsPrintLists::familyTable($linked_fam), '</div>';
		}
		if ($linked_obje) {
			echo '<div id="media-note">', FunctionsPrintLists::mediaTable($linked_obje), '</div>';
		}
		if ($linked_sour) {
			echo '<div id="source-note">', FunctionsPrintLists::sourceTable($linked_sour), '</div>';
		}
		?>
	</div>
</div>
