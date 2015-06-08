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
	// Generally, these tags need to be lists explicitly in FunctionsEdit::add_simple_tag()
	//////////////////////////////////////////////////////////////////////////////

case 'DATE':
	$title = GedcomTag::getLabel('DATE');
	$dates = array(
		'1900'                                                       => new Date('1900'),
		'JAN 1900'                                                   => new Date('JAN 1900'),
		'FEB 1900'                                                   => new Date('FEB 1900'),
		'MAR 1900'                                                   => new Date('MAR 1900'),
		'APR 1900'                                                   => new Date('APR 1900'),
		'MAY 1900'                                                   => new Date('MAY 1900'),
		'JUN 1900'                                                   => new Date('JUN 1900'),
		'JUL 1900'                                                   => new Date('JUL 1900'),
		'AUG 1900'                                                   => new Date('AUG 1900'),
		'SEP 1900'                                                   => new Date('SEP 1900'),
		'OCT 1900'                                                   => new Date('OCT 1900'),
		'NOV 1900'                                                   => new Date('NOV 1900'),
		'DEC 1900'                                                   => new Date('DEC 1900'),
		'11 DEC 1913'                                                => new Date('11 DEC 1913'),
		'01 FEB 2003'                                                => new Date('01 FEB 2003'),
		'ABT 1900'                                                   => new Date('ABT 1900'),
		'EST 1900'                                                   => new Date('EST 1900'),
		'CAL 1900'                                                   => new Date('CAL 1900'),
		'INT 1900 (...)'                                             => new Date('INT 1900 (...)'),
		'@#DJULIAN@ 44 B.C.'                                         => new Date('@#DJULIAN@ 44 B.C.'),
		'@#DJULIAN@ 14 JAN 1700'                                     => new Date('@#DJULIAN@ 14 JAN 1700'),
		'BET @#DJULIAN@ 01 SEP 1752 AND @#DGREGORIAN@ 30 SEP 1752'   => new Date('BET @#DJULIAN@ 01 SEP 1752 AND @#DGREGORIAN@ 30 SEP 1752'),
		'@#DJULIAN@ 20 FEB 1742/43'                                  => new Date('@#DJULIAN@ 20 FEB 1742/43'),
		'FROM 1900 TO 1910'                                          => new Date('FROM 1900 TO 1910'),
		'FROM 1900'                                                  => new Date('FROM 1900'),
		'TO 1910'                                                    => new Date('TO 1910'),
		'BET 1900 AND 1910'                                          => new Date('BET 1900 AND 1910'),
		'BET JAN 1900 AND MAR 1900'                                  => new Date('BET JAN 1900 AND MAR 1900'),
		'BET APR 1900 AND JUN 1900'                                  => new Date('BET APR 1900 AND JUN 1900'),
		'BET JUL 1900 AND SEP 1900'                                  => new Date('BET JUL 1900 AND SEP 1900'),
		'BET OCT 1900 AND DEC 1900'                                  => new Date('BET OCT 1900 AND DEC 1900'),
		'AFT 1900'                                                   => new Date('AFT 1900'),
		'BEF 1910'                                                   => new Date('BEF 1910'),
		// Hijri dates
		'@#DHIJRI@ 1497'                                    => new Date('@#DHIJRI@ 1497'),
		'@#DHIJRI@ MUHAR 1497'                              => new Date('@#DHIJRI@ MUHAR 1497'),
		'ABT @#DHIJRI@ SAFAR 1497'                          => new Date('ABT @#DHIJRI@ SAFAR 1497'),
		'BET @#DHIJRI@ RABIA 1497 AND @#DHIJRI@ RABIT 1497' => new Date('BET @#DHIJRI@ RABIA 1497 AND @#DHIJRI@ RABIT 1497'),
		'FROM @#DHIJRI@ JUMAA 1497 TO @#DHIJRI@ JUMAT 1497' => new Date('FROM @#DHIJRI@ JUMAA 1497 TO @#DHIJRI@ JUMAT 1497'),
		'AFT @#DHIJRI@ RAJAB 1497'                          => new Date('AFT @#DHIJRI@ RAJAB 1497'),
		'BEF @#DHIJRI@ SHAAB 1497'                          => new Date('BEF @#DHIJRI@ SHAAB 1497'),
		'ABT @#DHIJRI@ RAMAD 1497'                          => new Date('ABT @#DHIJRI@ RAMAD 1497'),
		'FROM @#DHIJRI@ SHAWW 1497'                         => new Date('FROM @#DHIJRI@ SHAWW 1497'),
		'TO @#DHIJRI@ DHUAQ 1497'                           => new Date('TO @#DHIJRI@ DHUAQ 1497'),
		'@#DHIJRI@ 03 DHUAH 1497'                           => new Date('@#DHIJRI@ 03 DHUAH 1497'),
		// French dates
		'@#DFRENCH R@ 12'                                   => new Date('@#DFRENCH R@ 12'),
		'@#DFRENCH R@ VEND 12'                              => new Date('@#DFRENCH R@ VEND 12'),
		'ABT @#DFRENCH R@ BRUM 12'                          => new Date('ABT @#DFRENCH R@ BRUM 12'),
		'BET @#DFRENCH R@ FRIM 12 AND @#DFRENCH R@ NIVO 12' => new Date('BET @#DFRENCH R@ FRIM 12 AND @#DFRENCH R@ NIVO 12'),
		'FROM @#DFRENCH R@ PLUV 12 TO @#DFRENCH R@ VENT 12' => new Date('FROM @#DFRENCH R@ PLUV 12 TO @#DFRENCH R@ VENT 12'),
		'AFT @#DFRENCH R@ GERM 12'                          => new Date('AFT @#DFRENCH R@ GERM 12'),
		'BEF @#DFRENCH R@ FLOR 12'                          => new Date('BEF @#DFRENCH R@ FLOR 12'),
		'ABT @#DFRENCH R@ PRAI 12'                          => new Date('ABT @#DFRENCH R@ PRAI 12'),
		'FROM @#DFRENCH R@ MESS 12'                         => new Date('FROM @#DFRENCH R@ MESS 12'),
		'TO @#DFRENCH R@ THER 12'                           => new Date('TO @#DFRENCH R@ THER 12'),
		'EST @#DFRENCH R@ FRUC 12'                          => new Date('EST @#DFRENCH R@ FRUC 12'),
		'@#DFRENCH R@ 03 COMP 12'                           => new Date('@#DFRENCH R@ 03 COMP 12'),
		// Jewish dates
		'@#DHEBREW@ 5481'                                 => new Date('@#DHEBREW@ 5481'),
		'@#DHEBREW@ TSH 5481'                             => new Date('@#DHEBREW@ TSH 5481'),
		'ABT @#DHEBREW@ CSH 5481'                         => new Date('ABT @#DHEBREW@ CSH 5481'),
		'BET @#DHEBREW@ KSL 5481 AND @#DHEBREW@ TVT 5481' => new Date('BET @#DHEBREW@ KSL 5481 AND @#DHEBREW@ TVT 5481'),
		'FROM @#DHEBREW@ SHV 5481 TO @#DHEBREW@ ADR 5481' => new Date('FROM @#DHEBREW@ SHV 5481 TO @#DHEBREW@ ADR 5481'),
		'AFT @#DHEBREW@ ADR 5481'                         => new Date('AFT @#DHEBREW@ ADR 5481'),
		'AFT @#DHEBREW@ ADS 5480'                         => new Date('AFT @#DHEBREW@ ADS 5480'),
		'BEF @#DHEBREW@ NSN 5481'                         => new Date('BEF @#DHEBREW@ NSN 5481'),
		'ABT @#DHEBREW@ IYR 5481'                         => new Date('ABT @#DHEBREW@ IYR 5481'),
		'FROM @#DHEBREW@ SVN 5481'                        => new Date('FROM @#DHEBREW@ SVN 5481'),
		'TO @#DHEBREW@ TMZ 5481'                          => new Date('TO @#DHEBREW@ TMZ 5481'),
		'EST @#DHEBREW@ AAV 5481'                         => new Date('EST @#DHEBREW@ AAV 5481'),
		'@#DHEBREW@ 03 ELL 5481'                          => new Date('@#DHEBREW@ 03 ELL 5481'),
	);

	foreach ($dates as &$date) {
		$date = strip_tags($date->display(false, null, false));
	}
	// These shortcuts work differently for different languages
	switch (preg_replace('/[^DMY]/', '', str_replace(array('J', 'F'), array('D', 'M'), I18N::dateFormat()))) {
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
		'<tr><td>' . $dates['1900'] . '</td><td><kbd dir="ltr" lang="en">1900</kbd></td><td></td></tr>' .
		'<tr><td>' . $dates['JAN 1900'] . '<br>' . $dates['FEB 1900'] . '<br>' . $dates['MAR 1900'] . '<br>' . $dates['APR 1900'] . '<br>' . $dates['MAY 1900'] . '<br>' . $dates['JUN 1900'] . '<br>' . $dates['JUL 1900'] . '<br>' . $dates['AUG 1900'] . '<br>' . $dates['SEP 1900'] . '<br>' . $dates['OCT 1900'] . '<br>' . $dates['NOV 1900'] . '<br>' . $dates['DEC 1900'] . '</td><td><kbd dir="ltr" lang="en">JAN 1900<br>FEB 1900<br>MAR 1900<br>APR 1900<br>MAY 1900<br>JUN 1900<br>JUL 1900<br>AUG 1900<br>SEP 1900<br>OCT 1900<br>NOV 1900<br>DEC 1900</kbd></td><td></td></tr>' .
		'<tr><td>' . $dates['11 DEC 1913'] . '</td><td><kbd dir="ltr" lang="en">11 DEC 1913</kbd></td><td><kbd dir="ltr" lang="en">' . $example1 . '</kbd></td></tr>' .
		'<tr><td>' . $dates['01 FEB 2003'] . '</td><td><kbd dir="ltr" lang="en">01 FEB 2003</kbd></td><td><kbd dir="ltr" lang="en">' . $example2 . '</kbd></td></tr>' .
		'<tr><td>' . $dates['ABT 1900'] . '</td><td><kbd dir="ltr" lang="en">ABT 1900</kbd></td><td><kbd dir="ltr" lang="en">~1900</kbd></td></tr>' .
		'<tr><td>' . $dates['EST 1900'] . '</td><td><kbd dir="ltr" lang="en">EST 1900</kbd></td><td><kbd dir="ltr" lang="en">*1900</kbd></td></tr>' .
		'<tr><td>' . $dates['CAL 1900'] . '</td><td><kbd dir="ltr" lang="en">CAL 1900</kbd></td><td><kbd dir="ltr" lang="en">#1900</kbd></td></tr>' .
		'<tr><td>' . $dates['INT 1900 (...)'] . '</td><td><kbd dir="ltr" lang="en">INT 1900 (...)</kbd></td><td></td></tr>' .
		'</table>' .
		'<p>' . I18N::translate('Date ranges are used to indicate that an event, such as a birth, happened on an unknown date within a possible range.') . '</p>' .
		'<table border="1">' .
		'<tr><th>' . I18N::translate('Date range') . '</th><th>' . I18N::translate('Format') . '</th><th>' . I18N::translate('Shortcut') . '</th></tr>' .
		'<tr><td>' . $dates['BET 1900 AND 1910'] . '</td><td><kbd dir="ltr" lang="en">BET 1900 AND 1910</kbd></td><td><kbd dir="ltr" lang="en">1900-1910</kbd></td></tr>' .
		'<tr><td>' . $dates['AFT 1900'] . '</td><td><kbd dir="ltr" lang="en">AFT 1900</kbd></td><td><kbd dir="ltr" lang="en">&gt;1900</kbd></td></tr>' .
		'<tr><td>' . $dates['BEF 1910'] . '</td><td><kbd dir="ltr" lang="en">BEF 1910</kbd></td><td><kbd dir="ltr" lang="en">&lt;1910</kbd></td></tr>' .
		'<tr><td>' . $dates['BET JAN 1900 AND MAR 1900'] . '</td><td><kbd dir="ltr" lang="en">BET JAN 1900 AND MAR 1900</kbd></td><td><kbd dir="ltr" lang="en">Q1 1900</kbd></td></tr>' .
		'<tr><td>' . $dates['BET APR 1900 AND JUN 1900'] . '</td><td><kbd dir="ltr" lang="en">BET APR 1900 AND JUN 1900</kbd></td><td><kbd dir="ltr" lang="en">Q2 1900</kbd></td></tr>' .
		'<tr><td>' . $dates['BET JUL 1900 AND SEP 1900'] . '</td><td><kbd dir="ltr" lang="en">BET JUL 1900 AND SEP 1900</kbd></td><td><kbd dir="ltr" lang="en">Q3 1900</kbd></td></tr>' .
		'<tr><td>' . $dates['BET OCT 1900 AND DEC 1900'] . '</td><td><kbd dir="ltr" lang="en">BET OCT 1900 AND DEC 1900</kbd></td><td><kbd dir="ltr" lang="en">Q4 1900</kbd></td></tr>' .
		'</table>' .
		'<p>' . I18N::translate('Date periods are used to indicate that a fact, such as an occupation, continued for a period of time.') . '</p>' .
		'<table border="1">' .
		'<tr><th>' . I18N::translate('Date period') . '</th><th>' . I18N::translate('Format') . '</th><th>' . I18N::translate('Shortcut') . '</th></tr>' .
		'<tr><td>' . $dates['FROM 1900 TO 1910'] . '</td><td><kbd dir="ltr" lang="en">FROM 1900 TO 1910</kbd></td><td><kbd dir="ltr" lang="en">1900~1910</kbd></td></tr>' .
		'<tr><td>' . $dates['FROM 1900'] . '</td><td><kbd dir="ltr" lang="en">FROM 1900</kbd></td><td><kbd dir="ltr" lang="en">1900-</kbd></td></tr>' .
		'<tr><td>' . $dates['TO 1910'] . '</td><td><kbd dir="ltr" lang="en">TO 1910</kbd></td><td><kbd dir="ltr" lang="en">-1900</kbd></td></tr>' .
		'</table>' .
		'<p>' . I18N::translate('Simple dates are assumed to be in the gregorian calendar.  To specify a date in another calendar, add a keyword before the date.  This keyword is optional if the month or year format make the date unambiguous.') . '</p>' .
		'<table border="1">' .
		'<tr><th>' . I18N::translate('Date') . '</th><th>' . I18N::translate('Format') . '</th></tr>' .
		'<tr><td colspan="2" align="center">' . I18N::translate('Julian') . '</td></tr>' .
		'<tr><td>' . $dates['@#DJULIAN@ 14 JAN 1700'] . '</td><td><kbd dir="ltr" lang="en">@#DJULIAN@ 14 JAN 1700</kbd></td></tr>' .
		'<tr><td>' . $dates['@#DJULIAN@ 44 B.C.'] . '</td><td><kbd dir="ltr" lang="en">@#DJULIAN@ 44 B.C.</kbd></td></tr>' .
		'<tr><td>' . $dates['@#DJULIAN@ 20 FEB 1742/43'] . '</td><td><kbd dir="ltr" lang="en">@#DJULIAN@ 20 FEB 1742/43</kbd></td></tr>' .
		'<tr><td>' . $dates['BET @#DJULIAN@ 01 SEP 1752 AND @#DGREGORIAN@ 30 SEP 1752'] . '</td><td><kbd dir="ltr" lang="en">BET @#DJULIAN@ 01 SEP 1752 AND @#DGREGORIAN@ 30 SEP 1752</kbd></td></tr>' .
		'<tr><td colspan="2" align="center">' . I18N::translate('Jewish') . '</td></tr>' .
		'<tr><td>' . $dates['@#DHEBREW@ 5481'] . '</td><td><kbd dir="ltr" lang="en">@#DHEBREW@ 5481</kbd></td></tr>' .
		'<tr><td>' . $dates['@#DHEBREW@ TSH 5481'] . '</td><td><kbd dir="ltr" lang="en">@#DHEBREW@ TSH 5481</kbd></td></tr>' .
		'<tr><td>' . $dates['ABT @#DHEBREW@ CSH 5481'] . '</td><td><kbd dir="ltr" lang="en">ABT @#DHEBREW@ CSH 5481</kbd></td></tr>' .
		'<tr><td>' . $dates['BET @#DHEBREW@ KSL 5481 AND @#DHEBREW@ TVT 5481'] . '</td><td><kbd dir="ltr" lang="en">BET @#DHEBREW@ KSL 5481 AND @#DHEBREW@ TVT 5481</kbd></td></tr>' .
		'<tr><td>' . $dates['FROM @#DHEBREW@ SHV 5481 TO @#DHEBREW@ ADR 5481'] . '</td><td><kbd dir="ltr" lang="en">FROM @#DHEBREW@ SHV 5481 TO @#DHEBREW@ ADR 5481</kbd></td></tr>' .
		'<tr><td>' . $dates['AFT @#DHEBREW@ ADR 5481'] . '</td><td><kbd dir="ltr" lang="en">AFT @#DHEBREW@ ADR 5481</kbd></td></tr>' .
		'<tr><td>' . $dates['AFT @#DHEBREW@ ADS 5480'] . '</td><td><kbd dir="ltr" lang="en">AFT @#DHEBREW@ ADS 5480</kbd></td></tr>' .
		'<tr><td>' . $dates['BEF @#DHEBREW@ NSN 5481'] . '</td><td><kbd dir="ltr" lang="en">BEF @#DHEBREW@ NSN 5481</kbd></td></tr>' .
		'<tr><td>' . $dates['ABT @#DHEBREW@ IYR 5481'] . '</td><td><kbd dir="ltr" lang="en">ABT @#DHEBREW@ IYR 5481</kbd></td></tr>' .
		'<tr><td>' . $dates['FROM @#DHEBREW@ SVN 5481'] . '</td><td><kbd dir="ltr" lang="en">FROM @#DHEBREW@ SVN 5481</kbd></td></tr>' .
		'<tr><td>' . $dates['TO @#DHEBREW@ TMZ 5481'] . '</td><td><kbd dir="ltr" lang="en">TO @#DHEBREW@ TMZ 5481</kbd></td></tr>' .
		'<tr><td>' . $dates['EST @#DHEBREW@ AAV 5481'] . '</td><td><kbd dir="ltr" lang="en">EST @#DHEBREW@ AAV 5481</kbd></td></tr>' .
		'<tr><td>' . $dates['@#DHEBREW@ 03 ELL 5481'] . '</td><td><kbd dir="ltr" lang="en">@#DHEBREW@ 03 ELL 5481</kbd></td></tr>' .
		'<tr><td colspan="2" align="center">' . I18N::translate('Hijri') . '</td></tr>' .
		'<tr><td>' . $dates['@#DHIJRI@ 1497'] . '</td><td><kbd dir="ltr" lang="en">@#DHIJRI@ 1497</kbd></td></tr>' .
		'<tr><td>' . $dates['@#DHIJRI@ MUHAR 1497'] . '</td><td><kbd dir="ltr" lang="en">@#DHIJRI@ MUHAR 1497</kbd></td></tr>' .
		'<tr><td>' . $dates['ABT @#DHIJRI@ SAFAR 1497'] . '</td><td><kbd dir="ltr" lang="en">ABT @#DHIJRI@ SAFAR 1497</kbd></td></tr>' .
		'<tr><td>' . $dates['BET @#DHIJRI@ RABIA 1497 AND @#DHIJRI@ RABIT 1497'] . '</td><td><kbd dir="ltr" lang="en">BET @#DHIJRI@ RABIA 1497 AND @#DHIJRI@ RABIT 1497</kbd></td></tr>' .
		'<tr><td>' . $dates['FROM @#DHIJRI@ JUMAA 1497 TO @#DHIJRI@ JUMAT 1497'] . '</td><td><kbd dir="ltr" lang="en">FROM @#DHIJRI@ JUMAA 1497 TO @#DHIJRI@ JUMAT 1497</kbd></td></tr>' .
		'<tr><td>' . $dates['AFT @#DHIJRI@ RAJAB 1497'] . '</td><td><kbd dir="ltr" lang="en">AFT @#DHIJRI@ RAJAB 1497</kbd></td></tr>' .
		'<tr><td>' . $dates['BEF @#DHIJRI@ SHAAB 1497'] . '</td><td><kbd dir="ltr" lang="en">BEF @#DHIJRI@ SHAAB 1497</kbd></td></tr>' .
		'<tr><td>' . $dates['ABT @#DHIJRI@ RAMAD 1497'] . '</td><td><kbd dir="ltr" lang="en">ABT @#DHIJRI@ RAMAD 1497</kbd></td></tr>' .
		'<tr><td>' . $dates['FROM @#DHIJRI@ SHAWW 1497'] . '</td><td><kbd dir="ltr" lang="en">FROM @#DHIJRI@ SHAWW 1497</kbd></td></tr>' .
		'<tr><td>' . $dates['TO @#DHIJRI@ DHUAQ 1497'] . '</td><td><kbd dir="ltr" lang="en">TO @#DHIJRI@ DHUAQ 1497</kbd></td></tr>' .
		'<tr><td>' . $dates['@#DHIJRI@ 03 DHUAH 1497'] . '</td><td><kbd dir="ltr" lang="en">@#DHIJRI@ 03 DHUAH 1497</kbd></td></tr>' .
		'<tr><td colspan="2" align="center">' . I18N::translate('French') . '</td></tr>' .
		'<tr><td>' . $dates['@#DFRENCH R@ 12'] . '</td><td><kbd dir="ltr" lang="en">@#DFRENCH R@ 12</kbd></td></tr>' .
		'<tr><td>' . $dates['@#DFRENCH R@ VEND 12'] . '</td><td><kbd dir="ltr" lang="en">@#DFRENCH R@ VEND 12</kbd></td></tr>' .
		'<tr><td>' . $dates['ABT @#DFRENCH R@ BRUM 12'] . '</td><td><kbd dir="ltr" lang="en">ABT @#DFRENCH R@ BRUM 12</kbd></td></tr>' .
		'<tr><td>' . $dates['BET @#DFRENCH R@ FRIM 12 AND @#DFRENCH R@ NIVO 12'] . '</td><td><kbd dir="ltr" lang="en">BET @#DFRENCH R@ FRIM 12 AND @#DFRENCH R@ NIVO 12</kbd></td></tr>' .
		'<tr><td>' . $dates['FROM @#DFRENCH R@ PLUV 12 TO @#DFRENCH R@ VENT 12'] . '</td><td><kbd dir="ltr" lang="en">FROM @#DFRENCH R@ PLUV 12 TO @#DFRENCH R@ VENT 12</kbd></td></tr>' .
		'<tr><td>' . $dates['AFT @#DFRENCH R@ GERM 12'] . '</td><td><kbd dir="ltr" lang="en">AFT @#DFRENCH R@ GERM 12</kbd></td></tr>' .
		'<tr><td>' . $dates['BEF @#DFRENCH R@ FLOR 12'] . '</td><td><kbd dir="ltr" lang="en">BEF @#DFRENCH R@ FLOR 12</kbd></td></tr>' .
		'<tr><td>' . $dates['ABT @#DFRENCH R@ PRAI 12'] . '</td><td><kbd dir="ltr" lang="en">ABT @#DFRENCH R@ PRAI 12</kbd></td></tr>' .
		'<tr><td>' . $dates['FROM @#DFRENCH R@ MESS 12'] . '</td><td><kbd dir="ltr" lang="en">FROM @#DFRENCH R@ MESS 12</kbd></td></tr>' .
		'<tr><td>' . $dates['TO @#DFRENCH R@ THER 12'] . '</td><td><kbd dir="ltr" lang="en">TO @#DFRENCH R@ THER 12</kbd></td></tr>' .
		'<tr><td>' . $dates['EST @#DFRENCH R@ FRUC 12'] . '</td><td><kbd dir="ltr" lang="en">EST @#DFRENCH R@ FRUC 12</kbd></td></tr>' .
		'<tr><td>' . $dates['@#DFRENCH R@ 03 COMP 12'] . '</td><td><kbd dir="ltr" lang="en">@#DFRENCH R@ 03 COMP 12</kbd></td></tr>' .
		'</table>';
	break;

// This help text is used for all NAME components
case 'NAME':
	$title = GedcomTag::getLabel('NAME');
	$text  =
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
	$text  = '<p>' .
		I18N::translate('The <b>surname</b> field contains a name that is used for sorting and grouping.  It can be different to the individual’s actual surname which is always taken from the <b>name</b> field.  This field can be used to sort surnames with or without a prefix (Gogh / van Gogh) and to group spelling variations or inflections (Kowalski / Kowalska).  If an individual needs to be listed under more than one surname, each name should be separated by a comma.') .
		'</p>';
	break;

case 'OBJE':
	$title = GedcomTag::getLabel('OBJE');
	$text  =
		'<p>' .
		I18N::translate('A media object is a record in the family tree which contains information about a media file.  This information may include a title, a copyright notice, a transcript, privacy restrictions, etc.  The media file, such as the photo or video, can be stored locally (on this webserver) or remotely (on a different webserver).') .
		'</p>';
	break;

case 'PLAC':
	$title = GedcomTag::getLabel('PLAC');
	$text  = I18N::translate('Places should be entered according to the standards for genealogy.  In genealogy, places are recorded with the most specific information about the place first and then working up to the least specific place last, using commas to separate the different place levels.  The level at which you record the place information should represent the levels of government or church where vital records for that place are kept.<br><br>For example, a place like Salt Lake City would be entered as “Salt Lake City, Salt Lake, Utah, USA”.<br><br>Let’s examine each part of this place.  The first part, “Salt Lake City,” is the city or township where the event occurred.  In some countries, there may be municipalities or districts inside a city which are important to note.  In that case, they should come before the city.  The next part, “Salt Lake,” is the county.  “Utah” is the state, and “USA” is the country.  It is important to note each place because genealogy records are kept by the governments of each level.<br><br>If a level of the place is unknown, you should leave a space between the commas.  Suppose, in the example above, you didn’t know the county for Salt Lake City.  You should then record it like this: “Salt Lake City, , Utah, USA”.  Suppose you only know that an individual was born in Utah.  You would enter the information like this: “, , Utah, USA”.  <br><br>You can use the <b>Find Place</b> link to help you find places that already exist in the database.');
	break;

case 'RESN':
	$title = GedcomTag::getLabel('RESN');
	$text  =
		I18N::translate('Restrictions can be added to records and/or facts.  They restrict who can view the data and who can edit it.') .
		'<br><br>' .
		I18N::translate('Note that if a user account is linked to a record, then that user will always be able to view that record.');
	break;

case 'ROMN':
	$title = GedcomTag::getLabel('ROMN');
	$text  = I18N::translate('In many cultures it is customary to have a traditional name spelled in the traditional characters and also a romanized version of the name as it would be spelled or pronounced in languages based on the Latin alphabet, such as English.<br><br>If you prefer to use a non-Latin alphabet such as Hebrew, Greek, Russian, Chinese, or Arabic to enter the name in the standard name fields, then you can use this field to enter the same name using the Latin alphabet.  Both versions of the name will appear in lists and charts.<br><br>Although this field is labeled “Romanized”, it is not restricted to containing only characters based on the Latin alphabet.  This might be of use with Japanese names, where three different alphabets may occur.');
	break;

case '_HEB':
	$title = GedcomTag::getLabel('_HEB');
	$text  = I18N::translate('In many cultures it is customary to have a traditional name spelled in the traditional characters and also a romanized version of the name as it would be spelled or pronounced in languages based on the Latin alphabet, such as English.<br><br>If you prefer to use the Latin alphabet to enter the name in the standard name fields, then you can use this field to enter the same name in the non-Latin alphabet such as Greek, Hebrew, Russian, Arabic, or Chinese.  Both versions of the name will appear in lists and charts.<br><br>Although this field is labeled “Hebrew”, it is not restricted to containing only Hebrew characters.');
	break;

	//////////////////////////////////////////////////////////////////////////////
	// This section contains all the other help items.
	//////////////////////////////////////////////////////////////////////////////

case 'annivers_year_select':
	$title = I18N::translate('Year input box');
	$text  = I18N::translate('This input box lets you change that year of the calendar.  Type a year into the box and press <b>Enter</b> to change the calendar to that year.<br><br><b>Advanced features</b> for <b>View year</b><dl><dt><b>More than one year</b></dt><dd>You can search for dates in a range of years.<br><br>Year ranges are <u>inclusive</u>.  This means that the date range extends from 1 January of the first year of the range to 31 December of the last year mentioned.  Here are a few examples of year ranges:<br><br><b>1992-5</b> for all events from 1992 to 1995.<br><b>1972-89</b> for all events from 1972 to 1989.<br><b>1610-759</b> for all events from 1610 to 1759.<br><b>1880-1905</b> for all events from 1880 to 1905.<br><b>880-1105</b> for all events from 880 to 1105.<br><br>To see all the events in a given decade or century, you can use <b>?</b> in place of the final digits.  For example, <b>197?</b> for all events from 1970 to 1979 or <b>16??</b> for all events from 1600 to 1699.<br><br>Selecting a range of years will change the calendar to the year view.</dd></dl>');
	break;

case 'edit_edit_raw':
	$title = I18N::translate('Edit raw GEDCOM');
	$text  =
		I18N::translate('This page allows you to bypass the usual forms, and edit the underlying data directly.  It is an advanced option, and you should not use it unless you understand the GEDCOM format.  If you make a mistake here, it can be difficult to fix.') .
		'<br><br>' .
		/* I18N: %s is a URL */ I18N::translate('You can download a copy of the GEDCOM specification from %s.', '<a href="http://wiki.webtrees.net/w/images-en/Ged551-5.pdf">http://wiki.webtrees.net/w/images-en/Ged551-5.pdf</a>');
	break;

case 'edit_SOUR_EVEN':
	$title = I18N::translate('Associate events with this source');
	$text  = I18N::translate('Each source records specific events, generally for a given date range and for a place jurisdiction.  For example a Census records census events and church records record birth, marriage, and death events.<br><br>Select the events that are recorded by this source from the list of events provided.  The date should be specified in a range format such as <i>FROM 1900 TO 1910</i>.  The place jurisdiction is the name of the lowest jurisdiction that encompasses all lower-level places named in this source.  For example, “Oneida, Idaho, USA” would be used as a source jurisdiction place for events occurring in the various towns within Oneida County.  “Idaho, USA” would be the source jurisdiction place if the events recorded took place not only in Oneida County but also in other counties in Idaho.');
	break;

case 'gedcom_news_archive':
	$title = I18N::translate('View archive');
	$text  = I18N::translate('To reduce the height of the News block, the administrator has hidden some articles.  You can reveal these hidden articles by clicking the <b>View archive</b> link.');
	break;

case 'google_chart_surname':
	$title = I18N::translate('Surname');
	$text  = I18N::translate('The number of occurrences of the specified name will be shown on the map.  If you leave this field empty, the most common surname will be used.');
	break;

case 'pending_changes':
	$title = I18N::translate('Pending changes');
	$text  =
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

default:
	$title = I18N::translate('Help');
	$text  = I18N::translate('The help text has not been written for this item.');
	break;
}
// This file is called by a getJSON call so return the data
// in correct format
header('Content-Type: application/json');
echo json_encode(array('title' => $title, 'content' => $text));
