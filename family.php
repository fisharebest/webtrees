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

use Fisharebest\Webtrees\Controller\FamilyController;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$record     = Family::getInstance(Filter::get('famid', WT_REGEX_XREF), $WT_TREE);
$controller = new FamilyController($record);

if ($controller->record && $controller->record->canShow()) {
	$controller->pageHeader();
} elseif ($controller->record && $controller->record->getTree()->getPreference('SHOW_PRIVATE_RELATIONSHIPS')) {
	$controller->pageHeader();
	// Continue - to display the children/parents/grandparents.
	// We'll check for showing the details again later
} else {
	http_response_code(404);
	$controller->pageHeader();

	echo View::make('alerts/error', [
		'alert' => I18N::translate('This family does not exist or you do not have permission to view it.'),
	]);

	return;
}

echo View::make('family-page', [
	'family' => $controller->record,
	'facts'  => $controller->familyFacts(),
]);
