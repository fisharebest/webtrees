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

use Fisharebest\Webtrees\Controller\IndividualController;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\Functions\FunctionsDb;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$pid    = Filter::get('pid', WT_REGEX_XREF);
$record = Individual::getInstance($pid, $WT_TREE);
if (!$record) {
	$record = Individual::getInstance(FunctionsDb::findRin($pid), $WT_TREE);
}
$controller = new IndividualController($record);

if ($controller->record && $controller->record->canShow()) {
	if (Filter::get('action') == 'ajax') {
		$controller->ajaxRequest();

		return;
	}
	// Generate the sidebar content *before* we display the page header,
	// as the clippings cart needs to have write access to the session.
	$sidebar_html = $controller->getSideBarContent();

	$controller->pageHeader();
} elseif ($controller->record && $controller->record->canShowName()) {
	// Just show the name.
	$controller->pageHeader();
	echo '<h2>', $controller->record->getFullName(), '</h2>';
	echo '<p>', I18N::translate('The details of this individual are private.'), '</p>';

	return;
} else {
	http_response_code(404);
	$controller->pageHeader();

	echo View::make('alerts/danger', [
		'alert' => I18N::translate('This individual does not exist or you do not have permission to view it.'),
	]);

	return;
}

// If this individual is linked to a user account, show the link
$user_link = '';
if (Auth::isAdmin()) {
	$user = User::findByIndividual($controller->record);
	if ($user) {
		$user_link = ' —  <a href="admin_users.php?filter=' . e($user->getUserName()) . '">' . e($user->getUserName()) . '</a>';
	};
}

// What is (was) the age of the individual
$bdate = $controller->record->getBirthDate();
$ddate = $controller->record->getDeathDate();
if ($bdate->isOK() && !$controller->record->isDead()) {
	// If living display age
	$age = ' (' . I18N::translate('age') . ' ' . FunctionsDate::getAgeAtEvent(Date::getAgeGedcom($bdate, new Date(strtoupper(date('d M Y'))))) . ')';
} elseif ($bdate->isOK() && $ddate->isOK()) {
	// If dead, show age at death
	$age = ' (' . I18N::translate('age') . ' ' . FunctionsDate::getAgeAtEvent(Date::getAgeGedcom($bdate, $ddate)) . ')';
} else {
	$age = '';
}

// Allow tabs to insert Javascript, etc.
// TODO: there's probably a cleaner way to do this.
foreach ($controller->getTabs() as $tab) {
	echo $tab->getPreLoadContent();
}

$controller->addInlineJavascript('
// If the URL contains a fragment, then activate the corresponding tab.
// Use a prefix on the fragment, to prevent scrolling to the element.
var target = window.location.hash.replace("tab-", "");
var tab = $("#individual-tabs .nav-link[href=\'" + target + "\']");
// If not, then activate the first tab.
if (tab.length === 0) {
	tab = $("#individual-tabs .nav-link:first");
}
tab.tab("show");
');

$individual_media = [];
foreach ($controller->record->getFacts() as $fact) {
	$media_object = $fact->getTarget();
	if ($media_object instanceof Media) {
		$individual_media[] = $media_object->firstImageFile();
	}
}
$individual_media = array_filter($individual_media);

$name_records = [];
foreach ($controller->record->getFacts('NAME') as $n => $name_fact) {
	$name_records[] = $controller->formatNameRecord($n, $name_fact);
}

$sex_records = [];
foreach ($controller->record->getFacts('SEX') as $n => $sex_fact) {
	$sex_records[] = $controller->formatSexRecord($sex_fact);
}

echo View::make('individual-page', [
	'age'              => $age,
	'individual'       => $controller->record,
	'individual_media' => $individual_media,
	'name_records'     => $name_records,
	'sex_records'      => $sex_records,
	'sidebar_html'     => $sidebar_html,
	'tabs'             => $controller->getTabs(),
	'user_link'        => $user_link,
]);
