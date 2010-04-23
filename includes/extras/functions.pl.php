<?php
/**
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_FUNCTIONS_PL_PHP', '');

////////////////////////////////////////////////////////////////////////////////
// Localise a date. Lokalizacja daty.
////////////////////////////////////////////////////////////////////////////////
/*
function date_localisation_pl(&$q1, &$d1, &$q2, &$d2, &$q3) {
	static $NOMINATIVE_MONTHS=NULL;
	static $GENITIVE_MONTHS=NULL;
	static $INSTRUMENTAL_MONTHS=NULL;
	static $LOCATIVE_MONTHS=NULL;

	if (empty($NOMINATIVE_MONTHS)) {
		$NOMINATIVE_MONTHS=array(i18n::translate('January'), i18n::translate('February'), i18n::translate('March'), i18n::translate('April'), i18n::translate('May'), i18n::translate('June'), i18n::translate('July'), i18n::translate('August'), i18n::translate('September'), i18n::translate('October'), i18n::translate('November'), i18n::translate('December'));
		$GENITIVE_MONTHS=array('stycznia', 'lutego', 'marca', 'kwietnia', 'maja', 'czerwca', 'lipca', 'sierpnia', 'września', 'października', 'listopada', 'grudnia');
		$INSTRUMENTAL_MONTHS=array('styczniem', 'lutym', 'marcem', 'kwietniem', 'majem', 'czerwcem', 'lipcem', 'sierpniem', 'wrześniem', 'październikiem', 'listopadem', 'grudniem');
		$LOCATIVE_MONTHS=array('styczniu', 'lutym', 'marcu', 'kwietniu', 'maju', 'czerwcu', 'lipcu', 'sierpniu', 'wrześniu', 'październiku', 'listopadzie', 'grudniu');
	}

	// Months with a day number are genitive, regardless of qualifier
	for ($i=0; $i<12; ++$i) {
		$d1=preg_replace("/(\d+ ){$NOMINATIVE_MONTHS[$i]}/", "$1{$GENITIVE_MONTHS[$i]}", $d1);
		$d2=preg_replace("/(\d+ ){$NOMINATIVE_MONTHS[$i]}/", "$1{$GENITIVE_MONTHS[$i]}", $d2);
	}

	// Months without a day number (i.e. month at start) depend on the qualifier
	switch ($q1) {
	case 'from': case 'to': case 'abt': case 'apx': case 'cir':
		for ($i=0; $i<12; ++$i)
			$d1=preg_replace("/^{$NOMINATIVE_MONTHS[$i]}/", $GENITIVE_MONTHS[$i], $d1);
		break;
	case 'bet': case 'bef':
		for ($i=0; $i<12; ++$i)
			$d1=preg_replace("/^{$NOMINATIVE_MONTHS[$i]}/", $INSTRUMENTAL_MONTHS[$i], $d1);
		break;
	case 'aft':
		for ($i=0; $i<12; ++$i)
			$d1=preg_replace("/^{$NOMINATIVE_MONTHS[$i]}/", $LOCATIVE_MONTHS[$i], $d1);
	 	break;
	}
	switch ($q2) {
	case 'to':
		for ($i=0; $i<12; ++$i)
			$d2=preg_replace("/^{$NOMINATIVE_MONTHS[$i]}/", $GENITIVE_MONTHS[$i], $d2);
		break;
	case 'and':
		for ($i=0; $i<12; ++$i)
			$d2=preg_replace("/^{$NOMINATIVE_MONTHS[$i]}/", $INSTRUMENTAL_MONTHS[$i], $d2);
		break;
	}
}
*/
////////////////////////////////////////////////////////////////////////////////
// Localise an age. Lokalizacja wieku.
////////////////////////////////////////////////////////////////////////////////
function age_localisation_pl(&$agestring, &$show_years) {
	$show_years=true;
	$agestring=preg_replace(
		array(
			'/\bchi(ld)?\b/i',
			'/\binf(ant)?\b/i',
			'/\bsti(llborn)?\b/i',
			'/\b1y/i',
			'/\b2y/i','/\b3y/i','/\b4y/i',
			'/\b22y/i','/\b23y/i','/\b24y/i',
			'/\b32y/i','/\b33y/i','/\b34y/i',
			'/\b42y/i','/\b43y/i','/\b44y/i',
			'/\b52y/i','/\b53y/i','/\b54y/i',
			'/\b62y/i','/\b63y/i','/\b64y/i',
			'/\b72y/i','/\b73y/i','/\b74y/i',
			'/\b82y/i','/\b83y/i','/\b84y/i',
			'/\b92y/i','/\b93y/i','/\b94y/i',
			'/\b102y/i','/\b103y/i','/\b104y/i',
			'/\b122y/i','/\b123y/i','/\b124y/i',
			'/\b132y/i','/\b133y/i','/\b134y/i',
			'/(\d+)y/i',
			'/\b1m/i',
			'/\b2m/i','/\b3m/i','/\b4m/i',
			'/\b22m/i','/\b23m/i','/\b24m/i',
			'/(\d+)m/i',
			'/\b1d/i',
			'/(\d+)d/i',
			'/\b1w/i',
			'/\b2w/i','/\b3w/i','/\b4w/i',
			'/\b22w/i','/\b23w/i','/\b24w/i',
			'/(\d+)w/i'
		),
		array(
			i18n::translate('Child'),
			i18n::translate('Infant'),
	 		i18n::translate('Stillborn'),
			$show_years ? '1 '.i18n::translate('year') : '1',
			$show_years ? '2 '.i18n::translate('years') : '2', $show_years ? '3 '.i18n::translate('years') : '3', $show_years ? '4 '.i18n::translate('years') : '4',
			$show_years ? '22 '.i18n::translate('years') : '22', $show_years ? '23 '.i18n::translate('years') : '23', $show_years ? '24 '.i18n::translate('years') : '24',
			$show_years ? '32 '.i18n::translate('years') : '32', $show_years ? '33 '.i18n::translate('years') : '33', $show_years ? '34 '.i18n::translate('years') : '34',
			$show_years ? '42 '.i18n::translate('years') : '42', $show_years ? '43 '.i18n::translate('years') : '43', $show_years ? '44 '.i18n::translate('years') : '44',
			$show_years ? '52 '.i18n::translate('years') : '52', $show_years ? '53 '.i18n::translate('years') : '53', $show_years ? '54 '.i18n::translate('years') : '54',
			$show_years ? '62 '.i18n::translate('years') : '62', $show_years ? '63 '.i18n::translate('years') : '63', $show_years ? '64 '.i18n::translate('years') : '64',
			$show_years ? '72 '.i18n::translate('years') : '72', $show_years ? '73 '.i18n::translate('years') : '73', $show_years ? '74 '.i18n::translate('years') : '74',
			$show_years ? '82 '.i18n::translate('years') : '82', $show_years ? '83 '.i18n::translate('years') : '83', $show_years ? '84 '.i18n::translate('years') : '84',
			$show_years ? '92 '.i18n::translate('years') : '92', $show_years ? '93 '.i18n::translate('years') : '93', $show_years ? '94 '.i18n::translate('years') : '94',
			$show_years ? '102 '.i18n::translate('years') : '102', $show_years ? '103 '.i18n::translate('years') : '103', $show_years ? '104 '.i18n::translate('years') : '104',
			$show_years ? '122 '.i18n::translate('years') : '122', $show_years ? '123 '.i18n::translate('years') : '123', $show_years ? '124 '.i18n::translate('years') : '124',
			$show_years ? '132 '.i18n::translate('years') : '132', $show_years ? '133 '.i18n::translate('years') : '133', $show_years ? '134 '.i18n::translate('years') : '134',
			$show_years ? '$1 '.i18n::translate('years') : '$1',
			'1 '.i18n::translate('month'),
			'2 miesiące', '3 miesiące', '4 miesiące',
			'22 miesiące', '23 miesiące', '24 miesiące',
	 		'$1 '.i18n::translate('months'),
			'1 '.i18n::translate('day'),
			'$1 '.i18n::translate('days'),
			'1 '.i18n::translate('week'),
			'2 tygodnie', '3 tygodnie', '4 tygodnie',
			'22 tygodnie', '23 tygodnie', '24 tygodnie',
			'$1 '.i18n::translate('weeks')
		),
		$agestring
	);
////////////////////////////////////////////////////////////////////////////////
// Localise the relationships. Lokalizacja pokrewieństwa.
////////////////////////////////////////////////////////////////////////////////
function rela_localisation_pl(&$rela, &$pid2) {
	if (isset($pid2)) {
		$record = Person::getInstance($pid2);
		if (!empty($record)) {
			$sex = $record->getSex();
			switch ($rela) {
				case i18n::translate('Informant'):
					if ($sex == "M")		$rela = "Informator";
					else if ($sex == "F")   $rela = "Informatorka";
					break;
				case i18n::translate('Lodger'):
					if ($sex == "M")		$rela = "Lokator";
					else if ($sex == "F")   $rela = "Lokatorka";
					break;
				case i18n::translate('Slave'):
					if ($sex == "M")		$rela = "Niewolnik";
					else if ($sex == "F")   $rela = "Niewolnica";
					break;
				case i18n::translate('Attending'):
					if ($sex == "M")		$rela = "Obsługujący";
					else if ($sex == "F")   $rela = "Obsługująca";
					break;
				case i18n::translate('Guardian'):
					if ($sex == "M")		$rela = "Opiekun";
					else if ($sex == "F")   $rela = "Opiekunka";
					break;
				case i18n::translate('Nurse'):
					if ($sex == "M")		$rela = "Pielęgniarz";
					else if ($sex == "F")   $rela = "Pielęgniarka";
					break;
				case i18n::translate('Attendant'):
					if ($sex == "M")		$rela = "Pomocnik";
					else if ($sex == "F")   $rela = "Pomocnica";
					break;
				case i18n::translate('Employee'):
					if ($sex == "M")		$rela = "Pracownik";
					else if ($sex == "F")   $rela = "Pracownica";
					break;
				case i18n::translate('Friend'):
					if ($sex == "M")		$rela = "Przyjaciel";
					else if ($sex == "F")   $rela = "Przyjaciółka";
					break;
				case i18n::translate('Servant'):
					if ($sex == "M")		$rela = "Służący";
					else if ($sex == "F")   $rela = "Służąca";
					break;
				case i18n::translate('Seller'):
					if ($sex == "M")		$rela = "Sprzedawca";
					else if ($sex == "F")   $rela = "Sprzedawczyni";
					break;
				case i18n::translate('Owner'):
					if ($sex == "M")		$rela = "Właściciel";
					else if ($sex == "F")   $rela = "Właścicielka";
					break;
				case i18n::translate('Ward'):
					if ($sex == "M")		$rela = "Wychowanek";
					else if ($sex == "F")   $rela = "Wychowanka";
					break;
			}
		}
	}
	return $rela;
}

function getRelationshipText_pl($relationshipDescription, $node, $pid1, $pid2) {
	if ($relationshipDescription != false) {
		return utf8_strtolower($relationshipDescription);
	}
	return false;
}

//-- functions to calculate polish specific genitive names
function getFirstRelationsName_pl($pid) {
	// In Polish we want the genitive form of the name
	$person=Person::getInstance($pid);
	if ($person) {
		$fname=$person->getFullName();
	}
	else {
		$fname='';
	}
	//return $fname;
	
	// tested
	$pname='';
	$sex = Person::getInstance($pid)->getSex();
	if ($sex == "M") {
		$names = explode(" ", $fname);
		foreach ($names as $name) {
			if (preg_match('/ski$/', $name)) {
				$pname .= " ".preg_replace('/ski$/', 'skiego', $name);
			}
			else if (preg_match('/cki$/', $name)) {
				$pname .= " ".preg_replace('/cki$/', 'ckiego', $name);
			}
			else if (preg_match('/dzki$/', $name)) {
				$pname .= " ".preg_replace('/dzki$/', 'dzkiego', $name);
			}
			else if (preg_match('/żki$/', $name)) {
				$pname .= " ".preg_replace('/żki$/', 'żkiego', $name);
			}
			else if (preg_match('/y$/', $name)) {
				$pname .= " ".preg_replace('/y$/','ego', $name);
			}
			else if (preg_match('/i$/', $name)) {
				$pname .= " ".preg_replace('/i$/','iego', $name);
			}
			else if (preg_match('/ek$/', $name)) {
				$pname .= " ".preg_replace('/ek$/','ka', $name);
			}
			else if (preg_match('/eł$/', $name)) {
				$pname .= " ".preg_replace('/eł$/','ła', $name);
			}
			else if (preg_match('/el$/', $name)) {
				$pname .= " ".preg_replace('/el$/','la', $name);
			}
			else if (preg_match('/ń$/', $name)) {
				$pname .= " ".preg_replace('/ń$/','nia', $name);
			}
			else if (preg_match('/ź$/', $name)) {
				$pname .= " ".preg_replace('/ź$/','zia', $name);
			}
			else if (preg_match('/niec$/', $name)) {
				$pname .= " ".preg_replace('/niec$/','ńca', $name);
			}
			else if (preg_match('/iec$/', $name)) {
				$pname .= " ".preg_replace('/iec$/','ca', $name);
			}
			else if (preg_match('/ec$/', $name)) {
				$pname .= " ".preg_replace('/ec$/','ca', $name);
			}
			else if (preg_match('/er$/', $name)) {
				$pname .= " ".preg_replace('/er$/','ra', $name);
			}
			// go
			else if (preg_match('/go$/', $name)) {
				$pname .= " ".preg_replace('/go$/','gi', $name);
			}
			// io
			else if (preg_match('/io$/', $name)) {
				$pname .= " ".preg_replace('/io$/','ii', $name);
			}
			// jo
			else if (preg_match('/jo$/', $name)) {
				$pname .= " ".preg_replace('/jo$/','ji', $name);
			}
			// ko
			else if (preg_match('/ko$/', $name)) {
				$pname .= " ".preg_replace('/ko$/','ki', $name);
			}
			// bo, co, do, fo, ho, lo, ło, mo, no, po, ro, so, to, wo, zo
			else if (preg_match('/o$/', $name)) {
				$pname .= " ".preg_replace('/o$/','y', $name);
			}
			// ga
			else if (preg_match('/ga$/', $name)) {
				$pname .= " ".preg_replace('/ga$/','gi', $name);
			}
			// ia
			else if (preg_match('/ia$/', $name)) {
				$pname .= " ".preg_replace('/ia$/','i', $name);
			}
			// ja
			else if (preg_match('/ja$/', $name)) {
				$pname .= " ".preg_replace('/ja$/','ji', $name);
			}
			// ka
			else if (preg_match('/ka$/', $name)) {
				$pname .= " ".preg_replace('/ka$/','ki', $name);
			}
			// ba, ca, da, fa, ha, la, ła, ma, na, pa, ra, sa, ta, wa, za
			else if (preg_match('/a$/', $name)) {
				$pname .= " ".preg_replace('/a$/','y', $name);
			}
			else if (preg_match('/ek]$/', $name)) {
				$pname .= " ".preg_replace('/ek]$/','ka]', $name);
			}
			else if (preg_match('/"$/', $name)) {
				$pname .= " ".preg_replace('/"$/','a"', $name);
			}
			else
				$pname .= " ".$name."a";
		}
	}
	else if ($sex == "F") {
		$names = explode(" ", $fname);
		foreach ($names as $name) {
			if (preg_match('/raska$/', $name)) {
				$pname .= " ".preg_replace('/ska$/', 'ski', $name);
			}
			else if (preg_match('/ska$/', $name)) {
				$pname .= " ".preg_replace('/ska$/', 'skiej', $name);
			}
			else if (preg_match('/cka$/', $name)) {
				$pname .= " ".preg_replace('/cka$/', 'ckiej', $name);
			}
			else if (preg_match('/dzka$/', $name)) {
				$pname .= " ".preg_replace('/dzka$/', 'dzkiej', $name);
			}
			else if (preg_match('/żka$/', $name)) {
				$pname .= " ".preg_replace('/żka$/', 'żkiej', $name);
			}
			else if (preg_match('/ka"$/', $name)) {
				$pname .= " ".preg_replace('/ka"$/', 'ki"', $name);
			}
			else if (preg_match('/ska]$/', $name)) {
				$pname .= " ".preg_replace('/ska]$/', 'skiej]', $name);
			}
			else if (preg_match('/cka]$/', $name)) {
				$pname .= " ".preg_replace('/cka]$/', 'ckiej]', $name);
			}
			else if (preg_match('/dzka]$/', $name)) {
				$pname .= " ".preg_replace('/dzka]$/', 'dzkiej]', $name);
			}
			else if (preg_match('/żka]$/', $name)) {
				$pname .= " ".preg_replace('/żka]$/', 'żkiej]', $name);
			}
			else if (preg_match('/ka]$/', $name)) {
				$pname .= " ".preg_replace('/ka]$/', 'ki]', $name);
			}
			else if (preg_match('/a]$/', $name)) {
				$pname .= " ".preg_replace('/a]$/','y]', $name);
			}
			else
				$pname .= " ".preg_replace(array('/eja$/','/ja$/','/ia$/','/la$/','/ga$/','/ea$/','/ka$/','/a$/'), array('ei','ji','ii','li','gi','ei','ki','y'), $name);
		}
	}
	else {
		$pname = "osoby: ".$pname;
	}
	if (!empty($pname)) return trim($pname);
	else return $fname;
}

function century_localisation_pl($n, $show=true) {
	$arab = array(1, 4, 5, 9, 10);
	$roman = array("I", "IV", "V", "IX", "X");
	$roman_century = "";
	for ($i=4; $i>=0; $i--) {
		while ($n>=$arab[$i]) {
			$n-=$arab[$i];
			$roman_century .= $roman[$i];
		}
	}
	if ($show) return $roman_century." w.";
	else return $roman_century;
}
?>
