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

use Fisharebest\Webtrees\Controller\MediaController;
use Fisharebest\Webtrees\Functions\FunctionsPrint;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$record     = Media::getInstance(Filter::get('mid', WT_REGEX_XREF), $WT_TREE);
$controller = new MediaController($record);

if ($controller->record && $controller->record->canShow()) {
	if ($controller->record->isPendingDeletion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			FlashMessages::addMessage(/* I18N: %1$s is “accept”, %2$s is “reject”. These are links. */
				I18N::translate('This media object has been deleted. You should review the deletion and then %1$s or %2$s it.', '<a href="#" class="alert-link" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'accept') . '</a>', '<a href="#" class="alert-link" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'reject') . '</a>') . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		} elseif (Auth::isEditor($controller->record->getTree())) {
			FlashMessages::addMessage(I18N::translate('This media object has been deleted. The deletion will need to be reviewed by a moderator.') . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		}
	} elseif ($controller->record->isPendingAddtion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			FlashMessages::addMessage(/* I18N: %1$s is “accept”, %2$s is “reject”. These are links. */
				I18N::translate('This media object has been edited. You should review the changes and then %1$s or %2$s them.', '<a href="#" class="alert-link" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'accept') . '</a>', '<a href="#" class="alert-link" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'reject') . '</a>') . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		} elseif (Auth::isEditor($controller->record->getTree())) {
			FlashMessages::addMessage(I18N::translate('This media object has been edited. The changes need to be reviewed by a moderator.') . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		}
	}
	$controller->pageHeader();
} else {
	FlashMessages::addMessage(I18N::translate('This media object does not exist or you do not have permission to view it.'), 'danger');
	http_response_code(404);
	$controller->pageHeader();

	return;
}

echo View::make('media-page', [
	'media'       => $controller->record,
	'individuals' => $controller->record->linkedIndividuals('OBJE'),
	'families'    => $controller->record->linkedFamilies('OBJE'),
	'sources'     => $controller->record->linkedSources('OBJE'),
	'notes'       => $controller->record->linkedNotes('OBJE'),
	'facts'       => $controller->getFacts(),
]);
