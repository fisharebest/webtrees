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

require 'includes/session.php';

$mod        = Filter::get('mod');
$mod_action = Filter::get('mod_action');
$module     = Module::getModuleByName($mod);


if ($mod === 'sitemap') {
	// Redirect legacy calls to the new router
	$_GET['route'] = 'module';
	if (Filter::get('file') === 'sitemap.xml') {
		$_GET['action'] = 'Index';
	} else {
		$_GET['action'] = 'File';
		$_GET['file'] = basename(substr(Filter::get('file'), 8), '.xml');
	}

	require __DIR__ . '/index.php';
}

if ($module) {
	$module->modAction($mod_action);
} else {
	header('Location: index.php');
}
