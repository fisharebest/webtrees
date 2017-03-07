<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$record     = Note::getInstance(Filter::get('nid', WT_REGEX_XREF), $WT_TREE);
$controller = new NoteController($record);

if ($controller->record && $controller->record->canShow()) {
	if ($controller->record->isPendingDeletion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			FlashMessages::addMessage(/* I18N: %1$s is “accept”, %2$s is “reject”. These are links. */ I18N::translate(
				'This note has been deleted. You should review the deletion and then %1$s or %2$s it.',
				'<a href="#" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'accept') . '</a>',
				'<a href="#" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'reject') . '</a>'
			) . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		} elseif (Auth::isEditor($controller->record->getTree())) {
			FlashMessages::addMessage(I18N::translate('This note has been deleted. The deletion will need to be reviewed by a moderator.') . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		}
	} elseif ($controller->record->isPendingAddtion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			FlashMessages::addMessage(/* I18N: %1$s is “accept”, %2$s is “reject”. These are links. */ I18N::translate(
				'This note has been edited. You should review the changes and then %1$s or %2$s them.',
				'<a href="#" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'accept') . '</a>',
				'<a href="#" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'reject') . '</a>'
			) . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		} elseif (Auth::isEditor($controller->record->getTree())) {
			FlashMessages::addMessage(I18N::translate('This note has been edited. The changes need to be reviewed by a moderator.') . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		}
	}
	$controller->pageHeader();
} else {
	FlashMessages::addMessage(I18N::translate('This note does not exist or you do not have permission to view it.'), 'danger');
	http_response_code(404);
	$controller->pageHeader();

	return;
}

$families      = $controller->record->linkedFamilies('NOTE');
$individuals   = $controller->record->linkedIndividuals('NOTE');
$notes         = [];
$media_objects = $controller->record->linkedMedia('NOTE');
$sources       = $controller->record->linkedSources('NOTE');

$facts = [];
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
<h2 class="wt-page-title">
	<?= $controller->record->getFullName() ?>
</h2>

<div class="wt-page-content">
	<ul class="nav nav-tabs" role="tablist">
		<li class="nav-item">
				<a class="nav-link active" data-toggle="tab" role="tab" href="#details">
				<?= I18N::translate('Details') ?>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link<?= empty($individuals) ? ' text-muted' : '' ?>" data-toggle="tab" role="tab" href="#individuals">
				<?= I18N::translate('Individuals') ?>
				<?= Bootstrap4::badgeCount($individuals) ?>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link<?= empty($families) ? ' text-muted' : '' ?>" data-toggle="tab" role="tab" href="#families">
				<?= I18N::translate('Families') ?>
				<?= Bootstrap4::badgeCount($families) ?>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link<?= empty($media_objects) ? ' text-muted' : '' ?>" data-toggle="tab" role="tab" href="#media">
				<?= I18N::translate('Media objects') ?>
				<?= Bootstrap4::badgeCount($media_objects) ?>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link<?= empty($sources) ? ' text-muted' : '' ?>" data-toggle="tab" role="tab" href="#sources">
				<?= I18N::translate('Sources') ?>
				<?= Bootstrap4::badgeCount($sources) ?>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link<?= empty($notes) ? ' text-muted' : '' ?>" data-toggle="tab" role="tab" href="#notes">
				<?= I18N::translate('Notes') ?>
				<?= Bootstrap4::badgeCount($notes) ?>
			</a>
		</li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active fade show" role="tabpanel" id="details">
			<table class="facts_table">
				<colgroup>
					<col class="width20">
					<col class="width80">
				</colgroup>
				<tr>
					<td class="descriptionbox">
						<?= I18N::translate('Shared note') ?>
						<?php if (Auth::isEditor($controller->record->getTree())) { ?>
							<div class="editfacts">
								<?= FontAwesome::linkIcon('edit', I18N::translate('Edit'), ['class' => 'btn btn-link', 'href' => 'edit_interface.php?action=editnote&ged=' . $controller->record->getTree()->getNameUrl() . '&xref=' . $controller->record->getXref()]) ?>
							</div>
						<?php } ?>
					</td>
					<td class="optionbox wrap width80"><?= $text ?></td>
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

		<div class="tab-pane fade" role="tabpanel" id="individuals">
			<?= FunctionsPrintLists::individualTable($individuals) ?>
		</div>

		<div class="tab-pane fade" role="tabpanel" id="families">
			<?= FunctionsPrintLists::familyTable($families) ?>
		</div>

		<div class="tab-pane fade" role="tabpanel" id="media">
			<?= FunctionsPrintLists::mediaTable($media_objects) ?>
		</div>

		<div class="tab-pane fade" role="tabpanel" id="sources">
			<?= FunctionsPrintLists::sourceTable($sources) ?>
		</div>

		<div class="tab-pane fade" role="tabpanel" id="notes">
			<?= FunctionsPrintLists::noteTable($notes) ?>
		</div>
	</div>
</div>
