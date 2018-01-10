<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

use Fisharebest\Webtrees\Controller\ChartController;

require 'includes/session.php';

$controller = new ChartController;
$controller
	->restrictAccess(Module::isActiveChart($controller->tree(), 'compact_tree_chart'))
	->setPageTitle(/* I18N: %s is an individual’s name */ I18N::translate('Compact tree of %s', $controller->root->getFullName()));

// Only generate the content for interactive users (not search robots).
if (Filter::getBool('ajax') && Session::has('initiated')) {
	echo view('compact-chart', [
		'ancestors'  => $controller->sosaAncestors(5),
		'controller' => $controller,
	]);

	return;
}

$ajax_url = Html::url('compact.php', [
	'rootid' => $controller->root->getXref(),
	'ged'    => $controller->root->getTree()->getName(),
	'ajax'   => 1,
]);

$controller->pageHeader();

echo view('compact-chart-page', [
	'title'      => $controller->getPageTitle(),
	'individual' => $controller->root,
	'ajax_url'   => $ajax_url,
]);
