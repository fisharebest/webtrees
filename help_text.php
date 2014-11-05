<?php
// Show help text in a popup window.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// This file also serves as a database of fact and label descriptions,
// allowing them to be discovered by xgettext, so we may use them dynamically
// in the rest of the code.
// Help links are generated using help_link('help_topic')
//
// Help text for modules belongs in WT_MODULES_DIR/XXX/help_text.php
// Module help links are generated using help_link('help_topic', 'module')
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;

define('WT_SCRIPT_NAME', 'help_text.php');
require './includes/session.php';

$help = WT_Filter::get('help');
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
	$title=WT_Gedcom_Tag::getLabel('ADDR');
	$text=WT_I18N::translate('Enter the address into the field just as you would write it on an envelope.<br><br>Leave this field blank if you do not want to include an address.');
	break;

case 'AGNC':
	$title=WT_Gedcom_Tag::getLabel('AGNC');
	$text=WT_I18N::translate('The organization, institution, corporation, individual, or other entity that has authority.<br><br>For example, an employer of an individual, or a church that administered rites or events, or an organization responsible for creating and/or archiving records.');
	break;

case 'ASSO_1':
	$title=WT_Gedcom_Tag::getLabel('ASSO');
	$text=WT_I18N::translate('An associate is another individual who was involved with this individual, such as a friend or an employer.');
	break;

case 'ASSO_2':
	$title=WT_Gedcom_Tag::getLabel('ASSO');
	$text=WT_I18N::translate('An associate is another individual who was involved with this fact or event, such as a witness or a priest.');
	break;

case 'CAUS':
	$title=WT_Gedcom_Tag::getLabel('CAUS');
	$text=WT_I18N::translate('A description of the cause of the associated event or fact, such as the cause of death.');
	break;

case 'DATE':
	$title=WT_Gedcom_Tag::getLabel('DATE');
	$CALENDAR_FORMAT=null; // Don't perform conversions here - it will confuse the examples!
	$dates=array(
		'1900'                     =>new WT_Date('1900'),
		'JAN 1900'                 =>new WT_Date('JAN 1900'),
		'FEB 1900'                 =>new WT_Date('FEB 1900'),
		'MAR 1900'                 =>new WT_Date('MAR 1900'),
		'APR 1900'                 =>new WT_Date('APR 1900'),
		'MAY 1900'                 =>new WT_Date('MAY 1900'),
		'JUN 1900'                 =>new WT_Date('JUN 1900'),
		'JUL 1900'                 =>new WT_Date('JUL 1900'),
		'AUG 1900'                 =>new WT_Date('AUG 1900'),
		'SEP 1900'                 =>new WT_Date('SEP 1900'),
		'OCT 1900'                 =>new WT_Date('OCT 1900'),
		'NOV 1900'                 =>new WT_Date('NOV 1900'),
		'DEC 1900'                 =>new WT_Date('DEC 1900'),
		'11 DEC 1913'              =>new WT_Date('11 DEC 1913'),
		'01 FEB 2003'              =>new WT_Date('01 FEB 2003'),
		'ABT 1900'                 =>new WT_Date('ABT 1900'),
		'EST 1900'                 =>new WT_Date('EST 1900'),
		'CAL 1900'                 =>new WT_Date('CAL 1900'),
		'INT 1900 (...)'           =>new WT_Date('INT 1900 (...)'),
		'@#DJULIAN@ 44 B.C.'       =>new WT_Date('@#DJULIAN@ 44 B.C.'),
		'@#DJULIAN@ 14 JAN 1700'   =>new WT_Date('@#DJULIAN@ 14 JAN 1700'),
		'BET @#DJULIAN@ 01 SEP 1752 AND @#DGREGORIAN@ 30 SEP 1752'   =>new WT_Date('BET @#DJULIAN@ 01 SEP 1752 AND @#DGREGORIAN@ 30 SEP 1752'),
		'@#DJULIAN@ 20 FEB 1742/43'=>new WT_Date('@#DJULIAN@ 20 FEB 1742/43'),
		'FROM 1900 TO 1910'        =>new WT_Date('FROM 1900 TO 1910'),
		'FROM 1900'                =>new WT_Date('FROM 1900'),
		'TO 1910'                  =>new WT_Date('TO 1910'),
		'BET 1900 AND 1910'        =>new WT_Date('BET 1900 AND 1910'),
		'BET JAN 1900 AND MAR 1900'=>new WT_Date('BET JAN 1900 AND MAR 1900'),
		'BET APR 1900 AND JUN 1900'=>new WT_Date('BET APR 1900 AND JUN 1900'),
		'BET JUL 1900 AND SEP 1900'=>new WT_Date('BET JUL 1900 AND SEP 1900'),
		'BET OCT 1900 AND DEC 1900'=>new WT_Date('BET OCT 1900 AND DEC 1900'),
		'AFT 1900'                 =>new WT_Date('AFT 1900'),
		'BEF 1910'                 =>new WT_Date('BEF 1910'),
		// Hijri dates
		'@#DHIJRI@ 1497'           =>new WT_Date('@#DHIJRI@ 1497'),
		'@#DHIJRI@ MUHAR 1497'     =>new WT_Date('@#DHIJRI@ MUHAR 1497'),
		'ABT @#DHIJRI@ SAFAR 1497' =>new WT_Date('ABT @#DHIJRI@ SAFAR 1497'),
		'BET @#DHIJRI@ RABIA 1497 AND @#DHIJRI@ RABIT 1497'=>new WT_Date('BET @#DHIJRI@ RABIA 1497 AND @#DHIJRI@ RABIT 1497'),
		'FROM @#DHIJRI@ JUMAA 1497 TO @#DHIJRI@ JUMAT 1497'=>new WT_Date('FROM @#DHIJRI@ JUMAA 1497 TO @#DHIJRI@ JUMAT 1497'),
		'AFT @#DHIJRI@ RAJAB 1497' =>new WT_Date('AFT @#DHIJRI@ RAJAB 1497'),
		'BEF @#DHIJRI@ SHAAB 1497' =>new WT_Date('BEF @#DHIJRI@ SHAAB 1497'),
		'ABT @#DHIJRI@ RAMAD 1497' =>new WT_Date('ABT @#DHIJRI@ RAMAD 1497'),
		'FROM @#DHIJRI@ SHAWW 1497'=>new WT_Date('FROM @#DHIJRI@ SHAWW 1497'),
		'TO @#DHIJRI@ DHUAQ 1497'  =>new WT_Date('TO @#DHIJRI@ DHUAQ 1497'),
		'@#DHIJRI@ 03 DHUAH 1497'  =>new WT_Date('@#DHIJRI@ 03 DHUAH 1497'),
		// French dates
		'@#DFRENCH R@ 12'          =>new WT_Date('@#DFRENCH R@ 12'),
		'@#DFRENCH R@ VEND 12'     =>new WT_Date('@#DFRENCH R@ VEND 12'),
		'ABT @#DFRENCH R@ BRUM 12' =>new WT_Date('ABT @#DFRENCH R@ BRUM 12'),
		'BET @#DFRENCH R@ FRIM 12 AND @#DFRENCH R@ NIVO 12'=>new WT_Date('BET @#DFRENCH R@ FRIM 12 AND @#DFRENCH R@ NIVO 12'),
		'FROM @#DFRENCH R@ PLUV 12 TO @#DFRENCH R@ VENT 12'=>new WT_Date('FROM @#DFRENCH R@ PLUV 12 TO @#DFRENCH R@ VENT 12'),
		'AFT @#DFRENCH R@ GERM 12' =>new WT_Date('AFT @#DFRENCH R@ GERM 12'),
		'BEF @#DFRENCH R@ FLOR 12' =>new WT_Date('BEF @#DFRENCH R@ FLOR 12'),
		'ABT @#DFRENCH R@ PRAI 12' =>new WT_Date('ABT @#DFRENCH R@ PRAI 12'),
		'FROM @#DFRENCH R@ MESS 12'=>new WT_Date('FROM @#DFRENCH R@ MESS 12'),
		'TO @#DFRENCH R@ THER 12'  =>new WT_Date('TO @#DFRENCH R@ THER 12'),
		'EST @#DFRENCH R@ FRUC 12' =>new WT_Date('EST @#DFRENCH R@ FRUC 12'),
		'@#DFRENCH R@ 03 COMP 12'  =>new WT_Date('@#DFRENCH R@ 03 COMP 12'),
		// Jewish dates
		'@#DHEBREW@ 5481'          =>new WT_Date('@#DHEBREW@ 5481'),
		'@#DHEBREW@ TSH 5481'      =>new WT_Date('@#DHEBREW@ TSH 5481'),
		'ABT @#DHEBREW@ CSH 5481'  =>new WT_Date('ABT @#DHEBREW@ CSH 5481'),
		'BET @#DHEBREW@ KSL 5481 AND @#DHEBREW@ TVT 5481'=>new WT_Date('BET @#DHEBREW@ KSL 5481 AND @#DHEBREW@ TVT 5481'),
		'FROM @#DHEBREW@ SHV 5481 TO @#DHEBREW@ ADR 5481'=>new WT_Date('FROM @#DHEBREW@ SHV 5481 TO @#DHEBREW@ ADR 5481'),
		'AFT @#DHEBREW@ ADR 5481'  =>new WT_Date('AFT @#DHEBREW@ ADR 5481'),
		'AFT @#DHEBREW@ ADS 5480'  =>new WT_Date('AFT @#DHEBREW@ ADS 5480'),
		'BEF @#DHEBREW@ NSN 5481'  =>new WT_Date('BEF @#DHEBREW@ NSN 5481'),
		'ABT @#DHEBREW@ IYR 5481'  =>new WT_Date('ABT @#DHEBREW@ IYR 5481'),
		'FROM @#DHEBREW@ SVN 5481' =>new WT_Date('FROM @#DHEBREW@ SVN 5481'),
		'TO @#DHEBREW@ TMZ 5481'   =>new WT_Date('TO @#DHEBREW@ TMZ 5481'),
		'EST @#DHEBREW@ AAV 5481'  =>new WT_Date('EST @#DHEBREW@ AAV 5481'),
		'@#DHEBREW@ 03 ELL 5481'   =>new WT_Date('@#DHEBREW@ 03 ELL 5481'),
	);

	foreach ($dates as &$date) {
		$date=strip_tags($date->Display());
	}
	// These shortcuts work differently for different languages
	switch (preg_replace('/[^DMY]/', '', str_replace(array('J', 'F'), array('D', 'M'), strtoupper($DATE_FORMAT)))) {
	case 'YMD':
		$example1='11/12/1913'; // Note: we ignore the DMY order if it doesn't make sense.
		$example2='03/02/01';
		break;
	case 'MDY':
		$example1='12/11/1913';
		$example2='02/01/03';
		break;
	case 'DMY':
	default:
		$example1='11/12/1913';
		$example2='01/02/03';
		break;
	}
	$example1.='<br>'.str_replace('/', '-', $example1).'<br>'.str_replace('/', '.', $example1);
	$example2.='<br>'.str_replace('/', '-', $example2).'<br>'.str_replace('/', '.', $example2);
	$text=
		'<p>'.WT_I18N::translate('Dates are stored using English abbreviations and keywords.  Shortcuts are available as alternatives to these abbreviations and keywords.').'</p>'.
		'<table border="1">'.
		'<tr><th>'.WT_I18N::translate('Date').'</th><th>'.WT_I18N::translate('Format').'</th><th>'.WT_I18N::translate('Shortcut').'</th></tr>'.
		'<tr><td>'.$dates['1900'].'</td><td><tt dir="ltr" lang="en">1900</tt></td><td>&nbsp;</td></tr>'.
		'<tr><td>'.$dates['JAN 1900'].'<br>'.$dates['FEB 1900'].'<br>'.$dates['MAR 1900'].'<br>'.$dates['APR 1900'].'<br>'.$dates['MAY 1900'].'<br>'.$dates['JUN 1900'].'<br>'.$dates['JUL 1900'].'<br>'.$dates['AUG 1900'].'<br>'.$dates['SEP 1900'].'<br>'.$dates['OCT 1900'].'<br>'.$dates['NOV 1900'].'<br>'.$dates['DEC 1900'].'</td><td><tt dir="ltr" lang="en">JAN 1900<br>FEB 1900<br>MAR 1900<br>APR 1900<br>MAY 1900<br>JUN 1900<br>JUL 1900<br>AUG 1900<br>SEP 1900<br>OCT 1900<br>NOV 1900<br>DEC 1900</tt></td><td>&nbsp;</td></tr>'.
		'<tr><td>'.$dates['11 DEC 1913'].'</td><td><tt dir="ltr" lang="en">11 DEC 1913</tt></td><td><tt dir="ltr" lang="en">'.$example1.'</tt></td></tr>'.
		'<tr><td>'.$dates['01 FEB 2003'].'</td><td><tt dir="ltr" lang="en">01 FEB 2003</tt></td><td><tt dir="ltr" lang="en">'.$example2.'</tt></td></tr>'.
		'<tr><td>'.$dates['ABT 1900'].'</td><td><tt dir="ltr" lang="en">ABT 1900</tt></td><td><tt dir="ltr" lang="en">~1900</tt></td></tr>'.
		'<tr><td>'.$dates['EST 1900'].'</td><td><tt dir="ltr" lang="en">EST 1900</tt></td><td><tt dir="ltr" lang="en">*1900</tt></td></tr>'.
		'<tr><td>'.$dates['CAL 1900'].'</td><td><tt dir="ltr" lang="en">CAL 1900</tt></td><td><tt dir="ltr" lang="en">#1900</tt></td></tr>'.
		'<tr><td>'.$dates['INT 1900 (...)'].'</td><td><tt dir="ltr" lang="en">INT 1900 (...)</tt></td><td>&nbsp;</td></tr>'.
		'</table>'.
		'<p>'.WT_I18N::translate('Date ranges are used to indicate that an event, such as a birth, happened on an unknown date within a possible range.').'</p>'.
		'<table border="1">'.
		'<tr><th>'.WT_I18N::translate('Date range').'</th><th>'.WT_I18N::translate('Format').'</th><th>'.WT_I18N::translate('Shortcut').'</th></tr>'.
		'<tr><td>'.$dates['BET 1900 AND 1910'].'</td><td><tt dir="ltr" lang="en">BET 1900 AND 1910</tt></td><td><tt dir="ltr" lang="en">1900-1910</tt></td></tr>'.
		'<tr><td>'.$dates['AFT 1900'].'</td><td><tt dir="ltr" lang="en">AFT 1900</tt></td><td><tt dir="ltr" lang="en">&gt;1900</tt></td></tr>'.
		'<tr><td>'.$dates['BEF 1910'].'</td><td><tt dir="ltr" lang="en">BEF 1910</tt></td><td><tt dir="ltr" lang="en">&lt;1910</tt></td></tr>'.
		'<tr><td>'.$dates['BET JAN 1900 AND MAR 1900'].'</td><td><tt dir="ltr" lang="en">BET JAN 1900 AND MAR 1900</tt></td><td><tt dir="ltr" lang="en">Q1 1900</tt></td></tr>'.
		'<tr><td>'.$dates['BET APR 1900 AND JUN 1900'].'</td><td><tt dir="ltr" lang="en">BET APR 1900 AND JUN 1900</tt></td><td><tt dir="ltr" lang="en">Q2 1900</tt></td></tr>'.
		'<tr><td>'.$dates['BET JUL 1900 AND SEP 1900'].'</td><td><tt dir="ltr" lang="en">BET JUL 1900 AND SEP 1900</tt></td><td><tt dir="ltr" lang="en">Q3 1900</tt></td></tr>'.
		'<tr><td>'.$dates['BET OCT 1900 AND DEC 1900'].'</td><td><tt dir="ltr" lang="en">BET OCT 1900 AND DEC 1900</tt></td><td><tt dir="ltr" lang="en">Q4 1900</tt></td></tr>'.
		'</table>'.
		'<p>'.WT_I18N::translate('Date periods are used to indicate that a fact, such as an occupation, continued for a period of time.').'</p>'.
		'<table border="1">'.
		'<tr><th>'.WT_I18N::translate('Date period').'</th><th>'.WT_I18N::translate('Format').'</th><th>'.WT_I18N::translate('Shortcut').'</th></tr>'.
		'<tr><td>'.$dates['FROM 1900 TO 1910'].'</td><td><tt dir="ltr" lang="en">FROM 1900 TO 1910</tt></td><td><tt dir="ltr" lang="en">1900~1910</tt></td></tr>'.
		'<tr><td>'.$dates['FROM 1900'].'</td><td><tt dir="ltr" lang="en">FROM 1900</tt></td><td><tt dir="ltr" lang="en">1900-</tt></td></tr>'.
		'<tr><td>'.$dates['TO 1910'].'</td><td><tt dir="ltr" lang="en">TO 1910</tt></td><td><tt dir="ltr" lang="en">-1900</tt></td></tr>'.
		'</table>'.
		'<p>'.WT_I18N::translate('Simple dates are assumed to be in the gregorian calendar.  To specify a date in another calendar, add a keyword before the date.  This keyword is optional if the month or year format make the date unambiguous.').'</p>'.
		'<table border="1">'.
		'<tr><th>'.WT_I18N::translate('Date').'</th><th>'.WT_I18N::translate('Format').'</th></tr>'.
		'<tr><td colspan="2" align="center">'.WT_I18N::translate('Julian').'</td></tr>'.
		'<tr><td>'.$dates['@#DJULIAN@ 14 JAN 1700'].'</td><td><tt dir="ltr" lang="en">@#DJULIAN@ 14 JAN 1700</tt></td></tr>'.
		'<tr><td>'.$dates['@#DJULIAN@ 44 B.C.'].'</td><td><tt dir="ltr" lang="en">@#DJULIAN@ 44 B.C.</tt></td></tr>'.
		'<tr><td>'.$dates['@#DJULIAN@ 20 FEB 1742/43'].'</td><td><tt dir="ltr" lang="en">@#DJULIAN@ 20 FEB 1742/43</tt></td></tr>'.
		'<tr><td>'.$dates['BET @#DJULIAN@ 01 SEP 1752 AND @#DGREGORIAN@ 30 SEP 1752'].'</td><td><tt dir="ltr" lang="en">BET @#DJULIAN@ 01 SEP 1752 AND @#DGREGORIAN@ 30 SEP 1752</tt></td></tr>'.
		'<tr><td colspan="2" align="center">'.WT_I18N::translate('Jewish').'</td></tr>'.
		'<tr><td>'.$dates['@#DHEBREW@ 5481'].'</td><td><tt dir="ltr" lang="en">@#DHEBREW@ 5481</tt></td></tr>'.
		'<tr><td>'.$dates['@#DHEBREW@ TSH 5481'].'</td><td><tt dir="ltr" lang="en">@#DHEBREW@ TSH 5481</tt></td></tr>'.
		'<tr><td>'.$dates['ABT @#DHEBREW@ CSH 5481'].'</td><td><tt dir="ltr" lang="en">ABT @#DHEBREW@ CSH 5481</tt></td></tr>'.
		'<tr><td>'.$dates['BET @#DHEBREW@ KSL 5481 AND @#DHEBREW@ TVT 5481'].'</td><td><tt dir="ltr" lang="en">BET @#DHEBREW@ KSL 5481 AND @#DHEBREW@ TVT 5481</tt></td></tr>'.
		'<tr><td>'.$dates['FROM @#DHEBREW@ SHV 5481 TO @#DHEBREW@ ADR 5481'].'</td><td><tt dir="ltr" lang="en">FROM @#DHEBREW@ SHV 5481 TO @#DHEBREW@ ADR 5481</tt></td></tr>'.
		'<tr><td>'.$dates['AFT @#DHEBREW@ ADR 5481'].'</td><td><tt dir="ltr" lang="en">AFT @#DHEBREW@ ADR 5481</tt></td></tr>'.
		'<tr><td>'.$dates['AFT @#DHEBREW@ ADS 5480'].'</td><td><tt dir="ltr" lang="en">AFT @#DHEBREW@ ADS 5480</tt></td></tr>'.
		'<tr><td>'.$dates['BEF @#DHEBREW@ NSN 5481'].'</td><td><tt dir="ltr" lang="en">BEF @#DHEBREW@ NSN 5481</tt></td></tr>'.
		'<tr><td>'.$dates['ABT @#DHEBREW@ IYR 5481'].'</td><td><tt dir="ltr" lang="en">ABT @#DHEBREW@ IYR 5481</tt></td></tr>'.
		'<tr><td>'.$dates['FROM @#DHEBREW@ SVN 5481'].'</td><td><tt dir="ltr" lang="en">FROM @#DHEBREW@ SVN 5481</tt></td></tr>'.
		'<tr><td>'.$dates['TO @#DHEBREW@ TMZ 5481'].'</td><td><tt dir="ltr" lang="en">TO @#DHEBREW@ TMZ 5481</tt></td></tr>'.
		'<tr><td>'.$dates['EST @#DHEBREW@ AAV 5481'].'</td><td><tt dir="ltr" lang="en">EST @#DHEBREW@ AAV 5481</tt></td></tr>'.
		'<tr><td>'.$dates['@#DHEBREW@ 03 ELL 5481'].'</td><td><tt dir="ltr" lang="en">@#DHEBREW@ 03 ELL 5481</tt></td></tr>'.
		'<tr><td colspan="2" align="center">'.WT_I18N::translate('Hijri').'</td></tr>'.
		'<tr><td>'.$dates['@#DHIJRI@ 1497'].'</td><td><tt dir="ltr" lang="en">@#DHIJRI@ 1497</tt></td></tr>'.
		'<tr><td>'.$dates['@#DHIJRI@ MUHAR 1497'].'</td><td><tt dir="ltr" lang="en">@#DHIJRI@ MUHAR 1497</tt></td></tr>'.
		'<tr><td>'.$dates['ABT @#DHIJRI@ SAFAR 1497'].'</td><td><tt dir="ltr" lang="en">ABT @#DHIJRI@ SAFAR 1497</tt></td></tr>'.
		'<tr><td>'.$dates['BET @#DHIJRI@ RABIA 1497 AND @#DHIJRI@ RABIT 1497'].'</td><td><tt dir="ltr" lang="en">BET @#DHIJRI@ RABIA 1497 AND @#DHIJRI@ RABIT 1497</tt></td></tr>'.
		'<tr><td>'.$dates['FROM @#DHIJRI@ JUMAA 1497 TO @#DHIJRI@ JUMAT 1497'].'</td><td><tt dir="ltr" lang="en">FROM @#DHIJRI@ JUMAA 1497 TO @#DHIJRI@ JUMAT 1497</tt></td></tr>'.
		'<tr><td>'.$dates['AFT @#DHIJRI@ RAJAB 1497'].'</td><td><tt dir="ltr" lang="en">AFT @#DHIJRI@ RAJAB 1497</tt></td></tr>'.
		'<tr><td>'.$dates['BEF @#DHIJRI@ SHAAB 1497'].'</td><td><tt dir="ltr" lang="en">BEF @#DHIJRI@ SHAAB 1497</tt></td></tr>'.
		'<tr><td>'.$dates['ABT @#DHIJRI@ RAMAD 1497'].'</td><td><tt dir="ltr" lang="en">ABT @#DHIJRI@ RAMAD 1497</tt></td></tr>'.
		'<tr><td>'.$dates['FROM @#DHIJRI@ SHAWW 1497'].'</td><td><tt dir="ltr" lang="en">FROM @#DHIJRI@ SHAWW 1497</tt></td></tr>'.
		'<tr><td>'.$dates['TO @#DHIJRI@ DHUAQ 1497'].'</td><td><tt dir="ltr" lang="en">TO @#DHIJRI@ DHUAQ 1497</tt></td></tr>'.
		'<tr><td>'.$dates['@#DHIJRI@ 03 DHUAH 1497'].'</td><td><tt dir="ltr" lang="en">@#DHIJRI@ 03 DHUAH 1497</tt></td></tr>'.
		'<tr><td colspan="2" align="center">'.WT_I18N::translate('French').'</td></tr>'.
		'<tr><td>'.$dates['@#DFRENCH R@ 12'].'</td><td><tt dir="ltr" lang="en">@#DFRENCH R@ 12</tt></td></tr>'.
		'<tr><td>'.$dates['@#DFRENCH R@ VEND 12'].'</td><td><tt dir="ltr" lang="en">@#DFRENCH R@ VEND 12</tt></td></tr>'.
		'<tr><td>'.$dates['ABT @#DFRENCH R@ BRUM 12'].'</td><td><tt dir="ltr" lang="en">ABT @#DFRENCH R@ BRUM 12</tt></td></tr>'.
		'<tr><td>'.$dates['BET @#DFRENCH R@ FRIM 12 AND @#DFRENCH R@ NIVO 12'].'</td><td><tt dir="ltr" lang="en">BET @#DFRENCH R@ FRIM 12 AND @#DFRENCH R@ NIVO 12</tt></td></tr>'.
		'<tr><td>'.$dates['FROM @#DFRENCH R@ PLUV 12 TO @#DFRENCH R@ VENT 12'].'</td><td><tt dir="ltr" lang="en">FROM @#DFRENCH R@ PLUV 12 TO @#DFRENCH R@ VENT 12</tt></td></tr>'.
		'<tr><td>'.$dates['AFT @#DFRENCH R@ GERM 12'].'</td><td><tt dir="ltr" lang="en">AFT @#DFRENCH R@ GERM 12</tt></td></tr>'.
		'<tr><td>'.$dates['BEF @#DFRENCH R@ FLOR 12'].'</td><td><tt dir="ltr" lang="en">BEF @#DFRENCH R@ FLOR 12</tt></td></tr>'.
		'<tr><td>'.$dates['ABT @#DFRENCH R@ PRAI 12'].'</td><td><tt dir="ltr" lang="en">ABT @#DFRENCH R@ PRAI 12</tt></td></tr>'.
		'<tr><td>'.$dates['FROM @#DFRENCH R@ MESS 12'].'</td><td><tt dir="ltr" lang="en">FROM @#DFRENCH R@ MESS 12</tt></td></tr>'.
		'<tr><td>'.$dates['TO @#DFRENCH R@ THER 12'].'</td><td><tt dir="ltr" lang="en">TO @#DFRENCH R@ THER 12</tt></td></tr>'.
		'<tr><td>'.$dates['EST @#DFRENCH R@ FRUC 12'].'</td><td><tt dir="ltr" lang="en">EST @#DFRENCH R@ FRUC 12</tt></td></tr>'.
		'<tr><td>'.$dates['@#DFRENCH R@ 03 COMP 12'].'</td><td><tt dir="ltr" lang="en">@#DFRENCH R@ 03 COMP 12</tt></td></tr>'.
		'</table>';
	break;

case 'EMAI':
case 'EMAIL':
case 'EMAL':
case '_EMAIL':
	$title=WT_Gedcom_Tag::getLabel('EMAIL');
	$text=WT_I18N::translate('Enter the email address.<br><br>An example email address looks like this: <b>name@hotmail.com</b>  Leave this field blank if you do not want to include an email address.');
	break;

case 'FAX':
	$title=WT_Gedcom_Tag::getLabel('FAX');
	$text=WT_I18N::translate('Enter the FAX number including the country and area code.<br><br>Leave this field blank if you do not want to include a FAX number.  For example, a number in Germany might be +49 25859 56 76 89 and a number in USA or Canada might be +1 888 555-1212.');
	break;

case 'FORM':
	$title=WT_Gedcom_Tag::getLabel('FORM');
	$text=WT_I18N::translate('This is an optional field that can be used to enter the file format of the media object.  Some genealogy programs may look at this field to determine how to handle the item.  However, since media do not transfer across computer systems very well, this field is not very important.');
	break;

// This help text is used for all NAME components
case 'NAME':
	$title=WT_Gedcom_Tag::getLabel('NAME');
	$text=
		'<p>' .
		WT_I18N::translate('The <b>name</b> field contains the individual’s full name, as they would have spelled it or as it was recorded.  This is how it will be displayed on screen.  It uses standard genealogical annotations to identify different parts of the name.') .
		'</p>' .
		'<ul><li>' .
		WT_I18N::translate('The surname is enclosed by slashes: <%s>John Paul /Smith/<%s>', 'span style="color:#0000ff;"', '/span') .
		'</li><li>' .
		WT_I18N::translate('If the surname is unknown, use empty slashes: <%s>Mary //<%s>', 'span style="color:#0000ff;"', '/span') .
		'</li><li>' .
		WT_I18N::translate('If an individual has two separate surnames, both should be enclosed by slashes: <%s>José Antonio /Gómez/ /Iglesias/<%s>', 'span style="color:#0000ff;"', '/span') .
		'</li><li>' .
		WT_I18N::translate('If an individual does not have a surname, no slashes are needed: <%s>Jón Einarsson<%s>', 'span style="color:#0000ff;"', '/span') .
		'</li><li>' .
		WT_I18N::translate('If an individual was not known by their first given name, the preferred name should be indicated with an asterisk: <%s>John Paul* /Smith/<%s>', 'span style="color:#0000ff;"', '/span') .
		'</li><li>' .
		WT_I18N::translate('If an individual was known by a nickname which is not part of their formal name, it should be enclosed by quotation marks.  For example, <%s>John &quot;Nobby&quot; /Clark/<%s>.', 'span style="color:#0000ff;"', '/span') .
		'</li></ul>';
	break;

case 'SURN':
	$title=WT_Gedcom_Tag::getLabel('SURN');
	$text='<p>' .
		WT_I18N::translate('The <b>surname</b> field contains a name that is used for sorting and grouping.  It can be different to the individual’s actual surname which is always taken from the <b>name</b> field.  This field can be used to sort surnames with or without a prefix (Gogh / van Gogh) and to group spelling variations or inflections (Kowalski / Kowalska).  If an individual needs to be listed under more than one surname, each name should be separated by a comma.') .
		'</p>';
	break;

case 'NOTE':
	$title=WT_Gedcom_Tag::getLabel('NOTE');
	$text=WT_I18N::translate('Notes are free-form text and will appear in the Fact Details section of the page.');
	break;

case 'OBJE':
	$title=WT_Gedcom_Tag::getLabel('OBJE');
	$text=
		'<p>'.
		WT_I18N::translate('A media object is a record in the family tree which contains information about a media file.  This information may include a title, a copyright notice, a transcript, privacy restrictions, etc.  The media file, such as the photo or video, can be stored locally (on this webserver) or remotely (on a different webserver).').
		'</p>';
	break;

case 'PAGE':
	$title=WT_Gedcom_Tag::getLabel('PAGE');
	$text=WT_I18N::translate('In the citation details field you would enter the page number or other information that might help someone find the information in the source.');
	break;

case 'PEDI':
	$title=WT_Gedcom_Tag::getLabel('PEDI');
	$text=WT_I18N::translate('A child may have more than one set of parents.  The relationship between the child and the parents can be biological, legal, or based on local culture and tradition.  If no pedigree is specified, then a biological relationship will be assumed.');
	break;

case 'PHON':
	$title=WT_Gedcom_Tag::getLabel('PHON');
	$text=WT_I18N::translate('Enter the phone number including the country and area code.<br><br>Leave this field blank if you do not want to include a phone number.  For example, a number in Germany might be +49 25859 56 76 89 and a number in USA or Canada might be +1 888 555-1212.');
	break;

case 'PLAC':
	$title=WT_Gedcom_Tag::getLabel('PLAC');
	$text=WT_I18N::translate('Places should be entered according to the standards for genealogy.  In genealogy, places are recorded with the most specific information about the place first and then working up to the least specific place last, using commas to separate the different place levels.  The level at which you record the place information should represent the levels of government or church where vital records for that place are kept.<br><br>For example, a place like Salt Lake City would be entered as “Salt Lake City, Salt Lake, Utah, USA”.<br><br>Let’s examine each part of this place.  The first part, “Salt Lake City,” is the city or township where the event occurred.  In some countries, there may be municipalities or districts inside a city which are important to note.  In that case, they should come before the city.  The next part, “Salt Lake,” is the county.  “Utah” is the state, and “USA” is the country.  It is important to note each place because genealogical records are kept by the governments of each level.<br><br>If a level of the place is unknown, you should leave a space between the commas.  Suppose, in the example above, you didn’t know the county for Salt Lake City.  You should then record it like this: “Salt Lake City, , Utah, USA”.  Suppose you only know that an individual was born in Utah.  You would enter the information like this: “, , Utah, USA”.  <br><br>You can use the <b>Find Place</b> link to help you find places that already exist in the database.');
	break;

case 'RELA':
	$title=WT_Gedcom_Tag::getLabel('RELA');
	$text=WT_I18N::translate('Select a relationship name from the list.  Selecting <b>Godfather</b> means: <i>This associate is the godfather of the current individual</i>.');
	break;

case 'RESN':
	$title=WT_Gedcom_Tag::getLabel('RESN');
	$text=
		WT_I18N::translate('Restrictions can be added to records and/or facts.  They restrict who can view the data and who can edit it.').
		'<br><br>'.
		WT_I18N::translate('Note that if a user account is linked to a record, then that user will always be able to view that record.');
	break;

case 'ROMN':
	$title=WT_Gedcom_Tag::getLabel('ROMN');
	$text=WT_I18N::translate('In many cultures it is customary to have a traditional name spelled in the traditional characters and also a romanized version of the name as it would be spelled or pronounced in languages based on the Latin alphabet, such as English.<br><br>If you prefer to use a non-Latin alphabet such as Hebrew, Greek, Russian, Chinese, or Arabic to enter the name in the standard name fields, then you can use this field to enter the same name using the Latin alphabet.  Both versions of the name will appear in lists and charts.<br><br>Although this field is labeled “Romanized”, it is not restricted to containing only characters based on the Latin alphabet.  This might be of use with Japanese names, where three different alphabets may occur.');
	break;

case 'SEX':
	$title=WT_Gedcom_Tag::getLabel('SEX');
	$text=WT_I18N::translate('Choose the appropriate gender from the drop-down list.  The <b>unknown</b> option indicates that the gender is unknown.');
	break;

case 'SHARED_NOTE':
	$title=WT_Gedcom_Tag::getLabel('SHARED_NOTE');
	$text=WT_I18N::translate('Shared notes are free-form text and will appear in the Fact Details section of the page.<br><br>Each shared note can be linked to more than one individual, family, source, or event.');
	break;

case 'SOUR':
	$title=WT_Gedcom_Tag::getLabel('SOUR');
	$text=WT_I18N::translate('This field allows you to change the source record that this fact’s source citation links to.  This field takes a source ID.  Beside the field will be listed the title of the current source ID.  Use the <b>Find ID</b> link to look up the source’s ID number.  To remove the entire citation, make this field blank.');
	break;

case 'STAT':
	$title=WT_Gedcom_Tag::getLabel('STAT');
	$text=WT_I18N::translate('This is an optional status field and is used mostly for LDS ordinances as they are run through the TempleReady program.');
	break;

case 'TEMP':
	$title=WT_Gedcom_Tag::getLabel('TEMP');
	$text=WT_I18N::translate('For LDS ordinances, this field records the temple where it was performed.');
	break;

case 'TEXT':
	$title=WT_Gedcom_Tag::getLabel('TEXT');
	$text=WT_I18N::translate('In this field you would enter the citation text for this source.  Examples of data may be a transcription of the text from the source, or a description of what was in the citation.');
	break;

case 'TIME':
	$title=WT_Gedcom_Tag::getLabel('TIME');
	$text=WT_I18N::translate('Enter the time for this event in 24-hour format with leading zeroes.  Midnight is 00:00.  Examples: 04:50 13:00 20:30.');
	break;

case 'URL':
	$title=WT_Gedcom_Tag::getLabel('URL');
	$text=WT_I18N::translate('Enter the URL address including the http://.<br><br>An example URL looks like this: <b>http://www.webtrees.net/</b>.  Leave this field blank if you do not want to include a URL.');
	break;

case '_HEB':
	$title=WT_Gedcom_Tag::getLabel('_HEB');
	$text=WT_I18N::translate('In many cultures it is customary to have a traditional name spelled in the traditional characters and also a romanized version of the name as it would be spelled or pronounced in languages based on the Latin alphabet, such as English.<br><br>If you prefer to use the Latin alphabet to enter the name in the standard name fields, then you can use this field to enter the same name in the non-Latin alphabet such as Greek, Hebrew, Russian, Arabic, or Chinese.  Both versions of the name will appear in lists and charts.<br><br>Although this field is labeled “Hebrew”, it is not restricted to containing only Hebrew characters.');
	break;

case '_PRIM':
	$title=WT_Gedcom_Tag::getLabel('_PRIM');
	$text=WT_I18N::translate('Use this field to signal that this media item is the highlighted or primary item for the individual it is attached to.  The highlighted image is the one that will be used on charts and on the individual’s page.');
	break;

	//////////////////////////////////////////////////////////////////////////////
	// This section contains an entry for every configuration item
	//////////////////////////////////////////////////////////////////////////////

case 'ADVANCED_NAME_FACTS':
	$title=WT_I18N::translate('Advanced name facts');
	$text=WT_I18N::translate('This is a comma separated list of GEDCOM fact tags that will be shown on the add/edit name form.  If you use non-Latin alphabets such as Hebrew, Greek, Cyrillic, or Arabic, you may want to add tags such as _HEB, ROMN, FONE, etc. to allow you to store names in several different alphabets.');
	break;

case 'ADVANCED_PLAC_FACTS':
	$title=WT_I18N::translate('Advanced place name facts');
	$text=WT_I18N::translate('This is a comma separated list of GEDCOM fact tags that will be shown when you add or edit place names.  If you use non-Latin alphabets such as Hebrew, Greek, Cyrillic, or Arabic, you may want to add tags such as _HEB, ROMN, FONE, etc. to allow you to store place names in several different alphabets.');
	break;

case 'ALLOW_CHANGE_GEDCOM':
	$title=WT_I18N::translate('Show list of family trees');
	$text=/* I18N: Help text for the “Show list of family trees” site configuration setting */ WT_I18N::translate('For sites with more than one family tree, this option will show the list of family trees in the main menu, the search pages, etc.');
	break;

case 'ALLOW_THEME_DROPDOWN':
	$title=WT_I18N::translate('Theme dropdown selector for theme changes');
	$text=WT_I18N::translate('Gives users the option of selecting their own theme from a menu.<br><br>Even with this option set, the theme currently in effect may not provide for such a menu.  To be effective, this option requires the <b>Allow users to select their own theme</b> option to be set as well.');
	break;

case 'ALLOW_USER_THEMES':
	$title=WT_I18N::translate('Allow users to select their own theme');
	$text=WT_I18N::translate('Gives users the option of selecting their own theme.');
	break;

case 'CALENDAR_FORMAT':
	$d1=new WT_Date('22 SEP 1792'); $d1=$d1->display();
	$d2=new WT_Date('31 DEC 1805'); $d2=$d2->display();
	$d3=new WT_Date('15 OCT 1582'); $d3=$d3->display();
	$title=WT_I18N::translate('Calendar conversion');
	$text=
		'<p>'.
		WT_I18N::translate('Different calendar systems are used in different parts of the world, and many other calendar systems have been used in the past.  Where possible, you should enter dates using the calendar in which the event was originally recorded.  You can then specify a conversion, to show these dates in a more familiar calendar.  If you regularly use two calendars, you can specify two conversions and dates will be converted to both the selected calendars.').
		'<p>'.
		WT_I18N::translate('The following calendars are supported:').
		'</p><ul>'.
		'<li>'.WT_Date_Gregorian::calendarName().'</li>'.
		'<li>'.WT_Date_Julian::calendarName().'</li>'.
		'<li>'.WT_Date_Jewish::calendarName().'</li>'.
		'<li>'.WT_Date_French::calendarName().'</li>'.
		'<li>'.WT_Date_Hijri::calendarName().'</li>'.
		'<li>'.WT_Date_Jalali::calendarName().'</li>'.
		'</ul><p>'.
		/* I18N: The three place holders are all dates. */ WT_I18N::translate('Dates are only converted if they are valid for the calendar.  For example, only dates between %1$s and %2$s will be converted to the French calendar and only dates after %3$s will be converted to the Gregorian calendar.', $d1, $d2, $d3).
		'</p><p>'.
		WT_I18N::translate('In some calendars, days start at midnight.  In other calendars, days start at sunset.  The conversion process does not take account of the time, so for any event that occurs between sunset and midnight, the conversion between these types of calendar will be one day out.').
		'</p>';
	break;

case 'CHART_BOX_TAGS':
	$title=WT_I18N::translate('Other facts to show in charts');
	$text=WT_I18N::translate('This should be a comma or space separated list of facts, in addition to birth and death, that you want to appear in chart boxes such as the pedigree chart.  This list requires you to use fact tags as defined in the GEDCOM 5.5.1 standard.  For example, if you wanted the occupation to show up in the box, you would add “OCCU” to this field.');
	break;

case 'CHECK_MARRIAGE_RELATIONS':
	$title=WT_I18N::translate('Check relationships by marriage');
	$text=WT_I18N::translate('When calculating relationships, this option controls whether webtrees will include spouses/partners as well as blood relatives.');
	break;

case 'COMMON_NAMES_ADD':
	$title=WT_I18N::translate('Names to add to common surnames (comma separated)');
	$text=WT_I18N::translate('If the number of times that a certain surname occurs is lower than the threshold, it will not appear in the list.  It can be added here manually.  If more than one surname is entered, they must be separated by a comma.  <b>Surnames are case-sensitive.</b>');
	break;

case 'COMMON_NAMES_REMOVE':
	$title=WT_I18N::translate('Names to remove from common surnames (comma separated)');
	$text=WT_I18N::translate('If you want to remove a surname from the Common Surname list without increasing the threshold value, you can do that by entering the surname here.  If more than one surname is entered, they must be separated by a comma. <b>Surnames are case-sensitive</b>.  Surnames entered here will also be removed from the “Top surnames” list on the “Home page”.');
	break;

case 'COMMON_NAMES_THRESHOLD':
	$title=WT_I18N::translate('Min. no. of occurrences to be a “common surname”');
	$text=WT_I18N::translate('This is the number of times that a surname must occur before it shows up in the Common Surname list on the “Home page”.');
	break;

case 'CONTACT_USER_ID':
	$title=WT_I18N::translate('Genealogy contact');
	$text=WT_I18N::translate('The individual to contact about the genealogical data on this site.');
	break;

case 'DEFAULT_PEDIGREE_GENERATIONS':
	$title=WT_I18N::translate('Default pedigree generations');
	$text=WT_I18N::translate('Set the default number of generations to display on descendancy and pedigree charts.');
	break;

case 'EXPAND_NOTES':
	$title=WT_I18N::translate('Automatically expand notes');
	$text=WT_I18N::translate('This option controls whether or not to automatically display content of a <i>Note</i> record on the Individual page.');
	break;

case 'EXPAND_RELATIVES_EVENTS':
	$title=WT_I18N::translate('Automatically expand list of events of close relatives');
	$text=WT_I18N::translate('This option controls whether or not to automatically expand the <i>Events of close relatives</i> list.');
	break;

case 'EXPAND_SOURCES':
	$title=WT_I18N::translate('Automatically expand sources');
	$text=WT_I18N::translate('This option controls whether or not to automatically display content of a <i>Source</i> record on the Individual page.');
	break;

case 'FAM_FACTS_ADD':
	$title=WT_I18N::translate('All family facts');
	$text=WT_I18N::translate('This is the list of GEDCOM facts that your users can add to families.  You can modify this list by removing or adding fact names, even custom ones, as necessary.  Fact names that appear in this list must not also appear in the “Unique family facts” list.');
	break;

case 'FAM_FACTS_QUICK':
	$title=WT_I18N::translate('Quick family facts');
	$text=WT_I18N::translate('This is the short list of GEDCOM family facts that appears next to the full list and can be added with a single click.');
	break;

case 'FAM_FACTS_UNIQUE':
	$title=WT_I18N::translate('Unique family facts');
	$text=WT_I18N::translate('This is the list of GEDCOM facts that your users can only add once to families.  For example, if MARR is in this list, users will not be able to add more than one MARR record to a family.  Fact names that appear in this list must not also appear in the “All family facts” list.');
	break;

case 'FAM_ID_PREFIX':
	$title=WT_I18N::translate('Family ID prefix');
	$text=WT_I18N::translate('When a new family record is added online in webtrees, a new ID for that family will be generated automatically.  The family ID will have this prefix.');
	break;

case 'FORMAT_TEXT':
	$title=WT_I18N::translate('Format text and notes');
	$text =
		'<p>' .
		WT_I18N::translate('To ensure compatibility with other genealogy applications, notes, text, and transcripts should be recorded in simple, unformatted text.  However, formatting is often desirable to aid presentation, comprehension, etc.') .
		'</p><p>' .
		WT_I18N::translate('Markdown is a simple system of formatting, used on websites such as Wikipedia.  It uses unobtrusive punctuation characters to create headings and sub-headings, bold and italic text, lists, tables, etc.') .
		'</p>';
	break;
case 'FULL_SOURCES':
	$title=WT_I18N::translate('Use full source citations');
	$text=WT_I18N::translate('Source citations can include fields to record the quality of the data (primary, secondary, etc.) and the date the event was recorded in the source.  If you don’t use these fields, you can disable them when creating new source citations.');
	break;

case 'GEDCOM_ID_PREFIX':
	$title=WT_I18N::translate('Individual ID prefix');
	$text=WT_I18N::translate('When a new individual record is added online in webtrees, a new ID for that individual will be generated automatically.  The individual ID will have this prefix.');
	break;

case 'GEDCOM_MEDIA_PATH':
	$title=WT_I18N::translate('GEDCOM media path');
	$text=
		'<p>'.
		// I18N: A “path” is something like “C:\Documents\My_User\Genealogy\Photos\Gravestones\John_Smith.jpeg”
		WT_I18N::translate('Some genealogy applications create GEDCOM files that contain media filenames with full paths.  These paths will not exist on the web-server.  To allow webtrees to find the file, the first part of the path must be removed.').
		'</p><p>'.
		// I18N: %s are all folder names; “GEDCOM media path” is a configuration setting
		WT_I18N::translate('For example, if the GEDCOM file contains %1$s and webtrees expects to find %2$s in the media folder, then the GEDCOM media path would be %3$s.', '<span class="filename">/home/fab/documents/family/photo.jpeg</span>', '<span class="filename">family/photo.jpeg</span>', '<span class="filename">/home/fab/documents/</span>').
		'</p><p>'.
		WT_I18N::translate('This setting is only used when you read or write GEDCOM files.').
		'</p>';
	break;

case 'GENERATE_GUID':
	$title=WT_I18N::translate('Automatically create globally unique IDs');
	$text=WT_I18N::translate('<b>GUID</b> in this context is an acronym for “Globally Unique ID”.<br><br>GUIDs are intended to help identify each individual in a manner that is repeatable, so that central organizations such as the Family History Center of the LDS church in Salt Lake City, or even compatible programs running on your own server, can determine whether they are dealing with the same individual no matter where the GEDCOM file originates.  The goal of the Family History Center is to have a central repository of genealogical data and expose it through web services.  This will enable any program to access the data and update their data within it.<br><br>If you do not intend to share this GEDCOM file with anyone else, you do not need to let webtrees create these GUIDs; however, doing so will do no harm other than increasing the size of your GEDCOM file.');
	break;

case 'HIDE_GEDCOM_ERRORS':
	$title=WT_I18N::translate('GEDCOM errors');
	$text=WT_I18N::translate('Many genealogy programs create GEDCOM files with custom tags, and webtrees understands most of them.  When unrecognized tags are found, this option lets you choose whether to ignore them or display a warning message.');
	break;

case 'HIDE_LIVE_PEOPLE':
        $title=WT_I18N::translate('Privacy options');
        $text=WT_I18N::translate('This option will enable all privacy settings and hide the details of living individuals, as defined or modified on the Privacy tab of each GEDCOM’s configuration page.');
        $text .= '<p>';
		$text .= WT_I18N::plural('Note: “living” is defined (if no death or burial is known) as ending %d year after birth or estimated birth.','Note: “living” is defined (if no death or burial is known) as ending %d years after birth or estimated birth.', $WT_TREE->getPreference('MAX_ALIVE_AGE'), $WT_TREE->getPreference('MAX_ALIVE_AGE'));
		$text .= ' ';
		$text .= WT_I18N::translate('The length of time after birth can be set on the “Privacy” tab option “Age at which to assume an individual is dead”.');
		$text .= '</p>';
        break;

case 'INDEX_DIRECTORY':
	$title=WT_I18N::translate('Data folder');
	$text=
		'<p>'.
		/* I18N: Help text for the "Data folder" site configuration setting */ WT_I18N::translate('This folder will be used by webtrees to store media files, GEDCOM files, temporary files, etc.  These files may contain private data, and should not be made available over the internet.').
		'</p><p>'.
		/* I18N: “Apache” is a software program. */ WT_I18N::translate('To protect this private data, webtrees uses an Apache configuration file (.htaccess) which blocks all access to this folder.  If your web-server does not support .htaccess files, and you cannot restrict access to this folder, then you can select another folder, away from your web documents.').
		'</p><p>'.
		WT_I18N::translate('If you select a different folder, you must also move all files (except config.ini.php, index.php, and .htaccess) from the existing folder to the new folder.').
		'</p><p>'.
		WT_I18N::translate('The folder can be specified in full (e.g. /home/user_name/webtrees_data/) or relative to the installation folder (e.g. ../../webtrees_data/).').
		'</p>';
	break;

case 'INDI_FACTS_ADD':
	$title=WT_I18N::translate('All individual facts');
	$text=WT_I18N::translate('This is the list of GEDCOM facts that your users can add to individuals.  You can modify this list by removing or adding fact names, even custom ones, as necessary.  Fact names that appear in this list must not also appear in the “Unique individual facts” list.');
	break;

case 'INDI_FACTS_QUICK':
	$title=WT_I18N::translate('Quick individual facts');
	$text=WT_I18N::translate('This is the short list of GEDCOM individual facts that appears next to the full list and can be added with a single click.');
	break;

case 'INDI_FACTS_UNIQUE':
	$title=WT_I18N::translate('Unique individual facts');
	$text=WT_I18N::translate('This is the list of GEDCOM facts that your users can only add once to individuals.  For example, if BIRT is in this list, users will not be able to add more than one BIRT record to an individual.  Fact names that appear in this list must not also appear in the “All individual facts” list.');
	break;

case 'KEEP_ALIVE':
	$title=WT_I18N::translate('Extend privacy to dead individuals');
	$text=WT_I18N::translate('In some countries, privacy laws apply not only to living individuals, but also to those who have died recently.  This option will allow you to extend the privacy rules for living individuals to those who were born or died within a specified number of years.  Leave these values empty to disable this feature.');
	break;

case 'LANGUAGE':
	$title=WT_I18N::translate('Language');
	$text=WT_I18N::translate('If a visitor to the site has not specified a preferred language in their browser configuration, or they have specified an unsupported language, then this language will be used.  Typically, this setting applies to search engines.');
	break;

case 'LOGIN_URL':
	$title=/* I18N: A site configuration setting */ WT_I18N::translate('Login URL');
	$text=/* I18N: Help text for the “Login URL” site configuration setting */ WT_I18N::translate('You only need to enter a Login URL if you want to redirect to a different site or location when your users login.  This is very useful if you need to switch from http to https when your users login.  Include the full URL to <i>login.php</i>.  For example, https://www.yourserver.com/webtrees/login.php .');
	break;

case 'MAX_ALIVE_AGE':
	$title=WT_I18N::translate('Age at which to assume an individual is dead');
	$text=WT_I18N::translate('If this individual has any events other than death, burial, or cremation more recent than this number of years, he is considered to be “alive”.  Children’s birth dates are considered to be such events for this purpose.');
	break;

case 'MAX_DESCENDANCY_GENERATIONS':
	$title=WT_I18N::translate('Maximum descendancy generations');
	$text=WT_I18N::translate('Set the maximum number of generations to display on descendancy charts.');
	break;

case 'MAX_EXECUTION_TIME':
	// Find the default value for max_execution_time
	ini_restore('max_execution_time');
	$dflt_cpu=ini_get('max_execution_time');
	$title=WT_I18N::translate('PHP time limit');
	$text=
		WT_I18N::plural(
			'By default, your server allows scripts to run for %s second.',
			'By default, your server allows scripts to run for %s seconds.',
			$dflt_cpu, $dflt_cpu
		).
		' '.
		WT_I18N::translate('You can request a higher or lower limit, although the server may ignore this request.').
		' '.
		WT_I18N::translate('If you leave this setting empty, the default value will be used.');
	break;

case 'MAX_PEDIGREE_GENERATIONS':
	$title=WT_I18N::translate('Maximum pedigree generations');
	$text=WT_I18N::translate('Set the maximum number of generations to display on pedigree charts.');
	break;

case 'MEDIA_DIRECTORY':
	$title=WT_I18N::translate('Media folder');
	$text=
		'<p>'.
		WT_I18N::translate('This folder will be used to store the media files for this family tree.').
		'</p><p>'.
		WT_I18N::translate('If you select a different folder, you must also move any media files from the existing folder to the new one.').
		'</p><p>'.
		WT_I18N::translate('If two family trees use the same media folder, then they will be able to share media files.  If they use different media folders, then their media files will be kept separate.').
		'</p>';
	break;

case 'MEDIA_ID_PREFIX':
	$title=WT_I18N::translate('Media ID prefix');
	$text=WT_I18N::translate('When a new media record is added online in webtrees, a new ID for that media will be generated automatically.  The media ID will have this prefix.');
	break;

case 'MEDIA_UPLOAD':
	$title=WT_I18N::translate('Who can upload new media files?');
	$text=WT_I18N::translate('If you are concerned that users might upload inappropriate images, you can restrict media uploads to managers only.');
	break;

case 'MEMORY_LIMIT':
	// Find the default value for max_execution_time
	ini_restore('memory_limit');
	$dflt_mem=ini_get('memory_limit');
	$title=WT_I18N::translate('Memory limit');
	$text= /* I18N: %s is an amount of memory, such as 32MB */ WT_I18N::translate('By default, your server allows scripts to use %s of memory.', $dflt_mem).
		' '.
		WT_I18N::translate('You can request a higher or lower limit, although the server may ignore this request.').
		' '.
		WT_I18N::translate('If you leave this setting empty, the default value will be used.');
	break;

case 'META_DESCRIPTION':
	$title=WT_I18N::translate('Description META tag');
	$text=WT_I18N::translate('The value to place in the “meta description” tag in the HTML page header.  Leave this field empty to use the name of the family tree.');
	break;

case 'META_TITLE':
	$title=WT_I18N::translate('Add to TITLE header tag');
	$text=WT_I18N::translate('This text will be appended to each page title.  It will be shown in the browser’s title bar, bookmarks, etc.');
	break;

case 'NOTE_ID_PREFIX':
	$title=WT_I18N::translate('Note ID prefix');
	$text=WT_I18N::translate('When a new note record is added online in webtrees, a new ID for that note will be generated automatically.  The note ID will have this prefix.');
	break;

case 'PEDIGREE_FULL_DETAILS':
	$title=/* I18N: A site configuration setting */ WT_I18N::translate('Show chart details by default');
	$text=/* I18N: Help text for the “Show chart details by default” tree configuration setting */ WT_I18N::translate('This is the initial setting for the “show details” option on the charts.');
	break;

case 'PEDIGREE_LAYOUT':
	$title=/* I18N: A site configuration setting */ WT_I18N::translate('Default pedigree chart layout');
	$text=/* I18N: Help text for the “Default pedigree chart layout” tree configuration setting */ WT_I18N::translate('This option indicates whether the pedigree chart should be generated in landscape or portrait mode.');
	break;

case 'PEDIGREE_SHOW_GENDER':
	$title=WT_I18N::translate('Gender icon on charts');
	$text=WT_I18N::translate('This option controls whether or not to show the individual’s gender icon on charts.<br><br>Since the gender is also indicated by the color of the box, this option doesn’t conceal the gender.  The option simply removes some duplicate information from the box.');
	break;

case 'RELATIONSHIP_PATH_LENGTH':
	$title=WT_I18N::translate('Restrict to immediate family');
	$text=
		WT_I18N::translate('Where a user is associated to an individual record in a family tree and has a role of member, editor, or moderator, you can prevent them from accessing the details of distant, living relations.  You specify the number of relationship steps that the user is allowed to see.').
		'<br><br>'.
		WT_I18N::translate('For example, if you specify a path length of 2, the individual will be able to see their grandson (child, child), their aunt (parent, sibling), their step-daughter (spouse, child), but not their first cousin (parent, sibling, child).').
		'<br><br>'.
		WT_I18N::translate('Note: longer path lengths require a lot of calculation, which can make your site run slowly for these users.');
	break;

case 'SESSION_TIME':
	$title/* I18N: A site configuration setting */ =WT_I18N::translate('Session timeout');
	$text=/* I18N: Help text for the “Session timeout” site configuration setting */ WT_I18N::translate('The time in seconds that a webtrees session remains active before requiring a login.  The default is 7200, which is 2 hours.');
	break;

case 'SMTP_ACTIVE':
	$title=/* I18N: A site configuration setting */ WT_I18N::translate('Messages');
	$text=/* I18N: Help text for the “Messages” site configuration setting */ WT_I18N::translate('webtrees needs to send emails, such as password reminders and site notifications.  To do this, it can use this server’s built in PHP mail facility (which is not always available) or an external SMTP (mail-relay) service, for which you will need to provide the connection details.');
	break;

case 'SMTP_AUTH_PASS':
	$title=WT_I18N::translate('Password');
	$text=WT_I18N::translate('The password required for authentication with the SMTP server.');
	break;

case 'SMTP_AUTH_USER':
	$title=WT_I18N::translate('Username');
	$text=WT_I18N::translate('The user name required for authentication with the SMTP server.');
	break;

case 'SMTP_AUTH':
	$title=WT_I18N::translate('Use password');
	$text=/* I18N: Help text for the “Use password” site configuration setting */ WT_I18N::translate('Most SMTP servers require a password.');
	break;

case 'SMTP_FROM_NAME':
	$title=/* I18N: A site configuration setting */ WT_I18N::translate('Sender name');
	$text=/* I18N: Help text for the “Sender name” site configuration setting */ WT_I18N::translate('This name is used in the “From” field, when sending automatic emails from this server.');
	break;

case 'SMTP_HELO':
	$title=/* I18N: A site configuration setting */ WT_I18N::translate('Sending server name');
	$text=/* I18N: Help text for the “Sending server name” site configuration setting */ WT_I18N::translate('Many mail servers require that the sending server identifies itself correctly, using a valid domain name.');
	break;

case 'SMTP_HOST':
	$title=/* I18N: A site configuration setting */ WT_I18N::translate('Server name');
	$text=/* I18N: Help text for the “Server name” site configuration setting */ WT_I18N::translate('This is the name of the SMTP server. “localhost” means that the mail service is running on the same computer as your web server.');
	break;

case 'SMTP_PORT':
	$title=/* I18N: A site configuration setting */ WT_I18N::translate('Port number');
	$text=/* I18N: Help text for the "Port number" site configuration setting */ WT_I18N::translate('By default, SMTP works on port 25.');
	break;

case 'SMTP_SSL':
	$title=/* I18N: A site configuration setting */ WT_I18N::translate('Secure connection');
	$text=/* I18N: Help text for the "Secure connection" site configuration setting */ WT_I18N::translate('Most servers do not use secure connections.');
	break;

case 'WEBTREES_EMAIL':
	$title=WT_I18N::translate('webtrees reply address');
	$text=WT_I18N::translate('E-mail address to be used in the “From:” field of e-mails that webtrees creates automatically.<br><br>webtrees can automatically create e-mails to notify administrators of changes that need to be reviewed.  webtrees also sends notification e-mails to users who have requested an account.<br><br>Usually, the “From:” field of these automatically created e-mails is something like <i>From: webtrees-noreply@yoursite</i> to show that no response to the e-mail is required.  To guard against spam or other e-mail abuse, some e-mail systems require each message’s “From:” field to reflect a valid e-mail account and will not accept messages that are apparently from account <i>webtrees-noreply</i>.');
	break;

case 'PREFER_LEVEL2_SOURCES':
	$title=WT_I18N::translate('Source type');
	$text=WT_I18N::translate('When adding new close relatives, you can add source citations to the records (e.g. INDI, FAM) or the facts (BIRT, MARR, DEAT).  This option controls which checkboxes are ticked by default.');
	break;

case 'QUICK_REQUIRED_FACTS':
	$title=WT_I18N::translate('Facts for new individuals');
	$text=WT_I18N::translate('This is a comma separated list of GEDCOM fact tags that will be shown when adding a new individual.  For example, if BIRT is in the list, fields for birth date and birth place will be shown on the form.');
	break;

case 'QUICK_REQUIRED_FAMFACTS':
	$title=WT_I18N::translate('Facts for new families');
	$text=WT_I18N::translate('This is a comma separated list of GEDCOM fact tags that will be shown when adding a new family.  For example, if MARR is in the list, then fields for marriage date and marriage place will be shown on the form.');
	break;

case 'REPO_FACTS_ADD':
	$title=WT_I18N::translate('All repository facts');
	$text=WT_I18N::translate('This is the list of GEDCOM facts that your users can add to repositories.  You can modify this list by removing or adding fact names, even custom ones, as necessary.  Fact names that appear in this list must not also appear in the “Unique repository facts” list.');
	break;

case 'REPO_FACTS_QUICK':
	$title=WT_I18N::translate('Quick repository facts');
	$text=WT_I18N::translate('This is the short list of GEDCOM repository facts that appears next to the full list and can be added with a single click.');
	break;

case 'REPO_FACTS_UNIQUE':
	$title=WT_I18N::translate('Unique repository facts');
	$text=WT_I18N::translate('This is the list of GEDCOM facts that your users can only add once to repositories.  For example, if NAME is in this list, users will not be able to add more than one NAME record to a repository.  Fact names that appear in this list must not also appear in the “All repository facts” list.');
	break;

case 'REPO_ID_PREFIX':
	$title=WT_I18N::translate('Repository ID prefix');
	$text=WT_I18N::translate('When a new repository record is added online in webtrees, a new ID for that repository will be generated automatically.  The repository ID will have this prefix.');
	break;

case 'REQUIRE_ADMIN_AUTH_REGISTRATION':
	$title=WT_I18N::translate('Require an administrator to approve new user registrations');
	$text=WT_I18N::translate('If the option <b>Allow visitors to request account registration</b> is enabled this setting controls whether the admin must approve the registration.<br><br>Setting this to <b>Yes</b> will require that all new users first verify themselves and then be approved by an admin before they can login.  With this setting on <b>No</b>, the “Approved by administrator” checkbox will be checked automatically when users verify their account, thus allowing an immediate login afterwards without admin intervention.');
	break;

case 'REQUIRE_AUTHENTICATION':
	$title=WT_I18N::translate('Require visitor authentication');
	$text=WT_I18N::translate('Enabling this option will force all visitors to login before they can view any data on the site.');
	break;

case 'SERVER_URL':
	$title=WT_I18N::translate('Website URL');
	$text=/* I18N: Help text for the "Website URL" site configuration setting */ WT_I18N::translate('If your site can be reached using more than one URL, such as <b>http://www.example.com/webtrees/</b> and <b>http://webtrees.example.com/</b>, you can specify the preferred URL.  Requests for the other URLs will be redirected to the preferred one.');
	break;

case 'SHOW_COUNTER':
	$title=WT_I18N::translate('Hit counters');
	$text=WT_I18N::translate('Show hit counters on Portal and Individual pages.');
	break;

case 'SHOW_DEAD_PEOPLE':
	$title=WT_I18N::translate('Show dead individuals');
	$text=WT_I18N::translate('Set the privacy access level for all dead individuals.');
	break;

case 'SHOW_EST_LIST_DATES':
	$title=WT_I18N::translate('Estimated dates for birth and death');
	$text=WT_I18N::translate('This option controls whether or not to show estimated dates for birth and death instead of leaving blanks on individual lists and charts for individuals whose dates are not known.');
	break;

case 'SHOW_FACT_ICONS':
	$title=WT_I18N::translate('Show fact icons');
	$text=WT_I18N::translate('Set this to <b>Yes</b> to display icons near Fact names on the Personal Facts and Details page.  Fact icons will be displayed only if they exist in the <i>images/facts</i> directory of the current theme.');
	break;

case 'SHOW_GEDCOM_RECORD':
	$title=WT_I18N::translate('Allow users to see raw GEDCOM records');
	$text=WT_I18N::translate('Setting this to <b>Yes</b> will place links on individuals, sources, and families to let users bring up another window containing the raw data taken right out of the GEDCOM file.');
	break;

case 'SHOW_LDS_AT_GLANCE':
	$title=WT_I18N::translate('LDS ordinance codes in chart boxes');
	$text=WT_I18N::translate('Setting this option to <b>Yes</b> will show status codes for LDS ordinances in chart boxes.<ul><li><b>B</b> - Baptism</li><li><b>E</b> - Endowed</li><li><b>S</b> - Sealed to spouse</li><li><b>P</b> - Sealed to parents</li></ul>An individual who has all of the ordinances done will have <b>BESP</b> printed after their name.  Missing ordinances are indicated by <b>_</b> in place of the corresponding letter code.  For example, <b>BE__</b> indicates missing <b>S</b> and <b>P</b> ordinances.');
	break;

case 'SHOW_LEVEL2_NOTES':
	$title=WT_I18N::translate('Show all notes and source references on notes and sources tabs');
	$text=WT_I18N::translate('This option controls whether Notes and Source references that are attached to Facts should be shown on the Notes and Sources tabs of the Individual page.<br><br>Ordinarily, the Notes and Sources tabs show only Notes and Source references that are attached directly to the individual’s database record.  These are <i>level 1</i> Notes and Source references.<br><br>The <b>Yes</b> option causes these tabs to also show Notes and Source references that are part of the various Facts in the individual’s database record.  These are <i>level 2</i> Notes and Source references because the various Facts are at level 1.');
	break;

case 'SHOW_LIVING_NAMES':
	$title=WT_I18N::translate('Names of private individuals');
	$text=WT_I18N::translate('This option will show the names (but no other details) of private individuals.  Individuals are private if they are still alive or if a privacy restriction has been added to their individual record.  To hide a specific name, add a privacy restriction to that name record.');

	break;

case 'SHOW_MEDIA_DOWNLOAD':
	$title=WT_I18N::translate('Show download link in media viewer');
	$text=WT_I18N::translate('The Media Viewer can show a link which, when clicked, will download the media file to the local PC.<br><br>You may want to hide the download link for security reasons.');
	break;

case 'SHOW_PARENTS_AGE':
	$title=WT_I18N::translate('Show age of parents next to child’s birthdate');
	$text=WT_I18N::translate('This option controls whether or not to show age of father and mother next to child’s birthdate on charts.');
	break;

case 'SHOW_PEDIGREE_PLACES':
	$title=WT_I18N::translate('Abbreviate place names');
	$text=WT_I18N::translate('Place names are frequently too long to fit on charts, lists, etc.  They can be abbreviated by showing just the first few parts of the name, such as <i>village, county</i>, or the last few part of it, such as <i>region, country</i>.');
	break;

case 'SHOW_PRIVATE_RELATIONSHIPS':
	$title=WT_I18N::translate('Show private relationships');
	$text=WT_I18N::translate('This option will retain family links in private records.  This means that you will see empty “private” boxes on the pedigree chart and on other charts with private individuals.');
	break;

case 'SHOW_REGISTER_CAUTION':
	$title=WT_I18N::translate('Show acceptable use agreement on “Request new user account” page');
	$text=WT_I18N::translate('When set to <b>Yes</b>, the following message will appear above the input fields on the “Request new user account” page:<div class="list_value_wrap"><div class="largeError">Notice:</div><div class="error">By completing and submitting this form, you agree:<ul><li>to protect the privacy of living individuals listed on our site;</li><li>and in the text box below, to explain to whom you are related, or to provide us with information on someone who should be listed on our site.</li></ul></div></div>');
	break;

case 'SHOW_STATS':
	$title=WT_I18N::translate('Execution statistics');
	$text=WT_I18N::translate('Show runtime statistics and database queries at the bottom of every page.');
	break;

case 'SOURCE_ID_PREFIX':
	$title=WT_I18N::translate('Source ID prefix');
	$text=WT_I18N::translate('When a new source record is added online in webtrees, a new ID for that source will be generated automatically.  The source ID will have this prefix.');
	break;

case 'SOUR_FACTS_ADD':
	$title=WT_I18N::translate('All source facts');
	$text=WT_I18N::translate('This is the list of GEDCOM facts that your users can add to sources.  You can modify this list by removing or adding fact names, even custom ones, as necessary.  Fact names that appear in this list must not also appear in the “Unique source facts” list.');
	break;

case 'SOUR_FACTS_QUICK':
	$title=WT_I18N::translate('Quick source facts');
	$text=WT_I18N::translate('This is the short list of GEDCOM source facts that appears next to the full list and can be added with a single click.');
	break;

case 'SOUR_FACTS_UNIQUE':
	$title=WT_I18N::translate('Unique source facts');
	$text=WT_I18N::translate('This is the list of GEDCOM facts that your users can only add once to sources.  For example, if TITL is in this list, users will not be able to add more than one TITL record to a source.  Fact names that appear in this list must not also appear in the “All source facts” list.');
	break;

case 'SUBLIST_TRIGGER_I':
	$title=WT_I18N::translate('Maximum number of surnames on individual list');
	$text=WT_I18N::translate('Long lists of individuals with the same surname can be broken into smaller sub-lists according to the first letter of the individual’s given name.<br><br>This option determines when sub-listing of surnames will occur.  To disable sub-listing completely, set this option to zero.');
	break;

case 'SURNAME_TRADITION':
	$title=WT_I18N::translate('Surname tradition');
	$text=
		WT_I18N::translate('When you add a new family member, a default surname can be provided.  This surname will depend on the local tradition.').
		'<br><br><dl><dt>'.
		/* I18N: https://en.wikipedia.org/wiki/Patrilineal (a system where children take their father’s surname */
		WT_I18N::translate('patrilineal').
		'</dt><dd>'.
		/* I18N: In the patrilineal surname tradition, ... */
		WT_I18N::translate('Children take their father’s surname.').
		'</dd></dl><dl><dt>'.
		/* I18N: https://en.wikipedia.org/wiki/Matrilineal (a system where children take their mother’s surname */
		WT_I18N::translate('matrilineal').
		'</dt><dd>'.
		/* I18N: In the matrilineal surname tradition, ... */
		WT_I18N::translate('Children take their mother’s surname.').
		'</dd></dl><dl><dt>'.
		WT_I18N::translate_c('Surname tradition', 'paternal').
		'</dt><dd>'.
		WT_I18N::translate('Children take their father’s surname.') . '<br>' .
		/* I18N: In the paternal surname tradition, ... */
		WT_I18N::translate('Wives take their husband’s surname.').
		'</dd></dl><dl><dt>'.
		WT_I18N::translate_c('Surname tradition', 'Spanish').
		'</dt><dd>'.
		/* I18N: In the Spanish surname tradition, ... */
		WT_I18N::translate('Children take one surname from the father and one surname from the mother.').
		'</dd></dl><dl><dt>'.
		WT_I18N::translate_c('Surname tradition', 'Portuguese').
		'</dt><dd>'.
		/* I18N: In the Portuguese surname tradition, ... */
		WT_I18N::translate('Children take one surname from the mother and one surname from the father.').
		'</dd></dl><dl><dt>'.
		WT_I18N::translate_c('Surname tradition', 'Icelandic').
		'</dt><dd>'.
		/* I18N: In the Icelandic surname tradition, ... */
		WT_I18N::translate('Children take a patronym instead of a surname.').
		'</dd></dl><dl><dt>'.
		WT_I18N::translate_c('Surname tradition', 'Polish').
		'</dt><dd>'.
		WT_I18N::translate('Children take their father’s surname.') . '<br>' .
		WT_I18N::translate('Wives take their husband’s surname.') . '<br>' .
		/* I18N: In the Polish surname tradition, ... */
		WT_I18N::translate('Surnames are inflected to indicate an individual’s gender.').
		'</dd></dl><dl><dt>'.
		WT_I18N::translate_c('Surname tradition', 'Lithuanian').
		'</dt><dd>'.
		WT_I18N::translate('Children take their father’s surname.') . '<br>' .
		WT_I18N::translate('Wives take their husband’s surname.') . '<br>' .
		/* I18N: In the Lithuanian surname tradition, ... */
		WT_I18N::translate('Surnames are inflected to indicate an individual’s gender and marital status.').
		'</dd></dl>';
	break;

case 'THEME':
	$title=WT_I18N::translate('Theme');
	$text=
		/* I18N: Help text for the "Default theme" site configuration setting */ WT_I18N::translate('You can change the appearance of webtrees using “themes”.  Each theme has a different style, layout, color scheme, etc.').
		'<br><br>'.
		WT_I18N::translate('Themes can be selected at three levels: user, GEDCOM, and site.  User settings take priority over GEDCOM settings, which in turn take priority over the site setting.  Selecting “default theme” at user level will give the setting for the current GEDCOM.  Selecting “default theme” at GEDCOM level will give the site setting.');
	break;

case 'THUMBNAIL_WIDTH':
	$title=WT_I18N::translate('Width of generated thumbnails');
	$text=WT_I18N::translate('This is the width (in pixels) that the program will use when automatically generating thumbnails.  The default setting is 100.');
	break;

case 'GEONAMES_ACCOUNT':
	$title=WT_I18N::translate('Use the GeoNames database for autocomplete on places');
	$text=WT_I18N::translate('The website www.geonames.org provides a large database of place names.  This can be searched when entering new places.  To use this feature, you must register for a free account at www.geonames.org and provide the username.');
	break;

case 'USE_REGISTRATION_MODULE':
	$title=WT_I18N::translate('Allow visitors to request account registration');
	$text=WT_I18N::translate('Gives visitors the option of registering themselves for an account on the site.<br><br>The visitor will receive an email message with a code to verify his application for an account.  After verification, an administrator will have to approve the registration before it becomes active.');
	break;

case 'USE_RELATIONSHIP_PRIVACY':
	$title=WT_I18N::translate('Use relationship privacy');
	$text=WT_I18N::translate('<b>No</b> means that authenticated users can see the details of all living individuals.  <b>Yes</b> means that users can only see the private information of living individuals they are related to.<br><br>This option sets the default for all users who have access to this genealogical database.  The administrator can override this option for individual users by editing the user’s account details.');
	break;

case 'USE_RIN':
	$title=WT_I18N::translate('Use RIN number instead of GEDCOM ID');
	$text=WT_I18N::translate('Set to <b>Yes</b> to use the RIN number instead of the GEDCOM ID when asked for individual IDs in configuration files, user settings, and charts.  This is useful for genealogy programs that do not consistently export GEDCOM files with the same ID assigned to each individual but always use the same RIN.');
	break;

case 'USE_SILHOUETTE':
	$title=WT_I18N::translate('Use silhouettes');
	$text=WT_I18N::translate('Use silhouette images when no highlighted image for that individual has been specified.  The images used are specific to the gender of the individual in question.<br><br><table><tr><td style="vertical-align:middle">This image might be used when the gender of the individual is unknown:')
	." </td><td><img src=\"" . $WT_IMAGES["default_image_U"] . "\" width=\"40\" alt=\"\" title=\"\" /></td></tr></table>";
	break;

case 'Watermarks':
	$title = WT_I18N::translate('Watermarks');
	$text  =
		'<p>' .
		WT_I18N::translate('A watermark is text that is added to an image, to discourage others from copying it without permission.') .
		'</p><p>' .
		WT_I18N::translate('Watermarks are optional and normally shown just to visitors.') .
		'</p><p>' .
		WT_I18N::translate('Watermarks can be slow to generate for large images.  Busy sites may prefer to generate them once and store the watermarked image on the server.') .
		'</p>';
	break;

case 'WEBMASTER_USER_ID':
	$title=WT_I18N::translate('Technical help contact');
	$text=WT_I18N::translate('The individual to be contacted about technical questions or errors encountered on your site.');
	break;

case 'WELCOME_TEXT_AUTH_MODE_CUST':
	$title=WT_I18N::translate('Custom welcome text');
	$text=WT_I18N::translate('If you have opted for custom welcome text, you can type that text here.  To set this text for other languages, you must switch to that language, and visit this page again.');
	break;

case 'WELCOME_TEXT_AUTH_MODE':
	$title=WT_I18N::translate('Welcome text on login page');
	$text=WT_I18N::translate('Here you can choose text to appear on the login screen.  You must determine which predefined text is most appropriate.<br><br>You can also choose to enter your own custom Welcome text.  Please refer to the help text associated with the <b>Custom Welcome text</b> field for more information.<br><br>The predefined texts are:<ul><li><b>Predefined text that states all users can request a user account:</b><div class="list_value_wrap"><center><b>Welcome to this genealogy website</b></center><br>Access to this site is permitted to every visitor who has a user account.<br><br>If you have a user account, you can login on this page.  If you don’t have a user account, you can apply for one by clicking on the appropriate link below.<br><br>After verifying your application, the site administrator will activate your account.  You will receive an email when your application has been approved.</div><br></li><li><b>Predefined text that states admin will decide on each request for a user account:</b><div class="list_value_wrap"><center><b>Welcome to this genealogy website</b></center><br>Access to this site is permitted to <u>authorized</u> users only.<br><br>If you have a user account you can login on this page.  If you don’t have a user account, you can apply for one by clicking on the appropriate link below.<br><br>After verifying your information, the administrator will either approve or decline your account application.  You will receive an email message when your application has been approved.</div><br></li><li><b>Predefined text that states only family members can request a user account:</b><div class="list_value_wrap"><center><b>Welcome to this genealogy website</b></center><br>Access to this site is permitted to <u>family members only</u>.<br><br>If you have a user account you can login on this page.  If you don’t have a user account, you can apply for one by clicking on the appropriate link below.<br><br>After verifying the information you provide, the administrator will either approve or decline your request for an account.  You will receive an email when your request is approved.</div></li></ul>');
	break;

case 'WORD_WRAPPED_NOTES':
	$title=WT_I18N::translate('Add spaces where notes were wrapped');
	$text=WT_I18N::translate('Some genealogy programs wrap notes at word boundaries while others wrap notes anywhere.  This can cause webtrees to run words together.  Setting this to <b>Yes</b> will add a space between words where they are wrapped in the original GEDCOM file during the import process.  If you have already imported the file you will need to re-import it.');
	break;

	//////////////////////////////////////////////////////////////////////////////
	// This section contains all the other help items.
	//////////////////////////////////////////////////////////////////////////////

case 'add_facts':
	$title=WT_I18N::translate('Add a fact');
	$text=WT_I18N::translate('Here you can add a fact to the record being edited.<br><br>First choose a fact from the drop-down list, then click the <b>Add</b> button.  All possible facts that you can add to the database are in that drop-down list.');
	$text.='<br><br>';
	$text.='<b>'.WT_I18N::translate('Add from clipboard').'</b>';
	$text.='<br><br>';
	$text.=WT_I18N::translate('webtrees allows you to copy up to 10 facts, with all their details, to a clipboard.  This clipboard is different from the clippings cart that you can use to export portions of your database.<br><br>You can select any of the facts from the clipboard and copy the selected fact to the individual, family, media, source, or repository record currently being edited.  However, you cannot copy facts of dissimilar record types.  For example, you cannot copy a marriage fact to a source or an individual record since the marriage fact is associated only with family records.<br><br>This is very helpful when entering similar facts, such as census facts, for many individuals or families.');
	break;

case 'add_new_gedcom':
	$title=WT_I18N::translate('Create a new family tree');
	$text=
		WT_I18N::translate('This option creates a new family tree.  The name you give it will be used to generate URLs and filenames, so you should choose something short, simple, and avoid punctuation.').
		'<br><br>'.
		WT_I18N::translate('After creating the family tree, you will be able to upload or import data from a GEDCOM file.');
	break;

case 'add_note':
	// This is a general help text for multiple pages
	$title=WT_I18N::translate('Add a new note');
	$text=WT_I18N::translate('If you have a note to add to this record, this is the place to do so.<br><br>Just click the link, a window will open, and you can type your note.  When you are finished typing, just click the button below the box, close the window, and that’s all.');
	break;

case 'add_shared_note':
	// This is a general help text for multiple pages
	$title=WT_I18N::translate('Add a new shared note');
	$text=WT_I18N::translate('When you click the <b>Add a new shared note</b> link, a new window will open.  You can choose to link to an existing shared note, or you can create a new shared note and at the same time create a link to it.');
	break;

case 'add_source':
	// This is a general help text for multiple pages
	$title=WT_I18N::translate('Add a new source citation');
	$text=WT_I18N::translate('Here you can add a source <b>Citation</b> to this record.<br><br>Just click the link, a window will open, and you can choose the source from the list (Find ID) or create a new source and then add the citation.<br><br>Adding sources is an important part of genealogy because it allows other researchers to verify where you obtained your information.');
	break;

case 'annivers_year_select':
	$title=WT_I18N::translate('Year input box');
	$text=WT_I18N::translate('This input box lets you change that year of the calendar.  Type a year into the box and press <b>Enter</b> to change the calendar to that year.<br><br><b>Advanced features</b> for <b>View year</b><dl><dt><b>More than one year</b></dt><dd>You can search for dates in a range of years.<br><br>Year ranges are <u>inclusive</u>.  This means that the date range extends from 1 January of the first year of the range to 31 December of the last year mentioned.  Here are a few examples of year ranges:<br><br><b>1992-5</b> for all events from 1992 to 1995.<br><b>1972-89</b> for all events from 1972 to 1989.<br><b>1610-759</b> for all events from 1610 to 1759.<br><b>1880-1905</b> for all events from 1880 to 1905.<br><b>880-1105</b> for all events from 880 to 1105.<br><br>To see all the events in a given decade or century, you can use <b>?</b> in place of the final digits.  For example, <b>197?</b> for all events from 1970 to 1979 or <b>16??</b> for all events from 1600 to 1699.<br><br>Selecting a range of years will change the calendar to the year view.</dd></dl>');
	break;

case 'apply_privacy':
	$title=WT_I18N::translate('Apply privacy settings?');
	$text=WT_I18N::translate('This option will remove private data from the downloaded GEDCOM file.  The file will be filtered according to the privacy settings that apply to each access level.  Privacy settings are specified on the GEDCOM configuration page.');
	break;

case 'block_move_right':
	$title=WT_I18N::translate('Move list entries');
	$text=WT_I18N::translate('Use these buttons to move an entry from one list to another.<br><br>Highlight the entry to be moved, and then click a button to move or copy that entry in the direction of the arrow.  Use the <b>&raquo;</b> and <b>&laquo;</b> buttons to move the highlighted entry from the leftmost to the rightmost list or vice-versa.  Use the <b>&gt;</b> and <b>&lt;</b> buttons to move the highlighted entry between the Available blocks list and the list to its right or left.<br><br>The entries in the Available Blocks list do not change, regardless of what you do with the Move right and Move left buttons.  This is so because the same block can appear several times on the same page.  The HTML block is a good example of why you might want to do this.');
	break;

case 'block_move_up':
	$title=WT_I18N::translate('Move list entries');
	$text=WT_I18N::translate('Use these buttons to re-arrange the order of the entries within the list.  The blocks will be printed in the order in which they are listed.<br><br>Highlight the entry to be moved, and then click a button to move that entry up or down.');
	break;

case 'default_gedcom':
	$title=WT_I18N::translate('Default family tree');
	$text=WT_I18N::translate('This option selects the family tree that is shown to visitors when they first arrive at the site.');
	break;

case 'default_individual':
	$title=WT_I18N::translate('Default individual');
	$text=WT_I18N::translate('This individual will be selected by default when viewing charts and reports.');
	break;

case 'download_gedcom':
	$title=WT_I18N::translate('Download family tree');
	$text=WT_I18N::translate('This option will download the family tree to a GEDCOM file on your computer.');
	break;

case 'download_zipped':
	$title=WT_I18N::translate('Download ZIP file');
	$text=WT_I18N::translate('When you check this option, a copy of the GEDCOM file will be compressed into ZIP format before the download begins.  This will reduce its size considerably, but you will need to use a compatible Unzip program (WinZIP, for example) to decompress the transmitted GEDCOM file before you can use it.<br><br>This is a useful option for downloading large GEDCOM files.  There is a risk that the download time for the uncompressed file may exceed the maximum allowed execution time, resulting in incompletely downloaded files.  The ZIP option should reduce the download time by 75 percent.');
	break;

case 'edit_add_ASSO':
	$title=WT_I18N::translate('Add a new associate');
	$text=WT_I18N::translate('Add a new associate allows you to link a fact with an associated individual in the site.  This is one way in which you might record that someone was the godfather of another individual.');
	break;

case 'edit_add_GEDFact_ASSISTED':
	$title=WT_I18N::translate('GEDFact shared note assistant');
	$text=WT_I18N::translate('Clicking the “+” icon will open the GEDFact shared note assistant window.<br>Specific help will be found there.<br><br>When you click the “save” button, the ID of the shared note will be pasted here.');
	break;

case 'edit_add_NOTE':
	$title=WT_I18N::translate('Add a new note');
	$text=WT_I18N::translate('This section allows you to add a new note to the fact that you are currently editing.  Notes are free-form text and will appear in the Fact Details section of the page.');
	break;

case 'edit_add_SHARED_NOTE':
	$title=WT_I18N::translate('Add a new shared note');
	$text=WT_I18N::translate('Shared notes, like regular notes, are free-form text.  Unlike regular notes, each shared note can be linked to more than one individual, family, source, or fact.<br><br>By clicking the appropriate icon, you can establish a link to an existing shared note or create a new shared note and at the same time link to it.  If a link to an existing shared note has already been established, you can also edit that note’s contents.<br><ul><li><b>Link to an existing shared note</b><div style="padding-left:20px;">If you already know the ID number of the desired shared note, you can enter that number directly into the field.<br><br>When you click the <b>Find shared note</b> icon, you will be able to search the text of all existing shared notes and then choose one of them.  The ID number of the chosen note will be entered into the field automatically.<br><br>You must click the <b>Add</b> button to update the original record.</div><br></li><li><b>Create a new shared note</b><div style="padding-left:20px;">When you click the <b>Create a new shared note</b> icon, a new window will open.  You can enter the text of the new note as you wish.  As with regular notes, you can enter URLs.<br><br>When you click the <b>Save</b> button, you will see a message with the ID number of the newly created shared note.  You should click on this message to close the editing window and also copy that new ID number directly into the ID number field.  If you just close the window, the newly created ID number will not be copied automatically.<br><br>You must click the <b>Add</b> button to update the original record.</div><br></li><li><b>Edit an existing shared note</b><div style="padding-left:20px;">When you click the <b>Edit shared note</b> icon, a new window will open.  You can change the text of the existing shared note as you wish.  As with regular notes, you can enter URLs.<br><br>When you click the <b>Save</b> button, the text of the shared note will be updated.  You can close the window and then click the <b>Save</b> button again.<br><br>When you change the text of a shared note, your change will be reflected in all places to which that shared note is currently linked.  New links that you establish after having made your change will also use the updated text.</div></li></ul>');
	if (array_key_exists('GEDFact_assistant', WT_Module::getActiveModules())) {
		$text.='<p class="warning">'.WT_I18N::translate('You should avoid using the vertical line character “|” in your notes.  It is used internally by webtrees and may cause your note to display incorrectly.').'</p>';
	}
	break;

case 'edit_add_SOUR':
	$title=WT_I18N::translate('Add a new source citation');
	$text=WT_I18N::translate('This section allows you to add a new source citation to the fact that you are currently editing.<br><br>In the Source field you enter the ID for the source.  Click the “Create a new source” link if you need to enter a new source.  In the citation details field you would enter the page number or other information that might help someone find the information in the source.  In the Text field you would enter the text transcription from the source.');
	break;

case 'edit_edit_raw':
	$title=WT_I18N::translate('Edit raw GEDCOM');
	$text=
		WT_I18N::translate('This page allows you to bypass the usual forms, and edit the underlying data directly.  It is an advanced option, and you should not use it unless you understand the GEDCOM format.  If you make a mistake here, it can be difficult to fix.').
		'<br><br>'.
		/* I18N: %s is a URL */ WT_I18N::translate('You can download a copy of the GEDCOM specification from %s.', '<a href="http://wiki.webtrees.net/w/images-en/Ged551-5.pdf">http://wiki.webtrees.net/w/images-en/Ged551-5.pdf</a>');
	break;

case 'edit_merge':
	$title=WT_I18N::translate('Merge records');
	$text=WT_I18N::translate('This page will allow you to merge two GEDCOM records from the same GEDCOM file.<br><br>This is useful for individuals who have merged GEDCOMs and now have many individuals, families, and sources that are the same.<br><br>The page consists of three steps.<br><ol><li>You enter two GEDCOM IDs.  The IDs <u>must</u> be of the same type.  You cannot merge an individual and a family or family and source, for example.<br>In the <b>Merge To ID:</b> field enter the ID of the record you want to be the new record after the merge is complete.<br>In the <b>Merge From ID:</b> field enter the ID of the record whose information will be merged into the Merge To ID: record.  This record will be deleted after the Merge.</li><li>You select what facts you want to keep from the two records when they are merged.  Just click the checkboxes next to the ones you want to keep.</li><li>You inspect the results of the merge, just like with all other changes made online.</li></ol>Someone with Accept rights will have to authorize your changes to make them permanent.');
	break;

case 'edit_SOUR_EVEN':
	$title=WT_I18N::translate('Associate events with this source');
	$text=WT_I18N::translate('Each source records specific events, generally for a given date range and for a place jurisdiction.  For example a Census records census events and church records record birth, marriage, and death events.<br><br>Select the events that are recorded by this source from the list of events provided.  The date should be specified in a range format such as <i>FROM 1900 TO 1910</i>.  The place jurisdiction is the name of the lowest jurisdiction that encompasses all lower-level places named in this source.  For example, “Oneida, Idaho, USA” would be used as a source jurisdiction place for events occurring in the various towns within Oneida County. “Idaho, USA” would be the source jurisdiction place if the events recorded took place not only in Oneida County but also in other counties in Idaho.');
	break;

case 'edituser_contact_meth':
	$title=WT_I18N::translate('Preferred contact method');
	$text=WT_I18N::translate('webtrees has several different contact methods.  The administrator determines which method will be used to contact him.  You have control over the method to be used to contact <u>you</u>.  Depending on site configuration, some of the listed methods may not be available to you.');
	$text.='<br><br><dl><dt>';
	$text.=WT_I18N::translate('Internal messaging');
	$text.='</dt><dd>';
	$text.=WT_I18N::translate('With this option, the webtrees internal messaging system will be used and no emails will be sent.  You will receive only <u>internal</u> messages from the other users.  When another site user sends you a message, that message will appear in the Message block on your “My page”.  If you have removed this block from your “My page”, you will not see any messages.  They will, however, show up as soon as you configure your “My page” to again have the Message block.');
	$text.='</dd><dt>';
	$text.=WT_I18N::translate('Internal messaging with emails');
	$text.='</dt><dd>';
	$text.=WT_I18N::translate('This option is like webtrees internal messaging, with one addition.  As an extra, a copy of the message will also be sent to the email address you configured on your Account page.  This is the default contact method.');
	$text.='</dd><dt>';
	$text.=WT_I18N::translate('Mailto link');
	$text.='</dt><dd>';
	$text.=WT_I18N::translate('With this option, you will only receive email messages at the address you configured on your Account page.  The messaging system internal to webtrees will not be used at all, and there will never be any messages in the Message block on your “My page”.');
	$text.='</dd><dt>';
	$text.=WT_I18N::translate('No contact method');
	$text.='</dt><dd>';
	$text.=WT_I18N::translate('With this option, you will not receive any messages.  Even the administrator will not be able to reach you.');
	$text.='</dd></dl>';
	break;

case 'edituser_gedcomid':
	$title=WT_I18N::translate('Individual record');
	$text=WT_I18N::translate('This is a link to your own record in the family tree.  If this is the wrong individual, contact an administrator.');
	break;

case 'email':
	$title=WT_I18N::translate('Email address');
	$text=WT_I18N::translate('This email address will be used to send you password reminders, site notifications, and messages from other family members who are registered on the site.');
	break;

case 'export_gedcom':
	$title=WT_I18N::translate('Export family tree');
	$text=
		'<p>' .
		WT_I18N::translate('This option will save the family tree to a GEDCOM file on the server.') .
		'</p><p>' .
		/* I18N: %s is a folder name */
		WT_I18N::translate('GEDCOM files are stored in the %s folder.', '<b dir="auto">' . WT_DATA_DIR . '</b>') .
		'</p>';
	break;

case 'fambook_descent':
	$title=WT_I18N::translate('Descendant generations');
	$text=WT_I18N::translate('This value determines the number of descendant generations of the root individual that will be printed in hourglass format.');
	break;

case 'fan_width':
	$title=WT_I18N::translate('Width');
	$text=WT_I18N::translate('Here you can change the diagram width from 50 percent to 300 percent.  At 100 percent the output image is about 640 pixels wide.');
	break;

case 'gedcom_news_archive':
	$title=WT_I18N::translate('View archive');
	$text=WT_I18N::translate('To reduce the height of the News block, the administrator has hidden some articles.  You can reveal these hidden articles by clicking the <b>View archive</b> link.');
	break;

case 'gedcom_news_flag':
	$title=WT_I18N::translate('Limit:');
	$text=WT_I18N::translate('Enter the limiting value here.<br><br>If you have opted to limit the news article display according to age, any article older than the number of days entered here will be hidden from view.  If you have opted to limit the news article display by number, only the specified number of recent articles, ordered by age, will be shown.  The remaining articles will be hidden from view.<br><br>Zeros entered here will disable the limit, causing all news articles to be shown.');
	break;

case 'gedcom_news_limit':
	$title=WT_I18N::translate('Limit display by:');
	$text=WT_I18N::translate('You can limit the number of news articles displayed, thereby reducing the height of the GEDCOM News block.<br><br>This option determines whether any limits should be applied or whether the limit should be according to the age of the article or according to the number of articles.');
	break;

case 'google_chart_surname':
	$title=WT_I18N::translate('Surname');
	$text=WT_I18N::translate('The number of occurrences of the specified name will be shown on the map.  If you leave this field empty, the most common surname will be used.');
	break;

case 'header_favorites':
	$title=WT_I18N::translate('Favorites');
	$text=WT_I18N::translate('The Favorites drop-down list shows the favorites that you have selected on your “My page”.  It also shows the favorites that the site administrator has selected for the currently active GEDCOM.  Clicking on one of the favorites entries will take you directly to the Individual Information page of that individual.<br><br>More help about adding favorites is available in your “My page”.');
	break;

case 'import_gedcom':
	$title=WT_I18N::translate('Import family tree');
	$text=
		'<p>' .
		WT_I18N::translate('This option deletes all the genealogy data in your family tree and replaces it with data from a GEDCOM file on the server.') .
		'</p><p>' .
		/* I18N: %s is a folder name */
		WT_I18N::translate('GEDCOM files are stored in the %s folder.', '<b dir="auto">' . WT_DATA_DIR . '</b>') .
		'</p>';
	break;

case 'include_media':
	$title=WT_I18N::translate('Include media (automatically zips files)');
	$text=WT_I18N::translate('Select this option to include the media files associated with the records in your clippings cart.  Choosing this option will automatically zip the files during download.');
	break;

case 'lifespan_chart':
	$title=WT_I18N::translate('Lifespans');
	$text=WT_I18N::translate('On this chart you can display one or more individuals along a horizontal timeline.  This chart allows you to see how the lives of different individuals overlapped.<br><br>You can add individuals to the chart individually or by family groups by their IDs.  The previous list will be remembered as you add more individuals to the chart.  You can clear the chart at any time with the <b>Clear chart</b> button.<br><br>You can also add individuals to the chart by searching for them by date range or locality.');
	break;

case 'next_path':
	$title=WT_I18N::translate('Find the next relationship path');
	$text=WT_I18N::translate('You can click this button to see whether there is another relationship path between the two individuals.  Previously found paths can be displayed again by clicking the link with the path number.');
	break;

case 'no_update_CHAN':
	$title=WT_I18N::translate('Do not update the “last change” record');
	$text=WT_I18N::translate('Administrators sometimes need to clean up and correct the data submitted by users.  For example, they might need to correct the PLAC location to include the country.  When administrators make such corrections, information about the original change is normally replaced.  This may not be desirable.<br><br>When this option is selected, webtrees will retain the original change information instead of replacing it with that of the current session.  With this option selected, administrators also have the ability to modify or delete the information associated with the original CHAN tag.');
	break;

case 'oldest_top':
	$title=WT_I18N::translate('Show oldest top');
	$text=WT_I18N::translate('When this check box is checked, the chart will be printed with oldest individuals at the top.  When it is unchecked, youngest individuals will appear at the top.');
	break;

case 'password':
	$title=WT_I18N::translate('Password');
	$text=WT_I18N::translate('Passwords must be at least 6 characters long and are case-sensitive, so that “secret” is different to “SECRET”.');
	break;

case 'password_confirm':
	$title=WT_I18N::translate('Confirm password');
	$text=WT_I18N::translate('Type your password again, to make sure you have typed it correctly.');
	break;

case 'PGV_WIZARD':
	$title=WT_I18N::translate('PhpGedView to webtrees transfer wizard');
	$text =WT_I18N::translate('The PGV to webtrees wizard is an automated process to assist administrators make the move from a PGV installation to a new webtrees one.  It will transfer all PGV GEDCOM and other database information directly to your new webtrees database.  The following requirements are necessary:');
	$text .= '<ul><li>';
	$text .= WT_I18N::translate('webtrees’ database must be on the same server as PGV’s');
	$text .= '</li><li>';
	$text .= WT_I18N::translate('PGV must be version 4.2.3, or any SVN up to #6973');
	$text .= '</li><li>';
	$text .= WT_I18N::translate('All changes in PGV must be accepted');
	$text .= '</li><li>';
	$text .= WT_I18N::translate('You must export your latest GEDCOM data');
	$text .= '</li><li>';
	$text .= WT_I18N::translate('The current webtrees admin username must be the same as an existing PGV admin username');
	$text .= '</li><li>';
	$text .= WT_I18N::translate('All existing PGV users must have distinct email addresses');
	$text .= '</li></ul><p>';
	$text .= WT_I18N::translate('<b>Important note:</b> The transfer wizard is not able to assist with moving media items.  You will need to set up and move or copy your media configuration and objects separately after the transfer wizard is finished.');
	$text .= '</p>';
	break;

case 'phpinfo':
	$title=WT_I18N::translate('PHP information');
	$text=WT_I18N::translate('This page provides extensive information about the server on which webtrees is being hosted.  Many configuration details about the server’s software, as it relates to PHP and webtrees, can be viewed.');
	break;

case 'pending_changes':
	$title=WT_I18N::translate('Pending changes');
	$text=
		'<p>'.
		WT_I18N::translate('When you add, edit, or delete information, the changes are not saved immediately.  Instead, they are kept in a “pending” area.  These pending changes need to be reviewed by a moderator before they are accepted.').
		'</p><p>'.
		WT_I18N::translate('This process allows the site’s owner to ensure that the new information follows the site’s standards and conventions, has proper source attributions, etc.').
		'</p><p>'.
		WT_I18N::translate('Pending changes are only shown when your account has permission to edit.  When you log out, you will no longer be able to see them.  Also, pending changes are only shown on certain pages.  For example, they are not shown in lists, reports, or search results.').
		'</p>';
	if (Auth::isAdmin()) {
		$text.=
			'<p>'.
			WT_I18N::translate('Each user account has an option to “automatically accept changes”.  When this is enabled, any changes made by that user are saved immediately.  Many administrators enable this for their own user account.').
			'</p>';
	}

	break;

case 'ppp_view_records':
	$title=WT_I18N::translate('View all records');
	$text=WT_I18N::translate('Clicking on this link will show you a list of all of the individuals and families that have events occurring in this place.  When you get to the end of a place hierarchy, which is normally a town or city, the name list will be shown automatically.');
	break;

case 'real_name':
	$title=WT_I18N::translate('Real name');
	$text=WT_I18N::translate('This is your real name, as you would like it displayed on screen.');
	break;

case 'register_comments':
	$title=WT_I18N::translate('Comments');
	$text=WT_I18N::translate('Use this field to tell the site administrator why you are requesting an account and how you are related to the genealogy displayed on this site.  You can also use this to enter any other comments you may have for the site administrator.');
	break;

case 'register_gedcomid':
	$title=WT_I18N::translate('Individual record');
	$text=WT_I18N::translate('Every individual in the database has a unique ID number on this site.  If you know the ID number for your own record, please enter it here.  If you don’t know your ID number or could not find it because of privacy settings, please provide enough information in the comments field to help the site administrator identify who you are on this site so that he can set the ID for you.');
	break;

case 'remove_person':
	$title=WT_I18N::translate('Remove person');
	$text=WT_I18N::translate('Click this link to remove the individual from the timeline.');
	break;

case 'role':
	$title=WT_I18N::translate('Role');
	$text=
		WT_I18N::translate('A role is a set of access rights, which give permission to view data, change configuration settings, etc.  Access rights are assigned to roles, and roles are granted to users.  Each family tree can assign different access to each role, and users can have a different role in each family tree.').
		'<br><br>'.
		'<dl>'.
		'<dt>'.WT_I18N::translate('Visitor').'</dt><dd>'.
		WT_I18N::translate('Everybody has this role, including visitors to the site and search engines.').
		'</dd>'.
		'<dl>'.
		'<dt>'.WT_I18N::translate('Member').'</dt><dd>'.
		WT_I18N::translate('This role has all the permissions of the visitor role, plus any additional access granted by the family tree configuration.').
		'</dd>'.
		'<dl>'.
		'<dt>'.WT_I18N::translate('Editor').'</dt><dd>'.
		WT_I18N::translate('This role has all the permissions of the member role, plus permission to add/change/delete data.  Any changes will need to be approved by a moderator, unless the user has the “automatically accept changes” option enabled.').
		'</dd>'.
		'<dl>'.
		'<dt>'.WT_I18N::translate('Moderator').'</dt><dd>'.
		WT_I18N::translate('This role has all the permissions of the editor role, plus permission to approve/reject changes made by other users.').
		'</dd>'.
		'<dl>'.
		'<dt>'.WT_I18N::translate('Manager').'</dt><dd>'.
		WT_I18N::translate('This role has all the permissions of the moderator role, plus any additional access granted by the family tree configuration, plus permission to change the settings/configuration of a family tree.').
		'</dd>'.
		'<dl>'.
		'<dt>'.WT_I18N::translate('Administrator').'</dt><dd>'.
		WT_I18N::translate('This role has all the permissions of the manager role in all family trees, plus permission to change the settings/configuration of the site, users, and modules.').
		'</dd>';
	break;

case 'SHOW_AGE_DIFF':
	$title=WT_I18N::translate('Date differences');
	$text=WT_I18N::translate('When this option is selected, webtrees will calculate the age differences between siblings, children, spouses, etc.');
	break;

case 'show_fact_sources':
	$title=WT_I18N::translate('Show all sources');
	$text=WT_I18N::translate('When this option is checked, you can see all source or note records for this individual.  When this option is unchecked, source or note records that are associated with other facts for this individual will not be shown.');
	break;

case 'show_spouse':
	$title=WT_I18N::translate('Show spouses');
	$text=WT_I18N::translate('By default this chart does not show spouses for the descendants because it makes the chart harder to read and understand.  Turning this option on will show spouses on the chart.');
	break;

case 'simple_filter':
	$title=WT_I18N::translate('Simple search filter');
	$text=WT_I18N::translate('Simple search filter based on the characters entered, no wildcards are accepted.');
	break;

case 'upload_gedcom':
	$title=WT_I18N::translate('Upload family tree');
	$text=WT_I18N::translate('This option deletes all the genealogy data in your family tree and replaces it with data from a GEDCOM file on your computer.');
	break;

case 'upload_media':
	$title=WT_I18N::translate('Upload media files');
	$text=WT_I18N::translate('Upload one or more media files from your local computer.  Media files can be pictures, video, audio, or other formats.');
	break;

case 'upload_server_file':
	$title=WT_I18N::translate('Filename on server');
	$text=WT_I18N::translate('The media file you are uploading can be, and probably should be, named differently on the server than it is on your local computer.  This is so because often the local filename has meaning to you but is much less meaningful to others visiting this site.  Consider also the possibility that you and someone else both try to upload different files called “granny.jpg“.<br><br>In this field, you specify the new name of the file you are uploading.  The name you enter here will also be used to name the thumbnail, which can be uploaded separately or generated automatically.  You do not need to enter the filename extension (jpg, gif, pdf, doc, etc.)<br><br>Leave this field blank to keep the original name of the file you have uploaded from your local computer.');
	break;

case 'upload_server_folder':
	$title=WT_I18N::translate('Folder name on server');
	$text=
		'<p>' .
		WT_I18N::translate('If you have a large number of media files, you can organize them into folders and subfolders.') .
		'</p>';
	break;

case 'upload_thumbnail_file':
	$title=WT_I18N::translate('Thumbnail to upload');
	$text=WT_I18N::translate('Choose the thumbnail image that you want to upload.  Although thumbnails can be generated automatically for images, you may wish to generate your own thumbnail, especially for other media types.  For example, you can provide a still image from a video, or a photograph of the individual who made an audio recording.');
	break;

case 'useradmin_auto_accept':
	$title=WT_I18N::translate('Automatically approve changes made by this user');
	$text=WT_I18N::translate('Normally, any changes made to a family tree need to be approved by a moderator.  This option allows a user to make changes without needing a moderator’s approval.');
	break;


case 'useradmin_editaccount':
	$title=WT_I18N::translate('Edit account information');
	$text=WT_I18N::translate('If this box is checked, this user will be able to edit his account information.  Although this is not generally recommended, you can create a single user name and password for multiple users.  When this box is unchecked for all users with the shared account, they are prevented from editing the account information and only an administrator can alter that account.');
	break;

case 'useradmin_gedcomid':
	$title=WT_I18N::translate('Individual record');
	$text=WT_I18N::translate('The individual record identifies the user in each family tree.  Since a user can view the details of their individual record, this can only be set by an administrator.  If the user does not have a record in a family tree, leave it empty.');
	break;

case 'useradmin_verification':
	$title=WT_I18N::translate('Account approval and email verification');
	$text=WT_I18N::translate('When a user registers for an account, an email is sent to their email address with a verification link.  When they click this link, we know the email address is correct, and the “email verified” option is selected automatically.').
		'<br><br>'.
		WT_I18N::translate('If an administrator creates a user account, the verification email is not sent, and the email must be verified manually.').
		'<br><br>'.
		WT_I18N::translate('You should not approve an account unless you know that the email address is correct.').
		'<br><br>'.
		WT_I18N::translate('A user will not be able to login until both the “email verified” and “approved by administrator” options are selected.');
	break;

case 'useradmin_visibleonline':
	$title=WT_I18N::translate('Visible online');
	$text=WT_I18N::translate('This checkbox controls your visibility to other users while you’re online.  It also controls your ability to see other online users who are configured to be visible.<br><br>When this box is unchecked, you will be completely invisible to others, and you will also not be able to see other online users.  When this box is checked, exactly the opposite is true.  You will be visible to others, and you will also be able to see others who are configured to be visible.');
	break;

case 'username':
	$title=WT_I18N::translate('Username');
	$text=
		'<p>'.
		WT_I18N::translate('Usernames are case-insensitive and ignore accented letters, so that “chloe”, “chloë”, and “Chloe” are considered to be the same.').
		'</p><p>'.
		WT_I18N::translate('Usernames may not contain the following characters: &lt; &gt; &quot; %% { } ;').
		'</p>';
	break;

case 'utf8_ansi':
	$title=WT_I18N::translate('Convert from UTF-8 to ANSI (ISO-8859-1)');
	$text=WT_I18N::translate('For optimal display on the internet, webtrees uses the UTF-8 character set.  Some programs, Family Tree Maker for example, do not support importing GEDCOM files encoded in UTF-8.  Checking this box will convert the file from <b>UTF-8</b> to <b>ANSI (ISO-8859-1)</b>.<br><br>The format you need depends on the program you use to work with your downloaded GEDCOM file.  If you aren’t sure, consult the documentation of that program.<br><br>Note that for special characters to remain unchanged, you will need to keep the file in UTF-8 and convert it to your program’s method for handling these special characters by some other means.  Consult your program’s manufacturer or author.<br><br>This <a href="http://en.wikipedia.org/wiki/UTF-8" target="_blank" title="Wikipedia article"><b>Wikipedia article</b></a> contains comprehensive information and links about UTF-8.');
	break;

case 'zip':
	$title=WT_I18N::translate('Zip clippings');
	$text=WT_I18N::translate('Select this option as to save your clippings in a ZIP file.  For more information about ZIP files, please visit <a href="http://www.winzip.com" target="_blank">http://www.winzip.com</a>.');
	break;

default:
	$title=WT_I18N::translate('Help');
	$text=WT_I18N::translate('The help text has not been written for this item.');
	// If we've been called from a module, allow the module to provide the help text
	$mod = WT_Filter::get('mod', '[A-Za-z0-9_]+');
	if (file_exists(WT_ROOT.WT_MODULES_DIR.$mod.'/help_text.php')) {
		require WT_ROOT.WT_MODULES_DIR.$mod.'/help_text.php';
	}
	break;
}
// This file is called by a getJSON call so return the data
// in correct format
header('Content-Type: application/json');
echo json_encode(array('title'=>$title,'content'=>$text));
