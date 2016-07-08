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

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\GedcomRecordController;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Functions\FunctionsPrintFacts;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;

define('WT_SCRIPT_NAME', 'gedrecord.php');
require './includes/session.php';

$record = GedcomRecord::getInstance(Filter::get('pid', WT_REGEX_XREF), $WT_TREE);
if (
	$record instanceof Individual ||
	$record instanceof Family ||
	$record instanceof Source ||
	$record instanceof Repository ||
	$record instanceof Note ||
	$record instanceof Media
) {
	header('Location: ' . WT_BASE_URL . $record->getRawUrl());

	return;
}
$controller = new GedcomRecordController($record);

if ($controller->record && $controller->record->canShow()) {
	if ($controller->record->isPendingDeletion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			FlashMessages::addMessage(/* I18N: %1$s is “accept”, %2$s is “reject”. These are links. */ I18N::translate(
				'This record has been deleted. You should review the deletion and then %1$s or %2$s it.',
				'<a href="#" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'accept') . '</a>',
				'<a href="#" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'reject') . '</a>'
			) . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		} elseif (Auth::isEditor($controller->record->getTree())) {
			FlashMessages::addMessage(I18N::translate('This record has been deleted. The deletion will need to be reviewed by a moderator.') . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		}
	} elseif ($controller->record->isPendingAddtion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			FlashMessages::addMessage(/* I18N: %1$s is “accept”, %2$s is “reject”. These are links. */ I18N::translate(
				'This record has been edited. You should review the changes and then %1$s or %2$s them.',
				'<a href="#" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'accept') . '</a>',
				'<a href="#" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'reject') . '</a>'
			) . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		} elseif (Auth::isEditor($controller->record->getTree())) {
			FlashMessages::addMessage(I18N::translate('This record has been edited. The changes need to be reviewed by a moderator.') . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		}
	}
	$controller->pageHeader();
} else {
	FlashMessages::addMessage(I18N::translate('This record does not exist or you do not have permission to view it.'), 'danger');
	http_response_code(404);
	$controller->pageHeader();

	return;
}

$controller->addInlineJavascript('
	jQuery("#record-tabs")
		.tabs({
			create: function(e, ui){
				jQuery(e.target).css("visibility", "visible");  // prevent FOUC
			}
		});
');

$linked_indi = $controller->record->linkedIndividuals('SUBM');
$linked_fam  = $controller->record->linkedFamilies('SUBM');
$linked_obje = $controller->record->linkedMedia('SUBM');
$linked_sour = $controller->record->linkedSources('SUBM');
$linked_note = $controller->record->linkedNotes('SUBM');

$facts = $controller->record->getFacts();

?>
<div id="record-details">
	<h2>
		<?php echo $controller->record->getFullName() ?>
	</h2>
	<div id="record-tabs">
		<ul>
			<li>
				<a href="#record-edit">
					<?php echo I18N::translate('Details') ?>
				</a>
			</li>
			<?php if ($linked_indi): ?>
			<li>
				<a href="#linked-individuals">
					<?php echo I18N::translate('Individuals') ?>
				</a>
			</li>
			<?php endif; ?>
			<?php if ($linked_fam): ?>
			<li>
				<a href="#linked-families">
					<?php echo I18N::translate('Families') ?>
				</a>
			</li>
			<?php endif; ?>
			<?php if ($linked_obje): ?>
			<li>
				<a href="#linked-media">
					<?php echo I18N::translate('Media objects') ?>
				</a>
			</li>
			<?php endif; ?>
			<?php if ($linked_sour): ?>
			<li>
				<a href="#linked-sources"><?php echo I18N::translate('Sources') ?></a>
			</li>
			<?php endif; ?>
			<?php if ($linked_note): ?>
			<li>
				<a href="#linked-notes"><?php echo I18N::translate('Notes') ?></a>
			</li>
			<?php endif; ?>
		</ul>

		<div id="record-edit">
			<table class="facts_table">
				<?php
				foreach ($facts as $fact) {
					FunctionsPrintFacts::printFact($fact, $controller->record);
				}
				?>
			</table>
		</div>

		<?php if ($linked_indi): ?>
			<div id="linked-individuals">
				<?php echo FunctionsPrintLists::individualTable($linked_indi) ?>
			</div>
		<?php endif; ?>

		<?php if ($linked_fam): ?>
			<div id="linked-families">
				<?php echo FunctionsPrintLists::familyTable($linked_fam) ?>
			</div>
		<?php endif; ?>

		<?php if ($linked_obje): ?>
			<div id="linked-media">
				<?php echo FunctionsPrintLists::mediaTable($linked_obje) ?>
			</div>
		<?php endif; ?>

		<?php if ($linked_sour): ?>
			<div id="linked-sources">
				<?php echo FunctionsPrintLists::sourceTable($linked_sour) ?>
			</div>
		<?php endif; ?>

		<?php if ($linked_note): ?>
			<div id="linked-notes">
				<?php echo FunctionsPrintLists::noteTable($linked_note) ?>
			</div>
		<?php endif; ?>
	</div>
</div>
