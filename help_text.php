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

define('WT_SCRIPT_NAME', 'help_text.php');
require './includes/session.php';

$help = Filter::get('help');
switch ($help) {
	//////////////////////////////////////////////////////////////////////////////
	// This is a list of all known gedcom tags.  We list them all here so that
	// xgettext() may find them.
	//
	// Tags such as BIRT:PLAC are only used as labels, and do not require help
	// text.  These are only used for translating labels.
	//
	// Tags such as _BIRT_CHIL are pseudo-tags, used to create family events.
	//
	// Generally, these tags need to be lists explicitly in add_simple_tag()
	//////////////////////////////////////////////////////////////////////////////

case 'ADDR':
	$title = GedcomTag::getLabel('ADDR');
	$text = I18N::translate('Enter the address into the field just as you would write it on an envelope.<br><br>Leave this field blank if you do not want to include an address.');
	break;

case 'AGNC':
	$title = GedcomTag::getLabel('AGNC');
	$text = I18N::translate('The organization, institution, corporation, individual, or other entity that has authority.<br><br>For example, an employer of an individual, or a church that administered rites or events, or an organization responsible for creating and/or archiving records.');
	break;

case 'ASSO_1':
	$title = GedcomTag::getLabel('ASSO');
	$text = I18N::translate('An associate is another individual who was involved with this individual, such as a friend or an employer.');
	break;

case 'ASSO_2':
	$title = GedcomTag::getLabel('ASSO');
	$text = I18N::translate('An associate is another individual who was involved with this fact or event, such as a witness or a priest.');
	break;

case 'CAUS':
	$title = GedcomTag::getLabel('CAUS');
	$text = I18N::translate('A description of the cause of the associated event or fact, such as the cause of death.');
	break;

case 'DATE':
	$title = GedcomTag::getLabel('DATE');
	$dates = array(
		'1900'                     =>new Date('1900'),
		'JAN 1900'                 =>new Date('JAN 1900'),
		'FEB 1900'                 =>new Date('FEB 1900'),
		'MAR 1900'                 =>new Date('MAR 1900'),
		'APR 1900'                 =>new Date('APR 1900'),
		'MAY 1900'                 =>new Date('MAY 1900'),
		'JUN 1900'                 =>new Date('JUN 1900'),
		'JUL 1900'                 =>new Date('JUL 1900'),
		'AUG 1900'                 =>new Date('AUG 1900'),
		'SEP 1900'                 =>new Date('SEP 1900'),
		'OCT 1900'                 =>new Date('OCT 1900'),
		'NOV 1900'                 =>new Date('NOV 1900'),
		'DEC 1900'                 =>new Date('DEC 1900'),
		'11 DEC 1913'              =>new Date('11 DEC 1913'),
		'01 FEB 2003'              =>new Date('01 FEB 2003'),
		'ABT 1900'                 =>new Date('ABT 1900'),
		'EST 1900'                 =>new Date('EST 1900'),
		'CAL 1900'                 =>new Date('CAL 1900'),
		'INT 1900 (...)'           =>new Date('INT 1900 (...)'),
		'@#DJULIAN@ 44 B.C.'       =>new Date('@#DJULIAN@ 44 B.C.'),
		'@#DJULIAN@ 14 JAN 1700'   =>new Date('@#DJULIAN@ 14 JAN 1700'),
		'BET @#DJULIAN@ 01 SEP 1752 AND @#DGREGORIAN@ 30 SEP 1752'   =>new Date('BET @#DJULIAN@ 01 SEP 1752 AND @#DGREGORIAN@ 30 SEP 1752'),
		'@#DJULIAN@ 20 FEB 1742/43'=>new Date('@#DJULIAN@ 20 FEB 1742/43'),
		'FROM 1900 TO 1910'        =>new Date('FROM 1900 TO 1910'),
		'FROM 1900'                =>new Date('FROM 1900'),
		'TO 1910'                  =>new Date('TO 1910'),
		'BET 1900 AND 1910'        =>new Date('BET 1900 AND 1910'),
		'BET JAN 1900 AND MAR 1900'=>new Date('BET JAN 1900 AND MAR 1900'),
		'BET APR 1900 AND JUN 1900'=>new Date('BET APR 1900 AND JUN 1900'),
		'BET JUL 1900 AND SEP 1900'=>new Date('BET JUL 1900 AND SEP 1900'),
		'BET OCT 1900 AND DEC 1900'=>new Date('BET OCT 1900 AND DEC 1900'),
		'AFT 1900'                 =>new Date('AFT 1900'),
		'BEF 1910'                 =>new Date('BEF 1910'),
		// Hijri dates
		'@#DHIJRI@ 1497'           =>new Date('@#DHIJRI@ 1497'),
		'@#DHIJRI@ MUHAR 1497'     =>new Date('@#DHIJRI@ MUHAR 1497'),
		'ABT @#DHIJRI@ SAFAR 1497' =>new Date('ABT @#DHIJRI@ SAFAR 1497'),
		'BET @#DHIJRI@ RABIA 1497 AND @#DHIJRI@ RABIT 1497'=>new Date('BET @#DHIJRI@ RABIA 1497 AND @#DHIJRI@ RABIT 1497'),
		'FROM @#DHIJRI@ JUMAA 1497 TO @#DHIJRI@ JUMAT 1497'=>new Date('FROM @#DHIJRI@ JUMAA 1497 TO @#DHIJRI@ JUMAT 1497'),
		'AFT @#DHIJRI@ RAJAB 1497' =>new Date('AFT @#DHIJRI@ RAJAB 1497'),
		'BEF @#DHIJRI@ SHAAB 1497' =>new Date('BEF @#DHIJRI@ SHAAB 1497'),
		'ABT @#DHIJRI@ RAMAD 1497' =>new Date('ABT @#DHIJRI@ RAMAD 1497'),
		'FROM @#DHIJRI@ SHAWW 1497'=>new Date('FROM @#DHIJRI@ SHAWW 1497'),
		'TO @#DHIJRI@ DHUAQ 1497'  =>new Date('TO @#DHIJRI@ DHUAQ 1497'),
		'@#DHIJRI@ 03 DHUAH 1497'  =>new Date('@#DHIJRI@ 03 DHUAH 1497'),
		// French dates
		'@#DFRENCH R@ 12'          =>new Date('@#DFRENCH R@ 12'),
		'@#DFRENCH R@ VEND 12'     =>new Date('@#DFRENCH R@ VEND 12'),
		'ABT @#DFRENCH R@ BRUM 12' =>new Date('ABT @#DFRENCH R@ BRUM 12'),
		'BET @#DFRENCH R@ FRIM 12 AND @#DFRENCH R@ NIVO 12'=>new Date('BET @#DFRENCH R@ FRIM 12 AND @#DFRENCH R@ NIVO 12'),
		'FROM @#DFRENCH R@ PLUV 12 TO @#DFRENCH R@ VENT 12'=>new Date('FROM @#DFRENCH R@ PLUV 12 TO @#DFRENCH R@ VENT 12'),
		'AFT @#DFRENCH R@ GERM 12' =>new Date('AFT @#DFRENCH R@ GERM 12'),
		'BEF @#DFRENCH R@ FLOR 12' =>new Date('BEF @#DFRENCH R@ FLOR 12'),
		'ABT @#DFRENCH R@ PRAI 12' =>new Date('ABT @#DFRENCH R@ PRAI 12'),
		'FROM @#DFRENCH R@ MESS 12'=>new Date('FROM @#DFRENCH R@ MESS 12'),
		'TO @#DFRENCH R@ THER 12'  =>new Date('TO @#DFRENCH R@ THER 12'),
		'EST @#DFRENCH R@ FRUC 12' =>new Date('EST @#DFRENCH R@ FRUC 12'),
		'@#DFRENCH R@ 03 COMP 12'  =>new Date('@#DFRENCH R@ 03 COMP 12'),
		// Jewish dates
		'@#DHEBREW@ 5481'          =>new Date('@#DHEBREW@ 5481'),
		'@#DHEBREW@ TSH 5481'      =>new Date('@#DHEBREW@ TSH 5481'),
		'ABT @#DHEBREW@ CSH 5481'  =>new Date('ABT @#DHEBREW@ CSH 5481'),
		'BET @#DHEBREW@ KSL 5481 AND @#DHEBREW@ TVT 5481'=>new Date('BET @#DHEBREW@ KSL 5481 AND @#DHEBREW@ TVT 5481'),
		'FROM @#DHEBREW@ SHV 5481 TO @#DHEBREW@ ADR 5481'=>new Date('FROM @#DHEBREW@ SHV 5481 TO @#DHEBREW@ ADR 5481'),
		'AFT @#DHEBREW@ ADR 5481'  =>new Date('AFT @#DHEBREW@ ADR 5481'),
		'AFT @#DHEBREW@ ADS 5480'  =>new Date('AFT @#DHEBREW@ ADS 5480'),
		'BEF @#DHEBREW@ NSN 5481'  =>new Date('BEF @#DHEBREW@ NSN 5481'),
		'ABT @#DHEBREW@ IYR 5481'  =>new Date('ABT @#DHEBREW@ IYR 5481'),
		'FROM @#DHEBREW@ SVN 5481' =>new Date('FROM @#DHEBREW@ SVN 5481'),
		'TO @#DHEBREW@ TMZ 5481'   =>new Date('TO @#DHEBREW@ TMZ 5481'),
		'EST @#DHEBREW@ AAV 5481'  =>new Date('EST @#DHEBREW@ AAV 5481'),
		'@#DHEBREW@ 03 ELL 5481'   =>new Date('@#DHEBREW@ 03 ELL 5481'),
	);

	foreach ($dates as &$date) {
		$date = strip_tags($date->display(false, null, false));
	}
	// These shortcuts work differently for different languages
	switch (preg_replace('/[^DMY]/', '', str_replace(array('J', 'F'), array('D', 'M'), strtoupper($DATE_FORMAT)))) {
	case 'YMD':
		$example1 = '11/12/1913'; // Note: we ignore the DMY order if it doesn't make sense.
		$example2 = '03/02/01';
		break;
	case 'MDY':
		$example1 = '12/11/1913';
		$example2 = '02/01/03';
		break;
	case 'DMY':
	default:
		$example1 = '11/12/1913';
		$example2 = '01/02/03';
		break;
	}
	$example1 .= '<br>' . str_replace('/', '-', $example1) . '<br>' . str_replace('/', '.', $example1);
	$example2 .= '<br>' . str_replace('/', '-', $example2) . '<br>' . str_replace('/', '.', $example2);
	$text =
		'<p>' . I18N::translate('Dates are stored using English abbreviations and keywords.  Shortcuts are available as alternatives to these abbreviations and keywords.') . '</p>' .
		'<table border="1">' .
		'<tr><th>' . I18N::translate('Date') . '</th><th>' . I18N::translate('Format') . '</th><th>' . I18N::translate('Shortcut') . '</th></tr>' .
		'<tr><td>' . $dates['1900'] . '</td><td><tt dir="ltr" lang="en">1900</tt></td><td></td></tr>' .
		'<tr><td>' . $dates['JAN 1900'] . '<br>' . $dates['FEB 1900'] . '<br>' . $dates['MAR 1900'] . '<br>' . $dates['APR 1900'] . '<br>' . $dates['MAY 1900'] . '<br>' . $dates['JUN 1900'] . '<br>' . $dates['JUL 1900'] . '<br>' . $dates['AUG 1900'] . '<br>' . $dates['SEP 1900'] . '<br>' . $dates['OCT 1900'] . '<br>' . $dates['NOV 1900'] . '<br>' . $dates['DEC 1900'] . '</td><td><tt dir="ltr" lang="en">JAN 1900<br>FEB 1900<br>MAR 1900<br>APR 1900<br>MAY 1900<br>JUN 1900<br>JUL 1900<br>AUG 1900<br>SEP 1900<br>OCT 1900<br>NOV 1900<br>DEC 1900</tt></td><td></td></tr>' .
		'<tr><td>' . $dates['11 DEC 1913'] . '</td><td><tt dir="ltr" lang="en">11 DEC 1913</tt></td><td><tt dir="ltr" lang="en">' . $example1 . '</tt></td></tr>' .
		'<tr><td>' . $dates['01 FEB 2003'] . '</td><td><tt dir="ltr" lang="en">01 FEB 2003</tt></td><td><tt dir="ltr" lang="en">' . $example2 . '</tt></td></tr>' .
		'<tr><td>' . $dates['ABT 1900'] . '</td><td><tt dir="ltr" lang="en">ABT 1900</tt></td><td><tt dir="ltr" lang="en">~1900</tt></td></tr>' .
		'<tr><td>' . $dates['EST 1900'] . '</td><td><tt dir="ltr" lang="en">EST 1900</tt></td><td><tt dir="ltr" lang="en">*1900</tt></td></tr>' .
		'<tr><td>' . $dates['CAL 1900'] . '</td><td><tt dir="ltr" lang="en">CAL 1900</tt></td><td><tt dir="ltr" lang="en">#1900</tt></td></tr>' .
		'<tr><td>' . $dates['INT 1900 (...)'] . '</td><td><tt dir="ltr" lang="en">INT 1900 (...)</tt></td><td></td></tr>' .
		'</table>' .
		'<p>' . I18N::translate('Date ranges are used to indicate that an event, such as a birth, happened on an unknown date within a possible range.') . '</p>' .
		'<table border="1">' .
		'<tr><th>' . I18N::translate('Date range') . '</th><th>' . I18N::translate('Format') . '</th><th>' . I18N::translate('Shortcut') . '</th></tr>' .
		'<tr><td>' . $dates['BET 1900 AND 1910'] . '</td><td><tt dir="ltr" lang="en">BET 1900 AND 1910</tt></td><td><tt dir="ltr" lang="en">1900-1910</tt></td></tr>' .
		'<tr><td>' . $dates['AFT 1900'] . '</td><td><tt dir="ltr" lang="en">AFT 1900</tt></td><td><tt dir="ltr" lang="en">&gt;1900</tt></td></tr>' .
		'<tr><td>' . $dates['BEF 1910'] . '</td><td><tt dir="ltr" lang="en">BEF 1910</tt></td><td><tt dir="ltr" lang="en">&lt;1910</tt></td></tr>' .
		'<tr><td>' . $dates['BET JAN 1900 AND MAR 1900'] . '</td><td><tt dir="ltr" lang="en">BET JAN 1900 AND MAR 1900</tt></td><td><tt dir="ltr" lang="en">Q1 1900</tt></td></tr>' .
		'<tr><td>' . $dates['BET APR 1900 AND JUN 1900'] . '</td><td><tt dir="ltr" lang="en">BET APR 1900 AND JUN 1900</tt></td><td><tt dir="ltr" lang="en">Q2 1900</tt></td></tr>' .
		'<tr><td>' . $dates['BET JUL 1900 AND SEP 1900'] . '</td><td><tt dir="ltr" lang="en">BET JUL 1900 AND SEP 1900</tt></td><td><tt dir="ltr" lang="en">Q3 1900</tt></td></tr>' .
		'<tr><td>' . $dates['BET OCT 1900 AND DEC 1900'] . '</td><td><tt dir="ltr" lang="en">BET OCT 1900 AND DEC 1900</tt></td><td><tt dir="ltr" lang="en">Q4 1900</tt></td></tr>' .
		'</table>' .
		'<p>' . I18N::translate('Date periods are used to indicate that a fact, such as an occupation, continued for a period of time.') . '</p>' .
		'<table border="1">' .
		'<tr><th>' . I18N::translate('Date period') . '</th><th>' . I18N::translate('Format') . '</th><th>' . I18N::translate('Shortcut') . '</th></tr>' .
		'<tr><td>' . $dates['FROM 1900 TO 1910'] . '</td><td><tt dir="ltr" lang="en">FROM 1900 TO 1910</tt></td><td><tt dir="ltr" lang="en">1900~1910</tt></td></tr>' .
		'<tr><td>' . $dates['FROM 1900'] . '</td><td><tt dir="ltr" lang="en">FROM 1900</tt></td><td><tt dir="ltr" lang="en">1900-</tt></td></tr>' .
		'<tr><td>' . $dates['TO 1910'] . '</td><td><tt dir="ltr" lang="en">TO 1910</tt></td><td><tt dir="ltr" lang="en">-1900</tt></td></tr>' .
		'</table>' .
		'<p>' . I18N::translate('Simple dates are assumed to be in the gregorian calendar.  To specify a date in another calendar, add a keyword before the date.  This keyword is optional if the month or year format make the date unambiguous.') . '</p>' .
		'<table border="1">' .
		'<tr><th>' . I18N::translate('Date') . '</th><th>' . I18N::translate('Format') . '</th></tr>' .
		'<tr><td colspan="2" align="center">' . I18N::translate('Julian') . '</td></tr>' .
		'<tr><td>' . $dates['@#DJULIAN@ 14 JAN 1700'] . '</td><td><tt dir="ltr" lang="en">@#DJULIAN@ 14 JAN 1700</tt></td></tr>' .
		'<tr><td>' . $dates['@#DJULIAN@ 44 B.C.'] . '</td><td><tt dir="ltr" lang="en">@#DJULIAN@ 44 B.C.</tt></td></tr>' .
		'<tr><td>' . $dates['@#DJULIAN@ 20 FEB 1742/43'] . '</td><td><tt dir="ltr" lang="en">@#DJULIAN@ 20 FEB 1742/43</tt></td></tr>' .
		'<tr><td>' . $dates['BET @#DJULIAN@ 01 SEP 1752 AND @#DGREGORIAN@ 30 SEP 1752'] . '</td><td><tt dir="ltr" lang="en">BET @#DJULIAN@ 01 SEP 1752 AND @#DGREGORIAN@ 30 SEP 1752</tt></td></tr>' .
		'<tr><td colspan="2" align="center">' . I18N::translate('Jewish') . '</td></tr>' .
		'<tr><td>' . $dates['@#DHEBREW@ 5481'] . '</td><td><tt dir="ltr" lang="en">@#DHEBREW@ 5481</tt></td></tr>' .
		'<tr><td>' . $dates['@#DHEBREW@ TSH 5481'] . '</td><td><tt dir="ltr" lang="en">@#DHEBREW@ TSH 5481</tt></td></tr>' .
		'<tr><td>' . $dates['ABT @#DHEBREW@ CSH 5481'] . '</td><td><tt dir="ltr" lang="en">ABT @#DHEBREW@ CSH 5481</tt></td></tr>' .
		'<tr><td>' . $dates['BET @#DHEBREW@ KSL 5481 AND @#DHEBREW@ TVT 5481'] . '</td><td><tt dir="ltr" lang="en">BET @#DHEBREW@ KSL 5481 AND @#DHEBREW@ TVT 5481</tt></td></tr>' .
		'<tr><td>' . $dates['FROM @#DHEBREW@ SHV 5481 TO @#DHEBREW@ ADR 5481'] . '</td><td><tt dir="ltr" lang="en">FROM @#DHEBREW@ SHV 5481 TO @#DHEBREW@ ADR 5481</tt></td></tr>' .
		'<tr><td>' . $dates['AFT @#DHEBREW@ ADR 5481'] . '</td><td><tt dir="ltr" lang="en">AFT @#DHEBREW@ ADR 5481</tt></td></tr>' .
		'<tr><td>' . $dates['AFT @#DHEBREW@ ADS 5480'] . '</td><td><tt dir="ltr" lang="en">AFT @#DHEBREW@ ADS 5480</tt></td></tr>' .
		'<tr><td>' . $dates['BEF @#DHEBREW@ NSN 5481'] . '</td><td><tt dir="ltr" lang="en">BEF @#DHEBREW@ NSN 5481</tt></td></tr>' .
		'<tr><td>' . $dates['ABT @#DHEBREW@ IYR 5481'] . '</td><td><tt dir="ltr" lang="en">ABT @#DHEBREW@ IYR 5481</tt></td></tr>' .
		'<tr><td>' . $dates['FROM @#DHEBREW@ SVN 5481'] . '</td><td><tt dir="ltr" lang="en">FROM @#DHEBREW@ SVN 5481</tt></td></tr>' .
		'<tr><td>' . $dates['TO @#DHEBREW@ TMZ 5481'] . '</td><td><tt dir="ltr" lang="en">TO @#DHEBREW@ TMZ 5481</tt></td></tr>' .
		'<tr><td>' . $dates['EST @#DHEBREW@ AAV 5481'] . '</td><td><tt dir="ltr" lang="en">EST @#DHEBREW@ AAV 5481</tt></td></tr>' .
		'<tr><td>' . $dates['@#DHEBREW@ 03 ELL 5481'] . '</td><td><tt dir="ltr" lang="en">@#DHEBREW@ 03 ELL 5481</tt></td></tr>' .
		'<tr><td colspan="2" align="center">' . I18N::translate('Hijri') . '</td></tr>' .
		'<tr><td>' . $dates['@#DHIJRI@ 1497'] . '</td><td><tt dir="ltr" lang="en">@#DHIJRI@ 1497</tt></td></tr>' .
		'<tr><td>' . $dates['@#DHIJRI@ MUHAR 1497'] . '</td><td><tt dir="ltr" lang="en">@#DHIJRI@ MUHAR 1497</tt></td></tr>' .
		'<tr><td>' . $dates['ABT @#DHIJRI@ SAFAR 1497'] . '</td><td><tt dir="ltr" lang="en">ABT @#DHIJRI@ SAFAR 1497</tt></td></tr>' .
		'<tr><td>' . $dates['BET @#DHIJRI@ RABIA 1497 AND @#DHIJRI@ RABIT 1497'] . '</td><td><tt dir="ltr" lang="en">BET @#DHIJRI@ RABIA 1497 AND @#DHIJRI@ RABIT 1497</tt></td></tr>' .
		'<tr><td>' . $dates['FROM @#DHIJRI@ JUMAA 1497 TO @#DHIJRI@ JUMAT 1497'] . '</td><td><tt dir="ltr" lang="en">FROM @#DHIJRI@ JUMAA 1497 TO @#DHIJRI@ JUMAT 1497</tt></td></tr>' .
		'<tr><td>' . $dates['AFT @#DHIJRI@ RAJAB 1497'] . '</td><td><tt dir="ltr" lang="en">AFT @#DHIJRI@ RAJAB 1497</tt></td></tr>' .
		'<tr><td>' . $dates['BEF @#DHIJRI@ SHAAB 1497'] . '</td><td><tt dir="ltr" lang="en">BEF @#DHIJRI@ SHAAB 1497</tt></td></tr>' .
		'<tr><td>' . $dates['ABT @#DHIJRI@ RAMAD 1497'] . '</td><td><tt dir="ltr" lang="en">ABT @#DHIJRI@ RAMAD 1497</tt></td></tr>' .
		'<tr><td>' . $dates['FROM @#DHIJRI@ SHAWW 1497'] . '</td><td><tt dir="ltr" lang="en">FROM @#DHIJRI@ SHAWW 1497</tt></td></tr>' .
		'<tr><td>' . $dates['TO @#DHIJRI@ DHUAQ 1497'] . '</td><td><tt dir="ltr" lang="en">TO @#DHIJRI@ DHUAQ 1497</tt></td></tr>' .
		'<tr><td>' . $dates['@#DHIJRI@ 03 DHUAH 1497'] . '</td><td><tt dir="ltr" lang="en">@#DHIJRI@ 03 DHUAH 1497</tt></td></tr>' .
		'<tr><td colspan="2" align="center">' . I18N::translate('French') . '</td></tr>' .
		'<tr><td>' . $dates['@#DFRENCH R@ 12'] . '</td><td><tt dir="ltr" lang="en">@#DFRENCH R@ 12</tt></td></tr>' .
		'<tr><td>' . $dates['@#DFRENCH R@ VEND 12'] . '</td><td><tt dir="ltr" lang="en">@#DFRENCH R@ VEND 12</tt></td></tr>' .
		'<tr><td>' . $dates['ABT @#DFRENCH R@ BRUM 12'] . '</td><td><tt dir="ltr" lang="en">ABT @#DFRENCH R@ BRUM 12</tt></td></tr>' .
		'<tr><td>' . $dates['BET @#DFRENCH R@ FRIM 12 AND @#DFRENCH R@ NIVO 12'] . '</td><td><tt dir="ltr" lang="en">BET @#DFRENCH R@ FRIM 12 AND @#DFRENCH R@ NIVO 12</tt></td></tr>' .
		'<tr><td>' . $dates['FROM @#DFRENCH R@ PLUV 12 TO @#DFRENCH R@ VENT 12'] . '</td><td><tt dir="ltr" lang="en">FROM @#DFRENCH R@ PLUV 12 TO @#DFRENCH R@ VENT 12</tt></td></tr>' .
		'<tr><td>' . $dates['AFT @#DFRENCH R@ GERM 12'] . '</td><td><tt dir="ltr" lang="en">AFT @#DFRENCH R@ GERM 12</tt></td></tr>' .
		'<tr><td>' . $dates['BEF @#DFRENCH R@ FLOR 12'] . '</td><td><tt dir="ltr" lang="en">BEF @#DFRENCH R@ FLOR 12</tt></td></tr>' .
		'<tr><td>' . $dates['ABT @#DFRENCH R@ PRAI 12'] . '</td><td><tt dir="ltr" lang="en">ABT @#DFRENCH R@ PRAI 12</tt></td></tr>' .
		'<tr><td>' . $dates['FROM @#DFRENCH R@ MESS 12'] . '</td><td><tt dir="ltr" lang="en">FROM @#DFRENCH R@ MESS 12</tt></td></tr>' .
		'<tr><td>' . $dates['TO @#DFRENCH R@ THER 12'] . '</td><td><tt dir="ltr" lang="en">TO @#DFRENCH R@ THER 12</tt></td></tr>' .
		'<tr><td>' . $dates['EST @#DFRENCH R@ FRUC 12'] . '</td><td><tt dir="ltr" lang="en">EST @#DFRENCH R@ FRUC 12</tt></td></tr>' .
		'<tr><td>' . $dates['@#DFRENCH R@ 03 COMP 12'] . '</td><td><tt dir="ltr" lang="en">@#DFRENCH R@ 03 COMP 12</tt></td></tr>' .
		'</table>';
	break;

case 'EMAI':
case 'EMAIL':
case 'EMAL':
case '_EMAIL':
	$title = GedcomTag::getLabel('EMAIL');
	$text = I18N::translate('Enter the email address.<br><br>An example email address looks like this: <b>name@hotmail.com</b>  Leave this field blank if you do not want to include an email address.');
	break;

case 'FAX':
	$title = GedcomTag::getLabel('FAX');
	$text = I18N::translate('Enter the FAX number including the country and area code.<br><br>Leave this field blank if you do not want to include a FAX number.  For example, a number in Germany might be +49 25859 56 76 89 and a number in USA or Canada might be +1 888 555-1212.');
	break;

case 'FORM':
	$title = GedcomTag::getLabel('FORM');
	$text = I18N::translate('This is an optional field that can be used to enter the file format of the media object.  Some genealogy programs may look at this field to determine how to handle the item.  However, since media do not transfer across computer systems very well, this field is not very important.');
	break;

// This help text is used for all NAME components
case 'NAME':
	$title = GedcomTag::getLabel('NAME');
	$text =
		'<p>' .
		I18N::translate('The <b>name</b> field contains the individual’s full name, as they would have spelled it or as it was recorded.  This is how it will be displayed on screen.  It uses standard genealogy annotations to identify different parts of the name.') .
		'</p>' .
		'<ul><li>' .
		I18N::translate('The surname is enclosed by slashes: <%s>John Paul /Smith/<%s>', 'span style="color:#0000ff;"', '/span') .
		'</li><li>' .
		I18N::translate('If the surname is unknown, use empty slashes: <%s>Mary //<%s>', 'span style="color:#0000ff;"', '/span') .
		'</li><li>' .
		I18N::translate('If an individual has two separate surnames, both should be enclosed by slashes: <%s>José Antonio /Gómez/ /Iglesias/<%s>', 'span style="color:#0000ff;"', '/span') .
		'</li><li>' .
		I18N::translate('If an individual does not have a surname, no slashes are needed: <%s>Jón Einarsson<%s>', 'span style="color:#0000ff;"', '/span') .
		'</li><li>' .
		I18N::translate('If an individual was not known by their first given name, the preferred name should be indicated with an asterisk: <%s>John Paul* /Smith/<%s>', 'span style="color:#0000ff;"', '/span') .
		'</li><li>' .
		I18N::translate('If an individual was known by a nickname which is not part of their formal name, it should be enclosed by quotation marks.  For example, <%s>John &quot;Nobby&quot; /Clark/<%s>.', 'span style="color:#0000ff;"', '/span') .
		'</li></ul>';
	break;

case 'SURN':
	$title = GedcomTag::getLabel('SURN');
	$text = '<p>' .
		I18N::translate('The <b>surname</b> field contains a name that is used for sorting and grouping.  It can be different to the individual’s actual surname which is always taken from the <b>name</b> field.  This field can be used to sort surnames with or without a prefix (Gogh / van Gogh) and to group spelling variations or inflections (Kowalski / Kowalska).  If an individual needs to be listed under more than one surname, each name should be separated by a comma.') .
		'</p>';
	break;

case 'NOTE':
	$title = GedcomTag::getLabel('NOTE');
	$text = I18N::translate('Notes are free-form text and will appear in the Fact Details section of the page.');
	break;

case 'OBJE':
	$title = GedcomTag::getLabel('OBJE');
	$text =
		'<p>' .
		I18N::translate('A media object is a record in the family tree which contains information about a media file.  This information may include a title, a copyright notice, a transcript, privacy restrictions, etc.  The media file, such as the photo or video, can be stored locally (on this webserver) or remotely (on a different webserver).') .
		'</p>';
	break;

case 'PAGE':
	$title = GedcomTag::getLabel('PAGE');
	$text = I18N::translate('In the citation details field you would enter the page number or other information that might help someone find the information in the source.');
	break;

case 'PEDI':
	$title = GedcomTag::getLabel('PEDI');
	$text = I18N::translate('A child may have more than one set of parents.  The relationship between the child and the parents can be biological, legal, or based on local culture and tradition.  If no pedigree is specified, then a biological relationship will be assumed.');
	break;

case 'PHON':
	$title = GedcomTag::getLabel('PHON');
	$text = I18N::translate('Enter the phone number including the country and area code.<br><br>Leave this field blank if you do not want to include a phone number.  For example, a number in Germany might be +49 25859 56 76 89 and a number in USA or Canada might be +1 888 555-1212.');
	break;

case 'PLAC':
	$title = GedcomTag::getLabel('PLAC');
	$text = I18N::translate('Places should be entered according to the standards for genealogy.  In genealogy, places are recorded with the most specific information about the place first and then working up to the least specific place last, using commas to separate the different place levels.  The level at which you record the place information should represent the levels of government or church where vital records for that place are kept.<br><br>For example, a place like Salt Lake City would be entered as “Salt Lake City, Salt Lake, Utah, USA”.<br><br>Let’s examine each part of this place.  The first part, “Salt Lake City,” is the city or township where the event occurred.  In some countries, there may be municipalities or districts inside a city which are important to note.  In that case, they should come before the city.  The next part, “Salt Lake,” is the county.  “Utah” is the state, and “USA” is the country.  It is important to note each place because genealogy records are kept by the governments of each level.<br><br>If a level of the place is unknown, you should leave a space between the commas.  Suppose, in the example above, you didn’t know the county for Salt Lake City.  You should then record it like this: “Salt Lake City, , Utah, USA”.  Suppose you only know that an individual was born in Utah.  You would enter the information like this: “, , Utah, USA”.  <br><br>You can use the <b>Find Place</b> link to help you find places that already exist in the database.');
	break;

case 'RELA':
	$title = GedcomTag::getLabel('RELA');
	$text = I18N::translate('Select a relationship name from the list.  Selecting <b>Godfather</b> means: <i>This associate is the godfather of the current individual</i>.');
	break;

case 'RESN':
	$title = GedcomTag::getLabel('RESN');
	$text =
		I18N::translate('Restrictions can be added to records and/or facts.  They restrict who can view the data and who can edit it.') .
		'<br><br>' .
		I18N::translate('Note that if a user account is linked to a record, then that user will always be able to view that record.');
	break;

case 'ROMN':
	$title = GedcomTag::getLabel('ROMN');
	$text = I18N::translate('In many cultures it is customary to have a traditional name spelled in the traditional characters and also a romanized version of the name as it would be spelled or pronounced in languages based on the Latin alphabet, such as English.<br><br>If you prefer to use a non-Latin alphabet such as Hebrew, Greek, Russian, Chinese, or Arabic to enter the name in the standard name fields, then you can use this field to enter the same name using the Latin alphabet.  Both versions of the name will appear in lists and charts.<br><br>Although this field is labeled “Romanized”, it is not restricted to containing only characters based on the Latin alphabet.  This might be of use with Japanese names, where three different alphabets may occur.');
	break;

case 'SEX':
	$title = GedcomTag::getLabel('SEX');
	$text = I18N::translate('Choose the appropriate gender from the drop-down list.  The <b>unknown</b> option indicates that the gender is unknown.');
	break;

case 'SHARED_NOTE':
	$title = GedcomTag::getLabel('SHARED_NOTE');
	$text = I18N::translate('Shared notes are free-form text and will appear in the Fact Details section of the page.<br><br>Each shared note can be linked to more than one individual, family, source, or event.');
	break;

case 'SOUR':
	$title = GedcomTag::getLabel('SOUR');
	$text = I18N::translate('This field allows you to change the source record that this fact’s source citation links to.  This field takes a source ID.  Beside the field will be listed the title of the current source ID.  Use the <b>Find ID</b> link to look up the source’s ID number.  To remove the entire citation, make this field blank.');
	break;

case 'STAT':
	$title = GedcomTag::getLabel('STAT');
	$text = I18N::translate('This is an optional status field and is used mostly for LDS ordinances as they are run through the TempleReady program.');
	break;

case 'TEMP':
	$title = GedcomTag::getLabel('TEMP');
	$text = I18N::translate('For LDS ordinances, this field records the temple where it was performed.');
	break;

case 'TEXT':
	$title = GedcomTag::getLabel('TEXT');
	$text = I18N::translate('In this field you would enter the citation text for this source.  Examples of data may be a transcription of the text from the source, or a description of what was in the citation.');
	break;

case 'TIME':
	$title = GedcomTag::getLabel('TIME');
	$text = I18N::translate('Enter the time for this event in 24-hour format with leading zeroes.  Midnight is 00:00.  Examples: 04:50 13:00 20:30.');
	break;

case 'WWW':
	$title = GedcomTag::getLabel('WWW');
	$text = I18N::translate('Enter the URL address including the http://.<br><br>An example URL looks like this: <b>http://www.webtrees.net/</b>.  Leave this field blank if you do not want to include a URL.');
	break;

case '_HEB':
	$title = GedcomTag::getLabel('_HEB');
	$text = I18N::translate('In many cultures it is customary to have a traditional name spelled in the traditional characters and also a romanized version of the name as it would be spelled or pronounced in languages based on the Latin alphabet, such as English.<br><br>If you prefer to use the Latin alphabet to enter the name in the standard name fields, then you can use this field to enter the same name in the non-Latin alphabet such as Greek, Hebrew, Russian, Arabic, or Chinese.  Both versions of the name will appear in lists and charts.<br><br>Although this field is labeled “Hebrew”, it is not restricted to containing only Hebrew characters.');
	break;

case '_PRIM':
	$title = GedcomTag::getLabel('_PRIM');
	$text = I18N::translate('Use this field to signal that this media item is the highlighted or primary item for the individual it is attached to.  The highlighted image is the one that will be used on charts and on the individual’s page.');
	break;

	//////////////////////////////////////////////////////////////////////////////
	// This section contains an entry for every configuration item
	//////////////////////////////////////////////////////////////////////////////

case 'CHECK_MARRIAGE_RELATIONS':
	$title = I18N::translate('Check relationships by marriage');
	$text = I18N::translate('When calculating relationships, this option controls whether webtrees will include spouses/partners as well as blood relatives.');
	break;

case 'MAX_DESCENDANCY_GENERATIONS':
	$title = I18N::translate('Maximum descendancy generations');
	$text = I18N::translate('Set the maximum number of generations to display on descendancy charts.');
	break;

case 'PEDIGREE_LAYOUT':
	$title = /* I18N: A site configuration setting */ I18N::translate('Default pedigree chart layout');
	$text = /* I18N: Help text for the “Default pedigree chart layout” tree configuration setting */ I18N::translate('This option indicates whether the pedigree chart should be generated in landscape or portrait mode.');
	break;

case 'PEDIGREE_SHOW_GENDER':
	$title = I18N::translate('Gender icon on charts');
	$text = I18N::translate('This option controls whether or not to show the individual’s gender icon on charts.<br><br>Since the gender is also indicated by the color of the box, this option doesn’t conceal the gender.  The option simply removes some duplicate information from the box.');
	break;

case 'SHOW_EST_LIST_DATES':
	$title = I18N::translate('Estimated dates for birth and death');
	$text = I18N::translate('This option controls whether or not to show estimated dates for birth and death instead of leaving blanks on individual lists and charts for individuals whose dates are not known.');

	break;

case 'SHOW_MEDIA_DOWNLOAD':
	$title = I18N::translate('Show download link in media viewer');
	$text = I18N::translate('The Media Viewer can show a link which, when clicked, will download the media file to the local PC.<br><br>You may want to hide the download link for security reasons.');
	break;

case 'SHOW_PARENTS_AGE':
	$title = I18N::translate('Show age of parents next to child’s birthdate');
	$text = I18N::translate('This option controls whether or not to show age of father and mother next to child’s birthdate on charts.');
	break;

case 'THUMBNAIL_WIDTH':
	$title = I18N::translate('Width of generated thumbnails');
	$text = I18N::translate('This is the width (in pixels) that the program will use when automatically generating thumbnails.  The default setting is 100.');
	break;

	//////////////////////////////////////////////////////////////////////////////
	// This section contains all the other help items.
	//////////////////////////////////////////////////////////////////////////////

case 'add_facts':
	$title = I18N::translate('Add a fact');
	$text = I18N::translate('Here you can add a fact to the record being edited.<br><br>First choose a fact from the drop-down list, then click the <b>Add</b> button.  All possible facts that you can add to the database are in that drop-down list.');
	$text .= '<br><br>';
	$text .= '<b>' . I18N::translate('Add from clipboard') . '</b>';
	$text .= '<br><br>';
	$text .= I18N::translate('webtrees allows you to copy up to 10 facts, with all their details, to a clipboard.  This clipboard is different from the clippings cart that you can use to export portions of your database.<br><br>You can select any of the facts from the clipboard and copy the selected fact to the individual, family, media, source, or repository record currently being edited.  However, you cannot copy facts of dissimilar record types.  For example, you cannot copy a marriage fact to a source or an individual record since the marriage fact is associated only with family records.<br><br>This is very helpful when entering similar facts, such as census facts, for many individuals or families.');
	break;

case 'add_note':
	// This is a general help text for multiple pages
	$title = I18N::translate('Add a new note');
	$text = I18N::translate('If you have a note to add to this record, this is the place to do so.<br><br>Just click the link, a window will open, and you can type your note.  When you are finished typing, just click the button below the box, close the window, and that’s all.');
	break;

case 'add_shared_note':
	// This is a general help text for multiple pages
	$title = I18N::translate('Add a new shared note');
	$text = I18N::translate('When you click the <b>Add a new shared note</b> link, a new window will open.  You can choose to link to an existing shared note, or you can create a new shared note and at the same time create a link to it.');
	break;

case 'add_source':
	// This is a general help text for multiple pages
	$title = I18N::translate('Add a new source citation');
	$text = I18N::translate('Here you can add a source <b>Citation</b> to this record.<br><br>Just click the link, a window will open, and you can choose the source from the list (Find ID) or create a new source and then add the citation.<br><br>Adding sources is an important part of genealogy because it allows other researchers to verify where you obtained your information.');
	break;

case 'annivers_year_select':
	$title = I18N::translate('Year input box');
	$text = I18N::translate('This input box lets you change that year of the calendar.  Type a year into the box and press <b>Enter</b> to change the calendar to that year.<br><br><b>Advanced features</b> for <b>View year</b><dl><dt><b>More than one year</b></dt><dd>You can search for dates in a range of years.<br><br>Year ranges are <u>inclusive</u>.  This means that the date range extends from 1 January of the first year of the range to 31 December of the last year mentioned.  Here are a few examples of year ranges:<br><br><b>1992-5</b> for all events from 1992 to 1995.<br><b>1972-89</b> for all events from 1972 to 1989.<br><b>1610-759</b> for all events from 1610 to 1759.<br><b>1880-1905</b> for all events from 1880 to 1905.<br><b>880-1105</b> for all events from 880 to 1105.<br><br>To see all the events in a given decade or century, you can use <b>?</b> in place of the final digits.  For example, <b>197?</b> for all events from 1970 to 1979 or <b>16??</b> for all events from 1600 to 1699.<br><br>Selecting a range of years will change the calendar to the year view.</dd></dl>');
	break;

case 'block_move_right':
	$title = I18N::translate('Move list entries');
	$text = I18N::translate('Use these buttons to move an entry from one list to another.<br><br>Highlight the entry to be moved, and then click a button to move or copy that entry in the direction of the arrow.  Use the <b>&raquo;</b> and <b>&laquo;</b> buttons to move the highlighted entry from the leftmost to the rightmost list or vice-versa.  Use the <b>&gt;</b> and <b>&lt;</b> buttons to move the highlighted entry between the Available blocks list and the list to its right or left.<br><br>The entries in the Available Blocks list do not change, regardless of what you do with the Move right and Move left buttons.  This is so because the same block can appear several times on the same page.  The HTML block is a good example of why you might want to do this.');
	break;

case 'block_move_up':
	$title = I18N::translate('Move list entries');
	$text = I18N::translate('Use these buttons to re-arrange the order of the entries within the list.  The blocks will be printed in the order in which they are listed.<br><br>Highlight the entry to be moved, and then click a button to move that entry up or down.');
	break;

case 'download_gedcom':
	$title = I18N::translate('Download family tree');
	$text = I18N::translate('This option will download the family tree to a GEDCOM file on your computer.');
	break;

case 'edit_add_ASSO':
	$title = I18N::translate('Add a new associate');
	$text = I18N::translate('Add a new associate allows you to link a fact with an associated individual in the website.  This is one way in which you might record that someone was the godfather of another individual.');
	break;

case 'edit_add_GEDFact_ASSISTED':
	$title = I18N::translate('GEDFact shared note assistant');
	$text = I18N::translate('Clicking the “+” icon will open the GEDFact shared note assistant window.<br>Specific help will be found there.<br><br>When you click the “save” button, the ID of the shared note will be pasted here.');
	break;

case 'edit_add_NOTE':
	$title = I18N::translate('Add a new note');
	$text = I18N::translate('This section allows you to add a new note to the fact that you are currently editing.  Notes are free-form text and will appear in the Fact Details section of the page.');
	break;

case 'edit_add_SHARED_NOTE':
	$title = I18N::translate('Add a new shared note');
	$text = I18N::translate('Shared notes, like regular notes, are free-form text.  Unlike regular notes, each shared note can be linked to more than one individual, family, source, or fact.<br><br>By clicking the appropriate icon, you can establish a link to an existing shared note or create a new shared note and at the same time link to it.  If a link to an existing shared note has already been established, you can also edit that note’s contents.<br><ul><li><b>Link to an existing shared note</b><div style="padding-left:20px;">If you already know the ID number of the desired shared note, you can enter that number directly into the field.<br><br>When you click the <b>Find shared note</b> icon, you will be able to search the text of all existing shared notes and then choose one of them.  The ID number of the chosen note will be entered into the field automatically.<br><br>You must click the <b>Add</b> button to update the original record.</div><br></li><li><b>Create a new shared note</b><div style="padding-left:20px;">When you click the <b>Create a new shared note</b> icon, a new window will open.  You can enter the text of the new note as you wish.  As with regular notes, you can enter URLs.<br><br>When you click the <b>Save</b> button, you will see a message with the ID number of the newly created shared note.  You should click on this message to close the editing window and also copy that new ID number directly into the ID number field.  If you just close the window, the newly created ID number will not be copied automatically.<br><br>You must click the <b>Add</b> button to update the original record.</div><br></li><li><b>Edit an existing shared note</b><div style="padding-left:20px;">When you click the <b>Edit shared note</b> icon, a new window will open.  You can change the text of the existing shared note as you wish.  As with regular notes, you can enter URLs.<br><br>When you click the <b>Save</b> button, the text of the shared note will be updated.  You can close the window and then click the <b>Save</b> button again.<br><br>When you change the text of a shared note, your change will be reflected in all places to which that shared note is currently linked.  New links that you establish after having made your change will also use the updated text.</div></li></ul>');
	if (Module::getModuleByName('GEDFact_assistant')) {
		$text .= '<p class="warning">' . I18N::translate('You should avoid using the vertical line character “|” in your notes.  It is used internally by webtrees and may cause your note to display incorrectly.') . '</p>';
	}
	break;

case 'edit_add_SOUR':
	$title = I18N::translate('Add a new source citation');
	$text = I18N::translate('This section allows you to add a new source citation to the fact that you are currently editing.<br><br>In the Source field you enter the ID for the source.  Click the “Create a new source” link if you need to enter a new source.  In the citation details field you would enter the page number or other information that might help someone find the information in the source.  In the Text field you would enter the text transcription from the source.');
	break;

case 'edit_edit_raw':
	$title = I18N::translate('Edit raw GEDCOM');
	$text =
		I18N::translate('This page allows you to bypass the usual forms, and edit the underlying data directly.  It is an advanced option, and you should not use it unless you understand the GEDCOM format.  If you make a mistake here, it can be difficult to fix.') .
		'<br><br>' .
		/* I18N: %s is a URL */ I18N::translate('You can download a copy of the GEDCOM specification from %s.', '<a href="http://wiki.webtrees.net/w/images-en/Ged551-5.pdf">http://wiki.webtrees.net/w/images-en/Ged551-5.pdf</a>');
	break;

case 'edit_merge':
	$title = I18N::translate('Merge records');
	$text = I18N::translate('This page will allow you to merge two GEDCOM records from the same GEDCOM file.<br><br>This is useful for individuals who have merged GEDCOMs and now have many individuals, families, and sources that are the same.<br><br>The page consists of three steps.<br><ol><li>You enter two GEDCOM IDs.  The IDs <u>must</u> be of the same type.  You cannot merge an individual and a family or family and source, for example.<br>In the <b>Merge To ID:</b> field enter the ID of the record you want to be the new record after the merge is complete.<br>In the <b>Merge From ID:</b> field enter the ID of the record whose information will be merged into the Merge To ID: record.  This record will be deleted after the Merge.</li><li>You select what facts you want to keep from the two records when they are merged.  Just click the checkboxes next to the ones you want to keep.</li><li>You inspect the results of the merge, just like with all other changes made online.</li></ol>Someone with Accept rights will have to authorize your changes to make them permanent.');
	break;

case 'edit_SOUR_EVEN':
	$title = I18N::translate('Associate events with this source');
	$text = I18N::translate('Each source records specific events, generally for a given date range and for a place jurisdiction.  For example a Census records census events and church records record birth, marriage, and death events.<br><br>Select the events that are recorded by this source from the list of events provided.  The date should be specified in a range format such as <i>FROM 1900 TO 1910</i>.  The place jurisdiction is the name of the lowest jurisdiction that encompasses all lower-level places named in this source.  For example, “Oneida, Idaho, USA” would be used as a source jurisdiction place for events occurring in the various towns within Oneida County.  “Idaho, USA” would be the source jurisdiction place if the events recorded took place not only in Oneida County but also in other counties in Idaho.');
	break;

case 'export_gedcom':
	$title = I18N::translate('Export family tree');
	$text =
		'<p>' .
		I18N::translate('This option will save the family tree to a GEDCOM file on the server.') .
		'</p><p>' .
		/* I18N: %s is a folder name */
		I18N::translate('GEDCOM files are stored in the %s folder.', '<b dir="auto">' . WT_DATA_DIR . '</b>') .
		'</p>';
	break;

case 'fambook_descent':
	$title = I18N::translate('Descendant generations');
	$text = I18N::translate('This value determines the number of descendant generations of the root individual that will be printed in hourglass format.');
	break;

case 'fan_width':
	$title = I18N::translate('Width');
	$text = I18N::translate('Here you can change the diagram width from 50 percent to 300 percent.  At 100 percent the output image is about 640 pixels wide.');
	break;

case 'gedcom_news_archive':
	$title = I18N::translate('View archive');
	$text = I18N::translate('To reduce the height of the News block, the administrator has hidden some articles.  You can reveal these hidden articles by clicking the <b>View archive</b> link.');
	break;

case 'gedcom_news_flag':
	$title = I18N::translate('Limit:');
	$text = I18N::translate('Enter the limiting value here.<br><br>If you have opted to limit the news article display according to age, any article older than the number of days entered here will be hidden from view.  If you have opted to limit the news article display by number, only the specified number of recent articles, ordered by age, will be shown.  The remaining articles will be hidden from view.<br><br>Zeros entered here will disable the limit, causing all news articles to be shown.');
	break;

case 'gedcom_news_limit':
	$title = I18N::translate('Limit display by:');
	$text = I18N::translate('You can limit the number of news articles displayed, thereby reducing the height of the GEDCOM News block.<br><br>This option determines whether any limits should be applied or whether the limit should be according to the age of the article or according to the number of articles.');
	break;

case 'google_chart_surname':
	$title = I18N::translate('Surname');
	$text = I18N::translate('The number of occurrences of the specified name will be shown on the map.  If you leave this field empty, the most common surname will be used.');
	break;

case 'import_gedcom':
	$title = I18N::translate('Import family tree');
	$text =
		'<p>' .
		I18N::translate('This option deletes all the genealogy data in your family tree and replaces it with data from a GEDCOM file on the server.') .
		'</p><p>' .
		/* I18N: %s is a folder name */
		I18N::translate('GEDCOM files are stored in the %s folder.', '<b dir="auto">' . WT_DATA_DIR . '</b>') .
		'</p>';
	break;

case 'include_media':
	$title = I18N::translate('Include media (automatically zips files)');
	$text = I18N::translate('Select this option to include the media files associated with the records in your clippings cart.  Choosing this option will automatically zip the files during download.');
	break;

case 'lifespan_chart':
	$title = I18N::translate('Lifespans');
	$text = I18N::translate('On this chart you can display one or more individuals along a horizontal timeline.  This chart allows you to see how the lives of different individuals overlapped.<br><br>You can add individuals to the chart individually or by family groups by their IDs.  The previous list will be remembered as you add more individuals to the chart.  You can clear the chart at any time with the <b>Clear chart</b> button.<br><br>You can also add individuals to the chart by searching for them by date range or locality.');
	break;

case 'next_path':
	$title = I18N::translate('Find the next relationship path');
	$text = I18N::translate('You can click this button to see whether there is another relationship path between the two individuals.  Previously found paths can be displayed again by clicking the link with the path number.');
	break;

case 'no_update_CHAN':
	$title = I18N::translate('Do not update the “last change” record');
	$text = I18N::translate('Administrators sometimes need to clean up and correct the data submitted by users.  For example, they might need to correct the PLAC location to include the country.  When administrators make such corrections, information about the original change is normally replaced.  This may not be desirable.<br><br>When this option is selected, webtrees will retain the original change information instead of replacing it with that of the current session.  With this option selected, administrators also have the ability to modify or delete the information associated with the original CHAN tag.');
	break;

case 'pending_changes':
	$title = I18N::translate('Pending changes');
	$text =
		'<p>' .
		I18N::translate('When you add, edit, or delete information, the changes are not saved immediately.  Instead, they are kept in a “pending” area.  These pending changes need to be reviewed by a moderator before they are accepted.') .
		'</p><p>' .
		I18N::translate('This process allows the site’s owner to ensure that the new information follows the site’s standards and conventions, has proper source attributions, etc.') .
		'</p><p>' .
		I18N::translate('Pending changes are only shown when your account has permission to edit.  When you log out, you will no longer be able to see them.  Also, pending changes are only shown on certain pages.  For example, they are not shown in lists, reports, or search results.') .
		'</p>';
	if (Auth::isAdmin()) {
		$text .=
			'<p>' .
			I18N::translate('Each user account has an option to “automatically accept changes”.  When this is enabled, any changes made by that user are saved immediately.  Many administrators enable this for their own user account.') .
			'</p>';
	}
	break;

case 'ppp_view_records':
	$title = I18N::translate('View all records');
	$text = I18N::translate('Clicking on this link will show you a list of all of the individuals and families that have events occurring in this place.  When you get to the end of a place hierarchy, which is normally a town or city, the name list will be shown automatically.');
	break;

case 'show_fact_sources':
	$title = I18N::translate('Show all sources');
	$text = I18N::translate('When this option is checked, you can see all source or note records for this individual.  When this option is unchecked, source or note records that are associated with other facts for this individual will not be shown.');
	break;

case 'show_spouse':
	$title = I18N::translate('Show spouses');
	$text = I18N::translate('By default this chart does not show spouses for the descendants because it makes the chart harder to read and understand.  Turning this option on will show spouses on the chart.');
	break;

case 'upload_gedcom':
	$title = I18N::translate('Upload family tree');
	$text = I18N::translate('This option deletes all the genealogy data in your family tree and replaces it with data from a GEDCOM file on your computer.');
	break;

case 'zip':
	$title = I18N::translate('Zip clippings');
	$text = I18N::translate('Select this option as to save your clippings in a ZIP file.  For more information about ZIP files, please visit <a href="http://www.winzip.com" target="_blank">http://www.winzip.com</a>.');
	break;

default:
	$title = I18N::translate('Help');
	$text  = I18N::translate('The help text has not been written for this item.');
	break;
}
// This file is called by a getJSON call so return the data
// in correct format
header('Content-Type: application/json');
echo json_encode(array('title' => $title, 'content' => $text));
