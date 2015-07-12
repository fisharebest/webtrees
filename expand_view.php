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

use Fisharebest\Webtrees\Functions\Functions;

define('WT_SCRIPT_NAME', 'expand_view.php');
require './includes/session.php';

header('Content-Type: text/html; charset=UTF-8');
$individual = Individual::getInstance(Filter::get('pid', WT_REGEX_XREF), $WT_TREE);
if (!$individual || !$individual->canShow()) {
	return I18N::translate('Private');
}

$facts = $individual->getFacts();
foreach ($individual->getSpouseFamilies() as $family) {
	foreach ($family->getFacts() as $fact) {
		$facts[] = $fact;
	}
}
Functions::sortFacts($facts);

foreach ($facts as $fact) {
	switch ($fact->getTag()) {
	case 'ADDR':
	case 'ALIA':
	case 'ASSO':
	case 'CHAN':
	case 'CHIL':
	case 'EMAIL':
	case 'FAMC':
	case 'FAMS':
	case 'HUSB':
	case 'NAME':
	case 'NOTE':
	case 'OBJE':
	case 'PHON':
	case 'RESI':
	case 'RESN':
	case 'SEX':
	case 'SOUR':
	case 'SSN':
	case 'SUBM':
	case 'TITL':
	case 'URL':
	case 'WIFE':
	case 'WWW':
	case '_EMAIL':
	case '_TODO':
	case '_UID':
	case '_WT_OBJE_SORT':
		// Do not show these
		break;
	default:
		// Simple version of FunctionsPrintFacts::print_fact()
		echo $fact->summary();
		break;
	}
}
