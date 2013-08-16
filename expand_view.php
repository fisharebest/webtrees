<?php
// Used by AJAX to load the expanded view inside person boxes
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2008 PGV Development Team. All rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

define('WT_SCRIPT_NAME', 'expand_view.php');
require './includes/session.php';

Zend_Session::writeClose();

header('Content-Type: text/html; charset=UTF-8');
$person = WT_Individual::getInstance(safe_GET_xref('pid'));
if (!$person || !$person->canShow()) {
	return WT_I18N::translate('Private');
}

$facts = $person->getFacts();
foreach ($person->getSpouseFamilies() as $family) {
	foreach ($family->getFacts() as $fact) {
		$facts[] = $fact;
	}
}
sort_facts($facts);

foreach ($facts as $event) {
	switch ($event->getTag()) {
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
		echo '<div><span class="details_label">', $event->getLabel(), '</span> ';
		echo print_asso_rela_record($event, $person), '</div>';
		break;
	default:
		// Simple version of print_fact()
		echo '<div>';
		echo '<span class="details_label">', $event->getLabel(), '</span> ';
		$details=$event->getValue();
		if ($details!='Y' && $details!='N') {
			echo '<span dir="auto">', $details, '</span>';
		}
		echo format_fact_date($event, $person, false, false);
		// Show spouse/family for family events
		if ($event->getParent() instanceof WT_Family) {
			$spouse = $event->getParent()->getSpouse($person);
			if ($spouse) {
				echo ' <a href="', $spouse->getHtmlUrl(), '">', $spouse->getFullName(), '</a> - ';
				echo '<a href="', $event->getParent()->getHtmlUrl(), '">', WT_I18N::translate('View Family'), ' - </a>';
			}
		}
		echo ' ',format_fact_place($event, true, true);
		echo '</div>';
		break;
	}
}
