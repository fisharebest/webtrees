<?php
/**
 * Used by AJAX to load the expanded view inside person boxes
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2008 PGV Development Team. All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package webtrees
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'expand_view.php');
require './includes/session.php';

header('Content-Type: text/html; charset=UTF-8');
$pid = safe_GET_xref('pid');
$person = WT_Person::getInstance($pid);
if (!$person->canDisplayDetails()) return WT_I18N::translate('Private');

$nonfacts = array("SEX","FAMS","FAMC","NAME","TITL","NOTE","SOUR","SSN","OBJE","HUSB","WIFE","CHIL","ALIA","ADDR","PHON","SUBM","_EMAIL","CHAN","URL","EMAIL","WWW","RESI","_UID","_TODO","_WT_OBJE_SORT","_WT_OBJE_SORT");
$person->add_family_facts(false);
$subfacts = $person->getIndiFacts();

sort_facts($subfacts);

$f2 = 0;
/* @var $event Event */
foreach ($subfacts as $indexval => $event) {
	if ($event->canShow()) {
		if ($f2>0) echo "<br />";
		$f2++;
		// handle ASSO record
		if ($event->getTag()=='ASSO') {
			print_asso_rela_record($event);
			continue;
		}
		$fact = $event->getTag();
		$details = $event->getDetail();
		echo '<span class="details_label">', $event->getLabel(), '</span> ';
		$details = $event->getDetail();
		if ($details!="Y" && $details!="N") print PrintReady($details);
		echo format_fact_date($event, false, false, $fact, $pid, $person->getGedcomRecord());
		//-- print spouse name for marriage events
		$famid = $event->getFamilyId();
		$spouseid = $event->getSpouseId();
		if (!empty($spouseid)) {
			$spouse = WT_Person::getInstance($spouseid);
			if ($spouse) {
				echo ' <a href="', $spouse->getHtmlUrl(), '">', PrintReady($spouse->getFullName()), '</a> - ';
			}
		}
		if (!empty($famid)) {
			$family = WT_Family::getInstance($famid);
			if ($family) {
				echo '<a href="', $family->getHtmlUrl(), '">[',WT_I18N::translate('View Family'), ']</a>';
			}
		}
		echo format_fact_place($event, true, true);
	}
}
