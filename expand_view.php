<?php
namespace Fisharebest\Webtrees;

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

use Zend_Session;

define('WT_SCRIPT_NAME', 'expand_view.php');
require './includes/session.php';

Zend_Session::writeClose();

header('Content-Type: text/html; charset=UTF-8');
$individual = Individual::getInstance(Filter::get('pid', WT_REGEX_XREF));
if (!$individual || !$individual->canShow()) {
	return I18N::translate('Private');
}

$facts = $individual->getFacts();
foreach ($individual->getSpouseFamilies() as $family) {
	foreach ($family->getFacts() as $fact) {
		$facts[] = $fact;
	}
}
sort_facts($facts);

foreach ($facts as $fact) {
	switch ($fact->getTag()) {
	case 'SEX':
	case 'FAMS':
	case 'FAMC':
	case 'NAME':
	case 'TITL':
	case 'NOTE':
	case 'SOUR':
	case 'SSN':
	case 'OBJE':
	case 'HUSB':
	case 'WIFE':
	case 'CHIL':
	case 'ALIA':
	case 'ADDR':
	case 'PHON':
	case 'SUBM':
	case '_EMAIL':
	case 'CHAN':
	case 'URL':
	case 'EMAIL':
	case 'WWW':
	case 'RESI':
	case 'RESN':
	case '_UID':
	case '_TODO':
	case '_WT_OBJE_SORT':
		// Do not show these
		break;
	case 'ASSO':
		// Associates
		echo format_asso_rela_record($fact);
		break;
	default:
		// Simple version of print_fact()
		echo $fact->summary();
		break;
	}
}
