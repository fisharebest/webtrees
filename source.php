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

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\SourceController;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Functions\FunctionsPrintFacts;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;

define('WT_SCRIPT_NAME', 'source.php');
require './includes/session.php';

$record = Source::getInstance(Filter::get('sid', WT_REGEX_XREF), $WT_TREE);
$controller = new SourceController($record);

if ($controller->record && $controller->record->canShow()) {
	$controller->pageHeader();
	if ($controller->record->isPendingDeletion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is “accept”, %2$s is “reject”.  These are links. */ I18N::translate(
					'This source has been deleted.  You should review the deletion and then %1$s or %2$s it.',
					'<a href="#" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'accept') . '</a>',
					'<a href="#" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'reject') . '</a>'
				),
				' ', FunctionsPrint::helpLink('pending_changes'),
				'</p>';
		} elseif (Auth::isEditor($controller->record->getTree())) {
			echo
				'<p class="ui-state-highlight">',
				I18N::translate('This source has been deleted.  The deletion will need to be reviewed by a moderator.'),
				' ', FunctionsPrint::helpLink('pending_changes'),
				'</p>';
		}
	} elseif ($controller->record->isPendingAddtion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is “accept”, %2$s is “reject”.  These are links. */ I18N::translate(
					'This source has been edited.  You should review the changes and then %1$s or %2$s them.',
					'<a href="#" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'accept') . '</a>',
					'<a href="#" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'reject') . '</a>'
				),
				' ', FunctionsPrint::helpLink('pending_changes'),
				'</p>';
		} elseif (Auth::isEditor($controller->record->getTree())) {
			echo
				'<p class="ui-state-highlight">',
				I18N::translate('This source has been edited.  The changes need to be reviewed by a moderator.'),
				' ', FunctionsPrint::helpLink('pending_changes'),
				'</p>';
		}
	}
} else {
	http_response_code(404);
	$controller->pageHeader();
	echo '<p class="ui-state-error">', I18N::translate('This source does not exist or you do not have permission to view it.'), '</p>';

	return;
}

$controller->addInlineJavascript('
	jQuery("#source-tabs")
		.tabs({
			create: function(e, ui){
				jQuery(e.target).css("visibility", "visible");  // prevent FOUC
			}
		});
');

$linked_fam  = $controller->record->linkedFamilies('SOUR');
$linked_indi = $controller->record->linkedIndividuals('SOUR');
$linked_note = $controller->record->linkedNotes('SOUR');
$linked_obje = $controller->record->linkedMedia('SOUR');
$linked_sour = array();

$facts = $controller->record->getFacts();

usort(
	$facts,
	function (Fact $x, Fact $y) {
		static $order = array(
			'TITL' => 0,
			'ABBR' => 1,
			'AUTH' => 2,
			'DATA' => 3,
			'PUBL' => 4,
			'TEXT' => 5,
			'NOTE' => 6,
			'OBJE' => 7,
			'REFN' => 8,
			'RIN'  => 9,
			'_UID' => 10,
			'CHAN' => 11,
		);

		return
			(array_key_exists($x->getTag(), $order) ? $order[$x->getTag()] : PHP_INT_MAX)
			-
			(array_key_exists($y->getTag(), $order) ? $order[$y->getTag()] : PHP_INT_MAX);
	}
);

?>
<div id="source-details">
	<h2>
		<?php echo $controller->record->getFullName() ?>
	</h2>
	<div id="source-tabs">
		<ul>
			<li>
				<a href="#source-edit">
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

		<div id="source-edit">
			<table class="facts_table">
				<?php
				foreach ($facts as $fact) {
					FunctionsPrintFacts::printFact($fact, $controller->record);
				}

				if ($controller->record->canEdit()) {
					FunctionsPrint::printAddNewFact($controller->record->getXref(), $facts, 'SOUR');
					// new media
					if ($controller->record->getTree()->getPreference('MEDIA_UPLOAD') >= Auth::accessLevel($WT_TREE)) {
						echo '<tr><td class="descriptionbox">';
						echo GedcomTag::getLabel('OBJE');
						echo '</td><td class="optionbox">';
						echo '<a href="#" onclick="window.open(\'addmedia.php?action=showmediaform&amp;linktoid=', $controller->record->getXref(), '\', \'_blank\', edit_window_specs); return false;">', I18N::translate('Add a new media object'), '</a>';
						echo FunctionsPrint::helpLink('OBJE');
						echo '<br>';
						echo '<a href="#" onclick="window.open(\'inverselink.php?linktoid=', $controller->record->getXref(), '&amp;linkto=source\', \'_blank\', find_window_specs); return false;">', I18N::translate('Link to an existing media object'), '</a>';
						echo '</td></tr>';
					}
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
