<?php
/**
 * Census Assistant Control module for phpGedView
 *
 * Census Shared Note Decode for a formatted file
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
 * @subpackage Census Assistant
 * @version $Id$
 */
 
	$text = "xCxAx<table cellpadding=\"0\"><tr><td>" . $text;
	$text = str_replace("<br />.start_formatted_area.<br />", "</td></tr></table><table cellpadding=\"0\"><tr><td class=\"notecell\">&nbsp;", $text);
	
		// -- Create View Header Tooltip explanations (Use embolden) -----------
		$text = str_replace(".b.".i18n::translate('Name'),   "<span class=\"note2\" alt=\"".i18n::translate('Full Name or Married name if married')."\"   title=\"".i18n::translate('Full Name or Married name if married')."\">  <b>".i18n::translate('Name')."</span>",   $text);
		$text = str_replace(".b.".i18n::translate('Relation'),   "<span class=\"note1\" alt=\"".i18n::translate('Relationship to Head of Household')."\"   title=\"".i18n::translate('Relationship to Head of Household')."\">  <b>".i18n::translate('Relation')."</span>",   $text);
		$text = str_replace(".b.".i18n::translate('Assets'),  "<span class=\"note1\" alt=\"".i18n::translate('Assets = Owned,Rented - Value,Rent - Radio - Farm')."\"  title=\"".i18n::translate('Assets = Owned,Rented - Value,Rent - Radio - Farm')."\"> <b>".i18n::translate('Assets')."</span>",  $text);
		$text = str_replace(".b.".i18n::translate('Sex'),    "<span class=\"note1\" alt=\"".i18n::translate('Male or Female')."\"    title=\"".i18n::translate('Male or Female')."\">   <b>".i18n::translate('Sex')."</span>",    $text);
		$text = str_replace(".b.".i18n::translate('Race'),   "<span class=\"note1\" alt=\"".i18n::translate('Race or Color - Black, White, Mulatto, Asian, Indian, Chinese etc')."\"   title=\"".i18n::translate('Race or Color - Black, White, Mulatto, Asian, Indian, Chinese etc')."\">  <b>".i18n::translate('Race')."</span>",   $text);
		$text = str_replace(".b.".i18n::translate('Age'),    "<span class=\"note1\" alt=\"".i18n::translate('Age at last birthday')."\"    title=\"".i18n::translate('Age at last birthday')."\">   <b>".i18n::translate('Age')."</span>",    $text);
		$text = str_replace(".b.".i18n::translate('MC'),  "<span class=\"note1\" alt=\"".i18n::translate('Marital Condition - Married, Single, Unmarried, Widowed or Divorced')."\"  title=\"".i18n::translate('Marital Condition - Married, Single, Unmarried, Widowed or Divorced')."\"> <b>".i18n::translate('MC')."</span>",  $text);
		$text = str_replace(".b.".i18n::translate('DOB'),    "<span class=\"note1\" alt=\"".i18n::translate('Date of Birth')."\"    title=\"".i18n::translate('Date of Birth')."\">   <b>".i18n::translate('DOB')."</span>",    $text);
		$text = str_replace(".b.".i18n::translate('Bmth'),   "<span class=\"note1\" alt=\"".i18n::translate('Month of birth - If born within Census year')."\"   title=\"".i18n::translate('Month of birth - If born within Census year')."\">  <b>".i18n::translate('Bmth')."</span>",   $text);
		$text = str_replace(".b.".i18n::translate('YrsM'),   "<span class=\"note1\" alt=\"".i18n::translate('Years Married, or Y if married in Census Year')."\"   title=\"".i18n::translate('Years Married, or Y if married in Census Year')."\">  <b>".i18n::translate('YrsM')."</span>",   $text);
		$text = str_replace(".b.".i18n::translate('ChB'),  "<span class=\"note1\" alt=\"".i18n::translate('Children born alive')."\"  title=\"".i18n::translate('Children born alive')."\"> <b>".i18n::translate('ChB')."</span>",  $text);
		$text = str_replace(".b.".i18n::translate('ChL'),  "<span class=\"note1\" alt=\"".i18n::translate('Children still living')."\"  title=\"".i18n::translate('Children still living')."\"> <b>".i18n::translate('ChL')."</span>",  $text);
		$text = str_replace(".b.".i18n::translate('ChD'),  "<span class=\"note1\" alt=\"".i18n::translate('Children who have died')."\"  title=\"".i18n::translate('Children who have died')."\"> <b>".i18n::translate('ChD')."</span>",  $text);
		$text = str_replace(".b.".i18n::translate('AgM'),    "<span class=\"note1\" alt=\"".i18n::translate('Age at first marriage')."\"    title=\"".i18n::translate('Age at first marriage')."\">   <b>".i18n::translate('AgM')."</span>",    $text);
		$text = str_replace(".b.".i18n::translate('Occupation'),   "<span class=\"note1\" alt=\"".i18n::translate('Occupation')."\"   title=\"".i18n::translate('Occupation')."\">  <b>".i18n::translate('Occupation')."</span>",   $text);
		$text = str_replace(".b.".i18n::translate('Birthplace'), "<span class=\"note1\" alt=\"".i18n::translate('Birthplace (Full format)')."\" title=\"".i18n::translate('Birthplace (Full format)')."\"><b>".i18n::translate('Birthplace')."</span>", $text);
		$text = str_replace(".b.".i18n::translate('BP'),     "<span class=\"note1\" alt=\"".i18n::translate('Birthplace - (Chapman format)')."\"     title=\"".i18n::translate('Birthplace - (Chapman format)')."\">    <b>".i18n::translate('BP')."</span>",     $text);
		$text = str_replace(".b.".i18n::translate('FBP'),    "<span class=\"note1\" alt=\"".i18n::translate('Father\'s Birthplace - (Chapman format)')."\"    title=\"".i18n::translate('Father\'s Birthplace - (Chapman format)')."\">   <b>".i18n::translate('FBP')."</span>",    $text);
		$text = str_replace(".b.".i18n::translate('MBP'),    "<span class=\"note1\" alt=\"".i18n::translate('Mother\'s Birthplace - (Chapman format)')."\"    title=\"".i18n::translate('Mother\'s Birthplace - (Chapman format)')."\">   <b>".i18n::translate('MBP')."</span>",    $text);
		$text = str_replace(".b.".i18n::translate('NL'),     "<span class=\"note1\" alt=\"".i18n::translate('If Foreign Born - Native Language')."\"     title=\"".i18n::translate('If Foreign Born - Native Language')."\">    <b>".i18n::translate('NL')."</span>",     $text);
		$text = str_replace(".b.".i18n::translate('YUS'),  "<span class=\"note1\" alt=\"".i18n::translate('If Foreign Born - Years in the USA')."\"  title=\"".i18n::translate('If Foreign Born - Years in the USA')."\"> <b>".i18n::translate('YUS')."</span>",  $text);
		$text = str_replace(".b.".i18n::translate('YOI'),    "<span class=\"note1\" alt=\"".i18n::translate('If Foreign Born - Year of Immigration')."\"    title=\"".i18n::translate('If Foreign Born - Year of Immigration')."\">   <b>".i18n::translate('YOI')."</span>",    $text);
		$text = str_replace(".b.".i18n::translate('N/A'),     "<span class=\"note1\" alt=\"".i18n::translate('If Foreign Born - Naturalized, Alien')."\"     title=\"".i18n::translate('If Foreign Born - Naturalized, Alien')."\">    <b>".i18n::translate('N/A')."</span>",     $text);
		$text = str_replace(".b.".i18n::translate('YON'),    "<span class=\"note1\" alt=\"".i18n::translate('If Foreign Born - Year of Naturalization')."\"    title=\"".i18n::translate('If Foreign Born - Year of Naturalization')."\">   <b>".i18n::translate('YON')."</span>",    $text);
		$text = str_replace(".b.".i18n::translate('EngL'),   "<span class=\"note1\" alt=\"".i18n::translate('English spoken?, if not, Native Language')."\"   title=\"".i18n::translate('English spoken?, if not, Native Language')."\">  <b>".i18n::translate('EngL')."</span>",   $text);
		$text = str_replace(".b.".i18n::translate('Health'), "<span class=\"note1\" alt=\"".i18n::translate('Health - 1.Blind, 2.Deaf&Dumb, 3.Idiotic, 4.Insane, 5.Disabled etc')."\" title=\"".i18n::translate('Health - 1.Blind, 2.Deaf&Dumb, 3.Idiotic, 4.Insane, 5.Disabled etc')."\"><b>".i18n::translate('Health')."</span>", $text);
		$text = str_replace(".b.".i18n::translate('Industry'),    "<span class=\"note1\" alt=\"".i18n::translate('Industry')."\"    title=\"".i18n::translate('Industry')."\">   <b>".i18n::translate('Industry')."</span>",    $text);
		$text = str_replace(".b.".i18n::translate('Employ'),    "<span class=\"note1\" alt=\"".i18n::translate('Employment')."\"    title=\"".i18n::translate('Employment')."\">   <b>".i18n::translate('Employ')."</span>",    $text);
		$text = str_replace(".b.".i18n::translate('EmR'),    "<span class=\"note1\" alt=\"".i18n::translate('Employer?')."\"    title=\"".i18n::translate('Employer?')."\">   <b>".i18n::translate('EmR')."</span>",    $text);
		$text = str_replace(".b.".i18n::translate('EmD'),    "<span class=\"note1\" alt=\"".i18n::translate('Employed?')."\"    title=\"".i18n::translate('Employed?')."\">   <b>".i18n::translate('EmD')."</span>",    $text);
		$text = str_replace(".b.".i18n::translate('WH'),    "<span class=\"note1\" alt=\"".i18n::translate('Working at Home?')."\"    title=\"".i18n::translate('Working at Home?')."\">   <b>".i18n::translate('WH')."</span>",    $text);
		$text = str_replace(".b.".i18n::translate('EmN'),    "<span class=\"note1\" alt=\"".i18n::translate('Unemployed?')."\"    title=\"".i18n::translate('Unemployed?')."\">   <b>".i18n::translate('EmN')."</span>",    $text);
		$text = str_replace(".b.".i18n::translate('Edu'),   "<span class=\"note1\" alt=\"".i18n::translate('Education - At School, Can Read, Can Write')."\"   title=\"".i18n::translate('Education - At School, Can Read, Can Write')."\">  <b>".i18n::translate('Edu')."</span>",   $text);
		$text = str_replace(".b.".i18n::translate('Eng?'),    "<span class=\"note1\" alt=\"".i18n::translate('English spoken?')."\"    title=\"".i18n::translate('English spoken?')."\">   <b>".i18n::translate('Eng?')."</span>",    $text);
		$text = str_replace(".b.".i18n::translate('BIC'),    "<span class=\"note1\" alt=\"".i18n::translate('Born in County')."\"    title=\"".i18n::translate('Born in County')."\">   <b>".i18n::translate('BIC')."</span>",    $text);
		$text = str_replace(".b.".i18n::translate('BOE'),    "<span class=\"note1\" alt=\"".i18n::translate('Born outside England')."\"    title=\"".i18n::translate('Born outside England')."\">   <b>".i18n::translate('BOE')."</span>",    $text);
		$text = str_replace(".b.".i18n::translate('Lang'),   "<span class=\"note1\" alt=\"".i18n::translate('If Foreign Born - Native Language')."\"   title=\"".i18n::translate('If Foreign Born - Native Language')."\">  <b>".i18n::translate('Lang')."</span>",   $text);
		$text = str_replace(".b.".i18n::translate('Infirm'), "<span class=\"note1\" alt=\"".i18n::translate('Infirmaties - 1.Deaf&Dumb, 2.Blind, 3.Lunatic, 4.Imbecile/feeble-minded')."\" title=\"".i18n::translate('Infirmaties - 1.Deaf&Dumb, 2.Blind, 3.Lunatic, 4.Imbecile/feeble-minded')."\"><b>".i18n::translate('Infirm')."</span>", $text);
		$text = str_replace(".b.".i18n::translate('Vet'),    "<span class=\"note1\" alt=\"".i18n::translate('War Veteran?')."\"    title=\"".i18n::translate('War Veteran?')."\">   <b>".i18n::translate('Vet')."</span>",    $text);

		$text = str_replace(".b.".i18n::translate('Ten'),      "<span class=\"note1\" alt=\"".i18n::translate('Tenure - Owned/Rented, (if owned)Free/Morgaged')."\"       title=\"".i18n::translate('Tenure - Owned/Rented, (if owned)Free/Morgaged')."\">     <b>".i18n::translate('Ten')."</span>",      $text);
		$text = str_replace(".b.".i18n::translate('Par'),      "<span class=\"note1\" alt=\"".i18n::translate('Parentage - Father if foreign born, Mother if foreign born')."\"       title=\"".i18n::translate('Parentage - Father if foreign born, Mother if foreign born')."\">     <b>".i18n::translate('Par')."</span>",      $text);
		$text = str_replace(".b.".i18n::translate('Mmth'),        "<span class=\"note1\" alt=\"".i18n::translate('Month of marriage - If married during Census Year')."\"         title=\"".i18n::translate('Month of marriage - If married during Census Year')."\">       <b>".i18n::translate('Mmth')."</span>",        $text);
		$text = str_replace(".b.".i18n::translate('MnsE'),        "<span class=\"note1\" alt=\"".i18n::translate('Months employed during Census Year')."\"         title=\"".i18n::translate('Months employed during Census Year')."\">       <b>".i18n::translate('MnsE')."</span>",        $text);
		$text = str_replace(".b.".i18n::translate('WksU'),        "<span class=\"note1\" alt=\"".i18n::translate('Weeks unemployed during Census Year')."\"         title=\"".i18n::translate('Weeks unemployed during Census Year')."\">       <b>".i18n::translate('WksU')."</span>",        $text);
		$text = str_replace(".b.".i18n::translate('MnsU'),        "<span class=\"note1\" alt=\"".i18n::translate('Months unemployed during Census Year')."\"         title=\"".i18n::translate('English spoken?')."\">        <b>".i18n::translate('Eng?')."</span>",         $text);
		$text = str_replace(".b.".i18n::translate('Edu'), "<span class=\"note1\" alt=\"".i18n::translate('Education - xxx - At School, Cannot Read, Cannot Write')."\"  title=\"".i18n::translate('Education - xxx - At School, Cannot Read, Cannot Write')."\"><b>".i18n::translate('Edu')."</span>", $text);
		$text = str_replace(".b.".i18n::translate('Home'),        "<span class=\"note1\" alt=\"".i18n::translate('Home Ownership - Owned/Rented-Free/Mortgaged-Farm/House-Farm Schedule number')."\"         title=\"".i18n::translate('Home Ownership - Owned/Rented-Free/Mortgaged-Farm/House-Farm Schedule number')."\">       <b>".i18n::translate('Home')."</span>",        $text);
		$text = str_replace(".b.".i18n::translate('Situ'),        "<span class=\"note1\" alt=\"".i18n::translate('Situation - Disease, Infirmaty, Convict, Pauper etc')."\"         title=\"".i18n::translate('Situation - Disease, Infirmaty, Convict, Pauper etc')."\">       <b>".i18n::translate('Situ')."</span>",        $text);
		$text = str_replace(".b.".i18n::translate('War'),         "<span class=\"note1\" alt=\"".i18n::translate('War or Expedition')."\"          title=\"".i18n::translate('War or Expedition')."\">        <b>".i18n::translate('War')."</span>",         $text);
		$text = str_replace(".b.".i18n::translate('Infirm'),  "<span class=\"note1\" alt=\"".i18n::translate('Infirmaties - Whether blind, Whether Deaf and Dumb')."\"   title=\"".i18n::translate('Infirmaties - Whether blind, Whether Deaf and Dumb')."\"> <b>".i18n::translate('Infirm')."</span>",  $text);


		// Regular Field Highlighting (Use embolden) ------------
		$text = str_replace(".b.", "<b>", $text); 
		
		// Replace "pipe" with </td><td> ------------------------
		$text = str_replace("|", "&nbsp;&nbsp;</td><td class=\"notecell\">", $text);
		
	$text = str_replace(".end_formatted_area.<br />", "</td></tr></table><table cellpadding=\"0\"><tr><td>", $text);
	$text = str_replace("<br />", "</td></tr><tr><td class=\"notecell\">&nbsp;", $text);
	$text = $text . "</td></tr></table>";
	$text = str_replace("xCxAx", $centitl."<br />", $text);
	$text = str_replace("Notes:", "<b>Notes:</b>", $text);

?>
