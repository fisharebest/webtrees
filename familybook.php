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

use Fisharebest\Webtrees\Controller\FamilyBookController;

require 'includes/session.php';

$controller = new FamilyBookController;
$controller->restrictAccess(Module::isActiveChart($controller->tree(), 'family_book_chart'));

// Only generate the content for interactive users (not search robots).
if (Filter::getBool('ajax') && Session::has('initiated')) {
	$controller->printFamilyBook($controller->root, $controller->descent);

	return;
}

$controller
	->addInlineJavascript('$(".wt-page-content").load(location.search + "&ajax=1");')
	->pageHeader();

echo View::make('family-book', [
	'title'       => $controller->getPageTitle(),
	'individual'  => $controller->root,
	'generations' => (int) $controller->generations,
	'descent'     => (int) $controller->descent,
	'show_spouse' => (bool) $controller->show_spouse,
]);
